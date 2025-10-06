@extends('layouts.guest')

@section('title', 'Establecer nueva contraseña')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h1 class="h4 mb-4 text-center">Establecer nueva contraseña</h1>

                        @if (session('status'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('status') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('password.store') }}">
                            @csrf

                            <input type="hidden" name="token" value="{{ $token }}">

                            <div class="mb-3">
                                <label class="form-label">Correo electrónico</label>
                                <input type="email" name="email" class="form-control" readonly
                                    value="{{ old('email', $email) }} ">
                                @error('email')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nueva contraseña</label>
                                <input type="password" name="password" class="form-control" required>
                                @error('password')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Confirmar contraseña</label>
                                <input type="password" name="password_confirmation" class="form-control" required>
                            </div>

                            <button class="btn btn-primary w-100" type="submit">Guardar contraseña</button>
                        </form>

                        <div class="text-center mt-3">
                            <a href="{{ route('login') }}">Regresar al inicio de sesión</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
