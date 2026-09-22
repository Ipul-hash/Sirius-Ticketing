<?php

namespace App\Http\Controllers\Api\V1\Ticket;

use App\Enums\TicketStatus;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Ticket;
use App\Models\TicketActivity;
use App\Models\TicketMessage;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TicketMergeController extends Controller
{
    /**
     * Mengambil daftar tiket kandidat target penggabungan dalam tenant yang sama.
     */
    public function candidates(Request $request, string $param1, ?string $param2 = null): JsonResponse
    {
        $tenant = $param2 !== null ? $param1 : null;
        $ticketId = $param2 !== null ? $param2 : $param1;

        $sourceTicket = $this->resolveTicket($request, $ticketId, $tenant);
        if ($sourceTicket === null) {
            return response()->json([
                'success' => false,
                'message' => 'Tiket asal tidak ditemukan.',
            ], 404);
        }

        $query = Ticket::query()
            ->with(['requester:id,name,email', 'category:id,name'])
            ->where('company_id', $sourceTicket->company_id)
            ->where('id', '!=', $sourceTicket->id)
            ->where('is_merged', false)
            ->where('status', '!=', TicketStatus::Closed);

        if ($request->filled('q')) {
            $search = trim((string) $request->query('q'));
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        $candidates = $query->orderBy('created_at', 'desc')->limit(20)->get();

        return response()->json([
            'success' => true,
            'source_ticket' => [
                'id' => $sourceTicket->id,
                'ticket_number' => $sourceTicket->ticket_number,
                'subject' => $sourceTicket->subject,
                'requester_name' => $sourceTicket->requester?->name,
            ],
            'candidates' => $candidates->map(function ($ticket) {
                return [
                    'id' => $ticket->id,
                    'ticket_number' => $ticket->ticket_number,
                    'subject' => $ticket->subject,
                    'status' => $ticket->status->value,
                    'priority' => $ticket->priority->value,
                    'category' => $ticket->category?->name,
                    'requester_name' => $ticket->requester?->name,
                    'created_at' => $ticket->created_at?->format('d M Y H:i'),
                ];
            }),
        ], 200);
    }

    /**
     * Menggabungkan tiket asal (duplikat) ke tiket target utama (primary).
     */
    public function merge(Request $request, string $param1, ?string $param2 = null): JsonResponse
    {
        $tenant = $param2 !== null ? $param1 : null;
        $ticketId = $param2 !== null ? $param2 : $param1;

        $sourceTicket = $this->resolveTicket($request, $ticketId, $tenant);
        if ($sourceTicket === null) {
            return response()->json([
                'success' => false,
                'message' => 'Tiket asal tidak ditemukan.',
            ], 404);
        }

        if ($sourceTicket->is_merged) {
            return response()->json([
                'success' => false,
                'message' => 'Tiket ini sudah pernah digabungkan sebelumnya ke tiket lain.',
            ], 422);
        }

        $targetTicketId = $request->input('target_ticket_id');
        if (! $targetTicketId) {
            return response()->json([
                'success' => false,
                'message' => 'Tiket target utama wajib dipilih.',
            ], 422);
        }

        $targetTicket = Ticket::where('company_id', $sourceTicket->company_id)
            ->where(function ($q) use ($targetTicketId) {
                $q->where('id', $targetTicketId)
                    ->orWhere('ticket_number', $targetTicketId);
            })
            ->first();

        if ($targetTicket === null) {
            return response()->json([
                'success' => false,
                'message' => 'Tiket target utama tidak ditemukan dalam perusahaan yang sama.',
            ], 404);
        }

        if ($targetTicket->id === $sourceTicket->id) {
            return response()->json([
                'success' => false,
                'message' => 'Tiket tidak dapat digabungkan ke dirinya sendiri.',
            ], 422);
        }

        if ($targetTicket->is_merged) {
            return response()->json([
                'success' => false,
                'message' => 'Tiket target utama yang dipilih sudah berstatus digabungkan (merged). Pilih tiket utama yang aktif.',
            ], 422);
        }

        $user = $request->user() ?? ($request->filled('user_id') ? User::find($request->user_id) : null) ?? $sourceTicket->assignedAgent;
        $userName = $user?->name ?? 'Teknisi Sistem';
        $reason = trim((string) ($request->input('reason_notes') ?? $request->input('notes') ?? ''));

        return DB::transaction(function () use ($sourceTicket, $targetTicket, $user, $userName, $reason) {
            $prevStatus = $sourceTicket->status->value;

            // 1. Update Tiket Asal (Duplikat/Child)
            $sourceTicket->update([
                'is_merged' => true,
                'merged_into_ticket_id' => $targetTicket->id,
                'status' => TicketStatus::Closed,
                'closed_at' => now(),
            ]);

            // 2. Tambahkan Catatan Internal di Tiket Asal
            $childNote = "Tiket ini telah digabungkan ke tiket utama #{$targetTicket->ticket_number} ('{$targetTicket->subject}') oleh {$userName}."
                ."\n\nSeluruh proses penanganan, komunikasi, dan resolusi kendala dilanjutkan pada tiket utama tersebut."
                .($reason !== '' ? "\n\nCatatan Penggabungan: {$reason}" : '');

            TicketMessage::create([
                'ticket_id' => $sourceTicket->id,
                'user_id' => $user?->id,
                'message' => $childNote,
                'is_internal_note' => true,
            ]);

            // 3. Tambahkan Catatan Internal di Tiket Utama (Target/Parent)
            $requesterName = $sourceTicket->requester?->name ?? 'Pemohon';
            $parentNote = "Tiket #{$sourceTicket->ticket_number} ('{$sourceTicket->subject}') dari {$requesterName} telah digabungkan ke tiket ini oleh {$userName}."
                ."\n\nDeskripsi Tiket yang Digabungkan:\n\"{$sourceTicket->description}\""
                .($reason !== '' ? "\n\nCatatan Penggabungan: {$reason}" : '');

            TicketMessage::create([
                'ticket_id' => $targetTicket->id,
                'user_id' => $user?->id,
                'message' => $parentNote,
                'is_internal_note' => true,
            ]);

            // 4. Catat Jejak Audit (Ticket Activities) pada kedua tiket
            TicketActivity::create([
                'ticket_id' => $sourceTicket->id,
                'user_id' => $user?->id,
                'activity_type' => 'ticket_merged',
                'old_value' => $prevStatus,
                'new_value' => 'closed',
                'notes' => "Tiket digabungkan ke tiket utama #{$targetTicket->ticket_number}",
            ]);

            TicketActivity::create([
                'ticket_id' => $targetTicket->id,
                'user_id' => $user?->id,
                'activity_type' => 'ticket_merged_source',
                'old_value' => null,
                'new_value' => $sourceTicket->ticket_number,
                'notes' => "Tiket #{$sourceTicket->ticket_number} berhasil digabungkan ke tiket ini",
            ]);

            return response()->json([
                'success' => true,
                'message' => "Tiket #{$sourceTicket->ticket_number} berhasil digabungkan ke tiket utama #{$targetTicket->ticket_number}.",
                'data' => [
                    'source_ticket_id' => $sourceTicket->id,
                    'source_ticket_number' => $sourceTicket->ticket_number,
                    'target_ticket_id' => $targetTicket->id,
                    'target_ticket_number' => $targetTicket->ticket_number,
                    'redirect_url' => url("/tickets/{$targetTicket->id}"),
                ],
            ], 200);
        });
    }

    /**
     * Mencari tiket berdasarkan ID atau nomor tiket dengan multi-tenant scoping.
     */
    protected function resolveTicket(Request $request, string $ticketId, ?string $tenant = null): ?Ticket
    {
        $companyId = null;

        if ($tenant !== null) {
            $company = Company::where('slug', $tenant)->first();
            $companyId = $company?->id;
        } elseif ($request->filled('company_id')) {
            $companyId = (int) $request->company_id;
        }

        return Ticket::query()
            ->when($companyId !== null, fn ($q) => $q->where('company_id', $companyId))
            ->where(function ($q) use ($ticketId) {
                $q->where('id', $ticketId)
                    ->orWhere('ticket_number', $ticketId);
            })
            ->first();
    }
}
