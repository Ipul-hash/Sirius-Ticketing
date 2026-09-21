<?php

namespace App\Http\Controllers\Api\V1\Master;

use App\Enums\AssetCategory;
use App\Enums\AssetStatus;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\CompanyAsset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CompanyAssetController extends Controller
{
    public function index(Request $request, ?string $tenant = null): JsonResponse
    {
        $companyId = $this->resolveCompanyId($request, $tenant);

        $query = CompanyAsset::query()
            ->with([
                'company:id,name,slug',
                'department:id,name',
                'assignedUser:id,name,email,job_title',
            ])
            ->withCount('tickets');

        if ($companyId !== null) {
            $query->where('company_id', $companyId);
        }

        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('asset_tag', 'like', "%{$search}%")
                    ->orWhere('serial_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->query('category'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->query('department_id'));
        }

        if ($request->filled('assigned_to_user_id')) {
            $query->where('assigned_to_user_id', $request->query('assigned_to_user_id'));
        }

        $perPage = (int) $request->query('per_page', 10);
        $assets = $query->orderBy('name')->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Daftar inventaris aset berhasil diambil.',
            'data' => $assets->items(),
            'meta' => [
                'current_page' => $assets->currentPage(),
                'last_page' => $assets->lastPage(),
                'per_page' => $assets->perPage(),
                'total' => $assets->total(),
            ],
        ], 200);
    }

    public function store(Request $request, ?string $tenant = null): JsonResponse
    {
        $companyId = $this->resolveCompanyId($request, $tenant);
        if ($companyId === null) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant perusahaan tidak ditemukan atau belum dipilih.',
            ], 422);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'asset_tag' => [
                'required',
                'string',
                'max:50',
                Rule::unique('company_assets', 'asset_tag')->where('company_id', $companyId),
            ],
            'serial_number' => 'nullable|string|max:100',
            'category' => ['required', Rule::enum(AssetCategory::class)],
            'status' => ['nullable', Rule::enum(AssetStatus::class)],
            'department_id' => [
                'nullable',
                Rule::exists('departments', 'id')->where('company_id', $companyId),
            ],
            'assigned_to_user_id' => [
                'nullable',
                Rule::exists('users', 'id')->where('company_id', $companyId),
            ],
            'notes' => 'nullable|string',
        ]);

        $validated['company_id'] = $companyId;
        $validated['status'] = $validated['status'] ?? AssetStatus::InUse;

        $asset = CompanyAsset::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Aset inventaris berhasil ditambahkan.',
            'data' => $asset->load(['company:id,name,slug', 'department:id,name', 'assignedUser:id,name,email,job_title']),
        ], 201);
    }

    public function show(Request $request, string $id, ?string $assetId = null): JsonResponse
    {
        $targetId = $assetId ?? $id;
        $tenantSlug = $assetId !== null ? $id : null;
        $companyId = $this->resolveCompanyId($request, $tenantSlug);

        $query = CompanyAsset::query()
            ->with(['company:id,name,slug', 'department:id,name', 'assignedUser:id,name,email,job_title'])
            ->withCount('tickets');

        if ($companyId !== null) {
            $query->where('company_id', $companyId);
        }

        $asset = $query->findOrFail($targetId);

        return response()->json([
            'success' => true,
            'message' => 'Detail aset berhasil diambil.',
            'data' => $asset,
        ], 200);
    }

    public function update(Request $request, string $id, ?string $assetId = null): JsonResponse
    {
        $targetId = $assetId ?? $id;
        $tenantSlug = $assetId !== null ? $id : null;
        $companyId = $this->resolveCompanyId($request, $tenantSlug);

        $query = CompanyAsset::query();
        if ($companyId !== null) {
            $query->where('company_id', $companyId);
        }

        $asset = $query->findOrFail($targetId);
        $targetCompanyId = $asset->company_id;

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:150',
            'asset_tag' => [
                'sometimes',
                'required',
                'string',
                'max:50',
                Rule::unique('company_assets', 'asset_tag')
                    ->where('company_id', $targetCompanyId)
                    ->ignore($asset->id),
            ],
            'serial_number' => 'nullable|string|max:100',
            'category' => ['sometimes', 'required', Rule::enum(AssetCategory::class)],
            'status' => ['sometimes', 'required', Rule::enum(AssetStatus::class)],
            'department_id' => [
                'nullable',
                Rule::exists('departments', 'id')->where('company_id', $targetCompanyId),
            ],
            'assigned_to_user_id' => [
                'nullable',
                Rule::exists('users', 'id')->where('company_id', $targetCompanyId),
            ],
            'notes' => 'nullable|string',
        ]);

        $asset->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data aset inventaris berhasil diperbarui.',
            'data' => $asset->load(['company:id,name,slug', 'department:id,name', 'assignedUser:id,name,email,job_title']),
        ], 200);
    }

    public function destroy(Request $request, string $id, ?string $assetId = null): JsonResponse
    {
        $targetId = $assetId ?? $id;
        $tenantSlug = $assetId !== null ? $id : null;
        $companyId = $this->resolveCompanyId($request, $tenantSlug);

        $query = CompanyAsset::query();
        if ($companyId !== null) {
            $query->where('company_id', $companyId);
        }

        $asset = $query->findOrFail($targetId);

        if ($asset->tickets()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Aset tidak dapat dihapus karena masih terhubung ke riwayat tiket. Ubah status aset menjadi "retired" sebagai gantinya.',
            ], 422);
        }

        $asset->delete();

        return response()->json([
            'success' => true,
            'message' => 'Aset berhasil dihapus dari inventaris.',
        ], 200);
    }

    protected function resolveCompanyId(Request $request, ?string $tenant = null): ?int
    {
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

        if ($request->user() !== null && $request->user()->company_id !== null) {
            return (int) $request->user()->company_id;
        }

        return null;
    }
}
