@extends('app')
@section('htmlheader_title', 'Listar Usuarios')

@push('styles')
    <link rel="stylesheet" href="{{ asset('bower_components/select2/dist/css/select2.min.css') }}">
    <style>
        /* no hacer wrap y mostrar separación real entre botones */
        .btn-actions {
            display: inline-flex;
            gap: 8px;
            /* <-- el “espacio” entre botones */
            align-items: center;
            flex-wrap: nowrap;
            /* evita 2ª fila */
        }

        td.dt-actions {
            white-space: nowrap;
        }

        /* estilo de los icon-badges (puedes reciclar tu .btn-icon existente) */
        .btn-icon {
            width: 34px;
            height: 34px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, .12);
            transition: transform .1s ease-in-out, box-shadow .1s;
        }

        .btn-icon:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, .18);
        }

        .btn-icon i {
            font-size: 16px;
        }
    </style>
@endpush

@section('main-content')
    <section class="content-header">
        <h1>Lista de usuarios</h1>
    </section>

    <div class="col-xs-12">
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
                    style="margin: 12px 0;">
                    <i class="fa fa-plus-circle"></i> Nuevo usuario
                </button>
            </div>

            <div class="box-body">
                <div id="div_tabla" class="table-responsive">
                    <table class="table table-bordered table-striped" id="tabla_usuarios" style="width:100%">
                        <thead>
                            <tr>
                                <th>Usuario</th>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Unidad</th>
                                <th style="width:110px;">Acciones</th>
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

@endsection

@push('scripts')
    <script src="{{ asset('bower_components/select2/dist/js/select2.full.min.js') }}"></script>
@endpush

