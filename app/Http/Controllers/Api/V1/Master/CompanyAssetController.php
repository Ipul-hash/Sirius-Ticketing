<?php

namespace App\Http\Controllers\Api\V1\Master;

use App\Http\Controllers\Controller;
use App\Models\CompanyAsset;
use App\Enums\AssetCategory;
use App\Enums\AssetStatus;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CompanyAssetController extends Controller
{
    public function index(Request $request, $tenant)
    {
        $query = CompanyAsset::where('company_id', $tenant);

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('assigned_to_user_id')) {
            $query->where('assigned_to_user_id', $request->assigned_to_user_id);
        }

        $query->with(['department', 'assignedUser']);

        return response()->json($query->paginate(10));
    }

    public function store(Request $request, $tenant)
    {
        $validated = $request->validate([
            'name'                => 'required|string|max:255',
            'asset_tag'           => 'required|string|unique:company_assets,asset_tag',
            'serial_number'       => 'nullable|string|max:255',
            'category'            => ['required', Rule::enum(AssetCategory::class)],
            'status'              => ['nullable', Rule::enum(AssetStatus::class)],
            'department_id'       => 'nullable|exists:departments,id',
            'assigned_to_user_id' => 'nullable|exists:users,id',
            'notes'               => 'nullable|string',
        ]);

        $validated['company_id'] = $tenant;
        $validated['status'] = $validated['status'] ?? AssetStatus::IN_USE;

        $asset = CompanyAsset::create($validated);

        return response()->json([
            'message' => 'Aset berhasil ditambahkan',
            'data'    => $asset
        ], 201);
    }

    public function show($tenant, $id)
    {
        $asset = CompanyAsset::where('company_id', $tenant)
            ->with(['company', 'department', 'assignedUser', 'tickets'])
            ->findOrFail($id);
        
        return response()->json($asset);
    }

    public function update(Request $request, $tenant, $id)
    {
        $asset = CompanyAsset::where('company_id', $tenant)->findOrFail($id);

        $validated = $request->validate([
            'name'                => 'sometimes|required|string|max:255',
            'serial_number'       => 'nullable|string|max:255',
            'category'            => ['sometimes', 'required', Rule::enum(AssetCategory::class)],
            'status'              => ['sometimes', 'required', Rule::enum(AssetStatus::class)],
            'department_id'       => 'nullable|exists:departments,id',
            'assigned_to_user_id' => 'nullable|exists:users,id',
            'notes'               => 'nullable|string',
        ]);

        $asset->update($validated);

        return response()->json([
            'message' => 'Data aset berhasil diperbarui',
            'data'    => $asset
        ]);
    }

    public function destroy($tenant, $id)
    {
        $asset = CompanyAsset::where('company_id', $tenant)->findOrFail($id);
        $asset->delete();

        return response()->json([
            'message' => 'Aset berhasil dihapus dari inventaris'
        ]);
    }
}