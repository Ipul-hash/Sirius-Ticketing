<!-- Modal Preview Canned Response -->
<div class="modal fade" id="kt_modal_preview_canned" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex align-items-center">
                    <span class="badge badge-light-primary fw-bold fs-6 me-3" id="preview_canned_shortcut"></span>
                    <h2 class="fw-bold mb-0" id="preview_canned_title"></h2>
                </div>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>

            <div class="modal-body py-8 px-lg-12">
                <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom">
                    <div>
                        <span class="text-muted fs-8 d-block">Perusahaan:</span>
                        <span class="fw-bold text-gray-800 fs-7" id="preview_canned_company"></span>
                    </div>
                    <div>
                        <span class="text-muted fs-8 d-block">Cakupan Divisi:</span>
                        <span class="badge badge-light-info fw-bold fs-8" id="preview_canned_scope"></span>
                    </div>
                </div>

                <div class="mb-5">
                    <label class="fs-7 fw-bold text-gray-700 mb-2">Teks Balasan Lengkap:</label>
                    <div class="bg-light p-5 rounded border border-gray-300 fs-6 text-gray-800" style="white-space: pre-wrap; font-family: inherit;" id="preview_canned_message"></div>
                </div>
            </div>

            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                <button type="button" id="btn_copy_preview_canned" class="btn btn-primary">
                    <i class="ki-duotone ki-copy fs-4 me-1"><span class="path1"></span><span class="path2"></span></i>
                    Salin Teks Balasan
                </button>
            </div>
        </div>
    </div>
</div>
