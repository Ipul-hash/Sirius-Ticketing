<!-- ============================================================== -->
<!-- DASHBOARD SUPERADMIN (PLATFORM SAAS OWNER)                     -->
<!-- ============================================================== -->
<div class="d-flex flex-column gap-7">

    <!-- 1. KARTU METRIK GLOBAL PLATFORM (4 CARDS) -->
    <div class="row g-5 g-xl-8">
        <!-- Total Perusahaan / Tenant -->
        <div class="col-xl-3 col-sm-6">
            <div class="card card-flush bg-body shadow-sm rounded-4 border-0 h-100">
                <div class="card-body p-6">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="symbol symbol-45px symbol-circle bg-light-primary">
                            <i class="ki-duotone ki-shop fs-2 text-primary"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                        </div>
                        <span class="badge badge-light-success fw-bolder fs-8">{{ $data['activeCompanies'] }} Aktif</span>
                    </div>
                    <div class="fs-2hx fw-bold text-gray-900 mb-1">{{ number_format($data['totalCompanies']) }}</div>
                    <div class="text-muted fs-7 fw-semibold">Total Tenant Terdaftar</div>
                </div>
            </div>
        </div>

        <!-- Total Pengguna Global -->
        <div class="col-xl-3 col-sm-6">
            <div class="card card-flush bg-body shadow-sm rounded-4 border-0 h-100">
                <div class="card-body p-6">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="symbol symbol-45px symbol-circle bg-light-success">
                            <i class="ki-duotone ki-profile-user fs-2 text-success"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                        </div>
                        <span class="badge badge-light-primary fw-bolder fs-8">Platform</span>
                    </div>
                    <div class="fs-2hx fw-bold text-gray-900 mb-1">{{ number_format($data['totalUsers']) }}</div>
                    <div class="text-muted fs-7 fw-semibold">Total Pengguna Global</div>
                </div>
            </div>
        </div>

        <!-- Total Tiket Global -->
        <div class="col-xl-3 col-sm-6">
            <div class="card card-flush bg-body shadow-sm rounded-4 border-0 h-100">
                <div class="card-body p-6">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="symbol symbol-45px symbol-circle bg-light-info">
                            <i class="ki-duotone ki-tablet-text-down fs-2 text-info"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                        </div>
                        <span class="badge badge-light-warning fw-bolder fs-8">{{ $data['openTickets'] }} Terbuka</span>
                    </div>
                    <div class="fs-2hx fw-bold text-gray-900 mb-1">{{ number_format($data['totalTickets']) }}</div>
                    <div class="text-muted fs-7 fw-semibold">Total Tiket Lintas Tenant</div>
                </div>
            </div>
        </div>

        <!-- Total Aset IT (CMDB) -->
        <div class="col-xl-3 col-sm-6">
            <div class="card card-flush bg-body shadow-sm rounded-4 border-0 h-100">
                <div class="card-body p-6">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="symbol symbol-45px symbol-circle bg-light-warning">
                            <i class="ki-duotone ki-devices fs-2 text-warning"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                        </div>
                        <span class="badge badge-light-info fw-bolder fs-8">CMDB</span>
                    </div>
                    <div class="fs-2hx fw-bold text-gray-900 mb-1">{{ number_format($data['totalAssets']) }}</div>
                    <div class="text-muted fs-7 fw-semibold">Total Aset Terdaftar</div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. TABEL PERUSAHAAN TERDAFTAR & AKTIVITAS TIKET GLOBAL -->
    <div class="row g-5 g-xl-8">
        <!-- Daftar Perusahaan / Tenant Terbaru -->
        <div class="col-xl-6">
            <div class="card card-flush bg-body shadow-sm rounded-4 border-0 h-100">
                <div class="card-header pt-6 px-6 border-0">
                    <h3 class="card-title fw-bolder text-gray-900 fs-6 mb-0">
                        <i class="ki-duotone ki-shop fs-4 text-primary me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                        Perusahaan / Tenant Terbaru
                    </h3>
                    <div class="card-toolbar">
                        <a href="{{ url('/companies') }}" class="btn btn-sm btn-light-primary rounded-3 fs-8">
                            Lihat Semua
                        </a>
                    </div>
                </div>

                <div class="card-body pt-2 px-6 pb-6">
                    <div class="table-responsive">
                        <table class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-3">
                            <thead>
                                <tr class="text-start text-muted fw-bold fs-8 text-uppercase gs-0">
                                    <th>Perusahaan</th>
                                    <th>Paket</th>
                                    <th>Pengguna</th>
                                    <th>Tiket</th>
                                    <th class="text-end">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data['recentCompanies'] as $comp)
                                    <tr>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <a href="{{ url('/companies') }}" class="text-gray-900 fw-bold text-hover-primary fs-7">
                                                    {{ $comp->name }}
                                                </a>
                                                <span class="text-muted fs-9">{{ $comp->domain ?? $comp->slug }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge badge-light-primary text-uppercase fs-9 fw-bold">
                                                {{ $comp->plan->value ?? $comp->plan }}
                                            </span>
                                        </td>
                                        <td class="text-gray-800 fw-semibold fs-8">{{ $comp->users_count }}</td>
                                        <td class="text-gray-800 fw-semibold fs-8">{{ $comp->tickets_count }}</td>
                                        <td class="text-end">
                                            @php $st = $comp->status->value ?? $comp->status; @endphp
                                            <span class="badge badge-light-{{ $st === 'active' ? 'success' : 'danger' }} fs-9 fw-bold">
                                                {{ ucfirst($st) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-5">Belum ada tenant terdaftar.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tiket Global Terbaru -->
        <div class="col-xl-6">
            <div class="card card-flush bg-body shadow-sm rounded-4 border-0 h-100">
                <div class="card-header pt-6 px-6 border-0">
                    <h3 class="card-title fw-bolder text-gray-900 fs-6 mb-0">
                        <i class="ki-duotone ki-tablet-text-down fs-4 text-info me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                        Aktivitas Tiket Global Terbaru
                    </h3>
                    <div class="card-toolbar">
                        <a href="{{ url('/tickets') }}" class="btn btn-sm btn-light-info rounded-3 fs-8">
                            Lihat Tiket
                        </a>
                    </div>
                </div>

                <div class="card-body pt-2 px-6 pb-6">
                    <div class="table-responsive">
                        <table class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-3">
                            <thead>
                                <tr class="text-start text-muted fw-bold fs-8 text-uppercase gs-0">
                                    <th>Nomor & Kendala</th>
                                    <th>Tenant</th>
                                    <th>Prioritas</th>
                                    <th class="text-end">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data['recentTickets'] as $tck)
                                    <tr>
                                        <td>
                                            <div class="d-flex flex-column overflow-hidden" style="max-width: 220px;">
                                                <a href="{{ url('/tickets/' . $tck->id) }}" class="text-gray-900 fw-bold text-hover-primary fs-7 text-truncate">
                                                    #{{ $tck->ticket_number }} - {{ $tck->subject }}
                                                </a>
                                                <span class="text-muted fs-9">{{ $tck->requester?->name ?? 'User' }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-gray-800 fs-8 fw-semibold text-truncate d-block" style="max-width: 120px;">
                                                {{ $tck->company?->name ?? '-' }}
                                            </span>
                                        </td>
                                        <td>
                                            @php $pr = $tck->priority->value ?? $tck->priority; @endphp
                                            <span class="badge badge-light-{{ $pr === 'urgent' || $pr === 'high' ? 'danger' : ($pr === 'medium' ? 'warning' : 'secondary') }} fs-9 fw-bold">
                                                {{ ucfirst($pr) }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            @php $st = $tck->status->value ?? $tck->status; @endphp
                                            <span class="badge badge-light-{{ $st === 'closed' || $st === 'resolved' ? 'success' : 'primary' }} fs-9 fw-bold">
                                                {{ ucfirst(str_replace('_', ' ', $st)) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-5">Belum ada tiket masuk.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
