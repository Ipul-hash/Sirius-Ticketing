@extends('layouts.app')

@section('title', 'Kebijakan SLA (Service Level Agreement) - SiriusTicketing')
@section('page_title', 'Kebijakan SLA')

@section('toolbar_actions')
    <!-- Tenant Switcher Dropdown -->
    <div class="d-flex align-items-center gap-2">
        <label class="fs-7 fw-bold text-gray-700 text-nowrap d-none d-sm-inline">Konfigurasi Tenant:</label>
        <select id="sla_tenant_switcher" class="form-select form-select-solid form-select-sm w-200px" data-control="select2" data-hide-search="true">
            @foreach($companies as $comp)
                <option value="{{ $comp->id }}" {{ $selectedCompany?->id == $comp->id ? 'selected' : '' }}>
                    {{ $comp->name }}
                </option>
            @endforeach
        </select>
    </div>
@endsection

@section('content')
<!-- Notice Banner -->
<div class="alert alert-dismissible bg-light-primary d-flex flex-column flex-sm-row p-5 mb-8 border border-primary border-dashed">
    <i class="ki-duotone ki-timer fs-2hx text-primary me-4 mb-5 mb-sm-0"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
    <div class="d-flex flex-column pe-0 pe-sm-10">
        <h5 class="fw-bold text-primary mb-1">Matriks Service Level Agreement (SLA) Multi-Tenant</h5>
        <span class="text-gray-700 fs-7">
            Target waktu respon pertama dan penyelesaian tiket dihitung otomatis sejak tiket dibuat oleh requester. Saat ini menampilkan kebijakan untuk: <strong>{{ $selectedCompany?->name ?? 'Belum ada tenant' }}</strong>.
        </span>
    </div>
</div>

