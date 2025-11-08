<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Evitar duplicados si el seeder se ejecuta múltiples veces
        if (User::where('email', 'admin@edusync.com')->exists()) {
            $this->command->info('Admin user already exists. Skipping...');
            return;
        }

        // Password desde env o fallback seguro (CAMBIAR EN PRODUCCIÓN)
        $password = env('ADMIN_PASSWORD', 'Admin2025!Secure');

        User::create([
            'name' => 'Administrador Principal',
            'email' => 'admin@edusync.com',
            'password' => Hash::make($password),
            'role' => 'admin',
        ]);

        $this->command->info('Admin user created: admin@edusync.com');
        if (!env('ADMIN_PASSWORD')) {
            $this->command->warn('⚠️  Using default password. Set ADMIN_PASSWORD env variable in production!');
        }
    }
}
