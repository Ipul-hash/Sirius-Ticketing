<?php

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Models\Company;
use App\Models\Department;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;

uses(DatabaseTransactions::class);

beforeEach(function () {
    $this->company = Company::first() ?? Company::create([
        'name' => 'Sirius Merge Corp',
        'slug' => 'sirius-merge',
        'domain' => 'merge.sirius.io',
        'plan' => 'enterprise',
        'status' => 'active',
    ]);

    $this->otherCompany = Company::create([
        'name' => 'Other Corp '.uniqid(),
        'slug' => 'other-corp-'.uniqid(),
        'domain' => 'other.'.uniqid().'.io',
        'plan' => 'enterprise',
        'status' => 'active',
    ]);

    $this->department = Department::first() ?? Department::create([
        'company_id' => $this->company->id,
        'name' => 'IT Infrastructure',
        'slug' => 'it-infra-'.uniqid(),
        'is_active' => true,
    ]);

    $this->category = TicketCategory::first() ?? TicketCategory::create([
        'company_id' => $this->company->id,
        'department_id' => $this->department->id,
        'name' => 'Network Issue',
        'slug' => 'network-issue-'.uniqid(),
        'default_priority' => 'medium',
        'requires_approval' => false,
        'is_active' => true,
    ]);

    $this->agent = User::create([
        'company_id' => $this->company->id,
        'department_id' => $this->department->id,
        'name' => 'Teknisi Rama Setiawan',
        'email' => 'rama.merge.'.uniqid().'@sirius.io',
        'password' => bcrypt('password'),
        'role' => UserRole::Agent,
        'is_active' => true,
    ]);

    $this->requester = User::where('role', UserRole::Requester)->first() ?? User::create([
        'company_id' => $this->company->id,
        'department_id' => $this->department->id,
        'name' => 'User Pelapor Merge',
        'email' => 'requester.merge.'.uniqid().'@sirius.io',
        'password' => bcrypt('password'),
        'role' => UserRole::Requester,
        'is_active' => true,
    ]);

    $this->primaryTicket = Ticket::create([
        'company_id' => $this->company->id,
        'ticket_number' => 'TCK-PRI-'.uniqid(),
        'subject' => 'Gangguan Koneksi Internet Lantai 3',
        'description' => 'Seluruh workstation di lantai 3 tidak mendapatkan IP address.',
        'category_id' => $this->category->id,
        'department_id' => $this->department->id,
        'requester_id' => $this->requester->id,
        'assigned_to' => $this->agent->id,
        'priority' => TicketPriority::High,
        'status' => TicketStatus::Open,
        'is_merged' => false,
    ]);

    $this->duplicateTicket = Ticket::create([
        'company_id' => $this->company->id,
        'ticket_number' => 'TCK-DUP-'.uniqid(),
        'subject' => 'Internet Mati di Meja 3B',
        'description' => 'Tidak bisa buka browser internet mati total.',
        'category_id' => $this->category->id,
        'department_id' => $this->department->id,
        'requester_id' => $this->requester->id,
        'assigned_to' => $this->agent->id,
        'priority' => TicketPriority::Medium,
        'status' => TicketStatus::Open,
        'is_merged' => false,
    ]);
});

test('can fetch candidate target tickets in the same tenant', function () {
    $response = $this->getJson("/api/v1/tickets/{$this->duplicateTicket->id}/merge-candidates");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
        ]);

    $candidates = $response->json('candidates');
    $candidateIds = array_column($candidates, 'id');

    // Primary ticket must be available as candidate
    expect($candidateIds)->toContain($this->primaryTicket->id);
    // Duplicate ticket itself must not be in candidate list
    expect($candidateIds)->not->toContain($this->duplicateTicket->id);
});

