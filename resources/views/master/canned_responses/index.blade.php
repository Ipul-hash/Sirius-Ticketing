@extends('layouts.app')

@section('title', 'Master Canned Responses (Balasan Cepat) - SiriusTicketing')
@section('page_title', 'Master Canned Responses')

@section('toolbar_actions')
    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#kt_modal_add_canned">
        <i class="ki-duotone ki-plus fs-3"><span class="path1"></span><span class="path2"></span></i>
        Tambah Template
    </button>
@endsection

@section('content')
<!-- Metric Summary Cards -->
<div class="row g-5 g-xl-8 mb-5 mb-xl-8">
    <!-- Card 1: Total Template -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-flush h-100 shadow-sm hover-elevate-up border-0 position-relative overflow-hidden">
            <div class="position-absolute top-0 start-0 end-0 h-3px bg-primary"></div>
            <div class="card-body d-flex flex-column justify-content-between p-6">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="symbol symbol-50px">
                        <div class="symbol-label bg-light-primary rounded-3">
                            <i class="ki-duotone ki-message-text-2 fs-2x text-primary"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                        </div>
                    </div>
                    <span class="badge badge-light-primary fw-bold fs-8 px-3 py-2">
                        Macros
                    </span>
                </div>
                <div class="mt-4 mb-2">
                    <div class="d-flex align-items-baseline">
                        <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{ $stats['total_responses'] }}</span>
                        <span class="fs-7 fw-bold text-gray-500 text-uppercase">Template</span>
                    </div>
                    <div class="fw-semibold text-gray-500 fs-7 mt-1">Total Template Balasan</div>
                </div>
                <div class="d-flex align-items-center pt-3 border-top border-gray-200 border-opacity-50">
                    <i class="ki-duotone ki-send fs-6 text-primary me-2"><span class="path1"></span><span class="path2"></span></i>
                    <span class="text-gray-600 fs-8 fw-semibold">Balasan cepat teknisi</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 2: Template Global -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-flush h-100 shadow-sm hover-elevate-up border-0 position-relative overflow-hidden">
            <div class="position-absolute top-0 start-0 end-0 h-3px bg-success"></div>
            <div class="card-body d-flex flex-column justify-content-between p-6">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="symbol symbol-50px">
                        <div class="symbol-label bg-light-success rounded-3">
                            <i class="ki-duotone ki-abstract-26 fs-2x text-success"><span class="path1"></span><span class="path2"></span></i>
                        </div>
                    </div>
                    <span class="badge badge-light-success fw-bold fs-8 px-3 py-2">
                        <span class="bullet bullet-dot bg-success me-1"></span>Lintas Divisi
                    </span>
                </div>
                <div class="mt-4 mb-2">
                    <div class="d-flex align-items-baseline">
                        <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{ $stats['global_responses'] }}</span>
                        <span class="fs-7 fw-bold text-gray-500 text-uppercase">Global</span>
                    </div>
                    <div class="fw-semibold text-gray-500 fs-7 mt-1">Berlaku untuk Semua Tim</div>
                </div>
                <div class="d-flex align-items-center justify-content-between pt-3 border-top border-gray-200 border-opacity-50">
                    <span class="text-gray-600 fs-8 fw-semibold">Standard greeting / closing</span>
                    <span class="badge badge-light-success fw-bold fs-8">General</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 3: Khusus Departemen -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-flush h-100 shadow-sm hover-elevate-up border-0 position-relative overflow-hidden">
            <div class="position-absolute top-0 start-0 end-0 h-3px bg-info"></div>
            <div class="card-body d-flex flex-column justify-content-between p-6">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="symbol symbol-50px">
                        <div class="symbol-label bg-light-info rounded-3">
                            <i class="ki-duotone ki-element-11 fs-2x text-info"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                        </div>
                    </div>
                    <span class="badge badge-light-info fw-bold fs-8 px-3 py-2">
                        Spesifik
                    </span>
                </div>
                <div class="mt-4 mb-2">
                    <div class="d-flex align-items-baseline">
                        <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{ $stats['dept_specific_responses'] }}</span>
                        <span class="fs-7 fw-bold text-gray-500 text-uppercase">Divisi Khusus</span>
                    </div>
                    <div class="fw-semibold text-gray-500 fs-7 mt-1">Dikhususkan per Departemen</div>
                </div>
                <div class="d-flex align-items-center justify-content-between pt-3 border-top border-gray-200 border-opacity-50">
                    <span class="text-gray-600 fs-8 fw-semibold">SOP Teknis Spesifik</span>
                    <span class="badge badge-light-info fw-bold fs-8">Scoped</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 4: Tenant Terlayani -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-flush h-100 shadow-sm hover-elevate-up border-0 position-relative overflow-hidden">
            <div class="position-absolute top-0 start-0 end-0 h-3px bg-warning"></div>
            <div class="card-body d-flex flex-column justify-content-between p-6">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="symbol symbol-50px">
                        <div class="symbol-label bg-light-warning rounded-3">
                            <i class="ki-duotone ki-bank fs-2x text-warning"><span class="path1"></span><span class="path2"></span></i>
                        </div>
                    </div>
                    <span class="badge badge-light-warning fw-bold fs-8 px-3 py-2">
                        Multi-Tenant
                    </span>
                </div>
                <div class="mt-4 mb-2">
                    <div class="d-flex align-items-baseline">
                        <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{ $stats['total_companies_with_responses'] }}</span>
                        <span class="fs-7 fw-bold text-gray-500 text-uppercase">Perusahaan</span>
                    </div>
                    <div class="fw-semibold text-gray-500 fs-7 mt-1">Tenant Memiliki Template</div>
                </div>
                <div class="d-flex align-items-center justify-content-between pt-3 border-top border-gray-200 border-opacity-50">
                    <span class="text-gray-600 fs-8 fw-semibold">Kemandirian template per tenant</span>
                    <span class="badge badge-light-warning fw-bold fs-8">Tenant</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Card & Data Table -->
