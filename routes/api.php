<?php

use App\Http\Controllers\ClaseController;
use App\Http\Controllers\CursoAlumnoController;
use App\Http\Controllers\CursoController;
use App\Http\Controllers\MateriaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::apiResource('/clases', ClaseController::class);

Route::apiResource('/cursos', CursoController::class);

Route::apiResource('/materias',MateriaController::class);
Route::apiResource('/cursoAlumnos', CursoAlumnoController::class);

