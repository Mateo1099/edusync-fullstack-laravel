<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    /**
     * Un rol puede tener muchos usuarios (relación polimórfica).
     * Se puede escalar para distintos tipos de usuario.
     */
    public function users()
    {
        // Relación polimórfica, ejemplo:
        // return $this->morphToMany(User::class, 'roleable');
    }
}
