<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Guardian;

class DemoUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Helper to avoid duplicates if seeder runs twice
        $ensureUser = function(array $data) {
            $existing = User::where('email', $data['email'])->first();
            return $existing ?: User::create($data);
        };

        // Common password for demo users (change later!)
        $demoPassword = Hash::make('Demo1234*');

        // TEACHER
        $teacher = $ensureUser([
            'name' => 'Docente Demo',
            'email' => 'docente.demo@edusync.local',
            'password' => $demoPassword,
            'role' => 'teacher'
        ]);
        // Crear registro base del docente (sin campos específicos por compatibilidad)
        if (class_exists(Teacher::class)) {
            Teacher::firstOrCreate(['user_id' => $teacher->id]);
        }

        // GUARDIANS (Padres/Tutores)
        $guardian1 = $ensureUser([
            'name' => 'Padre Demo Uno',
            'email' => 'padre.uno@edusync.local',
            'password' => $demoPassword,
            'role' => 'guardian'
        ]);
        if (class_exists(Guardian::class)) {
            Guardian::firstOrCreate(['user_id' => $guardian1->id]);
        }

        $guardian2 = $ensureUser([
            'name' => 'Madre Demo Dos',
            'email' => 'madre.dos@edusync.local',
            'password' => $demoPassword,
            'role' => 'guardian'
        ]);
        if (class_exists(Guardian::class)) {
            Guardian::firstOrCreate(['user_id' => $guardian2->id]);
        }

        // STUDENTS (Alumnos)
        $student1 = $ensureUser([
            'name' => 'Alumno Demo Uno',
            'email' => 'alumno.uno@edusync.local',
            'password' => $demoPassword,
            'role' => 'student'
        ]);
        if (class_exists(Student::class)) {
            Student::firstOrCreate(['user_id' => $student1->id], [
                'matricula' => 'STU-' . Str::upper(Str::random(6))
            ]);
        }

        $student2 = $ensureUser([
            'name' => 'Alumno Demo Dos',
            'email' => 'alumno.dos@edusync.local',
            'password' => $demoPassword,
            'role' => 'student'
        ]);
        if (class_exists(Student::class)) {
            Student::firstOrCreate(['user_id' => $student2->id], [
                'matricula' => 'STU-' . Str::upper(Str::random(6))
            ]);
        }

        // OPTIONAL: Link guardians to students if pivot exists (adjust table name/fields if different)
    if (DB::getSchemaBuilder()->hasTable('guardian_student')) {
            DB::table('guardian_student')->updateOrInsert([
                'guardian_id' => $guardian1->id,
                'student_id' => $student1->id
            ], []);
            DB::table('guardian_student')->updateOrInsert([
                'guardian_id' => $guardian2->id,
                'student_id' => $student2->id
            ], []);
        }

        $this->command->info('Demo users seeded. Password for all demo users: Demo1234*');
    }
}
