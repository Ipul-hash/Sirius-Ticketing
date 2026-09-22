<!-- Modal Quick Assign Teknisi -->
<div class="modal fade" id="kt_modal_assign_ticket" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-500px">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="fw-bold mb-0">Tugaskan Teknisi</h3>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>

            <form id="form_assign_ticket" class="form">
                <input type="hidden" id="quick_assign_ticket_id" />

                <div class="modal-body py-6 px-lg-10">
                    <div class="mb-5 pb-3 border-bottom">
                        <span class="badge badge-light-primary fw-bold fs-7 mb-1" id="quick_assign_ticket_number"></span>
                        <div class="fw-bold text-gray-800 fs-6 text-truncate" id="quick_assign_ticket_subject"></div>
                    </div>

                    <div class="fv-row mb-6">
                        <label class="required fs-6 fw-semibold mb-2">Pilih Teknisi / Staf</label>
                        <select id="quick_assign_user_id" class="form-select form-select-solid" required>
                            <option value="">-- Pilih Teknisi --</option>
                            @foreach($users as $user)
                                @if(in_array($user->role->value ?? $user->role, ['agent', 'company_admin', 'superadmin']))
                                    <option value="{{ $user->id }}" data-company="{{ $user->company_id }}">
                                        {{ $user->name }} - {{ $user->job_title ?? 'Staf IT' }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="fv-row mb-4">
                        <label class="fs-6 fw-semibold mb-2">Catatan Penugasan (Opsional)</label>
                        <textarea class="form-control form-control-solid" id="quick_assign_notes" rows="2" placeholder="Contoh: Mohon diprioritaskan penanganannya..."></textarea>
                    </div>
                </div>

                <div class="modal-footer flex-center">
                    <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="btn_submit_quick_assign" class="btn btn-primary">
                        <span class="indicator-label">Tugaskan Tiket</span>
                        <span class="indicator-progress">Menyimpan...
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
