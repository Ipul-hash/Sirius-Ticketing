<!-- ============================================================== -->
<!-- DASHBOARD COMPANY ADMIN (IT MANAGER / ADMIN TENANT)            -->
<!-- ============================================================== -->
<div class="d-flex flex-column gap-7">

    <!-- 1. KARTU METRIK OPERASIONAL TENANT (4 CARDS) -->
    <div class="row g-5 g-xl-8">
        <!-- Tiket Aktif / Belum Selesai -->
        <div class="col-xl-3 col-sm-6">
            <div class="card card-flush bg-body shadow-sm rounded-4 border-0 h-100">
                <div class="card-body p-6">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="symbol symbol-45px symbol-circle bg-light-primary">
                            <i class="ki-duotone ki-tablet-text-down fs-2 text-primary"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                        </div>
                        <span class="badge badge-light-primary fw-bolder fs-8">Antrean</span>
                    </div>
                    <div class="fs-2hx fw-bold text-gray-900 mb-1">{{ number_format($data['openTickets']) }}</div>
                    <div class="text-muted fs-7 fw-semibold">Tiket Aktif Berjalan</div>
                </div>
            </div>
        </div>

        <!-- Butuh Otorisasi ITIL -->
        <div class="col-xl-3 col-sm-6">
            <div class="card card-flush bg-body shadow-sm rounded-4 border-0 h-100">
                <div class="card-body p-6">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="symbol symbol-45px symbol-circle bg-light-warning">
                            <i class="ki-duotone ki-verify fs-2 text-warning"><span class="path1"></span><span class="path2"></span></i>
                        </div>
                        <span class="badge badge-light-warning fw-bolder fs-8">Approval</span>
                    </div>
                    <div class="fs-2hx fw-bold text-gray-900 mb-1">{{ number_format($data['pendingApprovalTickets']) }}</div>
                    <div class="text-muted fs-7 fw-semibold">Menunggu Approval ITIL</div>
                </div>
            </div>
        </div>

        <!-- Tiket Terselesaikan -->
        <div class="col-xl-3 col-sm-6">
            <div class="card card-flush bg-body shadow-sm rounded-4 border-0 h-100">
                <div class="card-body p-6">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="symbol symbol-45px symbol-circle bg-light-success">
                            <i class="ki-duotone ki-check-circle fs-2 text-success"><span class="path1"></span><span class="path2"></span></i>
                        </div>
                        <span class="badge badge-light-success fw-bolder fs-8">Selesai</span>
                    </div>
                    <div class="fs-2hx fw-bold text-gray-900 mb-1">{{ number_format($data['resolvedTickets']) }}</div>
                    <div class="text-muted fs-7 fw-semibold">Tiket Selesai / Ditutup</div>
                </div>
            </div>
        </div>

        <!-- Aset Kantor (CMDB) -->
        <div class="col-xl-3 col-sm-6">
            <div class="card card-flush bg-body shadow-sm rounded-4 border-0 h-100">
                <div class="card-body p-6">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="symbol symbol-45px symbol-circle bg-light-info">
                            <i class="ki-duotone ki-devices fs-2 text-info"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                        </div>
                        <span class="badge badge-light-info fw-bolder fs-8">{{ $data['inUseAssets'] }} Terpakai</span>
                    </div>
                    <div class="fs-2hx fw-bold text-gray-900 mb-1">{{ number_format($data['totalAssets']) }}</div>
                    <div class="text-muted fs-7 fw-semibold">Total Aset IT Kantor</div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. TIKET PRIORITAS TINGGI & BEBAN KERJA TEKNISI -->
    <div class="row g-5 g-xl-8">
        <!-- Antrean Tiket Urgent & Kritis -->
        <div class="col-xl-7">
            <div class="card card-flush bg-body shadow-sm rounded-4 border-0 h-100">
                <div class="card-header pt-6 px-6 border-0">
                    <div class="card-title d-flex align-items-center">
                        <i class="ki-duotone ki-flag fs-3 text-danger me-2"><span class="path1"></span><span class="path2"></span></i>
                        <h3 class="fw-bolder text-gray-900 fs-6 mb-0">Tiket Prioritas Tinggi & Mendesak</h3>
                    </div>
                    <div class="card-toolbar">
                        <a href="{{ url('/tickets') }}" class="btn btn-sm btn-light-danger rounded-3 fs-8">
                            Buka Antrean
                        </a>
                    </div>
                </div>

                <div class="card-body pt-2 px-6 pb-6">
                    <div class="table-responsive">
                        <table class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-3">
                            <thead>
                                <tr class="text-start text-muted fw-bold fs-8 text-uppercase gs-0">
                                    <th>Nomor & Subjek</th>
                                    <th>Pemohon</th>
                                    <th>Teknisi</th>
                                    <th class="text-end">Prioritas</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data['urgentTickets'] as $tck)
                                    <tr>
                                        <td>
                                            <div class="d-flex flex-column overflow-hidden" style="max-width: 250px;">
                                                <a href="{{ url('/tickets/' . $tck->id) }}" class="text-gray-900 fw-bold text-hover-primary fs-7 text-truncate">
                                                    #{{ $tck->ticket_number }} - {{ $tck->subject }}
                                                </a>
                                                <span class="text-muted fs-9">{{ $tck->category?->name ?? 'Umum' }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-gray-800 fs-8 fw-semibold">{{ $tck->requester?->name ?? 'User' }}</span>
                                        </td>
                                        <td>
                                            @if($tck->assignedAgent)
                                                <span class="badge badge-light-primary fs-9 fw-semibold">{{ $tck->assignedAgent->name }}</span>
                                            @else
                                                <span class="badge badge-light-warning fs-9 fw-semibold">Belum Ditugaskan</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            @php $pr = $tck->priority->value ?? $tck->priority; @endphp
                                            <span class="badge badge-light-{{ $pr === 'urgent' ? 'danger' : 'warning' }} fw-bold fs-9">
                                                {{ ucfirst($pr) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-5">
                                            <i class="ki-duotone ki-shield-tick fs-2x text-success mb-2"><span class="path1"></span><span class="path2"></span></i>
                                            <div class="fs-8 fw-semibold text-gray-700">Tidak ada tiket mendesak/urgent saat ini. Semua terkendali!</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Beban Kerja Teknisi (Agent Workload) -->
        <div class="col-xl-5">
            <div class="card card-flush bg-body shadow-sm rounded-4 border-0 h-100">
                <div class="card-header pt-6 px-6 border-0">
                    <div class="card-title d-flex align-items-center">
                        <i class="ki-duotone ki-user-square fs-3 text-primary me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                        <h3 class="fw-bolder text-gray-900 fs-6 mb-0">Beban Kerja Teknisi (Agent Workload)</h3>
                    </div>
                </div>

                <div class="card-body pt-2 px-6 pb-6">
                    <div class="d-flex flex-column gap-4">
                        @forelse($data['agentsWorkload'] as $ag)
                            @php
                                $assignedCount = $ag->assigned_tickets_count;
                                $barColor = $assignedCount >= 5 ? 'danger' : ($assignedCount >= 3 ? 'warning' : 'success');
                            @endphp
                            <div class="d-flex flex-column p-3 rounded-3 bg-light border border-secondary border-opacity-10">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <div class="d-flex align-items-center">
                                        <div class="symbol symbol-30px symbol-circle bg-light-primary text-primary fw-bold fs-8 me-2">
                                            {{ strtoupper(substr($ag->name, 0, 1)) }}
                                        </div>
                                        <span class="fw-bold text-gray-900 fs-8">{{ $ag->name }}</span>
                                    </div>
                                    <span class="badge badge-light-{{ $barColor }} fw-bolder fs-9">{{ $assignedCount }} Tiket Aktif</span>
                                </div>
                                <div class="progress h-6px w-100 bg-secondary bg-opacity-25 rounded-pill mt-1">
                                    <div class="progress-bar bg-{{ $barColor }} rounded-pill" role="progressbar" style="width: {{ min(100, $assignedCount * 15) }}%"></div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-5 fs-8">Belum ada staf teknisi (agent) di perusahaan ini.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
