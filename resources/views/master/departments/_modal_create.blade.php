<div class="modal fade" id="kt_modal_add_department" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header" id="kt_modal_add_department_header">
                <h2 class="fw-bold">Tambah Departemen Baru</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>

            <!-- Modal Form -->
            <form id="form_add_department" class="form" novalidate>
                <div class="modal-body py-10 px-lg-17">
                    <!-- Perusahaan / Tenant -->
                    <div class="fv-row mb-7">
                        <label class="required fs-6 fw-semibold mb-2">Perusahaan / Tenant</label>
                        <select class="form-select form-select-solid" name="company_id" id="add_company_id" required>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}" {{ $loop->first ? 'selected' : '' }}>
                                    {{ $company->name }} ({{ $company->slug }})
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text">Departemen ini akan menjadi divisi di bawah perusahaan ini.</div>
                    </div>

                    <!-- Nama Departemen -->
                    <div class="fv-row mb-7">
                        <label class="required fs-6 fw-semibold mb-2">Nama Departemen</label>
                        <input type="text" class="form-control form-control-solid" placeholder="Contoh: IT Support, HRD, Finance" name="name" id="add_name" required maxlength="100" />
                    </div>

                    <!-- Deskripsi -->
                    <div class="fv-row mb-7">
                        <label class="fs-6 fw-semibold mb-2">Deskripsi Tugas</label>
                        <textarea class="form-control form-control-solid" rows="3" placeholder="Jelaskan ruang lingkup divisi ini..." name="description" id="add_description"></textarea>
                    </div>

                    <!-- Lead User (Kepala Departemen) -->
                    <div class="fv-row mb-7">
                        <label class="fs-6 fw-semibold mb-2">Kepala / Lead Departemen</label>
                        <select class="form-select form-select-solid" name="lead_user_id" id="add_lead_user_id">
                            <option value="">-- Pilih User Lead (Opsional) --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">
                                    {{ $user->name }} ({{ $user->email }}) - {{ $user->job_title ?? 'Staf' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status Aktif -->
                    <div class="fv-row mb-7">
                        <div class="d-flex flex-stack">
                            <div class="me-5">
                                <label class="fs-6 fw-semibold">Status Departemen Aktif</label>
                                <div class="fs-7 fw-semibold text-muted">Departemen aktif dapat dipilih saat pembuatan tiket baru.</div>
                            </div>
                            <label class="form-check form-switch form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" name="is_active" id="add_is_active" value="1" checked="checked" />
                                <span class="form-check-label fw-semibold text-muted">Aktif</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer flex-center">
                    <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="btn_submit_add" class="btn btn-primary">
                        <span class="indicator-label">Simpan Departemen</span>
                        <span class="indicator-progress">Mohon tunggu...
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
