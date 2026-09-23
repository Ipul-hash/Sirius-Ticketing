<?php

use App\Enums\AssetCategory;
use App\Enums\AssetStatus;
use App\Enums\CompanyPlan;
use App\Enums\CompanyStatus;
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Models\Company;
use App\Models\CompanyAsset;
use App\Models\Department;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;

uses(DatabaseTransactions::class);

beforeEach(function () {
    // Tenant A (PT Sugiharto)
    $this->companyA = Company::create([
        'name' => 'PT Sugiharto Tech '.uniqid(),
        'slug' => 'sugiharto-'.uniqid(),
        'domain' => 'sutech-'.uniqid().'.id',
        'plan' => CompanyPlan::Professional,
        'status' => CompanyStatus::Active,
    ]);

    $this->deptA = Department::create([
        'company_id' => $this->companyA->id,
        'name' => 'IT Dept Sutech '.uniqid(),
        'slug' => 'it-sutech-'.uniqid(),
        'is_active' => true,
    ]);

    $this->userA = User::create([
        'company_id' => $this->companyA->id,
        'department_id' => $this->deptA->id,
        'name' => 'Admin Sutech '.uniqid(),
        'email' => 'admin.sutech.'.uniqid().'@sutech.id',
        'password' => bcrypt('password'),
        'role' => UserRole::CompanyAdmin,
        'is_active' => true,
    ]);

    $this->categoryA = TicketCategory::create([
        'company_id' => $this->companyA->id,
        'department_id' => $this->deptA->id,
        'name' => 'Kategori Sutech '.uniqid(),
        'slug' => 'kat-sutech-'.uniqid(),
        'is_active' => true,
    ]);

    $this->assetA = CompanyAsset::create([
        'company_id' => $this->companyA->id,
        'department_id' => $this->deptA->id,
        'assigned_to_user_id' => $this->userA->id,
        'name' => 'MacBook Sutech '.uniqid(),
        'asset_tag' => 'AST-A-'.uniqid(),
        'category' => AssetCategory::Hardware,
        'status' => AssetStatus::InUse,
    ]);

    $this->ticketA = Ticket::create([
        'company_id' => $this->companyA->id,
        'department_id' => $this->deptA->id,
        'category_id' => $this->categoryA->id,
        'requester_id' => $this->userA->id,
        'ticket_number' => 'TCK-A-'.strtoupper(uniqid()),
        'subject' => 'Tiket Sutech '.uniqid(),
        'description' => 'Deskripsi tiket sutech',
        'priority' => TicketPriority::High,
        'status' => TicketStatus::Open,
    ]);

    // Tenant B (PT Nusantara Mediatama)
    $this->companyB = Company::create([
        'name' => 'PT Nusantara Media '.uniqid(),
        'slug' => 'nusantara-'.uniqid(),
        'domain' => 'nusantara-'.uniqid().'.id',
        'plan' => CompanyPlan::Enterprise,
        'status' => CompanyStatus::Active,
    ]);

    $this->deptB = Department::create([
        'company_id' => $this->companyB->id,
        'name' => 'HR Dept Nusantara '.uniqid(),
        'slug' => 'hr-nusantara-'.uniqid(),
        'is_active' => true,
    ]);

    $this->userB = User::create([
        'company_id' => $this->companyB->id,
        'department_id' => $this->deptB->id,
        'name' => 'Admin Nusantara '.uniqid(),
        'email' => 'admin.nusantara.'.uniqid().'@nusantara.id',
        'password' => bcrypt('password'),
        'role' => UserRole::CompanyAdmin,
        'is_active' => true,
    ]);

    $this->categoryB = TicketCategory::create([
        'company_id' => $this->companyB->id,
        'department_id' => $this->deptB->id,
        'name' => 'Kategori Nusantara '.uniqid(),
        'slug' => 'kat-nusantara-'.uniqid(),
        'is_active' => true,
    ]);

    $this->assetB = CompanyAsset::create([
        'company_id' => $this->companyB->id,
        'department_id' => $this->deptB->id,
        'assigned_to_user_id' => $this->userB->id,
        'name' => 'Server Nusantara '.uniqid(),
        'asset_tag' => 'AST-B-'.uniqid(),
        'category' => AssetCategory::Server,
        'status' => AssetStatus::InUse,
    ]);

    $this->ticketB = Ticket::create([
        'company_id' => $this->companyB->id,
        'department_id' => $this->deptB->id,
        'category_id' => $this->categoryB->id,
        'requester_id' => $this->userB->id,
        'ticket_number' => 'TCK-B-'.strtoupper(uniqid()),
        'subject' => 'Tiket Nusantara '.uniqid(),
        'description' => 'Deskripsi tiket nusantara',
        'priority' => TicketPriority::Medium,
        'status' => TicketStatus::Open,
    ]);

    // Superadmin
    $this->superadmin = User::create([
        'company_id' => $this->companyA->id,
        'name' => 'Superadmin '.uniqid(),
        'email' => 'superadmin.'.uniqid().'@sirius.io',
        'password' => bcrypt('password'),
        'role' => UserRole::Superadmin,
        'is_active' => true,
    ]);
});

