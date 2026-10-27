<!-- Universal Standardized Dashboard Confirmation Action Modal -->
<div class="modal fade" id="confirmationActionModal" tabindex="-1" aria-labelledby="confirmationActionModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-sm" style="max-width: 440px;">
        <div class="modal-content radius-16 bg-base border-0 shadow-lg overflow-hidden position-relative">
            <!-- Top Color Accent Bar -->
            <div id="modalActionColorBar" style="height: 5px; width: 100%; transition: background-color 0.2s ease;" class="bg-danger-600"></div>

            <div class="modal-body p-24 text-center">
                <!-- Icon with Glowing Border & Soft Background -->
                <div class="mb-16 d-inline-flex justify-content-center align-items-center w-64-px h-64-px rounded-circle mx-auto shadow-sm transition-all" id="modalActionIconWrap" style="transition: all 0.2s ease;">
                    <iconify-icon icon="solar:danger-triangle-bold" id="modalActionIcon" class="text-3xl"></iconify-icon>
                </div>

                <!-- Context Badge -->
                <div class="mb-8">
                    <span id="modalActionBadge" class="badge radius-4 px-10 py-3 text-2xs fw-bold text-uppercase letter-spacing-1">
                        Konfirmasi
                    </span>
                </div>

                <!-- Title & Message -->
                <h6 class="text-lg fw-bold text-primary-light mb-8" id="modalActionTitle">Konfirmasi Tindakan</h6>
                <p class="text-sm text-secondary-light mb-0 line-height-1-5 px-8" id="modalActionMessage">Apakah Anda yakin ingin melanjutkan tindakan ini?</p>
                
                <!-- Action Form -->
                <form id="modalActionForm" method="post" action="" class="mt-16">
                    <?= csrf_field() ?>
                    <input type="hidden" name="_method" id="modalActionMethod" value="POST">
                    
                    <!-- Optional Reason / Note Input Container -->
                    <div id="modalActionInputContainer" class="text-start d-none mb-16">
                        <label class="form-label text-xs fw-semibold text-secondary-dark mb-4" id="modalActionInputLabel">Alasan / Catatan</label>
                        <textarea class="form-control radius-8 text-sm py-8 px-12" name="reason" id="modalActionInputText" rows="3" placeholder="Masukkan alasan atau catatan..."></textarea>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex align-items-center justify-content-center gap-12 mt-20 pt-16 border-top">
                        <button type="button" class="w-50 btn btn-outline-neutral-300 text-secondary-dark text-sm px-16 py-10 radius-8 fw-medium" data-bs-dismiss="modal">
                            Batal
                        </button>
                        <button type="submit" class="w-50 btn btn-danger-600 text-sm px-16 py-10 radius-8 fw-semibold shadow-xs d-inline-flex align-items-center justify-content-center gap-1" id="modalActionConfirmBtn">
                            <span>Konfirmasi</span>
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

    let pendingTriggerForm = null;
    let pendingTriggerLink = null;
    let pendingResolve = null;

    // Standard Color Palettes & Config
    const typeConfigs = {
        danger: {
            color: 'danger',
            barClass: 'bg-danger-600',
            iconWrap: 'bg-danger-50 text-danger-600 border border-danger-200',
            badgeClass: 'bg-danger-100 text-danger-700',
            badgeText: 'Peringatan / Hapus',
            btnClass: 'btn-danger-600 text-white',
            defaultIcon: 'solar:trash-bin-trash-bold',
            btnText: 'Ya, Lanjutkan Hapus'
        },
        warning: {
            color: 'warning',
            barClass: 'bg-warning-500',
            iconWrap: 'bg-warning-50 text-warning-main border border-warning-200',
            badgeClass: 'bg-warning-100 text-warning-800',
            badgeText: 'Perhatian / Tindakan',
            btnClass: 'btn-warning-600 text-white',
            defaultIcon: 'solar:restart-bold',
            btnText: 'Ya, Lanjutkan'
        },
        success: {
            color: 'success',
            barClass: 'bg-success-600',
            iconWrap: 'bg-success-50 text-success-600 border border-success-200',
            badgeClass: 'bg-success-100 text-success-700',
            badgeText: 'Persetujuan / Aktifkan',
            btnClass: 'btn-success-600 text-white',
            defaultIcon: 'solar:check-circle-bold',
            btnText: 'Ya, Setujui'
        },
        info: {
            color: 'primary',
            barClass: 'bg-primary-600',
            iconWrap: 'bg-primary-50 text-primary-600 border border-primary-200',
            badgeClass: 'bg-primary-100 text-primary-700',
            badgeText: 'Informasi / Tindakan',
            btnClass: 'btn-primary-600 text-white',
            defaultIcon: 'solar:info-circle-bold',
            btnText: 'Lanjutkan'
        },
        primary: {
            color: 'primary',
            barClass: 'bg-primary-600',
            iconWrap: 'bg-primary-50 text-primary-600 border border-primary-200',
            badgeClass: 'bg-primary-100 text-primary-700',
            badgeText: 'Konfirmasi',
            btnClass: 'btn-primary-600 text-white',
            defaultIcon: 'solar:refresh-circle-bold',
            btnText: 'Lanjutkan'
        }
    };

    function detectActionType(str) {
        const s = (str || '').toLowerCase();
        if (s.includes('danger') || s.includes('hapus') || s.includes('delete') || s.includes('destroy') || s.includes('cabut') || s.includes('tolak') || s.includes('reject') || s.includes('purge')) {
            return 'danger';
        }
        if (s.includes('warning') || s.includes('requeue') || s.includes('regenerate') || s.includes('reset') || s.includes('suspend') || s.includes('nonaktif') || s.includes('batal') || s.includes('revisi')) {
            return 'warning';
        }
        if (s.includes('success') || s.includes('setuju') || s.includes('approve') || s.includes('aktif') || s.includes('terbit') || s.includes('publish') || s.includes('verifikasi')) {
            return 'success';
        }
        if (s.includes('info') || s.includes('primary') || s.includes('sinkron') || s.includes('proses') || s.includes('kirim')) {
            return 'primary';
        }
        return 'danger';
    }

    function applyModalConfig(opts) {
        const type = (opts.type || 'danger').toLowerCase();
        const conf = typeConfigs[type] || typeConfigs.danger;

        // 1. Color Bar
        const barEl = document.getElementById('modalActionColorBar');
        barEl.className = conf.barClass;

        // 2. Icon & Icon Wrapper
        const iconWrap = document.getElementById('modalActionIconWrap');
        iconWrap.className = 'mb-16 d-inline-flex justify-content-center align-items-center w-64-px h-64-px rounded-circle mx-auto shadow-sm ' + (opts.iconWrapClass || conf.iconWrap);

        const iconEl = document.getElementById('modalActionIcon');
        iconEl.setAttribute('icon', opts.icon || conf.defaultIcon);

        // 3. Badge
        const badgeEl = document.getElementById('modalActionBadge');
        badgeEl.className = 'badge radius-4 px-10 py-3 text-2xs fw-bold text-uppercase letter-spacing-1 ' + (opts.badgeClass || conf.badgeClass);
        badgeEl.textContent = opts.badgeText || conf.badgeText;

        // 4. Title & Message
        document.getElementById('modalActionTitle').textContent = opts.title || (type === 'danger' ? 'Konfirmasi Penghapusan' : (type === 'warning' ? 'Konfirmasi Tindakan' : (type === 'success' ? 'Konfirmasi Persetujuan' : 'Konfirmasi Tindakan')));
        document.getElementById('modalActionMessage').textContent = opts.message || 'Apakah Anda yakin ingin melanjutkan tindakan ini?';

        // 5. Button
        const confirmBtn = document.getElementById('modalActionConfirmBtn');
        confirmBtn.innerHTML = '<span>' + (opts.btnText || conf.btnText) + '</span>';
        confirmBtn.className = 'w-50 btn ' + (opts.btnClass || conf.btnClass) + ' text-sm px-16 py-10 radius-8 fw-semibold shadow-xs d-inline-flex align-items-center justify-content-center gap-1';

        // 6. Form setup
        const form = document.getElementById('modalActionForm');
        form.action = opts.action || '#';
        document.getElementById('modalActionMethod').value = opts.method || 'POST';

        // 7. Input Container
        const inputContainer = document.getElementById('modalActionInputContainer');
        const inputText = document.getElementById('modalActionInputText');
        const labelEl = document.getElementById('modalActionInputLabel');

        if (opts.requireInput) {
            inputContainer.classList.remove('d-none');
            labelEl.textContent = opts.inputLabel || 'Alasan / Catatan *';
            inputText.name = opts.inputName || 'reason';
            inputText.placeholder = opts.inputPlaceholder || 'Masukkan catatan...';
            inputText.required = true;
            inputText.value = '';
        } else {
            inputContainer.classList.add('d-none');
            inputText.required = false;
            inputText.value = '';
        }
    }

    // Modal show.bs.modal listener (declarative data-bs-toggle="modal")
    modalEl.addEventListener('show.bs.modal', function (event) {
        const trigger = event.relatedTarget;
        if (!trigger) return;

        pendingTriggerForm = null;
        pendingTriggerLink = null;
        pendingResolve = null;

        // Auto-detect type from attributes/classes if not explicit
        let detectedType = trigger.getAttribute('data-type');
        if (!detectedType) {
            const btnClass = trigger.className || '';
            const color = trigger.getAttribute('data-icon-color') || '';
            const actionStr = (trigger.getAttribute('data-action') || trigger.getAttribute('href') || '') + ' ' + (trigger.getAttribute('title') || '') + ' ' + (trigger.textContent || '');
            detectedType = detectActionType(color + ' ' + btnClass + ' ' + actionStr);
        }

        applyModalConfig({
            type: detectedType,
            action: trigger.getAttribute('data-action') || trigger.getAttribute('href') || '#',
            method: trigger.getAttribute('data-method') || 'POST',
            title: trigger.getAttribute('data-title'),
            message: trigger.getAttribute('data-message') || trigger.getAttribute('data-confirm') || 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
            btnText: trigger.getAttribute('data-btn-text'),
            btnClass: trigger.getAttribute('data-btn-class'),
            icon: trigger.getAttribute('data-icon'),
            requireInput: trigger.getAttribute('data-require-input') === 'true',
            inputLabel: trigger.getAttribute('data-input-label'),
            inputName: trigger.getAttribute('data-input-name'),
            inputPlaceholder: trigger.getAttribute('data-input-placeholder')
        });
    });

    // Cleanup and promise resolution on modal dismiss
    modalEl.addEventListener('hidden.bs.modal', function () {
        if (pendingResolve) {
            const res = pendingResolve;
            pendingResolve = null;
            res(false);
        }
        pendingTriggerForm = null;
        pendingTriggerLink = null;
    });

    // Form submit inside modal
    const actionForm = document.getElementById('modalActionForm');
    if (actionForm) {
        actionForm.addEventListener('submit', function (e) {
            if (pendingResolve) {
                e.preventDefault();
                const res = pendingResolve;
                pendingResolve = null;
                bootstrap.Modal.getInstance(modalEl).hide();
                res(true);
                return;
            }

            if (pendingTriggerForm) {
                e.preventDefault();
                const targetForm = pendingTriggerForm;
                pendingTriggerForm = null;
                
                // Copy modal text input if active and needed
                const inputText = document.getElementById('modalActionInputText');
                const inputContainer = document.getElementById('modalActionInputContainer');
                if (inputText && inputText.value && !inputContainer.classList.contains('d-none')) {
                    let hidden = targetForm.querySelector(`input[name="${inputText.name}"]`);
                    if (!hidden) {
                        hidden = document.createElement('input');
                        hidden.type = 'hidden';
                        hidden.name = inputText.name;
                        targetForm.appendChild(hidden);
                    }
                    hidden.value = inputText.value;
                }

                bootstrap.Modal.getInstance(modalEl).hide();
                targetForm.__begawiConfirmed = true;
                targetForm.submit();
                return;
            }

            if (pendingTriggerLink) {
                e.preventDefault();
                const targetLink = pendingTriggerLink;
                pendingTriggerLink = null;
                bootstrap.Modal.getInstance(modalEl).hide();
                window.location.href = targetLink;
                return;
            }

            // Normal declarative modal action submission: let browser submit actionForm to action URL!
        });
    }

    // Global Interceptor for click events on elements with data-confirm or class btn-confirm-action
    document.addEventListener('click', function (e) {
        const trigger = e.target.closest('[data-confirm], .btn-confirm-action, [data-confirm-modal]');
        if (!trigger) return;

        // Skip if already using bootstrap modal attributes
        if (trigger.getAttribute('data-bs-toggle') === 'modal') return;

        e.preventDefault();
        e.stopPropagation();

        const msg = trigger.getAttribute('data-confirm') || trigger.getAttribute('data-message') || 'Apakah Anda yakin ingin melanjutkan tindakan ini?';
        const type = trigger.getAttribute('data-type') || detectActionType((trigger.className || '') + ' ' + (trigger.getAttribute('href') || '') + ' ' + msg);
        const title = trigger.getAttribute('data-title');
        const action = trigger.getAttribute('data-action') || trigger.getAttribute('href') || '';
        const method = trigger.getAttribute('data-method') || 'POST';

        pendingTriggerForm = trigger.closest('form');
        pendingTriggerLink = (trigger.tagName === 'A' && action && !action.startsWith('#')) ? action : null;
        pendingResolve = null;

        applyModalConfig({
            type: type,
            action: action || '#',
            method: method,
            title: title,
            message: msg,
            btnText: trigger.getAttribute('data-btn-text'),
            btnClass: trigger.getAttribute('data-btn-class'),
            icon: trigger.getAttribute('data-icon'),
            requireInput: trigger.getAttribute('data-require-input') === 'true',
            inputLabel: trigger.getAttribute('data-input-label'),
            inputName: trigger.getAttribute('data-input-name'),
            inputPlaceholder: trigger.getAttribute('data-input-placeholder')
        });

        bootstrap.Modal.getOrCreateInstance(modalEl).show();
    }, true);

    // Universal DOM Auto-Upgrade for legacy inline confirms
    function autoUpgradeConfirmElements(root) {
        const scope = root || document;

        // 1. Upgrade forms with onsubmit="return confirm(...)"
        scope.querySelectorAll('form').forEach(form => {
            const onsubmitStr = form.getAttribute('onsubmit') || '';
            if (onsubmitStr.includes('confirm(')) {
                let msg = 'Apakah Anda yakin ingin melanjutkan tindakan ini?';
                const match = onsubmitStr.match(/confirm\s*\(\s*(['"`])(.*?)\1\s*\)/s);
                if (match && match[2]) {
                    msg = match[2];
                }

                form.removeAttribute('onsubmit');
                form.onsubmit = null;

                const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
                const detectContext = (form.action || '') + ' ' + onsubmitStr + ' ' + (submitBtn?.className || '') + ' ' + msg;
                const type = detectActionType(detectContext);

                form.addEventListener('submit', function (e) {
                    if (form.__begawiConfirmed) {
                        delete form.__begawiConfirmed;
                        return; // proceed to submit
                    }
                    e.preventDefault();
                    pendingTriggerForm = form;
                    pendingTriggerLink = null;
                    pendingResolve = null;

                    applyModalConfig({
                        type: type,
                        action: form.action,
                        method: form.method || 'POST',
                        message: msg,
                        title: type === 'danger' ? 'Konfirmasi Penghapusan' : (type === 'warning' ? 'Konfirmasi Tindakan' : (type === 'success' ? 'Konfirmasi Persetujuan' : 'Konfirmasi Tindakan')),
                        btnText: type === 'danger' ? 'Ya, Lanjutkan Hapus' : (type === 'warning' ? 'Ya, Lanjutkan' : (type === 'success' ? 'Ya, Setujui' : 'Lanjutkan'))
                    });

                    bootstrap.Modal.getOrCreateInstance(modalEl).show();
                });
            }
        });

        // 2. Upgrade buttons/links with onclick="return confirm(...)"
        scope.querySelectorAll('[onclick]').forEach(el => {
            const onclickStr = el.getAttribute('onclick') || '';
            if (onclickStr.includes('confirm(')) {
                let msg = 'Apakah Anda yakin ingin melanjutkan tindakan ini?';
                const match = onclickStr.match(/confirm\s*\(\s*(['"`])(.*?)\1\s*\)/s);
                if (match && match[2]) {
                    msg = match[2];
                }

                el.removeAttribute('onclick');
                el.onclick = null;

                const detectContext = (el.className || '') + ' ' + (el.getAttribute('href') || '') + ' ' + onclickStr + ' ' + msg;
                const type = detectActionType(detectContext);

                el.setAttribute('data-confirm', msg);
                el.setAttribute('data-type', type);
            }
        });
    }

    // Run auto-upgrade immediately
    autoUpgradeConfirmElements(document);

    // Provide globally accessible helper API
    window.showConfirmationModal = function (options) {
        pendingTriggerForm = null;
        pendingTriggerLink = null;
        pendingResolve = null;
        applyModalConfig(options || {});
        bootstrap.Modal.getOrCreateInstance(modalEl).show();
    };

    window.confirmModal = function (options) {
        return new Promise((resolve) => {
            pendingTriggerForm = null;
            pendingTriggerLink = null;
            pendingResolve = resolve;

            let opts = typeof options === 'string' ? { message: options } : (options || {});
            if (!opts.type) {
                opts.type = detectActionType((opts.message || '') + ' ' + (opts.title || ''));
            }
            applyModalConfig(opts);
            bootstrap.Modal.getOrCreateInstance(modalEl).show();
        });
    };

    window.upgradeBegawiConfirmElements = autoUpgradeConfirmElements;
});
</script>
