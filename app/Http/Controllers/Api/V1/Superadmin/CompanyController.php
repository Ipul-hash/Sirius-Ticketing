<?php

namespace App\Http\Controllers\Api\V1\Superadmin;

use App\Enums\CompanyPlan;
use App\Enums\CompanyStatus;
use App\Enums\TicketPriority;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\SlaPolicy;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class CompanyController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Company::query()
            ->withCount(['departments', 'users', 'assets', 'tickets']);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->search.'%')
                ->orWhere('slug', 'like', '%'.$request->search.'%')
                ->orWhere('domain', 'like', '%'.$request->search.'%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('plan')) {
            $query->where('plan', $request->plan);
        }

        $companies = $query->orderBy('name')->paginate((int) $request->query('per_page', 10));

        return response()->json([
            'success' => true,
            'message' => 'Daftar perusahaan berhasil diambil.',
            'data' => $companies->items(),
            'meta' => [
                'current_page' => $companies->currentPage(),
                'last_page' => $companies->lastPage(),
                'per_page' => $companies->perPage(),
                'total' => $companies->total(),
            ],
        ], 200);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'slug' => 'required|string|max:100|unique:companies,slug',
            'domain' => 'nullable|string|max:150',
            'plan' => ['required', Rule::enum(CompanyPlan::class)],
            'admin_name' => 'required|string|max:150',
            'admin_email' => 'required|email|max:191|unique:users,email',
            'admin_password' => 'required|string|min:8',
        ]);

        DB::beginTransaction();
        try {
            $company = Company::create([
                'name' => $validated['name'],
                'slug' => $validated['slug'],
                'domain' => $validated['domain'] ?? null,
                'plan' => $validated['plan'],
                'status' => CompanyStatus::Active,
            ]);

            User::create([
                'company_id' => $company->id,
                'name' => $validated['admin_name'],
                'email' => $validated['admin_email'],
                'password' => Hash::make($validated['admin_password']),
                'role' => UserRole::CompanyAdmin,
                'is_active' => true,
            ]);

            // Auto-seed default SLA policies untuk tenant baru
            $defaultSlas = [
                ['priority' => TicketPriority::Low, 'first_response' => 1440, 'resolution' => 2880],
                ['priority' => TicketPriority::Medium, 'first_response' => 480, 'resolution' => 1440],
                ['priority' => TicketPriority::High, 'first_response' => 120, 'resolution' => 480],
                ['priority' => TicketPriority::Urgent, 'first_response' => 30, 'resolution' => 120],
            ];

            foreach ($defaultSlas as $sla) {
                SlaPolicy::create([
                    'company_id' => $company->id,
                    'priority' => $sla['priority'],
                    'first_response_time_minutes' => $sla['first_response'],
                    'resolution_time_minutes' => $sla['resolution'],
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Perusahaan dan admin berhasil didaftarkan.',
                'data' => $company->loadCount(['departments', 'users', 'assets', 'tickets']),
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data perusahaan: '.$e->getMessage(),
            ], 500);
        }
    }

    public function show(string|int $id): JsonResponse
    {
        $company = Company::withCount(['departments', 'users', 'assets', 'tickets'])
            ->where('id', $id)
            ->orWhere('slug', $id)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'message' => 'Detail perusahaan berhasil diambil.',
            'data' => $company,
        ], 200);
    }

    public function update(Request $request, string|int $id): JsonResponse
    {
        $company = Company::where('id', $id)->orWhere('slug', $id)->firstOrFail();

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:150',
            'slug' => ['sometimes', 'required', 'string', 'max:100', Rule::unique('companies', 'slug')->ignore($company->id)],
            'domain' => 'nullable|string|max:150',
            'plan' => ['sometimes', 'required', Rule::enum(CompanyPlan::class)],
        ]);

        $company->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Identitas perusahaan berhasil diperbarui.',
            'data' => $company,
        ], 200);
    }

    public function updateStatus(Request $request, string|int $id): JsonResponse
    {
        $company = Company::where('id', $id)->orWhere('slug', $id)->firstOrFail();

        $validated = $request->validate([
            'status' => ['required', Rule::enum(CompanyStatus::class)],
        ]);

        $company->update(['status' => $validated['status']]);

        return response()->json([
            'success' => true,
            'message' => 'Status perusahaan berhasil diperbarui.',
            'data' => $company,
        ], 200);
    }

    public function destroy(string|int $id): JsonResponse
    {
        $company = Company::where('id', $id)->orWhere('slug', $id)->firstOrFail();

        if ($company->tickets()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Perusahaan tidak dapat dihapus karena masih memiliki tiket terkait. Ubah status menjadi suspended sebagai gantinya.',
            ], 422);
        }

        $company->delete();

        return response()->json([
            'success' => true,
            'message' => 'Perusahaan berhasil dihapus.',
        ], 200);
    }
}
