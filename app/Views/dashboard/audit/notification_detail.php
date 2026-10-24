<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$item          = $item ?? [];
$notifId       = (int) ($item['id'] ?? 0);
$relatedNotifs = $relatedNotifs ?? [];
$backUrl       = $backUrl ?? site_url('dashboard/sistem/notifikasi');

$catInfo = \App\Services\NotificationService::formatCategory($item['type'] ?? 'system');
$timeAgo = \App\Services\NotificationService::timeAgo($item['created_at'] ?? null);
$isRead  = !empty($item['read_at']);

$profileLabel = match ($item['user_profile_type'] ?? '') {
    'asn'      => ['label' => 'ASN Pemprov', 'badge' => 'bg-primary-50 text-primary-600 border border-primary-200'],
    'provider' => ['label' => 'Penyedia PBJ', 'badge' => 'bg-warning-50 text-warning-700 border border-warning-200'],
    'academic' => ['label' => 'Akademisi', 'badge' => 'bg-info-50 text-info-700 border border-info-200'],
    'public'   => ['label' => 'Masyarakat', 'badge' => 'bg-neutral-100 text-neutral-700 border border-neutral-300'],
    default    => ['label' => 'Pengguna', 'badge' => 'bg-neutral-100 text-neutral-600 border border-neutral-300'],
};

// Parse JSON payload jika ada
$rawPayload = $item['data'] ?? '';
$parsedData = null;
$actionUrl  = null;
if (!empty($rawPayload)) {
    try {
        $parsedData = is_array($rawPayload) ? $rawPayload : json_decode((string) $rawPayload, true);
        if (is_array($parsedData)) {
            $actionUrl = $parsedData['url'] ?? ($parsedData['link'] ?? ($parsedData['action_url'] ?? null));
        }
    } catch (\Throwable $e) {
        $parsedData = null;
    }
}
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <div class="d-flex align-items-center gap-2 mb-4">
            <a href="<?= esc($backUrl) ?>" class="text-secondary-light hover-text-primary-600 d-inline-flex align-items-center gap-1 text-sm">
                <iconify-icon icon="solar:arrow-left-linear"></iconify-icon>
                <span>Kembali ke Log Notifikasi</span>
            </a>
            <span class="text-secondary-light text-xs">•</span>
            <span class="text-secondary-light text-xs">ID Catatan: #<?= $notifId ?></span>
        </div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Detail Notifikasi Sistem') ?></h6>
        <p class="text-secondary-light mb-0">Rincian rekaman pemberitahuan, data sasaran penerima, waktu distribusi, dan verifikasi status pembacaan.</p>
    </div>
    <div class="d-flex flex-wrap align-items-center gap-2">
        <button type="button" onclick="window.print()" class="btn btn-outline-neutral-300 radius-8 px-16 py-9 d-inline-flex align-items-center gap-2">
            <iconify-icon icon="solar:printer-outline" class="text-lg"></iconify-icon>
            <span>Cetak Rincian</span>
        </button>

        <?php if (!$isRead): ?>
            <form action="<?= site_url('dashboard/sistem/notifikasi/' . $notifId . '/baca') ?>" method="post" class="d-inline">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-outline-success-600 radius-8 px-16 py-9 d-inline-flex align-items-center gap-2">
                    <iconify-icon icon="solar:check-read-outline" class="text-lg"></iconify-icon>
                    <span>Tandai Sudah Dibaca</span>
                </button>
            </form>
        <?php endif; ?>

        <form action="<?= site_url('dashboard/sistem/notifikasi/' . $notifId . '/hapus') ?>" method="post" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus catatan log notifikasi ini?')">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-outline-danger-600 radius-8 px-16 py-9 d-inline-flex align-items-center gap-2">
                <iconify-icon icon="solar:trash-bin-trash-outline" class="text-lg"></iconify-icon>
                <span>Hapus Notifikasi</span>
            </button>
        </form>
    </div>
</div>

