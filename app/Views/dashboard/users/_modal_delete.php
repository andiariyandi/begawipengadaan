<!-- Modal Peringatan & Konfirmasi Hapus Data Pengguna (Cascade Foreign Key) -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content radius-16 border-0 shadow-lg overflow-hidden">
            <!-- Header Modal -->
            <div class="modal-header bg-danger-600 text-white py-16 px-24 border-0">
                <div class="d-flex align-items-center gap-3">
                    <div class="w-40-px h-40-px bg-white text-danger-600 rounded-circle d-flex align-items-center justify-content-center text-xl flex-shrink-0 shadow-sm">
                        <iconify-icon icon="solar:danger-triangle-bold"></iconify-icon>
                    </div>
                    <div>
                        <h6 class="modal-title fs-5 fw-semibold text-white mb-0" id="deleteUserModalLabel">Hapus Permanen Data Pengguna</h6>
                        <small class="text-white-50">Tindakan ini akan memusnahkan akun beserta seluruh relasi data (Foreign Key).</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>

            <!-- Body Modal -->
            <div class="modal-body p-24">
                <!-- User Profile Summary Box -->
                <div class="card border border-neutral-200 radius-12 p-16 mb-20 bg-neutral-50">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-12">
                            <span class="w-48-px h-48-px bg-primary-100 text-primary-700 rounded-circle d-flex justify-content-center align-items-center flex-shrink-0 fw-bold fs-5" id="delUserInitials">
                                U
                            </span>
                            <div>
                                <h6 class="text-md fw-semibold mb-1" id="delUserName">Nama Pengguna</h6>
                                <div class="d-flex align-items-center gap-2 text-sm text-secondary-light">
                                    <span id="delUserEmail">email@domain.com</span>
                                    <span>&bull;</span>
                                    <span class="badge bg-neutral-200 text-secondary-dark px-8 py-2 radius-4 text-xs fw-medium" id="delUserProfileType">ASN</span>
                                </div>
                            </div>
                        </div>
                        <span class="badge bg-danger-100 text-danger-600 px-10 py-5 radius-6 text-xs fw-semibold d-inline-flex align-items-center gap-1">
                            <iconify-icon icon="solar:shield-warning-bold"></iconify-icon>
                            <span>Target Pembersihan</span>
                        </span>
                    </div>
                </div>

                <!-- Protected Account Alert (Hidden by default) -->
                <div id="delProtectedAlert" class="alert alert-danger radius-10 p-16 mb-20 d-none">
                    <div class="d-flex align-items-start gap-2">
                        <iconify-icon icon="solar:shield-cross-bold" class="text-2xl flex-shrink-0 mt-2 text-danger-600"></iconify-icon>
                        <div>
                            <strong class="d-block mb-4 fs-6">Akun Ini Dilindungi Sistem!</strong>
                            <span id="delProtectedMsg">Akun Administrator Master atau akun yang sedang aktif tidak dapat dihapus demi integritas dan keamanan sistem.</span>
                        </div>
                    </div>
                </div>

                <!-- Impact Summary Container -->
                <div id="delImpactSection">
                    <h6 class="text-sm fw-semibold text-secondary-dark mb-12 d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:database-outline" class="text-primary-600 text-lg"></iconify-icon>
                        <span>Ringkasan Data Relasi Terdampak (Cascading Dependency)</span>
                    </h6>

                    <div id="delImpactLoading" class="text-center py-20 text-secondary-light">
                        <div class="spinner-border spinner-border-sm text-primary-600 me-2" role="status"></div>
                        <span class="text-sm">Menghitung keterikatan foreign key data pengguna...</span>
                    </div>

                    <div id="delImpactContent" class="row row-cols-sm-2 row-cols-1 gy-3 mb-20 d-none">
                        <div class="col">
                            <div class="border radius-8 p-12 bg-white d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-2">
                                    <iconify-icon icon="solar:diploma-outline" class="text-warning-main text-xl"></iconify-icon>
                                    <span class="text-xs text-secondary-light">Sertifikat Terbit</span>
                                </div>
                                <span class="badge bg-warning-100 text-warning-main radius-4 px-8 py-2 text-xs fw-bold" id="delCountCertificates">0</span>
                            </div>
                        </div>
                        <div class="col">
                            <div class="border radius-8 p-12 bg-white d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-2">
                                    <iconify-icon icon="solar:book-bookmark-outline" class="text-info-main text-xl"></iconify-icon>
                                    <span class="text-xs text-secondary-light">Kelas Pelatihan LMS</span>
                                </div>
                                <span class="badge bg-info-100 text-info-main radius-4 px-8 py-2 text-xs fw-bold" id="delCountEnrollments">0</span>
                            </div>
                        </div>
                        <div class="col">
                            <div class="border radius-8 p-12 bg-white d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-2">
                                    <iconify-icon icon="solar:document-text-outline" class="text-purple text-xl"></iconify-icon>
                                    <span class="text-xs text-secondary-light">Riwayat Kuis &amp; Tugas</span>
                                </div>
                                <span class="badge bg-purple-100 text-purple radius-4 px-8 py-2 text-xs fw-bold" id="delCountAssessments">0</span>
                            </div>
                        </div>
                        <div class="col">
                            <div class="border radius-8 p-12 bg-white d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-2">
                                    <iconify-icon icon="solar:calendar-date-outline" class="text-success-main text-xl"></iconify-icon>
                                    <span class="text-xs text-secondary-light">Registrasi Kegiatan</span>
                                </div>
                                <span class="badge bg-success-100 text-success-main radius-4 px-8 py-2 text-xs fw-bold" id="delCountEvents">0</span>
                            </div>
                        </div>
                    </div>

                    <!-- Safety Note -->
                    <div class="alert alert-warning border-0 radius-8 p-14 mb-20 bg-warning-50">
                        <div class="d-flex align-items-start gap-2">
                            <iconify-icon icon="solar:info-circle-bold" class="text-warning-main text-xl flex-shrink-0 mt-1"></iconify-icon>
                            <div class="text-xs text-secondary-dark line-height-1-5">
                                <strong>Mitigasi Keamanan Modul Aktif:</strong>
                                <ul class="mb-0 ps-16 mt-4">
                                    <li>Artikel Bunga Rampai &amp; Berita yang pernah ditulis pengguna ini akan <strong>dialihkan kepemilikannya ke Super Admin</strong> agar portal publik tidak rusak / 404.</li>
                                    <li>Seluruh riwayat nilai, submission tugas, token verifikasi, dan data profil pengguna akan <strong>dimusnahkan permanen</strong>. Tindakan ini tidak dapat dibatalkan.</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Form Konfirmasi -->
                    <form action="#" method="POST" id="formDeleteUserCascade">
                        <?= csrf_field() ?>
                        <div class="mb-20">
                            <label for="delUserConfirmInput" class="form-label text-sm fw-semibold text-secondary-dark mb-6">
                                Ketik kata <span class="text-danger-600 fw-bold">HAPUS</span> pada kotak di bawah untuk mengonfirmasi:
                            </label>
                            <input type="text"
                                   id="delUserConfirmInput"
                                   name="confirmation"
                                   class="form-control radius-8 py-10 px-16 text-center fw-bold fs-6 border-danger-200"
                                   placeholder="Ketik HAPUS"
                                   autocomplete="off"
                                   required>
                            <small class="text-secondary-light text-xs mt-4 d-block text-center">
                                Tombol hapus akan aktif secara otomatis setelah Anda mengetik <strong>HAPUS</strong> dengan benar.
                            </small>
                        </div>

                        <div class="d-flex align-items-center justify-content-end gap-12 pt-16 border-top">
                            <button type="button" class="btn btn-outline-neutral-300 radius-8 px-20 py-11" data-bs-dismiss="modal">
                                Batal
                            </button>
                            <button type="submit" id="btnConfirmDeleteUserSubmit" class="btn btn-danger-600 radius-8 px-24 py-11 d-inline-flex align-items-center gap-2" disabled>
                                <iconify-icon icon="solar:trash-bin-trash-bold" class="text-lg"></iconify-icon>
                                <span>Ya, Hapus Permanen Seluruh Data</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modalEl = document.getElementById('deleteUserModal');
    if (!modalEl) return;

    const bsModal = new bootstrap.Modal(modalEl);
    const formEl = document.getElementById('formDeleteUserCascade');
    const inputConfirm = document.getElementById('delUserConfirmInput');
    const btnSubmit = document.getElementById('btnConfirmDeleteUserSubmit');

    const nameEl = document.getElementById('delUserName');
    const emailEl = document.getElementById('delUserEmail');
    const badgeEl = document.getElementById('delUserProfileType');
    const initialsEl = document.getElementById('delUserInitials');

    const protectedAlert = document.getElementById('delProtectedAlert');
    const protectedMsg = document.getElementById('delProtectedMsg');
    const impactSection = document.getElementById('delImpactSection');

    const impactLoading = document.getElementById('delImpactLoading');
    const impactContent = document.getElementById('delImpactContent');

    const countCert = document.getElementById('delCountCertificates');
    const countEnroll = document.getElementById('delCountEnrollments');
    const countAssess = document.getElementById('delCountAssessments');
    const countEvent = document.getElementById('delCountEvents');

    // Input verification
    if (inputConfirm && btnSubmit) {
        inputConfirm.addEventListener('input', function () {
            if (this.value.trim().toUpperCase() === 'HAPUS') {
                btnSubmit.disabled = false;
                btnSubmit.classList.remove('opacity-50');
            } else {
                btnSubmit.disabled = true;
                btnSubmit.classList.add('opacity-50');
            }
        });
    }

    // Delegate click on any .btn-delete-user
    document.addEventListener('click', function (e) {
        const trigger = e.target.closest('.btn-delete-user');
        if (!trigger) return;

        e.preventDefault();

        const userId = trigger.dataset.userId;
        const userName = trigger.dataset.userName || 'Pengguna';
        const userEmail = trigger.dataset.userEmail || '-';
        const userProfileType = trigger.dataset.userProfileType || 'ASN';
        const actionUrl = trigger.dataset.actionUrl;
        const summaryUrl = trigger.dataset.summaryUrl;

        // Reset UI state
        formEl.action = actionUrl;
        inputConfirm.value = '';
        btnSubmit.disabled = true;
        btnSubmit.classList.add('opacity-50');

        nameEl.textContent = userName;
        emailEl.textContent = userEmail;
        badgeEl.textContent = userProfileType.toUpperCase();
        initialsEl.textContent = userName.trim().charAt(0).toUpperCase() || 'U';

        // Check if master protected
        if (userId === '13' || userId === '14') {
            protectedAlert.classList.remove('d-none');
            protectedMsg.textContent = 'Akun Master Administrator (ID ' + userId + ') dilindungi permanen dan tidak dapat dihapus.';
            impactSection.classList.add('d-none');
            bsModal.show();
            return;
        }

        protectedAlert.classList.add('d-none');
        impactSection.classList.remove('d-none');
        impactLoading.classList.remove('d-none');
        impactContent.classList.add('d-none');

        bsModal.show();

        // Fetch live impact counts
        if (summaryUrl) {
            fetch(summaryUrl, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(res => {
                impactLoading.classList.add('d-none');
                if (res.status === 'success' && res.data) {
                    const data = res.data;
                    if (data.is_protected) {
                        protectedAlert.classList.remove('d-none');
                        protectedMsg.textContent = 'Pengguna ini memiliki hak akses Super Administrator dan dilindungi demi keamanan.';
                        impactSection.classList.add('d-none');
                        return;
                    }

                    impactContent.classList.remove('d-none');
                    const counts = data.counts || {};
                    countCert.textContent = counts.certificates || 0;
                    countEnroll.textContent = counts.enrollments || 0;
                    countAssess.textContent = (counts.quiz_attempts || 0) + (counts.assignment_submissions || 0);
                    countEvent.textContent = counts.event_registrations || 0;
                } else {
                    impactContent.classList.remove('d-none');
                }
            })
            .catch(err => {
                console.error('Error fetching impact summary:', err);
                impactLoading.classList.add('d-none');
                impactContent.classList.remove('d-none');
            });
        } else {
            impactLoading.classList.add('d-none');
            impactContent.classList.remove('d-none');
        }
    });
});
</script>
