<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Teacher;
use App\Models\Enrollment;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class AssignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $user = Auth::user();
        $query = Assignment::with(['course', 'teacher'])->latest();
        if ($user && $user->role === 'teacher') {
            $teacher = Teacher::where('user_id', $user->id)->first();
            if ($teacher) { $query->where('teacher_id', $teacher->id); }
        } elseif ($user && $user->role === 'student') {
            // Filtrar tareas solo de los cursos en los que está inscrito el alumno
            $student = \App\Models\Student::where('user_id', $user->id)->first();
            if ($student) {
                $courseIds = \App\Models\Enrollment::where('student_id', $student->id)->pluck('course_id');
                $query->whereIn('course_id', $courseIds);
            } else {
                $query->whereRaw('1=0'); // sin resultados si no hay perfil estudiante
            }
        }
        $assignments = $query->paginate(10);
        return response()->json(['success'=>true,'data'=>$assignments]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'teacher') {
            return response()->json(['success'=>false,'message'=>'Unauthorized'], 403);
        }
        $teacher = Teacher::where('user_id', $user->id)->first();
        if (!$teacher) {
            return response()->json(['success'=>false,'message'=>'Teacher profile not found'], 422);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'course_id' => 'required|exists:courses,id',
            'due_date' => 'required|date|after:now',
            'max_score' => 'required|integer|min:0|max:100',
            'attachment_path' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

    // Asignar teacher_id del docente autenticado
    $payload = $request->all();
    $payload['teacher_id'] = $teacher->id;
    $assignment = Assignment::create($payload);

        return response()->json([
            'success' => true,
            'data' => $assignment->load(['course', 'teacher'])
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Assignment $assignment): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $assignment->load(['course', 'teacher', 'grades'])
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Assignment $assignment): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'string|max:255',
            'description' => 'string',
            'course_id' => 'exists:courses,id',
            'teacher_id' => 'exists:teachers,id',
            'due_date' => 'date|after:now',
            'max_score' => 'integer|min:0|max:100',
            'status' => 'string|in:active,archived',
            'attachment_path' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $assignment->update($request->all());

        return response()->json([
            'success' => true,
            'data' => $assignment->load(['course', 'teacher'])
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Assignment $assignment): JsonResponse
    {
        $assignment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Assignment deleted successfully'
        ]);
    }
}
