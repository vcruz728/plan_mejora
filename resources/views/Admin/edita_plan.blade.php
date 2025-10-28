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
        // ====== Pickers / select2 existentes ======
        $('.datepicker, [data-provide="datepicker"]').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true,
            language: 'es'
        });
        $('#ods_pdi_select').select2();
        $('#objetivo_pdi').select2();

        // ====== Guardar ======
        async function guardaCambios() {
            const body = new FormData(document.getElementById('form_edita_plan'));
            body.append('tipo_plan', $('input[name="tipo"]:checked').val());
            body.append('id_plan', {{ $plan->id }});
            try {
                const resp = await fetch(`${base_url}/admin/guarda/actualizacion/plan-mejora`, {
                    method: 'POST',
                    body,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                const data = await resp.json();
                if (data.code === 200) swal('¡Correcto!', data.mensaje, 'success');
                else if (data.code === 411) {
                    const first = Object.values(data.errors)[0][0];
                    swal('¡Error!', first, 'error');
                } else swal('¡Error!', data.mensaje || 'No se pudo guardar.', 'error');
            } finally {
                $("#btn_edita").prop("disabled", false);
            }
        }
        window.guardaCambios = guardaCambios; // expone para el botón

        // ====== Autocompleta plan_no desde procedencia ======
        async function tipoProcedencia(valor) {
            const resp = await fetch(`${base_url}/admin/get/siglas-procedencia/${valor}`);
            const data = await resp.json();
            $("#plan_no").val(data?.data?.siglas ? `${data.data.siglas}-` : '');
        }
        window.tipoProcedencia = tipoProcedencia;

        // ================== MEMORIA + CACHÉ (AC/Dependencia) ==================
        let prefilling = false; // evita handlers durante precarga
        let currentTipo = ''; // '1' AC, '2' Dependencia

        // Memoria de lo que el usuario eligió/capturó por tipo
        const formMemory = {
            '1': null,
            '2': null
        };


        // Helpers de fetch + caché
        async function getDesCached(tipo) {
            if (!listsCache.des[tipo]) {
                const r = await fetch(`${base_url}/admin/get/des-o-dependencias/${tipo}`);
                const j = await r.json();
                listsCache.des[tipo] = j.data || [];
            }
            return listsCache.des[tipo];
        }
        async function getUaCached(idDes) {
            if (!listsCache.ua[idDes]) {
                const r = await fetch(`${base_url}/admin/get/unidades/${idDes}`);
                const j = await r.json();
                listsCache.ua[idDes] = j.data || [];
            }
            return listsCache.ua[idDes];
        }
        async function getSedesCached(idUa) {
            if (!listsCache.sedes[idUa]) {
                const r = await fetch(`${base_url}/admin/get/sedes/${idUa}`);
                const j = await r.json();
                listsCache.sedes[idUa] = j.data || [];
            }
            return listsCache.sedes[idUa];
        }
        async function getProgramasCached(idSede) {
            if (!listsCache.programas[idSede]) {
                const r = await fetch(`${base_url}/admin/get/programas/${idSede}`);
                const j = await r.json();
                listsCache.programas[idSede] = j.data || [];
            }
            return listsCache.programas[idSede];
        }
        async function getNivelesCached(idProg) {
            if (!listsCache.niveles[idProg]) {
                const r = await fetch(`${base_url}/admin/get/niveles/${idProg}`);
                const j = await r.json();
                listsCache.niveles[idProg] = j.data || [];
            }
            return listsCache.niveles[idProg];
        }
        async function getModalidadesCached(idNivel) {
            if (!listsCache.modalidades[idNivel]) {
                const r = await fetch(`${base_url}/admin/get/modalidades/${idNivel}`);
                const j = await r.json();
                listsCache.modalidades[idNivel] = j.data || [];
            }
            return listsCache.modalidades[idNivel];
        }

        // Rellena un select con items [{id,nombre}] y selecciona "selectedId"
        function fillSelect($sel, items = [], selectedId = '') {
            const frag = document.createDocumentFragment();
            const empty = new Option('', '', false, false);
            empty.value = '';
            frag.appendChild(empty);
            items.forEach(({
                id,
                nombre
            }) => {
                frag.appendChild(new Option(nombre, String(id), false, String(id) === String(selectedId)));
            });
            const el = $sel[0];
            el.innerHTML = '';
            el.appendChild(frag);
            $sel.val(String(selectedId || ''));
            // si algún día pones select2 en estos combos, descomenta:
            // $sel.trigger('change.select2');
        }

        // Guarda estado actual en memoria del tipo
        function remember(tipo) {
            if (!tipo) return;
            formMemory[tipo] = {
                des: $('#des').val() || '',
                ua: $('#unidad_academica').val() || '',
                sede: $('#sede').val() || '',
                prog: $('#programa_educativo').val() || '',
                nivel: $('#nivel').val() || '',
                mod: $('#modalidad').val() || ''
            };
        }

        // Lee opciones del DOM para “sembrar” el caché en el tipo actual (arranca más rápido)
        function optionList($sel) {
            const out = [];
            $sel.find('option').each(function() {
                const v = $(this).attr('value');
                if (v !== '' && v != null) out.push({
                    id: v,
                    nombre: $(this).text()
                });
            });
            return out;
        }

        function seedCacheFromDOM() {
            const t = $('#tipo_mejora').val() || '';
            if (!t) return;
            const idDes = $('#des').val() || '';
            const idUa = $('#unidad_academica').val() || '';
            const idSede = $('#sede').val() || '';
            const idProg = $('#programa_educativo').val() || '';
            const idNivel = $('#nivel').val() || '';

            const desList = optionList($('#des'));
            if (desList.length) listsCache.des[t] = desList;
            const uaList = optionList($('#unidad_academica'));
            if (uaList.length && idDes) listsCache.ua[idDes] = uaList;
            const sedList = optionList($('#sede'));
            if (sedList.length && idUa) listsCache.sedes[idUa] = sedList;
            const proList = optionList($('#programa_educativo'));
            if (proList.length && idSede) listsCache.programas[idSede] = proList;
            const nivList = optionList($('#nivel'));
            if (nivList.length && idProg) listsCache.niveles[idProg] = nivList;
            const modList = optionList($('#modalidad'));
            if (modList.length && idNivel) listsCache.modalidades[idNivel] = modList;
        }

        // Restaura memoria + listas para un tipo
        async function restore(tipo) {
            const snap = formMemory[tipo] || {};
            prefilling = true;

            if (tipo === '2') { // Dependencia
                $('#div_des').attr('hidden', true);
                $('#labelDes').text('Dependencia');
            } else {
                $('#div_des').removeAttr('hidden');
                $('#labelDes').text('AC');
            }

            // DES
            const desList = await getDesCached(tipo);
            fillSelect($('#des'), desList, snap.des);

            if (tipo === '2') {
                // Solo DES; limpia resto
                fillSelect($('#unidad_academica'), [], '');
                fillSelect($('#sede'), [], '');
                fillSelect($('#programa_educativo'), [], '');
                fillSelect($('#nivel'), [], '');
                fillSelect($('#modalidad'), [], '');
                prefilling = false;
                return;
            }

            // UA
            if (snap.des) fillSelect($('#unidad_academica'), await getUaCached(snap.des), snap.ua);
            else fillSelect($('#unidad_academica'), [], '');

            // Sede
            if (snap.ua) fillSelect($('#sede'), await getSedesCached(snap.ua), snap.sede);
            else fillSelect($('#sede'), [], '');

            // Programa
            if (snap.sede) fillSelect($('#programa_educativo'), await getProgramasCached(snap.sede), snap.prog);
            else fillSelect($('#programa_educativo'), [], '');

            // Nivel
            if (snap.prog) fillSelect($('#nivel'), await getNivelesCached(snap.prog), snap.nivel);
            else fillSelect($('#nivel'), [], '');

            // Modalidad
            if (snap.nivel) fillSelect($('#modalidad'), await getModalidadesCached(snap.nivel), snap.mod);
            else fillSelect($('#modalidad'), [], '');

            prefilling = false;
        }

        // ====== “Sobreescribo” las funciones que llama tu HTML (onchange="...") ======
        window.getDesoDep = async function(valor) {
            // Cambiar de AC↔︎Dependencia preservando lo capturado
            const nuevo = String(valor || '');
            if (nuevo === currentTipo) return;
            remember(currentTipo);
            currentTipo = nuevo;
            await restore(currentTipo);
        };

        window.getUa = async function(idDes) {
            if (prefilling || currentTipo === '2') return; // En Dependencia no aplica cascada
            // limpia abajo
            fillSelect($('#unidad_academica'), [], '');
            fillSelect($('#sede'), [], '');
            fillSelect($('#programa_educativo'), [], '');
            fillSelect($('#nivel'), [], '');
            fillSelect($('#modalidad'), [], '');
            if (idDes) {
                const list = await getUaCached(idDes);
                fillSelect($('#unidad_academica'), list, '');
            }
            remember(currentTipo);
        };

        window.getSedes = async function(idUa) {
            if (prefilling || currentTipo === '2') return;
            fillSelect($('#sede'), [], '');
            fillSelect($('#programa_educativo'), [], '');
            fillSelect($('#nivel'), [], '');
            fillSelect($('#modalidad'), [], '');
            if (idUa) {
                const list = await getSedesCached(idUa);
                fillSelect($('#sede'), list, '');
            }
            remember(currentTipo);
        };

        window.getProgramas = async function(idSede) {
            if (prefilling || currentTipo === '2') return;
            fillSelect($('#programa_educativo'), [], '');
            fillSelect($('#nivel'), [], '');
            fillSelect($('#modalidad'), [], '');
            if (idSede) {
                const list = await getProgramasCached(idSede);
                fillSelect($('#programa_educativo'), list, '');
            }
            remember(currentTipo);
        };

        window.getNiveles = async function(idProg) {
            if (prefilling || currentTipo === '2') return;
            fillSelect($('#nivel'), [], '');
            fillSelect($('#modalidad'), [], '');
            if (idProg) {
                const list = await getNivelesCached(idProg);
                fillSelect($('#nivel'), list, '');
            }
            remember(currentTipo);
        };

        window.getModalidades = async function(idNivel) {
            if (prefilling || currentTipo === '2') return;
            fillSelect($('#modalidad'), [], '');
            if (idNivel) {
                const list = await getModalidadesCached(idNivel);
                fillSelect($('#modalidad'), list, '');
            }
            remember(currentTipo);
        };

        // ====== PDI/SEAEs (como ya los tenías) ======
        window.getOdsPdi = async function(valor) {
            const r = await fetch(`${base_url}/admin/get/ods-pdi/${valor}`);
            const data = await r.json();
            if (data.code === 200) {
                $("#estrategias,#meta_pdi,#objetivo_pdi,#indicador_pdi").html(
                    `<option value="">Seleccione una opción</option>`);
                $("#ods_pdi_select").html(`<option value="">Seleccione una opción</option>`);
                data.data.forEach(x => $("#ods_pdi_select").append(
                    `<option value="${x.id}">${x.descripcion}</option>`));
                $('#ods_pdi_select').select2();
            } else swal("¡Error!", data.mensaje, "error");
        };
        window.getObjetivosPdi = async function(valor) {
            const r = await fetch(`${base_url}/admin/get/objetivos-pdi/${valor}`);
            const data = await r.json();
            if (data.code === 200) {
                $("#estrategias,#meta_pdi,#objetivo_pdi,#indicador_pdi").html(
                    `<option value="">Seleccione una opción</option>`);
                data.data.forEach(x => $("#objetivo_pdi").append(
                    `<option value="${x.id}">${x.descripcion}</option>`));
                $('#objetivo_pdi').select2();
            } else swal("¡Error!", data.mensaje, "error");
        };
        window.getEstrategiasPdi = async function(valor) {
            const r = await fetch(`${base_url}/admin/get/estrategias-pdi/${valor}`);
            const data = await r.json();
            if (data.code === 200) {
                $("#meta_pdi").html(`<option value="">Seleccione una opción</option>`);
                $("#estrategias").html(`<option value="">Seleccione una opción</option>`);
                data.data.forEach(x => $("#estrategias").append(
                    `<option value="${x.id}">${x.descripcion}</option>`));
                $('#estrategias').select2();
            } else swal("¡Error!", data.mensaje, "error");
        };
        window.getIndicadoresMetas = async function(valor) {
            const r = await fetch(`${base_url}/admin/get/metas-pdi/${valor}`);
            const data = await r.json();
            if (data.code === 200) {
                $("#meta_pdi").html(`<option value="">Seleccione una opción</option>`);
                data.data.forEach(x => $("#meta_pdi").append(`<option value="${x.id}">${x.descripcion}</option>`));
                $('#meta_pdi').select2();
            } else swal("¡Error!", data.mensaje, "error");
        };

        // ====== Estado inicial al cargar ======
        $(function() {
            // Ajusta visibilidad según valor inicial
            const preset = $('#tipo_mejora').val();
            currentTipo = String(preset || '');
            if (currentTipo === '2') {
                $('#div_des').attr('hidden', true);
                $('#labelDes').text('Dependencia');
            } else {
                $('#div_des').removeAttr('hidden');
                $('#labelDes').text('AC');
            }

            // Si el servidor ya trajo combos llenos, siembro el caché para que alternar sea inmediato
            seedCacheFromDOM();
            // Guardo snapshot inicial del tipo actual
            remember(currentTipo);
        });
        (function() {
            // Muestra/oculta la cascada y pone el label con el *
            function toggleACUI(tipo) {
                const esDep = String(tipo) === '2';
                $('#div_des').prop('hidden', esDep);
                $('#labelDes').html(`${esDep ? 'Dependencia' : 'AC'} <small class="obligatorio">*</small>`);
            }

            // Define la versión GLOBAL (para el onchange inline)
            window.getDesoDep = async function(valor) {
                const nuevo = String(valor || '');

                // Si tienes la memoria/caché del snippet anterior, respétala
                if (typeof remember === 'function') remember(typeof currentTipo !== 'undefined' ? currentTipo :
                    '');
                if (typeof currentTipo !== 'undefined') currentTipo = nuevo;

                // Toggle visual inmediato
                toggleACUI(nuevo);

                // Restaura selects desde memoria/caché si existe esa función
                if (typeof restore === 'function') {
                    try {
                        await restore(nuevo);
                    } catch (e) {
                        console.warn('restore() falló:', e);
                    }
                } else {
                    // Si no tienes restore(), al menos limpia los combos cuando sea AC
                    if (nuevo === '1') {
                        ['#unidad_academica', '#sede', '#programa_educativo', '#nivel', '#modalidad']
                        .forEach(sel => $(sel).val(''));
                    } else {
                        // Dependencia: solo se usa DES
                        ['#unidad_academica', '#sede', '#programa_educativo', '#nivel', '#modalidad']
                        .forEach(sel => $(sel).val(''));
                    }
                }
            };

            // Por si el handler inline no se dispara en tu navegador/escenario:
            $('#tipo_mejora').off('change._acdep').on('change._acdep', function() {
                window.getDesoDep(this.value);
            });

            // Estado inicial al cargar la página
            const initial = $('#tipo_mejora').val();
            toggleACUI(initial);
        })();



        const listsCache = {
            des: {
                '1': null,
                '2': null
            }, // por tipo
            ua: {}, // id_des -> []
            sedes: {}, // id_ua  -> []
            programas: {}, // id_sede-> []
            niveles: {}, // id_prog-> []
            modalidades: {} // id_nivel-> []
        };

        function setACUI(tipo) {
            const esDep = String(tipo) === '2';
            $('#div_des').prop('hidden', esDep);
            $('#labelDes').html(`${esDep ? 'Dependencia' : 'AC'} <small class="obligatorio">*</small>`);
        }

        function fillSelect($sel, items = [], selectedId = '') {
            const frag = document.createDocumentFragment();
            const empty = new Option('', '', false, false);
            empty.value = '';
            frag.appendChild(empty);
            items.forEach(({
                    id,
                    nombre
                }) =>
                frag.appendChild(new Option(nombre, String(id), false, String(id) === String(selectedId)))
            );
            const el = $sel[0];
            el.innerHTML = '';
            el.appendChild(frag);
            $sel.val(String(selectedId || ''));
        }

        function remember(tipo) {
            if (!tipo) return;
            formMemory[tipo] = {
                des: $('#des').val() || '',
                ua: $('#unidad_academica').val() || '',
                sede: $('#sede').val() || '',
                prog: $('#programa_educativo').val() || '',
                nivel: $('#nivel').val() || '',
                mod: $('#modalidad').val() || '',
            };
        }

        function optionList($sel) {
            const out = [];
            $sel.find('option').each(function() {
                const v = $(this).attr('value');
                if (v !== '' && v != null) out.push({
                    id: v,
                    nombre: $(this).text()
                });
            });
            return out;
        }

        function seedCacheFromDOM() {
            const t = $('#tipo_mejora').val() || '';
            if (!t) return;
            const idDes = $('#des').val() || '';
            const idUa = $('#unidad_academica').val() || '';
            const idSede = $('#sede').val() || '';
            const idProg = $('#programa_educativo').val() || '';
            const idNivel = $('#nivel').val() || '';

            const desList = optionList($('#des'));
            if (desList.length) listsCache.des[t] = desList;
            const uaList = optionList($('#unidad_academica'));
            if (uaList.length && idDes) listsCache.ua[idDes] = uaList;
            const sedList = optionList($('#sede'));
            if (sedList.length && idUa) listsCache.sedes[idUa] = sedList;
            const proList = optionList($('#programa_educativo'));
            if (proList.length && idSede) listsCache.programas[idSede] = proList;
            const nivList = optionList($('#nivel'));
            if (nivList.length && idProg) listsCache.niveles[idProg] = nivList;
            const modList = optionList($('#modalidad'));
            if (modList.length && idNivel) listsCache.modalidades[idNivel] = modList;
        }

        // Fetchers con caché
        async function getDesCached(tipo) {
            if (!listsCache.des[tipo]) {
                const j = await (await fetch(`${base_url}/admin/get/des-o-dependencias/${tipo}`)).json();
                listsCache.des[tipo] = j.data || [];
            }
            return listsCache.des[tipo];
        }
        async function getUaCached(idDes) {
            if (!listsCache.ua[idDes]) {
                const j = await (await fetch(`${base_url}/admin/get/unidades/${idDes}`)).json();
                listsCache.ua[idDes] = j.data || [];
            }
            return listsCache.ua[idDes];
        }
        async function getSedesCached(idUa) {
            if (!listsCache.sedes[idUa]) {
                const j = await (await fetch(`${base_url}/admin/get/sedes/${idUa}`)).json();
                listsCache.sedes[idUa] = j.data || [];
            }
            return listsCache.sedes[idUa];
        }
        async function getProgramasCached(idSede) {
            if (!listsCache.programas[idSede]) {
                const j = await (await fetch(`${base_url}/admin/get/programas/${idSede}`)).json();
                listsCache.programas[idSede] = j.data || [];
            }
            return listsCache.programas[idSede];
        }
        async function getNivelesCached(idProg) {
            if (!listsCache.niveles[idProg]) {
                const j = await (await fetch(`${base_url}/admin/get/niveles/${idProg}`)).json();
                listsCache.niveles[idProg] = j.data || [];
            }
            return listsCache.niveles[idProg];
        }
        async function getModalidadesCached(idNivel) {
            if (!listsCache.modalidades[idNivel]) {
                const j = await (await fetch(`${base_url}/admin/get/modalidades/${idNivel}`)).json();
                listsCache.modalidades[idNivel] = j.data || [];
            }
            return listsCache.modalidades[idNivel];
        }

        async function restore(tipo) {
            const snap = formMemory[tipo] || {};
            prefilling = true;
            setACUI(tipo);

            // DES
            const desList = await getDesCached(tipo);
            fillSelect($('#des'), desList, snap.des);

            // Dependencia: solo DES
            if (String(tipo) === '2') {
                fillSelect($('#unidad_academica'), [], '');
                fillSelect($('#sede'), [], '');
                fillSelect($('#programa_educativo'), [], '');
                fillSelect($('#nivel'), [], '');
                fillSelect($('#modalidad'), [], '');
                prefilling = false;
                return;
            }

            // AC: cascada
            if (snap.des) fillSelect($('#unidad_academica'), await getUaCached(snap.des), snap.ua);
            else fillSelect($('#unidad_academica'), [], '');
            if (snap.ua) fillSelect($('#sede'), await getSedesCached(snap.ua), snap.sede);
            else fillSelect($('#sede'), [], '');
            if (snap.sede) fillSelect($('#programa_educativo'), await getProgramasCached(snap.sede), snap.prog);
            else fillSelect($('#programa_educativo'), [], '');
            if (snap.prog) fillSelect($('#nivel'), await getNivelesCached(snap.prog), snap.nivel);
            else fillSelect($('#nivel'), [], '');
            if (snap.nivel) fillSelect($('#modalidad'), await getModalidadesCached(snap.nivel), snap.mod);
            else fillSelect($('#modalidad'), [], '');

            prefilling = false;
        }

        // ===== EXPONE UNA SOLA VERSIÓN de las funciones llamadas desde el HTML =====
        window.getDesoDep = async function(valor) {
            const nuevo = String(valor || '');
            if (nuevo === currentTipo) return;
            remember(currentTipo);
            currentTipo = nuevo;
            await restore(currentTipo);
        };
        window.getUa = async function(idDes) {
            if (prefilling || currentTipo === '2') return;
            fillSelect($('#unidad_academica'), [], '');
            fillSelect($('#sede'), [], '');
            fillSelect($('#programa_educativo'), [], '');
            fillSelect($('#nivel'), [], '');
            fillSelect($('#modalidad'), [], '');
            if (idDes) fillSelect($('#unidad_academica'), await getUaCached(idDes), '');
            remember(currentTipo);
        };
        window.getSedes = async function(idUa) {
            if (prefilling || currentTipo === '2') return;
            fillSelect($('#sede'), [], '');
            fillSelect($('#programa_educativo'), [], '');
            fillSelect($('#nivel'), [], '');
            fillSelect($('#modalidad'), [], '');
            if (idUa) fillSelect($('#sede'), await getSedesCached(idUa), '');
            remember(currentTipo);
        };
        window.getProgramas = async function(idSede) {
            if (prefilling || currentTipo === '2') return;
            fillSelect($('#programa_educativo'), [], '');
            fillSelect($('#nivel'), [], '');
            fillSelect($('#modalidad'), [], '');
            if (idSede) fillSelect($('#programa_educativo'), await getProgramasCached(idSede), '');
            remember(currentTipo);
        };
        window.getNiveles = async function(idProg) {
            if (prefilling || currentTipo === '2') return;
            fillSelect($('#nivel'), [], '');
            fillSelect($('#modalidad'), [], '');
            if (idProg) fillSelect($('#nivel'), await getNivelesCached(idProg), '');
            remember(currentTipo);
        };
        window.getModalidades = async function(idNivel) {
            if (prefilling || currentTipo === '2') return;
            fillSelect($('#modalidad'), [], '');
            if (idNivel) fillSelect($('#modalidad'), await getModalidadesCached(idNivel), '');
            remember(currentTipo);
        };

        // Bind seguro por si el inline no disparara
        $('#tipo_mejora').off('change.acdep').on('change.acdep', function() {
            window.getDesoDep(this.value);
        });

        // Estado inicial
        $(function() {
            currentTipo = String($('#tipo_mejora').val() || '');
            setACUI(currentTipo);
            seedCacheFromDOM();
            remember(currentTipo);
        });
    </script>
@endsection
