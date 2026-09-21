<!-- Modal Tambah Perusahaan (Tenant) -->
<div class="modal fade" id="kt_modal_add_company" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h2 class="fw-bold">Pendaftaran Tenant Baru</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>

            <!-- Form -->
            <form id="form_add_company" class="form">
                <div class="modal-body py-10 px-lg-17">
                    <!-- Section: Profil Perusahaan -->
                    <div class="d-flex align-items-center mb-5">
                        <div class="symbol symbol-35px me-3">
                            <span class="symbol-label bg-light-primary text-primary fw-bold">1</span>
                        </div>
                        <h4 class="fw-bold m-0 text-gray-800">Profil Organisasi / Perusahaan</h4>
                    </div>

                    <div class="fv-row mb-7">
                        <label class="required fs-6 fw-semibold mb-2">Nama Perusahaan</label>
                        <input type="text" class="form-control form-control-solid" id="add_company_name" placeholder="Contoh: PT Sirius Solusi Digital" required />
                    </div>

                    <div class="row g-9 mb-7">
                        <div class="col-md-6 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">Slug Tenant (URL ID)</label>
                            <input type="text" class="form-control form-control-solid" id="add_company_slug" placeholder="contoh: sirius-solusi" required />
                            <div class="text-muted fs-8 mt-1">Digunakan untuk akses tenant & isolasi data.</div>
                        </div>
                        <div class="col-md-6 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">Paket Langganan</label>
                            <select id="add_company_plan" class="form-select form-select-solid" required>
                                <option value="starter">Starter (Dasar)</option>
                                <option value="professional" selected>Professional (Bisnis)</option>
                                <option value="enterprise">Enterprise (Korporasi)</option>
                            </select>
                        </div>
                    </div>

                    <div class="fv-row mb-10">
                        <label class="fs-6 fw-semibold mb-2">Custom Domain (Opsional)</label>
                        <input type="text" class="form-control form-control-solid" id="add_company_domain" placeholder="Contoh: helpdesk.sirius.co.id" />
                    </div>

                    <div class="separator separator-dashed my-7"></div>

                    <!-- Section: Akun Admin Utama -->
                    <div class="d-flex align-items-center mb-5">
                        <div class="symbol symbol-35px me-3">
                            <span class="symbol-label bg-light-success text-success fw-bold">2</span>
                        </div>
                        <h4 class="fw-bold m-0 text-gray-800">Akun Superadmin Perusahaan</h4>
                    </div>

                    <div class="fv-row mb-7">
                        <label class="required fs-6 fw-semibold mb-2">Nama Lengkap Admin</label>
                        <input type="text" class="form-control form-control-solid" id="add_admin_name" placeholder="Nama Penanggung Jawab IT / Admin" required />
                    </div>

                    <div class="row g-9 mb-7">
                        <div class="col-md-6 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">Email Login Admin</label>
                            <input type="email" class="form-control form-control-solid" id="add_admin_email" placeholder="admin@perusahaan.com" required />
                        </div>
                        <div class="col-md-6 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">Password Awal</label>
                            <input type="password" class="form-control form-control-solid" id="add_admin_password" placeholder="Minimal 8 karakter" required minlength="8" />
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer flex-center">
                    <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="btn_submit_add_company" class="btn btn-primary">
                        <span class="indicator-label">Daftarkan Perusahaan</span>
                        <span class="indicator-progress">Memproses... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
