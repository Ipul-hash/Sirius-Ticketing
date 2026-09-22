<?php

use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Models\Company;
use App\Models\Department;
use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\TicketCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(DatabaseTransactions::class);

beforeEach(function () {
    $this->company = Company::first() ?? Company::create([
        'name' => 'Sirius Test Corp',
        'slug' => 'sirius-test',
        'domain' => 'test.sirius.io',
        'plan' => 'enterprise',
        'status' => 'active',
    ]);

    $this->department = Department::first() ?? Department::create([
        'company_id' => $this->company->id,
        'name' => 'IT Support',
        'slug' => 'it-support',
        'is_active' => true,
    ]);

    $this->category = TicketCategory::first() ?? TicketCategory::create([
        'company_id' => $this->company->id,
        'department_id' => $this->department->id,
        'name' => 'Hardware Issue',
        'slug' => 'hardware-issue',
        'default_priority' => 'medium',
        'requires_approval' => false,
        'is_active' => true,
    ]);

    $this->agent = User::where('role', UserRole::Agent)->first() ?? User::create([
        'company_id' => $this->company->id,
        'department_id' => $this->department->id,
        'name' => 'Agent Test',
        'email' => 'agent.test@sirius.io',
        'password' => bcrypt('password'),
        'role' => UserRole::Agent,
        'is_active' => true,
    ]);

    $this->requester = User::where('role', UserRole::Requester)->first() ?? User::create([
        'company_id' => $this->company->id,
        'department_id' => $this->department->id,
        'name' => 'Requester Test',
        'email' => 'requester.test@sirius.io',
        'password' => bcrypt('password'),
        'role' => UserRole::Requester,
        'is_active' => true,
    ]);

    $this->ticket = Ticket::first() ?? Ticket::create([
        'company_id' => $this->company->id,
        'ticket_number' => 'TCK-2026-99999',
        'subject' => 'Kendala Jaringan LAN',
        'description' => 'Kabel LAN terputus di ruang rapat utama',
        'category_id' => $this->category->id,
        'department_id' => $this->department->id,
        'requester_id' => $this->requester->id,
        'assigned_to' => $this->agent->id,
        'priority' => 'medium',
        'status' => 'open',
        'first_response_due_at' => now()->addHours(2),
        'resolution_due_at' => now()->addHours(8),
    ]);
});

test('can retrieve ticket messages thread via api', function () {
    $response = $this->getJson("/api/v1/tickets/{$this->ticket->id}/messages");

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'ticket_id',
            'ticket_number',
            'total_messages',
            'data',
        ]);
});

test('agent can send a public reply and update first response sla', function () {
    $response = $this->postJson("/api/v1/tickets/{$this->ticket->id}/messages", [
        'user_id' => $this->agent->id,
        'message' => 'Halo, teknisi kami sedang menuju ke lokasi.',
        'is_internal_note' => false,
        'status' => 'in_progress',
    ]);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'message' => 'Balasan berhasil dikirimkan.',
        ]);

    $this->ticket->refresh();
    expect($this->ticket->status)->toBe(TicketStatus::InProgress)
        ->and($this->ticket->replies_count)->toBeGreaterThan(0)
        ->and($this->ticket->first_responded_at)->not->toBeNull()
        ->and($this->ticket->last_reply_at)->not->toBeNull();
});

test('agent can post an internal note', function () {
    $response = $this->postJson("/api/v1/tickets/{$this->ticket->id}/messages", [
        'user_id' => $this->agent->id,
        'message' => 'Catatan teknis: Port switch 4 di lantai 2 kemungkinan flapping.',
        'is_internal_note' => true,
    ]);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'message' => 'Catatan internal berhasil disimpan.',
        ]);

    $this->assertDatabaseHas('ticket_messages', [
        'ticket_id' => $this->ticket->id,
        'is_internal_note' => true,
    ]);
});

test('can upload file attachment along with message and download it', function () {
    Storage::fake('public');

    $file = UploadedFile::fake()->create('network_diagnostic.log', 120, 'text/plain');

    $response = $this->post("/api/v1/tickets/{$this->ticket->id}/messages", [
        'user_id' => $this->agent->id,
        'message' => 'Berikut file log diagnostik jaringan.',
        'is_internal_note' => false,
        'attachments' => [$file],
    ], [
        'Accept' => 'application/json',
    ]);

    $response->assertStatus(201);

    $this->assertDatabaseHas('ticket_attachments', [
        'ticket_id' => $this->ticket->id,
        'file_name' => 'network_diagnostic.log',
    ]);

    $attachment = TicketAttachment::where('file_name', 'network_diagnostic.log')->first();
    expect($attachment)->not->toBeNull();

    $downloadResponse = $this->get("/api/v1/attachments/{$attachment->id}/download");
    $downloadResponse->assertStatus(200);
});

test('ticket detail blade page renders successfully', function () {
    $response = $this->get("/tickets/{$this->ticket->id}");

    $response->assertStatus(200)
        ->assertSee($this->ticket->ticket_number)
        ->assertSee($this->ticket->subject);
});
