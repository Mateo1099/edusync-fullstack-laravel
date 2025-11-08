<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'nombre','descripcion','created_by','fecha_inicio','fecha_fin','codigo_curso','creditos','estado'
    ];
    /**
     * Un curso pertenece a un profesor.
     */
    // Si se requiere teacher en el futuro, agregar columna y relación

    /**
     * Un curso puede tener muchas inscripciones.
     */
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Un curso puede tener muchos estudiantes (relación a través de inscripciones).
     */
    public function students()
    {
        return $this->hasManyThrough(Student::class, Enrollment::class);
    }
}
