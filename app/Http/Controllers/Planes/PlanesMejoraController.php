<?php

namespace App\Http\Controllers\Planes;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Mejoras\Planes;
use App\Models\Mejoras\ActividadControl;
use App\Models\Mejoras\Acciones;
use App\Models\Mejoras\ComplementosPlan;
use App\Models\Catalogos\Procedencias;
use App\Models\Catalogos\EjesPDI;
use App\Models\Catalogos\ObjetivosEspesificos;
use App\Models\Catalogos\OdsPDI;
use App\Models\Catalogos\Estrategias;
use App\Models\Catalogos\AmbitosSiemec;
use App\Models\Catalogos\CriteriosSiemec;
use App\Models\Catalogos\Metas;
use App\Models\Catalogos\Des;
use App\Models\Catalogos\UnidadesAcademicas;
use App\Models\Catalogos\Sedes;
use App\Models\Catalogos\ProgramasEducativos;
use App\Models\Catalogos\NivelesEstudio;
use App\Models\Catalogos\Modalidad;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use DB;

class PlanesMejoraController extends Controller
{
    public function getPlanes()
    {
        try {
            $u = \Auth::user();

            if ($u->rol == 1) {
                $planes = Planes::select(
                    'tipo',
                    'recomendacion_meta',
                    'mejoras.procedencia as procedencia', // ← asegúrate de traerlo
                    'mejoras.id',
                    'plan_no',
                    'complemento_plan.archivo as cerrado',
                    'fecha_vencimiento',
                    DB::raw("FORMAT(GetDate(), 'yyyy-MM-dd') AS fecha_hoy"),
                    'users.name',
                    DB::raw("coalesce(acciones.id_plan,0) as acciones")
                )
                    ->leftJoin('users', function ($join) {
                        $join->on('users.nivel', '=', 'mejoras.nivel');
                        $join->on('users.id_des', '=', 'mejoras.id_des');
                        $join->on(DB::raw("coalesce(users.id_ua,0)"), '=', DB::raw("coalesce(mejoras.id_ua,0)"));
                        $join->on(DB::raw("coalesce(users.id_sede,0)"), '=', DB::raw("coalesce(mejoras.id_sede,0)"));
                        $join->on(DB::raw("coalesce(users.id_programa,0)"), '=', DB::raw("coalesce(mejoras.id_programa_educativo,0)"));
                        $join->on(DB::raw("coalesce(users.id_nivel,0)"), '=', DB::raw("coalesce(mejoras.id_nivel_estudio,0)"));
                        $join->on(DB::raw("coalesce(users.id_modalidad,0)"), '=', DB::raw("coalesce(mejoras.id_modalidad_estudio,0)"));
                    })
                    ->leftJoin('complemento_plan', 'complemento_plan.id_plan', 'mejoras.id')
                    ->leftJoin(DB::raw("(SELECT id_plan FROM acciones GROUP BY id_plan) as acciones"), 'acciones.id_plan', 'mejoras.id')
                    ->where('activo', 1)->orderBy('orden')->get();
            } else if ($u->rol == 4) {
                $planes = Planes::select(
                    'tipo',
                    'recomendacion_meta',
                    'mejoras.procedencia as procedencia', // ← añade procedencia
                    'mejoras.id',
                    'plan_no',
                    'complemento_plan.archivo as cerrado',
                    'fecha_vencimiento',
                    DB::raw("FORMAT(GetDate(), 'yyyy-MM-dd') AS fecha_hoy")
                )
                    ->leftJoin('complemento_plan', 'complemento_plan.id_plan', 'mejoras.id') // ← corregido
                    ->where('procedencia', $u->procedencia)
                    ->where('activo', 1)
                    ->orderBy('orden')
                    ->get();
            } else {
                $where = '';
                switch ($u->nivel) {
                    case 1:
                        $where = " AND id_des = " . (int)$u->id_des;
                        break;
                    case 2:
                        $where = " AND id_ua = " . (int)$u->id_ua;
                        break;
                    case 3:
                        $where = " AND id_sede = " . (int)$u->id_sede;
                        break;
                    case 4:
                        $where = " AND id_programa_educativo = " . (int)$u->id_programa;
                        break;
                    case 5:
                        $where = " AND id_nivel_estudio = " . (int)$u->id_nivel;
                        break;
                    case 6:
                        $where = " AND id_modalidad_estudio = " . (int)$u->id_modalidad;
                        break;
                }

                $planes = Planes::select(
                    'tipo',
                    'recomendacion_meta',
                    'mejoras.procedencia as procedencia', // ← añade procedencia
                    'mejoras.id',
                    'plan_no',
                    'complemento_plan.archivo as cerrado',
                    'fecha_vencimiento',
                    DB::raw("FORMAT(GetDate(), 'yyyy-MM-dd') AS fecha_hoy")
                )
                    ->leftJoin('complemento_plan', 'complemento_plan.id_plan', 'mejoras.id') // ← corregido
                    ->where('activo', 1)
                    ->where('mejoras.nivel', $u->nivel)
                    ->whereRaw("1=1 $where")
                    ->orderBy('orden')
                    ->get();
            }

            $msg = ['code' => 200, 'mensaje' => 'Listado de planes de mejora.', 'data' => $planes, 'rol' => $u->rol];
        } catch (\Illuminate\Database\QueryException $ex) {
            $msg = ['code' => 400, 'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.', 'data' => $ex];
        } catch (\Exception $e) {
            $msg = ['code' => 400, 'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.', 'data' => $e];
        }

        return response()->json($msg, $msg['code']);
    }


