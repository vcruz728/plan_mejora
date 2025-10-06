@extends('app')
@section('htmlheader_title')
    Listar Usuarios
@endsection
@section('main-content')


<section class="content-header">
    <h1>Lista de usuarios</h1>
</section>
<!-- search container -->

<div class="col-xs-12">
    <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title">Usuarios</h3>
        </div>
        <div class="box-body" style="padding-top: 2rem;">
          <div class="row">
            <div class="col-md-12" id="div_tabla">
              <table class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>Usuario</th>
                    <th>Nombre</th>
                    <th>Email</th>
                  </tr>
                </thead>
              </table>
            </div>
          </div>

        </div>
    </div>
</div>


<div class="modal fade" id="modal_edita_usuario" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
      <div class="modal-content">
          <div class="modal-header">
              <h3 class="modal-title">Cambiar contraseña</h3>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
              </button>
          </div>
          <div class="modal-body" >
            <div class="row">
              <form id="form_edita_usuario">
                <div class="col-md-12">
                  <div class="form-group">
                    <label for="nueva_pass">Nueva contraseña <small style="color: grey">(Mínimo 5 caracteres, máximo 20 caracteres)</small></label>
                    <input type="text" name="nueva_pass" id="nueva_pass" class="form-control">
                  </div>
                </div>
              </form>     
            </div>
          </div>
          <div class="modal-footer" id="container_btns_modal_confirmacion">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
              <button type="button" class="btn btn-primary" id="btn_guarda" onclick="reseteaPassword(); this.disabled=true;">Cambiar contraseña</button>
          </div>
      </div>
  </div>
</div>

@endsection
@section('localscripts')
<script>
    var base_url = $("input[name='base_url']").val();
    var id

    const getUsuario = async () => {
      $("#div_tabla").html("<h4>Cargando..</h4>");
      const response = await fetch(`${ base_url }/admin/get/usuarios/sistema`, {
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
          { title: "Usuario", data: 'usuario' },
          { title: "Nombre", data: 'name' },
          { title: "Email", data: 'email' },
          {
            title: 'Acciones', defaultContent: '', fnCreatedCell: (nTd, sData, oData, iRow, iCol) => {
              $(nTd).append(`
                <div style="text-align: center;">
                  <a class="btn btn-warning btn-sm" href="${ base_url }/edita/usuario/${ oData.id }" title="Editar" style="font-size: 16px;"><i class="fa fa-edit"></i></a>
                  <Button class="btn btn-primary btn-sm" title="Cambiar contraseña" onclick="abreModal(${ oData.id })"><i class="fa fa-retweet" style="font-size: 15px;"></i></Button>
                </div>`
              );
            }
          }
        ],
      });
    }

    const abreModal = (id_usuario) => {
      id = id_usuario
      $("#modal_edita_usuario").modal()
    }


    const reseteaPassword = async () => {
      const body = new FormData(document.getElementById('form_edita_usuario'));
      const response = await fetch(`${ base_url }/admin/usuario/resetea-password/${ id }`, {
        method: 'POST',
        body,
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });

      const data = await response.json();

      if (data.code == 200) {
        $("#modal_edita_usuario").modal('hide')
        $("#nueva_pass").val("")
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

    getUsuario()


    const setUsuario = (valor) => {
      $("#usuario").val(valor)
    }
    
</script>
@endsection