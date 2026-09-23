<?php

use App\Enums\CompanyPlan;
use App\Enums\CompanyStatus;
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

    $this->adminA = User::create([
        'company_id' => $this->companyA->id,
        'department_id' => $this->deptA->id,
        'name' => 'Admin Sutech '.uniqid(),
        'email' => 'admin.sutech.'.uniqid().'@sutech.id',
        'password' => bcrypt('password'),
        'role' => UserRole::CompanyAdmin,
        'is_active' => true,
    ]);

    $this->agentA = User::create([
        'company_id' => $this->companyA->id,
        'department_id' => $this->deptA->id,
        'name' => 'Agent Sutech '.uniqid(),
        'email' => 'agent.sutech.'.uniqid().'@sutech.id',
        'password' => bcrypt('password'),
        'role' => UserRole::Agent,
        'is_active' => true,
    ]);

    $this->requesterA = User::create([
        'company_id' => $this->companyA->id,
        'department_id' => $this->deptA->id,
        'name' => 'Requester Sutech '.uniqid(),
        'email' => 'requester.sutech.'.uniqid().'@sutech.id',
        'password' => bcrypt('password'),
        'role' => UserRole::Requester,
        'is_active' => true,
    ]);

    // Tenant B (PT Nusantara)
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

    $this->adminB = User::create([
        'company_id' => $this->companyB->id,
        'department_id' => $this->deptB->id,
        'name' => 'Admin Nusantara '.uniqid(),
        'email' => 'admin.nusantara.'.uniqid().'@nusantara.id',
        'password' => bcrypt('password'),
        'role' => UserRole::CompanyAdmin,
        'is_active' => true,
    ]);

    // Superadmin
    $this->superadmin = User::create([
        'name' => 'Super Administrator '.uniqid(),
        'email' => 'superadmin.'.uniqid().'@sirius.id',
        'password' => bcrypt('password'),
        'role' => UserRole::Superadmin,
        'is_active' => true,
    ]);
});

test('guest cannot access user management page', function () {
    $response = $this->get('/users');
    $response->assertRedirect('/login');
});

test('agent and requester cannot access user management page', function () {
    $this->actingAs($this->agentA)
        ->get('/users')
        ->assertForbidden();

    $this->actingAs($this->requesterA)
        ->get('/users')
        ->assertForbidden();
});

test('company admin and superadmin can access user management page', function () {
    $this->actingAs($this->adminA)
        ->get('/users')
        ->assertOk()
        ->assertSee('Manajemen Staf & Pengguna')
        ->assertSee($this->agentA->name)
        ->assertDontSee($this->adminB->name);

    $this->actingAs($this->superadmin)
        ->get('/users')
        ->assertOk()
        ->assertSee('Manajemen Staf & Pengguna')
        ->assertSee($this->agentA->name)
        ->assertSee($this->adminB->name);
});

test('company admin can fetch users list scoped only to their own company', function () {
    $response = $this->actingAs($this->adminA)
        ->getJson('/api/v1/users');

    $response->assertOk();
    $data = $response->json('data');

    $ids = collect($data)->pluck('id');
    expect($ids)->toContain($this->adminA->id)
        ->and($ids)->toContain($this->agentA->id)
        ->and($ids)->not->toContain($this->adminB->id);
});

test('company admin can create a new agent or requester for their own company', function () {
    $email = 'new.staff.'.uniqid().'@sutech.id';

    $response = $this->actingAs($this->adminA)
        ->postJson('/api/v1/users', [
            'name' => 'Staf Baru Sutech',
            'email' => $email,
            'password' => 'secret123',
            'role' => UserRole::Agent->value,
            'department_id' => $this->deptA->id,
            'job_title' => 'Junior Helpdesk',
            'phone' => '081234567890',
            'is_active' => true,
        ]);

    $response->assertCreated()
        ->assertJsonPath('success', true);

    $createdUser = User::where('email', $email)->first();
    expect($createdUser)->not->toBeNull()
        ->and($createdUser->company_id)->toBe($this->companyA->id)
        ->and($createdUser->role)->toBe(UserRole::Agent);
});

