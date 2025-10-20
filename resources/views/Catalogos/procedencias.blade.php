@extends('app')
@section('htmlheader_title')
    Catalogos - Procedencias
@endsection

@push('styles')
    <link rel="stylesheet"
        href="{{ asset('dist/css/forms-modern.css') }}?v={{ filemtime(public_path('dist/css/forms-modern.css')) }}">
@endpush

@section('main-content')
    <section class="content-header">
        <h1 style="text-align: center; margin: 15px 0;">Catalogos - Procedencias</h1>
    </section>

    <div class="col-xs-12 list-narrow">
        <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title">Listado</h3>
            </div>
            <div class="box-body table-tight" style="padding-top: 2rem;">
                <div class="row">
                    <div class="col-md-12" style="display:flex; gap:8px; justify-content:flex-end;">
                        <button class="btn btn-success" onclick="abreModalAgregar();">
                            <i class="fa fa-plus-circle"></i> Agregar
                        </button>

                    </div>

                    <div class="col-md-12" id="div_tabla">
                        <!-- placeholder; se reemplaza en JS -->
                        <table class="table table-bordered table-striped compact" id="tabla_procedencias"
                            style="width:100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Procedencia</th>
                                    <th>Siglas</th>
                                    <th>Acciones</th>
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
    <div class="modal fade" id="modal_edita_procedencia" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Edita procedencia</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <form id="form_edita_procedencia">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="procedencia_edit">Procedencia</label>
                                    <input type="text" name="procedencia_edit" id="procedencia_edit" class="form-control"
                                        placeholder="Máximo 100 caracteres...">
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="siglas_edit">Siglas</label>
                                    <input type="text" name="siglas_edit" id="siglas_edit" class="form-control"
                                        placeholder="Máximo 100 caracteres...">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="modal-footer" id="container_btns_modal_confirmacion">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success" id="btn_edita" onclick="editaProcedencia()">Guardar
                        cambios</button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL NUEVA --}}
    <div class="modal fade" id="modal_nueva_procedencia" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Nueva procedencia</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <form id="form_nueva_procedencia">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="procedencia">Procedencia</label>
                                    <input type="text" name="procedencia" id="procedencia" class="form-control"
                                        placeholder="Máximo 100 caracteres...">
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="siglas">Siglas</label>
                                    <input type="text" name="siglas" id="siglas" class="form-control"
                                        placeholder="Máximo 100 caracteres...">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success" id="btn_guarda"
                        onclick="guardaProcedencia(); this.disabled=true;">Guardar</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('localscripts')
    <script>
        var base_url = $("input[name='base_url']").val();
        var id;
        let dt = null; // referencia global para destruir/recrear

        const safe = (v) => (v ?? '').toString().replace(/'/g, "\\'"); // escapa comillas simples para inline

        const pintaTablaVacia = () => {
            $("#div_tabla").html(`
      <table class="table table-bordered table-striped compact" id="tabla_procedencias" style="width:100%">
        <thead>
          <tr>
            <th>#</th>
            <th>Procedencia</th>
            <th>Siglas</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    `);
        };

        const getProcedencias = async () => {
            pintaTablaVacia();
            const response = await fetch(`${base_url}/admin/get/catalogo/procedencia`, {
                method: 'GET'
            });
            const {
                data = []
            } = await response.json();

            // Destruye instancia previa si existe
            if (dt && typeof dt.destroy === 'function') {
                dt.destroy();
                dt = null;
            }

            const excelBtnHTML = `
  <span class="excel-badge">X</span>
  <span class="excel-text">Exportar a excel</span>
`;

            // Inicializa con el constructor VANILLA de DataTables v2
            dt = new DataTable('#tabla_procedencias', {
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
                order: [
                    [1, 'asc']
                ],
                layout: {
                    topStart: 'pageLength',
                    topEnd: 'buttons',
                    bottomStart: 'info',
                    bottomEnd: 'paging'
                },

                buttons: [{
                    extend: 'excelHtml5',
                    name: 'excel',
                    className: 'btn btn-outline-excel', // cambia a 'btn btn-excel' si prefieres la sólida
                    titleAttr: 'Exportar a Excel',
                    text: excelBtnHTML,
                    title: 'Catalogo_Procedencias',
                    exportOptions: {
                        columns: [1, 2],
                        modifier: {
                            search: 'applied',
                            page: 'all'
                        }
                    }
                }],
                columns: [{
                        title: "#",
                        data: null,
                        orderable: false,
                        visible: false,
                        className: "text-center",
                        render: (data, type, row, meta) => meta.row + meta.settings._iDisplayStart + 1
                    },
                    {
                        title: "Procedencia",
                        data: 'descripcion',
                        defaultContent: ''
                    },
                    {
                        title: "Siglas",
                        data: 'siglas',
                        defaultContent: ''
                    },
                    {
                        title: 'Acciones',
                        data: null,
                        orderable: false,
                        className: 'text-center dt-actions',
                        render: (_, __, o) => `
            <div class="btn-actions" style="display:flex;gap:6px;justify-content:center;">
              <button class="btn btn-primary btn-icon" title="Editar"
                onclick="abreModal('${o.id}', '${safe(o.descripcion)}', '${safe(o.siglas)}')">
                <i class="fa fa-pencil"></i>
              </button>
              <button class="btn btn-danger btn-icon" title="Eliminar"
                onclick="confirmaElimina('${o.id}')">
                <i class="fa fa-trash"></i>
              </button>
            </div>`
                    }
                ]
            });

            // Botón externo (opcional)
            const btn = document.getElementById('btn_export_xlsx');
            if (btn) {
                btn.onclick = () => dt.button('excel:name').trigger();
            }
        };


        const abreModal = async (id_pro, texto, sigla) => {
            id = id_pro;
            $("#procedencia_edit").val(texto || '');
            $("#siglas_edit").val((sigla === null || sigla == 'null') ? '' : (sigla || ''));
            $("#modal_edita_procedencia").modal();
        };

        const editaProcedencia = async () => {
            const body = new FormData(document.getElementById('form_edita_procedencia'));
            const response = await fetch(`${ base_url }/admin/edita/catalogo/procedencia/${ id }`, {
                method: 'post',
                body,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            const data = await response.json();

            if (data.code == 200) {
                $("#modal_edita_procedencia").modal('hide');
                getProcedencias();
                $("#procedencia_edit").val("");
                $("#siglas_edit").val("");
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
            $("#procedencia").val("");
            $("#siglas").val("");
            $("#modal_nueva_procedencia").modal();
        };

        const guardaProcedencia = async () => {
            const body = new FormData(document.getElementById('form_nueva_procedencia'));
            const response = await fetch(`${ base_url }/admin/catalogo/nueva/procedencia`, {
                method: 'post',
                body,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            const data = await response.json();

            if (data.code == 200) {
                $("#modal_nueva_procedencia").modal('hide');
                getProcedencias();
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
                if (isConfirm) eliminaProcedencia(id);
            });
        };

        const eliminaProcedencia = async (id) => {
            const response = await fetch(`${ base_url }/admin/catalogo/elimina/procedencia/${ id }`, {
                method: 'delete',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            const data = await response.json();

            setTimeout(() => {
                if (data.code == 200) {
                    swal("¡Correcto!", data.mensaje, "success");
                    getProcedencias();
                } else {
                    swal("¡Error!", data.mensaje, "error");
                }
            }, 200);
        };

        const setUsuario = (valor) => {
            $("#usuario").val(valor)
        };

        // Carga inicial
        document.addEventListener('DOMContentLoaded', () => {
            // Diagnóstico mínimo
            try {
                console.log('DT version:', (window.DataTable && DataTable.version) || 'missing');
            } catch (e) {}
            getProcedencias();
        });
    </script>
@endsection
