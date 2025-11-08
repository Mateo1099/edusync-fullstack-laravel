<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $fillable = [
        'user_id', 'especialidad', 'telefono', 'bio'
    ];
    /**
     * Un profesor puede tener muchos cursos.
     */
    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
