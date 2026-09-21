<!-- Modal Edit Identitas Perusahaan -->
<div class="modal fade" id="kt_modal_edit_company" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-600px">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h2 class="fw-bold">Ubah Identitas Perusahaan</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>

            <!-- Form -->
            <form id="form_edit_company" class="form">
                <input type="hidden" id="edit_company_id" />

                <div class="modal-body py-10 px-lg-17">
                    <div class="fv-row mb-7">
                        <label class="required fs-6 fw-semibold mb-2">Nama Perusahaan</label>
                        <input type="text" class="form-control form-control-solid" id="edit_company_name" required />
                    </div>

                    <div class="row g-9 mb-7">
                        <div class="col-md-6 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">Slug Tenant</label>
                            <input type="text" class="form-control form-control-solid" id="edit_company_slug" required />
                        </div>
                        <div class="col-md-6 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">Paket Langganan</label>
                            <select id="edit_company_plan" class="form-select form-select-solid" required>
                                <option value="starter">Starter</option>
                                <option value="professional">Professional</option>
                                <option value="enterprise">Enterprise</option>
                            </select>
                        </div>
                    </div>

                    <div class="fv-row mb-7">
                        <label class="fs-6 fw-semibold mb-2">Custom Domain (Opsional)</label>
                        <input type="text" class="form-control form-control-solid" id="edit_company_domain" placeholder="Contoh: helpdesk.sirius.co.id" />
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer flex-center">
                    <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="btn_submit_edit_company" class="btn btn-primary">
                        <span class="indicator-label">Simpan Perubahan</span>
                        <span class="indicator-progress">Memproses... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
