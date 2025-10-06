@extends('app')
@section('htmlheader_title')
    Editar usuario
@endsection
@section('main-content')


<section class="content-header">
    <h1>Editar de usuario</h1>
</section>
<!-- search container -->

<div class="col-xs-12">
    <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title">Formulario</h3>
        </div>
        <div class="box-body" style="padding-top: 2rem;">
          <div class="row">
            <form id="form_alta_usuario">
              <div class="col-md-3">
                <div class="form-group">
                  <input type="hidden" name="id_usuario" id="id_usuario" value="{{ $usuario->id }}">
                  <label for="tipo_mejora">Seleccione el tipo de recomendación/meta</label>
                  <select name="tipo_mejora" id="tipo_mejora" class="form-control" onchange="getDesoDep(this.value)">
                    <option value="">Seleccine una opción</option>
                    <option value="1" @if($usuario->tipo_mejora == 1) selected  @endif >DES</option>
                    <option value="2" @if($usuario->tipo_mejora == 2) selected  @endif >Dependencia</option>
                  </select>
                </div>
              </div>
              <div class="col-md-12"></div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="des" id="labelDes">DES/Dependencia <small><p class="obligatorio">*</p></small></label>
                    <select name="des" id="des" class="form-control" onchange="getUa(this.value);">
                      <option value="">Seleccione una opción</option>
                      @foreach( $des as $value )
                          <option value="{{ $value->id }}" @if($usuario->id_des == $value->id) selected @endif>{{ $value->nombre }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>

                <div id="div_des">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="unidad_academica">Unidad académica </label>
                      <select name="unidad_academica" id="unidad_academica" class="form-control" onchange="getSedes(this.value);">
                        <option value="">Seleccione una opción</option>
                        @foreach( $ua as $value )
                          <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>

                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="sede">Sede</label>
                      <select name="sede" id="sede" class="form-control" onchange="getProgramas(this.value);">
                        <option value="">Seleccione una opción</option>
                        @foreach( $sedes as $value )
                          <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>

                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="programa_educativo">Programa educativo</label>
                      <select name="programa_educativo" id="programa_educativo" class="form-control" onchange="getNiveles(this.value);">
                        <option value="">Seleccione una opción</option>
                        @foreach( $programas as $value )
                          <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>

                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="nivel">Nivel</label>
                      <select name="nivel" id="nivel" class="form-control" onchange="getModalidades(this.value);">
                        <option value="">Seleccione una opción</option>
                        @foreach( $nivel_estudios as $value )
                          <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>

                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="modalidad">Modalidad</label>
                      <select name="modalidad" id="modalidad" class="form-control">
                        <option value="">Seleccione una opción</option>
                        @foreach( $modalidades as $value )
                          <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                </div>

              <div class="col-md-12"></div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="name">Nombre del responsable</label>
                  <input type="text" name="name" id="name" class="form-control" value="{{ $usuario->name }}">
                </div>  
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <label for="usuario">Usuario</label>
                  <input type="text" name="usuario" id="usuario" class="form-control" value="{{ $usuario->usuario }}">
                </div>
              </div>



              <div class="col-md-6">
                <div class="form-group">
                  <label for="email">Correo electrónico</label>
                  <input type="text" name="email" id="email" class="form-control" value="{{ $usuario->email }}" disabled>
                </div>
              </div>


              <div class="col-md-12" style="display: flex; justify-content: flex-end;">
                <button class="btn btn-success" id="btn_guarda" onclick="event.preventDefault(); guardaUsuario(); this.disabled=true;">Actualizar usuario</button>
              </div>
            </form>

          </div>

        </div>
    </div>
</div>

@endsection
@section('localscripts')
<script>
    var base_url = $("input[name='base_url']").val();

    const guardaUsuario = async() => {
      const body = new FormData(document.getElementById('form_alta_usuario'));
      const response = await fetch(`${ base_url }/admin/edita/usuario`, {
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

    const getDesoDep = async (valor) => {
      const response = await fetch(`${ base_url }/admin/get/des-o-dependencias/${ valor }`, {
        method: 'get',
      });

      const data = await response.json();

      if(valor == 2){
        $("#div_des").attr('hidden', true)
        $("#labelDes").html(`Dependencia <small><p class="obligatorio">*</p></small>`)
      }else{
        $("#div_des").removeAttr('hidden')
        $("#labelDes").html(`DES <small><p class="obligatorio">*</p></small>`)
      }

      $("#des").html(`<option value="">Seleccione una opción</option>`);
      $("#unidad_academica").html(`<option value="">Seleccione una opción</option>`);
      $("#sede").html(`<option value="">Seleccione una opción</option>`);
      $("#programa_educativo").html(`<option value="">Seleccione una opción</option>`);
      $("#nivel").html(`<option value="">Seleccione una opción</option>`);
      $("#modalidad").html(`<option value="">Seleccione una opción</option>`);

      if(data.code == 200){
        data.data.forEach(({ id,nombre }) => {
          $("#des").append($('<option>').val(id).text(`${nombre}`));
        });
      }else{
        swal("¡Error!",data.mensaje,"error")
      }
    }

    const getUa = async (valor) => {
      const response = await fetch(`${ base_url }/admin/get/unidades/${ valor }`, {
        method: 'get',
      });

      const data = await response.json();

      $("#unidad_academica").html(`<option value="">Seleccione una opción</option>`);
      $("#sede").html(`<option value="">Seleccione una opción</option>`);
      $("#programa_educativo").html(`<option value="">Seleccione una opción</option>`);
      $("#nivel").html(`<option value="">Seleccione una opción</option>`);
      $("#modalidad").html(`<option value="">Seleccione una opción</option>`);

      if(data.code == 200){
        data.data.forEach(({ id,nombre }) => {
          $("#unidad_academica").append($('<option>').val(id).text(`${nombre}`));
        });
      }else{
        swal("¡Error!",data.mensaje,"error")
      }
    }
    
    const getSedes = async (valor) => {
      const response = await fetch(`${ base_url }/admin/get/sedes/${ valor }`, {
        method: 'get',
      });

      const data = await response.json();

      $("#sede").html(`<option value="">Seleccione una opción</option>`);
      $("#programa_educativo").html(`<option value="">Seleccione una opción</option>`);
      $("#nivel").html(`<option value="">Seleccione una opción</option>`);
      $("#modalidad").html(`<option value="">Seleccione una opción</option>`);

      if(data.code == 200){
        data.data.forEach(({ id,nombre }) => {
          $("#sede").append($('<option>').val(id).text(`${nombre}`));
        });
      }else{
        swal("¡Error!",data.mensaje,"error")
      }
    }
    
    const getProgramas = async (valor) => {
      const response = await fetch(`${ base_url }/admin/get/programas/${ valor }`, {
        method: 'get',
      });

      const data = await response.json();

      $("#programa_educativo").html(`<option value="">Seleccione una opción</option>`);
      $("#nivel").html(`<option value="">Seleccione una opción</option>`);
      $("#modalidad").html(`<option value="">Seleccione una opción</option>`);

      if(data.code == 200){
        data.data.forEach(({ id,nombre }) => {
          $("#programa_educativo").append($('<option>').val(id).text(`${nombre}`));
        });
      }else{
        swal("¡Error!",data.mensaje,"error")
      }
    }
    
    const getNiveles = async (valor) => {
      const response = await fetch(`${ base_url }/admin/get/niveles/${ valor }`, {
        method: 'get',
      });

      const data = await response.json();

      $("#nivel").html(`<option value="">Seleccione una opción</option>`);
      $("#modalidad").html(`<option value="">Seleccione una opción</option>`);

      if(data.code == 200){
        data.data.forEach(({ id,nombre }) => {
          $("#nivel").append($('<option>').val(id).text(`${nombre}`));
        });
      }else{
        swal("¡Error!",data.mensaje,"error")
      }
    }
    
    const getModalidades = async (valor) => {
      const response = await fetch(`${ base_url }/admin/get/modalidades/${ valor }`, {
        method: 'get',
      });

      const data = await response.json();

      $("#modalidad").html(`<option value="">Seleccione una opción</option>`);

      if(data.code == 200){
        data.data.forEach(({ id,nombre }) => {
          $("#modalidad").append($('<option>').val(id).text(`${nombre}`));
        });
      }else{
        swal("¡Error!",data.mensaje,"error")
      }
    }

    const setUsuario = (valor) => {
      $("#usuario").val(valor)
    }


    const esconde = async (valor) => {
      if(valor == 2){
        $("#div_des").attr('hidden', true)
        $("#labelDes").html(`Dependencia <small><p class="obligatorio">*</p></small>`)
      }else{
        $("#div_des").removeAttr('hidden')
        $("#labelDes").html(`DES <small><p class="obligatorio">*</p></small>`)
        $("#unidad_academica").val({{ $usuario->id_ua }});
        $("#sede").val({{ $usuario->id_sede }});
        $("#programa_educativo").val({{ $usuario->id_programa }});
        $("#nivel").val({{ $usuario->id_nivel }});
        $("#modalidad").val({{ $usuario->id_modalidad }});
      }

      
    }

    esconde({{ $usuario->tipo_mejora }})

    
</script>
@endsection