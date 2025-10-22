@extends('app')

@section('htmlheader_title')
    Acciones Plan de mejora
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('bower_components/select2/css/select2.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('dist/css/forms-modern.css') }}?v={{ filemtime(public_path('dist/css/forms-modern.css')) }}">
    <style>
        /* Ajustes finos para esta vista */
        .form-narrow {
            max-width: 1200px;
        }

        .section-card__title {
            font-weight: 600;
        }

        label {
            margin-bottom: .35rem;
            color: #333;
        }

        .w-120 {
            max-width: 120px;
        }

        .w-160 {
            max-width: 160px;
        }

        .w-200 {
            max-width: 200px;
        }

        .text-muted-help {
            color: #6b7280;
            font-size: .85rem;
        }

        .seg-control input[type="radio"] {
            position: absolute;
            opacity: 0;
        }

        .seg-control label {
            display: inline-block;
            border: 1px solid #d0d7de;
            padding: .35rem .75rem;
            border-right: none;
            cursor: pointer;
            background: #fff;
            user-select: none
        }

        .seg-control label:first-of-type {
            border-radius: .5rem 0 0 .5rem;
        }

        .seg-control label:last-of-type {
            border-right: 1px solid #d0d7de;
            border-radius: 0 .5rem .5rem 0;
        }

        .seg-control input[type="radio"]:checked+label {
            background: #e6f4ff;
            border-color: #84caff;
            color: #0b5cab;
        }

        .seg-control.equal label {
            min-width: 150px;
            text-align: center;
        }

        .readonly-chip {
            display: inline-block;
            padding: .25rem .5rem;
            background: #f4f5f7;
            border: 1px solid #e5e7eb;
            border-radius: .375rem;
        }

        /* DataTable acciones */
        #tabla_acciones {
            width: 100% !important;
        }

        .dt-actions__wrap {
            display: flex;
            gap: .35rem;
            justify-content: center;
        }

        .modal .obligatorio {
            color: #e11d48;
            font-weight: 600;
            margin: 0 .15rem;
        }

        /* Modal moderno */
        .modal-modern .modal-dialog {
            width: auto;
            max-width: 880px;
            margin: 30px auto;
        }

        .modal-modern .modal-header {
            padding: 14px 18px;
            border-bottom: 1px solid #e5e7eb;
        }

        .modal-modern .modal-title {
            font-size: 20px;
            font-weight: 600;
        }

        .modal-modern .modal-body {
            padding: 16px 18px;
        }

        .modal-modern .modal-footer {
            padding: 12px 18px;
        }

        .modal-modern .help {
            color: #6b7280;
            font-size: .85rem;
        }

        .modal-modern .required {
            color: #e11d48;
            font-weight: 600;
            margin-left: 2px;
        }

        .modal-modern .field {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-bottom: 12px;
        }

        .modal-modern textarea {
            min-height: 110px;
            resize: vertical;
        }

        .modal-modern .inline-2 {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px 16px;
        }

        @media (max-width: 768px) {
            .modal-modern .inline-2 {
                grid-template-columns: 1fr;
            }
        }

        .w-160 {
            max-width: 160px;
        }

        .count-hint {
            text-align: right;
            font-size: .8rem;
            color: #9ca3af;
            margin-top: -2px;
        }

        .modal-modern .btn+.btn {
            margin-left: 6px;
        }

        table.dataTable tbody td.dt-type-date {
            text-align: center !important;
        }

        .evidence-actions {
            display: flex;
            gap: .5rem;
            align-items: center;
        }
    </style>
@endpush