@section('localscripts')
    <script>
        var base_url = $("input[name='base_url']").val();
        var tabla, idParaReset = null;

        // =============== Helper desbloquear Scroll despues de usar modal ===============
        // --- Parche anti-overlay SweetAlert v1 ---
        function unlockScroll() {
            // Limpia estados de scroll
            $('body')
                .removeClass('stop-scrolling modal-open swal2-shown swal2-height-auto')
                .css({
                    overflow: '',
                    height: '',
                    'padding-right': ''
                });

            // NO eliminar .sweet-overlay: solo ocultarlo y desactivar eventos
            var $ov = $('.sweet-overlay');
            if ($ov.length) {
                $ov.off(); // quita handlers colgados
                $ov.css('display', 'none'); // lo deja oculto
                $ov.css('pointer-events', 'none'); // que no bloquee clics
            }

            // Backdrops de Bootstrap sí se pueden quitar
            $('.modal-backdrop').remove();
        }

        // Refuerzos
        $(document).on('hidden.bs.modal', unlockScroll);
        $(document).on('click', '.sweet-overlay', function() {
            setTimeout(unlockScroll, 0);
        });
        // =============== Select2 (filtros) ===============
        $('.select2').select2({
            placeholder: function() {
                return $(this).data('placeholder')
            },
            allowClear: true,
            width: 'resolve'
        });

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

        async function cargarDes(tipo) {
            $('#f_des').empty().append('<option value=""></option>');
            if (!tipo) return;
            const r = await fetch(`${base_url}/admin/get/des-o-dependencias/${tipo}`);
            const j = await r.json();
            if (j.code === 200) {
                j.data.forEach(({
                    id,
                    nombre
                }) => $('#f_des').append(new Option(nombre, id)));
            }
        }
        async function cargarUa(idDes) {
            $('#f_ua').empty().append('<option value=""></option>');
            if (!idDes) return;
            const r = await fetch(`${base_url}/admin/get/unidades/${idDes}`);
            const j = await r.json();
            if (j.code === 200) {
                j.data.forEach(({
                    id,
                    nombre
                }) => $('#f_ua').append(new Option(nombre, id)));
            }
        }
        async function cargarSedes(idUa) {
            $('#f_sede').empty().append('<option value=""></option>');
            if (!idUa) return;
            const r = await fetch(`${base_url}/admin/get/sedes/${idUa}`);
            const j = await r.json();
            if (j.code === 200) {
                j.data.forEach(({
                    id,
                    nombre
                }) => $('#f_sede').append(new Option(nombre, id)));
            }
        }

        // =============== DataTable ===============
        $.fn.dataTable.ext.errMode = 'none'; // evita el alert de DataTables
        var base_url = $("input[name='base_url']").val();
        var tabla;

        // --- inicializar 1 sola vez
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
                        if (json && json.code === 200) return json.data || [];
                        console.error('API error', json);
                        return [];
                    },
                    error: function(xhr, textStatus) {
                        if (textStatus === 'abort') return; // no mostrar error cuando DT cancela
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
                               <button class="btn btn-primary btn-icon" title="Editar"
                                onclick="location.href='${base_url}/edita/usuario/${id}'">
                                <i class="fa fa-pencil"></i>
                                </button>
                                <button class="btn btn-info btn-icon" title="Resetear contraseña"
                                        onclick="abrirReset('${id}')">
                                <i class="fa fa-key"></i>
                                </button>
                                <button class="btn btn-danger btn-icon" title="Eliminar"
                                        onclick="eliminarUsuario('${id}','${nombre}')">
                                <i class="fa fa-trash"></i>
                                </button>
                            </div>
                            `;
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

        // --- recargar sin re-crear
        function recargar() {
            if (tabla) tabla.ajax.reload(null, false);
        }

        // --- filtros: SOLO recarga
        $('#flt_tipo, #flt_des, #flt_ua, #flt_sede').on('change', function() {
            recargar();
        });

        // botón “Quitar filtros”
        $('#btn_limpiar').on('click', function() {
            $('#flt_tipo').val('').trigger('change.select2');
            $('#flt_des').val('').trigger('change.select2');
            $('#flt_ua').val('').trigger('change.select2');
            $('#flt_sede').val('').trigger('change.select2');
            recargar();
        });

        // init
        $(function() {
            initTabla();
        });
        // =============== Reset password (modal) ===============
        function abrirReset(id) {
            idParaReset = id;
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
            } catch (e) {
                swal('¡Error!', 'Problema de red al resetear.', 'error');
            } finally {
                $('#btn_reset_enviar').prop('disabled', false);
            }
        }

        // =============== Eliminar ===============
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
                } catch (e) {
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

        // =============== Init ===============
        $(function() {
            initTabla();
        });

        // --- Select2 dentro del modal
        $('#modal_nuevo_usuario').on('shown.bs.modal', function() {
            $('#nu_des, #nu_ua, #nu_sede, #nu_programa, #nu_nivel, #nu_modalidad').select2({
                dropdownParent: $('#modal_nuevo_usuario'),
                width: 'resolve',
                allowClear: true,
                placeholder: ''
            });
        });

        // --- helpers para vaciar combos del modal
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

        // --- eventos del modal
        $('#nu_tipo').on('change', async function() {
            const tipo = $(this).val();
            $('#nu_des').empty().append('<option value=""></option>');
            nu_resetCascada('des');

            if (tipo === '2') { // Dependencia
                $('#nu_div_des').attr('hidden', true);
                $('#nu_labelDes').text('Dependencia');
            } else {
                $('#nu_div_des').removeAttr('hidden');
                $('#nu_labelDes').text('AC');
            }

            if (!tipo) return;
            const r = await fetch(`${base_url}/admin/get/des-o-dependencias/${tipo}`);
            const j = await r.json();
            if (j.code === 200) {
                j.data.forEach(({
                    id,
                    nombre
                }) => $('#nu_des').append(new Option(nombre, id)));
            }
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

        // --- guardar nuevo usuario (modal)
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
            } catch (e) {
                swal('¡Error!', 'Problema de red al guardar.', 'error');
            } finally {
                $('#btn_nu_guardar').prop('disabled', false);
            }
        });
    </script>
@endsection
