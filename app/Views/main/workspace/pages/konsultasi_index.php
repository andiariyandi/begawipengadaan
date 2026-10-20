<?= $this->extend('main/workspace/layouts/workspace') ?>

<?= $this->section('content') ?>
<div class="ws-page-header mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <span class="ws-eyebrow"><i class="fas fa-comments text-primary me-1"></i> Layanan Konsultasi &amp; Helpdesk</span>
            <h1 class="h3 fw-bold mb-1" style="color: #082f5c;">Konsultasi &amp; Tiket Saya</h1>
            <p class="text-muted mb-0" style="font-size: 14.5px;">
                Pantau status permohonan konsultasi PBJ Anda dan lakukan percakapan dua arah langsung dengan tim Helpdesk PBJ Provinsi Lampung.
            </p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-primary radius-8 px-3 py-2 fw-bold d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalNewTicket">
                <i class="fas fa-plus-circle"></i>
                <span>Buat Tiket Baru</span>
            </button>
        </div>
    </div>
</div>

<?php if ($msg = session()->getFlashdata('success')): ?>
    <div class="alert alert-success rounded-4 mb-4 d-flex align-items-center gap-2">
        <i class="fas fa-check-circle fs-5"></i>
        <div><?= esc($msg) ?></div>
    </div>
<?php endif; ?>
<?php if ($msg = session()->getFlashdata('error')): ?>
    <div class="alert alert-danger rounded-4 mb-4 d-flex align-items-center gap-2">
        <i class="fas fa-exclamation-triangle fs-5"></i>
        <div><?= esc($msg) ?></div>
    </div>
<?php endif; ?>

<!-- Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
            <div class="d-flex align-items-center gap-3">
                <span class="w-48-px h-48-px rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fs-4">
                    <i class="fas fa-ticket-alt"></i>
                </span>
                <div>
                    <span class="text-muted small d-block">Total Tiket Konsultasi</span>
                    <h4 class="fw-bold mb-0 text-dark"><?= number_format($totalTickets ?? 0) ?></h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
            <div class="d-flex align-items-center gap-3">
                <span class="w-48-px h-48-px rounded-circle bg-warning-subtle text-warning-emphasis d-flex align-items-center justify-content-center fs-4">
                    <i class="fas fa-hourglass-half"></i>
                </span>
                <div>
                    <span class="text-muted small d-block">Sedang Ditelaah / Aktif</span>
                    <h4 class="fw-bold mb-0 text-warning-emphasis"><?= number_format($activeCount ?? 0) ?></h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
            <div class="d-flex align-items-center gap-3">
                <span class="w-48-px h-48-px rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center fs-4">
                    <i class="fas fa-check-double"></i>
                </span>
                <div>
                    <span class="text-muted small d-block">Terbalas &amp; Selesai</span>
                    <h4 class="fw-bold mb-0 text-success"><?= number_format($repliedCount ?? 0) ?></h4>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tickets List Card -->
