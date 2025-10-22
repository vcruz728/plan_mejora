@extends('app')
@section('htmlheader_title')
    Inicio
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('bower_components/select2/dist/css/select2.min.css') }}">

    <style>
        /* Select2 alto */
        .select2-container--default .select2-selection--single,
        .select2-container--bootstrap .select2-selection--single {
            height: 34px;
        }

        .select2-container .select2-selection--single .select2-selection__rendered {
            line-height: 32px;
        }

        .select2-container .select2-selection--single .select2-selection__arrow {
            height: 32px;
        }
    </style>
@endpush

@php
    // Permisos por rol
    $isAdmin = (int) (Auth::user()->rol ?? 0) === 1;
    $canDelete = $isAdmin; // solo admin elimina

    // Mapa id => descripción para etiquetar opciones del select desde JS
    $procMap = [];
    foreach ($procedencias as $p) {
        $procMap[(string) $p->id] = $p->descripcion;
    }
@endphp

@section('main-content')
    <section class="content-header">
        <h1 style="text-align: center; margin: 15px 0;">Plan de mejora</h1>
    </section>

    <div class="col-xs-12">
        <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title">Listados</h3>

                {{-- Filtro por Dependencia (las opciones se pintan dinámicamente desde los datos) --}}
                <div class="form-inline" style="margin-right:10px">
                    <label for="filtro_procedencia" class="control-label" style="margin-right:10px;">
                        Filtrar por procedencia:
                    </label>

                    <select id="filtro_procedencia" class="form-control select2" data-placeholder="Todas las procedencias"
                        style="width:400px">
                        <option value=""></option> {{-- Las opciones reales se agregan en JS --}}
                    </select>

                    <button id="btn_limpiar_filtro" class="btn btn-default">Quitar filtro</button>
                </div>
            </div>

            <div class="box-body">
                <div class="container-fluid">
                    <div class="col-md-12" id="div_tabla">
                        <!-- placeholder; lo pinta JS -->
                        <table class="table table-bordered table-striped compact" id="tabla_planes" style="width:100%">
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('bower_components/select2/js/select2.min.js') }}"></script>
@endpush

