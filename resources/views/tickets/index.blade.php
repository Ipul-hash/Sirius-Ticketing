@extends('layouts.app')

@section('title', 'Antrean Tiket & Helpdesk - SiriusTicketing')
@section('page_title', 'Antrean Tiket Helpdesk')

@section('toolbar_actions')
    <button type="button" class="btn btn-primary btn-sm rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#kt_modal_create_ticket">
        <i class="ki-duotone ki-plus fs-3"><span class="path1"></span><span class="path2"></span></i>
        Buat Tiket Baru
    </button>
@endsection

@section('content')
<!-- Hero Executive Dashboard Section -->
<div class="row g-5 g-xl-8 mb-6 align-items-stretch">
    <!-- Kolom Kiri: Layered Floating Card (Mengadopsi Desain Referensi) -->
    <div class="col-xl-4 col-lg-5">
        <div class="d-flex flex-column h-100">
            <!-- Background Container Biru / Hero -->
            <div class="card card-flush border-0 rounded-4 overflow-hidden position-relative pt-6 px-6 pb-16" 
                 style="background: linear-gradient(135deg, #1B84FF 0%, #0056B3 100%);">
                <!-- Header Atas: Title & Icon -->
                <div class="d-flex align-items-center justify-content-between text-white">
                    <span class="fw-bold fs-5 text-white ls-n1">Operasional Tiket</span>
                    <div class="symbol symbol-30px">
                        <div class="symbol-label bg-white bg-opacity-20 rounded-2">
                            <i class="ki-duotone ki-element-11 fs-3 text-white"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                        </div>
                    </div>
                </div>

                <!-- Hero Balance / Total Metric (Tepat Sesuai Referensi) -->
                <div class="text-center text-white my-5">
                    <div class="text-white text-opacity-75 fs-7 fw-semibold text-uppercase ls-1 mb-1">Total Tiket Aktif</div>
                    <div class="fs-2tx fw-bolder text-white lh-1">
                        {{ $stats['total_open'] }}
                        <span class="fs-4 fw-normal text-white text-opacity-75">Tiket</span>
                    </div>
                </div>
            </div>

            <!-- Floating White Card (Bertumpuk di Atas Biru dengan Shadow Kuat) -->
            <div class="card card-flush bg-body shadow-lg rounded-4 p-5 border-0 mt-n14 mx-3 position-relative z-index-1">
                <div class="d-flex flex-column gap-4">
                    <!-- Item 1: Tiket Baru (Open) -->
                    <div class="d-flex align-items-center justify-content-between p-2 rounded-3 hover-bg-light transition-all">
                        <div class="d-flex align-items-center">
                            <div class="symbol symbol-40px me-3">
                                <div class="symbol-label bg-light-primary rounded-3">
                                    <i class="ki-duotone ki-tablet-text-down fs-3 text-primary"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                                </div>
                            </div>
                            <div class="d-flex flex-column">
                                <span class="text-gray-900 fw-bold fs-6">Tiket Open</span>
                                <span class="text-muted fs-8">Baru masuk & aktif</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="fs-5 fw-bolder text-gray-900 me-2">{{ $stats['total_open'] }}</span>
                            <span class="text-success fs-7 fw-bolder" title="Aktif">↑</span>
                        </div>
                    </div>

                    <!-- Item 2: Menunggu Teknisi (Unassigned) -->
                    <div class="d-flex align-items-center justify-content-between p-2 rounded-3 hover-bg-light transition-all">
                        <div class="d-flex align-items-center">
                            <div class="symbol symbol-40px me-3">
                                <div class="symbol-label bg-light-warning rounded-3">
                                    <i class="ki-duotone ki-user-square fs-3 text-warning"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                                </div>
                            </div>
                            <div class="d-flex flex-column">
                                <span class="text-gray-900 fw-bold fs-6">Unassigned</span>
                                <span class="text-muted fs-8">Menunggu teknisi</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="fs-5 fw-bolder text-gray-900 me-2">{{ $stats['total_unassigned'] }}</span>
                            <span class="text-warning fs-7 fw-bolder" title="Butuh tindakan">↓</span>
                        </div>
                    </div>

                    <!-- Item 3: Menunggu Tindakan (Pending) -->
                    <div class="d-flex align-items-center justify-content-between p-2 rounded-3 hover-bg-light transition-all">
                        <div class="d-flex align-items-center">
                            <div class="symbol symbol-40px me-3">
                                <div class="symbol-label bg-light-info rounded-3">
                                    <i class="ki-duotone ki-timer fs-3 text-info"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                                </div>
                            </div>
                            <div class="d-flex flex-column">
                                <span class="text-gray-900 fw-bold fs-6">Pending Action</span>
                                <span class="text-muted fs-8">User / approval</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="fs-5 fw-bolder text-gray-900 me-2">{{ $stats['total_pending'] }}</span>
                            <span class="text-info fs-7 fw-bolder" title="Standby">↑</span>
                        </div>
                    </div>

                    <!-- Item 4: Overdue SLA (Kritis) -->
                    <div class="d-flex align-items-center justify-content-between p-2 rounded-3 hover-bg-light transition-all">
                        <div class="d-flex align-items-center">
                            <div class="symbol symbol-40px me-3">
                                <div class="symbol-label bg-light-danger rounded-3">
                                    <i class="ki-duotone ki-cross-circle fs-3 text-danger"><span class="path1"></span><span class="path2"></span></i>
                                </div>
                            </div>
                            <div class="d-flex flex-column">
                                <span class="text-gray-900 fw-bold fs-6">Overdue SLA</span>
                                <span class="text-muted fs-8">Melewati batas waktu</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="fs-5 fw-bolder text-danger me-2">{{ $stats['total_overdue'] }}</span>
                            <span class="text-danger fs-7 fw-bolder" title="Kritis">⚠️</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Command Center & Antrean Cepat (Seimbang dengan Card Kiri) -->
    <div class="col-xl-8 col-lg-7">
        <div class="d-flex flex-column h-100 justify-content-between gap-4">
            <!-- Top Section: 2 Balanced Cards (col-md-6 each) -->
            <div class="row g-4 flex-grow-1">
                <!-- Card 1: Kesehatan SLA & Sebaran Prioritas -->
                <div class="col-md-6 d-flex flex-column">
                    <div class="card card-flush bg-body shadow-sm rounded-4 p-5 h-100 border-0 d-flex flex-column justify-content-between">
                        <div>
                            <!-- Card 1 Header -->
                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-35px me-3">
                                        <div class="symbol-label bg-light-primary rounded-3">
                                            <i class="ki-duotone ki-chart-simple-3 fs-3 text-primary"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h5 class="card-title fw-bolder text-gray-900 fs-6 mb-0">Kesehatan SLA & Prioritas</h5>
                                        <span class="text-muted fs-8">Kepatuhan resolusi & sebaran</span>
                                    </div>
                                </div>
                                @php
                                    $slaRate = $dashboardMetrics['sla_compliance_rate'] ?? 100;
                                    $slaBadgeClass = $slaRate >= 90 ? 'badge-light-success text-success' : ($slaRate >= 75 ? 'badge-light-warning text-warning' : 'badge-light-danger text-danger');
                                @endphp
                                <span class="badge {{ $slaBadgeClass }} fw-bolder fs-8 px-2 py-1">
                                    {{ $slaRate }}% On-Track
                                </span>
                            </div>

                            <!-- SLA Progress Metric -->
                            <div class="p-3 rounded-3 bg-light mb-4 border border-secondary border-opacity-10">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-gray-700 fs-8 fw-semibold">Tingkat Kepatuhan SLA</span>
                                    <span class="fs-7 fw-bolder text-gray-900">{{ $slaRate }}%</span>
                                </div>
                                <div class="progress h-8px bg-body rounded-pill overflow-hidden shadow-xs">
                                    <div class="progress-bar {{ $slaRate >= 90 ? 'bg-success' : ($slaRate >= 75 ? 'bg-warning' : 'bg-danger') }}" 
                                         role="progressbar" 
                                         style="width: {{ $slaRate }}%" 
                                         aria-valuenow="{{ $slaRate }}" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between mt-2 text-muted fs-8">
                                    <span>Target Standar: &ge;95%</span>
                                    <span><strong>{{ $stats['total_overdue'] }}</strong> Overdue / <strong>{{ $dashboardMetrics['active_tickets_count'] ?? 0 }}</strong> Aktif</span>
                                </div>
                            </div>
                        </div>

                        <!-- 4-Grid Priority Distribution Pills -->
                        <div>
                            <span class="fs-8 fw-bold text-gray-600 text-uppercase ls-1 d-block mb-2">Sebaran Prioritas Tiket Aktif</span>
                            <div class="row g-2">
                                <!-- Urgent -->
                                <div class="col-6">
                                    <a href="{{ url('/tickets?priority=urgent') }}" 
                                       class="d-flex align-items-center justify-content-between p-2 rounded-3 bg-light-danger border border-danger border-dashed text-decoration-none hover-elevate-up transition-all"
                                       title="Filter Tiket Urgent">
                                        <div class="d-flex align-items-center">
                                            <span class="bullet bullet-dot bg-danger me-2"></span>
                                            <span class="fs-8 fw-bold text-danger">Urgent</span>
                                        </div>
                                        <span class="fs-7 fw-bolder text-danger">{{ $dashboardMetrics['urgent_count'] ?? 0 }}</span>
                                    </a>
                                </div>

                                <!-- High -->
                                <div class="col-6">
                                    <a href="{{ url('/tickets?priority=high') }}" 
                                       class="d-flex align-items-center justify-content-between p-2 rounded-3 bg-light-warning border border-warning border-dashed text-decoration-none hover-elevate-up transition-all"
                                       title="Filter Tiket Tinggi">
                                        <div class="d-flex align-items-center">
                                            <span class="bullet bullet-dot bg-warning me-2"></span>
                                            <span class="fs-8 fw-bold text-warning">Tinggi</span>
                                        </div>
                                        <span class="fs-7 fw-bolder text-warning">{{ $dashboardMetrics['high_count'] ?? 0 }}</span>
                                    </a>
                                </div>

                                <!-- Medium -->
                                <div class="col-6">
                                    <a href="{{ url('/tickets?priority=medium') }}" 
                                       class="d-flex align-items-center justify-content-between p-2 rounded-3 bg-light-primary border border-primary border-dashed text-decoration-none hover-elevate-up transition-all"
                                       title="Filter Tiket Sedang">
                                        <div class="d-flex align-items-center">
                                            <span class="bullet bullet-dot bg-primary me-2"></span>
                                            <span class="fs-8 fw-bold text-primary">Sedang</span>
                                        </div>
                                        <span class="fs-7 fw-bolder text-primary">{{ $dashboardMetrics['medium_count'] ?? 0 }}</span>
                                    </a>
                                </div>

                                <!-- Low -->
                                <div class="col-6">
                                    <a href="{{ url('/tickets?priority=low') }}" 
                                       class="d-flex align-items-center justify-content-between p-2 rounded-3 bg-light-secondary border border-gray-300 border-dashed text-decoration-none hover-elevate-up transition-all"
                                       title="Filter Tiket Rendah">
                                        <div class="d-flex align-items-center">
                                            <span class="bullet bullet-dot bg-gray-500 me-2"></span>
                                            <span class="fs-8 fw-bold text-gray-700">Rendah</span>
                                        </div>
                                        <span class="fs-7 fw-bolder text-gray-700">{{ $dashboardMetrics['low_count'] ?? 0 }}</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Spotlight Tiket Kritis & Beban Kerja Tim -->
                <div class="col-md-6 d-flex flex-column">
                    <div class="card card-flush bg-body shadow-sm rounded-4 p-5 h-100 border-0 d-flex flex-column justify-content-between">
                        <div>
                            <!-- Card 2 Header -->
                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-35px me-3">
                                        <div class="symbol-label bg-light-danger rounded-3">
                                            <i class="ki-duotone ki-security-user fs-3 text-danger"><span class="path1"></span><span class="path2"></span></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h5 class="card-title fw-bolder text-gray-900 fs-6 mb-0">Spotlight Tiket Kritis</h5>
                                        <span class="text-muted fs-8">Perhatian & beban tim teknisi</span>
                                    </div>
                                </div>
                                <span class="badge badge-light-secondary text-gray-700 fw-bold fs-8 px-2 py-1">
                                    Live Queue
                                </span>
                            </div>

                            <!-- Spotlight Tiket Paling Urgent / Overdue -->
                            @if(!empty($dashboardMetrics['critical_ticket']))
                                @php
                                    $crit = $dashboardMetrics['critical_ticket'];
                                    $isBreached = $crit->is_sla_breached || ($crit->resolution_due_at && $crit->resolution_due_at->isPast());
                                @endphp
                                <div class="p-3 rounded-3 bg-light-danger border border-danger border-opacity-25 mb-4 position-relative">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge {{ $isBreached ? 'badge-danger' : 'badge-light-danger text-danger' }} fw-bold fs-9 px-2 py-1">
                                                {{ $isBreached ? 'SLA Overdue' : 'Prioritas Urgent' }}
                                            </span>
                                            <span class="fw-bold text-gray-900 fs-8">{{ $crit->ticket_number }}</span>
                                        </div>
                                        @if(!auth()->user()?->isRequester())
                                            @if(!$crit->assigned_to)
                                                @if(auth()->user()?->isCompanyAdmin() || auth()->user()?->isSuperadmin())
                                                    <button type="button" class="btn btn-xs btn-warning py-1 px-2 fw-bold fs-9 rounded-2 btn-quick-assign"
                                                            data-id="{{ $crit->id }}"
                                                            data-number="{{ $crit->ticket_number }}"
                                                            data-subject="{{ $crit->subject }}"
                                                            data-company="{{ $crit->company_id }}"
                                                            title="Tugaskan Teknisi">
                                                        Tugaskan
                                                    </button>
                                                @elseif(auth()->user()?->isAgent())
                                                    <button type="button" class="btn btn-xs btn-success py-1 px-2 fw-bold fs-9 rounded-2 btn-claim-ticket"
                                                            data-id="{{ $crit->id }}"
                                                            data-number="{{ $crit->ticket_number }}"
                                                            data-subject="{{ $crit->subject }}"
                                                            data-status="{{ $crit->status->value ?? $crit->status }}"
                                                            data-approval-status="{{ $crit->approval_status->value ?? $crit->approval_status }}"
                                                            title="Ambil Tiket">
                                                        Ambil Tiket
                                                    </button>
                                                @endif
                                            @else
                                                <button type="button" class="btn btn-xs btn-danger py-1 px-2 fw-bold fs-9 rounded-2 btn-quick-status"
                                                        data-id="{{ $crit->id }}"
                                                        data-number="{{ $crit->ticket_number }}"
                                                        data-subject="{{ $crit->subject }}"
                                                        data-status="{{ $crit->status->value ?? $crit->status }}"
                                                        title="Perbarui Status">
                                                    Tangani
                                                </button>
                                            @endif
                                        @endif
                                    </div>
                                    <a href="{{ url('/tickets/' . $crit->id) }}" class="text-gray-900 fw-bold fs-7 text-truncate mb-1 text-hover-primary text-decoration-none d-block" title="{{ $crit->subject }}">
                                        {{ Str::limit($crit->subject, 38) }}
                                    </a>
                                    <div class="d-flex align-items-center justify-content-between fs-8 text-gray-600">
                                        <span class="text-truncate" style="max-width: 130px;">
                                            <i class="ki-duotone ki-user fs-8 me-1"><span class="path1"></span><span class="path2"></span></i>
                                            {{ $crit->requester?->name ?? 'Requester' }}
                                        </span>
                                        <span class="text-danger fw-semibold">
                                            {{ $crit->resolution_due_at ? $crit->resolution_due_at->diffForHumans() : 'Segera' }}
                                        </span>
                                    </div>
                                </div>
                            @else
                                <div class="p-3 rounded-3 bg-light-success border border-success border-opacity-25 mb-4 d-flex align-items-center gap-3">
                                    <i class="ki-duotone ki-shield-tick text-success fs-2x"><span class="path1"></span><span class="path2"></span></i>
                                    <div>
                                        <div class="text-gray-900 fw-bold fs-8">Antrean Berjalan Lancar</div>
                                        <div class="text-muted fs-9">Tidak ada tiket kritis atau overdue saat ini.</div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Beban Kerja Teknisi (Team Workload) -->
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="fs-8 fw-bold text-gray-600 text-uppercase ls-1">Utilisasi Teknisi</span>
                                <span class="fs-9 text-muted">{{ count($dashboardMetrics['agent_workloads'] ?? []) }} Teknisi Tersedia</span>
                            </div>
                            <div class="d-flex flex-column gap-2">
                                @forelse($dashboardMetrics['agent_workloads'] ?? [] as $agent)
                                    <div class="d-flex align-items-center justify-content-between p-2 rounded-3 bg-light hover-bg-light transition-all border border-secondary border-opacity-10">
                                        <div class="d-flex align-items-center">
                                            <div class="symbol symbol-25px me-2">
                                                <div class="symbol-label bg-light-primary text-primary fw-bold fs-8">
                                                    {{ strtoupper(substr($agent->name, 0, 1)) }}
                                                </div>
                                            </div>
                                            <div class="d-flex flex-column">
                                                <span class="text-gray-900 fw-bold fs-8 text-truncate" style="max-width: 120px;" title="{{ $agent->name }}">{{ $agent->name }}</span>
                                                <span class="text-muted fs-9">{{ $agent->job_title ?? ucfirst($agent->role->value) }}</span>
                                            </div>
                                        </div>
                                        <span class="badge badge-light-primary fw-bold fs-9 px-2 py-1">
                                            {{ $agent->assigned_tickets_count }} Tiket Aktif
                                        </span>
                                    </div>
                                @empty
                                    <div class="text-muted fs-9 text-center py-2">Belum ada data beban kerja teknisi</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Section: Quick Antrean Tabs Pill Modern -->
            <div class="card card-flush bg-body shadow-sm rounded-4 p-3 border-0">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div class="d-flex align-items-center">
                        <span class="fs-8 fw-bold text-gray-600 text-uppercase ls-1 me-2">Antrean:</span>
                    </div>
                    <div class="d-flex flex-wrap gap-2 flex-grow-1 justify-content-start justify-content-md-end">
                        <!-- Tab Semua -->
                        <a href="{{ url('/tickets?tab=all') }}" 
                           class="btn btn-sm rounded-3 py-2 px-3 d-flex align-items-center {{ $activeTab === 'all' ? 'btn-primary shadow-sm' : 'btn-light text-gray-700' }}">
                            <i class="ki-duotone ki-element-11 fs-6 me-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                            Semua
                            <span class="badge {{ $activeTab === 'all' ? 'bg-white text-primary' : 'badge-light-primary' }} ms-2 fw-bold fs-9">{{ $stats['total_all'] }}</span>
                        </a>

                        <!-- Tab Unassigned -->
                        <a href="{{ url('/tickets?tab=unassigned') }}" 
                           class="btn btn-sm rounded-3 py-2 px-3 d-flex align-items-center {{ $activeTab === 'unassigned' ? 'btn-warning text-white shadow-sm' : 'btn-light text-gray-700' }}">
                            <i class="ki-duotone ki-user-square fs-6 me-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                            Unassigned
                            <span class="badge {{ $activeTab === 'unassigned' ? 'bg-white text-warning' : 'badge-light-warning' }} ms-2 fw-bold fs-9">{{ $stats['total_unassigned'] }}</span>
                        </a>

                        <!-- Tab Tiket Saya -->
                        <a href="{{ url('/tickets?tab=my_tickets') }}" 
                           class="btn btn-sm rounded-3 py-2 px-3 d-flex align-items-center {{ $activeTab === 'my_tickets' ? 'btn-info shadow-sm' : 'btn-light text-gray-700' }}">
                            <i class="ki-duotone ki-profile-user fs-6 me-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                            Tiket Saya
                        </a>

                        <!-- Tab Menunggu -->
                        <a href="{{ url('/tickets?tab=pending') }}" 
                           class="btn btn-sm rounded-3 py-2 px-3 d-flex align-items-center {{ $activeTab === 'pending' ? 'btn-info shadow-sm' : 'btn-light text-gray-700' }}">
                            <i class="ki-duotone ki-timer fs-6 me-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                            Pending
                            <span class="badge {{ $activeTab === 'pending' ? 'bg-white text-info' : 'badge-light-info' }} ms-2 fw-bold fs-9">{{ $stats['total_pending'] }}</span>
                        </a>

                        <!-- Tab Overdue -->
                        <a href="{{ url('/tickets?tab=overdue') }}" 
                           class="btn btn-sm rounded-3 py-2 px-3 d-flex align-items-center {{ $activeTab === 'overdue' ? 'btn-danger shadow-sm' : 'btn-light text-gray-700' }}">
                            <i class="ki-duotone ki-cross-circle fs-6 me-1"><span class="path1"></span><span class="path2"></span></i>
                            Overdue SLA
                            <span class="badge {{ $activeTab === 'overdue' ? 'bg-white text-danger' : 'badge-danger' }} ms-2 fw-bold fs-9">{{ $stats['total_overdue'] }}</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Table Card: Desain Lebih Modern, Bersih & Tidak Kaku -->
