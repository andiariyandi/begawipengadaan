<?= $this->extend('main/workspace/layouts/workspace') ?>

<?= $this->section('content') ?>
<!-- Header -->
<div class="ws-page-header">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <span class="ws-eyebrow"><i class="fas fa-user-circle"></i> Manajemen Profil</span>
            <h1><?= esc($user['name'] ?? $userName ?? 'Profil Pengguna') ?></h1>
            <p>Kelola identitas, alamat, data profil spesifik, dan keamanan akun Anda.</p>
        </div>
        <div>
            <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill fw-semibold fs-6">
                <i class="fas fa-id-badge me-1"></i> <?= esc(strtoupper($user['profile_type'] ?? 'PUBLIC')) ?>
            </span>
        </div>
    </div>
</div>

<?php if (! empty($latestRequest)): ?>
    <?php
    $reqStatus = $latestRequest['status'] ?? 'submitted';
    $reqPayload = json_decode((string) ($latestRequest['requested_payload'] ?? '{}'), true) ?: [];
    ?>
    <?php if (in_array($reqStatus, ['submitted', 'in_review'], true)): ?>
        <div class="alert alert-warning border-warning-subtle shadow-xs rounded-4 p-3 mb-4 d-flex align-items-start gap-3">
            <span class="w-40-px h-40-px rounded-circle bg-warning-subtle text-warning d-flex align-items-center justify-content-center flex-shrink-0 mt-1">
                <i class="fas fa-hourglass-half"></i>
            </span>
            <div class="flex-grow-1">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                    <h6 class="fw-bold text-dark mb-0">Permohonan Perubahan Profil Sedang Diproses</h6>
                    <span class="badge bg-warning text-dark px-2-5 py-1 text-2xs fw-semibold">Menunggu Persetujuan Admin / Operator</span>
                </div>
                <p class="text-secondary small mb-2">
                    Diajukan pada <strong><?= date('d M Y, H:i', strtotime($latestRequest['created_at'])) ?> WIB</strong>.
                    Perubahan data identitas Anda sedang dalam antrean verifikasi oleh Administrator atau Operator.
                </p>
                <?php if (! empty($latestRequest['reason'])): ?>
                    <div class="p-2 bg-white rounded-3 border text-xs text-dark mb-2">
                        <strong>Catatan/Alasan Pengajuan:</strong> <?= esc($latestRequest['reason']) ?>
                    </div>
                <?php endif; ?>
                <?php if (! empty($reqPayload)): ?>
                    <div class="small text-muted">
                        <strong>Field yang diajukan diubah:</strong>
                        <span class="badge bg-light text-dark border ms-1"><?= implode(', ', array_map(static fn($k) => ucwords(str_replace('_', ' ', $k)), array_keys($reqPayload))) ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php elseif ($reqStatus === 'revision_required'): ?>
        <div class="alert alert-danger border-danger-subtle shadow-xs rounded-4 p-3 mb-4 d-flex align-items-start gap-3">
            <span class="w-40-px h-40-px rounded-circle bg-danger-subtle text-danger d-flex align-items-center justify-content-center flex-shrink-0 mt-1">
                <i class="fas fa-exclamation-circle"></i>
            </span>
            <div class="flex-grow-1">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                    <h6 class="fw-bold text-danger mb-0">Perbaikan / Revisi Diperlukan</h6>
                    <span class="badge bg-danger text-white px-2-5 py-1 text-2xs fw-semibold">Perlu Revisi</span>
                </div>
                <p class="text-secondary small mb-2">
                    Verifikator meminta revisi terhadap pengajuan perubahan profil Anda.
                </p>
                <div class="p-2 bg-white rounded-3 border text-xs text-danger mb-2">
                    <strong>Catatan Revisi:</strong> <?= esc($latestRequest['review_notes'] ?: 'Mohon periksa dan perbarui data yang valid.') ?>
                </div>
                <small class="text-muted">Silakan sesuaikan data pada formulir di bawah ini kemudian klik <strong>Ajukan Perubahan Profil</strong> kembali.</small>
            </div>
        </div>
    <?php elseif ($reqStatus === 'rejected'): ?>
        <div class="alert alert-secondary border shadow-xs rounded-4 p-3 mb-4 d-flex align-items-start gap-3">
            <span class="w-40-px h-40-px rounded-circle bg-light text-secondary d-flex align-items-center justify-content-center flex-shrink-0 mt-1">
                <i class="fas fa-times-circle"></i>
            </span>
            <div class="flex-grow-1">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                    <h6 class="fw-bold text-dark mb-0">Pengajuan Perubahan Terakhir Ditolak</h6>
                    <span class="badge bg-secondary text-white px-2-5 py-1 text-2xs">Ditolak</span>
                </div>
                <p class="text-secondary small mb-0">
                    Alasan penolakan: <em><?= esc($latestRequest['reason'] ?: 'Data tidak sesuai ketentuan.') ?></em>. Anda dapat mengajukan permohonan baru dengan data yang valid.
                </p>
            </div>
        </div>
    <?php elseif ($reqStatus === 'approved'): ?>
        <div class="alert alert-success border-success-subtle shadow-xs rounded-4 p-3 mb-4 d-flex align-items-center gap-3">
            <span class="w-36-px h-36-px rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center flex-shrink-0">
                <i class="fas fa-check-circle"></i>
            </span>
            <div class="flex-grow-1">
                <span class="text-success small fw-semibold">Perubahan profil terakhir telah disetujui dan diterapkan pada <?= date('d M Y, H:i', strtotime($latestRequest['applied_at'] ?? $latestRequest['updated_at'])) ?> WIB.</span>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>

