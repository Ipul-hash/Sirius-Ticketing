<?php

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Models\Company;
use App\Models\Department;
use App\Models\Ticket;
use App\Models\TicketActivity;
use App\Models\TicketCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;

uses(DatabaseTransactions::class);

beforeEach(function () {
    $this->company = Company::first() ?? Company::create([
        'name' => 'Sirius Activity Corp',
        'slug' => 'sirius-activity',
        'domain' => 'activity.sirius.io',
        'plan' => 'enterprise',
        'status' => 'active',
    ]);

    $this->department = Department::first() ?? Department::create([
        'company_id' => $this->company->id,
        'name' => 'IT Operations',
        'slug' => 'it-ops',
        'is_active' => true,
    ]);

    $this->category = TicketCategory::first() ?? TicketCategory::create([
        'company_id' => $this->company->id,
        'department_id' => $this->department->id,
        'name' => 'Server Maintenance',
        'slug' => 'server-maint',
        'default_priority' => 'high',
        'requires_approval' => false,
        'is_active' => true,
    ]);

    $this->agent = User::where('role', UserRole::Agent)->first() ?? User::create([
        'company_id' => $this->company->id,
        'department_id' => $this->department->id,
        'name' => 'Agent Activity Tester',
        'email' => 'agent.activity@sirius.io',
        'password' => bcrypt('password'),
        'role' => UserRole::Agent,
        'is_active' => true,
    ]);

    $this->requester = User::where('role', UserRole::Requester)->first() ?? User::create([
        'company_id' => $this->company->id,
        'department_id' => $this->department->id,
        'name' => 'Requester Activity Tester',
        'email' => 'requester.activity@sirius.io',
        'password' => bcrypt('password'),
        'role' => UserRole::Requester,
        'is_active' => true,
    ]);

    $this->ticket = Ticket::create([
        'company_id' => $this->company->id,
        'ticket_number' => 'TCK-ACT-'.uniqid(),
        'subject' => 'High CPU Usage on Web Cluster',
        'description' => 'Monitoring alert CPU 98% sustained for 15 mins.',
        'category_id' => $this->category->id,
        'department_id' => $this->department->id,
        'requester_id' => $this->requester->id,
        'assigned_to' => $this->agent->id,
        'priority' => TicketPriority::High,
        'status' => TicketStatus::Open,
    ]);

    // Seed diverse activities
    TicketActivity::create([
        'ticket_id' => $this->ticket->id,
        'user_id' => $this->requester->id,
        'activity_type' => 'ticket_created',
        'old_value' => null,
        'new_value' => 'open',
        'notes' => 'Tiket dibuat oleh pengguna',
    ]);

    TicketActivity::create([
        'ticket_id' => $this->ticket->id,
        'user_id' => $this->agent->id,
        'activity_type' => 'assigned_agent',
        'old_value' => null,
        'new_value' => (string) $this->agent->id,
        'notes' => 'Tiket ditugaskan ke teknisi',
    ]);

    TicketActivity::create([
        'ticket_id' => $this->ticket->id,
        'user_id' => $this->agent->id,
        'activity_type' => 'status_changed',
        'old_value' => 'open',
        'new_value' => 'in_progress',
        'notes' => 'Sedang dalam pengecekan server',
    ]);

    TicketActivity::create([
        'ticket_id' => $this->ticket->id,
        'user_id' => $this->agent->id,
        'activity_type' => 'public_reply',
        'old_value' => null,
        'new_value' => null,
        'notes' => 'Kapasitas RAM dan CPU sedang kami restart.',
    ]);

    $this->actingAs($this->agent);
});

test('can retrieve ticket activities via api endpoint', function () {
    $response = $this->getJson("/api/v1/tickets/{$this->ticket->id}/activities");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
        ])
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                '*' => [
                    'id',
                    'ticket_id',
                    'activity_type',
                    'old_value',
                    'new_value',
                    'notes',
                    'created_at',
                    'user' => [
                        'id',
                        'name',
                        'email',
                    ],
                ],
            ],
            'meta' => [
                'ticket_id',
                'ticket_number',
                'current_page',
                'total',
            ],
        ]);

    expect(count($response->json('data')))->toBeGreaterThanOrEqual(4);
});

test('can filter activities by category status and assignment', function () {
    $statusResponse = $this->getJson("/api/v1/tickets/{$this->ticket->id}/activities?category=status");
    $statusResponse->assertStatus(200);
    $statusActivities = $statusResponse->json('data');

    foreach ($statusActivities as $act) {
        expect(in_array($act['activity_type'], ['status_changed', 'priority_changed', 'ticket_created']))->toBeTrue();
    }

    $assignmentResponse = $this->getJson("/api/v1/tickets/{$this->ticket->id}/activities?category=assignment");
    $assignmentResponse->assertStatus(200);
    $assignmentActivities = $assignmentResponse->json('data');

    foreach ($assignmentActivities as $act) {
        expect($act['activity_type'])->toBe('assigned_agent');
    }
});

test('can filter activities by specific activity_type', function () {
    $response = $this->getJson("/api/v1/tickets/{$this->ticket->id}/activities?activity_type=public_reply");

    $response->assertStatus(200);
    $items = $response->json('data');

    expect($items)->not->toBeEmpty();
    foreach ($items as $item) {
        expect($item['activity_type'])->toBe('public_reply');
    }
});

test('can retrieve ticket activities with tenant scoping in URL', function () {
    $response = $this->getJson("/api/v1/{$this->company->slug}/tickets/{$this->ticket->id}/activities");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
        ]);
});

test('returns 404 when ticket is not found', function () {
    $response = $this->getJson('/api/v1/tickets/99999999/activities');

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'message' => 'Tiket tidak ditemukan.',
        ]);
});

test('ticket detail web view renders audit trail tab and timeline activities', function () {
    $response = $this->get("/tickets/{$this->ticket->id}");

    $response->assertStatus(200)
        ->assertSee('Kronologi Jejak Audit')
        ->assertSee('pane_audit_trail')
        ->assertSee('audit_filter_chips')
        ->assertSee('High CPU Usage on Web Cluster');
});
