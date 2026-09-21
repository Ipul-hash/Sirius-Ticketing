@extends('layouts.app')

@section('title', 'Master Departemen - SiriusTicketing')
@section('page_title', 'Master Departemen')

@section('toolbar_actions')
    <!-- Tombol Tambah Departemen -->
    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#kt_modal_add_department">
        <i class="ki-duotone ki-plus fs-3"><span class="path1"></span><span class="path2"></span></i>
        Tambah Departemen
    </button>
@endsection

@section('content')
<!-- Metric Summary Cards -->
<div class="row g-5 g-xl-8 mb-5 mb-xl-8">
    <!-- Card 1: Total Departemen -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-flush h-100 shadow-sm hover-elevate-up border-0 position-relative overflow-hidden">
            <!-- Top Accent Line -->
            <div class="position-absolute top-0 start-0 end-0 h-3px bg-primary"></div>

            <div class="card-body d-flex flex-column justify-content-between p-6">
                <!-- Header: Icon & Badge -->
                <div class="d-flex align-items-center justify-content-between">
                    <div class="symbol symbol-50px">
                        <div class="symbol-label bg-light-primary rounded-3">
                            <i class="ki-duotone ki-briefcase fs-2x text-primary">
                                <span class="path1"></span><span class="path2"></span>
                            </i>
                        </div>
                    </div>
                    <span class="badge badge-light-primary fw-bold fs-8 px-3 py-2">
                        <i class="ki-duotone ki-abstract-26 fs-8 text-primary me-1"><span class="path1"></span><span class="path2"></span></i>
                        Unit Kerja
                    </span>
                </div>

                <!-- Body: Value & Label -->
                <div class="mt-4 mb-2">
                    <div class="d-flex align-items-baseline">
                        <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2" id="stat_total_departments">{{ $stats['total_departments'] ?? ($departments->total() ?? 0) }}</span>
                        <span class="fs-7 fw-bold text-gray-500 text-uppercase">Departemen</span>
                    </div>
                    <div class="fw-semibold text-gray-500 fs-7 mt-1">Total Unit Terdaftar</div>
                </div>

                <!-- Footer: Micro detail -->
                <div class="d-flex align-items-center pt-3 border-top border-gray-200 border-opacity-50">
                    <i class="ki-duotone ki-check-circle fs-6 text-primary me-2"><span class="path1"></span><span class="path2"></span></i>
                    <span class="text-gray-600 fs-8 fw-semibold">Terdistribusi lintas tenant</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 2: Departemen Aktif -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-flush h-100 shadow-sm hover-elevate-up border-0 position-relative overflow-hidden">
            <!-- Top Accent Line -->
            <div class="position-absolute top-0 start-0 end-0 h-3px bg-success"></div>

            <div class="card-body d-flex flex-column justify-content-between p-6">
                <!-- Header: Icon & Badge -->
                <div class="d-flex align-items-center justify-content-between">
                    <div class="symbol symbol-50px">
                        <div class="symbol-label bg-light-success rounded-3">
                            <i class="ki-duotone ki-check-circle fs-2x text-success">
                                <span class="path1"></span><span class="path2"></span>
                            </i>
                        </div>
                    </div>
                    <span class="badge badge-light-success fw-bold fs-8 px-3 py-2">
                        <i class="ki-duotone ki-shield-tick fs-8 text-success me-1"><span class="path1"></span><span class="path2"></span></i>
                        Operasional
                    </span>
                </div>

                <!-- Body: Value & Label -->
                <div class="mt-4 mb-2">
                    <div class="d-flex align-items-baseline">
                        <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2" id="stat_active_departments">{{ $stats['active_departments'] ?? 0 }}</span>
                        <span class="fs-7 fw-bold text-gray-500 text-uppercase">Aktif</span>
                    </div>
                    <div class="fw-semibold text-gray-500 fs-7 mt-1">Status Siap Beroperasi</div>
                </div>

                <!-- Footer: Micro Progress Bar -->
                <div class="pt-3 border-top border-gray-200 border-opacity-50">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-gray-500 fs-8 fw-semibold">Rasio Keaktifan</span>
                        <span class="text-success fs-8 fw-bold">{{ $stats['active_percent'] ?? 100 }}%</span>
                    </div>
                    <div class="progress h-4px bg-light-success rounded-pill">
                        <div class="progress-bar bg-success rounded-pill" role="progressbar" style="width: {{ $stats['active_percent'] ?? 100 }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 3: Total Karyawan & Staf -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-flush h-100 shadow-sm hover-elevate-up border-0 position-relative overflow-hidden">
            <!-- Top Accent Line -->
            <div class="position-absolute top-0 start-0 end-0 h-3px bg-info"></div>

            <div class="card-body d-flex flex-column justify-content-between p-6">
                <!-- Header: Icon & Badge -->
                <div class="d-flex align-items-center justify-content-between">
                    <div class="symbol symbol-50px">
                        <div class="symbol-label bg-light-info rounded-3">
                            <i class="ki-duotone ki-people fs-2x text-info">
                                <span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span>
                            </i>
                        </div>
                    </div>
                    <span class="badge badge-light-info fw-bold fs-8 px-3 py-2">
                        <i class="ki-duotone ki-profile-user fs-8 text-info me-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                        Personel
                    </span>
                </div>

                <!-- Body: Value & Label -->
                <div class="mt-4 mb-2">
                    <div class="d-flex align-items-baseline">
                        <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{ $stats['total_users'] ?? $users->count() }}</span>
                        <span class="fs-7 fw-bold text-gray-500 text-uppercase">Orang</span>
                    </div>
                    <div class="fw-semibold text-gray-500 fs-7 mt-1">Total Karyawan & Staf</div>
                </div>

                <!-- Footer: Micro detail -->
                <div class="d-flex align-items-center justify-content-between pt-3 border-top border-gray-200 border-opacity-50">
                    <span class="text-gray-600 fs-8 fw-semibold">
                        <i class="ki-duotone ki-user-tick fs-6 text-info me-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>Rata-rata staf
                    </span>
                    <span class="badge badge-light-info fw-bold fs-8">~{{ $stats['avg_users_per_dept'] ?? 0 }} / Unit</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 4: Tenant Perusahaan -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-flush h-100 shadow-sm hover-elevate-up border-0 position-relative overflow-hidden">
            <!-- Top Accent Line -->
            <div class="position-absolute top-0 start-0 end-0 h-3px bg-warning"></div>

            <div class="card-body d-flex flex-column justify-content-between p-6">
                <!-- Header: Icon & Badge -->
                <div class="d-flex align-items-center justify-content-between">
                    <div class="symbol symbol-50px">
                        <div class="symbol-label bg-light-warning rounded-3">
                            <i class="ki-duotone ki-shop fs-2x text-warning">
                                <span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span>
                            </i>
                        </div>
                    </div>
                    <span class="badge badge-light-warning fw-bold fs-8 px-3 py-2">
                        <i class="ki-duotone ki-shield-search fs-8 text-warning me-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                        Multi-Tenant
                    </span>
                </div>

                <!-- Body: Value & Label -->
                <div class="mt-4 mb-2">
                    <div class="d-flex align-items-baseline">
                        <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{ $stats['total_companies'] ?? $companies->count() }}</span>
                        <span class="fs-7 fw-bold text-gray-500 text-uppercase">Perusahaan</span>
                    </div>
                    <div class="fw-semibold text-gray-500 fs-7 mt-1">Tenant Organisasi Aktif</div>
                </div>

                <!-- Footer: Micro detail -->
                <div class="d-flex align-items-center justify-content-between pt-3 border-top border-gray-200 border-opacity-50">
                    <span class="text-gray-600 fs-8 fw-semibold">
                        <i class="ki-duotone ki-security-user fs-6 text-warning me-1"><span class="path1"></span><span class="path2"></span></i>Multi-Tenant
                    </span>
                    <span class="badge badge-light-warning fw-bold fs-8">Data Terpisah</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Table Card -->
