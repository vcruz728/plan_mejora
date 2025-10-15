@extends('app')
@section('htmlheader_title')
    Listar consultores
@endsection
@section('main-content')
    <section class="content-header">
        <h1>Lista de consultores</h1>
    </section>
    <!-- search container -->

    <div class="col-xs-12">
        <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title">Usuarios</h3>
            </div>
            <div class="box-body" style="padding-top: 2rem;">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group" style="display: flex; justify-content: flex-end;">
                            <button class="btn btn-primary btn-sm mt-3" data-toggle="modal"
                                data-target="#modal_agrega_consultor">Nueva Consultor <i
                                    class="fa fa-plus-circle"></i></button>
                        </div>
                    </div>

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

    <div class="modal fade" id="modal_agrega_consultor" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Agrega consultor</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <form id="form_alta_usuario">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="usuario">Usuario</label>
                                    <input type="text" name="usuario" id="usuario" class="form-control">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Nombre del responsable</label>
                                    <input type="text" name="name" id="name" class="form-control">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Correo electrónico</label>
                                    <input type="text" name="email" id="email" class="form-control">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="procedencia">Procedencia</label>
                                    <select name="procedencia" id="procedencia" class="form-control">
                                        <option value="">Seleccione una opción</option>
                                        @foreach ($procedencias as $value)
                                            <option value="{{ $value->id }}">{{ $value->descripcion }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="modal-footer" id="container_btns_modal_confirmacion">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success" id="btn_guarda"
                        onclick="guardaUsuario(); this.disabled=true;">Agregar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_edita_usuario" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Edita Usuario</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <form id="form_edita_usuario">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="usuario_edit">Usuario</label>
                                    <input type="text" name="usuario_edit" id="usuario_edit" class="form-control">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name_edit">Nombre del responsable</label>
                                    <input type="text" name="name_edit" id="name_edit" class="form-control">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email_edit">Correo electrónico</label>
                                    <input type="text" name="email_edit" id="email_edit" class="form-control">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="procedencia_edit">Procedencia</label>
                                    <select name="procedencia_edit" id="procedencia_edit" class="form-control">
                                        <option value="">Seleccione una opción</option>
                                        @foreach ($procedencias as $value)
                                            <option value="{{ $value->id }}">{{ $value->descripcion }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="modal-footer" id="container_btns_modal_confirmacion">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btn_guarda" onclick="reseteaPassword()">Resetear
                        contraseña</button>
                    <button type="button" class="btn btn-success" id="btn_guarda" onclick="editaUsuario()">Guardar
                        cambios</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('localscripts')
    <script>
        var base_url = $("input[name='base_url']").val();
        var id


        const guardaUsuario = async () => {
            const body = new FormData(document.getElementById('form_alta_usuario'));
            const response = await fetch(`${ base_url }/admin/guarda/nuevo/consultor`, {
                method: 'post',
                body,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            const data = await response.json();

            if (data.code == 200) {
                $("#procedencia").val("").change()
                $("#name").val("")
                $("#email").val("")
                $("#usuario").val("")
                $("#modal_agrega_consultor").modal('hide')
                getUsuario()
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


        const getUsuario = async () => {
            $("#div_tabla").html("<h4>Cargando..</h4>");
            const response = await fetch(`${ base_url }/admin/get/usuarios/sistema/consultores`, {
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
                language: {
                    url: base_url + '/js/Spanish.json'
                },
                columns: [{
                        title: "Usuario",
                        data: 'usuario'
                    },
                    {
                        title: "Nombre",
                        data: 'name'
                    },
                    {
                        title: "Email",
                        data: 'email'
                    },
                    {
                        title: "Procedencia",
                        data: 'procedencia'
                    },
                    {
                        title: 'Acciones',
                        defaultContent: '',
                        fnCreatedCell: (nTd, sData, oData, iRow, iCol) => {
                            $(nTd).append(`
                <div style="text-align: center;">
                  <button class="btn btn-warning btn-sm" onclick="abreModal('${ oData.id }')">Editar</button>
                </div>`);
                        }
                    }
                ],
            });
        }

        const abreModal = async (id_user) => {
            id = id_user
            const response = await fetch(`${ base_url }/admin/get/informacion/usuario/${ id_user }`, {
                method: 'GET'
            });

            const data = await response.json();


            $("#procedencia_edit").val(data.data.procedencia)
            $("#usuario_edit").val(data.data.usuario)
            $("#name_edit").val(data.data.name)
            $("#email_edit").val(data.data.email)

            $("#modal_edita_usuario").modal()
        }

        const editaUsuario = async () => {
            const body = new FormData(document.getElementById('form_edita_usuario'));
            const response = await fetch(`${ base_url }/admin/edita/consultor/${ id }`, {
                method: 'post',
                body,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            const data = await response.json();

            if (data.code == 200) {
                $("#modal_edita_usuario").modal('hide')
                getUsuario()
                $("#procedencia_edit").val("")
                $("#name_edit").val("")
                $("#email_edit").val("")
                $("#usuario_edit").val("")
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

        const reseteaPassword = async () => {
            const response = await fetch(`${ base_url }/admin/usuario/resetea-password/${ id }`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            const data = await response.json();

            if (data.code == 200) {
                $("#modal_edita_usuario").modal('hide')
                swal("¡Correcto!", data.mensaje, "success");
            } else {
                swal("¡Error!", data.mensaje, "error");
            }
        }

        getUsuario()
    </script>
@endsection