    public function delMejora($id)
    {
        try {
            if (Acciones::where('id_plan', $id)->count() > 0) {
                $acciones = Acciones::where('id_plan', $id)->get();
                foreach ($acciones as $value) {
                    $elimina = Acciones::find($value->id);
                    if ($elimina->evidencia != '') {
                        Storage::disk('files')->delete($elimina->evidencia);
                    }
                    $elimina->delete();
                }
            }

            $plan = Planes::find($id);
            $plan->delete();


            $msg = [
                'code' => 200,
                'mensaje' => 'Registro eliminado de manera correcta.'
            ];
        } catch (\Illuminate\DataBase\QueryException $ex) {
            $msg = [
                'code' => 400,
                'mensaje' => 'Intente de nuevo o consulte al administrador del sistema',
                'data' => $ex
            ];
        } catch (Exception $e) {
            $msg = [
                'code' => 400,
                'mensaje' => 'Intente de nuevo o consulte al administrador del sistema',
                'data' => $e
            ];
        }

        return response()->json($msg, $msg['code']);
    }

    public function adminEdita($id)
    {
        $plan = Planes::find($id);
        $procedencias = Procedencias::orderBy('descripcion')->get();
        $des = Des::where('tipo', $plan->tipo_mejora)->orderBy('nombre')->get();
        $ejes = EjesPDI::all();
        $ambitos = AmbitosSiemec::all();
        $criterios = CriteriosSiemec::all();
        $verificadores = User::where('rol', 3)->get();
        $unidades = UnidadesAcademicas::where('id_des', $plan->id_des)->orderBy('nombre')->get();
        $sedes = Sedes::where('id_ua', $plan->id_ua)->orderBy('nombre')->get();
        $programasEducativos = ProgramasEducativos::where('id_sede', $plan->id_sede)->orderBy('nombre')->get();
        $nivelesEstudio = NivelesEstudio::where('id_programa_estudio', $plan->id_programa_educativo)->orderBy('nombre')->get();
        $modalidad = Modalidad::where('id_nivel_estudio', $plan->id_nivel_estudio)->orderBy('nombre')->get();

        $ods = OdsPDI::where('id_eje', $plan->eje_pdi)->orderBy('descripcion')->get();
        $objetivos = ObjetivosEspesificos::where('id_ods', $plan->id_ods_pdi)->orderBy('descripcion')->get();
        $estategias = Estrategias::where('id_objetivo', $plan->objetivo_pdi)->orderBy('descripcion')->get();
        $metas = Metas::where('id_estrategia', $plan->id_estrategia)->get();

        return view('Admin.edita_plan', compact('plan', 'procedencias', 'verificadores', 'des', 'ejes', 'ambitos', 'criterios', 'unidades', 'sedes', 'programasEducativos', 'nivelesEstudio', 'modalidad', 'ods', 'objetivos', 'estategias', 'metas'));
    }

    public function editPlan(Request $request)
    {
        try {
            $validate =  \Validator::make(
                $request->all(),
                [
                    'tipo_plan' => 'required',
                    'procedencia' => 'required',
                    'plan_no' => 'required',
                    'cantidad' => 'required|numeric|min:1',
                    'fecha_vencimiento' => 'required|date_format:Y-m-d|after:' . date("Y-m-d"),
                    'tipo_mejora' => 'required',
                    'des' => 'required',
                    'recomendacion_meta' => 'required|string|min:2|max:700',
                    'verifico' => 'nullable|string|min:2|max:100',
                    'eje_pdi' => 'required',
                    'ods_pdi_select' => 'required',
                    'objetivo_pdi' => 'required',
                    'estrategias' => 'required',
                    'meta_pdi' => 'required',
                    'ambito_siemec' => 'required',
                    'criterio_siemec' => 'required',
                ],
            );

            if ($validate->fails()) {
                $msg = [
                    'code' => 411,
                    'mensaje' => 'Error',
                    'errors' => $validate->errors()
                ];

                return response()->json($msg, $msg['code']);
            }

            if ($request->input('unidad_academica') === NULL) {
                $nivel = 1;
            } else if ($request->input('sede') === NULL) {
                $nivel = 2;
            } else if ($request->input('programa_educativo') === NULL) {
                $nivel = 3;
            } else if ($request->input('nivel') === NULL) {
                $nivel = 4;
            } else if ($request->input('modalidad') === NULL) {
                $nivel = 5;
            } else if ($request->input('modalidad') !== NULL) {
                $nivel = 6;
            }

            $plan = Planes::find($request->input('id_plan'));
            $plan->tipo = $request->input('tipo_plan');
            $plan->procedencia = $request->input('procedencia');
            $plan->cantidad = $request->input('cantidad');
            $plan->fecha_vencimiento = $request->input('fecha_vencimiento');
            $plan->id_des = $request->input('des');
            $plan->id_ua = $request->input('unidad_academica');
            $plan->id_sede = $request->input('sede');
            $plan->id_programa_educativo = $request->input('programa_educativo');
            $plan->id_nivel_estudio = $request->input('nivel');
            $plan->id_modalidad_estudio = $request->input('modalidad');
            $plan->verifico = $request->input('verifico');
            $plan->recomendacion_meta = $request->input('recomendacion_meta');
            $plan->eje_pdi = $request->input('eje_pdi');
            $plan->objetivo_pdi = $request->input('objetivo_pdi');
            $plan->indicador_pdi = $request->input('indicador_pdi');
            $plan->id_ods_pdi = $request->input('ods_pdi_select');
            $plan->id_estrategia = $request->input('estrategias');
            $plan->id_meta = $request->input('meta_pdi');
            $plan->ambito_siemec = $request->input('ambito_siemec');
            $plan->criterio_siemec = $request->input('criterio_siemec');
            $plan->activo = 1;
            $plan->nivel = $nivel;
            $plan->tipo_mejora = $request->input('tipo_mejora');
            $plan->save();


            $msg = [
                'code' => 200,
                'mensaje' => 'Registro actualizado correctamente.',
            ];


            $msg = [
                'code' => 200,
                'mensaje' => 'Registro actualizado correctamente.',
            ];
        } catch (\Illuminate\Database\QueryException $ex) {
            $msg = [
                'code' => 400,
                'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.',
                'data' => $ex,
            ];
        } catch (Exception $e) {
            $msg = [
                'code' => 400,
                'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.',
                'data' => $e,
            ];
        }

        return response()->json($msg, $msg['code']);
    }



