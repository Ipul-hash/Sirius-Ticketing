<?php

use App\Enums\ApprovalStatus;
use App\Enums\TicketApprovalStatus;
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Models\Company;
use App\Models\Department;
use App\Models\Ticket;
use App\Models\TicketApproval;
use App\Models\TicketCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;

uses(DatabaseTransactions::class);

beforeEach(function () {
    $this->company = Company::first() ?? Company::create([
        'name' => 'Sirius Approval Corp',
        'slug' => 'sirius-approval',
        'domain' => 'approval.sirius.io',
        'plan' => 'enterprise',
        'status' => 'active',
    ]);

    $this->approver = User::where('role', UserRole::CompanyAdmin)->first() ?? User::create([
        'company_id' => $this->company->id,
        'name' => 'Manager Approver',
        'email' => 'manager.approver@sirius.io',
        'password' => bcrypt('password'),
        'role' => UserRole::CompanyAdmin,
        'is_active' => true,
    ]);

    $this->department = Department::first() ?? Department::create([
        'company_id' => $this->company->id,
        'lead_user_id' => $this->approver->id,
        'name' => 'Finance & Procurement',
        'slug' => 'procurement',
        'is_active' => true,
    ]);

    $this->approvalCategory = TicketCategory::create([
        'company_id' => $this->company->id,
        'department_id' => $this->department->id,
        'name' => 'Pengadaan Laptop Baru (Budget > 15jt)',
        'slug' => 'new-laptop-request-'.uniqid(),
        'default_priority' => 'high',
        'requires_approval' => true,
        'is_active' => true,
    ]);

    $this->requester = User::where('role', UserRole::Requester)->first() ?? User::create([
        'company_id' => $this->company->id,
        'department_id' => $this->department->id,
        'name' => 'Requester Approval Test',
        'email' => 'requester.approval@sirius.io',
        'password' => bcrypt('password'),
        'role' => UserRole::Requester,
        'is_active' => true,
    ]);

    $this->actingAs($this->approver);
});

test('ticket created with requires_approval category automatically sets status pending_approval and creates TicketApproval', function () {
    $response = $this->postJson('/api/v1/tickets', [
        'company_id' => $this->company->id,
        'category_id' => $this->approvalCategory->id,
        'department_id' => $this->department->id,
        'requester_id' => $this->requester->id,
        'subject' => 'Request Penggantian Macbook M3 Pro',
        'description' => 'Memerlukan spesifikasi baru untuk kompilasi mobile app.',
        'priority' => 'high',
    ]);

    $response->assertStatus(201);
    $ticketId = $response->json('data.id');

    $ticket = Ticket::find($ticketId);
    expect($ticket->status)->toBe(TicketStatus::PendingApproval)
        ->and($ticket->approval_status)->toBe(TicketApprovalStatus::Pending);

    $this->assertDatabaseHas('ticket_approvals', [
        'ticket_id' => $ticketId,
        'status' => 'pending',
    ]);
});

test('can list pending approval tickets via api', function () {
    $ticket = Ticket::create([
        'company_id' => $this->company->id,
        'ticket_number' => 'TCK-2026-APP01',
        'subject' => 'Pembelian Lisensi IDE JetBrains',
        'description' => 'Memerlukan lisensi PhpStorm untuk tim backend.',
        'category_id' => $this->approvalCategory->id,
        'department_id' => $this->department->id,
        'requester_id' => $this->requester->id,
        'status' => TicketStatus::PendingApproval,
        'priority' => TicketPriority::High,
        'approval_status' => TicketApprovalStatus::Pending,
    ]);

    TicketApproval::create([
        'ticket_id' => $ticket->id,
        'approver_id' => $this->approver->id,
        'status' => ApprovalStatus::Pending,
    ]);

    $response = $this->getJson('/api/v1/approvals/pending');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
        ]);

    $ticketNumbers = collect($response->json('data'))->pluck('ticket_number')->all();
    expect($ticketNumbers)->toContain('TCK-2026-APP01');
});

test('approver can approve ticket, transitioning status to open or in_progress', function () {
    $ticket = Ticket::create([
        'company_id' => $this->company->id,
        'ticket_number' => 'TCK-2026-APP02',
        'subject' => 'Akses Server Production Database',
        'description' => 'Migrasi skema database tengah malam.',
        'category_id' => $this->approvalCategory->id,
        'department_id' => $this->department->id,
        'requester_id' => $this->requester->id,
        'status' => TicketStatus::PendingApproval,
        'priority' => TicketPriority::Urgent,
        'approval_status' => TicketApprovalStatus::Pending,
    ]);

    $approval = TicketApproval::create([
        'ticket_id' => $ticket->id,
        'approver_id' => $this->approver->id,
        'status' => ApprovalStatus::Pending,
    ]);

    $response = $this->postJson("/api/v1/tickets/{$ticket->id}/approve", [
        'approver_id' => $this->approver->id,
        'reason_notes' => 'Disetujui untuk maintenance window pukul 00:00 - 02:00 WIB.',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'data' => [
                'status' => 'open',
                'approval_status' => 'approved',
            ],
        ]);

    $ticket->refresh();
    expect($ticket->status)->toBe(TicketStatus::Open)
        ->and($ticket->approval_status)->toBe(TicketApprovalStatus::Approved);

    $approval->refresh();
    expect($approval->status)->toBe(ApprovalStatus::Approved)
        ->and($approval->decided_at)->not->toBeNull();

    $this->assertDatabaseHas('ticket_activities', [
        'ticket_id' => $ticket->id,
        'activity_type' => 'ticket_approved',
    ]);
});

