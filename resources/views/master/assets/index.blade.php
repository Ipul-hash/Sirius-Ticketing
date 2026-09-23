@extends('layouts.app')

@section('title', 'Master Inventaris Aset (CMDB) - SiriusTicketing')
@section('page_title', 'Master Aset Inventaris')

@section('toolbar_actions')
    <!-- Tombol Tambah Aset -->
    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#kt_modal_add_asset">
        <i class="ki-duotone ki-plus fs-3"><span class="path1"></span><span class="path2"></span></i>
        Tambah Aset
    </button>
@endsection

@section('content')
<!-- Metric Summary Cards -->
<div class="row g-5 g-xl-8 mb-5 mb-xl-8">
    <!-- Card 1: Total Aset -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-flush h-100 shadow-sm hover-elevate-up border-0 position-relative overflow-hidden">
            <div class="position-absolute top-0 start-0 end-0 h-3px bg-primary"></div>
            <div class="card-body d-flex flex-column justify-content-between p-6">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="symbol symbol-50px">
                        <div class="symbol-label bg-light-primary rounded-3">
                            <i class="ki-duotone ki-screen fs-2x text-primary"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                        </div>
                    </div>
                    <span class="badge badge-light-primary fw-bold fs-8 px-3 py-2">
                        <i class="ki-duotone ki-abstract-26 fs-8 text-primary me-1"><span class="path1"></span><span class="path2"></span></i>
                        CMDB
                    </span>
                </div>
                <div class="mt-4 mb-2">
                    <div class="d-flex align-items-baseline">
                        <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{ $stats['total_assets'] }}</span>
                        <span class="fs-7 fw-bold text-gray-500 text-uppercase">Perangkat</span>
                    </div>
                    <div class="fw-semibold text-gray-500 fs-7 mt-1">Total Aset Terdata</div>
                </div>
                <div class="d-flex align-items-center pt-3 border-top border-gray-200 border-opacity-50">
                    <i class="ki-duotone ki-shield-tick fs-6 text-primary me-2"><span class="path1"></span><span class="path2"></span></i>
                    <span class="text-gray-600 fs-8 fw-semibold">Inventaris terdaftar di sistem</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 2: Aset In Use -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-flush h-100 shadow-sm hover-elevate-up border-0 position-relative overflow-hidden">
            <div class="position-absolute top-0 start-0 end-0 h-3px bg-success"></div>
            <div class="card-body d-flex flex-column justify-content-between p-6">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="symbol symbol-50px">
                        <div class="symbol-label bg-light-success rounded-3">
                            <i class="ki-duotone ki-profile-user fs-2x text-success"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                        </div>
                    </div>
                    <span class="badge badge-light-success fw-bold fs-8 px-3 py-2">
                        <span class="bullet bullet-dot bg-success me-1"></span>In Use
                    </span>
                </div>
                <div class="mt-4 mb-2">
                    <div class="d-flex align-items-baseline">
                        <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{ $stats['in_use_assets'] }}</span>
                        <span class="fs-7 fw-bold text-gray-500 text-uppercase">Aktif Digunakan</span>
                    </div>
                    <div class="fw-semibold text-gray-500 fs-7 mt-1">Aset Terdistribusi ke Staf</div>
                </div>
                <div class="pt-3 border-top border-gray-200 border-opacity-50">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-gray-500 fs-8 fw-semibold">Utilisasi Aset</span>
                        <span class="text-success fs-8 fw-bold">{{ $stats['utilization_percent'] }}%</span>
                    </div>
                    <div class="progress h-4px bg-light-success rounded-pill">
                        <div class="progress-bar bg-success rounded-pill" role="progressbar" style="width: {{ $stats['utilization_percent'] }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 3: Aset Ready (Available) -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-flush h-100 shadow-sm hover-elevate-up border-0 position-relative overflow-hidden">
            <div class="position-absolute top-0 start-0 end-0 h-3px bg-info"></div>
            <div class="card-body d-flex flex-column justify-content-between p-6">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="symbol symbol-50px">
                        <div class="symbol-label bg-light-info rounded-3">
                            <i class="ki-duotone ki-archive fs-2x text-info"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                        </div>
                    </div>
                    <span class="badge badge-light-info fw-bold fs-8 px-3 py-2">
                        <i class="ki-duotone ki-archive fs-8 text-info me-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                        Ready Stock
                    </span>
                </div>
                <div class="mt-4 mb-2">
                    <div class="d-flex align-items-baseline">
                        <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{ $stats['available_assets'] }}</span>
                        <span class="fs-7 fw-bold text-gray-500 text-uppercase">Tersedia</span>
                    </div>
                    <div class="fw-semibold text-gray-500 fs-7 mt-1">Siap untuk Diberikan ke Karyawan</div>
                </div>
                <div class="d-flex align-items-center justify-content-between pt-3 border-top border-gray-200 border-opacity-50">
                    <span class="text-gray-600 fs-8 fw-semibold">Stok di gudang IT</span>
                    <span class="badge badge-light-info fw-bold fs-8">Standby</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 4: Dalam Perbaikan (Maintenance) -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-flush h-100 shadow-sm hover-elevate-up border-0 position-relative overflow-hidden">
            <div class="position-absolute top-0 start-0 end-0 h-3px bg-warning"></div>
            <div class="card-body d-flex flex-column justify-content-between p-6">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="symbol symbol-50px">
                        <div class="symbol-label bg-light-warning rounded-3">
                            <i class="ki-duotone ki-wrench fs-2x text-warning"><span class="path1"></span><span class="path2"></span></i>
                        </div>
                    </div>
                    <span class="badge badge-light-warning fw-bold fs-8 px-3 py-2">
                        <i class="ki-duotone ki-wrench fs-8 text-warning me-1"><span class="path1"></span><span class="path2"></span></i>
                        Servis
                    </span>
                </div>
                <div class="mt-4 mb-2">
                    <div class="d-flex align-items-baseline">
                        <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{ $stats['maintenance_assets'] }}</span>
                        <span class="fs-7 fw-bold text-gray-500 text-uppercase">Unit</span>
                    </div>
                    <div class="fw-semibold text-gray-500 fs-7 mt-1">Dalam Masa Perbaikan / Garansi</div>
                </div>
                <div class="d-flex align-items-center justify-content-between pt-3 border-top border-gray-200 border-opacity-50">
                    <span class="text-gray-600 fs-8 fw-semibold">Butuh penanganan teknisi</span>
                    <span class="badge badge-light-warning fw-bold fs-8">Maintenance</span>
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
                <input type="text" id="table_search_asset" class="form-control form-control-solid w-250px ps-12" placeholder="Cari tag, nama, S/N..." value="{{ request('search') }}" />
            </div>
        </div>

        <!-- Filter Toolbar (Single Clean Row) -->
        <div class="card-toolbar d-flex align-items-center gap-3">
            @if(auth()->user()->isSuperadmin())
            <!-- Filter Tenant -->
            <div class="w-180px">
                <select id="filter_company" class="form-select form-select-solid" data-control="select2" data-hide-search="true">
                    <option value="">Semua Tenant</option>
                    @foreach($companies as $comp)
                        <option value="{{ $comp->id }}" {{ request('company_id') == $comp->id ? 'selected' : '' }}>{{ $comp->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif
            <!-- Filter Kategori -->
            <div class="w-140px">
                <select id="filter_category" class="form-select form-select-solid" data-control="select2" data-hide-search="true">
                    <option value="">Semua Kategori</option>
                    <option value="hardware" {{ request('category') === 'hardware' ? 'selected' : '' }}>Hardware</option>
                    <option value="server" {{ request('category') === 'server' ? 'selected' : '' }}>Server</option>
                    <option value="network" {{ request('category') === 'network' ? 'selected' : '' }}>Network</option>
                    <option value="software_license" {{ request('category') === 'software_license' ? 'selected' : '' }}>Lisensi</option>
                </select>
            </div>
            <!-- Filter Status -->
            <div class="w-140px">
                <select id="filter_status" class="form-select form-select-solid" data-control="select2" data-hide-search="true">
                    <option value="">Semua Status</option>
                    <option value="in_use" {{ request('status') === 'in_use' ? 'selected' : '' }}>In Use</option>
                    <option value="available" {{ request('status') === 'available' ? 'selected' : '' }}>Available</option>
                    <option value="maintenance" {{ request('status') === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                    <option value="retired" {{ request('status') === 'retired' ? 'selected' : '' }}>Retired</option>
                </select>
            </div>
            @if(request('search') || request('company_id') || request('category') || request('status'))
                <a href="{{ route('assets.index') }}" class="btn btn-icon btn-light-danger btn-sm" title="Reset Filter">
                    <i class="ki-duotone ki-cross fs-2"><span class="path1"></span><span class="path2"></span></i>
                </a>
            @endif
        </div>
    </div>

    <!-- Card Body -->
    <div class="card-body pt-0">
        <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_assets">
                <thead>
                    <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                        <th class="w-50px">No</th>
                        <th class="min-w-200px">Aset & Tag</th>
                        <th class="min-w-150px">Tenant Perusahaan</th>
                        <th class="min-w-130px">Kategori</th>
                        <th class="min-w-180px">Penugasan (User / Dept)</th>
                        <th class="min-w-110px text-center">Status</th>
                        <th class="min-w-80px text-center">Tiket</th>
                        <th class="text-end min-w-100px">Aksi</th>
                    </tr>
                </thead>
                <tbody class="fw-semibold text-gray-600">
                    @forelse($assets as $index => $asset)
                    <tr>
                        <td>{{ $assets->firstItem() + $index }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-40px me-3">
                                    <span class="symbol-label bg-light-primary text-primary">
                                        @if($asset->category?->value === 'server')
                                            <i class="ki-duotone ki-disk fs-3 text-primary"><span class="path1"></span><span class="path2"></span></i>
                                        @elseif($asset->category?->value === 'network')
                                            <i class="ki-duotone ki-router fs-3 text-primary"><span class="path1"></span><span class="path2"></span></i>
                                        @elseif($asset->category?->value === 'software_license')
                                            <i class="ki-duotone ki-code fs-3 text-primary"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                                        @else
                                            <i class="ki-duotone ki-laptop fs-3 text-primary"><span class="path1"></span><span class="path2"></span></i>
                                        @endif
                                    </span>
                                </div>
                                <div class="d-flex flex-column">
                                    <span class="text-gray-900 fw-bold text-hover-primary fs-6">{{ $asset->name }}</span>
                                    <div class="d-flex align-items-center gap-2 mt-1">
                                        <span class="badge badge-light fw-bold text-gray-700 fs-8">{{ $asset->asset_tag }}</span>
                                        @if($asset->serial_number)
                                            <span class="text-muted fs-8">S/N: {{ $asset->serial_number }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="text-gray-900 fw-semibold">{{ $asset->company?->name ?? '-' }}</span>
                        </td>
                        <td>
                            @if($asset->category?->value === 'server')
                                <span class="badge badge-light-danger fw-bold">Server</span>
                            @elseif($asset->category?->value === 'network')
                                <span class="badge badge-light-warning fw-bold">Network</span>
                            @elseif($asset->category?->value === 'software_license')
                                <span class="badge badge-light-info fw-bold">Software License</span>
                            @else
                                <span class="badge badge-light-primary fw-bold">Hardware</span>
                            @endif
                        </td>
                        <td>
                            @if($asset->assignedUser)
                                <div class="d-flex align-items-center">
                                    <i class="ki-duotone ki-profile-user fs-5 text-info me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                                    <div>
                                        <div class="text-gray-900 fw-bold fs-7">{{ $asset->assignedUser->name }}</div>
                                        <div class="text-muted fs-8">{{ $asset->department?->name ?? 'Tanpa Divisi' }}</div>
                                    </div>
                                </div>
                            @elseif($asset->department)
                                <div class="d-flex align-items-center">
                                    <i class="ki-duotone ki-briefcase fs-5 text-muted me-2"><span class="path1"></span><span class="path2"></span></i>
                                    <span class="text-gray-700 fs-7">{{ $asset->department->name }} (Umum)</span>
                                </div>
                            @else
                                <span class="badge badge-light text-muted fs-8">Belum ditugaskan</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($asset->status?->value === 'in_use')
                                <span class="badge badge-light-success fw-bold"><span class="bullet bullet-dot bg-success me-1"></span>In Use</span>
                            @elseif($asset->status?->value === 'available')
                                <span class="badge badge-light-info fw-bold"><span class="bullet bullet-dot bg-info me-1"></span>Available</span>
                            @elseif($asset->status?->value === 'maintenance')
                                <span class="badge badge-light-warning fw-bold"><span class="bullet bullet-dot bg-warning me-1"></span>Maintenance</span>
                            @else
                                <span class="badge badge-light-danger fw-bold"><span class="bullet bullet-dot bg-danger me-1"></span>Retired</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($asset->tickets_count > 0)
                                <span class="badge badge-light-danger fw-bold" title="Total Tiket Terkait">
                                    <i class="ki-duotone ki-message-text-2 fs-9 text-danger me-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>{{ $asset->tickets_count }}
                                </span>
                            @else
                                <span class="text-muted fs-8">-</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end flex-shrink-0">
                                <button type="button" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1 btn-edit-asset" data-id="{{ $asset->id }}" title="Edit Aset">
                                    <i class="ki-duotone ki-pencil fs-4"><span class="path1"></span><span class="path2"></span></i>
                                </button>
                                <button type="button" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm btn-delete-asset" data-id="{{ $asset->id }}" data-name="{{ $asset->name }}" data-tag="{{ $asset->asset_tag }}" title="Hapus Aset">
                                    <i class="ki-duotone ki-trash fs-4"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-10 text-muted">
                            <i class="ki-duotone ki-devices fs-3x text-muted mb-2 d-block"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                            Belum ada data aset inventaris. Silakan tambah aset baru.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center flex-wrap pt-4">
            <div class="fs-7 text-muted">
                Menampilkan {{ $assets->firstItem() ?? 0 }} sampai {{ $assets->lastItem() ?? 0 }} dari {{ $assets->total() }} aset
            </div>
            <div>
                {{ $assets->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

<!-- Include Modals -->
@include('master.assets._modal_create')
@include('master.assets._modal_edit')
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    const apiBaseUrl = '{{ url("/api/v1/assets") }}';

    // Filter dynamic department & user options based on selected tenant in Modal Add
    $('#add_asset_company_id').on('change', function() {
        const companyId = $(this).val();

        $('#add_asset_department_id option').each(function() {
            const comp = $(this).data('company');
            if (!comp || comp == companyId) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
        $('#add_asset_department_id').val('');

        $('#add_asset_user_id option').each(function() {
            const comp = $(this).data('company');
            if (!comp || comp == companyId) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
        $('#add_asset_user_id').val('');
    });

    // 1. Filter Handling Table
    function applyFilters() {
        const search = $('#table_search_asset').val();
        const company = $('#filter_company').val();
        const category = $('#filter_category').val();
        const status = $('#filter_status').val();

        let params = new URLSearchParams();
        if (search) params.set('search', search);
        if (company) params.set('company_id', company);
        if (category) params.set('category', category);
        if (status) params.set('status', status);

        window.location.href = '{{ route("assets.index") }}?' + params.toString();
    }

    $('#filter_company, #filter_category, #filter_status').on('change', function() {
        applyFilters();
    });

    $('#table_search_asset').on('keypress', function(e) {
        if (e.which === 13) {
            applyFilters();
        }
    });

    // 2. Submit Tambah Aset
    $('#form_add_asset').on('submit', function(e) {
        e.preventDefault();
        const btn = $('#btn_submit_add_asset');
        btn.attr('data-kt-indicator', 'on').prop('disabled', true);

        const payload = {
            company_id: $('#add_asset_company_id').val(),
            name: $('#add_asset_name').val(),
            asset_tag: $('#add_asset_tag').val(),
            serial_number: $('#add_asset_serial_number').val() || null,
            category: $('#add_asset_category').val(),
            status: $('#add_asset_status').val(),
            department_id: $('#add_asset_department_id').val() || null,
            assigned_to_user_id: $('#add_asset_user_id').val() || null,
            notes: $('#add_asset_notes').val() || null,
        };

        $.ajax({
            url: apiBaseUrl,
            type: 'POST',
            data: JSON.stringify(payload),
            contentType: 'application/json',
            success: function(res) {
                btn.removeAttr('data-kt-indicator').prop('disabled', false);
                $('#kt_modal_add_asset').modal('hide');
                $('#form_add_asset')[0].reset();

                Swal.fire({
                    text: res.message || "Aset berhasil ditambahkan ke inventaris!",
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
                let msg = 'Gagal mendaftarkan aset.';
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

    // 3. Modal Edit Aset
    $(document).on('click', '.btn-edit-asset', function() {
        const id = $(this).data('id');
        $.get(`${apiBaseUrl}/${id}`, function(res) {
            const data = res.data;
            $('#edit_asset_id').val(data.id);
            $('#edit_asset_company_id').val(data.company_id);
            $('#edit_asset_company_name_display').text(data.company ? data.company.name : '-');
            $('#edit_asset_name').val(data.name);
            $('#edit_asset_tag').val(data.asset_tag);
            $('#edit_asset_serial_number').val(data.serial_number || '');
            $('#edit_asset_category').val(data.category ? (data.category.value || data.category) : 'hardware');
            $('#edit_asset_status').val(data.status ? (data.status.value || data.status) : 'available');
            $('#edit_asset_department_id').val(data.department_id || '');
            $('#edit_asset_user_id').val(data.assigned_to_user_id || '');
            $('#edit_asset_notes').val(data.notes || '');

            $('#kt_modal_edit_asset').modal('show');
        });
    });

    $('#form_edit_asset').on('submit', function(e) {
        e.preventDefault();
        const id = $('#edit_asset_id').val();
        const btn = $('#btn_submit_edit_asset');
        btn.attr('data-kt-indicator', 'on').prop('disabled', true);

        const payload = {
            company_id: $('#edit_asset_company_id').val(),
            name: $('#edit_asset_name').val(),
            asset_tag: $('#edit_asset_tag').val(),
            serial_number: $('#edit_asset_serial_number').val() || null,
            category: $('#edit_asset_category').val(),
            status: $('#edit_asset_status').val(),
            department_id: $('#edit_asset_department_id').val() || null,
            assigned_to_user_id: $('#edit_asset_user_id').val() || null,
            notes: $('#edit_asset_notes').val() || null,
        };

        $.ajax({
            url: `${apiBaseUrl}/${id}`,
            type: 'PUT',
            data: JSON.stringify(payload),
            contentType: 'application/json',
            success: function(res) {
                btn.removeAttr('data-kt-indicator').prop('disabled', false);
                $('#kt_modal_edit_asset').modal('hide');

                Swal.fire({
                    text: res.message || "Data aset berhasil diperbarui!",
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
                let msg = 'Gagal memperbarui data aset.';
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

    // 4. Hapus Aset
    $(document).on('click', '.btn-delete-asset', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        const tag = $(this).data('tag');

        Swal.fire({
            text: `Yakin ingin menghapus aset "${name}" [${tag}] dari inventaris?`,
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
                            text: res.message || "Aset berhasil dihapus.",
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
                            text: xhr.responseJSON?.message || "Gagal menghapus aset.",
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