    public function edita($id)
    {
        $plan = Planes::select(
            'mejoras.id',
            'mejoras.tipo',
            'mejoras.verifico',
            'mejoras.tipo_mejora',
            'mejoras.procedencia',
            'mejoras.plan_no',
            'mejoras.fecha_creacion',
            'mejoras.cantidad',
            'mejoras.orden',
            'mejoras.fecha_vencimiento',
            'mejoras.recomendacion_meta',
            'mejoras.ods_pdi',
            'mejoras.indicador_pdi',
            'cat_ejes_pdi.descripcion as eje_pdi',
            'cat_objetivos_espesifico.descripcion as objetivo_pdi',
            'cat_ambitos_siemec.descripcion as ambito_siemec',
            'cat_criterios_siemec.descripcion as criterio_siemec',
            'cat_des.nombre as des',
            'cat_unidades_academicas.nombre as unidad_academica',
            'cat_sedes.nombre as sede',
            'cat_programas_educativos_dos.nombre as programa_educativo',
            'cat_niveles_estudio.nombre as nivel',
            'cat_modalidades_estudio.nombre as modalidad',
            'cat_ods_pdi.descripcion as ods',
            'cat_estrategias.descripcion as estrategia',
            'cat_metas.descripcion as meta',
        )
            ->join('cat_ejes_pdi', 'cat_ejes_pdi.id', 'mejoras.eje_pdi')
            ->join('cat_objetivos_espesifico', 'cat_objetivos_espesifico.id', 'mejoras.objetivo_pdi')
            ->join('cat_ambitos_siemec', 'cat_ambitos_siemec.id', 'mejoras.ambito_siemec')
            ->join('cat_criterios_siemec', 'cat_criterios_siemec.id', 'mejoras.criterio_siemec')
            ->join('cat_des', 'cat_des.id', 'mejoras.id_des')
            ->join('cat_ods_pdi', 'cat_ods_pdi.id', 'mejoras.id_ods_pdi')
            ->join('cat_estrategias', 'cat_estrategias.id', 'mejoras.id_estrategia')
            ->join('cat_metas', 'cat_metas.id', 'mejoras.id_meta')
            ->leftJoin('cat_unidades_academicas', 'cat_unidades_academicas.id', 'mejoras.id_ua')
            ->leftJoin('cat_sedes', 'cat_sedes.id', 'mejoras.id_sede')
            ->leftJoin('cat_programas_educativos_dos', 'cat_programas_educativos_dos.id', 'mejoras.id_programa_educativo')
            ->leftJoin('cat_niveles_estudio', 'cat_niveles_estudio.id', 'mejoras.id_nivel_estudio')
            ->leftJoin('cat_modalidades_estudio', 'cat_modalidades_estudio.id', 'mejoras.id_modalidad_estudio')
            ->where('mejoras.id', $id)
            ->first();

        $procedencias = Procedencias::orderBy('descripcion')->get();
        $programas = ProgramasEducativos::all();
        $complemento = ComplementosPlan::where('id_plan', $id)->first();

        return view('Unidad.edita_plan', compact('plan', 'procedencias', 'programas', 'complemento'));
    }

    public function getAcciones($id)
    {
        try {
            $acciones = Acciones::where('id_plan', $id)->orderBy('id')->get();

            $msg = [
                'code' => 200,
                'mensaje' => 'Listado de acciones.',
                'data' => $acciones
            ];
        } catch (\Illuminate\Database\QueryException $ex) {
            $msg = [
                'code' => 400,
                'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.',
                'data' => $ex,
            ];
        } catch (Exception $e) {
            $msg = [
                'code' => 400,
                'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.',
                'data' => $e,
            ];
        }

        return response()->json($msg, $msg['code']);
    }

    public function saveAccion(Request $request)
    {
        try {
            $mejora = Planes::find($request->input('id_plan'));

            $bandera = ComplementosPlan::where('id_plan', $request->input('id_plan'))->first();

            if (empty($bandera)) {
                $msg = [
                    'code' => 400,
                    'mensaje' => 'Para poder subir una acción primero debe de guardar su indicador clave. ',
                ];

                return response()->json($msg, $msg['code']);
            } elseif (!empty($bandera) && $bandera->indicador_clave == '') {
                $msg = [
                    'code' => 400,
                    'mensaje' => 'Para poder subir una acción primero debe de guardar su indicador clave. ',
                ];

                return response()->json($msg, $msg['code']);
            }

            $validate =  \Validator::make(
                $request->all(),
                [
                    'accion' => 'required|string|min:2|max:500',
                    'producto_resultado' => 'required|string|min:2|max:500',
                    'fecha_inicio' => 'required|date_format:Y-m-d|after_or_equal:' . $mejora->fecha_creacion . '|before_or_equal:' . $mejora->fecha_vencimiento,
                    'fecha_fin' => 'required|date_format:Y-m-d|after_or_equal:fecha_inicio|before_or_equal:' . $mejora->fecha_vencimiento,
                    'evidencia' => 'nullable|mimes:pdf|max:6144',
                    'responsable' => 'required|string|min:2|max:200',
                ],
            );

            if ($validate->fails()) {
                $msg = [
                    'code' => 411,
                    'mensaje' => 'Error',
                    'errors' => $validate->errors()
                ];

                return response()->json($msg, $msg['code']);
            }

            $accion = new Acciones();

            if ($request->file('evidencia')) {
                $evidencia = 'acciones/' . time() . "_archivo_" . $request->input('id_plan') . "_file." . $request->evidencia->getClientOriginalExtension();
                $path = Storage::disk('public')->put($evidencia, \File::get($request->evidencia));

                $accion->evidencia = $evidencia;
            }

            $accion->id_plan = $request->input('id_plan');
            $accion->id_usuario = \Auth::user()->id;
            $accion->unidad_academica = \Auth::user()->usuario;
            $accion->accion = $request->input('accion');
            $accion->responsable = $request->input('responsable');
            $accion->producto_resultado = $request->input('producto_resultado');
            $accion->fecha_inicio = $request->input('fecha_inicio');
            $accion->fecha_fin = $request->input('fecha_fin');
            $accion->save();


            $msg = [
                'code' => 200,
                'mensaje' => 'Acción agregada correctamente.',
            ];
        } catch (\Illuminate\Database\QueryException $ex) {
            $msg = [
                'code' => 400,
                'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.',
                'data' => $ex,
            ];
        } catch (Exception $e) {
            $msg = [
                'code' => 400,
                'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.',
                'data' => $e,
            ];
        }

        return response()->json($msg, $msg['code']);
    }


