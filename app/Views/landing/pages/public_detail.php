<?= $this->extend('landing/layouts/public') ?>
<?= $this->section('content') ?>
<?= view('landing/components/page_header', ['page_title' => $pageTitle]) ?>
<section class="about-three learning-detail"><div class="container"><div class="row"><div class="col-xl-8"><div class="about-three__right">
    <div class="section-title-two text-left"><div class="section-title-two__tagline-box"><span class="section-title-two__tagline">BegawiPBJ Publik</span></div><h2 class="section-title-two__title"><?= esc($itemTitle) ?></h2></div>
    <p class="learning-detail__lead"><?= esc($description) ?></p>
    <p class="about-three__text-1">Halaman ini berisi konten dummy yang dapat dikembangkan menjadi informasi resmi, pembaruan, atau dokumen publik sesuai kebutuhan pengelolaan Pengadaan Barang/Jasa Pemerintah Provinsi Lampung.</p>
    <?php if (str_contains(strtolower($pageTitle), 'regulasi')): ?><a href="<?= site_url('regulasi/' . url_title($itemTitle, '-', true) . '/unduh') ?>" class="thm-btn"><span class="icon-download"></span> Unduh Dokumen</a><?php endif; ?>
    <a href="<?= site_url($backUrl) ?>" class="thm-btn"><span class="icon-angles-right"></span> Kembali</a>
</div></div><div class="col-xl-4"><div class="learning-detail__card"><h3>Informasi</h3><p>Status</p><strong>Publik</strong><p>Diperbarui</p><strong>30 September 2026</strong><p>Sumber</p><strong>BegawiPBJ Lampung</strong></div></div></div></div></section>
<?= $this->endSection() ?>
