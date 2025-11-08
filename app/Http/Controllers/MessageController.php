<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $user = Auth::user();
        $messages = Message::where('receiver_id', $user->id)
            ->orWhere('sender_id', $user->id)
            ->with(['sender', 'receiver'])
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $messages
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'receiver_id' => 'required|exists:users,id',
            'subject' => 'required|string|max:255',
            'content' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'subject' => $request->subject,
            'content' => $request->content,
            'status' => 'sent'
        ]);

        return response()->json([
            'success' => true,
            'data' => $message->load(['sender', 'receiver'])
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Message $message): JsonResponse
    {
        // Marcar como leído si el usuario actual es el receptor
        if (Auth::id() === $message->receiver_id && !$message->is_read) {
            $message->update([
                'is_read' => true,
                'read_at' => now()
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $message->load(['sender', 'receiver'])
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Message $message): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|string|in:sent,deleted,archived'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $message->update($request->only('status'));

        return response()->json([
            'success' => true,
            'data' => $message->load(['sender', 'receiver'])
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Message $message): JsonResponse
    {
        // Verificar que el usuario sea el remitente o el receptor
        if (Auth::id() !== $message->sender_id && Auth::id() !== $message->receiver_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $message->delete();

        return response()->json([
            'success' => true,
            'message' => 'Message deleted successfully'
        ]);
    }
}