<!-- 4 Priority Cards -->
<div class="row g-5 g-xl-8 mb-5 mb-xl-8">
    <!-- 1. Urgent -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-flush h-100 shadow-sm hover-elevate-up border-0 position-relative overflow-hidden">
            <div class="position-absolute top-0 start-0 end-0 h-3px bg-danger"></div>
            <div class="card-body d-flex flex-column justify-content-between p-6">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="symbol symbol-50px">
                        <div class="symbol-label bg-light-danger rounded-3">
                            <i class="ki-duotone ki-electricity fs-2x text-danger"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                        </div>
                    </div>
                    <span class="badge badge-light-danger fw-bold fs-8 px-3 py-2">
                        <span class="bullet bullet-dot bg-danger me-1"></span>Kritis / Urgent
                    </span>
                </div>

                <div class="mt-4 mb-3">
                    <div class="fs-4 fw-bold text-gray-900 mb-2">Target Respon: {{ $slaUrgent ? ($slaUrgent->first_response_time_minutes >= 60 ? round($slaUrgent->first_response_time_minutes / 60, 1) . ' Jam' : $slaUrgent->first_response_time_minutes . ' Menit') : '30 Menit' }}</div>
                    <div class="fs-7 text-muted">Target Resolusi: <strong class="text-danger">{{ $slaUrgent ? ($slaUrgent->resolution_time_minutes >= 60 ? round($slaUrgent->resolution_time_minutes / 60, 1) . ' Jam' : $slaUrgent->resolution_time_minutes . ' Menit') : '2 Jam' }}</strong></div>
                </div>

                <div class="pt-3 border-top border-gray-200 border-opacity-50 d-flex justify-content-between align-items-center">
                    <span class="text-gray-600 fs-8 fw-semibold">Sistem Down / Fatal</span>
                    <button type="button" class="btn btn-sm btn-light-danger py-1 px-3 fs-8 fw-bold btn-edit-sla" 
                        data-priority="urgent" 
                        data-priority-name="Urgent (Kritis)"
                        data-response="{{ $slaUrgent?->first_response_time_minutes ?? 30 }}" 
                        data-resolution="{{ $slaUrgent?->resolution_time_minutes ?? 120 }}">
                        Konfigurasi
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. High -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-flush h-100 shadow-sm hover-elevate-up border-0 position-relative overflow-hidden">
            <div class="position-absolute top-0 start-0 end-0 h-3px bg-warning"></div>
            <div class="card-body d-flex flex-column justify-content-between p-6">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="symbol symbol-50px">
                        <div class="symbol-label bg-light-warning rounded-3">
                            <i class="ki-duotone ki-notification-status fs-2x text-warning"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                        </div>
                    </div>
                    <span class="badge badge-light-warning fw-bold fs-8 px-3 py-2">
                        Tinggi / High
                    </span>
                </div>

                <div class="mt-4 mb-3">
                    <div class="fs-4 fw-bold text-gray-900 mb-2">Target Respon: {{ $slaHigh ? ($slaHigh->first_response_time_minutes >= 60 ? round($slaHigh->first_response_time_minutes / 60, 1) . ' Jam' : $slaHigh->first_response_time_minutes . ' Menit') : '2 Jam' }}</div>
                    <div class="fs-7 text-muted">Target Resolusi: <strong class="text-warning">{{ $slaHigh ? ($slaHigh->resolution_time_minutes >= 60 ? round($slaHigh->resolution_time_minutes / 60, 1) . ' Jam' : $slaHigh->resolution_time_minutes . ' Menit') : '8 Jam' }}</strong></div>
                </div>

                <div class="pt-3 border-top border-gray-200 border-opacity-50 d-flex justify-content-between align-items-center">
                    <span class="text-gray-600 fs-8 fw-semibold">Operasional Terganggu</span>
                    <button type="button" class="btn btn-sm btn-light-warning py-1 px-3 fs-8 fw-bold btn-edit-sla" 
                        data-priority="high" 
                        data-priority-name="High (Tinggi)"
                        data-response="{{ $slaHigh?->first_response_time_minutes ?? 120 }}" 
                        data-resolution="{{ $slaHigh?->resolution_time_minutes ?? 480 }}">
                        Konfigurasi
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Medium -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-flush h-100 shadow-sm hover-elevate-up border-0 position-relative overflow-hidden">
            <div class="position-absolute top-0 start-0 end-0 h-3px bg-primary"></div>
            <div class="card-body d-flex flex-column justify-content-between p-6">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="symbol symbol-50px">
                        <div class="symbol-label bg-light-primary rounded-3">
                            <i class="ki-duotone ki-information fs-2x text-primary"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                        </div>
                    </div>
                    <span class="badge badge-light-primary fw-bold fs-8 px-3 py-2">
                        Sedang / Medium
                    </span>
                </div>

                <div class="mt-4 mb-3">
                    <div class="fs-4 fw-bold text-gray-900 mb-2">Target Respon: {{ $slaMedium ? ($slaMedium->first_response_time_minutes >= 60 ? round($slaMedium->first_response_time_minutes / 60, 1) . ' Jam' : $slaMedium->first_response_time_minutes . ' Menit') : '8 Jam' }}</div>
                    <div class="fs-7 text-muted">Target Resolusi: <strong class="text-primary">{{ $slaMedium ? ($slaMedium->resolution_time_minutes >= 60 ? round($slaMedium->resolution_time_minutes / 60, 1) . ' Jam' : $slaMedium->resolution_time_minutes . ' Menit') : '24 Jam' }}</strong></div>
                </div>

                <div class="pt-3 border-top border-gray-200 border-opacity-50 d-flex justify-content-between align-items-center">
                    <span class="text-gray-600 fs-8 fw-semibold">Isu Normal Bisnis</span>
                    <button type="button" class="btn btn-sm btn-light-primary py-1 px-3 fs-8 fw-bold btn-edit-sla" 
                        data-priority="medium" 
                        data-priority-name="Medium (Sedang)"
                        data-response="{{ $slaMedium?->first_response_time_minutes ?? 480 }}" 
                        data-resolution="{{ $slaMedium?->resolution_time_minutes ?? 1440 }}">
                        Konfigurasi
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. Low -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-flush h-100 shadow-sm hover-elevate-up border-0 position-relative overflow-hidden">
            <div class="position-absolute top-0 start-0 end-0 h-3px bg-success"></div>
            <div class="card-body d-flex flex-column justify-content-between p-6">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="symbol symbol-50px">
                        <div class="symbol-label bg-light-success rounded-3">
                            <i class="ki-duotone ki-check fs-2x text-success"></i>
                        </div>
                    </div>
                    <span class="badge badge-light-success fw-bold fs-8 px-3 py-2">
                        Rendah / Low
                    </span>
                </div>

                <div class="mt-4 mb-3">
                    <div class="fs-4 fw-bold text-gray-900 mb-2">Target Respon: {{ $slaLow ? ($slaLow->first_response_time_minutes >= 60 ? round($slaLow->first_response_time_minutes / 60, 1) . ' Jam' : $slaLow->first_response_time_minutes . ' Menit') : '24 Jam' }}</div>
                    <div class="fs-7 text-muted">Target Resolusi: <strong class="text-success">{{ $slaLow ? ($slaLow->resolution_time_minutes >= 60 ? round($slaLow->resolution_time_minutes / 60, 1) . ' Jam' : $slaLow->resolution_time_minutes . ' Menit') : '48 Jam' }}</strong></div>
                </div>

                <div class="pt-3 border-top border-gray-200 border-opacity-50 d-flex justify-content-between align-items-center">
                    <span class="text-gray-600 fs-8 fw-semibold">Permintaan Umum / Info</span>
                    <button type="button" class="btn btn-sm btn-light-success py-1 px-3 fs-8 fw-bold btn-edit-sla" 
                        data-priority="low" 
                        data-priority-name="Low (Rendah)"
                        data-response="{{ $slaLow?->first_response_time_minutes ?? 1440 }}" 
                        data-resolution="{{ $slaLow?->resolution_time_minutes ?? 2880 }}">
                        Konfigurasi
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SLA Matrix Table -->
<div class="card card-flush shadow-sm">
    <div class="card-header align-items-center py-5">
        <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bold fs-4 text-gray-900">Tabel Rincian Matriks SLA</span>
            <span class="text-muted mt-1 fw-semibold fs-7">Aturan batas waktu penanganan tiket untuk {{ $selectedCompany?->name ?? 'Tenant Terpilih' }}</span>
        </h3>
    </div>

    <div class="card-body pt-0">
        <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-6 gy-5">
                <thead>
                    <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                        <th class="min-w-150px">Tingkat Prioritas</th>
                        <th class="min-w-200px">Dampak Bisnis & Operasional</th>
                        <th class="min-w-150px">Waktu Respon Awal</th>
                        <th class="min-w-150px">Waktu Penyelesaian</th>
                        <th class="min-w-120px text-center">Status Enforce</th>
                        <th class="text-end min-w-100px">Aksi</th>
                    </tr>
                </thead>
                <tbody class="fw-semibold text-gray-600">
                    <!-- Row Urgent -->
                    <tr>
                        <td>
                            <span class="badge badge-light-danger fw-bold fs-7">
                                <span class="bullet bullet-dot bg-danger me-2"></span>Urgent
                            </span>
                        </td>
                        <td>
                            <div class="text-gray-900 fw-bold fs-7">Kritis / Pelayanan Utama Terhenti Total</div>
                            <span class="text-muted fs-8">Seluruh pengguna terdampak, membutuhkan respon segera.</span>
                        </td>
                        <td>
                            <div class="text-gray-900 fw-bold fs-7">{{ $slaUrgent?->first_response_time_minutes ?? 30 }} Menit</div>
                            <span class="text-muted fs-8">~{{ round(($slaUrgent?->first_response_time_minutes ?? 30) / 60, 1) }} Jam</span>
                        </td>
                        <td>
                            <div class="text-danger fw-bold fs-7">{{ $slaUrgent?->resolution_time_minutes ?? 120 }} Menit</div>
                            <span class="text-muted fs-8">~{{ round(($slaUrgent?->resolution_time_minutes ?? 120) / 60, 1) }} Jam</span>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-light-success fw-bold">Otomatis Aktif</span>
                        </td>
                        <td class="text-end">
                            <button type="button" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm btn-edit-sla" 
                                data-priority="urgent" 
                                data-priority-name="Urgent (Kritis)"
                                data-response="{{ $slaUrgent?->first_response_time_minutes ?? 30 }}" 
                                data-resolution="{{ $slaUrgent?->resolution_time_minutes ?? 120 }}"
                                title="Ubah Kebijakan">
                                <i class="ki-duotone ki-pencil fs-4"><span class="path1"></span><span class="path2"></span></i>
                            </button>
                        </td>
                    </tr>

                    <!-- Row High -->
                    <tr>
                        <td>
                            <span class="badge badge-light-warning fw-bold fs-7">
                                <span class="bullet bullet-dot bg-warning me-2"></span>High
                            </span>
                        </td>
                        <td>
                            <div class="text-gray-900 fw-bold fs-7">Tinggi / Fungsi Kritis Terganggu Sebagian</div>
                            <span class="text-muted fs-8">Operasional terhambat, ada alternatif sementara.</span>
                        </td>
                        <td>
                            <div class="text-gray-900 fw-bold fs-7">{{ $slaHigh?->first_response_time_minutes ?? 120 }} Menit</div>
                            <span class="text-muted fs-8">~{{ round(($slaHigh?->first_response_time_minutes ?? 120) / 60, 1) }} Jam</span>
                        </td>
                        <td>
                            <div class="text-warning fw-bold fs-7">{{ $slaHigh?->resolution_time_minutes ?? 480 }} Menit</div>
                            <span class="text-muted fs-8">~{{ round(($slaHigh?->resolution_time_minutes ?? 480) / 60, 1) }} Jam</span>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-light-success fw-bold">Otomatis Aktif</span>
                        </td>
                        <td class="text-end">
                            <button type="button" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm btn-edit-sla" 
                                data-priority="high" 
                                data-priority-name="High (Tinggi)"
                                data-response="{{ $slaHigh?->first_response_time_minutes ?? 120 }}" 
                                data-resolution="{{ $slaHigh?->resolution_time_minutes ?? 480 }}"
                                title="Ubah Kebijakan">
                                <i class="ki-duotone ki-pencil fs-4"><span class="path1"></span><span class="path2"></span></i>
                            </button>
                        </td>
                    </tr>

                    <!-- Row Medium -->
                    <tr>
                        <td>
                            <span class="badge badge-light-primary fw-bold fs-7">
                                <span class="bullet bullet-dot bg-primary me-2"></span>Medium
                            </span>
                        </td>
                        <td>
                            <div class="text-gray-900 fw-bold fs-7">Sedang / Masalah Rutin Harian</div>
                            <span class="text-muted fs-8">Pengguna individu terdampak, tidak menghentikan core system.</span>
                        </td>
                        <td>
                            <div class="text-gray-900 fw-bold fs-7">{{ $slaMedium?->first_response_time_minutes ?? 480 }} Menit</div>
                            <span class="text-muted fs-8">~{{ round(($slaMedium?->first_response_time_minutes ?? 480) / 60, 1) }} Jam</span>
                        </td>
                        <td>
                            <div class="text-primary fw-bold fs-7">{{ $slaMedium?->resolution_time_minutes ?? 1440 }} Menit</div>
                            <span class="text-muted fs-8">~{{ round(($slaMedium?->resolution_time_minutes ?? 1440) / 60, 1) }} Jam</span>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-light-success fw-bold">Otomatis Aktif</span>
                        </td>
                        <td class="text-end">
                            <button type="button" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm btn-edit-sla" 
                                data-priority="medium" 
                                data-priority-name="Medium (Sedang)"
                                data-response="{{ $slaMedium?->first_response_time_minutes ?? 480 }}" 
                                data-resolution="{{ $slaMedium?->resolution_time_minutes ?? 1440 }}"
                                title="Ubah Kebijakan">
                                <i class="ki-duotone ki-pencil fs-4"><span class="path1"></span><span class="path2"></span></i>
                            </button>
                        </td>
                    </tr>

                    <!-- Row Low -->
                    <tr>
                        <td>
                            <span class="badge badge-light-success fw-bold fs-7">
                                <span class="bullet bullet-dot bg-success me-2"></span>Low
                            </span>
                        </td>
                        <td>
                            <div class="text-gray-900 fw-bold fs-7">Rendah / Permintaan Informasi & Konsultasi</div>
                            <span class="text-muted fs-8">Pertanyaan fitur, perubahan data minor, atau usulan.</span>
                        </td>
                        <td>
                            <div class="text-gray-900 fw-bold fs-7">{{ $slaLow?->first_response_time_minutes ?? 1440 }} Menit</div>
                            <span class="text-muted fs-8">~{{ round(($slaLow?->first_response_time_minutes ?? 1440) / 60, 1) }} Jam</span>
                        </td>
                        <td>
                            <div class="text-success fw-bold fs-7">{{ $slaLow?->resolution_time_minutes ?? 2880 }} Menit</div>
                            <span class="text-muted fs-8">~{{ round(($slaLow?->resolution_time_minutes ?? 2880) / 60, 1) }} Jam</span>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-light-success fw-bold">Otomatis Aktif</span>
                        </td>
                        <td class="text-end">
                            <button type="button" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm btn-edit-sla" 
                                data-priority="low" 
                                data-priority-name="Low (Rendah)"
                                data-response="{{ $slaLow?->first_response_time_minutes ?? 1440 }}" 
                                data-resolution="{{ $slaLow?->resolution_time_minutes ?? 2880 }}"
                                title="Ubah Kebijakan">
                                <i class="ki-duotone ki-pencil fs-4"><span class="path1"></span><span class="path2"></span></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Include Modal -->
