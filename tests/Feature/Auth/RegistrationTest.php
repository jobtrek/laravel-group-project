<?php

use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;

beforeEach(fn () => $this->seed(RoleAndPermissionSeeder::class));

test('registration screen cannot be rendered by a non-admin', function () {
    $user = User::factory()->create()->assignRole('collaborateur');

    $response = $this->actingAs($user)->get('/register');

    $response->assertForbidden();
});

test('registration screen can be rendered by an admin', function () {
    $admin = User::factory()->create()->assignRole('admin');

    $response = $this->actingAs($admin)->get('/register');

    $response->assertStatus(200);
});

test('an admin can register a new user', function () {
    $admin = User::factory()->create()->assignRole('admin');

    $response = $this->actingAs($admin)->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    $response->assertRedirect(route('administration', absolute: false));
});
