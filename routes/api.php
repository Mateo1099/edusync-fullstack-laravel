<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// -----------------------------
// AUTENTICACIÓN (Sanctum)
// -----------------------------
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:sensitive');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Health check OpenSSL (público para diagnóstico local)
Route::get('/health/openssl', [\App\Http\Controllers\HealthController::class,'openssl']);

// Rutas API para EduSync protegidas por roles
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::apiResource('admins', App\Http\Controllers\AdminController::class);
    Route::apiResource('roles', App\Http\Controllers\RoleController::class);
    // Como admin, gestionar docentes, tutores y cursos
    Route::apiResource('teachers', App\Http\Controllers\TeacherController::class)->only(['index','store','show','update','destroy']);
    Route::apiResource('guardians', App\Http\Controllers\GuardianController::class)->only(['index','store','show','update','destroy']);
    Route::apiResource('courses', App\Http\Controllers\CourseController::class)->only(['index','store','show','update','destroy']);
    Route::apiResource('enrollments', App\Http\Controllers\EnrollmentController::class)->only(['index','store','show','update','destroy']);
    // Listar todos los usuarios
    Route::get('users/all', function() {
        return response()->json(\App\Models\User::orderBy('created_at','desc')->get());
    });
});

Route::middleware(['auth:sanctum', 'role:teacher'])->group(function () {
    // Docente puede gestionar sus cursos; mantenemos acceso
    Route::apiResource('courses', App\Http\Controllers\CourseController::class);
    // Docente gestiona tareas y calificaciones
    Route::apiResource('assignments', App\Http\Controllers\AssignmentController::class);
    Route::apiResource('grades', App\Http\Controllers\GradeController::class)->only(['index','store','show','update','destroy']);
    // Docente puede gestionar inscripciones en sus cursos (opcional)
    Route::apiResource('enrollments', App\Http\Controllers\EnrollmentController::class)->only(['index','store','show','update','destroy']);
});

// Tutores: acceso a sus recursos (si aplica)
Route::middleware(['auth:sanctum', 'role:guardian'])->group(function () {
    Route::apiResource('guardians', App\Http\Controllers\GuardianController::class)->only(['index','show']);
});

Route::middleware(['auth:sanctum', 'role:student'])->group(function () {
    Route::apiResource('students', App\Http\Controllers\StudentController::class)->only(['index','show']);
    // Alumno puede ver sus tareas y calificaciones
    Route::get('my/assignments', [App\Http\Controllers\AssignmentController::class, 'index']);
    Route::get('my/grades', [App\Http\Controllers\GradeController::class, 'index']);
    Route::get('my/courses', [App\Http\Controllers\CourseController::class, 'myCourses']);
});

// Recuperación de contraseña (API-friendly, usa mailer "log" por defecto)
Route::post('/password/forgot', [\App\Http\Controllers\PasswordResetController::class, 'requestReset'])
    ->middleware('throttle:sensitive');
Route::post('/password/reset', [\App\Http\Controllers\PasswordResetController::class, 'reset'])
    ->middleware('throttle:sensitive');

// Puedes agregar rutas personalizadas aquí para funcionalidades extra
// Ejemplo: Route::get('courses/{id}/students', [CourseController::class, 'students']);
