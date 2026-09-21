<!-- Modal Tambah Aset Inventaris -->
<div class="modal fade" id="kt_modal_add_asset" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Pendaftaran Aset Inventaris (CMDB)</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>

            <form id="form_add_asset" class="form">
                <div class="modal-body py-10 px-lg-17">
                    <!-- Tenant Selection -->
                    <div class="fv-row mb-7">
                        <label class="required fs-6 fw-semibold mb-2">Perusahaan / Tenant</label>
                        <select id="add_asset_company_id" class="form-select form-select-solid" required>
                            <option value="">Pilih Tenant Perusahaan</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}">{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row g-9 mb-7">
                        <div class="col-md-6 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">Tag Aset (Kode Unik)</label>
                            <input type="text" class="form-control form-control-solid" id="add_asset_tag" placeholder="Contoh: AST-SRV-001" required />
                            <div class="text-muted fs-8 mt-1">Unik per perusahaan.</div>
                        </div>
                        <div class="col-md-6 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">Kategori Aset</label>
                            <select id="add_asset_category" class="form-select form-select-solid" required>
                                <option value="hardware">Hardware (Laptop, PC, Monitor)</option>
                                <option value="server">Server & Storage</option>
                                <option value="network">Perangkat Jaringan (Router, Switch)</option>
                                <option value="software_license">Lisensi Software</option>
                            </select>
                        </div>
                    </div>

                    <div class="fv-row mb-7">
                        <label class="required fs-6 fw-semibold mb-2">Nama Perangkat / Aset</label>
                        <input type="text" class="form-control form-control-solid" id="add_asset_name" placeholder="Contoh: MacBook Pro M2 Max 32GB" required />
                    </div>

                    <div class="row g-9 mb-7">
                        <div class="col-md-6 fv-row">
                            <label class="fs-6 fw-semibold mb-2">Nomor Seri / S/N</label>
                            <input type="text" class="form-control form-control-solid" id="add_asset_serial_number" placeholder="Nomor seri resmi pabrikan" />
                        </div>
                        <div class="col-md-6 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">Status Operasional</label>
                            <select id="add_asset_status" class="form-select form-select-solid" required>
                                <option value="in_use">In Use (Sedang Digunakan)</option>
                                <option value="available" selected>Available (Tersedia di Gudang)</option>
                                <option value="maintenance">Maintenance (Perbaikan)</option>
                                <option value="retired">Retired (Afkir / Tidak Aktif)</option>
                            </select>
                        </div>
                    </div>

                    <div class="separator separator-dashed my-7"></div>

                    <!-- Penugasan Karyawan / Departemen -->
                    <div class="row g-9 mb-7">
                        <div class="col-md-6 fv-row">
                            <label class="fs-6 fw-semibold mb-2">Departemen Penempatan</label>
                            <select id="add_asset_department_id" class="form-select form-select-solid">
                                <option value="">Tanpa Departemen (Umum)</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" data-company="{{ $dept->company_id }}">{{ $dept->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 fv-row">
                            <label class="fs-6 fw-semibold mb-2">User Penanggung Jawab</label>
                            <select id="add_asset_user_id" class="form-select form-select-solid">
                                <option value="">Belum Ditugaskan</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" data-company="{{ $user->company_id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="fv-row mb-7">
                        <label class="fs-6 fw-semibold mb-2">Catatan Spesifikasi / Keterangan</label>
                        <textarea class="form-control form-control-solid" id="add_asset_notes" rows="3" placeholder="Informasi spesifikasi CPU, RAM, garansi, lokasi rak..."></textarea>
                    </div>
                </div>

                <div class="modal-footer flex-center">
                    <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="btn_submit_add_asset" class="btn btn-primary">
                        <span class="indicator-label">Simpan Aset</span>
                        <span class="indicator-progress">Menyimpan... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
