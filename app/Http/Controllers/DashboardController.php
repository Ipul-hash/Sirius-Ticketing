<?php

namespace App\Http\Controllers;

use App\Enums\AssetStatus;
use App\Enums\CompanyStatus;
use App\Enums\TicketApprovalStatus;
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Models\Company;
use App\Models\CompanyAsset;
use App\Models\Department;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Menampilkan Dashboard utama sesuai dengan peran (role) pengguna aktif.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $role = $user?->role ?? UserRole::CompanyAdmin;
        $companyId = $user?->company_id;

        $data = [
            'user' => $user,
            'role' => $role,
        ];

        switch ($role) {
            case UserRole::Superadmin:
                $data['superadmin'] = $this->getSuperadminData();
                break;

            case UserRole::CompanyAdmin:
                $data['companyAdmin'] = $this->getCompanyAdminData($companyId);
                break;

            case UserRole::Agent:
                $data['agent'] = $this->getAgentData($user, $companyId);
                break;

            case UserRole::Requester:
                $data['requester'] = $this->getRequesterData($user);
                break;
        }

        return view('dashboard.index', $data);
    }

    /**
     * Mengambil data analitik metrik untuk Dashboard Superadmin (SaaS Owner).
     *
     * @return array<string, mixed>
     */
    protected function getSuperadminData(): array
    {
        $totalCompanies = Company::count();
        $activeCompanies = Company::where('status', CompanyStatus::Active)->count();
        $totalUsers = User::count();
        $totalTickets = Ticket::count();
        $openTickets = Ticket::whereNotIn('status', [TicketStatus::Closed, TicketStatus::Resolved])->count();
        $totalAssets = CompanyAsset::count();

        $recentCompanies = Company::query()
            ->withCount(['users', 'tickets'])
            ->latest()
            ->take(5)
            ->get();

        $recentTickets = Ticket::query()
            ->with(['company:id,name', 'requester:id,name', 'category:id,name'])
            ->latest()
            ->take(6)
            ->get();

        return [
            'totalCompanies' => $totalCompanies,
            'activeCompanies' => $activeCompanies,
            'totalUsers' => $totalUsers,
            'totalTickets' => $totalTickets,
            'openTickets' => $openTickets,
            'totalAssets' => $totalAssets,
            'recentCompanies' => $recentCompanies,
            'recentTickets' => $recentTickets,
        ];
    }

    /**
     * Mengambil data analitik metrik untuk Dashboard Company Admin (IT Manager Tenant).
     *
     * @return array<string, mixed>
     */
    protected function getCompanyAdminData(?int $companyId): array
    {
        $ticketsQuery = Ticket::query()->when($companyId !== null, fn ($q) => $q->where('company_id', $companyId));

        $totalTickets = (clone $ticketsQuery)->count();
        $openTickets = (clone $ticketsQuery)->whereNotIn('status', [TicketStatus::Closed, TicketStatus::Resolved])->count();
        $pendingApprovalTickets = (clone $ticketsQuery)->where('approval_status', TicketApprovalStatus::Pending)->count();
        $resolvedTickets = (clone $ticketsQuery)->whereIn('status', [TicketStatus::Closed, TicketStatus::Resolved])->count();

        $assetsQuery = CompanyAsset::query()->when($companyId !== null, fn ($q) => $q->where('company_id', $companyId));
        $totalAssets = (clone $assetsQuery)->count();
        $inUseAssets = (clone $assetsQuery)->where('status', AssetStatus::InUse)->count();

        $urgentTickets = (clone $ticketsQuery)
            ->whereNotIn('status', [TicketStatus::Closed, TicketStatus::Resolved])
            ->whereIn('priority', [TicketPriority::Urgent, TicketPriority::High])
            ->with(['requester:id,name', 'assignedAgent:id,name', 'category:id,name'])
            ->latest()
            ->take(5)
            ->get();

        $agentsWorkload = User::query()
            ->when($companyId !== null, fn ($q) => $q->where('company_id', $companyId))
            ->where('role', UserRole::Agent)
            ->withCount(['assignedTickets' => function ($q) {
                $q->whereNotIn('status', [TicketStatus::Closed, TicketStatus::Resolved]);
            }])
            ->take(6)
            ->get();

        $departments = Department::query()
            ->when($companyId !== null, fn ($q) => $q->where('company_id', $companyId))
            ->withCount('tickets')
            ->take(5)
            ->get();

        return [
            'totalTickets' => $totalTickets,
            'openTickets' => $openTickets,
            'pendingApprovalTickets' => $pendingApprovalTickets,
            'resolvedTickets' => $resolvedTickets,
            'totalAssets' => $totalAssets,
            'inUseAssets' => $inUseAssets,
            'urgentTickets' => $urgentTickets,
            'agentsWorkload' => $agentsWorkload,
            'departments' => $departments,
        ];
    }

    /**
     * Mengambil data antrean dan tugas aktif untuk Dashboard Agent (Teknisi IT).
     *
     * @return array<string, mixed>
     */
    protected function getAgentData(User $user, ?int $companyId): array
    {
        $myAssignedTicketsCount = Ticket::where('assigned_to', $user->id)
            ->whereNotIn('status', [TicketStatus::Closed, TicketStatus::Resolved])
            ->count();

        $unassignedTicketsCount = Ticket::query()
            ->when($companyId !== null, fn ($q) => $q->where('company_id', $companyId))
            ->whereNull('assigned_to')
            ->whereNotIn('status', [TicketStatus::Closed, TicketStatus::Resolved])
            ->count();

        $myResolvedTodayCount = Ticket::where('assigned_to', $user->id)
            ->where('status', TicketStatus::Resolved)
            ->whereDate('updated_at', today())
            ->count();

        $urgentAssignedCount = Ticket::where('assigned_to', $user->id)
            ->whereNotIn('status', [TicketStatus::Closed, TicketStatus::Resolved])
            ->whereIn('priority', [TicketPriority::Urgent, TicketPriority::High])
            ->count();

        $myActiveTickets = Ticket::where('assigned_to', $user->id)
            ->whereNotIn('status', [TicketStatus::Closed, TicketStatus::Resolved])
            ->with(['requester:id,name', 'category:id,name'])
            ->latest()
            ->take(6)
            ->get();

        $unassignedQueue = Ticket::query()
            ->when($companyId !== null, fn ($q) => $q->where('company_id', $companyId))
            ->whereNull('assigned_to')
            ->whereNotIn('status', [TicketStatus::Closed, TicketStatus::Resolved])
            ->with(['requester:id,name', 'category:id,name'])
            ->latest()
            ->take(6)
            ->get();

        return [
            'myAssignedTicketsCount' => $myAssignedTicketsCount,
            'unassignedTicketsCount' => $unassignedTicketsCount,
            'myResolvedTodayCount' => $myResolvedTodayCount,
            'urgentAssignedCount' => $urgentAssignedCount,
            'myActiveTickets' => $myActiveTickets,
            'unassignedQueue' => $unassignedQueue,
        ];
    }

    /**
     * Mengambil data permohonan tiket dan aset untuk Dashboard Requester (Karyawan Pelapor).
     *
     * @return array<string, mixed>
     */
    protected function getRequesterData(User $user): array
    {
        $myTicketsTotal = Ticket::where('requester_id', $user->id)->count();

        $myOpenTicketsCount = Ticket::where('requester_id', $user->id)
            ->whereNotIn('status', [TicketStatus::Closed, TicketStatus::Resolved])
            ->count();

        $myPendingUserCount = Ticket::where('requester_id', $user->id)
            ->where('status', TicketStatus::PendingUser)
            ->count();

        $myResolvedTicketsCount = Ticket::where('requester_id', $user->id)
            ->whereIn('status', [TicketStatus::Closed, TicketStatus::Resolved])
            ->count();

        $myAssignedAssets = CompanyAsset::where('assigned_to_user_id', $user->id)
            ->take(4)
            ->get();

        $myRecentTickets = Ticket::where('requester_id', $user->id)
            ->with(['category:id,name', 'assignedAgent:id,name'])
            ->latest()
            ->take(5)
            ->get();

        $myPendingApprovals = Ticket::query()
            ->whereHas('approvals', fn ($q) => $q->where('approver_id', $user->id)->where('status', TicketApprovalStatus::Pending))
            ->take(3)
            ->get();

        return [
            'myTicketsTotal' => $myTicketsTotal,
            'myOpenTicketsCount' => $myOpenTicketsCount,
            'myPendingUserCount' => $myPendingUserCount,
            'myResolvedTicketsCount' => $myResolvedTicketsCount,
            'myAssignedAssets' => $myAssignedAssets,
            'myRecentTickets' => $myRecentTickets,
            'myPendingApprovals' => $myPendingApprovals,
        ];
    }
}
