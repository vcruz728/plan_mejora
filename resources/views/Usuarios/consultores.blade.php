@extends('app')

@push('styles')
    {{-- Extras de UI para esta pantalla --}}
    <style>
        .btn-icon {
            width: 34px;
            height: 34px;
            padding: 6px 0;
            text-align: center;
        }

        .btn-icon i {
            font-size: 16px;
            line-height: 20px;
        }

        .dt-loader {
            padding: 1rem;
            color: #666;
        }

        .modal .help {
            font-size: 12px;
            color: #777;
        }

        .select2-container--default .select2-selection--single {
            height: 34px;
        }

        .select2-container .select2-selection--single .select2-selection__rendered {
            line-height: 32px;
        }

        .select2-container .select2-selection--single .select2-selection__arrow {
            height: 32px;
        }
    </style>
@endpush

@push('styles')
    <link rel="stylesheet" href="{{ asset('bower_components/select2/dist/css/select2.min.css') }}">
@endpush


@section('htmlheader_title', 'Listar consultores')

@section('main-content')
    <section class="content-header">
        <h1>Lista de consultores</h1>
    </section>

    <div class="col-xs-12">
        <div class="box box-success">
            <div class="box-header with-border">

                <div class="form-inline" style="margin-right:10px">
                    <label for="filtro_procedencia" class="control-label" style="margin-right:10px;">Filtrar por
                        procedencia:</label>
                    <select id="filtro_procedencia" class="form-control select2" data-placeholder="Todas las procedencias"
                        style="width:400px">
                        <option value=""></option>
                        @foreach ($procedencias as $p)
                            <option value="{{ $p->id }}">{{ $p->descripcion }}</option>
                        @endforeach
                    </select>
                    <button id="btn_limpiar_filtro" class="btn btn-default">Quitar filtro</button>
                </div>
            </div>

            <div style="display:flex; justify-content:center; align-items:center;">
                <button class="btn
                btn-primary btn-sm" data-toggle="modal" data-target="#modal_nuevo"
                    style="margin: 15px 0;">
                    <i class="fa fa-plus-circle"></i> Nuevo consultor
                </button>
            </div>


            <div class="box-body" style="padding-top: .75rem;">
                <div id="div_tabla" class="table-responsive">
                    <div class="dt-loader"><i class="fa fa-spinner fa-spin"></i> Cargando…</div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL: Nuevo --}}
    <div class="modal fade" id="modal_nuevo" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form id="form_nuevo" class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Agregar consultor</h3>
                    <button type="button" class="close" data-dismiss="modal"
                        aria-label="Cerrar"><span>&times;</span></button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="usuario">Usuario <span class="text-danger">*</span></label>
                                <input type="text" name="usuario" id="usuario" class="form-control" required>
                                <div class="help">Cuenta de acceso</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Nombre del responsable <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" class="form-control" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">Correo electrónico <span class="text-danger">*</span></label>
                                <input type="email" name="email" id="email" class="form-control" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="procedencia">Procedencia <span class="text-danger">*</span></label>
                                <select name="procedencia" id="procedencia" class="form-control select2" style="width:100%"
                                    required>
                                    <option value="">Seleccione una opción</option>
                                    @foreach ($procedencias as $value)
                                        <option value="{{ $value->id }}">{{ $value->descripcion }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="button" id="btn_guardar_nuevo" class="btn btn-success"
                        onclick="guardarNuevo()">Agregar</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL: Editar --}}
    <div class="modal fade" id="modal_editar" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form id="form_editar" class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Editar consultor</h3>
                    <button type="button" class="close" data-dismiss="modal"
                        aria-label="Cerrar"><span>&times;</span></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="consultor_id">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="usuario_edit">Usuario</label>
                                <input type="text" name="usuario_edit" id="usuario_edit" class="form-control"
                                    required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name_edit">Nombre del responsable</label>
                                <input type="text" name="name_edit" id="name_edit" class="form-control" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email_edit">Correo electrónico</label>
                                <input type="email" name="email_edit" id="email_edit" class="form-control" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="procedencia_edit">Procedencia</label>
                                <select name="procedencia_edit" id="procedencia_edit" class="form-control select2"
                                    style="width:100%" required>
                                    <option value="">Seleccione una opción</option>
                                    @foreach ($procedencias as $value)
                                        <option value="{{ $value->id }}">{{ $value->descripcion }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <div class="pull-left">
                        <button type="button" id="btn_resetear" class="btn btn-info" onclick="abrirReset()">
                            <i class="fa fa-key"></i> Resetear contraseña
                        </button>
                    </div>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="button" id="btn_guardar_edicion" class="btn btn-success"
                        onclick="guardarEdicion()">Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL: Restablecer contraseña --}}
    <div class="modal fade" id="modal_reset" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="form_reset" class="modal-content" onsubmit="enviarReset(event)">
                <div class="modal-header">
                    <h3 class="modal-title">Restablecer contraseña</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="reset_user_id">
                    <div class="form-group">
                        <label for="nueva_pass">Nueva contraseña <span class="text-danger">*</span></label>
                        <input type="password" id="nueva_pass" name="nueva_pass" class="form-control" required
                            minlength="5" maxlength="20">
                    </div>
                    <div class="form-group">
                        <label for="nueva_pass_confirmation">Confirmar contraseña <span
                                class="text-danger">*</span></label>
                        <input type="password" id="nueva_pass_confirmation" class="form-control" required minlength="5"
                            maxlength="20">
                    </div>
                    <small class="help">Longitud permitida: 5 a 20 caracteres.</small>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="submit" id="btn_reset_enviar" class="btn btn-info">
                        <i class="fa fa-key"></i> Guardar nueva contraseña
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
    {{-- Select2 (si ya lo cargas en layout, puedes quitar esto) --}}
    <script src="{{ asset('bower_components/select2/js/select2.min.js') }}"></script>
