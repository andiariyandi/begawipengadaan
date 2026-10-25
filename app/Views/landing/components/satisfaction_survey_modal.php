<?php
$isUserLoggedIn = session()->get('isLoggedIn') === true;
$loggedUserName = trim((string) (session()->get('name') ?: session()->get('username') ?: ''));
$loggedUserEmail = trim((string) (session()->get('email') ?: ''));
$loggedUserPhone = trim((string) (session()->get('phone') ?: ''));
$loggedUserAgency = trim((string) (session()->get('agency') ?: ''));
$loggedUserRoles = (array) session()->get('roles');

$defaultRoleVal = '';
if ($isUserLoggedIn) {
    if (in_array('penyedia', $loggedUserRoles, true) || in_array('vendor', $loggedUserRoles, true)) {
        $defaultRoleVal = 'Penyedia Barang / Jasa (Vendor / Rekanan / UMKM)';
    } elseif (in_array('super_administrator', $loggedUserRoles, true) || in_array('administrator', $loggedUserRoles, true) || in_array('operator', $loggedUserRoles, true)) {
        $defaultRoleVal = 'Pokja Pemilihan / Biro Pengadaan UKPBJ';
    } elseif (in_array('ppk', $loggedUserRoles, true)) {
        $defaultRoleVal = 'Pejabat Pembuat Komitmen (PPK) / Pejabat Pengadaan';
    } elseif (in_array('auditor', $loggedUserRoles, true) || in_array('apip', $loggedUserRoles, true)) {
        $defaultRoleVal = 'Pengawas / APIP / Inspektorat / Auditor';
    } else {
        $defaultRoleVal = 'ASN OPD Perangkat Daerah Pemprov Lampung';
    }
}

