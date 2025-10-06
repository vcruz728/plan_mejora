<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('htmlheader_title')</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="https://comunicacion.buap.mx/sites/default/files/favicon.ico">
    <link rel="stylesheet" href="{{ asset('bower_components/bootstrap/dist/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('bower_components/font-awesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('bower_components/Ionicons/css/ionicons.min.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.css" />
    <link rel="stylesheet" href="{{ asset('dist/css/AdminLTE.min.css') }}">
    <link rel="stylesheet" href="{{ asset('dist/css/skins/_all-skins.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/fixedColumns.dataTables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/custom.css?v=ccb56f50cd5c3fc4509b89f18bc871d3') }}">
    <link rel="stylesheet" href="{{ asset('css/apexcharts.css') }}">
    <link rel="stylesheet" href="{{ asset('bower_components/sweetalert/sweetalert.css') }}">
    <script src="{{ asset('bower_components/jquery/dist/jquery.min.js') }}"></script>
    <link href="{{ asset('css/toastr/toastr.min.css') }}" rel="stylesheet">
    <script src="{{ asset('js/index.js?v=ccb56f50cd5c3fc4509b89f18bc871d3') }}"></script>
    
</head>