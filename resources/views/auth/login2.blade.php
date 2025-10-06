<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="description" content="Seguimiento de planes de mejora BUAP">
    <meta name="keywords" content="buap">
    <link rel="icon" href="https://comunicacion.buap.mx/sites/default/files/favicon.ico">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/material-design-iconic-font@2.2.0/dist/css/material-design-iconic-font.min.css">

    {{-- Fuentes / Bootstrap 5 --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" id="bootstrapLink"
        rel="stylesheet">

    {{-- Tus estilos --}}
    <link rel="stylesheet" href="{{ asset('dist/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('dist/css/login2.css') }}">

    <title>Seguimiento de planes de mejora | Login</title>
</head>

<body>

    <div class="login-img">

        <div class="page">
            {{--             <div class="col-login mx-auto mt-7">
                <div class="text-center">
                    <img src="{{ asset('dist/images/logos/buap_blanco.png') }}" class="header-brand-img"
                        alt="BUAP" />
                </div>
            </div> --}}

            <div class="hero text-center py-3">
                <img src="{{ asset('dist/images/logos/buap_blanco.png') }}" class="header-brand-img" alt="BUAP">
                <p class="system-desc mb-0">Plataforma para seguimiento de planes de mejora</p>
            </div>


            <div class="container-login100">
                <div class="wrap-login100 p-6">
                    <div class="login100-form validate-form">
                        <span class="login100-form-title pb-2 mt-3">Inicia Sesión</span>
                        {{--                         <div class="text-muted mb-4" style="font-size:.95rem;">
                            Plataforma para seguimiento de planes de mejora
                        </div> --}}

                        {{-- status (éxito) --}}
                        @if (session('status'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fa fa-check-circle-o me-2" aria-hidden="true"></i>
                                {{ session('status') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        <form id="loginForm" method="POST" action="{{ route('login') }}">
                            @csrf

                            {{-- USUARIO --}}
                            <div class="wrap-input100 validate-input input-group mb-2">
                                <span class="input-group-text bg-white text-muted">
                                    <i class="zmdi zmdi-account text-muted" aria-hidden="true"></i>
                                </span>
                                <input class="input100 border-start-0 form-control ms-0" type="text" name="usuario"
                                    placeholder="Usuario" value="{{ old('usuario') }}" required autofocus
                                    aria-label="Usuario" />
                            </div>
                            @error('usuario')
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="zmdi zmdi-mood-bad me-2" aria-hidden="true"></i>
                                    {{ $message }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Cerrar"></button>
                                </div>
                            @enderror

                            {{-- PASSWORD + toggle (equivalente a InputGroup de TSX) --}}
                            <div class="wrap-input100 validate-input input-group mt-3" id="Password-toggle">
                                <button type="button" id="togglePassword" class="input-group-text bg-white p-0"
                                    aria-label="Mostrar/Ocultar contraseña">
                                    <div class="bg-white text-muted p-3">
                                        <i id="togglePasswordIcon" class="zmdi zmdi-eye-off text-muted"
                                            aria-hidden="true"></i>
                                    </div>
                                </button>
                                <input type="password" id="password" name="password" class="form-control"
                                    placeholder="Contraseña" required autocomplete="current-password" />
                            </div>

                            @error('password')
                                <div class="alert alert-danger alert-dismissible fade show mt-2" role="alert">
                                    <i class="fa fa-frown-o me-2" aria-hidden="true"></i>
                                    {{ $message }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @enderror

                            {{-- Botón (equivalente a disabled={processing} y loading spinner) --}}
                            <div class="container-login100-form-btn">
                                <button id="loginBtn" class="login100-form-btn btn btn-primary" type="submit">
                                    Ingresar
                                    <span id="loginSpinner" role="status" aria-hidden="true"
                                        class="spinner-border spinner-border-sm ms-2 d-none"></span>
                                </button>
                            </div>
                        </form>

                        <div class="text-center pt-3">
                            <p class="text-dark mb-0 fs-13">
                                ¿No recuerdas tu contraseña?
                                <a href="{{ route('password.request') }}" class="text-primary ms-1">Recupérala</a>
                            </p>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Bootstrap 5 JS (bundle con Popper) --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Toggle de contraseña + spinner al enviar (sustituye lógica de TSX: setPasswordshow / loading) --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const pwd = document.getElementById('password');
            const toggle = document.getElementById('togglePassword');
            const icon = document.getElementById('togglePasswordIcon');
            const form = document.getElementById('loginForm');
            const btn = document.getElementById('loginBtn');
            const spinner = document.getElementById('loginSpinner');

            toggle?.addEventListener('click', () => {
                const showing = pwd.type === 'text';
                pwd.type = showing ? 'password' : 'text';
                icon.classList.toggle('zmdi-eye', !showing);
                icon.classList.toggle('zmdi-eye-off', showing);
            });

            form?.addEventListener('submit', () => {
                btn.setAttribute('disabled', 'disabled');
                spinner.classList.remove('d-none');
            });
        });
    </script>
</body>

</html>