$surveyNum1 = rand(2, 9);
$surveyNum2 = rand(1, 9);
session()->set('survey_captcha_answer', $surveyNum1 + $surveyNum2);
$surveyCaptchaQuestion = "{$surveyNum1} + {$surveyNum2}";
?>
<!-- Modal Survei Kepuasan Pengguna (IKM) -->
<div class="modal fade modal-survey-kepuasan" id="modalSurveyKepuasan" tabindex="-1" aria-labelledby="modalSurveyKepuasanLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content survey-modal-card">
            
            <!-- Modal Header -->
            <div class="survey-modal-header">
                <div class="survey-modal-header__badge">
                    <i class="fas fa-award me-1"></i> Indeks Kepuasan Masyarakat (IKM) &bull; PermenPAN-RB No. 14/2017
                </div>
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <h4 class="survey-modal-title" id="modalSurveyKepuasanLabel">
                            <i class="fas fa-clipboard-check text-warning me-2"></i>Survei Kepuasan Layanan Pengguna
                        </h4>
                        <p class="survey-modal-subtitle mb-0">
                            Bantu Biro Pengadaan Barang dan Jasa Provinsi Lampung mewujudkan tata kelola PBJ yang transparan, akuntabel, dan prima.
                        </p>
                    </div>
                    <button type="button" class="btn-close btn-close-white survey-close-btn" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="modal-body survey-modal-body">
                
                <!-- Alert Feedback (Error / Validation) -->
                <div id="surveyAlertError" class="alert alert-danger d-none my-2 py-2 px-3 radius-8" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-circle me-2 fs-5"></i>
                        <span id="surveyAlertErrorMessage" class="small">Terjadi kesalahan. Silakan periksa kembali formulir Anda.</span>
                    </div>
                </div>

                <!-- STATE 1: BEFORE SUBMIT (FORM STATE) -->
                <div id="surveyFormBeforeSubmit" class="survey-state survey-state--active">
                    <form id="surveyKepuasanForm" action="<?= site_url('survei-kepuasan') ?>" method="post">
                        <?= csrf_field() ?>

                        <!-- Anti-Spam Honeypot Field (Invisible to real users) -->
                        <div style="position: absolute; left: -9999px; opacity: 0; pointer-events: none;" aria-hidden="true">
                            <input type="text" name="website_url" tabindex="-1" autocomplete="off" value="">
                        </div>

                        <!-- 1. Kepuasan Keseluruhan (Rating Cards) -->
                        <div class="survey-step-box">
                            <div class="survey-step-header">
                                <span class="survey-step-num">1</span>
                                <div>
                                    <h5 class="survey-step-title">Bagaimana Tingkat Kepuasan Anda Terhadap Layanan BegawiPengadaan?</h5>
                                    <p class="survey-step-desc">Pilih salah satu ikon yang paling menggambarkan pengalaman Anda.</p>
                                </div>
                            </div>

                            <input type="hidden" name="rating_overall" id="surveyRatingOverall" value="5">

                            <div class="survey-emojis-grid">
                                <button type="button" class="survey-emoji-card" data-rating="1">
                                    <span class="survey-emoji-icon">😡</span>
                                    <span class="survey-emoji-stars">★☆☆☆☆</span>
                                    <span class="survey-emoji-text">Sangat Buruk</span>
                                </button>
                                <button type="button" class="survey-emoji-card" data-rating="2">
                                    <span class="survey-emoji-icon">🙁</span>
                                    <span class="survey-emoji-stars">★★☆☆☆</span>
                                    <span class="survey-emoji-text">Kurang Baik</span>
                                </button>
                                <button type="button" class="survey-emoji-card" data-rating="3">
                                    <span class="survey-emoji-icon">😐</span>
                                    <span class="survey-emoji-stars">★★★☆☆</span>
                                    <span class="survey-emoji-text">Cukup Baik</span>
                                </button>
                                <button type="button" class="survey-emoji-card" data-rating="4">
                                    <span class="survey-emoji-icon">😊</span>
                                    <span class="survey-emoji-stars">★★★★☆</span>
                                    <span class="survey-emoji-text">Baik / Puas</span>
                                </button>
                                <button type="button" class="survey-emoji-card active" data-rating="5">
                                    <span class="survey-emoji-icon">🤩</span>
                                    <span class="survey-emoji-stars">★★★★★</span>
                                    <span class="survey-emoji-text">Sangat Puas</span>
                                </button>
                            </div>
                            <div class="survey-rating-feedback text-center mt-2" id="surveyRatingFeedback">
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1">
                                    <i class="fas fa-check-circle me-1"></i> Penilaian Dipilih: <strong>5 - Sangat Puas</strong>
                                </span>
                            </div>
                        </div>

                        <!-- 2. Aspek Penilaian Mutu (4 Indikator Layanan) -->
                        <div class="survey-step-box">
                            <div class="survey-step-header">
                                <span class="survey-step-num">2</span>
                                <div>
                                    <h5 class="survey-step-title">Penilaian Aspek Layanan Utama</h5>
                                    <p class="survey-step-desc">Berikan penilaian spesifik untuk masing-masing pilar layanan.</p>
                                </div>
                            </div>

                            <div class="survey-aspects-list">
                                <!-- Aspek 1 -->
                                <div class="survey-aspect-item">
                                    <div class="survey-aspect-info">
                                        <div class="survey-aspect-name"><i class="fas fa-desktop text-primary me-2"></i>Kemudahan Akses & Kecepatan Portal</div>
                                        <div class="survey-aspect-hint">Kecepatan navigasi, tampilan responsif, kemudahan pencarian informasi</div>
                                    </div>
                                    <div class="survey-aspect-stars" data-input="service_access_rating">
                                        <input type="hidden" name="service_access_rating" value="5">
                                        <button type="button" class="aspect-star" data-value="1" title="1 Bintang"><i class="fas fa-star"></i></button>
                                        <button type="button" class="aspect-star" data-value="2" title="2 Bintang"><i class="fas fa-star"></i></button>
                                        <button type="button" class="aspect-star" data-value="3" title="3 Bintang"><i class="fas fa-star"></i></button>
                                        <button type="button" class="aspect-star" data-value="4" title="4 Bintang"><i class="fas fa-star"></i></button>
                                        <button type="button" class="aspect-star active" data-value="5" title="5 Bintang"><i class="fas fa-star"></i></button>
                                    </div>
                                </div>

                                <!-- Aspek 2 -->
                                <div class="survey-aspect-item">
                                    <div class="survey-aspect-info">
                                        <div class="survey-aspect-name"><i class="fas fa-gavel text-warning me-2"></i>Kelengkapan Regulasi & Dokumen PBJ</div>
                                        <div class="survey-aspect-hint">Perpres, PerLKPP, Standar Dokumen Pemilihan (SDP), dan Surat Edaran</div>
                                    </div>
                                    <div class="survey-aspect-stars" data-input="service_regulation_rating">
                                        <input type="hidden" name="service_regulation_rating" value="5">
                                        <button type="button" class="aspect-star" data-value="1" title="1 Bintang"><i class="fas fa-star"></i></button>
                                        <button type="button" class="aspect-star" data-value="2" title="2 Bintang"><i class="fas fa-star"></i></button>
                                        <button type="button" class="aspect-star" data-value="3" title="3 Bintang"><i class="fas fa-star"></i></button>
                                        <button type="button" class="aspect-star" data-value="4" title="4 Bintang"><i class="fas fa-star"></i></button>
                                        <button type="button" class="aspect-star active" data-value="5" title="5 Bintang"><i class="fas fa-star"></i></button>
                                    </div>
                                </div>

                                <!-- Aspek 3 -->
                                <div class="survey-aspect-item">
                                    <div class="survey-aspect-info">
                                        <div class="survey-aspect-name"><i class="fas fa-graduation-cap text-success me-2"></i>Kualitas Arena Belajar & Pelatihan PBJ</div>
                                        <div class="survey-aspect-hint">Kesesuaian silabus, kompetensi instruktur, kejelasan modul bimtek</div>
                                    </div>
                                    <div class="survey-aspect-stars" data-input="service_learning_rating">
                                        <input type="hidden" name="service_learning_rating" value="5">
                                        <button type="button" class="aspect-star" data-value="1" title="1 Bintang"><i class="fas fa-star"></i></button>
                                        <button type="button" class="aspect-star" data-value="2" title="2 Bintang"><i class="fas fa-star"></i></button>
                                        <button type="button" class="aspect-star" data-value="3" title="3 Bintang"><i class="fas fa-star"></i></button>
                                        <button type="button" class="aspect-star" data-value="4" title="4 Bintang"><i class="fas fa-star"></i></button>
                                        <button type="button" class="aspect-star active" data-value="5" title="5 Bintang"><i class="fas fa-star"></i></button>
                                    </div>
                                </div>

                                <!-- Aspek 4 -->
                                <div class="survey-aspect-item">
                                    <div class="survey-aspect-info">
                                        <div class="survey-aspect-name"><i class="fas fa-headset text-info me-2"></i>Responsivitas Helpdesk & Konsultasi LPSE</div>
                                        <div class="survey-aspect-hint">Keramahan petugas, kecepatan jawaban konsultasi, solutif terhadap kendala</div>
                                    </div>
                                    <div class="survey-aspect-stars" data-input="service_helpdesk_rating">
                                        <input type="hidden" name="service_helpdesk_rating" value="5">
                                        <button type="button" class="aspect-star" data-value="1" title="1 Bintang"><i class="fas fa-star"></i></button>
                                        <button type="button" class="aspect-star" data-value="2" title="2 Bintang"><i class="fas fa-star"></i></button>
                                        <button type="button" class="aspect-star" data-value="3" title="3 Bintang"><i class="fas fa-star"></i></button>
                                        <button type="button" class="aspect-star" data-value="4" title="4 Bintang"><i class="fas fa-star"></i></button>
                                        <button type="button" class="aspect-star active" data-value="5" title="5 Bintang"><i class="fas fa-star"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 3. Profil Responden -->
                        <div class="survey-step-box">
                            <div class="survey-step-header">
                                <span class="survey-step-num">3</span>
                                <div>
                                    <h5 class="survey-step-title">Profil & Kategori Responden</h5>
                                    <p class="survey-step-desc">Identitas dapat dirahasiakan (anonim) sesuai preferensi Anda.</p>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label survey-field-label">Kategori / Peran Pengguna <span class="text-danger">*</span></label>
                                    <?php
                                    $roleOptions = [
                                        'Pejabat Pembuat Komitmen (PPK) / Pejabat Pengadaan',
                                        'Pokja Pemilihan / Biro Pengadaan UKPBJ',
                                        'Penyedia Barang / Jasa (Vendor / Rekanan / UMKM)',
                                        'Pengawas / APIP / Inspektorat / Auditor',
                                        'ASN OPD Perangkat Daerah Pemprov Lampung',
                                        'Akademisi / Mahasiswa / Peneliti',
                                        'Masyarakat Umum / Pengguna Lainnya',
                                    ];
                                    ?>
                                    <select class="form-select survey-select ignore" name="respondent_role" id="surveyRespondentRole" required>
                                        <option value="" <?= empty($defaultRoleVal) ? 'selected' : '' ?> disabled>-- Pilih Kategori / Peran Anda --</option>
                                        <?php foreach ($roleOptions as $rOpt): ?>
                                            <option value="<?= esc($rOpt) ?>" <?= ($defaultRoleVal === $rOpt) ? 'selected' : '' ?>><?= esc($rOpt) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="invalid-feedback">Silakan pilih kategori / peran Anda terlebih dahulu.</div>
                                </div>

                                <div class="col-12">
                                    <div class="survey-anonymous-toggle">
                                        <div class="form-check form-switch mb-0">
                                            <input class="form-check-input" type="checkbox" id="surveyIsAnonymous" name="is_anonymous" value="1" <?= $isUserLoggedIn ? '' : 'checked' ?>>
                                            <label class="form-check-label fw-semibold" for="surveyIsAnonymous">
                                                <i class="fas fa-user-secret text-secondary me-1"></i> Kirim survei secara Anonim (Identitas dirahasiakan)
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Field Identitas (Collapsible when not anonymous) -->
                                <div id="surveyIdentityFields" class="col-12 survey-identity-collapse <?= $isUserLoggedIn ? '' : 'd-none' ?>">
                                    <div class="row g-3 p-3 rounded-3 bg-light border border-dashed">
                                        <div class="col-md-6">
                                            <label class="form-label survey-field-label">Nama Lengkap</label>
                                            <input type="text" class="form-control" name="respondent_name" value="<?= esc($loggedUserName) ?>" placeholder="Contoh: Budi Prasetyo, S.T.">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label survey-field-label">Instansi / Perusahaan</label>
                                            <input type="text" class="form-control" name="respondent_institution" value="<?= esc($loggedUserAgency) ?>" placeholder="Contoh: PT Lampung Konstruksi Utama">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label survey-field-label">No. WhatsApp / Email (Opsional)</label>
                                            <input type="text" class="form-control" name="respondent_contact" value="<?= esc($loggedUserEmail ?: $loggedUserPhone) ?>" placeholder="Untuk konfirmasi tindak lanjut konsultasi jika berkenan">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 4. Kritik, Saran & Rekomendasi -->
                        <div class="survey-step-box">
                            <div class="survey-step-header">
                                <span class="survey-step-num">4</span>
                                <div>
                                    <h5 class="survey-step-title">Kritik, Masukan & Rekomendasi</h5>
                                    <p class="survey-step-desc">Tuliskan masukan spesifik untuk peningkatan layanan atau klik label cepat.</p>
                                </div>
                            </div>

                            <!-- Quick Suggestion Tags -->
                            <div class="survey-quick-tags mb-3">
                                <span class="survey-quick-tags__title"><i class="fas fa-tags me-1"></i> Ide Cepat:</span>
                                <button type="button" class="btn-quick-tag" data-text="Materi di Arena Pembelajaran sangat aplikatif dan terstruktur, mohon perbanyak kelas teknis dan simulasi ujian sertifikasi PBJ."><i class="fas fa-graduation-cap me-1 text-primary"></i>+ Arena Pembelajaran</button>
                                <button type="button" class="btn-quick-tag" data-text="Koleksi artikel, studi kasus, dan template dokumen di Bunga Rampai sangat informatif untuk acuan kerja pengadaan."><i class="fas fa-book-open me-1 text-warning"></i>+ Bunga Rampai</button>
                                <button type="button" class="btn-quick-tag" data-text="Agenda pelatihan, webinar, dan bimtek di Event Agenda sangat bermanfaat, mohon kuota peserta dan frekuensi kegiatan diperbanyak."><i class="fas fa-calendar-alt me-1 text-success"></i>+ Event &amp; Agenda</button>
                                <button type="button" class="btn-quick-tag" data-text="Repositori Regulasi PBJ sangat lengkap dan terstruktur, pencarian dokumen hukum serta hierarki aturan mudah diakses."><i class="fas fa-gavel me-1 text-info"></i>+ Regulasi PBJ</button>
                            </div>

                            <div class="mb-3">
                                <label class="form-label survey-field-label">Saran Pengembangan Layanan</label>
                                <textarea class="form-control survey-textarea" name="feedback" id="surveyFeedback" rows="3" placeholder="Tuliskan saran, masukan, atau kendala yang Anda temui selama menggunakan ekosistem BegawiPengadaan..." maxlength="1000"></textarea>
                                <div class="d-flex justify-content-between mt-1">
                                    <small class="text-muted"><i class="fas fa-shield-alt text-success me-1"></i> Masukan Anda langsung dievaluasi oleh Tim Penjamin Mutu UKPBJ.</small>
                                    <small class="text-muted"><span id="surveyCharCount">0</span>/1000</small>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label survey-field-label">Apakah Anda akan merekomendasikan BegawiPengadaan ke rekan kerja?</label>
                                <div class="survey-recommend-options">
                                    <label class="survey-radio-card">
                                        <input type="radio" name="recommendation" value="Pasti Merekomendasikan" checked>
                                        <span class="survey-radio-label"><i class="fas fa-thumbs-up text-success me-1"></i> Pasti Merekomendasikan</span>
                                    </label>
                                    <label class="survey-radio-card">
                                        <input type="radio" name="recommendation" value="Mungkin">
                                        <span class="survey-radio-label"><i class="fas fa-meh text-warning me-1"></i> Mungkin</span>
                                    </label>
                                    <label class="survey-radio-card">
                                        <input type="radio" name="recommendation" value="Belum Tentu">
                                        <span class="survey-radio-label"><i class="fas fa-question-circle text-secondary me-1"></i> Belum Tentu</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Verifikasi Keamanan Anti-Spam (Simple Math Captcha) -->
                            <div class="survey-captcha-box p-3 rounded-3 bg-light border border-primary-subtle mt-3">
                                <div class="row align-items-center g-2">
                                    <div class="col-md-7">
                                        <label class="form-label survey-field-label d-flex align-items-center gap-2 mb-1" for="surveyCaptchaAnswer">
                                            <i class="fas fa-shield-alt text-primary fs-5"></i>
                                            <span class="fw-bold text-dark">Verifikasi Keamanan (Captcha Matematika) <span class="text-danger">*</span></span>
                                        </label>
                                        <small class="text-muted d-block">
                                            Berapa hasil penjumlahan: <strong class="badge bg-primary text-white font-monospace fs-6 px-2 py-1 ms-1"><?= $surveyCaptchaQuestion ?> = ?</strong>
                                        </small>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="input-group">
                                            <span class="input-group-text bg-white text-primary border-end-0"><i class="fas fa-calculator"></i></span>
                                            <input type="number" class="form-control font-monospace border-start-0" name="captcha_answer" id="surveyCaptchaAnswer" placeholder="Ketik hasil angka..." required autocomplete="off">
                                            <div class="invalid-feedback">Silakan isi jawaban captcha dengan benar.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Footer Buttons -->
                        <div class="survey-modal-footer">
                            <button type="button" class="btn btn-outline-secondary survey-btn-cancel" data-bs-dismiss="modal">
                                Batal
                            </button>
                            <button type="submit" class="thm-btn survey-btn-submit" id="surveyBtnSubmit">
                                <span class="spinner-border spinner-border-sm me-2 d-none" role="status" aria-hidden="true" id="surveySubmitSpinner"></span>
                                <i class="fas fa-paper-plane me-2" id="surveySubmitIcon"></i>
                                <span>Kirim Penilaian &amp; Survei</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- STATE 2: AFTER SUBMIT (SUCCESS RECEIPT STATE) -->
                <div id="surveySuccessAfterSubmit" class="survey-state survey-state--success d-none">
                    <div class="survey-success-container">
                        
                        <!-- Celebration Animation / Icon -->
                        <div class="survey-success-icon-wrap">
                            <div class="survey-success-icon-ring"></div>
                            <div class="survey-success-icon">
                                <i class="fas fa-check"></i>
                            </div>
                        </div>

                        <span class="survey-success-badge">
                            <i class="fas fa-shield-check me-1"></i> Survei Berhasil Dicatat
                        </span>

                        <h3 class="survey-success-title">Terima Kasih Atas Partisipasi Anda!</h3>
                        <p class="survey-success-subtitle">
                            Aspirasi dan penilaian Anda sangat berharga bagi peningkatan mutu pelayanan pengadaan barang dan jasa Pemerintah Provinsi Lampung.
                        </p>

                        <!-- Receipt Summary Card -->
                        <div class="survey-receipt-card">
                            <div class="survey-receipt-header">
                                <div class="d-flex align-items-center">
                                    <div class="survey-receipt-logo-wrap d-flex align-items-center justify-content-center bg-primary text-white rounded-3 me-2" style="width: 40px; height: 40px; min-width: 40px;">
                                        <i class="fas fa-award fs-5"></i>
                                    </div>
                                    <div class="text-start">
                                        <div class="fw-bold text-dark">Bukti Survei Kepuasan (IKM)</div>
                                        <small class="text-muted">Biro Pengadaan Barang & Jasa Provinsi Lampung</small>
                                    </div>
                                </div>
                                <span class="badge bg-success-subtle text-success border border-success-subtle">
                                    Status: Terverifikasi
                                </span>
                            </div>

                            <div class="survey-receipt-body">
                                <div class="row g-2">
                                    <div class="col-6 text-start">
                                        <span class="receipt-label">No. Registrasi Survei:</span>
                                        <div class="receipt-value text-primary fw-bold" id="receiptTicketNumber">IKM-20261018-8F2A1C</div>
                                    </div>
                                    <div class="col-6 text-end">
                                        <span class="receipt-label">Waktu Pengisian:</span>
                                        <div class="receipt-value" id="receiptSubmittedAt"><?= date('d F Y, H:i') ?> WIB</div>
                                    </div>
                                    <div class="col-6 text-start mt-2">
                                        <span class="receipt-label">Tingkat Kepuasan:</span>
                                        <div class="receipt-value" id="receiptRatingScore">
                                            <span class="text-warning">★★★★★</span> (Sangat Puas)
                                        </div>
                                    </div>
                                    <div class="col-6 text-end mt-2">
                                        <span class="receipt-label">Kategori Pengguna:</span>
                                        <div class="receipt-value text-truncate" id="receiptRespondentRole">Penyedia Barang / Jasa</div>
                                    </div>
                                </div>
                            </div>

                            <div class="survey-receipt-footer">
                                <i class="fas fa-info-circle text-primary me-1"></i>
                                Data survei ini otomatis diintegrasikan dalam rekapitulasi capaian IKM publik triwulan berjalan.
                            </div>
                        </div>

                        <!-- Action Buttons After Submit -->
                        <div class="survey-success-actions">
                            <button type="button" class="btn btn-outline-primary survey-btn-reset me-2" id="surveyBtnFillAgain">
                                <i class="fas fa-redo-alt me-1"></i> Isi Survei Baru
                            </button>
                            <a href="<?= site_url('statistik') ?>" class="btn btn-outline-secondary me-2">
                                <i class="fas fa-chart-bar me-1"></i> Lihat Statistik Publik
                            </a>
                            <button type="button" class="thm-btn" data-bs-dismiss="modal">
                                <span>Selesai &amp; Tutup</span>
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Satisfaction Survey Vanilla & jQuery Controller -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const surveyModal = document.getElementById('modalSurveyKepuasan');
    const surveyForm = document.getElementById('surveyKepuasanForm');
    const formBefore = document.getElementById('surveyFormBeforeSubmit');
    const successAfter = document.getElementById('surveySuccessAfterSubmit');
    const ratingHidden = document.getElementById('surveyRatingOverall');
    const ratingFeedback = document.getElementById('surveyRatingFeedback');
    const emojiCards = document.querySelectorAll('.survey-emoji-card');
    const isAnonymousCheck = document.getElementById('surveyIsAnonymous');
    const identityFields = document.getElementById('surveyIdentityFields');
    const feedbackTextarea = document.getElementById('surveyFeedback');
    const charCounter = document.getElementById('surveyCharCount');
    const quickTags = document.querySelectorAll('.btn-quick-tag');
    const fillAgainBtn = document.getElementById('surveyBtnFillAgain');
    const submitBtn = document.getElementById('surveyBtnSubmit');
    const submitSpinner = document.getElementById('surveySubmitSpinner');
    const submitIcon = document.getElementById('surveySubmitIcon');

    // Emoji Rating Text Mapping
    const ratingLabels = {
        1: { text: '1 - Sangat Buruk', stars: '★☆☆☆☆', badge: 'bg-danger-subtle text-danger border-danger-subtle' },
        2: { text: '2 - Kurang Baik', stars: '★★☆☆☆', badge: 'bg-warning-subtle text-warning border-warning-subtle' },
        3: { text: '3 - Cukup Baik', stars: '★★★☆☆', badge: 'bg-info-subtle text-info border-info-subtle' },
        4: { text: '4 - Baik / Puas', stars: '★★★★☆', badge: 'bg-primary-subtle text-primary border-primary-subtle' },
        5: { text: '5 - Sangat Puas', stars: '★★★★★', badge: 'bg-success-subtle text-success border-success-subtle' }
    };

    // 1. Emoji Selection Click Handling
    emojiCards.forEach(card => {
        card.addEventListener('click', function () {
            emojiCards.forEach(c => c.classList.remove('active'));
            this.classList.add('active');
            const rating = parseInt(this.getAttribute('data-rating'), 10) || 5;
            ratingHidden.value = rating;

            // Also update all aspect stars to match general rating by default
            document.querySelectorAll('.survey-aspect-stars').forEach(group => {
                const hiddenInput = group.querySelector('input');
                if (hiddenInput) hiddenInput.value = rating;
                const stars = group.querySelectorAll('.aspect-star');
                stars.forEach((s, idx) => {
                    if (idx < rating) {
                        s.classList.add('active');
                    } else {
                        s.classList.remove('active');
                    }
                });
            });

            // Update feedback pill
            const info = ratingLabels[rating] || ratingLabels[5];
            ratingFeedback.innerHTML = `<span class="badge ${info.badge} border px-3 py-1"><i class="fas fa-check-circle me-1"></i> Penilaian Dipilih: <strong>${info.text}</strong></span>`;
        });
    });

    // 2. Aspect Stars Interactive Click Handling
    document.querySelectorAll('.survey-aspect-stars').forEach(group => {
        const hiddenInput = group.querySelector('input');
        const stars = group.querySelectorAll('.aspect-star');

        stars.forEach(star => {
            star.addEventListener('click', function () {
                const val = parseInt(this.getAttribute('data-value'), 10);
                if (hiddenInput) hiddenInput.value = val;
                stars.forEach((s, idx) => {
                    if (idx < val) {
                        s.classList.add('active');
                    } else {
                        s.classList.remove('active');
                    }
                });
            });
        });
    });

    // 3. Anonymous Toggle
    if (isAnonymousCheck && identityFields) {
        isAnonymousCheck.addEventListener('change', function () {
            if (this.checked) {
                identityFields.classList.add('d-none');
            } else {
                identityFields.classList.remove('d-none');
            }
        });
    }

    // 4. Quick Tags Appending
    quickTags.forEach(tag => {
        tag.addEventListener('click', function () {
            const addText = this.getAttribute('data-text');
            if (!addText || !feedbackTextarea) return;

            let current = feedbackTextarea.value.trim();
            if (current.length > 0) {
                if (!current.includes(addText)) {
                    feedbackTextarea.value = current + ' ' + addText;
                }
            } else {
                feedbackTextarea.value = addText;
            }

            if (charCounter) {
                charCounter.textContent = feedbackTextarea.value.length;
            }
            feedbackTextarea.focus();
        });
    });

    // 5. Textarea Character Counter
    if (feedbackTextarea && charCounter) {
        feedbackTextarea.addEventListener('input', function () {
            charCounter.textContent = this.value.length;
        });
    }

    // 5.5 Role Select Validation Helper
    const roleSelect = surveyForm ? surveyForm.querySelector('[name="respondent_role"]') : null;
    if (roleSelect) {
        roleSelect.addEventListener('change', function () {
            if (this.value.trim()) {
                this.classList.remove('is-invalid');
            }
        });
    }

    // 5.6 Captcha Input Validation Helper
    const captchaInput = surveyForm ? surveyForm.querySelector('[name="captcha_answer"]') : null;
    if (captchaInput) {
        captchaInput.addEventListener('input', function () {
            if (this.value.trim()) {
                this.classList.remove('is-invalid');
            }
        });
    }

    // 6. Form Submission (AJAX with Fallback)
    if (surveyForm) {
        surveyForm.addEventListener('submit', function (e) {
            e.preventDefault();

            // Client-side validation: Kategori / Peran Pengguna wajib dipilih
            if (roleSelect && !roleSelect.value.trim()) {
                roleSelect.classList.add('is-invalid');
                roleSelect.focus();
                roleSelect.reportValidity();
                return;
            }

            // Client-side validation: Captcha matematika wajib diisi
            if (captchaInput && !captchaInput.value.trim()) {
                captchaInput.classList.add('is-invalid');
                captchaInput.focus();
                captchaInput.reportValidity();
                return;
            }

            // UI Feedback: Button Loading
            if (submitBtn) submitBtn.disabled = true;
            if (submitSpinner) submitSpinner.classList.remove('d-none');
            if (submitIcon) submitIcon.classList.add('d-none');

            hideErrorAlert();

            const formData = new FormData(surveyForm);
            const actionUrl = surveyForm.getAttribute('action') || window.location.origin + '/survei-kepuasan';

            fetch(actionUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(async res => {
                let data = null;
                try {
                    data = await res.json();
                } catch (jsonErr) {
                    data = null;
                }

                if (!res.ok || !data || !data.success) {
                    const errorMsg = (data && data.message) ? data.message : ('Gagal mengirim survei (Kode: ' + res.status + '). Silakan periksa kembali isian formulir.');
                    showErrorAlert(errorMsg);
                    return;
                }

                hideErrorAlert();
                showSuccessState(data);
            })
            .catch(err => {
                showErrorAlert('Gagal terhubung ke server. Pastikan koneksi internet Anda aktif lalu coba kirim kembali.');
            })
            .finally(() => {
                if (submitBtn) submitBtn.disabled = false;
                if (submitSpinner) submitSpinner.classList.add('d-none');
                if (submitIcon) submitIcon.classList.remove('d-none');
            });
        });
    }

    function showErrorAlert(msg) {
        const alertBox = document.getElementById('surveyAlertError');
        const alertMsg = document.getElementById('surveyAlertErrorMessage');
        if (alertBox && alertMsg) {
            alertMsg.textContent = msg || 'Terjadi kesalahan pada formulir survei.';
            alertBox.classList.remove('d-none');
            alertBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }

    function hideErrorAlert() {
        const alertBox = document.getElementById('surveyAlertError');
        if (alertBox) {
            alertBox.classList.add('d-none');
        }
    }

    function showSuccessState(data) {
        if (!data || !data.success) return;

        // Fill receipt
        const ticketEl = document.getElementById('receiptTicketNumber');
        const timeEl = document.getElementById('receiptSubmittedAt');
        const ratingEl = document.getElementById('receiptRatingScore');
        const roleEl = document.getElementById('receiptRespondentRole');

        if (ticketEl) ticketEl.textContent = data.ticket_number || 'IKM-2026-X79B';
        if (timeEl) timeEl.textContent = data.submitted_at || (new Date()).toLocaleString('id-ID');
        if (roleEl) roleEl.textContent = data.respondent_role || 'Penyedia Barang / Jasa';

        if (ratingEl) {
            const r = parseInt(data.rating_overall, 10) || 5;
            const info = ratingLabels[r] || ratingLabels[5];
            ratingEl.innerHTML = `<span class="text-warning">${info.stars}</span> (${info.text})`;
        }

        // Toggle Views
        if (formBefore) formBefore.classList.add('d-none');
        if (successAfter) {
            successAfter.classList.remove('d-none');
            successAfter.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    // 7. Fill Again / Reset Button
    if (fillAgainBtn) {
        fillAgainBtn.addEventListener('click', function () {
            hideErrorAlert();
            if (surveyForm) surveyForm.reset();
            // Reset emoji cards to 5
            emojiCards.forEach(c => c.classList.remove('active'));
            const defaultCard = document.querySelector('.survey-emoji-card[data-rating="5"]');
            if (defaultCard) defaultCard.classList.add('active');
            if (ratingHidden) ratingHidden.value = '5';
            if (identityFields) identityFields.classList.add('d-none');
            if (charCounter) charCounter.textContent = '0';
            if (captchaInput) {
                captchaInput.value = '';
                captchaInput.classList.remove('is-invalid');
            }

            if (successAfter) successAfter.classList.add('d-none');
            if (formBefore) {
                formBefore.classList.remove('d-none');
                formBefore.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    }

    // 8. Auto-open modal if URL hash is #survei or #modalSurveyKepuasan
    if (window.location.hash === '#survei' || window.location.hash === '#modalSurveyKepuasan' || window.location.hash === '#survei-kepuasan') {
        const bootstrapModal = bootstrap.Modal.getOrCreateInstance(surveyModal);
        if (bootstrapModal) bootstrapModal.show();
    }
});
</script>
