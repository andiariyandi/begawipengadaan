<?= $this->extend('landing/layouts/public') ?>

<?= $this->section('content') ?>
<section class="page__title-area pt-120 pb-90" data-background="<?= base_url('assets/main/assets/img/page-title/page-title.jpg') ?>">
    <div class="page__title-shape"><img class="page-title-shape-5" src="<?= base_url('assets/main/assets/img/page-title/page-title-shape-1.png') ?>" alt=""></div>
    <div class="container"><div class="row"><div class="col-xxl-12"><div class="page__title-content text-center"><h1 class="page__title">Syarat dan Ketentuan</h1><?= view('landing/components/breadcrumb', ['breadcrumbs' => [['label' => 'Syarat dan Ketentuan']]]) ?></div></div></div></div>
</section>

<section class="blog__area pt-115 pb-120">
    <div class="container"><div class="row justify-content-center"><div class="col-xxl-9 col-xl-9 col-lg-10"><article class="blog__wrapper"><div class="blog__text mb-40">
        <h2>Syarat Penggunaan BegawiPBJ</h2>
        <h3>Penggunaan layanan</h3>
        <p>Pengguna wajib menggunakan layanan BegawiPBJ secara sah, bertanggung jawab, dan sesuai peran, permission, scope, serta ketentuan domain yang berlaku.</p>
        <h3>Akun dan keamanan</h3>
        <p>Pengguna bertanggung jawab menjaga kredensial akun, tidak membagikan token atau kata sandi, serta segera melaporkan aktivitas yang tidak sah.</p>
        <h3>Konten dan dokumen</h3>
        <p>Konten, materi, regulasi, media, dan dokumen hanya boleh digunakan sesuai visibility, lisensi, status publikasi, dan otorisasi yang diberikan sistem.</p>
        <h3>Pembelajaran dan sertifikat</h3>
        <p>Enrollment, assessment, nilai, feedback, dan penerbitan sertifikat mengikuti status kelas, batas percobaan, eligibility, serta workflow yang berlaku.</p>
        <h3>Perubahan layanan</h3>
        <p>Sistem dapat diperbarui untuk kebutuhan keamanan, perbaikan layanan, atau penyesuaian kebijakan tanpa mengurangi kewajiban perlindungan data pengguna.</p>
    </div></article></div></div></div>
</section>
<?= $this->endSection() ?>