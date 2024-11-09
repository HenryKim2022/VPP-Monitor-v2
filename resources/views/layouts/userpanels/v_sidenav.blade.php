<!-- BEGIN: Main Menu-->
<div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
    <div class="navbar-header">
        <ul class="nav navbar-nav flex-row">
            <li class="nav-item mr-auto"><a class="navbar-brand" href=""><span class="brand-logo">
                        <!-- route('userPanels.dashboard') }} -->
                        <img src="{{ asset('public/assets/logo/vp_logo.svg') }}" alt="VPP Monitor Logo">
                    </span>
                    <h2 class="brand-text">VPP Monitor</h2>
                </a></li>
            <li class="nav-item nav-toggle"><a class="nav-link modern-nav-toggle pr-0" data-toggle="collapse"><i
                        class="d-block d-xl-none text-primary toggle-icon font-medium-4" data-feather="x"></i><i
                        class="d-none d-xl-block collapse-toggle-icon font-medium-4 text-primary" data-feather="disc"
                        data-ticon="disc"></i></a></li>
        </ul>
    </div>
    <style>
        li.nav-item.mr-auto {
            margin-top: -0.5rem;
        }
    </style>
    <div class="shadow-bottom"></div>
    <div class="main-menu-content">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
            @if (Route::has('userPanels.dashboard'))
                <li class=" nav-item"><a class="d-flex align-items-center" href="{{ route('userPanels.dashboard') }}"><i
                            data-feather="home"></i><span class="menu-title text-truncate"
                            data-i18n="Dashboard">Dashboard</span></a>
                </li>
            @endif
            @if (auth()->user()->type == 'Superuser' || auth()->user()->type == 'Supervisor')
                <li class=" navigation-header"><span data-i18n="Data Employee">Employees R&T</span><i
                        data-feather="more-horizontal"></i>
                </li>
            @endif
            @if (auth()->user()->type === 'Superuser')
                @if (Route::has('m.emp'))
                    <li class=" nav-item"><a class="d-flex align-items-center" href="{{ route('m.emp') }}"><i
                                data-feather="users"></i><span class="menu-title text-truncate"
                                data-i18n="Employees">Employees</span></a>
                    </li>
                @endif
            @endif

            @if (auth()->user()->type === 'Superuser' || auth()->user()->type === 'Supervisor')
                @if (Route::has('m.emp.roles'))
                    <li class=" nav-item"><a class="d-flex align-items-center" href="{{ route('m.emp.roles') }}"><i
                                data-feather="briefcase"></i><span class="menu-title text-truncate"
                                data-i18n="Office Roles">Office Roles</span></a>
                    </li>
                @endif
                @if (Route::has('m.emp.teams'))
                    <li class=" nav-item"><a class="d-flex align-items-center" href="{{ route('m.emp.teams') }}"><i
                                data-feather="users"></i><span class="menu-title text-truncate"
                                data-i18n="Teams">Teams</span></a>
                    </li>
                @endif
            @endif

            {{-- <li class=" nav-item">
                <a class="d-flex align-items-center" href="#"><i data-feather="users"></i><span
                        class="menu-title text-truncate" data-i18n="Employees">Employees</span></a>
                <ul class="menu-content">
                    <li><a class="d-flex align-items-center" href="{{ route('m.emp') }}">
                            <i data-feather="circle"></i><span class="menu-item text-truncate"
                                data-i18n="List Manage">List Manage</span></a>
                    </li>
                    <li><a class="d-flex align-items-center" href="{{ route('m.emp.roles') }}">
                            <i data-feather="circle"></i><span class="menu-item text-truncate"
                                data-i18n="Role Manage">Role Manage</span></a>
                    </li>
                </ul>
            </li> --}}


            @if (auth()->user()->type === 'Superuser' || auth()->user()->type === 'Supervisor' || auth()->user()->type == 'Engineer')
                <li class=" navigation-header"><span data-i18n="Data Employee">Projects M&W</span><i
                        data-feather="more-horizontal"></i>
                </li>
                @if (Route::has('m.projects'))
                    <li class=" nav-item">
                        <a class="d-flex align-items-center" href="{{ route('m.projects') }}"><i
                                data-feather="monitor"></i><span class="menu-title text-truncate"
                                data-i18n="Projects">Project List</span></a>
                    </li>
                @endif
            @endif
            {{-- @if (Route::has('m.monitoring'))
                <li class=" nav-item">
                    <a class="d-flex align-items-center" href="{{ route('m.monitoring') }}"><i
                            data-feather="monitor"></i><span class="menu-title text-truncate"
                            data-i18n="Monitoring & Worksheets">Monitoring & Worksheets</span></a>
                </li>
            @endif
            @if (Route::has('m.wrksheet'))
                <li class=" nav-item">
                    <a class="d-flex align-items-center" href="{{ route('m.wrksheet') }}"><i
                            data-feather="book-open"></i><span class="menu-title text-truncate"
                            data-i18n="Worksheets">Worksheets</span></a>
                </li>
            @endif --}}



            @if (auth()->user()->type === 'Superuser')
                <li class=" navigation-header"><span data-i18n="Data Accounts">Data Accounts</span><i
                        data-feather="more-horizontal"></i>
                </li>
                @if (Route::has('m.user.emp'))
                    <li class=" nav-item"><a class="d-flex align-items-center" href="#"><i
                                data-feather="users"></i><span class="menu-title text-truncate"
                                data-i18n="Employee Accounts">Employees</span></a>
                        <ul class="menu-content">
                            <li><a class="d-flex align-items-center" href="{{ route('m.user.emp') }}">
                                    <i data-feather="circle"></i><span class="menu-item text-truncate"
                                        data-i18n="User List">User List</span></a>
                            </li>

                        </ul>
                    </li>
                @endif
                @if (Route::has('m.user.client'))
                    <li class=" nav-item"><a class="d-flex align-items-center" href="#"><i
                                data-feather="users"></i><span class="menu-title text-truncate"
                                data-i18n="Client Accounts">Clients</span></a>
                        <ul class="menu-content">
                            <li><a class="d-flex align-items-center" href="{{ route('m.user.client') }}">
                                    <i data-feather="circle"></i><span class="menu-item text-truncate"
                                        data-i18n="User List">User List</span></a>
                            </li>

                        </ul>
                    </li>
                @endif
            @endif


            {{-- @if (auth()->user()->type == 'Superuser' || auth()->user()->type == 'Supervisor' || auth()->user()->type == 'Engineer' || auth()->user()->type == 'Client' || auth()->user()->type == 'Public' || auth()->user()->type == '') --}}
            <li class="navigation-header">
                <span data-i18n="Help &amp; Supports">Help &amp; Supports</span>
                <i data-feather="more-horizontal"></i>
            </li>
            <li onclick="openModal('#contactUsModal')">
                <a class="d-flex align-items-center" id="contactUsLink">
                    <i data-feather="mail"></i>
                    <span class="menu-item text-truncate" data-i18n="ContactUS">ContactUS</span>
                </a>
            </li>
            <li onclick="openModal('#aboutUsModal')">
                <a class="d-flex align-items-center" id="aboutUsLink">
                    <i data-feather="help-circle"></i>
                    <span class="menu-item text-truncate" data-i18n="AboutUs">AboutUs</span>
                </a>
            </li>
            {{-- <li class="nav-item">
                <a class="d-flex align-items-center" href="#">
                    <i data-feather="package"></i>
                    <span class="menu-title text-truncate" data-i18n="Help">Help</span>
                </a>
                <ul class="menu-content">
                    <li onclick="openModal('#contactUsModal')">
                        <a class="d-flex align-items-center" id="contactUsLink">
                            <i data-feather="circle"></i>
                            <span class="menu-item text-truncate" data-i18n="ContactUS">ContactUS</span>
                        </a>
                    </li>
                    <li onclick="openModal('#aboutUsModal')">
                        <a class="d-flex align-items-center" id="aboutUsLink">
                            <i data-feather="circle"></i>
                            <span class="menu-item text-truncate" data-i18n="AboutUs">AboutUs</span>
                        </a>
                    </li>

                </ul>
            </li> --}}
            {{-- @endif --}}



        </ul>
    </div>
</div>
<!-- END: Main Menu-->
