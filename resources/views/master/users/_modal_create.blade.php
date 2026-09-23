<div class="modal fade" id="kt_modal_add_user" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header" id="kt_modal_add_user_header">
                <h2 class="fw-bold">Tambah Pengguna / Staf Baru</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>

            <!-- Modal Form -->
            <form id="form_add_user" class="form" novalidate>
                <div class="modal-body py-10 px-lg-17">
                    @if(auth()->user()->isSuperadmin())
                        <!-- Perusahaan / Tenant Selection -->
                        <div class="fv-row mb-7">
                            <label class="required fs-6 fw-semibold mb-2">Perusahaan / Tenant</label>
                            <select class="form-select form-select-solid" name="company_id" id="add_company_id" required>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}" {{ $loop->first ? 'selected' : '' }}>
                                        {{ $company->name }} ({{ $company->slug }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">Staf ini akan didaftarkan di bawah organisasi tenant yang dipilih.</div>
                        </div>
                    @else
                        <!-- Perusahaan / Tenant (Terkunci Otomatis) -->
                        <input type="hidden" name="company_id" id="add_company_id" value="{{ auth()->user()->company_id }}" />
                        <div class="fv-row mb-7">
                            <label class="fs-6 fw-semibold mb-2">Perusahaan / Tenant</label>
                            <input type="text" class="form-control form-control-solid bg-light" value="{{ auth()->user()->company?->name }}" readonly disabled />
                            <div class="form-text">Staf otomatis terdaftar di bawah organisasi tenant Anda.</div>
                        </div>
                    @endif

                    <!-- Nama Lengkap -->
                    <div class="fv-row mb-7">
                        <label class="required fs-6 fw-semibold mb-2">Nama Lengkap</label>
                        <input type="text" class="form-control form-control-solid" placeholder="Contoh: Budi Pratama" name="name" id="add_name" required maxlength="100" />
                    </div>

                    <!-- Email & Password -->
                    <div class="row g-5 mb-7">
                        <div class="col-md-6 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">Alamat Email</label>
                            <input type="email" class="form-control form-control-solid" placeholder="budi@example.com" name="email" id="add_email" required maxlength="150" />
                        </div>
                        <div class="col-md-6 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">Password Awal</label>
                            <input type="password" class="form-control form-control-solid" placeholder="Minimal 6 karakter" name="password" id="add_password" required minlength="6" />
                        </div>
                    </div>

                    <!-- Peran (Role) & Departemen -->
                    <div class="row g-5 mb-7">
                        <div class="col-md-6 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">Peran (Role)</label>
                            <select class="form-select form-select-solid" name="role" id="add_role" required>
                                <option value="requester" selected>Karyawan (Requester)</option>
                                <option value="agent">Teknisi / Helpdesk (Agent)</option>
                                <option value="company_admin">Admin Tenant (Company Admin)</option>
                                @if(auth()->user()->isSuperadmin())
                                    <option value="superadmin">Superadmin Platform</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-md-6 fv-row">
                            <label class="fs-6 fw-semibold mb-2">Departemen</label>
                            <select class="form-select form-select-solid" name="department_id" id="add_department_id">
                                <option value="">-- Pilih Departemen (Opsional) --</option>
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
                            <input type="text" class="form-control form-control-solid" placeholder="Contoh: IT Specialist, Staff Finance" name="job_title" id="add_job_title" maxlength="100" />
                        </div>
                        <div class="col-md-6 fv-row">
                            <label class="fs-6 fw-semibold mb-2">No. HP / WhatsApp</label>
                            <input type="text" class="form-control form-control-solid" placeholder="Contoh: 08123456789" name="phone" id="add_phone" maxlength="30" />
                        </div>
                    </div>

                    <!-- Status Aktif -->
                    <div class="fv-row mb-7">
                        <div class="d-flex flex-stack">
                            <div class="me-5">
                                <label class="fs-6 fw-semibold">Status Akun Aktif</label>
                                <div class="fs-7 fw-semibold text-muted">Akun aktif dapat langsung masuk (login) ke sistem SiriusTicketing.</div>
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
                        <span class="indicator-label">Simpan Pengguna</span>
                        <span class="indicator-progress">Mohon tunggu...
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
