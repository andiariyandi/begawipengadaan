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

<?php if (session()->getFlashdata('message') || session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4" role="alert">
        <i class="fas fa-check-circle me-2"></i> <?= esc(session()->getFlashdata('message') ?: session()->getFlashdata('success')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show rounded-4 mb-4" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i> <?= esc(session()->getFlashdata('error')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- Profile Body -->
<div class="row g-4">
    <!-- Left Column: Data Diri Form -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
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

                    <div class="col-md-6">
                        <label class="form-label small fw-semibold text-muted">Provinsi</label>
                        <select name="province_code" class="form-select rounded-3">
                            <option value="">Pilih Provinsi</option>
                            <?php foreach ($provinces ?? [] as $p): ?>
                                <option value="<?= esc($p['kode']) ?>" <?= ($user['province_code'] ?? '') === $p['kode'] ? 'selected' : '' ?>>
                                    <?= esc($p['nama']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold text-muted">Kode Pos</label>
                        <input type="text" name="postal_code" class="form-control rounded-3" value="<?= esc($profile['postal_code'] ?? '') ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-semibold text-muted">Alamat Lengkap</label>
                        <textarea name="address" class="form-control rounded-3" rows="3"><?= esc($profile['address'] ?? '') ?></textarea>
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
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
            <h2 class="h5 fw-bold mb-3 pb-2 border-bottom" style="color: #082f5c;">
                <i class="fas fa-key text-warning me-2"></i> Ubah Kata Sandi
            </h2>
            <form action="<?= route_to('workspace.profile.password.update') ?>" method="post">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Kata Sandi Saat Ini</label>
                    <input type="password" name="current_password" class="form-control rounded-3" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Kata Sandi Baru</label>
                    <input type="password" name="new_password" class="form-control rounded-3" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Ulangi Kata Sandi Baru</label>
                    <input type="password" name="confirm_password" class="form-control rounded-3" required>
                </div>
                <button type="submit" class="btn btn-outline-primary btn-sm w-100 rounded-pill py-2">
                    Perbarui Kata Sandi
                </button>
            </form>
        </div>

        <!-- Info Ringkas Akun -->
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
            <h2 class="h5 fw-bold mb-3 pb-2 border-bottom" style="color: #082f5c;">
                <i class="fas fa-shield-alt text-success me-2"></i> Status Akun
            </h2>
            <div class="list-group list-group-flush small">
                <div class="list-group-item px-0 d-flex justify-content-between">
                    <span class="text-muted">Tipe Profil:</span>
                    <strong><?= esc(ucfirst($user['profile_type'] ?? 'Public')) ?></strong>
                </div>
                <div class="list-group-item px-0 d-flex justify-content-between">
                    <span class="text-muted">Status:</span>
                    <span class="badge bg-success-subtle text-success">Aktif</span>
                </div>
                <div class="list-group-item px-0 d-flex justify-content-between">
                    <span class="text-muted">Terdaftar:</span>
                    <span><?= !empty($user['created_at']) ? esc(date('d M Y', strtotime($user['created_at']))) : '-' ?></span>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
