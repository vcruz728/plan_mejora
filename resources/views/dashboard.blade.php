@extends('app')
@section('htmlheader_title')
    Inicio
@endsection

@section('main-content')
    <section class="content-header">
        <h1>Plan de mejora</h1>
    </section>

    <div class="col-xs-12">
        <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title">Listados</h3>

                {{-- Filtro por Dependencia --}}
                <div class="row" style="margin-top:10px;">
                    <div class="col-sm-8">
                        <div class="form-group" style="display:flex; align-items:center; gap:10px;">
                            <label for="filtro_procedencia" class="control-label" style="margin:0;">Procedencia</label>
                            <select id="filtro_procedencia" class="form-control" style="flex:1; min-width:320px;">
                                <option value="">Todas</option>
                                @foreach ($procedencias as $proc)
                                    <option value="{{ $proc->id }}">{{ $proc->descripcion }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
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
@section('localscripts')
    <script>
        var base_url = $("input[name='base_url']").val();
        let table = null; // <-- fuera para usarla en el change

        const getPlanes = async () => {
            mostrarLoader('div_tabla', 'Espere un momento...');

            const response = await fetch(`${base_url}/get/planes-mejora`, {
                method: 'get'
            });

            $("#div_tabla").html(`<table class="table table-bordered table-striped" id="tabla_planes"></table>`)

            const data = await response.json();

            if (data.code == 200) {
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
                    columns: [{
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
                        @endif

                        {
                            title: "Tipo",
                            data: 'tipo'
                        },
                        {
                            title: "Plan",
                            data: 'plan_no'
                        },
                        {
                            title: "Recomendación/Meta",
                            data: 'recomendacion_meta'
                        },
                        {
                            title: 'Estatus',
                            defaultContent: '',
                            fnCreatedCell: (nTd, sData, oData) => {
                                if (oData.cerrado === null && oData.fecha_hoy <= oData
                                    .fecha_vencimiento) {
                                    $(nTd).append(
                                            `<div style="text-align: center;"><p>En proceso</p></div>`
                                        )
                                        .css('color', 'black').css('background', 'yellow');
                                } else if (oData.cerrado === null && oData.fecha_hoy > oData
                                    .fecha_vencimiento) {
                                    $(nTd).append(
                                            `<div style="text-align: center;"><p>Vencida</p></div>`)
                                        .css('color', 'white').css('background', 'red');
                                } else if (oData.cerrado !== null) {
                                    $(nTd).append(
                                            `<div style="text-align: center;"><p>Concluída</p></div>`
                                        )
                                        .css('color', 'white').css('background', 'green');
                                }
                            }
                        },
                        {
                            title: 'Acciones',
                            defaultContent: '',
                            fnCreatedCell: (nTd, sData, oData) => {
                                if (data.rol == 1) {
                                    $(nTd).append(`
                                  <div style="text-align: center;">
                                    <a href="${base_url}/admin/edita/plan-mejora/${oData.id}" class="btn btn-sm btn-warning"><i class="fa fa-edit"></i></a>
                                    <a href="${base_url}/admin/ver/plan-mejora/${oData.id}" class="btn btn-sm btn-success"><i class="fa fa-eye"></i></a>
                                    <button class="btn btn-sm btn-danger" onclick="confirmaElimina(${oData.id}, ${oData.acciones})"><i class="fa fa-trash"></i></button>
                                  </div>
                                `);
                                } else if (data.rol == 4) {
                                    $(nTd).append(`
                                  <div style="text-align: center;">
                                    <a href="${base_url}/admin/ver/plan-mejora/${oData.id}" class="btn btn-sm btn-success"><i class="fa fa-eye"></i></a>
                                  </div>
                                `);
                                } else {
                                    $(nTd).append(`
                                  <div style="text-align: center;">
                                   <a href="${base_url}/edita/plan-mejora/${oData.id}" class="btn btn-sm btn-warning"><i class="fa fa-edit"></i></a>
                                  </div>
                                `);
                                }
                            }
                        },
                    ],
                });

                // Filtro por procedencia (columna 0, la oculta)
                $('#filtro_procedencia').off('change').on('change', function() {
                    const v = $(this).val();
                    if (!v) {
                        table.column(0).search('').draw(); // limpiar filtro
                    } else {
                        table.column(0).search('^' + v + '$', true, false).draw(); // match exacto por id
                    }
                });

                const pre = $('#filtro_procedencia').val();
                if (pre) table.column(0).search('^' + pre + '$', true, false).draw();

            } else {
                swal("¡Error!", data.mensaje, "error");
            }
        };

        const confirmaElimina = (id, accion) => {
            let mensaje = '¿Está seguro?';
            let sub = 'El registro se eliminará de forma permanente.';
            if (accion > 0) {
                sub =
                    'El registro cuenta con acciones registradas, si elimina el registro tambien eliminara las acciones.';
            }
            swal({
                title: mensaje,
                text: sub,
                type: "warning",
                showCancelButton: true,
                canceluttonColor: '#FFFFFF',
                confirmButtonColor: '#b38e5d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
            }, async function(isConfirm) {
                if (isConfirm) eliminaLinea(id)
            });
        }

        const eliminaLinea = async (id) => {
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

        getPlanes();
    </script>
@endsection
