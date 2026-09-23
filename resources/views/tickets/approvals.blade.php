@extends('layouts.app')

@section('title', 'Menunggu Approval - SiriusTicketing')
@section('page_title', 'Pusat Otorisasi & Approval Tiket')

@section('toolbar_actions')
    <a href="{{ route('tickets.index') }}" class="btn btn-light-primary btn-sm rounded-3 me-2">
        <i class="ki-duotone ki-arrow-left fs-4"><span class="path1"></span><span class="path2"></span></i>
        Semua Antrean Tiket
    </a>
@endsection

@section('content')
<!-- Main Section: Tabs & Table -->
<div class="card card-flush bg-body shadow-sm rounded-4 border-0">
    <!-- Card Header: Tabs & Search Filter -->
    <div class="card-header pt-6 pb-2 px-6 border-0 flex-wrap gap-4 align-items-center justify-content-between">
        <!-- Status Navigation Tabs -->
        <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-6 fw-bold">
            <li class="nav-item">
                <a class="nav-link text-active-primary pb-4 {{ $activeTab === 'pending' ? 'active' : '' }}" 
                   href="{{ route('approvals.index', array_merge(request()->except('page'), ['status' => 'pending'])) }}">
                    <i class="ki-duotone ki-time fs-4 me-2 {{ $activeTab === 'pending' ? 'text-primary' : '' }}"><span class="path1"></span><span class="path2"></span></i>
                    Menunggu Tindakan
                    @if($stats['pending_count'] > 0)
                        <span class="badge badge-warning text-white ms-2 px-2 py-1 fs-9">{{ $stats['pending_count'] }}</span>
                    @endif
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-active-success pb-4 {{ $activeTab === 'approved' ? 'active' : '' }}" 
                   href="{{ route('approvals.index', array_merge(request()->except('page'), ['status' => 'approved'])) }}">
                    <i class="ki-duotone ki-check fs-4 me-2 {{ $activeTab === 'approved' ? 'text-success' : '' }}"></i>
                    Telah Disetujui
                    <span class="badge badge-light-success text-success ms-2 px-2 py-1 fs-9">{{ $stats['approved_count'] }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-active-danger pb-4 {{ $activeTab === 'rejected' ? 'active' : '' }}" 
                   href="{{ route('approvals.index', array_merge(request()->except('page'), ['status' => 'rejected'])) }}">
                    <i class="ki-duotone ki-cross fs-4 me-2 {{ $activeTab === 'rejected' ? 'text-danger' : '' }}"><span class="path1"></span><span class="path2"></span></i>
                    Ditolak
                    <span class="badge badge-light-danger text-danger ms-2 px-2 py-1 fs-9">{{ $stats['rejected_count'] }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-active-dark pb-4 {{ $activeTab === 'all' ? 'active' : '' }}" 
                   href="{{ route('approvals.index', array_merge(request()->except('page'), ['status' => 'all'])) }}">
                    Semua Riwayat
                </a>
            </li>
        </ul>

        <!-- Filter Form Toolbar -->
        <form method="GET" action="{{ route('approvals.index') }}" class="d-flex align-items-center gap-2 flex-wrap">
            <input type="hidden" name="status" value="{{ $activeTab }}" />

            <!-- Search Input -->
            <div class="d-flex align-items-center position-relative">
                <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-4 text-gray-500"><span class="path1"></span><span class="path2"></span></i>
                <input type="text" name="search" value="{{ request('search') }}" 
                       class="form-control form-control-solid form-control-sm w-225px ps-11 rounded-3" 
                       placeholder="Cari No. Tiket / Pemohon..." />
            </div>

            <!-- Tenant Filter (Khusus Superadmin) -->
            @if(auth()->user()->isSuperadmin() && $companies->count() > 1)
                <select name="company_id" class="form-select form-select-solid form-select-sm w-150px rounded-3" onchange="this.form.submit()">
                    <option value="">Semua Tenant</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>
                            {{ $company->name }}
                        </option>
                    @endforeach
                </select>
            @endif

            <!-- Priority Filter -->
            <select name="priority" class="form-select form-select-solid form-select-sm w-130px rounded-3" onchange="this.form.submit()">
                <option value="">Semua Prioritas</option>
                @foreach($priorities as $priority)
                    <option value="{{ $priority->value }}" {{ request('priority') == $priority->value ? 'selected' : '' }}>
                        {{ ucfirst($priority->value) }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="btn btn-sm btn-primary rounded-3 px-4">
                <i class="ki-duotone ki-filter fs-5"><span class="path1"></span><span class="path2"></span></i>
                Filter
            </button>

            @if(request()->hasAny(['search', 'company_id', 'department_id', 'priority']))
                <a href="{{ route('approvals.index', ['status' => $activeTab]) }}" class="btn btn-sm btn-icon btn-light rounded-3" title="Reset Filter">
                    <i class="ki-duotone ki-cross fs-3"><span class="path1"></span><span class="path2"></span></i>
                </a>
            @endif
        </form>
    </div>

    <!-- Card Body: Table -->
    <div class="card-body pt-3 px-6 pb-6">
        @if($tickets->isEmpty())
            <div class="text-center py-12">
                <div class="symbol symbol-80px mb-4">
                    <div class="symbol-label bg-light-success rounded-circle">
                        <i class="ki-duotone ki-verify fs-2tx text-success"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                </div>
                <h4 class="fw-bolder text-gray-900 mb-1">
                    @if($activeTab === 'pending')
                        Semua Pengajuan Telah Selesai Diproses!
                    @else
                        Tidak Ada Data Tiket Approval Ditemukan
                    @endif
                </h4>
                <p class="text-muted fs-7 mb-4">
                    @if($activeTab === 'pending')
                        Tidak ada tiket yang saat ini tertahan menunggu persetujuan Anda. Tim teknisi dapat terus memproses antrean.
                    @else
                        Silakan sesuaikan kriteria pencarian atau pilih tab status lain di atas.
                    @endif
                </p>
                <a href="{{ route('tickets.index') }}" class="btn btn-sm btn-light-primary rounded-3">
                    <i class="ki-duotone ki-arrow-left fs-4 me-1"></i>
                    Kembali ke Antrean Tiket
                </a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-row-dashed table-row-gray-200 align-middle gs-4 gy-4">
                    <thead>
                        <tr class="fw-bold fs-7 text-uppercase text-gray-500 border-bottom">
                            <th class="min-w-140px">No. Tiket & Tanggal</th>
                            <th class="min-w-220px">Subjek & Layanan Terkait</th>
                            <th class="min-w-160px">Pemohon (Requester)</th>
                            <th class="min-w-140px">Tenant / Aset Terkait</th>
                            <th class="min-w-100px text-center">Prioritas</th>
                            <th class="min-w-140px text-center">Status Approval</th>
                            <th class="min-w-160px text-end">Aksi Otorisasi</th>
                        </tr>
                    </thead>
                    <tbody class="fs-7 fw-semibold text-gray-800">
                        @foreach($tickets as $ticket)
                            @php
                                $appStatus = $ticket->approval_status?->value ?? 'pending';
                                $prio = $ticket->priority?->value ?? 'medium';
                                $prioBadge = match($prio) {
                                    'urgent' => 'badge-danger',
                                    'high' => 'badge-warning',
                                    'medium' => 'badge-light-primary text-primary',
                                    default => 'badge-light-secondary text-gray-600',
                                };
                                $latestApproval = $ticket->approvals->first();
                            @endphp
                            <tr>
                                <!-- No Tiket & Waktu -->
                                <td>
                                    <div class="d-flex flex-column">
                                        <a href="{{ route('tickets.show', $ticket->id) }}" class="text-primary fw-bolder hover-primary fs-7 mb-1">
                                            {{ $ticket->ticket_number }}
                                        </a>
                                        <span class="text-muted fs-8">
                                            {{ $ticket->created_at->translatedFormat('d M Y, H:i') }}
                                        </span>
                                        <span class="text-gray-500 fs-9">
                                            {{ $ticket->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Subjek & Layanan -->
                                <td>
                                    <div class="d-flex flex-column">
                                        <a href="{{ route('tickets.show', $ticket->id) }}" class="text-gray-900 fw-bold text-hover-primary fs-7 mb-1 line-clamp-1" title="{{ $ticket->subject }}">
                                            {{ $ticket->subject }}
                                        </a>
                                        <div class="d-flex align-items-center gap-2 flex-wrap">
                                            <span class="badge badge-light-primary fs-9 fw-semibold">
                                                {{ $ticket->category?->name ?? 'Umum' }}
                                            </span>
                                            @if($ticket->department)
                                                <span class="text-muted fs-8">&bull; {{ $ticket->department->name }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <!-- Pemohon -->
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="symbol symbol-30px me-2">
                                            @if($ticket->requester?->avatar_path)
                                                <img src="{{ asset('storage/' . $ticket->requester->avatar_path) }}" alt="Avatar" />
                                            @else
                                                <div class="symbol-label bg-light-info text-info fw-bold fs-8">
                                                    {{ strtoupper(substr($ticket->requester?->name ?? 'U', 0, 1)) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="d-flex flex-column">
                                            <span class="text-gray-900 fw-bold fs-7">{{ $ticket->requester?->name ?? 'Tamu' }}</span>
                                            <span class="text-muted fs-9">{{ $ticket->requester?->job_title ?? $ticket->requester?->email ?? '-' }}</span>
                                        </div>
                                    </div>
                                </td>

                                <!-- Tenant & Aset CMDB -->
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold text-gray-800 fs-8">{{ $ticket->company?->name }}</span>
                                        @if($ticket->asset)
                                            <div class="d-flex align-items-center text-muted fs-9 mt-1">
                                                <i class="ki-duotone ki-laptop fs-7 text-primary me-1"><span class="path1"></span><span class="path2"></span></i>
                                                <span class="text-truncate max-w-120px" title="{{ $ticket->asset->name }} ({{ $ticket->asset->asset_tag }})">
                                                    {{ $ticket->asset->asset_tag }}
                                                </span>
                                            </div>
                                        @else
                                            <span class="text-gray-400 fs-9">-</span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Prioritas -->
                                <td class="text-center">
                                    <span class="badge {{ $prioBadge }} fw-bold fs-8 px-2 py-1 text-uppercase">
                                        {{ $prio }}
                                    </span>
                                </td>

                                <!-- Status Approval -->
                                <td class="text-center">
                                    @if($appStatus === 'pending')
                                        <span class="badge badge-warning text-white fw-bold fs-8 px-3 py-1">
                                            <span class="spinner-grow spinner-grow-sm me-1" style="width: 7px; height: 7px;"></span>
                                            Menunggu
                                        </span>
                                    @elseif($appStatus === 'approved')
                                        <div class="d-flex flex-column align-items-center">
                                            <span class="badge badge-light-success text-success fw-bold fs-8 px-3 py-1 mb-1">
                                                <i class="ki-duotone ki-check fs-6 text-success me-1"></i> Disetujui
                                            </span>
                                            @if($latestApproval?->approver)
                                                <span class="text-muted fs-9">Oleh: {{ $latestApproval->approver->name }}</span>
                                            @endif
                                        </div>
                                    @elseif($appStatus === 'rejected')
                                        <div class="d-flex flex-column align-items-center">
                                            <span class="badge badge-light-danger text-danger fw-bold fs-8 px-3 py-1 mb-1">
                                                <i class="ki-duotone ki-cross fs-6 text-danger me-1"></i> Ditolak
                                            </span>
                                            @if($latestApproval?->reason_notes)
                                                <span class="text-muted fs-9 text-truncate max-w-120px" title="{{ $latestApproval->reason_notes }}">
                                                    "{{ Str::limit($latestApproval->reason_notes, 20) }}"
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="badge badge-light fs-9 text-muted">-</span>
                                    @endif
                                </td>

                                <!-- Aksi Otorisasi -->
                                <td class="text-end">
                                    @if($appStatus === 'pending')
                                        <div class="d-flex align-items-center justify-content-end gap-1">
                                            <button type="button" class="btn btn-sm btn-icon btn-light-success btn-approve-action" 
                                                    data-id="{{ $ticket->id }}"
                                                    data-number="{{ $ticket->ticket_number }}"
                                                    data-subject="{{ $ticket->subject }}"
                                                    title="Setujui Tiket">
                                                <i class="ki-duotone ki-check fs-4"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-icon btn-light-danger btn-reject-action" 
                                                    data-id="{{ $ticket->id }}"
                                                    data-number="{{ $ticket->ticket_number }}"
                                                    data-subject="{{ $ticket->subject }}"
                                                    title="Tolak Tiket">
                                                <i class="ki-duotone ki-cross fs-4"><span class="path1"></span><span class="path2"></span></i>
                                            </button>
                                            <a href="{{ route('tickets.show', $ticket->id) }}" class="btn btn-sm btn-icon btn-light-primary" title="Lihat Detail Percakapan">
                                                <i class="ki-duotone ki-eye fs-4"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                                            </a>
                                        </div>
                                    @else
                                        <a href="{{ route('tickets.show', $ticket->id) }}" class="btn btn-sm btn-light-primary px-3 py-1 fs-8">
                                            <i class="ki-duotone ki-eye fs-5 me-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                                            Detail
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center flex-wrap pt-4">
                <div class="text-muted fs-8">
                    Menampilkan <span class="fw-bold">{{ $tickets->firstItem() ?? 0 }}</span> sampai <span class="fw-bold">{{ $tickets->lastItem() ?? 0 }}</span> dari <span class="fw-bold">{{ $tickets->total() }}</span> tiket
                </div>
                <div>
                    {{ $tickets->links() }}
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Sertakan Modal Approval (Approve & Reject) -->
@include('tickets._modal_approval')

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Inisialisasi Modal Bootstrap
    const approveModalEl = document.getElementById('kt_modal_approve_ticket');
    const rejectModalEl = document.getElementById('kt_modal_reject_ticket');
    const approveModal = approveModalEl ? new bootstrap.Modal(approveModalEl) : null;
    const rejectModal = rejectModalEl ? new bootstrap.Modal(rejectModalEl) : null;

    // Klik tombol Setujui pada tabel
    document.querySelectorAll('.btn-approve-action').forEach(btn => {
        btn.addEventListener('click', function () {
            const ticketId = this.dataset.id;
            const ticketNumber = this.dataset.number;
            const ticketSubject = this.dataset.subject;

            const inputId = document.getElementById('approve_ticket_id');
            const spanNumber = document.getElementById('approve_modal_ticket_number');
            const divSubject = document.getElementById('approve_modal_ticket_subject');
            const textareaNotes = document.getElementById('approve_notes');

            if (inputId) inputId.value = ticketId;
            if (spanNumber) spanNumber.textContent = ticketNumber;
            if (divSubject) divSubject.textContent = ticketSubject;
            if (textareaNotes) textareaNotes.value = '';

            if (approveModal) approveModal.show();
        });
    });

    // Klik tombol Tolak pada tabel
    document.querySelectorAll('.btn-reject-action').forEach(btn => {
        btn.addEventListener('click', function () {
            const ticketId = this.dataset.id;
            const ticketNumber = this.dataset.number;
            const ticketSubject = this.dataset.subject;

            const inputId = document.getElementById('reject_ticket_id');
            const spanNumber = document.getElementById('reject_modal_ticket_number');
            const divSubject = document.getElementById('reject_modal_ticket_subject');
            const textareaReason = document.getElementById('reject_reason');

            if (inputId) inputId.value = ticketId;
            if (spanNumber) spanNumber.textContent = ticketNumber;
            if (divSubject) divSubject.textContent = ticketSubject;
            if (textareaReason) textareaReason.value = '';

            if (rejectModal) rejectModal.show();
        });
    });

    // Handle Submit Form Approve
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
                    title: 'Berhasil Disetujui!',
                    text: data.message || 'Tiket berhasil disetujui.',
                    icon: 'success',
                    confirmButtonText: 'OK',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    }
                }).then(() => location.reload());
            })
            .catch(err => {
                Swal.fire({
                    title: 'Gagal Menyetujui',
                    text: err.message || 'Terjadi kesalahan sistem saat memproses persetujuan.',
                    icon: 'error',
                    confirmButtonText: 'Tutup',
                    customClass: {
                        confirmButton: 'btn btn-danger'
                    }
                });
            })
            .finally(() => {
                btnSubmitApprove.removeAttribute('data-kt-indicator');
                btnSubmitApprove.disabled = false;
            });
        });
    }

    // Handle Submit Form Reject
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
                    title: 'Tiket Ditolak',
                    text: data.message || 'Tiket telah berhasil ditolak dan status ditutup.',
                    icon: 'info',
                    confirmButtonText: 'OK',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    }
                }).then(() => location.reload());
            })
            .catch(err => {
                Swal.fire({
                    title: 'Gagal Menolak',
                    text: err.message || 'Terjadi kesalahan saat memproses penolakan.',
                    icon: 'error',
                    confirmButtonText: 'Tutup',
                    customClass: {
                        confirmButton: 'btn btn-danger'
                    }
                });
            })
            .finally(() => {
                btnSubmitReject.removeAttribute('data-kt-indicator');
                btnSubmitReject.disabled = false;
            });
        });
    }
});
</script>
@endpush
