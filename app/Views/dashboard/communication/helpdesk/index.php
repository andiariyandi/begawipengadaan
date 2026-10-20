<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Widget WhatsApp Helpdesk') ?></h6>
        <p class="text-secondary-light mb-0">Kelola nomor WhatsApp admin/helpdesk PBJ, teks pesan sambutan, bubble teaser, dan pilihan topik konsultasi cepat.</p>
    </div>
</div>

<div class="row gy-24">
    <!-- Form Pengaturan Widget -->
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header border-bottom py-16 px-24">
                <h6 class="text-sm fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                    <iconify-icon icon="logos:whatsapp-icon" class="text-xl"></iconify-icon>
                    Konfigurasi Widget WhatsApp
                </h6>
            </div>
            <div class="card-body p-24">
                <form method="post" action="<?= site_url('dashboard/komunikasi/whatsapp-widget/pengaturan') ?>">
                    <?= csrf_field() ?>
                    <div class="mb-16">
                        <div class="form-check form-switch d-flex align-items-center gap-2">
                            <input class="form-check-input" type="checkbox" name="wa_enabled" value="1" id="waEnabledSwitch" <?= ($settings['wa_enabled'] ?? '1') === '1' ? 'checked' : '' ?> style="width: 2.5em; height: 1.3em;">
                            <label class="form-check-label fw-bold text-dark text-sm mb-0" for="waEnabledSwitch">
                                Aktifkan Widget Chat Melayang
                            </label>
                        </div>
                    </div>
                    <div class="mb-16">
                        <label class="form-label fw-semibold text-xs mb-6">Nomor WhatsApp Resmi (Format: 628xxxxxxxxxx) <span class="text-danger">*</span></label>
                        <input type="text" name="wa_phone" class="form-control radius-8 font-monospace" value="<?= esc($settings['wa_phone'] ?? '6285117779783') ?>" required>
                        <small class="text-muted text-2xs">Gunakan kode negara tanpa spasi atau strip (contoh: 6285117779783).</small>
                    </div>
                    <div class="mb-16">
                        <label class="form-label fw-semibold text-xs mb-6">Judul Header Widget</label>
                        <input type="text" name="wa_title" class="form-control radius-8" value="<?= esc($settings['wa_title'] ?? 'Helpdesk BegawiPengadaan') ?>" required>
                    </div>
                    <div class="mb-16">
                        <label class="form-label fw-semibold text-xs mb-6">Subjudul Header Widget</label>
                        <input type="text" name="wa_subtitle" class="form-control radius-8" value="<?= esc($settings['wa_subtitle'] ?? 'Biro PBJ Setda Prov. Lampung') ?>" required>
                    </div>
                    <div class="mb-16">
                        <label class="form-label fw-semibold text-xs mb-6">Teks Balon Teaser Melayang</label>
                        <input type="text" name="wa_teaser_text" class="form-control radius-8" value="<?= esc($settings['wa_teaser_text'] ?? 'Butuh bantuan seputar PBJ? Chat kami! 👋') ?>">
                    </div>
                    <div class="mb-20">
                        <label class="form-label fw-semibold text-xs mb-6">Pesan Sambutan Chat Bubble Pertama</label>
                        <textarea name="wa_welcome_text" class="form-control radius-8" rows="4"><?= esc($settings['wa_welcome_text'] ?? "Halo! Selamat datang di BegawiPengadaan 👋\n\nAda yang bisa kami bantu terkait regulasi PBJ, pelatihan di Arena Belajar, atau template dokumen kerja?") ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary-600 w-100 radius-8 py-11 text-sm fw-semibold">Simpan Konfigurasi WhatsApp</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Manajemen Topik Cepat (Quick Chips) -->
    <div class="col-lg-7">
        <div class="card mb-24">
            <div class="card-header border-bottom py-16 px-24 d-flex align-items-center justify-content-between flex-wrap gap-2">
                <h6 class="text-sm fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:chat-square-code-bold" class="text-primary text-xl"></iconify-icon>
                    Pilihan Topik Cepat (Quick Chips)
                </h6>
                <button type="button" class="btn btn-primary-600 btn-sm radius-8 px-12 py-6 d-inline-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#modalTambahTopik">
                    <iconify-icon icon="solar:add-circle-outline"></iconify-icon>
                    <span>Tambah Topik</span>
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table bordered-table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th style="width: 50px;">Urutan</th>
                                <th>Topik & Pesan Otomatis</th>
                                <th style="width: 100px;">Status</th>
                                <th style="width: 100px;" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($topics)): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">Belum ada topik cepat.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($topics as $t): ?>
                                    <tr>
                                        <td class="text-center fw-bold text-secondary"><?= esc($t['sort_order']) ?></td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                <iconify-icon icon="<?= esc($t['icon_class'] ?: 'solar:chat-round-dots-bold') ?>" class="text-primary"></iconify-icon>
                                                <strong class="text-sm text-dark"><?= esc($t['topic_title']) ?></strong>
                                            </div>
                                            <small class="text-secondary-light d-block text-truncate" style="max-width: 380px;">"<?= esc($t['default_message']) ?>"</small>
                                        </td>
                                        <td>
                                            <a href="<?= site_url('dashboard/komunikasi/whatsapp-widget/toggle-topic/' . $t['id']) ?>" class="badge <?= $t['is_active'] ? 'bg-success-subtle text-success-main border border-success-subtle' : 'bg-neutral-100 text-neutral-600 border' ?> px-8 py-4 radius-4 text-2xs" title="Ubah Status">
                                                <?= $t['is_active'] ? 'Aktif' : 'Nonaktif' ?>
                                            </a>
                                        </td>
                                        <td class="text-center">
                                            <a href="<?= site_url('dashboard/komunikasi/whatsapp-widget/hapus-topic/' . $t['id']) ?>" onclick="return confirm('Hapus topik ini?')" class="btn btn-outline-danger btn-sm radius-8 px-8 py-4" title="Hapus Topik">
                                                <iconify-icon icon="solar:trash-bin-trash-linear"></iconify-icon>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Topik Cepat -->
