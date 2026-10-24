<?= $this->extend('main/workspace/layouts/workspace') ?>

<?= $this->section('content') ?>
<?php
$threads = $notes['threads'] ?? [];
$initialAttachments = $notes['attachments'] ?? [];
$logs = $notes['logs'] ?? [];
$isClosed = in_array($ticket['status'], ['closed', 'archived'], true);

$statusBadge = match ($ticket['status']) {
    'unread'        => 'bg-danger-subtle text-danger border border-danger-subtle',
    'waiting_admin' => 'bg-info-subtle text-info border border-info-subtle',
    'in_progress'   => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
    'waiting_user'  => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
    'read'          => 'bg-light text-secondary border',
    'replied'       => 'bg-success-subtle text-success border border-success-subtle',
    'closed'        => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
    default         => 'bg-light text-dark border',
};
$statusLabel = match ($ticket['status']) {
    'unread'        => 'Menunggu Ditelaah',
    'waiting_admin' => 'Tanggapan Terkirim ke Petugas',
    'in_progress'   => 'Sedang Ditelaah Petugas',
    'waiting_user'  => 'Menunggu Balasan Anda',
    'read'          => 'Sudah Dibaca Petugas',
    'replied'       => 'Telah Dibalas Petugas',
    'closed'        => 'Selesai & Ditutup',
    default         => ucfirst($ticket['status']),
};

$priorityBadge = match ($notes['priority'] ?? 'normal') {
    'urgent' => 'bg-danger text-white',
    'high'   => 'bg-warning text-dark',
    'low'    => 'bg-light text-secondary border',
    default  => 'bg-primary-subtle text-primary border border-primary-subtle',
};
$priorityLabel = match ($notes['priority'] ?? 'normal') {
    'urgent' => 'Mendesak (Urgent)',
    'high'   => 'Prioritas Tinggi',
    'low'    => 'Prioritas Rendah',
    default  => 'Prioritas Normal',
};

// Roadmap Stage Calculation
$stageIndex = match ($ticket['status']) {
    'unread'                                         => 1,
    'read', 'in_progress', 'waiting_admin'           => 2,
    'waiting_user', 'replied'                        => 3,
    'closed', 'archived'                             => 4,
    default                                          => 1,
};
if (! empty($threads) && $stageIndex < 3) {
    $stageIndex = 3;
}
?>

