<div class="modal fade" id="kt_modal_edit_department" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h2 class="fw-bold">Edit Departemen</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>

            <!-- Modal Form -->
            <form id="form_edit_department" class="form" novalidate>
                <input type="hidden" id="edit_department_id" name="id" />

                <div class="modal-body py-10 px-lg-17">
                    <!-- Info Perusahaan -->
                    <div class="mb-7">
                        <label class="fs-6 fw-semibold mb-2">Perusahaan / Tenant</label>
                        <input type="text" class="form-control form-control-solid bg-light" id="edit_company_name" disabled readonly />
                    </div>

                    <!-- Nama Departemen -->
                    <div class="fv-row mb-7">
                        <label class="required fs-6 fw-semibold mb-2">Nama Departemen</label>
                        <input type="text" class="form-control form-control-solid" name="name" id="edit_name" required maxlength="100" />
                    </div>

                    <!-- Deskripsi -->
                    <div class="fv-row mb-7">
                        <label class="fs-6 fw-semibold mb-2">Deskripsi Tugas</label>
                        <textarea class="form-control form-control-solid" rows="3" name="description" id="edit_description"></textarea>
                    </div>

                    <!-- Lead User -->
                    <div class="fv-row mb-7">
                        <label class="fs-6 fw-semibold mb-2">Kepala / Lead Departemen</label>
                        <select class="form-select form-select-solid" name="lead_user_id" id="edit_lead_user_id">
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
                                <div class="fs-7 fw-semibold text-muted">Nonaktifkan jika divisi ini sudah tidak beroperasi.</div>
                            </div>
                            <label class="form-check form-switch form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" name="is_active" id="edit_is_active" value="1" />
                                <span class="form-check-label fw-semibold text-muted">Aktif</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer flex-center">
                    <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="btn_submit_edit" class="btn btn-primary">
                        <span class="indicator-label">Perbarui Data</span>
                        <span class="indicator-progress">Mohon tunggu...
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
