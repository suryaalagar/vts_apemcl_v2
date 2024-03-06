<div class="navbar-container content">
    <div class="collapse navbar-collapse" id="navbar-mobile">
        <ul class="nav navbar-nav mr-auto float-left">

        </ul>
        <ul class="nav navbar-nav float-right">
            <li>
                <div style="margin-top : 10px;">
                    <button type="button" class="btn btn-primary">{{ Auth::user()->last_login_time }}</button>
                </div>
            </li>
            <li class="dropdown dropdown-user nav-item"><a class="dropdown-toggle nav-link dropdown-user-link"
                    href="#" data-toggle="dropdown">
                    <div class="avatar avatar-online"><img src="app-assets/images/portrait/small/avatar-s-1.png"
                            alt="avatar"><i></i></div><span class="user-name">{{ Auth::user()->name }}</span>
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="{{ route('logout') }}"
                        onclick="event.preventDefault();document.getElementById('logout-form').submit();"><i
                            class="feather icon-power"></i> Logout</a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </li>
        </ul>
    </div>
</div>
