<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\User;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Obtiene todos los estudiantes con su usuario asociado
        $students = Student::with('user')->get();
        return response()->json($students);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // En una API normalmente no se usa, pero en web retorna la vista de creación
        return view('students.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'matricula' => 'required|unique:students,matricula',
            'grupo' => 'nullable|string',
            'fecha_nacimiento' => 'nullable|date',
            'telefono' => 'nullable|string',
            'direccion' => 'nullable|string',
        ], [
            'user_id.required' => 'El campo usuario es obligatorio.',
            'user_id.exists' => 'El usuario seleccionado no es válido.',
            'matricula.required' => 'El campo matrícula es obligatorio.',
            'matricula.unique' => 'La matrícula ya está en uso.',
            'grupo.string' => 'El grupo debe ser un texto.',
            'fecha_nacimiento.date' => 'La fecha de nacimiento debe ser una fecha válida.',
            'telefono.string' => 'El teléfono debe ser un texto.',
            'direccion.string' => 'La dirección debe ser un texto.',
        ]);
        // Crear el estudiante
        $student = Student::create($validated);
        return response()->json(['success' => true, 'student' => $student], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $student = Student::with('user')->findOrFail($id);
        return response()->json($student);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // En una API normalmente no se usa, pero en web retorna la vista de edición
        $student = Student::findOrFail($id);
        return view('students.edit', compact('student'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $student = Student::findOrFail($id);
        $validated = $request->validate([
            'user_id' => 'sometimes|required|exists:users,id',
            'matricula' => 'sometimes|required|unique:students,matricula,' . $student->id,
            'grupo' => 'nullable|string',
            'fecha_nacimiento' => 'nullable|date',
            'telefono' => 'nullable|string',
            'direccion' => 'nullable|string',
        ]);
        $student->update($validated);
        return response()->json(['success' => true, 'student' => $student]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $student = Student::findOrFail($id);
        $student->delete();
        return response()->json(['success' => true]);
    }
}
