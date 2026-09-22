<!-- Modal Gabungkan Tiket (Ticket Merging) -->
<div class="modal fade" id="kt_modal_merge_ticket" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header pb-0 border-0 justify-content-between px-8 pt-8">
                <div class="d-flex align-items-center">
                    <div class="symbol symbol-40px symbol-circle bg-light-primary me-3">
                        <i class="ki-duotone ki-switch fs-2 text-primary">
                            <span class="path1"></span><span class="path2"></span>
                        </i>
                    </div>
                    <div>
                        <h3 class="modal-title fw-bolder text-gray-900 fs-5 mb-0">Gabungkan Tiket (Ticket Merging)</h3>
                        <span class="text-muted fs-8">Satukan tiket duplikat ke dalam tiket induk utama (Primary Ticket).</span>
                    </div>
                </div>
                <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>

            <form id="form_merge_ticket">
                <div class="modal-body py-6 px-8">
                    <!-- Peringatan Aturan ITIL / Zendesk Style -->
                    <div class="alert alert-dismissible bg-light-warning border border-warning border-dashed d-flex align-items-center p-4 rounded-3 mb-5">
                        <i class="ki-duotone ki-information-5 fs-2hx text-warning me-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                        <div class="d-flex flex-column">
                            <span class="fw-bold text-gray-900 fs-8">Perhatian Konsekuensi Penggabungan:</span>
                            <span class="text-gray-700 fs-9">
                                Tiket saat ini (<strong id="modal_source_number">{{ $ticket->ticket_number }}</strong>) akan otomatis <strong>ditutup (Closed)</strong>. Seluruh proses penanganan, komunikasi, dan resolusi berikutnya dialihkan ke tiket utama yang dipilih.
                            </span>
                        </div>
                    </div>

                    <!-- Ringkasan Tiket Asal (Duplikat) -->
                    <div class="p-3 bg-light rounded-3 mb-5 border border-secondary border-opacity-10">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <span class="text-muted fs-9 fw-bold text-uppercase">Tiket Asal (Akan Ditutup):</span>
                            <span class="badge badge-light-primary fs-9 fw-bolder">{{ $ticket->ticket_number }}</span>
                        </div>
                        <div class="fw-bold text-gray-900 fs-7 text-truncate">{{ $ticket->subject }}</div>
                        <div class="text-muted fs-8 mt-1">
                            Pelapor: <strong>{{ $ticket->requester?->name ?? 'Pemohon' }}</strong> &bull; Kategori: {{ $ticket->category?->name ?? 'Umum' }}
                        </div>
                    </div>

                    <!-- Pilih Tiket Target (Utama) -->
                    <div class="fv-row mb-5">
                        <label class="required fs-7 fw-bold mb-2">Pilih Tiket Target (Utama / Primary)</label>
                        <select id="select_target_ticket" class="form-select form-select-solid rounded-3 fs-7" required>
                            <option value="">Memuat daftar tiket kandidat...</option>
                        </select>
                        <div class="form-text fs-9 text-muted">
                            Hanya menampilkan tiket aktif yang belum digabungkan dalam perusahaan yang sama.
                        </div>
                    </div>

                    <!-- Catatan Tambahan Penggabungan -->
                    <div class="fv-row mb-2">
                        <label class="fs-7 fw-bold mb-2">Catatan Penggabungan (Opsional)</label>
                        <textarea id="merge_reason_notes" rows="3" class="form-control form-control-solid rounded-3 fs-7" 
                                  placeholder="Contoh: Tiket duplikat dari kendala koneksi switch lantai 2 yang sama..."></textarea>
                    </div>
                </div>

                <div class="modal-footer border-0 px-8 pb-8 pt-0">
                    <button type="button" class="btn btn-sm btn-light rounded-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="btn_submit_merge" class="btn btn-sm btn-primary rounded-3 px-5">
                        <span class="indicator-label">
                            <i class="ki-duotone ki-switch fs-5 me-1"><span class="path1"></span><span class="path2"></span></i>
                            Gabungkan Sekarang
                        </span>
                        <span class="indicator-progress">Menggabungkan...
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
