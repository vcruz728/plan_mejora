<?php

namespace App\Http\Controllers\Planes;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Mejora;
use App\Models\ActividadControl;
use App\Models\Accion;
use App\Models\ComplementoPlan;
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
        $hoySql = "CONVERT(varchar(10), GETDATE(), 23)"; // YYYY-MM-DD
        try {
            $u = \Auth::user();

            $base = \App\Models\Mejora::query()
                // match del responsable por ámbito
                ->leftJoin('users as u_scope', function ($join) {
                    $join->on('u_scope.nivel', '=', 'mejoras.nivel');
                    $join->on('u_scope.id_des', '=', 'mejoras.id_des');
                    $join->on(DB::raw('COALESCE(u_scope.id_ua,0)'), '=', DB::raw('COALESCE(mejoras.id_ua,0)'));
                    $join->on(DB::raw('COALESCE(u_scope.id_sede,0)'), '=', DB::raw('COALESCE(mejoras.id_sede,0)'));
                    $join->on(DB::raw('COALESCE(u_scope.id_programa,0)'), '=', DB::raw('COALESCE(mejoras.id_programa_educativo,0)'));
                    $join->on(DB::raw('COALESCE(u_scope.id_nivel,0)'), '=', DB::raw('COALESCE(mejoras.id_nivel_estudio,0)'));
                    $join->on(DB::raw('COALESCE(u_scope.id_modalidad,0)'), '=', DB::raw('COALESCE(mejoras.id_modalidad_estudio,0)'));
                })
                // (opcional) quién “verificó”, por si lo necesitas en otras vistas
                ->leftJoin('users as u_veri', function ($join) {
                    $join->on('u_veri.id', '=', DB::raw('TRY_CONVERT(int, mejoras.verifico)'));
                })
                ->leftJoin('complemento_plan as cp', 'cp.id_plan', '=', 'mejoras.id')
                ->select([
                    'mejoras.id',
                    'mejoras.tipo',
                    'mejoras.recomendacion_meta',
                    'mejoras.procedencia',
                    'mejoras.plan_no',
                    'mejoras.fecha_vencimiento',
                    DB::raw("$hoySql AS fecha_hoy"),
                    DB::raw('cp.archivo AS cerrado'),
                    // Responsable SOLO desde el match del ámbito
                    DB::raw('u_scope.name AS responsable'),
                ])
                // mantiene el alias "acciones" como antes
                ->withCount(['acciones as acciones'])
                ->where('mejoras.activo', 1);


            // filtros por rol/ámbito (mismo comportamiento de antes)
            if ((int) $u->rol === 1) {
                $base->orderBy('mejoras.orden');
            } elseif ((int) $u->rol === 4) {
                $base->where('mejoras.procedencia', $u->procedencia)
                    ->orderBy('mejoras.orden')
                    ->distinct();
            } else {
                $base->where('mejoras.nivel', (int) $u->nivel);
                switch ((int) $u->nivel) {
                    case 1:
                        $base->where('mejoras.id_des', (int) $u->id_des);
                        break;
                    case 2:
                        $base->where('mejoras.id_ua', (int) $u->id_ua);
                        break;
                    case 3:
                        $base->where('mejoras.id_sede', (int) $u->id_sede);
                        break;
                    case 4:
                        $base->where('mejoras.id_programa_educativo', (int) $u->id_programa);
                        break;
                    case 5:
                        $base->where('mejoras.id_nivel_estudio', (int) $u->id_nivel);
                        break;
                    case 6:
                        $base->where('mejoras.id_modalidad_estudio', (int) $u->id_modalidad);
                        break;
                }
                $base->orderBy('mejoras.orden');
            }

            $planes = $base->get();

            return response()->json([
                'code'    => 200,
                'mensaje' => 'Listado de planes de mejora.',
                'data'    => $planes,
                'rol'     => $u->rol,
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'code'    => 400,
                'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.',
                'data'    => $e->getMessage(),
            ], 400);
        }
    }

    public function delMejora($id)
    {
        DB::transaction(function () use ($id) {
            // Acciones
            Accion::where('id_plan', $id)->each(function ($a) {
                if ($a->evidencia) Storage::disk('public')->delete($a->evidencia);
                $a->delete();
            });

            // Actividades
            ActividadControl::where('id_plan', $id)->delete();

            // Complemento (con archivo)
            if ($c = ComplementoPlan::where('id_plan', $id)->first()) {
                if ($c->archivo) Storage::disk('public')->delete($c->archivo);
                $c->delete();
            }

            Mejora::findOrFail($id)->delete();
        });

        return response()->json(['code' => 200, 'mensaje' => 'Registro eliminado de manera correcta.']);
    }


    // 2) ADMIN: editar (carga el plan con relaciones + catálogos dependientes)
    public function adminEdita($id)
    {
        $plan = Mejora::with([
            'des:id,nombre',
            'unidadAcademica:id,nombre',
            'sede:id,nombre',
            'programaEducativo:id,nombre',
            'nivelEstudio:id,nombre',
            'modalidad:id,nombre',
            'odsPdi:id,descripcion',
            'estrategia:id,descripcion',
            'meta:id,descripcion',
            'complemento:id,id_plan,archivo,indicador_clave,logros,impactos,observaciones',
        ])->findOrFail($id);

        $procedencias         = Procedencias::orderBy('descripcion')->get();
        $des                  = Des::where('tipo', $plan->tipo_mejora)->orderBy('nombre')->get();
        $ejes                 = EjesPDI::all();
        $ambitos              = AmbitosSiemec::all();
        $criterios            = CriteriosSiemec::all();
        $verificadores        = User::where('rol', 3)->get();

        $unidades             = UnidadesAcademicas::where('id_des', $plan->id_des)->orderBy('nombre')->get();
        $sedes                = Sedes::where('id_ua', $plan->id_ua)->orderBy('nombre')->get();
        $programasEducativos  = ProgramasEducativos::where('id_sede', $plan->id_sede)->orderBy('nombre')->get();
        $nivelesEstudio       = NivelesEstudio::where('id_programa_estudio', $plan->id_programa_educativo)->orderBy('nombre')->get();
        $modalidad            = Modalidad::where('id_nivel_estudio', $plan->id_nivel_estudio)->orderBy('nombre')->get();

        $ods                  = OdsPDI::where('id_eje', $plan->eje_pdi)->orderBy('descripcion')->get();
        $objetivos            = ObjetivosEspesificos::where('id_ods', $plan->id_ods_pdi)->orderBy('descripcion')->get();
        $estategias           = Estrategias::where('id_objetivo', $plan->objetivo_pdi)->orderBy('descripcion')->get(); // (mantengo el nombre $estategias)
        $metas                = Metas::where('id_estrategia', $plan->id_estrategia)->get();

        return view('Admin.edita_plan', compact(
            'plan',
            'procedencias',
            'verificadores',
            'des',
            'ejes',
            'ambitos',
            'criterios',
            'unidades',
            'sedes',
            'programasEducativos',
            'nivelesEstudio',
            'modalidad',
            'ods',
            'objetivos',
            'estategias',
            'metas'
        ));
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

            $plan = Mejora::find($request->input('id_plan'));
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
        } catch (Exception $e) {
            $msg = [
                'code' => 400,
                'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.',
                'data' => $e,
            ];
        }

        return response()->json($msg, $msg['code']);
    }



    // 2) UNIDAD: editar (detalle) — simplificado con relaciones
    public function edita($id)
    {
        $plan = Mejora::with([
            'des:id,nombre',
            'unidadAcademica:id,nombre',
            'sede:id,nombre',
            'programaEducativo:id,nombre',
            'nivelEstudio:id,nombre',
            'modalidad:id,nombre',
            'odsPdi:id,descripcion',
            'estrategia:id,descripcion',
            'meta:id,descripcion',
            'complemento:id,id_plan,archivo,indicador_clave,logros,impactos,observaciones',
        ])->findOrFail($id);

        $procedencias = Procedencias::orderBy('descripcion')->get();
        $programas    = ProgramasEducativos::all();
        $complemento  = $plan->complemento; // ya viene por relación

        return view('Unidad.edita_plan', compact('plan', 'procedencias', 'programas', 'complemento'));
    }

    public function getAcciones($id)
    {
        try {
            $acciones = Accion::where('id_plan', $id)->orderBy('id')->get();

            $msg = [
                'code' => 200,
                'mensaje' => 'Listado de acciones.',
                'data' => $acciones
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
            $mejora = Mejora::find($request->input('id_plan'));

            $bandera = ComplementoPlan::where('id_plan', $request->input('id_plan'))->first();

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

            $accion = new Accion();

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
            $acciones = Accion::find($id);
            if ($acciones->evidencia != '' && $acciones->evidencia !== null) {
                Storage::disk('public')->delete($acciones->evidencia);
            }
            $acciones->delete();

            $msg = [
                'code' => 200,
                'mensaje' => 'Registro eliminado correctamente.',
                'data' => $acciones
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
            $accion = Accion::find($id);

            $msg = [
                'code' => 200,
                'mensaje' => 'Detalle de la acción.',
                'data' => $accion
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
            $mejora = Mejora::find($request->input('id_plan'));

            $validate =  \Validator::make(
                $request->all(),
                [
                    'accion_edit' => 'required|string|min:2|max:500',
                    'producto_resultado_edit' => 'required|string|min:2|max:500',
                    'fecha_inicio_edit' => 'required|date_format:Y-m-d|after_or_equal:' . $mejora->fecha_creacion . '|before_or_equal:' . $mejora->fecha_vencimiento,
                    'fecha_fin_edit' => 'required|date|after_or_equal:fecha_inicio_edit|before_or_equal:' . $mejora->fecha_vencimiento,
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

            $accion = Accion::find($request->input('id_accion'));

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
        } catch (Exception $e) {
            $msg = [
                'code' => 400,
                'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.',
                'data' => $e,
            ];
        }

        return response()->json($msg, $msg['code']);
    }

    public function getActividadesControl($id)
    {
        try {
            $actividades = ActividadControl::where('id_plan', $id)->orderBy('id')->get();

            $msg = [
                'code' => 200,
                'mensaje' => 'Listado de actividades.',
                'data' => $actividades
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


    public function saveActividadControl(Request $request)
    {
        try {
            $mejora = Mejora::findOrFail($request->id_plan);
            $request->validate([
                'id_plan'            => 'required|integer|exists:mejoras,id',
                'actividad'          => 'required|string|min:2|max:500',
                'producto_resultado' => 'required|string|min:2|max:500',
                'fecha_inicio' => 'required|date_format:Y-m-d|after_or_equal:' . $mejora->fecha_creacion . '|before_or_equal:' . $mejora->fecha_vencimiento,
                'fecha_fin'    => 'required|date_format:Y-m-d|after_or_equal:fecha_inicio|before_or_equal:' . $mejora->fecha_vencimiento,
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

    // 3) FIX: editar Actividad de Control — corrige validación y mapeo de campos
    public function editActividadControl(Request $request)
    {
        try {
            $mejora = Mejora::findOrFail($request->id_plan);

            $request->validate([
                'id'                 => 'required|integer|exists:actividades_control,id',
                'id_plan'            => 'required|integer|exists:mejoras,id',
                'actividad'          => 'required|string|min:2|max:500',
                'producto_resultado' => 'required|string|min:2|max:500',
                'fecha_inicio_edit'  => 'required|date_format:Y-m-d|after_or_equal:' . $mejora->fecha_creacion . '|before_or_equal:' . $mejora->fecha_vencimiento,
                'fecha_fin_edit'     => 'required|date_format:Y-m-d|after_or_equal:fecha_inicio_edit|before_or_equal:' . $mejora->fecha_vencimiento,
                'responsable'        => 'required|string|min:2|max:200',
                'evidencia_edit'     => 'nullable|mimes:pdf|max:6144', // por si en el futuro agregas evidencia a Actividad
            ]);

            $row = ActividadControl::findOrFail($request->id);

            // Si llegaras a manejar archivo en ActividadControl, aquí iría la lógica de reemplazo
            // (similar a Accion). Ahora mismo se omite porque tu tabla no tiene 'evidencia'.

            $row->update([
                'actividad'          => $request->actividad,
                'producto_resultado' => $request->producto_resultado,
                'fecha_inicio'       => $request->fecha_inicio_edit,  // mapeo correcto
                'fecha_fin'          => $request->fecha_fin_edit,     // mapeo correcto
                'responsable'        => $request->responsable,
            ]);

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
            $bandera = ComplementoPlan::where('id_plan', $request->input('id_plan'))->first();

            // si ya hay archivo, el nuevo es opcional; si no hay, es requerido
            $fileRule = ($bandera && $bandera->archivo) ? 'nullable' : 'required';



            $validate = \Validator::make($request->all(), [
                'logros' => 'required|string|min:2|max:150',
                'impactos' => 'required|string|min:2|max:150',
                'evidencia'              => $fileRule . '|file|mimes:pdf|max:6144',
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
                $comple = new ComplementoPlan();
                $comple->id_plan    = (int) $request->input('id_plan');
                $comple->id_usuario = \Auth::id();
                // Usa el ID real de UA si existe; si no, déjalo null
                $comple->id_ua      = optional(\Auth::user())->id_ua;
            } else {
                $comple = ComplementoPlan::find($bandera->id);
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
                $plan = Mejora::find($request->input('id_plan'));
                if ($plan) {
                    $comple->programa_educativo = $plan->id_programa_educativo ?? null;
                }
            }

            $comple->logros                = $request->input('logros');
            $comple->impactos              = $request->input('impactos');
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

            $bandera = ComplementoPlan::where('id_plan', $request->id_plan)->first();
            if (!$bandera) {
                $bandera = new ComplementoPlan();
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
            $comple = ComplementoPlan::where('id_plan', $idPlan)->firstOrFail();
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

            $bandera = ComplementoPlan::where('id_plan', $request->input('id_plan'))->first();
            if (empty($bandera)) {
                $comple = new ComplementoPlan();
                $comple->id_plan = $request->input('id_plan');
                $comple->id_usuario = \Auth::user()->id;
                $comple->id_ua = \Auth::user()->usuario;
            } else {
                $comple = ComplementoPlan::find($bandera->id);
            }

            $comple->indicador_clave = $request->input('indicador_clave');
            $comple->save();




            $msg = [
                'code' => 200,
                'mensaje' => 'Indicador clave guardado correctamente.',
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

    // 2) ADMIN: ver (solo lectura) — también por relaciones
    public function adminVer($id)
    {
        $plan = Mejora::with([
            'des:id,nombre',
            'unidadAcademica:id,nombre',
            'sede:id,nombre',
            'programaEducativo:id,nombre',
            'nivelEstudio:id,nombre',
            'modalidad:id,nombre',
            'odsPdi:id,descripcion',
            'estrategia:id,descripcion',
            'meta:id,descripcion',
            'complemento:id,id_plan,archivo,indicador_clave,logros,impactos,observaciones',
        ])->findOrFail($id);

        $procedencias  = Procedencias::orderBy('descripcion')->get();
        $programas     = ProgramasEducativos::all();
        $complemento   = $plan->complemento;
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
            $validate = \Validator::make(
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
                return response()->json([
                    'code' => 411,
                    'mensaje' => 'Error',
                    'errors' => $validate->errors()
                ], 411);
            }

            // Tu lógica de nivel tal cual
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
            } else { // ($request->input('modalidad') != "")
                $nivel = 6;
            }

            // === AQUÍ VA LA TRANSACCIÓN ===
            $plan = DB::transaction(function () use ($request, $nivel) {
                // Calcular el siguiente orden de forma segura
                $maxOrden = Mejora::where('procedencia', $request->input('procedencia'))
                    ->lockForUpdate()
                    ->max('orden');
                $orden = ($maxOrden ?? 0) + 1;

                $plan = new Mejora();
                $plan->tipo                   = $request->input('tipo_plan');
                $plan->procedencia            = $request->input('procedencia');
                $plan->orden                  = $orden;
                $plan->plan_no                = $request->input('plan_no') . $orden;
                $plan->cantidad               = $request->input('cantidad');
                $plan->fecha_vencimiento      = $request->input('fecha_vencimiento');
                $plan->id_des                 = $request->input('des');
                $plan->id_ua                  = $request->input('unidad_academica');
                $plan->id_sede                = $request->input('sede');
                $plan->id_programa_educativo  = $request->input('programa_educativo');
                $plan->id_nivel_estudio       = $request->input('nivel');
                $plan->id_modalidad_estudio   = $request->input('modalidad');
                $plan->verifico               = $request->input('verifico');
                $plan->recomendacion_meta     = $request->input('recomendacion_meta');
                $plan->ods_pdi                = $request->input('ods_pdi');
                $plan->eje_pdi                = $request->input('eje_pdi');
                $plan->objetivo_pdi           = $request->input('objetivo_pdi');
                $plan->indicador_pdi          = $request->input('indicador_pdi');
                $plan->id_ods_pdi             = $request->input('ods_pdi_select');
                $plan->id_estrategia          = $request->input('estrategias');
                $plan->id_meta                = $request->input('meta_pdi');
                $plan->ambito_siemec          = $request->input('ambito_siemec');
                $plan->criterio_siemec        = $request->input('criterio_siemec');
                $plan->activo                 = 1;
                $plan->nivel                  = $nivel;
                // Usa TZ de la app (en vez de date())
                $plan->fecha_creacion         = now()->timezone(config('app.timezone'))->toDateString();
                $plan->tipo_mejora            = $request->input('tipo_mejora');
                $plan->save();

                return $plan;
            });

            return response()->json([
                'code'    => 200,
                'mensaje' => 'Registro actualizado correctamente.',
                'id'      => $plan->id,
                'orden'   => $plan->orden,
                'plan_no' => $plan->plan_no,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 400,
                'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.',
                'data' => $e->getMessage(), // en prod puedes omitir el detalle
            ], 400);
        }
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
        } catch (Exception $e) {
            $msg = [
                'code' => 400,
                'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.',
                'data' => $e,
            ];
        }

        return response()->json($msg, $msg['code']);
    }


    public function delArchivo($id)
    {
        try {
            $elimina = Accion::find($id);
            Storage::disk('public')->delete($elimina->evidencia);
            $elimina->evidencia = null;
            $elimina->save();

            return response()->json([
                'code' => 200,
                'mensaje' => 'Archivo eliminado de manera correcta.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 400,
                'mensaje' => 'Intente de nuevo o consulte al administrador del sistema',
                'data' => $e
            ], 400);
        }
    }
}
