@extends('app')

@section('htmlheader_title')
    Acciones Plan de mejora
@endsection

@section('main-content')
    @push('styles')
        <link rel="stylesheet" href="{{ asset('bower_components/select2/css/select2.min.css') }}">
        <link rel="stylesheet"
            href="{{ asset('dist/css/forms-modern.css') }}?v={{ filemtime(public_path('dist/css/forms-modern.css')) }}">
        <style>
            /* Solo para esta vista de lectura */
            .seg-control.is-disabled {
                opacity: .6;
                pointer-events: none;
            }

            .section-card input[disabled],
            .section-card select[disabled],
            .section-card textarea[disabled] {
                background: #f9fafb;
                color: #334155;
            }
        </style>
    @endpush

    <section class="content-header">
        <h1 style="text-align: center; margin: 15px 0;">Acciones Plan de mejora</h1>
    </section>

    <div class="col-xs-12 form-narrow">
        <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title">Formulario</h3>
            </div>

            <div class="box-body" style="padding-top:1rem;">
                <div class="form-narrow">
                    {{-- ====== Datos generales ====== --}}
                    <div class="section-card">
                        <div class="section-card__title">Datos generales</div>
                        <div class="section-card__body gutters-sm form-compact">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Tipo</label><br>
                                    <div class="seg-control equal tipo-toggle is-disabled">
                                        <input class="no-icheck" type="radio" id="tipo_rec" name="tipo"
                                            value="Recomendación" @if ($plan->tipo == 'Recomendación') checked @endif disabled>
                                        <label for="tipo_rec" style="margin:0 15px;">Recomendación</label>

                                        <input class="no-icheck" type="radio" id="tipo_meta" name="tipo" value="Meta"
                                            @if ($plan->tipo == 'Meta') checked @endif disabled>
                                        <label for="tipo_meta" style="margin:0 15px;">Meta</label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="procedencia">Procedencia</label>
                                    <select id="procedencia" class="form-control" disabled>
                                        @foreach ($procedencias as $value)
                                            <option value="{{ $value->id }}"
                                                @if ($plan->procedencia == $value->id) selected @endif>
                                                {{ $value->descripcion }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="plan_no">Plan no.</label>
                                    <input id="plan_no" class="form-control" value="{{ $plan->plan_no }}" disabled>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="fecha_creacion">Fecha</label>
                                    <input id="fecha_creacion" class="form-control w-200"
                                        value="{{ $plan->fecha_creacion }}" disabled>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="cantidad">Cantidad</label>
                                    <input id="cantidad" class="form-control w-160" value="{{ $plan->cantidad }}" disabled>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="fecha_vencimiento">Fecha de vencimiento</label>
                                    <input id="fecha_vencimiento" class="form-control w-200"
                                        value="{{ $plan->fecha_vencimiento }}" disabled>
                                </div>
                            </div>

                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="verifico">Verificó</label>
                                    <select id="verifico" class="form-control" disabled>
                                        <option value="">Seleccione una opción</option>
                                        @foreach ($verificadores as $value)
                                            <option value="{{ $value->id }}"
                                                @if ($plan->verifico == $value->id) selected @endif>
                                                {{ $value->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ====== Ubicación / Estructura ====== --}}
                    <div class="section-card">
                        <div class="section-card__title">Ubicación / Estructura</div>
                        <div class="section-card__body gutters-sm">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="tipo_mejora">AC/Dependencia</label>
                                    <select id="tipo_mejora" class="form-control" disabled>
                                        <option value="">Seleccione una opción</option>
                                        <option value="1" @if ($plan->tipo_mejora == 1) selected @endif>AC</option>
                                        <option value="2" @if ($plan->tipo_mejora == 2) selected @endif>Dependencia
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="des">
                                        @if ($plan->tipo_mejora == 2)
                                            Dependencia
                                        @else
                                            AC
                                        @endif
                                    </label>
                                    <input id="des" class="form-control" value="{{ $plan->des }}" disabled>
                                </div>
                            </div>

                            <div id="div_des" class="col-md-12" @if ($plan->tipo_mejora == 2) hidden @endif>
                                <div class="row gutters-sm">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="unidad_academica">Unidad académica</label>
                                            <input id="unidad_academica" class="form-control"
                                                value="{{ $plan->unidad_academica }}" disabled>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="sede">Sede</label>
                                            <input id="sede" class="form-control" value="{{ $plan->sede }}"
                                                disabled>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="programa_educativo">Programa educativo</label>
                                            <input id="programa_educativo" class="form-control"
                                                value="{{ $plan->programa_educativo }}" disabled>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="nivel">Nivel</label>
                                            <input id="nivel" class="form-control" value="{{ $plan->nivel }}"
                                                disabled>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="modalidad">Modalidad</label>
                                            <input id="modalidad" class="form-control" value="{{ $plan->modalidad }}"
                                                disabled>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ====== Descripción ====== --}}
                    <div class="section-card">
                        <div class="section-card__title">Descripción</div>
                        <div class="section-card__body">
                            <div class="form-group">
                                <label for="recomendacion_meta">Recomendación/Meta</label>
                                <textarea id="recomendacion_meta" class="form-control" style="min-width:100%;max-width:100%;min-height:115px;"
                                    disabled>{{ $plan->recomendacion_meta }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- ====== Alineación PDI / SEAES ====== --}}
                    <div class="section-card">
                        <div class="section-card__title">Alineación PDI / SEAES</div>
                        <div class="row gutters-sm section-card__body">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="eje_pdi">Eje PDI</label>
                                    <input id="eje_pdi" class="form-control" value="{{ $plan->eje_pdi }}" disabled>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="ods_pdi_select">ODS PDI</label>
                                    <input id="ods_pdi_select" class="form-control" value="{{ $plan->ods }}"
                                        disabled>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="objetivo_pdi">Objetivo PDI</label>
                                    <input id="objetivo_pdi" class="form-control" value="{{ $plan->objetivo_pdi }}"
                                        disabled>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="estrategias">Estrategias</label>
                                    <input id="estrategias" class="form-control" value="{{ $plan->estrategia }}"
                                        disabled>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="meta_pdi">Meta</label>
                                    <input id="meta_pdi" class="form-control" value="{{ $plan->meta }}" disabled>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="indicador_pdi">Indicador PDI</label>
                                    <input id="indicador_pdi" class="form-control" value="{{ $plan->indicador_pdi }}"
                                        disabled>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="ambito_siemec">Ámbito SEAES</label>
                                    <input id="ambito_siemec" class="form-control" value="{{ $plan->ambito_siemec }}"
                                        disabled>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="criterio_siemec">Criterio SEAES</label>
                                    <input id="criterio_siemec" class="form-control"
                                        value="{{ $plan->criterio_siemec }}" disabled>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ====== Acciones ====== --}}
                    <div class="section-card">
                        <div class="section-card__title">Acciones</div>
                        <div class="section-card__body">
                            <div id="div_tabla_acciones"></div>
                        </div>
                    </div>

                    {{-- ====== Complemento ====== --}}
                    <div class="section-card">
                        <div class="section-card__title">Complemento</div>
                        <div class="row gutters-sm section-card__body">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="indicador_clave">Indicador clave</label>
                                    <input id="indicador_clave" class="form-control"
                                        value="{{ $complemento?->indicador_clave }}" disabled>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="logros">Logros</label>
                                    <input id="logros" class="form-control" value="{{ $complemento?->logros }}"
                                        disabled>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="impactos">Impactos</label>
                                    <input id="impactos" class="form-control" value="{{ $complemento?->impactos }}"
                                        disabled>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="observaciones">Observaciones</label>
                                    <textarea id="observaciones" class="form-control" style="min-width:100%;max-width:100%;" disabled
                                        placeholder="Máximo 600 caracteres">{{ $complemento?->observaciones }}</textarea>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Evidencia</label><br>
                                    @if ($complemento?->archivo)
                                        <a href="{{ asset('storage/' . $complemento?->archivo) }}" target="_BLANK">Ver
                                            archivo</a>
                                    @else
                                        <span class="help-hint">Sin archivo adjunto.</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- /Complemento --}}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('localscripts')
    <script src="{{ asset('bower_components/select2/js/select2.min.js') }}"></script>
    <script>
        var base_url = $("input[name='base_url']").val();

        const getAcciones = async () => {
            if (typeof mostrarLoader === 'function') {
                mostrarLoader('div_tabla_acciones', 'Espere un momento...');
            }
            const response = await fetch(`${base_url}/get/acciones/plan/{{ $plan->id }}`, {
                method: 'get'
            });
            $("#div_tabla_acciones").html(
                `<table class="table table-bordered table-striped" id="tabla_acciones"></table>`);
            const data = await response.json();

            if (data.code == 200) {
                $("#tabla_acciones").DataTable({
                    data: data.data,
                    scrollX: true,
                    searching: true,
                    ordering: true,
                    info: false,
                    paging: true,
                    autoWidth: true,
                    language: {
                        url: base_url + '/js/Spanish.json'
                    },
                    columns: [{
                            title: "Acción",
                            data: 'accion'
                        },
                        {
                            title: "Producto/Resultado",
                            data: 'producto_resultado'
                        },
                        {
                            title: "Fecha de inicio",
                            data: 'fecha_inicio'
                        },
                        {
                            title: "Fecha de término",
                            data: 'fecha_fin'
                        },
                        {
                            title: 'Evidencia',
                            defaultContent: '',
                            fnCreatedCell: (nTd, sData, oData) => {
                                if (oData.evidencia) {
                                    $(nTd).append(`
                                    <div style="text-align:center;">
                                        <a href="${base_url}/storage/${oData.evidencia}" target="_BLANK">Ver evidencia</a>
                                    </div>
                                `);
                                }
                            }
                        }
                    ]
                });
            } else {
                swal("¡Error!", data.mensaje, "error");
            }
        };

        // Carga inicial
        getAcciones();
    </script>
@endsection
