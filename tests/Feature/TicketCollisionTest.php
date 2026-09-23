<?php

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Models\Company;
use App\Models\Department;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\TicketCollision;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;

uses(DatabaseTransactions::class);

beforeEach(function () {
    $this->company = Company::first() ?? Company::create([
        'name' => 'Sirius Collision Corp',
        'slug' => 'sirius-collision',
        'domain' => 'collision.sirius.io',
        'plan' => 'enterprise',
        'status' => 'active',
    ]);

    $this->department = Department::first() ?? Department::create([
        'company_id' => $this->company->id,
        'name' => 'Network & Infrastructure',
        'slug' => 'network-infra',
        'is_active' => true,
    ]);

    $this->category = TicketCategory::first() ?? TicketCategory::create([
        'company_id' => $this->company->id,
        'department_id' => $this->department->id,
        'name' => 'Switch Flapping',
        'slug' => 'switch-flapping',
        'default_priority' => 'medium',
        'requires_approval' => false,
        'is_active' => true,
    ]);

    $this->agent1 = User::create([
        'company_id' => $this->company->id,
        'department_id' => $this->department->id,
        'name' => 'Teknisi Andi Wijaya',
        'email' => 'andi.wijaya.'.uniqid().'@sirius.io',
        'password' => bcrypt('password'),
        'role' => UserRole::Agent,
        'is_active' => true,
    ]);

    $this->agent2 = User::create([
        'company_id' => $this->company->id,
        'department_id' => $this->department->id,
        'name' => 'Teknisi Budi Pratama',
        'email' => 'budi.pratama.'.uniqid().'@sirius.io',
        'password' => bcrypt('password'),
        'role' => UserRole::Agent,
        'is_active' => true,
    ]);

    $this->requester = User::where('role', UserRole::Requester)->first() ?? User::create([
        'company_id' => $this->company->id,
        'department_id' => $this->department->id,
        'name' => 'Requester Collision',
        'email' => 'requester.collision.'.uniqid().'@sirius.io',
        'password' => bcrypt('password'),
        'role' => UserRole::Requester,
        'is_active' => true,
    ]);

    $this->ticket = Ticket::create([
        'company_id' => $this->company->id,
        'ticket_number' => 'TCK-COL-'.uniqid(),
        'subject' => 'Core Switch Flapping Port 12',
        'description' => 'Port 12 flapping berkali-kali menyebabkan packet loss.',
        'category_id' => $this->category->id,
        'department_id' => $this->department->id,
        'requester_id' => $this->requester->id,
        'assigned_to' => $this->agent1->id,
        'priority' => TicketPriority::Medium,
        'status' => TicketStatus::Open,
    ]);

    $this->actingAs($this->agent1);
});

test('can send heartbeat ping and store presence in ticket_collisions', function () {
    $response = $this->postJson("/api/v1/tickets/{$this->ticket->id}/collisions/ping", [
        'user_id' => $this->agent1->id,
        'user_name' => $this->agent1->name,
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'has_collision' => false,
            'total_other_agents' => 0,
        ]);

    $this->assertDatabaseHas('ticket_collisions', [
        'ticket_id' => $this->ticket->id,
        'user_id' => $this->agent1->id,
        'user_name' => $this->agent1->name,
    ]);
});

test('ping detects other active agents on the same ticket', function () {
    // Agent 2 is already active viewing the ticket
    TicketCollision::updateOrInsert(
        [
            'ticket_id' => $this->ticket->id,
            'user_id' => $this->agent2->id,
        ],
        [
            'user_name' => $this->agent2->name,
            'last_seen_at' => now(),
        ]
    );

    // Agent 1 now opens and pings the ticket
    $response = $this->postJson("/api/v1/tickets/{$this->ticket->id}/collisions/ping", [
        'user_id' => $this->agent1->id,
        'user_name' => $this->agent1->name,
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'has_collision' => true,
            'total_other_agents' => 1,
            'agents' => [
                [
                    'user_id' => $this->agent2->id,
                    'name' => $this->agent2->name,
                ],
            ],
        ]);
});

test('does not include current agent in active collisions list', function () {
    // Agent 1 pings
    $this->postJson("/api/v1/tickets/{$this->ticket->id}/collisions/ping", [
        'user_id' => $this->agent1->id,
        'user_name' => $this->agent1->name,
    ]);

    // Active collisions GET endpoint with agent 1 user_id filter
    $response = $this->getJson("/api/v1/tickets/{$this->ticket->id}/collisions?user_id={$this->agent1->id}");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'has_collision' => false,
            'total_other_agents' => 0,
        ]);
});

test('expired collisions older than 30 seconds are not treated as active', function () {
    // Agent 2 was active 45 seconds ago (stale presence)
    TicketCollision::updateOrInsert(
        [
            'ticket_id' => $this->ticket->id,
            'user_id' => $this->agent2->id,
        ],
        [
            'user_name' => $this->agent2->name,
            'last_seen_at' => now()->subSeconds(45),
        ]
    );

    // Agent 1 pings
    $response = $this->postJson("/api/v1/tickets/{$this->ticket->id}/collisions/ping", [
        'user_id' => $this->agent1->id,
        'user_name' => $this->agent1->name,
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'has_collision' => false,
            'total_other_agents' => 0,
        ]);
});

test('agent can leave presence upon closing or navigating away from ticket', function () {
    // Agent 1 registers presence
    $this->postJson("/api/v1/tickets/{$this->ticket->id}/collisions/ping", [
        'user_id' => $this->agent1->id,
        'user_name' => $this->agent1->name,
    ]);

    $this->assertDatabaseHas('ticket_collisions', [
        'ticket_id' => $this->ticket->id,
        'user_id' => $this->agent1->id,
    ]);

    // Agent 1 leaves
    $leaveResponse = $this->postJson("/api/v1/tickets/{$this->ticket->id}/collisions/leave", [
        'user_id' => $this->agent1->id,
    ]);

    $leaveResponse->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Status kehadiran tiket berhasil dilepas.',
        ]);

    $this->assertDatabaseMissing('ticket_collisions', [
        'ticket_id' => $this->ticket->id,
        'user_id' => $this->agent1->id,
    ]);
});

test('returns 404 when ticket is not found for collisions endpoint', function () {
    $response = $this->postJson('/api/v1/tickets/99999999/collisions/ping', [
        'user_id' => $this->agent1->id,
    ]);

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'message' => 'Tiket tidak ditemukan.',
        ]);
});

test('ticket detail web view renders agent collision banner container', function () {
    $response = $this->get("/tickets/{$this->ticket->id}");

    $response->assertStatus(200)
        ->assertSee('agent_collision_banner')
        ->assertSee('Peringatan Kehadiran Teknisi')
        ->assertSee('agent_collision_avatars');
});