    public function delAccion($id)
    {
        try {
            $acciones = Acciones::find($id);
            if ($acciones->evidencia != '' && $acciones->evidencia !== null) {
                Storage::disk('public')->delete($acciones->evidencia);
            }
            $acciones->delete();

            $msg = [
                'code' => 200,
                'mensaje' => 'Registro eliminado correctamente.',
                'data' => $acciones
            ];
        } catch (\Illuminate\Database\QueryException $ex) {
            $msg = [
                'code' => 400,
                'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.',
                'data' => $ex,
            ];
        } catch (Exception $e) {
            $msg = [
                'code' => 400,
                'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.',
                'data' => $e,
            ];
        }

        return response()->json($msg, $msg['code']);
    }

    public function detalleAccion($id)
    {
        try {
            $accion = Acciones::find($id);

            $msg = [
                'code' => 200,
                'mensaje' => 'Detalle de la acción.',
                'data' => $accion
            ];
        } catch (\Illuminate\Database\QueryException $ex) {
            $msg = [
                'code' => 400,
                'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.',
                'data' => $ex,
            ];
        } catch (Exception $e) {
            $msg = [
                'code' => 400,
                'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.',
                'data' => $e,
            ];
        }

        return response()->json($msg, $msg['code']);
    }

    public function editAccion(Request $request)
    {
        try {
            $mejora = Planes::find($request->input('id_plan'));

            $validate =  \Validator::make(
                $request->all(),
                [
                    'accion_edit' => 'required|string|min:2|max:500',
                    'producto_resultado_edit' => 'required|string|min:2|max:500',
                    'fecha_inicio_edit' => 'required|date_format:Y-m-d|after_or_equal:' . $mejora->fecha_creacion . '|before_or_equal:' . $mejora->fecha_vencimiento,
                    'fecha_fin_edit' => 'required|date_format:Y-m-d|after_or_equal:fecha_inicio|before_or_equal:' . $mejora->fecha_vencimiento,
                    'evidencia_edit' => 'nullable|mimes:pdf|max:6144',
                    'responsable_edit' => 'required|string|min:2|max:200',
                ],
            );

            if ($validate->fails()) {
                $msg = [
                    'code' => 411,
                    'mensaje' => 'Error',
                    'errors' => $validate->errors()
                ];

                return response()->json($msg, $msg['code']);
            }

            $accion = Acciones::find($request->input('id_accion'));

            if ($request->file('evidencia_edit')) {

                if ($accion->evidencia != '' && $accion->evidencia !== null) {
                    Storage::disk('public')->delete($accion->evidencia);
                }

                $evidencia = 'acciones/' . time() . "_archivo_" . $request->input('id_plan') . "_file." . $request->evidencia_edit->getClientOriginalExtension();
                $path = Storage::disk('public')->put($evidencia, \File::get($request->evidencia_edit));

                $accion->evidencia = $evidencia;
            }

            $accion->accion = $request->input('accion_edit');
            $accion->producto_resultado = $request->input('producto_resultado_edit');
            $accion->fecha_inicio = $request->input('fecha_inicio_edit');
            $accion->fecha_fin = $request->input('fecha_fin_edit');
            $accion->responsable = $request->input('responsable_edit');
            $accion->save();


            $msg = [
                'code' => 200,
                'mensaje' => 'Acción actualizada correctamente.',
            ];
        } catch (\Illuminate\Database\QueryException $ex) {
            $msg = [
                'code' => 400,
                'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.',
                'data' => $ex,
            ];
        } catch (Exception $e) {
            $msg = [
                'code' => 400,
                'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.',
                'data' => $e,
            ];
        }

        return response()->json($msg, $msg['code']);
    }

    public function getActividadesControl($idPlan)
    {
        try {
            $rows = ActividadControl::where('id_plan', $idPlan)->orderBy('id')->get();
            return response()->json(['code' => 200, 'mensaje' => 'Listado de actividades.', 'data' => $rows], 200);
        } catch (\Throwable $e) {
            return response()->json(['code' => 400, 'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.'], 400);
        }
    }

    public function saveActividadControl(Request $request)
    {
        try {
            $mejora = Planes::findOrFail($request->id_plan);
            $request->validate([
                'id_plan'            => 'required|integer|exists:mejoras,id',
                'actividad'          => 'required|string|min:2|max:500',
                'producto_resultado' => 'required|string|min:2|max:500',
                'fecha_inicio'       => 'required|date|after_or_equal:' . $mejora->fecha_creacion . '|before_or_equal:' . $mejora->fecha_vencimiento,
                'fecha_fin'          => 'required|date|after_or_equal:fecha_inicio|before_or_equal:' . $mejora->fecha_vencimiento,
                'responsable'        => 'required|string|min:2|max:200',
            ]);

            ActividadControl::create([
                'id_plan' => $request->id_plan,
                'actividad' => $request->actividad,
                'producto_resultado' => $request->producto_resultado,
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin' => $request->fecha_fin,
                'responsable' => $request->responsable,
                'id_usuario' => \Auth::id(),
            ]);

            return response()->json(['code' => 200, 'mensaje' => 'Actividad agregada correctamente.'], 200);
        } catch (\Illuminate\Validation\ValidationException $ve) {
            return response()->json(['code' => 411, 'mensaje' => 'Error', 'errors' => $ve->errors()], 411);
        } catch (\Throwable $e) {
            return response()->json(['code' => 400, 'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.'], 400);
        }
    }

