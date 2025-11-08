<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Event::query()->with('creator');

        // Filtrar por tipo de evento
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Filtrar por fecha
        if ($request->has('start_date')) {
            $query->where('start_date', '>=', $request->start_date);
        }
        if ($request->has('end_date')) {
            $query->where('end_date', '<=', $request->end_date);
        }

        // Solo mostrar eventos públicos o creados por el usuario
        $query->where(function($q) {
            $q->where('is_public', true)
              ->orWhere('creator_id', Auth::id());
        });

        $events = $query->latest()->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $events
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'location' => 'nullable|string',
            'type' => 'required|string',
            'is_public' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $event = Event::create([
            ...$request->all(),
            'creator_id' => Auth::id(),
            'status' => 'scheduled'
        ]);

        return response()->json([
            'success' => true,
            'data' => $event->load('creator')
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event): JsonResponse
    {
        // Verificar si el usuario puede ver el evento
        if (!$event->is_public && $event->creator_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $event->load('creator')
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event): JsonResponse
    {
        // Solo el creador puede actualizar el evento
        if ($event->creator_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'string|max:255',
            'description' => 'string',
            'start_date' => 'date|after_or_equal:today',
            'end_date' => 'date|after_or_equal:start_date',
            'location' => 'nullable|string',
            'type' => 'string',
            'is_public' => 'boolean',
            'status' => 'string|in:scheduled,cancelled,completed'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $event->update($request->all());

        return response()->json([
            'success' => true,
            'data' => $event->load('creator')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event): JsonResponse
    {
        // Solo el creador puede eliminar el evento
        if ($event->creator_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $event->delete();

        return response()->json([
            'success' => true,
            'message' => 'Event deleted successfully'
        ]);
    }
}
