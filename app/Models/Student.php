<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    // Permitir asignación masiva de los campos usados en controladores y registro
    protected $fillable = [
        'user_id', 'matricula', 'grupo', 'fecha_nacimiento', 'telefono', 'direccion'
    ];
    /**
     * Un estudiante pertenece a un curso.
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Un estudiante tiene un tutor/responsable (guardian).
     */
    public function guardian()
    {
        return $this->belongsTo(Guardian::class);
    }

    /**
     * Un estudiante puede tener muchas inscripciones.
     */
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Relación con el usuario base (tabla users)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
