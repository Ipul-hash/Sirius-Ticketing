<!-- Modal Edit Kebijakan SLA -->
<div class="modal fade" id="kt_modal_edit_sla" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-550px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Konfigurasi Kebijakan SLA</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>

            <form id="form_edit_sla" class="form">
                <input type="hidden" id="edit_sla_priority" />
                <input type="hidden" id="edit_sla_company_id" />

                <div class="modal-body py-10 px-lg-17">
                    <!-- Info Header -->
                    <div class="d-flex align-items-center mb-7 p-4 bg-light rounded">
                        <div class="symbol symbol-45px me-4">
                            <span class="symbol-label" id="edit_sla_priority_icon_wrapper">
                                <i class="ki-duotone ki-timer fs-2 text-primary" id="edit_sla_priority_icon"></i>
                            </span>
                        </div>
                        <div>
                            <div class="fs-6 fw-bold text-gray-900" id="edit_sla_priority_title">Prioritas Tiket</div>
                            <div class="fs-7 text-muted" id="edit_sla_company_title">Tenant: -</div>
                        </div>
                    </div>

                    <!-- Target Respon Awal -->
                    <div class="fv-row mb-7">
                        <label class="required fs-6 fw-semibold mb-2">Target Respon Pertama (First Response)</label>
                        <div class="input-group">
                            <input type="number" class="form-control form-control-solid" id="edit_sla_first_response" min="1" required />
                            <span class="input-group-text">Menit</span>
                        </div>
                        <div class="d-flex gap-2 mt-2">
                            <button type="button" class="btn btn-xs btn-light-primary btn-quick-resp" data-min="15">15 Menit</button>
                            <button type="button" class="btn btn-xs btn-light-primary btn-quick-resp" data-min="30">30 Menit</button>
                            <button type="button" class="btn btn-xs btn-light-primary btn-quick-resp" data-min="60">1 Jam</button>
                            <button type="button" class="btn btn-xs btn-light-primary btn-quick-resp" data-min="120">2 Jam</button>
                            <button type="button" class="btn btn-xs btn-light-primary btn-quick-resp" data-min="480">8 Jam</button>
                            <button type="button" class="btn btn-xs btn-light-primary btn-quick-resp" data-min="1440">24 Jam</button>
                        </div>
                    </div>

                    <!-- Target Waktu Resolusi -->
                    <div class="fv-row mb-7">
                        <label class="required fs-6 fw-semibold mb-2">Target Waktu Penyelesaian (Resolution Time)</label>
                        <div class="input-group">
                            <input type="number" class="form-control form-control-solid" id="edit_sla_resolution" min="1" required />
                            <span class="input-group-text">Menit</span>
                        </div>
                        <div class="d-flex gap-2 mt-2 flex-wrap">
                            <button type="button" class="btn btn-xs btn-light-success btn-quick-resol" data-min="60">1 Jam</button>
                            <button type="button" class="btn btn-xs btn-light-success btn-quick-resol" data-min="120">2 Jam</button>
                            <button type="button" class="btn btn-xs btn-light-success btn-quick-resol" data-min="240">4 Jam</button>
                            <button type="button" class="btn btn-xs btn-light-success btn-quick-resol" data-min="480">8 Jam</button>
                            <button type="button" class="btn btn-xs btn-light-success btn-quick-resol" data-min="1440">24 Jam (1 Hari)</button>
                            <button type="button" class="btn btn-xs btn-light-success btn-quick-resol" data-min="2880">48 Jam (2 Hari)</button>
                        </div>
                        <div class="text-muted fs-8 mt-2">
                            *Waktu resolusi harus lebih besar atau sama dengan waktu respon awal.
                        </div>
                    </div>
                </div>

                <div class="modal-footer flex-center">
                    <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="btn_submit_edit_sla" class="btn btn-primary">
                        <span class="indicator-label">Simpan Kebijakan</span>
                        <span class="indicator-progress">Menyimpan... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
