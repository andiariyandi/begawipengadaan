<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?= view('dashboard/components/flash_message') ?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Detail Pesan Masuk') ?></h6>
        <p class="text-secondary-light mb-0">Tiket No: <code class="fw-bold"><?= esc($message['ticket_number'] ?: ('#' . $message['id'])) ?></code> &middot; Diterima: <?= date('d F Y, H:i', strtotime($message['created_at'])) ?> WIB</p>
    </div>
    <a href="<?= site_url('dashboard/komunikasi/pesan-masuk') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Kembali ke Inbox</a>
</div>

<div class="row gy-24">
    <div class="col-lg-8">
        <div class="card mb-24">
            <div class="card-header border-bottom py-16 px-24 d-flex align-items-center justify-content-between flex-wrap gap-2">
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle text-xs px-12 py-6">
                    <?= esc($message['service_category'] ?: 'Umum / Pertanyaan PBJ') ?>
                </span>
                <span class="text-xs text-muted">
                    Waktu: <?= date('d M Y, H:i:s', strtotime($message['created_at'])) ?> WIB
                </span>
            </div>
            <div class="card-body p-24">
                <h5 class="fw-bold text-dark mb-16"><?= esc($message['subject']) ?></h5>
                <div class="p-20 bg-neutral-50 radius-10 border text-slate-700 leading-relaxed mb-24" style="font-size: 14.5px; white-space: pre-wrap;"><?= esc($message['message']) ?></div>

                <div class="d-flex flex-wrap gap-2">
                    <a href="mailto:<?= esc($message['sender_email']) ?>?subject=Re: <?= urlencode($message['subject']) ?>" class="btn btn-primary-600 radius-8 px-20 py-10 d-inline-flex align-items-center gap-2">
                        <iconify-icon icon="solar:plain-bold"></iconify-icon>
                        <span>Balas via Email (<?= esc($message['sender_email']) ?>)</span>
                    </a>
                    <?php if (! empty($message['sender_phone'])): ?>
                        <?php $cleanPhone = preg_replace('/[^0-9]/', '', $message['sender_phone']); ?>
                        <a href="https://wa.me/<?= $cleanPhone ?>" target="_blank" rel="noopener noreferrer" class="btn btn-success radius-8 px-20 py-10 d-inline-flex align-items-center gap-2">
                            <iconify-icon icon="logos:whatsapp-icon"></iconify-icon>
                            <span>Chat WhatsApp (<?= esc($message['sender_phone']) ?>)</span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Pengirim Info Card -->
        <div class="card mb-24">
            <div class="card-header border-bottom py-16 px-24">
                <h6 class="text-sm fw-bold mb-0 text-dark">Data Pengirim</h6>
            </div>
            <div class="card-body p-24">
                <div class="mb-16">
                    <label class="text-xs text-muted d-block">Nama Lengkap</label>
                    <strong class="text-sm text-dark"><?= esc($message['sender_name']) ?></strong>
                </div>
                <div class="mb-16">
                    <label class="text-xs text-muted d-block">Email</label>
                    <a href="mailto:<?= esc($message['sender_email']) ?>" class="text-sm text-primary"><?= esc($message['sender_email']) ?></a>
                </div>
                <div class="mb-16">
                    <label class="text-xs text-muted d-block">No. Telepon / WhatsApp</label>
                    <span class="text-sm text-dark"><?= esc($message['sender_phone'] ?: '-') ?></span>
                </div>
                <div>
                    <label class="text-xs text-muted d-block">Kategori Permintaan</label>
                    <span class="badge bg-neutral-200 text-neutral-800 text-xs"><?= esc($message['service_category'] ?: 'Umum') ?></span>
                </div>
            </div>
        </div>

        <!-- Status & Tindak Lanjut Card -->
        <div class="card">
            <div class="card-header border-bottom py-16 px-24">
                <h6 class="text-sm fw-bold mb-0 text-dark">Status & Tindak Lanjut</h6>
            </div>
            <div class="card-body p-24">
                <form method="post" action="<?= site_url('dashboard/komunikasi/pesan-masuk/update-status/' . $message['id']) ?>">
                    <?= csrf_field() ?>
                    <div class="mb-16">
                        <label class="form-label fw-semibold text-xs mb-6">Status Penanganan</label>
                        <select name="status" class="form-select radius-8 text-sm">
                            <option value="unread" <?= $message['status'] === 'unread' ? 'selected' : '' ?>>Belum Dibaca (Unread)</option>
                            <option value="read" <?= $message['status'] === 'read' ? 'selected' : '' ?>>Sudah Dibaca (Read)</option>
                            <option value="replied" <?= $message['status'] === 'replied' ? 'selected' : '' ?>>Sudah Dibalas (Replied)</option>
                            <option value="archived" <?= $message['status'] === 'archived' ? 'selected' : '' ?>>Diarsipkan (Archived)</option>
                        </select>
                    </div>
                    <div class="mb-20">
                        <label class="form-label fw-semibold text-xs mb-6">Catatan Admin / Tim Helpdesk</label>
                        <textarea name="admin_notes" class="form-control radius-8 text-sm" rows="4" placeholder="Catat ringkasan jawaban atau nama petugas yang menangani"><?= esc($message['admin_notes'] ?? '') ?></textarea>
                    </div>
                    <?php if (! empty($message['replied_at'])): ?>
                        <small class="text-muted text-2xs d-block mb-12">Ditandai dibalas pada: <?= date('d M Y H:i', strtotime($message['replied_at'])) ?> WIB</small>
                    <?php endif; ?>
                    <button type="submit" class="btn btn-primary-600 w-100 radius-8 py-10 text-sm">Simpan Catatan & Status</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
