<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Clase extends Model
{
    public $table = "clases";

    protected $fillable = [
        'profesor_id', 	
        'curso_id', 	
        'materia_id', 	
        'hora_entrada', 	
        'hora_salida', 	
        'aula'
    ];    

    public function materia(){
        return $this->belongsTo(Materia::class);
    }

    public function cursos(){
        return $this->belongsTo(Curso::class);
    }
}
