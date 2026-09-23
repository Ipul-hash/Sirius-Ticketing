<?php

namespace Database\Seeders;

use App\Enums\CompanyPlan;
use App\Enums\CompanyStatus;
use App\Enums\UserRole;
use App\Models\Company;
use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
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

        $users = [
            [
                'email' => 'superadmin@sirius.io',
                'name' => 'Bambang Pamungkas (Superadmin)',
                'role' => UserRole::Superadmin,
                'job_title' => 'Chief Technology Officer',
            ],
            [
                'email' => 'admin@sirius.io',
                'name' => 'Andi Wijaya (Admin Tenant)',
                'role' => UserRole::CompanyAdmin,
                'job_title' => 'Head of IT Helpdesk',
            ],
            [
                'email' => 'teknisi@sirius.io',
                'name' => 'Rian Kurniawan (Teknisi)',
                'role' => UserRole::Agent,
                'job_title' => 'Senior Support Engineer',
            ],
            [
                'email' => 'siti.requester@sirius.io',
                'name' => 'Siti Nurhaliza (Karyawan)',
                'role' => UserRole::Requester,
                'job_title' => 'Staff Finance & Accounting',
            ],
            [
                'email' => 'admin@sirius-tech.com',
                'name' => 'Andi Wijaya (Admin)',
                'role' => UserRole::CompanyAdmin,
                'job_title' => 'IT Manager',
            ],
            [
                'email' => 'agent.rian@sirius-tech.com',
                'name' => 'Rian Hidayat (Agent)',
                'role' => UserRole::Agent,
                'job_title' => 'Support Specialist',
            ],
            [
                'email' => 'siti.rahma@sirius-tech.com',
                'name' => 'Siti Rahma (Requester)',
                'role' => UserRole::Requester,
                'job_title' => 'Staff Member',
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'company_id' => $company->id,
                    'department_id' => $department->id,
                    'name' => $userData['name'],
                    'password' => 'password',
                    'role' => $userData['role'],
                    'job_title' => $userData['job_title'],
                    'is_active' => true,
                ]
            );
        }
    }
}