@endpush

@section('localscripts')
    <script>
        var base_url = $("input[name='base_url']").val();
        var tabla, idEnEdicion = null;

        // ======================= Helpers =======================
        const enable = (btnId) => $(btnId).prop('disabled', false);
        const disable = (btnId) => $(btnId).prop('disabled', true);

        const toastErrorFromValidation = (payload) => {
            if (payload && payload.errors) {
                const firstKey = Object.keys(payload.errors)[0];
                if (firstKey) swal("¡Error!", payload.errors[firstKey][0], "error");
                return true;
            }
            return false;
        }

        const reloadTabla = () => {
            if (tabla) {
                tabla.ajax.reload(null, false);
            } else {
                initTabla();
            }
        };

        // ======================= DataTable =======================
        function escapeHtml(str) {
            return String(str || '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        function initTabla() {
            $("#div_tabla").html(`
      <table class="table table-bordered table-striped" id="tabla_consultores" style="width:100%">
        <thead>
          <tr>
            <th>Usuario</th>
            <th>Nombre</th>
            <th>Email</th>
            <th>Procedencia</th>
            <th style="width:110px;">Acciones</th>
          </tr>
        </thead>
      </table>
    `);

            tabla = $('#tabla_consultores').DataTable({
                rowId: (row) => `row_${row.id}`,
                processing: true,
                serverSide: false,
                deferRender: true,
                ajax: {
                    url: `${base_url}/admin/get/usuarios/sistema/consultores`,
                    type: 'GET',
                    dataSrc: function(json) {
                        // si tu API devuelve { code, mensaje, data }
                        if (json && typeof json === 'object') {
                            if (json.code !== undefined && json.code !== 200) {
                                swal("¡Error!", json.mensaje || 'No se pudo cargar la tabla.', "error");
                                return [];
                            }
                            return json.data || [];
                        }
                        // si no es JSON válido
                        swal("¡Error!", "La respuesta no es JSON. ¿La sesión expiró?", "error");
                        return [];
                    },
                    error: function(xhr) {
                        let msg = 'No se pudo cargar la tabla.';
                        if (xhr.status === 419) msg = 'Sesión expirada (419). Vuelve a iniciar sesión.';
                        if (xhr.status === 401) msg = 'No autorizado (401).';
                        $('#div_tabla').prepend(`<div class="alert alert-danger">${escapeHtml(msg)}</div>`);
                    }
                },
                ajax: {
                    url: `${base_url}/admin/get/usuarios/sistema/consultores`,
                    type: 'GET',
                    data: d => {
                        d.procedencia = $('#filtro_procedencia').val() || '';
                    },
                    dataSrc: function(json) {
                        console.log('DT response:', json); // <-- mira la forma real en la consola

                        // 1) Toma el array correcto
                        let rows = Array.isArray(json) ? json :
                            (Array.isArray(json?.data) ? json.data : []);

                        // 2) Devuelve SIEMPRE objetos con las llaves que tus columnas esperan
                        return rows.map(r => ({
                            id: r.id,
                            usuario: r.usuario || r.username || '',
                            name: r.name || r.nombre || '',
                            email: r.email || '',
                            procedencia: r.procedencia || r?.cat_procedencias?.descripcion || ''
                        }));
                    }
                },

                columns: [{
                        data: 'usuario',
                        title: 'Usuario'
                    },
                    {
                        data: 'name',
                        title: 'Nombre'
                    },
                    {
                        data: 'email',
                        title: 'Email'
                    },
                    {
                        data: 'procedencia',
                        title: 'Procedencia'
                    },
                    {
                        data: null,
                        orderable: false,
                        className: 'text-center',
                        render: o => {
                            const id = o.id ?? '';
                            const nombre = (o.name ?? '').replace(/"/g, '&quot;');
                            return `
          <div class="btn-group" role="group">
            <button class="btn btn-primary btn-icon" title="Editar" onclick="abrirEdicion('${id}')"><i class="fa fa-pencil"></i></button>
            <button class="btn btn-info btn-icon" title="Resetear contraseña" onclick="abrirReset('${id}')"><i class="fa fa-key"></i></button>
            <button class="btn btn-danger btn-icon" title="Eliminar" onclick="eliminaConsultor('${id}')"><i class="fa fa-trash"></i></button>
          </div>`;
                        }
                    }
                ],
                language: {
                    url: base_url + '/js/Spanish.json'
                },
                paging: true,
                pageLength: 10,
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, 'Todos']
                ],
                order: [
                    [1, 'asc']
                ],
                autoWidth: false,
                responsive: true,
                drawCallback: function() {
                    $('[data-toggle="tooltip"]').tooltip();
                }
            });
        }

        // ======================= Crear =======================
        async function guardarNuevo() {
            disable('#btn_guardar_nuevo');
            try {
                const body = new FormData(document.getElementById('form_nuevo'));
                const resp = await fetch(`${base_url}/admin/guarda/nuevo/consultor`, {
                    method: 'POST',
                    body,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                const data = await resp.json();

                if (data.code === 200) {
                    $('#form_nuevo')[0].reset();
                    $('#procedencia').val('').trigger('change');
                    $('#modal_nuevo').modal('hide');
                    reloadTabla();
                    swal("¡Correcto!", data.mensaje, "success");
                } else if (data.code === 411 && toastErrorFromValidation(data)) {
                    // handled
                } else {
                    swal("¡Error!", data.mensaje || 'No se pudo guardar.', "error");
                }
            } catch (e) {
                swal("¡Error!", "Ocurrió un problema de red.", "error");
            } finally {
                enable('#btn_guardar_nuevo');
            }
        }

        // ======================= Editar (abrir + guardar) =======================
        async function abrirEdicion(id) {
            try {
                const resp = await fetch(`${base_url}/admin/get/informacion/usuario/${id}`, {
                    method: 'GET'
                });
                const data = await resp.json();
                if (data.code !== 200) return swal("¡Error!", data.mensaje || 'No se pudo obtener la información.',
                    "error");

                idEnEdicion = id;
                $('#usuario_edit').val(data.data.usuario);
                $('#name_edit').val(data.data.name);
                $('#email_edit').val(data.data.email);
                $('#procedencia_edit').val(data.data.procedencia).trigger('change');

                $('#modal_editar').modal('show');
            } catch (e) {
                swal("¡Error!", "No se pudo cargar la información.", "error");
            }
        }

        async function guardarEdicion() {
            if (!idEnEdicion) return;
            disable('#btn_guardar_edicion');
            try {
                const body = new FormData(document.getElementById('form_editar'));
                const resp = await fetch(`${base_url}/admin/edita/consultor/${idEnEdicion}`, {
                    method: 'POST',
                    body,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                const data = await resp.json();

                if (data.code === 200) {
                    $('#modal_editar').modal('hide');
                    reloadTabla();
                    $('#form_editar')[0].reset();
                    $('#procedencia_edit').val('').trigger('change');
                    swal("¡Correcto!", data.mensaje, "success");
                } else if (data.code === 411 && toastErrorFromValidation(data)) {
                    // handled
                } else {
                    swal("¡Error!", data.mensaje || 'No se pudo guardar los cambios.', "error");
                }
            } catch (e) {
                swal("¡Error!", "Ocurrió un problema de red.", "error");
            } finally {
                enable('#btn_guardar_edicion');
            }
        }

        // ======================= Resetear contraseña =======================
        function abrirReset(id = null) {
            const targetId = id || idEnEdicion;
            if (!targetId) return;
            $('#reset_user_id').val(targetId);
            $('#form_reset')[0].reset();
            $('#modal_reset').modal('show');
        }

        async function enviarReset(e) {
            e.preventDefault();
            const userId = $('#reset_user_id').val();
            const pass1 = $('#nueva_pass').val().trim();
            const pass2 = $('#nueva_pass_confirmation').val().trim();

            if (pass1 !== pass2) {
                return swal("¡Error!", "Las contraseñas no coinciden.", "error");
            }
            if (pass1.length < 5 || pass1.length > 20) {
                return swal("¡Error!", "La contraseña debe tener entre 5 y 20 caracteres.", "error");
            }

            $('#btn_reset_enviar').prop('disabled', true);

            try {
                const fd = new FormData();
                fd.append('nueva_pass', pass1);
                fd.append('nueva_pass_confirmation', pass2);

                const resp = await fetch(`${base_url}/admin/usuario/resetea-password/${userId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: fd
                });

                const data = await resp.json();

                if (data.code === 200) {
                    $('#modal_reset').modal('hide');
                    swal("¡Listo!", data.mensaje, "success");
                } else if (data.code === 411 && data.errors && data.errors.nueva_pass) {
                    swal("¡Error!", data.errors.nueva_pass[0], "error");
                } else {
                    swal("¡Error!", data.mensaje || 'No se pudo resetear.', "error");
                }
            } catch (err) {
                swal("¡Error!", "Problema de red al resetear.", "error");
            } finally {
                $('#btn_reset_enviar').prop('disabled', false);
            }
        }


        function confirmarReseteo(id = null, nombre = '') {
            const targetId = id || idEnEdicion;
            if (!targetId) return;
            swal({
                title: '¿Resetear contraseña?',
                text: nombre ? `Se reseteará la contraseña de ${nombre}.` : 'Se reseteará la contraseña.',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Sí, resetear'
            }, async function(isConfirm) {
                if (!isConfirm) return;
                try {
                    const resp = await fetch(`${base_url}/admin/usuario/resetea-password/${targetId}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        body: new URLSearchParams({
                            action: 'reset'
                        })
                    });

                    const data = await resp.json();
                    if (data.code === 200) {
                        $('#modal_editar').modal('hide');
                        swal("¡Listo!", data.mensaje, "success");
                    } else {
                        swal("¡Error!", data.mensaje || 'No se pudo resetear.', "error");
                    }
                } catch (e) {
                    swal("¡Error!", "Problema de red al resetear.", "error");
                }
            });
        }

        // ======================= Eliminar =======================

        const eliminaConsultor = async (userId) => {
            swal({
                title: '¿Eliminar consultor?',
                text: 'Esta acción no se puede deshacer.',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d9534f',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }, async (isConfirm) => {
                if (!isConfirm) return;
                const resp = await fetch(`${base_url}/admin/consultor/${userId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const data = await resp.json();
                if (data.code == 200) {
                    swal('¡Correcto!', data.mensaje, 'success');
                    tabla.row(`#row_${userId}`).remove().draw(false);
                    reloadTabla();
                } else {
                    swal('¡Error!', data.mensaje || 'No se pudo eliminar.', 'error');
                }
            });
        };


        function confirmarEliminacion(id, nombre = '') {
            swal({
                title: '¿Eliminar consultor?',
                text: nombre ? `Esto eliminará a ${nombre} permanentemente.` :
                    'Esto eliminará el registro permanentemente.',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }, function(isConfirm) {
                if (isConfirm) eliminar(id);
            });
        }

        async function eliminar(id) {
            try {
                const resp = await fetch(`${base_url}/admin/elimina/consultor/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                const data = await resp.json();
                if (data.code === 200) {
                    reloadTabla();
                    swal("¡Eliminado!", data.mensaje, "success");
                } else {
                    swal("¡Error!", data.mensaje || 'No se pudo eliminar.', "error");
                }
            } catch (e) {
                swal("¡Error!", "Problema de red al eliminar.", "error");
            }
        }

        // ======================= Filtros =======================

        $('#filtro_procedencia').select2({
            placeholder: $('#filtro_procedencia').data('placeholder'),
            allowClear: true,
            width: 'resolve'
        });

        $('#filtro_procedencia').on('change', function() {
            tabla.ajax.reload(null, false);
        });

        // quitar filtro
        $('#btn_limpiar_filtro').on('click', function() {
            $('#filtro_procedencia').val(null).trigger('change'); // vuelve el placeholder
            tabla.ajax.reload(null, false);
        });
        // ======================= Init =======================
        $(function() {
            $('.select2').select2({
                width: 'resolve'
            });
            initTabla();

            // enter para enviar formularios desde modal
            $('#form_nuevo').on('submit', function(e) {
                e.preventDefault();
                guardarNuevo();
            });
            $('#form_editar').on('submit', function(e) {
                e.preventDefault();
                guardarEdicion();
            });
        });
    </script>
@endsection