    public function detalleActividadControl($id)
    {
        try {
            $row = ActividadControl::findOrFail($id);
            return response()->json(['code' => 200, 'mensaje' => 'Detalle.', 'data' => $row], 200);
        } catch (\Throwable $e) {
            return response()->json(['code' => 400, 'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.'], 400);
        }
    }

    public function editActividadControl(Request $request)
    {
        try {
            $mejora = Planes::findOrFail($request->id_plan);
            $request->validate([
                'id'                 => 'required|integer|exists:actividades_control,id',
                'id_plan'            => 'required|integer|exists:mejoras,id',
                'actividad'          => 'required|string|min:2|max:500',
                'producto_resultado' => 'required|string|min:2|max:500',
                'fecha_inicio'       => 'required|date|after_or_equal:' . $mejora->fecha_creacion . '|before_or_equal:' . $mejora->fecha_vencimiento,
                'fecha_fin'          => 'required|date|after_or_equal:fecha_inicio|before_or_equal:' . $mejora->fecha_vencimiento,
                'responsable'        => 'required|string|min:2|max:200',
            ]);

            $row = ActividadControl::findOrFail($request->id);
            $row->update($request->only(['actividad', 'producto_resultado', 'fecha_inicio', 'fecha_fin', 'responsable']));

            return response()->json(['code' => 200, 'mensaje' => 'Actividad actualizada correctamente.'], 200);
        } catch (\Illuminate\Validation\ValidationException $ve) {
            return response()->json(['code' => 411, 'mensaje' => 'Error', 'errors' => $ve->errors()], 411);
        } catch (\Throwable $e) {
            return response()->json(['code' => 400, 'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.'], 400);
        }
    }

    public function delActividadControl($id)
    {
        try {
            ActividadControl::findOrFail($id)->delete();
            return response()->json(['code' => 200, 'mensaje' => 'Registro eliminado correctamente.'], 200);
        } catch (\Throwable $e) {
            return response()->json(['code' => 400, 'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.'], 400);
        }
    }

    public function getinfoPrograma($id)
    {
        try {
            $programa = ProgramasEducativos::find($id);

            $msg = [
                'code' => 200,
                'mensaje' => 'Detalle del programa educativo.',
                'data' => $programa
            ];
        } catch (\Illuminate\Database\QueryException $ex) {
            $msg = [
                'code' => 400,
                'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.',
                'data' => $ex,
            ];
        } catch (Exception $e) {
            $msg = [
                'code' => 400,
                'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.',
                'data' => $e,
            ];
        }

        return response()->json($msg, $msg['code']);
    }


    public function saveComplemento(Request $request)
    {
        try {
            $bandera = ComplementosPlan::where('id_plan', $request->input('id_plan'))->first();

            // si ya hay archivo, el nuevo es opcional; si no hay, es requerido
            $fileRule = ($bandera && $bandera->archivo) ? 'nullable' : 'required';



            $validate = \Validator::make($request->all(), [
                'logros' => 'required|string|min:2|max:150',
                'impactos' => 'required|string|min:2|max:150',
                'evidencia'              => $fileRule . '|file|mimes:pdf|max:6144',
                'control_observaciones' => 'nullable|string|min:2|max:600', // <-- antes required
                'observaciones' => 'nullable|string|min:2|max:600',
            ]);

            if ($validate->fails()) {
                return response()->json([
                    'code'    => 411,
                    'mensaje' => 'Error',
                    'errors'  => $validate->errors()
                ], 411);
            }

            if (!$bandera) {
                $comple = new ComplementosPlan();
                $comple->id_plan    = (int) $request->input('id_plan');
                $comple->id_usuario = \Auth::id();
                // Usa el ID real de UA si existe; si no, déjalo null
                $comple->id_ua      = optional(\Auth::user())->id_ua;
            } else {
                $comple = ComplementosPlan::find($bandera->id);
            }

            // Guardar/actualizar archivo si viene en la petición
            if ($request->hasFile('evidencia')) {
                $file = $request->file('evidencia');

                // Si había un archivo previo, elimínalo
                if (!empty($comple->archivo)) {
                    Storage::disk('public')->delete($comple->archivo);
                }

                $filename = 'evidencias/' . time() . "_archivo_{$request->input('id_plan')}_file." . $file->getClientOriginalExtension();
                Storage::disk('public')->put($filename, file_get_contents($file->getRealPath()));
                $comple->archivo = $filename;
            }

            // Si tu tabla tiene la columna programa_educativo NO NULL y el form no lo manda,
            // toma el valor desde el plan (ajusta el nombre si difiere)
            if (is_null($comple->programa_educativo)) {
                $plan = Planes::find($request->input('id_plan'));
                if ($plan) {
                    $comple->programa_educativo = $plan->id_programa_educativo ?? null;
                }
            }

            $comple->logros                = $request->input('logros');
            $comple->impactos              = $request->input('impactos');
            $comple->control_observaciones = $request->input('control_observaciones');
            // Guarda null si viene vacío
            $comple->observaciones         = $request->filled('observaciones') ? $request->input('observaciones') : null;

            $comple->save();

            return response()->json([
                'code'    => 200,
                'mensaje' => 'Complemento guardado correctamente.',
                'archivo' => $comple->archivo
            ], 200);
        } catch (\Throwable $e) {
            \Log::error('saveComplemento error', ['msg' => $e->getMessage()]);
            // Manda el mensaje para depurar (puedes dejarlo genérico en producción)
            return response()->json(['code' => 400, 'mensaje' => $e->getMessage()], 400);
        }
    }