<div class="card card-flush shadow-sm">
    <!-- Header & Filter Toolbar -->
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <form method="GET" action="{{ url('/canned-responses') }}" class="d-flex align-items-center position-relative my-1">
                <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-4 text-gray-500">
                    <span class="path1"></span><span class="path2"></span>
                </i>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-solid w-250px ps-12" placeholder="Cari shortcut atau judul..." />
                @if(request('company_id')) <input type="hidden" name="company_id" value="{{ request('company_id') }}" /> @endif
                @if(request('department_id')) <input type="hidden" name="department_id" value="{{ request('department_id') }}" /> @endif
            </form>
        </div>

        <div class="card-toolbar">
            <form method="GET" action="{{ url('/canned-responses') }}" id="canned_filter_form" class="d-flex flex-wrap align-items-center gap-3">
                @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}" /> @endif

                @if(auth()->user()->isSuperadmin())
                <!-- Filter Perusahaan -->
                <div class="w-180px">
                    <select name="company_id" id="filter_canned_company_id" class="form-select form-select-solid form-select-sm" onchange="this.form.submit()">
                        <option value="">Semua Perusahaan</option>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>
                                {{ $company->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                <!-- Filter Departemen -->
                <div class="w-200px">
                    <select name="department_id" id="filter_canned_department_id" class="form-select form-select-solid form-select-sm" onchange="this.form.submit()">
                        <option value="">Semua Cakupan</option>
                        <option value="global" {{ request('department_id') === 'global' ? 'selected' : '' }}>Global Saja (Semua Divisi)</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" data-company="{{ $dept->company_id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                @if(request()->hasAny(['search', 'company_id', 'department_id']))
                    <a href="{{ url('/canned-responses') }}" class="btn btn-icon btn-light-danger btn-sm" data-bs-toggle="tooltip" title="Reset Filter">
                        <i class="ki-duotone ki-cross fs-3"><span class="path1"></span><span class="path2"></span></i>
                    </a>
                @endif
            </form>
        </div>
    </div>

    <!-- Table Body -->
    <div class="card-body pt-0">
        <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-6 gy-4">
                <thead>
                    <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                        <th class="w-50px">No</th>
                        <th class="min-w-140px">Shortcut Macro</th>
                        <th class="min-w-180px">Judul Template</th>
                        <th class="min-w-180px">Perusahaan & Divisi</th>
                        <th class="min-w-250px">Pratinjau Pesan</th>
                        <th class="min-w-120px">Diperbarui</th>
                        <th class="text-end min-w-120px pe-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="fw-semibold text-gray-600">
                    @forelse($responses as $res)
                        <tr>
                            <td>
                                <span class="text-muted fs-7">{{ $loop->iteration + ($responses->currentPage() - 1) * $responses->perPage() }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <code class="badge badge-light-primary fw-bold fs-7 px-3 py-2 cursor-pointer btn-copy-shortcut" 
                                          data-shortcut="{{ $res->shortcut }}" data-bs-toggle="tooltip" title="Klik untuk salin shortcut">
                                        {{ $res->shortcut }}
                                    </code>
                                </div>
                            </td>
                            <td>
                                <span class="text-gray-900 fw-bold fs-6 d-block">{{ $res->title }}</span>
                                <span class="text-muted fs-8">ID #{{ $res->id }}</span>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="text-gray-800 fw-semibold fs-7">{{ $res->company->name ?? 'Semua Perusahaan' }}</span>
                                    @if($res->department_id === null)
                                        <span class="badge badge-light-success fw-bold fs-8 px-2 py-1 mt-1 w-fit">
                                            <i class="ki-duotone ki-abstract-26 fs-9 me-1"><span class="path1"></span><span class="path2"></span></i>
                                            Global (Semua Divisi)
                                        </span>
                                    @else
                                        <span class="badge badge-light-info text-gray-700 fs-8 px-2 py-1 mt-1 w-fit">
                                            <i class="ki-duotone ki-element-11 fs-9 me-1"><span class="path1"></span><span class="path2"></span></i>
                                            {{ $res->department->name ?? 'Departemen' }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="text-gray-700 fs-7 text-truncate" style="max-width: 320px;" title="{{ $res->message }}">
                                    {{ Str::limit($res->message, 85) }}
                                </div>
                            </td>
                            <td>
                                <span class="text-muted fs-8">{{ $res->updated_at ? $res->updated_at->format('d M Y, H:i') : '-' }}</span>
                            </td>
                            <td class="text-end pe-3">
                                <div class="d-flex justify-content-end gap-2">
                                    <!-- Preview Button -->
                                    <button type="button" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm btn-preview-canned" 
                                            data-id="{{ $res->id }}" title="Lihat Teks Lengkap">
                                        <i class="ki-duotone ki-eye fs-4"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                                    </button>
                                    <!-- Edit Button -->
                                    <button type="button" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm btn-edit-canned" 
                                            data-id="{{ $res->id }}" title="Edit Template">
                                        <i class="ki-duotone ki-pencil fs-4"><span class="path1"></span><span class="path2"></span></i>
                                    </button>
                                    <!-- Delete Button -->
                                    <button type="button" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm btn-delete-canned" 
                                            data-id="{{ $res->id }}" data-title="{{ $res->title }}" data-shortcut="{{ $res->shortcut }}" title="Hapus Template">
                                        <i class="ki-duotone ki-trash fs-4"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-10">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="ki-duotone ki-message-text-2 fs-3x text-gray-400 mb-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                                    <span class="text-gray-600 fs-5 fw-bold mb-1">Belum Ada Template Balasan Cepat</span>
                                    <span class="text-gray-400 fs-7">Klik tombol "Tambah Template" untuk mempermudah teknisi merespon tiket.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center flex-wrap pt-5">
            <div class="fs-7 fw-semibold text-gray-700">
                Menampilkan {{ $responses->firstItem() ?? 0 }} s/d {{ $responses->lastItem() ?? 0 }} dari {{ $responses->total() }} template balasan
            </div>
            <div>
                {{ $responses->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Include Modals -->
@include('master.canned_responses._modal_create')
@include('master.canned_responses._modal_edit')
@include('master.canned_responses._modal_preview')

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Dynamic department dropdown filter in modal create
    const addCompanySelect = document.getElementById('add_canned_company_id');
    const addDeptSelect = document.getElementById('add_canned_department_id');

    function filterModalAddDepartments() {
        const companyId = addCompanySelect.value;
        Array.from(addDeptSelect.options).forEach(opt => {
            if (opt.value === '') {
                opt.style.display = 'block';
                return;
            }
            const deptCompany = opt.getAttribute('data-company');
            opt.style.display = (!companyId || deptCompany === companyId) ? 'block' : 'none';
        });
        const selected = addDeptSelect.selectedOptions[0];
        if (selected && selected.style.display === 'none') {
            addDeptSelect.value = '';
        }
    }

    if (addCompanySelect && addDeptSelect) {
        addCompanySelect.addEventListener('change', filterModalAddDepartments);
        filterModalAddDepartments();
    }

    // Dynamic department dropdown in filter toolbar
    const filterCompanySelect = document.getElementById('filter_canned_company_id');
    const filterDeptSelect = document.getElementById('filter_canned_department_id');
    if (filterCompanySelect && filterDeptSelect) {
        const selectedCompany = filterCompanySelect.value;
        if (selectedCompany) {
            Array.from(filterDeptSelect.options).forEach(opt => {
                if (opt.value === '' || opt.value === 'global') return;
                const deptCompany = opt.getAttribute('data-company');
                opt.style.display = (deptCompany === selectedCompany) ? 'block' : 'none';
            });
        }
    }

    // Quick copy shortcut click
    document.querySelectorAll('.btn-copy-shortcut').forEach(el => {
        el.addEventListener('click', function () {
            const shortcut = this.getAttribute('data-shortcut');
            navigator.clipboard.writeText(shortcut).then(() => {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: `Shortcut ${shortcut} disalin!`,
                    showConfirmButton: false,
                    timer: 1500
                });
            });
        });
    });

    // Submit Tambah Template
    const formAdd = document.getElementById('form_add_canned');
    const btnSubmitAdd = document.getElementById('btn_submit_add_canned');

    formAdd.addEventListener('submit', function (e) {
        e.preventDefault();

        const companyId = document.getElementById('add_canned_company_id').value;
        const deptId = document.getElementById('add_canned_department_id').value;
        const title = document.getElementById('add_canned_title').value.trim();
        let shortcut = document.getElementById('add_canned_shortcut').value.trim();
        const message = document.getElementById('add_canned_message').value.trim();

        if (!companyId) {
            Swal.fire('Validasi Gagal', 'Silakan pilih perusahaan/tenant.', 'warning');
            return;
        }

        if (!title) {
            Swal.fire('Validasi Gagal', 'Judul template wajib diisi.', 'warning');
            return;
        }

        if (!shortcut) {
            Swal.fire('Validasi Gagal', 'Shortcut macro wajib diisi.', 'warning');
            return;
        }

        if (!message) {
            Swal.fire('Validasi Gagal', 'Isi teks balasan wajib diisi.', 'warning');
            return;
        }

        if (!shortcut.startsWith('/')) {
            shortcut = '/' + shortcut;
        }

        btnSubmitAdd.setAttribute('data-kt-indicator', 'on');
        btnSubmitAdd.disabled = true;

        fetch('/api/v1/canned-responses', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: JSON.stringify({
                company_id: companyId,
                department_id: deptId || null,
                title: title,
                shortcut: shortcut,
                message: message
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
                text: data.message || 'Template balasan cepat berhasil dibuat.',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => {
                location.reload();
            });
        })
        .catch(err => {
            const msg = err.message || (err.errors ? Object.values(err.errors).flat().join('<br>') : 'Terjadi kesalahan sistem.');
            Swal.fire('Gagal Menyimpan', msg, 'error');
        })
        .finally(() => {
            btnSubmitAdd.removeAttribute('data-kt-indicator');
            btnSubmitAdd.disabled = false;
        });
    });

    // Preview Modal Handler
    const previewModalEl = document.getElementById('kt_modal_preview_canned');
    const previewModal = new bootstrap.Modal(previewModalEl);
    let currentPreviewMessage = '';

    document.querySelectorAll('.btn-preview-canned').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.getAttribute('data-id');

            fetch('/api/v1/canned-responses/' + id, {
                headers: { 'Accept': 'application/json' }
            })
            .then(res => res.json())
            .then(res => {
                if (!res.success) {
                    Swal.fire('Error', res.message || 'Data tidak ditemukan.', 'error');
                    return;
                }
                const cr = res.data;
                currentPreviewMessage = cr.message;
                document.getElementById('preview_canned_title').innerText = cr.title;
                document.getElementById('preview_canned_shortcut').innerText = cr.shortcut;
                document.getElementById('preview_canned_company').innerText = cr.company ? cr.company.name : '-';
                document.getElementById('preview_canned_scope').innerText = cr.department ? cr.department.name : 'Global (Semua Divisi)';
                document.getElementById('preview_canned_message').innerText = cr.message;

                previewModal.show();
            })
            .catch(err => {
                Swal.fire('Error', 'Gagal memuat pratinjau template balasan.', 'error');
            });
        });
    });

    // Copy from preview modal
    document.getElementById('btn_copy_preview_canned').addEventListener('click', function () {
        if (!currentPreviewMessage) return;
        navigator.clipboard.writeText(currentPreviewMessage).then(() => {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Teks balasan berhasil disalin!',
                showConfirmButton: false,
                timer: 1500
            });
        });
    });

    // Edit Modal Handler
    const editModalEl = document.getElementById('kt_modal_edit_canned');
    const editModal = new bootstrap.Modal(editModalEl);
    const formEdit = document.getElementById('form_edit_canned');
    const btnSubmitEdit = document.getElementById('btn_submit_edit_canned');

    document.querySelectorAll('.btn-edit-canned').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.getAttribute('data-id');

            fetch('/api/v1/canned-responses/' + id, {
                headers: { 'Accept': 'application/json' }
            })
            .then(res => res.json())
            .then(res => {
                if (!res.success) {
                    Swal.fire('Error', res.message || 'Data tidak ditemukan.', 'error');
                    return;
                }
                const cr = res.data;
                document.getElementById('edit_canned_id').value = cr.id;
                document.getElementById('edit_canned_company_name').value = cr.company ? cr.company.name : 'Unknown';
                document.getElementById('edit_canned_title').value = cr.title;
                document.getElementById('edit_canned_shortcut').value = cr.shortcut;
                document.getElementById('edit_canned_message').value = cr.message;

                // Scope department options to the company
                const editDeptSelect = document.getElementById('edit_canned_department_id');
                Array.from(editDeptSelect.options).forEach(opt => {
                    if (opt.value === '') return;
                    const deptCompany = opt.getAttribute('data-company');
                    opt.style.display = (deptCompany == cr.company_id) ? 'block' : 'none';
                });
                editDeptSelect.value = cr.department_id || '';

                editModal.show();
            })
            .catch(err => {
                Swal.fire('Error', 'Gagal memuat template untuk diedit.', 'error');
            });
        });
    });

    // Submit Edit Form
    formEdit.addEventListener('submit', function (e) {
        e.preventDefault();

        const id = document.getElementById('edit_canned_id').value;
        const deptId = document.getElementById('edit_canned_department_id').value;
        const title = document.getElementById('edit_canned_title').value.trim();
        let shortcut = document.getElementById('edit_canned_shortcut').value.trim();
        const message = document.getElementById('edit_canned_message').value.trim();

        if (!title) {
            Swal.fire('Validasi Gagal', 'Judul template wajib diisi.', 'warning');
            return;
        }

        if (!shortcut) {
            Swal.fire('Validasi Gagal', 'Shortcut macro wajib diisi.', 'warning');
            return;
        }

        if (!shortcut.startsWith('/')) {
            shortcut = '/' + shortcut;
        }

        if (!message) {
            Swal.fire('Validasi Gagal', 'Isi teks balasan wajib diisi.', 'warning');
            return;
        }

        btnSubmitEdit.setAttribute('data-kt-indicator', 'on');
        btnSubmitEdit.disabled = true;

        fetch('/api/v1/canned-responses/' + id, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: JSON.stringify({
                department_id: deptId || null,
                title: title,
                shortcut: shortcut,
                message: message
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
                text: data.message || 'Template balasan cepat berhasil diperbarui.',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => {
                location.reload();
            });
        })
        .catch(err => {
            const msg = err.message || (err.errors ? Object.values(err.errors).flat().join('<br>') : 'Terjadi kesalahan saat memperbarui.');
            Swal.fire('Gagal Memperbarui', msg, 'error');
        })
        .finally(() => {
            btnSubmitEdit.removeAttribute('data-kt-indicator');
            btnSubmitEdit.disabled = false;
        });
    });

    // Delete Action
    document.querySelectorAll('.btn-delete-canned').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const title = this.getAttribute('data-title');
            const shortcut = this.getAttribute('data-shortcut');

            Swal.fire({
                title: 'Hapus Template Balasan?',
                html: `Apakah Anda yakin ingin menghapus template <strong>"${title}"</strong> (<code>${shortcut}</code>)?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#d33'
            }).then(result => {
                if (result.isConfirmed) {
                    fetch('/api/v1/canned-responses/' + id, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                        }
                    })
                    .then(async res => {
                        const data = await res.json();
                        if (!res.ok) throw data;
                        return data;
                    })
                    .then(data => {
                        Swal.fire('Terhapus!', data.message || 'Template berhasil dihapus.', 'success')
                            .then(() => location.reload());
                    })
                    .catch(err => {
                        Swal.fire('Gagal Menghapus', err.message || 'Terjadi kesalahan sistem.', 'error');
                    });
                }
            });
        });
    });
});
</script>
@endpush
