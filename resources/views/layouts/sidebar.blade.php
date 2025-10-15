@once
    @push('styles')
        <link rel="stylesheet" href="{{ asset('dist/css/sidebar.css') }}?v={{ filemtime(public_path('dist/css/sidebar.css')) }}">
    @endpush
@endonce


<aside class="main-sidebar">
    <section class="sidebar">
        <ul class="sidebar-menu" data-widget="tree">
            @if (Auth::user()->rol == 1)
                <li class="{{ request()->is('dashboard') ? 'active' : '' }}">
                    <a href="{{ url('dashboard') }}">
                        <i class="fa fa-home nav-icon"></i>
                        <span class="text">Inicio</span>
                    </a>
                </li>

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
                        <li class="{{ request()->is('/admin/planes-mejora') ? 'active' : '' }}">
                            <a href="{{ url('/admin/planes-mejora') }}">
                                <i class="fa fa-list-ul nav-icon"></i>
                                <span class="text">Listar</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li
                    class="treeview {{ request()->is('admin/nuevo/usuario', 'admin/lista/usuario', 'admin/edita/usuario*', 'edita/usuario/*') ? 'menu-open active' : '' }}">
                    <a href="#">
                        <i class="fa fa-users nav-icon"></i>
                        <span class="text">Usuarios</span>
                        <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                    </a>
                    <ul class="treeview-menu">
                        <li class="{{ request()->is('admin/nuevo/usuario') ? 'active' : '' }}">
                            <a href="{{ url('/admin/nuevo/usuario') }}">
                                <i class="fa fa-address-book nav-icon"></i>
                                <span class="text">Nuevo usuario</span>
                            </a>
                        </li>
                        <li class="{{ request()->is('admin/lista/usuario') ? 'active' : '' }}">
                            <a href="{{ url('/admin/lista/usuario') }}">
                                <i class="fa fa-list-ol nav-icon"></i>
                                <span class="text">Listar</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <li
                    class="treeview {{ request()->is('admin/consultores', 'admin/edita/usuario*', 'edita/usuario/*') ? 'menu-open active' : '' }}">
                    <a href="#">
                        <i class="fa fa-eye nav-icon"></i>
                        <span class="text">Consultores</span>
                        <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                    </a>
                    <ul class="treeview-menu">
                        <li class="{{ request()->is('admin/consultores') ? 'active' : '' }}">
                            <a href="{{ url('/admin/consultores') }}">
                                <i class="fa fa-address-book nav-icon"></i>
                                <span class="text">Administrar Consultores</span>
                            </a>
                        </li>
                    </ul>
                </li>

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
            @endif
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
            }, 200);
        });
    });
</script>