@section('localscripts')
    <script>
        var base_url = $("input[name='base_url']").val();
        let dt = null;

        // Flags rol
        const IS_ADMIN = @json($isAdmin);
        const CAN_DELETE = @json($canDelete);
        const HAS_RESP = {{ Auth::user()->rol == 1 ? 'true' : 'false' }};

        // Mapa id => texto desde Blade para etiquetar las opciones
        const PROC_MAP = @json($procMap);

        // ======================= Helpers filtro =======================
        function escapeRegex(s) {
            return String(s).replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        }

        /**
         * Rellena el select #filtro_procedencia únicamente con los IDs de procedencia
         * presentes en 'rows'. Si hay solo una, se auto-selecciona.
         * Devuelve el valor seleccionado (o '' si no hay).
         */
        function refreshProcedenciaOptionsFromRows(rows) {
            const $proc = $('#filtro_procedencia');
            const prev = $proc.val();

            // IDs únicos presentes en la data (ajusta 'procedencia' si tu campo se llama distinto)
            const ids = Array.from(new Set(
                (rows || [])
                .map(r => r?.procedencia)
                .filter(v => v !== null && v !== undefined && v !== '')
            )).map(String);

            // id + texto usando PROC_MAP; fallback si falta
            const pairs = ids
                .map(id => ({
                    id,
                    text: PROC_MAP[id] ?? `Procedencia ${id}`
                }))
                .sort((a, b) => a.text.localeCompare(b.text, 'es', {
                    sensitivity: 'base'
                }));

            // Reconstruir opciones del select
            $proc.find('option').remove();
            $proc.append(new Option('', '', false, false)); // opción vacía = todas

            for (const p of pairs) {
                $proc.append(new Option(p.text, p.id, false, false));
            }

            // Auto-selecciona si hay 1; si no, restaura si sigue; si no, vacío
            const newVal = (ids.length === 1) ?
                ids[0] :
                (ids.includes(prev) ? prev : '');

            $proc.val(newVal).trigger('change.select2'); // refresca UI de select2
            return newVal;
        }

        // ======================= Init =======================
        $(function() {
            const $proc = $('#filtro_procedencia');

            // Select2
            $proc.select2({
                width: 'resolve',
                placeholder: $proc.data('placeholder'),
                allowClear: true
            });

            // Limpiar filtro
            $('#btn_limpiar_filtro').on('click', function() {
                $proc.val(null).trigger('change');
                if (dt) dt.column(0).search('').draw(); // columna 0 = procedencia (oculta)
            });

            // Cargar tabla y enganchar filtro
            getPlanes().then(() => {
                $proc.off('change.pm').on('change.pm', function() {
                    const v = $(this).val();
                    if (!dt) return;
                    if (!v) {
                        dt.column(0).search('').draw();
                    } else {
                        // filtro exacto por ID en col oculta
                        dt.column(0).search('^' + escapeRegex(v) + '$', true, false).draw();
                    }
                });
            });
        });

        // ======================= Tabla =======================
        async function getPlanes() {
            // mini loader
            $("#div_tabla").html(`
                <div class="text-center" style="padding:24px;">
                    <i class="fa fa-spinner fa-spin"></i> Espere un momento...
                </div>
            `);

            const resp = await fetch(`${base_url}/get/planes-mejora`, {
                method: 'get'
            });
            const data = await resp.json();

            $("#div_tabla").html(
                `<table class="table table-bordered table-striped compact" id="tabla_planes" style="width:100%"></table>`
            );

            if (data.code !== 200) {
                swal("¡Error!", data.mensaje || 'No fue posible cargar los datos', "error");
                return;
            }

            // helper: estatus como badge (igual que tenías)
            function renderEstatus(row) {
                const cerrada = row.cerrado !== null && row.cerrado !== undefined;
                if (cerrada) return '<span class="dt-badge success">Concluida</span>';
                const vencida = (row.fecha_hoy || '') > (row.fecha_vencimiento || '');
                if (vencida) return '<span class="dt-badge danger">Vencida</span>';
                return '<span class="dt-badge warn">En proceso</span>';
            }

            // Destruye instancia previa
            if (dt && typeof dt.destroy === 'function') {
                dt.destroy();
                dt = null;
            }

            const excelBtnHTML = `
                <span class="excel-badge">X</span>
                <span class="excel-text">Exportar a Excel</span>
            `;

            // Columnas base (índice 0 = procedencia oculta para filtrar)
            const cols = [{
                title: 'procedencia',
                data: 'procedencia',
                visible: false,
                searchable: true
            }];

            if (HAS_RESP) {
                cols.push({
                    title: "Responsable",
                    data: 'name',
                    className: 'dt-center dt-vmiddle'
                });
            }

            cols.push({
                title: "Tipo",
                data: 'tipo',
                className: 'dt-center dt-vmiddle'
            }, {
                title: "Plan",
                data: 'plan_no',
                className: 'dt-center dt-vmiddle text-nowrap'
            }, {
                title: "Recomendación/Meta",
                data: 'recomendacion_meta',
                className: 'dt-justify dt-vmiddle'
            }, {
                title: 'Estatus',
                data: null,
                render: (data, type, row) => renderEstatus(row),
                orderable: true
            }, {
                title: 'Acciones',
                data: null,
                className: 'dt-actions',
                orderable: false,
                render: o => {
                    let html = `<div class="dt-actions__wrap">`;

                    if (IS_ADMIN) {
                        // Admin: Editar (admin), Ver (admin) y Eliminar
                        html += `
                                <button class="btn btn-primary btn-icon" title="Editar"
                                    onclick="location.href='${base_url}/admin/edita/plan-mejora/${o.id}'">
                                    <i class="fa fa-pencil"></i>
                                </button>
                                <button class="btn btn-success btn-icon" title="Ver plan de mejora"
                                    onclick="location.href='${base_url}/admin/ver/plan-mejora/${o.id}'">
                                    <i class="fa fa-eye"></i>
                                </button>
                            `;
                        if (CAN_DELETE) {
                            html += `
                                    <button class="btn btn-danger btn-icon" title="Eliminar"
                                        onclick="confirmaElimina(${o.id}, ${o.acciones||0})">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                `;
                        }
                    } else {
                        // Rol 2: solo Editar (sin Ver, sin Eliminar)
                        html += `
                                <button class="btn btn-primary btn-icon" title="Editar"
                                    onclick="location.href='${base_url}/edita/plan-mejora/${o.id}'">
                                    <i class="fa fa-pencil"></i>
                                </button>
                            `;
                    }

                    html += `</div>`;
                    return html;
                }
            });

            // Columnas para exportar (excluye procedencia oculta y Acciones)
            const EXPORT_COLS = HAS_RESP ? [1, 2, 3, 4, 5] : [1, 2, 3, 4];

            function numToCol(n) {
                let s = '';
                while (n > 0) {
                    let m = (n - 1) % 26;
                    s = String.fromCharCode(65 + m) + s;
                    n = Math.floor((n - 1) / 26);
                }
                return s;
            }
            const estatusExportPos = HAS_RESP ? 5 : 4;

            dt = new DataTable('#tabla_planes', {
                data: data.data,
                deferRender: true,
                searching: true,
                ordering: true,
                info: true,
                paging: true,
                scrollX: true,
                scrollCollapse: true,
                autoWidth: false,
                responsive: true,
                fixedHeader: {
                    header: true
                },
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
                    title: 'Plan_de_Mejora',
                    exportOptions: {
                        columns: EXPORT_COLS,
                        modifier: {
                            search: 'applied',
                            page: 'all'
                        }
                    },
                    customize: function(xlsx) {
                        function numToCol(n) {
                            let s = '';
                            while (n > 0) {
                                let m = (n - 1) % 26;
                                s = String.fromCharCode(65 + m) + s;
                                n = Math.floor((n - 1) / 26);
                            }
                            return s;
                        }
                        var HAS_RESP_LOCAL = {{ Auth::user()->rol == 1 ? 'true' : 'false' }};
                        var estatusExportPos = HAS_RESP_LOCAL ? 5 : 4;
                        var estatusColLetter = numToCol(estatusExportPos);

                        var $ = window.jQuery;
                        var sheet = xlsx.xl.worksheets['sheet1.xml'];
                        var styles = xlsx.xl['styles.xml'];
                        var sst = xlsx.xl['sharedStrings.xml'];

                        var $sheet = $(sheet);
                        var $styles = $(styles);
                        var $fills = $styles.find('fills');
                        var $cellXfs = $styles.find('cellXfs');
                        var $sst = sst ? $(sst) : null;

                        function cellText($c) {
                            var t = $c.attr('t');
                            if (t === 's' && $sst) {
                                var idx = parseInt($c.find('v').text(), 10);
                                var $si = $sst.find('si').eq(idx);
                                var txt = '';
                                $si.find('t').each(function() {
                                    txt += $(this).text();
                                });
                                return txt;
                            }
                            if (t === 'inlineStr') return $c.find('is t').text();
                            return $c.find('v').text();
                        }

                        // === Fills (fondos) ===
                        var fillCount = parseInt($fills.attr('count'), 10);
                        // En proceso → amarillo
                        $fills.append(
                            '<fill><patternFill patternType="solid"><fgColor rgb="FFE98A"/><bgColor indexed="64"/></patternFill></fill>'
                        );
                        var yellowFillId = fillCount;
                        fillCount++;
                        // Vencida → rojo
                        $fills.append(
                            '<fill><patternFill patternType="solid"><fgColor rgb="DE4D3A"/><bgColor indexed="64"/></patternFill></fill>'
                        );
                        var redFillId = fillCount;
                        fillCount++;
                        // Concluida → verde (#21C05C)
                        $fills.append(
                            '<fill><patternFill patternType="solid"><fgColor rgb="21C05C"/><bgColor indexed="64"/></patternFill></fill>'
                        );
                        var greenFillId = fillCount;
                        fillCount++;
                        $fills.attr('count', fillCount);

                        // === Estilos de celda que aplican esos fills ===
                        var xfCount = parseInt($cellXfs.attr('count'), 10);
                        // Amarillo
                        $cellXfs.append('<xf xfId="0" applyFill="1" fillId="' + yellowFillId +
                            '"/>');
                        var yellowStyleId = xfCount;
                        xfCount++;
                        // Rojo
                        $cellXfs.append('<xf xfId="0" applyFill="1" fillId="' + redFillId + '"/>');
                        var redStyleId = xfCount;
                        xfCount++;
                        // Verde
                        $cellXfs.append('<xf xfId="0" applyFill="1" fillId="' + greenFillId +
                        '"/>');
                        var greenStyleId = xfCount;
                        xfCount++;
                        $cellXfs.attr('count', xfCount);

                        var selector = 'row c[r^="' + estatusColLetter + '"]';
                        $(selector, $sheet).each(function() {
                            var $c = $(this);
                            var rAttr = $c.attr('r');
                            var rowNum = parseInt(rAttr.replace(/^[A-Z]+/, ''), 10);
                            if (rowNum === 1) return;

                            var text = (cellText($c) || '').trim().toLowerCase();
                            // Prioridad: concluidas > vencidas > en proceso
                            if (text.includes('concluida')) {
                                $c.attr('s', greenStyleId);
                            } else if (text.includes('vencida')) {
                                $c.attr('s', redStyleId);
                            } else if (text.includes('en proceso')) {
                                $c.attr('s', yellowStyleId);
                            }
                        });
                    }

                }],
                columns: cols
            });

            // === Rellenar el filtro según lo que trae la data y auto-seleccionar si aplica
            const selected = refreshProcedenciaOptionsFromRows(data.data);

            // Aplicar el filtro inicial si hay selección (regex exacto)
            if (selected) {
                dt.column(0).search('^' + escapeRegex(selected) + '$', true, false).draw();
            }
        }

        // ======================= Eliminar (protegido por rol) =======================
        function confirmaElimina(id, accion) {
            if (!CAN_DELETE) {
                swal("Acción no permitida", "No cuentas con permisos para eliminar.", "warning");
                return;
            }

            let mensaje = '¿Está seguro?';
            let sub = 'El registro se eliminará de forma permanente.';
            if (accion > 0) sub =
                'El registro cuenta con acciones registradas, si elimina el registro también eliminará las acciones.';
            swal({
                title: mensaje,
                text: sub,
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: '#d9534f',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
            }, async function(isConfirm) {
                if (isConfirm) eliminaLinea(id);
            });
        }

        async function eliminaLinea(id) {
            const response = await fetch(`${base_url}/admin/elimina-meta/${id}`, {
                method: 'delete',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            const data = await response.json();
            setTimeout(() => {
                if (data.code == 200) {
                    swal("¡Correcto!", data.mensaje, "success");
                    getPlanes();
                } else {
                    swal("¡Error!", data.mensaje, "error");
                }
            }, 200);
        }

        // Recalcular columnas tras expandir/colapsar el menú lateral (animación ~300ms)
        $(document).on('expanded.pushMenu collapsed.pushMenu', function() {
            setTimeout(function() {
                if (dt) dt.columns.adjust();
            }, 320);
        });
        (function() {
            let tm = null;
            $(window).on('resize.dt', function() {
                clearTimeout(tm);
                tm = setTimeout(function() {
                    if (dt) dt.columns.adjust();
                }, 150);
            });
        })();
    </script>
@endsection
