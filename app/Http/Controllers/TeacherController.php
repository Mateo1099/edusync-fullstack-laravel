<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Teacher;
use App\Models\User;

class TeacherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Obtiene todos los profesores con su usuario asociado
        $teachers = Teacher::with('user')->get();
        return response()->json($teachers);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // En una API normalmente no se usa, pero en web retorna la vista de creación
        return view('teachers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Este endpoint requiere rol admin (ya asegurado por middleware en rutas)
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'password' => ['required','string','min:8','regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&\\/\-+]).{8,}$/'],
            'especialidad' => ['nullable','string','max:255'],
            'telefono' => ['nullable','string','max:30'],
            'bio' => ['nullable','string','max:2000'],
        ], [
            'password.regex' => 'La contraseña debe incluir mayúsculas, minúsculas, números y símbolos.',
        ]);

        // Generar email único basado en nombre
        $base = \Illuminate\Support\Str::slug($data['name'], '.');
        if ($base === '') { $base = 'docente'; }
        $email = $base.'@edusync.com';
        $i=1; while (User::where('email',$email)->exists()) { $email=$base.$i.'@edusync.com'; $i++; }

        $user = User::create([
            'name' => $data['name'],
            'email' => $email,
            'password' => \Illuminate\Support\Facades\Hash::make($data['password']),
            'role' => 'teacher',
        ]);

        $teacher = Teacher::create([
            'user_id' => $user->id,
            'especialidad' => $data['especialidad'] ?? null,
            'telefono' => $data['telefono'] ?? null,
            'bio' => $data['bio'] ?? null,
        ]);

        return response()->json([
            'message' => 'Docente creado',
            'email' => $email,
            'user' => $user,
            'teacher' => $teacher
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
    $teacher = Teacher::with('user','courses')->findOrFail($id);
    return response()->json($teacher);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // En una API normalmente no se usa, pero en web retorna la vista de edición
        $teacher = Teacher::findOrFail($id);
        return view('teachers.edit', compact('teacher'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $teacher = Teacher::findOrFail($id);
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:teachers,email,' . $teacher->id,
        ]);
        $teacher->update($validated);
        return response()->json($teacher);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $teacher = Teacher::findOrFail($id);
        $teacher->delete();
        return response()->json(['success' => true]);
    }
}
