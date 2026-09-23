<?php

use App\Enums\CompanyPlan;
use App\Enums\CompanyStatus;
use App\Enums\UserRole;
use App\Models\Company;
use App\Models\Department;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;

uses(DatabaseTransactions::class);

beforeEach(function () {
    $this->company = Company::first() ?? Company::create([
        'name' => 'Sirius Auth Test Corp',
        'slug' => 'sirius-auth-'.uniqid(),
        'domain' => 'auth.'.uniqid().'.io',
        'plan' => CompanyPlan::Enterprise,
        'status' => CompanyStatus::Active,
    ]);

    $this->department = Department::first() ?? Department::create([
        'company_id' => $this->company->id,
        'name' => 'IT Operations',
        'slug' => 'it-ops-'.uniqid(),
        'is_active' => true,
    ]);

    $this->user = User::create([
        'company_id' => $this->company->id,
        'department_id' => $this->department->id,
        'name' => 'Auth Test User',
        'email' => 'auth.test.'.uniqid().'@sirius.io',
        'password' => Hash::make('password123'),
        'role' => UserRole::CompanyAdmin,
        'is_active' => true,
    ]);
});

test('guest can access login page', function () {
    $response = $this->get('/login');

    $response->assertOk();
    $response->assertSee('Masuk ke Akun Anda');
    $response->assertSee('SiriusTicketing');
    $response->assertSee('Alamat Email');
    $response->assertSee('Kata Sandi');
    $response->assertDontSee('Akun Demo Cepat');
});

test('guest can login with valid credentials and is redirected to dashboard', function () {
    $response = $this->post('/login', [
        'email' => $this->user->email,
        'password' => 'password123',
    ]);

    $response->assertRedirect(route('dashboard'));
    $this->assertAuthenticatedAs($this->user);
});

test('guest cannot login with incorrect password', function () {
    $response = $this->from('/login')->post('/login', [
        'email' => $this->user->email,
        'password' => 'wrong-password',
    ]);

    $response->assertRedirect('/login');
    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});

test('authenticated user accessing login page is redirected to dashboard', function () {
    $response = $this->actingAs($this->user)->get('/login');

    $response->assertRedirect(route('dashboard'));
});

test('authenticated user can logout successfully', function () {
    $response = $this->actingAs($this->user)->post('/logout');

    $response->assertRedirect('/login');
    $this->assertGuest();
});

test('unauthenticated visitor accessing dashboard is redirected to login', function () {
    $response = $this->get('/dashboard');

    $response->assertRedirect('/login');
});

test('unauthenticated visitor accessing tickets is redirected to login', function () {
    $response = $this->get('/tickets');

    $response->assertRedirect('/login');
});
