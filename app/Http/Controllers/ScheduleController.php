<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Schedule::query()->with(['course', 'teacher']);

        // Filtrar por día de la semana
        if ($request->has('day_of_week')) {
            $query->where('day_of_week', $request->day_of_week);
        }

        // Filtrar por período académico
        if ($request->has('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        }
        if ($request->has('period')) {
            $query->where('period', $request->period);
        }

        // Filtrar por curso o profesor
        if ($request->has('course_id')) {
            $query->where('course_id', $request->course_id);
        }
        if ($request->has('teacher_id')) {
            $query->where('teacher_id', $request->teacher_id);
        }

        $schedules = $query->orderBy('day_of_week')->orderBy('start_time')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $schedules
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'course_id' => 'required|exists:courses,id',
            'teacher_id' => 'required|exists:teachers,id',
            'day_of_week' => 'required|string|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'room' => 'nullable|string',
            'period' => 'required|string',
            'academic_year' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Verificar superposición de horarios
        $overlap = Schedule::where('teacher_id', $request->teacher_id)
            ->where('day_of_week', $request->day_of_week)
            ->where('academic_year', $request->academic_year)
            ->where('period', $request->period)
            ->where(function($query) use ($request) {
                $query->whereBetween('start_time', [$request->start_time, $request->end_time])
                    ->orWhereBetween('end_time', [$request->start_time, $request->end_time]);
            })->exists();

        if ($overlap) {
            return response()->json([
                'success' => false,
                'message' => 'Schedule overlaps with existing class'
            ], 422);
        }

        $schedule = Schedule::create($request->all());

        return response()->json([
            'success' => true,
            'data' => $schedule->load(['course', 'teacher'])
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Schedule $schedule): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $schedule->load(['course', 'teacher'])
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Schedule $schedule): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'course_id' => 'exists:courses,id',
            'teacher_id' => 'exists:teachers,id',
            'day_of_week' => 'string|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'start_time' => 'date_format:H:i',
            'end_time' => 'date_format:H:i|after:start_time',
            'room' => 'nullable|string',
            'period' => 'string',
            'academic_year' => 'string',
            'status' => 'string|in:active,cancelled,completed'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Verificar superposición si se cambia el horario
        if ($request->has('start_time') || $request->has('end_time') || $request->has('day_of_week')) {
            $overlap = Schedule::where('teacher_id', $schedule->teacher_id)
                ->where('day_of_week', $request->day_of_week ?? $schedule->day_of_week)
                ->where('academic_year', $schedule->academic_year)
                ->where('period', $schedule->period)
                ->where('id', '!=', $schedule->id)
                ->where(function($query) use ($request, $schedule) {
                    $start = $request->start_time ?? $schedule->start_time;
                    $end = $request->end_time ?? $schedule->end_time;
                    $query->whereBetween('start_time', [$start, $end])
                        ->orWhereBetween('end_time', [$start, $end]);
                })->exists();

            if ($overlap) {
                return response()->json([
                    'success' => false,
                    'message' => 'Schedule overlaps with existing class'
                ], 422);
            }
        }

        $schedule->update($request->all());

        return response()->json([
            'success' => true,
            'data' => $schedule->load(['course', 'teacher'])
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Schedule $schedule): JsonResponse
    {
        $schedule->delete();

        return response()->json([
            'success' => true,
            'message' => 'Schedule deleted successfully'
        ]);
    }
}