<!-- Header Info Strip -->
<div class="row g-3 mb-24">
    <!-- Status Pembacaan Card -->
    <div class="col-xl-3 col-md-6">
        <div class="card p-20 radius-12 border <?= $isRead ? 'bg-success-50 border-success-200' : 'bg-warning-50 border-warning-200' ?> h-100 shadow-none">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-xs fw-semibold text-uppercase d-block mb-1 <?= $isRead ? 'text-success-800' : 'text-warning-800' ?>">Status Penerimaan</span>
                    <h6 class="fw-bold mb-1 <?= $isRead ? 'text-success-main' : 'text-warning-main' ?>">
                        <?= $isRead ? 'Telah Dibaca' : 'Belum Dibaca' ?>
                    </h6>
                    <span class="text-xs text-secondary-light">
                        <?= $isRead ? 'Dibaca: ' . date('d M Y, H:i:s', strtotime($item['read_at'])) : 'Menunggu dibaca oleh penerima' ?>
                    </span>
                </div>
                <div class="w-48-px h-48-px <?= $isRead ? 'bg-success-100 text-success-600' : 'bg-warning-100 text-warning-main' ?> rounded-circle d-flex align-items-center justify-content-center text-2xl flex-shrink-0">
                    <iconify-icon icon="<?= $isRead ? 'solar:check-circle-bold' : 'solar:clock-circle-bold' ?>"></iconify-icon>
                </div>
            </div>
        </div>
    </div>

    <!-- Kategori Card -->
    <div class="col-xl-3 col-md-6">
        <div class="card p-20 radius-12 border bg-base h-100 shadow-none">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-secondary-light text-xs fw-semibold text-uppercase d-block mb-1">Kategori Notifikasi</span>
                    <h6 class="fw-bold text-neutral-800 mb-1"><?= esc($catInfo['label']) ?></h6>
                    <span class="text-xs text-secondary-light">Tipe: <code class="text-primary-600"><?= esc($item['type'] ?? 'system') ?></code></span>
                </div>
                <div class="w-48-px h-48-px bg-primary-50 text-primary-600 rounded-circle d-flex align-items-center justify-content-center text-2xl flex-shrink-0">
                    <iconify-icon icon="solar:bell-bing-bold"></iconify-icon>
                </div>
            </div>
        </div>
    </div>

    <!-- Kanal Card -->
    <div class="col-xl-3 col-md-6">
        <div class="card p-20 radius-12 border bg-base h-100 shadow-none">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-secondary-light text-xs fw-semibold text-uppercase d-block mb-1">Kanal Distribusi</span>
                    <h6 class="fw-bold text-neutral-800 mb-1"><?= esc(ucfirst($item['channel'] ?? 'database')) ?></h6>
                    <span class="text-xs text-secondary-light"><?= ($item['channel'] ?? 'database') === 'email' ? 'Kanal Ganda (Email & In-App)' : 'Pemberitahuan Dashboard (In-App)' ?></span>
                </div>
                <div class="w-48-px h-48-px bg-info-50 text-info-600 rounded-circle d-flex align-items-center justify-content-center text-2xl flex-shrink-0">
                    <iconify-icon icon="solar:letter-unread-bold"></iconify-icon>
                </div>
            </div>
        </div>
    </div>

    <!-- Waktu Kirim Card -->
    <div class="col-xl-3 col-md-6">
        <div class="card p-20 radius-12 border bg-base h-100 shadow-none">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-secondary-light text-xs fw-semibold text-uppercase d-block mb-1">Waktu Kirim Sistem</span>
                    <h6 class="fw-bold text-neutral-800 mb-1">
                        <?= !empty($item['created_at']) ? date('d M Y, H:i', strtotime($item['created_at'])) : '-' ?>
                    </h6>
                    <span class="text-xs text-secondary-light"><?= $timeAgo ?></span>
                </div>
                <div class="w-48-px h-48-px bg-neutral-100 text-neutral-700 rounded-circle d-flex align-items-center justify-content-center text-2xl flex-shrink-0">
                    <iconify-icon icon="solar:calendar-date-bold"></iconify-icon>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Kolom Kiri: Isi Notifikasi & Payload Metadata -->
    <div class="col-lg-8">
        <!-- Kartu Pesan Notifikasi -->
        <div class="card radius-12 border mb-24">
            <div class="card-header bg-base py-16 px-24 border-bottom border-neutral-200 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:document-text-outline" class="text-xl text-primary-600"></iconify-icon>
                    <h6 class="fw-semibold mb-0 text-md">Isi Notifikasi Resmi</h6>
                </div>
                <span class="badge radius-6 text-xs px-10 py-4 <?= $catInfo['badge'] ?>"><?= esc($catInfo['label']) ?></span>
            </div>
            <div class="card-body p-24">
                <div class="mb-20">
                    <label class="form-label text-xs fw-semibold text-secondary-light text-uppercase mb-6">Judul Pemberitahuan</label>
                    <h5 class="fw-bold text-primary-light mb-0"><?= esc($item['title'] ?? '-') ?></h5>
                </div>

                <div class="mb-24">
                    <label class="form-label text-xs fw-semibold text-secondary-light text-uppercase mb-6">Badan Pesan / Body</label>
                    <div class="p-20 radius-8 bg-neutral-50 border border-neutral-200 text-neutral-800 text-sm leading-relaxed" style="white-space: pre-wrap; font-family: inherit;">
