<!-- Modal Buat Tiket Baru -->
<div class="modal fade" id="kt_modal_create_ticket" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-750px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Buat Tiket Baru</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>

            <form id="form_create_ticket" class="form" novalidate>
                <div class="modal-body py-10 px-lg-17">
                    <!-- Tenant Selection -->
                    <div class="fv-row mb-7">
                        <label class="required fs-6 fw-semibold mb-2">Perusahaan / Tenant</label>
                        <select id="create_ticket_company_id" class="form-select form-select-solid" required>
                            <option value="">-- Pilih Perusahaan --</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}" {{ $loop->first ? 'selected' : '' }}>
                                    {{ $company->name }} ({{ $company->slug }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row g-9 mb-7">
                        <!-- Kategori Tiket -->
                        <div class="col-md-6 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">Kategori Masalah</label>
                            <select id="create_ticket_category_id" class="form-select form-select-solid" required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" 
                                            data-company="{{ $cat->company_id }}" 
                                            data-department="{{ $cat->department_id }}" 
                                            data-priority="{{ $cat->default_priority->value ?? 'medium' }}"
                                            data-approval="{{ $cat->requires_approval ? '1' : '0' }}">
                                        {{ $cat->name }} {{ $cat->requires_approval ? '🔒 (Perlu Approval)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="text-muted fs-8 mt-1" id="category_approval_hint"></div>
                        </div>

                        <!-- Departemen Penanggung Jawab -->
                        <div class="col-md-6 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">Departemen Tujuan</label>
                            <select id="create_ticket_department_id" class="form-select form-select-solid" required>
                                <option value="">-- Pilih Departemen --</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" data-company="{{ $dept->company_id }}">
                                        {{ $dept->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row g-9 mb-7">
                        <!-- Prioritas Tiket -->
                        <div class="col-md-6 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">Tingkat Prioritas (SLA)</label>
                            <select id="create_ticket_priority" class="form-select form-select-solid" required>
                                <option value="low">Low (Santai - SLA Panjang)</option>
                                <option value="medium" selected>Medium (Normal)</option>
                                <option value="high">High (Penting - SLA Dipercepat)</option>
                                <option value="urgent">Urgent (Kritis / Darurat - Penanganan Segera)</option>
                            </select>
                            <div class="text-muted fs-8 mt-1">Target respon & resolusi akan disesuaikan secara otomatis.</div>
                        </div>

                        <!-- Pelapor (Requester) -->
                        <div class="col-md-6 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">Pelapor (Requester)</label>
                            <select id="create_ticket_requester_id" class="form-select form-select-solid" required>
                                <option value="">-- Pilih Pelapor --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" data-company="{{ $user->company_id }}">
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Penautan Aset CMDB (Opsional) -->
                    <div class="fv-row mb-7">
                        <label class="fs-6 fw-semibold mb-2">Tautkan Aset Inventaris / Perangkat (Opsional)</label>
                        <select id="create_ticket_asset_id" class="form-select form-select-solid">
                            <option value="">-- Tidak Terkait Aset Tertentu --</option>
                            @foreach($assets as $asset)
                                <option value="{{ $asset->id }}" data-company="{{ $asset->company_id }}">
                                    [{{ $asset->asset_tag }}] {{ $asset->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="text-muted fs-8 mt-1">Pilih perangkat jika keluhan ini terkait kerusakan laptop, PC kantor, atau server.</div>
                    </div>

                    <!-- Subjek Tiket -->
                    <div class="fv-row mb-7">
                        <label class="required fs-6 fw-semibold mb-2">Subjek Masalah</label>
                        <input type="text" class="form-control form-control-solid" id="create_ticket_subject" placeholder="Ringkasan singkat kendala yang dialami..." required maxlength="200" />
                    </div>

                    <!-- Deskripsi Rinci Masalah -->
                    <div class="fv-row mb-7">
                        <label class="required fs-6 fw-semibold mb-2">Deskripsi Rinci</label>
                        <textarea class="form-control form-control-solid" id="create_ticket_description" rows="5" placeholder="Jelaskan secara detail kronologi masalah, error yang muncul, atau permohonan yang diajukan..." required></textarea>
                    </div>

                    <!-- Penugasan Awal Teknisi (Opsional) -->
                    <div class="fv-row mb-7">
                        <label class="fs-6 fw-semibold mb-2">Tugaskan Teknisi (Opsional)</label>
                        <select id="create_ticket_assigned_to" class="form-select form-select-solid">
                            <option value="">-- Biarkan Masuk Antrean (Unassigned) --</option>
                            @foreach($users as $user)
                                @if(in_array($user->role->value ?? $user->role, ['agent', 'company_admin', 'superadmin']))
                                    <option value="{{ $user->id }}" data-company="{{ $user->company_id }}">
                                        {{ $user->name }} - {{ $user->job_title ?? 'Teknisi' }} ({{ $user->email }})
                                    </option>
                                @endif
                            @endforeach
                        </select>
                        <div class="text-muted fs-8 mt-1">Bila dikosongkan, tiket akan masuk ke antrean "Unassigned".</div>
                    </div>
                </div>

                <div class="modal-footer flex-center">
                    <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="btn_submit_create_ticket" class="btn btn-primary">
                        <span class="indicator-label">Buat Tiket</span>
                        <span class="indicator-progress">Memproses...
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
