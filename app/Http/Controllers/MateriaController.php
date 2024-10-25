<?php

namespace App\Http\Controllers;

use App\Models\Materia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Helper\Helper;

class MateriaController extends Controller
{    
    public function index(Request $request){
        $busqueda = $request->query('b');        
        $query = Materia::query();

        if($request->has('b')){  
            $query->whereLike('name', "%$busqueda%");
        }else{
            $query->orderByDesc('id');
        }        
    
        $materias = $query->get();

        return response()->json($materias);
    }
    
    public function store(Request $request){        
        $response = Helper::validarToken($request); 
        if($response != 'auth'){
            return $response;
        }
    
        if(!$request->has('bulk')){
            $validator = Validator::make($request->all(), [
                'name' => 'required|string'
            ]);
    
            if($validator->fails()){
                return response()->json([
                    'message' => 'error en la validacion',
                    'errors' => $validator->errors(),
                ], 400);
            }
    
            $materia = Materia::create([
                'name' => $request->name,
            ]);
    
            return response()->json([
                'message' => 'materia creada',
                'materia' => $materia
            ], 201);
        }
        else{
            return Helper::arrStoreMaterias($request);
        }        
    }

    public function update(Request $request, String $id){
        $response = Helper::validarToken($request);
        if($response != 'auth'){
            return $response;
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string'
        ]);
        if($validator->fails()){
            return response()->json([
                'message' => 'error en la validacion',
                'errors' => $validator->errors(),
            ]);
        }
        $materia = Materia::find($id);
        if(!$materia){
            return response()->json(['message' => 'materia no encontrada'], 404);
        }
        $materia->update($request->all());

        return response()->json([
            'message' => 'materia actualizada',
            'materia' => $materia
        ]);
    }

    public function destroy(String $id){
        $materia = Materia::destroy($id);
        if(!$materia){
            return response()->json(['message' => 'no se pudo borrar'], 400);
        }
        return response()->json('materia borrado', 200);
    }
}
