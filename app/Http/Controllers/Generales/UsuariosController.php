<?php

namespace App\Http\Controllers\Generales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\Catalogos\Des;
use App\Models\Catalogos\UnidadesAcademicas;
use App\Models\Catalogos\Sedes;
use App\Models\Catalogos\ProgramasEducativos;
use App\Models\Catalogos\NivelesEstudio;
use App\Models\Catalogos\Modalidad;
use App\Models\Catalogos\Procedencias;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;


class UsuariosController extends Controller
{
    public function viewAlta()
    {
        $des = Des::orderBy('nombre')->get();

        return view('Usuarios.alta', compact('des'));
    }

    public function addNuevo(Request $request)
    {
        try {
            $validate =  \Validator::make(
                $request->all(),
                [
                    'des' => 'required',
                    'name' => 'required',
                    'email' => 'required|email:rfc,dns|unique:users,email',
                    'usuario' => 'alpha_dash|' . utf8_encode('regex:/^[áéíóúÁÉÍÓÚñÑa-zA-Z-0-9$#.() ]*$/') . '|min:4|max:20|unique:users,usuario|regex:/^[-áéíóúÁÉÍÓÚñÑa-zA-Z0-9$#.() ]*$/',
                    'tipo_mejora' => 'required'
                ],
                [
                    'email.unique' => 'El correo electrónico ingresado ya se encuentra registrado en el sistema.'
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

            $usuario = new User();
            $usuario->usuario = $request->input('usuario');
            $usuario->name = $request->input('name');
            $usuario->email = $request->input('email');
            $usuario->tipo_mejora = $request->input('tipo_mejora');
            $usuario->id_des = $request->input('des');
            $usuario->id_ua = $request->input('unidad_academica');
            $usuario->id_sede = $request->input('sede');
            $usuario->id_programa = $request->input('programa_educativo');
            $usuario->id_nivel = $request->input('nivel');
            $usuario->id_modalidad = $request->input('modalidad');
            $usuario->password = Hash::make($request->input('usuario'));
            $usuario->rol = 2;
            $usuario->nivel = $nivel;
            $usuario->save();


            $msg = [
                'code' => 200,
                'mensaje' => 'Usuario actualizado correctamente.',
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

    public function viewEdit($id)
    {
        $usuario = User::select(
            'id',
            'usuario',
            'name',
            'email',
            'tipo_mejora',
            'id_des',
            DB::raw('COALESCE(id_ua, 0) as id_ua'),
            DB::raw('COALESCE(id_sede, 0) as id_sede'),
            DB::raw('COALESCE(id_programa, 0) as id_programa'),
            DB::raw('COALESCE(id_nivel, 0) as id_nivel'),
            DB::raw('COALESCE(id_modalidad, 0) as id_modalidad'),
            'password',
            'rol',
            'nivel'
        )
            ->where('id', $id)
            ->first();
        $des = Des::where('tipo', $usuario->tipo_mejora)->orderBy('nombre')->get();


        $ua = UnidadesAcademicas::where('id_des', $usuario->id_des)->get();
        $sedes = Sedes::where('id_ua', $usuario->id_ua)->get();
        $programas = ProgramasEducativos::where('id_sede', $usuario->id_sede)->get();
        $nivel_estudios = NivelesEstudio::where('id_programa_estudio', $usuario->id_programa)->get();
        $modalidades = Modalidad::where('id_nivel_estudio', $usuario->id_nivel)->get();


        return view('Usuarios.editar', compact('des', 'usuario', 'ua', 'sedes', 'programas', 'nivel_estudios', 'modalidades'));
    }

    public function editUser(Request $request)
    {
        try {
            $id_usuario = $request->input('id_usuario');

            $validate =  \Validator::make(
                $request->all(),
                [
                    'des' => 'required',
                    'name' => 'required',
                    'tipo_mejora' => 'required',
                    'usuario' => ['alpha_dash', utf8_encode('regex:/^[áéíóúÁÉÍÓÚñÑa-zA-Z-0-9$#.() ]*$/'), 'min:4', 'max:20', 'regex:/^[-áéíóúÁÉÍÓÚñÑa-zA-Z0-9$#.() ]*$/', Rule::unique('users', 'usuario')->ignore($id_usuario)]
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

            $usuario = User::find($id_usuario);
            $usuario->usuario = $request->input('usuario');
            $usuario->tipo_mejora = $request->input('tipo_mejora');
            $usuario->name = $request->input('name');
            $usuario->id_des = $request->input('des');
            $usuario->id_ua = $request->input('unidad_academica');
            $usuario->id_sede = $request->input('sede');
            $usuario->id_programa = $request->input('programa_educativo');
            $usuario->id_nivel = $request->input('nivel');
            $usuario->id_modalidad = $request->input('modalidad');
            $usuario->nivel = $nivel;
            $usuario->save();


            $msg = [
                'code' => 200,
                'mensaje' => 'Usuario agregado correctamente.',
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

    public function listaUsuarios()
    {
        return view('Usuarios.lista');
    }

    public function getUsuarios(Request $request)
    {
        try {
            $q = User::query()
                ->select([
                    'users.id',
                    'users.name',
                    'users.email',
                    'users.usuario',
                    'ua.nombre as unidad',
                ])
                ->leftJoin('cat_procedencias as cp', 'cp.id', '=', 'users.procedencia')
                ->leftJoin('cat_unidades_academicas as ua', 'ua.id', '=', 'cp.id_ua')
                ->where('users.rol', 2);

            // Filtros pasan a cp.*
            if ($request->filled('tipo_mejora')) {
                $q->where('cp.tipo_mejora', $request->tipo_mejora);
            }
            if ($request->filled('des')) {
                $q->where('cp.id_des', $request->des);
            }
            if ($request->filled('ua')) {
                $q->where('cp.id_ua', $request->ua);
            }
            if ($request->filled('sede')) {
                $q->where('cp.id_sede', $request->sede);
            }

            $usuarios = $q->orderBy('users.name')->get();

            return response()->json([
                'code'    => 200,
                'mensaje' => 'Listado de usuarios.',
                'data'    => $usuarios,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'code'    => 400,
                'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.'
            ], 400);
        }
    }



    public function getInfoUser($id)
    {
        try {
            $usuario = \App\Models\User::query()
                ->leftJoin('cat_unidades_academicas as ua', 'ua.id', 'users.id_ua')
                ->select([
                    'users.id',
                    'users.name',
                    'users.email',
                    'users.usuario',
                    'users.rol',
                    'users.tipo_mejora',
                    'users.id_des',
                    'users.id_ua',
                    'users.id_sede',
                    'users.id_programa',
                    'users.id_nivel',
                    'users.id_modalidad',
                    'users.procedencia',
                    \DB::raw('ua.nombre as unidad'), // back-compat para consultores
                ])
                ->where('users.id', $id)
                ->first();

            if (!$usuario) {
                return response()->json([
                    'code'    => 404,
                    'mensaje' => 'Usuario no encontrado',
                ], 404);
            }

            return response()->json([
                'code'    => 200,
                'mensaje' => 'Información del usuario.',
                'data'    => $usuario,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'code'    => 500,
                'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }


    public function editUsuario(Request $request, $id)
    {
        try {
            $validate =  \Validator::make(
                $request->all(),
                [
                    'name' => 'required',
                    'email' => ['required', 'email:rfc,dns', Rule::unique('users', 'email')->ignore($id)],
                    'usuario' => ['required', 'alpha_dash', 'min:4', 'max:20', 'regex:/^[-áéíóúÁÉÍÓÚñÑa-zA-Z0-9$#.() ]*$/', Rule::unique('users', 'usuario')->ignore($id)],
                ],
                [
                    'email.unique' => 'El correo electrónico ingresado ya se encuentra registrado en el sistema.'
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

            $usuario = User::find($id);
            $usuario->name = $request->input('name');
            $usuario->email = $request->input('email');
            $usuario->usuario = $request->input('usuario');
            $usuario->save();


            $msg = [
                'code' => 200,
                'mensaje' => 'Usuario actualizado correctamente.',
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

    public function resetPassword(Request $request, $id)
    {
        try {
            $validate = \Validator::make(
                $request->all(),
                [
                    'nueva_pass' => 'required|min:5|max:20|confirmed',
                ],
                [
                    'nueva_pass.confirmed' => 'Las contraseñas no coinciden.',
                ]
            );

            if ($validate->fails()) {
                return response()->json([
                    'code' => 411,
                    'mensaje' => 'Error',
                    'errors' => $validate->errors()
                ], 411);
            }

            $usuario = \App\Models\User::findOrFail($id);

            $usuario->password = \Illuminate\Support\Facades\Hash::make($request->input('nueva_pass'));
            $usuario->setRememberToken(Str::random(60));
            $usuario->save();

            return response()->json([
                'code' => 200,
                'mensaje' => 'Contraseña actualizada correctamente.'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'code' => 404,
                'mensaje' => 'Usuario no encontrado.'
            ], 404);
        } catch (\Throwable $e) {
            // En desarrollo, te puede ayudar ver el error exacto:
            // return response()->json(['code' => 400, 'mensaje' => $e->getMessage()], 400);
            return response()->json([
                'code' => 400,
                'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.'
            ], 400);
        }
    }


    public function viewAltaRev()
    {
        $procedencias = Procedencias::orderBy('descripcion')->get();

        return view('Usuarios.consultores', compact('procedencias'));
    }



    public function getConsultores(Request $request)
    {
        try {
            $q = User::select(
                'users.id',
                'users.name',
                'users.email',
                'users.usuario',
                'cat_procedencias.descripcion as procedencia'
            )
                ->join('cat_procedencias', 'cat_procedencias.id', 'users.procedencia')
                ->where('users.rol', 4);

            // ⬇️ Filtro por procedencia (si viene)
            if ($request->filled('procedencia')) {
                $q->where('users.procedencia', (int) $request->input('procedencia'));
            }

            $usuarios = $q->orderBy('name')->get();

            $msg = [
                'code' => 200,
                'mensaje' => 'Listado de consultores.',
                'data' => $usuarios
            ];
        } catch (\Illuminate\Database\QueryException $ex) {
            $msg = [
                'code' => 400,
                'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.',
                'data' => $ex,
            ];
        } catch (\Exception $e) {
            $msg = [
                'code' => 400,
                'mensaje' => 'Intente de nuevo o consulte al administrador del sistema.',
                'data' => $e,
            ];
        }

        return response()->json($msg, $msg['code']);
    }


    public function saveConsultor(Request $request)
    {
        try {
            $validate =  \Validator::make(
                $request->all(),
                [
                    'name' => 'required',
                    'email' => 'required|email:rfc,dns|unique:users,email',
                    'usuario' => 'alpha_dash|' . utf8_encode('regex:/^[áéíóúÁÉÍÓÚñÑa-zA-Z-0-9$#.() ]*$/') . '|min:4|max:20|unique:users,usuario|regex:/^[-áéíóúÁÉÍÓÚñÑa-zA-Z0-9$#.() ]*$/',
                    'procedencia' => 'required',
                ],
                [
                    'email.unique' => 'El correo electrónico ingresado ya se encuentra registrado en el sistema.'
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



            $usuario = new User();
            $usuario->usuario = $request->input('usuario');
            $usuario->name = $request->input('name');
            $usuario->email = $request->input('email');
            $usuario->procedencia = $request->input('procedencia');
            $usuario->password = Hash::make($request->input('usuario'));
            $usuario->rol = 4;
            $usuario->save();


            $msg = [
                'code' => 200,
                'mensaje' => 'Consultor agregado correctamente.',
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

    public function editConsultor(Request $request, $id)
    {
        try {
            $validate =  \Validator::make(
                $request->all(),
                [
                    'name_edit' => 'required',
                    'email_edit' => ['required', 'email:rfc,dns', Rule::unique('users', 'email')->ignore($id)],
                    'usuario_edit' => ['required', 'alpha_dash', 'min:4', 'max:20', 'regex:/^[-áéíóúÁÉÍÓÚñÑa-zA-Z0-9$#.() ]*$/', Rule::unique('users', 'usuario')->ignore($id)],
                    'procedencia_edit' => 'required',
                ],
                [
                    'email.unique' => 'El correo electrónico ingresado ya se encuentra registrado en el sistema.'
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

            $usuario = User::find($id);
            $usuario->name = $request->input('name_edit');
            $usuario->email = $request->input('email_edit');
            $usuario->usuario = $request->input('usuario_edit');
            $usuario->procedencia = $request->input('procedencia_edit');
            $usuario->save();


            $msg = [
                'code' => 200,
                'mensaje' => 'Usuario actualizado correctamente.',
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
    public function destroyConsultor($id)
    {
        try {
            $user = \App\Models\User::where('id', $id)
                ->where('rol', 4)
                ->firstOrFail();

            // (Opcional) valida relaciones para evitar borrado con dependencias
            //if ($user->planes()->exists()) {
            //      return response()->json(['code' => 409, 'mensaje' => 'No se puede eliminar...']);
            //}

            $user->delete();

            return response()->json([
                'code' => 200,
                'mensaje' => 'Consultor eliminado correctamente.'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'code' => 400,
                'mensaje' => 'No se pudo eliminar el consultor.',
            ]);
        }
    }
    public function destroyUsuario($id)
    {
        try {
            $user = User::where('id', $id)->where('rol', 2)->firstOrFail();
            $user->delete();
            return response()->json(['code' => 200, 'mensaje' => 'Usuario eliminado correctamente.']);
        } catch (\Throwable $e) {
            return response()->json(['code' => 400, 'mensaje' => 'No se pudo eliminar el usuario.'], 400);
        }
    }
}