test('approver can reject ticket with mandatory reason, closing the ticket', function () {
    $ticket = Ticket::create([
        'company_id' => $this->company->id,
        'ticket_number' => 'TCK-2026-APP03',
        'subject' => 'Upgrade RAM Server 128GB',
        'description' => 'Server staging kehabisan memori.',
        'category_id' => $this->approvalCategory->id,
        'department_id' => $this->department->id,
        'requester_id' => $this->requester->id,
        'status' => TicketStatus::PendingApproval,
        'priority' => TicketPriority::High,
        'approval_status' => TicketApprovalStatus::Pending,
    ]);

    TicketApproval::create([
        'ticket_id' => $ticket->id,
        'approver_id' => $this->approver->id,
        'status' => ApprovalStatus::Pending,
    ]);

    $response = $this->postJson("/api/v1/tickets/{$ticket->id}/reject", [
        'approver_id' => $this->approver->id,
        'reason_notes' => 'Ditolak: Alokasi anggaran hardware tahun ini telah habis. Gunakan optimasi query terlebih dahulu.',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'data' => [
                'status' => 'closed',
                'approval_status' => 'rejected',
            ],
        ]);

    $ticket->refresh();
    expect($ticket->status)->toBe(TicketStatus::Closed)
        ->and($ticket->approval_status)->toBe(TicketApprovalStatus::Rejected)
        ->and($ticket->closed_at)->not->toBeNull();

    $this->assertDatabaseHas('ticket_activities', [
        'ticket_id' => $ticket->id,
        'activity_type' => 'ticket_rejected',
    ]);
});

test('halaman web approvals dapat diakses dan menampilkan tiket yang membutuhkan otorisasi', function () {
    $ticket = Ticket::create([
        'company_id' => $this->company->id,
        'ticket_number' => 'TCK-2026-WEB-APP',
        'subject' => 'Permintaan Lisensi Figma Enterprise',
        'description' => 'Dibutuhkan untuk tim UI/UX.',
        'category_id' => $this->approvalCategory->id,
        'department_id' => $this->department->id,
        'requester_id' => $this->requester->id,
        'status' => TicketStatus::PendingApproval,
        'priority' => TicketPriority::Medium,
        'approval_status' => TicketApprovalStatus::Pending,
    ]);

    $response = $this->get('/approvals');

    $response->assertStatus(200)
        ->assertViewIs('tickets.approvals')
        ->assertSee('Permintaan Lisensi Figma Enterprise')
        ->assertSee('TCK-2026-WEB-APP');
});

test('penugasan teknisi dicegah jika tiket masih berstatus pending approval', function () {
    $ticket = Ticket::create([
        'company_id' => $this->company->id,
        'ticket_number' => 'TCK-2026-GUARD-1',
        'subject' => 'Pembelian Monitor UltraWide 34 Inch',
        'description' => 'Untuk kebutuhan coding.',
        'category_id' => $this->approvalCategory->id,
        'department_id' => $this->department->id,
        'requester_id' => $this->requester->id,
        'status' => TicketStatus::PendingApproval,
        'priority' => TicketPriority::High,
        'approval_status' => TicketApprovalStatus::Pending,
    ]);

    $agent = User::create([
        'company_id' => $this->company->id,
        'name' => 'Teknisi Test',
        'email' => 'teknisi.test.'.uniqid().'@sirius.io',
        'password' => bcrypt('password'),
        'role' => UserRole::Agent,
        'is_active' => true,
    ]);

    $response = $this->patchJson("/api/v1/tickets/{$ticket->id}/assign", [
        'assigned_to' => $agent->id,
        'notes' => 'Coba assign sebelum di-approve',
    ]);

    $response->assertStatus(422)
        ->assertJson([
            'success' => false,
        ])
        ->assertJsonFragment([
            'message' => 'Tiket ini masih berstatus Pending Approval. Mohon setujui (approve) oleh atasan terkait terlebih dahulu sebelum menugaskan teknisi.',
        ]);

    $ticket->refresh();
    expect($ticket->assigned_to)->toBeNull();
});

test('perubahan status tiket dicegah jika tiket masih berstatus pending approval', function () {
    $ticket = Ticket::create([
        'company_id' => $this->company->id,
        'ticket_number' => 'TCK-2026-GUARD-2',
        'subject' => 'Server Cloud AWS Tambahan',
        'description' => 'Server staging baru.',
        'category_id' => $this->approvalCategory->id,
        'department_id' => $this->department->id,
        'requester_id' => $this->requester->id,
        'status' => TicketStatus::PendingApproval,
        'priority' => TicketPriority::Urgent,
        'approval_status' => TicketApprovalStatus::Pending,
    ]);

    $response = $this->patchJson("/api/v1/tickets/{$ticket->id}/status", [
        'status' => 'in_progress',
    ]);

    $response->assertStatus(422)
        ->assertJson([
            'success' => false,
        ]);

    $ticket->refresh();
    expect($ticket->status)->toBe(TicketStatus::PendingApproval);
});

test('halaman utama antrean tiket tickets.index dapat dirender tanpa error view', function () {
    $response = $this->get('/tickets');

    $response->assertStatus(200)
        ->assertViewIs('tickets.index');
});
