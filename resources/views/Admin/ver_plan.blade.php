@extends('app')
@section('htmlheader_title')
    Acciones Plan de mejora
@endsection
@section('main-content')

<link rel="stylesheet" href="{{ asset('bower_components/select2/css/select2.min.css') }}">
<section class="content-header">
    <h1>Acciones Plan de mejora</h1>
</section>
<!-- search container -->

<div class="col-xs-12">
    <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title">Formulario</h3>
        </div>
        <div class="box-body" style="padding-top: 2rem;">
          <div class="row">
              <div class="col-md-2">
                <div class="form-group">
                  <label for="">Tipo:</label>
                  <br>

                  <input type="radio" id="opcion_uno" disabled name="tipo" value="Recomendación" @if($plan->tipo == 'Recomendación') checked  @endif>
                  <label for="opcion_uno">Recomendación</label>
                  &nbsp;&nbsp;&nbsp;
                  <input type="radio" id="opcion_dos" disabled name="tipo" value="Meta" @if($plan->tipo == 'Meta') checked  @endif>
                  <label for="opcion_dos">Meta</label>
                </div>
              </div>
              
              <div class="col-md-3">
                <div class="form-group">
                  <label for="procedencia">Procedencia</label>
                  <select name="procedencia" id="procedencia" class="form-control" disabled>
                    @foreach( $procedencias as $value )
                        <option value="{{ $value->id }}" @if( $plan->procedencia == $value->id ) selected @endif >{{ $value->descripcion }}</option>
                    @endforeach
                  </select>
                </div>
              </div>

              <div class="col-md-4">
                <div class="form-grup">
                  <label for="plan_no">Plan no.</label>
                  <input type="text" name="plan_no" id="plan_no" class="form-control" value="{{ $plan->plan_no }}" disabled>
                </div>
              </div>

              <div class="col-md-3">
                <div class="form-group">
                  <label for="fecha_creacion">Fecha</label>
                  <input type="text" name="fecha_creacion" id="fecha_creacion" class="form-control" value="{{ $plan->fecha_creacion }}" disabled>
                </div>
              </div>

              <div class="col-md-3">
                <div class="form-group">
                  <label for="cantidad">Cantidad</label>
                  <input type="number" name="cantidad" id="cantidad" class="form-control" value="{{ $plan->cantidad }}" disabled>
                </div>
              </div>

              <div class="col-md-3">
                <label for="fecha_vencimiento">Fecha de vencimiento</label>
                <input type="text" data-provide="datepicker" name="fecha_vencimiento" id="fecha_vencimiento" class="form-control" value="{{ $plan->fecha_vencimiento }}" disabled>
              </div>

              <div class="col-md-3">
                <div class="form-group">
                  <label for="verifico">Verifico</label>
                  <select name="verifico" id="verifico" class="form-control" disabled>
                    <option value="">Seleccione una opción</option>
                    @foreach($verificadores as $value)
                      <option value="{{ $value->id }}" @if($plan->verifico == $value->id) selected @endif >{{ $value->name }}</option>
                    @endforeach
                  </select>
                </div>  
              </div>

              <div class="col-md-3">
                <div class="form-group">
                  <label for="tipo_mejora">AC/Dependencia</label>
                  <select name="tipo_mejora" id="tipo_mejora" class="form-control" disabled>
                    <option value="">Seleccine una opción</option>
                    <option value="1" @if($plan->tipo_mejora == 1) selected @endif>AC</option>
                    <option value="2" @if($plan->tipo_mejora == 2) selected @endif>Dependencia</option>
                  </select>
                </div>
              </div>
  
              <div class="col-md-12"></div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="des">@if($plan->tipo_mejora == 2) Dependencia @else AC @endif </label>
                    <input type="text" name="des" id="des" class="form-control" disabled value="{{ $plan->des }}">
                  </div>
                </div>

                <div id="div_des" @if($plan->tipo_mejora == 2) hidden @endif>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="unidad_academica">Unidad académica </label>
                      <input type="text" name="unidad_academica" id="unidad_academica" class="form-control" disabled value="{{ $plan->unidad_academica }}">
                    </div>
                  </div>

                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="sede">Sede</label>
                      <input type="text" name="sede" id="sede" class="form-control" disabled value="{{ $plan->sede }}">
                    </div>
                  </div>

                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="programa_educativo">Programa educativo</label>
                      <input type="text" name="programa_educativo" id="programa_educativo" class="form-control" disabled value="{{ $plan->programa_educativo }}">
                    </div>
                  </div>

                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="nivel">Nivel</label>
                      <input type="text" name="nivel" id="nivel" class="form-control" disabled value="{{ $plan->nivel }}">
                    </div>
                  </div>

                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="modalidad">Modalidad</label>
                      <input type="text" name="modalidad" id="modalidad" class="form-control" disabled value="{{ $plan->modalidad }}">
                    </div>
                  </div>
                </div>

              <div class="col-md-12"></div>

              <div class="col-md-12">
                <div class="form-group">
                  <label for="recomendacion_meta">Recomendación/Meta</label>
                  <textarea name="recomendacion_meta" id="recomendacion_meta" class="form-control" style="min-width: 100%; max-width: 100%;  min-height: 115px;" disabled>{{ $plan->recomendacion_meta }}</textarea>
                </div>
              </div>


              <div class="col-md-6" hidden>
                <div class="form-group">
                  <label for="ods_pdi">ODS PDI</label>
                  <textarea name="ods_pdi" id="ods_pdi" class="form-control" style="min-width: 100%; max-width: 100%; min-height: 115px;" disabled>{{ $plan->ods_pdi }}</textarea>
                </div>
              </div>

              <div class="col-md-4">
                <div class="form-group">
                  <label for="eje_pdi">Eje PDI</label>
                  <input type="text" name="eje_pdi" id="eje_pdi" class="form-control" value="{{ $plan->eje_pdi }}" disabled>
                </div>
              </div>

              <div class="col-md-4">
                <div class="form-group">
                  <label for="ods_pdi_select">ODS PDI</label>
                  <input type="text" name="ods_pdi_select" id="ods_pdi_select" class="form-control" value="{{ $plan->ods }}" disabled>
                </div>
              </div>

              <div class="col-md-4">
                <div class="form-group">
                  <label for="objetivo_pdi">Objetivo PDI</label>
                  <input type="text" name="objetivo_pdi" id="objetivo_pdi" class="form-control" value="{{ $plan->objetivo_pdi }}" disabled>
                </div>
              </div>

              <div class="col-md-4">
                <div class="form-group">
                  <label for="estrategias">Estrategias</label>
                  <input type="text" name="estrategias" id="estrategias" class="form-control" value="{{ $plan->estrategia }}" disabled>
                </div>
              </div>

              <div class="col-md-4">
                <div class="form-group">
                  <label for="meta_pdi">Meta</label>
                  <input type="text" name="meta_pdi" id="meta_pdi" class="form-control" value="{{ $plan->meta }}" disabled>
                </div>
              </div>

              <div class="col-md-4">
                <div class="form-group">
                  <label for="indicador_pdi">Indicador PDI</label>
                  <input type="text" name="indicador_pdi" id="indicador_pdi" class="form-control" value="{{ $plan->indicador_pdi }}" disabled>
                </div>
              </div>

              <div class="col-md-4">
                <div class="form-group">
                  <label for="ambito_siemec">Ámbito SEAES</label>
                  <input type="text" name="ambito_siemec" id="ambito_siemec" class="form-control" value="{{ $plan->ambito_siemec }}" disabled>
                </div>
              </div>

              <div class="col-md-4">
                <div class="form-group">
                  <label for="criterio_siemec">Criterio SEAES</label>
                  <input type="text" name="criterio_siemec" id="criterio_siemec" class="form-control" value="{{ $plan->criterio_siemec }}" disabled>
                </div>
              </div>
          </div>


          <div class="row" style="margin-top: 1rem;">
            <div class="col-md-12">
              <h3>Acciones</h3>
            </div>

            <div class="col-md-12" id="div_tabla_acciones">
              
            </div>
          </div>


          <div class="row" style="margin-top: 1rem;">
            <form id="form_guarda_complemento">
              <div class="col-md-12">
                <h3>Complemento</h3>
              </div>

              <div class="col-md-4">
                <div class="form-group">
                  <label for="indicador_clave">Indicador clave</label>
                  <input type="text" name="indicador_clave" id="indicador_clave" class="form-control" value="{{ $complemento?->indicador_clave }}" disabled>
                </div>
              </div>

              <div class="col-md-4">
                <div class="form-group">
                  <label for="logros">Logros</label>
                  <input type="text" name="logros" id="logros" class="form-control" value="{{ $complemento?->logros }}" disabled>
                </div>
              </div>

              <div class="col-md-4">
                <div class="form-group">
                  <label for="impactos">Impactos</label>
                  <input type="text" name="impactos" id="impactos" class="form-control" value="{{ $complemento?->impactos }}" disabled>
                </div>
              </div>

              <div class="col-md-12">
                <div class="form-group">
                  <label for="observaciones">Observaciones</label>
                  <textarea name="observaciones" id="observaciones" class="form-control" style="min-width: 100%; max-width: 100%;" disabled placeholder="Máximo 600 caracteres">{{ $complemento?->observaciones }}</textarea>
                </div>
              </div>

              <div class="col-md-4">
                <div class="form-group">
                  <label for="evidencia">Evidencia</label>
                  @if( $complemento?->archivo )
                    <a href="{{ asset('storage/'.$complemento?->archivo) }}" target="_BLANK">Ver archivo</a>
                  @endif
                </div>
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
    var id_accion;



 

    const getAcciones = async () => {
        mostrarLoader('div_tabla_acciones', 'Espere un momento...');

        const response = await fetch(`${ base_url }/get/acciones/plan/{{ $plan->id }}`, {
            method: 'get'
        })

        $("#div_tabla_acciones").html(`<table class="table table-bordered table-striped" id="tabla_acciones"></table>`)

        const data = await response.json();

        if(data.code == 200){
          const table = $("#tabla_acciones").DataTable({
            data: data.data,
            scrollX: true,
            searching: true,
            ordering: true,
            info: false,
            paging: true,
            autoWidth: true,
            language: { url: base_url + '/js/Spanish.json' },
            columns: [
                { title: "Accion", data: 'accion' },
                { title: "Producto/Resultado", data: 'producto_resultado' },
                { title: "Fecha de inicio", data: 'fecha_inicio' },
                { title: "Fecha de termino", data: 'fecha_fin' },
                {
                  title: 'Evidencia', defaultContent: '', fnCreatedCell: (nTd, sData, oData, iRow, iCol) => {
                    if(oData.evidencia !== null){
                      $(nTd).append(`
                        <div style="text-align: center;">
                          <a href="${ base_url }/storage/${ oData.evidencia }" target="_BLANK" >Ver evidencia</a>
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





    getAcciones()
    
</script>
@endsection