<?php

namespace App\Http\Controllers\Api\V1\Master;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Department;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DepartmentController extends Controller
{
    public function index(Request $request, ?string $tenant = null): JsonResponse
    {
        $companyId = $this->resolveCompanyId($request, $tenant);

        $query = Department::query()
            ->with(['company:id,name,slug', 'leadUser:id,name,email,job_title'])
            ->withCount(['users', 'tickets', 'assets']);

        if ($companyId !== null) {
            $query->where('company_id', $companyId);
        }

        if ($request->filled('search')) {
            $searchTerm = $request->query('search');
            $query->where('name', 'like', "%{$searchTerm}%");
        }

        if ($request->has('is_active') && $request->query('is_active') !== '') {
            $isActive = filter_var($request->query('is_active'), FILTER_VALIDATE_BOOLEAN);
            $query->where('is_active', $isActive);
        }

        $sortBy = $request->query('sort_by', 'name');
        $sortOrder = $request->query('sort_order', 'asc');
        if (in_array($sortBy, ['id', 'name', 'created_at'], true)) {
            $query->orderBy($sortBy, $sortOrder === 'desc' ? 'desc' : 'asc');
        }

        if ($request->query('paginate') === 'false') {
            $departments = $query->get();

            return response()->json([
                'success' => true,
                'message' => 'Daftar semua departemen berhasil diambil.',
                'data' => $departments,
            ], 200);
        }

        $perPage = (int) $request->query('per_page', 10);
        $departments = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Daftar departemen berhasil diambil.',
            'data' => $departments->items(),
            'meta' => [
                'current_page' => $departments->currentPage(),
                'last_page' => $departments->lastPage(),
                'per_page' => $departments->perPage(),
                'total' => $departments->total(),
            ],
        ], 200);
    }

    public function store(Request $request, ?string $tenant = null): JsonResponse
    {
        $companyId = $this->resolveCompanyId($request, $tenant);

        $validated = $request->validate([
            'company_id' => [
                $companyId !== null ? 'nullable' : 'required',
                'exists:companies,id',
            ],
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'lead_user_id' => [
                'nullable',
                Rule::exists('users', 'id')->where(function ($query) use ($companyId, $request) {
                    $targetCompanyId = $companyId ?? $request->input('company_id');
                    if ($targetCompanyId !== null) {
                        $query->where('company_id', $targetCompanyId);
                    }
                }),
            ],
            'is_active' => 'sometimes|boolean',
        ], [
            'company_id.required' => 'ID Perusahaan (company_id) wajib diisi.',
            'company_id.exists' => 'Perusahaan tidak ditemukan dalam sistem.',
            'name.required' => 'Nama departemen wajib diisi.',
            'name.max' => 'Nama departemen maksimal 100 karakter.',
            'lead_user_id.exists' => 'User lead tidak ditemukan atau bukan karyawan di perusahaan ini.',
        ]);

        if ($companyId !== null) {
            $validated['company_id'] = $companyId;
        }
        if (! isset($validated['is_active'])) {
            $validated['is_active'] = true;
        }

        $department = Department::create($validated);
        $department->load(['company:id,name,slug', 'leadUser:id,name,email,job_title']);

        return response()->json([
            'success' => true,
            'message' => 'Departemen berhasil ditambahkan.',
            'data' => $department,
        ], 201);
    }

    public function show(Request $request, string $id, ?string $departmentId = null): JsonResponse
    {
        $targetId = $departmentId ?? $id;
        $tenantSlug = $departmentId !== null ? $id : null;
        $companyId = $this->resolveCompanyId($request, $tenantSlug);

        $department = Department::query()
            ->with(['company:id,name,slug', 'leadUser:id,name,email,job_title,phone'])
            ->withCount(['users', 'tickets', 'assets', 'ticketCategories'])
            ->when($companyId !== null, fn ($q) => $q->where('company_id', $companyId))
            ->find($targetId);

        if ($department === null) {
            return response()->json([
                'success' => false,
                'message' => 'Departemen tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail departemen berhasil diambil.',
            'data' => $department,
        ], 200);
    }

    public function update(Request $request, string $id, ?string $departmentId = null): JsonResponse
    {
        $targetId = $departmentId ?? $id;
        $tenantSlug = $departmentId !== null ? $id : null;
        $companyId = $this->resolveCompanyId($request, $tenantSlug);

        $department = Department::query()
            ->when($companyId !== null, fn ($q) => $q->where('company_id', $companyId))
            ->find($targetId);

        if ($department === null) {
            return response()->json([
                'success' => false,
                'message' => 'Departemen tidak ditemukan.',
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:100',
            'description' => 'nullable|string',
            'lead_user_id' => [
                'nullable',
                Rule::exists('users', 'id')->where(function ($query) use ($department) {
                    $query->where('company_id', $department->company_id);
                }),
            ],
            'is_active' => 'sometimes|boolean',
        ], [
            'name.required' => 'Nama departemen tidak boleh kosong jika diisi.',
            'name.max' => 'Nama departemen maksimal 100 karakter.',
            'lead_user_id.exists' => 'User lead tidak ditemukan atau bukan anggota perusahaan ini.',
        ]);

        $department->update($validated);
        $department->load(['company:id,name,slug', 'leadUser:id,name,email,job_title']);

        return response()->json([
            'success' => true,
            'message' => 'Data departemen berhasil diperbarui.',
            'data' => $department,
        ], 200);
    }

    public function destroy(Request $request, string $id, ?string $departmentId = null): JsonResponse
    {
        $targetId = $departmentId ?? $id;
        $tenantSlug = $departmentId !== null ? $id : null;
        $companyId = $this->resolveCompanyId($request, $tenantSlug);

        $department = Department::query()
            ->when($companyId !== null, fn ($q) => $q->where('company_id', $companyId))
            ->find($targetId);

        if ($department === null) {
            return response()->json([
                'success' => false,
                'message' => 'Departemen tidak ditemukan.',
            ], 404);
        }

        if ($department->tickets()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Departemen tidak dapat dihapus karena memiliki tiket terkait. Nonaktifkan departemen (is_active = false) sebagai gantinya.',
            ], 422);
        }

        $department->delete();

        return response()->json([
            'success' => true,
            'message' => 'Departemen berhasil dihapus.',
        ], 200);
    }

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
