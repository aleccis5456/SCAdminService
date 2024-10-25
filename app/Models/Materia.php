<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Materia extends Model
{
    public $table = 'materias';
    protected $fillable = ['name'];
}
