@extends('app')
@section('htmlheader_title')
    Acciones Plan de mejora
@endsection
@section('main-content')
    <link rel="stylesheet" href="{{ asset('bower_components/select2/css/select2.min.css') }}">
    <section class="content-header">
        <h1>Acciones Plan de mejora</h1>
    </section>
    <!-- search container -->

    <div class="col-xs-12">
        <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title">Formulario <small style="color: #DDDDDD;">
                        <p style="color: red; display: inline;">*</p>campos obligatorios
                    </small></h3>
            </div>
            <div class="box-body" style="padding-top: 2rem;">
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="">Tipo:</label>
                            <br>

                            <input type="radio" id="opcion_uno" disabled name="tipo" value="Recomendación"
                                @if ($plan->tipo == 'Recomendación') checked @endif>
                            <label for="opcion_uno">Recomendación</label>
                            &nbsp;&nbsp;&nbsp;
                            <input type="radio" id="opcion_dos" disabled name="tipo" value="Meta"
                                @if ($plan->tipo == 'Meta') checked @endif>
                            <label for="opcion_dos">Meta</label>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="procedencia">Procedencia</label>
                            <select name="procedencia" id="procedencia" class="form-control" disabled>
                                @foreach ($procedencias as $value)
                                    <option value="{{ $value->id }}" @if ($plan->procedencia == $value->id) selected @endif>
                                        {{ $value->descripcion }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-grup">
                            <label for="plan_no">Plan no.</label>
                            <input type="text" name="plan_no" id="plan_no" class="form-control"
                                value="{{ $plan->plan_no }}" disabled>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="fecha_creacion">Fecha</label>
                            <input type="text" name="fecha_creacion" id="fecha_creacion" class="form-control"
                                value="{{ $plan->fecha_creacion }}" disabled>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="cantidad">Cantidad</label>
                            <input type="number" name="cantidad" id="cantidad" class="form-control"
                                value="{{ $plan->cantidad }}" disabled>
                        </div>
                    </div>



                    <div class="col-md-3">
                        <label for="fecha_vencimiento">Fecha de vencimiento</label>
                        <input type="text" data-provide="datepicker" name="fecha_vencimiento" id="fecha_vencimiento"
                            class="form-control" value="{{ $plan->fecha_vencimiento }}" disabled>
                    </div>

                    <div class="col-md-12"></div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="des">
                                @if ($plan->tipo_mejora == 2)
                                    Dependencia
                                @else
                                    AC
                                @endif
                                <small>
                                    <p class="obligatorio">*</p>
                                </small>
                            </label>
                            <input type="text" name="des" id="des" class="form-control" disabled
                                value="{{ $plan->des }}">
                        </div>
                    </div>

                    <div id="div_des" @if ($plan->tipo_mejora == 2) hidden @endif>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="unidad_academica">Unidad académica <small>
                                        <p class="obligatorio">*</p>
                                    </small></label>
                                <input type="text" name="unidad_academica" id="unidad_academica" class="form-control"
                                    disabled value="{{ $plan->unidad_academica }}">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="sede">Sede<small>
                                        <p class="obligatorio">*</p>
                                    </small></label>
                                <input type="text" name="sede" id="sede" class="form-control" disabled
                                    value="{{ $plan->sede }}">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="programa_educativo">Programa educativo<small>
                                        <p class="obligatorio">*</p>
                                    </small></label>
                                <input type="text" name="programa_educativo" id="programa_educativo"
                                    class="form-control" disabled value="{{ $plan->programa_educativo }}">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="nivel">Nivel<small>
                                        <p class="obligatorio">*</p>
                                    </small></label>
                                <input type="text" name="nivel" id="nivel" class="form-control" disabled
                                    value="{{ $plan->nivel }}">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="modalidad">Modalidad<small>
                                        <p class="obligatorio">*</p>
                                    </small></label>
                                <input type="text" name="modalidad" id="modalidad" class="form-control" disabled
                                    value="{{ $plan->modalidad }}">
                            </div>
                        </div>
                    </div>


                    <div class="col-md-12"></div>

                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="recomendacion_meta">Recomendación/Meta</label>
                            <textarea name="recomendacion_meta" id="recomendacion_meta" class="form-control"
                                style="min-width: 100%; max-width: 100%;  min-height: 115px;" disabled>{{ $plan->recomendacion_meta }}</textarea>
                        </div>
                    </div>


                    <div class="col-md-6" hidden>
                        <div class="form-group">
                            <label for="ods_pdi">ODS PDI</label>
                            <textarea name="ods_pdi" id="ods_pdi" class="form-control"
                                style="min-width: 100%; max-width: 100%; min-height: 115px;" disabled>{{ $plan->ods_pdi }}</textarea>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="eje_pdi">Eje PDI</label>
                            <input type="text" name="eje_pdi" id="eje_pdi" class="form-control"
                                value="{{ $plan->eje_pdi }}" disabled>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="ods_pdi_select">ODS PDI</label>
                            <input type="text" name="ods_pdi_select" id="ods_pdi_select" class="form-control"
                                value="{{ $plan->ods }}" disabled>
                        </div>
                    </div>

                    <div class="col-md-12"></div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="objetivo_pdi">Objetivo PDI</label>
                            <textarea name="objetivo_pdi" id="objetivo_pdi" class="form-control"
                                style="min-width: 100%; max-width: 100%;  min-height: 115px;" disabled>{{ $plan->objetivo_pdi }}</textarea>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="estrategias">Estrategias</label>
                            <textarea name="estrategias" id="estrategias" class="form-control"
                                style="min-width: 100%; max-width: 100%;  min-height: 115px;" disabled>{{ $plan->estrategia }}</textarea>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="meta_pdi">Meta</label>
                            <textarea name="meta_pdi" id="meta_pdi" class="form-control"
                                style="min-width: 100%; max-width: 100%;  min-height: 115px;" disabled>{{ $plan->meta }}</textarea>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="indicador_pdi">Indicador PDI</label>
                            <textarea name="indicador_pdi" id="indicador_pdi" class="form-control"
                                style="min-width: 100%; max-width: 100%;  min-height: 115px;" disabled>{{ $plan->indicador_pdi }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-12"></div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="ambito_siemec">Ámbito SEAES</label>
                            <input type="text" name="ambito_siemec" id="ambito_siemec" class="form-control"
                                value="{{ $plan->ambito_siemec }}" disabled>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="criterio_siemec">Criterio SEAES</label>
                            <input type="text" name="criterio_siemec" id="criterio_siemec" class="form-control"
                                value="{{ $plan->criterio_siemec }}" disabled>
                        </div>
                    </div>

                    <div class="col-md-12"></div>

                </div>


                <div class="row" style="margin-top: 1rem;">
                    <div class="col-md-12">
                        <h3>Acciones</h3>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="indicador_clave">Indicador clave <small>
                                    <p class="obligatorio">*</p>
                                </small></label>
                            <input type="text" name="indicador_clave" id="indicador_clave" class="form-control"
                                value="{{ $complemento?->indicador_clave }}">
                        </div>
                    </div>

                    <div class="col-md-12">
                        <button class="btn btn-success" onclick="guardaIndicador()">Guardar indicador clave</button>
                    </div>

                    <div class="col-md-12" style="display: flex; justify-content: flex-end;">
                        <button class="btn btn-primary" onclick="abreModal()"><i
                                class="fa fa-plus-circle"></i>&nbsp;&nbsp;Agregar acción</button>
                    </div>

                    <div class="col-md-12" id="div_tabla_acciones">

                    </div>
                </div>


                <div class="row" style="margin-top: 1rem;">
                    <form id="form_guarda_complemento">
                        <div class="col-md-12">
                            <h3>Cierre del plan de mejora</h3>
                        </div>



                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="logros">Logros <small>
                                        <p class="obligatorio">*</p>
                                    </small></label>
                                <input type="text" name="logros" id="logros" class="form-control"
                                    value="{{ $complemento?->logros }}">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="impactos">Impactos <small>
                                        <p class="obligatorio">*</p>
                                    </small></label>
                                <input type="text" name="impactos" id="impactos" class="form-control"
                                    value="{{ $complemento?->impactos }}">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="evidencia">Evidencia <small>
                                        <p class="obligatorio">*</p>
                                    </small></label>
                                <input type="file" name="evidencia" id="evidencia" class="form-control"
                                    accept=".pdf">
                            </div>
                            <div id="archivo_com">

                                @if ($complemento?->archivo)
                                    <a href="{{ asset('storage/' . $complemento?->archivo) }}" target="_BLANK">Ver
                                        archivo</a>
                                @endif
                            </div>
                        </div>



                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="control_observaciones">Control observaciones</label>
                                <p class="obligatorio">*</p>
                                <textarea name="control_observaciones" id="control_observaciones" class="form-control"
                                    style="min-width: 100%; max-width: 100%;" placeholder="Máximo 600 caracteres">{{ $complemento?->control_observaciones }}</textarea>
                            </div>
                        </div>


                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="observaciones">Observaciones</label>
                                <textarea name="observaciones" id="observaciones" class="form-control" style="min-width: 100%; max-width: 100%;"
                                    placeholder="Máximo 600 caracteres">{{ $complemento?->observaciones }}</textarea>
                            </div>
                        </div>

                        <div class="col-md-12" style="display: flex; justify-content: flex-end;">
                            <button class="btn btn-success" id="btn_guardaComplemento"
                                onclick="guardaComplemento(); this.disabled=true;">Guardar evidencia final</button>
                        </div>
                    </form>
                </div>


            </div>
        </div>
    </div>


    <div class="modal fade" id="modal_agrega_accion" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Agregar acción de mejora <small style="color: #DDDDDD;">
                            <p style="color: red; display: inline;">*</p>campos obligatorios
                        </small></h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <form id="form_agrega_accion">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="accion">Acción <small>
                                            <p class="obligatorio">*</p>
                                        </small></label>
                                    <textarea name="accion" id="accion" class="form-control" style="min-width: 100%; max-width: 100;"
                                        placeholder="Máximo 500 caracteres"></textarea>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="producto_resultado">Resultado <small>
                                            <p class="obligatorio">*</p>
                                        </small></label>
                                    <textarea name="producto_resultado" id="producto_resultado" class="form-control"
                                        style="min-width: 100%; max-width: 100%;" placeholder="Máximo 500 caracteres"></textarea>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fecha_inicio">Fecha de inicio <small>
                                            <p class="obligatorio">*</p>
                                        </small></label>
                                    <input type="text" data-provide="datepicker" autocomplete="off"
                                        name="fecha_inicio" id="fecha_inicio" class="form-control">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fecha_fin">Fecha de termino <small>
                                            <p class="obligatorio">*</p>
                                        </small></label>
                                    <input type="text" data-provide="datepicker" autocomplete="off" name="fecha_fin"
                                        id="fecha_fin" class="form-control">
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="responsable">Respnosable <small>
                                            <p class="obligatorio">*</p>
                                        </small></label>
                                    <input type="text" name="responsable" id="responsable" class="form-control">
                                </div>
                            </div>

                        </form>
                    </div>

                    <div class="modal-footer" id="container_btns_modal_confirmacion">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" id="btn_agregaAccion"
                            onclick="agregaAccion(); this.disabled=true;">Guardar acción</button>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_edita_accion" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Edita acción de mejora</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <form id="form_edita_accion">
                            <div class="col-md-12 esconde">
                                <div class="form-group">
                                    <label for="accion_edit">Acción</label>
                                    <textarea name="accion_edit" id="accion_edit" class="form-control" style="min-width: 100%; max-width: 100;"
                                        placeholder="Máximo 500 caracteres"></textarea>
                                </div>
                            </div>
                            <div class="col-md-12 esconde">
                                <div class="form-group">
                                    <label for="producto_resultado_edit">Resultado</label>
                                    <textarea name="producto_resultado_edit" id="producto_resultado_edit" class="form-control"
                                        style="min-width: 100%; max-width: 100%;" placeholder="Máximo 500 caracteres"></textarea>
                                </div>
                            </div>

                            <div class="col-md-6 esconde">
                                <div class="form-group">
                                    <label for="fecha_inicio_edit">Fecha de inicio</label>
                                    <input type="text" data-provide="datepicker" autocomplete="off"
                                        name="fecha_inicio_edit" id="fecha_inicio_edit" class="form-control">
                                </div>
                            </div>

                            <div class="col-md-6 esconde">
                                <div class="form-group">
                                    <label for="fecha_fin_edit">Fecha de termino</label>
                                    <input type="text" data-provide="datepicker" autocomplete="off"
                                        name="fecha_fin_edit" id="fecha_fin_edit" class="form-control">
                                </div>
                            </div>

                            <div class="col-md-12 esconde">
                                <div class="form-group">
                                    <label for="responsable_edit">Respnosable <small>
                                            <p class="obligatorio">*</p>
                                        </small></label>
                                    <input type="text" name="responsable_edit" id="responsable_edit"
                                        class="form-control">
                                </div>
                            </div>

                            <div class="col-md-12 esconde_evidencia">
                                <div class="form-group">
                                    <label for="evidencia_edit">Evidencia</label>
                                    <input type="file" name="evidencia_edit" id="evidencia_edit"
                                        class="form-control">
                                </div>
                            </div>

                        </form>
                    </div>

                    <div class="modal-footer" id="container_btns_modal_confirmacion">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" id="btn_editaAccion"
                            onclick="editaAccion(); this.disabled=true;">Actualizar acción</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
@section('localscripts')
    <script src="{{ asset('bower_components/select2/js/select2.min.js') }}"></script>
    <script>
        var base_url = $("input[name='base_url']").val();
        var id_accion;

        $(function() {
            'use strict';

            // Select2 by showing the search
            $('.select2-show-search').select2({
                minimumResultsForSearch: ''
            });


        });

        $('#fecha_inicio, #fecha_fin, #fecha_inicio_edit, #fecha_fin_edit').datepicker({
            dateFormat: 'yy-mm-dd'
        }).datepicker();

        const getAcciones = async () => {
            mostrarLoader('div_tabla_acciones', 'Espere un momento...');

            const response = await fetch(`${ base_url }/get/acciones/plan/{{ $plan->id }}`, {
                method: 'get'
            })

            $("#div_tabla_acciones").html(
                `<table class="table table-bordered table-striped" id="tabla_acciones"></table>`)

            const data = await response.json();

            if (data.code == 200) {
                const table = $("#tabla_acciones").DataTable({
                    data: data.data,
                    scrollX: true,
                    searching: true,
                    ordering: false,
                    info: false,
                    paging: true,
                    autoWidth: true,
                    language: {
                        url: base_url + '/js/Spanish.json'
                    },
                    columns: [{
                            title: "Accion",
                            data: 'accion'
                        },
                        {
                            title: "Resultado",
                            data: 'producto_resultado'
                        },
                        {
                            title: "Fecha de inicio",
                            data: 'fecha_inicio'
                        },
                        {
                            title: "Fecha de termino",
                            data: 'fecha_fin'
                        },
                        {
                            title: "Responsable",
                            data: 'responsable'
                        },
                        {
                            title: 'Evidencia',
                            defaultContent: '',
                            fnCreatedCell: (nTd, sData, oData, iRow, iCol) => {
                                if (oData.evidencia !== null) {
                                    $(nTd).append(`
                        <div style="text-align: center;">
                          <a href="${ base_url }/storage/${ oData.evidencia }" target="_BLANK" >Ver evidencia</a>
                          <button class="btn btn-sm btn-danger" onclick="confirmaEliminaArchivo(${ oData.id }, 1)"><i class="fa fa-remove"></i></button>
                        </div>
                      `);
                                } else {
                                    $(nTd).append(`
                        <div style="text-align: center;">
                          <button class="btn btn-sm btn-success" onclick="modalEdita(${ oData.id }, 1)">Subir evidencia <i class="fa fa-upload"></i></button>
                        </div>
                      `);
                                }
                            }
                        },
                        {
                            title: 'Acciones',
                            defaultContent: '',
                            fnCreatedCell: (nTd, sData, oData, iRow, iCol) => {
                                $(nTd).append(`
                      <div style="text-align: center;">
                        <button class="btn btn-sm btn-warning" onclick="modalEdita(${ oData.id }, 0)"><i class="fa fa-edit"></i></button>
                        <button class="btn btn-sm btn-danger" onclick="eliminaAccion(${ oData.id })" ><i class="fa fa-trash"></i></button>
                      </div>
                    `);
                            }
                        },
                    ],

                });

            } else {
                swal("¡Error!", data.mensaje, "error");
            }
        }

        const confirmaEliminaArchivo = (id) => {
            swal({
                    title: "¿Está seguro?",
                    text: "El archivo se eliminará de forma permanente.",
                    type: "warning",
                    showCancelButton: true,
                    canceluttonColor: '#FFFFFF',
                    confirmButtonColor: '#dd4b39',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar',
                },
                async function(isConfirm) {
                    if (isConfirm) {
                        eliminaArchivo(id)
                    }
                });
        }

        const eliminaArchivo = async (id) => {
            const response = await fetch(`${ base_url }/elimina/archivo/acciones/${ id }`, {
                method: 'delete',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            const data = await response.json();

            setTimeout(() => {
                if (data.code == 200) {
                    swal("¡Correcto!", data.mensaje, "success");
                    getAcciones();
                } else {
                    swal("¡Error!", data.mensaje, "error");
                }
            }, "200");

        }

        const abreModal = () => {
            $('#modal_agrega_accion').modal()
        }

        const agregaAccion = async () => {
            const body = new FormData(document.getElementById('form_agrega_accion'));
            body.append('id_plan', {{ $plan->id }})
            const response = await fetch(`${ base_url }/guarda/nueva-accion`, {
                method: 'post',
                body,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            const data = await response.json();

            if (data.code == 200) {
                $("#accion").val("")
                $("#producto_resultado").val("")
                $("#fecha_inicio").val("")
                $("#fecha_fin").val("")
                $("#evidencia").val(null)
                getAcciones()
                $('#modal_agrega_accion').modal('hide')
                toastr.success(data.mensaje);
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
            $("#btn_agregaAccion").removeAttr("disabled")
        }

        const eliminaAccion = (id) => {
            swal({
                    title: "¿Está seguro?",
                    text: "El registro se eliminará de forma permanente.",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: '#ff0000',
                    confirmButtonText: 'Si, seguro.',
                    cancelButtonText: 'Cancelar.',
                },
                function(isConfirm) {
                    if (isConfirm) {
                        confirmaElimina(id)
                    }
                });
        }

        const confirmaElimina = async (id) => {
            const response = await fetch(base_url + '/elimina/accion/' + id, {
                method: 'delete',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            const data = await response.json();

            if (data.code == 200) {
                getAcciones()
                toastr.success(data.mensaje);
            } else {
                swal("¡Error!", data.mensaje, "error");
            }
        }

        const modalEdita = async (id, valor) => {
            if (valor == 0) {
                $(".esconde").removeAttr('hidden')
                $(".esconde_evidencia").attr('hidden', true)
            } else {
                $(".esconde_evidencia").removeAttr('hidden')
                $(".esconde").attr('hidden', true)
            }

            const response = await fetch(`${ base_url }/get/detalle/accion/${ id }`, {
                method: 'get'
            })

            const data = await response.json();

            if (data.code == 200) {
                id_accion = id
                $("#accion_edit").val(data.data.accion)
                $("#producto_resultado_edit").val(data.data.producto_resultado)
                $("#fecha_inicio_edit").val(data.data.fecha_inicio)
                $("#fecha_fin_edit").val(data.data.fecha_fin)
                $("#responsable_edit").val(data.data.responsable)
                $("#modal_edita_accion").modal()
            } else {
                swal("¡Error!", data.mensaje, "error")
            }
        }

        const editaAccion = async () => {
            const body = new FormData(document.getElementById('form_edita_accion'));
            body.append('id_accion', id_accion)
            body.append('id_plan', {{ $plan->id }})
            const response = await fetch(`${ base_url }/edita/accion`, {
                method: 'post',
                body,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            const data = await response.json();

            if (data.code == 200) {
                getAcciones()
                $("#evidencia_edit").val(null)
                $('#modal_edita_accion').modal('hide')
                toastr.success(data.mensaje);
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
            $("#btn_editaAccion").removeAttr("disabled")
        }

        const selProgramaEduc = async (valor) => {
            $("#des").val("")
            $("#unidad_academica").val("")
            $("#sede").val("")
            $("#nivel").val("")
            $("#modalidad").val("")
            if (valor != '') {
                const response = await fetch(`${ base_url }/get/info/programa-educativo/${ valor }`, {
                    method: 'get'
                })

                const data = await response.json();

                if (data.code == 200) {
                    $("#des").val(data.data.des)
                    $("#unidad_academica").val(data.data.unidad_academica)
                    $("#sede").val(data.data.sede)
                    $("#nivel").val(data.data.nivel)
                    $("#modalidad").val(data.data.modalidad)
                } else {
                    swal("¡Error!", data.mensaje, "error");
                }
            }
        }


        const guardaComplemento = async () => {
            const body = new FormData(document.getElementById('form_guarda_complemento'));
            body.append('id_plan', {{ $plan->id }})
            const response = await fetch(`${ base_url }/guarda/complemento/plan`, {
                method: 'post',
                body,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            const data = await response.json();

            if (data.code == 200) {
                toastr.success(data.mensaje);

                $("#archivo_com").html(
                    `<a href="${base_url}/storage/${data.archivo}" target="_BLANK">Ver archivo</a>`)

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
            $("#btn_guardaComplemento").removeAttr("disabled")
        }

        const guardaIndicador = async () => {
            const body = new FormData();
            body.append('id_plan', {{ $plan->id }})
            body.append('indicador_clave', $("#indicador_clave").val())
            const response = await fetch(`${ base_url }/guarda/indicador-clave/plan`, {
                method: 'post',
                body,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            const data = await response.json();

            if (data.code == 200) {
                toastr.success(data.mensaje);
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
            $("#btn_guardaComplemento").removeAttr("disabled")
        }

        getAcciones()
    </script>
@endsection
