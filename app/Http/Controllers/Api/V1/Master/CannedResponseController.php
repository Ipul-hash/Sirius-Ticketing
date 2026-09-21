<?php

namespace App\Http\Controllers\Api\V1\Master;

use App\Http\Controllers\Controller;
use App\Models\CannedResponse;
use Illuminate\Http\Request;

class CannedResponseController extends Controller
{
    public function index(Request $request, $tenant)
    {
        $query = CannedResponse::where('company_id', $tenant);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('shortcut', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%");
            });
        }

        if ($request->filled('department_id')) {
            $query->where(function($q) use ($request) {
                $q->where('department_id', $request->department_id)
                  ->orWhereNull('department_id');
            });
        }

        $query->with(['company', 'department']);

        return response()->json([
            'message' => 'Daftar template balasan cepat berhasil dimuat',
            'data'    => $query->get()
        ]);
    }

    public function store(Request $request, $tenant)
    {
        $validated = $request->validate([
            'title'         => 'required|string|max:255',
            'shortcut'      => 'required|string|max:100',
            'message'       => 'required|string',
            'department_id' => 'nullable|exists:departments,id',
        ]);

        $validated['company_id'] = $tenant;

        $cannedResponse = CannedResponse::create($validated);

        return response()->json([
            'message' => 'Template balasan cepat berhasil dibuat',
            'data'    => $cannedResponse
        ], 201);
    }

    public function show($tenant, $id)
    {
        $cannedResponse = CannedResponse::where('company_id', $tenant)
            ->with(['company', 'department'])
            ->findOrFail($id);

        return response()->json([
            'message' => 'Detail template balasan cepat',
            'data'    => $cannedResponse
        ]);
    }

    public function update(Request $request, $tenant, $id)
    {
        $cannedResponse = CannedResponse::where('company_id', $tenant)->findOrFail($id);

        $validated = $request->validate([
            'title'         => 'sometimes|required|string|max:255',
            'shortcut'      => 'sometimes|required|string|max:100',
            'message'       => 'sometimes|required|string',
            'department_id' => 'nullable|exists:departments,id',
        ]);

        $cannedResponse->update($validated);

        return response()->json([
            'message' => 'Template balasan cepat berhasil diperbarui',
            'data'    => $cannedResponse
        ]);
    }

    public function destroy($tenant, $id)
    {
        $cannedResponse = CannedResponse::where('company_id', $tenant)->findOrFail($id);
        $cannedResponse->delete();

        return response()->json([
            'message' => 'Template balasan cepat berhasil dihapus'
        ]);
    }
}