test('company admin can only view their own company departments and lead user list is strictly scoped', function () {
    $response = $this->actingAs($this->userA)->get('/departments');

    $response->assertOk();
    $response->assertSee($this->deptA->name);
    $response->assertDontSee($this->deptB->name);

    // Verifikasi view data $users hanya berisi user dari companyA
    $usersInView = $response->viewData('users');
    expect($usersInView->pluck('id'))->toContain($this->userA->id)
        ->and($usersInView->pluck('id'))->not->toContain($this->userB->id);
});

test('company admin can only view their own company ticket categories', function () {
    $response = $this->actingAs($this->userA)->get('/categories');

    $response->assertOk();
    $response->assertSee($this->categoryA->name);
    $response->assertDontSee($this->categoryB->name);
});

test('company admin can only configure SLA for their own company and cannot see tenant switcher dropdown', function () {
    $response = $this->actingAs($this->userA)->get('/sla-policies?company_id='.$this->companyB->id);

    $response->assertOk();
    // Non-superadmin dipaksa melihat companyA meskipun query param company_id=companyB diberikan
    $selectedCompany = $response->viewData('selectedCompany');
    expect($selectedCompany->id)->toBe($this->companyA->id);

    // Dropdown switcher tenant tidak boleh tampil untuk non-superadmin
    $response->assertDontSee('id="sla_tenant_switcher"', false);
    $response->assertSee($this->companyA->name);
});

test('company admin can only view their own company assets', function () {
    $response = $this->actingAs($this->userA)->get('/company-assets');

    $response->assertOk();
    $response->assertSee($this->assetA->name);
    $response->assertDontSee($this->assetB->name);
});

test('company admin can only view their own company tickets and cannot access other company ticket detail', function () {
    $response = $this->actingAs($this->userA)->get('/tickets');

    $response->assertOk();
    $response->assertSee($this->ticketA->ticket_number);
    $response->assertDontSee($this->ticketB->ticket_number);

    // Detail tiket milik perusahaannya sendiri -> OK
    $this->actingAs($this->userA)->get('/tickets/'.$this->ticketA->id)->assertOk();

    // Detail tiket milik perusahaan lain -> 404 (Not Found / Abort)
    $this->actingAs($this->userA)->get('/tickets/'.$this->ticketB->id)->assertNotFound();
});

test('superadmin can access all departments across tenants and can filter by tenant', function () {
    $response = $this->actingAs($this->superadmin)->get('/departments');

    $response->assertOk();
    $response->assertSee($this->deptA->name);
    $response->assertSee($this->deptB->name);

    // Filter per tenant
    $filteredResponse = $this->actingAs($this->superadmin)->get('/departments?company_id='.$this->companyA->id);
    $filteredResponse->assertOk();
    $filteredResponse->assertSee($this->deptA->name);
    $filteredResponse->assertDontSee($this->deptB->name);
});

test('api department creation enforces tenant company_id for non-superadmin', function () {
    $response = $this->actingAs($this->userA)->postJson('/api/v1/departments', [
        'name' => 'New R&D Dept '.uniqid(),
        'company_id' => $this->companyB->id, // Mencoba memalsukan tenant B
    ]);

    $response->assertCreated();
    // Otomatis dipaksa ke companyA
    expect($response->json('data.company_id'))->toBe($this->companyA->id);
});

test('requester user sees their own account locked in create ticket modal', function () {
    $requester = User::create([
        'company_id' => $this->companyA->id,
        'department_id' => $this->deptA->id,
        'name' => 'Karyawan Sutech '.uniqid(),
        'email' => 'karyawan.'.uniqid().'@sutech.id',
        'password' => bcrypt('password'),
        'role' => UserRole::Requester,
        'is_active' => true,
    ]);

    $response = $this->actingAs($requester)->get('/tickets');

    $response->assertOk();
    // Harus melihat akunnya sendiri di input readonly
    $response->assertSee($requester->name.' ('.$requester->email.')');
    // Tidak boleh melihat dropdown pilihan pelapor
    $response->assertDontSee('-- Pilih Pelapor --');
});

