<?php

namespace App\Http\Controllers;

use App\Enums\AssetStatus;
use App\Enums\CompanyPlan;
use App\Enums\CompanyStatus;
use App\Enums\TicketApprovalStatus;
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Models\CannedResponse;
use App\Models\Company;
use App\Models\CompanyAsset;
use App\Models\Department;
use App\Models\SlaPolicy;
use App\Models\Ticket;
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
        $user = $request->user();
        $isSuperadmin = $user?->isSuperadmin() ?? false;
        $companyId = $user?->company_id;

        $companiesQuery = Company::query()
            ->where('status', CompanyStatus::Active)
            ->orderBy('name');

        if (! $isSuperadmin && $companyId !== null) {
            $companiesQuery->where('id', $companyId);
        }
        $companies = $companiesQuery->get(['id', 'name', 'slug']);

        $usersQuery = User::query()
            ->where('is_active', true)
            ->orderBy('name');

        if (! $isSuperadmin && $companyId !== null) {
            $usersQuery->where('company_id', $companyId);
        }
        $users = $usersQuery->get(['id', 'name', 'email', 'job_title', 'company_id']);

        $departmentsQuery = Department::query()
            ->with(['company:id,name,slug', 'leadUser:id,name,email,job_title'])
            ->withCount(['users', 'tickets']);

        if (! $isSuperadmin && $companyId !== null) {
            $departmentsQuery->where('company_id', $companyId);
        } elseif ($request->filled('company_id')) {
            $departmentsQuery->where('company_id', $request->company_id);
        }

        if ($request->filled('search')) {
            $departmentsQuery->where('name', 'like', '%'.$request->search.'%');
        }

        if ($request->filled('status')) {
            $departmentsQuery->where('is_active', $request->status === '1');
        }

        $departments = $departmentsQuery->orderBy('name')->paginate(10)->withQueryString();

        $statsDeptQuery = Department::query();
        if (! $isSuperadmin && $companyId !== null) {
            $statsDeptQuery->where('company_id', $companyId);
        }

        $totalDepartments = (clone $statsDeptQuery)->count();
        $activeDepartments = (clone $statsDeptQuery)->where('is_active', true)->count();
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
     * Menampilkan Halaman Master Pengguna / Staf (Metronic 8).
     */
    public function users(Request $request): View
    {
        $user = $request->user();
        $isSuperadmin = $user?->isSuperadmin() ?? false;
        $companyId = $user?->company_id;

        $companiesQuery = Company::query()
            ->where('status', CompanyStatus::Active)
            ->orderBy('name');

        if (! $isSuperadmin && $companyId !== null) {
            $companiesQuery->where('id', $companyId);
        }
        $companies = $companiesQuery->get(['id', 'name', 'slug']);

        $departmentsQuery = Department::query()
            ->where('is_active', true)
            ->orderBy('name');

        if (! $isSuperadmin && $companyId !== null) {
            $departmentsQuery->where('company_id', $companyId);
        }
        $departments = $departmentsQuery->get(['id', 'name', 'company_id']);

        $staffQuery = User::query()
            ->with(['company:id,name,slug', 'department:id,name'])
            ->withCount(['assignedTickets', 'requestedTickets']);

        if (! $isSuperadmin && $companyId !== null) {
            $staffQuery->where('company_id', $companyId);
        } elseif ($request->filled('company_id')) {
            $staffQuery->where('company_id', $request->company_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $staffQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('job_title', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $staffQuery->where('role', $request->role);
        }

        if ($request->filled('department_id')) {
            $staffQuery->where('department_id', $request->department_id);
        }

        if ($request->filled('status')) {
            $staffQuery->where('is_active', $request->status === '1');
        }

        $staffUsers = $staffQuery->orderBy('name')->paginate(10)->withQueryString();

        $statsQuery = User::query();
        if (! $isSuperadmin && $companyId !== null) {
            $statsQuery->where('company_id', $companyId);
        }

        $totalStaff = (clone $statsQuery)->count();
        $activeStaff = (clone $statsQuery)->where('is_active', true)->count();
        $totalAgents = (clone $statsQuery)->where('role', UserRole::Agent)->count();
        $totalRequesters = (clone $statsQuery)->where('role', UserRole::Requester)->count();
        $totalAdmins = (clone $statsQuery)->where('role', UserRole::CompanyAdmin)->count();
        $activePercent = $totalStaff > 0 ? (int) round(($activeStaff / $totalStaff) * 100) : 100;

        $stats = [
            'total_staff' => $totalStaff,
            'active_staff' => $activeStaff,
            'total_agents' => $totalAgents,
            'total_requesters' => $totalRequesters,
            'total_admins' => $totalAdmins,
            'active_percent' => $activePercent,
        ];

        return view('master.users.index', compact('staffUsers', 'companies', 'departments', 'stats'));
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
        $user = $request->user();
        $isSuperadmin = $user?->isSuperadmin() ?? false;
        $companyId = $user?->company_id;

        $companiesQuery = Company::query()
            ->where('status', CompanyStatus::Active)
            ->orderBy('name');

        if (! $isSuperadmin && $companyId !== null) {
            $companiesQuery->where('id', $companyId);
        }
        $companies = $companiesQuery->get(['id', 'name', 'slug']);

        $departmentsQuery = Department::query()
            ->where('is_active', true)
            ->orderBy('name');

        if (! $isSuperadmin && $companyId !== null) {
            $departmentsQuery->where('company_id', $companyId);
        }
        $departments = $departmentsQuery->get(['id', 'name', 'company_id']);

        $usersQuery = User::query()
            ->where('is_active', true)
            ->orderBy('name');

        if (! $isSuperadmin && $companyId !== null) {
            $usersQuery->where('company_id', $companyId);
        }
        $users = $usersQuery->get(['id', 'name', 'email', 'company_id']);

        $query = CompanyAsset::query()
            ->with([
                'company:id,name,slug',
                'department:id,name',
                'assignedUser:id,name,email,job_title',
            ])
            ->withCount('tickets');

        if (! $isSuperadmin && $companyId !== null) {
            $query->where('company_id', $companyId);
        } elseif ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

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

        $assets = $query->orderBy('name')->paginate(10)->withQueryString();

        $statsQuery = CompanyAsset::query();
        if (! $isSuperadmin && $companyId !== null) {
            $statsQuery->where('company_id', $companyId);
        }

        $totalAssets = (clone $statsQuery)->count();
        $inUseAssets = (clone $statsQuery)->where('status', AssetStatus::InUse)->count();
        $availableAssets = (clone $statsQuery)->where('status', AssetStatus::Available)->count();
        $maintenanceAssets = (clone $statsQuery)->where('status', AssetStatus::Maintenance)->count();
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
        $user = $request->user();
        $isSuperadmin = $user?->isSuperadmin() ?? false;
        $companyId = $user?->company_id;

        $companiesQuery = Company::query()
            ->where('status', CompanyStatus::Active)
            ->orderBy('name');

        if (! $isSuperadmin && $companyId !== null) {
            $companiesQuery->where('id', $companyId);
        }
        $companies = $companiesQuery->get(['id', 'name', 'slug']);

        if (! $isSuperadmin && $companyId !== null) {
            $selectedCompanyId = $companyId;
        } else {
            $selectedCompanyId = $request->query('company_id', $companies->first()?->id);
        }

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
        $user = $request->user();
        $isSuperadmin = $user?->isSuperadmin() ?? false;
        $companyId = $user?->company_id;

        $companiesQuery = Company::query()
            ->where('status', CompanyStatus::Active)
            ->orderBy('name');

        if (! $isSuperadmin && $companyId !== null) {
            $companiesQuery->where('id', $companyId);
        }
        $companies = $companiesQuery->get(['id', 'name', 'slug']);

        $departmentsQuery = Department::query()
            ->where('is_active', true)
            ->orderBy('name');

        if (! $isSuperadmin && $companyId !== null) {
            $departmentsQuery->where('company_id', $companyId);
        }
        $departments = $departmentsQuery->get(['id', 'name', 'company_id']);

        $query = TicketCategory::query()
            ->with([
                'company:id,name,slug',
                'department:id,name,is_active',
            ])
            ->withCount('tickets');

        if (! $isSuperadmin && $companyId !== null) {
            $query->where('company_id', $companyId);
        } elseif ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
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

        $statsQuery = TicketCategory::query();
        if (! $isSuperadmin && $companyId !== null) {
            $statsQuery->where('company_id', $companyId);
        }

        $totalCategories = (clone $statsQuery)->count();
        $activeCategories = (clone $statsQuery)->where('is_active', true)->count();
        $approvalCategories = (clone $statsQuery)->where('requires_approval', true)->count();
        $totalTicketsLinked = (clone $statsQuery)->withCount('tickets')->get()->sum('tickets_count');

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
        $user = $request->user();
        $isSuperadmin = $user?->isSuperadmin() ?? false;
        $companyId = $user?->company_id;

        $companiesQuery = Company::query()
            ->where('status', CompanyStatus::Active)
            ->orderBy('name');

        if (! $isSuperadmin && $companyId !== null) {
            $companiesQuery->where('id', $companyId);
        }
        $companies = $companiesQuery->get(['id', 'name', 'slug']);

        $departmentsQuery = Department::query()
            ->where('is_active', true)
            ->orderBy('name');

        if (! $isSuperadmin && $companyId !== null) {
            $departmentsQuery->where('company_id', $companyId);
        }
        $departments = $departmentsQuery->get(['id', 'name', 'company_id']);

        $query = CannedResponse::query()
            ->with([
                'company:id,name,slug',
                'department:id,name',
            ]);

        if (! $isSuperadmin && $companyId !== null) {
            $query->where('company_id', $companyId);
        } elseif ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('shortcut', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%");
            });
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

        $statsQuery = CannedResponse::query();
        if (! $isSuperadmin && $companyId !== null) {
            $statsQuery->where('company_id', $companyId);
        }

        $totalResponses = (clone $statsQuery)->count();
        $globalResponses = (clone $statsQuery)->whereNull('department_id')->count();
        $deptSpecificResponses = (clone $statsQuery)->whereNotNull('department_id')->count();
        $totalCompaniesWithResponses = (clone $statsQuery)->distinct('company_id')->count('company_id');

        $stats = [
            'total_responses' => $totalResponses,
            'global_responses' => $globalResponses,
            'dept_specific_responses' => $deptSpecificResponses,
            'total_companies_with_responses' => $totalCompaniesWithResponses,
        ];

        return view('master.canned_responses.index', compact('responses', 'companies', 'departments', 'stats'));
    }

    /**
     * Menampilkan Halaman Antrean Tiket Helpdesk (Metronic 8).
     */
    public function tickets(Request $request): View
    {
        $user = $request->user();
        $isSuperadmin = $user?->isSuperadmin() ?? false;
        $companyId = $user?->company_id;

        $companiesQuery = Company::query()
            ->where('status', CompanyStatus::Active)
            ->orderBy('name');

        if (! $isSuperadmin && $companyId !== null) {
            $companiesQuery->where('id', $companyId);
        }
        $companies = $companiesQuery->get(['id', 'name', 'slug']);

        $departmentsQuery = Department::query()
            ->where('is_active', true)
            ->orderBy('name');

        if (! $isSuperadmin && $companyId !== null) {
            $departmentsQuery->where('company_id', $companyId);
        }
        $departments = $departmentsQuery->get(['id', 'name', 'company_id']);

        $categoriesQuery = TicketCategory::query()
            ->where('is_active', true)
            ->orderBy('name');

        if (! $isSuperadmin && $companyId !== null) {
            $categoriesQuery->where('company_id', $companyId);
        }
        $categories = $categoriesQuery->get(['id', 'name', 'company_id', 'department_id', 'default_priority', 'requires_approval']);

        $usersQuery = User::query()
            ->where('is_active', true)
            ->orderBy('name');

        if (! $isSuperadmin && $companyId !== null) {
            $usersQuery->where('company_id', $companyId);
        }
        $users = $usersQuery->get(['id', 'name', 'email', 'role', 'company_id', 'job_title']);

        $assetsQuery = CompanyAsset::query()->orderBy('name');
        if (! $isSuperadmin && $companyId !== null) {
            $assetsQuery->where('company_id', $companyId);
        }
        $assets = $assetsQuery->get(['id', 'name', 'asset_tag', 'company_id', 'department_id']);

        $query = Ticket::query()
            ->with([
                'company:id,name,slug',
                'category:id,name,requires_approval',
                'department:id,name',
                'requester:id,name,email,avatar_path',
                'assignedAgent:id,name,email,job_title,avatar_path',
                'asset:id,name,asset_tag,category',
            ]);

        if (! $isSuperadmin && $companyId !== null) {
            $query->where('company_id', $companyId);
        } elseif ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        // Filter Tab Antrean
        $activeTab = $request->query('tab', 'all');
        if ($activeTab === 'unassigned') {
            $query->whereNull('assigned_to')
                ->whereNotIn('status', [TicketStatus::Resolved, TicketStatus::Closed]);
        } elseif ($activeTab === 'my_tickets') {
            $currentUserId = $user?->id ?? $request->query('user_id');
            if ($currentUserId) {
                $query->where('assigned_to', $currentUserId);
            }
        } elseif ($activeTab === 'pending') {
            $query->whereIn('status', [TicketStatus::PendingApproval, TicketStatus::PendingUser]);
        } elseif ($activeTab === 'overdue') {
            $query->where(function ($q) {
                $q->where('is_sla_breached', true)
                    ->orWhere(function ($sub) {
                        $sub->whereNull('resolved_at')
                            ->where('resolution_due_at', '<', now());
                    });
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('assigned_to')) {
            if ($request->assigned_to === 'unassigned') {
                $query->whereNull('assigned_to');
            } else {
                $query->where('assigned_to', $request->assigned_to);
            }
        }

        $tickets = $query->orderByDesc('created_at')->paginate(10)->withQueryString();

        // 4 Metric Summary Cards (Scoped by company)
        $ticketStatsBase = Ticket::query();
        if (! $isSuperadmin && $companyId !== null) {
            $ticketStatsBase->where('company_id', $companyId);
        }

        $totalOpen = (clone $ticketStatsBase)->whereIn('status', [TicketStatus::Open, TicketStatus::InProgress])->count();
        $totalUnassigned = (clone $ticketStatsBase)->whereNull('assigned_to')->whereNotIn('status', [TicketStatus::Resolved, TicketStatus::Closed])->count();
        $totalPending = (clone $ticketStatsBase)->whereIn('status', [TicketStatus::PendingApproval, TicketStatus::PendingUser])->count();
        $totalOverdue = (clone $ticketStatsBase)->where(function ($q) {
            $q->where('is_sla_breached', true)
                ->orWhere(function ($sub) {
                    $sub->whereNull('resolved_at')->where('resolution_due_at', '<', now());
                });
        })->count();

        $stats = [
            'total_open' => $totalOpen,
            'total_unassigned' => $totalUnassigned,
            'total_pending' => $totalPending,
            'total_overdue' => $totalOverdue,
            'total_all' => (clone $ticketStatsBase)->count(),
        ];

        // Sebaran Prioritas Tiket Aktif
        $urgentCount = (clone $ticketStatsBase)->where('priority', TicketPriority::Urgent)
            ->whereNotIn('status', [TicketStatus::Resolved, TicketStatus::Closed])
            ->count();
        $highCount = (clone $ticketStatsBase)->where('priority', TicketPriority::High)
            ->whereNotIn('status', [TicketStatus::Resolved, TicketStatus::Closed])
            ->count();
        $mediumCount = (clone $ticketStatsBase)->where('priority', TicketPriority::Medium)
            ->whereNotIn('status', [TicketStatus::Resolved, TicketStatus::Closed])
            ->count();
        $lowCount = (clone $ticketStatsBase)->where('priority', TicketPriority::Low)
            ->whereNotIn('status', [TicketStatus::Resolved, TicketStatus::Closed])
            ->count();

        // Tingkat Kepatuhan SLA Tiket Aktif
        $activeTicketsCount = (clone $ticketStatsBase)->whereNotIn('status', [TicketStatus::Resolved, TicketStatus::Closed])->count();
        $onTrackCount = max(0, $activeTicketsCount - $totalOverdue);
        $slaComplianceRate = $activeTicketsCount > 0 ? (int) round(($onTrackCount / $activeTicketsCount) * 100) : 100;

        // Spotlight Tiket Kritis / Overdue Terlama yang Butuh Penanganan
        $criticalTicketQuery = Ticket::query()
            ->with(['company:id,name,slug', 'category:id,name', 'requester:id,name,avatar_path', 'assignedAgent:id,name,avatar_path'])
            ->whereNotIn('status', [TicketStatus::Resolved, TicketStatus::Closed])
            ->where(function ($q) {
                $q->where('is_sla_breached', true)
                    ->orWhere('priority', TicketPriority::Urgent)
                    ->orWhere(function ($sub) {
                        $sub->whereNull('resolved_at')->where('resolution_due_at', '<', now());
                    });
            });
        if (! $isSuperadmin && $companyId !== null) {
            $criticalTicketQuery->where('company_id', $companyId);
        }
        $criticalTicket = $criticalTicketQuery->orderBy('created_at', 'asc')->first();

        // Beban Kerja Agen / Teknisi Aktif
        $agentQuery = User::query()
            ->where('is_active', true)
            ->whereIn('role', [UserRole::Agent, UserRole::CompanyAdmin, UserRole::Superadmin]);
        if (! $isSuperadmin && $companyId !== null) {
            $agentQuery->where('company_id', $companyId);
        }
        $agentWorkloads = $agentQuery->withCount(['assignedTickets' => function ($q) {
            $q->whereNotIn('status', [TicketStatus::Resolved, TicketStatus::Closed]);
        }])
            ->orderByDesc('assigned_tickets_count')
            ->limit(3)
            ->get(['id', 'name', 'job_title', 'avatar_path', 'role']);

        $dashboardMetrics = [
            'urgent_count' => $urgentCount,
            'high_count' => $highCount,
            'medium_count' => $mediumCount,
            'low_count' => $lowCount,
            'sla_compliance_rate' => $slaComplianceRate,
            'active_tickets_count' => $activeTicketsCount,
            'critical_ticket' => $criticalTicket,
            'agent_workloads' => $agentWorkloads,
        ];

        $priorities = TicketPriority::cases();
        $statuses = TicketStatus::cases();

        return view('tickets.index', compact(
            'tickets',
            'companies',
            'departments',
            'categories',
            'users',
            'assets',
            'stats',
            'dashboardMetrics',
            'priorities',
            'statuses',
            'activeTab'
        ));
    }

    /**
     * Menampilkan Halaman Menunggu Approval & Workflow Otorisasi (Metronic 8).
     */
    public function approvals(Request $request): View
    {
        $user = $request->user();
        $isSuperadmin = $user?->isSuperadmin() ?? false;
        $companyId = $user?->company_id;

        $companiesQuery = Company::query()
            ->where('status', CompanyStatus::Active)
            ->orderBy('name');

        if (! $isSuperadmin && $companyId !== null) {
            $companiesQuery->where('id', $companyId);
        }
        $companies = $companiesQuery->get(['id', 'name', 'slug']);

        $departmentsQuery = Department::query()
            ->where('is_active', true)
            ->orderBy('name');

        if (! $isSuperadmin && $companyId !== null) {
            $departmentsQuery->where('company_id', $companyId);
        }
        $departments = $departmentsQuery->get(['id', 'name', 'company_id']);

        $categoriesQuery = TicketCategory::query()
            ->where('is_active', true)
            ->orderBy('name');

        if (! $isSuperadmin && $companyId !== null) {
            $categoriesQuery->where('company_id', $companyId);
        }
        $categories = $categoriesQuery->get(['id', 'name', 'company_id']);

        $query = Ticket::query()
            ->with([
                'company:id,name,slug',
                'department:id,name',
                'category:id,name,requires_approval',
                'requester:id,name,email,avatar_path,job_title,department_id',
                'assignedAgent:id,name,email,avatar_path,job_title',
                'asset:id,name,asset_tag,category',
                'approvals' => function ($q) {
                    $q->with('approver:id,name,email,role,job_title')->latest();
                },
            ]);

        if (! $isSuperadmin && $companyId !== null) {
            $query->where('company_id', $companyId);
        } elseif ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        // Status tab: 'pending' (default), 'approved', 'rejected', 'all'
        $activeTab = $request->query('status', 'pending');

        if ($activeTab === 'approved') {
            $query->where('approval_status', TicketApprovalStatus::Approved);
        } elseif ($activeTab === 'rejected') {
            $query->where('approval_status', TicketApprovalStatus::Rejected);
        } elseif ($activeTab === 'all') {
            $query->where(function ($q) {
                $q->where('status', TicketStatus::PendingApproval)
                    ->orWhere('approval_status', '!=', TicketApprovalStatus::None);
            });
        } else {
            $activeTab = 'pending';
            $query->where(function ($q) {
                $q->where('status', TicketStatus::PendingApproval)
                    ->orWhere('approval_status', TicketApprovalStatus::Pending);
            });
        }

        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhereHas('requester', function ($u) use ($search) {
                        $u->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        $tickets = $query->orderByRaw("CASE WHEN priority = 'urgent' THEN 1 WHEN priority = 'high' THEN 2 WHEN priority = 'medium' THEN 3 ELSE 4 END")
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        $statsQuery = Ticket::query();
        if (! $isSuperadmin && $companyId !== null) {
            $statsQuery->where('company_id', $companyId);
        }

        $pendingCount = (clone $statsQuery)->where(function ($q) {
            $q->where('status', TicketStatus::PendingApproval)
                ->orWhere('approval_status', TicketApprovalStatus::Pending);
        })->count();

        $urgentPendingCount = (clone $statsQuery)->where(function ($q) {
            $q->where('status', TicketStatus::PendingApproval)
                ->orWhere('approval_status', TicketApprovalStatus::Pending);
        })->whereIn('priority', [TicketPriority::Urgent, TicketPriority::High])->count();

        $approvedCount = (clone $statsQuery)->where('approval_status', TicketApprovalStatus::Approved)->count();
        $rejectedCount = (clone $statsQuery)->where('approval_status', TicketApprovalStatus::Rejected)->count();

        $stats = [
            'pending_count' => $pendingCount,
            'urgent_pending_count' => $urgentPendingCount,
            'approved_count' => $approvedCount,
            'rejected_count' => $rejectedCount,
        ];

        $priorities = TicketPriority::cases();

        return view('tickets.approvals', compact(
            'tickets',
            'companies',
            'departments',
            'categories',
            'stats',
            'priorities',
            'activeTab'
        ));
    }

    /**
     * Menampilkan Halaman Detail Tiket & Percakapan (Metronic 8).
     */
    public function ticketDetail(Request $request, string $id): View
    {
        $user = $request->user();
        $isSuperadmin = $user?->isSuperadmin() ?? false;
        $companyId = $user?->company_id;

        $ticketQuery = Ticket::query()
            ->with([
                'company:id,name,slug',
                'department:id,name',
                'category:id,name,requires_approval',
                'requester:id,name,email,avatar_path,job_title,phone,department_id',
                'assignedAgent:id,name,email,avatar_path,job_title',
                'asset:id,name,asset_tag,category,status',
                'messages' => function ($q) {
                    $q->with(['user:id,name,email,role,job_title,avatar_path', 'attachments'])
                        ->orderBy('created_at', 'asc');
                },
                'activities' => function ($q) {
                    $q->with('user:id,name,role,job_title,avatar_path')->orderBy('created_at', 'desc');
                },
                'approvals' => function ($q) {
                    $q->with('approver:id,name,email,role,job_title')->latest();
                },
                'mergedInto:id,ticket_number,subject,status,priority',
                'mergedTickets' => function ($q) {
                    $q->with('requester:id,name,email')->orderBy('created_at', 'desc');
                },
            ]);

        if (! $isSuperadmin && $companyId !== null) {
            $ticketQuery->where('company_id', $companyId);
        }

        $ticket = $ticketQuery->where(function ($q) use ($id) {
            $q->where('id', $id)->orWhere('ticket_number', $id);
        })->firstOrFail();

        $agents = User::query()
            ->where('company_id', $ticket->company_id)
            ->where('is_active', true)
            ->whereIn('role', [UserRole::Agent, UserRole::CompanyAdmin, UserRole::Superadmin])
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'job_title', 'role']);

        $cannedResponses = CannedResponse::query()
            ->where(function ($q) use ($ticket) {
                $q->where('company_id', $ticket->company_id)
                    ->where(function ($sub) use ($ticket) {
                        $sub->whereNull('department_id')
                            ->orWhere('department_id', $ticket->department_id);
                    });
            })
            ->orderBy('shortcut')
            ->get(['id', 'title', 'shortcut', 'message']);

        $statuses = TicketStatus::cases();
        $priorities = TicketPriority::cases();
        $users = $agents;

        return view('tickets.show', compact('ticket', 'agents', 'users', 'cannedResponses', 'statuses', 'priorities'));
    }
}
