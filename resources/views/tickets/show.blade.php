@extends('layouts.app')

@section('title', 'Tiket #' . $ticket->ticket_number . ' - ' . $ticket->subject . ' - SiriusTicketing')
@section('page_title', 'Detail Tiket #' . $ticket->ticket_number)

@section('toolbar_actions')
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('tickets.index') }}" class="btn btn-sm btn-light rounded-3 shadow-xs">
            <i class="ki-duotone ki-arrow-left fs-4 me-1"><span class="path1"></span><span class="path2"></span></i>
            Kembali ke Antrean
        </a>

        @if(!$ticket->is_merged)
            <!-- Tombol Gabungkan Tiket (Ticket Merging) -->
            <button type="button" class="btn btn-sm btn-light-primary rounded-3 shadow-xs" data-bs-toggle="modal" data-bs-target="#kt_modal_merge_ticket">
                <i class="ki-duotone ki-switch fs-4 me-1"><span class="path1"></span><span class="path2"></span></i>
                Gabungkan Tiket
            </button>
        @endif

        <!-- Tombol Cepat Tugaskan -->
        <button type="button" class="btn btn-sm btn-light-warning rounded-3 shadow-xs btn-quick-assign"
                data-id="{{ $ticket->id }}"
                data-number="{{ $ticket->ticket_number }}"
                data-subject="{{ $ticket->subject }}"
                data-company="{{ $ticket->company_id }}">
            <i class="ki-duotone ki-user-square fs-4 me-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
            {{ $ticket->assigned_to ? 'Ubah Teknisi' : 'Tugaskan Teknisi' }}
        </button>

        <!-- Tombol Cepat Ubah Status -->
        <button type="button" class="btn btn-sm btn-light-info rounded-3 shadow-xs btn-quick-status"
                data-id="{{ $ticket->id }}"
                data-number="{{ $ticket->ticket_number }}"
                data-subject="{{ $ticket->subject }}"
                data-status="{{ $ticket->status->value ?? $ticket->status }}">
            <i class="ki-duotone ki-arrows-circle fs-4 me-1"><span class="path1"></span><span class="path2"></span></i>
            Ubah Status
        </button>
    </div>
@endsection

