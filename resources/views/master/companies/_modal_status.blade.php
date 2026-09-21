<!-- Modal Ubah Status Perusahaan -->
<div class="modal fade" id="kt_modal_status_company" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-500px">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h2 class="fw-bold">Ubah Status Tenant</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>

            <!-- Form -->
            <form id="form_status_company" class="form">
                <input type="hidden" id="status_company_id" />

                <div class="modal-body py-10 px-lg-17">
                    <div class="mb-5">
                        <label class="fs-6 fw-semibold mb-2">Nama Tenant:</label>
                        <div class="fs-5 fw-bold text-gray-900" id="status_company_name_display">-</div>
                    </div>

                    <div class="fv-row mb-7">
                        <label class="required fs-6 fw-semibold mb-2">Pilih Status Baru</label>
                        <select id="status_company_select" class="form-select form-select-solid" required>
                            <option value="active">Active (Operasional Penuh)</option>
                            <option value="trial">Trial (Masa Uji Coba)</option>
                            <option value="suspended">Suspended (Ditangguhkan / Kunci Akses)</option>
                        </select>
                        <div class="text-muted fs-8 mt-2">
                            *Jika status <strong>Suspended</strong>, seluruh user tenant ini tidak akan bisa login ke helpdesk.
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer flex-center">
                    <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="btn_submit_status_company" class="btn btn-primary">
                        <span class="indicator-label">Perbarui Status</span>
                        <span class="indicator-progress">Memproses... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
