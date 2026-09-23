<?php

use App\Enums\CompanyPlan;
use App\Enums\CompanyStatus;
use App\Enums\UserRole;
use App\Models\Company;
use App\Models\Department;
use App\Models\User;
use App\Services\NavigationService;
use Illuminate\Foundation\Testing\DatabaseTransactions;

uses(DatabaseTransactions::class);

beforeEach(function () {
    $this->company = Company::first() ?? Company::create([
        'name' => 'Sirius RBAC Corp',
        'slug' => 'sirius-rbac-'.uniqid(),
        'domain' => 'rbac.'.uniqid().'.io',
        'plan' => CompanyPlan::Enterprise,
        'status' => CompanyStatus::Active,
    ]);

    $this->department = Department::first() ?? Department::create([
        'company_id' => $this->company->id,
        'name' => 'Operations IT',
        'slug' => 'ops-it-'.uniqid(),
        'is_active' => true,
    ]);

    $this->superadmin = User::create([
        'company_id' => $this->company->id,
        'department_id' => $this->department->id,
        'name' => 'Super User '.uniqid(),
        'email' => 'super.'.uniqid().'@sirius.io',
        'password' => bcrypt('password'),
        'role' => UserRole::Superadmin,
        'is_active' => true,
    ]);

    $this->companyAdmin = User::create([
        'company_id' => $this->company->id,
        'department_id' => $this->department->id,
        'name' => 'Admin Tenant '.uniqid(),
        'email' => 'admin.'.uniqid().'@sirius.io',
        'password' => bcrypt('password'),
        'role' => UserRole::CompanyAdmin,
        'is_active' => true,
    ]);

    $this->agent = User::create([
        'company_id' => $this->company->id,
        'department_id' => $this->department->id,
        'name' => 'Teknisi Support '.uniqid(),
        'email' => 'agent.'.uniqid().'@sirius.io',
        'password' => bcrypt('password'),
        'role' => UserRole::Agent,
        'is_active' => true,
    ]);

    $this->requester = User::create([
        'company_id' => $this->company->id,
        'department_id' => $this->department->id,
        'name' => 'Karyawan Pelapor '.uniqid(),
        'email' => 'requester.'.uniqid().'@sirius.io',
        'password' => bcrypt('password'),
        'role' => UserRole::Requester,
        'is_active' => true,
    ]);
});

test('superadmin can access companies page', function () {
    $response = $this->actingAs($this->superadmin)->get('/companies');

    $response->assertStatus(200);
});

test('company_admin is forbidden from accessing companies page', function () {
    $response = $this->actingAs($this->companyAdmin)->get('/companies');

    $response->assertStatus(403);
});

test('agent is forbidden from accessing companies page', function () {
    $response = $this->actingAs($this->agent)->get('/companies');

    $response->assertStatus(403);
});

test('requester is forbidden from accessing companies page', function () {
    $response = $this->actingAs($this->requester)->get('/companies');

    $response->assertStatus(403);
});

test('company_admin can access departments, categories, and sla-policies', function () {
    $this->actingAs($this->companyAdmin)->get('/departments')->assertStatus(200);
    $this->actingAs($this->companyAdmin)->get('/categories')->assertStatus(200);
    $this->actingAs($this->companyAdmin)->get('/sla-policies')->assertStatus(200);
});

test('agent is forbidden from accessing sla-policies', function () {
    $response = $this->actingAs($this->agent)->get('/sla-policies');

    $response->assertStatus(403);
});

test('agent can access canned-responses and tickets', function () {
    $this->actingAs($this->agent)->get('/canned-responses')->assertStatus(200);
    $this->actingAs($this->agent)->get('/tickets')->assertStatus(200);
});

test('requester is forbidden from accessing canned-responses', function () {
    $response = $this->actingAs($this->requester)->get('/canned-responses');

    $response->assertStatus(403);
});

test('NavigationService filters sidebar menus based on user role', function () {
    $service = new NavigationService;

    // 1. Superadmin navigation
    $superadminNav = $service->getNavigation($this->superadmin);
    $superadminTitles = [];
    foreach ($superadminNav as $sec) {
        foreach ($sec['items'] as $item) {
            $superadminTitles[] = $item['title'];
        }
    }
    expect($superadminTitles)->toContain('Perusahaan (Tenant)');
    expect($superadminTitles)->toContain('Departemen');
    expect($superadminTitles)->toContain('Antrean Tiket');

    // 2. Company Admin navigation
    $adminNav = $service->getNavigation($this->companyAdmin);
    $adminTitles = [];
    foreach ($adminNav as $sec) {
        foreach ($sec['items'] as $item) {
            $adminTitles[] = $item['title'];
        }
    }
    expect($adminTitles)->not->toContain('Perusahaan (Tenant)');
    expect($adminTitles)->toContain('Departemen');
    expect($adminTitles)->toContain('Kebijakan SLA');
    expect($adminTitles)->toContain('Antrean Tiket');

    // 3. Requester navigation
    $requesterNav = $service->getNavigation($this->requester);
    $requesterTitles = [];
    $requesterHeadings = [];
    foreach ($requesterNav as $sec) {
        if ($sec['heading'] !== null) {
            $requesterHeadings[] = $sec['heading'];
        }
        foreach ($sec['items'] as $item) {
            $requesterTitles[] = $item['title'];
        }
    }
    expect($requesterHeadings)->toContain('Portal Layanan Mandiri');
    expect($requesterHeadings)->not->toContain('Master Data Organisasi');
    expect($requesterTitles)->toContain('Tiket Permintaan Saya');
    expect($requesterTitles)->not->toContain('Kebijakan SLA');
    expect($requesterTitles)->not->toContain('Perusahaan (Tenant)');
});

test('header user menu renders profile and sign out without role switcher', function () {
    $response = $this->actingAs($this->companyAdmin)->get('/dashboard');

    $response->assertOk();
    $response->assertSee('Keluar (Sign Out)');
    $response->assertDontSee('Ganti Peran (Role Switcher)');
    $response->assertDontSee('Role Switcher');
});

test('sidebar renders dynamic menus matching user role in HTML', function () {
    // Sebagai Requester
    $resRequester = $this->actingAs($this->requester)->get('/tickets');
    $resRequester->assertStatus(200)
        ->assertSee('Portal Layanan Mandiri')
        ->assertSee('Tiket Permintaan Saya')
        ->assertDontSee('Perusahaan (Tenant)')
        ->assertDontSee('Kebijakan SLA');

    // Sebagai Superadmin
    $resSuper = $this->actingAs($this->superadmin)->get('/tickets');
    $resSuper->assertStatus(200)
        ->assertSee('Perusahaan (Tenant)')
        ->assertSee('Master Data Organisasi');
});
