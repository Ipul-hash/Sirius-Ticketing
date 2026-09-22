<!-- Modal Quick Update Status Tiket -->
<div class="modal fade" id="kt_modal_status_ticket" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-500px">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="fw-bold mb-0">Ubah Status Tiket</h3>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>

            <form id="form_status_ticket" class="form">
                <input type="hidden" id="quick_status_ticket_id" />

                <div class="modal-body py-6 px-lg-10">
                    <div class="mb-5 pb-3 border-bottom">
                        <span class="badge badge-light-primary fw-bold fs-7 mb-1" id="quick_status_ticket_number"></span>
                        <div class="fw-bold text-gray-800 fs-6 text-truncate" id="quick_status_ticket_subject"></div>
                    </div>

                    <div class="fv-row mb-6">
                        <label class="required fs-6 fw-semibold mb-2">Status Baru</label>
                        <select id="quick_status_value" class="form-select form-select-solid" required>
                            <option value="open">Open (Baru / Dalam Antrean)</option>
                            <option value="in_progress">In Progress (Sedang Dikerjakan Teknisi)</option>
                            <option value="pending_user">Pending User (Menunggu Jawaban Pelapor)</option>
                            <option value="pending_approval">Pending Approval (Menunggu Persetujuan)</option>
                            <option value="resolved">Resolved (Masalah Terselesaikan)</option>
                            <option value="closed">Closed (Tiket Ditutup Permanen)</option>
                        </select>
                    </div>

                    <div class="fv-row mb-4">
                        <label class="fs-6 fw-semibold mb-2">Catatan Perubahan Status (Opsional)</label>
                        <textarea class="form-control form-control-solid" id="quick_status_notes" rows="3" placeholder="Alasan perubahan atau ringkasan solusi..."></textarea>
                    </div>
                </div>

                <div class="modal-footer flex-center">
                    <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="btn_submit_quick_status" class="btn btn-primary">
                        <span class="indicator-label">Simpan Status</span>
                        <span class="indicator-progress">Menyimpan...
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
