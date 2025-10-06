@extends('app')
@section('htmlheader_title')
    Catalogos - Procedencias
@endsection
@section('main-content')


<section class="content-header">
    <h1>Catalogos - Procedencias</h1>
</section>
<!-- search container -->

<div class="col-xs-12">
    <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title">Listado</h3>
        </div>
        <div class="box-body" style="padding-top: 2rem;">
          <div class="row">
            <div class="col-md-12" style="display: flex; justify-content: flex-end;">
              <button class="btn btn-success"  onclick="abreModalAgregar();"><i class="fa fa-plus-circle"></i> Agregar</button>
            </div>
            <div class="col-md-12" id="div_tabla">
              <table class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Procedencia</th>
                  </tr>
                </thead>
              </table>
            </div>
          </div>

        </div>
    </div>
</div>


<div class="modal fade" id="modal_edita_procedencia" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
      <div class="modal-content">
          <div class="modal-header">
              <h3 class="modal-title">Edita procedencia</h3>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
              </button>
          </div>
          <div class="modal-body" >
            <div class="row">
              <form id="form_edita_procedencia">
                <div class="col-md-12">
                  <div class="form-group">
                    <label for="procedencia_edit">Procedencia</label>
                    <input type="text" name="procedencia_edit" id="procedencia_edit" class="form-control" placeholder="Máximo 100 caracteres...">
                  </div>
                </div>

                <div class="col-md-12">
                  <div class="form-group">
                    <label for="siglas_edit">Siglas</label>
                    <input type="text" name="siglas_edit" id="siglas_edit" class="form-control" placeholder="Máximo 100 caracteres...">
                  </div>
                </div>
                
            
              </form>     
            </div>
          </div>
          <div class="modal-footer" id="container_btns_modal_confirmacion">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
              <button type="button" class="btn btn-success" id="btn_edita" onclick="editaProcedencia()">Guardar cambios</button>
          </div>
      </div>
  </div>
</div>

<div class="modal fade" id="modal_nueva_procedencia" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
      <div class="modal-content">
          <div class="modal-header">
              <h3 class="modal-title">Nueva procedencia</h3>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
              </button>
          </div>
          <div class="modal-body" >
            <div class="row">
              <form id="form_nueva_procedencia">
                <div class="col-md-12">
                  <div class="form-group">
                    <label for="procedencia">Procedencia</label>
                    <input type="text" name="procedencia" id="procedencia" class="form-control" placeholder="Máximo 100 caracteres...">
                  </div>
                </div>

                <div class="col-md-12">
                  <div class="form-group">
                    <label for="siglas">Siglas</label>
                    <input type="text" name="siglas" id="siglas" class="form-control" placeholder="Máximo 100 caracteres...">
                  </div>
                </div>
                
            
              </form>     
            </div>
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
              <button type="button" class="btn btn-success" id="btn_guarda" onclick="guardaProcedencia(); this.disabled=true;">Guardar</button>
          </div>
      </div>
  </div>
</div>

@endsection
@section('localscripts')
<script>
    var base_url = $("input[name='base_url']").val();
    var id

    const getProcedencias = async () => {
      $("#div_tabla").html("<h4>Cargando..</h4>");
      const response = await fetch(`${ base_url }/admin/get/catalogo/procedencia`, {
          method: 'GET'
      });

      const data = await response.json();
      $("#div_tabla").html(`<table class="table table-bordered table-striped" id="tabla_usuarios">
      </table>`);


      const table = $("#tabla_usuarios").DataTable({
        data: data.data,
        scrollX: true,
        searching: true,
        ordering: true,
        info: false,
        paging: false,
        autoWidth: true,
        language: { url: base_url + '/js/Spanish.json' },
        columns: [
          { title: "Procedencia", data: 'descripcion' },
          { title: "Siglas", data: 'siglas' },
          {
            title: 'Acciones', defaultContent: '', fnCreatedCell: (nTd, sData, oData, iRow, iCol) => {
              $(nTd).append(`
                <div style="text-align: center;">
                  <button class="btn btn-warning btn-sm" onclick="abreModal('${ oData.id }', '${ oData.descripcion }', '${ oData.siglas }')">Editar</button>
                  <button class="btn btn-danger btn-sm" onclick="confirmaElimina('${ oData.id }')">Elimina</button>
                </div>`
              );
            }
          }
        ],
      });
    }

    const abreModal = async (id_pro, texto, sigla) => {
      id = id_pro

      $("#procedencia_edit").val(texto)

      if(sigla === null || sigla == 'null'){
        $("#siglas_edit").val("")
      }else{
        $("#siglas_edit").val(sigla)
      }

      $("#modal_edita_procedencia").modal()
    }

    const editaProcedencia = async() => {
      const body = new FormData(document.getElementById('form_edita_procedencia'));
      const response = await fetch(`${ base_url }/admin/edita/catalogo/procedencia/${ id }`, {
          method: 'post',
          body,
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });

      const data = await response.json();

      if (data.code == 200) {
        $("#modal_edita_procedencia").modal('hide')
        getProcedencias()
        $("#procedencia_edit").val("")
        $("#siglas_edit").val("")
        swal("¡Correcto!", data.mensaje, "success");
      } else if (data.code == 411) {
          var num = 0;
          $.each(data.errors, function(key, value) {
              if (num == 0) {
                  swal("¡Error!", value[0], "error");
              }
              num++;
          });
      } else {
          swal("¡Error!", data.mensaje, "error");
      }
      $("#btn_edita").removeAttr("disabled")
    }

    const abreModalAgregar = () => {
      $("#procedencia").val("")
      $("#siglas").val("")
      $("#modal_nueva_procedencia").modal();
    }


    const guardaProcedencia = async() => {
      const body = new FormData(document.getElementById('form_nueva_procedencia'));
      const response = await fetch(`${ base_url }/admin/catalogo/nueva/procedencia`, {
          method: 'post',
          body,
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });

      const data = await response.json();

      if (data.code == 200) {
        $("#modal_nueva_procedencia").modal('hide')
        getProcedencias()
        swal("¡Correcto!", data.mensaje, "success");
      } else if (data.code == 411) {
          var num = 0;
          $.each(data.errors, function(key, value) {
              if (num == 0) {
                  swal("¡Error!", value[0], "error");
              }
              num++;
          });
      } else {
          swal("¡Error!", data.mensaje, "error");
      }
      $("#btn_guarda").removeAttr("disabled")
    }

    const confirmaElimina = (id) => {
        swal({
                title: "¿Está seguro?",
                text: "El registro se eliminará de forma permanente.",
                type: "warning",
                showCancelButton: true,
                canceluttonColor: '#FFFFFF',
                confirmButtonColor: '#dd4b39',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
            },
            async function(isConfirm) {
                if (isConfirm) {
                    eliminaProcedencia(id)
                }
            });
    }

    const eliminaProcedencia = async (id) => {
        const response = await fetch(`${ base_url }/admin/catalogo/elimina/procedencia/${ id }`, {
            method: 'delete',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        const data = await response.json();

        setTimeout(() => {
            if (data.code == 200) {
                swal("¡Correcto!", data.mensaje, "success");
                getProcedencias();
            } else {
                swal("¡Error!", data.mensaje, "error");
            }
        }, "200");

    }


    getProcedencias()


    const setUsuario = (valor) => {
      $("#usuario").val(valor)
    }
    
</script>
@endsection