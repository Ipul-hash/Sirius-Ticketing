<?php

namespace App\Http\Controllers\Api\V1\Ticket;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Ticket;
use App\Models\TicketCollision;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TicketCollisionController extends Controller
{
    /**
     * Heartbeat ping dari teknisi yang sedang membuka tiket.
     */
    public function ping(Request $request, string $param1, ?string $param2 = null): JsonResponse
    {
        $tenant = $param2 !== null ? $param1 : null;
        $ticketId = $param2 !== null ? $param2 : $param1;

        $ticket = $this->resolveTicket($request, $ticketId, $tenant);
        if ($ticket === null) {
            return response()->json([
                'success' => false,
                'message' => 'Tiket tidak ditemukan.',
            ], 404);
        }

        $user = $request->user() ?? ($request->filled('user_id') ? User::find($request->user_id) : null);
        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Data pengguna tidak teridentifikasi.',
            ], 422);
        }

        $userName = $request->input('user_name', $user->name);

        // Catat atau perbarui kehadiran pengguna di tiket ini
        TicketCollision::updateOrInsert(
            [
                'ticket_id' => $ticket->id,
                'user_id' => $user->id,
            ],
            [
                'user_name' => $userName,
                'last_seen_at' => now(),
            ]
        );

        // Bersihkan data kedaluwarsa (> 60 detik tidak ada heartbeat)
        TicketCollision::where('last_seen_at', '<', now()->subSeconds(60))->delete();

        // Ambil agen lain yang sedang aktif di tiket ini (30 detik terakhir, exclude diri sendiri)
        $activeCollisions = TicketCollision::query()
            ->with(['user:id,name,email,role,job_title,avatar_path'])
            ->where('ticket_id', $ticket->id)
            ->where('user_id', '!=', $user->id)
            ->where('last_seen_at', '>=', now()->subSeconds(30))
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Heartbeat presence berhasil diperbarui.',
            'has_collision' => $activeCollisions->isNotEmpty(),
            'total_other_agents' => $activeCollisions->count(),
            'agents' => $activeCollisions->map(function ($collision) {
                return [
                    'user_id' => $collision->user_id,
                    'name' => $collision->user?->name ?? $collision->user_name,
                    'email' => $collision->user?->email,
                    'role' => $collision->user?->role?->value ?? $collision->user?->role,
                    'job_title' => $collision->user?->job_title ?? 'Teknisi',
                    'avatar_path' => $collision->user?->avatar_path,
                    'last_seen_at' => $collision->last_seen_at?->toIso8601String(),
                ];
            }),
        ], 200);
    }

    /**
     * Mengambil daftar teknisi yang sedang aktif di tiket saat ini.
     */
    public function active(Request $request, string $param1, ?string $param2 = null): JsonResponse
    {
        $tenant = $param2 !== null ? $param1 : null;
        $ticketId = $param2 !== null ? $param2 : $param1;

        $ticket = $this->resolveTicket($request, $ticketId, $tenant);
        if ($ticket === null) {
            return response()->json([
                'success' => false,
                'message' => 'Tiket tidak ditemukan.',
            ], 404);
        }

        $currentUserId = $request->user()?->id ?? $request->query('user_id');

        $activeCollisions = TicketCollision::query()
            ->with(['user:id,name,email,role,job_title,avatar_path'])
            ->where('ticket_id', $ticket->id)
            ->when($currentUserId, fn ($q) => $q->where('user_id', '!=', $currentUserId))
            ->where('last_seen_at', '>=', now()->subSeconds(30))
            ->get();

        return response()->json([
            'success' => true,
            'ticket_id' => $ticket->id,
            'ticket_number' => $ticket->ticket_number,
            'has_collision' => $activeCollisions->isNotEmpty(),
            'total_other_agents' => $activeCollisions->count(),
            'agents' => $activeCollisions->map(function ($collision) {
                return [
                    'user_id' => $collision->user_id,
                    'name' => $collision->user?->name ?? $collision->user_name,
                    'email' => $collision->user?->email,
                    'role' => $collision->user?->role?->value ?? $collision->user?->role,
                    'job_title' => $collision->user?->job_title ?? 'Teknisi',
                    'avatar_path' => $collision->user?->avatar_path,
                    'last_seen_at' => $collision->last_seen_at?->toIso8601String(),
                ];
            }),
        ], 200);
    }

    /**
     * Melepaskan status kehadiran teknisi ketika meninggalkan halaman tiket.
     */
    public function leave(Request $request, string $param1, ?string $param2 = null): JsonResponse
    {
        $tenant = $param2 !== null ? $param1 : null;
        $ticketId = $param2 !== null ? $param2 : $param1;

        $ticket = $this->resolveTicket($request, $ticketId, $tenant);
        if ($ticket === null) {
            return response()->json([
                'success' => false,
                'message' => 'Tiket tidak ditemukan.',
            ], 404);
        }

        $userId = $request->user()?->id ?? $request->input('user_id');
        if ($userId) {
            TicketCollision::where('ticket_id', $ticket->id)
                ->where('user_id', $userId)
                ->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Status kehadiran tiket berhasil dilepas.',
        ], 200);
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
