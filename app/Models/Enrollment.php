<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    protected $fillable = [
        'student_id', 'course_id', 'fecha_inscripcion', 'estado', 'calificacion_final'
    ];
    /**
     * Una inscripción pertenece a un estudiante.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Una inscripción pertenece a un curso.
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
