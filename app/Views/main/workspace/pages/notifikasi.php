<?= $this->extend('main/workspace/layouts/workspace') ?>
<?= $this->section('content') ?>

<div class="ws-page-header mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <span class="ws-eyebrow"><i class="fas fa-bell text-primary"></i> <?= esc($sectionContent['eyebrow'] ?? 'Pusat Informasi') ?></span>
            <h1 class="h3 fw-bold mb-1" style="color: #082f5c;"><?= esc($sectionContent['title'] ?? 'Notifikasi') ?></h1>
            <p class="text-muted mb-0"><?= esc($sectionContent['description'] ?? 'Pusat pembaruan pembelajaran, tugas, konsultasi, dan sistem.') ?></p>
        </div>
        <?php if (! empty($sectionContent['unread'])): ?>
            <form action="<?= route_to('workspace.notifications.read-all') ?>" method="post">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-outline-primary btn-sm rounded-pill px-3 shadow-2xs">
                    <i class="fas fa-check-double me-1"></i> Tandai Semua Dibaca
                </button>
            </form>
        <?php endif; ?>
    </div>
</div>

<!-- KPI Metric Cards -->
<div class="row g-2 g-md-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="card ws-stat-card border-0 shadow-sm rounded-4 p-3 h-100 bg-white">
            <span class="text-muted small text-uppercase fw-semibold">Belum Dibaca</span>
            <div class="display-6 fw-bold my-1 text-danger"><?= sprintf('%02d', $categoryCounts['unread'] ?? 0) ?></div>
            <span class="text-secondary small">membutuhkan perhatian Anda</span>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card ws-stat-card border-0 shadow-sm rounded-4 p-3 h-100 bg-white">
            <span class="text-muted small text-uppercase fw-semibold">Pembelajaran &amp; Kelas</span>
            <div class="display-6 fw-bold my-1" style="color: #082f5c;"><?= sprintf('%02d', $categoryCounts['learning'] ?? 0) ?></div>
            <span class="text-secondary small">materi, kuis, &amp; tugas</span>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card ws-stat-card border-0 shadow-sm rounded-4 p-3 h-100 bg-white">
            <span class="text-muted small text-uppercase fw-semibold">Konsultasi PBJ</span>
            <div class="display-6 fw-bold my-1 text-info"><?= sprintf('%02d', $categoryCounts['consultation'] ?? 0) ?></div>
            <span class="text-secondary small">tanggapan tiket helpdesk</span>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card ws-stat-card border-0 shadow-sm rounded-4 p-3 h-100 bg-white">
            <span class="text-muted small text-uppercase fw-semibold">Total Notifikasi</span>
            <div class="display-6 fw-bold my-1 text-secondary"><?= sprintf('%02d', $categoryCounts['all'] ?? 0) ?></div>
            <span class="text-secondary small">tercatat di riwayat sistem</span>
        </div>
    </div>
</div>

