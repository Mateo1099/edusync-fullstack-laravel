<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthAndRolesTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_creates_student_and_returns_token(): void
    {
        $resp = $this->postJson('/api/register', [
            'name' => 'Juan Perez',
            'password' => 'Aa1@segura',
            'personal_email' => 'juan@example.com',
        ]);

        $resp->assertCreated()
            ->assertJsonStructure([
                'generated_email', 'token', 'user' => ['id','name','email','role'], 'student' => ['id','matricula']
            ]);

        $this->assertSame('student', $resp->json('user.role'));
        $this->assertStringContainsString('@edusync.com', $resp->json('generated_email'));
    }

    public function test_login_returns_token_and_user(): void
    {
        $user = User::create([
            'name' => 'Admin One',
            'email' => 'admin@edusync.com',
            'password' => Hash::make('Aa1@segura'),
            'role' => 'admin',
        ]);

        $resp = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'Aa1@segura',
        ]);

        $resp->assertOk()->assertJsonStructure(['token','user'=>['id','role']]);
    }

    public function test_student_cannot_access_admin_routes(): void
    {
        $student = User::create([
            'name' => 'Alumno Uno',
            'email' => 'alumno@edusync.com',
            'password' => Hash::make('Aa1@segura'),
            'role' => 'student',
        ]);

        Sanctum::actingAs($student);
        $resp = $this->getJson('/api/teachers');
        $resp->assertStatus(403);
    }

    public function test_admin_can_access_admin_routes(): void
    {
        $admin = User::create([
            'name' => 'Admin Two',
            'email' => 'admin2@edusync.com',
            'password' => Hash::make('Aa1@segura'),
            'role' => 'admin',
        ]);
        Sanctum::actingAs($admin);

        $resp = $this->getJson('/api/teachers');
        // Puede devolver 200 con lista vacía
        $resp->assertStatus(200);
    }
}
