<?php

namespace App\Helper;

use App\Models\Clase;
use App\Models\Curso;
use App\Models\Materia;
use App\Models\CursoAlumno;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class Helper 
{
    public static function arrAggCursoAlumno(Request $request){
        $curAluCreadas = [];        
        foreach($request->bulk as $cursoAlumno){
            $validator = Validator::make($cursoAlumno,[
                'curso_id' => 'required|string|exists:cursos,id',
                'alumno_id' => 'required|string|exists:alumnos,id'
            ]);
            if($validator->fails()){
                return response()->json([
                    'message' => 'error en la validacion en el helper',
                    'errors' => $validator->errors(),
                ]);
            }
            try{
                $newCurAlu = CursoAlumno::create([
                    'curso_id' => $cursoAlumno['curso_id'],
                    'alumno_id' => $cursoAlumno['alumno_id'],
                ]);
    
                $curAluCreadas[] = $newCurAlu;
            }catch(\Exception $e){
                return response()->json([
                    'message' => 'error al crear la relacion',
                    ''
                ]);
            }            
        }
        return response()->json([
            'message' => 'se han agregado los alumnos a los cursos',
            'relations' => $curAluCreadas,
        ]);        
        
    }

    public static function arrStoreMaterias(Request $request)
    {
        $matiasCreadas = [];        
        foreach ($request->bulk as $materia) {
            $validator = Validator::make($materia, [
                'name' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'error en la validacion del helper',
                    'errors' => $validator->errors(),
                ]);
            }
            $materia = Materia::create(['name' => $materia['name']]);

            $matiasCreadas[] = $materia;
        }

        return response()->json([
            'message' => 'materias creadas',
            'materias' => $matiasCreadas,
        ]);
    }

    public static function arrStoreCursos(Request $request) {
        $cursos = $request->bulk;        
        $cursosCreados = [];             
        foreach($cursos as $curso){
            $validator = Validator::make($curso, [
                'curso' => 'required|string',
                'promocion' => 'required|numeric|digits:4',
                'bachillerato' => 'required|string'
            ]);
            if($validator->fails()){
                return response()->json([
                    'message' => 'error en la validacion del helper',
                    'errors' => $validator->errors(),
                ]);
            }
            try{
                $newCurso = Curso::create([
                    'curso' => $curso['curso'],
                    'promocion' => $curso['promocion'],
                    'bachillerato' => $curso['bachillerato'],
                ]);

                $cursosCreados[] = $newCurso;
            }catch(\Exception $e){
                return response()->json([
                    'message' => $e->getMessage(),
                ]);
            }                        
        }

        return response()->json([
            'message' => 'cursos creados',
            'cursos' => $cursosCreados,
        ]);
    }

    public static function arrStoreClases(Request $request){
        $clasesCreadas = [];
        $clases = $request->bulk;        
        foreach($clases as $clase){
            $validator = Validator::make($clase, [
                'profesor_id' => 'required|numeric|exists:profesores,id',
                'curso_id' => 'required|numeric|exists:cursos,id',
                'materia_id' => 'required|numeric|exists:materias,id',
                'hora_entrada' => 'required|date_format:H:i',
                'hora_salida' => 'required|date_format:H:i',
                'aula' => 'nullable|string',
            ]);
    
            if($validator->fails()){
                return response()->json([
                    'message' => 'error en la validacion en el helper',
                    'errors' => $validator->errors(),
                ]);
            }

            try{
                $newClase = Clase::create([
                    'profesor_id' => $clase['profesor_id'],
                    'curso_id' => $clase['curso_id'],
                    'materia_id' => $clase['materia_id'],
                    'hora_entrada' => $clase['hora_entrada'],
                    'hora_salida' => $clase['hora_salida'],
                    'aula' => $clase['aula']
                ]);
    
                $clasesCreadas[] = $newClase;
            }catch(\Exception $e){
                return response()->json([
                    'message' => 'error al crear clase',
                    'errors' => $e->getMessage(),
                ]);
            }            
        }
        return response()->json([
            'message' => 'clases creadas',
            'clases' => $clasesCreadas,
        ]);

    }

    public static function validarToken(Request $request){        
        $token = $request->header('authorization');
        $response = Http::withHeaders([
            'Authorization' => $token
        ])->post('http://127.0.0.1:8001/api/validarToken');

        $user = $response->object();        

        if(!$user){
            return response()->json(['message' => 'no autorizado'], 401);
        }
        if($user->rol != 'admin'){  
            return response()->json(['message' => 'no autorizado'], 401);
        }        
        return 'auth';
    }   

    public static function validarProfesor(Request $request){        
        $token = $request->header('authorization');
        $response = Http::withHeaders([
            'Authorization' => $token
        ])->post('http://127.0.0.1:8001/api/validarToken');

        $user = $response->object();        

        if(!$user){
            return response()->json(['message' => 'no autorizado'], 401);
        }
        if($user->rol != 'profesor'){  
            return response()->json(['message' => 'no autorizado'], 401);
        }  
        
        $profesor = DB::table('profesores')->where('user_id', $user->id)->first();
        
        return ([
            'auth' => 1,
            'user' => $user,
            'profesor_id' => $profesor->id,            
        ]);
    }   
}