test('company admin cannot create a user with role superadmin', function () {
    $response = $this->actingAs($this->adminA)
        ->postJson('/api/v1/users', [
            'name' => 'Fake Superadmin',
            'email' => 'fake.super.'.uniqid().'@sutech.id',
            'password' => 'secret123',
            'role' => UserRole::Superadmin->value,
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors('role');
});

test('company admin cannot create a user for another company', function () {
    $email = 'tampered.user.'.uniqid().'@sutech.id';

    $response = $this->actingAs($this->adminA)
        ->postJson('/api/v1/users', [
            'company_id' => $this->companyB->id,
            'name' => 'Tampered Company User',
            'email' => $email,
            'password' => 'secret123',
            'role' => UserRole::Requester->value,
        ]);

    $response->assertCreated();

    // Forced to company A
    $user = User::where('email', $email)->first();
    expect($user->company_id)->toBe($this->companyA->id);
});

test('company admin cannot assign department belonging to another company', function () {
    $response = $this->actingAs($this->adminA)
        ->postJson('/api/v1/users', [
            'name' => 'Invalid Dept User',
            'email' => 'invalid.dept.'.uniqid().'@sutech.id',
            'password' => 'secret123',
            'role' => UserRole::Requester->value,
            'department_id' => $this->deptB->id, // belongs to Company B
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors('department_id');
});

test('company admin cannot view or edit user from another company', function () {
    // Show
    $this->actingAs($this->adminA)
        ->getJson("/api/v1/users/{$this->adminB->id}")
        ->assertForbidden();

    // Update
    $this->actingAs($this->adminA)
        ->putJson("/api/v1/users/{$this->adminB->id}", [
            'name' => 'Hacked Name',
            'email' => $this->adminB->email,
            'role' => UserRole::Agent->value,
        ])
        ->assertForbidden();
});

test('company admin can update staff in their own company', function () {
    $response = $this->actingAs($this->adminA)
        ->putJson("/api/v1/users/{$this->agentA->id}", [
            'name' => 'Agent Sutech Updated',
            'email' => $this->agentA->email,
            'role' => UserRole::Agent->value,
            'department_id' => $this->deptA->id,
            'job_title' => 'Senior Helpdesk Support',
            'is_active' => true,
        ]);

    $response->assertOk()
        ->assertJsonPath('success', true);

    expect($this->agentA->fresh()->name)->toBe('Agent Sutech Updated')
        ->and($this->agentA->fresh()->job_title)->toBe('Senior Helpdesk Support');
});

test('user cannot delete themselves', function () {
    $this->actingAs($this->adminA)
        ->deleteJson("/api/v1/users/{$this->adminA->id}")
        ->assertStatus(422)
        ->assertJsonPath('success', false);
});

test('user with ticket history cannot be deleted', function () {
    $category = TicketCategory::create([
        'company_id' => $this->companyA->id,
        'department_id' => $this->deptA->id,
        'name' => 'General Issue '.uniqid(),
        'slug' => 'gen-'.uniqid(),
        'is_active' => true,
    ]);

    // Buat tiket dengan requester agentA
    Ticket::create([
        'company_id' => $this->companyA->id,
        'department_id' => $this->deptA->id,
        'category_id' => $category->id,
        'requester_id' => $this->agentA->id,
        'ticket_number' => 'TCK-DEL-'.uniqid(),
        'subject' => 'Tiket Uji Delete',
        'description' => 'Test ticket',
        'priority' => TicketPriority::Medium,
        'status' => TicketStatus::Open,
    ]);

    $response = $this->actingAs($this->adminA)
        ->deleteJson("/api/v1/users/{$this->agentA->id}");

    $response->assertStatus(422)
        ->assertJsonPath('success', false);

    expect(User::find($this->agentA->id))->not->toBeNull();
});

test('staff without ticket history can be deleted successfully', function () {
    $tempUser = User::create([
        'company_id' => $this->companyA->id,
        'department_id' => $this->deptA->id,
        'name' => 'Temp User '.uniqid(),
        'email' => 'temp.'.uniqid().'@sutech.id',
        'password' => bcrypt('password'),
        'role' => UserRole::Requester,
        'is_active' => true,
    ]);

    $response = $this->actingAs($this->adminA)
        ->deleteJson("/api/v1/users/{$tempUser->id}");

    $response->assertOk()
        ->assertJsonPath('success', true);

    expect(User::find($tempUser->id))->toBeNull();
});
