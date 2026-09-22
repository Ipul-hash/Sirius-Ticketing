@extends('layouts.app')

@section('title', 'Master Kategori Tiket - SiriusTicketing')
@section('page_title', 'Master Kategori Tiket')

@section('toolbar_actions')
    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#kt_modal_add_category">
        <i class="ki-duotone ki-plus fs-3"><span class="path1"></span><span class="path2"></span></i>
        Tambah Kategori
    </button>
@endsection

@section('content')
<!-- Metric Summary Cards -->
<div class="row g-5 g-xl-8 mb-5 mb-xl-8">
    <!-- Card 1: Total Kategori -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-flush h-100 shadow-sm hover-elevate-up border-0 position-relative overflow-hidden">
            <div class="position-absolute top-0 start-0 end-0 h-3px bg-primary"></div>
            <div class="card-body d-flex flex-column justify-content-between p-6">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="symbol symbol-50px">
                        <div class="symbol-label bg-light-primary rounded-3">
                            <i class="ki-duotone ki-category fs-2x text-primary"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                        </div>
                    </div>
                    <span class="badge badge-light-primary fw-bold fs-8 px-3 py-2">
                        Klasifikasi
                    </span>
                </div>
                <div class="mt-4 mb-2">
                    <div class="d-flex align-items-baseline">
                        <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{ $stats['total_categories'] }}</span>
                        <span class="fs-7 fw-bold text-gray-500 text-uppercase">Kategori</span>
                    </div>
                    <div class="fw-semibold text-gray-500 fs-7 mt-1">Total Kategori Masalah</div>
                </div>
                <div class="d-flex align-items-center pt-3 border-top border-gray-200 border-opacity-50">
                    <i class="ki-duotone ki-check-circle fs-6 text-primary me-2"><span class="path1"></span><span class="path2"></span></i>
                    <span class="text-gray-600 fs-8 fw-semibold">Struktur masalah terdaftar</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 2: Kategori Aktif -->
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
                        <span class="bullet bullet-dot bg-success me-1"></span>Aktif
                    </span>
                </div>
                <div class="mt-4 mb-2">
                    <div class="d-flex align-items-baseline">
                        <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{ $stats['active_categories'] }}</span>
                        <span class="fs-7 fw-bold text-gray-500 text-uppercase">Aktif</span>
                    </div>
                    <div class="fw-semibold text-gray-500 fs-7 mt-1">Bisa Dipilih Requester</div>
                </div>
                <div class="d-flex align-items-center justify-content-between pt-3 border-top border-gray-200 border-opacity-50">
                    <span class="text-gray-600 fs-8 fw-semibold">Kategori siap pakai</span>
                    <span class="badge badge-light-success fw-bold fs-8">Ready</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 3: Memerlukan Approval -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-flush h-100 shadow-sm hover-elevate-up border-0 position-relative overflow-hidden">
            <div class="position-absolute top-0 start-0 end-0 h-3px bg-warning"></div>
            <div class="card-body d-flex flex-column justify-content-between p-6">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="symbol symbol-50px">
                        <div class="symbol-label bg-light-warning rounded-3">
                            <i class="ki-duotone ki-security-user fs-2x text-warning"><span class="path1"></span><span class="path2"></span></i>
                        </div>
                    </div>
                    <span class="badge badge-light-warning fw-bold fs-8 px-3 py-2">
                        ITIL Workflow
                    </span>
                </div>
                <div class="mt-4 mb-2">
                    <div class="d-flex align-items-baseline">
                        <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{ $stats['approval_categories'] }}</span>
                        <span class="fs-7 fw-bold text-gray-500 text-uppercase">Wajib Approval</span>
                    </div>
                    <div class="fw-semibold text-gray-500 fs-7 mt-1">Perlu Persetujuan Supervisor</div>
                </div>
                <div class="d-flex align-items-center justify-content-between pt-3 border-top border-gray-200 border-opacity-50">
                    <span class="text-gray-600 fs-8 fw-semibold">ServiceNow standard flow</span>
                    <span class="badge badge-light-warning fw-bold fs-8">Controlled</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 4: Total Tiket Terhubung -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-flush h-100 shadow-sm hover-elevate-up border-0 position-relative overflow-hidden">
            <div class="position-absolute top-0 start-0 end-0 h-3px bg-info"></div>
            <div class="card-body d-flex flex-column justify-content-between p-6">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="symbol symbol-50px">
                        <div class="symbol-label bg-light-info rounded-3">
                            <i class="ki-duotone ki-tablet-text-down fs-2x text-info"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                        </div>
                    </div>
                    <span class="badge badge-light-info fw-bold fs-8 px-3 py-2">
                        Helpdesk Traffic
                    </span>
                </div>
                <div class="mt-4 mb-2">
                    <div class="d-flex align-items-baseline">
                        <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{ $stats['total_tickets_linked'] }}</span>
                        <span class="fs-7 fw-bold text-gray-500 text-uppercase">Tiket</span>
                    </div>
                    <div class="fw-semibold text-gray-500 fs-7 mt-1">Tiket Masuk Menggunakan Kategori</div>
                </div>
                <div class="d-flex align-items-center justify-content-between pt-3 border-top border-gray-200 border-opacity-50">
                    <span class="text-gray-600 fs-8 fw-semibold">Distribusi tiket sistem</span>
                    <span class="badge badge-light-info fw-bold fs-8">Volume</span>
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
            <form method="GET" action="{{ url('/categories') }}" class="d-flex align-items-center position-relative my-1">
                <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-4 text-gray-500">
                    <span class="path1"></span><span class="path2"></span>
                </i>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-solid w-250px ps-12" placeholder="Cari nama kategori..." />
                @if(request('company_id')) <input type="hidden" name="company_id" value="{{ request('company_id') }}" /> @endif
                @if(request('department_id')) <input type="hidden" name="department_id" value="{{ request('department_id') }}" /> @endif
                @if(request('default_priority')) <input type="hidden" name="default_priority" value="{{ request('default_priority') }}" /> @endif
                @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}" /> @endif
            </form>
        </div>

        <div class="card-toolbar">
            <form method="GET" action="{{ url('/categories') }}" id="filter_form" class="d-flex flex-wrap align-items-center gap-3">
                @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}" /> @endif

                <!-- Filter Perusahaan -->
                <div class="w-180px">
                    <select name="company_id" id="filter_company_id" class="form-select form-select-solid form-select-sm" onchange="this.form.submit()">
                        <option value="">Semua Perusahaan</option>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>
                                {{ $company->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Departemen -->
                <div class="w-180px">
                    <select name="department_id" id="filter_department_id" class="form-select form-select-solid form-select-sm" onchange="this.form.submit()">
                        <option value="">Semua Departemen</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" data-company="{{ $dept->company_id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Prioritas -->
                <div class="w-140px">
                    <select name="default_priority" class="form-select form-select-solid form-select-sm" onchange="this.form.submit()">
                        <option value="">Semua Prioritas</option>
                        <option value="low" {{ request('default_priority') == 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ request('default_priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ request('default_priority') == 'high' ? 'selected' : '' }}>High</option>
                        <option value="urgent" {{ request('default_priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                    </select>
                </div>

                <!-- Filter Status -->
                <div class="w-130px">
                    <select name="status" class="form-select form-select-solid form-select-sm" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>

                @if(request()->hasAny(['search', 'company_id', 'department_id', 'default_priority', 'status']))
                    <a href="{{ url('/categories') }}" class="btn btn-icon btn-light-danger btn-sm" data-bs-toggle="tooltip" title="Reset Filter">
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
                        <th class="min-w-180px">Kategori Masalah</th>
                        <th class="min-w-180px">Perusahaan & Divisi</th>
                        <th class="min-w-120px">Prioritas Awal</th>
                        <th class="min-w-140px">ITIL Approval</th>
                        <th class="min-w-100px">Status</th>
                        <th class="min-w-80px text-center">Tiket</th>
                        <th class="text-end min-w-100px pe-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="fw-semibold text-gray-600">
                    @forelse($categories as $category)
                        <tr>
                            <td>
                                <span class="text-muted fs-7">{{ $loop->iteration + ($categories->currentPage() - 1) * $categories->perPage() }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-35px me-3">
                                        <div class="symbol-label bg-light-primary text-primary fw-bold">
                                            <i class="ki-duotone ki-category fs-3 text-primary"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                                        </div>
                                    </div>
                                    <div>
                                        <span class="text-gray-900 fw-bold text-hover-primary d-block fs-6">{{ $category->name }}</span>
                                        <span class="text-muted fs-8">ID #{{ $category->id }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="text-gray-800 fw-bold fs-7">{{ $category->company->name ?? 'Semua Perusahaan' }}</span>
                                    <span class="badge badge-light-secondary text-gray-600 fs-8 px-2 py-1 mt-1 w-fit">
                                        <i class="ki-duotone ki-element-11 fs-9 me-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                                        {{ $category->department->name ?? 'Tanpa Divisi' }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                @if($category->default_priority->value === 'urgent')
                                    <span class="badge badge-light-danger fw-bold fs-7 px-3 py-2">
                                        <span class="bullet bullet-dot bg-danger me-1"></span>Urgent
                                    </span>
                                @elseif($category->default_priority->value === 'high')
                                    <span class="badge badge-light-warning fw-bold fs-7 px-3 py-2">
                                        <span class="bullet bullet-dot bg-warning me-1"></span>High
                                    </span>
                                @elseif($category->default_priority->value === 'medium')
                                    <span class="badge badge-light-primary fw-bold fs-7 px-3 py-2">
                                        <span class="bullet bullet-dot bg-primary me-1"></span>Medium
                                    </span>
                                @else
                                    <span class="badge badge-light-secondary text-gray-700 fw-bold fs-7 px-3 py-2">
                                        <span class="bullet bullet-dot bg-gray-500 me-1"></span>Low
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($category->requires_approval)
                                    <span class="badge badge-light-warning fw-bold fs-8 px-3 py-2" data-bs-toggle="tooltip" title="Wajib disetujui atasan">
                                        <i class="ki-duotone ki-security-user fs-7 text-warning me-1"><span class="path1"></span><span class="path2"></span></i>
                                        Wajib Approval
                                    </span>
                                @else
                                    <span class="badge badge-light-success fw-bold fs-8 px-3 py-2" data-bs-toggle="tooltip" title="Langsung dikerjakan agen">
                                        <i class="ki-duotone ki-check-circle fs-7 text-success me-1"><span class="path1"></span><span class="path2"></span></i>
                                        Langsung Proses
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($category->is_active)
                                    <span class="badge badge-light-success fw-bold fs-8">Aktif</span>
                                @else
                                    <span class="badge badge-light-danger fw-bold fs-8">Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge badge-light-info fw-bold fs-7">{{ $category->tickets_count }}</span>
                            </td>
                            <td class="text-end pe-3">
                                <div class="d-flex justify-content-end gap-2">
                                    <button type="button" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm btn-edit-category" 
                                            data-id="{{ $category->id }}" title="Edit Kategori">
                                        <i class="ki-duotone ki-pencil fs-4"><span class="path1"></span><span class="path2"></span></i>
                                    </button>
                                    <button type="button" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm btn-delete-category" 
                                            data-id="{{ $category->id }}" data-name="{{ $category->name }}" data-tickets="{{ $category->tickets_count }}" title="Hapus Kategori">
                                        <i class="ki-duotone ki-trash fs-4"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-10">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="ki-duotone ki-category fs-3x text-gray-400 mb-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                                    <span class="text-gray-600 fs-5 fw-bold mb-1">Belum Ada Kategori Tiket</span>
                                    <span class="text-gray-400 fs-7">Klik tombol "Tambah Kategori" di atas untuk mendaftarkan jenis masalah baru.</span>
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
                Menampilkan {{ $categories->firstItem() ?? 0 }} s/d {{ $categories->lastItem() ?? 0 }} dari {{ $categories->total() }} kategori
            </div>
            <div>
                {{ $categories->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Include Modals -->
@include('master.categories._modal_create')
@include('master.categories._modal_edit')

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Dynamic department dropdown filter in modal create
    const addCompanySelect = document.getElementById('add_category_company_id');
    const addDeptSelect = document.getElementById('add_category_department_id');

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
        // Jika opsi yang terpilih sebelumnya tersembunyi, reset ke default
        const selected = addDeptSelect.selectedOptions[0];
        if (selected && selected.style.display === 'none') {
            addDeptSelect.value = '';
        }
    }

    if (addCompanySelect && addDeptSelect) {
        addCompanySelect.addEventListener('change', filterModalAddDepartments);
        filterModalAddDepartments();
    }

    // Dynamic department filter in toolbar
    const filterCompanySelect = document.getElementById('filter_company_id');
    const filterDeptSelect = document.getElementById('filter_department_id');
    if (filterCompanySelect && filterDeptSelect) {
        const selectedCompany = filterCompanySelect.value;
        if (selectedCompany) {
            Array.from(filterDeptSelect.options).forEach(opt => {
                if (opt.value === '') return;
                const deptCompany = opt.getAttribute('data-company');
                opt.style.display = (deptCompany === selectedCompany) ? 'block' : 'none';
            });
        }
    }

    // Submit Tambah Kategori
    const formAdd = document.getElementById('form_add_category');
    const btnSubmitAdd = document.getElementById('btn_submit_add_category');

    formAdd.addEventListener('submit', function (e) {
        e.preventDefault();

        const companyId = document.getElementById('add_category_company_id').value;
        const deptId = document.getElementById('add_category_department_id').value;
        const name = document.getElementById('add_category_name').value.trim();
        const priority = document.getElementById('add_category_default_priority').value;
        const reqApproval = document.getElementById('add_category_requires_approval').checked;
        const isActive = document.getElementById('add_category_is_active').checked;

        if (!companyId) {
            Swal.fire('Validasi Gagal', 'Silakan pilih perusahaan/tenant terlebih dahulu.', 'warning');
            return;
        }

        if (!deptId) {
            Swal.fire('Validasi Gagal', 'Silakan pilih departemen penanggung jawab.', 'warning');
            return;
        }

        if (!name) {
            Swal.fire('Validasi Gagal', 'Nama kategori tiket wajib diisi.', 'warning');
            return;
        }

        btnSubmitAdd.setAttribute('data-kt-indicator', 'on');
        btnSubmitAdd.disabled = true;

        fetch('/api/v1/categories', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: JSON.stringify({
                company_id: companyId,
                department_id: deptId,
                name: name,
                default_priority: priority,
                requires_approval: reqApproval,
                is_active: isActive
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
                text: data.message || 'Kategori tiket berhasil ditambahkan.',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => {
                location.reload();
            });
        })
        .catch(err => {
            const message = err.message || (err.errors ? Object.values(err.errors).flat().join('<br>') : 'Terjadi kesalahan pada server.');
            Swal.fire('Gagal Menyimpan', message, 'error');
        })
        .finally(() => {
            btnSubmitAdd.removeAttribute('data-kt-indicator');
            btnSubmitAdd.disabled = false;
        });
    });

    // Load Data Edit Modal
    const editModalEl = document.getElementById('kt_modal_edit_category');
    const editModal = new bootstrap.Modal(editModalEl);
    const formEdit = document.getElementById('form_edit_category');
    const btnSubmitEdit = document.getElementById('btn_submit_edit_category');

    document.querySelectorAll('.btn-edit-category').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.getAttribute('data-id');

            fetch('/api/v1/categories/' + id, {
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(res => {
                if (!res.success) {
                    Swal.fire('Error', res.message || 'Data tidak ditemukan', 'error');
                    return;
                }
                const cat = res.data;
                document.getElementById('edit_category_id').value = cat.id;
                document.getElementById('edit_category_company_name').value = cat.company ? cat.company.name : 'Unknown';
                document.getElementById('edit_category_name').value = cat.name;
                document.getElementById('edit_category_default_priority').value = typeof cat.default_priority === 'object' ? cat.default_priority.value : cat.default_priority;
                document.getElementById('edit_category_requires_approval').checked = Boolean(cat.requires_approval);
                document.getElementById('edit_category_is_active').checked = Boolean(cat.is_active);

                // Scope department options to the company
                const editDeptSelect = document.getElementById('edit_category_department_id');
                Array.from(editDeptSelect.options).forEach(opt => {
                    if (opt.value === '') return;
                    const deptCompany = opt.getAttribute('data-company');
                    opt.style.display = (deptCompany == cat.company_id) ? 'block' : 'none';
                });
                editDeptSelect.value = cat.department_id;

                editModal.show();
            })
            .catch(err => {
                Swal.fire('Error', 'Gagal memuat data kategori tiket.', 'error');
            });
        });
    });

    // Submit Update Kategori
    formEdit.addEventListener('submit', function (e) {
        e.preventDefault();

        const id = document.getElementById('edit_category_id').value;
        const deptId = document.getElementById('edit_category_department_id').value;
        const name = document.getElementById('edit_category_name').value.trim();
        const priority = document.getElementById('edit_category_default_priority').value;
        const reqApproval = document.getElementById('edit_category_requires_approval').checked;
        const isActive = document.getElementById('edit_category_is_active').checked;

        if (!deptId) {
            Swal.fire('Validasi Gagal', 'Silakan pilih departemen penanggung jawab.', 'warning');
            return;
        }

        if (!name) {
            Swal.fire('Validasi Gagal', 'Nama kategori tiket wajib diisi.', 'warning');
            return;
        }

        btnSubmitEdit.setAttribute('data-kt-indicator', 'on');
        btnSubmitEdit.disabled = true;

        fetch('/api/v1/categories/' + id, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: JSON.stringify({
                department_id: deptId,
                name: name,
                default_priority: priority,
                requires_approval: reqApproval,
                is_active: isActive
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
                text: data.message || 'Kategori tiket berhasil diperbarui.',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => {
                location.reload();
            });
        })
        .catch(err => {
            const message = err.message || (err.errors ? Object.values(err.errors).flat().join('<br>') : 'Terjadi kesalahan saat memperbarui.');
            Swal.fire('Gagal Memperbarui', message, 'error');
        })
        .finally(() => {
            btnSubmitEdit.removeAttribute('data-kt-indicator');
            btnSubmitEdit.disabled = false;
        });
    });

    // Delete / Deactivate Action
    document.querySelectorAll('.btn-delete-category').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const ticketCount = parseInt(this.getAttribute('data-tickets') || '0', 10);

            if (ticketCount > 0) {
                Swal.fire({
                    title: 'Tidak Dapat Dihapus Langsung',
                    html: `Kategori <strong>"${name}"</strong> memiliki <strong>${ticketCount} tiket terkait</strong>.<br>Kategori tidak boleh dihapus demi menjaga integritas riwayat tiket.<br><br>Apakah Anda ingin <strong>menonaktifkan (Deactivate)</strong> kategori ini agar tidak bisa dipilih lagi?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Nonaktifkan',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#f1416c'
                }).then(result => {
                    if (result.isConfirmed) {
                        fetch('/api/v1/categories/' + id, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                            },
                            body: JSON.stringify({ is_active: false })
                        })
                        .then(res => res.json())
                        .then(res => {
                            Swal.fire('Kategori Dinonaktifkan', 'Kategori telah dinonaktifkan dari sistem.', 'success')
                                .then(() => location.reload());
                        });
                    }
                });
                return;
            }

            Swal.fire({
                title: 'Hapus Kategori Tiket?',
                html: `Apakah Anda yakin ingin menghapus kategori <strong>"${name}"</strong>?<br>Data yang dihapus tidak dapat dikembalikan.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#d33'
            }).then(result => {
                if (result.isConfirmed) {
                    fetch('/api/v1/categories/' + id, {
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
                        Swal.fire('Terhapus!', data.message || 'Kategori berhasil dihapus.', 'success')
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
