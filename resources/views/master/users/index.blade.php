@extends('layouts.app')

@section('title', 'Manajemen Staf & Pengguna - SiriusTicketing')
@section('page_title', 'Manajemen Staf & Pengguna')

@section('toolbar_actions')
    <!-- Tombol Tambah Pengguna/Staf -->
    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#kt_modal_add_user">
        <i class="ki-duotone ki-plus fs-3"><span class="path1"></span><span class="path2"></span></i>
        Tambah Staf
    </button>
@endsection

@section('content')
<!-- Metric Summary Cards -->
<div class="row g-5 g-xl-8 mb-5 mb-xl-8">
    <!-- Card 1: Total Staf -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-flush h-100 shadow-sm hover-elevate-up border-0 position-relative overflow-hidden">
            <div class="position-absolute top-0 start-0 end-0 h-3px bg-primary"></div>
            <div class="card-body d-flex flex-column justify-content-between p-6">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="symbol symbol-50px">
                        <div class="symbol-label bg-light-primary rounded-3">
                            <i class="ki-duotone ki-profile-user fs-2x text-primary">
                                <span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span>
                            </i>
                        </div>
                    </div>
                    <span class="badge badge-light-primary fw-bold fs-8 px-3 py-2">
                        Total Akun
                    </span>
                </div>
                <div class="mt-4 mb-2">
                    <div class="d-flex align-items-baseline">
                        <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{ $stats['total_staff'] ?? 0 }}</span>
                        <span class="fs-7 fw-bold text-gray-500 text-uppercase">Pengguna</span>
                    </div>
                    <div class="fw-semibold text-gray-500 fs-7 mt-1">Staf Terdaftar</div>
                </div>
                <div class="d-flex align-items-center pt-3 border-top border-gray-200 border-opacity-50">
                    <i class="ki-duotone ki-check-circle fs-6 text-primary me-2"><span class="path1"></span><span class="path2"></span></i>
                    <span class="text-gray-600 fs-8 fw-semibold">
                        @if(auth()->user()->isSuperadmin())
                            Semua tenant organisasi
                        @else
                            {{ auth()->user()->company?->name }}
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 2: Staf Aktif -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-flush h-100 shadow-sm hover-elevate-up border-0 position-relative overflow-hidden">
            <div class="position-absolute top-0 start-0 end-0 h-3px bg-success"></div>
            <div class="card-body d-flex flex-column justify-content-between p-6">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="symbol symbol-50px">
                        <div class="symbol-label bg-light-success rounded-3">
                            <i class="ki-duotone ki-check-circle fs-2x text-success">
                                <span class="path1"></span><span class="path2"></span>
                            </i>
                        </div>
                    </div>
                    <span class="badge badge-light-success fw-bold fs-8 px-3 py-2">
                        Aktif
                    </span>
                </div>
                <div class="mt-4 mb-2">
                    <div class="d-flex align-items-baseline">
                        <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{ $stats['active_staff'] ?? 0 }}</span>
                        <span class="fs-7 fw-bold text-gray-500 text-uppercase">Aktif</span>
                    </div>
                    <div class="fw-semibold text-gray-500 fs-7 mt-1">Dapat Login Sistem</div>
                </div>
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

    <!-- Card 3: Teknisi / Agent Helpdesk -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-flush h-100 shadow-sm hover-elevate-up border-0 position-relative overflow-hidden">
            <div class="position-absolute top-0 start-0 end-0 h-3px bg-info"></div>
            <div class="card-body d-flex flex-column justify-content-between p-6">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="symbol symbol-50px">
                        <div class="symbol-label bg-light-info rounded-3">
                            <i class="ki-duotone ki-support-24 fs-2x text-info">
                                <span class="path1"></span><span class="path2"></span><span class="path3"></span>
                            </i>
                        </div>
                    </div>
                    <span class="badge badge-light-info fw-bold fs-8 px-3 py-2">
                        Helpdesk
                    </span>
                </div>
                <div class="mt-4 mb-2">
                    <div class="d-flex align-items-baseline">
                        <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{ $stats['total_agents'] ?? 0 }}</span>
                        <span class="fs-7 fw-bold text-gray-500 text-uppercase">Agent</span>
                    </div>
                    <div class="fw-semibold text-gray-500 fs-7 mt-1">Petugas Penangan Tiket</div>
                </div>
                <div class="d-flex align-items-center justify-content-between pt-3 border-top border-gray-200 border-opacity-50">
                    <span class="text-gray-600 fs-8 fw-semibold">PIC Tiket Helpdesk</span>
                    <span class="badge badge-light-info fw-bold fs-8">{{ $stats['total_admins'] ?? 0 }} Admin</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 4: Karyawan / Pemohon (Requester) -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-flush h-100 shadow-sm hover-elevate-up border-0 position-relative overflow-hidden">
            <div class="position-absolute top-0 start-0 end-0 h-3px bg-warning"></div>
            <div class="card-body d-flex flex-column justify-content-between p-6">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="symbol symbol-50px">
                        <div class="symbol-label bg-light-warning rounded-3">
                            <i class="ki-duotone ki-people fs-2x text-warning">
                                <span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span>
                            </i>
                        </div>
                    </div>
                    <span class="badge badge-light-warning fw-bold fs-8 px-3 py-2">
                        Pemohon
                    </span>
                </div>
                <div class="mt-4 mb-2">
                    <div class="d-flex align-items-baseline">
                        <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{ $stats['total_requesters'] ?? 0 }}</span>
                        <span class="fs-7 fw-bold text-gray-500 text-uppercase">Requester</span>
                    </div>
                    <div class="fw-semibold text-gray-500 fs-7 mt-1">Pengaju Permintaan Layanan</div>
                </div>
                <div class="d-flex align-items-center justify-content-between pt-3 border-top border-gray-200 border-opacity-50">
                    <span class="text-gray-600 fs-8 fw-semibold">End-User / Karyawan</span>
                    <span class="badge badge-light-warning fw-bold fs-8">Klien Internal</span>
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
                <input type="text" id="table_search_input" class="form-control form-control-solid w-250px ps-12" placeholder="Cari nama, email, jabatan..." />
            </div>
        </div>

        <!-- Filter Toolbar -->
        <div class="card-toolbar d-flex align-items-center gap-3 flex-wrap">
            <!-- Filter Role -->
            <div class="w-150px">
                <select id="filter_role" class="form-select form-select-solid" data-control="select2" data-hide-search="true">
                    <option value="">Semua Peran</option>
                    <option value="company_admin">Company Admin</option>
                    <option value="agent">Agent (Teknisi)</option>
                    <option value="requester">Requester (Pemohon)</option>
                    @if(auth()->user()->isSuperadmin())
                        <option value="superadmin">Superadmin</option>
                    @endif
                </select>
            </div>

            <!-- Filter Status -->
            <div class="w-130px">
                <select id="filter_status" class="form-select form-select-solid" data-control="select2" data-hide-search="true">
                    <option value="">Semua Status</option>
                    <option value="1">Aktif</option>
                    <option value="0">Nonaktif</option>
                </select>
            </div>

            <!-- Filter Departemen -->
            <div class="w-160px">
                <select id="filter_department" class="form-select form-select-solid" data-control="select2" data-hide-search="true">
                    <option value="">Semua Departemen</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>

            @if(auth()->user()->isSuperadmin())
            <!-- Filter Perusahaan (Tenant) -->
            <div class="w-180px">
                <select id="filter_company" class="form-select form-select-solid" data-control="select2" data-hide-search="true">
                    <option value="">Semua Tenant</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif
        </div>
    </div>

    <!-- Card Body -->
    <div class="card-body pt-0">
        <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_users">
                <thead>
                    <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                        <th class="w-50px">No</th>
                        <th class="min-w-220px">Pengguna / Karyawan</th>
                        @if(auth()->user()->isSuperadmin())
                            <th class="min-w-150px">Perusahaan / Tenant</th>
                        @endif
                        <th class="min-w-120px">Peran (Role)</th>
                        <th class="min-w-160px">Departemen & Jabatan</th>
                        <th class="min-w-120px">Kontak</th>
                        <th class="min-w-90px text-center">Status</th>
                        <th class="text-end min-w-100px">Aksi</th>
                    </tr>
                </thead>
                <tbody class="fw-semibold text-gray-600" id="table_body">
                    @forelse($staffUsers as $staf)
                    <tr id="row_user_{{ $staf->id }}">
                        <td>{{ ($staffUsers->currentPage() - 1) * $staffUsers->perPage() + $loop->iteration }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <!-- Avatar or Initial -->
                                <div class="symbol symbol-40px symbol-circle me-3">
                                    @php
                                        $initial = strtoupper(substr($staf->name, 0, 1));
                                        $colors = ['primary', 'success', 'info', 'warning', 'danger'];
                                        $color = $colors[ord($initial) % count($colors)];
                                    @endphp
                                    <div class="symbol-label fs-5 fw-bold bg-light-{{ $color }} text-{{ $color }}">
                                        {{ $initial }}
                                    </div>
                                </div>
                                <div class="d-flex flex-column">
                                    <span class="text-gray-900 fw-bold text-hover-primary mb-1 fs-6">
                                        {{ $staf->name }}
                                        @if(auth()->id() === $staf->id)
                                            <span class="badge badge-light-primary ms-1 fs-9">Anda</span>
                                        @endif
                                    </span>
                                    <span class="text-gray-500 fs-7">{{ $staf->email }}</span>
                                </div>
                            </div>
                        </td>

                        @if(auth()->user()->isSuperadmin())
                            <td>
                                @if($staf->company)
                                    <span class="badge badge-light-dark fw-semibold">
                                        <i class="ki-duotone ki-shop fs-8 me-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                                        {{ $staf->company->name }}
                                    </span>
                                @else
                                    <span class="text-muted fs-7">SaaS Global</span>
                                @endif
                            </td>
                        @endif

                        <td>
                            @php
                                $roleVal = $staf->role instanceof \App\Enums\UserRole ? $staf->role->value : $staf->role;
                            @endphp
                            @if($roleVal === 'superadmin')
                                <span class="badge badge-light-danger fw-bold fs-7">
                                    <i class="ki-duotone ki-shield-star fs-8 text-danger me-1"><span class="path1"></span><span class="path2"></span></i>
                                    Superadmin
                                </span>
                            @elseif($roleVal === 'company_admin')
                                <span class="badge badge-light-warning fw-bold fs-7">
                                    <i class="ki-duotone ki-security-user fs-8 text-warning me-1"><span class="path1"></span><span class="path2"></span></i>
                                    Company Admin
                                </span>
                            @elseif($roleVal === 'agent')
                                <span class="badge badge-light-primary fw-bold fs-7">
                                    <i class="ki-duotone ki-support-24 fs-8 text-primary me-1"><span class="path1"></span><span class="path2"></span></i>
                                    Agent (Teknisi)
                                </span>
                            @else
                                <span class="badge badge-light-info fw-bold fs-7">
                                    <i class="ki-duotone ki-profile-user fs-8 text-info me-1"><span class="path1"></span><span class="path2"></span></i>
                                    Requester
                                </span>
                            @endif
                        </td>

                        <td>
                            <div class="d-flex flex-column">
                                @if($staf->department)
                                    <span class="text-gray-800 fw-semibold fs-7">{{ $staf->department->name }}</span>
                                @else
                                    <span class="text-muted fs-8 fst-italic">Tanpa Departemen</span>
                                @endif
                                <span class="text-gray-500 fs-8">{{ $staf->job_title ?: 'Staf' }}</span>
                            </div>
                        </td>

                        <td>
                            @if($staf->phone)
                                <span class="text-gray-700 fs-7">
                                    <i class="ki-duotone ki-phone fs-8 text-success me-1"><span class="path1"></span><span class="path2"></span></i>
                                    {{ $staf->phone }}
                                </span>
                            @else
                                <span class="text-muted fs-8">-</span>
                            @endif
                        </td>

                        <td class="text-center">
                            @if($staf->is_active)
                                <span class="badge badge-light-success fw-bold fs-8">Aktif</span>
                            @else
                                <span class="badge badge-light-danger fw-bold fs-8">Nonaktif</span>
                            @endif
                        </td>

                        <td class="text-end">
                            <button type="button" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1 btn-edit" data-id="{{ $staf->id }}" title="Edit Pengguna">
                                <i class="ki-duotone ki-pencil fs-4"><span class="path1"></span><span class="path2"></span></i>
                            </button>
                            @if(auth()->id() !== $staf->id)
                                <button type="button" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm btn-delete" data-id="{{ $staf->id }}" data-name="{{ $staf->name }}" title="Hapus / Nonaktifkan">
                                    <i class="ki-duotone ki-trash fs-4"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                                </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ auth()->user()->isSuperadmin() ? 8 : 7 }}" class="text-center py-12">
                            <div class="d-flex flex-column align-items-center">
                                <i class="ki-duotone ki-profile-user fs-4x text-muted mb-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                                <span class="text-gray-500 fw-semibold fs-6">Belum ada staf yang terdaftar.</span>
                                <span class="text-muted fs-7">Klik tombol "Tambah Staf" di atas untuk menambahkan anggota tim Anda.</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center flex-wrap pt-4">
            <div class="fs-7 text-muted">
                Menampilkan {{ $staffUsers->firstItem() ?? 0 }} sampai {{ $staffUsers->lastItem() ?? 0 }} dari {{ $staffUsers->total() }} staf
            </div>
            <div>
                {{ $staffUsers->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

<!-- Include Modals -->
@include('master.users._modal_create')
@include('master.users._modal_edit')
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    const apiBaseUrl = '{{ url("/api/v1/users") }}';

    // 1. Dynamic Filter Departemen saat memilih Tenant (Khusus Superadmin)
    function filterDepartmentsByCompany() {
        const companyId = $('#add_company_id').val();
        $('#add_department_id option').each(function() {
            const comp = $(this).data('company');
            if (!comp || String(comp) === String(companyId)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
        const selected = $('#add_department_id option:selected');
        if (selected.length && selected.css('display') === 'none') {
            $('#add_department_id').val('');
        }
    }
    $('#add_company_id').on('change', filterDepartmentsByCompany);
    filterDepartmentsByCompany();

    // 2. Submit Tambah Pengguna / Staf (AJAX ke API)
    $('#form_add_user').on('submit', function(e) {
        e.preventDefault();

        const btn = $('#btn_submit_add');
        btn.attr('data-kt-indicator', 'on').prop('disabled', true);

        const formData = {
            company_id: $('#add_company_id').val(),
            name: $('#add_name').val(),
            email: $('#add_email').val(),
            password: $('#add_password').val(),
            role: $('#add_role').val(),
            department_id: $('#add_department_id').val() || null,
            job_title: $('#add_job_title').val() || null,
            phone: $('#add_phone').val() || null,
            is_active: $('#add_is_active').is(':checked') ? 1 : 0
        };

        $.ajax({
            url: apiBaseUrl,
            type: 'POST',
            data: JSON.stringify(formData),
            contentType: 'application/json',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                btn.removeAttr('data-kt-indicator').prop('disabled', false);
                $('#kt_modal_add_user').modal('hide');
                $('#form_add_user')[0].reset();

                Swal.fire({
                    text: response.message || "Pengguna staf berhasil ditambahkan!",
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

    // 3. Buka Modal Edit & Ambil Detail dari API
    $(document).on('click', '.btn-edit', function() {
        const id = $(this).data('id');

        $.ajax({
            url: `${apiBaseUrl}/${id}`,
            type: 'GET',
            success: function(response) {
                if (response.success && response.data) {
                    const data = response.data;
                    $('#edit_user_id').val(data.id);
                    $('#edit_company_name').val(data.company ? data.company.name : 'SaaS Global');
                    $('#edit_name').val(data.name);
                    $('#edit_email').val(data.email);
                    $('#edit_password').val('');
                    $('#edit_role').val(data.role.value || data.role).trigger('change');
                    $('#edit_department_id').val(data.department_id || '').trigger('change');
                    $('#edit_job_title').val(data.job_title || '');
                    $('#edit_phone').val(data.phone || '');
                    $('#edit_is_active').prop('checked', data.is_active == 1);

                    $('#kt_modal_edit_user').modal('show');
                }
            },
            error: function(xhr) {
                const msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : "Gagal mengambil data staf.";
                Swal.fire({
                    text: msg,
                    icon: "error",
                    confirmButtonText: "Tutup"
                });
            }
        });
    });

    // 4. Submit Update Pengguna (AJAX ke API)
    $('#form_edit_user').on('submit', function(e) {
        e.preventDefault();

        const id = $('#edit_user_id').val();
        const btn = $('#btn_submit_edit');
        btn.attr('data-kt-indicator', 'on').prop('disabled', true);

        const formData = {
            name: $('#edit_name').val(),
            email: $('#edit_email').val(),
            role: $('#edit_role').val(),
            department_id: $('#edit_department_id').val() || null,
            job_title: $('#edit_job_title').val() || null,
            phone: $('#edit_phone').val() || null,
            is_active: $('#edit_is_active').is(':checked') ? 1 : 0
        };

        const pass = $('#edit_password').val();
        if (pass && pass.trim().length > 0) {
            formData.password = pass;
        }

        $.ajax({
            url: `${apiBaseUrl}/${id}`,
            type: 'PUT',
            data: JSON.stringify(formData),
            contentType: 'application/json',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                btn.removeAttr('data-kt-indicator').prop('disabled', false);
                $('#kt_modal_edit_user').modal('hide');

                Swal.fire({
                    text: response.message || "Data staf berhasil diperbarui!",
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
                let errorMsg = 'Gagal memperbarui data staf.';
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

    // 5. Hapus Pengguna (AJAX ke API)
    $(document).on('click', '.btn-delete', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');

        Swal.fire({
            text: `Apakah Anda yakin ingin menghapus akun staf "${name}"?`,
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
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Swal.fire({
                            text: response.message || "Pengguna berhasil dihapus!",
                            icon: "success",
                            buttonsStyling: false,
                            confirmButtonText: "Ok",
                            customClass: { confirmButton: "btn btn-primary" }
                        }).then(() => {
                            $(`#row_user_${id}`).fadeOut(400, function() { $(this).remove(); });
                        });
                    },
                    error: function(xhr) {
                        const errorMsg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Gagal menghapus akun pengguna.';
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

    // 6. Filter Search, Role, Status di Frontend
    function applyFrontendFilters() {
        const searchVal = $('#table_search_input').val().toLowerCase();
        const roleVal = $('#filter_role').val().toLowerCase();
        const statusVal = $('#filter_status').val();

        $('#table_body tr').filter(function() {
            const rowText = $(this).text().toLowerCase();
            const matchesSearch = !searchVal || rowText.indexOf(searchVal) > -1;

            let matchesRole = true;
            if (roleVal) {
                const roleText = $(this).find('td:nth-child({{ auth()->user()->isSuperadmin() ? 4 : 3 }})').text().toLowerCase();
                if (roleVal === 'company_admin') {
                    matchesRole = roleText.indexOf('company admin') > -1;
                } else if (roleVal === 'agent') {
                    matchesRole = roleText.indexOf('agent') > -1 || roleText.indexOf('teknisi') > -1;
                } else if (roleVal === 'requester') {
                    matchesRole = roleText.indexOf('requester') > -1;
                } else if (roleVal === 'superadmin') {
                    matchesRole = roleText.indexOf('superadmin') > -1;
                }
            }

            let matchesStatus = true;
            if (statusVal !== '') {
                const statusBadge = $(this).find('td:nth-child({{ auth()->user()->isSuperadmin() ? 7 : 6 }})').text().trim().toLowerCase();
                const expected = statusVal === '1' ? 'aktif' : 'nonaktif';
                matchesStatus = statusBadge === expected;
            }

            $(this).toggle(matchesSearch && matchesRole && matchesStatus);
        });
    }

    $('#table_search_input').on('keyup', applyFrontendFilters);
    $('#filter_role, #filter_status').on('change', applyFrontendFilters);
});
</script>
@endpush
