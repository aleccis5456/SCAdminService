<?php

namespace App\Http\Controllers;

use App\Models\CursoAlumno;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Helper\Helper;

class CursoAlumnoController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(CursoAlumno::all());
    }

    public function store(Request $request)
    {    
        $response = Helper::validarToken($request);
        if ($response != 'auth') {
            return $response;
        }
        if ($request->has('bulk')) {
            return Helper::arrAggCursoAlumno($request);
        } else {
            $validator = Validator::make($request->all(), [
                'curso_id' => 'required|string|exists:cursos,id',
                'alumno_id' => 'required|string|exists:alumnos,id'
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'message' => 'error en la validacion',
                    'errors' => $validator->errors(),
                ]);
            }
            $cursoAlumno = CursoAlumno::create([
                'curso_id' => $request->curso_id,
                'alumno_id' => $request->alumno_id,
            ]);

            return response()->json([
                'message' => 'se ha agregado el alumno al curso',
                'relation' => $cursoAlumno,
            ]);
        }
    }

    public function update(Request $request, String $id)
    {
        $response = Helper::validarToken($request);
        if ($response != 'auth') {
            return $response;
        }

        $validator = Validator::make($request->all(), [
            'curso_id' => 'sometimes|string|exists:cursos,id',
            'alumno_id' => 'sometimes|string|exists:alumnos,id'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'error en la validacion',
                'errors' => $validator->errors(),
            ]);
        }

        $relation = CursoAlumno::find($id);
        if (!$relation) {
            throw new \Exception('Relacion no existe');
        }

        try {
            $relation->update($request->all());

            return response()->json([
                'message' => 'el cambio se ejecuto con exito',
                'relation' => $relation,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'error',
                'errors' => $e->getMessage(),
            ]);
        }
    }

    public function destroy(String $id, Request $request)
    {
        $response = Helper::validarToken($request);
        if ($response != 'auth') {
            return $response;
        }

        try {
            $relation = CursoAlumno::destroy($id);
            return response()->json(['message' => 'relacion eliminada']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }
}