<?= esc($item['body'] ?? '-') ?>
                    </div>
                </div>

                <?php if ($actionUrl): ?>
                    <div class="p-16 radius-8 bg-primary-50 border border-primary-100 d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div class="d-flex align-items-center gap-2">
                            <iconify-icon icon="solar:link-circle-bold" class="text-2xl text-primary-600"></iconify-icon>
                            <div>
                                <span class="fw-semibold text-sm text-primary-light d-block">Tautan Aksi Terkait</span>
                                <span class="text-xs text-secondary-light"><?= esc($actionUrl) ?></span>
                            </div>
                        </div>
                        <a href="<?= esc($actionUrl) ?>" class="btn btn-sm btn-primary-600 radius-8 px-16 py-8 d-inline-flex align-items-center gap-1" target="_blank" rel="noopener noreferrer">
                            <span>Buka Halaman</span>
                            <iconify-icon icon="solar:square-arrow-right-up-linear"></iconify-icon>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Kartu Payload / Metadata JSON (layaknya Log Audit) -->
        <div class="card radius-12 border">
            <div class="card-header bg-base py-16 px-24 border-bottom border-neutral-200 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:code-file-outline" class="text-xl text-primary-600"></iconify-icon>
                    <h6 class="fw-semibold mb-0 text-md">Data Teknis & Metadata JSON</h6>
                </div>
                <button type="button" class="btn btn-xs btn-outline-neutral-300 radius-6 px-10 py-4 d-inline-flex align-items-center gap-1" id="btnCopyJson">
                    <iconify-icon icon="solar:copy-outline" class="text-sm"></iconify-icon>
                    <span id="btnCopyJsonText">Salin JSON</span>
                </button>
            </div>
            <div class="card-body p-24">
                <?php if ($parsedData !== null && !empty($parsedData)): ?>
                    <pre class="p-16 radius-8 bg-neutral-900 text-neutral-100 text-xs font-monospace mb-0 overflow-auto" id="notificationJsonBlock" style="max-height: 380px;"><?= esc(json_encode($parsedData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)) ?></pre>
                <?php elseif (!empty($rawPayload) && trim($rawPayload) !== 'null'): ?>
                    <pre class="p-16 radius-8 bg-neutral-900 text-neutral-100 text-xs font-monospace mb-0 overflow-auto" id="notificationJsonBlock" style="max-height: 380px;"><?= esc($rawPayload) ?></pre>
                <?php else: ?>
                    <div class="p-24 text-center text-secondary-light">
                        <iconify-icon icon="solar:code-circle-outline" class="text-3xl text-neutral-400 mb-8 d-block mx-auto"></iconify-icon>
                        <span class="text-sm">Tidak ada payload atau data metadata tambahan pada notifikasi ini.</span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Rincian Penerima & Riwayat Notifikasi Terkait -->
    <div class="col-lg-4">
        <!-- Kartu Profil Penerima -->
        <div class="card radius-12 border mb-24">
            <div class="card-header bg-base py-16 px-20 border-bottom border-neutral-200">
                <div class="d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:user-circle-bold" class="text-xl text-primary-600"></iconify-icon>
                    <h6 class="fw-semibold mb-0 text-md">Sasaran Penerima</h6>
                </div>
            </div>
            <div class="card-body p-20">
                <div class="d-flex align-items-center gap-3 mb-16 pb-16 border-bottom border-neutral-200">
                    <div class="w-48-px h-48-px rounded-circle bg-primary-100 text-primary-700 d-flex justify-content-center align-items-center fw-bold text-base flex-shrink-0">
                        <?= strtoupper(substr($item['user_name'] ?? ($item['user_username'] ?? 'U'), 0, 2)) ?>
                    </div>
                    <div class="overflow-hidden">
                        <h6 class="text-sm fw-bold mb-1 text-truncate">
                            <?= esc($item['user_name'] ?? ($item['user_username'] ?? 'Pengguna #' . $item['user_id'])) ?>
                        </h6>
                        <span class="badge text-xxs px-8 py-2 radius-4 <?= $profileLabel['badge'] ?> mb-1">
                            <?= $profileLabel['label'] ?>
                        </span>
                        <p class="text-xs text-secondary-light mb-0 text-truncate"><?= esc($item['user_email'] ?? '-') ?></p>
                    </div>
                </div>

                <div class="d-flex flex-column gap-12 text-sm">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-secondary-light text-xs">User ID:</span>
                        <span class="font-monospace fw-semibold text-xs text-neutral-800">#<?= esc($item['user_id'] ?? '-') ?></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-secondary-light text-xs">Username:</span>
                        <span class="fw-medium text-xs text-neutral-800">@<?= esc($item['user_username'] ?? '-') ?></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-secondary-light text-xs">Status Akun:</span>
                        <?php if (($item['user_status'] ?? '') === 'active'): ?>
                            <span class="badge bg-success-50 text-success-600 radius-4 text-xxs px-6 py-2">Aktif</span>
                        <?php else: ?>
                            <span class="badge bg-neutral-100 text-neutral-600 radius-4 text-xxs px-6 py-2"><?= esc(ucfirst($item['user_status'] ?? 'Aktif')) ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if (!empty($item['user_id'])): ?>
                    <div class="mt-16 pt-16 border-top border-neutral-200">
                        <a href="<?= site_url('dashboard/akses/pengguna/' . $item['user_id'] . '/ubah') ?>" class="btn btn-outline-primary-600 btn-sm w-100 radius-8 py-8 d-inline-flex justify-content-center align-items-center gap-2">
                            <iconify-icon icon="solar:user-id-outline" class="text-md"></iconify-icon>
                            <span>Lihat Manajemen Pengguna</span>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Kartu Riwayat Notifikasi Terkait Penerima Ini -->
        <div class="card radius-12 border">
            <div class="card-header bg-base py-16 px-20 border-bottom border-neutral-200 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:history-bold" class="text-xl text-primary-600"></iconify-icon>
                    <h6 class="fw-semibold mb-0 text-md">Notifikasi Lain Penerima</h6>
                </div>
            </div>
            <div class="card-body p-0">
                <?php if (empty($relatedNotifs)): ?>
                    <div class="p-20 text-center text-secondary-light">
                        <span class="text-xs">Tidak ada riwayat notifikasi lain untuk penerima ini.</span>
                    </div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($relatedNotifs as $rel): 
                            $relIsRead = !empty($rel['read_at']);
                        ?>
                            <a href="<?= site_url('dashboard/sistem/notifikasi/' . $rel['id']) ?>" class="list-group-item list-group-item-action p-16 border-bottom hover-bg-neutral-50 transition-2">
                                <div class="d-flex align-items-start justify-content-between gap-2 mb-1">
                                    <h6 class="text-xs fw-semibold text-primary-light mb-0 text-truncate" style="max-width: 190px;">
                                        <?= esc($rel['title'] ?? 'Pemberitahuan #' . $rel['id']) ?>
                                    </h6>
                                    <?php if ($relIsRead): ?>
                                        <span class="badge bg-success-50 text-success-600 text-3xs px-6 py-2 radius-4">Dibaca</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning-50 text-warning-700 text-3xs px-6 py-2 radius-4">Belum</span>
                                    <?php endif; ?>
                                </div>
                                <div class="d-flex align-items-center justify-content-between text-xxs text-secondary-light">
                                    <span><?= esc(ucfirst($rel['type'] ?? 'system')) ?></span>
                                    <span><?= !empty($rel['created_at']) ? date('d/m/Y H:i', strtotime($rel['created_at'])) : '-' ?></span>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const btnCopy = document.getElementById('btnCopyJson');
    const jsonBlock = document.getElementById('notificationJsonBlock');
    const btnText = document.getElementById('btnCopyJsonText');

    if (btnCopy && jsonBlock) {
        btnCopy.addEventListener('click', function () {
            const textToCopy = jsonBlock.textContent || '';
            navigator.clipboard.writeText(textToCopy).then(function () {
                const originalText = btnText.textContent;
                btnText.textContent = 'Tersalin!';
                btnCopy.classList.add('btn-success');
                btnCopy.classList.remove('btn-outline-neutral-300');
                setTimeout(function () {
                    btnText.textContent = originalText;
                    btnCopy.classList.remove('btn-success');
                    btnCopy.classList.add('btn-outline-neutral-300');
                }, 2000);
            }).catch(function (err) {
                console.error('Gagal menyalin:', err);
            });
        });
    }
});
</script>
<?= $this->endSection() ?>