    public function uploadComplementEvidence(Request $request)
    {
        try {
            $request->validate([
                'id_plan'   => 'required|integer|exists:mejoras,id',
                'evidencia' => 'required|mimes:pdf|max:6144',
            ]);

            $bandera = ComplementosPlan::where('id_plan', $request->id_plan)->first();
            if (!$bandera) {
                $bandera = new ComplementosPlan();
                $bandera->id_plan   = $request->id_plan;
                $bandera->id_usuario = \Auth::id();
                $bandera->id_ua      = \Auth::user()->usuario;
            } else if ($bandera->archivo) {
                Storage::disk('public')->delete($bandera->archivo);
            }

            $archivo = 'evidencias/' . time() . "_archivo_{$request->id_plan}_file." . $request->evidencia->getClientOriginalExtension();
            Storage::disk('public')->put($archivo, \File::get($request->evidencia));
            $bandera->archivo = $archivo;
            $bandera->save();

            return response()->json(['code' => 200, 'mensaje' => 'Evidencia subida correctamente.', 'archivo' => $archivo], 200);
        } catch (\Illuminate\Validation\ValidationException $ve) {
            return response()->json(['code' => 411, 'mensaje' => 'Error', 'errors' => $ve->errors()], 411);
        } catch (\Throwable $e) {
            return response()->json(['code' => 400, 'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.'], 400);
        }
    }

    public function delArchivoComplemento($idPlan)
    {
        try {
            $comple = ComplementosPlan::where('id_plan', $idPlan)->firstOrFail();
            if ($comple->archivo) {
                Storage::disk('public')->delete($comple->archivo);
                $comple->archivo = null;
                $comple->save();
            }
            return response()->json(['code' => 200, 'mensaje' => 'Archivo eliminado de manera correcta.'], 200);
        } catch (\Throwable $e) {
            return response()->json(['code' => 400, 'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.'], 400);
        }
    }

    public function saveIndicador(Request $request)
    {
        try {
            $validate =  \Validator::make(
                $request->all(),
                [
                    'indicador_clave' => 'required|string|min:2|max:150',
                ],
            );

            if ($validate->fails()) {
                $msg = [
                    'code' => 411,
                    'mensaje' => 'Error',
                    'errors' => $validate->errors()
                ];

                return response()->json($msg, $msg['code']);
            }

            $bandera = ComplementosPlan::where('id_plan', $request->input('id_plan'))->first();
            if (empty($bandera)) {
                $comple = new ComplementosPlan();
                $comple->id_plan = $request->input('id_plan');
                $comple->id_usuario = \Auth::user()->id;
                $comple->id_ua = \Auth::user()->usuario;
            } else {
                $comple = ComplementosPlan::find($bandera->id);
            }

            $comple->indicador_clave = $request->input('indicador_clave');
            $comple->save();




            $msg = [
                'code' => 200,
                'mensaje' => 'Indicador clave guardado correctamente.',
            ];
        } catch (\Illuminate\Database\QueryException $ex) {
            $msg = [
                'code' => 400,
                'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.',
                'data' => $ex,
            ];
        } catch (Exception $e) {
            $msg = [
                'code' => 400,
                'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.',
                'data' => $e,
            ];
        }

        return response()->json($msg, $msg['code']);
    }

    public function adminVer($id)
    {
        $plan = Planes::select(
            'mejoras.id',
            'mejoras.tipo',
            'mejoras.verifico',
            'mejoras.tipo_mejora',
            'mejoras.procedencia',
            'mejoras.plan_no',
            'mejoras.fecha_creacion',
            'mejoras.cantidad',
            'mejoras.orden',
            'mejoras.fecha_vencimiento',
            'mejoras.recomendacion_meta',
            'mejoras.ods_pdi',
            'mejoras.indicador_pdi',
            'cat_ejes_pdi.descripcion as eje_pdi',
            'cat_objetivos_espesifico.descripcion as objetivo_pdi',
            'cat_ambitos_siemec.descripcion as ambito_siemec',
            'cat_criterios_siemec.descripcion as criterio_siemec',
            'cat_des.nombre as des',
            'cat_unidades_academicas.nombre as unidad_academica',
            'cat_sedes.nombre as sede',
            'cat_programas_educativos_dos.nombre as programa_educativo',
            'cat_niveles_estudio.nombre as nivel',
            'cat_modalidades_estudio.nombre as modalidad',
            'cat_ods_pdi.descripcion as ods',
            'cat_estrategias.descripcion as estrategia',
            'cat_metas.descripcion as meta',
        )
            ->join('cat_ejes_pdi', 'cat_ejes_pdi.id', 'mejoras.eje_pdi')
            ->join('cat_objetivos_espesifico', 'cat_objetivos_espesifico.id', 'mejoras.objetivo_pdi')
            ->join('cat_ambitos_siemec', 'cat_ambitos_siemec.id', 'mejoras.ambito_siemec')
            ->join('cat_criterios_siemec', 'cat_criterios_siemec.id', 'mejoras.criterio_siemec')
            ->join('cat_des', 'cat_des.id', 'mejoras.id_des')
            ->join('cat_ods_pdi', 'cat_ods_pdi.id', 'mejoras.id_ods_pdi')
            ->join('cat_estrategias', 'cat_estrategias.id', 'mejoras.id_estrategia')
            ->join('cat_metas', 'cat_metas.id', 'mejoras.id_meta')
            ->leftJoin('cat_unidades_academicas', 'cat_unidades_academicas.id', 'mejoras.id_ua')
            ->leftJoin('cat_sedes', 'cat_sedes.id', 'mejoras.id_sede')
            ->leftJoin('cat_programas_educativos_dos', 'cat_programas_educativos_dos.id', 'mejoras.id_programa_educativo')
            ->leftJoin('cat_niveles_estudio', 'cat_niveles_estudio.id', 'mejoras.id_nivel_estudio')
            ->leftJoin('cat_modalidades_estudio', 'cat_modalidades_estudio.id', 'mejoras.id_modalidad_estudio')
            ->where('mejoras.id', $id)
            ->first();


        $procedencias = Procedencias::orderBy('descripcion')->get();
        $programas = ProgramasEducativos::all();
        $complemento = ComplementosPlan::where('id_plan', $id)->first();
        $verificadores = User::where('rol', 3)->get();

        return view('Admin.ver_plan', compact('plan', 'procedencias', 'programas', 'complemento', 'verificadores'));
    }

