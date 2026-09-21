@extends('layouts.app')

@section('title', 'Master Perusahaan (Tenant) - SiriusTicketing')
@section('page_title', 'Master Perusahaan')

@section('toolbar_actions')
    <!-- Tombol Tambah Perusahaan -->
    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#kt_modal_add_company">
        <i class="ki-duotone ki-plus fs-3"><span class="path1"></span><span class="path2"></span></i>
        Tambah Perusahaan
    </button>
@endsection

@section('content')
<!-- Metric Summary Cards -->
<div class="row g-5 g-xl-8 mb-5 mb-xl-8">
    <!-- Card 1: Total Perusahaan -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-flush h-100 shadow-sm hover-elevate-up border-0 position-relative overflow-hidden">
            <div class="position-absolute top-0 start-0 end-0 h-3px bg-primary"></div>
            <div class="card-body d-flex flex-column justify-content-between p-6">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="symbol symbol-50px">
                        <div class="symbol-label bg-light-primary rounded-3">
                            <i class="ki-duotone ki-shop fs-2x text-primary"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                        </div>
                    </div>
                    <span class="badge badge-light-primary fw-bold fs-8 px-3 py-2">
                        <i class="ki-duotone ki-abstract-26 fs-8 text-primary me-1"><span class="path1"></span><span class="path2"></span></i>
                        Multi-Tenant
                    </span>
                </div>
                <div class="mt-4 mb-2">
                    <div class="d-flex align-items-baseline">
                        <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{ $stats['total_companies'] }}</span>
                        <span class="fs-7 fw-bold text-gray-500 text-uppercase">Tenant</span>
                    </div>
                    <div class="fw-semibold text-gray-500 fs-7 mt-1">Total Perusahaan Terdaftar</div>
                </div>
                <div class="d-flex align-items-center pt-3 border-top border-gray-200 border-opacity-50">
                    <i class="ki-duotone ki-check-circle fs-6 text-primary me-2"><span class="path1"></span><span class="path2"></span></i>
                    <span class="text-gray-600 fs-8 fw-semibold">Ekosistem helpdesk terpusat</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 2: Tenant Aktif -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-flush h-100 shadow-sm hover-elevate-up border-0 position-relative overflow-hidden">
            <div class="position-absolute top-0 start-0 end-0 h-3px bg-success"></div>
            <div class="card-body d-flex flex-column justify-content-between p-6">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="symbol symbol-50px">
                        <div class="symbol-label bg-light-success rounded-3">
                            <i class="ki-duotone ki-verify fs-2x text-success"><span class="path1"></span><span class="path2"></span></i>
                        </div>
                    </div>
                    <span class="badge badge-light-success fw-bold fs-8 px-3 py-2">
                        <i class="ki-duotone ki-shield-tick fs-8 text-success me-1"><span class="path1"></span><span class="path2"></span></i>
                        Operasional
                    </span>
                </div>
                <div class="mt-4 mb-2">
                    <div class="d-flex align-items-baseline">
                        <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{ $stats['active_companies'] }}</span>
                        <span class="fs-7 fw-bold text-gray-500 text-uppercase">Aktif</span>
                    </div>
                    <div class="fw-semibold text-gray-500 fs-7 mt-1">Status Langganan Aktif</div>
                </div>
                <div class="pt-3 border-top border-gray-200 border-opacity-50">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-gray-500 fs-8 fw-semibold">Rasio Keaktifan</span>
                        <span class="text-success fs-8 fw-bold">{{ $stats['active_percent'] }}%</span>
                    </div>
                    <div class="progress h-4px bg-light-success rounded-pill">
                        <div class="progress-bar bg-success rounded-pill" role="progressbar" style="width: {{ $stats['active_percent'] }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 3: Paket Enterprise -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-flush h-100 shadow-sm hover-elevate-up border-0 position-relative overflow-hidden">
            <div class="position-absolute top-0 start-0 end-0 h-3px bg-warning"></div>
            <div class="card-body d-flex flex-column justify-content-between p-6">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="symbol symbol-50px">
                        <div class="symbol-label bg-light-warning rounded-3">
                            <i class="ki-duotone ki-crown-2 fs-2x text-warning"><span class="path1"></span><span class="path2"></span></i>
                        </div>
                    </div>
                    <span class="badge badge-light-warning fw-bold fs-8 px-3 py-2">
                        <i class="ki-duotone ki-medal-star fs-8 text-warning me-1"><span class="path1"></span><span class="path2"></span></i>
                        Tier Korporasi
                    </span>
                </div>
                <div class="mt-4 mb-2">
                    <div class="d-flex align-items-baseline">
                        <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{ $stats['enterprise_companies'] }}</span>
                        <span class="fs-7 fw-bold text-gray-500 text-uppercase">Enterprise</span>
                    </div>
                    <div class="fw-semibold text-gray-500 fs-7 mt-1">Paket Tertinggi Terpasang</div>
                </div>
                <div class="d-flex align-items-center justify-content-between pt-3 border-top border-gray-200 border-opacity-50">
                    <span class="text-gray-600 fs-8 fw-semibold">Fitur SLA & CMDB Penuh</span>
                    <span class="badge badge-light-warning fw-bold fs-8">Dedicated SLA</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 4: Total Pengguna Lintas Tenant -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-flush h-100 shadow-sm hover-elevate-up border-0 position-relative overflow-hidden">
            <div class="position-absolute top-0 start-0 end-0 h-3px bg-info"></div>
            <div class="card-body d-flex flex-column justify-content-between p-6">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="symbol symbol-50px">
                        <div class="symbol-label bg-light-info rounded-3">
                            <i class="ki-duotone ki-people fs-2x text-info"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                        </div>
                    </div>
                    <span class="badge badge-light-info fw-bold fs-8 px-3 py-2">
                        <i class="ki-duotone ki-profile-user fs-8 text-info me-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                        Populasi
                    </span>
                </div>
                <div class="mt-4 mb-2">
                    <div class="d-flex align-items-baseline">
                        <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{ $stats['total_users'] }}</span>
                        <span class="fs-7 fw-bold text-gray-500 text-uppercase">Akun</span>
                    </div>
                    <div class="fw-semibold text-gray-500 fs-7 mt-1">Total Pengguna Lintas Tenant</div>
                </div>
                <div class="d-flex align-items-center justify-content-between pt-3 border-top border-gray-200 border-opacity-50">
                    <span class="text-gray-600 fs-8 fw-semibold">Superadmin, Agen & Requester</span>
                    <span class="badge badge-light-info fw-bold fs-8">Platform-wide</span>
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
                <input type="text" id="table_search_company" class="form-control form-control-solid w-250px ps-12" placeholder="Cari nama, slug, domain..." value="{{ request('search') }}" />
            </div>
        </div>

        <!-- Filter Toolbar -->
        <div class="card-toolbar d-flex align-items-center gap-3">
            <!-- Filter Paket -->
            <div class="w-150px">
                <select id="filter_plan" class="form-select form-select-solid" data-control="select2" data-hide-search="true">
                    <option value="">Semua Paket</option>
                    <option value="starter" {{ request('plan') === 'starter' ? 'selected' : '' }}>Starter</option>
                    <option value="professional" {{ request('plan') === 'professional' ? 'selected' : '' }}>Professional</option>
                    <option value="enterprise" {{ request('plan') === 'enterprise' ? 'selected' : '' }}>Enterprise</option>
                </select>
            </div>
            <!-- Filter Status -->
            <div class="w-150px">
                <select id="filter_status" class="form-select form-select-solid" data-control="select2" data-hide-search="true">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="trial" {{ request('status') === 'trial' ? 'selected' : '' }}>Trial</option>
                    <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>
            </div>
            @if(request('search') || request('plan') || request('status'))
                <a href="{{ route('companies.index') }}" class="btn btn-icon btn-light-danger btn-sm" title="Reset Filter">
                    <i class="ki-duotone ki-cross fs-2"><span class="path1"></span><span class="path2"></span></i>
                </a>
            @endif
        </div>
    </div>

    <!-- Card Body -->
    <div class="card-body pt-0">
        <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_companies">
                <thead>
                    <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                        <th class="w-50px">No</th>
                        <th class="min-w-200px">Organisasi / Tenant</th>
                        <th class="min-w-120px">Paket</th>
                        <th class="min-w-150px">Domain</th>
                        <th class="min-w-200px">Statistik Terpasang</th>
                        <th class="min-w-100px text-center">Status</th>
                        <th class="text-end min-w-120px">Aksi</th>
                    </tr>
                </thead>
                <tbody class="fw-semibold text-gray-600">
                    @forelse($companies as $index => $comp)
                    <tr>
                        <td>{{ $companies->firstItem() + $index }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-40px me-3">
                                    <span class="symbol-label bg-light-primary text-primary fw-bold fs-4">
                                        {{ strtoupper(substr($comp->name, 0, 1)) }}
                                    </span>
                                </div>
                                <div class="d-flex flex-column">
                                    <span class="text-gray-900 fw-bold text-hover-primary fs-6">{{ $comp->name }}</span>
                                    <span class="text-muted fs-7">Slug: <code>{{ $comp->slug }}</code></span>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($comp->plan?->value === 'enterprise')
                                <span class="badge badge-light-warning fw-bold"><i class="ki-duotone ki-crown-2 fs-8 text-warning me-1"><span class="path1"></span><span class="path2"></span></i>Enterprise</span>
                            @elseif($comp->plan?->value === 'professional')
                                <span class="badge badge-light-primary fw-bold">Professional</span>
                            @else
                                <span class="badge badge-light fw-bold text-gray-700">Starter</span>
                            @endif
                        </td>
                        <td>
                            @if($comp->domain)
                                <span class="text-gray-800 fs-7"><i class="ki-duotone ki-geolocation fs-7 text-muted me-1"><span class="path1"></span><span class="path2"></span></i>{{ $comp->domain }}</span>
                            @else
                                <span class="text-muted fs-8 fst-italic">Default Sirius</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-2 flex-wrap">
                                <span class="badge badge-light-primary fs-8" title="Total Departemen">
                                    <i class="ki-duotone ki-briefcase fs-9 text-primary me-1"><span class="path1"></span><span class="path2"></span></i>{{ $comp->departments_count }} Dept
                                </span>
                                <span class="badge badge-light-info fs-8" title="Total Pengguna">
                                    <i class="ki-duotone ki-profile-user fs-9 text-info me-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>{{ $comp->users_count }} User
                                </span>
                                <span class="badge badge-light-success fs-8" title="Total Aset">
                                    <i class="ki-duotone ki-devices fs-9 text-success me-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>{{ $comp->assets_count }} Aset
                                </span>
                                <span class="badge badge-light-warning fs-8" title="Total Tiket">
                                    <i class="ki-duotone ki-message-text-2 fs-9 text-warning me-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>{{ $comp->tickets_count }} Tiket
                                </span>
                            </div>
                        </td>
                        <td class="text-center">
                            @if($comp->status?->value === 'active')
                                <span class="badge badge-light-success fw-bold"><span class="bullet bullet-dot bg-success me-1"></span>Active</span>
                            @elseif($comp->status?->value === 'trial')
                                <span class="badge badge-light-warning fw-bold"><span class="bullet bullet-dot bg-warning me-1"></span>Trial</span>
                            @else
                                <span class="badge badge-light-danger fw-bold"><span class="bullet bullet-dot bg-danger me-1"></span>Suspended</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end flex-shrink-0">
                                <button type="button" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1 btn-edit-company" data-id="{{ $comp->id }}" title="Edit Identitas">
                                    <i class="ki-duotone ki-pencil fs-4"><span class="path1"></span><span class="path2"></span></i>
                                </button>
                                <button type="button" class="btn btn-icon btn-bg-light btn-active-color-warning btn-sm me-1 btn-status-company" data-id="{{ $comp->id }}" data-name="{{ $comp->name }}" data-status="{{ $comp->status?->value }}" title="Ubah Status">
                                    <i class="ki-duotone ki-shield-search fs-4"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                                </button>
                                <button type="button" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm btn-delete-company" data-id="{{ $comp->id }}" data-name="{{ $comp->name }}" title="Hapus Perusahaan">
                                    <i class="ki-duotone ki-trash fs-4"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-10 text-muted">
                            <i class="ki-duotone ki-information fs-3x text-muted mb-2 d-block"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                            Belum ada data perusahaan tenant. Silakan tambah perusahaan baru.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center flex-wrap pt-4">
            <div class="fs-7 text-muted">
                Menampilkan {{ $companies->firstItem() ?? 0 }} sampai {{ $companies->lastItem() ?? 0 }} dari {{ $companies->total() }} tenant
            </div>
            <div>
                {{ $companies->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

<!-- Include Modals -->
@include('master.companies._modal_create')
@include('master.companies._modal_edit')
@include('master.companies._modal_status')
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    const apiBaseUrl = '{{ url("/api/v1/superadmin/companies") }}';

    $('#add_company_name').on('input', function() {
        const slug = $(this).val().toLowerCase()
            .replace(/[^a-z0-9 -]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-');
        $('#add_company_slug').val(slug);
    });

    // 1. Filter Handling
    function applyFilters() {
        const search = $('#table_search_company').val();
        const plan = $('#filter_plan').val();
        const status = $('#filter_status').val();

        let params = new URLSearchParams();
        if (search) params.set('search', search);
        if (plan) params.set('plan', plan);
        if (status) params.set('status', status);

        window.location.href = '{{ route("companies.index") }}?' + params.toString();
    }

    $('#filter_plan, #filter_status').on('change', function() {
        applyFilters();
    });

    $('#table_search_company').on('keypress', function(e) {
        if (e.which === 13) {
            applyFilters();
        }
    });

    // 2. Submit Tambah Perusahaan
    $('#form_add_company').on('submit', function(e) {
        e.preventDefault();
        const btn = $('#btn_submit_add_company');
        btn.attr('data-kt-indicator', 'on').prop('disabled', true);

        const payload = {
            name: $('#add_company_name').val(),
            slug: $('#add_company_slug').val(),
            plan: $('#add_company_plan').val(),
            domain: $('#add_company_domain').val() || null,
            admin_name: $('#add_admin_name').val(),
            admin_email: $('#add_admin_email').val(),
            admin_password: $('#add_admin_password').val(),
        };

        $.ajax({
            url: apiBaseUrl,
            type: 'POST',
            data: JSON.stringify(payload),
            contentType: 'application/json',
            success: function(res) {
                btn.removeAttr('data-kt-indicator').prop('disabled', false);
                $('#kt_modal_add_company').modal('hide');
                $('#form_add_company')[0].reset();

                Swal.fire({
                    text: res.message || "Tenant dan akun admin berhasil didaftarkan!",
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
                let msg = 'Terjadi kesalahan saat pendaftaran tenant.';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                Swal.fire({
                    html: msg,
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: "Perbaiki",
                    customClass: { confirmButton: "btn btn-danger" }
                });
            }
        });
    });

    // 3. Modal Edit Identitas Perusahaan
    $(document).on('click', '.btn-edit-company', function() {
        const id = $(this).data('id');
        $.get(`${apiBaseUrl}/${id}`, function(res) {
            const data = res.data;
            $('#edit_company_id').val(data.id);
            $('#edit_company_name').val(data.name);
            $('#edit_company_slug').val(data.slug);
            $('#edit_company_domain').val(data.domain || '');
            $('#edit_company_plan').val(data.plan ? (data.plan.value || data.plan) : 'starter');

            $('#kt_modal_edit_company').modal('show');
        });
    });

    $('#form_edit_company').on('submit', function(e) {
        e.preventDefault();
        const id = $('#edit_company_id').val();
        const btn = $('#btn_submit_edit_company');
        btn.attr('data-kt-indicator', 'on').prop('disabled', true);

        const payload = {
            name: $('#edit_company_name').val(),
            slug: $('#edit_company_slug').val(),
            domain: $('#edit_company_domain').val() || null,
            plan: $('#edit_company_plan').val(),
        };

        $.ajax({
            url: `${apiBaseUrl}/${id}`,
            type: 'PUT',
            data: JSON.stringify(payload),
            contentType: 'application/json',
            success: function(res) {
                btn.removeAttr('data-kt-indicator').prop('disabled', false);
                $('#kt_modal_edit_company').modal('hide');

                Swal.fire({
                    text: res.message || "Data tenant berhasil diperbarui!",
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
                let msg = 'Gagal memperbarui data tenant.';
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

    // 4. Modal Ubah Status Tenant
    $(document).on('click', '.btn-status-company', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        const status = $(this).data('status');

        $('#status_company_id').val(id);
        $('#status_company_name_display').text(name);
        $('#status_company_select').val(status || 'active');

        $('#kt_modal_status_company').modal('show');
    });

    $('#form_status_company').on('submit', function(e) {
        e.preventDefault();
        const id = $('#status_company_id').val();
        const btn = $('#btn_submit_status_company');
        btn.attr('data-kt-indicator', 'on').prop('disabled', true);

        const payload = {
            status: $('#status_company_select').val(),
        };

        $.ajax({
            url: `${apiBaseUrl}/${id}/status`,
            type: 'PATCH',
            data: JSON.stringify(payload),
            contentType: 'application/json',
            success: function(res) {
                btn.removeAttr('data-kt-indicator').prop('disabled', false);
                $('#kt_modal_status_company').modal('hide');

                Swal.fire({
                    text: res.message || "Status tenant berhasil diperbarui!",
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
                Swal.fire({
                    text: xhr.responseJSON?.message || "Gagal mengubah status.",
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: "Tutup",
                    customClass: { confirmButton: "btn btn-danger" }
                });
            }
        });
    });

    // 5. Hapus Perusahaan
    $(document).on('click', '.btn-delete-company', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');

        Swal.fire({
            text: `Yakin ingin menghapus perusahaan "${name}"? Seluruh data yang belum berstatus aman akan terhapus.`,
            icon: "warning",
            showCancelButton: true,
            buttonsStyling: false,
            confirmButtonText: "Ya, Hapus!",
            cancelButtonText: "Batal",
            customClass: {
                confirmButton: "btn btn-danger",
                cancelButton: "btn btn-active-light"
            }
        }).then(function(result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: `${apiBaseUrl}/${id}`,
                    type: 'DELETE',
                    success: function(res) {
                        Swal.fire({
                            text: res.message || "Perusahaan berhasil dihapus.",
                            icon: "success",
                            buttonsStyling: false,
                            confirmButtonText: "Ok",
                            customClass: { confirmButton: "btn btn-primary" }
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            text: xhr.responseJSON?.message || "Gagal menghapus perusahaan.",
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
});
</script>
@endpush
