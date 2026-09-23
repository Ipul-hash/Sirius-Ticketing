<?php

namespace App\Http\Controllers\Api\V1\Ticket;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Ticket;
use App\Models\TicketActivity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TicketActivityController extends Controller
{
    /**
     * Mengambil riwayat jejak audit (Audit Trail & Timeline) dari tiket.
     */
    public function index(Request $request, string $param1, ?string $param2 = null): JsonResponse
    {
        $tenant = $param2 !== null ? $param1 : null;
        $ticketId = $param2 !== null ? $param2 : $param1;

        $companyId = $this->resolveCompanyId($request, $tenant);

        $ticket = Ticket::query()
            ->when($companyId !== null, fn ($q) => $q->where('company_id', $companyId))
            ->where(function ($q) use ($ticketId) {
                $q->where('id', $ticketId)
                    ->orWhere('ticket_number', $ticketId);
            })
            ->first();

        if ($ticket === null) {
            return response()->json([
                'success' => false,
                'message' => 'Tiket tidak ditemukan.',
            ], 404);
        }

        $query = TicketActivity::query()
            ->with(['user:id,name,email,role,job_title,avatar_path'])
            ->where('ticket_id', $ticket->id);

        // Filter berdasarkan kategori aktivitas
        if ($request->filled('category')) {
            $category = $request->query('category');
            match ($category) {
                'status' => $query->whereIn('activity_type', ['status_changed', 'priority_changed', 'ticket_created']),
                'assignment' => $query->where('activity_type', 'assigned_agent'),
                'approval' => $query->whereIn('activity_type', ['approval_requested', 'ticket_approved', 'ticket_rejected']),
                'communication' => $query->whereIn('activity_type', ['public_reply', 'internal_note']),
                default => null,
            };
        }

        // Filter tipe aktivitas spesifik
        if ($request->filled('activity_type')) {
            $types = is_array($request->query('activity_type'))
                ? $request->query('activity_type')
                : explode(',', (string) $request->query('activity_type'));

            $query->whereIn('activity_type', $types);
        }

        $direction = strtolower((string) $request->query('direction', 'desc')) === 'asc' ? 'asc' : 'desc';
        $query->orderBy('created_at', $direction);

        $perPage = (int) $request->query('per_page', 20);
        $activities = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Riwayat jejak audit aktivitas tiket berhasil dimuat.',
            'data' => $activities->items(),
            'meta' => [
                'ticket_id' => $ticket->id,
                'ticket_number' => $ticket->ticket_number,
                'current_page' => $activities->currentPage(),
                'last_page' => $activities->lastPage(),
                'per_page' => $activities->perPage(),
                'total' => $activities->total(),
            ],
        ], 200);
    }

    /**
     * Membantu mencari ID perusahaan berdasarkan slug tenant atau request parameter.
     */
    protected function resolveCompanyId(Request $request, ?string $tenant = null): ?int
    {
        $user = $request->user();
        if ($user !== null && ! $user->isSuperadmin() && $user->company_id !== null) {
            return (int) $user->company_id;
        }

        if ($tenant !== null && $tenant !== '') {
            $company = Company::where('slug', $tenant)->orWhere('id', $tenant)->first();

            return $company?->id;
        }

        if ($request->filled('company_id')) {
            return (int) $request->company_id;
        }

        if ($user !== null && $user->company_id !== null) {
            return (int) $user->company_id;
        }

        return null;
    }
}
