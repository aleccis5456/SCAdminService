<?php

namespace App\Http\Controllers;

use App\Models\Clase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Helper\Helper;

class ClaseController extends Controller
{
    public function index()
    {
        return response()->json(Clase::all());
    }

    public function store(Request $request)
    {
        $response = Helper::validarToken($request);
        if ($response != 'auth') {
            return $response;
        }
        if ($request->has('bulk')) {
            return Helper::arrStoreClases($request);
        } else {
            $validator = Validator::make($request->all(), [
                'profesor_id' => 'required|numeric|exists:profesores,id',
                'curso_id' => 'required|numeric|exists:cursos,id',
                'materia_id' => 'required|numeric|exists:materias,id',
                'hora_entrada' => 'required|date_format:H:i',
                'hora_salida' => 'required|date_format:H:i',
                'aula' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'error en la validacion',
                    'errors' => $validator->errors(),
                ]);
            }
            try {
                $clase = Clase::create($request->all());
                return response()->json([
                    'message' => 'clase creada',
                    'clase' => $clase
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'message' => 'error al crear la clase',
                    'errors' => $e->getMessage(),
                ], 400);
            }
        }
    }
    //pendiente
    public function update(Request $request, String $id)
    {
        $response = Helper::validarToken($request);
        if ($response != 'auth') {
            return $response;
        }
        $validator = Validator::make($request->all(), [
            'profesor_id' => 'sometimes|numeric|exists:profesores,id',
            'curso_id' => 'sometimes|numeric|exists:cursos,id',
            'materia_id' => 'sometimes|numeric|exists:materias,id',
            'hora_entrada' => 'sometimes|date_format:H:i',
            'hora_salida' => 'sometimes|date_format:H:i',
            'aula' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'error en la validacion',
                'errors' => $validator->errors(),
            ]);
        }

        $clase = Clase::find($id);
        if(!$clase){
            throw new \Exception('clase no encontrada');
        }
        try{
            $clase->update($request->all());

            return response()->json([
                'message' => 'clase actualizada',
                'clase' => $clase
            ]);
        }catch(\Exception $e){
            return response()->json([
                'message' => 'error al actualizar',
                'errors' => $e->getMessage(),
            ]);
        }
    }

    public function destroy(String $id)
    {
        $clase = Clase::destroy($id);
        if (!$clase) {
            return response()->json([
                'message' => 'clase no encontrada'
            ], 404);
        }
        return response()->json([
            'message' => 'clase eliminada'
        ]);
    }
}
