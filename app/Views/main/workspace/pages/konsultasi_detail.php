<?= $this->extend('main/workspace/layouts/workspace') ?>

<?= $this->section('content') ?>
<?php
$threads = $notes['threads'] ?? [];
$isClosed = $ticket['status'] === 'closed' || $ticket['status'] === 'archived';

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
    'closed'        => 'Selesai &amp; Ditutup',
    default         => ucfirst($ticket['status']),
};
?>

<!-- Header Section -->
<div class="ws-page-header mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                <span class="badge bg-light text-primary border font-monospace text-xs px-2 py-1">
                    #<?= esc($ticket['ticket_number']) ?>
                </span>
                <span class="badge <?= $statusBadge ?> text-xs px-2 py-1">
                    <?= $statusLabel ?>
                </span>
                <span class="badge bg-light text-muted border text-xs">
                    <?= esc($ticket['service_category'] ?: 'Umum') ?>
                </span>
            </div>
            <h1 class="h4 fw-bold mb-1" style="color: #082f5c;"><?= esc($ticket['subject']) ?></h1>
            <p class="text-muted small mb-0">
                Diajukan pada <?= date('d F Y, H:i', strtotime($ticket['created_at'])) ?> WIB &middot; Terakhir diperbarui <?= date('d F Y, H:i', strtotime($ticket['updated_at'])) ?> WIB
            </p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="<?= route_to('workspace.consultation.index') ?>" class="btn btn-outline-secondary radius-8 px-3 py-2 text-sm d-inline-flex align-items-center gap-1">
                <i class="fas fa-arrow-left me-1"></i>
                <span>Kembali ke Tiket Saya</span>
            </a>
        </div>
    </div>
</div>

