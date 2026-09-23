<?php

namespace App\Http\Controllers\Api\V1\Ticket;

use App\Enums\ApprovalStatus;
use App\Enums\TicketApprovalStatus;
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Department;
use App\Models\SlaPolicy;
use App\Models\Ticket;
use App\Models\TicketActivity;
use App\Models\TicketApproval;
use App\Models\TicketCategory;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TicketController extends Controller
{
    /**
     * Menampilkan daftar tiket dengan berbagai filter dan antrean.
     */
    public function index(Request $request, ?string $tenant = null): JsonResponse
    {
        $companyId = $this->resolveCompanyId($request, $tenant);

        $query = Ticket::query()
            ->with([
                'company:id,name,slug',
                'category:id,name,requires_approval',
                'department:id,name',
                'requester:id,name,email,avatar_path',
                'assignedAgent:id,name,email,job_title,avatar_path',
                'asset:id,name,asset_tag,category',
            ]);

        if ($companyId !== null) {
            $query->where('company_id', $companyId);
        }

        // Quick Queue Tabs
        if ($request->filled('tab')) {
            $tab = $request->query('tab');
            if ($tab === 'unassigned') {
                $query->whereNull('assigned_to')
                    ->whereNotIn('status', [TicketStatus::Resolved, TicketStatus::Closed]);
            } elseif ($tab === 'my_tickets') {
                $userId = $request->user()?->id ?? $request->query('user_id');
                if ($userId !== null) {
                    $query->where('assigned_to', $userId);
                }
            } elseif ($tab === 'pending') {
                $query->whereIn('status', [TicketStatus::PendingApproval, TicketStatus::PendingUser]);
            } elseif ($tab === 'overdue') {
                $query->where(function ($q) {
                    $q->where('is_sla_breached', true)
                        ->orWhere(function ($sub) {
                            $sub->whereNull('resolved_at')
                                ->where('resolution_due_at', '<', now());
                        });
                });
            }
        }

        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->query('priority'));
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->query('category_id'));
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->query('department_id'));
        }

        if ($request->filled('assigned_to')) {
            $assignedTo = $request->query('assigned_to');
            if ($assignedTo === 'unassigned' || $assignedTo === 'null') {
                $query->whereNull('assigned_to');
            } else {
                $query->where('assigned_to', $assignedTo);
            }
        }

        if ($request->filled('requester_id')) {
            $query->where('requester_id', $request->query('requester_id'));
        }

        if ($request->filled('is_sla_breached')) {
            $isBreached = filter_var($request->query('is_sla_breached'), FILTER_VALIDATE_BOOLEAN);
            $query->where('is_sla_breached', $isBreached);
        }

        $sortBy = $request->query('sort_by', 'created_at');
        $sortOrder = $request->query('sort_order', 'desc');
        if (in_array($sortBy, ['id', 'ticket_number', 'created_at', 'priority', 'status', 'resolution_due_at'], true)) {
            $query->orderBy($sortBy, $sortOrder === 'desc' ? 'desc' : 'asc');
        }

        if ($request->query('paginate') === 'false') {
            $tickets = $query->get();

            return response()->json([
                'success' => true,
                'message' => 'Daftar semua tiket berhasil dimuat.',
                'data' => $tickets,
            ], 200);
        }

        $perPage = (int) $request->query('per_page', 10);
        $tickets = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Daftar tiket berhasil dimuat.',
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
     * Membuat tiket baru dengan penomoran otomatis dan kalkulasi SLA.
     */
    public function store(Request $request, ?string $tenant = null): JsonResponse
    {
        $companyId = $this->resolveCompanyId($request, $tenant);

        $authUser = $request->user();
        $isRequester = $authUser?->isRequester() ?? false;

        $validated = $request->validate([
            'company_id' => [
                $companyId !== null ? 'nullable' : 'required',
                'integer',
                'exists:companies,id',
            ],
            'subject' => 'required|string|max:200',
            'description' => 'required|string',
            'category_id' => [
                'required',
                'integer',
                Rule::exists('ticket_categories', 'id')->where(function ($query) use ($companyId, $request) {
                    $targetCompanyId = $companyId ?? $request->input('company_id');
                    if ($targetCompanyId !== null) {
                        $query->where('company_id', $targetCompanyId);
                    }
                }),
            ],
            'department_id' => [
                'nullable',
                'integer',
                Rule::exists('departments', 'id')->where(function ($query) use ($companyId, $request) {
                    $targetCompanyId = $companyId ?? $request->input('company_id');
                    if ($targetCompanyId !== null) {
                        $query->where('company_id', $targetCompanyId);
                    }
                }),
            ],
            'priority' => ['nullable', Rule::enum(TicketPriority::class)],
            'requester_id' => [
                $isRequester ? 'nullable' : 'required',
                'integer',
                'exists:users,id',
            ],
            'assigned_to' => 'nullable|integer|exists:users,id',
            'asset_id' => [
                'nullable',
                'integer',
                Rule::exists('company_assets', 'id')->where(function ($query) use ($companyId, $request) {
                    $targetCompanyId = $companyId ?? $request->input('company_id');
                    if ($targetCompanyId !== null) {
                        $query->where('company_id', $targetCompanyId);
                    }
                }),
            ],
        ]);

        if ($isRequester && $authUser !== null) {
            $validated['requester_id'] = $authUser->id;
            $validated['assigned_to'] = null;
        }

        if ($authUser?->isAgent()) {
            if (! empty($validated['assigned_to']) && (int) $validated['assigned_to'] !== $authUser->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses ditolak. Sebagai Teknisi/Agent, Anda hanya dapat mengambil tiket untuk diri sendiri atau membiarkannya unassigned.',
                ], 403);
            }
        }

        $finalCompanyId = $companyId ?? (int) $validated['company_id'];

        // Ambil data kategori untuk mewarisi departemen, prioritas default, dan cek kebutuhan approval
        $category = TicketCategory::find($validated['category_id']);
        $departmentId = ! empty($validated['department_id']) ? (int) $validated['department_id'] : $category->department_id;
        $priorityValue = ! empty($validated['priority']) ? $validated['priority'] : ($category->default_priority->value ?? TicketPriority::Medium->value);

        // Generate nomor tiket berurutan TCK-YYYY-XXXXX per tenant
        $ticketNumber = $this->generateTicketNumber($finalCompanyId);

        // Kalkulasi Otomatis Deadline SLA (First Response & Resolution)
        $slaPolicy = SlaPolicy::where('company_id', $finalCompanyId)
            ->where('priority', $priorityValue)
            ->first();

        if ($slaPolicy !== null) {
            $firstResponseDueAt = now()->addMinutes($slaPolicy->first_response_time_minutes);
            $resolutionDueAt = now()->addMinutes($slaPolicy->resolution_time_minutes);
        } else {
            // Fallback SLA bawaan jika tenant belum mengatur matriks SLA
            $defaultMinutes = match ($priorityValue) {
                TicketPriority::Urgent->value, 'urgent' => ['resp' => 30, 'res' => 240],
                TicketPriority::High->value, 'high' => ['resp' => 120, 'res' => 480],
                TicketPriority::Medium->value, 'medium' => ['resp' => 240, 'res' => 1440],
                default => ['resp' => 480, 'res' => 2880],
            };
            $firstResponseDueAt = now()->addMinutes($defaultMinutes['resp']);
            $resolutionDueAt = now()->addMinutes($defaultMinutes['res']);
        }

        // Logika Approval ITIL
        $requiresApproval = (bool) ($category->requires_approval ?? false);
        $initialStatus = $requiresApproval ? TicketStatus::PendingApproval : TicketStatus::Open;
        $initialApprovalStatus = $requiresApproval ? TicketApprovalStatus::Pending : TicketApprovalStatus::None;

        $ticket = Ticket::create([
            'company_id' => $finalCompanyId,
            'ticket_number' => $ticketNumber,
            'subject' => trim($validated['subject']),
            'description' => $validated['description'],
            'category_id' => (int) $validated['category_id'],
            'department_id' => $departmentId,
            'requester_id' => (int) $validated['requester_id'],
            'assigned_to' => ! empty($validated['assigned_to']) ? (int) $validated['assigned_to'] : null,
            'asset_id' => ! empty($validated['asset_id']) ? (int) $validated['asset_id'] : null,
            'status' => $initialStatus,
            'priority' => $priorityValue,
            'approval_status' => $initialApprovalStatus,
            'first_response_due_at' => $firstResponseDueAt,
            'resolution_due_at' => $resolutionDueAt,
            'is_sla_breached' => false,
            'replies_count' => 0,
        ]);

        // Buat Catatan Persetujuan (TicketApproval) jika Kategori Memerlukan Approval
        if ($requiresApproval) {
            $dept = $departmentId ? Department::find($departmentId) : null;
            $approverId = $dept?->lead_user_id
                ?? User::where('company_id', $finalCompanyId)
                    ->whereIn('role', [UserRole::CompanyAdmin, UserRole::Superadmin])
                    ->value('id')
                ?? 1;

            TicketApproval::create([
                'ticket_id' => $ticket->id,
                'approver_id' => $approverId,
                'status' => ApprovalStatus::Pending,
            ]);

            TicketActivity::create([
                'ticket_id' => $ticket->id,
                'user_id' => (int) $validated['requester_id'],
                'activity_type' => 'approval_requested',
                'notes' => 'Tiket membutuhkan persetujuan resmi sebelum dikerjakan.',
            ]);
        }

        // Rekam Jejak Audit Trail (TicketActivity)
        TicketActivity::create([
            'ticket_id' => $ticket->id,
            'user_id' => (int) $validated['requester_id'],
            'activity_type' => 'ticket_created',
            'old_value' => null,
            'new_value' => $ticket->status->value,
            'notes' => 'Tiket baru berhasil dibuat oleh pelapor.',
        ]);

        if (! empty($validated['assigned_to'])) {
            TicketActivity::create([
                'ticket_id' => $ticket->id,
                'user_id' => (int) $validated['requester_id'],
                'activity_type' => 'assigned_agent',
                'old_value' => 'Unassigned',
                'new_value' => (string) $validated['assigned_to'],
                'notes' => 'Teknisi langsung ditugaskan saat pembuatan tiket.',
            ]);
        }

        $ticket->load([
            'company:id,name,slug',
            'category:id,name,requires_approval',
            'department:id,name',
            'requester:id,name,email',
            'assignedAgent:id,name,email',
            'asset:id,name,asset_tag',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tiket baru berhasil dibuat dengan nomor '.$ticketNumber,
            'data' => $ticket,
        ], 201);
    }

    /**
     * Menampilkan detail lengkap satu tiket.
     */
    public function show(Request $request, string $param1, ?string $param2 = null): JsonResponse
    {
        $tenant = $param2 !== null ? $param1 : null;
        $targetId = $param2 !== null ? $param2 : $param1;

        $companyId = $this->resolveCompanyId($request, $tenant);

        $ticket = Ticket::query()
            ->with([
                'company:id,name,slug',
                'category:id,name,requires_approval,default_priority',
                'department:id,name',
                'requester:id,name,email,job_title,phone,avatar_path',
                'assignedAgent:id,name,email,job_title,avatar_path',
                'asset:id,name,asset_tag,category,serial_number,status',
                'activities' => fn ($q) => $q->with('user:id,name,email')->latest(),
                'approvals.approver:id,name,email',
                'messages.user:id,name,email,role',
                'attachments',
            ])
            ->when($companyId !== null, fn ($q) => $q->where('company_id', $companyId))
            ->where(function ($q) use ($targetId) {
                $q->where('id', $targetId)
                    ->orWhere('ticket_number', $targetId);
            })
            ->first();

        if ($ticket === null) {
            return response()->json([
                'success' => false,
                'message' => 'Tiket tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail tiket berhasil diambil.',
            'data' => $ticket,
        ], 200);
    }

    /**
     * Memperbarui informasi umum tiket.
     */
    public function update(Request $request, string $param1, ?string $param2 = null): JsonResponse
    {
        $tenant = $param2 !== null ? $param1 : null;
        $targetId = $param2 !== null ? $param2 : $param1;

        $companyId = $this->resolveCompanyId($request, $tenant);

        $ticket = Ticket::query()
            ->when($companyId !== null, fn ($q) => $q->where('company_id', $companyId))
            ->where(function ($q) use ($targetId) {
                $q->where('id', $targetId)
                    ->orWhere('ticket_number', $targetId);
            })
            ->first();

        if ($ticket === null) {
            return response()->json([
                'success' => false,
                'message' => 'Tiket tidak ditemukan.',
            ], 404);
        }

        $validated = $request->validate([
            'subject' => 'sometimes|required|string|max:200',
            'description' => 'sometimes|required|string',
            'category_id' => [
                'sometimes',
                'required',
                'integer',
                Rule::exists('ticket_categories', 'id')->where('company_id', $ticket->company_id),
            ],
            'department_id' => [
                'sometimes',
                'required',
                'integer',
                Rule::exists('departments', 'id')->where('company_id', $ticket->company_id),
            ],
            'priority' => ['sometimes', 'required', Rule::enum(TicketPriority::class)],
            'asset_id' => [
                'nullable',
                'integer',
                Rule::exists('company_assets', 'id')->where('company_id', $ticket->company_id),
            ],
        ]);

        // Cek perubahan prioritas untuk audit log
        if (isset($validated['priority']) && $validated['priority'] !== $ticket->priority->value) {
            TicketActivity::create([
                'ticket_id' => $ticket->id,
                'user_id' => $request->user()?->id,
                'activity_type' => 'priority_changed',
                'old_value' => $ticket->priority->value,
                'new_value' => $validated['priority'],
                'notes' => 'Prioritas tiket diubah.',
            ]);
        }

        $ticket->update($validated);
        $ticket->load(['category', 'department', 'asset']);

        return response()->json([
            'success' => true,
            'message' => 'Data tiket berhasil diperbarui.',
            'data' => $ticket,
        ], 200);
    }

    /**
     * Memperbarui status tiket (Open, In Progress, Pending, Resolved, Closed).
     */
    public function updateStatus(Request $request, string $param1, ?string $param2 = null): JsonResponse
    {
        $tenant = $param2 !== null ? $param1 : null;
        $targetId = $param2 !== null ? $param2 : $param1;

        $companyId = $this->resolveCompanyId($request, $tenant);

        $ticket = Ticket::query()
            ->when($companyId !== null, fn ($q) => $q->where('company_id', $companyId))
            ->where(function ($q) use ($targetId) {
                $q->where('id', $targetId)
                    ->orWhere('ticket_number', $targetId);
            })
            ->first();

        if ($ticket === null) {
            return response()->json([
                'success' => false,
                'message' => 'Tiket tidak ditemukan.',
            ], 404);
        }

        $validated = $request->validate([
            'status' => ['required', Rule::enum(TicketStatus::class)],
            'notes' => 'nullable|string',
        ]);

        $oldStatus = $ticket->status->value;
        $newStatus = $validated['status'];

        // Pengguna Requester tidak berwenang mengubah tiket ke status teknis/resolve
        if ($request->user()?->isRequester() && in_array($newStatus, [TicketStatus::Resolved->value, TicketStatus::InProgress->value, TicketStatus::PendingApproval->value], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Pengguna dengan peran Pemohon (Requester) tidak berwenang mengubah status tiket menjadi '.$newStatus.'.',
            ], 403);
        }

        // Cegah transisi status ke aktif jika tiket masih berstatus Pending Approval
        if (($ticket->status === TicketStatus::PendingApproval || $ticket->approval_status === TicketApprovalStatus::Pending)
            && in_array($newStatus, [TicketStatus::Open->value, TicketStatus::InProgress->value, TicketStatus::Resolved->value])) {
            return response()->json([
                'success' => false,
                'message' => 'Tiket ini masih berstatus Pending Approval. Mohon setujui (approve) tiket oleh atasan terkait terlebih dahulu sebelum mengubah status operasional.',
            ], 422);
        }

        $updateData = ['status' => $newStatus];

        if ($newStatus === TicketStatus::Resolved->value && $ticket->resolved_at === null) {
            $updateData['resolved_at'] = now();
        }

        if ($newStatus === TicketStatus::Closed->value && $ticket->closed_at === null) {
            $updateData['closed_at'] = now();
            if ($ticket->resolved_at === null) {
                $updateData['resolved_at'] = now();
            }
        }

        $ticket->update($updateData);

        TicketActivity::create([
            'ticket_id' => $ticket->id,
            'user_id' => $request->user()?->id,
            'activity_type' => 'status_changed',
            'old_value' => $oldStatus,
            'new_value' => $newStatus,
            'notes' => $validated['notes'] ?? 'Status tiket diperbarui.',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status tiket berhasil diubah menjadi '.$newStatus,
            'data' => $ticket,
        ], 200);
    }

    /**
     * Menugaskan tiket ke teknisi tertentu.
     */
    public function assign(Request $request, string $param1, ?string $param2 = null): JsonResponse
    {
        $tenant = $param2 !== null ? $param1 : null;
        $targetId = $param2 !== null ? $param2 : $param1;

        $companyId = $this->resolveCompanyId($request, $tenant);

        $ticket = Ticket::query()
            ->when($companyId !== null, fn ($q) => $q->where('company_id', $companyId))
            ->where(function ($q) use ($targetId) {
                $q->where('id', $targetId)
                    ->orWhere('ticket_number', $targetId);
            })
            ->first();

        if ($ticket === null) {
            return response()->json([
                'success' => false,
                'message' => 'Tiket tidak ditemukan.',
            ], 404);
        }

        $authUser = $request->user();

        if ($authUser?->isRequester()) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Pengguna dengan peran Pemohon (Requester) tidak berwenang menugaskan teknisi.',
            ], 403);
        }

        if ($authUser?->isAgent()) {
            // Jika tiket sudah ditugaskan ke teknisi lain, tolak
            if ($ticket->assigned_to !== null && $ticket->assigned_to !== $authUser->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses ditolak. Tiket ini sudah ditugaskan kepada teknisi lain. Hanya Administrator yang dapat memindahkan penugasan tiket.',
                ], 403);
            }

            // Jika agent mencoba menugaskan ke staf/teknisi lain
            $requestedAgent = ! empty($request->input('assigned_to')) ? (int) $request->input('assigned_to') : $authUser->id;
            if ($requestedAgent !== $authUser->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses ditolak. Sebagai Teknisi/Agent, Anda hanya diperbolehkan mengambil tiket untuk diri sendiri (Ambil Tiket) dan tidak dapat menugaskan ke staf lain.',
                ], 403);
            }
        }

        $validated = $request->validate([
            'assigned_to' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id')->where(function ($query) use ($ticket) {
                    $query->where(function ($q) use ($ticket) {
                        $q->where('company_id', $ticket->company_id)
                            ->orWhereNull('company_id'); // Memungkinkan superadmin jika diperlukan
                    });
                }),
            ],
            'notes' => 'nullable|string',
        ]);

        $oldAgentId = $ticket->assigned_to;
        $newAgentId = ! empty($validated['assigned_to']) ? (int) $validated['assigned_to'] : ($authUser?->isAgent() ? $authUser->id : null);

        // Cegah penugasan teknisi jika tiket masih berstatus Pending Approval
        if (($ticket->status === TicketStatus::PendingApproval || $ticket->approval_status === TicketApprovalStatus::Pending) && $newAgentId !== null) {
            return response()->json([
                'success' => false,
                'message' => 'Tiket ini masih berstatus Pending Approval. Mohon setujui (approve) oleh atasan terkait terlebih dahulu sebelum menugaskan teknisi.',
            ], 422);
        }

        $updateData = ['assigned_to' => $newAgentId];

        // Jika sebelumnya berstatus open dan baru pertama kali ditugaskan, ubah ke in_progress
        if ($ticket->status === TicketStatus::Open && $newAgentId !== null) {
            $updateData['status'] = TicketStatus::InProgress;
        }

        $ticket->update($updateData);

        $isSelfClaim = $authUser?->isAgent() && $newAgentId === $authUser->id;

        TicketActivity::create([
            'ticket_id' => $ticket->id,
            'user_id' => $authUser?->id,
            'activity_type' => 'assigned_agent',
            'old_value' => $oldAgentId ? (string) $oldAgentId : 'Unassigned',
            'new_value' => $newAgentId ? (string) $newAgentId : 'Unassigned',
            'notes' => $validated['notes'] ?? ($isSelfClaim ? 'Tiket diambil secara mandiri oleh teknisi.' : ($newAgentId ? 'Tiket berhasil ditugaskan ke teknisi.' : 'Penugasan teknisi dibatalkan (Unassigned).')),
        ]);

        $ticket->load('assignedAgent:id,name,email,job_title');

        return response()->json([
            'success' => true,
            'message' => $isSelfClaim ? 'Tiket berhasil diambil dan ditugaskan kepada Anda.' : ($newAgentId ? 'Tiket berhasil ditugaskan ke teknisi.' : 'Tiket dikembalikan ke antrean belum ditugaskan.'),
            'data' => $ticket,
        ], 200);
    }

    /**
     * Menghapus tiket (Soft delete).
     */
    public function destroy(Request $request, string $param1, ?string $param2 = null): JsonResponse
    {
        $tenant = $param2 !== null ? $param1 : null;
        $targetId = $param2 !== null ? $param2 : $param1;

        $authUser = $request->user();
        if (! ($authUser?->isSuperadmin() || $authUser?->isCompanyAdmin())) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Hanya Administrator yang memiliki hak akses untuk menghapus tiket.',
            ], 403);
        }

        $companyId = $this->resolveCompanyId($request, $tenant);

        $ticket = Ticket::query()
            ->when($companyId !== null, fn ($q) => $q->where('company_id', $companyId))
            ->where(function ($q) use ($targetId) {
                $q->where('id', $targetId)
                    ->orWhere('ticket_number', $targetId);
            })
            ->first();

        if ($ticket === null) {
            return response()->json([
                'success' => false,
                'message' => 'Tiket tidak ditemukan.',
            ], 404);
        }

        TicketActivity::create([
            'ticket_id' => $ticket->id,
            'user_id' => $request->user()?->id,
            'activity_type' => 'ticket_deleted',
            'old_value' => $ticket->status->value,
            'new_value' => 'deleted',
            'notes' => 'Tiket dihapus oleh pengguna.',
        ]);

        $ticket->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tiket berhasil dihapus.',
        ], 200);
    }

    /**
     * Menghasilkan nomor tiket berurutan TCK-YYYY-XXXXX per tenant (aman dari duplikasi & soft-deletes).
     */
    protected function generateTicketNumber(int $companyId): string
    {
        $year = date('Y');
        $prefix = "TCK-{$year}-";

        // Menggunakan withTrashed() agar nomor dari tiket yang terhapus/diarsipkan tidak bentrok
        $latestTicket = Ticket::withTrashed()
            ->where('company_id', $companyId)
            ->where('ticket_number', 'like', "{$prefix}%")
            ->orderByRaw('CAST(SUBSTRING(ticket_number, -5) AS UNSIGNED) DESC')
            ->first();

        $sequence = 1;
        if ($latestTicket !== null) {
            $lastNumber = substr($latestTicket->ticket_number, -5);
            if (is_numeric($lastNumber)) {
                $sequence = (int) $lastNumber + 1;
            }
        }

        $candidate = sprintf('%s%05d', $prefix, $sequence);

        // Safety loop untuk memastikan nomor benar-benar unik di database
        while (Ticket::withTrashed()->where('company_id', $companyId)->where('ticket_number', $candidate)->exists()) {
            $sequence++;
            $candidate = sprintf('%s%05d', $prefix, $sequence);
        }

        return $candidate;
    }

    /**
     * Menyelesaikan company_id dari tenant URL slug/ID atau query/body request.
     */
    protected function resolveCompanyId(Request $request, ?string $tenant = null): ?int
    {
        $user = $request->user();
        if ($user !== null && ! $user->isSuperadmin() && $user->company_id !== null) {
            return (int) $user->company_id;
        }

        if ($tenant !== null && $tenant !== '') {
            $company = Company::where('slug', $tenant)
                ->orWhere('id', $tenant)
                ->first();

            if ($company !== null) {
                return (int) $company->id;
            }
        }

        if ($request->filled('company_id')) {
            return (int) $request->input('company_id');
        }

        if ($user !== null && $user->company_id !== null) {
            return (int) $user->company_id;
        }

        return null;
    }
}