    public function viewAlta()
    {
        $procedencias = Procedencias::orderBy('descripcion')->get();
        $ejes = EjesPDI::all();
        $ambitos = AmbitosSiemec::all();
        $criterios = CriteriosSiemec::all();
        $verificadores = User::where('rol', 3)->get();

        return view('Admin.agrega_nueva_meta', compact('procedencias', 'ejes', 'ambitos', 'criterios', 'verificadores'));
    }

    public function addNuevo(Request $request)
    {
        try {
            $validate =  \Validator::make(
                $request->all(),
                [
                    'tipo_plan' => 'required',
                    'procedencia' => 'required',
                    'plan_no' => 'required',
                    'cantidad' => 'required|numeric|min:1',
                    'fecha_vencimiento' => 'required|date_format:Y-m-d|after:' . date("Y-m-d"),
                    'tipo_mejora' => 'required',
                    'des' => 'required',
                    'recomendacion_meta' => 'required|string|min:2|max:4000',
                    'verifico' => 'required',
                    'eje_pdi' => 'required',
                    'ods_pdi_select' => 'required',
                    'objetivo_pdi' => 'required',
                    'estrategias' => 'required',
                    'meta_pdi' => 'required',
                    'indicador_pdi' => 'nullable|min:2|max:1000',
                    'ambito_siemec' => 'required',
                    'criterio_siemec' => 'required',
                ],
            );

            if ($validate->fails()) {
                $msg = [
                    'code' => 411,
                    'mensaje' => 'Error',
                    'errors' => $validate->errors()
                ];

                return response()->json($msg, $msg['code']);
            }


            if ($request->input('unidad_academica') == '') {
                $nivel = 1;
            } else if ($request->input('sede') == "") {
                $nivel = 2;
            } else if ($request->input('programa_educativo') == "") {
                $nivel = 3;
            } else if ($request->input('nivel') == "") {
                $nivel = 4;
            } else if ($request->input('modalidad') == "") {
                $nivel = 5;
            } else if ($request->input('modalidad') != "") {
                $nivel = 6;
            }

            $orden = Planes::where('procedencia', $request->input('procedencia'))->count();
            $orden = $orden + 1;

            $plan = new Planes();
            $plan->tipo = $request->input('tipo_plan');
            $plan->procedencia = $request->input('procedencia');
            $plan->orden = $orden;
            $plan->plan_no = $request->input('plan_no') . $orden;
            $plan->cantidad = $request->input('cantidad');
            $plan->fecha_vencimiento = $request->input('fecha_vencimiento');
            $plan->id_des = $request->input('des');
            $plan->id_ua = $request->input('unidad_academica');
            $plan->id_sede = $request->input('sede');
            $plan->id_programa_educativo = $request->input('programa_educativo');
            $plan->id_nivel_estudio = $request->input('nivel');
            $plan->id_modalidad_estudio = $request->input('modalidad');
            $plan->verifico = $request->input('verifico');
            $plan->recomendacion_meta = $request->input('recomendacion_meta');
            $plan->ods_pdi = $request->input('ods_pdi');
            $plan->eje_pdi = $request->input('eje_pdi');
            $plan->objetivo_pdi = $request->input('objetivo_pdi');
            $plan->indicador_pdi = $request->input('indicador_pdi');
            $plan->id_ods_pdi = $request->input('ods_pdi_select');
            $plan->id_estrategia = $request->input('estrategias');
            $plan->id_meta = $request->input('meta_pdi');
            $plan->ambito_siemec = $request->input('ambito_siemec');
            $plan->criterio_siemec = $request->input('criterio_siemec');
            $plan->activo = 1;
            $plan->nivel = $nivel;
            $plan->fecha_creacion = date('Y-m-d');
            $plan->tipo_mejora = $request->input('tipo_mejora');
            $plan->save();


            $msg = [
                'code' => 200,
                'mensaje' => 'Registro actualizado correctamente.',
            ];
        } catch (\Illuminate\Database\QueryException $ex) {
            $msg = [
                'code' => 400,
                'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.',
                'data' => $ex,
            ];
        } catch (Exception $e) {
            $msg = [
                'code' => 400,
                'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.',
                'data' => $e,
            ];
        }

        return response()->json($msg, $msg['code']);
    }

    public function getOdsPdi($id)
    {
        try {
            $ods = OdsPDI::where('id_eje', $id)->get();

            $msg = [
                'code' => 200,
                'mensaje' => 'ODS PDI.',
                'data' => $ods
            ];
        } catch (\Illuminate\Database\QueryException $ex) {
            $msg = [
                'code' => 400,
                'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.',
                'data' => $ex,
            ];
        } catch (Exception $e) {
            $msg = [
                'code' => 400,
                'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.',
                'data' => $e,
            ];
        }

        return response()->json($msg, $msg['code']);
    }

