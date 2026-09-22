<!-- Modal Tambah Kategori Tiket -->
<div class="modal fade" id="kt_modal_add_category" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Tambah Kategori Tiket Baru</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>

            <form id="form_add_category" class="form" novalidate>
                <div class="modal-body py-10 px-lg-17">
                    <!-- Perusahaan / Tenant Selection -->
                    <div class="fv-row mb-7">
                        <label class="required fs-6 fw-semibold mb-2">Perusahaan / Tenant</label>
                        <select id="add_category_company_id" class="form-select form-select-solid" required>
                            <option value="">-- Pilih Perusahaan --</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}" {{ $loop->first ? 'selected' : '' }}>
                                    {{ $company->name }} ({{ $company->slug }})
                                </option>
                            @endforeach
                        </select>
                        <div class="text-muted fs-8 mt-1">Kategori tiket terikat pada lingkup perusahaan ini.</div>
                    </div>

                    <!-- Departemen Penanggung Jawab -->
                    <div class="fv-row mb-7">
                        <label class="required fs-6 fw-semibold mb-2">Departemen Penanggung Jawab</label>
                        <select id="add_category_department_id" class="form-select form-select-solid" required>
                            <option value="">-- Pilih Departemen --</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" data-company="{{ $dept->company_id }}">
                                    {{ $dept->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="text-muted fs-8 mt-1">Divisi yang akan menangani tiket dengan kategori ini.</div>
                    </div>

                    <!-- Nama Kategori -->
                    <div class="fv-row mb-7">
                        <label class="required fs-6 fw-semibold mb-2">Nama Kategori</label>
                        <input type="text" class="form-control form-control-solid" id="add_category_name" placeholder="Contoh: Gangguan Wi-Fi, Akses Server, Pengadaan Laptop" required maxlength="100" />
                    </div>

                    <!-- Default Prioritas Tiket -->
                    <div class="fv-row mb-7">
                        <label class="required fs-6 fw-semibold mb-2">Prioritas Bawaan (Default Priority)</label>
                        <select id="add_category_default_priority" class="form-select form-select-solid" required>
                            <option value="low">Low (Rendah - Penanganan Santai)</option>
                            <option value="medium" selected>Medium (Normal / Standar)</option>
                            <option value="high">High (Tinggi - Prioritas Penting)</option>
                            <option value="urgent">Urgent (Kritis / Darurat - SLA Tercepat)</option>
                        </select>
                        <div class="text-muted fs-8 mt-1">Prioritas awal saat requester mengajukan tiket kategori ini.</div>
                    </div>

                    <!-- Approval Workflow Switch -->
                    <div class="fv-row mb-7 p-4 bg-light-warning rounded border border-warning border-dashed">
                        <div class="d-flex flex-stack">
                            <div class="me-5">
                                <label class="fs-6 fw-bold text-gray-800 d-block">Persetujuan Atasan (Approval Workflow)</label>
                                <div class="fs-7 text-muted">Jika aktif, tiket wajib disetujui (Approved) oleh Lead/Manager sebelum diproses teknisi.</div>
                            </div>
                            <label class="form-check form-switch form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" id="add_category_requires_approval" value="1" />
                                <span class="form-check-label fw-semibold text-warning">Wajib Approval</span>
                            </label>
                        </div>
                    </div>

                    <!-- Status Aktif Switch -->
                    <div class="fv-row mb-7">
                        <div class="d-flex flex-stack">
                            <div class="me-5">
                                <label class="fs-6 fw-semibold">Status Kategori</label>
                                <div class="fs-7 text-muted">Kategori aktif dapat langsung dipilih saat pembuatan tiket.</div>
                            </div>
                            <label class="form-check form-switch form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" id="add_category_is_active" value="1" checked />
                                <span class="form-check-label fw-semibold text-muted">Aktif</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="modal-footer flex-center">
                    <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="btn_submit_add_category" class="btn btn-primary">
                        <span class="indicator-label">Simpan Kategori</span>
                        <span class="indicator-progress">Mohon tunggu...
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
