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

@section('main-content')
    <section class="content-header">
        <h1 style="text-align: center; margin: 15px 0;">Plan de mejora</h1>
    </section>

    <div class="col-xs-12">
        <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title">Listados</h3>

                {{-- Filtro por Dependencia --}}
                <div class="form-inline" style="margin-right:10px">
                    <label for="filtro_procedencia" class="control-label" style="margin-right:10px;">
                        Filtrar por procedencia:
                    </label>

                    <select id="filtro_procedencia" class="form-control select2" data-placeholder="Todas las procedencias"
                        style="width:400px">
                        <option value=""></option>
                        @foreach ($procedencias as $p)
                            <option value="{{ $p->id }}">{{ $p->descripcion }}</option>
                        @endforeach
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

        // Si el usuario admin ve "Responsable"
        const HAS_RESP = {{ Auth::user()->rol == 1 ? 'true' : 'false' }};

        // ======================= Init =======================
        $(function() {
            // Select2
            const $proc = $('#filtro_procedencia');
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
                        dt.column(0).search('^' + v + '$', true, false).draw();
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

            // helper: estatus como badge
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
            }, ];

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
                render: o => `
          <div class="dt-actions__wrap">
            <button class="btn btn-primary btn-icon" title="Editar"
              onclick="location.href='${base_url}/admin/edita/plan-mejora/${o.id}'">
              <i class="fa fa-pencil"></i>
            </button>
            <button class="btn btn-success btn-icon" title="Ver plan de mejora"
              onclick="location.href='${base_url}/admin/ver/plan-mejora/${o.id}'">
              <i class="fa fa-eye"></i>
            </button>
            <button class="btn btn-danger btn-icon" title="Eliminar"
              onclick="confirmaElimina(${o.id}, ${o.acciones||0})">
              <i class="fa fa-trash"></i>
            </button>
          </div>`
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
                autoWidth: false,
                searching: true,
                ordering: true,
                info: true,
                paging: true,
                scrollX: true,
                pageLength: 10,
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, 'Todos']
                ],
                language: {
                    url: base_url + '/js/Spanish.json'
                },
                layout: {
                    topStart: 'pageLength',
                    topEnd: 'buttons',
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
                        columns: EXPORT_COLS, // lo que ya tienes
                        modifier: {
                            search: 'applied',
                            page: 'all'
                        }
                    },
                    customize: function(xlsx) {
                        // ===== helpers locales (no dependemos de variables externas) =====
                        function numToCol(n) {
                            let s = '';
                            while (n > 0) {
                                let m = (n - 1) % 26;
                                s = String.fromCharCode(65 + m) + s;
                                n = Math.floor((n - 1) / 26);
                            }
                            return s;
                        }
                        var HAS_RESP_LOCAL =
                            {{ Auth::user()->rol == 1 ? 'true' : 'false' }}; // blade, no variable global
                        var estatusExportPos = HAS_RESP_LOCAL ? 5 :
                            4; // Estatus es la 5a o 4a col del Excel
                        var estatusColLetter = numToCol(estatusExportPos); // E o D

                        var $ = window.jQuery;
                        var sheet = xlsx.xl.worksheets['sheet1.xml'];
                        var styles = xlsx.xl['styles.xml'];
                        var sst = xlsx.xl[
                            'sharedStrings.xml']; // puede ser undefined si no hay shared strings

                        var $sheet = $(sheet);
                        var $styles = $(styles);
                        var $fills = $styles.find('fills');
                        var $cellXfs = $styles.find('cellXfs');
                        var $sst = sst ? $(sst) : null;

                        // Leer texto de celda sin importar formato
                        function cellText($c) {
                            var t = $c.attr('t');
                            if (t === 's' && $sst) { // shared string
                                var idx = parseInt($c.find('v').text(), 10);
                                var $si = $sst.find('si').eq(idx);
                                var txt = '';
                                $si.find('t').each(function() {
                                    txt += $(this).text();
                                });
                                return txt;
                            }
                            if (t === 'inlineStr') { // inline string
                                return $c.find('is t').text();
                            }
                            return $c.find('v').text(); // número u otro
                        }

                        // ===== crea estilos (fills + xfs) =====
                        var fillCount = parseInt($fills.attr('count'), 10);
                        // amarillo
                        $fills.append(
                            '<fill><patternFill patternType="solid"><fgColor rgb="FFE98A"/><bgColor indexed="64"/></patternFill></fill>'
                        );
                        var yellowFillId = fillCount;
                        fillCount++;
                        // rojo
                        $fills.append(
                            '<fill><patternFill patternType="solid"><fgColor rgb="DE4D3A"/><bgColor indexed="64"/></patternFill></fill>'
                        );
                        var redFillId = fillCount;
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
                        $cellXfs.attr('count', xfCount);

                        // ===== aplica estilo a la columna de Estatus (toda la col, excepto encabezado) =====
                        var selector = 'row c[r^="' + estatusColLetter + '"]';
                        $(selector, $sheet).each(function() {
                            var $c = $(this);
                            var rAttr = $c.attr('r'); // p.ej. "E2"
                            var rowNum = parseInt(rAttr.replace(/^[A-Z]+/, ''), 10);
                            if (rowNum === 1) return; // saltar header

                            var text = (cellText($c) || '').trim().toLowerCase();
                            if (text.includes('vencida')) {
                                $c.attr('s', redStyleId);
                            } else if (text.includes('en proceso')) {
                                $c.attr('s', yellowStyleId);
                            }
                        });
                    }
                }],
                columns: cols
            });

            // Si ya había un filtro seleccionado al cargar, aplícalo
            const pre = $('#filtro_procedencia').val();
            if (pre) dt.column(0).search('^' + pre + '$', true, false).draw();
        }

        // Sticky del header cuando hay scrollX (opcional, si ya tienes estilos .stuck)
        (function() {
            const limit = parseInt(getComputedStyle(document.documentElement).getPropertyValue('--topbar-h')) || 50;
            window.addEventListener('scroll', function() {
                const head = document.querySelector('.dataTables_wrapper .dataTables_scrollHead');
                if (!head) return;
                (head.getBoundingClientRect().top <= limit + 1) ? head.classList.add('stuck'): head.classList
                    .remove('stuck');
            }, {
                passive: true
            });
        })();

        // ======================= Eliminar =======================
        function confirmaElimina(id, accion) {
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
    </script>
@endsection