test('requester submitting ticket creation is forced to use their own account as requester_id', function () {
    $requester = User::create([
        'company_id' => $this->companyA->id,
        'department_id' => $this->deptA->id,
        'name' => 'Karyawan Pemohon '.uniqid(),
        'email' => 'pemohon.'.uniqid().'@sutech.id',
        'password' => bcrypt('password'),
        'role' => UserRole::Requester,
        'is_active' => true,
    ]);

    // Requester mencoba mengirim requester_id orang lain ($this->userA->id)
    $response = $this->actingAs($requester)->postJson('/api/v1/tickets', [
        'category_id' => $this->categoryA->id,
        'department_id' => $this->deptA->id,
        'subject' => 'Tiket Uji Karyawan',
        'description' => 'Keluhan koneksi internet lambat',
        'requester_id' => $this->userA->id, // Mencoba spoofing
        'assigned_to' => $this->userA->id, // Mencoba assign teknisi sendiri
    ]);

    $response->assertCreated();
    $data = $response->json('data');

    // Wajib dipaksa ke akun requester yang sedang login
    expect($data['requester_id'])->toBe($requester->id)
        ->and($data['assigned_to'])->toBeNull();
});

test('requester cannot see quick assign or quick status buttons on tickets index and show page', function () {
    $requester = User::create([
        'company_id' => $this->companyA->id,
        'department_id' => $this->deptA->id,
        'name' => 'Requester Views '.uniqid(),
        'email' => 'req.views.'.uniqid().'@sutech.id',
        'password' => bcrypt('password'),
        'role' => UserRole::Requester,
        'is_active' => true,
    ]);

    // Halaman list tiket
    $responseIndex = $this->actingAs($requester)->get('/tickets');
    $responseIndex->assertOk();
    $responseIndex->assertDontSee('btn-quick-assign');
    $responseIndex->assertDontSee('btn-quick-status');
    $responseIndex->assertDontSee('btn-delete-ticket');

    // Halaman detail tiket
    $responseShow = $this->actingAs($requester)->get('/tickets/'.$this->ticketA->id);
    $responseShow->assertOk();
    $responseShow->assertDontSee('btn-quick-assign');
    $responseShow->assertDontSee('btn-quick-status');
});

test('requester is forbidden from assigning technician via api', function () {
    $requester = User::create([
        'company_id' => $this->companyA->id,
        'department_id' => $this->deptA->id,
        'name' => 'Requester Assign '.uniqid(),
        'email' => 'req.assign.'.uniqid().'@sutech.id',
        'password' => bcrypt('password'),
        'role' => UserRole::Requester,
        'is_active' => true,
    ]);

    $response = $this->actingAs($requester)->patchJson("/api/v1/tickets/{$this->ticketA->id}/assign", [
        'assigned_to' => $this->userA->id,
    ]);

    $response->assertForbidden()
        ->assertJsonPath('success', false);
});

test('requester is forbidden from resolving ticket via api', function () {
    $requester = User::create([
        'company_id' => $this->companyA->id,
        'department_id' => $this->deptA->id,
        'name' => 'Requester Resolve '.uniqid(),
        'email' => 'req.resolve.'.uniqid().'@sutech.id',
        'password' => bcrypt('password'),
        'role' => UserRole::Requester,
        'is_active' => true,
    ]);

    $response = $this->actingAs($requester)->patchJson("/api/v1/tickets/{$this->ticketA->id}/status", [
        'status' => TicketStatus::Resolved->value,
    ]);

    $response->assertForbidden()
        ->assertJsonPath('success', false);
});

test('agent cannot assign ticket to another user via api', function () {
    $agent1 = User::create([
        'company_id' => $this->companyA->id,
        'department_id' => $this->deptA->id,
        'name' => 'Agent One '.uniqid(),
        'email' => 'agent1.'.uniqid().'@sutech.id',
        'password' => bcrypt('password'),
        'role' => UserRole::Agent,
        'is_active' => true,
    ]);

    $agent2 = User::create([
        'company_id' => $this->companyA->id,
        'department_id' => $this->deptA->id,
        'name' => 'Agent Two '.uniqid(),
        'email' => 'agent2.'.uniqid().'@sutech.id',
        'password' => bcrypt('password'),
        'role' => UserRole::Agent,
        'is_active' => true,
    ]);

    $unassignedTicket = Ticket::create([
        'company_id' => $this->companyA->id,
        'ticket_number' => 'TCK-'.uniqid(),
        'subject' => 'Unassigned Ticket '.uniqid(),
        'description' => 'Test unassigned',
        'category_id' => $this->categoryA->id,
        'department_id' => $this->deptA->id,
        'requester_id' => $this->userA->id,
        'assigned_to' => null,
        'status' => TicketStatus::Open,
        'priority' => TicketPriority::Medium,
    ]);

    $response = $this->actingAs($agent1)->patchJson("/api/v1/tickets/{$unassignedTicket->id}/assign", [
        'assigned_to' => $agent2->id,
    ]);

    $response->assertForbidden()
        ->assertJsonPath('success', false);
});

