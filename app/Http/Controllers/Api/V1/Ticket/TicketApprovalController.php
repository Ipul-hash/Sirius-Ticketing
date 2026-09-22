<?php

namespace App\Http\Controllers\Api\V1\Ticket;

use App\Enums\ApprovalStatus;
use App\Enums\TicketApprovalStatus;
use App\Enums\TicketStatus;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Ticket;
use App\Models\TicketActivity;
use App\Models\TicketApproval;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TicketApprovalController extends Controller
{
    /**
     * Mengambil daftar tiket yang sedang menunggu persetujuan (ITIL Approval).
     */
    public function indexPending(Request $request, ?string $tenant = null): JsonResponse
    {
        $query = Ticket::query()
            ->with([
                'company:id,name,slug',
                'category:id,name,requires_approval',
                'department:id,name',
                'requester:id,name,email,avatar_path',
                'assignedAgent:id,name,email,avatar_path',
                'asset:id,name,asset_tag',
                'approvals' => function ($q) {
                    $q->with('approver:id,name,email,role')->latest();
                },
            ])
            ->where(function ($q) {
                $q->where('status', TicketStatus::PendingApproval)
                    ->orWhere('approval_status', TicketApprovalStatus::Pending);
            });

        // Filter tenant jika ada
        if ($tenant !== null) {
            $company = Company::where('slug', $tenant)->first();
            if ($company) {
                $query->where('company_id', $company->id);
            }
        } elseif ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        $perPage = (int) $request->query('per_page', 10);
        $tickets = $query->orderBy('created_at', 'asc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Daftar tiket menunggu persetujuan berhasil dimuat.',
            'data' => $tickets->items(),
            'meta' => [
                'current_page' => $tickets->currentPage(),
                'last_page' => $tickets->lastPage(),
                'per_page' => $tickets->perPage(),
                'total' => $tickets->total(),
            ],
        ], 200);
    }

    /**
     * Menyetujui tiket yang memerlukan approval.
     */
    public function approve(Request $request, string $ticketId): JsonResponse
    {
        $ticket = Ticket::where('id', $ticketId)
            ->orWhere('ticket_number', $ticketId)
            ->firstOrFail();

        $validated = $request->validate([
            'reason_notes' => ['nullable', 'string', 'max:1000'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'approver_id' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        $approverId = $request->user()?->id
            ?? $request->input('approver_id')
            ?? 1;

        $notes = $validated['reason_notes'] ?? $validated['notes'] ?? null;

        // Cari atau buat record persetujuan
        $approval = TicketApproval::where('ticket_id', $ticket->id)
            ->where('status', ApprovalStatus::Pending)
            ->latest()
            ->first();

        if (! $approval) {
            $approval = new TicketApproval;
            $approval->ticket_id = $ticket->id;
        }

        $approval->approver_id = $approverId;
        $approval->status = ApprovalStatus::Approved;
        $approval->reason_notes = $notes;
        $approval->decided_at = now();
        $approval->save();

        // Update status tiket
        $oldStatus = $ticket->status;
        $ticket->approval_status = TicketApprovalStatus::Approved;
        $newStatus = $ticket->assigned_to ? TicketStatus::InProgress : TicketStatus::Open;
        $ticket->status = $newStatus;
        $ticket->save();

        // Rekam riwayat jejak audit
        $approver = User::find($approverId);
        $approverName = $approver?->name ?? 'Approver';

        TicketActivity::create([
            'ticket_id' => $ticket->id,
            'user_id' => $approverId,
            'activity_type' => 'ticket_approved',
            'old_value' => $oldStatus->value,
            'new_value' => $newStatus->value,
            'notes' => "Tiket telah disetujui oleh {$approverName}".($notes ? ". Catatan: {$notes}" : '.'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tiket berhasil disetujui. Status tiket kini aktif.',
            'data' => [
                'ticket_id' => $ticket->id,
                'ticket_number' => $ticket->ticket_number,
                'status' => $ticket->status->value,
                'approval_status' => $ticket->approval_status->value,
                'approval' => $approval->load('approver:id,name,email,role'),
            ],
        ], 200);
    }

    /**
     * Menolak tiket yang memerlukan approval.
     */
    public function reject(Request $request, string $ticketId): JsonResponse
    {
        $ticket = Ticket::where('id', $ticketId)
            ->orWhere('ticket_number', $ticketId)
            ->firstOrFail();

        $validated = $request->validate([
            'reason_notes' => ['required', 'string', 'min:3', 'max:1000'],
            'approver_id' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        $approverId = $request->user()?->id
            ?? $request->input('approver_id')
            ?? 1;

        // Cari atau buat record persetujuan
        $approval = TicketApproval::where('ticket_id', $ticket->id)
            ->where('status', ApprovalStatus::Pending)
            ->latest()
            ->first();

        if (! $approval) {
            $approval = new TicketApproval;
            $approval->ticket_id = $ticket->id;
        }

        $approval->approver_id = $approverId;
        $approval->status = ApprovalStatus::Rejected;
        $approval->reason_notes = $validated['reason_notes'];
        $approval->decided_at = now();
        $approval->save();

        // Update status tiket menjadi closed (ditutup karena ditolak)
        $oldStatus = $ticket->status;
        $ticket->approval_status = TicketApprovalStatus::Rejected;
        $ticket->status = TicketStatus::Closed;
        $ticket->closed_at = now();
        $ticket->save();

        // Rekam riwayat audit trail
        $approver = User::find($approverId);
        $approverName = $approver?->name ?? 'Approver';

        TicketActivity::create([
            'ticket_id' => $ticket->id,
            'user_id' => $approverId,
            'activity_type' => 'ticket_rejected',
            'old_value' => $oldStatus->value,
            'new_value' => TicketStatus::Closed->value,
            'notes' => "Tiket ditolak oleh {$approverName}. Alasan: {$validated['reason_notes']}",
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tiket telah ditolak dan ditutup.',
            'data' => [
                'ticket_id' => $ticket->id,
                'ticket_number' => $ticket->ticket_number,
                'status' => $ticket->status->value,
                'approval_status' => $ticket->approval_status->value,
                'approval' => $approval->load('approver:id,name,email,role'),
            ],
        ], 200);
    }
}