<div class="card border-0 shadow-sm rounded-4 p-3 p-md-4 bg-white mb-4">
    <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom flex-wrap gap-2">
        <h5 class="fw-bold mb-0 text-dark" style="font-size: 16px;">
            <i class="fas fa-list-alt text-primary me-2"></i>Daftar Percakapan Tiket
        </h5>
        <span class="text-muted small">Menampilkan <?= count($tickets) ?> tiket</span>
    </div>

    <?php if (empty($tickets)): ?>
        <div class="text-center py-5">
            <i class="fas fa-comments fs-1 text-muted opacity-50 mb-3 d-block"></i>
            <h5 class="fw-bold text-dark mb-1">Belum Ada Tiket Konsultasi</h5>
            <p class="text-muted small mb-3">Anda belum pernah mengajukan pertanyaan atau permohonan konsultasi pengadaan barang dan jasa.</p>
            <button type="button" class="btn btn-primary radius-8 px-4 py-2 fw-bold" data-bs-toggle="modal" data-bs-target="#modalNewTicket">
                <i class="fas fa-plus-circle me-1"></i> Ajukan Konsultasi Sekarang
            </button>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table bordered-table sm-table mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width: 160px;">No. Tiket</th>
                        <th>Subjek &amp; Perihal</th>
                        <th>Kategori</th>
                        <th>Waktu Pengajuan</th>
                        <th>Status</th>
                        <th style="width: 130px;" class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tickets as $t): ?>
                        <?php
                        $badgeClass = match ($t['status']) {
                            'unread'        => 'bg-danger-subtle text-danger border border-danger-subtle',
                            'waiting_admin' => 'bg-info-subtle text-info border border-info-subtle',
                            'in_progress'   => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
                            'waiting_user'  => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
                            'read'          => 'bg-light text-secondary border',
                            'replied'       => 'bg-success-subtle text-success border border-success-subtle',
                            'closed'        => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
                            default         => 'bg-light text-dark border',
                        };
                        $statusLabel = match ($t['status']) {
                            'unread'        => 'Menunggu Ditelaah',
                            'waiting_admin' => 'Tanggapan Terkirim',
                            'in_progress'   => 'Sedang Ditelaah Petugas',
                            'waiting_user'  => 'Menunggu Balasan Anda',
                            'read'          => 'Sudah Dibaca Petugas',
                            'replied'       => 'Telah Dibalas Petugas',
                            'closed'        => 'Selesai &amp; Ditutup',
                            default         => ucfirst($t['status']),
                        };
                        ?>
                        <tr>
                            <td>
                                <a href="<?= route_to('workspace.consultation.detail', $t['ticket_number']) ?>" class="fw-bold font-monospace text-primary text-decoration-none small">
                                    <?= esc($t['ticket_number']) ?>
                                </a>
                                <?php if (! empty($t['threads_count']) && $t['threads_count'] > 0): ?>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle text-2xs d-block mt-1" style="width: fit-content;">
                                        <i class="fas fa-comment-dots me-1"></i><?= $t['threads_count'] ?> balasan
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?= route_to('workspace.consultation.detail', $t['ticket_number']) ?>" class="fw-bold text-dark text-decoration-none d-block small mb-1">
                                    <?= esc($t['subject']) ?>
                                </a>
                                <p class="text-muted text-xs mb-0 text-truncate" style="max-width: 320px;">
                                    <?= esc($t['message']) ?>
                                </p>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border text-2xs">
                                    <?= esc($t['service_category'] ?: 'Umum') ?>
                                </span>
                            </td>
                            <td>
                                <span class="small d-block text-dark"><?= date('d M Y', strtotime($t['created_at'])) ?></span>
                                <small class="text-muted text-2xs"><?= date('H:i', strtotime($t['created_at'])) ?> WIB</small>
                            </td>
                            <td>
                                <span class="badge <?= $badgeClass ?> px-2 py-1 text-2xs">
                                    <?= $statusLabel ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="<?= route_to('workspace.consultation.detail', $t['ticket_number']) ?>" class="btn btn-sm btn-primary radius-8 px-3 fw-bold d-inline-flex align-items-center gap-1">
                                    <span>Buka Percakapan</span>
                                    <i class="fas fa-chevron-right small"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- Modal Buat Tiket Konsultasi Baru -->
<div class="modal fade" id="modalNewTicket" tabindex="-1" aria-labelledby="modalNewTicketLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 shadow">
            <form action="<?= route_to('workspace.consultation.store') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-header border-bottom p-3 px-4">
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="modalNewTicketLabel">
                            <i class="fas fa-comments text-primary me-2"></i>Formulir Konsultasi Pengadaan Baru
                        </h5>
                        <small class="text-muted">Ajukan pertanyaan atau konsultasi pengadaan barang dan jasa langsung ke tim Helpdesk PBJ.</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="alert alert-info py-2 px-3 rounded-3 small mb-3">
                        <i class="fas fa-info-circle me-1"></i> Anda login sebagai <strong><?= esc(session()->get('name') ?: session()->get('username')) ?></strong> (<?= esc(session()->get('email')) ?>). Tiket ini akan terhubung langsung dengan akun Anda sehingga Anda dapat berdiskusi dua arah secara realtime.
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-dark">Kategori Layanan *</label>
                            <select name="service_type" class="form-select text-sm radius-8" required>
                                <option value="" disabled selected>Pilih Kategori Permohonan...</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= esc($cat) ?>"><?= esc($cat) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-dark">No. WhatsApp / Telepon</label>
                            <input type="text" name="phone" class="form-control text-sm radius-8" placeholder="Contoh: 081234567890" value="<?= esc(session()->get('phone') ?? '') ?>">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold small text-dark">Subjek / Pokok Pertanyaan *</label>
                            <input type="text" name="subject" class="form-control text-sm radius-8" placeholder="Contoh: Permohonan Klarifikasi Metode Pengadaan E-Purchasing Komputer" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold small text-dark">Uraian Pertanyaan / Isi Pesan *</label>
                            <textarea name="message" class="form-control text-sm radius-8" rows="5" placeholder="Tuliskan secara jelas pokok persoalan atau pertanyaan yang ingin Anda konsultasikan..." required></textarea>
                            <small class="text-muted">Minimal 10 karakter.</small>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-top p-3 px-4">
                    <button type="button" class="btn btn-outline-secondary radius-8 px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary radius-8 px-4 fw-bold">
                        <i class="fas fa-paper-plane me-1"></i> Kirim Permohonan Konsultasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
