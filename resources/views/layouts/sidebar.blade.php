@once
    @push('styles')
        <link rel="stylesheet" href="{{ asset('dist/css/sidebar.css') }}?v={{ filemtime(public_path('dist/css/sidebar.css')) }}">
    @endpush
@endonce

@php($rol = Auth::user()->rol ?? null)

<aside class="main-sidebar">
    <section class="sidebar">
        <ul class="sidebar-menu" data-widget="tree">

            {{-- Inicio (rol 1 y 2) --}}
            <li class="{{ request()->is('dashboard') ? 'active' : '' }}">
                <a href="{{ url('dashboard') }}">
                    <i class="fa fa-home nav-icon"></i>
                    <span class="text">Inicio</span>
                </a>
            </li>

            {{-- Recomendación/Metas: rol1 = agregar+listar, rol2 = solo listar --}}
            @if (in_array($rol, [1]))
                <li
                    class="treeview {{ request()->is('admin/agregar/nueva', 'admin/planes-mejora') ? 'menu-open active' : '' }}">
                    <a href="#">
                        <i class="fa fa-newspaper-o nav-icon"></i>
                        <span class="text">Recomendación/Metas</span>
                        <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                    </a>
                    <ul class="treeview-menu">

                        <li class="{{ request()->is('admin/agregar/nueva') ? 'active' : '' }}">
                            <a href="{{ url('/admin/agregar/nueva') }}">
                                <i class="fa fa-plus-circle nav-icon"></i>
                                <span class="text">Agregar nueva</span>
                            </a>
                        </li>

                        <li class="{{ request()->is('admin/planes-mejora') ? 'active' : '' }}">
                            <a href="{{ url('/admin/planes-mejora') }}">
                                <i class="fa fa-list-ul nav-icon"></i>
                                <span class="text">Listar</span>
                            </a>
                        </li>
                    </ul>
                </li>
            @endif

            {{-- Usuarios: solo rol 1 --}}
            @if ($rol == 1)
                <li
                    class="treeview {{ request()->is('admin/nuevo/usuario', 'admin/lista/usuario', 'admin/edita/usuario*', 'edita/usuario/*') ? 'menu-open active' : '' }}">
                    <a href="#">
                        <i class="fa fa-users nav-icon"></i>
                        <span class="text">Usuarios</span>
                        <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                    </a>
                    <ul class="treeview-menu">
                        <li class="{{ request()->is('admin/lista/usuario') ? 'active' : '' }}">
                            <a href="{{ url('/admin/lista/usuario') }}">
                                <i class="fa fa-address-book nav-icon"></i>
                                <span class="text">Administrar usuarios</span>
                            </a>
                        </li>
                    </ul>
                </li>
            @endif

            {{-- Consultores: solo rol 1 --}}
            @if ($rol == 1)
                <li class="treeview {{ request()->is('admin/consultores') ? 'menu-open active' : '' }}">
                    <a href="#">
                        <i class="fa fa-handshake-o nav-icon"></i>
                        <span class="text">Consultores</span>
                        <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                    </a>
                    <ul class="treeview-menu">
                        <li class="{{ request()->is('admin/consultores') ? 'active' : '' }}">
                            <a href="{{ url('/admin/consultores') }}">
                                <i class="fa fa-address-book nav-icon"></i>
                                <span class="text">Administrar consultores</span>
                            </a>
                        </li>
                    </ul>
                </li>
            @endif

            {{-- Catálogos (lo dejo visible; ajusta si quieres restringir) --}}
            <li class="treeview {{ request()->is('admin/catalogo/*') ? 'menu-open active' : '' }}">
                <a href="#">
                    <i class="fa fa-book nav-icon"></i>
                    <span class="text">Catálogos</span>
                    <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                </a>
                <ul class="treeview-menu">
                    <li class="{{ request()->is('admin/catalogo/procedencias') ? 'active' : '' }}">
                        <a href="{{ url('/admin/catalogo/procedencias') }}">
                            <i class="fa fa-cog nav-icon"></i>
                            <span class="text">Procedencias</span>
                        </a>
                    </li>
                    <li class="{{ request()->is('admin/catalogo/ambito-siemec') ? 'active' : '' }}">
                        <a href="{{ url('/admin/catalogo/ambito-siemec') }}">
                            <i class="fa fa-cog nav-icon"></i>
                            <span class="text">Ámbito SEAES</span>
                        </a>
                    </li>
                    <li class="{{ request()->is('admin/catalogo/criterio-siemec') ? 'active' : '' }}">
                        <a href="{{ url('/admin/catalogo/criterio-siemec') }}">
                            <i class="fa fa-cog nav-icon"></i>
                            <span class="text">Criterio SEAES</span>
                        </a>
                    </li>
                </ul>
            </li>

            {{-- Reportes (igual que antes; ojo con la ruta "resportes" si era typo) --}}
            <li class="treeview {{ request()->is('admin/resportes/*') ? 'menu-open active' : '' }}">
                <a href="#">
                    <i class="fa fa-file nav-icon"></i>
                    <span class="text">Reportes</span>
                    <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                </a>
            </li>

        </ul>
    </section>
</aside>

<script>
    $(function() {
        $('body').removeClass('sidebar-collapse');
        var saved = localStorage.getItem('sidebar_state');
        if (saved === 'collapsed') $('body').addClass('sidebar-collapse');

        $('.sidebar-toggle').on('click', function() {
            setTimeout(function() {
                var isCollapsed = $('body').hasClass('sidebar-collapse');
                localStorage.setItem('sidebar_state', isCollapsed ? 'collapsed' : 'open');
                // Si usas DataTables, puedes forzar ajuste tras colapsar:
                // setTimeout(function(){ $('.dataTable').each(function(){ $(this).DataTable?.().columns?.adjust?.(); }); }, 320);
            }, 200);
        });
    });
</script>
