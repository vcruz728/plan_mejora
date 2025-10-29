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

        // ===== Estado global =====
        let dt = null;
        let REDRAW_TIMER = null;
        let DATA_REFRESH_TIMER = null;
        let _redrawPending = false;

        // Flags rol
        const IS_ADMIN = @json($isAdmin);
        const CAN_DELETE = @json($canDelete);
        const HAS_RESP_ROLE = {{ (int) Auth::user()->rol === 1 ? 'true' : 'false' }};

        // Mapa id => texto desde Blade para etiquetar select
        const PROC_MAP = @json($procMap);

        // ===== Helpers =====
        function redrawNow() {
            if (!dt || _redrawPending) return;
            _redrawPending = true;
            requestAnimationFrame(() => {
                try {
                    dt.rows().invalidate().draw(false);
                } finally {
                    _redrawPending = false;
                }
            });
        }

        function escapeRegex(s) {
            return String(s).replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        }

        function refreshProcedenciaOptionsFromRows(rows) {
            const $proc = $('#filtro_procedencia');
            const prev = $proc.val();
            const ids = Array.from(new Set((rows || [])
                .map(r => r?.procedencia)
                .filter(v => v !== null && v !== undefined && v !== '')
            )).map(String);

            const pairs = ids.map(id => ({
                    id,
                    text: PROC_MAP[id] ?? `Procedencia ${id}`
                }))
                .sort((a, b) => a.text.localeCompare(b.text, 'es', {
                    sensitivity: 'base'
                }));

            $proc.find('option').remove();
            $proc.append(new Option('', '', false, false));
            for (const p of pairs) $proc.append(new Option(p.text, p.id, false, false));

            const newVal = (ids.length === 1) ? ids[0] : (ids.includes(prev) ? prev : '');
            $proc.val(newVal).trigger('change.select2');
            return newVal;
        }

        function parseFecha(s) {
            if (!s) return null;
            let m = /^(\d{4})-(\d{2})-(\d{2})/.exec(s);
            if (m) return new Date(+m[1], +m[2] - 1, +m[3]);
            m = /^(\d{2})\/(\d{2})\/(\d{4})/.exec(s);
            if (m) return new Date(+m[3], +m[2] - 1, +m[1]);
            const d = new Date(s);
            return isNaN(d) ? null : d;
        }

        function renderEstatus(row) {
            const cerrada = row.cerrado !== null && row.cerrado !== undefined;
            if (cerrada) return '<span class="dt-badge success">Concluida</span>';
            const fin = parseFecha(row.fecha_vencimiento);
            const hoy = new Date();
            hoy.setHours(0, 0, 0, 0);
            if (fin) fin.setHours(0, 0, 0, 0);
            const vencida = !!fin && fin.getTime() < hoy.getTime();
            if (vencida) return '<span class="dt-badge danger">Vencida</span>';
            return '<span class="dt-badge warn">En proceso</span>';
        }

        function numToCol(n) {
            let s = '';
            while (n > 0) {
                let m = (n - 1) % 26;
                s = String.fromCharCode(65 + m) + s;
                n = Math.floor((n - 1) / 26);
            }
            return s;
        }

        // ===== Init =====
        $(function() {
            const $proc = $('#filtro_procedencia');
            $proc.select2({
                width: 'resolve',
                placeholder: $proc.data('placeholder'),
                allowClear: true
            });

            $('#btn_limpiar_filtro').on('click', function() {
                $proc.val(null).trigger('change');
                if (dt) dt.column(0).search('').draw();
            });

            getPlanes();
        });

        // ===== Core =====
        async function getPlanes() {
            $("#div_tabla").html(`
            <div class="text-center" style="padding:24px;">
                <i class="fa fa-spinner fa-spin"></i> Espere un momento...
            </div>`);

            const resp = await fetch(`${base_url}/get/planes-mejora`);
            const data = await resp.json();

            $("#div_tabla").html(
                `<table class="table table-bordered table-striped compact" id="tabla_planes" style="width:100%"></table>`
            );

            if (data.code !== 200) {
                swal("¡Error!", data.mensaje || 'No fue posible cargar los datos', "error");
                return;
            }

            // Detectar si VIENE una llave de responsable en el dataset
            const firstRow = Array.isArray(data.data) && data.data.length ? data.data[0] : null;
            const HAS_RESP_COL = HAS_RESP_ROLE && firstRow && (
                ('name' in firstRow) || ('responsable' in firstRow) ||
                ('responsable_nombre' in firstRow) || ('usuario' in firstRow)
            );

            // Timers (crear SOLO una vez, usando globales)
            if (!REDRAW_TIMER) {
                REDRAW_TIMER = setInterval(() => redrawNow(), 60 * 1000);
            }
            if (!DATA_REFRESH_TIMER) {
                DATA_REFRESH_TIMER = setInterval(async () => {
                    try {
                        const r = await fetch(`${base_url}/get/planes-mejora`);
                        const json = await r.json();
                        if (json.code === 200 && dt) {
                            const prevFilter = $('#filtro_procedencia').val();
                            dt.clear().rows.add(json.data).draw(false);

                            const selected = refreshProcedenciaOptionsFromRows(json.data);
                            const useVal = prevFilter || selected || '';
                            dt.column(0).search(useVal ? '^' + escapeRegex(String(useVal)) + '$' : '', true,
                                false).draw(false);
                        }
                    } catch (e) {
                        /* opcional: console.warn(e) */
                    }
                }, 5 * 60 * 1000);
            }

            // Destruir DT previo si existía
            if (dt && typeof dt.destroy === 'function') {
                dt.destroy();
                dt = null;
            }

            const excelBtnHTML = `<span class="excel-badge">X</span><span class="excel-text">Exportar a Excel</span>`;

            // ===== Columnas =====
            const cols = [
                // Col 0 oculta para filtrar por procedencia
                {
                    title: 'Procedencia',
                    data: null,
                    visible: true,
                    searchable: true,
                    render: (d, t, row) => row?.procedencia ?? ''
                }
            ];

            if (HAS_RESP_COL) {
                cols.push({
                    title: "Responsable",
                    data: null,
                    render: (d, t, row) => row?.responsable ?? '',
                    className: 'dt-center dt-vmiddle'
                });
            }

            cols.push({
                title: "Tipo",
                data: null,
                className: 'dt-center dt-vmiddle',
                render: (d, t, row) => row?.tipo ?? ''
            }, {
                title: "Plan",
                data: null,
                className: 'dt-center dt-vmiddle text-nowrap',
                render: (d, t, row) => row?.plan_no ?? ''
            }, {
                title: "Recomendación/Meta",
                data: null,
                className: 'dt-justify dt-vmiddle',
                render: (d, t, row) => row?.recomendacion_meta ?? ''
            }, {
                title: "Estatus",
                data: null,
                orderable: true,
                render: (d, t, row) => renderEstatus(row)
            }, {
                title: "Acciones",
                data: null,
                orderable: false,
                className: 'dt-actions',
                render: (d, t, o) => {
                    let html = `<div class="dt-actions__wrap">`;
                    if (IS_ADMIN) {
                        html += `
                        <button class="btn btn-primary btn-icon" title="Editar"
                            onclick="location.href='${base_url}/admin/edita/plan-mejora/${o.id}'">
                            <i class="fa fa-pencil"></i>
                        </button>
                        <button class="btn btn-success btn-icon" title="Ver plan de mejora"
                            onclick="location.href='${base_url}/admin/ver/plan-mejora/${o.id}'">
                            <i class="fa fa-eye"></i>
                        </button>`;
                        if (CAN_DELETE) {
                            html += `
                            <button class="btn btn-danger btn-icon" title="Eliminar"
                                onclick="confirmaElimina(${o.id}, ${o.acciones||0})">
                                <i class="fa fa-trash"></i>
                            </button>`;
                        }
                    } else {
                        html += `
                        <button class="btn btn-primary btn-icon" title="Editar"
                            onclick="location.href='${base_url}/edita/plan-mejora/${o.id}'">
                            <i class="fa fa-pencil"></i>
                        </button>`;
                    }
                    html += `</div>`;
                    return html;
                }
            });

            // Columnas para exportar (omite procedencia oculta y Acciones)
            //  const EXPORT_COLS = HAS_RESP_COL ? [1, 2, 3, 4, 5] : [1, 2, 3, 4];
            const EXPORT_COLS = HAS_RESP_COL ? [0, 1, 2, 3, 4, 5] : [0, 1, 2, 3, 4];
            //  const estatusExportPos = HAS_RESP_COL ? 5 : 4; // 1-based para Excel

            // Posición (1-based) de la columna "Estatus" DENTRO DEL ARCHIVO EXPORTADO:
            const estatusExportPos =
                EXPORT_COLS.map(i => String(cols[i].title).toLowerCase())
                .indexOf('estatus') + 1; // +1 porque Excel usa 1-based
            // ===== DataTable =====
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
                        const estatusColLetter = numToCol(estatusExportPos);
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

                        var fillCount = parseInt($fills.attr('count'), 10);
                        $fills.append(
                            '<fill><patternFill patternType="solid"><fgColor rgb="FFE98A"/><bgColor indexed="64"/></patternFill></fill>'
                        );
                        var yellowFillId = fillCount;
                        fillCount++;
                        $fills.append(
                            '<fill><patternFill patternType="solid"><fgColor rgb="DE4D3A"/><bgColor indexed="64"/></patternFill></fill>'
                        );
                        var redFillId = fillCount;
                        fillCount++;
                        $fills.append(
                            '<fill><patternFill patternType="solid"><fgColor rgb="21C05C"/><bgColor indexed="64"/></patternFill></fill>'
                        );
                        var greenFillId = fillCount;
                        fillCount++;
                        $fills.attr('count', fillCount);

                        var xfCount = parseInt($cellXfs.attr('count'), 10);
                        $cellXfs.append('<xf xfId="0" applyFill="1" fillId="' + yellowFillId +
                            '"/>');
                        var yellowStyleId = xfCount;
                        xfCount++;
                        $cellXfs.append('<xf xfId="0" applyFill="1" fillId="' + redFillId + '"/>');
                        var redStyleId = xfCount;
                        xfCount++;
                        $cellXfs.append('<xf xfId="0" applyFill="1" fillId="' + greenFillId +
                            '"/>');
                        var greenStyleId = xfCount;
                        xfCount++;
                        $cellXfs.attr('count', xfCount);

                        var selector = 'row c[r^="' + estatusColLetter + '"]';
                        $(selector, $sheet).each(function() {
                            var $c = $(this);
                            var rowNum = parseInt($c.attr('r').replace(/^[A-Z]+/, ''), 10);
                            if (rowNum === 1) return;
                            var text = (cellText($c) || '').trim().toLowerCase();
                            if (text.includes('concluida')) $c.attr('s', greenStyleId);
                            else if (text.includes('vencida')) $c.attr('s', redStyleId);
                            else if (text.includes('en proceso')) $c.attr('s',
                                yellowStyleId);
                        });
                    }
                }],
                columns: cols
            });

            // Filtro por procedencia
            const $proc = $('#filtro_procedencia');
            const selected = refreshProcedenciaOptionsFromRows(data.data);
            if (selected) dt.column(0).search('^' + escapeRegex(selected) + '$', true, false).draw();

            $proc.off('change.pm').on('change.pm', function() {
                const v = $(this).val();
                if (!dt) return;
                dt.column(0).search(v ? '^' + escapeRegex(v) + '$' : '', true, false).draw();
            });
        }

        // ===== Acciones =====
        function confirmaElimina(id, accion) {
            if (!CAN_DELETE) {
                swal("Acción no permitida", "No cuentas con permisos para eliminar.", "warning");
                return;
            }
            let sub = (accion > 0) ?
                'El registro cuenta con acciones registradas, si elimina el registro también eliminará las acciones.' :
                'El registro se eliminará de forma permanente.';
            swal({
                title: '¿Está seguro?',
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

        // Ajuste columnas tras expandir/cerrar menú
        $(document).on('expanded.pushMenu collapsed.pushMenu', function() {
            setTimeout(function() {
                if (dt) dt.columns.adjust();
            }, 320);
        });
        // Resize debounced
        (function() {
            let tm = null;
            $(window).on('resize.dt', function() {
                clearTimeout(tm);
                tm = setTimeout(function() {
                    if (dt) dt.columns.adjust();
                }, 150);
            });
        })();
        // Redibuja al volver a la pestaña
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) redrawNow();
        });
    </script>
@endsection
