<?= $this->extend('landing/layouts/public') ?>
<?= $this->section('content') ?>

<?= view('landing/components/page_header', ['page_title' => $pageTitle]) ?>

<section class="about-three learning-detail">
    <div class="container">
        <div class="row">
            <div class="col-xl-8">
                <div class="about-three__right">
                    <div class="section-title-two text-left">
                        <div class="section-title-two__tagline-box"><span class="section-title-two__tagline">Bunga Rampai BegawiPBJ</span></div>
                        <h2 class="section-title-two__title"><?= esc($itemTitle) ?></h2>
                    </div>
                    <p class="learning-detail__lead">Konten dummy ini menggambarkan halaman pengetahuan publik BegawiPBJ yang dapat dikembangkan menjadi artikel, studi kasus, atau dokumen pendukung PBJ.</p>
                    <p class="about-three__text-1">Materi disusun dengan bahasa yang mudah dipahami dan diarahkan untuk membantu aparatur serta pemangku kepentingan menerapkan Pengadaan Barang/Jasa secara transparan, efektif, dan akuntabel.</p>
                    <?php if ($catalogType === 'templates'): ?>
                        <a href="<?= site_url('bunga-rampai/template-dokumen/' . url_title($itemTitle, '-', true) . '/unduh') ?>" class="thm-btn"><span class="icon-download"></span> Unduh Template</a>
                    <?php endif; ?>
                    <a href="<?= site_url('bunga-rampai') ?>" class="thm-btn"><span class="icon-angles-right"></span> Kembali ke Bunga Rampai</a>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="learning-detail__card"><h3>Informasi Materi</h3><p>Kategori</p><strong><?= esc(ucfirst($catalogType)) ?></strong><p>Status</p><strong>Materi publik</strong><p>Terakhir diperbarui</p><strong>30 September 2026</strong></div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
