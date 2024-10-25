<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Helper\Helper;

class CursoController extends Controller
{
    public function index(){
        return response()->json(Curso::all());
    }

    public function store(Request $request){        
        $response = Helper::validarToken($request); 
        if($response != 'auth'){
            return $response;
        }                  
        if($request->has('bulk')){
            return Helper::arrStoreCursos($request);
        }else{        
            $validator = Validator::make($request->all(), [
                "curso" => 'required|string',
                "promocion" => "required|numeric|digits:4",
                "bachillerato" => 'required|string'
            ]);
    
            if($validator->fails()){
                return response()->json([
                    'message' => 'error en la validacion',
                    'errors' => $validator->errors(),
                ]);
            }
    
            $curso = Curso::create($request->all());
            return response()->json([
                'message' => 'curso creado',
                'curso' => $curso
            ]);
        }        
    }

    public function update(Request $request, String $id){
        $response = Helper::validarToken($request);        
        if($response != 'auth'){
            return $response;            
        }

        $validator = Validator::make($request->all(), [
            "curso" => 'sometimes|string',
            "promocion" => "sometimes|numeric|digits:4",
            "bachillerato" => 'sometimes|string'
        ]);

        if($validator->fails()){
            return response()->json([
                'message' => 'error en la validacion',
                'errors' => $validator->errors(),
            ]);
        }

        $curso = Curso::find($id);
        if(!$curso){
            return response()->json([
                'message' => 'curso no econtrado'
            ], 404);
        }

        $curso->update($request->all());
        
        return response()->json([
            'message' => 'curso actualizado',
            'curso' => $curso
        ]);
    }

    public function destroy(String $id){
        $curso = Curso::destroy($id);
        if(!$curso){
            return response()->json([
                'message' => 'curso no encontrado'
            ], 404);
        }
        return response()->json(['message' => 'cursos eliminado']);
    }
}