test('agent can claim unassigned ticket for themselves via api', function () {
    $agent = User::create([
        'company_id' => $this->companyA->id,
        'department_id' => $this->deptA->id,
        'name' => 'Agent Claim '.uniqid(),
        'email' => 'agent.claim.'.uniqid().'@sutech.id',
        'password' => bcrypt('password'),
        'role' => UserRole::Agent,
        'is_active' => true,
    ]);

    $unassignedTicket = Ticket::create([
        'company_id' => $this->companyA->id,
        'ticket_number' => 'TCK-'.uniqid(),
        'subject' => 'Unassigned Ticket Claim '.uniqid(),
        'description' => 'Test claim ticket',
        'category_id' => $this->categoryA->id,
        'department_id' => $this->deptA->id,
        'requester_id' => $this->userA->id,
        'assigned_to' => null,
        'status' => TicketStatus::Open,
        'priority' => TicketPriority::Medium,
    ]);

    $response = $this->actingAs($agent)->patchJson("/api/v1/tickets/{$unassignedTicket->id}/assign", [
        'assigned_to' => $agent->id,
    ]);

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.assigned_to', $agent->id);

    $unassignedTicket->refresh();
    expect($unassignedTicket->assigned_to)->toBe($agent->id)
        ->and($unassignedTicket->status)->toBe(TicketStatus::InProgress);
});

test('agent cannot claim ticket already assigned to another technician via api', function () {
    $agent1 = User::create([
        'company_id' => $this->companyA->id,
        'department_id' => $this->deptA->id,
        'name' => 'Agent Occupied '.uniqid(),
        'email' => 'agent.occ.'.uniqid().'@sutech.id',
        'password' => bcrypt('password'),
        'role' => UserRole::Agent,
        'is_active' => true,
    ]);

    $agent2 = User::create([
        'company_id' => $this->companyA->id,
        'department_id' => $this->deptA->id,
        'name' => 'Agent Stealer '.uniqid(),
        'email' => 'agent.steal.'.uniqid().'@sutech.id',
        'password' => bcrypt('password'),
        'role' => UserRole::Agent,
        'is_active' => true,
    ]);

    $assignedTicket = Ticket::create([
        'company_id' => $this->companyA->id,
        'ticket_number' => 'TCK-'.uniqid(),
        'subject' => 'Assigned Ticket '.uniqid(),
        'description' => 'Already assigned',
        'category_id' => $this->categoryA->id,
        'department_id' => $this->deptA->id,
        'requester_id' => $this->userA->id,
        'assigned_to' => $agent1->id,
        'status' => TicketStatus::InProgress,
        'priority' => TicketPriority::Medium,
    ]);

    $response = $this->actingAs($agent2)->patchJson("/api/v1/tickets/{$assignedTicket->id}/assign", [
        'assigned_to' => $agent2->id,
    ]);

    $response->assertForbidden()
        ->assertJsonPath('success', false);
});

test('agent sees Ambil Tiket button for unassigned ticket and cannot see quick assign modal', function () {
    $agent = User::create([
        'company_id' => $this->companyA->id,
        'department_id' => $this->deptA->id,
        'name' => 'Agent UI '.uniqid(),
        'email' => 'agent.ui.'.uniqid().'@sutech.id',
        'password' => bcrypt('password'),
        'role' => UserRole::Agent,
        'is_active' => true,
    ]);

    $unassignedTicket = Ticket::create([
        'company_id' => $this->companyA->id,
        'ticket_number' => 'TCK-'.uniqid(),
        'subject' => 'Unassigned Ticket UI '.uniqid(),
        'description' => 'Unassigned for UI test',
        'category_id' => $this->categoryA->id,
        'department_id' => $this->deptA->id,
        'requester_id' => $this->userA->id,
        'assigned_to' => null,
        'status' => TicketStatus::Open,
        'priority' => TicketPriority::Medium,
    ]);

    $responseIndex = $this->actingAs($agent)->get('/tickets');
    $responseIndex->assertOk();
    $responseIndex->assertSee('btn-claim-ticket');
    $responseIndex->assertDontSee('kt_modal_assign_ticket');

    $responseShow = $this->actingAs($agent)->get('/tickets/'.$unassignedTicket->id);
    $responseShow->assertOk();
    $responseShow->assertSee('btn-claim-ticket');
    $responseShow->assertDontSee('kt_modal_assign_ticket');
});
