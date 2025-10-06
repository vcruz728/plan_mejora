<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="description" content="Seguimiento de planes de mejora BUAP">
    <meta name="keywords" content="buap">
    <link rel="icon" href="https://comunicacion.buap.mx/sites/default/files/favicon.ico">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    {{-- Iconos ZMDI (los de zmdi-*) --}}
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

    <title>@yield('title', 'Seguimiento de planes de mejora | Acceso')</title>

    {{-- CSS específico por página --}}
    @stack('styles')
</head>

<body>
    {{-- Zona opcional arriba (hero, logo, mensaje, etc.) --}}
    @yield('hero')

    {{-- Contenido principal de cada vista --}}
    @yield('content')

    {{-- JS global --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

    {{-- JS específico por página --}}
    @stack('scripts')
</body>

</html>
