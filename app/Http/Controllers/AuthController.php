<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeStudentCredentialsMail;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * POST /api/register
     * Crea un usuario (por defecto estudiante) y su registro Student asociado.
     * Body esperado: { name, email, password, telefono? }
     * Opcional: role (admin|teacher|guardian|student) restringido para ampliaciones futuras.
     */
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            // Email ya no viene del cliente; lo generamos
            'password' => ['required','string','min:8','regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&\\/\-+]).{8,}$/'],
            'telefono' => ['nullable','string','max:30'],
            'fecha_nacimiento' => ['nullable','date'],
            'direccion' => ['nullable','string','max:500'],
            'personal_email' => ['nullable','email','max:255'],
        ], [
            'password.regex' => 'La contraseña debe incluir mayúsculas, minúsculas, números y símbolos.',
        ]);

        // Rol fijo estudiante
        $role = 'student';

        // Generar base de email a partir del nombre: "nombre-apellidos" => usar punto.
        $base = Str::slug($data['name'], '.'); // ej: "mateo.gomez"
        if ($base === '') {
            $base = 'usuario';
        }
        $email = $base.'@edusync.com';
        $i = 1;
        while (User::where('email', $email)->exists()) {
            $email = $base.$i.'@edusync.com';
            $i++;
        }

        $plainPassword = $data['password'];
        $user = User::create([
            'name' => $data['name'],
            'email' => $email,
            'password' => Hash::make($plainPassword),
            'role' => $role,
        ]);

        // Generar matrícula: STU + año + ID padded
        $matricula = 'STU'.date('Y').str_pad($user->id, 5, '0', STR_PAD_LEFT);

        $student = Student::create([
            'user_id' => $user->id,
            'matricula' => $matricula,
            'telefono' => $data['telefono'] ?? null,
            'fecha_nacimiento' => $data['fecha_nacimiento'] ?? null,
            'direccion' => $data['direccion'] ?? null,
        ]);

        // Enviar correo de bienvenida si proporcionó correo personal
        if (!empty($data['personal_email'])) {
            try {
                // Ya no enviamos contraseña en claro
                Mail::to($data['personal_email'])->send(new WelcomeStudentCredentialsMail($user, $student, $email));
            } catch (\Throwable $mailError) {
                \Log::warning('Fallo envío correo bienvenida: '.$mailError->getMessage());
            }
        }

        $token = $user->createToken('registration')->plainTextToken;

        return response()->json([
            'message' => 'Registro exitoso',
            'generated_email' => $email,
            'token' => $token,
            'user' => $user,
            'student' => $student,
            'email_sent' => !empty($data['personal_email'])
        ], 201);
    }
    /**
     * POST /api/login
     * Body: { email, password }
     * Returns: { token, user }
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return response()->json(['error' => 'Credenciales inválidas'], 401);
        }

        $token = $user->createToken('web')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user,
        ]);
    }

    /**
     * POST /api/logout (Auth required)
     */
    public function logout(Request $request)
    {
        $token = $request->user()?->currentAccessToken();
        if ($token) {
            $token->delete();
        }

        return response()->json(['message' => 'Sesión cerrada']);
    }
}
