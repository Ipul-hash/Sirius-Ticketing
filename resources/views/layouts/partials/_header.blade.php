@php
    $headerUser = auth()->user();
    $hRoleVal = $headerUser?->role?->value ?? 'company_admin';
    $hRoleColor = match($hRoleVal) {
        'superadmin' => 'danger',
        'company_admin' => 'primary',
        'agent' => 'info',
        'requester' => 'success',
        default => 'secondary',
    };
    $hRoleLabel = match($hRoleVal) {
        'superadmin' => 'Superadmin',
        'company_admin' => 'Company Admin',
        'agent' => 'Teknisi IT',
        'requester' => 'Requester (User)',
        default => ucfirst($hRoleVal),
    };
    $hInitials = strtoupper(substr($headerUser?->name ?? 'User', 0, 2));
    $tenantName = $headerUser?->company?->name ?? 'Sirius Global Tech';
@endphp

<div id="kt_app_header" class="app-header" data-kt-sticky="true" data-kt-sticky-activate="{default: true, lg: true}" data-kt-sticky-name="app-header-minimize" data-kt-sticky-offset="{default: '200px', lg: '0'}" data-kt-sticky-animation="false">
    <div class="app-container container-fluid d-flex align-items-stretch justify-content-between" id="kt_app_header_container">
        <!-- Mobile sidebar toggle -->
        <div class="d-flex align-items-center d-lg-none ms-n3 me-1 me-md-2" title="Show sidebar menu">
            <div class="btn btn-icon btn-active-color-primary w-35px h-35px" id="kt_app_sidebar_mobile_toggle">
                <i class="ki-duotone ki-abstract-14 fs-2"><span class="path1"></span><span class="path2"></span></i>
            </div>
        </div>

        <!-- Mobile logo (Hanya tampil di HP / tablet) -->
        <div class="d-flex align-items-center flex-grow-1 flex-lg-grow-0 me-lg-15 d-lg-none">
            <a href="{{ url('/') }}" class="d-flex align-items-center">
                <img alt="Logo Sirius" src="{{ asset('assets/media/logos/sirius-icon.svg') }}" class="h-30px me-2" />
                <span class="fs-4 fw-bolder text-gray-900">Sirius</span>
            </a>
        </div>

        <!-- Header Navbar -->
        <div class="d-flex align-items-stretch justify-content-between flex-lg-grow-1" id="kt_app_header_wrapper">
            <div class="app-header-menu app-header-mobile-drawer align-items-stretch">
                <!-- Tenant Badge / Info -->
                <div class="d-flex align-items-center">
                    <span class="badge badge-light-primary fs-7 fw-bold me-2 px-3 py-2">
                        <i class="ki-duotone ki-shop fs-6 me-1 text-primary"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                        Tenant: {{ $tenantName }}
                    </span>
                    <span class="badge badge-light-{{ $hRoleColor }} fs-8 fw-bold px-2 py-1 d-none d-md-inline-block">
                        Mode: {{ $hRoleLabel }}
                    </span>
                </div>
            </div>

            <!-- Header Right Menu -->
            <div class="app-navbar flex-shrink-0 align-items-center">
                <!-- Theme mode switcher (Bootstrap 5 Dropdown) -->
                <div class="app-navbar-item ms-1 ms-md-3 dropdown">
                    <!-- Toggle Button -->
                    <button type="button" class="btn btn-icon btn-custom btn-icon-muted btn-active-light btn-active-color-primary w-35px h-35px" data-bs-toggle="dropdown" aria-expanded="false" title="Ganti Mode Tampilan (Light / Dark)">
                        <i class="ki-duotone ki-night-day theme-light-show fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span><span class="path7"></span><span class="path8"></span><span class="path9"></span><span class="path10"></span></i>
                        <i class="ki-duotone ki-moon theme-dark-show fs-2"><span class="path1"></span><span class="path2"></span></i>
                    </button>
                    <!-- Dropdown Menu -->
                    <div class="dropdown-menu dropdown-menu-end menu menu-sub menu-column menu-rounded menu-title-gray-700 menu-icon-gray-500 menu-active-bg menu-state-color fw-semibold py-4 fs-base w-175px shadow-sm" data-kt-element="theme-mode-menu">
                        <!-- Light Mode -->
                        <div class="menu-item px-3 my-0">
                            <a href="javascript:void(0);" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="light">
                                <span class="menu-icon" data-kt-element="icon">
                                    <i class="ki-duotone ki-night-day fs-2 text-warning"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span><span class="path7"></span><span class="path8"></span><span class="path9"></span><span class="path10"></span></i>
                                </span>
                                <span class="menu-title">Light (Terang)</span>
                            </a>
                        </div>
                        <!-- Dark Mode -->
                        <div class="menu-item px-3 my-0">
                            <a href="javascript:void(0);" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="dark">
                                <span class="menu-icon" data-kt-element="icon">
                                    <i class="ki-duotone ki-moon fs-2 text-primary"><span class="path1"></span><span class="path2"></span></i>
                                </span>
                                <span class="menu-title">Dark (Gelap)</span>
                            </a>
                        </div>
                        <!-- System Mode -->
                        <div class="menu-item px-3 my-0">
                            <a href="javascript:void(0);" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="system">
                                <span class="menu-icon" data-kt-element="icon">
                                    <i class="ki-duotone ki-screen fs-2 text-info"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                                </span>
                                <span class="menu-title">Ikuti Sistem</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- User menu (Bootstrap 5 Dropdown) -->
                <div class="app-navbar-item ms-1 ms-md-4 dropdown" id="kt_header_user_menu_toggle">
                    <div class="cursor-pointer symbol symbol-35px" data-bs-toggle="dropdown" aria-expanded="false">
                        @if($headerUser?->avatar_path)
                            <img src="{{ asset('storage/' . $headerUser->avatar_path) }}" alt="{{ $headerUser->name }}" />
                        @else
                            <div class="symbol-label bg-light-{{ $hRoleColor }} text-{{ $hRoleColor }} fw-bolder fs-7">
                                {{ $hInitials }}
                            </div>
                        @endif
                    </div>
                    <!-- User dropdown menu -->
                    <div class="dropdown-menu dropdown-menu-end menu menu-sub menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-4 fs-6 w-300px shadow-sm">
                        <!-- Profil Singkat Pengguna -->
                        <div class="menu-item px-3">
                            <div class="menu-content d-flex align-items-center px-3">
                                <div class="symbol symbol-45px symbol-circle me-4">
                                    <div class="symbol-label bg-light-{{ $hRoleColor }} text-{{ $hRoleColor }} fw-bolder fs-6">
                                        {{ $hInitials }}
                                    </div>
                                </div>
                                <div class="d-flex flex-column overflow-hidden">
                                    <div class="fw-bolder text-gray-900 fs-7 text-truncate">
                                        {{ $headerUser?->name ?? 'User' }}
                                    </div>
                                    <span class="text-muted fs-8 text-truncate mb-1">{{ $headerUser?->email ?? '-' }}</span>
                                    <div>
                                        <span class="badge badge-light-{{ $hRoleColor }} fw-bold fs-9 px-2 py-0">
                                            {{ $hRoleLabel }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="separator my-2"></div>

                        <!-- Menu Navigasi Pengguna -->
                        <div class="menu-item px-3 my-0">
                            <a href="{{ url('/') }}" class="menu-link px-3 py-2 rounded-2">
                                <i class="ki-duotone ki-element-11 fs-5 me-2 text-primary"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                                <span class="fs-8">Dashboard Utama</span>
                            </a>
                        </div>

                        <div class="menu-item px-3 my-0">
                            <a href="{{ url('/tickets') }}" class="menu-link px-3 py-2 rounded-2">
                                <i class="ki-duotone ki-tablet-text-down fs-5 me-2 text-info"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                                <span class="fs-8">Antrean Tiket</span>
                            </a>
                        </div>

                        <div class="separator my-2"></div>

                        <!-- Keluar / Logout -->
                        <div class="menu-item px-3">
                            <form action="{{ route('logout') }}" method="POST" id="logout-form">
                                @csrf
                                <button type="submit" class="menu-link px-3 py-2 rounded-2 text-danger w-100 bg-transparent border-0 text-start d-flex align-items-center">
                                    <i class="ki-duotone ki-entrance-right fs-4 me-2 text-danger"><span class="path1"></span><span class="path2"></span></i>
                                    <span class="fs-8 fw-bold">Keluar (Sign Out)</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
