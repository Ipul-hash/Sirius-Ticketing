<?php

namespace App\Http\Middleware;

use App\Enums\CompanyPlan;
use App\Enums\CompanyStatus;
use App\Enums\UserRole;
use App\Models\Company;
use App\Models\Department;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveUserSession
{
    /**
     * Memastikan sesi web memiliki pengguna terotentikasi yang aktif.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Hanya memproses request web browser
        if (! $request->is('api/*') && ! $request->is('assets/*') && ! $request->expectsJson()) {
            $currentRole = session('active_role');

            if (! Auth::check()) {
                $targetRole = $currentRole ? UserRole::tryFrom($currentRole) : UserRole::CompanyAdmin;
                $user = $this->resolveOrCreateUserForRole($targetRole ?? UserRole::CompanyAdmin);
                if ($user !== null) {
                    Auth::login($user);
                    session(['active_role' => $user->role->value]);
                }
            } elseif ($currentRole !== null && Auth::user()?->role?->value !== $currentRole) {
                $targetRole = UserRole::tryFrom($currentRole);
                if ($targetRole !== null) {
                    $user = $this->resolveOrCreateUserForRole($targetRole);
                    if ($user !== null) {
                        Auth::login($user);
                    }
                }
            }
        }

        return $next($request);
    }

    /**
     * Mengambil atau membuat user demo representatif untuk peran yang diminta.
     */
    public function resolveOrCreateUserForRole(UserRole $role): User
    {
        $existing = User::where('role', $role)->where('is_active', true)->first();
        if ($existing !== null) {
            return $existing;
        }

        $company = Company::first() ?? Company::create([
            'name' => 'Sirius Global Tech',
            'slug' => 'sirius-global',
            'domain' => 'sirius.io',
            'plan' => CompanyPlan::Enterprise,
            'status' => CompanyStatus::Active,
        ]);

        $department = Department::first() ?? Department::create([
            'company_id' => $company->id,
            'name' => 'IT Infrastructure',
            'slug' => 'it-infra',
            'is_active' => true,
        ]);

        return match ($role) {
            UserRole::Superadmin => User::create([
                'company_id' => $company->id,
                'department_id' => $department->id,
                'name' => 'Bambang Pamungkas (Superadmin)',
                'email' => 'superadmin@sirius.io',
                'password' => bcrypt('password'),
                'role' => UserRole::Superadmin,
                'job_title' => 'Chief Technology Officer',
                'is_active' => true,
            ]),
            UserRole::CompanyAdmin => User::create([
                'company_id' => $company->id,
                'department_id' => $department->id,
                'name' => 'Andi Wijaya (Admin Tenant)',
                'email' => 'admin@sirius.io',
                'password' => bcrypt('password'),
                'role' => UserRole::CompanyAdmin,
                'job_title' => 'Head of IT Helpdesk',
                'is_active' => true,
            ]),
            UserRole::Agent => User::create([
                'company_id' => $company->id,
                'department_id' => $department->id,
                'name' => 'Rian Kurniawan (Teknisi)',
                'email' => 'teknisi@sirius.io',
                'password' => bcrypt('password'),
                'role' => UserRole::Agent,
                'job_title' => 'Senior Support Engineer',
                'is_active' => true,
            ]),
            UserRole::Requester => User::create([
                'company_id' => $company->id,
                'department_id' => $department->id,
                'name' => 'Siti Nurhaliza (Karyawan)',
                'email' => 'siti.requester@sirius.io',
                'password' => bcrypt('password'),
                'role' => UserRole::Requester,
                'job_title' => 'Staff Finance & Accounting',
                'is_active' => true,
            ]),
        };
    }
}
