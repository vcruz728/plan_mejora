<header class="main-header">
    <a class="logo">
        <span class="logo-mini"><b>BUAP</b></span>
        <span class="logo-lg"><b>BUAP</b></span>
    </a>

    <nav class="navbar navbar-static-top">
        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </a>

        <ul class="nav navbar-nav">
            <li><a href="{{ route('dashboard') }}" style="padding: 0; padding-top: 9px;"><img
                        src="{{ asset('img/Imagen1.jpg') }}" style="width: 41px;"></a></li>
        </ul>

        <!-- Texto centrado y responsivo -->
        <div class="navbar-center d-none d-sm-block"
            style="position: absolute; left: 0; right: 0; text-align: center; pointer-events: none;">
            <span style="font-size: 1.5rem; font-weight: 500; color: white; white-space: nowrap;">
                Plataforma de seguimiento de planes de mejora
            </span>
        </div>
        <!-- Fin texto centrado -->

        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">


                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <span class="hidden-xs" style="text-transform: capitalize;">{{ Auth::user()->name }}</span>
                    </a>

                    <ul class="dropdown-menu"
                        style="background-color: #FFFFFF; border: 0px; box-shadow: 0 10px 20px rgba(0,0,0,0.19), 0 6px 6px rgba(0,0,0,0.23); margin-right: 30px; padding-top: 6px; padding-bottom: 6px; border-radius: 0px; margin-top: -10px;">


                        <a href="{{ route('logout') }}"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                            style="color: #424242;">
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                            </form>
                            <div class="dropdown__item" onclick="">
                                <svg style="width: 24px; font-size: 20px;" xmlns="http://www.w3.org/2000/svg"
                                    xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" focusable="false"
                                    width="1em" height="1em"
                                    style="-ms-transform: rotate(360deg); -webkit-transform: rotate(360deg); transform: rotate(360deg);"
                                    preserveAspectRatio="xMidYMid meet" viewBox="0 0 512 512">
                                    <path
                                        d="M400 54.1c63 45 104 118.6 104 201.9 0 136.8-110.8 247.7-247.5 248C120 504.3 8.2 393 8 256.4 7.9 173.1 48.9 99.3 111.8 54.2c11.7-8.3 28-4.8 35 7.7L162.6 90c5.9 10.5 3.1 23.8-6.6 31-41.5 30.8-68 79.6-68 134.9-.1 92.3 74.5 168.1 168 168.1 91.6 0 168.6-74.2 168-169.1-.3-51.8-24.7-101.8-68.1-134-9.7-7.2-12.4-20.5-6.5-30.9l15.8-28.1c7-12.4 23.2-16.1 34.8-7.8zM296 264V24c0-13.3-10.7-24-24-24h-32c-13.3 0-24 10.7-24 24v240c0 13.3 10.7 24 24 24h32c13.3 0 24-10.7 24-24z"
                                        fill="#626262" />
                                </svg>
                                &nbsp;
                                <span>Cerrar Sesión</span>
                            </div>
                        </a>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
    @stack('styles')
    @stack('scripts')
</header>
