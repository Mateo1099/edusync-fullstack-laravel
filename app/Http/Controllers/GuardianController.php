<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Guardian;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class GuardianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Guardian::all();
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
        // Requiere rol admin (middleware en rutas)
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'password' => ['required','string','min:8','regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&\\/\-+]).{8,}$/'],
        ], [
            'password.regex' => 'La contraseña debe incluir mayúsculas, minúsculas, números y símbolos.',
        ]);

        $base = Str::slug($data['name'], '.');
        if ($base === '') { $base = 'guardian'; }
        $email = $base.'@edusync.com';
        $i=1; while (User::where('email',$email)->exists()) { $email=$base.$i.'@edusync.com'; $i++; }

        $user = User::create([
            'name' => $data['name'],
            'email' => $email,
            'password' => Hash::make($data['password']),
            'role' => 'guardian',
        ]);

        $guardian = Guardian::create([
            'user_id' => $user->id,
        ]);

        return response()->json([
            'message' => 'Tutor creado',
            'email' => $email,
            'user' => $user,
            'guardian' => $guardian
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Guardian::findOrFail($id);
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
    public function update(Request $request, $id)
    {
        $guardian = Guardian::findOrFail($id);
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            // el email pertenece al usuario, no a guardians
            'email' => 'sometimes|required|email|unique:users,email,' . ($guardian->user->id ?? 'null'),
        ]);

        // Actualizar datos del usuario asociado si vienen
        if (isset($validated['name']) || isset($validated['email'])) {
            $user = $guardian->user;
            if ($user) {
                if (isset($validated['name'])) { $user->name = $validated['name']; }
                if (isset($validated['email'])) { $user->email = $validated['email']; }
                $user->save();
            }
        }

        return response()->json(['message' => 'Tutor actualizado', 'guardian' => $guardian->load('user')]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $guardian = Guardian::findOrFail($id);
        $guardian->delete();
        return response()->json(null, 204);
    }
}
