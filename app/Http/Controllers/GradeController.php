<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\Assignment;
use App\Models\Enrollment;
use App\Models\Teacher;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class GradeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $grades = Grade::with(['student', 'assignment'])
            ->latest()
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $grades
        ]);
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

        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'assignment_id' => 'required|exists:assignments,id',
            'score' => 'required|numeric|min:0|max:100',
            'feedback' => 'nullable|string',
            'submission_path' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Verificar si ya existe una calificación para esta tarea y estudiante
        $existingGrade = Grade::where('student_id', $request->student_id)
            ->where('assignment_id', $request->assignment_id)
            ->first();

        if ($existingGrade) {
            return response()->json([
                'success' => false,
                'message' => 'A grade already exists for this student and assignment'
            ], 422);
        }

        // Validar que el docente propietario de la tarea sea el que califica
        $assignment = Assignment::find($request->assignment_id);
        $teacher = Teacher::where('user_id', $user->id)->first();
        if (!$assignment || !$teacher || $assignment->teacher_id !== $teacher->id) {
            return response()->json(['success'=>false,'message'=>'No puedes calificar esta tarea'], 403);
        }

        // Validar que el estudiante esté inscrito en el curso de la tarea
        $enrolled = Enrollment::where('student_id',$request->student_id)
            ->where('course_id',$assignment->course_id)
            ->exists();
        if (!$enrolled) {
            return response()->json(['success'=>false,'message'=>'El estudiante no está inscrito en el curso'], 422);
        }

        $grade = Grade::create($request->all());

        return response()->json([
            'success' => true,
            'data' => $grade->load(['student', 'assignment'])
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Grade $grade): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $grade->load(['student', 'assignment'])
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Grade $grade): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'score' => 'numeric|min:0|max:100',
            'feedback' => 'nullable|string',
            'status' => 'string|in:submitted,graded,revised',
            'submission_path' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $grade->update($request->all());

        return response()->json([
            'success' => true,
            'data' => $grade->load(['student', 'assignment'])
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Grade $grade): JsonResponse
    {
        $grade->delete();

        return response()->json([
            'success' => true,
            'message' => 'Grade deleted successfully'
        ]);
    }
}
