<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>

<?php
$isRegistered = ! empty($senderUser);
$threads = $notes['threads'] ?? [];
$logs    = $notes['logs'] ?? [];
$ticketNo = $message['ticket_number'] ?: ('#' . $message['id']);

// Auto trigger popup/redirect for WA or Email if session contains it
$waUrl = session()->getFlashdata('wa_url');
$mailtoUrl = session()->getFlashdata('mailto_url');
?>

<?php if ($waUrl): ?>
    <script>
        window.open('<?= esc($waUrl, 'js') ?>', '_blank');
    </script>
<?php endif; ?>

<?php if ($mailtoUrl): ?>
    <script>
        window.location.href = '<?= esc($mailtoUrl, 'js') ?>';
    </script>
<?php endif; ?>

<!-- Header Breadcrumb & Actions -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
            <h6 class="fw-bold mb-0 text-primary-light"><?= esc($message['subject']) ?></h6>
            <span class="badge bg-neutral-100 text-neutral-900 border border font-monospace text-xs px-8 py-4">
                <?= esc($ticketNo) ?>
            </span>
            <?php
            $badgeClass = match ($message['status']) {
                'unread'        => 'bg-danger-subtle text-danger-main border border-danger-subtle',
                'waiting_admin' => 'bg-warning-subtle text-warning-main border border-warning-subtle',
                'in_progress'   => 'bg-info-subtle text-info border border-info-subtle',
                'waiting_user'  => 'bg-info-subtle text-info border border-info-subtle',
                'read'          => 'bg-neutral-200 text-neutral-700',
                'replied'       => 'bg-success-subtle text-success-main border border-success-subtle',
                'closed'        => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
                'archived'      => 'bg-neutral-200 text-neutral-700',
                default         => 'bg-neutral-100 text-neutral-600',
            };
            $statusLabel = match ($message['status']) {
                'unread'        => 'Belum Dibaca',
                'waiting_admin' => 'Tanggapan Pemohon',
                'in_progress'   => 'Sedang Diproses',
                'waiting_user'  => 'Menunggu Pemohon',
                'read'          => 'Sudah Dibaca',
                'replied'       => 'Sudah Dibalas',
                'closed'        => 'Selesai (Closed)',
                'archived'      => 'Diarsipkan',
                default         => ucfirst($message['status']),
            };
            ?>
            <span class="badge <?= $badgeClass ?> px-10 py-6 radius-4 text-xs fw-bold">
                <?= $statusLabel ?>
            </span>
        </div>
        <p class="text-secondary-light mb-0 text-xs">
            Dibuat: <?= date('d F Y, H:i', strtotime($message['created_at'])) ?> WIB &middot;
            Kategori: <strong><?= esc($message['service_category'] ?: 'Umum') ?></strong> &middot;
            Total Pesan: <strong><?= count($threads) + 1 ?> percakapan</strong>
        </p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="<?= site_url('dashboard/komunikasi/pesan-masuk') ?>" class="btn btn-outline-neutral-300 radius-8 px-16 py-8 text-xs d-inline-flex align-items-center gap-1">
            <iconify-icon icon="solar:arrow-left-linear"></iconify-icon>
            <span>Kembali ke Inbox</span>
        </a>
    </div>
</div>

