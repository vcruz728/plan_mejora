<!DOCTYPE html>
<html lang="en">
<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
@section('htmlheader')
    @include('layouts.header')

@show



<body class="hold-transition skin-green-light sidebar-mini">
    <input type="hidden" id="base_url" name="base_url" value="{{ url('/') }}">
    <div class="wrapper">
        @include('layouts.sidebar')
        @include('layouts.navbar')
        <div class="content-wrapper">
            <section class="content-header">
                @yield('main-content')
            </section>
        </div>

        <div id="snackbar"></div>
        @section('scripts')
            @include('layouts.scripts')
            @yield('localscripts')
        @show
</body>

</html>