<div class="card card-flush shadow-sm">
    <!-- Card Header -->
    <div class="card-header align-items-center py-5 gap-2 gap-md-5">
        <!-- Search input -->
        <div class="card-title">
            <div class="d-flex align-items-center position-relative my-1">
                <i class="ki-duotone ki-magnifier fs-2 position-absolute ms-4"><span class="path1"></span><span class="path2"></span></i>
                <input type="text" id="table_search_input" class="form-control form-control-solid w-250px ps-12" placeholder="Cari nama departemen..." />
            </div>
        </div>

        <!-- Filter Toolbar -->
        <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
            <!-- Filter Status -->
            <div class="w-100 mw-150px">
                <select id="filter_status" class="form-select form-select-solid" data-control="select2" data-hide-search="true">
                    <option value="">Semua Status</option>
                    <option value="1">Aktif</option>
                    <option value="0">Nonaktif</option>
                </select>
            </div>
            <!-- Filter Perusahaan (Tenant) -->
            <div class="w-100 mw-200px">
                <select id="filter_company" class="form-select form-select-solid" data-control="select2" data-hide-search="true">
                    <option value="">Semua Tenant</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Card Body -->
    <div class="card-body pt-0">
        <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_departments">
                <thead>
                    <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                        <th class="w-50px">No</th>
                        <th class="min-w-200px">Departemen</th>
                        <th class="min-w-150px">Perusahaan / Tenant</th>
                        <th class="min-w-150px">Kepala / Lead</th>
                        <th class="min-w-150px">Statistik</th>
                        <th class="min-w-100px text-center">Status</th>
                        <th class="text-end min-w-100px">Aksi</th>
                    </tr>
                </thead>
                <tbody class="fw-semibold text-gray-600" id="table_body">
                    @forelse($departments as $dept)
                    <tr id="row_dept_{{ $dept->id }}">
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <div class="d-flex flex-column">
                                <span class="text-gray-900 fw-bold fs-6">{{ $dept->name }}</span>
                                <span class="text-muted fs-7">{{ $dept->description ?? 'Tidak ada deskripsi' }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-light-primary fw-semibold">{{ $dept->company->name ?? '-' }}</span>
                        </td>
                        <td>
                            @if($dept->leadUser)
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-30px symbol-circle me-3">
                                        <span class="symbol-label bg-light-info text-info fw-bold">{{ substr($dept->leadUser->name, 0, 1) }}</span>
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="text-gray-800 fw-bold fs-7">{{ $dept->leadUser->name }}</span>
                                        <span class="text-muted fs-8">{{ $dept->leadUser->job_title ?? 'Staf' }}</span>
                                    </div>
                                </div>
                            @else
                                <span class="text-muted fs-7 fst-italic">Belum Ditentukan</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <span class="badge badge-light-secondary text-gray-700" title="Total Anggota">
                                    <i class="ki-duotone ki-people fs-7 me-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                                    {{ $dept->users_count ?? $dept->users()->count() }} User
                                </span>
                                <span class="badge badge-light-warning text-gray-700" title="Total Tiket">
                                    <i class="ki-duotone ki-tablet-text-down fs-7 me-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                                    {{ $dept->tickets_count ?? $dept->tickets()->count() }} Tiket
                                </span>
                            </div>
                        </td>
                        <td class="text-center">
                            @if($dept->is_active)
                                <span class="badge badge-light-success fw-bold px-3 py-2">Aktif</span>
                            @else
                                <span class="badge badge-light-danger fw-bold px-3 py-2">Nonaktif</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end flex-shrink-0">
                                <button type="button" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1 btn-edit" data-id="{{ $dept->id }}" title="Edit Departemen">
                                    <i class="ki-duotone ki-pencil fs-4"><span class="path1"></span><span class="path2"></span></i>
                                </button>
                                <button type="button" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm btn-delete" data-id="{{ $dept->id }}" data-name="{{ $dept->name }}" title="Hapus Departemen">
                                    <i class="ki-duotone ki-trash fs-4"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-10 text-muted">
                            <i class="ki-duotone ki-information fs-3x text-muted mb-2 d-block"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                            Belum ada data departemen. Silakan tambah departemen baru.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Pagination Links -->
        <div class="d-flex justify-content-between align-items-center flex-wrap pt-4">
            <div class="fs-7 text-muted">
                Menampilkan {{ $departments->firstItem() ?? 0 }} sampai {{ $departments->lastItem() ?? 0 }} dari {{ $departments->total() }} data
            </div>
            <div>
                {{ $departments->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

<!-- Include Modals -->
@include('master.departments._modal_create')
@include('master.departments._modal_edit')
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    const apiBaseUrl = '{{ url("/api/v1/departments") }}';

    // 1. Submit Tambah Departemen (AJAX ke API)
    $('#form_add_department').on('submit', function(e) {
        e.preventDefault();

        const btn = $('#btn_submit_add');
        btn.attr('data-kt-indicator', 'on').prop('disabled', true);

        const formData = {
            company_id: $('#add_company_id').val(),
            name: $('#add_name').val(),
            description: $('#add_description').val(),
            lead_user_id: $('#add_lead_user_id').val() || null,
            is_active: $('#add_is_active').is(':checked') ? 1 : 0
        };

        $.ajax({
            url: apiBaseUrl,
            type: 'POST',
            data: JSON.stringify(formData),
            contentType: 'application/json',
            success: function(response) {
                btn.removeAttr('data-kt-indicator').prop('disabled', false);
                $('#kt_modal_add_department').modal('hide');
                $('#form_add_department')[0].reset();

                Swal.fire({
                    text: response.message || "Departemen berhasil ditambahkan!",
                    icon: "success",
                    buttonsStyling: false,
                    confirmButtonText: "Ok, Mengerti",
                    customClass: { confirmButton: "btn btn-primary" }
                }).then(() => {
                    location.reload();
                });
            },
            error: function(xhr) {
                btn.removeAttr('data-kt-indicator').prop('disabled', false);
                let errorMsg = 'Terjadi kesalahan pada sistem.';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMsg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                Swal.fire({
                    html: errorMsg,
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: "Coba Lagi",
                    customClass: { confirmButton: "btn btn-danger" }
                });
            }
        });
    });

    // 2. Buka Modal Edit & Ambil Detail dari API
    $(document).on('click', '.btn-edit', function() {
        const id = $(this).data('id');

        $.ajax({
            url: `${apiBaseUrl}/${id}`,
            type: 'GET',
            success: function(response) {
                if (response.success && response.data) {
                    const data = response.data;
                    $('#edit_department_id').val(data.id);
                    $('#edit_company_name').val(data.company ? data.company.name : '-');
                    $('#edit_name').val(data.name);
                    $('#edit_description').val(data.description || '');
                    $('#edit_lead_user_id').val(data.lead_user_id || '').trigger('change');
                    $('#edit_is_active').prop('checked', data.is_active == 1);

                    $('#kt_modal_edit_department').modal('show');
                }
            },
            error: function() {
                Swal.fire({
                    text: "Gagal mengambil data departemen.",
                    icon: "error",
                    confirmButtonText: "Tutup"
                });
            }
        });
    });

    // 3. Submit Update Departemen (AJAX ke API)
    $('#form_edit_department').on('submit', function(e) {
        e.preventDefault();

        const id = $('#edit_department_id').val();
        const btn = $('#btn_submit_edit');
        btn.attr('data-kt-indicator', 'on').prop('disabled', true);

        const formData = {
            name: $('#edit_name').val(),
            description: $('#edit_description').val(),
            lead_user_id: $('#edit_lead_user_id').val() || null,
            is_active: $('#edit_is_active').is(':checked') ? 1 : 0
        };

        $.ajax({
            url: `${apiBaseUrl}/${id}`,
            type: 'PUT',
            data: JSON.stringify(formData),
            contentType: 'application/json',
            success: function(response) {
                btn.removeAttr('data-kt-indicator').prop('disabled', false);
                $('#kt_modal_edit_department').modal('hide');

                Swal.fire({
                    text: response.message || "Departemen berhasil diperbarui!",
                    icon: "success",
                    buttonsStyling: false,
                    confirmButtonText: "Selesai",
                    customClass: { confirmButton: "btn btn-primary" }
                }).then(() => {
                    location.reload();
                });
            },
            error: function(xhr) {
                btn.removeAttr('data-kt-indicator').prop('disabled', false);
                let errorMsg = 'Gagal memperbarui departemen.';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMsg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                Swal.fire({
                    html: errorMsg,
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: "Coba Lagi",
                    customClass: { confirmButton: "btn btn-danger" }
                });
            }
        });
    });

    // 4. Hapus Departemen (Dengan Proteksi Tiket)
    $(document).on('click', '.btn-delete', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');

        Swal.fire({
            text: `Apakah Anda yakin ingin menghapus departemen "${name}"?`,
            icon: "warning",
            showCancelButton: true,
            buttonsStyling: false,
            confirmButtonText: "Ya, Hapus!",
            cancelButtonText: "Batal",
            customClass: {
                confirmButton: "btn btn-danger",
                cancelButton: "btn btn-light"
            }
        }).then(function(result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: `${apiBaseUrl}/${id}`,
                    type: 'DELETE',
                    success: function(response) {
                        Swal.fire({
                            text: response.message || "Departemen berhasil dihapus!",
                            icon: "success",
                            buttonsStyling: false,
                            confirmButtonText: "Ok",
                            customClass: { confirmButton: "btn btn-primary" }
                        }).then(() => {
                            $(`#row_dept_${id}`).fadeOut(400, function() { $(this).remove(); });
                        });
                    },
                    error: function(xhr) {
                        const errorMsg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Gagal menghapus departemen.';
                        Swal.fire({
                            text: errorMsg,
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "Tutup",
                            customClass: { confirmButton: "btn btn-danger" }
                        });
                    }
                });
            }
        });
    });

    // 5. Filter Search & Status di Frontend
    $('#table_search_input').on('keyup', function() {
        const value = $(this).val().toLowerCase();
        $('#table_body tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });

    $('#filter_status').on('change', function() {
        const val = $(this).val();
        if (val === '') {
            $('#table_body tr').show();
        } else {
            const matchText = val === '1' ? 'aktif' : 'nonaktif';
            $('#table_body tr').filter(function() {
                const statusBadge = $(this).find('td:nth-child(6)').text().trim().toLowerCase();
                $(this).toggle(statusBadge === matchText);
            });
        }
    });

    $('#filter_company').on('change', function() {
        const val = $(this).find('option:selected').text().trim().toLowerCase();
        if ($(this).val() === '') {
            $('#table_body tr').show();
        } else {
            $('#table_body tr').filter(function() {
                const companyCol = $(this).find('td:nth-child(3)').text().trim().toLowerCase();
                $(this).toggle(companyCol.indexOf(val) > -1);
            });
        }
    });
});
</script>
@endpush
