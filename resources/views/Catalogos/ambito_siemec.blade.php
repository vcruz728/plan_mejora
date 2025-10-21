@extends('app')
@php
    $rol = (int) (Auth::user()->rol ?? 0);
    $canManage = $rol === 1;
@endphp

@section('htmlheader_title')
    Catalogos - Ámbitos SIEMEC
@endsection

@push('styles')
    <link rel="stylesheet"
        href="{{ asset('dist/css/forms-modern.css') }}?v={{ filemtime(public_path('dist/css/forms-modern.css')) }}">
@endpush



@section('main-content')
    <section class="content-header">
        <h1 style="text-align: center; margin: 15px 0;">Catalogos - Ámbitos SIEMEC</h1>
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
                        <!-- placeholder; se reemplaza en JS -->
                        <table class="table table-bordered table-striped compact" id="tabla_ambitos" style="width:100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Ámbito SIEMEC</th>
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

    {{-- MODAL EDITA --}}
    @if ($canManage)
        <div class="modal fade" id="modal_edita_ambito" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title">Edita ámbito SIEMEC</h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <form id="form_edita_ambito">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="ambito_edit">Ámbito SIEMEC</label>
                                        <input type="text" name="ambito_edit" id="ambito_edit" class="form-control"
                                            placeholder="Máximo 50 caracteres...">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="modal-footer" id="container_btns_modal_confirmacion">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-success" id="btn_edita" onclick="editaAmbito()">Guardar
                            cambios</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- MODAL NUEVO --}}
        <div class="modal fade" id="modal_nuevo_ambito" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title">Nuevo ámbito SIEMEC</h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <form id="form_nuevo_ambito">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="ambito">Ámbito SIEMEC</label>
                                        <input type="text" name="ambito" id="ambito" class="form-control"
                                            placeholder="Máximo 50 caracteres...">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-success" id="btn_guarda"
                            onclick="guardaAmbito(); this.disabled=true;">Guardar</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection


@section('localscripts')
    <script>
        const CAN_MANAGE = @json($canManage); // true/false firme
        var base_url = $("input[name='base_url']").val();
        var id;
        let dt = null;

        const safe = (v) => (v ?? '').toString().replace(/'/g, "\\'");

        const pintaTablaVacia = () => {
            const accionesTh = CAN_MANAGE ? '<th>Acciones</th>' : '';
            $("#div_tabla").html(`
            <table class="table table-bordered table-striped compact" id="tabla_ambitos" style="width:100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Ámbito SIEMEC</th>
                        ${accionesTh}
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        `);
        };


        const getAmbitos = async () => {
            pintaTablaVacia();

            const response = await fetch(`${ base_url }/admin/get/catalogo/ambitos-siemec`, {
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
            <span class="excel-text">Exportar a Excel</span>
        `;

            // <<< NUEVO: columnas dinámicas
            const columns = [{
                    title: "#",
                    data: null,
                    orderable: false,
                    visible: false,
                    className: "text-center",
                    render: (data, type, row, meta) => meta.row + meta.settings._iDisplayStart + 1
                },
                {
                    title: "Ámbito SIEMEC",
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

            dt = new DataTable('#tabla_ambitos', {
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
                    title: 'Catalogo_Ambitos_SIEMEC',
                    exportOptions: {
                        columns: [1], // solo la descripción
                        modifier: {
                            search: 'applied',
                            page: 'all'
                        }
                    }
                }],
                columns // <<< usamos las columnas dinámicas
            });

            const btn = document.getElementById('btn_export_xlsx');
            if (btn) btn.onclick = () => dt.button('excel:name').trigger();
        };

        const abreModal = async (id_pro, texto) => {
            id = id_pro;
            $("#ambito_edit").val(texto || '');
            $("#modal_edita_ambito").modal();
        };

        const editaAmbito = async () => {
            const body = new FormData(document.getElementById('form_edita_ambito'));
            const response = await fetch(`${ base_url }/admin/edita/catalogo/ambito-siemec/${ id }`, {
                method: 'post',
                body,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            const data = await response.json();

            if (data.code == 200) {
                $("#modal_edita_ambito").modal('hide');
                getAmbitos();
                $("#ambito_edit").val("");
                swal("¡Correcto!", data.mensaje, "success");
            } else if (data.code == 411) {
                let num = 0;
                $.each(data.errors, function(key, value) {
                    if (num == 0) swal("¡Error!", value[0], "error");
                    num++;
                });
            } else {
                swal("¡Error!", data.mensaje, "error");
            }
            $("#btn_edita").removeAttr("disabled");
        };

        const abreModalAgregar = () => {
            $("#ambito").val("");
            $("#modal_nuevo_ambito").modal();
        };

        const guardaAmbito = async () => {
            const body = new FormData(document.getElementById('form_nuevo_ambito'));
            const response = await fetch(`${ base_url }/admin/catalogo/nuevo/ambito-siemec`, {
                method: 'post',
                body,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            const data = await response.json();

            if (data.code == 200) {
                $("#modal_nuevo_ambito").modal('hide');
                getAmbitos();
                swal("¡Correcto!", data.mensaje, "success");
            } else if (data.code == 411) {
                let num = 0;
                $.each(data.errors, function(key, value) {
                    if (num == 0) swal("¡Error!", value[0], "error");
                    num++;
                });
            } else {
                swal("¡Error!", data.mensaje, "error");
            }
            $("#btn_guarda").removeAttr("disabled");
        };

        const confirmaElimina = (id) => {
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
                if (isConfirm) eliminaAmbito(id);
            });
        };

        const eliminaAmbito = async (id) => {
            const response = await fetch(`${ base_url }/admin/catalogo/elimina/ambito-siemec/${ id }`, {
                method: 'delete',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            const data = await response.json();

            setTimeout(() => {
                if (data.code == 200) {
                    swal("¡Correcto!", data.mensaje, "success");
                    getAmbitos();
                } else {
                    swal("¡Error!", data.mensaje, "error");
                }
            }, 200);
        };

        const setUsuario = (valor) => {
            $("#usuario").val(valor)
        };

        document.addEventListener('DOMContentLoaded', () => {
            try {
                console.log('DT version:', (window.DataTable && DataTable.version) || 'missing');
            } catch (e) {}
            getAmbitos();
        });
    </script>
@endsection
