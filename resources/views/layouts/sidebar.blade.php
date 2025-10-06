<aside class="main-sidebar">
  <section class="sidebar">
    <ul class="sidebar-menu" data-widget="tree">

      @if( Auth::user()->rol == 1 )
        <li class='treeview active'>
          <a href='#'>
            <i class="fa fa-newspaper-o"></i>
            <span>Recomendación/Metas</span>
          </a>
          <ul class='treeview-menu'>
            <li><a href="{{ url('/admin/agregar/nuvea') }}"><i class="fa fa-plus-circle"></i>Agregar nueva</a></li>
            <li><a href="{{ url('/dashboard') }}"><i class="fa fa-sort-amount-asc"></i>Listar</a></li>
          </ul>
        </li>

        <li class='treeview active'>
          <a href='#'>
            <i class="fa fa-users"></i>
            <span>Usuario</span>
          </a>
          <ul class='treeview-menu'>
            <li><a href="{{ url('/admin/nuevo/usuario') }}"><i class="fa fa-user-plus"></i>Nuevo usuario</a></li>
            <li><a href="{{ url('/admin/lista/usuario') }}"><i class="fa fa-sort-amount-asc"></i>Listar</a></li>
            <li><a href="{{ url('/admin/consultores') }}"><i class="fa fa-search"></i>Nuevo consultor</a></li>
          </ul>
        </li>


        <li class='treeview active'>
          <a href='#'>
            <i class="fa fa-book"></i>
            <span>Catalogos</span>
          </a>
          <ul class='treeview-menu'>
            <li><a href="{{ url('/admin/catalogo/procedencias') }}"><i class="fa fa-gear"></i>Procedencias</a></li>
            <li><a href="{{ url('/admin/catalogo/ambito-siemec') }}"><i class="fa fa-gear"></i>Ámbito SEAES</a></li>
            <li><a href="{{ url('/admin/catalogo/criterio-siemec') }}"><i class="fa fa-gear"></i>Criterio SEAES</a></li>
          </ul>
        </li>
      @endif

  
    </ul>
  </section>
</aside>