<div class="modal fade" id="modalTambahTopik" tabindex="-1" aria-labelledby="modalTambahTopikLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content radius-12 border-0">
            <form method="post" action="<?= site_url('dashboard/komunikasi/whatsapp-widget/simpan-topic') ?>">
                <?= csrf_field() ?>
                <div class="modal-header border-bottom py-16 px-24">
                    <h6 class="modal-title text-base fw-bold text-dark" id="modalTambahTopikLabel">Tambah Topik Cepat WhatsApp</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-24">
                    <div class="mb-16">
                        <label class="form-label fw-semibold text-xs mb-6">Judul Topik (Tampil pada Tombol Chip) <span class="text-danger">*</span></label>
                        <input type="text" name="topic_title" class="form-control radius-8" placeholder="Misal: Regulasi & Payung Hukum" required>
                    </div>
                    <div class="mb-16">
                        <label class="form-label fw-semibold text-xs mb-6">Pesan Otomatis (Prefilled Text ke WhatsApp) <span class="text-danger">*</span></label>
                        <textarea name="default_message" class="form-control radius-8" rows="3" placeholder="Halo Helpdesk BegawiPengadaan, saya ingin bertanya tentang..." required></textarea>
                    </div>
                    <div class="row gy-16">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-xs mb-6">Ikon Iconify</label>
                            <input type="text" name="icon_class" class="form-control radius-8" value="solar:chat-round-dots-bold">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-xs mb-6">Urutan Tampil</label>
                            <input type="number" name="sort_order" class="form-control radius-8" value="1" min="1" max="99">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pb-24 px-24">
                    <button type="button" class="btn btn-outline-neutral-300 radius-8 px-20 py-10" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-10">Simpan Topik</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
