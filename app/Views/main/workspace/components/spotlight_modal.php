<!-- Command Palette (Ctrl + K) Spotlight Search Modal -->
<div class="modal fade ws-spotlight-modal" id="wsSpotlightModal" tabindex="-1" aria-labelledby="wsSpotlightModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden" style="background: rgba(255, 255, 255, 0.98); backdrop-filter: blur(16px);">
            
            <!-- Spotlight Input Box -->
            <div class="p-3 border-bottom d-flex align-items-center gap-3 bg-white">
                <i class="fas fa-search text-primary fs-5 ms-2"></i>
                <input type="text" 
                       id="wsSpotlightInput" 
                       class="form-control form-control-lg border-0 shadow-none px-0 fs-6 fw-medium text-navy" 
                       placeholder="Ketik navigasi, nama kelas, event, atau regulasi PBJ..." 
                       autocomplete="off">
                <span id="wsSpotlightSpinner" class="spinner-border spinner-border-sm text-primary d-none" role="status"></span>
                <button type="button" class="btn btn-sm btn-light rounded-circle text-muted" id="wsSpotlightClearBtn" style="width: 32px; height: 32px; display: none;">
                    <i class="fas fa-times"></i>
                </button>
                <kbd class="bg-light text-muted border px-2 py-1 rounded small d-none d-sm-inline font-monospace" style="font-size: 0.72rem;">ESC</kbd>
            </div>

            <!-- Spotlight Results Body -->
            <div class="modal-body p-0" id="wsSpotlightResults" style="max-height: 440px; overflow-y: auto;">
                <div class="p-4 text-center text-muted" id="wsSpotlightInitial">
                    <div class="avatar-circle mx-auto mb-2 bg-light text-primary" style="width: 48px; height: 48px; font-size: 1.25rem;">
                        <i class="fas fa-compass"></i>
                    </div>
                    <h6 class="fw-bold text-navy mb-1">Pencarian Cepat Workspace</h6>
                    <p class="small text-muted mb-0" style="max-width: 420px; margin: 0 auto;">
                        Ketik kata kunci untuk melompat langsung ke kelas, tugas, berkas, atau agenda tanpa klik berulang kali di menu.
                    </p>
                </div>
                <div id="wsSpotlightList" class="p-2"></div>
            </div>

            <!-- Spotlight Keyboard Shortcuts Footer -->
            <div class="p-2 px-3 bg-light border-top d-flex justify-content-between align-items-center text-muted small" style="font-size: 0.72rem;">
                <div class="d-flex align-items-center gap-2">
                    <span><kbd class="bg-white border px-1.5 py-0.5 rounded shadow-2xs">↑</kbd> <kbd class="bg-white border px-1.5 py-0.5 rounded shadow-2xs">↓</kbd> Navigasi</span>
                    <span>&middot;</span>
                    <span><kbd class="bg-white border px-1.5 py-0.5 rounded shadow-2xs">↵</kbd> Buka</span>
                    <span>&middot;</span>
                    <span><kbd class="bg-white border px-1.5 py-0.5 rounded shadow-2xs">ESC</kbd> Tutup</span>
                </div>
                <span class="text-secondary fw-semibold">
                    <i class="fas fa-bolt text-warning me-1"></i>Begawi Spotlight
                </span>
            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const spotlightModalEl = document.getElementById('wsSpotlightModal');
    if (!spotlightModalEl) return;

    let spotlightBsModal = null;
    const inputEl = document.getElementById('wsSpotlightInput');
    const spinnerEl = document.getElementById('wsSpotlightSpinner');
    const clearBtnEl = document.getElementById('wsSpotlightClearBtn');
    const initialEl = document.getElementById('wsSpotlightInitial');
    const listEl = document.getElementById('wsSpotlightList');
    const searchUrl = '<?= route_to('workspace.search.spotlight') ?>';

    let searchTimer = null;
    let selectedIndex = -1;
    let allRenderedItems = [];

    function getModalInstance() {
        if (!spotlightBsModal && window.bootstrap) {
            spotlightBsModal = new bootstrap.Modal(spotlightModalEl);
        }
        return spotlightBsModal;
    }

    // Toggle Modal via Ctrl + K or Cmd + K
    window.addEventListener('keydown', function (e) {
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
            e.preventDefault();
            const modal = getModalInstance();
            if (modal) {
                if (spotlightModalEl.classList.contains('show')) {
                    modal.hide();
                } else {
                    modal.show();
                }
            }
        }
    });

    // Auto-focus input on modal open & initial load
    spotlightModalEl.addEventListener('shown.bs.modal', function () {
        inputEl.focus();
        if (inputEl.value.trim() === '') {
            fetchResults('');
        }
    });

    spotlightModalEl.addEventListener('hidden.bs.modal', function () {
        selectedIndex = -1;
    });

    // Clear input
    clearBtnEl.addEventListener('click', function () {
        inputEl.value = '';
        clearBtnEl.style.display = 'none';
        fetchResults('');
        inputEl.focus();
    });

    // Debounced Input Handler
    inputEl.addEventListener('input', function () {
        const val = inputEl.value.trim();
        clearBtnEl.style.display = val.length > 0 ? 'inline-block' : 'none';

        clearTimeout(searchTimer);
        searchTimer = setTimeout(function () {
            fetchResults(val);
        }, 180);
    });

    // Keyboard Navigation (ArrowUp, ArrowDown, Enter)
    inputEl.addEventListener('keydown', function (e) {
        if (allRenderedItems.length === 0) return;

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            selectedIndex = (selectedIndex + 1) % allRenderedItems.length;
            updateActiveItem();
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            selectedIndex = (selectedIndex - 1 + allRenderedItems.length) % allRenderedItems.length;
            updateActiveItem();
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (selectedIndex >= 0 && selectedIndex < allRenderedItems.length) {
                const targetUrl = allRenderedItems[selectedIndex].getAttribute('href');
                if (targetUrl) {
                    window.location.href = targetUrl;
                }
            }
        }
    });

    function updateActiveItem() {
        allRenderedItems.forEach(function (el, idx) {
            if (idx === selectedIndex) {
                el.classList.add('is-active');
                el.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
            } else {
                el.classList.remove('is-active');
            }
        });
    }

    function fetchResults(keyword) {
        spinnerEl.classList.remove('d-none');
        fetch(searchUrl + '?q=' + encodeURIComponent(keyword))
            .then(function (res) { return res.json(); })
            .then(function (data) {
                spinnerEl.classList.add('d-none');
                renderResults(data, keyword);
            })
            .catch(function () {
                spinnerEl.classList.add('d-none');
            });
    }

    function renderResults(data, keyword) {
        selectedIndex = -1;
        allRenderedItems = [];

        if (!data || !data.categories || data.categories.length === 0) {
            initialEl.style.display = 'block';
            initialEl.innerHTML = `
                <div class="avatar-circle mx-auto mb-2 bg-light text-secondary" style="width: 48px; height: 48px; font-size: 1.25rem;">
                    <i class="fas fa-search"></i>
                </div>
                <h6 class="fw-bold text-navy mb-1">Tidak Ditemukan Hasil</h6>
                <p class="small text-muted mb-0">Tidak ada item yang sesuai dengan kata kunci "${keyword}". Coba kata kunci lainnya.</p>
            `;
            listEl.innerHTML = '';
            return;
        }

        initialEl.style.display = 'none';
        let html = '';

        data.categories.forEach(function (cat) {
            html += `
                <div class="ws-spotlight-category mb-3">
                    <div class="d-flex align-items-center gap-2 px-3 py-1 text-uppercase text-muted fw-bold" style="font-size: 0.68rem; letter-spacing: 0.5px;">
                        <i class="${cat.icon} text-primary"></i>
                        <span>${cat.name}</span>
                    </div>
                    <div class="list-group list-group-flush rounded-3 overflow-hidden">
            `;

            cat.items.forEach(function (item) {
                html += `
                    <a href="${item.url}" class="list-group-item list-group-item-action ws-spotlight-item p-2 px-3 border-0 d-flex align-items-center justify-content-between gap-3">
                        <div class="d-flex align-items-center gap-3 overflow-hidden">
                            <div class="avatar-circle flex-shrink-0 bg-light text-primary" style="width: 36px; height: 36px; font-size: 0.95rem;">
                                <i class="${item.icon}"></i>
                            </div>
                            <div class="overflow-hidden">
                                <strong class="text-navy d-block text-truncate small" style="font-size: 0.85rem;">${item.title}</strong>
                                <small class="text-muted d-block text-truncate" style="font-size: 0.72rem;">${item.subtitle || ''}</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2 flex-shrink-0">
                            ${item.badge ? `<span class="badge bg-secondary-subtle text-secondary small rounded-pill px-2 py-1" style="font-size: 0.65rem;">${item.badge}</span>` : ''}
                            <i class="fas fa-chevron-right text-muted opacity-50 small"></i>
                        </div>
                    </a>
                `;
            });

            html += `
                    </div>
                </div>
            `;
        });

        listEl.innerHTML = html;
        allRenderedItems = Array.from(listEl.querySelectorAll('.ws-spotlight-item'));

        // Highlight first item if user typed something
        if (allRenderedItems.length > 0 && keyword !== '') {
            selectedIndex = 0;
            updateActiveItem();
        }

        // Mouse hover selection sync
        allRenderedItems.forEach(function (el, idx) {
            el.addEventListener('mouseenter', function () {
                selectedIndex = idx;
                updateActiveItem();
            });
        });
    }
});
</script>
