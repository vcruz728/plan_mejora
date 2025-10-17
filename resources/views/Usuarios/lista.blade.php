@extends('app')
@section('htmlheader_title', 'Listar Usuarios')

@push('styles')
    <link rel="stylesheet" href="{{ asset('bower_components/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('dist/css/forms-modern.css') }}?v={{ filemtime(public_path('dist/css/forms-modern.css')) }}">
@endpush

@section('main-content')
    <section class="content-header">
        <h1 style="text-align:center; margin:15px 0;">Lista de usuarios</h1>
    </section>

    <div class="col-xs-12 list-narrow">
        <div class="box box-success">
            <div class="box-header with-border">
                <div class="form-inline">
                    <label class="control-label" style="margin-right:10px;">Filtro:</label>

                    <select id="f_tipo" class="form-control" style="width:160px; margin-right:8px;">
                        <option value="">AC/Dependencia</option>
                        <option value="1">AC</option>
                        <option value="2">Dependencia</option>
                    </select>

                    <select id="f_des" class="form-control select2" style="width:240px; margin-right:8px;"
                        data-placeholder="DES/Dependencia">
                        <option value=""></option>
                    </select>

                    <select id="f_ua" class="form-control select2" style="width:220px; margin-right:8px;"
                        data-placeholder="Unidad académica">
                        <option value=""></option>
                    </select>

                    <select id="f_sede" class="form-control select2" style="width:200px; margin-right:8px;"
                        data-placeholder="Sede">
                        <option value=""></option>
                    </select>

                    <button id="btn_limpiar_filtros" class="btn btn-default">Quitar filtros</button>
                </div>
            </div>

            <div style="display:flex; justify-content:center; align-items:center;">
                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modal_nuevo_usuario"
                    style="margin:12px 0;">
                    <i class="fa fa-plus-circle"></i> Nuevo usuario
                </button>
            </div>

            <div class="box-body table-tight">
                <div id="div_tabla" class="table-responsive">
                    <table class="table table-bordered table-striped compact" id="tabla_usuarios" style="width:100%">
                        <thead>
                            <tr>
                                <th>Usuario</th>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Unidad</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL: Reset contraseña --}}
    <div class="modal fade" id="modal_reset" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="form_reset" class="modal-content" onsubmit="enviarReset(event)">
                <div class="modal-header">
                    <h3 class="modal-title">Restablecer contraseña</h3>
                    <button type="button" class="close" data-dismiss="modal"
                        aria-label="Cerrar"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="reset_user_id">
                    <div class="form-group">
                        <label for="nueva_pass">Nueva contraseña <span class="text-danger">*</span></label>
                        <input type="password" id="nueva_pass" name="nueva_pass" class="form-control" required
                            minlength="5" maxlength="20">
                    </div>
                    <div class="form-group">
                        <label for="nueva_pass_confirmation">Confirmar contraseña <span class="text-danger">*</span></label>
                        <input type="password" id="nueva_pass_confirmation" class="form-control" required minlength="5"
                            maxlength="20">
                    </div>
                    <small class="help">Longitud: 5 a 20 caracteres.</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="submit" id="btn_reset_enviar" class="btn btn-info"><i class="fa fa-key"></i>
                        Guardar</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL: Nuevo --}}
    <div class="modal fade" id="modal_nuevo_usuario" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form id="form_nuevo_usuario" class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Agregar usuario</h3>
                    <button type="button" class="close" data-dismiss="modal"
                        aria-label="Cerrar"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="nu_tipo">AC/Dependencia</label>
                                <select id="nu_tipo" name="tipo_mejora" class="form-control">
                                    <option value="">Seleccione una opción</option>
                                    <option value="1">AC</option>
                                    <option value="2">Dependencia</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="nu_des" id="nu_labelDes">AC/Dependencia <small
                                        class="text-muted">*</small></label>
                                <select id="nu_des" name="des" class="form-control select2"
                                    style="width:100%"></select>
                            </div>
                        </div>

                        <div id="nu_div_des" class="col-md-12">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="nu_ua">Unidad académica</label>
                                        <select id="nu_ua" name="unidad_academica" class="form-control select2"
                                            style="width:100%"></select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="nu_sede">Sede</label>
                                        <select id="nu_sede" name="sede" class="form-control select2"
                                            style="width:100%"></select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="nu_programa">Programa educativo</label>
                                        <select id="nu_programa" name="programa_educativo" class="form-control select2"
                                            style="width:100%"></select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="nu_nivel">Nivel</label>
                                        <select id="nu_nivel" name="nivel" class="form-control select2"
                                            style="width:100%"></select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="nu_modalidad">Modalidad</label>
                                        <select id="nu_modalidad" name="modalidad" class="form-control select2"
                                            style="width:100%"></select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nu_name">Nombre del responsable</label>
                                <input id="nu_name" name="name" type="text" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nu_usuario">Usuario</label>
                                <input id="nu_usuario" name="usuario" type="text" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nu_email">Correo electrónico</label>
                                <input id="nu_email" name="email" type="email" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="submit" id="btn_nu_guardar" class="btn btn-success">Agregar</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL: Editar usuario --}}
    <div class="modal fade" id="modal_editar_usuario" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form id="form_editar_usuario" class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Editar usuario</h3>
                    <button type="button" class="close" data-dismiss="modal"
                        aria-label="Cerrar"><span>&times;</span></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="eu_id_usuario" name="id_usuario">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="eu_tipo">AC/Dependencia</label>
                                <select id="eu_tipo" name="tipo_mejora" class="form-control">
                                    <option value="">Seleccione una opción</option>
                                    <option value="1">AC</option>
                                    <option value="2">Dependencia</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="eu_des" id="eu_labelDes">AC/Dependencia <small
                                        class="text-muted">*</small></label>
                                <select id="eu_des" name="des" class="form-control select2"
                                    style="width:100%"></select>
                            </div>
                        </div>

                        <div id="eu_div_des" class="col-md-12">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="eu_ua">Unidad académica</label>
                                        <select id="eu_ua" name="unidad_academica" class="form-control select2"
                                            style="width:100%"></select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="eu_sede">Sede</label>
                                        <select id="eu_sede" name="sede" class="form-control select2"
                                            style="width:100%"></select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="eu_programa">Programa educativo</label>
                                        <select id="eu_programa" name="programa_educativo" class="form-control select2"
                                            style="width:100%"></select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="eu_nivel">Nivel</label>
                                        <select id="eu_nivel" name="nivel" class="form-control select2"
                                            style="width:100%"></select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="eu_modalidad">Modalidad</label>
                                        <select id="eu_modalidad" name="modalidad" class="form-control select2"
                                            style="width:100%"></select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12"></div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="eu_name">Nombre del responsable</label>
                                <input id="eu_name" name="name" type="text" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="eu_usuario">Usuario</label>
                                <input id="eu_usuario" name="usuario" type="text" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="eu_email">Correo electrónico</label>
                                <input id="eu_email" name="email" type="email" class="form-control" disabled>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer" style="display:flex; justify-content:space-between; width:100%;">
                    <button type="button" class="btn btn-info" id="eu_btn_reset"><i class="fa fa-key"></i> Resetear
                        contraseña</button>
                    <div>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success" id="eu_btn_guardar">Actualizar usuario</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('bower_components/select2/dist/js/select2.full.min.js') }}"></script>
