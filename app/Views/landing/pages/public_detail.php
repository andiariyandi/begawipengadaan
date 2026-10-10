<?= $this->extend('landing/layouts/public') ?>

<?= $this->section('content') ?>
<?= view('landing/components/page_header', ['page_title' => $pageTitle]) ?>

<section class="about-three learning-detail">
    <div class="container">
        <?= view('dashboard/components/flash_message') ?>
        <div class="row">
            <div class="col-xl-8">
                <div class="about-three__right">
                    <div class="section-title-two text-left">
                        <div class="section-title-two__tagline-box">
                            <span class="section-title-two__tagline"><?= esc($category ?? 'BegawiPBJ Publik') ?></span>
                        </div>
                        <h2 class="section-title-two__title"><?= esc($itemTitle) ?></h2>
                    </div>

                    <?php if (!empty($coverUrl)): ?>
                        <div class="mb-4">
                            <img src="<?= esc($coverUrl) ?>" alt="<?= esc($itemTitle) ?>" class="img-fluid rounded" style="max-height: 400px; width: 100%; object-fit: cover;">
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($description)): ?>
                        <p class="learning-detail__lead fw-semibold"><?= esc($description) ?></p>
                    <?php endif; ?>

                    <?php if (!empty($content)): ?>
                        <div class="about-three__text-1 mb-4">
                            <?= nl2br(esc($content)) ?>
                        </div>
                    <?php else: ?>
                        <p class="about-three__text-1">Halaman ini memuat informasi resmi, pembaruan, atau agenda publik dalam ekosistem Pengadaan Barang/Jasa Pemerintah Provinsi Lampung.</p>
                    <?php endif; ?>

                    <?php if (!empty($event)): ?>
                        <!-- Event Action Box -->
                        <div class="card p-4 border bg-light mb-4">
                            <h4 class="h5 mb-3">Pendaftaran Peserta Event</h4>
                            <?php if (!empty($isRegistered)): ?>
                                <div class="alert alert-success mb-0">
                                    <strong>✅ Anda sudah terdaftar</strong> pada kegiatan ini. Silakan simpan jadwal dan pantau email/notifikasi akun Anda.
                                </div>
                            <?php elseif (($event['status'] ?? '') === 'completed'): ?>
                                <div class="alert alert-secondary mb-0">Kegiatan ini telah selesai diselenggarakan.</div>
                            <?php else: ?>
                                <p class="mb-3">Kuota: <strong><?= esc($registeredCount ?? 0) ?> / <?= esc($event['capacity'] ?? '100') ?> peserta</strong></p>
                                <form method="post" action="<?= site_url('event/' . esc($event['slug']) . '/daftar') ?>">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="thm-btn"><span class="icon-angles-right"></span> Daftar Sekarang</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (str_contains(strtolower($pageTitle), 'regulasi')): ?>
                        <a href="<?= site_url('regulasi/' . url_title($itemTitle, '-', true) . '/unduh') ?>" class="thm-btn"><span class="icon-download"></span> Unduh Dokumen</a>
                    <?php endif; ?>

                    <a href="<?= site_url($backUrl ?? 'berita') ?>" class="thm-btn"><span class="icon-angles-right"></span> Kembali</a>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="learning-detail__card">
                    <h3>Informasi</h3>
                    <p>Status</p>
                    <strong><?= esc($statusLabel ?? 'Publik') ?></strong>
                    <p>Tanggal</p>
                    <strong><?= esc($dateLabel ?? date('d F Y')) ?></strong>
                    <?php if (!empty($locationLabel)): ?>
                        <p>Lokasi / Moda</p>
                        <strong><?= esc($locationLabel) ?></strong>
                    <?php endif; ?>
                    <p>Sumber / Penyelenggara</p>
                    <strong><?= esc($authorLabel ?? 'Biro Pengadaan Barang dan Jasa Provinsi Lampung') ?></strong>
                    <?php if (!empty($viewCount)): ?>
                        <p>Dilihat</p>
                        <strong><?= esc($viewCount) ?> kali</strong>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
