<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guardian extends Model
{
    protected $fillable = [
        'user_id'
    ];
    /**
     * Un tutor/responsable puede tener muchos estudiantes.
     */
    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
