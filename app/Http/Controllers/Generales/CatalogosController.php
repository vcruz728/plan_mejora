<?php

namespace App\Http\Controllers\Generales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Catalogos\Procedencias;
use App\Models\Catalogos\AmbitosSiemec;
use App\Models\Catalogos\CriteriosSiemec;


class CatalogosController extends Controller
{
 	public function viewProcedencias(){
 		return view('Catalogos.procedencias');
 	}

 	public function getProcedencias(){
 		try {
            $procedencias = Procedencias::all();

            $msg = [
                'code' => 200,
                'mensaje' => 'Catalogo de procedencias.',
                'data' => $procedencias
            ];
        } catch(\Illuminate\Database\QueryException $ex){
            $msg = [
                'code' => 400,
                'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.',
                'data' => $ex,
            ];
        }catch (Exception $e) {
            $msg = [
                'code' => 400,
                'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.',
                'data' => $e,
            ];
        }
           
        return response()->json($msg, $msg['code']);
 	}

    public function getSigla($id){
        try {
            $procedencias = Procedencias::find($id);

            $msg = [
                'code' => 200,
                'mensaje' => 'Catalogo de procedencias.',
                'data' => $procedencias
            ];
        } catch(\Illuminate\Database\QueryException $ex){
            $msg = [
                'code' => 400,
                'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.',
                'data' => $ex,
            ];
        }catch (Exception $e) {
            $msg = [
                'code' => 400,
                'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.',
                'data' => $e,
            ];
        }
           
        return response()->json($msg, $msg['code']);
    }

 	public function editProcedencia(Request $request, $id){
        try {
            $validate =  \Validator::make($request->all(), [
                    'procedencia_edit' => 'required|min:2|max:100',
                    'siglas_edit' => 'required|min:2|max:100',
                ]
            );

            if($validate->fails()){
                $msg = [
                    'code' => 411,
                    'mensaje' => 'Error',
                    'errors' => $validate->errors()
                ];

                return response()->json($msg, $msg['code']);
            }

            $procedencia = Procedencias::find($id);
            $procedencia->descripcion = $request->input('procedencia_edit');
            $procedencia->siglas = $request->input('siglas_edit');
            $procedencia->save();


            $msg = [
                'code' => 200,
                'mensaje' => 'Procedencia actualizada correctamente.',
            ];
        } catch(\Illuminate\Database\QueryException $ex){
            $msg = [
                'code' => 400,
                'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.',
                'data' => $ex,
            ];
        }catch (Exception $e) {
            $msg = [
                'code' => 400,
                'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.',
                'data' => $e,
            ];
        }
           
        return response()->json($msg, $msg['code']);
    }

    public function addProcedencia(Request $request){
        try {
            $validate =  \Validator::make($request->all(), [
                    'procedencia' => 'required|min:2|max:100',
                    'siglas' => 'required|min:2|max:100',
                ]
            );

            if($validate->fails()){
                $msg = [
                    'code' => 411,
                    'mensaje' => 'Error',
                    'errors' => $validate->errors()
                ];

                return response()->json($msg, $msg['code']);
            }

            $procedencia = new Procedencias();
            $procedencia->descripcion = $request->input('procedencia');
            $procedencia->siglas = $request->input('siglas');
            $procedencia->save();


            $msg = [
                'code' => 200,
                'mensaje' => 'Procedencia agregada correctamente.',
            ];
        } catch(\Illuminate\Database\QueryException $ex){
            $msg = [
                'code' => 400,
                'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.',
                'data' => $ex,
            ];
        }catch (Exception $e) {
            $msg = [
                'code' => 400,
                'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.',
                'data' => $e,
            ];
        }
           
        return response()->json($msg, $msg['code']);
    }

    public function delProcedencia($id){
        try {

            $procedencia = Procedencias::find($id);
            $procedencia->delete();


            $msg = [
                'code' => 200,
                'mensaje' => 'Procedencia eliminada.',
            ];
        } catch(\Illuminate\Database\QueryException $ex){
            $msg = [
                'code' => 400,
                'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.',
                'data' => $ex,
            ];
        }catch (Exception $e) {
            $msg = [
                'code' => 400,
                'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.',
                'data' => $e,
            ];
        }
           
        return response()->json($msg, $msg['code']);
    }


    public function ambitoSiemec(){
        return view('Catalogos.ambito_siemec');
    }

    public function getAmbitosSiemec(){
        try {
            $ambitos = AmbitosSiemec::all();

            $msg = [
                'code' => 200,
                'mensaje' => 'Catalogo de ámbitos siemec.',
                'data' => $ambitos
            ];
        } catch(\Illuminate\Database\QueryException $ex){
            $msg = [
                'code' => 400,
                'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.',
                'data' => $ex,
            ];
        }catch (Exception $e) {
            $msg = [
                'code' => 400,
                'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.',
                'data' => $e,
            ];
        }
           
        return response()->json($msg, $msg['code']);
    }

