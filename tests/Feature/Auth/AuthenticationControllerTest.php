<?php
namespace Tests\Feature\Auth;


use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthenticationControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Ejecuta migraciones y seeders antes de cada prueba
        Artisan::call('migrate:fresh --seed');
    }

    /** @test */
    public function it_fails_login_with_invalid_data()
    {
        $response = $this->postJson('/api/login', []);

        $response->assertStatus(422)
                 ->assertJsonStructure(['status', 'errors']);
    }

    /** @test */
    public function it_fails_login_with_wrong_credentials()
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('correctpassword')
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
                 ->assertJson([
                     'status' => false,
                     'message' => 'Credenciales inválidas',
                 ]);
    }

    /** @test */
    public function it_logs_in_successfully()
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'status',
                     'access_token',
                     'token_type',
                     'expires_in',
                     'user',
                     'roles',
                     'permissions'
                 ]);
    }

    /** @test */
    public function it_refreshes_token_successfully()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeader('Authorization', "Bearer {$token}")
                         ->postJson('/api/refresh');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'status',
                     'access_token',
                     'token_type',
                     'expires_in',
                     'user',
                     'roles',
                     'permissions'
                 ]);
    }

    /** @test */
    public function it_logs_out_successfully()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeader('Authorization', "Bearer {$token}")
                         ->postJson('/api/logout');

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => true,
                     'message' => 'Sesión cerrada correctamente'
                 ]);
    }
}