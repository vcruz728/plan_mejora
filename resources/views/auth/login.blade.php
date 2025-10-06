<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="description" content="Convocatoria PRODEP">
        <meta name="keywords" content="buap">
        <link rel="icon" href="https://comunicacion.buap.mx/sites/default/files/favicon.ico">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

        <!-- <link rel="icon" href="{{ asset('img/favicon.ico') }}"> -->
        <link rel="stylesheet" href="{{ asset('bower_components/bootstrap/dist/css/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('dist/css/AdminLTE.min.css') }}">
        <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Roboto:300,400,500,700" type="text/css">

        <title>Seguimiento de planes de mejora | Login</title>
    </head>
    <body style="display: flex; flex-direction: column; background-image: linear-gradient(to top, #fff1eb 0%, #ace0f9 100%); font-family: 'Roboto','Helvetica','Arial',sans-serif;">
        <input type="hidden" id="url_login" value="{{ url('Usuarios/auth') }}">
        <div style="width: 100%; flex: 1;">
            <div class="container" style="display: flex;align-items: center; justify-content: center;">
                <div class="col-xs-6">
                    <div class="login-box-body" style="box-shadow: 0 3px 6px rgba(0,0,0,0.16), 0 3px 6px rgba(0,0,0,0.23); border: 0px; margin-top: 94px;">
                        <div style="display: flex;">
                            <div style="flex: 1;">
                                <!-- <img src="{{ asset('img/cfe-distribucion.png') }}" width="160" alt=""/> -->
                            </div>
                        </div>

                        <div>
                            <span style="font-weight: bold; font-size: 26px; color: #424242;">
                                Plataforma de seguimiento de planes de mejora
                            </span>
                        </div>

                        <div>
                            <span style="font-weight: bold; font-size: 20px; color: #616161;">
                                Acceder
                            </span>
                        </div>

                        <div style="margin-bottom: 12px; color: #424242; font-size: 14px;">
                            <span>Usa tu cuenta</span>
                        </div>
                    <form method="POST" action="{{ url('/login') }}">

                        @csrf
                            @method('post')
                            @error('usuario')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                            <div class="form-group has-feedback">
                                <input type="text" class="form-control" placeholder="Capture su usuario" id="usuario" name="usuario" value="{{old('usuario')}}"
                                autofocus >
                                <span class="glyphicon glyphicon-user form-control-feedback"></span>
                            </div>
                            <div class="form-group has-feedback" id="div_passLogin">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                            </div>

                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <div class="alert alert-danger">{{ $message }}</div>
                                </span>
                            @enderror



                        <div class="form-group row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Login') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                </div>
            </div>
        </div>

        <div style="display: flex; align-items: center; justify-content: center; margin-bottom: 12px;">
            <span style="color: #757575;">Revisión 1.0.0</span>
        </div>

        <script src="{{ asset('bower_components/jquery/dist/jquery.min.js') }}"></script>
        <script src="{{ asset('bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    </body>
</html>