@section('main-content')
    <section class="content-header">
        <h1 style="text-align:center; margin:15px 0;">Acciones Plan de mejora</h1>
    </section>

    <div class="col-xs-12 form-narrow">
        <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title">
                    Formulario
                    <small class="text-muted-help">
                        <span style="color:red">*</span> campos obligatorios
                    </small>
                </h3>
            </div>

            <div class="box-body" style="padding-top:1rem;">
                <div class="form-compact">
                    {{-- =================== Datos del plan (solo lectura) =================== --}}
                    <div class="section-card">
                        <div class="section-card__title">Datos del plan</div>
                        <div class="section-card__body gutters-sm">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="required">Tipo</label><br>
                                    <div class="seg-control equal tipo-toggle">
                                        <input class="no-icheck" type="radio" id="tipo_rec" name="tipo"
                                            value="Recomendación" {{ $plan->tipo === 'Recomendación' ? 'checked' : '' }}
                                            disabled>
                                        <label for="tipo_rec" style="margin:0 15px;">Recomendación</label>

                                        <input class="no-icheck" type="radio" id="tipo_meta" name="tipo" value="Meta"
                                            {{ $plan->tipo === 'Meta' ? 'checked' : '' }} disabled>
                                        <label for="tipo_meta">Meta</label>
                                    </div>

                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Procedencia</label>
                                    <select class="form-control" disabled>
                                        @foreach ($procedencias as $value)
                                            <option value="{{ $value->id }}" @selected($plan->procedencia == $value->id)>
                                                {{ $value->descripcion }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Plan no.</label>
                                    <input type="text" class="form-control" value="{{ $plan->plan_no }}" disabled>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Cantidad</label>
                                    <input type="number" class="form-control w-120" value="{{ $plan->cantidad }}" disabled>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Fecha de creación</label>
                                    <input type="text" class="form-control w-200" value="{{ $plan->fecha_creacion }}"
                                        disabled>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Fecha de vencimiento</label>
                                    <input type="text" class="form-control w-200" value="{{ $plan->fecha_vencimiento }}"
                                        disabled>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>AC <small class="obligatorio">*</small></label>
                                    <input type="text" class="form-control" value="{{ $plan->des }}" disabled>
                                </div>
                            </div>

                            <div id="div_des" @if ($plan->tipo_mejora == 2) hidden @endif>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Unidad académica <small class="obligatorio">*</small></label>
                                        <input type="text" class="form-control" value="{{ $plan->unidad_academica }}"
                                            disabled>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Sede <small class="obligatorio">*</small></label>
                                        <input type="text" class="form-control" value="{{ $plan->sede }}" disabled>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Programa educativo <small class="obligatorio">*</small></label>
                                        <input type="text" class="form-control" value="{{ $plan->programa_educativo }}"
                                            disabled>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Nivel <small class="obligatorio">*</small></label>
                                        <input type="text" class="form-control" value="{{ $plan->nivel }}" disabled>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Modalidad <small class="obligatorio">*</small></label>
                                        <input type="text" class="form-control" value="{{ $plan->modalidad }}" disabled>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12"></div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Recomendación/Meta</label>
                                    <textarea class="form-control" style="min-width:100%;max-width:100%;min-height:115px;" disabled>{{ $plan->recomendacion_meta }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- =================== Alineación PDI / SEAES (solo lectura) =================== --}}
                    <div class="section-card">
                        <div class="section-card__title">Alineación PDI / SEAES</div>
                        <div class="section-card__body gutters-sm">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Eje PDI</label>
                                    <input type="text" class="form-control" value="{{ $plan->eje_pdi }}" disabled>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>ODS PDI</label>
                                    <input type="text" class="form-control" value="{{ $plan->ods }}" disabled>
                                </div>
                            </div>

                            <div class="col-md-12"></div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Objetivo PDI</label>
                                    <textarea class="form-control" style="min-height:115px;" disabled>{{ $plan->objetivo_pdi }}</textarea>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Estrategias</label>
                                    <textarea class="form-control" style="min-height:115px;" disabled>{{ $plan->estrategia }}</textarea>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Meta</label>
                                    <textarea class="form-control" style="min-height:115px;" disabled>{{ $plan->meta }}</textarea>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Indicador PDI</label>
                                    <textarea class="form-control" style="min-height:115px;" disabled>{{ $plan->indicador_pdi }}</textarea>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Ámbito SEAES</label>
                                    <input type="text" class="form-control" value="{{ $plan->ambito_siemec }}"
                                        disabled>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Criterio SEAES</label>
                                    <input type="text" class="form-control" value="{{ $plan->criterio_siemec }}"
                                        disabled>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- =================== Acciones =================== --}}
                    <div class="section-card">
                        <div class="section-card__title">Acciones</div>
                        <div class="section-card__body gutters-sm">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Indicador clave (UA o PE) <small class="obligatorio">*</small></label>
                                    <input type="text" id="indicador_clave" class="form-control"
                                        value="{{ $complemento?->indicador_clave }}">
                                </div>
                            </div>
                            <div class="col-md-12" style="margin-bottom:.5rem;">
                                <button class="btn btn-success" onclick="guardaIndicador()">Guardar indicador
                                    clave</button>
                            </div>

                            <div class="col-md-12" style="display:flex;justify-content:flex-end;margin-bottom:.5rem;">
                                <button class="btn btn-primary" onclick="abreModal()">
                                    <i class="fa fa-plus-circle"></i>&nbsp;Agregar acción
                                </button>
                            </div>

                            <div class="col-md-12" id="div_tabla_acciones">
                                <!-- se pinta con JS -->
                            </div>
                        </div>
                    </div>

                    {{-- =================== Cierre del plan =================== --}}
                    <div class="section-card">
                        <div class="section-card__title">Cierre del plan de mejora</div>
                        <div class="section-card__body gutters-sm">
                            <form id="form_guarda_complemento" enctype="multipart/form-data">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Logros (internos) <small class="obligatorio">*</small></label>
                                        <input type="text" name="logros" id="logros" class="form-control"
                                            value="{{ $complemento?->logros }}">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Impactos (externos) <small class="obligatorio">*</small></label>
                                        <input type="text" name="impactos" id="impactos" class="form-control"
                                            value="{{ $complemento?->impactos }}">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Evidencia <small class="obligatorio">*</small></label>

                                        {{-- input real oculto --}}
                                        <input type="file" name="evidencia" id="evidencia" accept=".pdf"
                                            style="display:none">

                                        <div class="evidence-actions">
                                            <button type="button" class="btn btn-success" id="btn_pick_evidencia">
                                                Seleccionar PDF <i class="fa fa-upload"></i>
                                            </button>
                                        </div>

                                        <small class="help">Solo PDF (máx. 6 MB).</small>
                                    </div>

                                    <div id="archivo_com">
                                        @if ($complemento?->archivo)
                                            <div class="dt-actions__wrap">
                                                <a class="btn btn-default"
                                                    href="{{ asset('storage/' . $complemento->archivo) }}"
                                                    target="_blank">
                                                    Ver evidencia
                                                </a>
                                                <button type="button" class="btn btn-danger btn-icon"
                                                    onclick="confirmaEliminaArchivoComplemento({{ $plan->id }})">
                                                    <i class="fa fa-remove"></i>
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Control observaciones <small class="obligatorio">*</small></label>
                                        <textarea name="control_observaciones" id="control_observaciones" class="form-control" style="min-height:100px;"
                                            placeholder="Máximo 600 caracteres">{{ $complemento?->control_observaciones }}</textarea>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Observaciones</label>
                                        <textarea name="observaciones" id="observaciones" class="form-control" style="min-height:100px;"
                                            placeholder="Máximo 600 caracteres">{{ $complemento?->observaciones }}</textarea>
                                    </div>
                                </div>

                                <div class="col-md-12" style="display:flex;justify-content:flex-end;">
                                    <button type="button" class="btn btn-success" id="btn_guardaComplemento"
                                        onclick="guardaComplemento(this)">
                                        Guardar evidencia final
                                    </button>

                                </div>
                            </form>
                        </div>
                    </div>
                    {{-- /sections --}}
                </div>
            </div>
        </div>
    </div>

    {{-- =================== Modales =================== --}}
    <div class="modal fade modal-modern" id="modal_agrega_accion" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">
                        Agregar acción de mejora
                        <small class="help"><span class="required">*</span> campos obligatorios</small>
                    </h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <form id="form_agrega_accion" class="form-compact">
                        <!-- ACCIÓN -->
                        <div class="field">
                            <label>Acción <span class="required">*</span></label>
                            <textarea name="accion" id="accion" class="form-control" maxlength="500" placeholder="Máximo 500 caracteres"></textarea>
                            <div id="accion_count" class="count-hint">0/500</div>
                        </div>

                        <!-- RESULTADO -->
                        <div class="field">
                            <label>Resultado/Producto <span class="required">*</span></label>
                            <textarea name="producto_resultado" id="producto_resultado" class="form-control" maxlength="500"
                                placeholder="Máximo 500 caracteres"></textarea>
                            <div id="producto_resultado_count" class="count-hint">0/500</div>
                        </div>

                        <div class="inline-2">
                            <div class="field">
                                <label>Fecha de inicio <span class="required">*</span></label>
                                <input type="text" data-provide="datepicker" autocomplete="off" name="fecha_inicio"
                                    id="fecha_inicio" class="form-control w-160">
                            </div>
                            <div class="field">
                                <label>Fecha de término <span class="required">*</span></label>
                                <input type="text" data-provide="datepicker" autocomplete="off" name="fecha_fin"
                                    id="fecha_fin" class="form-control w-160">
                            </div>
                        </div>

                        <div class="field">
                            <label>Responsable (nombre o puesto) <span class="required">*</span></label>
                            <input type="text" name="responsable" id="responsable" class="form-control">
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btn_agregaAccion"
                        onclick="agregaAccion(); this.disabled=true;">Guardar acción</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade modal-modern" id="modal_edita_accion" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Edita acción de mejora</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <form id="form_edita_accion" class="form-compact" enctype="multipart/form-data">

                        <div class="esconde">
                            <div class="field">
                                <label>Acción</label>
                                <textarea name="accion_edit" id="accion_edit" class="form-control" maxlength="500"
                                    placeholder="Máximo 500 caracteres"></textarea>
                                <div id="accion_edit_count" class="count-hint">0/500</div>
                            </div>

                            <div class="field">
                                <label>Resultado/Producto</label>
                                <textarea name="producto_resultado_edit" id="producto_resultado_edit" class="form-control" maxlength="500"
                                    placeholder="Máximo 500 caracteres"></textarea>
                                <div id="producto_resultado_edit_count" class="count-hint">0/500</div>
                            </div>

                            <div class="inline-2">
                                <div class="field">
                                    <label>Fecha de inicio</label>
                                    <input type="text" data-provide="datepicker" autocomplete="off"
                                        name="fecha_inicio_edit" id="fecha_inicio_edit" class="form-control w-160">
                                </div>
                                <div class="field">
                                    <label>Fecha de término</label>
                                    <input type="text" data-provide="datepicker" autocomplete="off"
                                        name="fecha_fin_edit" id="fecha_fin_edit" class="form-control w-160">
                                </div>
                            </div>

                            <div class="field">
                                <label>Responsable (nombre o puesto)</label>
                                <input type="text" name="responsable_edit" id="responsable_edit"
                                    class="form-control">
                            </div>
                        </div>
                        <!-- Bloque SOLO para subir evidencia -->
                        <div class="esconde_evidencia" hidden>
                            <div class="field">
                                <label for="evidencia_edit">Evidencia <span class="required">*</span></label>
                                <input type="file" name="evidencia_edit" id="evidencia_edit" class="form-control"
                                    accept=".pdf">
                                <small class="help">Solo PDF (máx. 6 MB).</small>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btn_editaAccion"
                        onclick="editaAccion()">Actualizar acción</button>

                </div>
            </div>
        </div>
    </div>
