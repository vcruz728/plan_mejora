@extends('layouts.guest')

@section('title', 'Recuperar contraseña')

{{-- Hero opcional arriba del contenido --}}
@section('hero')
    <div class="mx-auto mt-4 text-center" style="max-width: 920px;">
        <p style="color:#fff; margin:0 12px;">
            ¿Olvidaste tu contraseña? No hay problema.
            Simplemente indícanos tu correo electrónico y te enviaremos
            un enlace para restablecerla. Podrás elegir una nueva.
        </p>
    </div>
@endsection

@push('styles')
    <style>
        .forgot-card {
            width: clamp(280px, 28vw, 420px);
        }
    </style>
@endpush

@section('content')
    <div class="container-login100">
        <div class="wrap-login100 p-6 forgot-card">
            <div class="login100-form validate-form">

                @if (session('status'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="zmdi zmdi-check-circle me-2" aria-hidden="true"></i>
                        {{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                    </div>
                @endif

                <form id="forgotForm" method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <div class="wrap-input100 validate-input input-group mb-2"
                        data-bs-validate="Valid email is required: ex@abc.xyz">
                        <span class="input-group-text bg-white text-muted">
                            <i class="zmdi zmdi-email text-muted" aria-hidden="true"></i>
                        </span>
                        <input class="input100 border-start-0 form-control ms-0" type="email" name="email"
                            placeholder="Correo electrónico" value="{{ old('email') }}" required autofocus />
                    </div>

                    @error('email')
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="zmdi zmdi-alert-circle-o me-2" aria-hidden="true"></i>
                            {{ $message }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                        </div>
                    @enderror

                    <div class="container-login100-form-btn">
                        <button id="sendBtn" class="login100-form-btn btn btn-primary" type="submit">
                            Enviar enlace
                            <span id="sendSpinner" role="status" aria-hidden="true"
                                class="spinner-border spinner-border-sm ms-2 d-none"></span>
                        </button>
                    </div>
                </form>

                <div class="text-center pt-3">
                    <a class="text-primary" href="{{ route('login') }}">Regresar al inicio de sesión</a>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('forgotForm');
            const btn = document.getElementById('sendBtn');
            const spinner = document.getElementById('sendSpinner');

            form?.addEventListener('submit', () => {
                btn.setAttribute('disabled', 'disabled');
                spinner.classList.remove('d-none');
            });
        });
    </script>
@endpush
