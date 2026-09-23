<!-- Modal Tambah Canned Response -->
<div class="modal fade" id="kt_modal_add_canned" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Tambah Template Balasan Cepat (Macro)</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>

            <form id="form_add_canned" class="form" novalidate>
                <div class="modal-body py-10 px-lg-17">
                    @if(auth()->user()->isSuperadmin())
                        <!-- Perusahaan / Tenant Selection -->
                        <div class="fv-row mb-7">
                            <label class="required fs-6 fw-semibold mb-2">Perusahaan / Tenant</label>
                            <select id="add_canned_company_id" class="form-select form-select-solid" required>
                                <option value="">-- Pilih Perusahaan --</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}" {{ $loop->first ? 'selected' : '' }}>
                                        {{ $company->name }} ({{ $company->slug }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="text-muted fs-8 mt-1">Template akan tersedia bagi teknisi di perusahaan ini.</div>
                        </div>
                    @else
                        <!-- Perusahaan / Tenant (Terkunci Otomatis) -->
                        <input type="hidden" id="add_canned_company_id" value="{{ auth()->user()->company_id }}" />
                        <div class="fv-row mb-7">
                            <label class="fs-6 fw-semibold mb-2">Perusahaan / Tenant</label>
                            <input type="text" class="form-control form-control-solid bg-light" value="{{ auth()->user()->company?->name }}" readonly disabled />
                            <div class="text-muted fs-8 mt-1">Template otomatis terdaftar di bawah perusahaan Anda.</div>
                        </div>
                    @endif

                    <!-- Departemen (Opsional / Global) -->
                    <div class="fv-row mb-7">
                        <label class="fs-6 fw-semibold mb-2">Cakupan Departemen</label>
                        <select id="add_canned_department_id" class="form-select form-select-solid">
                            <option value="">-- Global (Semua Departemen dalam Tenant) --</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" data-company="{{ $dept->company_id }}">
                                    {{ $dept->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="text-muted fs-8 mt-1">Kosongkan jika template ini dapat dipakai oleh seluruh tim teknisi lintas divisi.</div>
                    </div>

                    <div class="row g-9 mb-7">
                        <!-- Judul Template -->
                        <div class="col-md-6 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">Judul Template</label>
                            <input type="text" class="form-control form-control-solid" id="add_canned_title" placeholder="Contoh: Instruksi Restart Router" required maxlength="150" />
                        </div>

                        <!-- Shortcut / Macro Trigger -->
                        <div class="col-md-6 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">Shortcut Macro</label>
                            <div class="input-group input-group-solid">
                                <span class="input-group-text text-gray-700 fw-bold">/</span>
                                <input type="text" class="form-control form-control-solid" id="add_canned_shortcut" placeholder="restart-router" required maxlength="50" />
                            </div>
                            <div class="text-muted fs-8 mt-1">Tekan shortcut ini di chatbox tiket untuk menyisipkan pesan otomatis.</div>
                        </div>
                    </div>

                    <!-- Isi Pesan Template -->
                    <div class="fv-row mb-7">
                        <label class="required fs-6 fw-semibold mb-2">Isi Pesan Balasan</label>
                        <textarea class="form-control form-control-solid" id="add_canned_message" rows="6" placeholder="Tuliskan isi teks standar yang akan dikirimkan kepada pelapor tiket..." required></textarea>
                        <div class="text-muted fs-8 mt-1">Gunakan bahasa yang ramah, jelas, dan solutif.</div>
                    </div>
                </div>

                <div class="modal-footer flex-center">
                    <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="btn_submit_add_canned" class="btn btn-primary">
                        <span class="indicator-label">Simpan Template</span>
                        <span class="indicator-progress">Menyimpan...
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
