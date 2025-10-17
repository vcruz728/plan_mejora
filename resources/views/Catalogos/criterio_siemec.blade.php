@extends('app')
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
    <!-- search container -->

    <div class="col-xs-12 list-narrow">
        <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title">Listado</h3>
            </div>
            <div class="box-body table-tight" style="padding-top: 2rem;">
                <div class="row">
                    <div class="col-md-12" style="display: flex; justify-content: flex-end;">
                        <button class="btn btn-success" onclick="abreModalAgregar();"><i class="fa fa-plus-circle"></i>
                            Agregar</button>
                    </div>
                    <div class="col-md-12" id="div_tabla">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Criterio SIEMEC</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>


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
@endsection
@section('localscripts')
    <script>
        var base_url = $("input[name='base_url']").val();
        var id

        const getCriterios = async () => {
            $("#div_tabla").html("<h4>Cargando..</h4>");
            const response = await fetch(`${ base_url }/admin/get/catalogo/criterios-siemec`, {
                method: 'GET'
            });

            const data = await response.json();
            $("#div_tabla").html(`<table class="table table-bordered table-striped compact" id="tabla_usuarios">
      </table>`);


            const table = $("#tabla_usuarios").DataTable({
                data: data.data,
                scrollX: true,
                searching: true,
                ordering: true,
                info: false,
                paging: false,
                autoWidth: true,
                language: {
                    url: base_url + '/js/Spanish.json'
                },
                columns: [{
                        title: "Criterio SIEMEC",
                        data: 'descripcion'
                    },
                    {
                        title: 'Acciones',
                        defaultContent: '',
                        data: null,
                        orderable: false,
                        className: 'text-center dt-actions',
                        fnCreatedCell: (nTd, sData, oData, iRow, iCol) => {
                            $(nTd).append(`
                        <div class="btn-actions">
                          <button class="btn btn-primary btn-icon" title="Editar" onclick="abreModal('${ oData.id }', '${ oData.descripcion }')"><i class="fa fa-pencil"></i></button>
                          <button class="btn btn-danger btn-icon" title="Eliminar"  onclick="confirmaElimina('${ oData.id }')"><i class="fa fa-trash"></i></button>
                        </div>`);
                        }
                    }
                ],
            });
        }

        const abreModal = async (id_pro, texto) => {
            id = id_pro

            $("#criterio_edit").val(texto)

            $("#modal_edita_criterio").modal()
        }

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
                $("#modal_edita_criterio").modal('hide')
                getCriterios()
                $("#criterio_edit").val("")
                swal("¡Correcto!", data.mensaje, "success");
            } else if (data.code == 411) {
                var num = 0;
                $.each(data.errors, function(key, value) {
                    if (num == 0) {
                        swal("¡Error!", value[0], "error");
                    }
                    num++;
                });
            } else {
                swal("¡Error!", data.mensaje, "error");
            }
            $("#btn_edita").removeAttr("disabled")
        }

        const abreModalAgregar = () => {
            $("#criterio").val("")
            $("#modal_nuevo_criterio").modal();
        }


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
                $("#modal_nuevo_criterio").modal('hide')
                getCriterios()
                swal("¡Correcto!", data.mensaje, "success");
            } else if (data.code == 411) {
                var num = 0;
                $.each(data.errors, function(key, value) {
                    if (num == 0) {
                        swal("¡Error!", value[0], "error");
                    }
                    num++;
                });
            } else {
                swal("¡Error!", data.mensaje, "error");
            }
            $("#btn_guarda").removeAttr("disabled")
        }

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
                },
                async function(isConfirm) {
                    if (isConfirm) {
                        eliminaCriterio(id)
                    }
                });
        }

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
            }, "200");

        }


        getCriterios()


        const setUsuario = (valor) => {
            $("#usuario").val(valor)
        }
    </script>
@endsection
