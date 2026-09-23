<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

// ==============================================================
// 1. RUTE OTENTIKASI (GUEST / PUBLIK)
// ==============================================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// ==============================================================
// 2. RUTE KELUAR SISTEM (LOGOUT)
// ==============================================================
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');
Route::get('/logout', [AuthController::class, 'logout'])->middleware('auth');

// ==============================================================
// 3. RUTE APLIKASI TERPROTEKSI (AUTH & RBAC)
// ==============================================================
Route::middleware('auth')->group(function () {
    // Dashboard Utama (Otomatis menyesuaikan peran pengguna yang login)
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index']);

    Route::controller(PageController::class)->group(function () {
        // 1. Khusus Superadmin (Platform SaaS Owner)
        Route::get('/companies', 'companies')->name('companies.index')->middleware('role:superadmin');

        // 2. Master Data Tenant (Superadmin & Company Admin)
        Route::get('/departments', 'departments')->name('departments.index')->middleware('role:superadmin,company_admin');
        Route::get('/users', 'users')->name('users.index')->middleware('role:superadmin,company_admin');
        Route::get('/staff', 'users')->middleware('role:superadmin,company_admin');
        Route::get('/categories', 'ticketCategories')->name('categories.index')->middleware('role:superadmin,company_admin');
        Route::get('/sla-policies', 'slaPolicies')->name('sla-policies.index')->middleware('role:superadmin,company_admin');

        // 3. Staf Helpdesk & Admin (Superadmin, Company Admin, Agent)
        Route::get('/canned-responses', 'cannedResponses')->name('canned-responses.index')->middleware('role:superadmin,company_admin,agent');

        // 4. Modul Operasional (Aset, Tiket & Approval)
        Route::get('/company-assets', 'assets')->name('assets.index')->middleware('role:superadmin,company_admin,agent,requester');
        Route::get('/inventaris', 'assets')->middleware('role:superadmin,company_admin,agent,requester');
        Route::get('/tickets', 'tickets')->name('tickets.index')->middleware('role:superadmin,company_admin,agent,requester');
        Route::get('/approvals', 'approvals')->name('approvals.index')->middleware('role:superadmin,company_admin,agent,requester');
        Route::get('/tickets/{id}', 'ticketDetail')->name('tickets.show')->middleware('role:superadmin,company_admin,agent,requester');
    });
});
