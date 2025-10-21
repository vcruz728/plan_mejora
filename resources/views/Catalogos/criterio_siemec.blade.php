@extends('app')

@php
    $rol = (int) (Auth::user()->rol ?? 0);
    $canManage = $rol === 1; // sólo rol 1 puede agregar/editar/eliminar
@endphp

@section('htmlheader_title')
    Catalogos - Criterios SIEMEC
@endsection

@push('styles')
    <link rel="stylesheet"
        href="{{ asset('dist/css/forms-modern.css') }}?v={{ filemtime(public_path('dist/css/forms-modern.css')) }}">
@endpush

@section('main-content')
    <section class="content-header">
        <h1 style="text-align: center; margin: 15px 0;">Criterios SIEMEC</h1>
    </section>

    <div class="col-xs-12 list-narrow">
        <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title">Listado</h3>
            </div>
            <div class="box-body table-tight" style="padding-top: 2rem;">
                <div class="row">
                    <div class="col-md-12" style="display:flex; gap:8px; justify-content:flex-end;">
                        @if ($canManage)
                            <button class="btn btn-success" onclick="abreModalAgregar();">
                                <i class="fa fa-plus-circle"></i> Agregar
                            </button>
                        @endif
                    </div>

                    <div class="col-md-12" id="div_tabla">
                        <table class="table table-bordered table-striped compact" id="tabla_criterios" style="width:100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Criterio SIEMEC</th>
                                    @if ($canManage)
                                        <th>Acciones</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- MODALES sólo si puede gestionar --}}
    @if ($canManage)
        {{-- MODAL EDITA --}}
        <div class="modal fade" id="modal_edita_criterio" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title">Edita criterio SIEMEC</h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <form id="form_edita_criterio">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="criterio_edit">Criterio SIEMEC</label>
                                        <input type="text" name="criterio_edit" id="criterio_edit" class="form-control"
                                            placeholder="Máximo 50 caracteres...">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="modal-footer" id="container_btns_modal_confirmacion">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-success" id="btn_edita" onclick="editaCriterio()">Guardar
                            cambios</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- MODAL NUEVO --}}
        <div class="modal fade" id="modal_nuevo_criterio" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title">Nuevo criterio SIEMEC</h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <form id="form_nuevo_criterio">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="criterio">Criterio SIEMEC</label>
                                        <input type="text" name="criterio" id="criterio" class="form-control"
                                            placeholder="Máximo 50 caracteres...">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-success" id="btn_guarda"
                            onclick="guardaCriterio(); this.disabled=true;">Guardar</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('localscripts')
    <script>
        const CAN_MANAGE = @json($canManage);

        var base_url = $("input[name='base_url']").val();
        var id;
        let dt = null;

        const safe = (v) => (v ?? '').toString().replace(/'/g, "\\'");

        const pintaTablaVacia = () => {
            const accionesTh = CAN_MANAGE ? '<th>Acciones</th>' : '';
            $("#div_tabla").html(`
                <table class="table table-bordered table-striped compact" id="tabla_criterios" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Criterio SIEMEC</th>
                            ${accionesTh}
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            `);
        };

        const getCriterios = async () => {
            pintaTablaVacia();

            const response = await fetch(`${base_url}/admin/get/catalogo/criterios-siemec`, {
                method: 'GET'
            });
            const {
                data = []
            } = await response.json();

            if (dt && typeof dt.destroy === 'function') {
                dt.destroy();
                dt = null;
            }

            const excelBtnHTML = `
                <span class="excel-badge">X</span>
                <span class="excel-text">Exportar a excel</span>
            `;

            const columns = [{
                    title: "#",
                    data: null,
                    orderable: false,
                    visible: false,
                    className: "text-center",
                    render: (data, type, row, meta) => meta.row + meta.settings._iDisplayStart + 1
                },
                {
                    title: "Criterio SIEMEC",
                    data: 'descripcion',
                    defaultContent: ''
                }
            ];

            if (CAN_MANAGE) {
                columns.push({
                    title: 'Acciones',
                    data: null,
                    orderable: false,
                    className: 'text-center dt-actions',
                    render: (_, __, o) => `
                        <div class="btn-actions" style="display:flex;gap:6px;justify-content:center;">
                          <button class="btn btn-primary btn-icon" title="Editar"
                            onclick="abreModal('${o.id}', '${safe(o.descripcion)}')">
                            <i class="fa fa-pencil"></i>
                          </button>
                          <button class="btn btn-danger btn-icon" title="Eliminar"
                            onclick="confirmaElimina('${o.id}')">
                            <i class="fa fa-trash"></i>
                          </button>
                        </div>`
                });
            }

            dt = new DataTable('#tabla_criterios', {
                data,
                deferRender: true,
                autoWidth: false,
                searching: true,
                ordering: true,
                info: true,
                paging: true,
                pageLength: 10,
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, 'Todos']
                ],
                language: {
                    url: base_url + '/js/Spanish.json'
                },
                search: {
                    placeholder: 'Buscar…'
                },
                order: [
                    [1, 'asc']
                ],
                layout: {
                    topStart: ['pageLength'],
                    topEnd: ['search', 'buttons'],
                    bottomStart: 'info',
                    bottomEnd: 'paging'
                },
                buttons: [{
                    extend: 'excelHtml5',
                    name: 'excel',
                    className: 'btn btn-outline-excel',
                    titleAttr: 'Exportar a Excel',
                    text: excelBtnHTML,
                    title: 'Catalogo_Criterios_SIEMEC',
                    exportOptions: {
                        columns: [1],
                        modifier: {
                            search: 'applied',
                            page: 'all'
                        }
                    }
                }],
                columns
            });

            const btn = document.getElementById('btn_export_xlsx');
            if (btn) btn.onclick = () => dt.button('excel:name').trigger();
        };

        // Funciones de CRUD (sólo se disparan si CAN_MANAGE = true)
        const abreModal = async (id_pro, texto) => {
            if (!CAN_MANAGE) return;
            id = id_pro;
            $("#criterio_edit").val(texto || '');
            $("#modal_edita_criterio").modal();
        };

        const editaCriterio = async () => {
            const body = new FormData(document.getElementById('form_edita_criterio'));
            const response = await fetch(`${ base_url }/admin/edita/catalogo/criterio-siemec/${ id }`, {
                method: 'post',
                body,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            const data = await response.json();

            if (data.code == 200) {
                $("#modal_edita_criterio").modal('hide');
                getCriterios();
                $("#criterio_edit").val("");
                swal("¡Correcto!", data.mensaje, "success");
            } else if (data.code == 411) {
                let num = 0;
                $.each(data.errors, function(_, value) {
                    if (num++ == 0) swal("¡Error!", value[0], "error");
                });
            } else {
                swal("¡Error!", data.mensaje, "error");
            }
            $("#btn_edita").removeAttr("disabled");
        };

        const abreModalAgregar = () => {
            if (!CAN_MANAGE) return;
            $("#criterio").val("");
            $("#modal_nuevo_criterio").modal();
        };

        const guardaCriterio = async () => {
            const body = new FormData(document.getElementById('form_nuevo_criterio'));
            const response = await fetch(`${ base_url }/admin/catalogo/nuevo/criterio-siemec`, {
                method: 'post',
                body,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            const data = await response.json();

            if (data.code == 200) {
                $("#modal_nuevo_criterio").modal('hide');
                getCriterios();
                swal("¡Correcto!", data.mensaje, "success");
            } else if (data.code == 411) {
                let num = 0;
                $.each(data.errors, function(_, value) {
                    if (num++ == 0) swal("¡Error!", value[0], "error");
                });
            } else {
                swal("¡Error!", data.mensaje, "error");
            }
            $("#btn_guarda").removeAttr("disabled");
        };

        const confirmaElimina = (id) => {
            if (!CAN_MANAGE) return;
            swal({
                title: "¿Está seguro?",
                text: "El registro se eliminará de forma permanente.",
                type: "warning",
                showCancelButton: true,
                canceluttonColor: '#FFFFFF',
                confirmButtonColor: '#dd4b39',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
            }, async function(isConfirm) {
                if (isConfirm) eliminaCriterio(id);
            });
        };

        const eliminaCriterio = async (id) => {
            const response = await fetch(`${ base_url }/admin/catalogo/elimina/criterio-siemec/${ id }`, {
                method: 'delete',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            const data = await response.json();

            setTimeout(() => {
                if (data.code == 200) {
                    swal("¡Correcto!", data.mensaje, "success");
                    getCriterios();
                } else {
                    swal("¡Error!", data.mensaje, "error");
                }
            }, 200);
        };

        document.addEventListener('DOMContentLoaded', () => {
            getCriterios();
        });
    </script>
@endsection
