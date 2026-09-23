@inject('navigationService', 'App\Services\NavigationService')
@php
    $navSections = $navigationService->getNavigation(auth()->user());
    $currentUser = auth()->user();
    $currentRoleName = $currentUser?->role?->name ?? 'CompanyAdmin';
    $currentRoleVal = $currentUser?->role?->value ?? 'company_admin';
@endphp

<div id="kt_app_sidebar" class="app-sidebar flex-column" data-kt-drawer="true" data-kt-drawer-name="app-sidebar" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="225px" data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_app_sidebar_mobile_toggle">
    <!-- Logo -->
    <div class="app-sidebar-logo px-6" id="kt_app_sidebar_logo">
        <a href="{{ url('/') }}" class="d-flex align-items-center">
            <!-- Full Logo (Saat Sidebar Terbuka) -->
            <img alt="Logo Sirius" src="{{ asset('assets/media/logos/sirius-logo.svg') }}" class="h-32px app-sidebar-logo-default" />
            <!-- Icon S Logo (Saat Sidebar Diminimize / Collapse) -->
            <img alt="Logo S" src="{{ asset('assets/media/logos/sirius-icon.svg') }}" class="h-32px app-sidebar-logo-minimize" />
        </a>

        <!-- Toggle Collapse / Expand Sidebar -->
        <div id="kt_app_sidebar_toggle" class="app-sidebar-toggle btn btn-icon btn-shadow btn-sm btn-color-muted btn-active-color-primary body-bg h-30px w-30px position-absolute top-50 start-100 translate-middle rotate" data-kt-toggle="true" data-kt-toggle-state="active" data-kt-toggle-target="body" data-kt-toggle-name="app-sidebar-minimize" title="Kecilkan / Besarkan Menu">
            <i class="ki-duotone ki-double-left fs-2 rotate-180"><span class="path1"></span><span class="path2"></span></i>
        </div>
    </div>

    <!-- Menu Container Dinamis (RBAC-Driven) -->
    <div class="app-sidebar-menu overflow-hidden flex-column-fluid">
        <div id="kt_app_sidebar_menu_wrapper" class="app-sidebar-wrapper hover-scroll-overlay-y my-5" data-kt-scroll="true" data-kt-scroll-activate="true" data-kt-scroll-height="auto" data-kt-scroll-dependencies="#kt_app_sidebar_logo, #kt_app_sidebar_footer" data-kt-scroll-wrappers="#kt_app_sidebar_menu" data-kt-scroll-offset="5px" data-kt-scroll-save-state="true">
            <div class="menu menu-column menu-rounded menu-sub-indention fw-semibold fs-6 px-3" id="#kt_app_sidebar_menu" data-kt-menu="true" data-kt-menu-expand="false">

                @foreach($navSections as $section)
                    @if(!empty($section['heading']))
                        <!-- Section Heading -->
                        <div class="menu-item pt-5">
                            <div class="menu-content">
                                <span class="menu-heading fw-bold text-uppercase fs-7">{{ $section['heading'] }}</span>
                            </div>
                        </div>
                    @endif

                    @foreach($section['items'] as $item)
                        <div class="menu-item">
                            <a class="menu-link {{ $item['active'] ? 'active' : '' }}" href="{{ $item['url'] }}">
                                <span class="menu-icon">
                                    <i class="ki-duotone {{ $item['icon'] }} fs-2">
                                        <span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span>
                                    </i>
                                </span>
                                <span class="menu-title">{{ $item['title'] }}</span>
                                @if(!empty($item['badge']))
                                    <span class="badge {{ $item['badge_class'] ?? 'badge-light-primary' }} fs-8 fw-bold ms-auto">
                                        {{ $item['badge'] }}
                                    </span>
                                @endif
                            </a>
                        </div>
                    @endforeach
                @endforeach

            </div>
        </div>
    </div>

    <!-- Footer Sidebar: Indikator Peran Pengguna Aktif -->
    <div class="app-sidebar-footer flex-column-auto pt-2 pb-6 px-6" id="kt_app_sidebar_footer">
        <div class="d-flex align-items-center bg-light-primary rounded-3 p-3 border border-primary border-opacity-10">
            <div class="symbol symbol-30px symbol-circle bg-primary text-white fw-bold me-3 d-flex align-items-center justify-content-center fs-8">
                {{ strtoupper(substr($currentRoleVal, 0, 1)) }}
            </div>
            <div class="d-flex flex-column flex-grow-1 overflow-hidden">
                <span class="fs-9 text-muted fw-semibold">Role RBAC Aktif:</span>
                <span class="fs-8 fw-bolder text-gray-900 text-truncate">
                    {{ ucfirst(str_replace('_', ' ', $currentRoleVal)) }}
                </span>
            </div>
        </div>
    </div>
</div>
