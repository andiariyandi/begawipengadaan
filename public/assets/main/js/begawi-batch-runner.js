/**
 * BegawiBatchRunner - Engine Eksekusi Batch Asinkron dengan Stay-Alive,
 * Auto-Continuation, Fault-Tolerance Retry, dan Real-Time Progress Bar.
 *
 * Mencegah timeout (504 Gateway Timeout) dan kehabisan memori PHP
 * dengan memecah beban penerbitan sertifikat / persetujuan masal
 * menjadi pecahan kecil (chunk 5-10 item) yang terus berjalan mandiri.
 */
(function (window, document) {
    'use strict';

    const BegawiBatchRunner = {
        activeRunner: null,

        /**
         * Jalankan proses batch.
         *
         * @param {Object} config
         *   - title: string
         *   - subtitle: string
         *   - prepareUrl: string|null (URL untuk fetch daftar ID jika dinamis)
         *   - prepareData: object (data untuk request prepare)
         *   - chunkUrl: string (URL endpoint pemroses chunk)
         *   - chunkDataBuilder: function(chunkIds, chunkIndex, totalChunks) => object|FormData
         *   - items: array (daftar ID atau item langsung jika sudah ada di client)
         *   - chunkSize: number (default 5 untuk sertifikat PDF, 10 untuk approval)
         *   - maxRetries: number (default 3 percobaan stay-alive sebelum jeda)
         *   - retryDelayMs: number (default 1800 ms)
         *   - onComplete: function(summary)
         */
        start: function (config) {
            const defaults = {
                title: 'Pemrosesan Batch Massal',
                subtitle: 'Sistem memproses antrean secara bertahap (chunked stay-alive) untuk mencegah timeout.',
                prepareUrl: null,
                prepareData: {},
                chunkUrl: '',
                chunkDataBuilder: null,
                items: [],
                chunkSize: 5,
                maxRetries: 3,
                retryDelayMs: 1800,
                onComplete: null,
            };

            const state = Object.assign({}, defaults, config, {
                modalEl: null,
                bsModal: null,
                isPaused: false,
                isCancelled: false,
                isFinished: false,
                currentIndex: 0,
                chunks: [],
                totalItems: 0,
                successCount: 0,
                skippedCount: 0,
                failedCount: 0,
                currentRetry: 0,
                startTime: Date.now(),
                timerInterval: null,
            });

            this.activeRunner = state;
            this.ensureModalDOM(state);
            this.openModal(state);

            if (state.prepareUrl) {
                this.executePrepare(state);
            } else if (Array.isArray(state.items) && state.items.length > 0) {
                this.initializeWithItems(state, state.items);
            } else {
                this.showError(state, 'Tidak ada antrean atau data peserta yang dapat diproses.');
            }
        },

        ensureModalDOM: function (state) {
            let modalEl = document.getElementById('begawiBatchProgressModal');
            if (!modalEl) {
                const modalHtml = `
                <div class="modal fade" id="begawiBatchProgressModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true" style="z-index: 1065;">
                    <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width: 680px;">
                        <div class="modal-content radius-16 border-0 shadow-lg overflow-hidden bg-base">
                            <!-- Top Accent Bar -->
                            <div id="bbmAccentBar" style="height: 5px; width: 100%;" class="bg-primary-600"></div>

                            <!-- Modal Header -->
                            <div class="modal-header border-bottom px-24 py-16 d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-12">
                                    <div class="w-44-px h-44-px radius-10 bg-primary-50 text-primary-600 d-flex align-items-center justify-content-center shadow-xs">
                                        <iconify-icon icon="solar:diploma-verified-bold" class="text-2xl" id="bbmHeaderIcon"></iconify-icon>
                                    </div>
                                    <div>
                                        <h6 class="mb-1 fw-bold text-primary-light" id="bbmModalTitle">Memproses Batch Sertifikat</h6>
                                        <p class="text-xs text-secondary-light mb-0" id="bbmModalSubtitle">Sistem memproses antrean secara bertahap (chunked stay-alive) untuk mencegah timeout.</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-success-50 text-success-600 border border-success-200 radius-pill px-10 py-4 text-2xs fw-semibold d-inline-flex align-items-center gap-1">
                                        <span class="w-6-px h-6-px rounded-circle bg-success-main d-inline-block animate-pulse"></span> Stay-Alive Aktif
                                    </span>
                                    <span class="badge bg-neutral-100 text-secondary-light border radius-pill px-10 py-4 text-2xs fw-medium" id="bbmChunkSizeBadge">5 Peserta/Pecahan</span>
                                </div>
                            </div>

                            <!-- Modal Body -->
                            <div class="modal-body p-24">
                                <!-- Status Line -->
                                <div class="d-flex align-items-center justify-content-between mb-12">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="spinner-border spinner-border-sm text-primary-600" id="bbmStatusSpinner" role="status">
                                            <span class="visually-hidden">Memproses...</span>
                                        </div>
                                        <span class="fw-semibold text-sm text-primary-light" id="bbmCurrentStatusText">Menyiapkan antrean batch...</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 text-xs text-secondary-light">
                                        <iconify-icon icon="solar:clock-circle-outline"></iconify-icon>
                                        <span id="bbmElapsedTime">00:00</span>
                                    </div>
                                </div>

                                <!-- Animated Progress Bar -->
                                <div class="progress radius-8 mb-8" style="height: 20px; background-color: #f1f5f9;">
                                    <div id="bbmProgressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-primary-600 fw-bold text-2xs" role="progressbar" style="width: 0%; min-width: 2.5em; transition: width 0.3s ease;">
                                        0%
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center text-xs text-secondary-light mb-20">
                                    <span id="bbmProgressItemText">0 dari 0 peserta diproses</span>
                                    <span id="bbmChunkProgressText">Pecahan 0 dari 0</span>
                                </div>

                                <!-- KPI Metric Cards -->
                                <div class="row row-cols-4 g-2 mb-20">
                                    <div class="col">
                                        <div class="p-12 radius-8 bg-neutral-100 text-center border">
                                            <span class="text-2xs text-secondary-light d-block mb-1">TOTAL TARGET</span>
                                            <h5 class="fw-bold mb-0 text-primary-light" id="bbmKpiTotal">0</h5>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="p-12 radius-8 bg-success-50 text-center border border-success-200">
                                            <span class="text-2xs text-success-600 d-block mb-1">BERHASIL</span>
                                            <h5 class="fw-bold mb-0 text-success-main" id="bbmKpiSuccess">0</h5>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="p-12 radius-8 bg-warning-50 text-center border border-warning-200">
                                            <span class="text-2xs text-warning-600 d-block mb-1">DILEWATI</span>
                                            <h5 class="fw-bold mb-0 text-warning-main" id="bbmKpiSkipped">0</h5>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="p-12 radius-8 bg-danger-50 text-center border border-danger-200">
                                            <span class="text-2xs text-danger-600 d-block mb-1">GAGAL</span>
                                            <h5 class="fw-bold mb-0 text-danger-main" id="bbmKpiFailed">0</h5>
                                        </div>
                                    </div>
                                </div>

                                <!-- Stay-Alive Retry Banner (Hidden by default) -->
                                <div id="bbmStayAliveAlert" class="alert alert-warning radius-8 py-10 px-14 mb-16 d-none align-items-center gap-10">
                                    <div class="spinner-grow spinner-grow-sm text-warning" role="status"></div>
                                    <div class="text-xs">
                                        <strong class="d-block text-warning-800" id="bbmStayAliveTitle">Koneksi Server Tertunda</strong>
                                        <span class="text-warning-700" id="bbmStayAliveMessage">Stay-Alive mendeteksi jeda respons. Mencoba ulang otomatis pecahan ini...</span>
                                    </div>
                                </div>

                                <!-- Real-Time Event Log Container -->
                                <div>
                                    <div class="d-flex align-items-center justify-content-between mb-6">
                                        <span class="text-2xs fw-bold text-uppercase text-secondary-light letter-spacing-1">Log Real-Time Aktivitas Batch</span>
                                        <span class="text-2xs text-secondary-light" id="bbmLogCount">0 entri</span>
                                    </div>
                                    <div id="bbmLogFeed" class="p-12 radius-8 border bg-neutral-50 scroll-sm" style="max-height: 160px; overflow-y: auto; font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; font-size: 0.74rem; line-height: 1.5;">
                                        <div class="text-secondary-light text-center py-12" id="bbmLogEmpty">Menunggu pemrosesan pecahan pertama...</div>
                                    </div>
                                </div>

                                <!-- Success Completion Banner -->
                                <div id="bbmCompleteBanner" class="alert alert-success radius-8 py-12 px-16 mt-16 mb-0 d-none text-center">
                                    <iconify-icon icon="solar:check-circle-bold" class="text-2xl text-success-main mb-1 d-block mx-auto"></iconify-icon>
                                    <h6 class="text-sm fw-bold text-success-main mb-1">Penerbitan Batch Selesai dengan Sukses!</h6>
                                    <p class="text-xs text-success-700 mb-0" id="bbmCompleteSummary">Seluruh pecahan batch telah diproses tuntas.</p>
                                </div>
                            </div>

                            <!-- Modal Footer -->
                            <div class="modal-footer border-top px-24 py-14 d-flex align-items-center justify-content-between bg-neutral-50">
                                <span class="text-xs text-secondary-light" id="bbmFooterHint">Jangan menutup tab browser hingga proses selesai.</span>
                                <div class="d-flex align-items-center gap-2">
                                    <button type="button" class="btn btn-sm btn-outline-neutral-400 text-secondary-light radius-8 px-14 py-8" id="bbmPauseBtn">
                                        <iconify-icon icon="solar:pause-circle-outline"></iconify-icon> Jeda
                                    </button>
                                    <button type="button" class="btn btn-sm btn-primary-600 radius-8 px-16 py-8 d-none" id="bbmResumeBtn">
                                        <iconify-icon icon="solar:play-circle-bold"></iconify-icon> Lanjutkan Batch
                                    </button>
                                    <button type="button" class="btn btn-sm btn-success-600 text-white radius-8 px-16 py-8 d-none" id="bbmCloseBtn">
                                        <iconify-icon icon="solar:check-circle-bold"></iconify-icon> Tutup &amp; Muat Ulang
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                `;
                document.body.insertAdjacentHTML('beforeend', modalHtml);
                modalEl = document.getElementById('begawiBatchProgressModal');
            }
            state.modalEl = modalEl;

            // Bind Event Listeners
            const pauseBtn = document.getElementById('bbmPauseBtn');
            const resumeBtn = document.getElementById('bbmResumeBtn');
            const closeBtn = document.getElementById('bbmCloseBtn');

            if (pauseBtn) {
                pauseBtn.onclick = () => {
                    state.isPaused = true;
                    pauseBtn.classList.add('d-none');
                    resumeBtn.classList.remove('d-none');
                    this.updateStatusText(state, 'Batch dijeda oleh pengguna.', 'warning');
                    this.appendLog(state, '⚠️ Pemrosesan batch dijeda oleh pengguna.', 'warning');
                };
            }

            if (resumeBtn) {
                resumeBtn.onclick = () => {
                    state.isPaused = false;
                    resumeBtn.classList.add('d-none');
                    pauseBtn.classList.remove('d-none');
                    this.updateStatusText(state, 'Melanjutkan pemrosesan pecahan batch...', 'primary');
                    this.appendLog(state, '▶️ Melanjutkan pemrosesan batch.', 'info');
                    this.processNextChunk(state);
                };
            }

            if (closeBtn) {
                closeBtn.onclick = () => {
                    if (state.bsModal) {
                        state.bsModal.hide();
                    }
                    if (typeof state.onComplete === 'function') {
                        state.onComplete({
                            total: state.totalItems,
                            success: state.successCount,
                            skipped: state.skippedCount,
                            failed: state.failedCount,
                        });
                    } else {
                        window.location.reload();
                    }
                };
            }
        },

        openModal: function (state) {
            document.getElementById('bbmModalTitle').textContent = state.title;
            document.getElementById('bbmModalSubtitle').textContent = state.subtitle;
            document.getElementById('bbmChunkSizeBadge').textContent = `${state.chunkSize} Peserta/Pecahan`;

            // Reset UI
            document.getElementById('bbmProgressBar').style.width = '0%';
            document.getElementById('bbmProgressBar').textContent = '0%';
            document.getElementById('bbmProgressBar').className = 'progress-bar progress-bar-striped progress-bar-animated bg-primary-600 fw-bold text-2xs';
            document.getElementById('bbmProgressItemText').textContent = 'Menyiapkan antrean...';
            document.getElementById('bbmChunkProgressText').textContent = 'Pecahan 0 dari 0';
            document.getElementById('bbmKpiTotal').textContent = '0';
            document.getElementById('bbmKpiSuccess').textContent = '0';
            document.getElementById('bbmKpiSkipped').textContent = '0';
            document.getElementById('bbmKpiFailed').textContent = '0';
            document.getElementById('bbmStayAliveAlert').classList.add('d-none');
            document.getElementById('bbmCompleteBanner').classList.add('d-none');
            document.getElementById('bbmPauseBtn').classList.remove('d-none');
            document.getElementById('bbmResumeBtn').classList.add('d-none');
            document.getElementById('bbmCloseBtn').classList.add('d-none');
            document.getElementById('bbmStatusSpinner').classList.remove('d-none');
            document.getElementById('bbmLogFeed').innerHTML = '<div class="text-secondary-light text-center py-12" id="bbmLogEmpty">Menyiapkan antrean...</div>';
            document.getElementById('bbmLogCount').textContent = '0 entri';

            // Start Timer
            state.startTime = Date.now();
            if (state.timerInterval) clearInterval(state.timerInterval);
            state.timerInterval = setInterval(() => {
                const diff = Math.floor((Date.now() - state.startTime) / 1000);
                const mm = String(Math.floor(diff / 60)).padStart(2, '0');
                const ss = String(diff % 60).padStart(2, '0');
                const el = document.getElementById('bbmElapsedTime');
                if (el) el.textContent = `${mm}:${ss}`;
            }, 1000);

            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                state.bsModal = bootstrap.Modal.getOrCreateInstance(state.modalEl);
                state.bsModal.show();
            } else if (typeof $ !== 'undefined' && $(state.modalEl).modal) {
                $(state.modalEl).modal('show');
            }
        },

        executePrepare: function (state) {
            this.updateStatusText(state, 'Menghitung dan memverifikasi antrean peserta...', 'primary');

            const payload = Object.assign({ action: 'prepare' }, state.prepareData);
            this.sendPostRequest(state.prepareUrl, payload)
                .then((res) => {
                    if (!res || !res.ok) {
                        const errMsg = (res && res.error) ? res.error : 'Gagal menyiapkan antrean target.';
                        this.showError(state, errMsg);
                        return;
                    }

                    const items = res.items || res.target_ids || [];
                    if (items.length === 0) {
                        this.showError(state, res.error || 'Seluruh peserta pada target ini sudah memiliki sertifikat resmi atau belum ada presensi.');
                        return;
                    }

                    // Set KPI Target
                    const totalTarget = res.total || items.length;
                    document.getElementById('bbmKpiTotal').textContent = totalTarget;
                    if (res.already_certified) {
                        state.skippedCount += parseInt(res.already_certified, 10);
                        document.getElementById('bbmKpiSkipped').textContent = state.skippedCount;
                    }

                    this.initializeWithItems(state, items);
                })
                .catch((err) => {
                    this.showError(state, 'Koneksi gagal saat menyiapkan antrean: ' + err.message);
                });
        },

        initializeWithItems: function (state, items) {
            state.totalItems = items.length;
            document.getElementById('bbmKpiTotal').textContent = state.totalItems;

            // Pecah array items menjadi chunks berukuran state.chunkSize
            state.chunks = [];
            for (let i = 0; i < items.length; i += state.chunkSize) {
                state.chunks.push(items.slice(i, i + state.chunkSize));
            }

            state.currentIndex = 0;
            const logEmpty = document.getElementById('bbmLogEmpty');
            if (logEmpty) logEmpty.remove();

            this.appendLog(state, `🚀 Antrean disiapkan: ${state.totalItems} target dipecah menjadi ${state.chunks.length} bagian (${state.chunkSize} item/bagian).`, 'info');
            this.updateStatusText(state, `Memulai pecahan 1 dari ${state.chunks.length}...`, 'primary');

            // Jalankan pecahan pertama
            this.processNextChunk(state);
        },

        processNextChunk: function (state) {
            if (state.isPaused || state.isCancelled || state.isFinished) {
                return;
            }

            if (state.currentIndex >= state.chunks.length) {
                this.completeBatch(state);
                return;
            }

            const chunkIndex = state.currentIndex;
            const currentChunk = state.chunks[chunkIndex];
            const chunkNum = chunkIndex + 1;
            const totalChunks = state.chunks.length;

            this.updateStatusText(state, `Memproses pecahan ${chunkNum} dari ${totalChunks} (${currentChunk.length} peserta)...`, 'primary');
            document.getElementById('bbmChunkProgressText').textContent = `Pecahan ${chunkNum} dari ${totalChunks}`;

            // Bangun payload chunk
            let payload = {};
            if (typeof state.chunkDataBuilder === 'function') {
                payload = state.chunkDataBuilder(currentChunk, chunkIndex, totalChunks);
            } else {
                const ids = currentChunk.map(item => (typeof item === 'object' && item !== null) ? (item.id || item) : item);
                payload = { action: 'chunk', chunk_ids: ids, is_chunk: 1 };
            }

            // Eksekusi HTTP Request
            this.sendPostRequest(state.chunkUrl, payload)
                .then((res) => {
                    // Berhasil respon HTTP
                    state.currentRetry = 0;
                    this.hideStayAliveAlert(state);

                    if (!res || !res.ok) {
                        const errMsg = (res && res.error) ? res.error : 'Terjadi kegagalan pemrosesan pada pecahan ini.';
                        this.appendLog(state, `❌ Pecahan #${chunkNum}: ${errMsg}`, 'danger');
                        state.failedCount += currentChunk.length;
                    } else {
                        // Update metrics
                        const succ = parseInt(res.success_count || 0, 10);
                        const skip = parseInt(res.skipped_count || 0, 10);
                        const fail = parseInt(res.failed_count || 0, 10);

                        state.successCount += succ;
                        state.skippedCount += skip;
                        state.failedCount += fail;

                        // Parse individual log items
                        if (Array.isArray(res.results)) {
                            res.results.forEach((r) => {
                                const name = r.name || `ID #${r.id}`;
                                if (r.status === 'success') {
                                    this.appendLog(state, `✅ ${name} — ${r.cert_number || ''} ${r.message || 'Berhasil'}`, 'success');
                                } else if (r.status === 'skipped') {
                                    this.appendLog(state, `⚠️ ${name} — ${r.message || 'Dilewati'}`, 'warning');
                                } else {
                                    this.appendLog(state, `❌ ${name} — ${r.message || 'Gagal'}`, 'danger');
                                }
                            });
                        } else {
                            this.appendLog(state, `✨ Pecahan #${chunkNum} selesai: +${succ} sukses, ${skip} dilewati, ${fail} gagal.`, 'success');
                        }
                    }

                    // Update UI Counters & Progress Bar
                    document.getElementById('bbmKpiSuccess').textContent = state.successCount;
                    document.getElementById('bbmKpiSkipped').textContent = state.skippedCount;
                    document.getElementById('bbmKpiFailed').textContent = state.failedCount;

                    const processedItems = Math.min(state.totalItems, (chunkIndex + 1) * state.chunkSize);
                    const percent = Math.min(100, Math.round((chunkNum / totalChunks) * 100));

                    const progressBar = document.getElementById('bbmProgressBar');
                    progressBar.style.width = `${percent}%`;
                    progressBar.textContent = `${percent}%`;
                    document.getElementById('bbmProgressItemText').textContent = `${processedItems} dari ${state.totalItems} peserta diproses`;

                    // Lanjutkan ke pecahan berikutnya
                    state.currentIndex++;
                    setTimeout(() => {
                        this.processNextChunk(state);
                    }, 100);
                })
                .catch((err) => {
                    // Error Jaringan atau HTTP 504 / 502: Jalankan STAY-ALIVE Auto-Retry
                    state.currentRetry++;
                    const maxRetries = state.maxRetries;

                    if (state.currentRetry <= maxRetries) {
                        this.showStayAliveAlert(
                            state,
                            `Terjadi jeda respons pada pecahan #${chunkNum}. Fitur Stay-Alive mencoba ulang otomatis (Percobaan ${state.currentRetry}/${maxRetries})...`
                        );
                        this.appendLog(
                            state,
                            `🔄 Stay-Alive: Jeda/timeout pada pecahan #${chunkNum} (${err.message}). Mencoba ulang otomatis [${state.currentRetry}/${maxRetries}]...`,
                            'warning'
                        );

                        setTimeout(() => {
                            this.processNextChunk(state);
                        }, state.retryDelayMs);
                    } else {
                        // Gagal setelah max percobaan: Jeda batch dan beri tombol resume
                        this.showStayAliveAlert(
                            state,
                            `Koneksi server gagal setelah ${maxRetries} percobaan. Antrean terjeda demi keamanan data. Klik "Lanjutkan Batch" untuk mencoba kembali.`
                        );
                        this.appendLog(state, `🛑 Pecahan #${chunkNum} terhenti setelah ${maxRetries} percobaan. Batch dijeda.`, 'danger');
                        state.isPaused = true;
                        document.getElementById('bbmPauseBtn').classList.add('d-none');
                        document.getElementById('bbmResumeBtn').classList.remove('d-none');
                        state.currentRetry = 0; // reset untuk percobaan manual berikutnya
                    }
                });
        },

        completeBatch: function (state) {
            state.isFinished = true;
            if (state.timerInterval) clearInterval(state.timerInterval);

            // Progress bar 100%
            const progressBar = document.getElementById('bbmProgressBar');
            progressBar.style.width = '100%';
            progressBar.textContent = '100%';
            progressBar.classList.remove('bg-primary-600', 'progress-bar-animated');
            progressBar.classList.add('bg-success-main');

            document.getElementById('bbmStatusSpinner').classList.add('d-none');
            document.getElementById('bbmAccentBar').className = 'bg-success-main';
            document.getElementById('bbmPauseBtn').classList.add('d-none');
            document.getElementById('bbmResumeBtn').classList.add('d-none');
            document.getElementById('bbmCloseBtn').classList.remove('d-none');
            document.getElementById('bbmStayAliveAlert').classList.add('d-none');

            this.updateStatusText(state, 'Penerbitan batch selesai 100%!', 'success');
            document.getElementById('bbmCompleteSummary').textContent =
                `Berhasil diterbitkan: ${state.successCount} sertifikat, Dilewati: ${state.skippedCount}, Gagal: ${state.failedCount}.`;
            document.getElementById('bbmCompleteBanner').classList.remove('d-none');

            this.appendLog(
                state,
                `🎉 Selesai! Total ${state.totalItems} target diproses. Berhasil: ${state.successCount}, Dilewati: ${state.skippedCount}, Gagal: ${state.failedCount}.`,
                'success'
            );
        },

        showError: function (state, message) {
            document.getElementById('bbmStatusSpinner').classList.add('d-none');
            document.getElementById('bbmAccentBar').className = 'bg-danger-600';
            this.updateStatusText(state, 'Batch dibatalkan / gagal.', 'danger');
            this.appendLog(state, `❌ ${message}`, 'danger');
            document.getElementById('bbmPauseBtn').classList.add('d-none');
            document.getElementById('bbmCloseBtn').classList.remove('d-none');
            document.getElementById('bbmCloseBtn').textContent = 'Tutup';
            if (state.timerInterval) clearInterval(state.timerInterval);
        },

        updateStatusText: function (state, text, type) {
            const el = document.getElementById('bbmCurrentStatusText');
            if (!el) return;
            el.textContent = text;
            el.className = `fw-semibold text-sm ${type === 'danger' ? 'text-danger-main' : (type === 'success' ? 'text-success-main' : (type === 'warning' ? 'text-warning-main' : 'text-primary-light'))}`;
        },

        showStayAliveAlert: function (state, message) {
            const el = document.getElementById('bbmStayAliveAlert');
            const msgEl = document.getElementById('bbmStayAliveMessage');
            if (el && msgEl) {
                msgEl.textContent = message;
                el.classList.remove('d-none');
                el.classList.add('d-flex');
            }
        },

        hideStayAliveAlert: function (state) {
            const el = document.getElementById('bbmStayAliveAlert');
            if (el) {
                el.classList.remove('d-flex');
                el.classList.add('d-none');
            }
        },

        appendLog: function (state, text, type) {
            const feed = document.getElementById('bbmLogFeed');
            const countEl = document.getElementById('bbmLogCount');
            if (!feed) return;

            const timeStr = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            let color = '#475569';
            if (type === 'success') color = '#16a34a';
            else if (type === 'warning') color = '#d97706';
            else if (type === 'danger') color = '#dc2626';
            else if (type === 'info') color = '#2563eb';

            const line = document.createElement('div');
            line.style.color = color;
            line.style.marginBottom = '4px';
            line.style.wordBreak = 'break-word';
            line.innerHTML = `<span style="opacity: 0.65;">[${timeStr}]</span> ${this.escapeHtml(text)}`;

            feed.appendChild(line);
            feed.scrollTop = feed.scrollHeight;

            if (countEl) {
                const totalLines = feed.children.length;
                countEl.textContent = `${totalLines} entri`;
            }
        },

        escapeHtml: function (str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;');
        },

        sendPostRequest: function (url, data) {
            const formData = new FormData();

            // Ambil CSRF dari halaman
            const csrfInput = document.querySelector('input[name="csrf_test_name"]') || document.querySelector('input[name="csrf_token"]');
            if (csrfInput) {
                formData.append(csrfInput.name, csrfInput.value);
            }

            if (data instanceof FormData) {
                for (const pair of data.entries()) {
                    formData.append(pair[0], pair[1]);
                }
            } else if (typeof data === 'object' && data !== null) {
                for (const key of Object.keys(data)) {
                    const val = data[key];
                    if (Array.isArray(val)) {
                        val.forEach(item => {
                            const subVal = (typeof item === 'object' && item !== null) ? (item.id || item) : item;
                            formData.append(`${key}[]`, subVal);
                        });
                    } else {
                        formData.append(key, val);
                    }
                }
            }

            return fetch(url, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: formData,
            }).then(async (response) => {
                const text = await response.text();
                let json;
                try {
                    json = JSON.parse(text);
                } catch (e) {
                    throw new Error(`Server mengembalikan respons non-JSON (Status ${response.status}): ${text.substring(0, 120)}...`);
                }
                if (!response.ok && !json.ok) {
                    throw new Error(json.error || `HTTP ${response.status}`);
                }
                return json;
            });
        },
    };

    window.BegawiBatchRunner = BegawiBatchRunner;
})(window, document);
