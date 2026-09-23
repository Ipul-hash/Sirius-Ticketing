<!-- ============================================================== -->
<!-- DASHBOARD REQUESTER (PORTAL LAYANAN MANDIRI / SELF-SERVICE)    -->
<!-- ============================================================== -->
<div class="d-flex flex-column gap-7">

    <!-- 1. HERO BANNER SELAMAT DATANG & AKSI CEPAT -->
    <div class="card card-flush bg-light-primary border-primary border-dashed border-1 rounded-4 overflow-hidden shadow-xs">
        <div class="card-body p-6 p-lg-8">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-4">
                <div class="d-flex align-items-center gap-4">
                    <div class="symbol symbol-60px symbol-circle bg-primary text-white d-none d-sm-flex align-items-center justify-content-center shadow-xs">
                        <i class="ki-duotone ki-user fs-1 text-white"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                    <div>
                        <h2 class="fw-bolder text-gray-900 fs-3 mb-1">
                            Halo, {{ auth()->user()->name ?? 'Rekan Kerja' }}! 👋
                        </h2>
                        <div class="text-muted fs-7">
                            Selamat datang di Portal Layanan Mandiri IT. Ada kendala teknis atau butuh bantuan perangkat kantor? Tim Helpdesk siap membantu Anda.
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2 flex-shrink-0">
                    <a href="{{ url('/tickets') }}" class="btn btn-sm btn-primary rounded-3 shadow-xs px-4 py-2">
                        <i class="ki-duotone ki-plus fs-4 me-1"><span class="path1"></span><span class="path2"></span></i>
                        Ajukan Tiket Baru
                    </a>
                    <a href="{{ url('/tickets') }}" class="btn btn-sm btn-outline btn-outline-primary bg-body rounded-3 px-4 py-2">
                        Lihat Tiket Saya
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. KARTU METRIK PERMOHONAN SAYA (4 CARDS) -->
    <div class="row g-5 g-xl-8">
        <!-- Tiket Aktif / Sedang Diproses -->
        <div class="col-xl-3 col-sm-6">
            <div class="card card-flush bg-body shadow-sm rounded-4 border-0 h-100">
                <div class="card-body p-6">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="symbol symbol-45px symbol-circle bg-light-primary">
                            <i class="ki-duotone ki-time fs-2 text-primary"><span class="path1"></span><span class="path2"></span></i>
                        </div>
                        <span class="badge badge-light-primary fw-bolder fs-8">Sedang Diproses</span>
                    </div>
                    <div class="fs-2hx fw-bold text-gray-900 mb-1">{{ number_format($data['myOpenTicketsCount']) }}</div>
                    <div class="text-muted fs-7 fw-semibold">Tiket Aktif Berjalan</div>
                </div>
            </div>
        </div>

        <!-- Menunggu Respon Saya -->
        <div class="col-xl-3 col-sm-6">
            <div class="card card-flush bg-body shadow-sm rounded-4 border-0 h-100">
                <div class="card-body p-6">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="symbol symbol-45px symbol-circle bg-light-warning">
                            <i class="ki-duotone ki-message-text-2 fs-2 text-warning"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                        </div>
                        <span class="badge badge-light-warning fw-bolder fs-8">Perlu Respon</span>
                    </div>
                    <div class="fs-2hx fw-bold text-gray-900 mb-1">{{ number_format($data['myPendingUserCount']) }}</div>
                    <div class="text-muted fs-7 fw-semibold">Menunggu Respon Saya</div>
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
                    <div class="fs-2hx fw-bold text-gray-900 mb-1">{{ number_format($data['myResolvedTicketsCount']) }}</div>
                    <div class="text-muted fs-7 fw-semibold">Tiket Selesai / Ditutup</div>
                </div>
            </div>
        </div>

        <!-- Total Seluruh Tiket Saya -->
        <div class="col-xl-3 col-sm-6">
            <div class="card card-flush bg-body shadow-sm rounded-4 border-0 h-100">
                <div class="card-body p-6">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="symbol symbol-45px symbol-circle bg-light-info">
                            <i class="ki-duotone ki-element-11 fs-2 text-info"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                        </div>
                        <span class="badge badge-light-info fw-bolder fs-8">Riwayat</span>
                    </div>
                    <div class="fs-2hx fw-bold text-gray-900 mb-1">{{ number_format($data['myTicketsTotal']) }}</div>
                    <div class="text-muted fs-7 fw-semibold">Total Pengajuan Saya</div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. KONTEN UTAMA: TIKET SAYA & INVENTARIS ASET SAYA -->
    <div class="row g-5 g-xl-8">
        <!-- Kolom Kiri: Daftar Tiket Terbaru Saya -->
        <div class="col-xl-7">
            <div class="card card-flush bg-body shadow-sm rounded-4 border-0 h-100">
                <div class="card-header pt-6 px-6 border-0">
                    <div class="card-title d-flex align-items-center">
                        <i class="ki-duotone ki-notepad fs-3 text-primary me-2"><span class="path1"></span><span class="path2"></span></i>
                        <h3 class="fw-bolder text-gray-900 fs-6 mb-0">Tiket Permohonan Bantuan Saya</h3>
                    </div>
                    <div class="card-toolbar">
                        <a href="{{ url('/tickets') }}" class="btn btn-sm btn-light-primary rounded-3 fs-8">
                            Lihat Semua Tiket
                        </a>
                    </div>
                </div>

                <div class="card-body pt-2 px-6 pb-6">
                    <div class="table-responsive">
                        <table class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-3">
                            <thead>
                                <tr class="text-start text-muted fw-bold fs-8 text-uppercase gs-0">
                                    <th>Nomor & Kendala</th>
                                    <th>Kategori</th>
                                    <th>Teknisi</th>
                                    <th>Status</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data['myRecentTickets'] as $tck)
                                    <tr>
                                        <td>
                                            <div class="d-flex flex-column overflow-hidden" style="max-width: 220px;">
                                                <a href="{{ url('/tickets/' . $tck->id) }}" class="text-gray-900 fw-bold text-hover-primary fs-7 text-truncate">
                                                    #{{ $tck->ticket_number }} - {{ $tck->subject }}
                                                </a>
                                                <span class="text-muted fs-9">{{ $tck->created_at->diffForHumans() }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge badge-light-secondary fs-9 fw-semibold">
                                                {{ $tck->category?->name ?? 'Umum' }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($tck->assignedAgent)
                                                <span class="text-gray-800 fs-8 fw-semibold">{{ $tck->assignedAgent->name }}</span>
                                            @else
                                                <span class="text-muted fs-9 fst-italic">Menunggu Teknisi</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $st = $tck->status->value ?? (string)$tck->status;
                                                $badgeClass = match($st) {
                                                    'open' => 'primary',
                                                    'assigned', 'in_progress' => 'info',
                                                    'pending_user' => 'warning',
                                                    'resolved', 'closed' => 'success',
                                                    default => 'secondary',
                                                };
                                            @endphp
                                            <span class="badge badge-light-{{ $badgeClass }} fw-bold fs-9">
                                                {{ ucfirst(str_replace('_', ' ', $st)) }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ url('/tickets/' . $tck->id) }}" class="btn btn-sm btn-light-primary rounded-3 px-3 py-1 fs-9">
                                                Lihat
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-8">
                                            <div class="symbol symbol-60px symbol-circle bg-light-primary mx-auto mb-3 d-flex align-items-center justify-content-center">
                                                <i class="ki-duotone ki-check-circle fs-2hx text-primary"><span class="path1"></span><span class="path2"></span></i>
                                            </div>
                                            <div class="fs-7 fw-bold text-gray-800 mb-1">Belum Ada Tiket yang Diajukan</div>
                                            <div class="fs-8 text-muted mb-4">Jika Anda mengalami kendala perangkat atau akun, silakan buat laporan tiket baru.</div>
                                            <a href="{{ url('/tickets') }}" class="btn btn-sm btn-primary rounded-3 shadow-xs">
                                                <i class="ki-duotone ki-plus fs-4 me-1"><span class="path1"></span><span class="path2"></span></i>
                                                Buat Laporan Baru
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Aset Saya & Panduan Bantuan -->
        <div class="col-xl-5">
            <div class="d-flex flex-column gap-6">

                <!-- Aset / Perangkat Kantor Saya -->
                <div class="card card-flush bg-body shadow-sm rounded-4 border-0">
                    <div class="card-header pt-6 px-6 border-0">
                        <div class="card-title d-flex align-items-center">
                            <i class="ki-duotone ki-devices fs-3 text-info me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                            <h3 class="fw-bolder text-gray-900 fs-6 mb-0">Perangkat & Aset Kantor Saya</h3>
                        </div>
                    </div>

                    <div class="card-body pt-2 px-6 pb-6">
                        <div class="d-flex flex-column gap-3">
                            @forelse($data['myAssignedAssets'] as $asset)
                                <div class="d-flex align-items-center justify-content-between p-3 rounded-3 bg-light border border-secondary border-opacity-10">
                                    <div class="d-flex align-items-center gap-3 overflow-hidden">
                                        <div class="symbol symbol-35px symbol-circle bg-light-info text-info flex-shrink-0 d-flex align-items-center justify-content-center">
                                            <i class="ki-duotone ki-laptop fs-4 text-info"><span class="path1"></span><span class="path2"></span></i>
                                        </div>
                                        <div class="d-flex flex-column overflow-hidden">
                                            <span class="fw-bold text-gray-900 fs-8 text-truncate">{{ $asset->name }}</span>
                                            <span class="text-muted fs-9">Tag: <span class="fw-semibold text-gray-700">{{ $asset->asset_tag }}</span> &bull; SN: {{ $asset->serial_number ?? '-' }}</span>
                                        </div>
                                    </div>
                                    <span class="badge badge-light-success fs-9 fw-semibold flex-shrink-0">
                                        {{ ucfirst($asset->status->value ?? 'In Use') }}
                                    </span>
                                </div>
                            @empty
                                <div class="text-center text-muted py-6 fs-8">
                                    <i class="ki-duotone ki-devices fs-2x text-muted mb-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                                    <div>Belum ada inventaris/aset terdaftar atas nama akun Anda.</div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Card Panduan & Kontak IT Helpdesk -->
                <div class="card card-flush bg-body shadow-sm rounded-4 border-0">
                    <div class="card-header pt-6 px-6 border-0">
                        <div class="card-title d-flex align-items-center">
                            <i class="ki-duotone ki-information-4 fs-3 text-warning me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                            <h3 class="fw-bolder text-gray-900 fs-6 mb-0">Pusat Informasi & Bantuan IT</h3>
                        </div>
                    </div>

                    <div class="card-body pt-2 px-6 pb-6">
                        <div class="d-flex flex-column gap-3 fs-8 text-gray-700">
                            <div class="d-flex align-items-start gap-2">
                                <i class="ki-duotone ki-time fs-4 text-primary mt-1"><span class="path1"></span><span class="path2"></span></i>
                                <div>
                                    <div class="fw-bold text-gray-900">Jam Operasional Helpdesk:</div>
                                    <div class="text-muted">Senin - Jumat, 08:30 - 17:30 WIB</div>
                                </div>
                            </div>
                            <div class="d-flex align-items-start gap-2">
                                <i class="ki-duotone ki-flash fs-4 text-warning mt-1"><span class="path1"></span><span class="path2"></span></i>
                                <div>
                                    <div class="fw-bold text-gray-900">SLA Respon Awal:</div>
                                    <div class="text-muted">Maksimal 30 menit setelah tiket baru dibuat.</div>
                                </div>
                            </div>
                            <div class="d-flex align-items-start gap-2">
                                <i class="ki-duotone ki-shield-tick fs-4 text-success mt-1"><span class="path1"></span><span class="path2"></span></i>
                                <div>
                                    <div class="fw-bold text-gray-900">Tips Penanganan Cepat:</div>
                                    <div class="text-muted">Sertakan screenshot atau pesan error saat membuka tiket agar tim IT dapat mendiagnosis lebih cepat.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>
