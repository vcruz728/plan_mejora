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
            user-select: none;
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
            flex-wrap: wrap;
            /* para que no se desborde en pantallas chicas */
        }

        .btn-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            padding: 0;
        }

        /* ===== Confirm Modal Moderno — Alto Contraste ===== */
        .confirm-modern .modal-content {
            background: #fff;
            border: 0;
            border-radius: 14px;
            box-shadow: 0 24px 48px rgba(0, 0, 0, .22);
        }

        .confirm-modern .modal-header {
            border: 0;
            padding: 18px 22px 10px;
        }

        .confirm-modern .cm-head {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .confirm-modern .cm-icon {
            width: 48px;
            height: 48px;
            border-radius: 9999px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fee2e2;
            color: #b91c1c;
            /* por defecto usamos 'danger soft' */
        }

        .confirm-modern .cm-icon i {
            font-size: 20px;
            line-height: 1;
        }

        .confirm-modern .modal-title {
            margin: 0;
            font-size: 20px;
            font-weight: 800;
            color: #111827 !important;
            /* título oscuro SIEMPRE */
            letter-spacing: .2px;
        }

        .confirm-modern .cm-subtitle {
            margin-top: 2px;
            color: #4b5563;
            /* más oscuro que antes */
        }

        .confirm-modern .modal-body {
            padding: 6px 22px 0;
        }

        .confirm-modern .cm-text {
            margin: 0;
            font-size: 15px;
            line-height: 1.5;
            color: #374151;
            /* texto más legible */
        }

        .confirm-modern .modal-footer {
            border: 0;
            padding: 16px 22px 22px;
        }

        .confirm-modern .btn {
            border-radius: 10px;
            padding: 9px 16px;
            font-weight: 600;
        }

        .confirm-modern .btn:focus {
            outline: 2px solid #60a5fa;
            outline-offset: 2px;
            box-shadow: none;
        }

        /* Cancel (que no parezca deshabilitado) */
        .confirm-modern .btn-cancel {
            background: #ffffff;
            color: #374151;
            border: 1px solid #d1d5db;
        }

        .confirm-modern .btn-cancel:hover {
            background: #f9fafb;
            border-color: #9ca3af;
            color: #111827;
        }

        /* Afirmar — danger con muy buen contraste */
        .confirm-modern .btn-ok.btn-danger {
            background: #dc2626;
            border-color: #b91c1c;
            color: #fff;
        }

        .confirm-modern .btn-ok.btn-danger:hover {
            background: #b91c1c;
            border-color: #991b1b;
        }

        .confirm-modern .btn-ok.btn-primary {
            background: #2563eb;
            border-color: #1d4ed8;
        }

        .confirm-modern .btn-ok.btn-primary:hover {
            background: #1d4ed8;
            border-color: #1e40af;
        }

        /* Variantes del icono (si usas otras) */
        .confirm-modern.confirm--warn .cm-icon {
            background: #fef3c7;
            color: #b45309;
        }

        .confirm-modern.confirm--info .cm-icon {
            background: #e0f2fe;
            color: #075985;
        }

        .confirm-modern.confirm--success .cm-icon {
            background: #dcfce7;
            color: #166534;
        }

        /* Backdrop un poco más oscuro para contraste del cuadro */
        .modal-backdrop.in,
        .modal-backdrop.show {
            opacity: .45;
        }

        /* Modo oscuro del SO (por si el navegador lo activa) */
        @media (prefers-color-scheme: dark) {
            .confirm-modern .modal-content {
                background: #0f172a;
                box-shadow: 0 24px 48px rgba(0, 0, 0, .6);
            }

            .confirm-modern .modal-title {
                color: #e5e7eb !important;
            }

            .confirm-modern .cm-subtitle {
                color: #9ca3af;
            }

            .confirm-modern .cm-text {
                color: #d1d5db;
            }

            .confirm-modern .btn-cancel {
                background: #111827;
                border-color: #374151;
                color: #e5e7eb;
            }

            .confirm-modern .btn-cancel:hover {
                background: #0b1220;
            }

            .confirm-modern .btn-ok.btn-primary {
                background: #3b82f6;
                border-color: #2563eb;
            }
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
                                <button type="button" class="btn btn-success" onclick="guardaIndicador()">Guardar
                                    indicador clave</button>
                            </div>

                            <div class="col-md-12" style="display:flex;justify-content:flex-end;margin-bottom:.5rem;">
                                <button type="button" class="btn btn-primary" onclick="verificaIndicadorYAbreModal()">
                                    <i class="fa fa-plus-circle"></i>&nbsp;Agregar acción
                                </button>
                            </div>

                            <div class="col-md-12" id="div_tabla_acciones"></div>
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
                                        <!-- input real oculto -->
                                        <input type="file" name="evidencia" id="evidencia" accept=".pdf"
                                            style="display:none">
                                        @php $tieneArchivo = !empty($complemento?->archivo); @endphp
                                        <div class="evidence-actions">
                                            <button type="button" class="btn btn-success" id="btn_pick_evidencia"
                                                @if ($tieneArchivo) style="display:none" @endif>
                                                Seleccionar PDF <i class="fa fa-upload"></i>
                                            </button>
                                            <a class="btn btn-default" id="btn_ver_evidencia"
                                                href="{{ $tieneArchivo ? asset('storage/' . $complemento->archivo) : '#' }}"
                                                target="_blank"
                                                @unless ($tieneArchivo) style="display:none" @endunless>
                                                Ver evidencia
                                            </a>
                                            <button type="button" class="btn btn-danger btn-icon"
                                                id="btn_delete_evidencia"
                                                onclick="confirmaEliminaArchivoComplemento({{ $plan->id }})"
                                                @unless ($tieneArchivo) style="display:none" @endunless>
                                                <i class="fa fa-remove"></i>
                                            </button>
                                        </div>
                                        <small class="help">Solo PDF (máx. 6 MB).</small>
                                    </div>
                                </div>

                                {{-- =================== Actividades de control =================== --}}
                                <div class="col-md-12"
                                    style="display:flex;justify-content:flex-end;margin-bottom:.5rem; margin-top:1rem;">
                                    <button type="button" class="btn btn-primary" onclick="abreModalControl(event)">
                                        <i class="fa fa-plus-circle"></i>&nbsp;Agregar actividad de control
                                    </button>
                                </div>
                                <div class="col-md-12" id="div_tabla_control"></div>

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

    {{-- =================== Modales independientes (FUERA del form grande) =================== --}}

    {{-- Modal agregar/editar actividad de control --}}
    <div class="modal fade modal-modern" id="modal_control" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="titulo_modal_control">Agregar actividad de control</h3>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <form id="form_control" class="form-compact">
                        <input type="hidden" id="id_control">
                        <div class="field">
                            <label>Actividad <span class="required">*</span></label>
                            <textarea id="actividad" class="form-control" maxlength="500" placeholder="Máximo 500 caracteres"></textarea>
                            <div id="actividad_count" class="count-hint">0/500</div>
                        </div>
                        <div class="field">
                            <label>Resultado/Producto <span class="required">*</span></label>
                            <textarea id="producto_resultado_ctrl" class="form-control" maxlength="500" placeholder="Máximo 500 caracteres"></textarea>
                            <div id="producto_resultado_ctrl_count" class="count-hint">0/500</div>
                        </div>
                        <div class="inline-2">
                            <div class="field">
                                <label>Fecha de inicio <span class="required">*</span></label>
                                <input type="text" id="fecha_inicio_ctrl" data-provide="datepicker"
                                    autocomplete="off" class="form-control w-160">
                            </div>
                            <div class="field">
                                <label>Fecha de término <span class="required">*</span></label>
                                <input type="text" id="fecha_fin_ctrl" data-provide="datepicker" autocomplete="off"
                                    class="form-control w-160">
                            </div>
                        </div>
                        <div class="field">
                            <label>Responsable (nombre o puesto) <span class="required">*</span></label>
                            <input type="text" id="responsable_ctrl" class="form-control">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btn_guardar_control"
                        onclick="guardarActividadControl()">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal agregar acción --}}
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
                        <div class="field">
                            <label>Acción <span class="required">*</span></label>
                            <textarea name="accion" id="accion" class="form-control" maxlength="500" placeholder="Máximo 500 caracteres"></textarea>
                            <div id="accion_count" class="count-hint">0/500</div>
                        </div>

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

    {{-- Modal editar acción --}}
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

    <!-- Modal de confirmación moderno y reutilizable -->
    <div class="modal fade confirm-modern" id="confirm_modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document" style="max-width:440px">
            <div class="modal-content">
                <div class="modal-header" style="border:0;">
                    <button type="button" class="close modal-close" data-dismiss="modal"
                        aria-label="Close"><span>&times;</span></button>
                    <div class="cm-head">
                        <div class="cm-icon"><i class="fa fa-question"></i></div>
                        <div class="cm-titles">
                            <h4 class="modal-title">Confirmar</h4>
                            <small class="cm-subtitle"></small>
                        </div>
                    </div>
                </div>
                <div class="modal-body">
                    <p class="cm-text">¿Está seguro?</p>
                </div>
                <div class="modal-footer" style="border:0;">
                    <button type="button" class="btn btn-default btn-cancel" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary btn-ok">Aceptar</button>
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
        let evidenciaFile = null;


        // Evita warnings y locks de foco/aria
        function blurActive() {
            try {
                document.activeElement && document.activeElement.blur();
            } catch (e) {}
        }

        // Limpia scroll-lock/backdrops si quedara algo
        function fixScrollLock() {
            const anyModal = $('.modal.show:visible, .modal.in:visible').length > 0;
            if (!anyModal) {
                $('body,html').css('overflow', '').removeClass('modal-open stop-scrolling');
                $('.modal-backdrop').removeClass('modal-stack').remove();
            }
        }
        $(document).on('hidden.bs.modal', fixScrollLock);


        // Confirmación moderna (Bootstrap) con variantes e ícono
        function confirmBS({
            title = 'Confirmar',
            text = '¿Está seguro?',
            subtitle = '',
            confirmText = 'Aceptar',
            cancelText = 'Cancelar',
            variant = 'info',
            icon = 'question',
            danger = false,
            focus = 'ok'
        } = {}) {
            return new Promise(resolve => {
                const $m = $('#confirm_modal');
                const $ok = $m.find('.btn-ok');
                const $can = $m.find('.btn-cancel');

                $m.find('.modal-title').text(title);
                $m.find('.cm-text').text(text);
                $m.find('.cm-subtitle').text(subtitle || '').toggle(!!subtitle);

                if (danger) variant = 'danger';
                $m.removeClass('confirm--danger confirm--warn confirm--info confirm--success').addClass(
                    'confirm--' + variant);

                $m.find('.cm-icon i').attr('class', 'fa fa-' + (icon || 'question'));

                // <-- aquí forzamos la clase visual del botón según variante
                $ok.text(confirmText)
                    .removeClass('btn-primary btn-danger')
                    .addClass(variant === 'danger' ? 'btn-danger' : 'btn-primary');
                $can.text(cancelText);

                const cleanup = () => {
                    $m.off('hidden.bs.modal', onCancel);
                    $ok.off('click', onOk);
                    $can.off('click', onCancel);
                };
                const onOk = () => {
                    resolve(true);
                    $m.modal('hide');
                    cleanup();
                };
                const onCancel = () => {
                    resolve(false);
                    cleanup();
                };

                $m.on('hidden.bs.modal', onCancel);
                $ok.on('click', onOk);
                $can.on('click', onCancel);

                const zIndex = 1040 + (10 * $('.modal:visible').length);
                $m.css('z-index', zIndex);
                setTimeout(() => {
                    $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass(
                        'modal-stack');
                }, 0);

                (document.activeElement && document.activeElement.blur && document.activeElement.blur());
                $m.modal({
                    backdrop: true,
                    keyboard: true,
                    show: true
                });
                setTimeout(() => (focus === 'cancel' ? $can : $ok).trigger('focus'), 90);
            });
        }

        /* --- Fix: SweetAlert v1 + Bootstrap scroll-lock --- */
        function fixScrollLock() {
            const $body = $('body');
            const anyModalOpen = $('.modal.show:visible, .modal.in:visible').length > 0;
            const anySwalOpen = $('.sweet-alert:visible, .swal2-container:visible').length > 0;
            if (!anyModalOpen && !anySwalOpen) {
                $body.removeClass('modal-open stop-scrolling').css('overflow', '');
                $('html').css('overflow', '');
                $('.modal-backdrop').remove();
                $('.sweet-overlay').remove();
            }
        }
        $(document).on('hidden.bs.modal', fixScrollLock);
        $(document).on('click', '.sweet-alert .confirm, .sweet-alert .cancel', () => setTimeout(fixScrollLock, 50));
        $(document).on('keydown', (e) => {
            if (e.key === 'Escape' || e.keyCode === 27) setTimeout(fixScrollLock, 50);
        });

        // === Indicador clave guardado? (inicializa desde servidor)
        let indicadorGuardado = {{ $complemento && $complemento->indicador_clave ? 'true' : 'false' }};
        // Si el usuario escribe, marcamos como pendiente de guardar
        $('#indicador_clave').on('input', () => {
            indicadorGuardado = false;
        });

        function verificaIndicadorYAbreModal() {
            const val = ($('#indicador_clave').val() || '').trim();
            if (!val || !indicadorGuardado) {
                swal('Falta el Indicador clave',
                    'Captura y guarda el Indicador clave antes de agregar acciones.',
                    'warning');
                $('#indicador_clave').focus();
                return;
            }
            $('#modal_agrega_accion').modal('show');
        }

        function setEditMode(evidence) {
            const $m = $('#modal_edita_accion');
            $m.find('.esconde, .esconde_evidencia').removeAttr('hidden');
            $m.find('.esconde').toggle(!evidence);
            $m.find('.esconde_evidencia').toggle(!!evidence);
            $m.find('.modal-title').text(evidence ? 'Subir evidencia' : 'Edita acción de mejora');
            $m.find('#btn_editaAccion').text(evidence ? 'Subir evidencia' : 'Actualizar acción');
            EDIT_MODE = evidence ? 'evidence' : 'full';
        }

        // Ajusta donde inicializas datepicker
        $('.datepicker, [data-provide="datepicker"]').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true,
            language: 'es'
        });

        // ===================== ACCIONES =====================
        let dtAcciones = null;

        async function getAcciones() {
            try {
                // loader solo si no existe la tabla aún
                if (!dtAcciones) {
                    $("#div_tabla_acciones").html(`
                        <div class="text-center" style="padding:24px;">
                          <i class="fa fa-spinner fa-spin"></i> Espere un momento...
                        </div>`);
                }

                const response = await fetch(`${base_url}/get/acciones/plan/{{ $plan->id }}?t=${Date.now()}`, {
                    method: 'GET',
                    cache: 'no-store',
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json().catch(() => ({}));
                const rows = (response.ok && data.code === 200 && Array.isArray(data.data)) ? data.data : [];

                if (!dtAcciones) {
                    // Crear tabla e inicializar DataTable SOLO la primera vez
                    $("#div_tabla_acciones").html(
                        '<table class="table table-bordered table-striped compact" id="tabla_acciones" style="width:100%"></table>'
                    );
                    dtAcciones = $('#tabla_acciones').DataTable({
                        data: rows,
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
                                            <div class="dt-actions__wrap">
                                                <a href="${base_url}/storage/${o.evidencia}" target="_blank">Ver evidencia</a>
                                                <button type="button" class="btn btn-sm btn-danger" onclick="confirmaEliminaArchivo(${o.id})">
                                                    <i class="fa fa-remove"></i>
                                                </button>
                                            </div>`;
                                    }
                                    return `
                                        <button type="button" class="btn btn-sm btn-success" onclick="modalEdita(${o.id}, 'evidence')">
                                            Subir evidencia <i class="fa fa-upload"></i>
                                        </button>`;
                                }
                            },
                            {
                                title: 'Acciones',
                                data: null,
                                orderable: false,
                                className: 'text-center',
                                render: (_, __, o) => `
                                    <div class="dt-actions__wrap">
                                        <button type="button" class="btn btn-primary btn-icon" title="Editar" onclick="modalEdita(${o.id}, 'edit')">
                                            <i class="fa fa-pencil"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger btn-icon" title="Eliminar" onclick="eliminaAccion(${o.id})">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </div>`
                            }
                        ]
                    });
                } else {
                    // SOLO refrescamos data
                    dtAcciones.clear().rows.add(rows).draw(false);
                }

                if (!(response.ok && data.code === 200)) {
                    swal("¡Error!", (data && data.mensaje) || 'No se pudo cargar Acciones.', "error");
                }
            } catch (err) {
                console.error('getAcciones error:', err);
                swal("¡Error!", "No se pudo cargar Acciones.", "error");
            }
        }

        const confirmaEliminaArchivo = async (id) => {
            const ok = await confirmBS({
                title: 'Eliminar evidencia',
                text: 'El archivo se eliminará de forma permanente.',
                confirmText: 'Sí, eliminar',
                variant: 'danger',
                icon: 'trash'
            });
            if (ok) await eliminaArchivo(id);
        };


        const eliminaArchivo = async (id) => {
            const response = await fetch(`${base_url}/elimina/archivo/acciones/${id}`, {
                method: 'delete',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            const data = await response.json().catch(() => ({}));

            setTimeout(async () => {
                if (response.ok && data.code == 200) {
                    toastr.success(data.mensaje);
                    await getAcciones();
                } else {
                    toastr.error((data && data.mensaje) || 'No se pudo eliminar.');
                }
                fixScrollLock(); // asegura que no quede bloqueado
            }, 200);
        };

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
            const data = await response.json().catch(() => ({}));
            if (response.ok && data.code == 200) {
                $("#accion, #producto_resultado, #fecha_inicio, #fecha_fin").val("");
                await getAcciones();
                $('#modal_agrega_accion').modal('hide');
                toastr.success(data.mensaje);
            } else if (data.code == 411) {
                const first = Object.values(data.errors)[0][0];
                swal("¡Error!", first, "error");
            } else {
                swal("¡Error!", (data && data.mensaje) || 'No se pudo guardar.', "error");
            }
            $("#btn_agregaAccion").prop("disabled", false);
        };

        const eliminaAccion = async (id) => {
            const ok = await confirmBS({
                title: '¿Eliminar acción?',
                text: 'Esta acción no se puede deshacer.',
                confirmText: 'Sí, eliminar',
                variant: 'danger',
                icon: 'exclamation-triangle'
            });
            if (ok) await confirmaElimina(id);
        };


        const confirmaElimina = async (id) => {
            const response = await fetch(`${base_url}/elimina/accion/${id}`, {
                method: 'delete',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            const data = await response.json().catch(() => ({}));
            if (response.ok && data.code == 200) {
                await getAcciones();
                toastr.success(data.mensaje);
            } else {
                swal("¡Error!", (data && data.mensaje) || 'No se pudo eliminar.', "error");
            }
        };

        const abreModal = () => $('#modal_agrega_accion').modal();

        // 'edit' muestra campos de acción, 'evidence' solo el file input
        const modalEdita = async (id, mode = 'edit') => {
            const evidence = mode === 'evidence';
            const form = document.getElementById('form_edita_accion');
            form.reset();
            evidenciaFile = null;
            $('#btn_editaAccion').prop('disabled', false);

            setEditMode(evidence);

            const r = await fetch(`${base_url}/get/detalle/accion/${id}`);
            const data = await r.json().catch(() => ({}));
            if (!r.ok || data.code !== 200) return swal("¡Error!", (data && data.mensaje) || 'No se pudo cargar.',
                "error");

            id_accion = id;

            $('#accion_edit').val(data.data.accion);
            $('#producto_resultado_edit').val(data.data.producto_resultado);
            $('#fecha_inicio_edit').val(data.data.fecha_inicio);
            $('#fecha_fin_edit').val(data.data.fecha_fin);
            $('#responsable_edit').val(data.data.responsable);
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
                const body = new FormData(form);

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
                    body.set('evidencia_edit', file, file.name);
                }

                const resp = await fetch(`${base_url}/edita/accion`, {
                    method: 'post',
                    body,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                let data = await resp.json().catch(() => ({}));

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
                    await getAcciones();
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

        $('#modal_edita_accion').on('hidden.bs.modal', function() {
            $('#btn_editaAccion').prop('disabled', false);
        });

        $('#evidencia_edit').on('change', function() {
            if (this.files && this.files.length) $('#btn_editaAccion').prop('disabled', false);
            evidenciaFile = this.files && this.files[0] ? this.files[0] : null;
        });

        // Indicador clave
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
            const data = await response.json().catch(() => ({}));
            if (response.ok && data.code == 200) {
                indicadorGuardado = true; // <-- ya quedó guardado
                toastr.success(data.mensaje);
            } else if (data.code == 411) {
                const first = Object.values(data.errors)[0][0];
                swal("¡Error!", first, "error");
            } else swal("¡Error!", (data && data.mensaje) || 'No se pudo guardar.', "error");
        };

        // === Evidencia complemento (UI inline) ===
        function _toggleEvidenceUI(hasFile, path) {
            const $pick = $('#btn_pick_evidencia');
            const $view = $('#btn_ver_evidencia');
            const $del = $('#btn_delete_evidencia');
            if (hasFile) {
                if (path) $view.attr('href', `${base_url}/storage/${path}`);
                $pick.hide();
                $view.show();
                $del.show();
            } else {
                $view.hide().attr('href', '#');
                $del.hide();
                $pick.show();
            }
        }
        $('#btn_pick_evidencia').on('click', function() {
            document.getElementById('evidencia').click();
        });
        const confirmaEliminaArchivoComplemento = async (idPlan) => {
            const ok = await confirmBS({
                title: 'Eliminar evidencia',
                text: 'El archivo se eliminará de forma permanente.',
                subtitle: 'Esta acción no se puede deshacer.',
                confirmText: 'Sí, eliminar',
                cancelText: 'Cancelar',
                variant: 'danger',
                icon: 'trash'
            });
            if (ok) await eliminaArchivoComplemento(idPlan);
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
                toastr.success(data.mensaje);
                _toggleEvidenceUI(false);
                $('#evidencia').val('');
            } else {
                toastr.error((data && data.mensaje) || 'No se pudo eliminar.');
            }
            fixScrollLock(); // por si acaso
        };


        async function guardaComplemento(btn) {
            btn.disabled = true;
            try {
                const form = document.getElementById('form_guarda_complemento');
                const fd = new FormData(form);
                fd.set('id_plan', {{ $plan->id }});

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
                        _toggleEvidenceUI(true, data.archivo);
                        $('#evidencia').val('');
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

        $('#evidencia').on('change', async function() {
            const file = this.files && this.files[0] ? this.files[0] : null;
            if (!file) return;

            if (!/\.pdf$/i.test(file.name)) {
                swal('Formato no válido', 'Solo se permite PDF.', 'warning');
                this.value = '';
                return;
            }
            if (file.size > 6 * 1024 * 1024) {
                swal('Archivo muy grande', 'Máx. 6 MB.', 'warning');
                this.value = '';
                return;
            }

            const fd = new FormData();
            fd.set('id_plan', {{ $plan->id }});
            fd.set('evidencia', file, file.name);

            const resp = await fetch(`${base_url}/sube/evidencia/complemento`, {
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
                _toggleEvidenceUI(true, data.archivo);
                this.value = '';
            } else if (resp.status === 411 && data.errors) {
                const first = Object.values(data.errors)[0][0];
                swal('¡Error!', first, 'error');
            } else {
                swal('¡Error!', data.mensaje || 'No se pudo subir el archivo.', 'error');
            }
        });

        // ===================== CONTROL =====================
        let dtCtrl = null;

        async function getActividadesControl() {
            const r = await fetch(`${base_url}/get/actividades/control/{{ $plan->id }}?t=${Date.now()}`, {
                cache: 'no-store'
            });
            const data = await r.json().catch(() => ({}));
            const rows = (r.ok && data && Array.isArray(data.data)) ? data.data : [];

            if (!dtCtrl) {
                $("#div_tabla_control").html(
                    '<table id="tabla_ctrl" class="table table-bordered table-striped" style="width:100%"></table>');
                dtCtrl = $('#tabla_ctrl').DataTable({
                    data: rows,
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
                    columns: [{
                            title: 'Actividad',
                            data: 'actividad'
                        },
                        {
                            title: 'Resultado/Producto',
                            data: 'producto_resultado'
                        },
                        {
                            title: 'Fecha de inicio',
                            data: 'fecha_inicio',
                            className: 'text-center'
                        },
                        {
                            title: 'Fecha de término',
                            data: 'fecha_fin',
                            className: 'text-center'
                        },
                        {
                            title: 'Responsable (nombre o puesto)',
                            data: 'responsable'
                        },
                        {
                            title: 'Acciones',
                            data: null,
                            orderable: false,
                            className: 'text-center',
                            render: (_, __, o) => `
                                <div class="dt-actions__wrap">
                                    <button type="button" class="btn btn-primary btn-icon" onclick="modalEditaControl(${o.id})"><i class="fa fa-pencil"></i></button>
                                    <button type="button" class="btn btn-danger btn-icon" onclick="eliminaActividadControl(${o.id})"><i class="fa fa-trash"></i></button>
                                </div>`
                        }
                    ],
                    autoWidth: false
                });
            } else {
                dtCtrl.clear().rows.add(rows).draw(false);
            }
        }

        function abreModalControl(e) {
            if (e) e.preventDefault();
            $('#titulo_modal_control').text('Agregar actividad de control');
            $('#id_control').val('');
            $('#actividad').val('');
            $('#producto_resultado_ctrl').val('');
            $('#fecha_inicio_ctrl').val('');
            $('#fecha_fin_ctrl').val('');
            $('#responsable_ctrl').val('');
            $('#btn_guardar_control').text('Guardar');

            $('#modal_control')
                .one('shown.bs.modal', () => $('#actividad').trigger('focus'))
                .modal('show');
        }

        async function guardarActividadControl() {
            const id = $('#id_control').val();
            const fd = new FormData();
            fd.set('id_plan', {{ $plan->id }});
            fd.set('actividad', $('#actividad').val());
            fd.set('producto_resultado', $('#producto_resultado_ctrl').val());
            fd.set('fecha_inicio', $('#fecha_inicio_ctrl').val());
            fd.set('fecha_fin', $('#fecha_fin_ctrl').val());
            fd.set('responsable', $('#responsable_ctrl').val());

            const url = id ? `${base_url}/edita/actividad-control` : `${base_url}/guarda/actividad-control`;
            if (id) fd.set('id', id);

            const r = await fetch(url, {
                method: 'POST',
                body: fd,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            const d = await r.json().catch(() => ({}));
            if (r.ok && d.code === 200) {
                $('#modal_control').modal('hide');
                await getActividadesControl();
                toastr.success(d.mensaje);
            } else if (d.code === 411) {
                swal('¡Error!', Object.values(d.errors)[0][0], 'error');
            } else {
                swal('¡Error!', d.mensaje || 'No se pudo guardar', 'error');
            }
        }

        // OJO: Ruta correcta solicitada: /get/detalle/actividad/{id}
        async function modalEditaControl(id) {
            const r = await fetch(`${base_url}/get/detalle/actividad/${id}`, {
                headers: {
                    'Accept': 'application/json'
                }
            });
            if (!r.ok) {
                swal('Error', 'No se encontró la actividad (404).', 'error');
                return;
            }
            const d = await r.json().catch(() => ({}));
            if (d.code !== 200) return swal('Error', d.mensaje || 'No se pudo cargar.', 'error');

            $('#titulo_modal_control').text('Editar actividad de control');
            $('#id_control').val(d.data.id);
            $('#actividad').val(d.data.actividad);
            $('#producto_resultado_ctrl').val(d.data.producto_resultado);
            $('#fecha_inicio_ctrl').val(d.data.fecha_inicio);
            $('#fecha_fin_ctrl').val(d.data.fecha_fin);
            $('#responsable_ctrl').val(d.data.responsable);
            $('#btn_guardar_control').text('Actualizar');
            $('#modal_control').modal('show');
        }

        function eliminaActividadControl(id) {
            (async () => {
                const ok = await confirmBS({
                    title: '¿Eliminar?',
                    text: 'Esta acción no se puede deshacer.',
                    confirmText: 'Sí, eliminar',
                    danger: true
                });
                if (!ok) return;

                const r = await fetch(`${base_url}/elimina/actividad-control/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                const d = await r.json().catch(() => ({}));
                if (r.ok && d.code === 200) {
                    toastr.success(d.mensaje);
                    await getActividadesControl();
                } else {
                    toastr.error(d.mensaje || 'No se pudo eliminar');
                }
                fixScrollLock();
            })();
        }

        // Contadores (texto)
        function updateCount(id, max) {
            const el = document.getElementById(id);
            if (!el) return;
            const hint = document.getElementById(id + '_count');
            const m = max || parseInt(el.getAttribute('maxlength') || '500', 10);
            if (hint) hint.textContent = `${el.value.length}/${m}`;
        }
        ['accion', 'producto_resultado', 'accion_edit', 'producto_resultado_edit', 'actividad', 'producto_resultado_ctrl']
        .forEach(k => {
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

        // Arranque
        (async function init() {
            await getAcciones();
            await getActividadesControl();
        })();
    </script>
@endsection