<!-- Tracking Progress Steps Bar -->
<div class="card border-0 shadow-sm rounded-4 p-3 p-md-4 bg-white mb-4">
    <div class="row text-center g-2 position-relative">
        <?php
        $step1Active = true;
        $step2Active = in_array($ticket['status'], ['read', 'in_progress', 'waiting_user', 'waiting_admin', 'replied', 'closed'], true);
        $step3Active = in_array($ticket['status'], ['replied', 'closed'], true);
        $step4Active = $ticket['status'] === 'closed';
        ?>
        <div class="col-3">
            <div class="p-2 radius-8 <?= $step1Active ? 'bg-primary-subtle text-primary fw-bold' : 'text-muted' ?>">
                <i class="fas fa-file-invoice fs-5 d-block mb-1"></i>
                <span class="small d-block">1. Diterima</span>
            </div>
        </div>
        <div class="col-3">
            <div class="p-2 radius-8 <?= $step2Active ? 'bg-primary-subtle text-primary fw-bold' : 'text-muted' ?>">
                <i class="fas fa-user-clock fs-5 d-block mb-1"></i>
                <span class="small d-block">2. Ditelaah Helpdesk</span>
            </div>
        </div>
        <div class="col-3">
            <div class="p-2 radius-8 <?= $step3Active ? 'bg-primary-subtle text-primary fw-bold' : 'text-muted' ?>">
                <i class="fas fa-comments fs-5 d-block mb-1"></i>
                <span class="small d-block">3. Dibalas / Diskusi</span>
            </div>
        </div>
        <div class="col-3">
            <div class="p-2 radius-8 <?= $step4Active ? 'bg-success-subtle text-success fw-bold' : 'text-muted' ?>">
                <i class="fas fa-check-circle fs-5 d-block mb-1"></i>
                <span class="small d-block">4. Selesai</span>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Main Column: Percakapan Dua Arah (8 Cols) -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 p-3 p-md-4 bg-white mb-4">
            <div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom">
                <h5 class="fw-bold mb-0 text-dark" style="font-size: 16px;">
                    <i class="fas fa-comments text-primary me-2"></i>Riwayat Percakapan
                </h5>
                <span class="badge bg-light text-dark border small">
                    <?= count($threads) + 1 ?> Pesan Terkirim
                </span>
            </div>

            <!-- 1. Pesan Awal dari Anda (Pemohon) -->
            <div class="p-3 rounded-4 bg-light border mb-4">
                <div class="d-flex align-items-center justify-content-between mb-2 pb-2 border-bottom flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="w-36-px h-36-px rounded-circle bg-primary text-white fw-bold d-flex align-items-center justify-content-center small">
                            <?= strtoupper(mb_substr($ticket['sender_name'], 0, 1)) ?>
                        </span>
                        <div>
                            <strong class="text-dark small d-block"><?= esc($ticket['sender_name']) ?> (Anda)</strong>
                            <span class="text-muted text-2xs">Pengirim Permohonan Konsultasi</span>
                        </div>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-primary-subtle text-primary text-2xs">Pesan Awal</span>
                        <small class="text-muted text-2xs d-block"><?= date('d M Y, H:i', strtotime($ticket['created_at'])) ?> WIB</small>
                    </div>
                </div>
                <div class="text-dark" style="font-size: 14.5px; line-height: 1.6; white-space: pre-wrap;"><?= esc($ticket['message']) ?></div>
            </div>

            <!-- 2. Loop Percakapan Lanjutan (Two-way Conversation Threads) -->
            <?php if (! empty($threads)): ?>
                <div class="d-flex flex-column gap-3 mb-4">
                    <?php foreach ($threads as $th): ?>
                        <?php
                        $isThAdmin = ($th['sender_type'] ?? '') === 'admin';
                        ?>
                        <?php if ($isThAdmin): ?>
                            <!-- Tanggapan Helpdesk (Admin) -->
                            <div class="p-3 rounded-4 border bg-primary-subtle border-primary-subtle ms-3 shadow-xs">
                                <div class="d-flex align-items-center justify-content-between mb-2 pb-2 border-bottom flex-wrap gap-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="w-32-px h-32-px rounded-circle bg-primary text-white fw-bold d-flex align-items-center justify-content-center text-xs">
                                            <i class="fas fa-headset"></i>
                                        </span>
                                        <div>
                                            <strong class="text-primary small d-block"><?= esc($th['sender_name'] ?? 'Tim Helpdesk PBJ Lampung') ?></strong>
                                            <span class="text-muted text-2xs">Petugas Resmi Biro PBJ Provinsi Lampung</span>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-primary text-white text-2xs">Jawaban Resmi</span>
                                        <small class="text-muted text-2xs d-block"><?= date('d M Y, H:i', strtotime($th['created_at'])) ?> WIB</small>
                                    </div>
                                </div>
                                <div class="text-dark" style="font-size: 14px; line-height: 1.6; white-space: pre-wrap;"><?= esc($th['message']) ?></div>
                            </div>
                        <?php else: ?>
                            <!-- Tanggapan Pemohon (User / Anda) -->
                            <div class="p-3 rounded-4 border bg-white me-3 shadow-xs">
                                <div class="d-flex align-items-center justify-content-between mb-2 pb-2 border-bottom flex-wrap gap-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="w-32-px h-32-px rounded-circle bg-neutral-200 text-dark fw-bold d-flex align-items-center justify-content-center text-xs">
                                            <?= strtoupper(mb_substr($th['sender_name'] ?? 'U', 0, 1)) ?>
                                        </span>
                                        <div>
                                            <strong class="text-dark small d-block"><?= esc($th['sender_name'] ?? 'Anda') ?> (Anda)</strong>
                                            <span class="text-muted text-2xs">Tanggapan Lanjutan Pemohon</span>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-light text-secondary border text-2xs">Tanggapan Anda</span>
                                        <small class="text-muted text-2xs d-block"><?= date('d M Y, H:i', strtotime($th['created_at'])) ?> WIB</small>
                                    </div>
                                </div>
                                <div class="text-dark" style="font-size: 14px; line-height: 1.6; white-space: pre-wrap;"><?= esc($th['message']) ?></div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-light border text-center py-3 rounded-4 mb-4 text-muted small">
                    <i class="fas fa-hourglass-half me-1 text-warning"></i>
                    Pertanyaan Anda telah masuk dalam antrean helpdesk. Tim PBJ Lampung akan menelaah dan membalas pertanyaan Anda di halaman ini.
                </div>
            <?php endif; ?>

            <?php if ($ticket['status'] === 'closed' && ! $isAdmin): ?>
                <!-- Notifikasi Tiket Selesai / Ditutup (View-Only untuk Pengirim) -->
                <div class="p-4 rounded-4 border bg-white shadow-xs text-center">
                    <div class="w-48-px h-48-px rounded-circle bg-secondary-subtle text-secondary mx-auto d-flex align-items-center justify-content-center mb-3">
                        <i class="fas fa-lock fa-lg"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">Tiket Telah Selesai & Ditutup</h6>
                    <p class="text-muted text-xs mb-3" style="max-width: 520px; margin-inline: auto;">
                        Tiket konsultasi ini telah ditutup oleh Administrator. Seluruh riwayat percakapan di atas bersifat <strong>hanya lihat (view only)</strong>. Apabila Anda memiliki pertanyaan atau kendala baru, silakan ajukan tiket konsultasi baru.
                    </p>
                    <a href="<?= route_to('workspace.consultation.index') ?>" class="btn btn-outline-primary radius-8 px-3 py-1-5 text-xs fw-semibold">
                        <i class="fas fa-plus me-1"></i> Ajukan Tiket Baru
                    </a>
                </div>
            <?php else: ?>
                <!-- 3. Form Kirim Tanggapan Dua Arah -->
                <div class="p-3 rounded-4 border bg-light">
                    <?php if ($ticket['status'] === 'closed' && $isAdmin): ?>
                        <div class="alert alert-warning py-2 px-3 rounded-3 small mb-3">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            <strong>Mode Administrator:</strong> Tiket ini berstatus <em>closed</em>. Sebagai pengelola/admin, Anda tetap dapat mengirimkan balasan tambahan ke pemohon.
                        </div>
                    <?php endif; ?>
                    <h6 class="fw-bold text-dark small mb-2">
                        <i class="fas fa-reply text-primary me-1"></i>Kirim Balasan / Pertanyaan Tambahan
                    </h6>
                    <p class="text-muted text-xs mb-3">
                        Gunakan kolom di bawah ini untuk saling membalas pesan, memberikan klarifikasi, atau menanyakan hal terkait lainnya langsung ke tim Helpdesk.
                    </p>

                    <form action="<?= route_to('workspace.consultation.reply', $ticket['ticket_number']) ?>" method="post">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <textarea name="reply_message" class="form-control rounded-3 text-sm" rows="4" placeholder="Tuliskan balasan, klarifikasi, atau tanggapan Anda di sini..." required></textarea>
                            <small class="text-muted text-2xs">Minimal 3 karakter.</small>
                        </div>

                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <small class="text-muted text-2xs">
                                <i class="fas fa-shield-alt text-primary me-1"></i>Percakapan tercatat secara resmi pada log tiket PBJ Lampung.
                            </small>
                            <button type="submit" class="btn btn-primary radius-8 px-4 py-2 fw-bold d-inline-flex align-items-center gap-1">
                                <i class="fas fa-paper-plane me-1"></i>
                                <span>Kirim Tanggapan</span>
                            </button>
                        </div>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Right Column: Sidebar Info & Help (4 Cols) -->
    <div class="col-lg-4">
        <!-- Ticket Information Card -->
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white mb-4">
            <h6 class="fw-bold text-dark mb-3 pb-2 border-bottom small">
                <i class="fas fa-info-circle text-primary me-1"></i>Informasi Tiket
            </h6>

            <ul class="list-unstyled mb-0 d-flex flex-column gap-2 small">
                <li class="d-flex justify-content-between pb-2 border-bottom">
                    <span class="text-muted">Nomor Tiket:</span>
                    <strong class="font-monospace text-dark"><?= esc($ticket['ticket_number']) ?></strong>
                </li>
                <li class="d-flex justify-content-between pb-2 border-bottom">
                    <span class="text-muted">Kategori:</span>
                    <span class="text-end fw-semibold text-dark"><?= esc($ticket['service_category'] ?: 'Umum') ?></span>
                </li>
                <li class="d-flex justify-content-between pb-2 border-bottom">
                    <span class="text-muted">Status:</span>
                    <span class="badge <?= $statusBadge ?>"><?= $statusLabel ?></span>
                </li>
                <li class="d-flex justify-content-between pb-2 border-bottom">
                    <span class="text-muted">Petugas:</span>
                    <span class="fw-semibold text-dark"><?= esc($notes['assigned_to'] ?: 'Tim Helpdesk PBJ') ?></span>
                </li>
                <li class="d-flex justify-content-between">
                    <span class="text-muted">Terakhir Update:</span>
                    <span class="text-muted"><?= date('d/m/Y H:i', strtotime($ticket['updated_at'])) ?> WIB</span>
                </li>
            </ul>
        </div>

        <!-- Helpdesk Quick Assistance Card -->
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white mb-4">
            <h6 class="fw-bold text-dark mb-2 small">
                <i class="fas fa-question-circle text-info me-1"></i>Butuh Bantuan Mendesak?
            </h6>
            <p class="text-muted text-xs mb-3">
                Jika permohonan konsultasi Anda bersifat darurat atau memerlukan penanganan teknis segera, Anda dapat menghubungi Helpdesk Biro Pengadaan Barang dan Jasa Pemerintah Provinsi Lampung:
            </p>

            <a href="https://wa.me/6285117779783" target="_blank" rel="noopener noreferrer" class="btn btn-outline-success btn-sm w-100 radius-8 py-2 mb-2 d-inline-flex align-items-center justify-content-center gap-2">
                <i class="fab fa-whatsapp fs-5"></i>
                <span class="fw-bold">Hotline WhatsApp PBJ</span>
            </a>

            <a href="mailto:pengadaan@lampungprov.go.id" class="btn btn-outline-primary btn-sm w-100 radius-8 py-2 d-inline-flex align-items-center justify-content-center gap-2">
                <i class="fas fa-envelope"></i>
                <span>Email Resmi Biro PBJ</span>
            </a>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