<!-- Native Browser Desktop & Push Notification Control Center Card -->
<div class="card border-0 rounded-4 shadow-sm bg-white overflow-hidden mb-4" id="wsPushNotificationCard">
    <div class="card-body p-3 p-md-4">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div class="d-flex align-items-start gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 shadow-2xs" style="width: 48px; height: 48px; background: rgba(12, 90, 166, 0.08);">
                    <i class="fas fa-desktop text-primary fs-5" id="wsPushIcon"></i>
                </div>
                <div>
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                        <h2 class="h6 fw-bold mb-0" style="color: #082f5c;">Notifikasi Desktop Browser &amp; Service Worker</h2>
                        <span id="wsPushStatusBadge" class="badge rounded-pill px-2.5 py-1 text-xs bg-secondary">
                            <i class="fas fa-spinner fa-spin me-1"></i> Memeriksa Status...
                        </span>
                    </div>
                    <p class="text-secondary small mb-0" id="wsPushStatusDesc">
                        Dapatkan notifikasi seketika saat ada pembaruan kelas, verifikasi berkas, tanggapan tiket helpdesk, atau konfirmasi event langsung di desktop tanpa harus terus membuka tab aplikasi.
                    </p>
                    <div id="wsPushInsecureNotice" class="text-muted text-xs mt-1 d-none" style="font-size: 11px;">
                        <i class="fas fa-info-circle text-info me-1"></i> <span id="wsPushInsecureText">Browser mendeteksi domain lokal HTTP. Mode <strong>In-App Floating Toast &amp; Audio Chime</strong> aktif seketika. Notifikasi native desktop browser akan aktif optimal pada koneksi HTTPS atau <code>http://localhost</code>.</span>
                    </div>
                </div>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-2 flex-shrink-0 align-self-start align-self-md-center">
                <button type="button" id="wsBtnActivatePush" class="btn btn-primary btn-sm rounded-pill px-3 shadow-2xs d-inline-flex align-items-center gap-1.5" onclick="window.wsActivatePushNotification()">
                    <i class="fas fa-bell"></i> <span id="wsBtnActivatePushText">Aktifkan Notifikasi Desktop</span>
                </button>
                <button type="button" id="wsBtnTestPush" class="btn btn-outline-primary btn-sm rounded-pill px-3 shadow-2xs d-inline-flex align-items-center gap-1.5" title="Kirim notifikasi uji coba untuk mengetes suara chime dan banner desktop" onclick="window.wsTestPushNotification()">
                    <i class="fas fa-vial"></i> <span>Uji Sekarang</span>
                </button>
                <button type="button" id="wsBtnTestBgPush" class="btn btn-outline-success btn-sm rounded-pill px-3 shadow-2xs d-inline-flex align-items-center gap-1.5" title="Kirim notifikasi dengan jeda 5 detik agar Anda dapat menguji saat aplikasi diminimalkan / di latar belakang / layar HP terkunci" onclick="window.wsTestBackgroundPushNotification()">
                    <i class="fas fa-stopwatch"></i> <span>Uji Latar Belakang (5s)</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Filter Tabs Navigation -->
<div class="card border-0 rounded-4 shadow-sm bg-white overflow-hidden mb-4">
    <div class="card-body p-2 p-md-3">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
            <ul class="nav nav-pills gap-1 flex-nowrap overflow-x-auto pb-1 pb-md-0" style="scrollbar-width: thin;">
                <?php
                $isAllActive = empty($currentCategory) && empty($currentStatus);
                $isUnreadActive = ($currentStatus === 'unread');
                $isLearningActive = ($currentCategory === 'learning');
                $isConsultActive = ($currentCategory === 'consultation');
                $isCertActive = ($currentCategory === 'certificate');
                $isSystemActive = ($currentCategory === 'system');
                ?>
                <li class="nav-item">
                    <a href="<?= route_to('workspace.notifications') ?>" class="nav-link rounded-pill px-3 py-1.5 fs-sm d-flex align-items-center gap-2 <?= $isAllActive ? 'active bg-primary text-white' : 'text-secondary hover-primary' ?>">
                        <span>Semua</span>
                        <span class="badge rounded-pill <?= $isAllActive ? 'bg-white text-primary' : 'bg-light text-secondary' ?>"><?= $categoryCounts['all'] ?? 0 ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= route_to('workspace.notifications') ?>?status=unread" class="nav-link rounded-pill px-3 py-1.5 fs-sm d-flex align-items-center gap-2 <?= $isUnreadActive ? 'active bg-danger text-white' : 'text-secondary hover-primary' ?>">
                        <span>Belum Dibaca</span>
                        <?php if (!empty($categoryCounts['unread'])): ?>
                            <span class="badge rounded-pill <?= $isUnreadActive ? 'bg-white text-danger' : 'bg-danger-subtle text-danger' ?>"><?= $categoryCounts['unread'] ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= route_to('workspace.notifications') ?>?category=learning" class="nav-link rounded-pill px-3 py-1.5 fs-sm d-flex align-items-center gap-2 <?= $isLearningActive ? 'active bg-primary text-white' : 'text-secondary hover-primary' ?>">
                        <span><i class="fas fa-graduation-cap"></i> Pembelajaran</span>
                        <span class="badge rounded-pill <?= $isLearningActive ? 'bg-white text-primary' : 'bg-light text-secondary' ?>"><?= $categoryCounts['learning'] ?? 0 ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= route_to('workspace.notifications') ?>?category=consultation" class="nav-link rounded-pill px-3 py-1.5 fs-sm d-flex align-items-center gap-2 <?= $isConsultActive ? 'active bg-info text-white' : 'text-secondary hover-primary' ?>">
                        <span><i class="fas fa-comments"></i> Konsultasi</span>
                        <span class="badge rounded-pill <?= $isConsultActive ? 'bg-white text-info' : 'bg-light text-secondary' ?>"><?= $categoryCounts['consultation'] ?? 0 ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= route_to('workspace.notifications') ?>?category=certificate" class="nav-link rounded-pill px-3 py-1.5 fs-sm d-flex align-items-center gap-2 <?= $isCertActive ? 'active bg-success text-white' : 'text-secondary hover-primary' ?>">
                        <span><i class="fas fa-award"></i> Sertifikat</span>
                        <span class="badge rounded-pill <?= $isCertActive ? 'bg-white text-success' : 'bg-light text-secondary' ?>"><?= $categoryCounts['certificate'] ?? 0 ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= route_to('workspace.notifications') ?>?category=system" class="nav-link rounded-pill px-3 py-1.5 fs-sm d-flex align-items-center gap-2 <?= $isSystemActive ? 'active bg-secondary text-white' : 'text-secondary hover-primary' ?>">
                        <span><i class="fas fa-bullhorn"></i> Sistem</span>
                        <span class="badge rounded-pill <?= $isSystemActive ? 'bg-white text-secondary' : 'bg-light text-secondary' ?>"><?= $categoryCounts['system'] ?? 0 ?></span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>

