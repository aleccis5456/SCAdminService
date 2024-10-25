<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CursoAlumno extends Model
{
    protected $fillable = [
        'curso_id', 	
        'alumno_id'
    ];
}
