<?php
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);
beforeEach(function () {
    Role::findOrCreate('Admin', 'api');
    Role::findOrCreate('Super Admin', 'api');
    // Usuario autorizado
    $this->user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('password123')
    ]);
    $this->user->assignRole('Admin'); // Usa Spatie

    // Usuario sin rol
    $this->unauthorizedUser = User::factory()->create([
        'email' => 'unauthorized@example.com',
        'password' => Hash::make('password123')
    ]);
    // No se le asigna ningún rol
});

it('inicia sesión correctamente en /api/auth/login', function () {
    $response = $this->postJson('/api/auth/login', [
        'email' => 'test@example.com',
        'password' => 'password123',
    ]);

    $response->assertOk()
        ->assertJsonStructure([
            'message',
            'status',
            'data' => [
                'access_token',
                'token_type',
                'expires_in',
                'user',
                'roles',
                'permissions',
            ]
        ]);
});

it('falla si falta email y contraseña', function () {
    $response = $this->postJson('/api/auth/login', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email', 'password']);
});

it('falla si la contraseña es incorrecta', function () {
    $response = $this->postJson('/api/auth/login', [
        'email' => 'test@example.com',
        'password' => 'wrongpassword',
    ]);

    $response->assertStatus(401)
        ->assertJson([
            'status' => false,
            'message' => 'Credenciales inválidas',
        ]);
});

it('refresca el token con rol autorizado en /api/auth/refresh', function () {
    $token = auth('api')->attempt([
        'email' => 'test@example.com',
        'password' => 'password123',
    ]);

    $response = $this->postJson('/api/auth/refresh', [], [
        'Authorization' => 'Bearer ' . $token
    ]);

    $response->assertOk()
        ->assertJsonStructure([
            'message',
            'status',
            'data' => [
                'access_token',
                'token_type',
                'expires_in',
                'user',
                'roles',
                'permissions',
            ]
        ]);
});

it('cierra sesión correctamente con rol autorizado en /api/auth/logout', function () {
    $token = auth('api')->attempt([
        'email' => 'test@example.com',
        'password' => 'password123',
    ]);

    $response = $this->postJson('/api/auth/logout', [], [
        'Authorization' => 'Bearer ' . $token
    ]);

    $response->assertOk()
        ->assertJson([
            'message' => 'Se cerro sesión coreectamente',
            'status' => true,
        ]);
});

it('no permite refresh sin rol autorizado', function () {
    $token = auth('api')->attempt([
        'email' => 'unauthorized@example.com',
        'password' => 'password123',
    ]);

    $response = $this->postJson('/api/auth/refresh', [], [
        'Authorization' => 'Bearer ' . $token
    ]);

    $response->assertStatus(403); // Tu middleware debería retornar 403 Forbidden
});