<div class="card card-flush bg-body shadow-sm rounded-4 border-0">
    <!-- Header & Filter Toolbar -->
    <div class="card-header border-0 pt-6 px-6">
        <div class="card-title">
            <form method="GET" action="{{ url('/tickets') }}" class="d-flex align-items-center position-relative my-1">
                <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-4 text-gray-500">
                    <span class="path1"></span><span class="path2"></span>
                </i>
                <input type="text" name="search" value="{{ request('search') }}" 
                       class="form-control form-control-solid rounded-3 w-250px ps-12 fs-7" placeholder="Cari nomor atau subjek tiket..." />
                @if(request('tab')) <input type="hidden" name="tab" value="{{ request('tab') }}" /> @endif
                @if(request('company_id')) <input type="hidden" name="company_id" value="{{ request('company_id') }}" /> @endif
                @if(request('department_id')) <input type="hidden" name="department_id" value="{{ request('department_id') }}" /> @endif
                @if(request('category_id')) <input type="hidden" name="category_id" value="{{ request('category_id') }}" /> @endif
                @if(request('priority')) <input type="hidden" name="priority" value="{{ request('priority') }}" /> @endif
                @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}" /> @endif
            </form>
        </div>

        <div class="card-toolbar">
            <form method="GET" action="{{ url('/tickets') }}" id="ticket_filter_form" class="d-flex flex-wrap align-items-center gap-2">
                @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}" /> @endif
                @if(request('tab')) <input type="hidden" name="tab" value="{{ request('tab') }}" /> @endif

                @if(auth()->user()->isSuperadmin())
                <!-- Filter Perusahaan -->
                <div class="w-160px">
                    <select name="company_id" id="filter_ticket_company" class="form-select form-select-solid form-select-sm rounded-3 fs-8" onchange="this.form.submit()">
                        <option value="">Semua Perusahaan</option>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>
                                {{ $company->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                <!-- Filter Departemen -->
                <div class="w-160px">
                    <select name="department_id" id="filter_ticket_department" class="form-select form-select-solid form-select-sm rounded-3 fs-8" onchange="this.form.submit()">
                        <option value="">Semua Departemen</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" data-company="{{ $dept->company_id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Prioritas -->
                <div class="w-130px">
                    <select name="priority" class="form-select form-select-solid form-select-sm rounded-3 fs-8" onchange="this.form.submit()">
                        <option value="">Semua Prioritas</option>
                        <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                        <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                    </select>
                </div>

                <!-- Filter Status -->
                <div class="w-130px">
                    <select name="status" class="form-select form-select-solid form-select-sm rounded-3 fs-8" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="pending_user" {{ request('status') == 'pending_user' ? 'selected' : '' }}>Pending User</option>
                        <option value="pending_approval" {{ request('status') == 'pending_approval' ? 'selected' : '' }}>Pending Approval</option>
                        <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                        <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>

                @if(request()->hasAny(['search', 'company_id', 'department_id', 'category_id', 'priority', 'status']) || request('tab') != 'all')
                    <a href="{{ url('/tickets') }}" class="btn btn-icon btn-light-danger btn-sm rounded-3" data-bs-toggle="tooltip" title="Reset Filter">
                        <i class="ki-duotone ki-cross fs-3"><span class="path1"></span><span class="path2"></span></i>
                    </a>
                @endif
            </form>
        </div>
    </div>

    <!-- Table Body -->
    <div class="card-body pt-2 px-6">
        <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-6 gy-4">
                <thead>
                    <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                        <th class="min-w-130px">Nomor Tiket</th>
                        <th class="min-w-260px">Subjek & Masalah</th>
                        <th class="min-w-160px">Pelapor</th>
                        <th class="min-w-160px">Teknisi</th>
                        <th class="min-w-110px">Prioritas</th>
                        <th class="min-w-120px">Status</th>
                        <th class="min-w-140px">Target SLA</th>
                        <th class="text-end min-w-100px pe-2">Aksi</th>
                    </tr>
                </thead>
                <tbody class="fw-semibold text-gray-600">
                    @forelse($tickets as $ticket)
                        @php
                            $sVal = $ticket->status->value ?? $ticket->status;
                            $pVal = $ticket->priority->value ?? $ticket->priority;
                            $approvalVal = $ticket->approval_status->value ?? $ticket->approval_status ?? 'none';
                        @endphp
                        <tr class="hover-bg-light transition-all">
                            <!-- Nomor Tiket -->
                            <td>
                                <div class="d-flex align-items-center">
                                    <a href="{{ url('/tickets/' . $ticket->id) }}" class="badge badge-light-primary fw-bold fs-7 px-3 py-2 cursor-pointer shadow-xs rounded-2 text-decoration-none text-hover-primary">
                                        {{ $ticket->ticket_number }}
                                    </a>
                                </div>
                                <span class="text-muted fs-8 d-block mt-1">{{ $ticket->created_at ? $ticket->created_at->diffForHumans() : '-' }}</span>
                            </td>

                            <!-- Subjek & Kategori Masalah -->
                            <td>
                                <div class="d-flex flex-column">
                                    <a href="{{ url('/tickets/' . $ticket->id) }}" class="text-gray-900 fw-bold text-hover-primary fs-6 mb-1 text-decoration-none">
                                        {{ $ticket->subject }}
                                    </a>
                                    <div class="d-flex flex-wrap gap-1 align-items-center">
                                        <span class="badge badge-light-secondary text-gray-700 fs-8 py-1 px-2 rounded-2">
                                            <i class="ki-duotone ki-category fs-9 me-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                                            {{ $ticket->category->name ?? 'Kategori' }}
                                        </span>
                                        <span class="badge badge-light-info text-gray-700 fs-8 py-1 px-2 rounded-2">
                                            {{ $ticket->department->name ?? 'Divisi' }}
                                        </span>
                                        @if($ticket->asset)
                                            <span class="badge badge-light-warning text-gray-800 fs-8 py-1 px-2 rounded-2" title="Terkait Aset Inventaris">
                                                <i class="ki-duotone ki-devices fs-9 me-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                                                {{ $ticket->asset->asset_tag }}
                                            </span>
                                        @endif
                                        @if($ticket->approval_status && $ticket->approval_status->value === 'pending')
                                            <span class="badge badge-warning text-white fs-8 py-1 px-2 rounded-2" title="Menunggu persetujuan atasan">
                                                🔒 Approval
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Pelapor -->
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-32px symbol-circle me-2">
                                        <div class="symbol-label bg-light-primary text-primary fw-bold fs-8">
                                            {{ strtoupper(substr($ticket->requester->name ?? 'U', 0, 1)) }}
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="text-gray-800 fw-semibold fs-7">{{ $ticket->requester->name ?? 'Unknown' }}</span>
                                        <span class="text-muted fs-8">{{ $ticket->requester->email ?? '-' }}</span>
                                    </div>
                                </div>
                            </td>

                            <!-- Teknisi Ditugaskan -->
                            <td>
                                @if($ticket->assignedAgent)
                                    <div class="d-flex align-items-center">
                                        <div class="symbol symbol-32px symbol-circle me-2">
                                            <div class="symbol-label bg-light-success text-success fw-bold fs-8">
                                                {{ strtoupper(substr($ticket->assignedAgent->name, 0, 1)) }}
                                            </div>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <span class="text-gray-900 fw-bold fs-7">{{ $ticket->assignedAgent->name }}</span>
                                            <span class="text-muted fs-8">{{ $ticket->assignedAgent->job_title ?? 'Teknisi' }}</span>
                                        </div>
                                    </div>
                                @else
                                    @if(auth()->user()?->isCompanyAdmin() || auth()->user()?->isSuperadmin())
                                        <button type="button" class="btn btn-light-warning btn-sm fs-8 py-1 px-3 rounded-2 btn-quick-assign" 
                                                data-id="{{ $ticket->id }}" 
                                                data-number="{{ $ticket->ticket_number }}" 
                                                data-subject="{{ $ticket->subject }}" 
                                                data-company="{{ $ticket->company_id }}"
                                                data-status="{{ $sVal }}"
                                                data-approval-status="{{ $approvalVal }}">
                                            <i class="ki-duotone ki-plus fs-7 me-1"><span class="path1"></span><span class="path2"></span></i>
                                            Tugaskan
                                        </button>
                                    @elseif(auth()->user()?->isAgent())
                                        <button type="button" class="btn btn-light-success btn-sm fs-8 py-1 px-3 rounded-2 btn-claim-ticket" 
                                                data-id="{{ $ticket->id }}" 
                                                data-number="{{ $ticket->ticket_number }}" 
                                                data-subject="{{ $ticket->subject }}" 
                                                data-company="{{ $ticket->company_id }}"
                                                data-status="{{ $sVal }}"
                                                data-approval-status="{{ $approvalVal }}">
                                            <i class="ki-duotone ki-check-circle fs-7 me-1 text-success"><span class="path1"></span><span class="path2"></span></i>
                                            Ambil Tiket
                                        </button>
                                    @else
                                        <span class="badge badge-light-warning fs-8">Menunggu Teknisi</span>
                                    @endif
                                @endif
                            </td>

                            <!-- Prioritas -->
                            <td>
                                @php
                                    $pVal = $ticket->priority->value ?? $ticket->priority;
                                @endphp
                                @if($pVal === 'urgent')
                                    <span class="badge badge-light-danger fw-bold fs-8 px-3 py-2 rounded-2">
                                        <span class="bullet bullet-dot bg-danger me-1"></span>Urgent
                                    </span>
                                @elseif($pVal === 'high')
                                    <span class="badge badge-light-warning fw-bold fs-8 px-3 py-2 rounded-2">
                                        <span class="bullet bullet-dot bg-warning me-1"></span>High
                                    </span>
                                @elseif($pVal === 'medium')
                                    <span class="badge badge-light-primary fw-bold fs-8 px-3 py-2 rounded-2">
                                        <span class="bullet bullet-dot bg-primary me-1"></span>Medium
                                    </span>
                                @else
                                    <span class="badge badge-light-secondary text-gray-700 fw-bold fs-8 px-3 py-2 rounded-2">
                                        <span class="bullet bullet-dot bg-gray-500 me-1"></span>Low
                                    </span>
                                @endif
                            </td>

                            <!-- Status -->
                            <td>
                                @php
                                    $sVal = $ticket->status->value ?? $ticket->status;
                                @endphp
                                @if($sVal === 'open')
                                    <span class="badge badge-light-primary fw-bold fs-8 px-3 py-2 rounded-2">Open</span>
                                @elseif($sVal === 'in_progress')
                                    <span class="badge badge-light-info fw-bold fs-8 px-3 py-2 rounded-2">In Progress</span>
                                @elseif($sVal === 'pending_user')
                                    <span class="badge badge-light-warning fw-bold fs-8 px-3 py-2 rounded-2">Pending User</span>
                                @elseif($sVal === 'pending_approval')
                                    <span class="badge badge-light-warning fw-bold fs-8 px-3 py-2 rounded-2">Pending Approval</span>
                                @elseif($sVal === 'resolved')
                                    <span class="badge badge-light-success fw-bold fs-8 px-3 py-2 rounded-2">Resolved</span>
                                @elseif($sVal === 'closed')
                                    <span class="badge badge-light-dark fw-bold fs-8 px-3 py-2 rounded-2">Closed</span>
                                @else
                                    <span class="badge badge-light fw-bold fs-8 px-3 py-2 rounded-2">{{ ucfirst($sVal) }}</span>
                                @endif
                            </td>

                            <!-- SLA Target -->
                            <td>
                                @if($ticket->resolved_at)
                                    <span class="text-success fs-8 fw-bold">
                                        <i class="ki-duotone ki-check-circle fs-8 text-success me-1"><span class="path1"></span><span class="path2"></span></i>
                                        Selesai
                                    </span>
                                @elseif($ticket->is_sla_breached || ($ticket->resolution_due_at && $ticket->resolution_due_at->isPast()))
                                    <span class="badge badge-danger fs-8 fw-bold px-2 py-1 rounded-2" title="Melewati batas SLA!">
                                        ⚠️ Breached
                                    </span>
                                @elseif($ticket->resolution_due_at)
                                    <div class="d-flex flex-column">
                                        <span class="text-gray-800 fs-8 fw-bold">Due {{ $ticket->resolution_due_at->format('H:i, d M') }}</span>
                                        <span class="text-muted fs-8">{{ $ticket->resolution_due_at->diffForHumans() }}</span>
                                    </div>
                                @else
                                    <span class="text-muted fs-8">-</span>
                                @endif
                            </td>

                            <!-- Aksi Cepat -->
                            <td class="text-end pe-2">
                                <div class="d-flex justify-content-end gap-1">
                                    <!-- View Detail -->
                                    <a href="{{ url('/tickets/' . $ticket->id) }}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm rounded-2" title="Lihat Detail & Chat">
                                        <i class="ki-duotone ki-eye fs-4"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                                    </a>

                                    @if(auth()->user()?->isCompanyAdmin() || auth()->user()?->isSuperadmin())
                                        <!-- Quick Assign -->
                                        <button type="button" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm rounded-2 btn-quick-assign" 
                                                data-id="{{ $ticket->id }}" 
                                                data-number="{{ $ticket->ticket_number }}" 
                                                data-subject="{{ $ticket->subject }}" 
                                                data-company="{{ $ticket->company_id }}"
                                                data-status="{{ $sVal }}"
                                                data-approval-status="{{ $approvalVal }}"
                                                title="Tugaskan Teknisi">
                                            <i class="ki-duotone ki-user-square fs-4"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                                        </button>
                                    @elseif(auth()->user()?->isAgent() && !$ticket->assigned_to)
                                        <!-- Ambil Tiket -->
                                        <button type="button" class="btn btn-icon btn-bg-light btn-active-color-success btn-sm rounded-2 btn-claim-ticket" 
                                                data-id="{{ $ticket->id }}" 
                                                data-number="{{ $ticket->ticket_number }}" 
                                                data-subject="{{ $ticket->subject }}" 
                                                data-company="{{ $ticket->company_id }}"
                                                data-status="{{ $sVal }}"
                                                data-approval-status="{{ $approvalVal }}"
                                                title="Ambil Tiket">
                                            <i class="ki-duotone ki-check-circle fs-4 text-success"><span class="path1"></span><span class="path2"></span></i>
                                        </button>
                                    @endif

                                    @if(!auth()->user()?->isRequester())
                                        <!-- Quick Status -->
                                        <button type="button" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm rounded-2 btn-quick-status" 
                                                data-id="{{ $ticket->id }}" 
                                                data-number="{{ $ticket->ticket_number }}" 
                                                data-subject="{{ $ticket->subject }}" 
                                                data-status="{{ $sVal }}"
                                                data-approval-status="{{ $approvalVal }}"
                                                title="Ubah Status">
                                            <i class="ki-duotone ki-arrows-circle fs-4"><span class="path1"></span><span class="path2"></span></i>
                                        </button>
                                    @endif

                                    @if(auth()->user()?->isCompanyAdmin() || auth()->user()?->isSuperadmin())
                                        <!-- Delete Ticket -->
                                        <button type="button" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm rounded-2 btn-delete-ticket" 
                                                data-id="{{ $ticket->id }}" 
                                                data-number="{{ $ticket->ticket_number }}" 
                                                title="Hapus Tiket">
                                            <i class="ki-duotone ki-trash fs-4"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-12">
                                <div class="d-flex flex-column align-items-center">
                                    <div class="symbol symbol-60px mb-4">
                                        <div class="symbol-label bg-light-primary rounded-circle">
                                            <i class="ki-duotone ki-tablet-text-down fs-2x text-primary"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                                        </div>
                                    </div>
                                    <span class="text-gray-800 fs-5 fw-bold mb-1">Tidak Ada Tiket Dalam Antrean Ini</span>
                                    <span class="text-gray-400 fs-7 mb-4">Seluruh tiket tertangani atau belum ada keluhan yang didaftarkan.</span>
                                    <button type="button" class="btn btn-primary btn-sm rounded-3" data-bs-toggle="modal" data-bs-target="#kt_modal_create_ticket">
                                        <i class="ki-duotone ki-plus fs-4"><span class="path1"></span><span class="path2"></span></i>
                                        Buat Tiket Baru
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center flex-wrap pt-5 pb-2">
            <div class="fs-7 fw-semibold text-gray-700">
                Menampilkan {{ $tickets->firstItem() ?? 0 }} s/d {{ $tickets->lastItem() ?? 0 }} dari {{ $tickets->total() }} tiket
            </div>
            <div>
                {{ $tickets->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
@include('tickets._modal_create')
@if(!auth()->user()?->isRequester())
    @if(auth()->user()?->isCompanyAdmin() || auth()->user()?->isSuperadmin())
        @include('tickets._modal_assign')
    @endif
    @include('tickets._modal_status')
@endif

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Dynamic Cascading in Create Modal
    const createCompany = document.getElementById('create_ticket_company_id');
    const createCategory = document.getElementById('create_ticket_category_id');
    const createDept = document.getElementById('create_ticket_department_id');
    const createPriority = document.getElementById('create_ticket_priority');
    const createRequester = document.getElementById('create_ticket_requester_id');
    const createAsset = document.getElementById('create_ticket_asset_id');
    const createAgent = document.getElementById('create_ticket_assigned_to');
    const approvalHint = document.getElementById('category_approval_hint');

    function filterCreateOptions() {
        const companyId = createCompany.value;

        // Filter Categories
        Array.from(createCategory.options).forEach(opt => {
            if (opt.value === '') return;
            const cId = opt.getAttribute('data-company');
            opt.style.display = (!companyId || cId === companyId) ? 'block' : 'none';
        });

        // Filter Departments
        Array.from(createDept.options).forEach(opt => {
            if (opt.value === '') return;
            const cId = opt.getAttribute('data-company');
            opt.style.display = (!companyId || cId === companyId) ? 'block' : 'none';
        });

        // Filter Requesters
        if (createRequester && createRequester.tagName === 'SELECT' && createRequester.options) {
            Array.from(createRequester.options).forEach(opt => {
                if (opt.value === '') return;
                const cId = opt.getAttribute('data-company');
                opt.style.display = (!companyId || cId === companyId || !cId) ? 'block' : 'none';
            });
        }

        // Filter Assets
        if (createAsset && createAsset.tagName === 'SELECT' && createAsset.options) {
            Array.from(createAsset.options).forEach(opt => {
                if (opt.value === '') return;
                const cId = opt.getAttribute('data-company');
                opt.style.display = (!companyId || cId === companyId) ? 'block' : 'none';
            });
        }

        // Filter Agents
        if (createAgent && createAgent.tagName === 'SELECT' && createAgent.options) {
            Array.from(createAgent.options).forEach(opt => {
                if (opt.value === '') return;
                const cId = opt.getAttribute('data-company');
                opt.style.display = (!companyId || cId === companyId || !cId) ? 'block' : 'none';
            });
        }
    }

    if (createCompany) {
        createCompany.addEventListener('change', function () {
            filterCreateOptions();
            createCategory.value = '';
            createDept.value = '';
            createAsset.value = '';
            if (approvalHint) approvalHint.innerText = '';
        });
        filterCreateOptions();
    }

    // Auto-fill Department & Priority when Category Selected
    if (createCategory) {
        createCategory.addEventListener('change', function () {
            const selectedOpt = this.selectedOptions[0];
            if (!selectedOpt || !selectedOpt.value) {
                if (approvalHint) approvalHint.innerText = '';
                return;
            }

            const defaultDept = selectedOpt.getAttribute('data-department');
            const defaultPri = selectedOpt.getAttribute('data-priority');
            const reqApproval = selectedOpt.getAttribute('data-approval');

            if (defaultDept && createDept) {
                createDept.value = defaultDept;
            }
            if (defaultPri && createPriority) {
                createPriority.value = defaultPri;
            }
            if (approvalHint) {
                if (reqApproval === '1') {
                    approvalHint.innerHTML = '<span class="text-warning fw-bold">⚠️ Kategori ini memerlukan persetujuan atasan sebelum dikerjakan.</span>';
                } else {
                    approvalHint.innerHTML = '<span class="text-success fw-bold">✓ Langsung diproses tanpa approval.</span>';
                }
            }
        });
    }

    // Submit Create Ticket Form
    const formCreate = document.getElementById('form_create_ticket');
    const btnSubmitCreate = document.getElementById('btn_submit_create_ticket');

    formCreate.addEventListener('submit', function (e) {
        e.preventDefault();

        const companyId = createCompany.value;
        const catId = createCategory.value;
        const deptId = createDept.value;
        const priority = createPriority.value;
        const requesterId = createRequester.value;
        const assetId = createAsset.value;
        const subject = document.getElementById('create_ticket_subject').value.trim();
        const description = document.getElementById('create_ticket_description').value.trim();
        const assignedTo = createAgent.value;

        if (!companyId) {
            Swal.fire('Validasi', 'Silakan pilih perusahaan/tenant.', 'warning');
            return;
        }

        if (!catId) {
            Swal.fire('Validasi', 'Silakan pilih kategori masalah.', 'warning');
            return;
        }

        if (!deptId) {
            Swal.fire('Validasi', 'Silakan pilih departemen penanggung jawab.', 'warning');
            return;
        }

        if (!requesterId) {
            Swal.fire('Validasi', 'Silakan pilih pelapor (requester).', 'warning');
            return;
        }

        if (!subject) {
            Swal.fire('Validasi', 'Subjek masalah tiket wajib diisi.', 'warning');
            return;
        }

        if (!description) {
            Swal.fire('Validasi', 'Deskripsi masalah wajib diisi.', 'warning');
            return;
        }

        btnSubmitCreate.setAttribute('data-kt-indicator', 'on');
        btnSubmitCreate.disabled = true;

        fetch('/api/v1/tickets', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: JSON.stringify({
                company_id: companyId,
                category_id: catId,
                department_id: deptId,
                priority: priority,
                requester_id: requesterId,
                asset_id: assetId || null,
                subject: subject,
                description: description,
                assigned_to: assignedTo || null
            })
        })
        .then(async res => {
            const data = await res.json();
            if (!res.ok) throw data;
            return data;
        })
        .then(data => {
            Swal.fire({
                title: 'Tiket Berhasil Dibuat!',
                html: `${data.message}<br><br>Nomor Tiket: <strong>${data.data.ticket_number}</strong>`,
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => {
                location.reload();
            });
        })
        .catch(err => {
            const msg = err.message || (err.errors ? Object.values(err.errors).flat().join('<br>') : 'Terjadi kesalahan sistem.');
            Swal.fire('Gagal Membuat Tiket', msg, 'error');
        })
        .finally(() => {
            btnSubmitCreate.removeAttribute('data-kt-indicator');
            btnSubmitCreate.disabled = false;
        });
    });

    @if(auth()->user()?->isCompanyAdmin() || auth()->user()?->isSuperadmin())
    // Quick Assign Handler (Admin / Superadmin)
    const assignModalEl = document.getElementById('kt_modal_assign_ticket');
    const assignModal = assignModalEl ? new bootstrap.Modal(assignModalEl) : null;
    const formAssign = document.getElementById('form_assign_ticket');
    const btnSubmitAssign = document.getElementById('btn_submit_quick_assign');

    document.querySelectorAll('.btn-quick-assign').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const number = this.getAttribute('data-number');
            const subject = this.getAttribute('data-subject');
            const companyId = this.getAttribute('data-company');
            const status = this.getAttribute('data-status');
            const approvalStatus = this.getAttribute('data-approval-status');

            if (status === 'pending_approval' || approvalStatus === 'pending') {
                Swal.fire({
                    title: 'Persetujuan Diperlukan!',
                    html: `Tiket <strong>"${number}"</strong> saat ini masih berstatus <strong>Pending Approval</strong>.<br><br>Mohon lakukan persetujuan (approve) oleh atasan atau approver terkait terlebih dahulu sebelum menugaskan teknisi penanganan.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Buka Menu Approval',
                    cancelButtonText: 'Tutup',
                    customClass: {
                        confirmButton: 'btn btn-warning',
                        cancelButton: 'btn btn-light'
                    }
                }).then(result => {
                    if (result.isConfirmed) {
                        window.location.href = '{{ route("approvals.index") }}';
                    }
                });
                return;
            }

            document.getElementById('quick_assign_ticket_id').value = id;
            document.getElementById('quick_assign_ticket_number').innerText = number;
            document.getElementById('quick_assign_ticket_subject').innerText = subject;
            document.getElementById('quick_assign_notes').value = '';

            // Filter agent dropdown by company
            const userSelect = document.getElementById('quick_assign_user_id');
            if (userSelect) {
                Array.from(userSelect.options).forEach(opt => {
                    if (opt.value === '') return;
                    const cId = opt.getAttribute('data-company');
                    opt.style.display = (!companyId || cId === companyId || !cId) ? 'block' : 'none';
                });
                userSelect.value = '';
            }

            if (assignModal) assignModal.show();
        });
    });

    if (formAssign) {
        formAssign.addEventListener('submit', function (e) {
            e.preventDefault();

            const id = document.getElementById('quick_assign_ticket_id').value;
            const agentId = document.getElementById('quick_assign_user_id').value;
            const notes = document.getElementById('quick_assign_notes').value.trim();

            btnSubmitAssign.setAttribute('data-kt-indicator', 'on');
            btnSubmitAssign.disabled = true;

            fetch(`/api/v1/tickets/${id}/assign`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify({
                    assigned_to: agentId || null,
                    notes: notes
                })
            })
            .then(async res => {
                const data = await res.json();
                if (!res.ok) throw data;
                return data;
            })
            .then(data => {
                Swal.fire({
                    title: 'Berhasil!',
                    text: data.message || 'Teknisi berhasil ditugaskan.',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => location.reload());
            })
            .catch(err => {
                Swal.fire('Gagal Menugaskan', err.message || 'Terjadi kesalahan sistem.', 'error');
            })
            .finally(() => {
                btnSubmitAssign.removeAttribute('data-kt-indicator');
                btnSubmitAssign.disabled = false;
            });
        });
    }
    @endif

    @if(auth()->user()?->isAgent())
    // -------------------------------------------------------------
    // Claim Ticket Handler (Khusus Agent)
    // -------------------------------------------------------------
    document.querySelectorAll('.btn-claim-ticket').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const number = this.getAttribute('data-number');
            const status = this.getAttribute('data-status');
            const approvalStatus = this.getAttribute('data-approval-status');

            if (status === 'pending_approval' || approvalStatus === 'pending') {
                Swal.fire({
                    title: 'Persetujuan Diperlukan!',
                    html: `Tiket <strong>"${number}"</strong> saat ini masih berstatus <strong>Pending Approval</strong>.<br><br>Mohon tunggu persetujuan dari atasan terlebih dahulu sebelum tiket dapat diambil.`,
                    icon: 'warning',
                    confirmButtonText: 'Mengerti',
                    customClass: { confirmButton: 'btn btn-warning' }
                });
                return;
            }

            Swal.fire({
                title: 'Ambil Tiket Ini?',
                html: `Apakah Anda yakin ingin mengambil tiket <strong>"${number}"</strong> untuk Anda tangani sendiri?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: '<i class="ki-duotone ki-check fs-6 me-1"><span class="path1"></span><span class="path2"></span></i> Ya, Ambil Tiket',
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-light'
                }
            }).then(result => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Sedang menugaskan tiket ke akun Anda',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); }
                    });

                    fetch(`/api/v1/tickets/${id}/assign`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                        },
                        body: JSON.stringify({
                            assigned_to: {{ auth()->id() }},
                            notes: 'Tiket diambil mandiri oleh teknisi.'
                        })
                    })
                    .then(res => res.json().then(data => ({ status: res.status, body: data })))
                    .then(({ status, body }) => {
                        if (status === 200 && body.success) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: body.message || 'Tiket berhasil diambil dan ditugaskan kepada Anda.',
                                icon: 'success',
                                confirmButtonText: 'OK',
                                customClass: { confirmButton: 'btn btn-primary' }
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Gagal Mengambil Tiket',
                                text: body.message || 'Terjadi kesalahan sistem saat mengambil tiket.',
                                icon: 'error',
                                confirmButtonText: 'Tutup',
                                customClass: { confirmButton: 'btn btn-danger' }
                            });
                        }
                    })
                    .catch(err => {
                        Swal.fire({
                            title: 'Error Jaringan',
                            text: 'Gagal menghubungi server.',
                            icon: 'error',
                            confirmButtonText: 'Tutup',
                            customClass: { confirmButton: 'btn btn-danger' }
                        });
                    });
                }
            });
        });
    });
    @endif

    @if(!auth()->user()?->isRequester())
    // Quick Status Handler
    const statusModalEl = document.getElementById('kt_modal_status_ticket');
    const statusModal = statusModalEl ? new bootstrap.Modal(statusModalEl) : null;
    const formStatus = document.getElementById('form_status_ticket');
    const btnSubmitStatus = document.getElementById('btn_submit_quick_status');

    document.querySelectorAll('.btn-quick-status').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const number = this.getAttribute('data-number');
            const subject = this.getAttribute('data-subject');
            const currentStatus = this.getAttribute('data-status');
            const approvalStatus = this.getAttribute('data-approval-status');

            if (currentStatus === 'pending_approval' || approvalStatus === 'pending') {
                Swal.fire({
                    title: 'Persetujuan Diperlukan!',
                    html: `Tiket <strong>"${number}"</strong> masih berstatus <strong>Pending Approval</strong>.<br><br>Status operasional tiket belum dapat diubah sebelum disetujui oleh atasan terkait.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Buka Menu Approval',
                    cancelButtonText: 'Tutup',
                    customClass: {
                        confirmButton: 'btn btn-warning',
                        cancelButton: 'btn btn-light'
                    }
                }).then(result => {
                    if (result.isConfirmed) {
                        window.location.href = '{{ route("approvals.index") }}';
                    }
                });
                return;
            }

            document.getElementById('quick_status_ticket_id').value = id;
            document.getElementById('quick_status_ticket_number').innerText = number;
            document.getElementById('quick_status_ticket_subject').innerText = subject;
            document.getElementById('quick_status_value').value = currentStatus;
            document.getElementById('quick_status_notes').value = '';

            if (statusModal) statusModal.show();
        });
    });

    if (formStatus) {
        formStatus.addEventListener('submit', function (e) {
            e.preventDefault();

            const id = document.getElementById('quick_status_ticket_id').value;
            const status = document.getElementById('quick_status_value').value;
            const notes = document.getElementById('quick_status_notes').value.trim();

            btnSubmitStatus.setAttribute('data-kt-indicator', 'on');
            btnSubmitStatus.disabled = true;

            fetch(`/api/v1/tickets/${id}/status`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify({
                    status: status,
                    notes: notes
                })
            })
            .then(async res => {
                const data = await res.json();
                if (!res.ok) throw data;
                return data;
            })
            .then(data => {
                Swal.fire({
                    title: 'Berhasil!',
                    text: data.message || 'Status tiket berhasil diubah.',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => location.reload());
            })
            .catch(err => {
                Swal.fire('Gagal Mengubah Status', err.message || 'Terjadi kesalahan sistem.', 'error');
            })
            .finally(() => {
                btnSubmitStatus.removeAttribute('data-kt-indicator');
                btnSubmitStatus.disabled = false;
            });
        });
    }
    @endif

    @if(auth()->user()?->isCompanyAdmin() || auth()->user()?->isSuperadmin())
    // Delete Ticket Handler
    document.querySelectorAll('.btn-delete-ticket').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const number = this.getAttribute('data-number');

            Swal.fire({
                title: 'Hapus Tiket?',
                html: `Apakah Anda yakin ingin menghapus tiket <strong>"${number}"</strong>?<br>Data tiket akan diarsipkan.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#d33'
            }).then(result => {
                if (result.isConfirmed) {
                    fetch(`/api/v1/tickets/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                        }
                    })
                    .then(async res => {
                        const data = await res.json();
                        if (!res.ok) throw data;
                        return data;
                    })
                    .then(data => {
                        Swal.fire('Terhapus!', data.message || 'Tiket berhasil dihapus.', 'success')
                            .then(() => location.reload());
                    })
                    .catch(err => {
                        Swal.fire('Gagal Menghapus', err.message || 'Terjadi kesalahan sistem.', 'error');
                    });
                }
            });
        });
    });
    @endif
});
</script>
@endpush