<div class="row gy-24">
    <!-- Left Column: Conversation Thread & Reply Form (8 Cols) -->
    <div class="col-xl-8 col-lg-7">
        <!-- Conversation Thread Card -->
        <div class="card mb-24 shadow-sm border-0 radius-16">
            <div class="card-header border-bottom py-16 px-24 d-flex align-items-center justify-content-between flex-wrap gap-2 bg-neutral-50 radius-top-16">
                <div class="d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:chat-round-line-bold" class="text-primary-600 fs-5"></iconify-icon>
                    <h6 class="text-sm fw-bold mb-0 text-primary-light">Thread Percakapan Dua Arah</h6>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <?php if ($isRegistered): ?>
                        <span class="badge bg-success-subtle text-success border border-success-subtle text-2xs px-8 py-4">
                            <iconify-icon icon="solar:verified-check-bold"></iconify-icon> Pengguna Terdaftar (Saling Membalas Aktif)
                        </span>
                    <?php else: ?>
                        <span class="badge bg-neutral-200 text-neutral-700 text-2xs px-8 py-4" title="Pengirim tamu via form publik">
                            <iconify-icon icon="solar:user-linear"></iconify-icon> Pengirim Tamu (Publik)
                        </span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card-body p-24" style="background-color: #fafbfc;">
                <!-- 1. Initial Message (Bubble dari Pengirim Pertama Kali) -->
                <div class="p-20 bg-white radius-12 border shadow-sm mb-20 position-relative">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-12 pb-12 border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <span class="w-36-px h-36-px radius-circle bg-primary-100 text-primary-700 fw-bold d-flex align-items-center justify-content-center text-sm">
                                <?= strtoupper(mb_substr($message['sender_name'], 0, 1)) ?>
                            </span>
                            <div>
                                <strong class="text-sm d-block text-primary-light"><?= esc($message['sender_name']) ?></strong>
                                <small class="text-muted text-2xs">Pemohon / Pengirim Pertama</small>
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-light text-secondary border text-2xs mb-1">Pesan Awal</span>
                            <small class="text-muted text-2xs d-block"><?= date('d M Y, H:i:s', strtotime($message['created_at'])) ?> WIB</small>
                        </div>
                    </div>

                    <div class="text-slate-800 leading-relaxed" style="font-size: 14.5px; white-space: pre-wrap;"><?= esc($message['message']) ?></div>
                </div>

                <!-- 2. Subsequent Conversation Replies -->
                <?php if (! empty($threads)): ?>
                    <div class="d-flex flex-column gap-3 mb-20">
                        <?php foreach ($threads as $index => $th): ?>
                            <?php
                            $isAdminSender = ($th['sender_type'] ?? '') === 'administrator';
                            $channel       = $th['channel'] ?? 'portal';
                            $isInternal    = $channel === 'internal';

                            if ($isInternal) {
                                $boxClass = 'bg-warning-subtle border border-warning-subtle';
                                $badgeTag = '<span class="badge bg-warning-50 text-warning-700 border border-warning-200 text-2xs"><iconify-icon icon="solar:lock-bold"></iconify-icon> Catatan Internal Petugas</span>';
                            } elseif ($isAdminSender) {
                                $boxClass = 'bg-primary-50 border border-primary-200 ms-auto';
                                $channelBadge = match ($channel) {
                                    'email'    => '<span class="badge bg-primary text-white text-2xs"><iconify-icon icon="solar:letter-bold"></iconify-icon> Balasan Email</span>',
                                    'whatsapp' => '<span class="badge bg-success text-white text-2xs"><iconify-icon icon="logos:whatsapp-icon"></iconify-icon> Balasan WhatsApp</span>',
                                    default    => '<span class="badge bg-primary text-white text-2xs"><iconify-icon icon="solar:chat-round-bold"></iconify-icon> Portal Helpdesk</span>',
                                };
                                $badgeTag = $channelBadge;
                            } else {
                                $boxClass = 'bg-white border shadow-sm';
                                $badgeTag = '<span class="badge bg-info-subtle text-info border border-info-subtle text-2xs"><iconify-icon icon="solar:user-bold"></iconify-icon> Tanggapan Pemohon</span>';
                            }
                            ?>
                            <div class="p-20 radius-12 <?= $boxClass ?>" style="max-width: <?= $isInternal ? '100%' : '90%' ?>;">
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-10 pb-8 border-bottom">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="w-32-px h-32-px radius-circle <?= $isAdminSender ? 'bg-primary-600 text-white' : 'bg-neutral-200 text-neutral-900' ?> fw-bold d-flex align-items-center justify-content-center text-xs">
                                            <?= strtoupper(mb_substr($th['sender_name'] ?? 'U', 0, 1)) ?>
                                        </span>
                                        <div>
                                            <strong class="text-sm d-block text-primary-light"><?= esc($th['sender_name'] ?? 'Petugas') ?></strong>
                                            <small class="text-muted text-2xs"><?= $isAdminSender ? 'Tim Helpdesk PBJ' : 'Pemohon' ?></small>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <?= $badgeTag ?>
                                        <small class="text-muted text-2xs d-block mt-1"><?= date('d M Y, H:i', strtotime($th['created_at'])) ?> WIB</small>
                                    </div>
                                </div>
                                <div class="text-slate-800 leading-relaxed" style="font-size: 14px; white-space: pre-wrap;"><?= esc($th['message']) ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if (empty($threads)): ?>
                    <div class="text-center py-20 text-muted small bg-white radius-8 border mb-20">
                        <iconify-icon icon="solar:chat-square-call-linear" class="fs-3 text-secondary-light mb-1 d-block"></iconify-icon>
                        Belum ada pesan balasan. Gunakan formulir di bawah untuk mengirim tanggapan atau solusi kepada pemohon.
                    </div>
                <?php endif; ?>

                <!-- 3. Form Kirim Balasan / Response Dispatcher -->
                <div class="card border radius-12 bg-white mt-16">
                    <div class="card-header bg-base border-bottom py-12 px-20">
                        <ul class="nav nav-pills gap-2 flex-wrap" id="replyTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active radius-8 text-xs py-6 px-14 fw-bold" id="portal-tab" data-bs-toggle="pill" data-bs-target="#tab-portal" type="button" role="tab">
                                    <iconify-icon icon="solar:chat-round-line-bold" class="me-1"></iconify-icon> Balas di Portal Web
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link radius-8 text-xs py-6 px-14 fw-bold text-secondary-light" id="email-tab" data-bs-toggle="pill" data-bs-target="#tab-email" type="button" role="tab">
                                    <iconify-icon icon="solar:letter-bold" class="me-1"></iconify-icon> Balas via Email
                                </button>
                            </li>
                            <?php if (! empty($message['sender_phone'])): ?>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link radius-8 text-xs py-6 px-14 fw-bold text-secondary-light" id="wa-tab" data-bs-toggle="pill" data-bs-target="#tab-wa" type="button" role="tab">
                                        <iconify-icon icon="logos:whatsapp-icon" class="me-1"></iconify-icon> Balas via WhatsApp
                                    </button>
                                </li>
                            <?php endif; ?>
                            <li class="nav-item ms-auto" role="presentation">
                                <button class="nav-link radius-8 text-xs py-6 px-14 fw-bold text-warning-main bg-warning-subtle" id="internal-tab" data-bs-toggle="pill" data-bs-target="#tab-internal" type="button" role="tab">
                                    <iconify-icon icon="solar:lock-bold" class="me-1"></iconify-icon> Catatan Internal
                                </button>
                            </li>
                        </ul>
                    </div>

                    <div class="card-body p-20">
                        <div class="tab-content" id="replyTabsContent">
                            <!-- Tab 1: Portal Reply (Two-way Chat) -->
                            <div class="tab-pane fade show active" id="tab-portal" role="tabpanel">
                                <?php if ($isRegistered): ?>
                                    <div class="alert alert-info py-8 px-14 radius-8 text-xs mb-14 d-flex align-items-center gap-2">
                                        <iconify-icon icon="solar:info-circle-bold" class="fs-5 flex-shrink-0 text-info"></iconify-icon>
                                        <span>Pemohon terdaftar dengan akun BegawiPengadaan. Balasan ini akan langsung masuk ke Ruang Kerja (Workspace) pemohon secara interaktif.</span>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-neutral-100 py-8 px-14 radius-8 text-xs mb-14 text-muted">
                                        Pemohon adalah tamu publik. Balasan ini akan tersimpan pada tiket dan dapat dilihat saat pemohon memasukkan nomor tiket pada formulir lacak publik. Disarankan juga meneruskan via Email/WA.
                                    </div>
                                <?php endif; ?>

                                <form method="post" action="<?= site_url('dashboard/komunikasi/pesan-masuk/reply/' . $message['id']) ?>">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="channel" value="portal">

                                    <div class="mb-14">
                                        <label class="form-label text-xs fw-semibold">Pesan Balasan / Solusi Helpdesk *</label>
                                        <textarea name="reply_message" class="form-control radius-8 text-sm" rows="4" placeholder="Tuliskan jawaban, instruksi, atau klarifikasi untuk pemohon..." required></textarea>
                                    </div>

                                    <div class="row g-2 align-items-center">
                                        <div class="col-sm-6">
                                            <label class="form-label text-2xs text-muted mb-1">Status Tiket Setelah Balasan</label>
                                            <select name="new_status" class="form-select form-select-sm radius-8 text-xs">
                                                <option value="replied" selected>Tandai Sudah Dibalas (Replied)</option>
                                                <option value="in_progress">Tetap Sedang Ditelaah (In Progress)</option>
                                                <option value="waiting_user">Menunggu Konfirmasi Pemohon (Waiting User)</option>
                                                <option value="closed">Selesaikan &amp; Tutup Tiket (Closed)</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-6 text-end pt-3">
                                            <button type="submit" class="btn btn-primary-600 btn-sm radius-8 px-20 py-8 fw-semibold">
                                                <iconify-icon icon="solar:plain-bold" class="me-1"></iconify-icon> Kirim Balasan ke Portal
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <!-- Tab 2: Email Reply -->
                            <div class="tab-pane fade" id="tab-email" role="tabpanel">
                                <form method="post" action="<?= site_url('dashboard/komunikasi/pesan-masuk/reply/' . $message['id']) ?>">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="channel" value="email">

                                    <div class="mb-12">
                                        <label class="form-label text-xs fw-semibold">Tujuan Email</label>
                                        <input type="text" class="form-control form-control-sm radius-8 text-xs bg-light" value="<?= esc($message['sender_email']) ?>" readonly>
                                    </div>

                                    <div class="mb-14">
                                        <label class="form-label text-xs fw-semibold">Isi Tanggapan Email *</label>
                                        <textarea name="reply_message" class="form-control radius-8 text-sm" rows="4" placeholder="Ketik draf jawaban yang akan dikirim via email..." required></textarea>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                        <small class="text-muted text-2xs">Sistem akan mencatat balasan ini ke thread tiket dan otomatis membuka email client Anda.</small>
                                        <button type="submit" class="btn btn-primary-600 btn-sm radius-8 px-20 py-8 fw-semibold">
                                            <iconify-icon icon="solar:letter-bold" class="me-1"></iconify-icon> Simpan &amp; Buka Email
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <!-- Tab 3: WhatsApp Reply -->
                            <?php if (! empty($message['sender_phone'])): ?>
                                <div class="tab-pane fade" id="tab-wa" role="tabpanel">
                                    <form method="post" action="<?= site_url('dashboard/komunikasi/pesan-masuk/reply/' . $message['id']) ?>">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="channel" value="whatsapp">

                                        <div class="mb-12">
                                            <label class="form-label text-xs fw-semibold">Nomor WhatsApp Tujuan</label>
                                            <input type="text" class="form-control form-control-sm radius-8 text-xs bg-light" value="<?= esc($message['sender_phone']) ?>" readonly>
                                        </div>

                                        <div class="mb-14">
                                            <label class="form-label text-xs fw-semibold">Pesan WhatsApp *</label>
                                            <textarea name="reply_message" class="form-control radius-8 text-sm" rows="4" placeholder="Tulis ringkasan jawaban untuk diteruskan via chat WhatsApp..." required></textarea>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                            <small class="text-muted text-2xs">Pesan akan dicatat ke riwayat tiket dan membuka WhatsApp Web/Aplikasi.</small>
                                            <button type="submit" class="btn btn-success btn-sm radius-8 px-20 py-8 fw-semibold">
                                                <iconify-icon icon="logos:whatsapp-icon" class="me-1"></iconify-icon> Simpan &amp; Buka WhatsApp
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            <?php endif; ?>

                            <!-- Tab 4: Internal Note -->
                            <div class="tab-pane fade" id="tab-internal" role="tabpanel">
                                <form method="post" action="<?= site_url('dashboard/komunikasi/pesan-masuk/reply/' . $message['id']) ?>">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="channel" value="internal">

                                    <div class="alert alert-warning py-8 px-14 radius-8 text-xs mb-14">
                                        <iconify-icon icon="solar:shield-warning-bold" class="me-1 text-warning"></iconify-icon>
                                        <strong>Catatan Internal:</strong> Pesan ini hanya bisa dibaca oleh tim Helpdesk / Administrator dan tidak akan ditampilkan kepada pemohon.
                                    </div>

                                    <div class="mb-14">
                                        <label class="form-label text-xs fw-semibold">Uraian Catatan Internal Helpdesk *</label>
                                        <textarea name="reply_message" class="form-control radius-8 text-sm" rows="3" placeholder="Contoh: Sudah dikoordinasikan dengan Pokja Pengadaan Barang, dokumen pendukung sedang diverifikasi..." required></textarea>
                                    </div>

                                    <div class="text-end">
                                        <button type="submit" class="btn btn-warning-600 btn-sm radius-8 px-20 py-8 fw-semibold text-neutral-900">
                                            <iconify-icon icon="solar:lock-bold" class="me-1"></iconify-icon> Simpan Catatan Internal
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Sender Profile, Ticket Management & Activity Log (4 Cols) -->
    <div class="col-xl-4 col-lg-5">
        <!-- Pengirim Info Card -->
        <div class="card mb-24 shadow-sm border-0 radius-16">
            <div class="card-header border-bottom py-16 px-24 bg-base radius-top-16 d-flex align-items-center justify-content-between">
                <h6 class="text-sm fw-bold mb-0 text-primary-light">Data Pemohon</h6>
                <?php if ($isRegistered): ?>
                    <span class="badge bg-success-subtle text-success border border-success-subtle text-2xs">User Terverifikasi</span>
                <?php else: ?>
                    <span class="badge bg-neutral-200 text-neutral-700 text-2xs">Tamu Publik</span>
                <?php endif; ?>
            </div>
            <div class="card-body p-24">
                <div class="mb-14 pb-12 border-bottom">
                    <label class="text-2xs text-muted d-block text-uppercase fw-semibold mb-1">Nama Lengkap</label>
                    <strong class="text-sm text-primary-light d-block"><?= esc($message['sender_name']) ?></strong>
                    <?php if ($isRegistered && ! empty($senderUser['profile_type'])): ?>
                        <span class="badge bg-primary-subtle text-primary text-2xs mt-1">Tipe: <?= strtoupper(esc($senderUser['profile_type'])) ?></span>
                    <?php endif; ?>
                </div>

                <div class="mb-14 pb-12 border-bottom">
                    <label class="text-2xs text-muted d-block text-uppercase fw-semibold mb-1">Alamat Email</label>
                    <div class="d-flex align-items-center justify-content-between">
                        <a href="mailto:<?= esc($message['sender_email']) ?>" class="text-sm text-primary fw-medium text-truncate" style="max-width: 200px;"><?= esc($message['sender_email']) ?></a>
                        <a href="mailto:<?= esc($message['sender_email']) ?>" class="btn btn-outline-primary btn-sm radius-8 px-8 py-2 text-2xs" title="Kirim Email">
                            <iconify-icon icon="solar:letter-bold"></iconify-icon>
                        </a>
                    </div>
                </div>

                <div class="mb-14 pb-12 border-bottom">
                    <label class="text-2xs text-muted d-block text-uppercase fw-semibold mb-1">No. WhatsApp / Telepon</label>
                    <?php if (! empty($message['sender_phone'])): ?>
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="text-sm text-primary-light"><?= esc($message['sender_phone']) ?></span>
                            <?php $cleanPhone = preg_replace('/[^0-9]/', '', $message['sender_phone']); ?>
                            <a href="https://wa.me/<?= $cleanPhone ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline-success btn-sm radius-8 px-8 py-2 text-2xs" title="Chat WA Langsung">
                                <iconify-icon icon="logos:whatsapp-icon"></iconify-icon>
                            </a>
                        </div>
                    <?php else: ?>
                        <span class="text-sm text-muted">-</span>
                    <?php endif; ?>
                </div>

                <div>
                    <label class="text-2xs text-muted d-block text-uppercase fw-semibold mb-1">Kategori Permintaan</label>
                    <span class="badge bg-neutral-200 text-neutral-800 text-xs"><?= esc($message['service_category'] ?: 'Umum') ?></span>
                </div>
            </div>
        </div>

        <!-- Status & Manajemen Tiket Card -->
        <div class="card mb-24 shadow-sm border-0 radius-16">
            <div class="card-header border-bottom py-16 px-24 bg-base radius-top-16">
                <h6 class="text-sm fw-bold mb-0 text-primary-light">Manajemen Tiket</h6>
            </div>
            <div class="card-body p-24">
                <form method="post" action="<?= site_url('dashboard/komunikasi/pesan-masuk/update-status/' . $message['id']) ?>">
                    <?= csrf_field() ?>

                    <div class="mb-14">
                        <label class="form-label fw-semibold text-xs mb-6">Status Tiket</label>
                        <select name="status" class="form-select form-select-sm radius-8 text-xs">
                            <option value="unread" <?= $message['status'] === 'unread' ? 'selected' : '' ?>>Belum Dibaca (Unread)</option>
                            <option value="read" <?= $message['status'] === 'read' ? 'selected' : '' ?>>Sudah Dibaca (Read)</option>
                            <option value="in_progress" <?= $message['status'] === 'in_progress' ? 'selected' : '' ?>>Sedang Ditelaah / Diproses (In Progress)</option>
                            <option value="waiting_user" <?= $message['status'] === 'waiting_user' ? 'selected' : '' ?>>Menunggu Konfirmasi Pemohon (Waiting User)</option>
                            <option value="waiting_admin" <?= $message['status'] === 'waiting_admin' ? 'selected' : '' ?>>Tanggapan Pemohon Masuk (Waiting Admin)</option>
                            <option value="replied" <?= $message['status'] === 'replied' ? 'selected' : '' ?>>Sudah Dibalas (Replied)</option>
                            <option value="closed" <?= $message['status'] === 'closed' ? 'selected' : '' ?>>Selesai &amp; Ditutup (Closed)</option>
                            <option value="archived" <?= $message['status'] === 'archived' ? 'selected' : '' ?>>Diarsipkan (Archived)</option>
                        </select>
                    </div>

                    <div class="mb-14">
                        <label class="form-label fw-semibold text-xs mb-6">Prioritas Penanganan</label>
                        <?php $currentPriority = $notes['priority'] ?? 'normal'; ?>
                        <select name="priority" class="form-select form-select-sm radius-8 text-xs">
                            <option value="low" <?= $currentPriority === 'low' ? 'selected' : '' ?>>Rendah (Low)</option>
                            <option value="normal" <?= $currentPriority === 'normal' ? 'selected' : '' ?>>Normal / Standar</option>
                            <option value="high" <?= $currentPriority === 'high' ? 'selected' : '' ?>>Tinggi (High)</option>
                            <option value="urgent" <?= $currentPriority === 'urgent' ? 'selected' : '' ?>>Mendesak (Urgent)</option>
                        </select>
                    </div>

                    <div class="mb-14">
                        <label class="form-label fw-semibold text-xs mb-6">Petugas Helpdesk (Assignee)</label>
                        <?php $currentAssigned = $notes['assigned_to'] ?? ''; ?>
                        <select name="assigned_to" class="form-select form-select-sm radius-8 text-xs">
                            <option value="">-- Belum Ditugaskan --</option>
                            <?php foreach ($staffUsers as $su): ?>
                                <option value="<?= esc($su['name']) ?>" <?= $currentAssigned === $su['name'] ? 'selected' : '' ?>>
                                    <?= esc($su['name']) ?> (<?= esc($su['username']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-16">
                        <label class="form-label fw-semibold text-xs mb-6">Catatan Ringkasan Petugas</label>
                        <textarea name="admin_notes" class="form-control radius-8 text-xs" rows="3" placeholder="Catatan umum atau kesimpulan penanganan tiket"><?= esc($notes['internal_note'] ?? '') ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary-600 w-100 radius-8 py-8 text-xs fw-semibold">
                        Simpan Pengaturan Tiket
                    </button>
                </form>
            </div>
        </div>

        <!-- Tracking & Activity Log Card (Audit Trail) -->
        <div class="card shadow-sm border-0 radius-16">
            <div class="card-header border-bottom py-16 px-24 bg-base radius-top-16 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:history-bold" class="text-primary-600"></iconify-icon>
                    <h6 class="text-sm fw-bold mb-0 text-primary-light">Tracking &amp; Log Riwayat</h6>
                </div>
                <span class="badge bg-neutral-200 text-neutral-800 text-2xs"><?= count($logs) ?> Entri</span>
            </div>
            <div class="card-body p-20">
                <?php if (empty($logs)): ?>
                    <p class="text-muted text-xs mb-0">Belum ada riwayat aktivitas tercatat.</p>
                <?php else: ?>
                    <div class="position-relative ps-20" style="border-left: 2px solid #e2e8f0;">
                        <?php foreach ($logs as $i => $lg): ?>
                            <div class="position-relative mb-16">
                                <span class="position-absolute translate-middle-y bg-primary-600 radius-circle" style="left: -26px; top: 10px; width: 10px; height: 10px;"></span>
                                <div>
                                    <strong class="text-xs text-primary-light d-block"><?= esc($lg['action']) ?></strong>
                                    <small class="text-muted text-2xs d-block mb-1">
                                        <?= esc($lg['actor']) ?> &middot; <?= date('d M Y H:i', strtotime($lg['time'])) ?> WIB
                                    </small>
                                    <?php if (! empty($lg['detail'])): ?>
                                        <p class="text-2xs text-secondary-light mb-0 bg-neutral-50 p-6 radius-4 border">
                                            <?= esc($lg['detail']) ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>