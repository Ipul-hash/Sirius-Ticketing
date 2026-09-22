<!-- Modal Konfirmasi Persetujuan Tiket (Approve) -->
<div class="modal fade" id="kt_modal_approve_ticket" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-500px">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="fw-bold mb-0">Setujui Tiket (Approve)</h3>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>

            <form id="form_approve_ticket" class="form">
                <input type="hidden" id="approve_ticket_id" value="{{ $ticket->id ?? '' }}" />

                <div class="modal-body py-6 px-lg-10">
                    <div class="alert alert-dismissible bg-light-success d-flex align-items-center p-4 rounded-3 border border-success border-dashed mb-5">
                        <i class="ki-duotone ki-verify fs-2hx text-success me-3"><span class="path1"></span><span class="path2"></span></i>
                        <div class="d-flex flex-column">
                            <span class="fw-bold text-gray-900 fs-7">Konfirmasi Persetujuan ITIL</span>
                            <span class="fs-8 text-gray-700">Tiket akan langsung dialihkan ke status aktif (Open / In Progress) dan dapat segera dikerjakan oleh tim teknisi.</span>
                        </div>
                    </div>

                    <div class="mb-4 pb-3 border-bottom">
                        <span class="badge badge-light-primary fw-bold fs-7 mb-1" id="approve_modal_ticket_number">{{ $ticket->ticket_number ?? '' }}</span>
                        <div class="fw-bold text-gray-800 fs-6 text-truncate" id="approve_modal_ticket_subject">{{ $ticket->subject ?? '' }}</div>
                    </div>

                    <div class="fv-row mb-2">
                        <label class="fs-6 fw-semibold mb-2">Catatan Persetujuan (Opsional)</label>
                        <textarea class="form-control form-control-solid" id="approve_notes" rows="3" placeholder="Contoh: Disetujui sesuai anggaran departemen Q3..."></textarea>
                    </div>
                </div>

                <div class="modal-footer flex-center">
                    <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="btn_submit_approve" class="btn btn-success">
                        <span class="indicator-label">
                            <i class="ki-duotone ki-check fs-4 me-1"></i>
                            Ya, Setujui Tiket
                        </span>
                        <span class="indicator-progress">Memproses...
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Penolakan Tiket (Reject) -->
<div class="modal fade" id="kt_modal_reject_ticket" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-500px">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="fw-bold mb-0 text-danger">Tolak Tiket (Reject)</h3>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>

            <form id="form_reject_ticket" class="form">
                <input type="hidden" id="reject_ticket_id" value="{{ $ticket->id ?? '' }}" />

                <div class="modal-body py-6 px-lg-10">
                    <div class="alert alert-dismissible bg-light-danger d-flex align-items-center p-4 rounded-3 border border-danger border-dashed mb-5">
                        <i class="ki-duotone ki-cross-circle fs-2hx text-danger me-3"><span class="path1"></span><span class="path2"></span></i>
                        <div class="d-flex flex-column">
                            <span class="fw-bold text-danger fs-7">Peringatan Penolakan Tiket</span>
                            <span class="fs-8 text-gray-700">Tiket akan langsung ditutup permanen (Closed). Alasan penolakan akan dicatat dan diberitahukan kepada pemohon.</span>
                        </div>
                    </div>

                    <div class="mb-4 pb-3 border-bottom">
                        <span class="badge badge-light-primary fw-bold fs-7 mb-1" id="reject_modal_ticket_number">{{ $ticket->ticket_number ?? '' }}</span>
                        <div class="fw-bold text-gray-800 fs-6 text-truncate" id="reject_modal_ticket_subject">{{ $ticket->subject ?? '' }}</div>
                    </div>

                    <div class="fv-row mb-2">
                        <label class="required fs-6 fw-semibold mb-2">Alasan Penolakan</label>
                        <textarea class="form-control form-control-solid" id="reject_reason" rows="3" 
                                  placeholder="Jelaskan alasan mengapa permintaan tiket ini ditolak..." required></textarea>
                    </div>
                </div>

                <div class="modal-footer flex-center">
                    <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="btn_submit_reject" class="btn btn-danger">
                        <span class="indicator-label">
                            <i class="ki-duotone ki-cross fs-4 me-1"></i>
                            Tolak & Tutup Tiket
                        </span>
                        <span class="indicator-progress">Memproses...
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
