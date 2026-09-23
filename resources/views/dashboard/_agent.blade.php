<!-- ============================================================== -->
<!-- DASHBOARD AGENT (WORKSPACE TEKNISI IT)                         -->
<!-- ============================================================== -->
<div class="d-flex flex-column gap-7">

    <!-- 1. KARTU METRIK WORKSPACE TEKNISI (4 CARDS) -->
    <div class="row g-5 g-xl-8">
        <!-- Tiket Ditugaskan ke Saya -->
        <div class="col-xl-3 col-sm-6">
            <div class="card card-flush bg-body shadow-sm rounded-4 border-0 h-100">
                <div class="card-body p-6">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="symbol symbol-45px symbol-circle bg-light-primary">
                            <i class="ki-duotone ki-user-square fs-2 text-primary"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                        </div>
                        <span class="badge badge-light-primary fw-bolder fs-8">Tugas Saya</span>
                    </div>
                    <div class="fs-2hx fw-bold text-gray-900 mb-1">{{ number_format($data['myAssignedTicketsCount']) }}</div>
                    <div class="text-muted fs-7 fw-semibold">Tiket Ditugaskan ke Saya</div>
                </div>
            </div>
        </div>

        <!-- Antrean Tiket Unassigned -->
        <div class="col-xl-3 col-sm-6">
            <div class="card card-flush bg-body shadow-sm rounded-4 border-0 h-100">
                <div class="card-body p-6">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="symbol symbol-45px symbol-circle bg-light-warning">
                            <i class="ki-duotone ki-element-plus fs-2 text-warning"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                        </div>
                        <span class="badge badge-light-warning fw-bolder fs-8">Antrean Bebas</span>
                    </div>
                    <div class="fs-2hx fw-bold text-gray-900 mb-1">{{ number_format($data['unassignedTicketsCount']) }}</div>
                    <div class="text-muted fs-7 fw-semibold">Tiket Belum Ditugaskan</div>
                </div>
            </div>
        </div>

        <!-- Tiket Urgent yang Perlu Diperhatikan -->
        <div class="col-xl-3 col-sm-6">
            <div class="card card-flush bg-body shadow-sm rounded-4 border-0 h-100">
                <div class="card-body p-6">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="symbol symbol-45px symbol-circle bg-light-danger">
                            <i class="ki-duotone ki-flag fs-2 text-danger"><span class="path1"></span><span class="path2"></span></i>
                        </div>
                        <span class="badge badge-light-danger fw-bolder fs-8">Perlu Aksi Cepat</span>
                    </div>
                    <div class="fs-2hx fw-bold text-gray-900 mb-1">{{ number_format($data['urgentAssignedCount']) }}</div>
                    <div class="text-muted fs-7 fw-semibold">Tiket Urgent Saya</div>
                </div>
            </div>
        </div>

        <!-- Selesai Hari Ini -->
        <div class="col-xl-3 col-sm-6">
            <div class="card card-flush bg-body shadow-sm rounded-4 border-0 h-100">
                <div class="card-body p-6">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="symbol symbol-45px symbol-circle bg-light-success">
                            <i class="ki-duotone ki-check-circle fs-2 text-success"><span class="path1"></span><span class="path2"></span></i>
                        </div>
                        <span class="badge badge-light-success fw-bolder fs-8">Hari Ini</span>
                    </div>
                    <div class="fs-2hx fw-bold text-gray-900 mb-1">{{ number_format($data['myResolvedTodayCount']) }}</div>
                    <div class="text-muted fs-7 fw-semibold">Tiket Diselesaikan Hari Ini</div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. DAFTAR TUGAS SAYA & ANTREAN TIKET BARU -->
    <div class="row g-5 g-xl-8">
        <!-- Tiket Aktif Saya -->
        <div class="col-xl-7">
            <div class="card card-flush bg-body shadow-sm rounded-4 border-0 h-100">
                <div class="card-header pt-6 px-6 border-0">
                    <div class="card-title d-flex align-items-center">
                        <i class="ki-duotone ki-time fs-3 text-primary me-2"><span class="path1"></span><span class="path2"></span></i>
                        <h3 class="fw-bolder text-gray-900 fs-6 mb-0">Tiket Aktif yang Sedang Saya Tangani</h3>
                    </div>
                    <div class="card-toolbar">
                        <a href="{{ url('/tickets') }}" class="btn btn-sm btn-light-primary rounded-3 fs-8">
                            Semua Tiket
                        </a>
                    </div>
                </div>

                <div class="card-body pt-2 px-6 pb-6">
                    <div class="table-responsive">
                        <table class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-3">
                            <thead>
                                <tr class="text-start text-muted fw-bold fs-8 text-uppercase gs-0">
                                    <th>Nomor & Kendala</th>
                                    <th>Pelapor</th>
                                    <th>Prioritas</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data['myActiveTickets'] as $tck)
                                    <tr>
                                        <td>
                                            <div class="d-flex flex-column overflow-hidden" style="max-width: 260px;">
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
                                            @php $pr = $tck->priority->value ?? $tck->priority; @endphp
                                            <span class="badge badge-light-{{ $pr === 'urgent' || $pr === 'high' ? 'danger' : ($pr === 'medium' ? 'warning' : 'secondary') }} fw-bold fs-9">
                                                {{ ucfirst($pr) }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ url('/tickets/' . $tck->id) }}" class="btn btn-sm btn-light-primary rounded-3 px-3 py-1 fs-9">
                                                Respon
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-6">
                                            <i class="ki-duotone ki-verify fs-2hx text-success mb-2"><span class="path1"></span><span class="path2"></span></i>
                                            <div class="fs-8 fw-semibold text-gray-700">Tidak ada tiket aktif yang ditugaskan ke Anda saat ini. Bagus sekali!</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Antrean Tiket Belum Ditugaskan (Unassigned Pool) -->
        <div class="col-xl-5">
            <div class="card card-flush bg-body shadow-sm rounded-4 border-0 h-100">
                <div class="card-header pt-6 px-6 border-0">
                    <div class="card-title d-flex align-items-center">
                        <i class="ki-duotone ki-element-plus fs-3 text-warning me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                        <h3 class="fw-bolder text-gray-900 fs-6 mb-0">Antrean Tiket Baru (Unassigned)</h3>
                    </div>
                </div>

                <div class="card-body pt-2 px-6 pb-6">
                    <div class="d-flex flex-column gap-3">
                        @forelse($data['unassignedQueue'] as $unTck)
                            <div class="d-flex align-items-center justify-content-between p-3 rounded-3 bg-light border border-secondary border-opacity-10">
                                <div class="d-flex flex-column overflow-hidden me-2" style="max-width: 220px;">
                                    <a href="{{ url('/tickets/' . $unTck->id) }}" class="fw-bold text-gray-900 text-hover-primary fs-8 text-truncate">
                                        #{{ $unTck->ticket_number }} - {{ $unTck->subject }}
                                    </a>
                                    <span class="text-muted fs-9">
                                        {{ $unTck->requester?->name ?? 'User' }} &bull; {{ $unTck->created_at->diffForHumans() }}
                                    </span>
                                </div>
                                <a href="{{ url('/tickets/' . $unTck->id) }}" class="btn btn-sm btn-light-warning rounded-3 px-3 py-1 fs-9 flex-shrink-0">
                                    Ambil Tiket
                                </a>
                            </div>
                        @empty
                            <div class="text-center text-muted py-6 fs-8">
                                <i class="ki-duotone ki-coffee fs-2x text-muted mb-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span></i>
                                <div>Semua tiket masuk telah berhasil ditugaskan ke teknisi.</div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
