<?php

namespace App\Http\Controllers\Api\V1\Master;

use App\Http\Controllers\Controller;
use App\Models\TicketCategory;
use App\Enums\TicketPriority;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TicketCategoryController extends Controller
{
    public function index(Request $request, $tenant)
    {
        $query = TicketCategory::where('company_id', $tenant);

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $query->with(['company', 'department']);

        return response()->json([
            'message' => 'Daftar kategori tiket berhasil dimuat',
            'data'    => $query->get()
        ]);
    }

    public function store(Request $request, $tenant)
    {
        $validated = $request->validate([
            'name'               => 'required|string|max:255',
            'department_id'      => 'nullable|exists:departments,id',
            'default_priority'   => ['nullable', Rule::enum(TicketPriority::class)],
            'requires_approval'  => 'boolean',
            'is_active'          => 'boolean',
        ]);

        $validated['company_id'] = $tenant;
        $validated['default_priority'] = $validated['default_priority'] ?? TicketPriority::MEDIUM;
        $validated['requires_approval'] = $validated['requires_approval'] ?? false;
        $validated['is_active'] = $validated['is_active'] ?? true;

        $category = TicketCategory::create($validated);

        return response()->json([
            'message' => 'Kategori tiket berhasil ditambahkan',
            'data'    => $category
        ], 201);
    }

    public function show($tenant, $id)
    {
        $category = TicketCategory::where('company_id', $tenant)
            ->with(['company', 'department', 'tickets'])
            ->findOrFail($id);

        return response()->json([
            'message' => 'Detail kategori tiket',
            'data'    => $category
        ]);
    }

    public function update(Request $request, $tenant, $id)
    {
        $category = TicketCategory::where('company_id', $tenant)->findOrFail($id);

        $validated = $request->validate([
            'name'               => 'sometimes|required|string|max:255',
            'department_id'      => 'nullable|exists:departments,id',
            'default_priority'   => ['sometimes', Rule::enum(TicketPriority::class)],
            'requires_approval'  => 'boolean',
            'is_active'          => 'boolean',
        ]);

        $category->update($validated);

        return response()->json([
            'message' => 'Kategori tiket berhasil diperbarui',
            'data'    => $category
        ]);
    }

    public function destroy($tenant, $id)
    {
        $category = TicketCategory::where('company_id', $tenant)->findOrFail($id);
        
        $category->update(['is_active' => false]);

        return response()->json([
            'message' => 'Kategori tiket berhasil dinonaktifkan'
        ]);
    }
}