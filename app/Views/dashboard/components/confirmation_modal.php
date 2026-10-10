<!-- Universal WowDash Confirmation Action Modal -->
<div class="modal fade" id="confirmationActionModal" tabindex="-1" aria-labelledby="confirmationActionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content radius-16 bg-base">
            <div class="modal-body p-24 text-center">
                <span class="mb-16 fs-1 line-height-1 d-inline-flex justify-content-center align-items-center w-56-px h-56-px rounded-circle bg-danger-100 text-danger-600 mx-auto" id="modalActionIconWrap">
                    <iconify-icon icon="fluent:delete-24-regular" id="modalActionIcon" class="text-2xl"></iconify-icon>
                </span>
                <h6 class="text-lg fw-semibold text-primary-light mb-8" id="modalActionTitle">Konfirmasi Tindakan</h6>
                <p class="text-sm text-secondary-light mb-0" id="modalActionMessage">Apakah Anda yakin ingin melanjutkan tindakan ini?</p>
                
                <form id="modalActionForm" method="post" action="">
                    <?= csrf_field() ?>
                    <input type="hidden" name="_method" id="modalActionMethod" value="POST">
                    
                    <div id="modalActionInputContainer" class="mt-16 text-start d-none">
                        <label class="form-label text-sm fw-medium text-secondary-light mb-4" id="modalActionInputLabel">Alasan / Catatan</label>
                        <textarea class="form-control radius-8 text-sm" name="reason" id="modalActionInputText" rows="3" placeholder="Masukkan alasan atau catatan..."></textarea>
                    </div>

                    <div class="d-flex align-items-center justify-content-center gap-3 mt-24">
                        <button type="button" class="w-50 border border-neutral-300 bg-hover-neutral-200 text-secondary-light text-md px-16 py-11 radius-8" data-bs-dismiss="modal">
                            Batal
                        </button>
                        <button type="submit" class="w-50 btn btn-danger-600 text-md px-16 py-11 radius-8" id="modalActionConfirmBtn">
                            Konfirmasi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modalEl = document.getElementById('confirmationActionModal');
    if (!modalEl) return;

    modalEl.addEventListener('show.bs.modal', function (event) {
        const trigger = event.relatedTarget;
        if (!trigger) return;

        const action = trigger.getAttribute('data-action') || '#';
        const method = trigger.getAttribute('data-method') || 'POST';
        const title = trigger.getAttribute('data-title') || 'Konfirmasi Tindakan';
        const message = trigger.getAttribute('data-message') || 'Apakah Anda yakin ingin melanjutkan tindakan ini?';
        const btnText = trigger.getAttribute('data-btn-text') || 'Konfirmasi';
        const btnClass = trigger.getAttribute('data-btn-class') || 'btn-danger-600';
        const icon = trigger.getAttribute('data-icon') || 'fluent:delete-24-regular';
        const color = trigger.getAttribute('data-icon-color') || 'danger';
        const requireInput = trigger.getAttribute('data-require-input') === 'true';
        const inputLabel = trigger.getAttribute('data-input-label') || 'Alasan / Catatan';
        const inputName = trigger.getAttribute('data-input-name') || 'reason';
        const inputPlaceholder = trigger.getAttribute('data-input-placeholder') || 'Masukkan keterangan...';

        // Update form action & method
        const form = document.getElementById('modalActionForm');
        form.action = action;
        document.getElementById('modalActionMethod').value = method;

        // Update texts
        document.getElementById('modalActionTitle').textContent = title;
        document.getElementById('modalActionMessage').textContent = message;

        // Update Icon & Colors
        const iconEl = document.getElementById('modalActionIcon');
        iconEl.setAttribute('icon', icon);
        const iconWrap = document.getElementById('modalActionIconWrap');
        iconWrap.className = 'mb-16 fs-1 line-height-1 d-inline-flex justify-content-center align-items-center w-56-px h-56-px rounded-circle mx-auto bg-' + color + '-100 text-' + color + '-600';

        // Update Button
        const confirmBtn = document.getElementById('modalActionConfirmBtn');
        confirmBtn.textContent = btnText;
        confirmBtn.className = 'w-50 btn ' + btnClass + ' text-md px-16 py-11 radius-8';

        // Update input field
        const inputContainer = document.getElementById('modalActionInputContainer');
        const inputText = document.getElementById('modalActionInputText');
        const labelEl = document.getElementById('modalActionInputLabel');

        if (requireInput) {
            inputContainer.classList.remove('d-none');
            labelEl.textContent = inputLabel;
            inputText.name = inputName;
            inputText.placeholder = inputPlaceholder;
            inputText.required = true;
            inputText.value = '';
        } else {
            inputContainer.classList.add('d-none');
            inputText.required = false;
            inputText.value = '';
        }
    });
});
</script>