    public function getObjetivosPdi($id)
    {
        try {
            $programa = ObjetivosEspesificos::where('id_ods', $id)->get();

            $msg = [
                'code' => 200,
                'mensaje' => 'Objetivos espesificos.',
                'data' => $programa
            ];
        } catch (\Illuminate\Database\QueryException $ex) {
            $msg = [
                'code' => 400,
                'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.',
                'data' => $ex,
            ];
        } catch (Exception $e) {
            $msg = [
                'code' => 400,
                'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.',
                'data' => $e,
            ];
        }

        return response()->json($msg, $msg['code']);
    }

    public function getEstrategiasPdi($id)
    {
        try {
            $programa = Estrategias::where('id_objetivo', $id)->get();

            $msg = [
                'code' => 200,
                'mensaje' => 'Estrategias.',
                'data' => $programa
            ];
        } catch (\Illuminate\Database\QueryException $ex) {
            $msg = [
                'code' => 400,
                'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.',
                'data' => $ex,
            ];
        } catch (Exception $e) {
            $msg = [
                'code' => 400,
                'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.',
                'data' => $e,
            ];
        }

        return response()->json($msg, $msg['code']);
    }

    public function getMetasPdi($id)
    {
        try {
            $programa = Metas::where('id_estrategia', $id)->get();

            $msg = [
                'code' => 200,
                'mensaje' => 'Metas.',
                'data' => $programa
            ];
        } catch (\Illuminate\Database\QueryException $ex) {
            $msg = [
                'code' => 400,
                'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.',
                'data' => $ex,
            ];
        } catch (Exception $e) {
            $msg = [
                'code' => 400,
                'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.',
                'data' => $e,
            ];
        }

        return response()->json($msg, $msg['code']);
    }

    public function getDesDep($valor)
    {
        try {
            $datos = Des::where('tipo', $valor)->orderBy('nombre')->get();

            $msg = [
                'code' => 200,
                'mensaje' => 'Des.',
                'data' => $datos
            ];
        } catch (\Illuminate\Database\QueryException $ex) {
            $msg = [
                'code' => 400,
                'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.',
                'data' => $ex,
            ];
        } catch (Exception $e) {
            $msg = [
                'code' => 400,
                'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.',
                'data' => $e,
            ];
        }

        return response()->json($msg, $msg['code']);
    }

    public function getUnidades($id)
    {
        try {
            $datos = UnidadesAcademicas::where('id_des', $id)->get();

            $msg = [
                'code' => 200,
                'mensaje' => 'Unidades académicas.',
                'data' => $datos
            ];
        } catch (\Illuminate\Database\QueryException $ex) {
            $msg = [
                'code' => 400,
                'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.',
                'data' => $ex,
            ];
        } catch (Exception $e) {
            $msg = [
                'code' => 400,
                'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.',
                'data' => $e,
            ];
        }

        return response()->json($msg, $msg['code']);
    }

    public function getSedes($id)
    {
        try {
            $datos = Sedes::where('id_ua', $id)->get();

            $msg = [
                'code' => 200,
                'mensaje' => 'Sedes.',
                'data' => $datos
            ];
        } catch (\Illuminate\Database\QueryException $ex) {
            $msg = [
                'code' => 400,
                'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.',
                'data' => $ex,
            ];
        } catch (Exception $e) {
            $msg = [
                'code' => 400,
                'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.',
                'data' => $e,
            ];
        }

        return response()->json($msg, $msg['code']);
    }

    public function getProgramas($id)
    {
        try {
            $datos = ProgramasEducativos::where('id_sede', $id)->get();

            $msg = [
                'code' => 200,
                'mensaje' => 'Programas educativos.',
                'data' => $datos
            ];
        } catch (\Illuminate\Database\QueryException $ex) {
            $msg = [
                'code' => 400,
                'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.',
                'data' => $ex,
            ];
        } catch (Exception $e) {
            $msg = [
                'code' => 400,
                'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.',
                'data' => $e,
            ];
        }

        return response()->json($msg, $msg['code']);
    }

    public function getNiveles($id)
    {
        try {
            $datos = NivelesEstudio::where('id_programa_estudio', $id)->get();

            $msg = [
                'code' => 200,
                'mensaje' => 'Nivel.',
                'data' => $datos
            ];
        } catch (\Illuminate\Database\QueryException $ex) {
            $msg = [
                'code' => 400,
                'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.',
                'data' => $ex,
            ];
        } catch (Exception $e) {
            $msg = [
                'code' => 400,
                'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.',
                'data' => $e,
            ];
        }

        return response()->json($msg, $msg['code']);
    }

    public function getModalidades($id)
    {
        try {
            $datos = Modalidad::where('id_nivel_estudio', $id)->get();

            $msg = [
                'code' => 200,
                'mensaje' => 'Modalidades.',
                'data' => $datos
            ];
        } catch (\Illuminate\Database\QueryException $ex) {
            $msg = [
                'code' => 400,
                'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.',
                'data' => $ex,
            ];
        } catch (Exception $e) {
            $msg = [
                'code' => 400,
                'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.',
                'data' => $e,
            ];
        }

        return response()->json($msg, $msg['code']);
    }

    public function delArcivo($id)
    {
        try {
            $elimina = Acciones::find($id);
            Storage::disk('public')->delete($elimina->evidencia);
            $elimina->evidencia = null;
            $elimina->save();

            $msg = [
                'code' => 200,
                'mensaje' => 'Archivo eliminado de manera correcta.'
            ];
        } catch (\Illuminate\DataBase\QueryException $ex) {
            $msg = [
                'code' => 400,
                'mensaje' => 'Intente de nuevo o consulte al administrador del sistema',
                'data' => $ex
            ];
        } catch (Exception $e) {
            $msg = [
                'code' => 400,
                'mensaje' => 'Intente de nuevo o consulte al administrador del sistema',
                'data' => $e
            ];
        }

        return response()->json($msg, $msg['code']);
    }
}
