<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Department;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DepartmentWebController extends Controller
{
    /**
     * Menampilkan halaman Master Departemen berbasis Metronic 8.
     */
    public function index(Request $request): View
    {
        $companies = Company::query()
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        $users = User::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'job_title', 'company_id']);

        $departments = Department::query()
            ->with(['company:id,name,slug', 'leadUser:id,name,email,job_title'])
            ->withCount(['users', 'tickets'])
            ->orderBy('name')
            ->paginate(10);

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
}
