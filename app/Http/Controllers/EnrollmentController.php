<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Enrollment;

class EnrollmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Enrollment::all();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'course_id' => 'required|exists:courses,id',
            // Agrega más campos según la migración
        ], [
            'student_id.required' => 'El estudiante es obligatorio.',
            'student_id.exists' => 'El estudiante seleccionado no existe.',
            'course_id.required' => 'El curso es obligatorio.',
            'course_id.exists' => 'El curso seleccionado no existe.'
        ]);
        $enrollment = Enrollment::create($validated);
        return response()->json($enrollment, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Enrollment::findOrFail($id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $enrollment = Enrollment::findOrFail($id);
        $validated = $request->validate([
            'student_id' => 'sometimes|required|exists:students,id',
            'course_id' => 'sometimes|required|exists:courses,id',
        ]);
        $enrollment->update($validated);
        return response()->json($enrollment);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $enrollment = Enrollment::findOrFail($id);
        $enrollment->delete();
        return response()->json(null, 204);
    }
}
