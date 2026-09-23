<!DOCTYPE html>
<html lang="id">
<!--begin::Head-->
<head>
    <meta charset="utf-8" />
    <title>Masuk - SiriusTicketing Enterprise Helpdesk</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!--begin::Fonts-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <!--end::Fonts-->

    <!--begin::Global Stylesheets Bundle(mandatory for all pages)-->
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    <!--end::Global Stylesheets Bundle-->

    <!--begin::Theme mode setup on page load-->
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
    <!--end::Theme mode setup on page load-->
</head>
<!--end::Head-->

<!--begin::Body-->
<body id="kt_body" class="app-blank">
    <!--begin::Root-->
    <div class="d-flex flex-column flex-root" id="kt_app_root">
        <!--begin::Authentication - Sign-in -->
        <div class="d-flex flex-column flex-lg-row flex-column-fluid">
            
            <!--begin::Body (Form Side)-->
            <div class="d-flex flex-column flex-lg-row-fluid w-lg-50 p-6 p-lg-12 order-2 order-lg-1">
                <!--begin::Form Wrapper-->
                <div class="d-flex flex-center flex-column flex-lg-row-fluid">
                    <!--begin::Form Container-->
                    <div class="w-100 w-md-450px w-xl-500px mx-auto">

                        <!--begin::Mobile Brand Logo-->
                        <div class="text-center d-lg-none mb-8">
                            <a href="{{ url('/') }}">
                                <img alt="Logo Sirius" src="{{ asset('assets/media/logos/sirius-logo.svg') }}" class="h-45px" />
                            </a>
                        </div>
                        <!--end::Mobile Brand Logo-->

                        <!--begin::Heading-->
                        <div class="text-center mb-8">
                            <h1 class="text-gray-900 fw-bolder fs-2x mb-3">
                                Masuk ke Akun Anda
                            </h1>
                            <div class="text-gray-500 fw-semibold fs-6">
                                SiriusTicketing &bull; Enterprise IT Service Management
                            </div>
                        </div>
                        <!--end::Heading-->

                        <!--begin::Status Alert (e.g. Logout Notification)-->
                        @if(session('status'))
                            <div class="alert alert-success d-flex align-items-center p-4 mb-6 rounded-3">
                                <i class="ki-duotone ki-check-circle fs-2 text-success me-3">
                                    <span class="path1"></span><span class="path2"></span>
                                </i>
                                <div class="d-flex flex-column">
                                    <span class="fs-7 fw-semibold text-success">{{ session('status') }}</span>
                                </div>
                            </div>
                        @endif
                        <!--end::Status Alert-->

                        <!--begin::Error Alert-->
                        @if($errors->any())
                            <div class="alert alert-danger d-flex align-items-center p-4 mb-6 rounded-3">
                                <i class="ki-duotone ki-cross-circle fs-2 text-danger me-3">
                                    <span class="path1"></span><span class="path2"></span>
                                </i>
                                <div class="d-flex flex-column">
                                    @foreach($errors->all() as $err)
                                        <span class="fs-7 fw-semibold text-danger">{{ $err }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        <!--end::Error Alert-->

                        <!--begin::Form-->
                        <form class="form w-100" novalidate="novalidate" id="kt_sign_in_form" action="{{ route('login') }}" method="POST">
                            @csrf

                            <!--begin::Input group Email-->
                            <div class="fv-row mb-6">
                                <label class="form-label fs-7 fw-bold text-gray-800 required mb-1" for="email">Alamat Email</label>
                                <div class="position-relative">
                                    <input type="email" 
                                           placeholder="nama@perusahaan.com" 
                                           name="email" 
                                           id="email" 
                                           autocomplete="username" 
                                           class="form-control bg-transparent rounded-3 @error('email') is-invalid @enderror" 
                                           value="{{ old('email') }}" 
                                           required 
                                           autofocus />
                                    <span class="position-absolute top-50 translate-middle-y end-0 me-4 text-muted pointer-events-none">
                                        <i class="ki-duotone ki-sms fs-4"><span class="path1"></span><span class="path2"></span></i>
                                    </span>
                                </div>
                            </div>
                            <!--end::Input group Email-->

                            <!--begin::Input group Password-->
                            <div class="fv-row mb-6">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <label class="form-label fs-7 fw-bold text-gray-800 required mb-0" for="password">Kata Sandi</label>
                                </div>
                                <div class="position-relative">
                                    <input type="password" 
                                           placeholder="••••••••" 
                                           name="password" 
                                           id="password" 
                                           autocomplete="current-password" 
                                           class="form-control bg-transparent rounded-3 @error('password') is-invalid @enderror" 
                                           required />
                                    <span class="btn btn-sm btn-icon position-absolute top-50 translate-middle-y end-0 me-2" id="toggle_password" type="button" title="Lihat/Sembunyikan Sandi">
                                        <i class="ki-duotone ki-eye fs-4 text-gray-500" id="eye_icon">
                                            <span class="path1"></span><span class="path2"></span><span class="path3"></span>
                                        </i>
                                    </span>
                                </div>
                            </div>
                            <!--end::Input group Password-->

                            <!--begin::Remember Me Checkbox-->
                            <div class="d-flex align-items-center justify-content-between mb-8">
                                <label class="form-check form-check-custom form-check-solid form-check-sm">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" value="1" checked />
                                    <span class="form-check-label text-gray-700 fs-7 fw-semibold ms-2">
                                        Ingat sesi saya
                                    </span>
                                </label>
                            </div>
                            <!--end::Remember Me Checkbox-->

                            <!--begin::Submit button-->
                            <div class="d-grid mb-8">
                                <button type="submit" id="kt_sign_in_submit" class="btn btn-primary rounded-3 py-3 fw-bold shadow-xs">
                                    <span class="indicator-label fs-6">
                                        Masuk ke Sistem
                                        <i class="ki-duotone ki-arrow-right fs-4 ms-1"><span class="path1"></span><span class="path2"></span></i>
                                    </span>
                                </button>
                            </div>
                            <!--end::Submit button-->
                        </form>
                        <!--end::Form-->

                    </div>
                    <!--end::Form Container-->
                </div>
                <!--end::Form Wrapper-->

                <!--begin::Footer-->
                <div class="d-flex flex-center flex-wrap px-5 pt-6">
                    <div class="text-gray-500 fw-semibold fs-8 text-center">
                        2026 &copy; <a href="{{ url('/') }}" class="text-gray-800 text-hover-primary">SiriusTicketing</a> - Enterprise IT Service Management
                    </div>
                </div>
                <!--end::Footer-->
            </div>
            <!--end::Body-->

            <!--begin::Aside (Branded Metronic Illustration Side)-->
            <div class="d-none d-lg-flex flex-lg-row-fluid w-lg-50 bgi-size-cover bgi-position-center order-1 order-lg-2 bg-primary" 
                 style="background-image: url('{{ asset('assets/media/misc/auth-bg.png') }}'); background-repeat: no-repeat; background-size: cover;">
                <!--begin::Content-->
                <div class="d-flex flex-column flex-center py-10 py-lg-15 px-5 px-md-15 w-100">
                    <!--begin::Logo-->
                    <a href="{{ url('/') }}" class="mb-10">
                        <img alt="Logo Sirius" src="{{ asset('assets/media/logos/sirius-logo.svg') }}" class="h-50px" style="filter: brightness(0) invert(1);" />
                    </a>
                    <!--end::Logo-->

                    <!--begin::Image-->
                    <img class="mx-auto w-275px w-md-50 w-xl-450px mb-8 mb-lg-12 shadow-sm rounded-4" 
                         src="{{ asset('assets/media/misc/auth-screens.png') }}" 
                         alt="SiriusTicketing Workspace" />
                    <!--end::Image-->

                    <!--begin::Title-->
                    <h1 class="text-white fs-2qx fw-bolder text-center mb-4">
                        Cepat, Efisien & Terintegrasi
                    </h1>
                    <!--end::Title-->

                    <!--begin::Text-->
                    <div class="text-white text-opacity-80 fs-base text-center mw-450px">
                        Platform Enterprise IT Service Management (ITSM) multi-tenant dengan otomasi matriks SLA ITIL, pelacakan antrean tiket realtime, dan tata kelola aset CMDB terpusat.
                    </div>
                    <!--end::Text-->
                </div>
                <!--end::Content-->
            </div>
            <!--end::Aside-->

        </div>
        <!--end::Authentication - Sign-in-->
    </div>
    <!--end::Root-->

    <!--begin::Javascript-->
    <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>

    <!--begin::Interactive Script-->
    <script>
        // Toggle Password Visibility
        const toggleBtn = document.getElementById('toggle_password');
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eye_icon');

        if (toggleBtn && passwordInput) {
            toggleBtn.addEventListener('click', function() {
                const isPassword = passwordInput.getAttribute('type') === 'password';
                passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                
                if (isPassword) {
                    eyeIcon.className = 'ki-duotone ki-eye-slash fs-4 text-primary';
                } else {
                    eyeIcon.className = 'ki-duotone ki-eye fs-4 text-gray-500';
                }
            });
        }
    </script>
    <!--end::Interactive Script-->
</body>
<!--end::Body-->
</html>
