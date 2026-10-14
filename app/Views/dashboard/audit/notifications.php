<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Log Notifikasi Sistem') ?></h6>
        <p class="text-secondary-light mb-0">Riwayat pengiriman, pemantauan status penerimaan, dan manajemen siaran notifikasi sistem.</p>
    </div>
    <div class="d-flex flex-wrap align-items-center gap-3">
        <form action="<?= site_url('dashboard/sistem/notifikasi/baca-semua') ?>" method="post" class="d-inline" onsubmit="return confirm('Tandai seluruh notifikasi sistem sebagai telah dibaca?')">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-outline-neutral-400 radius-8 px-16 py-10 d-inline-flex align-items-center gap-2">
                <iconify-icon icon="solar:check-read-outline" class="text-xl"></iconify-icon>
                <span>Tandai Semua Dibaca</span>
            </button>
        </form>
        <button type="button" class="btn btn-primary-600 radius-8 px-20 py-10 d-inline-flex align-items-center gap-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#broadcastModal">
            <iconify-icon icon="solar:letter-unread-bold" class="text-xl"></iconify-icon>
            <span>+ Kirim Siaran Notifikasi</span>
        </button>
    </div>
</div>

<!-- KPI Metric Cards -->
<div class="row row-cols-xxxl-4 row-cols-lg-2 row-cols-1 gy-4">
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-1 h-100">
            <div class="card-body p-20">
                <div class="d-flex align-items-center justify-content-between mb-8">
                    <p class="fw-medium text-primary-light mb-0">Total Notifikasi</p>
                    <span class="w-36-px h-36-px bg-primary-100 text-primary-600 rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:bell-linear" class="text-xl"></iconify-icon>
                    </span>
                </div>
                <h5 class="mb-0 fw-bold"><?= number_format($stats['total'] ?? 0, 0, ',', '.') ?></h5>
                <p class="text-xs text-primary-light mt-8 mb-0">Tercatat di seluruh kanal komunikasi</p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-2 h-100">
            <div class="card-body p-20">
                <div class="d-flex align-items-center justify-content-between mb-8">
                    <p class="fw-medium text-primary-light mb-0">Belum Dibaca</p>
                    <span class="w-36-px h-36-px bg-warning-100 text-warning-main rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:clock-circle-outline" class="text-xl"></iconify-icon>
                    </span>
                </div>
                <h5 class="mb-0 fw-bold"><?= number_format($stats['unread'] ?? 0, 0, ',', '.') ?></h5>
                <p class="text-xs text-primary-light mt-8 mb-0">Menunggu dibuka oleh pengguna</p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-3 h-100">
            <div class="card-body p-20">
                <div class="d-flex align-items-center justify-content-between mb-8">
                    <p class="fw-medium text-primary-light mb-0">Hari Ini</p>
                    <span class="w-36-px h-36-px bg-success-100 text-success-main rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:calendar-date-outline" class="text-xl"></iconify-icon>
                    </span>
                </div>
                <h5 class="mb-0 fw-bold"><?= number_format($stats['today'] ?? 0, 0, ',', '.') ?></h5>
                <p class="text-xs text-primary-light mt-8 mb-0">Terkirim dalam 24 jam terakhir</p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-5 h-100">
            <div class="card-body p-20">
                <div class="d-flex align-items-center justify-content-between mb-8">
                    <p class="fw-medium text-primary-light mb-0">Siaran / Broadcast</p>
                    <span class="w-36-px h-36-px bg-info-100 text-info-main rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:megaphone-outline" class="text-xl"></iconify-icon>
                    </span>
                </div>
                <h5 class="mb-0 fw-bold"><?= number_format($stats['broadcast'] ?? 0, 0, ',', '.') ?></h5>
                <p class="text-xs text-primary-light mt-8 mb-0">Pengumuman & informasi massal</p>
            </div>
        </div>
    </div>
</div>