<!-- Notification Feed List -->
<div class="card border-0 rounded-4 shadow-sm bg-white overflow-hidden">
    <div class="card-header bg-white border-bottom p-3 px-md-4 d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
            <h2 class="h5 fw-bold mb-0" style="color: #082f5c;">Riwayat Pemberitahuan</h2>
            <?php if (! empty($sectionContent['unread'])): ?>
                <span class="badge bg-danger rounded-pill px-2.5 py-1 text-xs">
                    <?= esc($sectionContent['unread']) ?> baru
                </span>
            <?php endif; ?>
        </div>
        <span class="text-muted small">Menampilkan <?= count($sectionContent['notifications'] ?? []) ?> notifikasi</span>
    </div>

    <div class="card-body p-0">
        <?php if (empty($sectionContent['notifications'])): ?>
            <div class="text-center py-5 px-3 text-muted">
                <div class="w-64-px h-64-px rounded-circle bg-light d-inline-flex justify-content-center align-items-center mb-3">
                    <i class="fas fa-bell-slash text-secondary fs-3"></i>
                </div>
                <h6 class="fw-semibold text-dark mb-1">Tidak Ada Notifikasi</h6>
                <p class="text-secondary small mb-3">Tidak ditemukan pemberitahuan pada filter atau status yang dipilih saat ini.</p>
                <?php if (!empty($currentCategory) || !empty($currentStatus)): ?>
                    <a href="<?= route_to('workspace.notifications') ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                        <i class="fas fa-undo me-1"></i> Tampilkan Semua Notifikasi
                    </a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="list-group list-group-flush">
                <?php foreach ($sectionContent['notifications'] as $item): ?>
                    <?php
                    $isUnread = empty($item['read_at']);
                    $catMeta = \App\Services\NotificationService::formatCategory($item['type'] ?? 'system');
                    $timeAgo = \App\Services\NotificationService::timeAgo($item['created_at'] ?? null);
                    $itemData = !empty($item['data']) ? (is_array($item['data']) ? $item['data'] : json_decode($item['data'], true)) : [];
                    $targetUrl = !empty($itemData['url']) ? $itemData['url'] : null;
                    ?>
                    <div class="list-group-item p-3 p-md-4 border-bottom <?= $isUnread ? 'bg-primary-subtle bg-opacity-25' : '' ?>" style="<?= $isUnread ? 'border-left: 4px solid #0c5aa6 !important;' : '' ?>">
                        <div class="d-flex align-items-start gap-3">
                            <!-- Category Icon Circle -->
                            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 mt-1 shadow-2xs" style="width: 42px; height: 42px; background: rgba(12, 90, 166, 0.08);">
                                <i class="<?= esc($catMeta['fa']) ?>" style="font-size: 17px;"></i>
                            </div>

                            <!-- Content Area -->
                            <div class="flex-grow-1 overflow-hidden">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-1">
                                    <div class="d-flex flex-wrap align-items-center gap-2">
                                        <span class="badge rounded-pill px-2.5 py-1 text-xs <?= esc($catMeta['badge']) ?>">
                                            <?= esc($catMeta['label']) ?>
                                        </span>
                                        <?php if ($isUnread): ?>
                                            <span class="badge bg-danger rounded-pill px-2 py-0.5" style="font-size: 10px;">Baru</span>
                                        <?php endif; ?>
                                        <h6 class="mb-0 fw-semibold text-dark"><?= esc($item['title']) ?></h6>
                                    </div>
                                    <span class="text-muted small text-nowrap" title="<?= esc($item['created_at']) ?>">
                                        <i class="far fa-clock me-1"></i><?= esc($timeAgo) ?>
                                    </span>
                                </div>

                                <p class="mb-2 text-secondary small text-break"><?= esc($item['body'] ?? '') ?></p>

                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 pt-1">
                                    <div>
                                        <?php if ($targetUrl): ?>
                                            <a href="<?= esc($targetUrl) ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 text-xs fw-semibold">
                                                <?php if (str_contains($item['type'], 'ticket') || str_contains($item['type'], 'contact')): ?>
                                                    <i class="fas fa-comments me-1"></i> Buka Tiket Konsultasi
                                                <?php elseif (str_contains($item['type'], 'certificate')): ?>
                                                    <i class="fas fa-award me-1"></i> Akses Sertifikat
                                                <?php elseif (str_contains($item['type'], 'account') || str_contains($item['type'], 'profile')): ?>
                                                    <i class="fas fa-id-card me-1"></i> Verifikasi Berkas
                                                <?php elseif (str_contains($item['type'], 'class') || str_contains($item['type'], 'lesson')): ?>
                                                    <i class="fas fa-graduation-cap me-1"></i> Akses Kelas
                                                <?php elseif (str_contains($item['type'], 'event')): ?>
                                                    <i class="fas fa-calendar-alt me-1"></i> Rincian Event
                                                <?php else: ?>
                                                    <i class="fas fa-external-link-alt me-1"></i> Lihat Rincian
                                                <?php endif; ?>
                                            </a>
                                        <?php endif; ?>
                                    </div>

                                    <?php if ($isUnread): ?>
                                        <form action="<?= route_to('workspace.notifications.read', $item['id']) ?>" method="post" class="m-0">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-link text-decoration-none text-muted py-0 px-1 text-xs" title="Tandai notifikasi telah dibaca">
                                                <i class="fas fa-check me-1"></i> Tandai Dibaca
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('pageScripts') ?>
<script>
window.updateWorkspacePushCard = function() {
    const badge = document.getElementById('wsPushStatusBadge');
    const desc = document.getElementById('wsPushStatusDesc');
    const btnAct = document.getElementById('wsBtnActivatePush');
    const btnActText = document.getElementById('wsBtnActivatePushText');
    const notice = document.getElementById('wsPushInsecureNotice');
    const icon = document.getElementById('wsPushIcon');

    if (!badge) return;

    const isSecure = window.isSecureContext || window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1';
    if (!isSecure && notice) {
        notice.classList.remove('d-none');
    }

    if (!('Notification' in window)) {
        badge.className = 'badge rounded-pill px-2.5 py-1 text-xs bg-info-subtle text-info';
        badge.innerHTML = '<i class="fas fa-volume-up me-1"></i> Mode In-App Toast &amp; Suara Aktif';
        if (desc) desc.textContent = 'Browser ini tidak menyediakan Web Notification API native. Fitur notifikasi mengambang (In-App Toast) dan nada suara chime aktif penuh.';
        if (btnAct) btnAct.style.display = 'none';
        return;
    }

    if (Notification.permission === 'granted') {
        badge.className = 'badge rounded-pill px-2.5 py-1 text-xs bg-success-subtle text-success';
        badge.innerHTML = '<i class="fas fa-check-circle me-1"></i> 🟢 Aktif (Desktop Banner &amp; Toast)';
        if (desc) desc.textContent = 'Notifikasi browser dan desktop Windows/Chrome/Edge aktif. Anda akan menerima notifikasi mengambang seketika dan bunyi chime harmonik saat ada aktivitas baru.';
        if (btnAct) {
            btnAct.className = 'btn btn-outline-success btn-sm rounded-pill px-3 shadow-2xs d-inline-flex align-items-center gap-1.5';
            btnAct.disabled = true;
            if (btnActText) btnActText.textContent = 'Sudah Diaktifkan';
        }
        if (icon) {
            icon.className = 'fas fa-check-circle text-success fs-5';
        }
    } else if (Notification.permission === 'denied') {
        badge.className = 'badge rounded-pill px-2.5 py-1 text-xs bg-danger-subtle text-danger';
        badge.innerHTML = '<i class="fas fa-ban me-1"></i> 🔴 Izin Diblokir Browser';
        if (desc) desc.textContent = 'Izin notifikasi diblokir di setelan browser Anda. Klik ikon gembok/setelan situs di sebelah URL browser untuk mengizinkan notifikasi, lalu muat ulang halaman.';
        if (btnAct) {
            btnAct.className = 'btn btn-outline-secondary btn-sm rounded-pill px-3 shadow-2xs d-inline-flex align-items-center gap-1.5';
            btnAct.disabled = false;
            if (btnActText) btnActText.textContent = 'Cara Buka Blokir';
            btnAct.onclick = function() {
                alert('Untuk mengizinkan notifikasi:\n1. Klik ikon setelan / gembok di samping URL browser.\n2. Ubah setelan "Notifikasi" menjadi "Izinkan" / "Allow".\n3. Muat ulang halaman ini.');
            };
        }
    } else {
        // default
        badge.className = 'badge rounded-pill px-2.5 py-1 text-xs bg-warning-subtle text-warning-emphasis';
        badge.innerHTML = '<i class="fas fa-bell me-1"></i> 🟡 Belum Diaktifkan';
        if (desc) desc.textContent = 'Notifikasi desktop belum diizinkan. Klik tombol "Aktifkan Notifikasi" agar browser dapat memunculkan popup notifikasi saat ada informasi baru.';
        if (btnAct) {
            btnAct.className = 'btn btn-primary btn-sm rounded-pill px-3 shadow-2xs d-inline-flex align-items-center gap-1.5';
            btnAct.disabled = false;
            if (btnActText) btnActText.textContent = 'Aktifkan Notifikasi Desktop';
            btnAct.onclick = window.wsActivatePushNotification;
        }
    }
};

