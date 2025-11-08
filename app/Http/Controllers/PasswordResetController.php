<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class PasswordResetController extends Controller
{
    /**
     * POST /api/password/forgot
     * Body: { email }
     * Envía link de reset (en mailer log). Siempre devuelve 200 para no filtrar usuarios.
     */
    public function requestReset(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        // Usa broker por defecto (users)
        $status = Password::sendResetLink($request->only('email'));

        // Normalizamos respuesta para no revelar si el correo existe
        return response()->json([
            'message' => $status === Password::RESET_LINK_SENT
                ? 'Si el correo existe se ha enviado un enlace de recuperación.'
                : 'Si el correo existe se ha enviado un enlace de recuperación.'
        ]);
    }

    /**
     * POST /api/password/reset
     * Body: { email, token, password }
     */
    public function reset(Request $request)
    {
        $data = $request->validate([
            'email' => ['required','email'],
            'token' => ['required','string'],
            'password' => ['required','string','confirmed','min:8','regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\\d)(?=.*[@$!%*?&\\/\\-+]).{8,}$/'],
        ], [
            'password.regex' => 'La contraseña debe incluir mayúsculas, minúsculas, números y símbolos.'
        ]);

        $status = Password::reset(
            $request->only('email','password','password_confirmation','token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages([
                'email' => ['Token inválido o expirado.']
            ]);
        }

        return response()->json(['message' => 'Contraseña actualizada']);
    }
}