<!-- Main Content Card -->
<div class="card mt-24">
    <div class="card-body p-24">
        <!-- Filter Form -->
        <form method="get" action="<?= site_url('dashboard/sistem/notifikasi') ?>" class="mb-24">
            <div class="row g-12 align-items-center">
                <div class="col-lg-3 col-md-6">
                    <div class="position-relative">
                        <input type="search" name="q" value="<?= esc($filters['q'] ?? '') ?>" class="form-control form-control-sm radius-8 ps-36" placeholder="Cari judul, pesan, penerima...">
                        <iconify-icon icon="ion:search-outline" class="position-absolute top-50 start-0 translate-middle-y ms-12 text-secondary-light"></iconify-icon>
                    </div>
                </div>

                <div class="col-lg-2 col-md-6">
                    <select name="type" class="form-select form-select-sm radius-8" onchange="this.form.submit()">
                        <option value="all" <?= ($filters['type'] ?? 'all') === 'all' ? 'selected' : '' ?>>Semua Kategori</option>
                        <option value="broadcast" <?= ($filters['type'] ?? '') === 'broadcast' ? 'selected' : '' ?>>Siaran Pengumuman</option>
                        <option value="account_approved" <?= ($filters['type'] ?? '') === 'account_approved' ? 'selected' : '' ?>>Akun Disetujui</option>
                        <option value="account_rejected" <?= ($filters['type'] ?? '') === 'account_rejected' ? 'selected' : '' ?>>Akun Ditolak</option>
                        <option value="assignment_graded" <?= ($filters['type'] ?? '') === 'assignment_graded' ? 'selected' : '' ?>>Penilaian Tugas</option>
                        <option value="certificate_issued" <?= ($filters['type'] ?? '') === 'certificate_issued' ? 'selected' : '' ?>>Penerbitan Sertifikat</option>
                        <option value="class_enrolled" <?= ($filters['type'] ?? '') === 'class_enrolled' ? 'selected' : '' ?>>Pembelajaran</option>
                        <option value="event_info" <?= ($filters['type'] ?? '') === 'event_info' ? 'selected' : '' ?>>Event & Agenda</option>
                        <option value="system" <?= ($filters['type'] ?? '') === 'system' ? 'selected' : '' ?>>Sistem</option>
                    </select>
                </div>

                <div class="col-lg-2 col-md-4">
                    <select name="status" class="form-select form-select-sm radius-8" onchange="this.form.submit()">
                        <option value="all" <?= ($filters['status'] ?? 'all') === 'all' ? 'selected' : '' ?>>Semua Status</option>
                        <option value="unread" <?= ($filters['status'] ?? '') === 'unread' ? 'selected' : '' ?>>Belum Dibaca</option>
                        <option value="read" <?= ($filters['status'] ?? '') === 'read' ? 'selected' : '' ?>>Telah Dibaca</option>
                    </select>
                </div>

                <div class="col-lg-2 col-md-4">
                    <select name="profile_type" class="form-select form-select-sm radius-8" onchange="this.form.submit()">
                        <option value="all" <?= ($filters['profile_type'] ?? 'all') === 'all' ? 'selected' : '' ?>>Semua Profil</option>
                        <option value="asn" <?= ($filters['profile_type'] ?? '') === 'asn' ? 'selected' : '' ?>>ASN Pemprov</option>
                        <option value="provider" <?= ($filters['profile_type'] ?? '') === 'provider' ? 'selected' : '' ?>>Penyedia PBJ</option>
                        <option value="academic" <?= ($filters['profile_type'] ?? '') === 'academic' ? 'selected' : '' ?>>Akademisi</option>
                        <option value="public" <?= ($filters['profile_type'] ?? '') === 'public' ? 'selected' : '' ?>>Masyarakat</option>
                    </select>
                </div>

                <div class="col-lg-3 col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary-600 radius-8 px-16 flex-grow-1">Filter</button>
                    <?php if (!empty($filters['q']) || ($filters['type'] ?? 'all') !== 'all' || ($filters['status'] ?? 'all') !== 'all' || ($filters['profile_type'] ?? 'all') !== 'all'): ?>
                        <a href="<?= site_url('dashboard/sistem/notifikasi') ?>" class="btn btn-sm btn-outline-neutral-400 radius-8 px-12" title="Reset Filter">
                            <iconify-icon icon="solar:restart-outline" class="text-base"></iconify-icon>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </form>

        <!-- Notification Table -->
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0 align-middle">
                <thead>
                    <tr>
                        <th style="min-width: 180px;">Penerima</th>
                        <th style="min-width: 260px;">Judul & Pesan</th>
                        <th style="min-width: 140px;">Kategori</th>
                        <th style="min-width: 100px;">Kanal</th>
                        <th style="min-width: 140px;">Waktu Kirim</th>
                        <th style="min-width: 110px;" class="text-center">Status</th>
                        <th style="min-width: 120px;" class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($items)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-48 text-secondary-light">
                                <iconify-icon icon="solar:bell-off-outline" class="text-5xl text-neutral-400 mb-12 d-block mx-auto"></iconify-icon>
                                <h6 class="text-md fw-semibold mb-4">Tidak Ada Notifikasi Ditemukan</h6>
                                <p class="text-sm text-secondary-light mb-0">Belum ada data notifikasi yang sesuai dengan kriteria penyaringan Anda.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($items as $item): 
                            $isRead = !empty($item['read_at']);
                            $catInfo = \App\Services\NotificationService::formatCategory($item['type'] ?? 'system');
                            $timeAgo = \App\Services\NotificationService::timeAgo($item['created_at'] ?? null);
                            $profileLabel = match ($item['user_profile_type'] ?? '') {
                                'asn'      => ['label' => 'ASN', 'badge' => 'bg-primary-50 text-primary-600'],
                                'provider' => ['label' => 'Penyedia', 'badge' => 'bg-warning-50 text-warning-700'],
                                'academic' => ['label' => 'Akademisi', 'badge' => 'bg-info-50 text-info-700'],
                                'public'   => ['label' => 'Masyarakat', 'badge' => 'bg-neutral-100 text-neutral-700'],
                                default    => ['label' => 'User', 'badge' => 'bg-neutral-100 text-neutral-600'],
                            };
                        ?>
                        <tr class="<?= $isRead ? '' : 'bg-primary-50/20' ?>">
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="w-36-px h-36-px rounded-circle bg-neutral-200 text-neutral-800 d-flex justify-content-center align-items-center fw-bold text-xs flex-shrink-0">
                                        <?= strtoupper(substr($item['user_name'] ?? ($item['user_username'] ?? 'U'), 0, 2)) ?>
                                    </div>
                                    <div class="overflow-hidden">
                                        <h6 class="text-sm fw-semibold mb-0 text-truncate" style="max-width: 140px;"><?= esc($item['user_name'] ?? ($item['user_username'] ?? 'Pengguna #' . $item['user_id'])) ?></h6>
                                        <div class="d-flex align-items-center gap-1 mt-2">
                                            <span class="badge text-xxs px-6 py-2 radius-4 <?= $profileLabel['badge'] ?>"><?= $profileLabel['label'] ?></span>
                                            <span class="text-xs text-secondary-light text-truncate" style="max-width: 90px;"><?= esc($item['user_email'] ?? '') ?></span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-semibold text-sm text-primary-light mb-2"><?= esc($item['title'] ?? '-') ?></span>
                                    <span class="text-xs text-secondary-light text-truncate" style="max-width: 320px;">
                                        <?= esc($item['body'] ?? '-') ?>
                                    </span>
                                </div>
                            </td>
                            <td>
                                <span class="badge text-xs px-10 py-4 radius-6 <?= $catInfo['badge'] ?>">
                                    <?= esc($catInfo['label']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-neutral-100 text-neutral-800 border radius-6 text-xs px-8 py-3">
                                    <?= esc(ucfirst($item['channel'] ?? 'database')) ?>
                                </span>
                            </td>
                            <td>
                                <span class="text-xs text-primary-light d-block fw-medium">
                                    <?= !empty($item['created_at']) ? date('d/m/Y H:i', strtotime($item['created_at'])) : '-' ?>
                                </span>
                                <span class="text-xxs text-secondary-light"><?= $timeAgo ?></span>
                            </td>
                            <td class="text-center">
                                <?php if ($isRead): ?>
                                    <span class="badge bg-success-focus text-success-main border-success px-8 py-4 radius-6 text-xs d-inline-flex align-items-center gap-1">
                                        <iconify-icon icon="solar:check-circle-outline"></iconify-icon>
                                        Dibaca
                                    </span>
                                    <span class="d-block text-xxs text-secondary-light mt-2" title="Waktu dibaca: <?= esc($item['read_at']) ?>">
                                        <?= date('d/m H:i', strtotime($item['read_at'])) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-warning-focus text-warning-main border-warning px-8 py-4 radius-6 text-xs d-inline-flex align-items-center gap-1">
                                        <iconify-icon icon="solar:unread-outline"></iconify-icon>
                                        Belum
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex align-items-center gap-1">
                                    <!-- Detail Modal Trigger -->
                                    <button type="button" 
                                            class="w-32-px h-32-px bg-primary-50 text-primary-600 rounded-circle d-flex justify-content-center align-items-center hover-bg-primary-100 transition-2 border-0 btn-show-detail" 
                                            title="Lihat Rincian"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#detailNotificationModal"
                                            data-id="<?= esc($item['id']) ?>"
                                            data-title="<?= esc($item['title'] ?? '') ?>"
                                            data-body="<?= esc($item['body'] ?? '') ?>"
                                            data-recipient="<?= esc($item['user_name'] ?? ($item['user_username'] ?? 'User #' . $item['user_id'])) ?>"
                                            data-email="<?= esc($item['user_email'] ?? '-') ?>"
                                            data-profile="<?= esc($profileLabel['label']) ?>"
                                            data-type="<?= esc($catInfo['label']) ?>"
                                            data-channel="<?= esc(ucfirst($item['channel'] ?? 'database')) ?>"
                                            data-time="<?= !empty($item['created_at']) ? date('d F Y H:i:s', strtotime($item['created_at'])) : '-' ?>"
                                            data-read="<?= $isRead ? date('d F Y H:i:s', strtotime($item['read_at'])) : 'Belum dibaca' ?>"
                                            data-json="<?= esc($item['data'] ?? '') ?>">
                                        <iconify-icon icon="solar:eye-outline" class="text-md"></iconify-icon>
                                    </button>

                                    <!-- Mark as Read Form (if unread) -->
                                    <?php if (!$isRead): ?>
                                    <form action="<?= site_url('dashboard/sistem/notifikasi/' . $item['id'] . '/baca') ?>" method="post" class="d-inline">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="w-32-px h-32-px bg-success-50 text-success-600 rounded-circle d-flex justify-content-center align-items-center hover-bg-success-100 transition-2 border-0" title="Tandai Sudah Dibaca">
                                            <iconify-icon icon="solar:check-read-outline" class="text-md"></iconify-icon>
                                        </button>
                                    </form>
                                    <?php endif; ?>

                                    <!-- Delete Form -->
                                    <form action="<?= site_url('dashboard/sistem/notifikasi/' . $item['id'] . '/hapus') ?>" method="post" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus catatan notifikasi ini?')">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="w-32-px h-32-px bg-danger-50 text-danger-600 rounded-circle d-flex justify-content-center align-items-center hover-bg-danger-100 transition-2 border-0" title="Hapus Notifikasi">
                                            <iconify-icon icon="solar:trash-bin-trash-outline" class="text-md"></iconify-icon>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mt-24">
            <span class="text-sm text-secondary-light">
                Menampilkan <?= count($items) ?> dari <?= number_format($total ?? 0, 0, ',', '.') ?> notifikasi
            </span>
            <div class="pagination-container">
                <?= $pager->links('notifications', 'default_full') ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Broadcast Notifikasi -->
<div class="modal fade" id="broadcastModal" tabindex="-1" aria-labelledby="broadcastModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content radius-16 border-0 shadow">
            <div class="modal-header border-bottom border-neutral-200 py-16 px-24">
                <div class="d-flex align-items-center gap-3">
                    <span class="w-40-px h-40-px bg-primary-50 text-primary-600 rounded-circle d-flex justify-content-center align-items-center flex-shrink-0">
                        <iconify-icon icon="solar:megaphone-bold" class="text-2xl"></iconify-icon>
                    </span>
                    <div>
                        <h6 class="modal-title fw-semibold text-lg mb-0" id="broadcastModalLabel">Kirim Siaran Notifikasi</h6>
                        <p class="text-xs text-secondary-light mb-0">Kirimkan pemberitahuan massal atau terarah ke pengguna sistem.</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= site_url('dashboard/sistem/notifikasi/kirim') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body p-24">
                    <div class="row gy-16">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-6" for="target_group">
                                Sasaran Penerima <span class="text-danger-600">*</span>
                            </label>
                            <select name="target_group" id="target_group" class="form-select radius-8" required onchange="toggleUserSelect(this.value)">
                                <option value="all">Semua Pengguna Aktif (Siaran Menyeluruh)</option>
                                <option value="asn">Khusus ASN Pemprov Lampung</option>
                                <option value="provider">Khusus Rekanan / Penyedia PBJ</option>
                                <option value="academic">Khusus Akademisi & Perguruan Tinggi</option>
                                <option value="public">Khusus Masyarakat Umum</option>
                                <option value="user">Pengguna Tertentu (Satu Orang)</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-6" for="type">
                                Kategori Notifikasi <span class="text-danger-600">*</span>
                            </label>
                            <select name="type" id="type" class="form-select radius-8" required>
                                <option value="broadcast">Siaran Pengumuman Umum</option>
                                <option value="system">Pemberitahuan Sistem</option>
                                <option value="class_reminder">Pengingat Pembelajaran / Kelas</option>
                                <option value="event_info">Informasi Event & Agenda PBJ</option>
                                <option value="regulation_info">Sosialisasi Regulasi & Kebijakan</option>
                            </select>
                        </div>

                        <!-- User Select (conditional) -->
                        <div class="col-12 d-none" id="userSelectWrapper">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-6" for="target_user_id">
                                Pilih Pengguna Spesifik <span class="text-danger-600">*</span>
                            </label>
                            <select name="target_user_id" id="target_user_id" class="form-select radius-8">
                                <option value="">-- Pilih Pengguna Terdaftar --</option>
                                <?php if (!empty($targetUsers)): ?>
                                    <?php foreach ($targetUsers as $u): ?>
                                        <option value="<?= esc($u['id']) ?>">
                                            <?= esc($u['name']) ?> (<?= esc($u['email']) ?>) - <?= strtoupper($u['profile_type'] ?? 'USER') ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-6" for="title">
                                Judul Siaran <span class="text-danger-600">*</span>
                            </label>
                            <input type="text" name="title" id="title" class="form-control radius-8" placeholder="Contoh: Pemeliharaan Server PBJ Terjadwal" required maxlength="255">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-6" for="body">
                                Isi Pesan Notifikasi <span class="text-danger-600">*</span>
                            </label>
                            <textarea name="body" id="body" rows="4" class="form-control radius-8" placeholder="Tuliskan isi pesan notifikasi secara rinci dan jelas..." required></textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-6">Kanal Pengiriman</label>
                            <div class="d-flex flex-wrap gap-16">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="channel" id="channel_db" value="database" checked>
                                    <label class="form-check-label text-sm fw-medium" for="channel_db">
                                        In-App / Database (Dashboard Pengguna)
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="channel" id="channel_email" value="email">
                                    <label class="form-check-label text-sm fw-medium" for="channel_email">
                                        Email & In-App
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top border-neutral-200 py-16 px-24">
                    <button type="button" class="btn btn-outline-neutral-300 radius-8 px-20 py-10" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-10 d-inline-flex align-items-center gap-2">
                        <iconify-icon icon="solar:plain-bold" class="text-lg"></iconify-icon>
                        <span>Kirim Sekarang</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Detail Notifikasi -->
<div class="modal fade" id="detailNotificationModal" tabindex="-1" aria-labelledby="detailNotificationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content radius-16 border-0 shadow">
            <div class="modal-header border-bottom border-neutral-200 py-16 px-24">
                <div class="d-flex align-items-center gap-3">
                    <span class="w-36-px h-36-px bg-primary-50 text-primary-600 rounded-circle d-flex justify-content-center align-items-center flex-shrink-0">
                        <iconify-icon icon="solar:info-circle-outline" class="text-xl"></iconify-icon>
                    </span>
                    <h6 class="modal-title fw-semibold text-lg mb-0" id="detailNotificationModalLabel">Detail Notifikasi</h6>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-24">
                <div class="mb-16">
                    <span class="badge bg-neutral-100 text-neutral-800 border radius-6 text-xs px-8 py-3 mb-8" id="modalCategoryBadge">-</span>
                    <h5 class="fw-bold text-primary-light mb-8" id="modalTitle">-</h5>
                </div>

                <div class="bg-neutral-50 radius-8 p-16 mb-16 border border-neutral-200">
                    <div class="row g-12 text-sm">
                        <div class="col-6">
                            <span class="text-secondary-light text-xs d-block">Penerima</span>
                            <span class="fw-semibold text-primary-light d-block text-truncate" id="modalRecipient">-</span>
                            <span class="text-xs text-secondary-light" id="modalEmail">-</span>
                        </div>
                        <div class="col-6">
                            <span class="text-secondary-light text-xs d-block">Profil / Peran</span>
                            <span class="fw-semibold text-primary-light d-block" id="modalProfile">-</span>
                            <span class="text-xs text-secondary-light" id="modalChannel">-</span>
                        </div>
                        <div class="col-6">
                            <span class="text-secondary-light text-xs d-block">Waktu Terkirim</span>
                            <span class="fw-semibold text-primary-light d-block" id="modalSentTime">-</span>
                        </div>
                        <div class="col-6">
                            <span class="text-secondary-light text-xs d-block">Status Dibaca</span>
                            <span class="fw-semibold text-primary-light d-block" id="modalReadStatus">-</span>
                        </div>
                    </div>
                </div>

                <div class="mb-16">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-4">Isi Pesan:</label>
                    <div class="p-16 radius-8 bg-neutral-50 border border-neutral-200 text-sm text-secondary-dark" style="white-space: pre-wrap;" id="modalBody">
                        -
                    </div>
                </div>

                <div id="modalDataWrapper" class="d-none">
                    <label class="form-label fw-semibold text-primary-light text-xs mb-4">Payload / Metadata Tambahan:</label>
                    <pre class="p-12 radius-8 bg-neutral-100 text-neutral-800 text-xxs font-monospace mb-0" id="modalDataJson">-</pre>
                </div>
            </div>
            <div class="modal-footer border-top border-neutral-200 py-12 px-24">
                <button type="button" class="btn btn-neutral-300 radius-8 px-20 py-8" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
function toggleUserSelect(targetGroup) {
    const wrapper = document.getElementById('userSelectWrapper');
    const userSelect = document.getElementById('target_user_id');
    if (targetGroup === 'user') {
        wrapper.classList.remove('d-none');
        userSelect.setAttribute('required', 'required');
    } else {
        wrapper.classList.add('d-none');
        userSelect.removeAttribute('required');
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const detailButtons = document.querySelectorAll('.btn-show-detail');
    detailButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('modalTitle').textContent = this.dataset.title || '-';
            document.getElementById('modalCategoryBadge').textContent = (this.dataset.type || '-') + ' (' + (this.dataset.channel || '-') + ')';
            document.getElementById('modalRecipient').textContent = this.dataset.recipient || '-';
            document.getElementById('modalEmail').textContent = this.dataset.email || '-';
            document.getElementById('modalProfile').textContent = this.dataset.profile || '-';
            document.getElementById('modalChannel').textContent = 'Kanal: ' + (this.dataset.channel || '-');
            document.getElementById('modalSentTime').textContent = this.dataset.time || '-';
            document.getElementById('modalReadStatus').textContent = this.dataset.read || '-';
            document.getElementById('modalBody').textContent = this.dataset.body || '-';

            const rawJson = this.dataset.json;
            const dataWrapper = document.getElementById('modalDataWrapper');
            const dataPre = document.getElementById('modalDataJson');
            if (rawJson && rawJson.trim() !== '' && rawJson.trim() !== 'null') {
                try {
                    const parsed = JSON.parse(rawJson);
                    dataPre.textContent = JSON.stringify(parsed, null, 2);
                    dataWrapper.classList.remove('d-none');
                } catch (e) {
                    dataPre.textContent = rawJson;
                    dataWrapper.classList.remove('d-none');
                }
            } else {
                dataWrapper.classList.add('d-none');
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
