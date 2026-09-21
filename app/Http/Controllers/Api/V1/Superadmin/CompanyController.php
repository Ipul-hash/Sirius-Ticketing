<?php

namespace App\Http\Controllers\Api\V1\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use App\Enums\CompanyPlan;
use App\Enums\CompanyStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        $query = Company::query();
        
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return response()->json($query->paginate(10));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'slug'           => 'required|string|unique:companies,slug',
            'domain'         => 'nullable|string|max:255',
            'plan'           => ['required', Rule::enum(CompanyPlan::class)],
            'admin_name'     => 'required|string|max:255',
            'admin_email'    => 'required|email|unique:users,email',
            'admin_password' => 'required|string|min:8',
        ]);

        DB::beginTransaction();
        try {
            $company = Company::create([
                'name'      => $validated['name'],
                'slug'      => $validated['slug'],
                'domain'    => $validated['domain'] ?? null,
                'plan'      => $validated['plan'],
                'status'    => CompanyStatus::ACTIVE ?? 'active',
            ]);
            
            User::create([
                'company_id' => $company->id,
                'name'       => $validated['admin_name'],
                'email'      => $validated['admin_email'],
                'password'   => Hash::make($validated['admin_password']),
                'role'       => 'Company Admin', 
                'is_active'  => true
            ]);

            DB::commit();
            return response()->json([
                'message' => 'Perusahaan dan admin berhasil didaftarkan', 
                'data'    => $company
            ], 201);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Gagal menyimpan data: ' . $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $company = Company::findOrFail($id);
        return response()->json($company);
    }

    public function update(Request $request, $id)
    {
        $company = Company::findOrFail($id);

        $validated = $request->validate([
            'name'   => 'sometimes|required|string|max:255',
            'slug'   => 'sometimes|required|string|unique:companies,slug,' . $id,
            'domain' => 'nullable|string|max:255',
            'plan'   => ['sometimes', 'required', Rule::enum(CompanyPlan::class)],
        ]);

        $company->update($validated);
        
        return response()->json([
            'message' => 'Identitas perusahaan berhasil diupdate', 
            'data'    => $company
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $company = Company::findOrFail($id);

        $validated = $request->validate([
            'status' => ['required', Rule::enum(CompanyStatus::class)],
        ]);
        
        $company->update(['status' => $validated['status']]);
        
        return response()->json([
            'message' => 'Status perusahaan berhasil diperbarui', 
            'data'    => $company
        ]);
    }

    public function destroy($id)
    {
        $company = Company::findOrFail($id);
        $company->delete();
        
        return response()->json(['message' => 'Perusahaan berhasil dihapus']);
    }
}