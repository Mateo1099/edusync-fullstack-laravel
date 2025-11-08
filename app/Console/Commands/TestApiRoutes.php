<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class TestApiRoutes extends Command
{
    protected $signature = 'test:api-routes';
    protected $description = 'Prueba las rutas API con diferentes roles de usuario';
    
    private $baseUrl = 'http://localhost:8000/api/v1';
    private $tokens = [];

    public function handle()
    {
        $this->info('Iniciando pruebas de rutas API...');
        
        // Crear usuarios de prueba
        $this->createTestUsers();
        
        // Probar autenticación
        $this->testAuthentication();
        
        // Probar rutas por rol
        $this->testAdminRoutes();
        $this->testTeacherRoutes();
        $this->testStudentRoutes();
        $this->testGuardianRoutes();
        
        // Probar rutas comunes
        $this->testCommonRoutes();
        
        $this->info('¡Pruebas completadas!');
    }

    private function createTestUsers()
    {
        $this->info('Creando usuarios de prueba...');
        
        $users = [
            ['name' => 'Admin Test', 'email' => 'admin@test.com', 'role' => 'admin'],
            ['name' => 'Teacher Test', 'email' => 'teacher@test.com', 'role' => 'teacher'],
            ['name' => 'Student Test', 'email' => 'student@test.com', 'role' => 'student'],
            ['name' => 'Guardian Test', 'email' => 'guardian@test.com', 'role' => 'guardian'],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make('password123'),
                    'role' => $userData['role']
                ]
            );
        }
    }

    private function testAuthentication()
    {
        $this->info('Probando autenticación...');
        
        $roles = ['admin', 'teacher', 'student', 'guardian'];
        
        foreach ($roles as $role) {
            $response = Http::post($this->baseUrl . '/auth/login', [
                'email' => $role . '@test.com',
                'password' => 'password123'
            ]);
            
            if ($response->successful()) {
                $this->tokens[$role] = $response->json('token');
                $this->info("✓ Login exitoso para {$role}");
            } else {
                $this->error("✗ Error en login para {$role}");
            }
        }
    }

    private function testAdminRoutes()
    {
        $this->info('Probando rutas de administrador...');
        
        $routes = [
            'GET:/admins' => 200,
            'GET:/roles' => 200,
            'GET:/system/stats' => 200
        ];

        $this->testRoutes($routes, 'admin');
    }

    private function testTeacherRoutes()
    {
        $this->info('Probando rutas de profesor...');
        
        $routes = [
            'GET:/courses' => 200,
            'GET:/assignments' => 200,
            'GET:/grades' => 200
        ];

        $this->testRoutes($routes, 'teacher');
    }

    private function testStudentRoutes()
    {
        $this->info('Probando rutas de estudiante...');
        
        $routes = [
            'GET:/student/dashboard/assignments' => 200,
            'GET:/student/dashboard/grades' => 200,
            'GET:/student/dashboard/schedule' => 200
        ];

        $this->testRoutes($routes, 'student');
    }

    private function testGuardianRoutes()
    {
        $this->info('Probando rutas de tutor...');
        
        $routes = [
            'GET:/guardians' => 200,
            'GET:/my-students' => 200
        ];

        $this->testRoutes($routes, 'guardian');
    }

    private function testCommonRoutes()
    {
        $this->info('Probando rutas comunes...');
        
        $routes = [
            'GET:/common/events' => 200,
            'GET:/common/messages' => 200
        ];

        // Probar con cada rol
        foreach (['admin', 'teacher', 'student', 'guardian'] as $role) {
            $this->testRoutes($routes, $role);
        }
    }

    private function testRoutes($routes, $role)
    {
        if (!isset($this->tokens[$role])) {
            $this->error("No hay token para el rol {$role}");
            return;
        }

        foreach ($routes as $route => $expectedStatus) {
            [$method, $path] = explode(':', $route);
            
            $response = Http::withToken($this->tokens[$role])
                ->withHeaders(['Accept' => 'application/json'])
                ->send($method, $this->baseUrl . $path);
            
            $actualStatus = $response->status();
            
            if ($actualStatus === $expectedStatus) {
                $this->info("✓ {$role} - {$method} {$path}");
            } else {
                $this->error("✗ {$role} - {$method} {$path} (Esperado: {$expectedStatus}, Obtenido: {$actualStatus})");
                $this->error("Respuesta: " . $response->body());
            }
        }
    }
}