@section('content')
<div class="row g-5 g-xl-8">
    <!-- =================================================================== -->
    <!-- KOLOM KIRI: THREAD PERCAKAPAN, DESKRIPSI & FORM BALASAN            -->
    <!-- =================================================================== -->
    <div class="col-xl-8 col-lg-7">
        <div class="d-flex flex-column gap-5">
            <!-- 0. AGENT COLLISION ALERT (REALTIME PRESENCE) -->
            <div id="agent_collision_banner" class="alert alert-dismissible bg-light-warning border border-warning border-dashed d-none flex-column flex-sm-row align-items-center p-4 rounded-4 shadow-xs">
                <div class="d-flex align-items-center me-sm-4 mb-2 mb-sm-0">
                    <div class="position-relative me-3">
                        <span class="bullet bullet-dot bg-warning h-10px w-10px position-absolute top-0 start-100 translate-middle animation-blink"></span>
                        <div class="symbol symbol-35px symbol-circle">
                            <div class="symbol-label bg-warning text-white">
                                <i class="ki-duotone ki-eye fs-3 text-white"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex flex-column">
                        <div class="d-flex align-items-center gap-2">
                            <span class="fw-bolder text-gray-900 fs-7">Peringatan Kehadiran Teknisi (Agent Collision)</span>
                            <span class="badge badge-warning text-white fs-9 py-0 px-2">Live</span>
                        </div>
                        <span class="text-gray-700 fs-8 mt-1" id="agent_collision_text">
                            Ada teknisi lain yang sedang membuka tiket ini.
                        </span>
                    </div>
                </div>
                <div class="symbol-group symbol-hover ms-sm-auto" id="agent_collision_avatars"></div>
            </div>

            @if($ticket->is_merged && $ticket->mergedInto)
                <!-- BANNER PERINGATAN TIKET TELAH DIGABUNGKAN (MERGED TICKET BANNER) -->
                <div class="alert alert-dismissible bg-light-info border border-info border-dashed d-flex flex-column flex-sm-row align-items-center p-5 rounded-4 shadow-xs">
                    <div class="d-flex align-items-center me-sm-4 mb-2 mb-sm-0 flex-grow-1">
                        <div class="symbol symbol-40px symbol-circle bg-info me-3">
                            <i class="ki-duotone ki-switch fs-2 text-white"><span class="path1"></span><span class="path2"></span></i>
                        </div>
                        <div class="d-flex flex-column">
                            <div class="d-flex align-items-center gap-2">
                                <span class="fw-bolder text-gray-900 fs-7">Tiket Ini Telah Digabungkan (Merged Ticket)</span>
                                <span class="badge badge-info text-white fs-9 py-0 px-2">Closed</span>
                            </div>
                            <span class="text-gray-700 fs-8 mt-1">
                                Tiket ini telah ditutup karena merupakan duplikat dan digabungkan ke tiket utama:
                                <a href="{{ url('/tickets/' . $ticket->mergedInto->id) }}" class="fw-bolder text-primary text-decoration-underline">
                                    #{{ $ticket->mergedInto->ticket_number }} - {{ $ticket->mergedInto->subject }}
                                </a>.
                            </span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center flex-shrink-0">
                        <a href="{{ url('/tickets/' . $ticket->mergedInto->id) }}" class="btn btn-sm btn-primary rounded-3 px-4 shadow-sm fs-8">
                            <i class="ki-duotone ki-arrow-right fs-5 me-1"><span class="path1"></span><span class="path2"></span></i>
                            Buka Tiket Utama
                        </a>
                    </div>
                </div>
            @endif

            <!-- 1. KARTU RINGKASAN MASALAH / TIKET AWAL -->
            <div class="card card-flush bg-body shadow-sm rounded-4 border-0">
                <div class="card-header pt-6 px-6 pb-2 border-0">
                    <div class="card-title d-flex flex-column">
                        <div class="d-flex align-items-center flex-wrap gap-2 mb-2">
                            <span class="badge badge-light-primary fw-bolder fs-7 px-3 py-1 rounded-2">
                                {{ $ticket->ticket_number }}
                            </span>
                            
                            <!-- Prioritas Badge -->
                            @php $pVal = $ticket->priority->value ?? $ticket->priority; @endphp
                            @if($pVal === 'urgent')
                                <span class="badge badge-danger fw-bold fs-8 px-2 py-1 rounded-2">Urgent</span>
                            @elseif($pVal === 'high')
                                <span class="badge badge-warning fw-bold fs-8 px-2 py-1 rounded-2">High</span>
                            @elseif($pVal === 'medium')
                                <span class="badge badge-light-primary fw-bold fs-8 px-2 py-1 rounded-2">Medium</span>
                            @else
                                <span class="badge badge-light-secondary text-gray-700 fw-bold fs-8 px-2 py-1 rounded-2">Low</span>
                            @endif

                            <!-- Status Badge -->
                            @php $sVal = $ticket->status->value ?? $ticket->status; @endphp
                            @if($sVal === 'open')
                                <span class="badge badge-light-primary fw-bold fs-8 px-2 py-1 rounded-2">Open</span>
                            @elseif($sVal === 'in_progress')
                                <span class="badge badge-light-info fw-bold fs-8 px-2 py-1 rounded-2">In Progress</span>
                            @elseif($sVal === 'pending_user')
                                <span class="badge badge-light-warning fw-bold fs-8 px-2 py-1 rounded-2">Pending User</span>
                            @elseif($sVal === 'pending_approval')
                                <span class="badge badge-light-warning fw-bold fs-8 px-2 py-1 rounded-2">Pending Approval</span>
                            @elseif($sVal === 'resolved')
                                <span class="badge badge-light-success fw-bold fs-8 px-2 py-1 rounded-2">Resolved</span>
                            @elseif($sVal === 'closed')
                                <span class="badge badge-light-dark fw-bold fs-8 px-2 py-1 rounded-2">Closed</span>
                            @else
                                <span class="badge badge-light fw-bold fs-8 px-2 py-1 rounded-2">{{ ucfirst($sVal) }}</span>
                            @endif

                            <span class="badge badge-light text-gray-700 fs-8 px-2 py-1 rounded-2">
                                <i class="ki-duotone ki-abstract-26 fs-8 me-1"><span class="path1"></span><span class="path2"></span></i>
                                {{ $ticket->category?->name ?? 'Umum' }}
                            </span>
                        </div>
                        <h3 class="fs-4 fw-bolder text-gray-900 mb-0">{{ $ticket->subject }}</h3>
                    </div>
                </div>

                <div class="card-body pt-2 px-6 pb-6">
                    <!-- Info Pemohon Awal -->
                    <div class="d-flex align-items-center justify-content-between p-3 rounded-3 bg-light mb-4 border border-secondary border-opacity-10">
                        <div class="d-flex align-items-center">
                            <div class="symbol symbol-40px me-3">
                                @if($ticket->requester?->avatar_path)
                                    <img src="{{ asset('storage/' . $ticket->requester->avatar_path) }}" alt="{{ $ticket->requester->name }}" />
                                @else
                                    <div class="symbol-label bg-light-primary text-primary fw-bold fs-6">
                                        {{ strtoupper(substr($ticket->requester?->name ?? 'U', 0, 1)) }}
                                    </div>
                                @endif
                            </div>
                            <div class="d-flex flex-column">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="fw-bold text-gray-900 fs-7">{{ $ticket->requester?->name ?? 'Pemohon' }}</span>
                                    <span class="badge badge-light-success fs-9 px-2 py-0">Pelapor</span>
                                </div>
                                <span class="text-muted fs-8">{{ $ticket->requester?->email }} &bull; {{ $ticket->department?->name ?? 'Tanpa Divisi' }}</span>
                            </div>
                        </div>
                        <span class="text-muted fs-8" title="{{ $ticket->created_at->format('d M Y H:i:s') }}">
                            <i class="ki-duotone ki-time fs-7 me-1"><span class="path1"></span><span class="path2"></span></i>
                            {{ $ticket->created_at->diffForHumans() }}
                        </span>
                    </div>

                    <!-- Uraian Masalah Awal -->
                    <div class="fs-6 text-gray-800 lh-base bg-body p-4 rounded-3 border border-dashed border-gray-300">
                        {!! nl2br(e($ticket->description)) !!}
                    </div>
                </div>
            </div>

            <!-- BANNER PERSETUJUAN ITIL (PENDING / APPROVED / REJECTED) -->
            @php
                $appStatus = $ticket->approval_status?->value ?? ($ticket->approval_status ?? 'none');
                $latestApproval = $ticket->approvals->first();
            @endphp
            @if($appStatus === 'pending' || ($ticket->status->value ?? $ticket->status) === 'pending_approval')
                <div class="card card-flush bg-light-warning border border-warning border-dashed rounded-4 p-5 shadow-xs">
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-4">
                        <div class="d-flex align-items-center">
                            <div class="symbol symbol-45px me-3">
                                <div class="symbol-label bg-warning text-white rounded-3">
                                    <i class="ki-duotone ki-lock-2 fs-2 text-white"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                                </div>
                            </div>
                            <div class="d-flex flex-column">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <h5 class="fs-6 fw-bolder text-gray-900 mb-0">Menunggu Persetujuan Resmi (ITIL Approval)</h5>
                                    <span class="badge badge-warning text-white fw-bold fs-9 px-2 py-0">Pending</span>
                                </div>
                                <span class="fs-8 text-gray-700">
                                    Kategori masalah ini memerlukan otorisasi manajer/lead sebelum dapat dikerjakan teknisi.
                                    @if($latestApproval?->approver)
                                        Approver tertunjuk: <strong>{{ $latestApproval->approver->name }}</strong>.
                                    @endif
                                </span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2 flex-shrink-0">
                            <button type="button" class="btn btn-sm btn-success rounded-3 px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#kt_modal_approve_ticket">
                                <i class="ki-duotone ki-check fs-5 me-1"></i>
                                Setujui Tiket
                            </button>
                            <button type="button" class="btn btn-sm btn-danger rounded-3 px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#kt_modal_reject_ticket">
                                <i class="ki-duotone ki-cross fs-5 me-1"></i>
                                Tolak Tiket
                            </button>
                        </div>
                    </div>
                </div>
            @elseif($appStatus === 'approved')
                <div class="alert alert-success d-flex align-items-center p-4 rounded-4 border border-success border-dashed">
                    <i class="ki-duotone ki-verify fs-2hx text-success me-3"><span class="path1"></span><span class="path2"></span></i>
                    <div class="d-flex flex-column">
                        <span class="fw-bold text-gray-900 fs-7">Tiket Telah Disetujui (Approved)</span>
                        <span class="fs-8 text-gray-700">
                            Disetujui oleh <strong>{{ $latestApproval?->approver?->name ?? 'Approver' }}</strong> 
                            pada {{ $latestApproval?->decided_at ? $latestApproval->decided_at->format('d M Y H:i') : '-' }}.
                            @if($latestApproval?->reason_notes)
                                Catatan: <em>"{{ $latestApproval->reason_notes }}"</em>
                            @endif
                        </span>
                    </div>
                </div>
            @elseif($appStatus === 'rejected')
                <div class="alert alert-danger d-flex align-items-center p-4 rounded-4 border border-danger border-dashed">
                    <i class="ki-duotone ki-cross-circle fs-2hx text-danger me-3"><span class="path1"></span><span class="path2"></span></i>
                    <div class="d-flex flex-column">
                        <span class="fw-bold text-danger fs-7">Tiket Ditolak (Rejected)</span>
                        <span class="fs-8 text-gray-700">
                            Ditolak oleh <strong>{{ $latestApproval?->approver?->name ?? 'Approver' }}</strong> 
                            pada {{ $latestApproval?->decided_at ? $latestApproval->decided_at->format('d M Y H:i') : '-' }}.
                            Alasan: <strong>"{{ $latestApproval?->reason_notes ?? '-' }}"</strong>
                        </span>
                    </div>
                </div>
            @endif

            <style>
                #ticketDetailTabs .nav-link {
                    border-top: 0 !important;
                    border-left: 0 !important;
                    border-right: 0 !important;
                    outline: none !important;
                    box-shadow: none !important;
                }
                #ticketDetailTabs .nav-link:focus,
                #ticketDetailTabs .nav-link:active,
                #ticketDetailTabs .nav-link:focus-visible {
                    outline: none !important;
                    box-shadow: none !important;
                }
            </style>

            <!-- TAB NAVIGATION: PERCAKAPAN vs JEJAK AUDIT & TIMELINE -->
            <div class="card card-flush bg-body shadow-sm rounded-4 border-0 mb-2">
                <div class="card-header border-0 pt-2 px-6">
                    <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-6 fw-bold" id="ticketDetailTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link text-active-primary pb-4 active" id="tab_thread_link" data-bs-toggle="tab" href="#pane_thread" role="tab" aria-selected="true">
                                <i class="ki-duotone ki-messages fs-4 text-primary me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                                Percakapan & Respon
                                <span class="badge badge-light-primary fw-bold ms-2 fs-9">{{ $ticket->messages->count() }}</span>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link text-active-primary pb-4" id="tab_audit_link" data-bs-toggle="tab" href="#pane_audit_trail" role="tab" aria-selected="false">
                                <i class="ki-duotone ki-time fs-4 text-primary me-2"><span class="path1"></span><span class="path2"></span></i>
                                Jejak Audit & Timeline
                                <span class="badge badge-light-info fw-bold ms-2 fs-9">{{ $ticket->activities->count() }}</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="tab-content" id="ticketDetailTabsContent">
                <!-- PANE 1: PERCAKAPAN THREAD -->
                <div class="tab-pane fade show active" id="pane_thread" role="tabpanel">
                    <div class="d-flex flex-column gap-5">
                    <!-- 2. THREAD PERCAKAPAN (MESSAGES TIMELINE) -->
                    <div class="d-flex align-items-center justify-content-between mt-2">
                        <h4 class="fs-6 fw-bold text-gray-800 mb-0">
                            <i class="ki-duotone ki-messages fs-4 text-primary me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                            Thread Percakapan ({{ $ticket->messages->count() }})
                        </h4>
                        <span class="fs-8 text-muted">Diurutkan berdasarkan waktu</span>
                    </div>

            @forelse($ticket->messages as $msg)
                @if($msg->is_internal_note)
                    <!-- BUBBLE CATATAN INTERNAL KHUSUS TEKNISI (ZENDESK YELLOW NOTE) -->
                    <div class="card card-flush bg-light-warning shadow-xs rounded-4 p-5 border border-warning border-dashed">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-35px me-3">
                                    <div class="symbol-label bg-warning text-white fw-bold fs-7">
                                        <i class="ki-duotone ki-lock fs-5 text-white"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                                    </div>
                                </div>
                                <div class="d-flex flex-column">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="fw-bold text-gray-900 fs-7">{{ $msg->user?->name ?? 'Teknisi' }}</span>
                                        <span class="badge badge-warning text-white fw-bold fs-9 px-2 py-0">Catatan Internal</span>
                                    </div>
                                    <span class="text-muted fs-8">Hanya terlihat oleh tim teknisi & admin (Rahasia)</span>
                                </div>
                            </div>
                            <span class="text-muted fs-8" title="{{ $msg->created_at->format('d M Y H:i:s') }}">
                                {{ $msg->created_at->diffForHumans() }}
                            </span>
                        </div>

                        <div class="fs-7 text-gray-900 lh-base ps-12">
                            {!! nl2br(e($msg->message)) !!}
                        </div>

                        <!-- Lampiran Berkas Internal Note -->
                        @if($msg->attachments->isNotEmpty())
                            <div class="d-flex flex-wrap gap-2 mt-4 ps-12">
                                @foreach($msg->attachments as $att)
                                    <a href="{{ url('/api/v1/attachments/' . $att->id . '/download') }}" 
                                       class="d-flex align-items-center p-2 rounded-2 bg-body border border-warning border-opacity-50 text-gray-800 text-decoration-none hover-elevate-up transition-all"
                                       target="_blank">
                                        <i class="ki-duotone ki-file fs-4 text-warning me-2"><span class="path1"></span><span class="path2"></span></i>
                                        <div class="d-flex flex-column me-3">
                                            <span class="fs-8 fw-semibold text-truncate" style="max-width: 160px;">{{ $att->file_name }}</span>
                                            <span class="fs-9 text-muted">{{ $att->file_size_kb }} KB</span>
                                        </div>
                                        <i class="ki-duotone ki-down fs-6 text-gray-500"></i>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @else
                    <!-- BUBBLE BALASAN PUBLIK (AGEN / PELAPOR) -->
                    @php
                        $isSenderAgent = in_array($msg->user?->role?->value ?? $msg->user?->role, ['agent', 'company_admin', 'superadmin']);
                    @endphp
                    <div class="card card-flush bg-body shadow-sm rounded-4 p-5 border-0">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-35px me-3">
                                    @if($msg->user?->avatar_path)
                                        <img src="{{ asset('storage/' . $msg->user->avatar_path) }}" alt="{{ $msg->user->name }}" />
                                    @else
                                        <div class="symbol-label {{ $isSenderAgent ? 'bg-light-primary text-primary' : 'bg-light-success text-success' }} fw-bold fs-7">
                                            {{ strtoupper(substr($msg->user?->name ?? 'U', 0, 1)) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="d-flex flex-column">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="fw-bold text-gray-900 fs-7">{{ $msg->user?->name ?? 'Pengguna' }}</span>
                                        @if($isSenderAgent)
                                            <span class="badge badge-light-primary fw-bold fs-9 px-2 py-0">Dukungan Teknisi</span>
                                        @else
                                            <span class="badge badge-light-success fw-bold fs-9 px-2 py-0">Pemohon</span>
                                        @endif
                                    </div>
                                    <span class="text-muted fs-8">{{ $msg->user?->job_title ?? ($isSenderAgent ? 'IT Support' : 'Karyawan') }}</span>
                                </div>
                            </div>
                            <span class="text-muted fs-8" title="{{ $msg->created_at->format('d M Y H:i:s') }}">
                                {{ $msg->created_at->diffForHumans() }}
                            </span>
                        </div>

                        <div class="fs-7 text-gray-800 lh-base ps-12">
                            {!! nl2br(e($msg->message)) !!}
                        </div>

                        <!-- Lampiran Berkas Balasan Publik -->
                        @if($msg->attachments->isNotEmpty())
                            <div class="d-flex flex-wrap gap-2 mt-4 ps-12">
                                @foreach($msg->attachments as $att)
                                    @php
                                        $isImage = in_array(strtolower(pathinfo($att->file_name, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                    @endphp
                                    @if($isImage)
                                        <a href="{{ asset('storage/' . $att->file_path) }}" target="_blank" class="d-inline-block border rounded-3 overflow-hidden shadow-xs hover-scale" style="max-width: 140px;">
                                            <img src="{{ asset('storage/' . $att->file_path) }}" alt="{{ $att->file_name }}" class="w-100 h-90px object-fit-cover" />
                                            <div class="p-1 bg-light text-truncate fs-9 text-gray-700 px-2" title="{{ $att->file_name }}">
                                                {{ $att->file_name }}
                                            </div>
                                        </a>
                                    @else
                                        <a href="{{ url('/api/v1/attachments/' . $att->id . '/download') }}" 
                                           class="d-flex align-items-center p-2 rounded-2 bg-light border border-secondary border-opacity-10 text-gray-800 text-decoration-none hover-elevate-up transition-all"
                                           target="_blank">
                                            <i class="ki-duotone ki-file fs-4 text-primary me-2"><span class="path1"></span><span class="path2"></span></i>
                                            <div class="d-flex flex-column me-3">
                                                <span class="fs-8 fw-semibold text-truncate" style="max-width: 160px;">{{ $att->file_name }}</span>
                                                <span class="fs-9 text-muted">{{ $att->file_size_kb }} KB</span>
                                            </div>
                                            <i class="ki-duotone ki-down fs-6 text-gray-500"></i>
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif
            @empty
                <div class="card card-flush bg-body shadow-sm rounded-4 p-8 text-center border-0">
                    <i class="ki-duotone ki-message-text-2 fs-3x text-muted mb-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                    <h5 class="fs-7 fw-bold text-gray-800">Belum Ada Percakapan Lanjutan</h5>
                    <p class="fs-8 text-muted mb-0">Jadilah yang pertama mengirimkan balasan untuk tiket ini.</p>
                </div>
            @endforelse

            @if($ticket->is_merged)
                <div class="card card-flush bg-light p-6 rounded-4 border border-secondary border-opacity-10 text-center mt-2 shadow-xs">
                    <div class="symbol symbol-40px symbol-circle bg-light-warning mx-auto mb-3">
                        <i class="ki-duotone ki-lock fs-2 text-warning"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                    </div>
                    <h5 class="fs-7 fw-bold text-gray-800 mb-1">Form Balasan Dinonaktifkan</h5>
                    <span class="text-muted fs-8">
                        Tiket ini telah digabungkan ke tiket utama 
                        <a href="{{ url('/tickets/' . $ticket->merged_into_ticket_id) }}" class="fw-bolder text-primary text-decoration-underline">
                            #{{ $ticket->mergedInto?->ticket_number ?? $ticket->merged_into_ticket_id }}
                        </a>. Silakan berikan respon atau catatan pada tiket utama tersebut.
                    </span>
                </div>
            @else
                <!-- 3. EDITOR BALASAN INTERAKTIF (KOMPONEN UTAMA MODUL 9 & 10) -->
                <div class="card card-flush bg-body shadow-sm rounded-4 border-0 mt-2" id="card_reply_editor">
                    <form id="form_send_message" enctype="multipart/form-data">
                        <input type="hidden" id="reply_ticket_id" value="{{ $ticket->id }}" />
                        <input type="hidden" id="reply_is_internal_note" name="is_internal_note" value="0" />

                        <div class="card-header pt-4 px-6 border-0">
                            <div class="d-flex align-items-center justify-content-between w-100 flex-wrap gap-2">
                                <!-- Toggle Tabs: Public Reply vs Internal Note -->
                                <div class="nav nav-pills p-1 bg-light rounded-3" role="tablist">
                                    <button type="button" class="btn btn-sm btn-color-gray-600 btn-active-primary active py-2 px-3 fw-bold fs-8 rounded-2" id="tab_public_reply">
                                        <i class="ki-duotone ki-messages fs-6 me-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                                        Balasan Publik (Ke Pemohon)
                                    </button>
                                    <button type="button" class="btn btn-sm btn-color-gray-600 btn-active-warning py-2 px-3 fw-bold fs-8 rounded-2" id="tab_internal_note">
                                        <i class="ki-duotone ki-lock fs-6 me-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                                        Catatan Internal (Rahasia)
                                    </button>
                                </div>

                                <!-- Macro / Canned Response Picker -->
                                @if($cannedResponses->isNotEmpty())
                                    <div class="d-flex align-items-center">
                                        <select id="select_canned_macro" class="form-select form-select-sm form-select-solid fs-8 rounded-3 w-225px">
                                            <option value="">⚡ Sisipkan Canned Response...</option>
                                            @foreach($cannedResponses as $cr)
                                                <option value="{{ $cr->message }}" data-title="{{ $cr->title }}" data-shortcut="{{ $cr->shortcut }}">
                                                    [{{ $cr->shortcut }}] {{ Str::limit($cr->title, 20) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="card-body pt-2 px-6 pb-4">
                            <!-- Indikator Banner Catatan Internal -->
                            <div id="internal_note_banner" class="alert alert-warning d-flex align-items-center p-3 rounded-3 mb-3 d-none">
                                <i class="ki-duotone ki-shield-cross fs-3 text-warning me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                                <span class="fs-8 text-gray-800">Pesan ini disimpan sebagai <strong>Catatan Internal</strong> dan tidak akan dikirimkan kepada pemohon tiket.</span>
                            </div>

                            <!-- Textarea Pesan -->
                            <div class="fv-row mb-3">
                                <textarea id="reply_message" name="message" rows="4" 
                                          class="form-control form-control-solid rounded-3 fs-7" 
                                          placeholder="Tuliskan balasan untuk pemohon tiket di sini..." required></textarea>
                            </div>

                            <!-- Preview Berkas Lampiran yang Dipilih -->
                            <div id="attachment_preview_container" class="d-flex flex-wrap gap-2 mb-3 d-none"></div>

                            <!-- Footer Editor: Attachment Button, Status Select & Submit -->
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 pt-2 border-top">
                                <div class="d-flex align-items-center gap-2">
                                    <!-- File Upload Button -->
                                    <input type="file" id="input_reply_attachments" name="attachments[]" multiple class="d-none" />
                                    <button type="button" class="btn btn-sm btn-light-secondary rounded-3 text-gray-700 py-2 px-3" id="btn_trigger_file">
                                        <i class="ki-duotone ki-paper-clip fs-5 me-1"><span class="path1"></span><span class="path2"></span></i>
                                        Lampirkan File / Screenshot
                                    </button>
                                    <span class="fs-8 text-muted" id="file_count_hint">Maks. 10MB</span>
                                </div>

                                <div class="d-flex align-items-center gap-2">
                                    <!-- Ubah Status Shortcut -->
                                    <select id="reply_status_shortcut" name="status" class="form-select form-select-sm form-select-solid w-175px fs-8 rounded-3">
                                        <option value="">Status Tetap ({{ ucfirst($sVal) }})</option>
                                        <option value="in_progress">Ubah ke In Progress</option>
                                        <option value="pending_user">Ubah ke Pending User</option>
                                        <option value="resolved">Ubah ke Resolved (Selesai)</option>
                                    </select>

                                    <!-- Submit Button -->
                                    <button type="submit" id="btn_submit_reply" class="btn btn-sm btn-primary rounded-3 py-2 px-4 shadow-sm">
                                        <span class="indicator-label">
                                            <i class="ki-duotone ki-send fs-5 me-1"><span class="path1"></span><span class="path2"></span></i>
                                            Kirim Pesan
                                        </span>
                                        <span class="indicator-progress">Mengirim...
                                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>
    <!-- END PANE 1: PERCAKAPAN THREAD -->

        <!-- PANE 2: JEJAK AUDIT LENGKAP & TIMELINE KRONOLOGIS -->
        <div class="tab-pane fade" id="pane_audit_trail" role="tabpanel">
                <div class="card card-flush bg-body shadow-sm rounded-4 border-0 p-6">
                    <!-- Header Timeline & Filter Chips -->
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-4 mb-6 pb-4 border-bottom border-gray-200">
                        <div>
                            <h4 class="fw-bolder text-gray-900 mb-1">
                                <i class="ki-duotone ki-time fs-3 text-primary me-2"><span class="path1"></span><span class="path2"></span></i>
                                Kronologi Jejak Audit & Riwayat Tiket
                            </h4>
                            <span class="text-muted fs-8">Catatan audit resmi dari setiap perubahan status, penugasan teknisi, persetujuan ITIL, dan komunikasi.</span>
                        </div>
                        
                        <!-- Filter Chips -->
                        <div class="d-flex align-items-center gap-1 flex-wrap" id="audit_filter_chips">
                            <button type="button" class="btn btn-sm btn-primary py-1 px-3 fs-9 rounded-pill audit-filter-btn active" data-filter="all">
                                Semua ({{ $ticket->activities->count() }})
                            </button>
                            <button type="button" class="btn btn-sm btn-light py-1 px-3 fs-9 rounded-pill audit-filter-btn" data-filter="status">
                                Status & Prioritas
                            </button>
                            <button type="button" class="btn btn-sm btn-light py-1 px-3 fs-9 rounded-pill audit-filter-btn" data-filter="assignment">
                                Penugasan Teknisi
                            </button>
                            <button type="button" class="btn btn-sm btn-light py-1 px-3 fs-9 rounded-pill audit-filter-btn" data-filter="approval">
                                Otorisasi ITIL
                            </button>
                            <button type="button" class="btn btn-sm btn-light py-1 px-3 fs-9 rounded-pill audit-filter-btn" data-filter="communication">
                                Pesan & Catatan
                            </button>
                        </div>
                    </div>

                    <!-- Timeline Body -->
                    @if($ticket->activities->isEmpty())
                        <div class="text-center py-10">
                            <div class="symbol symbol-60px mb-3">
                                <div class="symbol-label bg-light-primary rounded-circle">
                                    <i class="ki-duotone ki-time fs-2tx text-primary"><span class="path1"></span><span class="path2"></span></i>
                                </div>
                            </div>
                            <div class="fw-bold text-gray-800 fs-7">Belum Ada Aktivitas Tercatat</div>
                            <div class="text-muted fs-8">Setiap perubahan pada tiket ini akan otomatis terekam secara kronologis di sini.</div>
                        </div>
                    @else
                        <div class="timeline timeline-border-dashed">
                            @foreach($ticket->activities as $act)
                                @php
                                    $type = $act->activity_type;
                                    $config = match($type) {
                                        'ticket_created' => [
                                            'cat' => 'status',
                                            'icon' => 'ki-plus',
                                            'color' => 'primary',
                                            'badge' => 'badge-light-primary',
                                            'title' => 'Tiket Dibuat',
                                        ],
                                        'approval_requested' => [
                                            'cat' => 'approval',
                                            'icon' => 'ki-shield-cross',
                                            'color' => 'warning',
                                            'badge' => 'badge-light-warning',
                                            'title' => 'Permintaan Otorisasi ITIL',
                                        ],
                                        'ticket_approved' => [
                                            'cat' => 'approval',
                                            'icon' => 'ki-verify',
                                            'color' => 'success',
                                            'badge' => 'badge-light-success',
                                            'title' => 'Tiket Disetujui (Approved)',
                                        ],
                                        'ticket_rejected' => [
                                            'cat' => 'approval',
                                            'icon' => 'ki-cross-circle',
                                            'color' => 'danger',
                                            'badge' => 'badge-light-danger',
                                            'title' => 'Tiket Ditolak (Rejected)',
                                        ],
                                        'assigned_agent' => [
                                            'cat' => 'assignment',
                                            'icon' => 'ki-user-tick',
                                            'color' => 'info',
                                            'badge' => 'badge-light-info',
                                            'title' => 'Penugasan Teknisi',
                                        ],
                                        'status_changed' => [
                                            'cat' => 'status',
                                            'icon' => 'ki-arrows-circle',
                                            'color' => 'primary',
                                            'badge' => 'badge-light-primary',
                                            'title' => 'Perubahan Status Tiket',
                                        ],
                                        'priority_changed' => [
                                            'cat' => 'status',
                                            'icon' => 'ki-flag',
                                            'color' => 'danger',
                                            'badge' => 'badge-light-danger',
                                            'title' => 'Eskalasi Prioritas',
                                        ],
                                        'public_reply' => [
                                            'cat' => 'communication',
                                            'icon' => 'ki-messages',
                                            'color' => 'primary',
                                            'badge' => 'badge-light-primary',
                                            'title' => 'Balasan Pesan Publik',
                                        ],
                                        'internal_note' => [
                                            'cat' => 'communication',
                                            'icon' => 'ki-notepad',
                                            'color' => 'warning',
                                            'badge' => 'badge-light-warning',
                                            'title' => 'Catatan Internal Teknisi',
                                        ],
                                        'ticket_merged' => [
                                            'cat' => 'status',
                                            'icon' => 'ki-switch',
                                            'color' => 'warning',
                                            'badge' => 'badge-light-warning',
                                            'title' => 'Penggabungan Tiket (Merged)',
                                        ],
                                        'ticket_merged_source' => [
                                            'cat' => 'status',
                                            'icon' => 'ki-switch',
                                            'color' => 'primary',
                                            'badge' => 'badge-light-primary',
                                            'title' => 'Penerimaan Tiket Gabungan',
                                        ],
                                        default => [
                                            'cat' => 'other',
                                            'icon' => 'ki-abstract-8',
                                            'color' => 'secondary',
                                            'badge' => 'badge-light',
                                            'title' => ucfirst(str_replace('_', ' ', $type)),
                                        ],
                                    };
                                @endphp

                                <div class="timeline-item mb-5 audit-log-item" data-category="{{ $config['cat'] }}">
                                    <!-- Timeline Line & Icon -->
                                    <div class="timeline-line"></div>
                                    <div class="timeline-icon symbol symbol-circle symbol-35px me-4">
                                        <div class="symbol-label bg-light-{{ $config['color'] }}">
                                            <i class="ki-duotone {{ $config['icon'] }} fs-4 text-{{ $config['color'] }}">
                                                <span class="path1"></span><span class="path2"></span><span class="path3"></span>
                                            </i>
                                        </div>
                                    </div>

                                    <!-- Timeline Content Card -->
                                    <div class="timeline-content p-4 rounded-3 bg-light border border-secondary border-opacity-10 w-100">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                                <span class="badge {{ $config['badge'] }} fw-bold fs-8">
                                                    {{ $config['title'] }}
                                                </span>
                                                <span class="text-gray-900 fw-bold fs-7">
                                                    {{ $act->user?->name ?? 'Sistem Otomatis' }}
                                                </span>
                                                @if($act->user?->role)
                                                    <span class="badge badge-light-secondary text-gray-700 fs-9">{{ ucfirst($act->user->role->value ?? $act->user->role) }}</span>
                                                @endif
                                                @if($act->user?->job_title)
                                                    <span class="text-muted fs-9">({{ $act->user->job_title }})</span>
                                                @endif
                                            </div>
                                            <div class="d-flex align-items-center text-muted fs-8">
                                                <i class="ki-duotone ki-calendar fs-8 text-gray-500 me-1"><span class="path1"></span><span class="path2"></span></i>
                                                <span title="{{ $act->created_at->format('d M Y, H:i:s') }}">
                                                    {{ $act->created_at->translatedFormat('d M Y, H:i') }} &bull; {{ $act->created_at->diffForHumans() }}
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Perbandingan Nilai Lama vs Nilai Baru -->
                                        @if($act->old_value && $act->new_value && $act->old_value !== $act->new_value)
                                            <div class="d-inline-flex align-items-center gap-2 mb-2 py-1 px-3 bg-body rounded-2 border border-secondary border-opacity-25">
                                                <span class="badge badge-light text-gray-600 fs-9">{{ ucfirst(str_replace('_', ' ', $act->old_value)) }}</span>
                                                <i class="ki-duotone ki-arrow-right fs-7 text-gray-400"><span class="path1"></span><span class="path2"></span></i>
                                                <span class="badge badge-light-{{ $config['color'] }} text-{{ $config['color'] }} fw-bold fs-9">{{ ucfirst(str_replace('_', ' ', $act->new_value)) }}</span>
                                            </div>
                                        @endif

                                        <!-- Catatan Tambahan -->
                                        @if($act->notes)
                                            <div class="fs-8 text-gray-700 lh-base mt-1">
                                                {{ $act->notes }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
            <!-- END PANE 2: JEJAK AUDIT -->
        </div>
        <!-- END TAB CONTENT -->
    </div>
</div>

    <!-- =================================================================== -->
    <!-- KOLOM KANAN: SIDEBAR PROPERTI SLA, PEMOHON, TEKNISI & CMDB         -->
    <!-- =================================================================== -->
    <div class="col-xl-4 col-lg-5">
        <div class="d-flex flex-column gap-5">
            <!-- 1. KARTU SLA & TARGET WAKTU -->
            <div class="card card-flush bg-body shadow-sm rounded-4 border-0">
                <div class="card-header pt-6 px-6 pb-2 border-0">
                    <h5 class="card-title fw-bolder text-gray-900 fs-6 mb-0">
                        <i class="ki-duotone ki-timer fs-4 text-primary me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                        Kesehatan & Target SLA
                    </h5>
                    @if($ticket->is_sla_breached)
                        <span class="badge badge-danger fw-bold fs-8 px-2 py-1">SLA Breached</span>
                    @elseif($ticket->resolved_at)
                        <span class="badge badge-success fw-bold fs-8 px-2 py-1">Selesai Tepat Waktu</span>
                    @else
                        <span class="badge badge-light-success text-success fw-bold fs-8 px-2 py-1">On-Track</span>
                    @endif
                </div>

                <div class="card-body pt-2 px-6 pb-6">
                    <!-- Respon Pertama -->
                    <div class="d-flex align-items-center justify-content-between py-2 border-bottom border-gray-200">
                        <div class="d-flex flex-column">
                            <span class="text-gray-700 fs-8 fw-semibold">Target Respon Pertama</span>
                            <span class="text-muted fs-9">
                                {{ $ticket->first_response_due_at ? $ticket->first_response_due_at->format('d M Y H:i') : '-' }}
                            </span>
                        </div>
                        <div>
                            @if($ticket->first_responded_at)
                                <span class="badge badge-light-success fs-8 fw-bold">
                                    <i class="ki-duotone ki-check fs-8 text-success me-1"></i>
                                    {{ $ticket->first_responded_at->format('H:i, d M') }}
                                </span>
                            @elseif($ticket->first_response_due_at && $ticket->first_response_due_at->isPast())
                                <span class="badge badge-light-danger text-danger fs-8 fw-bold">Terlambat</span>
                            @else
                                <span class="badge badge-light-info fs-8 fw-bold">
                                    {{ $ticket->first_response_due_at ? $ticket->first_response_due_at->diffForHumans() : '-' }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Target Penyelesaian (Resolution) -->
                    <div class="d-flex align-items-center justify-content-between py-2">
                        <div class="d-flex flex-column">
                            <span class="text-gray-700 fs-8 fw-semibold">Batas Waktu Resolusi</span>
                            <span class="text-muted fs-9">
                                {{ $ticket->resolution_due_at ? $ticket->resolution_due_at->format('d M Y H:i') : '-' }}
                            </span>
                        </div>
                        <div>
                            @if($ticket->resolved_at)
                                <span class="badge badge-success fs-8 fw-bold">
                                    Selesai {{ $ticket->resolved_at->format('H:i, d M') }}
                                </span>
                            @elseif($ticket->resolution_due_at && $ticket->resolution_due_at->isPast())
                                <span class="badge badge-danger fs-8 fw-bold">Melewati Tenggat</span>
                            @else
                                <span class="badge badge-light-primary fs-8 fw-bold">
                                    {{ $ticket->resolution_due_at ? $ticket->resolution_due_at->diffForHumans() : '-' }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. KARTU PROPERTI & PENUGASAN -->
            <div class="card card-flush bg-body shadow-sm rounded-4 border-0">
                <div class="card-header pt-6 px-6 pb-2 border-0">
                    <h5 class="card-title fw-bolder text-gray-900 fs-6 mb-0">
                        <i class="ki-duotone ki-element-11 fs-4 text-primary me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                        Properti Tiket
                    </h5>
                </div>

                <div class="card-body pt-2 px-6 pb-6">
                    <!-- Teknisi Penanggung Jawab -->
                    <div class="mb-4 pb-3 border-bottom border-gray-200">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted fs-8 fw-semibold text-uppercase ls-1">Teknisi (Assigned Agent)</span>
                            <button type="button" class="btn btn-link btn-color-primary p-0 fs-8 fw-bold btn-quick-assign"
                                    data-id="{{ $ticket->id }}"
                                    data-number="{{ $ticket->ticket_number }}"
                                    data-subject="{{ $ticket->subject }}"
                                    data-company="{{ $ticket->company_id }}"
                                    data-status="{{ $ticket->status->value }}"
                                    data-approval-status="{{ $ticket->approval_status->value }}">
                                Ubah
                            </button>
                        </div>
                        @if($ticket->assignedAgent)
                            <div class="d-flex align-items-center p-2 rounded-3 bg-light">
                                <div class="symbol symbol-35px me-3">
                                    <div class="symbol-label bg-light-primary text-primary fw-bold fs-7">
                                        {{ strtoupper(substr($ticket->assignedAgent->name, 0, 1)) }}
                                    </div>
                                </div>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold text-gray-900 fs-7">{{ $ticket->assignedAgent->name }}</span>
                                    <span class="text-muted fs-8">{{ $ticket->assignedAgent->job_title ?? 'IT Specialist' }}</span>
                                </div>
                            </div>
                        @else
                            <div class="d-flex align-items-center justify-content-between p-3 rounded-3 bg-light-warning border border-warning border-dashed">
                                <span class="fs-8 text-warning fw-semibold">Belum ada teknisi yang ditugaskan.</span>
                                <button type="button" class="btn btn-xs btn-warning fw-bold py-1 px-2 btn-quick-assign"
                                        data-id="{{ $ticket->id }}"
                                        data-number="{{ $ticket->ticket_number }}"
                                        data-subject="{{ $ticket->subject }}"
                                        data-company="{{ $ticket->company_id }}"
                                        data-status="{{ $ticket->status->value }}"
                                        data-approval-status="{{ $ticket->approval_status->value }}">
                                    Tugaskan
                                </button>
                            </div>
                        @endif
                    </div>

                    <!-- Departemen & Tenant -->
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom border-gray-200">
                        <span class="text-gray-700 fs-8 fw-semibold">Perusahaan / Tenant</span>
                        <span class="fw-bold text-gray-900 fs-8">{{ $ticket->company?->name }}</span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom border-gray-200">
                        <span class="text-gray-700 fs-8 fw-semibold">Departemen</span>
                        <span class="fw-bold text-gray-900 fs-8">{{ $ticket->department?->name ?? '-' }}</span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom border-gray-200">
                        <span class="text-gray-700 fs-8 fw-semibold">Kategori Layanan</span>
                        <span class="fw-bold text-gray-900 fs-8">{{ $ticket->category?->name ?? '-' }}</span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center py-2">
                        <span class="text-gray-700 fs-8 fw-semibold">Approval ITIL</span>
                        @if($appStatus === 'pending')
                            <span class="badge badge-warning text-white fw-bold fs-9">Menunggu Approval</span>
                        @elseif($appStatus === 'approved')
                            <span class="badge badge-light-success text-success fw-bold fs-9">Disetujui</span>
                        @elseif($appStatus === 'rejected')
                            <span class="badge badge-light-danger text-danger fw-bold fs-9">Ditolak</span>
                        @else
                            <span class="badge badge-light-secondary text-gray-600 fs-9">Tidak Butuh</span>
                        @endif
                    </div>
                </div>
            </div>

            @if($ticket->mergedTickets->isNotEmpty())
                <!-- KARTU TIKET YANG DIGABUNGKAN (MERGED TICKETS) -->
                <div class="card card-flush bg-body shadow-sm rounded-4 border-0">
                    <div class="card-header pt-6 px-6 pb-2 border-0">
                        <div class="card-title d-flex align-items-center justify-content-between w-100">
                            <h5 class="fw-bolder text-gray-900 fs-6 mb-0">
                                <i class="ki-duotone ki-switch fs-4 text-primary me-2"><span class="path1"></span><span class="path2"></span></i>
                                Tiket Tergabung
                            </h5>
                            <span class="badge badge-light-primary fw-bold fs-9">{{ $ticket->mergedTickets->count() }} tiket</span>
                        </div>
                    </div>
                    <div class="card-body pt-2 px-6 pb-6">
                        <span class="text-muted fs-8 d-block mb-3">Tiket duplikat yang telah digabungkan ke tiket ini:</span>
                        <div class="d-flex flex-column gap-2">
                            @foreach($ticket->mergedTickets as $mTicket)
                                <div class="d-flex align-items-center justify-content-between p-3 rounded-3 bg-light border border-secondary border-opacity-10">
                                    <div class="d-flex flex-column me-2 overflow-hidden">
                                        <a href="{{ url('/tickets/' . $mTicket->id) }}" class="fw-bold text-gray-900 text-hover-primary fs-8 text-truncate" title="{{ $mTicket->subject }}">
                                            #{{ $mTicket->ticket_number }} - {{ $mTicket->subject }}
                                        </a>
                                        <span class="text-muted fs-9">
                                            {{ $mTicket->requester?->name ?? 'Pemohon' }} &bull; {{ $mTicket->created_at->format('d/m/Y') }}
                                        </span>
                                    </div>
                                    <a href="{{ url('/tickets/' . $mTicket->id) }}" class="btn btn-icon btn-sm btn-light-primary rounded-circle w-25px h-25px flex-shrink-0" title="Buka tiket">
                                        <i class="ki-duotone ki-arrow-right fs-6"><span class="path1"></span><span class="path2"></span></i>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- 3. KARTU ASET CMDB (JIKA ADA) -->
            <div class="card card-flush bg-body shadow-sm rounded-4 border-0">
                <div class="card-header pt-6 px-6 pb-2 border-0">
                    <h5 class="card-title fw-bolder text-gray-900 fs-6 mb-0">
                        <i class="ki-duotone ki-laptop fs-4 text-primary me-2"><span class="path1"></span><span class="path2"></span></i>
                        Aset IT Terkait (CMDB)
                    </h5>
                </div>

                <div class="card-body pt-2 px-6 pb-6">
                    @if($ticket->asset)
                        @php
                            $assetCat = $ticket->asset->category->value ?? $ticket->asset->category;
                            $assetStat = $ticket->asset->status->value ?? $ticket->asset->status;
                        @endphp
                        <div class="p-3 rounded-3 bg-light border border-secondary border-opacity-10">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="badge badge-light-primary fw-bold fs-9">{{ $ticket->asset->asset_tag }}</span>
                                <span class="badge badge-light-success fs-9">{{ ucfirst($assetStat) }}</span>
                            </div>
                            <div class="fw-bold text-gray-900 fs-7">{{ $ticket->asset->name }}</div>
                            <div class="text-muted fs-8">{{ ucfirst(str_replace('_', ' ', $assetCat)) }} &bull; S/N: {{ $ticket->asset->serial_number ?? '-' }}</div>
                        </div>
                    @else
                        <div class="text-center py-3 text-muted fs-8">
                            Tidak ada aset perangkat keras/lunak yang ditautkan pada tiket ini.
                        </div>
                    @endif
                </div>
            </div>

            <!-- 4. RIWAYAT AUDIT TRAIL AKTIVITAS TIKET (MINI TIMELINE) -->
            <div class="card card-flush bg-body shadow-sm rounded-4 border-0">
                <div class="card-header pt-6 px-6 pb-2 border-0">
                    <h5 class="card-title fw-bolder text-gray-900 fs-6 mb-0">
                        <i class="ki-duotone ki-time fs-4 text-primary me-2"><span class="path1"></span><span class="path2"></span></i>
                        Log Aktivitas Tiket
                    </h5>
                </div>

                <div class="card-body pt-2 px-6 pb-6">
                    <div class="timeline-label">
                        @forelse($ticket->activities->take(6) as $act)
                            <div class="timeline-item">
                                <div class="timeline-label fw-bold text-gray-600 fs-9">{{ $act->created_at->format('H:i') }}</div>
                                <div class="timeline-badge">
                                    <i class="ki-duotone ki-abstract-8 text-primary fs-6"><span class="path1"></span><span class="path2"></span></i>
                                </div>
                                <div class="timeline-content fs-8 text-gray-800 ps-3">
                                    <span class="fw-bold">{{ $act->user?->name ?? 'Sistem' }}</span>:
                                    {{ $act->notes ?? ucfirst(str_replace('_', ' ', $act->activity_type)) }}
                                    <div class="text-muted fs-9">{{ $act->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                        @empty
                            <div class="text-muted fs-8 text-center py-2">Belum ada aktivitas tercatat</div>
                        @endforelse
                    </div>

                    @if($ticket->activities->count() > 0)
                        <div class="pt-3 border-top border-secondary border-opacity-10 mt-3">
                            <button type="button" class="btn btn-sm btn-light-primary w-100 fs-8 py-2 rounded-3" id="btn_switch_to_audit_trail">
                                <i class="ki-duotone ki-time fs-5 me-1"><span class="path1"></span><span class="path2"></span></i>
                                Lihat Semua Jejak Audit ({{ $ticket->activities->count() }})
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Quick Actions -->
@include('tickets._modal_assign', ['users' => $users])
@include('tickets._modal_status')
@include('tickets._modal_approval')
@include('tickets._modal_merge')
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // -------------------------------------------------------------
    // Toggle Tabs: Public Reply vs Internal Note (Zendesk Style)
    // -------------------------------------------------------------
    const tabPublic = document.getElementById('tab_public_reply');
    const tabInternal = document.getElementById('tab_internal_note');
    const isInternalInput = document.getElementById('reply_is_internal_note');
    const replyTextarea = document.getElementById('reply_message');
    const internalBanner = document.getElementById('internal_note_banner');
    const btnSubmit = document.getElementById('btn_submit_reply');

    function setTabMode(isInternal) {
        if (isInternal) {
            tabInternal.classList.add('active');
            tabPublic.classList.remove('active');
            tabPublic.blur();
            isInternalInput.value = '1';
            internalBanner.classList.remove('d-none');
            replyTextarea.classList.add('border-warning');
            replyTextarea.placeholder = 'Tuliskan catatan internal teknisi (tidak akan terlihat oleh pemohon)...';
            btnSubmit.classList.remove('btn-primary');
            btnSubmit.classList.add('btn-warning');
            btnSubmit.querySelector('.indicator-label').innerHTML = '<i class="ki-duotone ki-lock fs-5 me-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i> Simpan Catatan Internal';
        } else {
            tabPublic.classList.add('active');
            tabInternal.classList.remove('active');
            tabInternal.blur();
            isInternalInput.value = '0';
            internalBanner.classList.add('d-none');
            replyTextarea.classList.remove('border-warning');
            replyTextarea.placeholder = 'Tuliskan balasan untuk pemohon tiket di sini...';
            btnSubmit.classList.remove('btn-warning');
            btnSubmit.classList.add('btn-primary');
            btnSubmit.querySelector('.indicator-label').innerHTML = '<i class="ki-duotone ki-send fs-5 me-1"><span class="path1"></span><span class="path2"></span></i> Kirim Pesan';
        }
    }

    if (tabPublic && tabInternal) {
        tabPublic.addEventListener('click', () => setTabMode(false));
        tabInternal.addEventListener('click', () => setTabMode(true));
    }

    // -------------------------------------------------------------
    // Canned Response Macro Picker
    // -------------------------------------------------------------
    const macroSelector = document.getElementById('select_canned_macro');
    if (macroSelector) {
        macroSelector.addEventListener('change', function () {
            const textToInsert = this.value;
            if (!textToInsert) return;

            if (replyTextarea.value.trim() !== '') {
                replyTextarea.value += "\n\n" + textToInsert;
            } else {
                replyTextarea.value = textToInsert;
            }
            replyTextarea.focus();
            this.value = ''; // reset dropdown
        });
    }

    // -------------------------------------------------------------
    // File Attachments Handling & Preview
    // -------------------------------------------------------------
    const btnTriggerFile = document.getElementById('btn_trigger_file');
    const inputFiles = document.getElementById('input_reply_attachments');
    const previewContainer = document.getElementById('attachment_preview_container');
    const fileCountHint = document.getElementById('file_count_hint');

    if (btnTriggerFile && inputFiles) {
        btnTriggerFile.addEventListener('click', () => inputFiles.click());

        inputFiles.addEventListener('change', function () {
            previewContainer.innerHTML = '';
            const files = Array.from(this.files);

            if (files.length > 0) {
                previewContainer.classList.remove('d-none');
                fileCountHint.innerText = `${files.length} berkas dipilih`;

                files.forEach((f, idx) => {
                    const badge = document.createElement('div');
                    badge.className = 'badge badge-light-primary d-flex align-items-center p-2 rounded-2 fs-8';
                    badge.innerHTML = `
                        <i class="ki-duotone ki-paper-clip fs-6 me-1"></i>
                        <span class="text-truncate" style="max-width: 150px;">${f.name}</span>
                        <span class="text-muted ms-1">(${Math.round(f.size / 1024)} KB)</span>
                    `;
                    previewContainer.appendChild(badge);
                });
            } else {
                previewContainer.classList.add('d-none');
                fileCountHint.innerText = 'Maks. 10MB';
            }
        });
    }

    // -------------------------------------------------------------
    // Submit Reply Form (AJAX)
    // -------------------------------------------------------------
    const formReply = document.getElementById('form_send_message');
    if (formReply) {
        formReply.addEventListener('submit', function (e) {
            e.preventDefault();

            const ticketId = document.getElementById('reply_ticket_id').value;
            const message = replyTextarea.value.trim();
            const isInternal = isInternalInput.value;
            const newStatus = document.getElementById('reply_status_shortcut').value;

            if (!message) {
                Swal.fire('Validasi', 'Silakan isi pesan balasan atau catatan internal.', 'warning');
                return;
            }

            const formData = new FormData();
            formData.append('message', message);
            formData.append('is_internal_note', isInternal);
            if (newStatus) {
                formData.append('status', newStatus);
            }

            if (inputFiles.files.length > 0) {
                for (let i = 0; i < inputFiles.files.length; i++) {
                    formData.append('attachments[]', inputFiles.files[i]);
                }
            }

            btnSubmit.setAttribute('data-kt-indicator', 'on');
            btnSubmit.disabled = true;

            fetch(`/api/v1/tickets/${ticketId}/messages`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: formData
            })
            .then(async res => {
                const data = await res.json();
                if (!res.ok) throw data;
                return data;
            })
            .then(data => {
                Swal.fire({
                    title: 'Berhasil!',
                    text: data.message || 'Pesan berhasil dikirimkan.',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => location.reload());
            })
            .catch(err => {
                Swal.fire('Gagal Mengirim', err.message || 'Terjadi kesalahan sistem saat mengirim pesan.', 'error');
            })
            .finally(() => {
                btnSubmit.removeAttribute('data-kt-indicator');
                btnSubmit.disabled = false;
            });
        });
    }

    // -------------------------------------------------------------
    // Quick Assign Handler
    // -------------------------------------------------------------
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
                    confirmButtonText: 'Setujui Sekarang',
                    cancelButtonText: 'Tutup',
                    customClass: {
                        confirmButton: 'btn btn-success',
                        cancelButton: 'btn btn-light'
                    }
                }).then(result => {
                    if (result.isConfirmed) {
                        const approveModalEl = document.getElementById('kt_modal_approve_ticket');
                        if (approveModalEl) {
                            const approveModal = bootstrap.Modal.getInstance(approveModalEl) || new bootstrap.Modal(approveModalEl);
                            approveModal.show();
                        }
                    }
                });
                return;
            }

            document.getElementById('quick_assign_ticket_id').value = id;
            document.getElementById('quick_assign_ticket_number').innerText = number;
            document.getElementById('quick_assign_ticket_subject').innerText = subject;
            document.getElementById('quick_assign_notes').value = '';

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

    // -------------------------------------------------------------
    // Quick Status Handler
    // -------------------------------------------------------------
    const statusModalEl = document.getElementById('kt_modal_status_ticket');
    const statusModal = statusModalEl ? new bootstrap.Modal(statusModalEl) : null;
    const formStatus = document.getElementById('form_status_ticket');
    const btnSubmitStatus = document.getElementById('btn_submit_quick_status');

    document.querySelectorAll('.btn-quick-status').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const number = this.getAttribute('data-number');
            const subject = this.getAttribute('data-subject');
            const status = this.getAttribute('data-status');

            @if($ticket->status->value === 'pending_approval' || $ticket->approval_status->value === 'pending')
                Swal.fire({
                    title: 'Persetujuan Diperlukan!',
                    html: `Tiket <strong>"${number}"</strong> saat ini masih berstatus <strong>Pending Approval</strong>.<br><br>Status operasional belum dapat diubah sebelum disetujui oleh atasan terkait.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Setujui Sekarang',
                    cancelButtonText: 'Tutup',
                    customClass: {
                        confirmButton: 'btn btn-success',
                        cancelButton: 'btn btn-light'
                    }
                }).then(result => {
                    if (result.isConfirmed) {
                        const approveModalEl = document.getElementById('kt_modal_approve_ticket');
                        if (approveModalEl) {
                            const approveModal = bootstrap.Modal.getInstance(approveModalEl) || new bootstrap.Modal(approveModalEl);
                            approveModal.show();
                        }
                    }
                });
                return;
            @endif

            document.getElementById('quick_status_ticket_id').value = id;
            document.getElementById('quick_status_ticket_number').innerText = number;
            document.getElementById('quick_status_ticket_subject').innerText = subject;
            document.getElementById('quick_status_value').value = status;
            document.getElementById('quick_status_notes').value = '';

            if (statusModal) statusModal.show();
        });
    });

    if (formStatus) {
        formStatus.addEventListener('submit', function (e) {
            e.preventDefault();

            const id = document.getElementById('quick_status_ticket_id').value;
            const newStatus = document.getElementById('quick_status_value').value;
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
                    status: newStatus,
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

    // -------------------------------------------------------------
    // Ticket Approval Actions (Approve & Reject)
    // -------------------------------------------------------------
    const formApprove = document.getElementById('form_approve_ticket');
    const btnSubmitApprove = document.getElementById('btn_submit_approve');
    if (formApprove) {
        formApprove.addEventListener('submit', function (e) {
            e.preventDefault();
            const ticketId = document.getElementById('approve_ticket_id').value;
            const notes = document.getElementById('approve_notes').value.trim();

            btnSubmitApprove.setAttribute('data-kt-indicator', 'on');
            btnSubmitApprove.disabled = true;

            fetch(`/api/v1/tickets/${ticketId}/approve`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify({ reason_notes: notes })
            })
            .then(async res => {
                const data = await res.json();
                if (!res.ok) throw data;
                return data;
            })
            .then(data => {
                Swal.fire({
                    title: 'Disetujui!',
                    text: data.message || 'Tiket berhasil disetujui.',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => location.reload());
            })
            .catch(err => {
                Swal.fire('Gagal Menyetujui', err.message || 'Terjadi kesalahan sistem.', 'error');
            })
            .finally(() => {
                btnSubmitApprove.removeAttribute('data-kt-indicator');
                btnSubmitApprove.disabled = false;
            });
        });
    }

    const formReject = document.getElementById('form_reject_ticket');
    const btnSubmitReject = document.getElementById('btn_submit_reject');
    if (formReject) {
        formReject.addEventListener('submit', function (e) {
            e.preventDefault();
            const ticketId = document.getElementById('reject_ticket_id').value;
            const reason = document.getElementById('reject_reason').value.trim();

            if (!reason) {
                Swal.fire('Validasi', 'Alasan penolakan tiket wajib diisi.', 'warning');
                return;
            }

            btnSubmitReject.setAttribute('data-kt-indicator', 'on');
            btnSubmitReject.disabled = true;

            fetch(`/api/v1/tickets/${ticketId}/reject`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify({ reason_notes: reason })
            })
            .then(async res => {
                const data = await res.json();
                if (!res.ok) throw data;
                return data;
            })
            .then(data => {
                Swal.fire({
                    title: 'Ditolak',
                    text: data.message || 'Tiket telah ditolak.',
                    icon: 'info',
                    confirmButtonText: 'OK'
                }).then(() => location.reload());
            })
            .catch(err => {
                Swal.fire('Gagal Menolak', err.message || 'Terjadi kesalahan sistem.', 'error');
            })
            .finally(() => {
                btnSubmitReject.removeAttribute('data-kt-indicator');
                btnSubmitReject.disabled = false;
            });
        });
    }

    // Filter chips for Audit Trail Timeline
    document.querySelectorAll('.audit-filter-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.audit-filter-btn').forEach(b => {
                b.classList.remove('btn-primary', 'active');
                b.classList.add('btn-light');
            });
            this.classList.remove('btn-light');
            this.classList.add('btn-primary', 'active');

            const filter = this.getAttribute('data-filter');
            document.querySelectorAll('.audit-log-item').forEach(item => {
                if (filter === 'all' || item.getAttribute('data-category') === filter) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });

    // Sidebar Mini Timeline shortcut to switch to Audit Trail tab
    const btnSwitchAudit = document.getElementById('btn_switch_to_audit_trail');
    if (btnSwitchAudit) {
        btnSwitchAudit.addEventListener('click', function () {
            const auditTab = document.getElementById('tab_audit_link');
            if (auditTab) {
                const tabInstance = bootstrap.Tab.getInstance(auditTab) || new bootstrap.Tab(auditTab);
                tabInstance.show();
                document.getElementById('pane_audit_trail')?.scrollIntoView({ behavior: 'smooth' });
            }
        });
    }

    // -------------------------------------------------------------
    // Ticket Merging Handler (Zendesk / Freshdesk Style)
    // -------------------------------------------------------------
    const mergeModalEl = document.getElementById('kt_modal_merge_ticket');
    const formMerge = document.getElementById('form_merge_ticket');
    const selectTargetTicket = document.getElementById('select_target_ticket');
    const btnSubmitMerge = document.getElementById('btn_submit_merge');

    if (mergeModalEl && selectTargetTicket) {
        mergeModalEl.addEventListener('show.bs.modal', function () {
            selectTargetTicket.innerHTML = '<option value="">Memuat daftar tiket kandidat...</option>';
            selectTargetTicket.disabled = true;

            fetch(`/api/v1/tickets/{{ $ticket->id }}/merge-candidates`, {
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                selectTargetTicket.innerHTML = '<option value="">-- Pilih Tiket Utama (Primary) --</option>';
                const candidates = data.candidates || [];
                if (candidates.length === 0) {
                    selectTargetTicket.innerHTML = '<option value="">(Tidak ada tiket lain yang tersedia untuk digabungkan)</option>';
                    return;
                }
                candidates.forEach(cand => {
                    const opt = document.createElement('option');
                    opt.value = cand.id;
                    const reqName = cand.requester ? ` - Pelapor: ${cand.requester.name}` : '';
                    opt.textContent = `#${cand.ticket_number} - ${cand.subject} [${cand.status.toUpperCase()}]${reqName}`;
                    selectTargetTicket.appendChild(opt);
                });
                selectTargetTicket.disabled = false;
            })
            .catch(err => {
                selectTargetTicket.innerHTML = '<option value="">Gagal memuat daftar tiket kandidat</option>';
            });
        });
    }

    if (formMerge) {
        formMerge.addEventListener('submit', function (e) {
            e.preventDefault();

            const targetId = selectTargetTicket ? selectTargetTicket.value : '';
            const reasonNotes = document.getElementById('merge_reason_notes')?.value?.trim() || '';

            if (!targetId) {
                Swal.fire('Validasi', 'Silakan tentukan tiket target utama untuk penggabungan.', 'warning');
                return;
            }

            Swal.fire({
                title: 'Konfirmasi Penggabungan',
                html: `Apakah Anda yakin ingin menggabungkan tiket ini?<br><br><span class="text-danger fw-bold">Perhatian:</span> Tiket ini akan langsung <strong>ditutup (Closed)</strong> dan dialihkan ke tiket utama.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Gabungkan Sekarang',
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-light'
                }
            }).then(result => {
                if (!result.isConfirmed) return;

                if (btnSubmitMerge) {
                    btnSubmitMerge.setAttribute('data-kt-indicator', 'on');
                    btnSubmitMerge.disabled = true;
                }

                fetch(`/api/v1/tickets/{{ $ticket->id }}/merge`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: JSON.stringify({
                        target_ticket_id: targetId,
                        notes: reasonNotes
                    })
                })
                .then(async res => {
                    const data = await res.json();
                    if (!res.ok) throw data;
                    return data;
                })
                .then(data => {
                    Swal.fire({
                        title: 'Berhasil Digabungkan!',
                        text: data.message || 'Tiket berhasil digabungkan ke tiket utama.',
                        icon: 'success',
                        confirmButtonText: 'Buka Tiket Utama'
                    }).then(() => {
                        window.location.href = `/tickets/${targetId}`;
                    });
                })
                .catch(err => {
                    Swal.fire('Gagal Menggabungkan', err.message || err.error || 'Terjadi kesalahan sistem saat menggabungkan tiket.', 'error');
                })
                .finally(() => {
                    if (btnSubmitMerge) {
                        btnSubmitMerge.removeAttribute('data-kt-indicator');
                        btnSubmitMerge.disabled = false;
                    }
                });
            });
        });
    }

    // -------------------------------------------------------------
    // Modul 13: Agent Collision Detection (Realtime Heartbeat Presence)
    // -------------------------------------------------------------
    const collisionTicketId = {{ $ticket->id }};
    const collisionUserId = {{ auth()->id() ?? 'null' }};
    const collisionUserName = @json(auth()->user()?->name ?? 'Teknisi');

    function sendCollisionPing() {
        if (!collisionTicketId || !collisionUserId) return;

        fetch(`/api/v1/tickets/${collisionTicketId}/collisions/ping`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: JSON.stringify({
                user_id: collisionUserId,
                user_name: collisionUserName
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data && data.success) {
                updateCollisionPresenceBanner(data.agents || []);
            }
        })
        .catch(() => {});
    }

    function updateCollisionPresenceBanner(agents) {
        const banner = document.getElementById('agent_collision_banner');
        const textEl = document.getElementById('agent_collision_text');
        const avatarsEl = document.getElementById('agent_collision_avatars');
        if (!banner || !textEl || !avatarsEl) return;

        if (agents && agents.length > 0) {
            const agentNames = agents.map(a => a.name).join(', ');
            textEl.innerHTML = `<strong>${agentNames}</strong> saat ini juga sedang aktif melihat tiket ini. Harap berkoordinasi untuk menghindari duplikasi respon.`;

            avatarsEl.innerHTML = '';
            agents.forEach(agent => {
                const initial = (agent.name || 'A').charAt(0).toUpperCase();
                const roleDesc = agent.job_title || agent.role || 'Teknisi';
                const item = document.createElement('div');
                item.className = 'symbol symbol-30px symbol-circle';
                item.setAttribute('data-bs-toggle', 'tooltip');
                item.setAttribute('data-bs-placement', 'top');
                item.setAttribute('title', `${agent.name} (${roleDesc})`);

                if (agent.avatar_path) {
                    item.innerHTML = `<img src="/storage/${agent.avatar_path}" alt="${agent.name}" />`;
                } else {
                    item.innerHTML = `<div class="symbol-label bg-warning text-white fw-bold fs-8">${initial}</div>`;
                }
                avatarsEl.appendChild(item);
            });

            if (window.bootstrap && bootstrap.Tooltip) {
                avatarsEl.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));
            }

            banner.classList.remove('d-none');
            banner.classList.add('d-flex');
        } else {
            banner.classList.add('d-none');
            banner.classList.remove('d-flex');
            avatarsEl.innerHTML = '';
        }
    }

    if (collisionTicketId && collisionUserId) {
        sendCollisionPing();
        setInterval(sendCollisionPing, 10000);

        window.addEventListener('beforeunload', function () {
            const leaveUrl = `/api/v1/tickets/${collisionTicketId}/collisions/leave`;
            const payload = JSON.stringify({ user_id: collisionUserId });

            if (navigator.sendBeacon) {
                const blob = new Blob([payload], { type: 'application/json' });
                navigator.sendBeacon(leaveUrl, blob);
            } else {
                fetch(leaveUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: payload,
                    keepalive: true
                });
            }
        });
    }
});
</script>
@endpush
