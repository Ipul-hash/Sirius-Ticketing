<!-- Modal Edit Canned Response -->
<div class="modal fade" id="kt_modal_edit_canned" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Ubah Template Balasan Cepat</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>

            <form id="form_edit_canned" class="form" novalidate>
                <input type="hidden" id="edit_canned_id" />

                <div class="modal-body py-10 px-lg-17">
                    <!-- Info Tenant (Readonly) -->
                    <div class="fv-row mb-7">
                        <label class="fs-6 fw-semibold mb-2">Perusahaan / Tenant</label>
                        <input type="text" class="form-control form-control-solid" id="edit_canned_company_name" readonly disabled />
                    </div>

                    <!-- Departemen (Opsional / Global) -->
                    <div class="fv-row mb-7">
                        <label class="fs-6 fw-semibold mb-2">Cakupan Departemen</label>
                        <select id="edit_canned_department_id" class="form-select form-select-solid">
                            <option value="">-- Global (Semua Departemen dalam Tenant) --</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" data-company="{{ $dept->company_id }}">
                                    {{ $dept->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row g-9 mb-7">
                        <!-- Judul Template -->
                        <div class="col-md-6 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">Judul Template</label>
                            <input type="text" class="form-control form-control-solid" id="edit_canned_title" required maxlength="150" />
                        </div>

                        <!-- Shortcut / Macro Trigger -->
                        <div class="col-md-6 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">Shortcut Macro</label>
                            <input type="text" class="form-control form-control-solid" id="edit_canned_shortcut" required maxlength="100" />
                            <div class="text-muted fs-8 mt-1">Harus diawali simbol <code>/</code></div>
                        </div>
                    </div>

                    <!-- Isi Pesan Template -->
                    <div class="fv-row mb-7">
                        <label class="required fs-6 fw-semibold mb-2">Isi Pesan Balasan</label>
                        <textarea class="form-control form-control-solid" id="edit_canned_message" rows="6" required></textarea>
                    </div>
                </div>

                <div class="modal-footer flex-center">
                    <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="btn_submit_edit_canned" class="btn btn-primary">
                        <span class="indicator-label">Perbarui Template</span>
                        <span class="indicator-progress">Menyimpan...
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
