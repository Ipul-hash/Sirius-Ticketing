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
    $this->company = Company::first() ?? Company::create([
        'name' => 'Sirius Dashboard Corp',
        'slug' => 'sirius-dashboard-'.uniqid(),
        'domain' => 'dashboard.'.uniqid().'.io',
        'plan' => CompanyPlan::Enterprise,
        'status' => CompanyStatus::Active,
    ]);

    $this->department = Department::first() ?? Department::create([
        'company_id' => $this->company->id,
        'name' => 'IT Operations',
        'slug' => 'it-ops-'.uniqid(),
        'is_active' => true,
    ]);

    $this->ticketCategory = TicketCategory::first() ?? TicketCategory::create([
        'company_id' => $this->company->id,
        'name' => 'Hardware Issue',
        'slug' => 'hardware-'.uniqid(),
        'is_active' => true,
    ]);

    $this->superadmin = User::create([
        'company_id' => $this->company->id,
        'department_id' => $this->department->id,
        'name' => 'Superadmin User '.uniqid(),
        'email' => 'superadmin.'.uniqid().'@sirius.io',
        'password' => bcrypt('password'),
        'role' => UserRole::Superadmin,
        'is_active' => true,
    ]);

    $this->companyAdmin = User::create([
        'company_id' => $this->company->id,
        'department_id' => $this->department->id,
        'name' => 'Company Admin User '.uniqid(),
        'email' => 'admin.'.uniqid().'@sirius.io',
        'password' => bcrypt('password'),
        'role' => UserRole::CompanyAdmin,
        'is_active' => true,
    ]);

    $this->agent = User::create([
        'company_id' => $this->company->id,
        'department_id' => $this->department->id,
        'name' => 'Agent Tech User '.uniqid(),
        'email' => 'agent.'.uniqid().'@sirius.io',
        'password' => bcrypt('password'),
        'role' => UserRole::Agent,
        'is_active' => true,
    ]);

    $this->requester = User::create([
        'company_id' => $this->company->id,
        'department_id' => $this->department->id,
        'name' => 'Requester User '.uniqid(),
        'email' => 'requester.'.uniqid().'@sirius.io',
        'password' => bcrypt('password'),
        'role' => UserRole::Requester,
        'is_active' => true,
    ]);
});

test('superadmin can access dashboard and view saas metrics and recent tenants', function () {
    $response = $this->actingAs($this->superadmin)->get('/dashboard');

    $response->assertOk();
    $response->assertSee('Platform Owner');
    $response->assertSee('Total Tenant Terdaftar');
    $response->assertSee('Perusahaan / Tenant Terbaru');
    $response->assertSee('Aktivitas Tiket Global Terbaru');
});

test('company admin can access dashboard and view tenant operational metrics and agent workload', function () {
    // Buat tiket urgent
    Ticket::create([
        'company_id' => $this->company->id,
        'department_id' => $this->department->id,
        'requester_id' => $this->requester->id,
        'assigned_to' => $this->agent->id,
        'category_id' => $this->ticketCategory->id,
        'ticket_number' => 'TCK-URGENT-'.uniqid(),
        'subject' => 'Server Switch Down Urgent',
        'description' => 'Gedung utama kehilangan koneksi jaringan',
        'status' => TicketStatus::InProgress,
        'priority' => TicketPriority::Urgent,
    ]);

    $response = $this->actingAs($this->companyAdmin)->get('/dashboard');

    $response->assertOk();
    $response->assertSee('Admin Tenant');
    $response->assertSee('Tiket Aktif Berjalan');
    $response->assertSee('Beban Kerja Teknisi');
    $response->assertSee('Server Switch Down Urgent');
});

