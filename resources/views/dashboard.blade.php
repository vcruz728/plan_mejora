@extends('app')
@section('htmlheader_title')
    Inicio
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('bower_components/select2/dist/css/select2.min.css') }}">
    {{-- Opcional: tema bootstrap para combinar con AdminLTE/Bootstrap --}}
    <link rel="stylesheet" href="{{ asset('bower_components/select2-bootstrap-theme/dist/select2-bootstrap.min.css') }}">

    <style>
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

        /* no hacer wrap y mostrar separación real entre botones */
        .btn-actions {
            display: inline-flex;
            gap: 8px;
            /* <-- el “espacio” entre botones */
            align-items: center;
            flex-wrap: nowrap;
            /* evita 2ª fila */
        }

        td.dt-actions {
            white-space: nowrap;
        }

        /* estilo de los icon-badges (puedes reciclar tu .btn-icon existente) */
        .btn-icon {
            width: 34px;
            height: 34px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, .12);
            transition: transform .1s ease-in-out, box-shadow .1s;
        }

        .btn-icon:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, .18);
        }

        .btn-icon i {
            font-size: 16px;
        }
    </style>
@endpush

@section('main-content')
    <section class="content-header">
        <h1>Plan de mejora</h1>
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
                    <div class="col-md-12" id="div_tabla"></div>
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
        let table = null;

        // ======================= Init =======================
        $(function() {
            // Inicializa Select2 (usa el tema bootstrap si cargaste su CSS)
            const $proc = $('#filtro_procedencia');
            $proc.select2({

                width: 'resolve',
                placeholder: $proc.data('placeholder'),
                allowClear: true
            });

            // Botón "Quitar filtro"
            $('#btn_limpiar_filtro').on('click', function() {
                $proc.val(null).trigger('change'); // vuelve a mostrar el placeholder
                if (table) table.column(0).search('').draw(); // limpia filtro en la tabla
            });

            // Carga la tabla
            getPlanes().then(() => {
                // Con la tabla ya creada, engancha el filtro
                $proc.off('change.pm').on('change.pm', function() {
                    const v = $(this).val();
                    if (!table) return;
                    if (!v) {
                        table.column(0).search('').draw();
                    } else {
                        // Coincidencia exacta por ID de procedencia (columna 0 está oculta)
                        table.column(0).search('^' + v + '$', true, false).draw();
                    }
                });
            });
        });

        // ======================= Tabla =======================
        async function getPlanes() {
            mostrarLoader('div_tabla', 'Espere un momento...');

            const response = await fetch(`${base_url}/get/planes-mejora`, {
                method: 'get'
            });
            $("#div_tabla").html(`<table class="table table-bordered table-striped" id="tabla_planes"></table>`);
            const data = await response.json();

            if (data.code !== 200) {
                swal("¡Error!", data.mensaje, "error");
                return;
            }

            table = $("#tabla_planes").DataTable({
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
                columns: [{ // Procedencia (ID) para filtrar — oculta pero searchable
                        title: 'procedencia',
                        data: 'procedencia',
                        visible: false,
                        searchable: true
                    },
                    @if (Auth::user()->rol == 1)
                        {
                            title: "Responsable",
                            data: 'name'
                        },
                    @endif {
                        title: "Tipo",
                        data: 'tipo'
                    },
                    {
                        title: "Plan",
                        data: 'plan_no',
                        class: "text-nowrap"
                    },
                    {
                        title: "Recomendación/Meta",
                        data: 'recomendacion_meta'
                    },
                    {
                        title: 'Estatus',
                        defaultContent: '',
                        fnCreatedCell: (nTd, sData, oData) => {
                            if (oData.cerrado === null && oData.fecha_hoy <= oData.fecha_vencimiento) {
                                $(nTd).html('<div style="text-align:center;"><p>En proceso</p></div>')
                                    .css('color', 'black').css('background', 'yellow');
                            } else if (oData.cerrado === null && oData.fecha_hoy > oData
                                .fecha_vencimiento) {
                                $(nTd).html('<div style="text-align:center;"><p>Vencida</p></div>')
                                    .css('color', 'white').css('background', 'red');
                            } else if (oData.cerrado !== null) {
                                $(nTd).html('<div style="text-align:center;"><p>Concluída</p></div>')
                                    .css('color', 'white').css('background', 'green');
                            }
                        }
                    },
                    {
                        title: 'Acciones',
                        defaultContent: '',
                        data: null,
                        orderable: false,
                        className: 'text-center dt-actions',
                        fnCreatedCell: (nTd, sData, oData) => {

                            if (data.rol == 1) {
                                $(nTd).html(`
                                 <div class="btn-actions">
                                    <button class="btn btn-primary btn-icon" title="Editar" onclick="location.href='${base_url}/admin/edita/plan-mejora/${oData.id}'"><i class="fa fa-pencil"></i></button>
                                    <button class="btn btn-success btn-icon" title="Ver plan de mejora" onclick="location.href='${base_url}/admin/ver/plan-mejora/${oData.id}'"><i class="fa fa-eye"></i></button>
                                    <button class="btn btn-danger btn-icon" title="Eliminar" onclick="confirmaElimina(${oData.id}, ${oData.acciones})"><i class="fa fa-trash"></i></button>
                                 </div>
                            `);
                            } else if (data.rol == 4) {
                                $(nTd).html(`
                                 <div class="btn-actions">
                                    <button class="btn btn-success btn-icon" title="Ver plan de mejora" onclick="location.href='${base_url}/admin/ver/plan-mejora/${oData.id}'"><i class="fa fa-eye"></i></button>
                                 </div>
                            `);
                            } else {
                                $(nTd).html(`
                                 <div class="btn-actions">
                                    <button class="btn btn-primary btn-icon" title="Editar" onclick="location.href='${base_url}/edita/plan-mejora/${oData.id}'"><i class="fa fa-pencil"></i></button>
                                </div>
                            `);
                            }
                        }

                    },
                ],
            });

            // Si ya había algo seleccionado en el filtro al cargar, aplícalo
            const pre = $('#filtro_procedencia').val();
            if (pre) table.column(0).search('^' + pre + '$', true, false).draw();
        }

        // ======================= Eliminar =======================
        function confirmaElimina(id, accion) {
            let mensaje = '¿Está seguro?';
            let sub = 'El registro se eliminará de forma permanente.';
            if (accion > 0) {
                sub = 'El registro cuenta con acciones registradas, si elimina el registro también eliminará las acciones.';
            }
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
