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

                    <div class="col-12 text-end mt-4">
                        <button type="submit" class="btn btn-primary px-4 py-2 rounded-pill">
                            <i class="fas fa-save me-1"></i> Simpan Perubahan Profil
                        </button>
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