test('agent can access dashboard and view assigned tasks and unassigned queue', function () {
    // Tiket ditugaskan ke agent
    $myTicket = Ticket::create([
        'company_id' => $this->company->id,
        'department_id' => $this->department->id,
        'requester_id' => $this->requester->id,
        'assigned_to' => $this->agent->id,
        'category_id' => $this->ticketCategory->id,
        'ticket_number' => 'TCK-AGENT-'.uniqid(),
        'subject' => 'Laptop BlueScreen Perlu Diperbaiki',
        'description' => 'Muncul kode BSOD saat booting Windows',
        'status' => TicketStatus::InProgress,
        'priority' => TicketPriority::High,
    ]);

    // Tiket belum ditugaskan (unassigned)
    $unassignedTicket = Ticket::create([
        'company_id' => $this->company->id,
        'department_id' => $this->department->id,
        'requester_id' => $this->requester->id,
        'assigned_to' => null,
        'category_id' => $this->ticketCategory->id,
        'ticket_number' => 'TCK-UNASSIGNED-'.uniqid(),
        'subject' => 'Printer Lantai 2 Paper Jam',
        'description' => 'Kertas tersangkut di roller penarik',
        'status' => TicketStatus::Open,
        'priority' => TicketPriority::Medium,
    ]);

    $response = $this->actingAs($this->agent)->get('/dashboard');

    $response->assertOk();
    $response->assertSee('IT Specialist Workspace');
    $response->assertSee('Tiket Ditugaskan ke Saya');
    $response->assertSee('Antrean Tiket Baru (Unassigned)');
    $response->assertSee($myTicket->subject);
    $response->assertSee($unassignedTicket->subject);
});

test('requester can access dashboard and view self-service portal, recent tickets, and assigned assets', function () {
    // Buat aset yang ditugaskan ke requester
    $asset = CompanyAsset::create([
        'company_id' => $this->company->id,
        'department_id' => $this->department->id,
        'assigned_to_user_id' => $this->requester->id,
        'asset_tag' => 'AST-LAPTOP-'.uniqid(),
        'name' => 'MacBook Pro M3 Max 16 inch',
        'category' => AssetCategory::Hardware,
        'serial_number' => 'C02-REQ-'.uniqid(),
        'status' => AssetStatus::InUse,
    ]);

    // Buat tiket milik requester
    $ticket = Ticket::create([
        'company_id' => $this->company->id,
        'department_id' => $this->department->id,
        'requester_id' => $this->requester->id,
        'assigned_to' => $this->agent->id,
        'category_id' => $this->ticketCategory->id,
        'ticket_number' => 'TCK-REQ-'.uniqid(),
        'subject' => 'Permintaan Akses VPN Kantor',
        'description' => 'Butuh akses VPN untuk WFH besok',
        'status' => TicketStatus::InProgress,
        'priority' => TicketPriority::Medium,
    ]);

    $response = $this->actingAs($this->requester)->get('/dashboard');

    $response->assertOk();
    $response->assertSee('Portal Layanan Mandiri');
    $response->assertSee('Tiket Permohonan Bantuan Saya');
    $response->assertSee('Aset Kantor Saya');
    $response->assertSee($ticket->subject);
    $response->assertSee($asset->name);
    $response->assertSee($asset->asset_tag);
});

test('root URL redirects or renders the active user role dashboard', function () {
    $response = $this->actingAs($this->requester)->get('/');

    $response->assertOk();
    $response->assertSee('Portal Layanan Mandiri');
});

test('each role is rendered their respective dashboard view', function () {
    // 1. Company Admin
    $resAdmin = $this->actingAs($this->companyAdmin)->get('/dashboard');
    $resAdmin->assertOk()->assertSee('Admin Tenant');

    // 2. Agent
    $resAgent = $this->actingAs($this->agent)->get('/dashboard');
    $resAgent->assertOk()->assertSee('IT Specialist Workspace');

    // 3. Requester
    $resReq = $this->actingAs($this->requester)->get('/dashboard');
    $resReq->assertOk()->assertSee('Portal Layanan Mandiri');

    // 4. Superadmin
    $resSuper = $this->actingAs($this->superadmin)->get('/dashboard');
    $resSuper->assertOk()->assertSee('Platform Owner');
});