window.wsActivatePushNotification = function() {
    if (typeof window.bpRequestPushPermission === 'function') {
        window.bpRequestPushPermission();
    } else if ('Notification' in window) {
        Notification.requestPermission().then(function(perm) {
            window.updateWorkspacePushCard();
            if (perm === 'granted') {
                window.wsTestPushNotification('Notifikasi Desktop Berhasil Diaktifkan!');
            }
        });
    }
};

window.wsTestPushNotification = function(customTitle) {
    if (typeof window.bpTestNotification === 'function') {
        window.bpTestNotification({
            id: Date.now(),
            title: customTitle || 'Uji Coba Notifikasi BegawiPengadaan',
            body: 'Selamat! Audio chime harmonik, floating toast, dan notifikasi native browser berhasil berjalan.',
            fa: 'fas fa-vial text-primary',
            badge: 'bg-primary text-white',
            url: window.location.href
        });
    } else {
        alert('Notifikasi uji coba terkirim.');
    }
};

window.wsTestBackgroundPushNotification = function() {
    const btn = document.getElementById('wsBtnTestBgPush');
    let secondsLeft = 5;
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = `<i class="fas fa-spinner fa-spin"></i> Minimalkan browser (${secondsLeft}s)...`;
    }

    if (typeof window.bpShowToast === 'function') {
        window.bpShowToast({
            title: 'Hitungan Mundur 5 Detik Dimulai!',
            body: 'Segera minimalkan browser, pindah ke aplikasi lain (seperti WhatsApp), atau kunci layar Android sekarang untuk menguji notifikasi di latar belakang.',
            badge: 'bg-warning text-dark',
            fa: 'fas fa-stopwatch text-warning'
        });
    }

    const countdown = setInterval(function() {
        secondsLeft--;
        if (secondsLeft > 0) {
            if (btn) btn.innerHTML = `<i class="fas fa-spinner fa-spin"></i> Minimalkan browser (${secondsLeft}s)...`;
        } else {
            clearInterval(countdown);
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = `<i class="fas fa-stopwatch"></i> <span>Uji Latar Belakang (5s)</span>`;
            }
            window.wsTestPushNotification('Uji Notifikasi Latar Belakang Berhasil! 🚀');
        }
    }, 1000);
};

document.addEventListener('DOMContentLoaded', function() {
    window.updateWorkspacePushCard();
});
</script>
<?= $this->endSection() ?>