@endsection

@section('localscripts')
    <script src="{{ asset('bower_components/select2/js/select2.min.js') }}"></script>
    <script>
        var base_url = $("input[name='base_url']").val();
        let id_accion;
        let EDIT_MODE = 'full'; // 'full' | 'evidence'
        let evidenciaFile = null; // <-- NUEVO

        function setEditMode(evidence) {
            const $m = $('#modal_edita_accion');

            // quita atributos hidden y luego toggle
            $m.find('.esconde, .esconde_evidencia').removeAttr('hidden');
            $m.find('.esconde').toggle(!evidence);
            $m.find('.esconde_evidencia').toggle(!!evidence);

            $m.find('.modal-title').text(evidence ? 'Subir evidencia' : 'Edita acción de mejora');
            $m.find('#btn_editaAccion').text(evidence ? 'Subir evidencia' : 'Actualizar acción');

            EDIT_MODE = evidence ? 'evidence' : 'full';
        }

        // Datepickers compactos
        $('#fecha_inicio, #fecha_fin, #fecha_inicio_edit, #fecha_fin_edit').datepicker({
            dateFormat: 'yy-mm-dd'
        }).datepicker();

        const loaderHTML = `
            <div class="text-center" style="padding:24px;">
                <i class="fa fa-spinner fa-spin"></i> Espere un momento...
            </div>`;

        const getAcciones = async () => {
            // loader
            if (typeof mostrarLoader === 'function') {
                mostrarLoader('div_tabla_acciones', 'Espere un momento...');
            } else {
                $("#div_tabla_acciones").html(loaderHTML);
            }

            const response = await fetch(`${base_url}/get/acciones/plan/{{ $plan->id }}`, {
                method: 'get'
            });
            $("#div_tabla_acciones").html(
                `<table class="table table-bordered table-striped compact" id="tabla_acciones"></table>`);
            const data = await response.json();

            if (data.code == 200) {
                new DataTable('#tabla_acciones', {
                    data: data.data,
                    deferRender: true,
                    searching: true,
                    ordering: false,
                    info: false,
                    paging: true,
                    autoWidth: false,
                    scrollX: true,
                    language: {
                        url: base_url + '/js/Spanish.json'
                    },
                    layout: {
                        topStart: 'pageLength',
                        topEnd: 'search',
                        bottomStart: 'info',
                        bottomEnd: 'paging'
                    },
                    columns: [{
                            title: "Acción",
                            data: 'accion'
                        },
                        {
                            title: "Resultado/Producto",
                            data: 'producto_resultado'
                        },
                        {
                            title: "Fecha inicio",
                            data: 'fecha_inicio',
                            className: 'text-center'
                        },
                        {
                            title: "Fecha término",
                            data: 'fecha_fin',
                            className: 'text-center'
                        },
                        {
                            title: "Responsable (nombre o puesto)",
                            data: 'responsable'
                        },
                        {
                            title: 'Evidencia',
                            data: null,
                            orderable: false,
                            className: 'text-center',
                            render: (_, __, o) => {
                                if (o.evidencia) {
                                    return `
                                    <div>
                                    <a href="${base_url}/storage/${o.evidencia}" target="_blank">Ver evidencia</a>
                                    <button class="btn btn-sm btn-danger" onclick="confirmaEliminaArchivo(${o.id})">
                                        <i class="fa fa-remove"></i>
                                    </button>
                                    </div>`;
                                }
                                return `
                                <div>
                                    <button class="btn btn-sm btn-success" onclick="modalEdita(${o.id}, 'evidence')">
                                    Subir evidencia <i class="fa fa-upload"></i>
                                    </button>
                                </div>`;
                            }
                        },
                        {
                            title: 'Acciones',
                            data: null,
                            orderable: false,
                            className: 'text-center',
                            render: (_, __, o) => `
                            <div class="dt-actions__wrap">
                             <button class="btn btn-primary btn-icon" title="Editar" onclick="modalEdita(${o.id}, 'edit')">
                                <i class="fa fa-pencil"></i>
                            </button>
                              <button class="btn btn-danger btn-icon" title="Eliminar" onclick="eliminaAccion(${o.id})">
                                <i class="fa fa-trash"></i>
                            </button>
                            </div>`
                        }
                    ]
                });
            } else {
                swal("¡Error!", data.mensaje, "error");
            }
        };

        const confirmaEliminaArchivo = (id) => {
            swal({
                title: "¿Está seguro?",
                text: "El archivo se eliminará de forma permanente.",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: '#dd4b39',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
            }, async function(isConfirm) {
                if (isConfirm) eliminaArchivo(id);
            });
        };

        const eliminaArchivo = async (id) => {
            const response = await fetch(`${base_url}/elimina/archivo/acciones/${id}`, {
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
                } else swal("¡Error!", data.mensaje, "error");
            }, 200);
        };

        const abreModal = () => $('#modal_agrega_accion').modal();

        const agregaAccion = async () => {
            const body = new FormData(document.getElementById('form_agrega_accion'));
            body.append('id_plan', {{ $plan->id }});
            const response = await fetch(`${base_url}/guarda/nueva-accion`, {
                method: 'post',
                body,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            const data = await response.json();
            if (data.code == 200) {
                $("#accion, #producto_resultado, #fecha_inicio, #fecha_fin").val("");
                $("#evidencia").val(null);
                getAcciones();
                $('#modal_agrega_accion').modal('hide');
                toastr.success(data.mensaje);
            } else if (data.code == 411) {
                const first = Object.values(data.errors)[0][0];
                swal("¡Error!", first, "error");
            } else {
                swal("¡Error!", data.mensaje, "error");
            }
            $("#btn_agregaAccion").prop("disabled", false);
        };

        const eliminaAccion = (id) => {
            swal({
                title: "¿Está seguro?",
                text: "El registro se eliminará de forma permanente.",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: '#ff0000',
                confirmButtonText: 'Sí, seguro',
                cancelButtonText: 'Cancelar',
            }, function(isConfirm) {
                if (isConfirm) confirmaElimina(id);
            });
        };

        const confirmaElimina = async (id) => {
            const response = await fetch(`${base_url}/elimina/accion/${id}`, {
                method: 'delete',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            const data = await response.json();
            if (data.code == 200) {
                getAcciones();
                toastr.success(data.mensaje);
            } else {
                swal("¡Error!", data.mensaje, "error");
            }
        };


        // 'edit' muestra campos de acción, 'evidence' solo el file input
        const modalEdita = async (id, mode = 'edit') => {
            const evidence = mode === 'evidence';

            // limpia y habilita botón
            const form = document.getElementById('form_edita_accion');
            form.reset();
            evidenciaFile = null;
            $('#btn_editaAccion').prop('disabled', false);

            setEditMode(evidence);

            const r = await fetch(`${base_url}/get/detalle/accion/${id}`);
            const data = await r.json();
            if (data.code !== 200) return swal("¡Error!", data.mensaje, "error");

            id_accion = id;

            // ⚠️ SIEMPRE rellenar los campos (aunque estén ocultos)
            $('#accion_edit').val(data.data.accion);
            $('#producto_resultado_edit').val(data.data.producto_resultado);
            $('#fecha_inicio_edit').val(data.data.fecha_inicio);
            $('#fecha_fin_edit').val(data.data.fecha_fin);
            $('#responsable_edit').val(data.data.responsable);

            // limpia file input
            $('#evidencia_edit').val('');

            $('#modal_edita_accion').one('shown.bs.modal', function() {
                if (evidence) $('#evidencia_edit').trigger('focus');
            }).modal('show');
        };

        let _enviandoEdit = false;

        const editaAccion = async () => {
            if (_enviandoEdit) return;
            _enviandoEdit = true;
            $('#btn_editaAccion').prop('disabled', true);

            try {
                const form = document.getElementById('form_edita_accion');
                const body = new FormData(form); // incluye TODOS los campos (ocultos también)

                body.set('id_accion', id_accion);
                body.set('id_plan', {{ $plan->id }});
                body.set('solo_evidencia', EDIT_MODE === 'evidence' ? 1 : 0);

                if (EDIT_MODE === 'evidence') {
                    const inp = document.getElementById('evidencia_edit');
                    const file = evidenciaFile || (inp.files && inp.files[0] ? inp.files[0] : null);

                    if (!file) {
                        swal('Falta el archivo', 'Selecciona un PDF para subir.', 'warning');
                        return;
                    }
                    const name = (file.name || '').toLowerCase();
                    if (!name.endsWith('.pdf')) {
                        swal('Formato no válido', 'Solo se permite PDF.', 'warning');
                        return;
                    }
                    if (file.size > 6 * 1024 * 1024) {
                        swal('Archivo muy grande', 'El PDF no debe exceder 6 MB.', 'warning');
                        return;
                    }

                    // fuerza que el archivo viaje con la MISMA clave del input
                    body.set('evidencia_edit', file, file.name);
                }

                // (opcional) DEBUG: ver qué viaja
                // for (const [k,v] of body.entries()) console.log(k, v instanceof File ? `File(${v.name}, ${v.size})` : v);

                const resp = await fetch(`${base_url}/edita/accion`, {
                    method: 'post',
                    body,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                let data = null;
                try {
                    data = await resp.json();
                } catch (_) {}

                // Manejo cuando el backend responde HTTP 411 (tus validaciones)
                if (!resp.ok) {
                    if (resp.status === 411 && data && data.errors) {
                        const first = Object.values(data.errors)[0][0];
                        swal('¡Error!', first, 'error');
                    } else if (resp.status === 413) {
                        swal('¡Error!', 'El servidor rechazó el archivo (muy grande).', 'error');
                    } else if (resp.status === 419) {
                        swal('¡Error!', 'Sesión expirada. Recarga la página.', 'error');
                    } else {
                        swal('¡Error!', 'No se pudo guardar.', 'error');
                    }
                    return;
                }

                if (data && data.code === 200) {
                    getAcciones();
                    evidenciaFile = null;
                    if (EDIT_MODE === 'evidence') $('#evidencia_edit').val('');
                    $('#modal_edita_accion').modal('hide');
                    toastr.success(data.mensaje);
                } else if (data && data.code === 411) {
                    const first = Object.values(data.errors)[0][0];
                    swal('¡Error!', first, 'error');
                } else {
                    swal('¡Error!', (data && data.mensaje) || 'No se pudo guardar.', 'error');
                }
            } catch (e) {
                console.error(e);
                swal('¡Error!', 'Ocurrió un problema al enviar la solicitud.', 'error');
            } finally {
                _enviandoEdit = false;
                $('#btn_editaAccion').prop('disabled', false);
            }
        };

        // Si el modal se cierra después de un warning, re-habilita el botón
        $('#modal_edita_accion').on('hidden.bs.modal', function() {
            $('#btn_editaAccion').prop('disabled', false);
        });

        // opcional: habilita el botón al elegir archivo (mejora UX)
        $('#evidencia_edit').on('change', function() {
            if (this.files && this.files.length) $('#btn_editaAccion').prop('disabled', false);
        });


        const guardaIndicador = async () => {
            const body = new FormData();
            body.append('id_plan', {{ $plan->id }});
            body.append('indicador_clave', $("#indicador_clave").val());
            const response = await fetch(`${base_url}/guarda/indicador-clave/plan`, {
                method: 'post',
                body,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            const data = await response.json();
            if (data.code == 200) toastr.success(data.mensaje);
            else if (data.code == 411) {
                const first = Object.values(data.errors)[0][0];
                swal("¡Error!", first, "error");
            } else swal("¡Error!", data.mensaje, "error");
        };

        // Arranque
        getAcciones();

        // Contadores
        function updateCount(id, max) {
            const el = document.getElementById(id);
            if (!el) return;
            const hint = document.getElementById(id + '_count');
            const m = max || parseInt(el.getAttribute('maxlength') || '500', 10);
            if (hint) hint.textContent = `${el.value.length}/${m}`;
        }

        ['accion', 'producto_resultado', 'accion_edit', 'producto_resultado_edit'].forEach(k => {
            document.addEventListener('input', e => {
                if (e.target && e.target.id === k) updateCount(k);
            });
        });

        $('#modal_agrega_accion').on('shown.bs.modal', function() {
            setTimeout(() => document.getElementById('accion')?.focus(), 50);
            updateCount('accion');
            updateCount('producto_resultado');
        });
        $('#modal_edita_accion').on('shown.bs.modal', function() {
            updateCount('accion_edit');
            updateCount('producto_resultado_edit');
        });

        // Captura firme del archivo (primera vez incluido)
        $(document).on('change', '#evidencia_edit', function() {
            evidenciaFile = this.files && this.files[0] ? this.files[0] : null;
            // re-habilita el botón por UX
            if (evidenciaFile) $('#btn_editaAccion').prop('disabled', false);
        });

        $('#modal_edita_accion, #modal_agrega_accion')
            .on('show.bs.modal', function() {
                $(this).attr('aria-hidden', 'false');
            })
            .on('hidden.bs.modal', function() {
                $(this).attr('aria-hidden', 'true');
            });

        // Abrir selector al hacer click en el botón
        $('#btn_pick_evidencia').on('click', function() {
            document.getElementById('evidencia').click();
        });
        // Validación inmediata (tipo / tamaño) al elegir archivo
        $('#evidencia').on('change', function() {
            const file = this.files && this.files[0] ? this.files[0] : null;
            if (!file) return;

            const isPdfByExt = /\.pdf$/i.test(file.name || '');
            const tooBig = file.size > 6 * 1024 * 1024;

            if (!isPdfByExt) {
                swal('Formato no válido', 'Solo se permite PDF.', 'warning');
                this.value = '';
                return;
            }
            if (tooBig) {
                swal('Archivo muy grande', 'El PDF no debe exceder 6 MB.', 'warning');
                this.value = '';
                return;
            }

            // Si quieres dar feedback sin mostrar chip:
            toastr.info('Archivo listo: ' + file.name);
        });
        // === Borrar archivo del complemento (mismo look/comportamiento que Acciones) ===
        const confirmaEliminaArchivoComplemento = (idPlan) => {
            swal({
                title: '¿Está seguro?',
                text: 'El archivo se eliminará de forma permanente.',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dd4b39',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
            }, async function(isConfirm) {
                if (isConfirm) eliminaArchivoComplemento(idPlan);
            });
        };

        const eliminaArchivoComplemento = async (idPlan) => {
            const r = await fetch(`${base_url}/elimina/archivo/complemento/${idPlan}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            const data = await r.json().catch(() => ({}));

            if (r.ok && data.code === 200) {
                swal('¡Correcto!', data.mensaje, 'success');
                // Limpia UI
                $('#archivo_com').empty();
                $('#evidencia').val('');
            } else {
                swal('¡Error!', (data && data.mensaje) || 'No se pudo eliminar.', 'error');
            }
        };

        // === Ajuste leve en guardaComplemento para refrescar la UI con los nuevos botones ===
        // Dentro del éxito de guardaComplemento, cambia el fragmento que actualiza #archivo_com por este:
        // (usa exactamente esto en tu función existente)
        // Renderiza "Ver evidencia" + "Eliminar" abajo cuando exista archivo
        function _renderArchivoComplemento(path) {
            $('#archivo_com').html(`
    <div class="dt-actions__wrap">
      <a class="btn btn-default" href="${base_url}/storage/${path}" target="_blank">Ver evidencia</a>
      <!-- Si no tienes endpoint para eliminar, quita este botón -->
      <button type="button" class="btn btn-danger btn-icon"
              onclick="confirmaEliminaArchivoComplemento(${/* id_plan */ {{ $plan->id }} })">
        <i class="fa fa-remove"></i>
      </button>
    </div>
  `);
        }

        async function guardaComplemento(btn) {
            btn.disabled = true;
            try {
                const form = document.getElementById('form_guarda_complemento');
                const fd = new FormData(form);
                fd.set('id_plan', {{ $plan->id }}); // <-- importante

                // Validación inmediata del PDF
                const f = form.querySelector('#evidencia').files[0];
                if (f) {
                    if (!/\.pdf$/i.test(f.name)) {
                        swal('Formato no válido', 'Solo PDF.', 'warning');
                        return;
                    }
                    if (f.size > 6 * 1024 * 1024) {
                        swal('Archivo muy grande', 'Máx. 6 MB.', 'warning');
                        return;
                    }
                    fd.set('evidencia', f, f.name);
                }

                // AQUÍ se define 'resp'
                const resp = await fetch(`${base_url}/guarda/complemento/plan`, {
                    method: 'POST',
                    body: fd,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                const data = await resp.json().catch(() => ({}));

                if (resp.ok && data.code === 200) {
                    toastr.success(data.mensaje);
                    if (data.archivo) {
                        _renderArchivoComplemento(data.archivo);
                        $('#evidencia').val(''); // limpiar input
                    }
                } else if (resp.status === 411 && data.errors) {
                    const first = Object.values(data.errors)[0][0];
                    swal('¡Error!', first, 'error');
                } else {
                    swal('¡Error!', data.mensaje || 'No se pudo guardar.', 'error');
                }
            } catch (e) {
                console.error(e);
                swal('¡Error!', 'Ocurrió un problema al enviar la solicitud.', 'error');
            } finally {
                btn.disabled = false;
            }
        }
    </script>
@endsection
