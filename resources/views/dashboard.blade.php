@extends('app')
@section('htmlheader_title')
    Inicio
@endsection
@section('main-content')


<section class="content-header">
    <h1>Plan de mejora</h1>
</section>
<!-- search container -->

<div class="col-xs-12">
    <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title">Listados</h3>
        </div>
        <div class="box-body">
            <div class="container-fluid">
                <div class="col-md-12" id="div_tabla">
                    
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@section('localscripts')
<script>
    var base_url = $("input[name='base_url']").val();

    const getPlanes = async () => {
        mostrarLoader('div_tabla', 'Espere un momento...');

        const response = await fetch(`${ base_url }/get/planes-mejora`, {
            method: 'get'
        })

        $("#div_tabla").html(`<table class="table table-bordered table-striped" id="tabla_planes"></table>`)

        const data = await response.json();

        if(data.code == 200){
          const table = $("#tabla_planes").DataTable({
            data: data.data,
            scrollX: true,
            searching: true,
            ordering: true,
            info: false,
            paging: true,
            autoWidth: true,
            language: { url: base_url + '/js/Spanish.json' },
            columns: [
              @if( Auth::user()->rol == 1 )
                { title: "Responsable", data: 'name' },
              @endif
                { title: "Tipo", data: 'tipo' },
                { title: "Plan", data: 'plan_no' },
                { title: "Recomendación/Meta", data: 'recomendacion_meta' },
                {
                  title: 'Estatus', defaultContent: '', fnCreatedCell: (nTd, sData, oData, iRow, iCol) => {
                      if(oData.cerrado === null && oData.fecha_hoy <= oData.fecha_vencimiento){
                        $(nTd).append(`
                          <div style="text-align: center;">
                            <p>En proceso</p>
                          </div>
                        `);
                        $(nTd).css('color', 'black')
                        $(nTd).css('background', 'yellow')
                      }else if(oData.cerrado === null && oData.fecha_hoy > oData.fecha_vencimiento){
                        $(nTd).append(`
                          <div style="text-align: center;">
                            <p>Vencida</p>
                          </div>
                        `);
                        $(nTd).css('color', 'white')
                        $(nTd).css('background', 'red')
                      }else if(oData.cerrado !== null){
                        $(nTd).append(`
                          <div style="text-align: center;">
                            <p>Concluída</p>
                          </div>
                        `);
                        $(nTd).css('color', 'white')
                        $(nTd).css('background', 'green')
                      }
                  }
                },
                {
                  title: 'Acciones', defaultContent: '', fnCreatedCell: (nTd, sData, oData, iRow, iCol) => {
                      if(data.rol == 1){
                        $(nTd).append(`
                          <div style="text-align: center;">
                            <a href="${ base_url }/admin/edita/plan-mejora/${ oData.id }" class="btn btn-sm btn-warning"><i class="fa fa-edit"></i></a>
                            <a href="${ base_url }/admin/ver/plan-mejora/${ oData.id }" class="btn btn-sm btn-success"><i class="fa fa-eye"></i></a>
                            <button class="btn btn-sm btn-danger" onclick="confirmaElimina(${ oData.id }, ${ oData.acciones })" ><i class="fa fa-trash"></i></button>
                          </div>
                        `);
                      }else if(data.rol == 4){
                        $(nTd).append(`
                          <div style="text-align: center;">
                            <a href="${ base_url }/admin/ver/plan-mejora/${ oData.id }" class="btn btn-sm btn-success"><i class="fa fa-eye"></i></a>
                          </div>
                        `);
                      }else{
                        $(nTd).append(`
                          <div style="text-align: center;">
                           <a href="${ base_url }/edita/plan-mejora/${ oData.id }" class="btn btn-sm btn-warning"><i class="fa fa-edit"></i></a>
                          </div>
                        `);
                      }
                  }
                },
            ],
            
          });
          
        }else{
          swal("¡Error!",data.mensaje,"error");
        }
    }

    const confirmaElimina = (id, accion) => {
      let mensaje = '¿Está seguro?'
      let sub = 'El registro se eliminará de forma permanente.'

      if(accion > 0){
        mensaje = '¿Está seguro?'
        sub = 'El registro cuenta con acciones registradas, si elimina el registro tambien eliminara las acciones.'
      }
      swal({
              title:mensaje,
              text: sub,
              type: "warning",
              showCancelButton: true,
              canceluttonColor: '#FFFFFF',
              confirmButtonColor: '#b38e5d',
              confirmButtonText: 'Sí, eliminar',
              cancelButtonText: 'Cancelar',
          },
          async function  (isConfirm) {
              if (isConfirm) {
                eliminaLinea(id)
              }
          });
    }

    const eliminaLinea = async (id) => {
      const response = await fetch(`${ base_url }/admin/elimina-meta/${ id }`, {
        method: 'delete',
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });

      const data = await response.json();
      setTimeout(() => {
        if(data.code == 200){
          swal("¡Correcto!",data.mensaje,"success");
          getLineas();
        }else{
          swal("¡Error!",data.mensaje,"error");
        }
          }, "200");
    }

    getPlanes()
</script>
@endsection