@include('master.sla_policies._modal_edit')
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    const activeCompanyId = '{{ $selectedCompany?->id }}';
    const activeCompanyName = '{{ $selectedCompany?->name }}';

    // Tenant Switcher
    $('#sla_tenant_switcher').on('change', function() {
        const companyId = $(this).val();
        window.location.href = '{{ route("sla-policies.index") }}?company_id=' + companyId;
    });

    // Quick minute setter buttons
    $('.btn-quick-resp').on('click', function() {
        $('#edit_sla_first_response').val($(this).data('min'));
    });

    $('.btn-quick-resol').on('click', function() {
        $('#edit_sla_resolution').val($(this).data('min'));
    });

    // Open Modal Edit SLA
    $(document).on('click', '.btn-edit-sla', function() {
        const priority = $(this).data('priority');
        const priorityName = $(this).data('priority-name');
        const resp = $(this).data('response');
        const resol = $(this).data('resolution');

        $('#edit_sla_priority').val(priority);
        $('#edit_sla_company_id').val(activeCompanyId);
        $('#edit_sla_priority_title').text('Konfigurasi Prioritas ' + priorityName);
        $('#edit_sla_company_title').text('Tenant: ' + activeCompanyName);
        $('#edit_sla_first_response').val(resp);
        $('#edit_sla_resolution').val(resol);

        $('#kt_modal_edit_sla').modal('show');
    });

    // Submit Edit SLA
    $('#form_edit_sla').on('submit', function(e) {
        e.preventDefault();
        const priority = $('#edit_sla_priority').val();
        const companyId = $('#edit_sla_company_id').val();
        const btn = $('#btn_submit_edit_sla');
        btn.attr('data-kt-indicator', 'on').prop('disabled', true);

        const payload = {
            first_response_time_minutes: parseInt($('#edit_sla_first_response').val()),
            resolution_time_minutes: parseInt($('#edit_sla_resolution').val()),
        };

        if (payload.resolution_time_minutes < payload.first_response_time_minutes) {
            btn.removeAttr('data-kt-indicator').prop('disabled', false);
            Swal.fire({
                text: "Target waktu penyelesaian (resolusi) harus lebih besar atau sama dengan waktu respon pertama!",
                icon: "warning",
                buttonsStyling: false,
                confirmButtonText: "Mengerti",
                customClass: { confirmButton: "btn btn-primary" }
            });
            return;
        }

        const url = `{{ url('/api/v1') }}/${companyId}/sla-policies/${priority}`;

        $.ajax({
            url: url,
            type: 'PUT',
            data: JSON.stringify(payload),
            contentType: 'application/json',
            success: function(res) {
                btn.removeAttr('data-kt-indicator').prop('disabled', false);
                $('#kt_modal_edit_sla').modal('hide');

                Swal.fire({
                    text: res.message || "Kebijakan SLA berhasil diperbarui!",
                    icon: "success",
                    buttonsStyling: false,
                    confirmButtonText: "Ok",
                    customClass: { confirmButton: "btn btn-primary" }
                }).then(() => {
                    location.reload();
                });
            },
            error: function(xhr) {
                btn.removeAttr('data-kt-indicator').prop('disabled', false);
                let msg = 'Gagal menyimpan kebijakan SLA.';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                Swal.fire({
                    html: msg,
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: "Coba Lagi",
                    customClass: { confirmButton: "btn btn-danger" }
                });
            }
        });
    });
});
</script>
@endpush
