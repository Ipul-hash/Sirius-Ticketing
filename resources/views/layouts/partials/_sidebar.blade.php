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


    <!-- Menu Container -->
    <div class="app-sidebar-menu overflow-hidden flex-column-fluid">
        <div id="kt_app_sidebar_menu_wrapper" class="app-sidebar-wrapper hover-scroll-overlay-y my-5" data-kt-scroll="true" data-kt-scroll-activate="true" data-kt-scroll-height="auto" data-kt-scroll-dependencies="#kt_app_sidebar_logo, #kt_app_sidebar_footer" data-kt-scroll-wrappers="#kt_app_sidebar_menu" data-kt-scroll-offset="5px" data-kt-scroll-save-state="true">
            <div class="menu menu-column menu-rounded menu-sub-indention fw-semibold fs-6 px-3" id="#kt_app_sidebar_menu" data-kt-menu="true" data-kt-menu-expand="false">

                <!-- Dashboard -->
                <div class="menu-item">
                    <a class="menu-link {{ request()->is('/') || request()->is('dashboard*') ? 'active' : '' }}" href="{{ url('/') }}">
                        <span class="menu-icon">
                            <i class="ki-duotone ki-element-11 fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                        </span>
                        <span class="menu-title">Dashboard</span>
                    </a>
                </div>

                <!-- Section: Master Data -->
                <div class="menu-item pt-5">
                    <div class="menu-content">
                        <span class="menu-heading fw-bold text-uppercase fs-7">Master Data</span>
                    </div>
                </div>

                <!-- Master Perusahaan / Tenant (Superadmin) -->
                <div class="menu-item">
                    <a class="menu-link {{ request()->is('companies*') ? 'active' : '' }}" href="{{ url('/companies') }}">
                        <span class="menu-icon">
                            <i class="ki-duotone ki-shop fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                        </span>
                        <span class="menu-title">Perusahaan (Tenant)</span>
                        <span class="badge badge-light-primary fs-8 fw-bold ms-auto">Superadmin</span>
                    </a>
                </div>

                <!-- Master Departemen -->
                <div class="menu-item">
                    <a class="menu-link {{ request()->is('departments*') ? 'active' : '' }}" href="{{ url('/departments') }}">
                        <span class="menu-icon">
                            <i class="ki-duotone ki-briefcase fs-2"><span class="path1"></span><span class="path2"></span></i>
                        </span>
                        <span class="menu-title">Departemen</span>
                        <span class="badge badge-light-success fs-8 fw-bold ms-auto">CRUD</span>
                    </a>
                </div>

                <!-- Master Kategori Tiket -->
                <div class="menu-item">
                    <a class="menu-link {{ request()->is('categories*') ? 'active' : '' }}" href="{{ url('/categories') }}">
                        <span class="menu-icon">
                            <i class="ki-duotone ki-category fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                        </span>
                        <span class="menu-title">Kategori Tiket</span>
                        <span class="badge badge-light-primary fs-8 fw-bold ms-auto">Klasifikasi</span>
                    </a>
                </div>

                <!-- Master Aset Kantor (CMDB) -->
                <div class="menu-item">
                    <a class="menu-link {{ request()->is('company-assets*') || request()->is('inventaris*') ? 'active' : '' }}" href="{{ url('/company-assets') }}">
                        <span class="menu-icon">
                            <i class="ki-duotone ki-devices fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                        </span>
                        <span class="menu-title">Aset Kantor (CMDB)</span>
                        <span class="badge badge-light-info fs-8 fw-bold ms-auto">Inventaris</span>
                    </a>
                </div>

                <!-- Master Kebijakan SLA -->
                <div class="menu-item">
                    <a class="menu-link {{ request()->is('sla*') ? 'active' : '' }}" href="{{ url('/sla-policies') }}">
                        <span class="menu-icon">
                            <i class="ki-duotone ki-timer fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                        </span>
                        <span class="menu-title">Kebijakan SLA</span>
                        <span class="badge badge-light-warning fs-8 fw-bold ms-auto">Matriks</span>
                    </a>
                </div>

                <!-- Master Canned Responses -->
                <div class="menu-item">
                    <a class="menu-link {{ request()->is('canned*') ? 'active' : '' }}" href="{{ url('/canned-responses') }}">
                        <span class="menu-icon">
                            <i class="ki-duotone ki-message-text-2 fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                        </span>
                        <span class="menu-title">Canned Responses</span>
                        <span class="badge badge-light-success fs-8 fw-bold ms-auto">Balasan Cepat</span>
                    </a>
                </div>

                <!-- Master Pengguna & Staf -->
                <div class="menu-item">
                    <a class="menu-link {{ request()->is('users*') ? 'active' : '' }}" href="#!">
                        <span class="menu-icon">
                            <i class="ki-duotone ki-profile-user fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                        </span>
                        <span class="menu-title">Pengguna & Staf</span>
                    </a>
                </div>

                <!-- Section: Tiket & Helpdesk -->
                <div class="menu-item pt-5">
                    <div class="menu-content">
                        <span class="menu-heading fw-bold text-uppercase fs-7">Helpdesk & Tiket</span>
                    </div>
                </div>

                <div class="menu-item">
                    <a class="menu-link {{ request()->is('tickets*') ? 'active' : '' }}" href="{{ url('/tickets') }}">
                        <span class="menu-icon">
                            <i class="ki-duotone ki-tablet-text-down fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                        </span>
                        <span class="menu-title">Semua Tiket</span>
                        <span class="badge badge-light-primary fs-8 fw-bold ms-auto">Antrean</span>
                    </a>
                </div>

                <div class="menu-item">
                    <a class="menu-link {{ request()->is('approvals*') ? 'active' : '' }}" href="{{ route('approvals.index') }}">
                        <span class="menu-icon">
                            <i class="ki-duotone ki-verify fs-2"><span class="path1"></span><span class="path2"></span></i>
                        </span>
                        <span class="menu-title">Menunggu Approval</span>
                        <span class="badge badge-light-warning fs-8 fw-bold ms-auto">Otorisasi</span>
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>
