<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeStudentCredentialsMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public Student $student;
    public string $generatedEmail;
    // Removemos password en claro por seguridad
    public ?string $plainPassword; // deprecated: ya no se usará en la vista

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, Student $student, string $generatedEmail, ?string $plainPassword = null)
    {
        $this->user = $user;
        $this->student = $student;
        $this->generatedEmail = $generatedEmail;
        // Mantener compatibilidad si se envió históricamente, pero no usar.
        $this->plainPassword = null; // Fuerza a no exponer contraseña
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Bienvenido a EduSync - Credenciales de acceso')
            ->view('emails.welcome_student_credentials');
    }
}
