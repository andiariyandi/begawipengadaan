<?= $this->extend('landing/layouts/public') ?>
<?= $this->section('content') ?>
<?= view('landing/components/page_header', ['page_title' => $pageTitle]) ?>
<section class="learning-detail"><div class="container"><div class="row justify-content-center"><div class="col-xl-7"><div class="learning-detail__card"><h3>Sertifikat Terverifikasi</h3><p>Nomor sertifikat</p><strong><?= esc($certificateId ?? 'BGPBJ-2026-0001') ?></strong><p>Nama peserta</p><strong>Peserta Pembelajaran PBJ Lampung</strong><p>Program</p><strong>Kompetensi Dasar Pengadaan</strong><p>Status</p><strong style="color:#16803a">Valid dan tercatat</strong><br><a href="<?= site_url('verifikasi-sertifikat') ?>" class="thm-btn mt-4"><span class="icon-angles-right"></span> Verifikasi Lain</a></div></div></div></div></section>
<?= $this->endSection() ?>
