<?= $this->extend('landing/layouts/public') ?>
<?= $this->section('content') ?>
<?= view('landing/components/page_header', ['page_title' => $pageTitle]) ?>
<section class="learning-detail"><div class="container"><div class="row justify-content-center"><div class="col-xl-7"><div class="learning-detail__card"><h3>Periksa Keabsahan Sertifikat</h3><p>Masukkan nomor sertifikat untuk melihat status penerbitan.</p><form action="<?= site_url('verifikasi-sertifikat/hasil') ?>" method="get"><label for="certificate-number">Nomor sertifikat</label><input id="certificate-number" name="nomor" type="text" class="form-control" placeholder="Contoh: BGPBJ-2026-0001" required><button class="thm-btn mt-4" type="submit"><span class="icon-search"></span> Verifikasi</button></form></div></div></div></div></section>
<?= $this->endSection() ?>
