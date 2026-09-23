<?php

namespace App\Http\Controllers\Api\V1\Ticket;

use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketActivity;
use App\Models\TicketAttachment;
use App\Models\TicketMessage;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TicketMessageController extends Controller
{
    /**
     * Ambil riwayat percakapan tiket.
     */
    public function index(Request $request, string $ticketId): JsonResponse
    {
        $ticket = Ticket::where('id', $ticketId)
            ->orWhere('ticket_number', $ticketId)
            ->firstOrFail();

        $query = $ticket->messages()->with([
            'user:id,name,email,role,job_title,avatar_path',
            'attachments',
        ]);

        // Sembunyikan catatan internal jika pengguna adalah requester
        $currentUser = $request->user();
        if ($currentUser && $currentUser->isRequester()) {
            $query->where('is_internal_note', false);
        }

        $messages = $query->orderBy('created_at', 'asc')->get();

        return response()->json([
            'success' => true,
            'ticket_id' => $ticket->id,
            'ticket_number' => $ticket->ticket_number,
            'total_messages' => $messages->count(),
            'data' => $messages,
        ]);
    }

    /**
     * Kirim balasan publik atau catatan internal pada tiket.
     */
    public function store(Request $request, string $ticketId): JsonResponse
    {
        $ticket = Ticket::where('id', $ticketId)
            ->orWhere('ticket_number', $ticketId)
            ->firstOrFail();

        $validated = $request->validate([
            'message' => ['required', 'string'],
            'is_internal_note' => ['nullable', 'boolean'],
            'status' => ['nullable', 'string'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'attachments.*' => ['nullable', 'file', 'max:10240'], // Max 10MB per file
        ]);

        $isInternalNote = $request->boolean('is_internal_note');

        $authUser = $request->user();
        if ($authUser && $authUser->isRequester()) {
            $isInternalNote = false;
            if (! empty($validated['status']) && in_array($validated['status'], [TicketStatus::Resolved->value, TicketStatus::InProgress->value, TicketStatus::PendingApproval->value], true)) {
                unset($validated['status']);
            }
        }

        // Tentukan user pengirim
        $userId = $authUser?->id
            ?? $request->input('user_id')
            ?? ($isInternalNote ? ($ticket->assigned_to ?? 1) : $ticket->requester_id);

        $sender = User::find($userId);

        // Buat pesan
        $message = TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => $userId,
            'message' => $validated['message'],
            'is_internal_note' => $isInternalNote,
        ]);

        // Simpan lampiran berkas jika ada
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $fileName = $file->getClientOriginalName();
                $fileSize = (int) round($file->getSize() / 1024);
                $fileType = $file->getClientMimeType() ?: ($file->getClientOriginalExtension() ?: 'unknown');
                $filePath = $file->store("attachments/tickets/{$ticket->id}", 'public');

                TicketAttachment::create([
                    'ticket_id' => $ticket->id,
                    'ticket_message_id' => $message->id,
                    'uploaded_by' => $userId,
                    'file_name' => $fileName,
                    'file_path' => $filePath,
                    'file_size_kb' => $fileSize,
                    'file_type' => $fileType,
                ]);
            }
        }

        // Kalkulasi SLA & update metrik tiket
        if (! $isInternalNote) {
            // Jika agen/admin pertama kali merespon, catat first_responded_at
            if ($sender && in_array($sender->role, [UserRole::Agent, UserRole::CompanyAdmin, UserRole::Superadmin])) {
                if ($ticket->first_responded_at === null) {
                    $ticket->first_responded_at = now();
                    if ($ticket->first_response_due_at && now()->greaterThan($ticket->first_response_due_at)) {
                        $ticket->is_sla_breached = true;
                    }
                }
            }

            $ticket->replies_count = ($ticket->replies_count ?? 0) + 1;
            $ticket->last_reply_at = now();
        }

        // Transisi status tiket jika ditentukan
        if (! empty($validated['status'])) {
            $newStatus = TicketStatus::tryFrom($validated['status']);
            if ($newStatus && $newStatus !== $ticket->status) {
                $oldStatus = $ticket->status;
                $ticket->status = $newStatus;

                if ($newStatus === TicketStatus::Resolved && $ticket->resolved_at === null) {
                    $ticket->resolved_at = now();
                    if ($ticket->resolution_due_at && now()->greaterThan($ticket->resolution_due_at)) {
                        $ticket->is_sla_breached = true;
                    }
                } elseif ($newStatus === TicketStatus::Closed && $ticket->closed_at === null) {
                    $ticket->closed_at = now();
                }

                TicketActivity::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => $userId,
                    'activity_type' => 'status_changed',
                    'old_value' => $oldStatus->value,
                    'new_value' => $newStatus->value,
                    'notes' => 'Status tiket diperbarui bersamaan dengan pengiriman balasan.',
                ]);
            }
        }

        $ticket->save();

        // Catat aktivitas pengiriman pesan
        TicketActivity::create([
            'ticket_id' => $ticket->id,
            'user_id' => $userId,
            'activity_type' => $isInternalNote ? 'internal_note_added' : 'reply_added',
            'notes' => $isInternalNote
                ? 'Menambahkan catatan internal rahasia teknisi.'
                : 'Mengirimkan balasan ke tiket.',
        ]);

        return response()->json([
            'success' => true,
            'message' => $isInternalNote
                ? 'Catatan internal berhasil disimpan.'
                : 'Balasan berhasil dikirimkan.',
            'data' => $message->load(['user:id,name,email,role,job_title,avatar_path', 'attachments']),
        ], 201);
    }

    /**
     * Unduh berkas lampiran tiket.
     */
    public function downloadAttachment(string $id): StreamedResponse|JsonResponse
    {
        $attachment = TicketAttachment::findOrFail($id);

        if (! Storage::disk('public')->exists($attachment->file_path)) {
            return response()->json([
                'success' => false,
                'message' => 'Berkas lampiran tidak ditemukan pada penyimpanan server.',
            ], 404);
        }

        return Storage::disk('public')->download($attachment->file_path, $attachment->file_name);
    }
}