@endpush

@section('localscripts')
    <script>
        /* ================== Utiles generales ================== */
        var base_url = $("input[name='base_url']").val();
        var tabla; // DataTable
        var prefilling = false; // para no disparar handlers durante precargas
        let currentTipo = ''; // '1' AC, '2' Dependencia
        let editAbort = null; // AbortController para abortar cargas viejas
        let editLoadId = 0; // token anti-carreras

        // memoria de formulario por tipo
        const formMemory = {
            '1': null,
            '2': null
        };

        // caché de listas
        const listsCache = {
            des: {
                '1': null,
                '2': null
            },
            ua: {}, // key: id_des
            sedes: {}, // key: id_ua
            programas: {}, // key: id_sede
            niveles: {}, // key: id_prog
            modalidades: {} // key: id_nivel
        };

        function clearEditState() {
            formMemory['1'] = null;
            formMemory['2'] = null;
            currentTipo = '';
            prefilling = false;
        }

        // ==== SweetAlert overlay fix ====
        function unlockScroll() {
            $('body').removeClass('stop-scrolling modal-open swal2-shown swal2-height-auto')
                .css({
                    overflow: '',
                    height: '',
                    'padding-right': ''
                });
            var $ov = $('.sweet-overlay');
            if ($ov.length) {
                $ov.off();
                $ov.css({
                    display: 'none',
                    'pointer-events': 'none'
                });
            }
            $('.modal-backdrop').remove();
        }
        $(document).on('hidden.bs.modal', unlockScroll);
        $(document).on('click', '.sweet-overlay', function() {
            setTimeout(unlockScroll, 0);
        });

        /* ================== Select2 (filtros) ================== */
        $('.select2').select2({
            placeholder: function() {
                return $(this).data('placeholder')
            },
            allowClear: true,
            width: 'resolve'
        });

        /* ================== Filtros tabla ================== */
        $('#f_tipo').on('change', async function() {
            await cargarDes($(this).val());
            $('#f_ua').val('').trigger('change').empty().append('<option value=""></option>');
            $('#f_sede').val('').trigger('change').empty().append('<option value=""></option>');
            tabla.ajax.reload(null, false);
        });
        $('#f_des').on('change', async function() {
            await cargarUa($(this).val());
            $('#f_sede').val('').trigger('change').empty().append('<option value=""></option>');
            tabla.ajax.reload(null, false);
        });
        $('#f_ua').on('change', async function() {
            await cargarSedes($(this).val());
            tabla.ajax.reload(null, false);
        });
        $('#f_sede').on('change', function() {
            tabla.ajax.reload(null, false);
        });

        $('#btn_limpiar_filtros').on('click', function() {
            $('#f_tipo').val('');
            $('#f_des').val(null).trigger('change');
            $('#f_ua').val(null).trigger('change');
            $('#f_sede').val(null).trigger('change');
            tabla.ajax.reload(null, false);
        });

        // helpers filtros
        async function cargarDes(tipo) {
            $('#f_des').empty().append('<option value=""></option>');
            if (!tipo) return;
            const r = await fetch(`${base_url}/admin/get/des-o-dependencias/${tipo}`);
            const j = await r.json();
            if (j.code === 200) j.data.forEach(({
                id,
                nombre
            }) => $('#f_des').append(new Option(nombre, id)));
        }
        async function cargarUa(idDes) {
            $('#f_ua').empty().append('<option value=""></option>');
            if (!idDes) return;
            const r = await fetch(`${base_url}/admin/get/unidades/${idDes}`);
            const j = await r.json();
            if (j.code === 200) j.data.forEach(({
                id,
                nombre
            }) => $('#f_ua').append(new Option(nombre, id)));
        }
        async function cargarSedes(idUa) {
            $('#f_sede').empty().append('<option value=""></option>');
            if (!idUa) return;
            const r = await fetch(`${base_url}/admin/get/sedes/${idUa}`);
            const j = await r.json();
            if (j.code === 200) j.data.forEach(({
                id,
                nombre
            }) => $('#f_sede').append(new Option(nombre, id)));
        }

        /* ================== DataTable ================== */
        $.fn.dataTable.ext.errMode = 'none';

        function initTabla() {
            if ($.fn.DataTable.isDataTable('#tabla_usuarios')) return;

            tabla = $('#tabla_usuarios').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url: `${base_url}/admin/get/usuarios/sistema`,
                    type: 'GET',
                    data: function(d) {
                        d.tipo_mejora = $('#f_tipo').val() || '';
                        d.des = $('#f_des').val() || '';
                        d.ua = $('#f_ua').val() || '';
                        d.sede = $('#f_sede').val() || '';
                    },
                    dataSrc: function(json) {
                        return (json && json.code === 200) ? (json.data || []) : [];
                    },
                    error: function(xhr, textStatus) {
                        if (textStatus === 'abort') return;
                        let msg = 'No se pudo cargar la tabla.';
                        if (xhr.status === 419) msg = 'Sesión expirada (419).';
                        if (xhr.status === 401) msg = 'No autorizado (401).';
                        $('#div_tabla').find('.alert').remove();
                        $('#div_tabla').prepend(`<div class="alert alert-danger">${msg}</div>`);
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
                        data: 'unidad',
                        title: 'Unidad'
                    },
                    {
                        data: null,
                        orderable: false,
                        className: 'text-center dt-actions',
                        render: function(o) {
                            const id = o.id ?? '';
                            const nombre = (o.name ?? '').replace(/"/g, '&quot;');
                            return `
            <div class="btn-actions">
              <button class="btn btn-primary btn-icon" title="Editar" onclick="abrirEditarUsuario('${id}')">
                <i class="fa fa-pencil"></i>
              </button>
              <button class="btn btn-info btn-icon" title="Resetear contraseña" onclick="abrirReset('${id}')">
                <i class="fa fa-key"></i>
              </button>
              <button class="btn btn-danger btn-icon" title="Eliminar" onclick="eliminarUsuario('${id}','${nombre}')">
                <i class="fa fa-trash"></i>
              </button>
            </div>`;
                        }
                    }
                ],
                language: {
                    url: base_url + '/js/Spanish.json'
                },
                paging: true,
                pageLength: 10,
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
        $(function() {
            initTabla();
        });

        /* ================== Reset password ================== */
        function abrirReset(id) {
            $('#reset_user_id').val(id);
            $('#form_reset')[0].reset();
            $('#modal_reset').modal('show');
        }
        async function enviarReset(e) {
            e.preventDefault();
            const userId = $('#reset_user_id').val();
            const p1 = $('#nueva_pass').val().trim();
            const p2 = $('#nueva_pass_confirmation').val().trim();
            if (p1 !== p2) return swal('¡Error!', 'Las contraseñas no coinciden.', 'error');
            if (p1.length < 5 || p1.length > 20) return swal('¡Error!', 'Longitud inválida.', 'error');

            $('#btn_reset_enviar').prop('disabled', true);
            try {
                const fd = new FormData();
                fd.append('nueva_pass', p1);
                fd.append('nueva_pass_confirmation', p2);
                const resp = await fetch(`${base_url}/admin/usuario/resetea-password/${userId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Accept': 'application/json'
                    },
                    body: fd
                });
                const data = await resp.json();
                if (data.code === 200) {
                    $('#modal_reset').modal('hide');
                    swal('¡Listo!', data.mensaje, 'success');
                } else if (data.code === 411 && data.errors?.nueva_pass) {
                    swal('¡Error!', data.errors.nueva_pass[0], 'error');
                } else {
                    swal('¡Error!', data.mensaje || 'No se pudo resetear.', 'error');
                }
            } catch (_) {
                swal('¡Error!', 'Problema de red al resetear.', 'error');
            } finally {
                $('#btn_reset_enviar').prop('disabled', false);
            }
        }

        /* ================== Eliminar ================== */
        function eliminarUsuario(id, nombre = '') {
            swal({
                title: '¿Eliminar usuario?',
                text: nombre ? `Esto eliminará a ${nombre}.` : 'Esta acción no se puede deshacer.',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                closeOnConfirm: true,
                closeOnCancel: true
            }, async isConfirm => {
                if (!isConfirm) {
                    unlockScroll();
                    return;
                }
                try {
                    const resp = await fetch(`${base_url}/admin/usuario/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            'Accept': 'application/json'
                        }
                    });
                    const data = await resp.json();
                    if (data.code === 200) {
                        swal({
                            title: '¡Correcto!',
                            text: data.mensaje,
                            type: 'success',
                            timer: 1300,
                            showConfirmButton: false
                        });
                        tabla.ajax?.reload(null, false);
                    } else {
                        swal({
                            title: '¡Error!',
                            text: data.mensaje || 'No se pudo eliminar.',
                            type: 'error',
                            timer: 1800,
                            showConfirmButton: false
                        });
                    }
                } catch (_) {
                    swal({
                        title: '¡Error!',
                        text: 'Problema de red.',
                        type: 'error',
                        timer: 1800,
                        showConfirmButton: false
                    });
                } finally {
                    setTimeout(unlockScroll, 0);
                }
            });
        }

        /* ================== Nuevo (modal) ================== */
        $('#modal_nuevo_usuario').on('shown.bs.modal', function() {
            $('#nu_des, #nu_ua, #nu_sede, #nu_programa, #nu_nivel, #nu_modalidad').select2({
                dropdownParent: $('#modal_nuevo_usuario'),
                width: 'resolve',
                allowClear: true,
                placeholder: ''
            });
        });

        function nu_resetCascada(desde) {
            const clears = {
                'des': ['#nu_ua', '#nu_sede', '#nu_programa', '#nu_nivel', '#nu_modalidad'],
                'ua': ['#nu_sede', '#nu_programa', '#nu_nivel', '#nu_modalidad'],
                'sede': ['#nu_programa', '#nu_nivel', '#nu_modalidad'],
                'prog': ['#nu_nivel', '#nu_modalidad'],
                'nivel': ['#nu_modalidad']
            } [desde] || [];
            clears.forEach(sel => $(sel).empty().append('<option value=""></option>').val(null).trigger('change'));
        }
        $('#nu_tipo').on('change', async function() {
            const tipo = $(this).val();
            $('#nu_des').empty().append('<option value=""></option>');
            nu_resetCascada('des');
            if (tipo === '2') {
                $('#nu_div_des').attr('hidden', true);
                $('#nu_labelDes').text('Dependencia');
            } else {
                $('#nu_div_des').removeAttr('hidden');
                $('#nu_labelDes').text('AC');
            }
            if (!tipo) return;
            const r = await fetch(`${base_url}/admin/get/des-o-dependencias/${tipo}`);
            const j = await r.json();
            if (j.code === 200) j.data.forEach(({
                id,
                nombre
            }) => $('#nu_des').append(new Option(nombre, id)));
        });
        $('#nu_des').on('change', async function() {
            nu_resetCascada('des');
            const idDes = $(this).val();
            if (!idDes) return;
            const r = await fetch(`${base_url}/admin/get/unidades/${idDes}`);
            const j = await r.json();
            if (j.code === 200) j.data.forEach(({
                id,
                nombre
            }) => $('#nu_ua').append(new Option(nombre, id)));
        });
        $('#nu_ua').on('change', async function() {
            nu_resetCascada('ua');
            const idUa = $(this).val();
            if (!idUa) return;
            const r = await fetch(`${base_url}/admin/get/sedes/${idUa}`);
            const j = await r.json();
            if (j.code === 200) j.data.forEach(({
                id,
                nombre
            }) => $('#nu_sede').append(new Option(nombre, id)));
        });
        $('#nu_sede').on('change', async function() {
            nu_resetCascada('sede');
            const idSede = $(this).val();
            if (!idSede) return;
            const r = await fetch(`${base_url}/admin/get/programas/${idSede}`);
            const j = await r.json();
            if (j.code === 200) j.data.forEach(({
                id,
                nombre
            }) => $('#nu_programa').append(new Option(nombre, id)));
        });
        $('#nu_programa').on('change', async function() {
            nu_resetCascada('prog');
            const idProg = $(this).val();
            if (!idProg) return;
            const r = await fetch(`${base_url}/admin/get/niveles/${idProg}`);
            const j = await r.json();
            if (j.code === 200) j.data.forEach(({
                id,
                nombre
            }) => $('#nu_nivel').append(new Option(nombre, id)));
        });
        $('#nu_nivel').on('change', async function() {
            nu_resetCascada('nivel');
            const idNivel = $(this).val();
            if (!idNivel) return;
            const r = await fetch(`${base_url}/admin/get/modalidades/${idNivel}`);
            const j = await r.json();
            if (j.code === 200) j.data.forEach(({
                id,
                nombre
            }) => $('#nu_modalidad').append(new Option(nombre, id)));
        });
        $('#form_nuevo_usuario').on('submit', async function(e) {
            e.preventDefault();
            $('#btn_nu_guardar').prop('disabled', true);
            try {
                const fd = new FormData(this);
                const resp = await fetch(`${base_url}/admin/guarda/nuevo/usuario`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    body: fd
                });
                const data = await resp.json();
                if (data.code === 200) {
                    $('#modal_nuevo_usuario').modal('hide');
                    this.reset();
                    $('#nu_des, #nu_ua, #nu_sede, #nu_programa, #nu_nivel, #nu_modalidad').val(null).trigger(
                        'change');
                    tabla.ajax.reload(null, false);
                    swal('¡Correcto!', data.mensaje, 'success');
                } else if (data.code === 411 && data.errors) {
                    const first = Object.values(data.errors)[0][0];
                    swal('¡Error!', first, 'error');
                } else {
                    swal('¡Error!', data.mensaje || 'No se pudo guardar.', 'error');
                }
            } catch (_) {
                swal('¡Error!', 'Problema de red al guardar.', 'error');
            } finally {
                $('#btn_nu_guardar').prop('disabled', false);
            }
        });

        /* ================== Editar (modal) ================== */
        function ensureSelect2($scope, $parentModal) {
            $scope.each(function() {
                const $s = $(this);
                if (!$s.hasClass('select2-hidden-accessible')) {
                    $s.select2({
                        dropdownParent: $parentModal,
                        width: 'resolve',
                        allowClear: true,
                        placeholder: ''
                    });
                }
            });
        }

        function fillSelect($sel, items = [], selectedId = '') {
            const frag = document.createDocumentFragment();
            const empty = new Option('', '', false, false);
            empty.value = '';
            frag.appendChild(empty);

            items.forEach(({
                id,
                nombre
            }) => {
                frag.appendChild(new Option(nombre, String(id), false, String(id) === String(selectedId)));
            });

            const el = $sel[0];
            el.innerHTML = '';
            el.appendChild(frag);
            $sel.val(String(selectedId || ''));
        }


        function refreshSelect2All() {
            $('#eu_des, #eu_ua, #eu_sede, #eu_programa, #eu_nivel, #eu_modalidad').each(function() {
                if ($(this).hasClass('select2-hidden-accessible')) {
                    $(this).trigger('change.select2');
                }
            });
        }


        async function getDesCached(tipo) {
            if (!listsCache.des[tipo]) {
                const r = await fetch(`${base_url}/admin/get/des-o-dependencias/${tipo}`);
                const j = await r.json();
                listsCache.des[tipo] = j.data || [];
            }
            return listsCache.des[tipo];
        }
        async function getUaCached(idDes) {
            if (!listsCache.ua[idDes]) {
                const r = await fetch(`${base_url}/admin/get/unidades/${idDes}`);
                const j = await r.json();
                listsCache.ua[idDes] = j.data || [];
            }
            return listsCache.ua[idDes];
        }
        async function getSedesCached(idUa) {
            if (!listsCache.sedes[idUa]) {
                const r = await fetch(`${base_url}/admin/get/sedes/${idUa}`);
                const j = await r.json();
                listsCache.sedes[idUa] = j.data || [];
            }
            return listsCache.sedes[idUa];
        }
        async function getProgramasCached(idSede) {
            if (!listsCache.programas[idSede]) {
                const r = await fetch(`${base_url}/admin/get/programas/${idSede}`);
                const j = await r.json();
                listsCache.programas[idSede] = j.data || [];
            }
            return listsCache.programas[idSede];
        }
        async function getNivelesCached(idProg) {
            if (!listsCache.niveles[idProg]) {
                const r = await fetch(`${base_url}/admin/get/niveles/${idProg}`);
                const j = await r.json();
                listsCache.niveles[idProg] = j.data || [];
            }
            return listsCache.niveles[idProg];
        }
        async function getModalidadesCached(idNivel) {
            if (!listsCache.modalidades[idNivel]) {
                const r = await fetch(`${base_url}/admin/get/modalidades/${idNivel}`);
                const j = await r.json();
                listsCache.modalidades[idNivel] = j.data || [];
            }
            return listsCache.modalidades[idNivel];
        }

        function remember(tipo) {
            if (!tipo) return;
            formMemory[tipo] = {
                des: $('#eu_des').val() || '',
                ua: $('#eu_ua').val() || '',
                sede: $('#eu_sede').val() || '',
                programa: $('#eu_programa').val() || '',
                nivel: $('#eu_nivel').val() || '',
                modalidad: $('#eu_modalidad').val() || ''

            };
        }

        async function restore(tipo) {
            const snap = formMemory[tipo] || {};
            prefilling = true;

            // Mostrar/ocultar cascada
            const esDep = (tipo === '2');
            $('#eu_div_des').prop('hidden', esDep);
            $('#eu_labelDes').text(esDep ? 'Dependencia' : 'AC');

            // Siempre cargar DES del tipo actual
            const desList = await getDesCached(tipo);
            fillSelect($('#eu_des'), desList, snap.des);

            // 🔴 IMPORTANTE: si es Dependencia, NO toques la cascada (se conserva).
            if (esDep) {
                prefilling = false;
                return;
            }

            // AC: carga TODO en paralelo usando lo que haya en memoria
            const [uaList, sedList, progList, nivList, modList] = await Promise.all([
                snap.des ? getUaCached(snap.des) : Promise.resolve([]),
                snap.ua ? getSedesCached(snap.ua) : Promise.resolve([]),
                snap.sede ? getProgramasCached(snap.sede) : Promise.resolve([]),
                snap.programa ? getNivelesCached(snap.programa) : Promise.resolve([]),
                snap.nivel ? getModalidadesCached(snap.nivel) : Promise.resolve([]),
            ]);

            fillSelect($('#eu_ua'), uaList, snap.ua);
            fillSelect($('#eu_sede'), sedList, snap.sede);
            fillSelect($('#eu_programa'), progList, snap.programa);
            fillSelect($('#eu_nivel'), nivList, snap.nivel);
            fillSelect($('#eu_modalidad'), modList, snap.modalidad);

            prefilling = false;
        }

        /* --- abrir modal editar --- */
        async function abrirEditarUsuario(id) {
            try {
                const $modal = $('#modal_editar_usuario');

                // aborta carga anterior y limpia memoria
                if (editAbort) editAbort.abort();
                editAbort = new AbortController();
                const signal = editAbort.signal;
                const myLoad = ++editLoadId;
                clearEditState();

                // UI
                $('#form_editar_usuario')[0].reset();
                ['#eu_des', '#eu_ua', '#eu_sede', '#eu_programa', '#eu_nivel', '#eu_modalidad']
                .forEach(sel => $(sel).empty().append('<option value=""></option>'));
                $('#eu_id_usuario').val(id);
                $modal.modal('show');
                ensureSelect2($('#eu_des, #eu_ua, #eu_sede, #eu_programa, #eu_nivel, #eu_modalidad'), $modal);
                requestIdleCallback?.(() => {
                    getDesCached('1');
                    getDesCached('2');
                });
                setTimeout(() => {
                    getDesCached('1');
                    getDesCached('2');
                }, 0); // fallback
                // fetch info
                const resp = await fetch(`${base_url}/admin/get/informacion/usuario/${id}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin',
                    signal
                });
                const ct = resp.headers.get('content-type') || '';
                if (!ct.includes('application/json')) {
                    const html = await resp.text();
                    console.error('Respuesta no JSON:', html);
                    throw new Error('El endpoint devolvió HTML/redirect. Revisa la ruta o la sesión.');
                }
                const {
                    code,
                    data: u,
                    mensaje
                } = await resp.json();
                if (code !== 200) throw new Error(mensaje || 'No se pudo cargar el usuario');
                if (signal.aborted || myLoad !== editLoadId) return;

                // campos planos
                $('#eu_name').val(u.name || '');
                $('#eu_usuario').val(u.usuario || '');
                $('#eu_email').val(u.email || '');

                // tipo y visibilidad
                const tipo = String(u.tipo_mejora || '');
                $('#eu_tipo').val(tipo);
                if (tipo === '2') {
                    $('#eu_div_des').attr('hidden', true);
                    $('#eu_labelDes').text('Dependencia');
                } else {
                    $('#eu_div_des').removeAttr('hidden');
                    $('#eu_labelDes').text('AC');
                }

                // listas en paralelo
                prefilling = true;
                const [desRes, uaRes, sedesRes, progsRes, nivelesRes, modsRes] = await Promise.all([
                    tipo ? fetch(`${base_url}/admin/get/des-o-dependencias/${tipo}`, {
                        signal
                    }).then(r => r.json()) : {
                        data: []
                    },
                    u.id_des ? fetch(`${base_url}/admin/get/unidades/${u.id_des}`, {
                        signal
                    }).then(r => r.json()) : {
                        data: []
                    },
                    u.id_ua ? fetch(`${base_url}/admin/get/sedes/${u.id_ua}`, {
                        signal
                    }).then(r => r.json()) : {
                        data: []
                    },
                    u.id_sede ? fetch(`${base_url}/admin/get/programas/${u.id_sede}`, {
                        signal
                    }).then(r => r.json()) : {
                        data: []
                    },
                    u.id_programa ? fetch(`${base_url}/admin/get/niveles/${u.id_programa}`, {
                        signal
                    }).then(r => r.json()) : {
                        data: []
                    },
                    u.id_nivel ? fetch(`${base_url}/admin/get/modalidades/${u.id_nivel}`, {
                        signal
                    }).then(r => r.json()) : {
                        data: []
                    },
                ]);
                if (signal.aborted || myLoad !== editLoadId) return;

                // pinta selects
                fillSelect($('#eu_des'), desRes.data || [], u.id_des);
                fillSelect($('#eu_ua'), uaRes.data || [], u.id_ua);
                fillSelect($('#eu_sede'), sedesRes.data || [], u.id_sede);
                fillSelect($('#eu_programa'), progsRes.data || [], u.id_programa);
                fillSelect($('#eu_nivel'), nivelesRes.data || [], u.id_nivel);
                fillSelect($('#eu_modalidad'), modsRes.data || [], u.id_modalidad);
                refreshSelect2All();
                // snapshot inicial
                currentTipo = tipo;
                remember(currentTipo);
            } catch (e) {
                if (e.name === 'AbortError') return;
                swal('¡Error!', e.message || 'No se pudo abrir el editor.', 'error');
                $('#modal_editar_usuario').modal('hide');
            } finally {
                prefilling = false;
            }
        }

        // limpiar estado al cerrar el modal
        $('#modal_editar_usuario').on('hidden.bs.modal', function() {
            if (editAbort) {
                editAbort.abort();
                editAbort = null;
            }
            clearEditState();
        });

        /* --- memoria por tipo: cambio de tipo --- */
        $('#eu_tipo').on('change', async function() {
            const nuevo = $(this).val() || '';
            if (nuevo === currentTipo) return;
            remember(currentTipo);
            currentTipo = nuevo;
            await restore(currentTipo);
        });

        /* --- cascadas del editor (UN SOLO SET) --- */
        $('#eu_des').on('change', async function() {
            if (prefilling) return;
            const idDes = $(this).val() || '';
            // limpiar abajo
            fillSelect($('#eu_ua'), [], '');
            fillSelect($('#eu_sede'), [], '');
            fillSelect($('#eu_programa'), [], '');
            fillSelect($('#eu_nivel'), [], '');
            fillSelect($('#eu_modalidad'), [], '');
            // cargar UA si hay DES
            if (idDes) {
                const list = await getUaCached(idDes);
                fillSelect($('#eu_ua'), list, '');
            }
            remember(currentTipo);
        });
        $('#eu_ua').on('change', async function() {
            if (prefilling) return;
            const idUa = $(this).val() || '';
            fillSelect($('#eu_sede'), [], '');
            fillSelect($('#eu_programa'), [], '');
            fillSelect($('#eu_nivel'), [], '');
            fillSelect($('#eu_modalidad'), [], '');
            if (idUa) {
                const list = await getSedesCached(idUa);
                fillSelect($('#eu_sede'), list, '');
            }
            remember(currentTipo);
        });
        $('#eu_sede').on('change', async function() {
            if (prefilling) return;
            const idSede = $(this).val() || '';
            fillSelect($('#eu_programa'), [], '');
            fillSelect($('#eu_nivel'), [], '');
            fillSelect($('#eu_modalidad'), [], '');
            if (idSede) {
                const list = await getProgramasCached(idSede);
                fillSelect($('#eu_programa'), list, '');
            }
            remember(currentTipo);
        });
        $('#eu_programa').on('change', async function() {
            if (prefilling) return;
            const idProg = $(this).val() || '';
            fillSelect($('#eu_nivel'), [], '');
            fillSelect($('#eu_modalidad'), [], '');
            if (idProg) {
                const list = await getNivelesCached(idProg);
                fillSelect($('#eu_nivel'), list, '');
            }
            remember(currentTipo);
        });
        $('#eu_nivel').on('change', async function() {
            if (prefilling) return;
            const idNivel = $(this).val() || '';
            fillSelect($('#eu_modalidad'), [], '');
            if (idNivel) {
                const list = await getModalidadesCached(idNivel);
                fillSelect($('#eu_modalidad'), list, '');
            }
            remember(currentTipo);
        });
        $('#eu_modalidad').on('change', function() {
            if (prefilling) return;
            remember(currentTipo);
        });
        $('#eu_name, #eu_usuario').on('input', function() {
            if (prefilling) return;
            remember(currentTipo);
        });

        /* --- guardar edición --- */
        $('#form_editar_usuario').on('submit', async function(e) {
            e.preventDefault();
            $('#eu_btn_guardar').prop('disabled', true);
            try {
                const fd = new FormData(this);
                const resp = await fetch(`${base_url}/admin/edita/usuario`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    body: fd
                });
                const data = await resp.json();
                if (data.code === 200) {
                    $('#modal_editar_usuario').modal('hide');
                    swal('¡Correcto!', data.mensaje, 'success');
                    tabla?.ajax?.reload(null, false);
                } else if (data.code === 411 && data.errors) {
                    const first = Object.values(data.errors)[0][0];
                    swal('¡Error!', first, 'error');
                } else {
                    swal('¡Error!', data.mensaje || 'No se pudo actualizar.', 'error');
                }
            } catch (_) {
                swal('¡Error!', 'Problema de red al actualizar.', 'error');
            } finally {
                $('#eu_btn_guardar').prop('disabled', false);
            }
        });

        // reset desde el modal de edición
        $('#eu_btn_reset').on('click', function() {
            const id = $('#eu_id_usuario').val();
            $('#reset_user_id').val(id);
            $('#form_reset')[0].reset();
            $('#modal_reset').modal('show');
        });
    </script>
@endsection