test('can merge source ticket into target primary ticket successfully', function () {
    $response = $this->postJson("/api/v1/tickets/{$this->duplicateTicket->id}/merge", [
        'target_ticket_id' => $this->primaryTicket->id,
        'notes' => 'Duplikat dari laporan gangguan lantai 3.',
        'user_id' => $this->agent->id,
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'data' => [
                'source_ticket_id' => $this->duplicateTicket->id,
                'target_ticket_id' => $this->primaryTicket->id,
            ],
        ]);

    // Verify source ticket is closed and marked as merged
    $this->duplicateTicket->refresh();
    expect($this->duplicateTicket->is_merged)->toBeTrue();
    expect($this->duplicateTicket->merged_into_ticket_id)->toBe($this->primaryTicket->id);
    expect($this->duplicateTicket->status)->toBe(TicketStatus::Closed);
    expect($this->duplicateTicket->closed_at)->not->toBeNull();

    // Verify internal note in source ticket
    $this->assertDatabaseHas('ticket_messages', [
        'ticket_id' => $this->duplicateTicket->id,
        'is_internal_note' => true,
    ]);

    // Verify internal note in target ticket
    $this->assertDatabaseHas('ticket_messages', [
        'ticket_id' => $this->primaryTicket->id,
        'is_internal_note' => true,
    ]);

    // Verify audit trail on source ticket
    $this->assertDatabaseHas('ticket_activities', [
        'ticket_id' => $this->duplicateTicket->id,
        'activity_type' => 'ticket_merged',
        'new_value' => 'closed',
    ]);

    // Verify audit trail on target ticket
    $this->assertDatabaseHas('ticket_activities', [
        'ticket_id' => $this->primaryTicket->id,
        'activity_type' => 'ticket_merged_source',
        'new_value' => $this->duplicateTicket->ticket_number,
    ]);
});

test('cannot merge a ticket into itself', function () {
    $response = $this->postJson("/api/v1/tickets/{$this->duplicateTicket->id}/merge", [
        'target_ticket_id' => $this->duplicateTicket->id,
        'user_id' => $this->agent->id,
    ]);

    $response->assertStatus(422)
        ->assertJson([
            'success' => false,
            'message' => 'Tiket tidak dapat digabungkan ke dirinya sendiri.',
        ]);
});

test('cannot merge tickets across different companies', function () {
    $crossTenantTicket = Ticket::create([
        'company_id' => $this->otherCompany->id,
        'ticket_number' => 'TCK-CROSS-'.uniqid(),
        'subject' => 'Cross tenant ticket',
        'description' => 'Different tenant',
        'category_id' => $this->category->id,
        'department_id' => $this->department->id,
        'requester_id' => $this->requester->id,
        'priority' => TicketPriority::Low,
        'status' => TicketStatus::Open,
        'is_merged' => false,
    ]);

    $response = $this->postJson("/api/v1/tickets/{$this->duplicateTicket->id}/merge", [
        'target_ticket_id' => $crossTenantTicket->id,
        'user_id' => $this->agent->id,
    ]);

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'message' => 'Tiket target utama tidak ditemukan dalam perusahaan yang sama.',
        ]);
});

test('cannot merge an already merged ticket again', function () {
    // First merge
    $this->postJson("/api/v1/tickets/{$this->duplicateTicket->id}/merge", [
        'target_ticket_id' => $this->primaryTicket->id,
        'user_id' => $this->agent->id,
    ]);

    // Second merge attempt
    $response = $this->postJson("/api/v1/tickets/{$this->duplicateTicket->id}/merge", [
        'target_ticket_id' => $this->primaryTicket->id,
        'user_id' => $this->agent->id,
    ]);

    $response->assertStatus(422)
        ->assertJson([
            'success' => false,
            'message' => 'Tiket ini sudah pernah digabungkan sebelumnya ke tiket lain.',
        ]);
});

test('ticket detail web view renders merge button for unmerged ticket', function () {
    $response = $this->get("/tickets/{$this->duplicateTicket->id}");

    $response->assertStatus(200)
        ->assertSee('Gabungkan Tiket')
        ->assertSee('kt_modal_merge_ticket');
});

test('ticket detail web view renders merged banner and disables reply form when ticket is merged', function () {
    // Perform merge
    $this->postJson("/api/v1/tickets/{$this->duplicateTicket->id}/merge", [
        'target_ticket_id' => $this->primaryTicket->id,
        'user_id' => $this->agent->id,
    ]);

    $response = $this->get("/tickets/{$this->duplicateTicket->id}");

    $response->assertStatus(200)
        ->assertSee('Tiket Ini Telah Digabungkan (Merged Ticket)')
        ->assertSee('Form Balasan Dinonaktifkan')
        ->assertDontSee('id="form_send_message"', false);
});

test('ticket detail web view of primary ticket renders merged tickets card', function () {
    // Perform merge
    $this->postJson("/api/v1/tickets/{$this->duplicateTicket->id}/merge", [
        'target_ticket_id' => $this->primaryTicket->id,
        'user_id' => $this->agent->id,
    ]);

    $response = $this->get("/tickets/{$this->primaryTicket->id}");

    $response->assertStatus(200)
        ->assertSee('Tiket Tergabung')
        ->assertSee($this->duplicateTicket->ticket_number);
});
