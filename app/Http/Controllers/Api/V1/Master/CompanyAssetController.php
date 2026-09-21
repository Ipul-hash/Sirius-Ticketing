<?php

namespace App\Http\Controllers\Api\V1\Master;

use App\Http\Controllers\Controller;
use App\Models\CompanyAsset;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CompanyAssetController extends Controller
{
    public function index(Request $request, $tenant)
    {
        $query = CompanyAsset::query();

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('assigned_to_user_id')) {
            $query->where('assigned_to_user_id', $request->assigned_to_user_id);
        }

        $query->with(['department', 'user']);

        return response()->json($query->paginate(10));
    }

    public function store(Request $request, $tenant)
    {
        $validated = $request->validate([
            'asset_tag'           => 'required|string|unique:company_assets,asset_tag',
            'serial_number'       => 'nullable|string|max:255',
            'specifications'      => 'nullable|string',
            'category'            => 'required|string',
            'department_id'       => 'nullable|exists:departments,id',
            'assigned_to_user_id' => 'nullable|exists:users,id',
        ]);

        $validated['status'] = 'in_use'; 

        $asset = CompanyAsset::create($validated);

        return response()->json([
            'message' => 'Aset berhasil ditambahkan',
            'data'    => $asset
        ], 201);
    }

    public function show($tenant, $id)
    {
        $asset = CompanyAsset::with(['company', 'department', 'user', 'tickets'])->findOrFail($id);
        
        return response()->json($asset);
    }

    public function update(Request $request, $tenant, $id)
    {
        $asset = CompanyAsset::findOrFail($id);

        $validated = $request->validate([
            'status' => ['required', Rule::in(['in_use', 'maintenance', 'retired'])],
        ]);

        $asset->update(['status' => $validated['status']]);

        return response()->json([
            'message' => 'Status aset berhasil diperbarui',
            'data'    => $asset
        ]);
    }

    public function destroy($tenant, $id)
    {
        $asset = CompanyAsset::findOrFail($id);
        $asset->delete();

        return response()->json([
            'message' => 'Aset berhasil dihapus dari inventaris'
        ]);
    }
}