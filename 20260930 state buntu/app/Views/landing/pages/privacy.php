<?= $this->extend('landing/layouts/public') ?>

<?= $this->section('content') ?>
<?= view('landing/components/page_header', [
    'title' => 'Privasi',
    'breadcrumbs' => [['label' => 'Privasi']],
]) ?>

<section class="blog-details">
    <div class="container"><div class="row justify-content-center"><div class="col-xl-9 col-lg-10"><div class="blog-details__left"><div class="blog-details__content">
        <h2 class="blog-details__title">Kebijakan Privasi BegawiPBJ</h2>
        <p>BegawiPBJ berkomitmen melindungi data pribadi dan informasi pengguna sesuai klasifikasi data, kebutuhan layanan, serta prinsip akses minimum.</p>
        <h3>Data yang diproses</h3><p>Data diproses untuk registrasi, autentikasi, pembelajaran, sertifikasi, notifikasi, administrasi layanan, dan pelaporan sesuai kebutuhan yang sah.</p>
        <h3>Penggunaan dan akses</h3><p>Akses terhadap data dibatasi berdasarkan role, permission, ownership, assignment, scope, serta status workflow. Data restricted tidak ditampilkan pada halaman publik.</p>
        <h3>Penyimpanan dan keamanan</h3><p>File internal dan restricted disimpan pada storage private. Sistem menerapkan validasi, audit, masking, retention, dan pengendalian akses sesuai kebijakan proyek.</p>
        <h3>Statistik publik</h3><p>Statistik publik hanya menampilkan data agregat yang telah melalui disclosure control, threshold, suppression, atau grouping bila diperlukan.</p>
        <p>Dokumen kebijakan lengkap mengacu pada kebijakan privasi dan klasifikasi data proyek BegawiPBJ.</p>
    </div></div></div></div></div>
</section>
<?= $this->endSection() ?>