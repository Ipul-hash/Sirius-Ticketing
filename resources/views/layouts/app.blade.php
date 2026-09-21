<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <title>@yield('title', 'SiriusTicketing - Enterprise Helpdesk')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Google Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />

    <!-- Metronic Global Stylesheets Bundle -->
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />

    <!-- Custom DataTables CSS -->
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />

    <!-- Metronic Theme Mode Setup (Light / Dark / System) -->
    <script>
        var defaultThemeMode = "light";
        var themeMode;
        if (document.documentElement) {
            if (document.documentElement.hasAttribute("data-bs-theme-mode")) {
                themeMode = document.documentElement.getAttribute("data-bs-theme-mode");
            } else {
                if (localStorage.getItem("data-bs-theme") !== null) {
                    themeMode = localStorage.getItem("data-bs-theme");
                } else {
                    themeMode = defaultThemeMode;
                }
            }
            if (themeMode === "system") {
                themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
            }
            document.documentElement.setAttribute("data-bs-theme", themeMode);
        }
    </script>

    @stack('styles')
</head>
<body id="kt_app_body" data-kt-app-layout="dark-sidebar" data-kt-app-header-fixed="true" data-kt-app-sidebar-enabled="true" data-kt-app-sidebar-fixed="true" data-kt-app-sidebar-hoverable="true" data-kt-app-sidebar-push-header="true" data-kt-app-sidebar-push-toolbar="true" data-kt-app-sidebar-push-footer="true" data-kt-app-toolbar-enabled="true" class="app-default">


    <!-- App Root -->
    <div class="d-flex flex-column flex-root app-root" id="kt_app_root">
        <!-- App Page -->
        <div class="app-page flex-column flex-column-fluid" id="kt_app_page">

            <!-- Header -->
            @include('layouts.partials._header')

            <!-- App Wrapper -->
            <div class="app-wrapper flex-column flex-row-fluid" id="kt_app_wrapper">

                <!-- Sidebar -->
                @include('layouts.partials._sidebar')

                <!-- Main Content Area -->
                <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
                    <div class="d-flex flex-column flex-column-fluid">

                        <!-- Toolbar -->
                        @include('layouts.partials._toolbar')

                        <!-- Page Body Content -->
                        <div id="kt_app_content" class="app-content flex-column-fluid">
                            <div id="kt_app_content_container" class="app-container container-fluid">
                                @yield('content')
                            </div>
                        </div>

                    </div>

                    <!-- Footer -->
                    @include('layouts.partials._footer')
                </div>

            </div>
        </div>
    </div>

    <!-- Metronic Global Javascript Bundle -->
    <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>

    <!-- DataTables Vendor Javascript -->
    <script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>

    <!-- Global AJAX CSRF Setup & Theme Mode Handler -->
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            }
        });

        function applyThemeMode(mode) {
            var targetMode = mode;
            if (mode === 'system') {
                targetMode = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
                localStorage.setItem('data-bs-theme-mode', 'system');
            } else {
                localStorage.setItem('data-bs-theme-mode', mode);
            }
            document.documentElement.setAttribute('data-bs-theme', targetMode);
            localStorage.setItem('data-bs-theme', targetMode);
        }

        $(document).on('click', '[data-kt-element="mode"]', function(e) {
            e.preventDefault();
            var mode = $(this).attr('data-kt-value');
            if (mode) {
                applyThemeMode(mode);
            }
        });

        // Sidebar Minimize Toggle Interactive Handler
        $(document).on('click', '#kt_app_sidebar_toggle', function(e) {
            e.preventDefault();
            var body = $('body');
            var btn = $(this);
            var isMinimized = body.attr('data-kt-app-sidebar-minimize') === 'on';

            if (isMinimized) {
                body.removeAttr('data-kt-app-sidebar-minimize');
                btn.removeClass('active');
                localStorage.setItem('sidebar_minimize_state', 'off');
            } else {
                body.attr('data-kt-app-sidebar-minimize', 'on');
                btn.addClass('active');
                localStorage.setItem('sidebar_minimize_state', 'on');
            }
        });

        // Restore sidebar state from localStorage
        if (localStorage.getItem('sidebar_minimize_state') === 'on') {
            $('body').attr('data-kt-app-sidebar-minimize', 'on');
            $('#kt_app_sidebar_toggle').addClass('active');
        }
    </script>

    @stack('scripts')


</body>
</html>
