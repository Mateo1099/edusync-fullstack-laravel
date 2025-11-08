<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     * Can be scaled for filters and pagination.
     */
    public function index()
    {
        return Course::all();
    }

    /**
     * GET /api/my/courses (solo estudiante)
     * Devuelve los cursos en los que el alumno autenticado está inscrito, junto con métricas básicas.
     */
    public function myCourses()
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'student') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $student = \App\Models\Student::where('user_id', $user->id)->first();
        if (!$student) {
            return response()->json(['data' => []]);
        }
        $enrollmentIds = \App\Models\Enrollment::where('student_id', $student->id)->pluck('course_id');
        $courses = Course::whereIn('id', $enrollmentIds)->get();
        // Adjuntar número de tareas activas y fecha de la próxima tarea
        $courses = $courses->map(function($c){
            $assignments = \App\Models\Assignment::where('course_id',$c->id)->where('due_date','>', now())->get();
            $c->active_assignments = $assignments->count();
            $c->next_due_date = $assignments->min('due_date');
            return $c;
        });
        return response()->json(['data' => $courses]);
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
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date',
            'creditos' => 'nullable|integer|min:1',
        ]);

        // Usuario autenticado (admin o teacher) que crea el curso
        $creator = Auth::user();
        if (!$creator) {
            return response()->json(['error' => 'No autenticado'], 401);
        }

        // Generar código único del curso
        $codigoBase = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/','', $validated['nombre']),0,6));
        if ($codigoBase === '') { $codigoBase = 'CURSO'; }
        $codigo = $codigoBase.'-'.rand(100,999);
        while (Course::where('codigo_curso',$codigo)->exists()) {
            $codigo = $codigoBase.'-'.rand(100,999);
        }

        $course = Course::create([
            'nombre' => $validated['nombre'],
            'descripcion' => $validated['descripcion'] ?? null,
            'created_by' => $creator->id,
            'fecha_inicio' => $validated['fecha_inicio'] ?? null,
            'fecha_fin' => $validated['fecha_fin'] ?? null,
            'codigo_curso' => $codigo,
            'creditos' => $validated['creditos'] ?? 3,
            'estado' => 'activo',
        ]);

        return response()->json([
            'message' => 'Curso creado',
            'course' => $course
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Course::findOrFail($id);
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
        $course = Course::findOrFail($id);
        $validated = $request->validate([
            'nombre' => 'sometimes|required|string|max:255',
            'descripcion' => 'nullable|string',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date',
            'creditos' => 'nullable|integer|min:1',
            'estado' => 'nullable|in:activo,inactivo'
        ]);
        $course->update($validated);
        return response()->json(['message' => 'Curso actualizado', 'course' => $course]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $course = Course::findOrFail($id);
        $course->delete();
        return response()->json(null, 204);
    }
}
