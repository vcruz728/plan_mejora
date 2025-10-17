@extends('app')

@section('htmlheader_title')
    Edita Plan de mejora
@endsection

@section('main-content')
    @push('styles')
        <link rel="stylesheet" href="{{ asset('bower_components/select2/css/select2.min.css') }}">
        <link rel="stylesheet"
            href="{{ asset('dist/css/forms-modern.css') }}?v={{ filemtime(public_path('dist/css/forms-modern.css')) }}">
    @endpush

    <section class="content-header">
        <h1 style="text-align: center; margin: 15px 0;">Edita Plan de mejora</h1>
    </section>

    <div class="col-xs-12 form-narrow">
        <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title">
                    Formulario
                    <small style="color:#4d4d4d;">
                        <p style="color:red;display:inline;">*</p> campos obligatorios
                    </small>
                </h3>
            </div>

            <div class="box-body" style="padding-top:1rem;">
                <div class="form-narrow">
                    <form id="form_edita_plan" class="form-compact">
                        {{-- ========= Datos generales ========= --}}
                        <div class="section-card">
                            <div class="section-card__title">Datos generales</div>
                            <div class="section-card__body gutters-sm form-compact">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="required">Tipo</label><br>
                                        <div class="seg-control equal tipo-toggle">
                                            <input class="no-icheck" type="radio" id="tipo_rec" name="tipo"
                                                value="Recomendación" @if ($plan->tipo === 'Recomendación') checked @endif>
                                            <label for="tipo_rec" style="margin:0 15px;">Recomendación</label>

                                            <input class="no-icheck" type="radio" id="tipo_meta" name="tipo"
                                                value="Meta" @if ($plan->tipo === 'Meta') checked @endif>
                                            <label for="tipo_meta" style="margin:0 15px;">Meta</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="procedencia">Procedencia <small class="obligatorio">*</small></label>
                                        <select name="procedencia" id="procedencia" class="form-control"
                                            onchange="tipoProcedencia(this.value)">
                                            <option value="">Seleccione una opción</option>
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
                                        <label for="plan_no">Plan no. <small class="obligatorio">*</small></label>
                                        <input type="text" name="plan_no" id="plan_no" class="form-control"
                                            value="{{ $plan->plan_no }}" readonly>
                                        <small class="help-hint">Se autocompleta con la sigla de la procedencia.</small>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="fecha_creacion">Fecha <small class="obligatorio">*</small></label>
                                        <input type="text" name="fecha_creacion" id="fecha_creacion"
                                            class="form-control w-200" value="{{ $plan->fecha_creacion }}" readonly>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="cantidad">Cantidad <small class="obligatorio">*</small></label>
                                        <input type="number" name="cantidad" id="cantidad" class="form-control w-160"
                                            value="{{ $plan->cantidad }}">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="fecha_vencimiento">Fecha de vencimiento <small
                                                class="obligatorio">*</small></label>
                                        <input type="text" data-provide="datepicker" name="fecha_vencimiento"
                                            id="fecha_vencimiento" class="form-control w-200"
                                            value="{{ $plan->fecha_vencimiento }}">
                                    </div>
                                </div>

                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label for="verifico">Verificó <small class="obligatorio">*</small></label>
                                        <select name="verifico" id="verifico" class="form-control">
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

                                {{-- oculto por compatibilidad --}}
                                <div class="col-md-3" hidden>
                                    <div class="form-group">
                                        <label for="orden">Orden <small class="obligatorio">*</small></label>
                                        <input type="number" name="orden" id="orden" class="form-control"
                                            value="{{ $plan->id }}" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ========= Ubicación / Estructura ========= --}}
                        <div class="section-card">
                            <div class="section-card__title">Ubicación / Estructura</div>
                            <div class="section-card__body gutters-sm">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="tipo_mejora">Seleccione el AC/Dependencia</label>
                                        <select name="tipo_mejora" id="tipo_mejora" class="form-control"
                                            onchange="getDesoDep(this.value)">
                                            <option value="">Seleccione una opción</option>
                                            <option value="1" @if ($plan->tipo_mejora == 1) selected @endif>AC
                                            </option>
                                            <option value="2" @if ($plan->tipo_mejora == 2) selected @endif>
                                                Dependencia</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label for="des" id="labelDes">
                                            @if ($plan->tipo_mejora == 2)
                                                Dependencia
                                            @else
                                                AC
                                            @endif
                                            <small class="obligatorio">*</small>
                                        </label>
                                        <select name="des" id="des" class="form-control"
                                            onchange="getUa(this.value);">
                                            <option value="">Seleccione una opción</option>
                                            @foreach ($des as $value)
                                                <option value="{{ $value->id }}"
                                                    @if ($plan->id_des == $value->id) selected @endif>
                                                    {{ $value->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div id="div_des" class="col-md-12" @if ($plan->tipo_mejora == 2) hidden @endif>
                                    <div class="row gutters-sm">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="unidad_academica">Unidad académica</label>
                                                <select name="unidad_academica" id="unidad_academica"
                                                    class="form-control" onchange="getSedes(this.value);">
                                                    <option value="">Seleccione una opción</option>
                                                    @foreach ($unidades as $value)
                                                        <option value="{{ $value->id }}"
                                                            @if ($plan->id_ua == $value->id) selected @endif>
                                                            {{ $value->nombre }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="sede">Sede</label>
                                                <select name="sede" id="sede" class="form-control"
                                                    onchange="getProgramas(this.value);">
                                                    <option value="">Seleccione una opción</option>
                                                    @foreach ($sedes as $value)
                                                        <option value="{{ $value->id }}"
                                                            @if ($plan->id_sede == $value->id) selected @endif>
                                                            {{ $value->nombre }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="programa_educativo">Programa educativo</label>
                                                <select name="programa_educativo" id="programa_educativo"
                                                    class="form-control" onchange="getNiveles(this.value);">
                                                    <option value="">Seleccione una opción</option>
                                                    @foreach ($programasEducativos as $value)
                                                        <option value="{{ $value->id }}"
                                                            @if ($plan->id_programa_educativo == $value->id) selected @endif>
                                                            {{ $value->nombre }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="nivel">Nivel</label>
                                                <select name="nivel" id="nivel" class="form-control"
                                                    onchange="getModalidades(this.value);">
                                                    <option value="">Seleccione una opción</option>
                                                    @foreach ($nivelesEstudio as $value)
                                                        <option value="{{ $value->id }}"
                                                            @if ($plan->id_nivel_estudio == $value->id) selected @endif>
                                                            {{ $value->nombre }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="modalidad">Modalidad</label>
                                                <select name="modalidad" id="modalidad" class="form-control">
                                                    <option value="">Seleccione una opción</option>
                                                    @foreach ($modalidad as $value)
                                                        <option value="{{ $value->id }}"
                                                            @if ($plan->id_modalidad_estudio == $value->id) selected @endif>
                                                            {{ $value->nombre }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ========= Descripción ========= --}}
                        <div class="section-card">
                            <div class="section-card__title">Descripción</div>
                            <div class="section-card__body">
                                <div class="form-group">
                                    <label for="recomendacion_meta">Recomendación/Meta <small
                                            class="obligatorio">*</small></label>
                                    <textarea name="recomendacion_meta" id="recomendacion_meta" class="form-control"
                                        style="min-width:100%;max-width:100%;min-height:115px;">{{ $plan->recomendacion_meta }}</textarea>
                                </div>
                            </div>
                        </div>

                        {{-- ========= Alineación PDI / SEAES ========= --}}
                        <div class="section-card">
                            <div class="section-card__title">Alineación PDI / SEAES</div>
                            <div class="row gutters-sm section-card__body">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="eje_pdi">Eje PDI <small class="obligatorio">*</small></label>
                                        <select name="eje_pdi" id="eje_pdi" class="form-control"
                                            onchange="getOdsPdi(this.value)">
                                            <option value="">Seleccione una opción</option>
                                            @foreach ($ejes as $value)
                                                <option value="{{ $value->id }}"
                                                    @if ($plan->eje_pdi == $value->id) selected @endif>
                                                    {{ $value->descripcion }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="ods_pdi_select">ODS PDI <small class="obligatorio">*</small></label>
                                        <select name="ods_pdi_select" id="ods_pdi_select" class="form-control"
                                            onchange="getObjetivosPdi(this.value)">
                                            @foreach ($ods as $value)
                                                <option value="{{ $value->id }}"
                                                    @if ($plan->id_ods_pdi == $value->id) selected @endif>
                                                    {{ $value->descripcion }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="objetivo_pdi">Objetivo PDI <small
                                                class="obligatorio">*</small></label>
                                        <select name="objetivo_pdi" id="objetivo_pdi" class="form-control"
                                            onchange="getEstrategiasPdi(this.value)">
                                            <option value="">Seleccione una opción</option>
                                            @foreach ($objetivos as $value)
                                                <option value="{{ $value->id }}"
                                                    @if ($plan->objetivo_pdi == $value->id) selected @endif>
                                                    {{ $value->descripcion }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="estrategias">Estrategias PDI <small
                                                class="obligatorio">*</small></label>
                                        <select name="estrategias" id="estrategias" class="form-control"
                                            onchange="getIndicadoresMetas(this.value)">
                                            <option value="">Seleccione una opción</option>
                                            @foreach ($estategias as $value)
                                                <option value="{{ $value->id }}"
                                                    @if ($plan->id_estrategia == $value->id) selected @endif>
                                                    {{ $value->descripcion }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="meta_pdi">Meta PDI <small class="obligatorio">*</small></label>
                                        <select name="meta_pdi" id="meta_pdi" class="form-control">
                                            <option value="">Seleccione una opción</option>
                                            @foreach ($metas as $value)
                                                <option value="{{ $value->id }}"
                                                    @if ($plan->id_meta == $value->id) selected @endif>
                                                    {{ $value->descripcion }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="indicador_pdi">Indicador PDI</label>
                                        <input type="text" name="indicador_pdi" id="indicador_pdi"
                                            class="form-control" value="{{ $plan->indicador_pdi }}">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="ambito_siemec">Ámbito SEAES <small
                                                class="obligatorio">*</small></label>
                                        <select name="ambito_siemec" id="ambito_siemec" class="form-control">
                                            <option value="">Seleccione una opción</option>
                                            @foreach ($ambitos as $value)
                                                <option value="{{ $value->id }}"
                                                    @if ($plan->ambito_siemec == $value->id) selected @endif>
                                                    {{ $value->descripcion }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="criterio_siemec">Criterio SEAES <small
                                                class="obligatorio">*</small></label>
                                        <select name="criterio_siemec" id="criterio_siemec" class="form-control">
                                            <option value="">Seleccione una opción</option>
                                            @foreach ($criterios as $value)
                                                <option value="{{ $value->id }}"
                                                    @if ($plan->criterio_siemec == $value->id) selected @endif>
                                                    {{ $value->descripcion }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button class="btn btn-success" id="btn_edita"
                                onclick="event.preventDefault(); guardaCambios(); this.disabled=true;">
                                Guardar cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('localscripts')
    <script src="{{ asset('bower_components/select2/js/select2.min.js') }}"></script>
    <script>
        var base_url = $("input[name='base_url']").val();

        $('#fecha_vencimiento').datepicker({
            dateFormat: 'yy-mm-dd'
        }).datepicker();

        // Select2 donde aplica
        $('#ods_pdi_select').select2();
        $('#objetivo_pdi').select2();

        const guardaCambios = async () => {
            const body = new FormData(document.getElementById('form_edita_plan'));
            body.append('tipo_plan', $('input[name="tipo"]:checked').val());
            body.append('id_plan', {{ $plan->id }});

            const response = await fetch(`${base_url}/admin/guarda/actualizacion/plan-mejora`, {
                method: 'post',
                body,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            const data = await response.json();

            if (data.code == 200) {
                swal("¡Correcto!", data.mensaje, "success");
            } else if (data.code == 411) {
                let num = 0;
                $.each(data.errors, function(key, value) {
                    if (num++ == 0) swal("¡Error!", value[0], "error");
                });
            } else {
                swal("¡Error!", data.mensaje, "error");
            }
            $("#btn_edita").removeAttr("disabled");
        };

        // Autocompletar plan_no desde siglas (mismo comportamiento que en "nuevo")
        const tipoProcedencia = async (valor) => {
            const resp = await fetch(`${base_url}/admin/get/siglas-procedencia/${valor}`);
            const data = await resp.json();
            $("#plan_no").val('');
            if (data?.data?.siglas) {
                $("#plan_no").val(`${data.data.siglas}-`);
            }
        };

        // Resto de helpers (idénticos a tu versión)
        const getOdsPdi = async (valor) => {
            const r = await fetch(`${base_url}/admin/get/ods-pdi/${valor}`);
            const data = await r.json();
            if (data.code == 200) {
                $("#estrategias,#meta_pdi,#objetivo_pdi,#indicador_pdi").html(
                    `<option value="">Seleccione una opción</option>`);
                $("#ods_pdi_select").html(`<option value="">Seleccione una opción</option>`);
                data.data.map(x => $("#ods_pdi_select").append(
                    `<option value="${x.id}">${x.descripcion}</option>`));
                $('#ods_pdi_select').select2();
            } else swal("¡Error!", data.mensaje, "error");
        };

        const getObjetivosPdi = async (valor) => {
            const r = await fetch(`${base_url}/admin/get/objetivos-pdi/${valor}`);
            const data = await r.json();
            if (data.code == 200) {
                $("#estrategias,#meta_pdi,#objetivo_pdi,#indicador_pdi").html(
                    `<option value="">Seleccione una opción</option>`);
                data.data.map(x => $("#objetivo_pdi").append(`<option value="${x.id}">${x.descripcion}</option>`));
                $('#objetivo_pdi').select2();
            } else swal("¡Error!", data.mensaje, "error");
        };

        const getEstrategiasPdi = async (valor) => {
            const r = await fetch(`${base_url}/admin/get/estrategias-pdi/${valor}`);
            const data = await r.json();
            if (data.code == 200) {
                $("#meta_pdi").html(`<option value="">Seleccione una opción</option>`);
                $("#estrategias").html(`<option value="">Seleccione una opción</option>`);
                data.data.map(x => $("#estrategias").append(`<option value="${x.id}">${x.descripcion}</option>`));
                $('#estrategias').select2();
            } else swal("¡Error!", data.mensaje, "error");
        };

        const getIndicadoresMetas = async (valor) => {
            const r = await fetch(`${base_url}/admin/get/metas-pdi/${valor}`);
            const data = await r.json();
            if (data.code == 200) {
                $("#meta_pdi").html(`<option value="">Seleccione una opción</option>`);
                data.data.map(x => $("#meta_pdi").append(`<option value="${x.id}">${x.descripcion}</option>`));
                $('#meta_pdi').select2();
            } else swal("¡Error!", data.mensaje, "error");
        };

        const getDesoDep = async (valor) => {
            const r = await fetch(`${base_url}/admin/get/des-o-dependencias/${valor}`);
            const data = await r.json();

            if (valor == 2) {
                $("#div_des").attr('hidden', true);
                $("#labelDes").html(`Dependencia <small><p class="obligatorio">*</p></small>`);
            } else {
                $("#div_des").removeAttr('hidden');
                $("#labelDes").html(`AC <small><p class="obligatorio">*</p></small>`);
            }

            $("#des,#unidad_academica,#sede,#programa_educativo,#nivel,#modalidad")
                .html(`<option value="">Seleccione una opción</option>`);

            if (data.code == 200) {
                data.data.forEach(({
                    id,
                    nombre
                }) => {
                    $("#des").append($('<option>').val(id).text(`${nombre}`));
                });
            } else swal("¡Error!", data.mensaje, "error");
        };

        const getUa = async (valor) => {
            const r = await fetch(`${base_url}/admin/get/unidades/${valor}`);
            const data = await r.json();
            $("#unidad_academica,#sede,#programa_educativo,#nivel,#modalidad")
                .html(`<option value="">Seleccione una opción</option>`);
            if (data.code == 200) {
                data.data.forEach(({
                    id,
                    nombre
                }) => {
                    $("#unidad_academica").append($('<option>').val(id).text(`${nombre}`));
                });
            } else swal("¡Error!", data.mensaje, "error");
        };

        const getSedes = async (valor) => {
            const r = await fetch(`${base_url}/admin/get/sedes/${valor}`);
            const data = await r.json();
            $("#sede,#programa_educativo,#nivel,#modalidad")
                .html(`<option value="">Seleccione una opción</option>`);
            if (data.code == 200) {
                data.data.forEach(({
                    id,
                    nombre
                }) => {
                    $("#sede").append($('<option>').val(id).text(`${nombre}`));
                });
            } else swal("¡Error!", data.mensaje, "error");
        };

        const getProgramas = async (valor) => {
            const r = await fetch(`${base_url}/admin/get/programas/${valor}`);
            const data = await r.json();
            $("#programa_educativo,#nivel,#modalidad")
                .html(`<option value="">Seleccione una opción</option>`);
            if (data.code == 200) {
                data.data.forEach(({
                    id,
                    nombre
                }) => {
                    $("#programa_educativo").append($('<option>').val(id).text(`${nombre}`));
                });
            } else swal("¡Error!", data.mensaje, "error");
        };

        const getNiveles = async (valor) => {
            const r = await fetch(`${base_url}/admin/get/niveles/${valor}`);
            const data = await r.json();
            $("#nivel,#modalidad").html(`<option value="">Seleccione una opción</option>`);
            if (data.code == 200) {
                data.data.forEach(({
                    id,
                    nombre
                }) => {
                    $("#nivel").append($('<option>').val(id).text(`${nombre}`));
                });
            } else swal("¡Error!", data.mensaje, "error");
        };

        const getModalidades = async (valor) => {
            const r = await fetch(`${base_url}/admin/get/modalidades/${valor}`);
            const data = await r.json();
            $("#modalidad").html(`<option value="">Seleccione una opción</option>`);
            if (data.code == 200) {
                data.data.forEach(({
                    id,
                    nombre
                }) => {
                    $("#modalidad").append($('<option>').val(id).text(`${nombre}`));
                });
            } else swal("¡Error!", data.mensaje, "error");
        };

        // Estado inicial para UA/Dependencia al cargar
        $(function() {
            const preset = $('#tipo_mejora').val();
            if (preset === '2') {
                $("#div_des").attr('hidden', true);
                $("#labelDes").html(`Dependencia <small><p class="obligatorio">*</p></small>`);
            }
        });
    </script>
@endsection
