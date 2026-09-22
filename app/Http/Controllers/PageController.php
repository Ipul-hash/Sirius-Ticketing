<?php

namespace App\Http\Controllers;

use App\Enums\AssetStatus;
use App\Enums\CompanyPlan;
use App\Enums\CompanyStatus;
use App\Enums\TicketPriority;
use App\Models\CannedResponse;
use App\Models\Company;
use App\Models\CompanyAsset;
use App\Models\Department;
use App\Models\SlaPolicy;
use App\Models\TicketCategory;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Menampilkan Halaman Master Departemen (Metronic 8).
     */
    public function departments(Request $request): View
    {
        $companies = Company::query()
            ->where('status', CompanyStatus::Active)
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        $users = User::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'job_title', 'company_id']);

        $departmentsQuery = Department::query()
            ->with(['company:id,name,slug', 'leadUser:id,name,email,job_title'])
            ->withCount(['users', 'tickets']);

        if ($request->filled('search')) {
            $departmentsQuery->where('name', 'like', '%'.$request->search.'%');
        }

        if ($request->filled('status')) {
            $departmentsQuery->where('is_active', $request->status === '1');
        }

        if ($request->filled('company_id')) {
            $departmentsQuery->where('company_id', $request->company_id);
        }

        $departments = $departmentsQuery->orderBy('name')->paginate(10)->withQueryString();

        $totalDepartments = Department::count();
        $activeDepartments = Department::where('is_active', true)->count();
        $totalUsers = $users->count();
        $totalCompanies = $companies->count();
        $activePercent = $totalDepartments > 0 ? (int) round(($activeDepartments / $totalDepartments) * 100) : 100;
        $avgUsersPerDept = $totalDepartments > 0 ? round($totalUsers / $totalDepartments, 1) : 0;

        $stats = [
            'total_departments' => $totalDepartments,
            'active_departments' => $activeDepartments,
            'total_users' => $totalUsers,
            'total_companies' => $totalCompanies,
            'active_percent' => $activePercent,
            'avg_users_per_dept' => $avgUsersPerDept,
        ];

        return view('master.departments.index', compact('departments', 'companies', 'users', 'stats'));
    }

    /**
     * Menampilkan Halaman Master Perusahaan / Tenant.
     */
    public function companies(Request $request): View
    {
        $query = Company::query()
            ->withCount(['departments', 'users', 'assets', 'tickets']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('domain', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('plan')) {
            $query->where('plan', $request->plan);
        }

        $companies = $query->orderBy('name')->paginate(10)->withQueryString();

        $totalCompanies = Company::count();
        $activeCompanies = Company::where('status', CompanyStatus::Active)->count();
        $enterpriseCompanies = Company::where('plan', CompanyPlan::Enterprise)->count();
        $totalUsers = User::count();
        $activePercent = $totalCompanies > 0 ? (int) round(($activeCompanies / $totalCompanies) * 100) : 100;

        $stats = [
            'total_companies' => $totalCompanies,
            'active_companies' => $activeCompanies,
            'enterprise_companies' => $enterpriseCompanies,
            'total_users' => $totalUsers,
            'active_percent' => $activePercent,
        ];

        return view('master.companies.index', compact('companies', 'stats'));
    }

    /**
     * Menampilkan Halaman Master Inventaris Aset.
     */
    public function assets(Request $request): View
    {
        $companies = Company::query()
            ->where('status', CompanyStatus::Active)
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        $departments = Department::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'company_id']);

        $users = User::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'company_id']);

        $query = CompanyAsset::query()
            ->with([
                'company:id,name,slug',
                'department:id,name',
                'assignedUser:id,name,email,job_title',
            ])
            ->withCount('tickets');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('asset_tag', 'like', "%{$search}%")
                    ->orWhere('serial_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        $assets = $query->orderBy('name')->paginate(10)->withQueryString();

        $totalAssets = CompanyAsset::count();
        $inUseAssets = CompanyAsset::where('status', AssetStatus::InUse)->count();
        $availableAssets = CompanyAsset::where('status', AssetStatus::Available)->count();
        $maintenanceAssets = CompanyAsset::where('status', AssetStatus::Maintenance)->count();
        $utilizationPercent = $totalAssets > 0 ? (int) round(($inUseAssets / $totalAssets) * 100) : 0;

        $stats = [
            'total_assets' => $totalAssets,
            'in_use_assets' => $inUseAssets,
            'available_assets' => $availableAssets,
            'maintenance_assets' => $maintenanceAssets,
            'utilization_percent' => $utilizationPercent,
        ];

        return view('master.assets.index', compact('assets', 'companies', 'departments', 'users', 'stats'));
    }

    /**
     * Menampilkan Halaman Master Kebijakan SLA.
     */
    public function slaPolicies(Request $request): View
    {
        $companies = Company::query()
            ->where('status', CompanyStatus::Active)
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        $selectedCompanyId = $request->query('company_id', $companies->first()?->id);
        $selectedCompany = $companies->firstWhere('id', $selectedCompanyId) ?? $companies->first();

        $policies = collect();
        if ($selectedCompany !== null) {
            $existingPolicies = SlaPolicy::where('company_id', $selectedCompany->id)->get();

            // Auto-seeding jika tenant belum memiliki matriks SLA
            if ($existingPolicies->isEmpty()) {
                $defaultSlas = [
                    ['priority' => TicketPriority::Low, 'first_response' => 1440, 'resolution' => 2880],
                    ['priority' => TicketPriority::Medium, 'first_response' => 480, 'resolution' => 1440],
                    ['priority' => TicketPriority::High, 'first_response' => 120, 'resolution' => 480],
                    ['priority' => TicketPriority::Urgent, 'first_response' => 30, 'resolution' => 120],
                ];

                foreach ($defaultSlas as $sla) {
                    SlaPolicy::create([
                        'company_id' => $selectedCompany->id,
                        'priority' => $sla['priority'],
                        'first_response_time_minutes' => $sla['first_response'],
                        'resolution_time_minutes' => $sla['resolution'],
                    ]);
                }

                $existingPolicies = SlaPolicy::where('company_id', $selectedCompany->id)->get();
            }

            $policies = $existingPolicies;
        }

        // Susun matriks SLA per prioritas
        $slaUrgent = $policies->firstWhere('priority', TicketPriority::Urgent);
        $slaHigh = $policies->firstWhere('priority', TicketPriority::High);
        $slaMedium = $policies->firstWhere('priority', TicketPriority::Medium);
        $slaLow = $policies->firstWhere('priority', TicketPriority::Low);

        return view('master.sla_policies.index', compact(
            'companies',
            'selectedCompany',
            'policies',
            'slaUrgent',
            'slaHigh',
            'slaMedium',
            'slaLow'
        ));
    }

    /**
     * Menampilkan Halaman Master Kategori Tiket.
     */
    public function ticketCategories(Request $request): View
    {
        $companies = Company::query()
            ->where('status', CompanyStatus::Active)
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        $departments = Department::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'company_id']);

        $query = TicketCategory::query()
            ->with([
                'company:id,name,slug',
                'department:id,name,is_active',
            ])
            ->withCount('tickets');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('default_priority')) {
            $query->where('default_priority', $request->default_priority);
        }

        if ($request->has('requires_approval') && $request->requires_approval !== '') {
            $query->where('requires_approval', $request->requires_approval === '1');
        }

        if ($request->has('status') && $request->status !== '') {
            $query->where('is_active', $request->status === '1');
        }

        $categories = $query->orderBy('name')->paginate(10)->withQueryString();

        $totalCategories = TicketCategory::count();
        $activeCategories = TicketCategory::where('is_active', true)->count();
        $approvalCategories = TicketCategory::where('requires_approval', true)->count();
        $totalTicketsLinked = TicketCategory::withCount('tickets')->get()->sum('tickets_count');

        $stats = [
            'total_categories' => $totalCategories,
            'active_categories' => $activeCategories,
            'approval_categories' => $approvalCategories,
            'total_tickets_linked' => $totalTicketsLinked,
        ];

        $priorities = TicketPriority::cases();

        return view('master.categories.index', compact('categories', 'companies', 'departments', 'stats', 'priorities'));
    }

    /**
     * Menampilkan Halaman Master Canned Responses / Balasan Cepat.
     */
    public function cannedResponses(Request $request): View
    {
        $companies = Company::query()
            ->where('status', CompanyStatus::Active)
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        $departments = Department::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'company_id']);

        $query = CannedResponse::query()
            ->with([
                'company:id,name,slug',
                'department:id,name',
            ]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('shortcut', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%");
            });
        }

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->filled('department_id')) {
            $deptId = $request->department_id;
            if ($deptId === 'global') {
                $query->whereNull('department_id');
            } else {
                $query->where('department_id', $deptId);
            }
        }

        $responses = $query->orderBy('title')->paginate(10)->withQueryString();

        $totalResponses = CannedResponse::count();
        $globalResponses = CannedResponse::whereNull('department_id')->count();
        $deptSpecificResponses = CannedResponse::whereNotNull('department_id')->count();
        $totalCompaniesWithResponses = CannedResponse::distinct('company_id')->count('company_id');

        $stats = [
            'total_responses' => $totalResponses,
            'global_responses' => $globalResponses,
            'dept_specific_responses' => $deptSpecificResponses,
            'total_companies_with_responses' => $totalCompaniesWithResponses,
        ];

        return view('master.canned_responses.index', compact('responses', 'companies', 'departments', 'stats'));
    }
}
