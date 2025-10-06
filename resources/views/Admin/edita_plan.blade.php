@extends('app')
@section('htmlheader_title')
    Edita Plan de mejora
@endsection
@section('main-content')

<link rel="stylesheet" href="{{ asset('bower_components/select2/css/select2.min.css') }}">
<section class="content-header">
    <h1>Edita Plan de mejora</h1>
</section>
<!-- search container -->

<div class="col-xs-12">
    <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title">Formulario <small style="color: #DDDDDD;"><p style="color: red; display: inline;">*</p>campos obligatorios</small></h3>
        </div>
        <div class="box-body" style="padding-top: 2rem;">
          <div class="row">
            <form id="form_edita_plan">
              <div class="col-md-3">
                <div class="form-group">
                  <label for="">Tipo:</label>
                  <br>

                  <input type="radio" id="opcion_uno" name="tipo" value="Recomendación" @if($plan->tipo == 'Recomendación') checked  @endif>
                  <label for="opcion_uno">Recomendación</label>
                  &nbsp;&nbsp;&nbsp;
                  <input type="radio" id="opcion_dos" name="tipo" value="Meta" @if($plan->tipo == 'Meta') checked  @endif>
                  <label for="opcion_dos">Meta</label>
                </div>
              </div>
              
              <div class="col-md-3">
                <div class="form-group">
                  <label for="procedencia">Procedencia</label>
                  <select name="procedencia" id="procedencia" class="form-control" onchange="tipoProcedencia(this.value)">
                    <option value="">Seleccione una opción</option>
                    @foreach( $procedencias as $value )
                        <option value="{{ $value->id }}" @if( $plan->procedencia == $value->id ) selected @endif >{{ $value->descripcion }}</option>
                    @endforeach
                  </select>
                </div>
              </div>

              <div class="col-md-3">
                <div class="form-grup">
                  <label for="plan_no">Plan no.</label>
                  <input type="text" name="plan_no" id="plan_no" class="form-control" value="{{ $plan->plan_no }}" readonly>
                </div>
              </div>

              <div class="col-md-3">
                <div class="form-group">
                  <label for="fecha_creacion">Fecha</label>
                  <input type="text" name="fecha_creacion" id="fecha_creacion" class="form-control" value="{{ $plan->fecha_creacion }}" readonly>
                </div>
              </div>

              <div class="col-md-3">
                <div class="form-group">
                  <label for="cantidad">Cantidad</label>
                  <input type="number" name="cantidad" id="cantidad" class="form-control" value="{{ $plan->cantidad }}">
                </div>
              </div>

              <div class="col-md-3" hidden>
                <div class="form-group">
                  <label for="orden">Orden</label>
                  <input type="number" name="orden" id="orden" class="form-control" value="{{ $plan->id }}" readonly>
                </div>
              </div>

              <div class="col-md-3">
                <label for="fecha_vencimiento">Fecha de vencimiento</label>
                <input type="text" data-provide="datepicker" name="fecha_vencimiento" id="fecha_vencimiento" class="form-control" value="{{ $plan->fecha_vencimiento }}">
              </div>


              <div class="col-md-3">
                <div class="form-group">
                  <label for="verifico">Verifico <small><p class="obligatorio">*</p></small></label>
                  <select name="verifico" id="verifico" class="form-control">
                    <option value="">Seleccione una opción</option>
                    @foreach($verificadores as $value)
                      <option value="{{ $value->id }}" @if($plan->verifico == $value->id) selected @endif >{{ $value->name }}</option>
                    @endforeach
                  </select>
                </div>  
              </div>

              <div class="col-md-3">
                <div class="form-group">
                  <label for="tipo_mejora">Seleccione el  AC/Dependencia</label>
                  <select name="tipo_mejora" id="tipo_mejora" class="form-control" onchange="getDesoDep(this.value)">
                    <option value="">Seleccine una opción</option>
                    <option value="1" @if($plan->tipo_mejora == 1) selected @endif>AC</option>
                    <option value="2" @if($plan->tipo_mejora == 2) selected @endif>Dependencia</option>
                  </select>
                </div>
              </div>


              <div class="col-md-12"></div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="des" id="labelDes">@if($plan->tipo_mejora == 2) Dependencia @else AC @endif <small><p class="obligatorio">*</p></small></label>
                    <select name="des" id="des" class="form-control" onchange="getUa(this.value);">
                      <option value="">Seleccione una opción</option>
                      @foreach( $des as $value )
                          <option value="{{ $value->id }}" @if($plan->id_des == $value->id) selected @endif>{{ $value->nombre }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>

                <div id="div_des" @if($plan->tipo_mejora == 2) hidden @endif>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="unidad_academica">Unidad académica </label>
                      <select name="unidad_academica" id="unidad_academica" class="form-control" onchange="getSedes(this.value);">
                        <option value="">Seleccione una opción</option>
                        @foreach( $unidades as $value )
                            <option value="{{ $value->id }}" @if($plan->id_ua == $value->id) selected @endif>{{ $value->nombre }}</option>
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
                            <option value="{{ $value->id }}" @if($plan->id_sede == $value->id) selected @endif>{{ $value->nombre }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>

                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="programa_educativo">Programa educativo</label>
                      <select name="programa_educativo" id="programa_educativo" class="form-control" onchange="getNiveles(this.value);">
                        <option value="">Seleccione una opción</option>
                        @foreach( $programasEducativos as $value )
                            <option value="{{ $value->id }}" @if($plan->id_programa_educativo == $value->id) selected @endif>{{ $value->nombre }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>

                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="nivel">Nivel</label>
                      <select name="nivel" id="nivel" class="form-control" onchange="getModalidades(this.value);">
                        <option value="">Seleccione una opción</option>
                        @foreach( $nivelesEstudio as $value )
                            <option value="{{ $value->id }}" @if($plan->id_nivel_estudio == $value->id) selected @endif>{{ $value->nombre }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>

                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="modalidad">Modalidad</label>
                      <select name="modalidad" id="modalidad" class="form-control">
                        <option value="">Seleccione una opción</option>
                        @foreach( $modalidad as $value )
                            <option value="{{ $value->id }}" @if($plan->id_modalidad_estudio == $value->id) selected @endif>{{ $value->nombre }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                </div>


              <div class="col-md-12"></div>

              <div class="col-md-12">
                <div class="form-group">
                  <label for="recomendacion_meta">Recomendación/Metdda</label>
                  <textarea name="recomendacion_meta" id="recomendacion_meta" class="form-control" style="min-width: 100%; max-width: 100%;  min-height: 115px;">{{ $plan->recomendacion_meta }}</textarea>
                </div>
              </div>


              <div class="col-md-6" hidden>
                <div class="form-group">
                  <label for="ods_pdi">ODS PDI</label>
                  <textarea name="ods_pdi" id="ods_pdi" class="form-control" style="min-width: 100%; max-width: 100%; min-height: 115px;">{{ $plan->ods_pdi }}</textarea>
                </div>
              </div>

               <div class="col-md-4">
                <div class="form-group">
                  <label for="eje_pdi">Eje PDI <small><p class="obligatorio">*</p></small></label>
                  <select name="eje_pdi" id="eje_pdi" class="form-control" onchange="getOdsPdi(this.value)">
                    <option value="">Seleccione una opción</option>
                    @foreach( $ejes as $value )
                      <option value="{{ $value->id }}" @if($plan->eje_pdi == $value->id) selected @endif>{{ $value->descripcion }}</option>
                    @endforeach
                  </select>
                </div>
              </div>

              <div class="col-md-4">
                <div class="form-group">
                  <label for="ods_pdi_select">ODS PDI <small><p class="obligatorio">*</p></small></label>
                  <select name="ods_pdi_select" id="ods_pdi_select" class="form-control" onchange="getObjetivosPdi(this.value)">
                    @foreach( $ods as $value )
                      <option value="{{ $value->id }}" @if($plan->id_ods_pdi == $value->id) selected @endif>{{ $value->descripcion }}</option>
                    @endforeach
                  </select>
                </div>
              </div>

              <div class="col-md-4">
                <div class="form-group">
                  <label for="objetivo_pdi">Objetivo PDI <small><p class="obligatorio">*</p></small></label>
                  <select name="objetivo_pdi" id="objetivo_pdi" class="form-control" onchange="getEstrategiasPdi(this.value)">
                    <option value="">Seleccione una opción</option>
                    @foreach( $objetivos as $value )
                      <option value="{{ $value->id }}" @if($plan->objetivo_pdi == $value->id) selected @endif>{{ $value->descripcion }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="col-md-12"></div>

              <div class="col-md-4">
                <div class="form-group">
                  <label for="estrategias">Estrategias <small><p class="obligatorio">*</p></small></label>
                  <select name="estrategias" id="estrategias" class="form-control" onchange="getIndicadoresMetas(this.value)">
                    <option value="">Seleccione una opción</option>
                    @foreach( $estategias as $value )
                      <option value="{{ $value->id }}" @if($plan->id_estrategia == $value->id) selected @endif>{{ $value->descripcion }}</option>
                    @endforeach
                  </select>
                </div>
              </div>

              <div class="col-md-4">
                <div class="form-group">
                  <label for="meta_pdi">Meta <small><p class="obligatorio">*</p></small></label>
                  <select name="meta_pdi" id="meta_pdi" class="form-control">
                    <option value="">Seleccione una opción</option>
                    @foreach( $metas as $value )
                      <option value="{{ $value->id }}" @if($plan->id_meta == $value->id) selected @endif>{{ $value->descripcion }}</option>
                    @endforeach
                  </select>
                </div>
              </div>


              <div class="col-md-4">
                <div class="form-group">
                  <label for="indicador_pdi">indicador PDI</label>
                  <input type="text" name="indicador_pdi" id="indicador_pdi" class="form-control" value="{{ $plan->indicador_pdi }}">
                </div>
              </div>
              <div class="col-md-12"></div>
              <div class="col-md-4">
                <div class="form-group">
                  <label for="ambito_siemec">Ámbito SIEMEC <small><p class="obligatorio">*</p></small></label>
                  <select name="ambito_siemec" id="ambito_siemec" class="form-control">
                    <option value="">Seleccione una opción</option>
                    @foreach( $ambitos as $value )
                      <option value="{{ $value->id }}" @if($plan->ambito_siemec == $value->id) selected @endif>{{ $value->descripcion }}</option>
                    @endforeach
                  </select>
                </div>
              </div>

              <div class="col-md-4">
                <div class="form-group">
                  <label for="criterio_siemec">Criterio SIEMEC <small><p class="obligatorio">*</p></small></label>
                  <select name="criterio_siemec" id="criterio_siemec" class="form-control">
                    <option value="">Seleccione una opción</option>
                    @foreach( $criterios as $value )
                      <option value="{{ $value->id }}" @if($plan->criterio_siemec == $value->id) selected @endif>{{ $value->descripcion }}</option>
                    @endforeach
                  </select>
                </div>
              </div>

              <div class="col-md-12" style="display: flex; justify-content: flex-end;">
                <button class="btn btn-success" id="btn_edita" onclick="event.preventDefault(); guardaCambios(); this.disabled=true;">Guardar cambios</button>
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
    var base_url = $("input[name='base_url']").val();
    $('#fecha_vencimiento').datepicker({
        dateFormat: 'yy-mm-dd'
    }).datepicker();

    $('#ods_pdi_select').select2();
    $('#objetivo_pdi').select2();

    const guardaCambios = async() => {
      const body = new FormData(document.getElementById('form_edita_plan'));
      body.append('tipo_plan', $('input[name="tipo"]:checked').val())
      body.append('id_plan', {{ $plan->id }})
      const response = await fetch(`${ base_url }/admin/guarda/actualizacion/plan-mejora`, {
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
      $("#btn_edita").removeAttr("disabled")
    }

    const tipoProcedencia = (valor) => {
      let orden = $("#orden").val()
      $("#plan_no").val("")
      if(valor != ''){
        switch(valor){
          case '1':
            $("#plan_no").val(`AUTO-SAES-${ orden }`)
            break
          case '2':
            $("#plan_no").val(`RETRO-SAES-${ orden }`)
            break
          case '3':
            $("#plan_no").val(`VD-SIEMEC-${ orden }`)
            break
        }
      }
    }



    const getOdsPdi = async (valor) => {
      const response = await fetch(`${ base_url }/admin/get/ods-pdi/${ valor }`, {
          method: 'get',
      });

      const data = await response.json();

      if(data.code == 200){
        $("#estrategias").html(`<option value="">Seleccione una opción</option>`);
        $("#meta_pdi").html(`<option value="">Seleccione una opción</option>`);
        $("#objetivo_pdi").html(`<option value="">Seleccione una opción</option>`);
        $("#indicador_pdi").html(`<option value="">Seleccione una opción</option>`);

        $("#ods_pdi_select").html(`<option value="">Seleccione una opción</option>`);
        data.data.map( x => {
          $("#ods_pdi_select").append(`<option value="${ x.id }">${ x.descripcion }</option>`);
        })

        $('#ods_pdi_select').select2();
      }else{
        swal("¡Error!",data.mensaje,"error");
      }
    }

    const getObjetivosPdi = async (valor) => {
      const response = await fetch(`${ base_url }/admin/get/objetivos-pdi/${ valor }`, {
          method: 'get',
      });

      const data = await response.json();

      if(data.code == 200){
        $("#estrategias").html(`<option value="">Seleccione una opción</option>`);
        $("#meta_pdi").html(`<option value="">Seleccione una opción</option>`);
        $("#objetivo_pdi").html(`<option value="">Seleccione una opción</option>`);
        $("#indicador_pdi").html(`<option value="">Seleccione una opción</option>`);
        data.data.map( x => {
          $("#objetivo_pdi").append(`<option value="${ x.id }">${ x.descripcion }</option>`);
        })

        $('#objetivo_pdi').select2();
      }else{
        swal("¡Error!",data.mensaje,"error");
      }
    }


    const getEstrategiasPdi = async (valor) => {
      const response = await fetch(`${ base_url }/admin/get/estrategias-pdi/${ valor }`, {
          method: 'get',
      });

      const data = await response.json();

      if(data.code == 200){
        $("#meta_pdi").html(`<option value="">Seleccione una opción</option>`);
        $("#estrategias").html(`<option value="">Seleccione una opción</option>`);
        data.data.map( x => {
          $("#estrategias").append(`<option value="${ x.id }">${ x.descripcion }</option>`);
        })

        $('#estrategias').select2();
      }else{
        swal("¡Error!",data.mensaje,"error");
      }
    }

    const getIndicadoresMetas = async (valor) => {
      const response = await fetch(`${ base_url }/admin/get/metas-pdi/${ valor }`, {
          method: 'get',
      });

      const data = await response.json();

      if(data.code == 200){
        $("#meta_pdi").html(`<option value="">Seleccione una opción</option>`);
        data.data.map( x => {
          $("#meta_pdi").append(`<option value="${ x.id }">${ x.descripcion }</option>`);
        })

        $('#meta_pdi').select2();
      }else{
        swal("¡Error!",data.mensaje,"error");
      }
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
        $("#labelDes").html(`AC <small><p class="obligatorio">*</p></small>`)
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
    
</script>
@endsection