<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Bienvenido a EduSync</title>
    <style>
        body{font-family:Arial,Helvetica,sans-serif;color:#222}
        .card{border:1px solid #eee;border-radius:8px;padding:16px}
        .muted{color:#666;font-size:12px}
        .btn{display:inline-block;background:#0d6efd;color:#fff;padding:10px 14px;border-radius:6px;text-decoration:none}
    </style>
    </head>
<body>
    <h2>¡Bienvenido/a a EduSync, {{ $user->name }}!</h2>
    <p>Tu registro de estudiante fue exitoso. Aquí están tus datos principales:</p>
    <div class="card">
        <p><strong>Correo institucional:</strong> {{ $generatedEmail }}</p>
        <p><strong>Contraseña:</strong> (por seguridad no se incluye en el correo). Usa la que definiste durante el registro. Si la olvidas podrás solicitar recuperación.</p>
        <p><strong>Matrícula:</strong> {{ $student->matricula }}</p>
    </div>
    <p style="margin-top:16px">Puedes iniciar sesión en el portal:</p>
    <p><a class="btn" href="{{ config('app.url') }}/login.html">Ir al portal</a></p>
    <p class="muted">Nunca compartas tu contraseña. Si este correo no es para ti, ignóralo.</p>
    <p class="muted">Si no fuiste tú, ignora este correo.</p>
</body>
</html>