<!-- Profile Body -->
<div class="row g-3 g-md-4">
    <!-- Left Column: Data Diri Form -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 p-3 p-md-4 bg-white mb-3">
            <h2 class="h5 fw-bold mb-4 pb-2 border-bottom" style="color: #082f5c;">
                <i class="fas fa-user-edit text-primary me-2"></i> Data Identitas Akun
            </h2>

            <form action="<?= route_to('workspace.profile.update') ?>" method="post">
                <?= csrf_field() ?>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold text-muted">Username</label>
                        <input type="text" name="username" class="form-control rounded-3" value="<?= esc($user['username'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold text-muted">Email</label>
                        <input type="email" name="email" class="form-control rounded-3" value="<?= esc($user['email'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold text-muted">Nama Lengkap</label>
                        <input type="text" name="full_name" class="form-control rounded-3" value="<?= esc($user['name'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold text-muted">Nomor HP / WhatsApp</label>
                        <input type="text" name="phone" class="form-control rounded-3" value="<?= esc($user['phone'] ?? '') ?>" required>
                    </div>

                    <div class="col-12 mt-4">
                        <h3 class="h6 fw-bold mb-3 pb-1 border-bottom" style="color: #082f5c;">Alamat & Wilayah</h3>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label small fw-semibold text-muted">Provinsi Domisili</label>
                        <select name="province_code" class="form-select rounded-3">
                            <option value="">Pilih Provinsi</option>
                            <?php foreach ($provinces ?? [] as $p): ?>
                                <option value="<?= esc($p['kode']) ?>" <?= (($profile['province_code'] ?? $user['province_code'] ?? '') === $p['kode']) ? 'selected' : '' ?>>
                                    <?= esc($p['nama']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-semibold text-muted">Alamat Lengkap</label>
                        <textarea name="address_line" class="form-control rounded-3" rows="3" placeholder="Masukkan alamat lengkap domisili Anda..."><?= esc($profile['address_line'] ?? $profile['address'] ?? '') ?></textarea>
                    </div>

                    <!-- Profil Spesifik Sesuai Tipe Pengguna -->
                    <?php if ($profileType === 'asn'): ?>
                        <div class="col-12 mt-4">
                            <h3 class="h6 fw-bold mb-3 pb-1 border-bottom" style="color: #082f5c;">Data Kepegawaian & Instansi ASN</h3>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">NIP / Nomor Identitas Pegawai</label>
                            <input type="text" name="employee_identifier" class="form-control rounded-3" value="<?= esc($detail['employee_identifier'] ?? '') ?>" placeholder="Nomor Induk Pegawai 18 digit">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Pangkat / Golongan Ruang</label>
                            <input type="text" name="rank_grade" class="form-control rounded-3" value="<?= esc($detail['rank_grade'] ?? '') ?>" placeholder="Contoh: Pembina (IV/a)">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Nama Instansi</label>
                            <input type="text" name="institution_name" class="form-control rounded-3" value="<?= esc($detail['institution_name'] ?? '') ?>" placeholder="Contoh: Pemerintah Provinsi Lampung">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Unit Kerja / OPD</label>
                            <input type="text" name="unit_name" class="form-control rounded-3" value="<?= esc($detail['unit_name'] ?? '') ?>" placeholder="Contoh: Biro Pengadaan Barang dan Jasa">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-muted">Jabatan / Posisi</label>
                            <input type="text" name="position_name" class="form-control rounded-3" value="<?= esc($detail['position_name'] ?? '') ?>" placeholder="Contoh: Pejabat Fungsional Pengelola Pengadaan">
                        </div>
                    <?php elseif ($profileType === 'provider'): ?>
                        <div class="col-12 mt-4">
                            <h3 class="h6 fw-bold mb-3 pb-1 border-bottom" style="color: #082f5c;">Data Badan Usaha / Penyedia</h3>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Nama Badan Usaha / Perusahaan</label>
                            <input type="text" name="business_name" class="form-control rounded-3" value="<?= esc($detail['business_name'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Nomor Induk Berusaha (NIB)</label>
                            <input type="text" name="business_identifier" class="form-control rounded-3" value="<?= esc($detail['business_identifier'] ?? '') ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-muted">Jabatan Pendaftar</label>
                            <input type="text" name="registrant_position" class="form-control rounded-3" value="<?= esc($detail['registrant_position'] ?? '') ?>" placeholder="Contoh: Direktur Utama / Penanggung Jawab">
                        </div>
                    <?php elseif ($profileType === 'academic'): ?>
                        <div class="col-12 mt-4">
                            <h3 class="h6 fw-bold mb-3 pb-1 border-bottom" style="color: #082f5c;">Data Institusi Akademik</h3>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Nama Perguruan Tinggi / Institusi</label>
                            <input type="text" name="institution_name" class="form-control rounded-3" value="<?= esc($detail['institution_name'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Jabatan / Peran Akademik</label>
                            <input type="text" name="role_name" class="form-control rounded-3" value="<?= esc($detail['role_name'] ?? '') ?>" placeholder="Contoh: Dosen / Peneliti PBJ">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-muted">Bidang Keahlian</label>
                            <input type="text" name="expertise" class="form-control rounded-3" value="<?= esc($detail['expertise'] ?? '') ?>" placeholder="Contoh: Hukum Kontrak Konstruksi">
                        </div>
                    <?php elseif ($profileType === 'public'): ?>
                        <div class="col-12 mt-4">
                            <h3 class="h6 fw-bold mb-3 pb-1 border-bottom" style="color: #082f5c;">Data Masyarakat Umum</h3>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">NIK (Nomor Induk Kependudukan)</label>
                            <input type="text" name="national_identifier" class="form-control rounded-3" value="<?= esc($detail['national_identifier'] ?? '') ?>" placeholder="16 digit NIK">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Organisasi / Lembaga Komunitas</label>
                            <input type="text" name="organization_name" class="form-control rounded-3" value="<?= esc($detail['organization_name'] ?? '') ?>" placeholder="Kosongkan bila mandiri">
                        </div>
                    <?php endif; ?>

                    <div class="col-12 mt-4">
                        <h3 class="h6 fw-bold mb-3 pb-1 border-bottom" style="color: #082f5c;">Alasan Perubahan Data Profil</h3>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-semibold text-muted">Keterangan / Alasan Pengajuan Perubahan (Opsional)</label>
                        <textarea name="reason" class="form-control rounded-3" rows="2" placeholder="Jelaskan alasan perubahan data profil Anda (misal: mutasi unit kerja, perbaikan NIP, pembaruan nomor kontak)..."></textarea>
                    </div>

                    <div class="col-12 mt-4 pt-2 border-top">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                            <div class="text-muted small" style="max-width: 450px;">
                                <i class="fas fa-shield-alt text-primary me-1"></i>
                                <span>Perubahan data identitas akan diverifikasi &amp; disetujui terlebih dahulu oleh Administrator/Operator sebelum aktif di sistem.</span>
                            </div>
                            <button type="submit" class="btn btn-primary px-4 py-2-5 rounded-pill fw-bold d-inline-flex align-items-center gap-2">
                                <i class="fas fa-paper-plane"></i>
                                <span>Ajukan Perubahan Profil</span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Right Column: Keamanan & Role Pengadaan -->
    <div class="col-lg-4">
        <!-- Keamanan Password -->
        <div class="card border-0 shadow-sm rounded-4 p-3 p-md-4 bg-white mb-3">
            <h2 class="h6 fw-bold mb-3 pb-2 border-bottom" style="color: #082f5c;">
                <i class="fas fa-key text-warning me-2"></i> Ubah Kata Sandi
            </h2>
            <form action="<?= route_to('workspace.profile.password.update') ?>" method="post">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Kata Sandi Saat Ini</label>
                    <input type="password" name="current_password" class="form-control rounded-3" required autocomplete="current-password">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Kata Sandi Baru</label>
                    <input type="password" name="new_password" class="form-control rounded-3" required autocomplete="new-password">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Ulangi Kata Sandi Baru</label>
                    <input type="password" name="confirm_password" class="form-control rounded-3" required autocomplete="new-password">
                </div>
                <button type="submit" class="btn btn-outline-primary btn-sm w-100 rounded-pill py-2">
                    <i class="fas fa-lock me-1"></i> Perbarui Kata Sandi
                </button>
            </form>
        </div>

        <!-- Detail Profil Spesifik jika ada -->
        <?php if (! empty($detail)): ?>
            <div class="card border-0 shadow-sm rounded-4 p-3 p-md-4 bg-white mb-3">
                <h2 class="h6 fw-bold mb-3 pb-2 border-bottom" style="color: #082f5c;">
                    <i class="fas fa-id-card-alt text-info me-2"></i> Profil <?= esc(strtoupper($profileType ?? '')) ?>
                </h2>
                <div class="list-group list-group-flush small">
                    <?php if (! empty($detail['employee_identifier'])): ?>
                        <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <span class="text-muted">NIP / Identitas:</span>
                            <span class="fw-semibold text-break"><?= esc($detail['employee_identifier']) ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if (! empty($detail['institution_name'])): ?>
                        <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <span class="text-muted">Instansi:</span>
                            <span class="fw-semibold text-end text-break"><?= esc($detail['institution_name']) ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if (! empty($detail['position_name'])): ?>
                        <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <span class="text-muted">Jabatan:</span>
                            <span class="fw-semibold text-end text-break"><?= esc($detail['position_name']) ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if (! empty($detail['business_name'])): ?>
                        <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <span class="text-muted">Nama Usaha:</span>
                            <span class="fw-semibold text-end text-break"><?= esc($detail['business_name']) ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if (! empty($detail['business_identifier'])): ?>
                        <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <span class="text-muted">NIB / Identitas:</span>
                            <span class="fw-semibold text-break"><?= esc($detail['business_identifier']) ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if (! empty($detail['occupation_status'])): ?>
                        <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <span class="text-muted">Status Kerja:</span>
                            <span class="fw-semibold text-break"><?= esc(ucwords(str_replace('_', ' ', $detail['occupation_status']))) ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Info Ringkas Akun -->
        <div class="card border-0 shadow-sm rounded-4 p-3 p-md-4 bg-white">
            <h2 class="h6 fw-bold mb-3 pb-2 border-bottom" style="color: #082f5c;">
                <i class="fas fa-shield-alt text-success me-2"></i> Status Akun
            </h2>
            <div class="list-group list-group-flush small">
                <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                    <span class="text-muted">Tipe Profil:</span>
                    <strong><?= esc(ucfirst($user['profile_type'] ?? 'Public')) ?></strong>
                </div>
                <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                    <span class="text-muted">Status:</span>
                    <span class="badge bg-success-subtle text-success">Aktif</span>
                </div>
                <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                    <span class="text-muted">Terdaftar:</span>
                    <span><?= !empty($user['created_at']) ? esc(date('d M Y', strtotime($user['created_at']))) : '-' ?></span>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
