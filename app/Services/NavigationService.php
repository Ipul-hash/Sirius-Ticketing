<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\User;

class NavigationService
{
    /**
     * Mengambil daftar struktur menu navigasi yang telah disaring berdasarkan wewenang (role) pengguna.
     *
     * @return array<int, array{heading?: string, items: array<int, array<string, mixed>>}>
     */
    public function getNavigation(?User $user): array
    {
        $role = $user?->role ?? UserRole::CompanyAdmin;
        $isSuperadmin = $role === UserRole::Superadmin;

        $rawSections = [
            // ==========================================
            // SEKSI 1: DASHBOARD
            // ==========================================
            [
                'heading' => null,
                'items' => [
                    [
                        'title' => 'Dashboard',
                        'url' => url('/'),
                        'active' => request()->is('/') || request()->is('dashboard*'),
                        'icon' => 'ki-element-11',
                        'roles' => [UserRole::Superadmin, UserRole::CompanyAdmin, UserRole::Agent, UserRole::Requester],
                    ],
                ],
            ],

            // ==========================================
            // SEKSI 2: PORTAL PEMOHON (KHUSUS REQUESTER)
            // ==========================================
            [
                'heading' => 'Portal Layanan Mandiri',
                'roles' => [UserRole::Requester],
                'items' => [
                    [
                        'title' => 'Tiket Permintaan Saya',
                        'url' => url('/tickets'),
                        'active' => request()->is('tickets*') && ! request()->filled('action'),
                        'icon' => 'ki-messages',
                        'roles' => [UserRole::Requester],
                        'badge' => 'Aktif',
                        'badge_class' => 'badge-light-primary',
                    ],
                    [
                        'title' => 'Perangkat & Aset Saya',
                        'url' => url('/company-assets'),
                        'active' => request()->is('company-assets*') || request()->is('inventaris*'),
                        'icon' => 'ki-devices',
                        'roles' => [UserRole::Requester],
                        'badge' => 'Aset',
                        'badge_class' => 'badge-light-info',
                    ],
                    [
                        'title' => 'Persetujuan Otorisasi',
                        'url' => route('approvals.index'),
                        'active' => request()->is('approvals*'),
                        'icon' => 'ki-verify',
                        'roles' => [UserRole::Requester],
                        'badge' => 'Approval',
                        'badge_class' => 'badge-light-warning',
                    ],
                ],
            ],

            // ==========================================
            // SEKSI 3: HELPDESK & OPERASIONAL (ADMIN & AGENT)
            // ==========================================
            [
                'heading' => 'Helpdesk & Operasional',
                'roles' => [UserRole::Superadmin, UserRole::CompanyAdmin, UserRole::Agent],
                'items' => [
                    [
                        'title' => 'Antrean Tiket',
                        'url' => url('/tickets'),
                        'active' => request()->is('tickets*'),
                        'icon' => 'ki-tablet-text-down',
                        'roles' => [UserRole::Superadmin, UserRole::CompanyAdmin, UserRole::Agent],
                        'badge' => 'Antrean',
                        'badge_class' => 'badge-light-primary',
                    ],
                    [
                        'title' => 'Menunggu Approval',
                        'url' => route('approvals.index'),
                        'active' => request()->is('approvals*'),
                        'icon' => 'ki-verify',
                        'roles' => [UserRole::Superadmin, UserRole::CompanyAdmin, UserRole::Agent],
                        'badge' => 'Otorisasi',
                        'badge_class' => 'badge-light-warning',
                    ],
                ],
            ],

            // ==========================================
            // SEKSI 4: MASTER DATA ORGANISASI
            // ==========================================
            [
                'heading' => 'Master Data Organisasi',
                'roles' => [UserRole::Superadmin, UserRole::CompanyAdmin, UserRole::Agent],
                'items' => [
                    // Khusus Superadmin (Tenant Management)
                    [
                        'title' => 'Perusahaan (Tenant)',
                        'url' => url('/companies'),
                        'active' => request()->is('companies*'),
                        'icon' => 'ki-shop',
                        'roles' => [UserRole::Superadmin],
                        'badge' => 'Superadmin',
                        'badge_class' => 'badge-light-danger',
                    ],
                    // Departemen (Superadmin & Company Admin)
                    [
                        'title' => 'Departemen',
                        'url' => url('/departments'),
                        'active' => request()->is('departments*'),
                        'icon' => 'ki-briefcase',
                        'roles' => [UserRole::Superadmin, UserRole::CompanyAdmin],
                        'badge' => 'Struktur',
                        'badge_class' => 'badge-light-success',
                    ],
                    // Manajemen Staf / Pengguna (Superadmin & Company Admin)
                    [
                        'title' => 'Manajemen Staf',
                        'url' => url('/users'),
                        'active' => request()->is('users*') || request()->is('staff*'),
                        'icon' => 'ki-profile-user',
                        'roles' => [UserRole::Superadmin, UserRole::CompanyAdmin],
                        'badge' => 'Pengguna',
                        'badge_class' => 'badge-light-primary',
                    ],
                    // Kategori Tiket
                    [
                        'title' => 'Kategori Tiket',
                        'url' => url('/categories'),
                        'active' => request()->is('categories*'),
                        'icon' => 'ki-category',
                        'roles' => [UserRole::Superadmin, UserRole::CompanyAdmin],
                        'badge' => 'Klasifikasi',
                        'badge_class' => 'badge-light-primary',
                    ],
                    // Aset Kantor (CMDB)
                    [
                        'title' => 'Aset Kantor (CMDB)',
                        'url' => url('/company-assets'),
                        'active' => request()->is('company-assets*') || request()->is('inventaris*'),
                        'icon' => 'ki-devices',
                        'roles' => [UserRole::Superadmin, UserRole::CompanyAdmin, UserRole::Agent],
                        'badge' => 'Inventaris',
                        'badge_class' => 'badge-light-info',
                    ],
                    // Kebijakan SLA (Matriks)
                    [
                        'title' => 'Kebijakan SLA',
                        'url' => url('/sla-policies'),
                        'active' => request()->is('sla*'),
                        'icon' => 'ki-timer',
                        'roles' => [UserRole::Superadmin, UserRole::CompanyAdmin],
                        'badge' => 'Matriks',
                        'badge_class' => 'badge-light-warning',
                    ],
                    // Canned Responses (Makro Pesan)
                    [
                        'title' => 'Canned Responses',
                        'url' => url('/canned-responses'),
                        'active' => request()->is('canned*'),
                        'icon' => 'ki-message-text-2',
                        'roles' => [UserRole::Superadmin, UserRole::CompanyAdmin, UserRole::Agent],
                        'badge' => 'Balasan Cepat',
                        'badge_class' => 'badge-light-success',
                    ],
                ],
            ],
        ];

        // Saring seksi dan item berdasarkan role
        $filteredSections = [];

        foreach ($rawSections as $section) {
            // Periksa hak akses seksi jika ada
            if (isset($section['roles']) && ! $isSuperadmin && ! in_array($role, $section['roles'], true)) {
                continue;
            }

            $visibleItems = [];
            foreach ($section['items'] as $item) {
                $allowedRoles = $item['roles'] ?? [];
                if ($isSuperadmin || in_array($role, $allowedRoles, true)) {
                    $visibleItems[] = $item;
                }
            }

            if (! empty($visibleItems)) {
                $filteredSections[] = [
                    'heading' => $section['heading'] ?? null,
                    'items' => $visibleItems,
                ];
            }
        }

        return $filteredSections;
    }
}