<!-- Header Section -->
<div class="ws-page-header mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                <span class="badge bg-light text-primary border font-monospace text-xs px-2 py-1">
                    <i class="fas fa-hashtag me-1"></i><?= esc($ticket['ticket_number']) ?>
                </span>
                <span class="badge <?= $statusBadge ?> text-xs px-2 py-1">
                    <i class="fas fa-circle text-2xs me-1"></i><?= esc($statusLabel) ?>
                </span>
                <span class="badge <?= $priorityBadge ?> text-xs px-2 py-1">
                    <?= esc($priorityLabel) ?>
                </span>
                <span class="badge bg-light text-muted border text-xs">
                    <i class="fas fa-folder-open me-1"></i><?= esc($ticket['service_category'] ?: 'Umum') ?>
                </span>
            </div>
            <h1 class="h4 fw-bold mb-1" style="color: #082f5c;"><?= esc($ticket['subject']) ?></h1>
            <p class="text-muted small mb-0">
                Diajukan pada <?= date('d F Y, H:i', strtotime($ticket['created_at'])) ?> WIB &middot; Pembaruan terakhir <?= date('d F Y, H:i', strtotime($ticket['updated_at'])) ?> WIB
            </p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="<?= route_to('workspace.consultation.index') ?>" class="btn btn-outline-secondary radius-8 px-3 py-2 text-sm d-inline-flex align-items-center gap-1">
                <i class="fas fa-arrow-left me-1"></i>
                <span>Daftar Tiket</span>
            </a>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Left Column: Modern Room Chat (8 Cols) -->
    <div class="col-lg-8">
        <div class="ws-chat-room">
            <!-- Room Chat Top Bar -->
            <div class="ws-chat-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="position-relative">
                        <span class="w-44-px h-44-px rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fs-5 shadow-xs">
                            <i class="fas fa-headset"></i>
                        </span>
                        <span class="position-absolute bottom-0 end-0 p-1 bg-success border border-white rounded-circle" title="Layanan Helpdesk Aktif"></span>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                            <span>Room Konsultasi Helpdesk PBJ</span>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle text-2xs">
                                <?= count($threads) + 1 ?> Pesan
                            </span>
                        </h6>
                        <small class="text-muted text-xs">
                            Petugas: <strong><?= esc($notes['assigned_to'] ?: 'Tim Helpdesk Biro PBJ Provinsi Lampung') ?></strong>
                        </small>
                    </div>
                </div>
                <div class="d-none d-sm-flex align-items-center gap-2">
                    <span class="badge bg-light text-secondary border text-2xs">
                        <i class="fas fa-lock text-success me-1"></i>Terenkripsi &amp; Tercatat Resmi
                    </span>
                </div>
            </div>

            <!-- Room Chat Messages Stream -->
            <div class="ws-chat-messages" id="wsChatMessagesStream">
                <!-- Date Divider -->
                <div class="text-center my-1">
                    <span class="badge bg-white text-muted border shadow-xs px-3 py-1 text-2xs fw-semibold rounded-pill">
                        <i class="far fa-calendar-alt me-1"></i>Tiket Dibuka &middot; <?= date('d M Y', strtotime($ticket['created_at'])) ?>
                    </span>
                </div>

                <?php
                // Initial Ticket Message Bubble
                $initIsMe = ! $isAdmin;
                ?>
                <div class="ws-chat-bubble <?= $initIsMe ? 'is-me' : 'is-other' ?>">
                    <div class="ws-chat-meta">
                        <strong>
                            <i class="fas <?= $initIsMe ? 'fa-user-check' : 'fa-user' ?> me-1"></i>
                            <?= esc($ticket['sender_name']) ?><?= $initIsMe ? ' (Anda)' : ' (Pemohon)' ?>
                        </strong>
                        <span>&middot;</span>
                        <span><?= date('d M Y, H:i', strtotime($ticket['created_at'])) ?> WIB</span>
                        <span class="badge <?= $initIsMe ? 'bg-white text-primary' : 'bg-primary-subtle text-primary' ?> text-2xs ms-1">Tiket Awal</span>
                    </div>
                    <div style="font-size: 0.9rem; line-height: 1.6; white-space: pre-wrap;"><?= esc($ticket['message']) ?></div>

                    <?php if (! empty($initialAttachments)): ?>
                        <div class="d-flex flex-wrap gap-2 mt-2">
                            <?php foreach ($initialAttachments as $att): ?>
                                <a href="<?= esc($att['url'] ?? '#') ?>" target="_blank" rel="noopener noreferrer" class="ws-chat-attachment">
                                    <i class="fas fa-paperclip"></i>
                                    <span><?= esc($att['name'] ?? 'Lampiran Berkas') ?></span>
                                    <?php if (! empty($att['size_human'])): ?>
                                        <small class="opacity-75">(<?= esc($att['size_human']) ?>)</small>
                                    <?php endif; ?>
                                    <i class="fas fa-download ms-1 small"></i>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Thread Messages Loop -->
                <?php if (! empty($threads)): ?>
                    <?php foreach ($threads as $th): ?>
                        <?php
                        $isThAdmin = ($th['sender_type'] ?? '') === 'administrator';
                        $isInternal = ($th['channel'] ?? 'portal') === 'internal';
                        $bubbleIsMe = ($isAdmin && $isThAdmin) || (! $isAdmin && ! $isThAdmin);
                        $thAttachments = is_array($th['attachments'] ?? null) ? $th['attachments'] : [];
                        ?>
                        <div class="ws-chat-bubble <?= $bubbleIsMe ? 'is-me' : 'is-other' ?>" <?= $isInternal ? 'style="border: 1.5px dashed #f59e0b; background: #fffbeb; color: #78350f;"' : '' ?>>
                            <div class="ws-chat-meta">
                                <?php if ($isThAdmin): ?>
                                    <strong>
                                        <i class="fas fa-headset me-1"></i>
                                        <?= esc($th['sender_name'] ?? 'Tim Helpdesk PBJ') ?><?= ($isAdmin && $isThAdmin) ? ' (Petugas)' : '' ?>
                                    </strong>
                                    <span class="badge <?= $bubbleIsMe ? 'bg-white text-primary' : 'bg-primary text-white' ?> text-2xs">
                                        <?= $isInternal ? 'Catatan Internal' : 'Helpdesk Resmi' ?>
                                    </span>
                                <?php else: ?>
                                    <strong>
                                        <i class="fas fa-user me-1"></i>
                                        <?= esc($th['sender_name'] ?? 'Pemohon') ?><?= (! $isAdmin) ? ' (Anda)' : '' ?>
                                    </strong>
                                <?php endif; ?>
                                <span>&middot;</span>
                                <span><?= date('d M Y, H:i', strtotime($th['created_at'])) ?> WIB</span>
                            </div>

                            <div style="font-size: 0.9rem; line-height: 1.6; white-space: pre-wrap;"><?= esc($th['message']) ?></div>

                            <?php if (! empty($thAttachments)): ?>
                                <div class="d-flex flex-wrap gap-2 mt-2">
                                    <?php foreach ($thAttachments as $att): ?>
                                        <a href="<?= esc($att['url'] ?? '#') ?>" target="_blank" rel="noopener noreferrer" class="ws-chat-attachment">
                                            <i class="fas fa-file-download"></i>
                                            <span><?= esc($att['name'] ?? 'Lampiran') ?></span>
                                            <?php if (! empty($att['size_human'])): ?>
                                                <small class="opacity-75">(<?= esc($att['size_human']) ?>)</small>
                                            <?php endif; ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center my-3">
                        <div class="d-inline-flex align-items-center gap-2 px-3 py-2 rounded-pill bg-white border shadow-xs text-muted small">
                            <i class="fas fa-hourglass-half text-warning"></i>
                            <span>Tiket Anda berada dalam antrean telaah Tim Helpdesk PBJ Lampung. Anda juga dapat menambahkan pesan atau lampiran di bawah ini.</span>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Room Chat Composer / Closed Banner -->
            <div class="ws-chat-composer">
                <?php if ($isClosed && ! $isAdmin): ?>
                    <div class="p-3 rounded-3 bg-light border text-center">
                        <div class="d-inline-flex align-items-center gap-2 text-secondary fw-bold small mb-1">
                            <i class="fas fa-lock"></i>
                            <span>Tiket Konsultasi Telah Selesai &amp; Ditutup</span>
                        </div>
                        <p class="text-muted text-xs mb-2">
                            Seluruh percakapan di atas tersimpan sebagai arsip resmi. Jika Anda memiliki pertanyaan baru, silakan buat tiket konsultasi baru.
                        </p>
                        <a href="<?= route_to('workspace.consultation.index') ?>" class="btn btn-sm btn-outline-primary radius-8 px-3 fw-bold">
                            <i class="fas fa-plus-circle me-1"></i> Buat Tiket Baru
                        </a>
                    </div>
                <?php else: ?>
                    <?php if ($isClosed && $isAdmin): ?>
                        <div class="alert alert-warning py-1-5 px-3 rounded-3 text-xs mb-2">
                            <i class="fas fa-info-circle me-1"></i>
                            <strong>Mode Petugas:</strong> Tiket ini berstatus ditutup, namun Anda tetap dapat mengirim pesan tambahan.
                        </div>
                    <?php endif; ?>

                    <form action="<?= route_to('workspace.consultation.reply', $ticket['ticket_number']) ?>" method="post" enctype="multipart/form-data" id="wsChatReplyForm">
                        <?= csrf_field() ?>

                        <!-- Attachment Preview Pill -->
                        <div id="wsAttachmentPreview" class="d-none mb-2">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1-5 d-inline-flex align-items-center gap-2 text-xs">
                                <i class="fas fa-paperclip"></i>
                                <span id="wsAttachmentFileName">berkas.pdf</span>
                                <button type="button" class="btn-close" style="font-size: 0.6rem;" id="wsClearAttachmentBtn" aria-label="Hapus lampiran"></button>
                            </span>
                        </div>

                        <div class="mb-2">
                            <textarea name="reply_message" id="wsReplyTextarea" class="form-control rounded-3 text-sm" rows="3" placeholder="Ketik pesan balasan, klarifikasi, atau pertanyaan lanjutan di sini... (Tekan Ctrl+Enter untuk mengirim)"></textarea>
                        </div>

                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <label for="wsChatAttachmentInput" class="btn btn-sm btn-outline-secondary radius-8 px-3 py-1-5 fw-semibold d-inline-flex align-items-center gap-1 mb-0" style="cursor: pointer;" data-bs-toggle="tooltip" title="Lampirkan PDF, Dokumen Office, Gambar, atau ZIP (Maks 10MB)">
                                    <i class="fas fa-paperclip text-primary"></i>
                                    <span>Lampirkan File</span>
                                </label>
                                <input type="file" name="attachment" id="wsChatAttachmentInput" class="d-none" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.webp,.zip,.rar">
                                <span class="text-muted text-2xs d-none d-md-inline">
                                    <i class="fas fa-bell text-warning me-1"></i>Notifikasi otomatis dikirim ke penerima
                                </span>
                            </div>

                            <button type="submit" class="btn btn-primary radius-8 px-4 py-2 fw-bold d-inline-flex align-items-center gap-2">
                                <i class="fas fa-paper-plane"></i>
                                <span>Kirim Pesan</span>
                            </button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Right Column: Vertical Status Roadmap & Historical Audit Log (4 Cols) -->
    <div class="col-lg-4">
        <!-- 1. Roadmap Progres Status Tiket (Vertical Roadmap) -->
        <div class="card border-0 shadow-sm rounded-4 p-3 p-md-4 bg-white mb-4">
            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                <h6 class="fw-bold text-dark mb-0 small">
                    <i class="fas fa-route text-primary me-2"></i>Roadmap Status Tiket
                </h6>
                <span class="badge <?= $statusBadge ?> text-2xs"><?= esc($statusLabel) ?></span>
            </div>

            <?php
            $roadmapStages = [
                1 => [
                    'title' => '1. Tiket Dibuat & Diterima',
                    'desc'  => 'Permohonan konsultasi tercatat dengan nomor #' . $ticket['ticket_number'],
                    'time'  => date('d M Y, H:i', strtotime($ticket['created_at'])) . ' WIB',
                    'icon'  => 'fa-file-signature',
                ],
                2 => [
                    'title' => '2. Ditelaah Petugas Helpdesk',
                    'desc'  => $notes['assigned_to']
                        ? 'Ditangani oleh: ' . $notes['assigned_to']
                        : 'Tim Helpdesk PBJ menelaah pokok permasalahan',
                    'time'  => $stageIndex >= 2 ? 'Dalam Penanganan' : 'Menunggu giliran telaah',
                    'icon'  => 'fa-user-shield',
                ],
                3 => [
                    'title' => '3. Diskusi & Tanggapan Resmi',
                    'desc'  => count($threads) > 0
                        ? count($threads) . ' balasan percakapan di Room Chat'
                        : 'Percakapan dua arah & klarifikasi dokumen',
                    'time'  => ! empty($ticket['replied_at'])
                        ? 'Dijawab ' . date('d M Y, H:i', strtotime($ticket['replied_at'])) . ' WIB'
                        : ($stageIndex >= 3 ? 'Diskusi Aktif' : 'Menunggu tanggapan'),
                    'icon'  => 'fa-comments',
                ],
                4 => [
                    'title' => '4. Konsultasi Selesai / Ditutup',
                    'desc'  => $isClosed
                        ? 'Tiket telah diselesaikan dan diarsipkan'
                        : 'Tiket akan ditutup setelah konsultasi tuntas',
                    'time'  => $isClosed ? date('d M Y, H:i', strtotime($ticket['updated_at'])) . ' WIB' : 'Tahap Akhir',
                    'icon'  => 'fa-check-double',
                ],
            ];
            ?>

            <div class="ws-roadmap mb-2">
                <?php foreach ($roadmapStages as $idx => $st): ?>
                    <?php
                    $stepClass = 'is-pending';
                    if ($idx < $stageIndex || ($idx === 4 && $isClosed)) {
                        $stepClass = 'is-done';
                    } elseif ($idx === $stageIndex) {
                        $stepClass = 'is-active';
                    }
                    ?>
                    <div class="ws-roadmap-step <?= $stepClass ?>">
                        <span class="ws-roadmap-dot">
                            <?php if ($stepClass === 'is-done'): ?>
                                <i class="fas fa-check"></i>
                            <?php elseif ($stepClass === 'is-active'): ?>
                                <i class="fas <?= $st['icon'] ?>"></i>
                            <?php else: ?>
                                <i class="fas fa-circle" style="font-size: 0.4rem;"></i>
                            <?php endif; ?>
                        </span>
                        <div class="ws-roadmap-card">
                            <div class="d-flex justify-content-between align-items-start gap-2 mb-1">
                                <strong class="text-dark" style="font-size: 0.8rem;"><?= esc($st['title']) ?></strong>
                            </div>
                            <p class="text-muted mb-1" style="font-size: 0.75rem; line-height: 1.4;"><?= esc($st['desc']) ?></p>
                            <span class="badge bg-white text-secondary border" style="font-size: 0.68rem;">
                                <i class="far fa-clock me-1"></i><?= esc($st['time']) ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- 2. Riwayat Log Aktivitas Historis (Audit Trail Timeline) -->
        <div class="card border-0 shadow-sm rounded-4 p-3 p-md-4 bg-white mb-4">
            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                <h6 class="fw-bold text-dark mb-0 small">
                    <i class="fas fa-history text-primary me-2"></i>Log Riwayat Aktivitas
                </h6>
                <span class="badge bg-light text-secondary border text-2xs"><?= count($logs) ?> Log</span>
            </div>

            <?php if (empty($logs)): ?>
                <p class="text-muted text-xs mb-0">Belum ada catatan log tambahan.</p>
            <?php else: ?>
                <div class="ws-roadmap" style="max-height: 290px; overflow-y: auto; padding-right: 4px;">
                    <?php foreach (array_reverse($logs) as $logIdx => $log): ?>
                        <?php
                        $isLatestLog = ($logIdx === 0);
                        $actorRole = $log['actor_role'] ?? 'user';
                        ?>
                        <div class="ws-roadmap-step <?= $isLatestLog ? 'is-active' : 'is-done' ?>" style="padding-bottom: 0.85rem;">
                            <span class="ws-roadmap-dot">
                                <i class="fas <?= $actorRole === 'administrator' ? 'fa-user-tie' : 'fa-check' ?>"></i>
                            </span>
                            <div class="ws-roadmap-card" style="padding: 0.55rem 0.75rem;">
                                <div class="d-flex justify-content-between align-items-center gap-1 mb-1">
                                    <strong class="text-dark" style="font-size: 0.76rem;"><?= esc($log['action'] ?? 'Aktivitas') ?></strong>
                                    <small class="text-muted" style="font-size: 0.66rem;">
                                        <?= ! empty($log['time']) ? date('d/m H:i', strtotime($log['time'])) : '' ?>
                                    </small>
                                </div>
                                <?php if (! empty($log['detail'])): ?>
                                    <p class="text-muted mb-1" style="font-size: 0.72rem; line-height: 1.35;"><?= esc($log['detail']) ?></p>
                                <?php endif; ?>
                                <span class="text-secondary" style="font-size: 0.68rem;">
                                    <i class="fas fa-user-circle me-1"></i><?= esc($log['actor'] ?? 'Sistem') ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- 3. Metadata & Kontak Darurat -->
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
            <h6 class="fw-bold text-dark mb-2 pb-2 border-bottom small">
                <i class="fas fa-info-circle text-primary me-1"></i>Detail Metadata Tiket
            </h6>
            <ul class="list-unstyled mb-3 d-flex flex-column gap-2 text-xs">
                <li class="d-flex justify-content-between">
                    <span class="text-muted">Pemohon:</span>
                    <strong class="text-dark"><?= esc($ticket['sender_name']) ?></strong>
                </li>
                <li class="d-flex justify-content-between">
                    <span class="text-muted">Email:</span>
                    <span class="text-dark"><?= esc($ticket['sender_email']) ?></span>
                </li>
                <?php if (! empty($ticket['sender_phone'])): ?>
                    <li class="d-flex justify-content-between">
                        <span class="text-muted">Telepon/WA:</span>
                        <span class="text-dark"><?= esc($ticket['sender_phone']) ?></span>
                    </li>
                <?php endif; ?>
            </ul>

            <a href="https://wa.me/6285117779783" target="_blank" rel="noopener noreferrer" class="btn btn-outline-success btn-sm w-100 radius-8 py-2 d-inline-flex align-items-center justify-content-center gap-2">
                <i class="fab fa-whatsapp"></i>
                <span class="fw-bold">Hotline WhatsApp PBJ</span>
            </a>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const stream = document.getElementById('wsChatMessagesStream');
    if (stream) {
        stream.scrollTop = stream.scrollHeight;
    }

    const fileInput = document.getElementById('wsChatAttachmentInput');
    const previewWrap = document.getElementById('wsAttachmentPreview');
    const fileNameEl = document.getElementById('wsAttachmentFileName');
    const clearBtn = document.getElementById('wsClearAttachmentBtn');

    if (fileInput && previewWrap && fileNameEl) {
        fileInput.addEventListener('change', function () {
            if (fileInput.files && fileInput.files.length > 0) {
                const f = fileInput.files[0];
                const sizeKB = Math.max(1, Math.round(f.size / 1024));
                fileNameEl.textContent = f.name + ' (' + (sizeKB > 1024 ? (sizeKB / 1024).toFixed(1) + ' MB' : sizeKB + ' KB') + ')';
                previewWrap.classList.remove('d-none');
            } else {
                previewWrap.classList.add('d-none');
            }
        });
    }

    if (clearBtn && fileInput && previewWrap) {
        clearBtn.addEventListener('click', function () {
            fileInput.value = '';
            previewWrap.classList.add('d-none');
        });
    }

    const textarea = document.getElementById('wsReplyTextarea');
    const form = document.getElementById('wsChatReplyForm');
    if (textarea && form) {
        textarea.addEventListener('keydown', function (e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
                e.preventDefault();
                form.submit();
            }
        });
    }
});
</script>
<?= $this->endSection() ?>
