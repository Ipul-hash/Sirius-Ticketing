<div class="modal fade" id="kt_modal_edit_user" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h2 class="fw-bold">Edit Pengguna / Staf</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>

            <!-- Modal Form -->
            <form id="form_edit_user" class="form" novalidate>
                <input type="hidden" id="edit_user_id" name="id" />

                <div class="modal-body py-10 px-lg-17">
                    <!-- Info Perusahaan -->
                    <div class="mb-7">
                        <label class="fs-6 fw-semibold mb-2">Perusahaan / Tenant</label>
                        <input type="text" class="form-control form-control-solid bg-light" id="edit_company_name" disabled readonly />
                    </div>

                    <!-- Nama Lengkap -->
                    <div class="fv-row mb-7">
                        <label class="required fs-6 fw-semibold mb-2">Nama Lengkap</label>
                        <input type="text" class="form-control form-control-solid" name="name" id="edit_name" required maxlength="100" />
                    </div>

                    <!-- Email & Password Baru -->
                    <div class="row g-5 mb-7">
                        <div class="col-md-6 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">Alamat Email</label>
                            <input type="email" class="form-control form-control-solid" name="email" id="edit_email" required maxlength="150" />
                        </div>
                        <div class="col-md-6 fv-row">
                            <label class="fs-6 fw-semibold mb-2">Ganti Password (Opsional)</label>
                            <input type="password" class="form-control form-control-solid" placeholder="Biarkan kosong jika tidak diubah" name="password" id="edit_password" minlength="6" />
                        </div>
                    </div>

                    <!-- Peran (Role) & Departemen -->
                    <div class="row g-5 mb-7">
                        <div class="col-md-6 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">Peran (Role)</label>
                            <select class="form-select form-select-solid" name="role" id="edit_role" required>
                                <option value="requester">Karyawan (Requester)</option>
                                <option value="agent">Teknisi / Helpdesk (Agent)</option>
                                <option value="company_admin">Admin Tenant (Company Admin)</option>
                                @if(auth()->user()->isSuperadmin())
                                    <option value="superadmin">Superadmin Platform</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-md-6 fv-row">
                            <label class="fs-6 fw-semibold mb-2">Departemen</label>
                            <select class="form-select form-select-solid" name="department_id" id="edit_department_id">
                                <option value="">-- Tanpa Departemen --</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" data-company="{{ $dept->company_id }}">
                                        {{ $dept->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Jabatan & No. Telepon -->
                    <div class="row g-5 mb-7">
                        <div class="col-md-6 fv-row">
                            <label class="fs-6 fw-semibold mb-2">Jabatan (Job Title)</label>
                            <input type="text" class="form-control form-control-solid" name="job_title" id="edit_job_title" maxlength="100" />
                        </div>
                        <div class="col-md-6 fv-row">
                            <label class="fs-6 fw-semibold mb-2">No. HP / WhatsApp</label>
                            <input type="text" class="form-control form-control-solid" name="phone" id="edit_phone" maxlength="30" />
                        </div>
                    </div>

                    <!-- Status Aktif -->
                    <div class="fv-row mb-7">
                        <div class="d-flex flex-stack">
                            <div class="me-5">
                                <label class="fs-6 fw-semibold">Status Akun Aktif</label>
                                <div class="fs-7 fw-semibold text-muted">Nonaktifkan jika staf sudah resign atau tidak lagi memiliki izin akses.</div>
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