    public function editAmbitoSiemec(Request $request, $id){
        try {
            $validate =  \Validator::make($request->all(), [
                    'ambito_edit' => 'required|min:2|max:50',
                ]
            );

            if($validate->fails()){
                $msg = [
                    'code' => 411,
                    'mensaje' => 'Error',
                    'errors' => $validate->errors()
                ];

                return response()->json($msg, $msg['code']);
            }

            $ambito = AmbitosSiemec::find($id);
            $ambito->descripcion = $request->input('ambito_edit');
            $ambito->save();


            $msg = [
                'code' => 200,
                'mensaje' => 'Ámbito siemec actualizada correctamente.',
            ];
        } catch(\Illuminate\Database\QueryException $ex){
            $msg = [
                'code' => 400,
                'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.',
                'data' => $ex,
            ];
        }catch (Exception $e) {
            $msg = [
                'code' => 400,
                'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.',
                'data' => $e,
            ];
        }
           
        return response()->json($msg, $msg['code']);
    }

    public function addAmbitoSiemec(Request $request){
        try {
            $validate =  \Validator::make($request->all(), [
                    'ambito' => 'required|min:2|max:50',
                ]
            );

            if($validate->fails()){
                $msg = [
                    'code' => 411,
                    'mensaje' => 'Error',
                    'errors' => $validate->errors()
                ];

                return response()->json($msg, $msg['code']);
            }

            $ambito = new AmbitosSiemec();
            $ambito->descripcion = $request->input('ambito');
            $ambito->save();


            $msg = [
                'code' => 200,
                'mensaje' => 'Ámbito SIEMEC agregado correctamente.',
            ];
        } catch(\Illuminate\Database\QueryException $ex){
            $msg = [
                'code' => 400,
                'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.',
                'data' => $ex,
            ];
        }catch (Exception $e) {
            $msg = [
                'code' => 400,
                'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.',
                'data' => $e,
            ];
        }
           
        return response()->json($msg, $msg['code']);
    }

    public function delAmbitoSiemec($id){
        try {

            $criterio = AmbitosSiemec::find($id);
            $criterio->delete();


            $msg = [
                'code' => 200,
                'mensaje' => 'Ámbito eliminada.',
            ];
        } catch(\Illuminate\Database\QueryException $ex){
            $msg = [
                'code' => 400,
                'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.',
                'data' => $ex,
            ];
        }catch (Exception $e) {
            $msg = [
                'code' => 400,
                'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.',
                'data' => $e,
            ];
        }
           
        return response()->json($msg, $msg['code']);
    }
    
    public function criterioSiemec(){
        return view('Catalogos.criterio_siemec');
    }

    public function getCriteriosSiemec(){
        try {
            $procedencias = CriteriosSiemec::all();

            $msg = [
                'code' => 200,
                'mensaje' => 'Catalogo de criterios siemec.',
                'data' => $procedencias
            ];
        } catch(\Illuminate\Database\QueryException $ex){
            $msg = [
                'code' => 400,
                'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.',
                'data' => $ex,
            ];
        }catch (Exception $e) {
            $msg = [
                'code' => 400,
                'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.',
                'data' => $e,
            ];
        }
           
        return response()->json($msg, $msg['code']);
    }

    public function editCriterioSiemec(Request $request, $id){
        try {
            $validate =  \Validator::make($request->all(), [
                    'criterio_edit' => 'required|min:2|max:50',
                ]
            );

            if($validate->fails()){
                $msg = [
                    'code' => 411,
                    'mensaje' => 'Error',
                    'errors' => $validate->errors()
                ];

                return response()->json($msg, $msg['code']);
            }

            $criterio = CriteriosSiemec::find($id);
            $criterio->descripcion = $request->input('criterio_edit');
            $criterio->save();


            $msg = [
                'code' => 200,
                'mensaje' => 'Criterio siemec actualizada correctamente.',
            ];
        } catch(\Illuminate\Database\QueryException $ex){
            $msg = [
                'code' => 400,
                'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.',
                'data' => $ex,
            ];
        }catch (Exception $e) {
            $msg = [
                'code' => 400,
                'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.',
                'data' => $e,
            ];
        }
           
        return response()->json($msg, $msg['code']);
    }

    public function addCriterioSiemec(Request $request){
        try {
            $validate =  \Validator::make($request->all(), [
                    'criterio' => 'required|min:2|max:50',
                ]
            );

            if($validate->fails()){
                $msg = [
                    'code' => 411,
                    'mensaje' => 'Error',
                    'errors' => $validate->errors()
                ];

                return response()->json($msg, $msg['code']);
            }

            $criterio = new CriteriosSiemec();
            $criterio->descripcion = $request->input('criterio');
            $criterio->save();


            $msg = [
                'code' => 200,
                'mensaje' => 'Procedencia agregada correctamente.',
            ];
        } catch(\Illuminate\Database\QueryException $ex){
            $msg = [
                'code' => 400,
                'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.',
                'data' => $ex,
            ];
        }catch (Exception $e) {
            $msg = [
                'code' => 400,
                'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.',
                'data' => $e,
            ];
        }
           
        return response()->json($msg, $msg['code']);
    }

    public function delCriterioSiemec($id){
        try {

            $criterio = CriteriosSiemec::find($id);
            $criterio->delete();


            $msg = [
                'code' => 200,
                'mensaje' => 'Procedencia eliminada.',
            ];
        } catch(\Illuminate\Database\QueryException $ex){
            $msg = [
                'code' => 400,
                'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.',
                'data' => $ex,
            ];
        }catch (Exception $e) {
            $msg = [
                'code' => 400,
                'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.',
                'data' => $e,
            ];
        }
           
        return response()->json($msg, $msg['code']);
    }
}
