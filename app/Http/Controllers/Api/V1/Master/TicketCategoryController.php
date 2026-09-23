<?php

namespace App\Http\Controllers\Api\V1\Master;

use App\Enums\TicketPriority;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\TicketCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TicketCategoryController extends Controller
{
    /**
     * Menampilkan daftar kategori tiket dengan filter dan pagination.
     */
    public function index(Request $request, ?string $tenant = null): JsonResponse
    {
        $companyId = $this->resolveCompanyId($request, $tenant);

        $query = TicketCategory::query()
            ->with([
                'company:id,name,slug',
                'department:id,name,is_active',
            ])
            ->withCount('tickets');

        if ($companyId !== null) {
            $query->where('company_id', $companyId);
        }

        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->query('department_id'));
        }

        if ($request->filled('default_priority')) {
            $query->where('default_priority', $request->query('default_priority'));
        }

        if ($request->has('requires_approval') && $request->query('requires_approval') !== '') {
            $reqApproval = filter_var($request->query('requires_approval'), FILTER_VALIDATE_BOOLEAN);
            $query->where('requires_approval', $reqApproval);
        }

        if ($request->has('is_active') && $request->query('is_active') !== '') {
            $isActive = filter_var($request->query('is_active'), FILTER_VALIDATE_BOOLEAN);
            $query->where('is_active', $isActive);
        }

        $sortBy = $request->query('sort_by', 'name');
        $sortOrder = $request->query('sort_order', 'asc');
        if (in_array($sortBy, ['id', 'name', 'created_at', 'default_priority'], true)) {
            $query->orderBy($sortBy, $sortOrder === 'desc' ? 'desc' : 'asc');
        }

        if ($request->query('paginate') === 'false') {
            $categories = $query->get();

            return response()->json([
                'success' => true,
                'message' => 'Daftar semua kategori tiket berhasil dimuat.',
                'data' => $categories,
            ], 200);
        }

        $perPage = (int) $request->query('per_page', 10);
        $categories = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Daftar kategori tiket berhasil dimuat.',
            'data' => $categories->items(),
            'meta' => [
                'current_page' => $categories->currentPage(),
                'last_page' => $categories->lastPage(),
                'per_page' => $categories->perPage(),
                'total' => $categories->total(),
            ],
        ], 200);
    }

    /**
     * Menyimpan kategori tiket baru ke database.
     */
    public function store(Request $request, ?string $tenant = null): JsonResponse
    {
        $companyId = $this->resolveCompanyId($request, $tenant);

        $validated = $request->validate([
            'company_id' => [
                $companyId !== null ? 'nullable' : 'required',
                'integer',
                'exists:companies,id',
            ],
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'department_id' => [
                'required',
                'integer',
                Rule::exists('departments', 'id')->where(function ($query) use ($companyId, $request) {
                    $targetCompanyId = $companyId ?? $request->input('company_id');
                    if ($targetCompanyId !== null) {
                        $query->where('company_id', $targetCompanyId);
                    }
                }),
            ],
            'default_priority' => ['nullable', Rule::enum(TicketPriority::class)],
            'requires_approval' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $finalCompanyId = $companyId ?? (int) $validated['company_id'];

        $category = TicketCategory::create([
            'company_id' => $finalCompanyId,
            'department_id' => (int) $validated['department_id'],
            'name' => trim($validated['name']),
            'default_priority' => $validated['default_priority'] ?? TicketPriority::Medium->value,
            'requires_approval' => filter_var($request->input('requires_approval', false), FILTER_VALIDATE_BOOLEAN),
            'is_active' => filter_var($request->input('is_active', true), FILTER_VALIDATE_BOOLEAN),
        ]);

        $category->load(['company:id,name,slug', 'department:id,name']);

        return response()->json([
            'success' => true,
            'message' => 'Kategori tiket berhasil ditambahkan.',
            'data' => $category,
        ], 201);
    }

    /**
     * Menampilkan detail satu kategori tiket.
     */
    public function show(Request $request, string $param1, ?string $param2 = null): JsonResponse
    {
        $tenant = $param2 !== null ? $param1 : null;
        $targetId = $param2 !== null ? $param2 : $param1;

        $companyId = $this->resolveCompanyId($request, $tenant);

        $category = TicketCategory::query()
            ->with(['company:id,name,slug', 'department:id,name', 'tickets'])
            ->withCount('tickets')
            ->when($companyId !== null, fn ($q) => $q->where('company_id', $companyId))
            ->find($targetId);

        if ($category === null) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tiket tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail kategori tiket berhasil diambil.',
            'data' => $category,
        ], 200);
    }

    /**
     * Memperbarui data kategori tiket.
     */
    public function update(Request $request, string $param1, ?string $param2 = null): JsonResponse
    {
        $tenant = $param2 !== null ? $param1 : null;
        $targetId = $param2 !== null ? $param2 : $param1;

        $companyId = $this->resolveCompanyId($request, $tenant);

        $category = TicketCategory::query()
            ->when($companyId !== null, fn ($q) => $q->where('company_id', $companyId))
            ->find($targetId);

        if ($category === null) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tiket tidak ditemukan.',
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'department_id' => [
                'sometimes',
                'required',
                'integer',
                Rule::exists('departments', 'id')->where('company_id', $category->company_id),
            ],
            'default_priority' => ['sometimes', 'nullable', Rule::enum(TicketPriority::class)],
            'requires_approval' => 'sometimes|boolean',
            'is_active' => 'sometimes|boolean',
        ]);

        if (isset($validated['name'])) {
            $validated['name'] = trim($validated['name']);
        }

        $category->update($validated);
        $category->load(['company:id,name,slug', 'department:id,name']);

        return response()->json([
            'success' => true,
            'message' => 'Kategori tiket berhasil diperbarui.',
            'data' => $category,
        ], 200);
    }

    /**
     * Menghapus atau menonaktifkan kategori tiket.
     */
    public function destroy(Request $request, string $param1, ?string $param2 = null): JsonResponse
    {
        $tenant = $param2 !== null ? $param1 : null;
        $targetId = $param2 !== null ? $param2 : $param1;

        $companyId = $this->resolveCompanyId($request, $tenant);

        $category = TicketCategory::query()
            ->when($companyId !== null, fn ($q) => $q->where('company_id', $companyId))
            ->find($targetId);

        if ($category === null) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tiket tidak ditemukan.',
            ], 404);
        }

        // Cek apakah kategori sudah dipakai di tiket
        if ($category->tickets()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tiket tidak dapat dihapus karena memiliki tiket terkait. Anda dapat menonaktifkannya (is_active = false) sebagai gantinya.',
            ], 422);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kategori tiket berhasil dihapus.',
        ], 200);
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
