<?= $this->extend('landing/layouts/public') ?>

<?= $this->section('content') ?>
<?= view('landing/components/page_header', [
    'title' => 'Syarat dan Ketentuan',
    'breadcrumbs' => [['label' => 'Syarat dan Ketentuan']],
]) ?>

<section class="blog-details">
    <div class="container"><div class="row justify-content-center"><div class="col-xl-9 col-lg-10"><div class="blog-details__left"><div class="blog-details__content">
        <h2 class="blog-details__title">Syarat Penggunaan BegawiPBJ</h2>
        <h3>Penggunaan layanan</h3><p>Pengguna wajib menggunakan layanan BegawiPBJ secara sah, bertanggung jawab, dan sesuai peran, permission, scope, serta ketentuan domain yang berlaku.</p>
        <h3>Akun dan keamanan</h3><p>Pengguna bertanggung jawab menjaga kredensial akun, tidak membagikan token atau kata sandi, serta segera melaporkan aktivitas yang tidak sah.</p>
        <h3>Konten dan dokumen</h3><p>Konten, materi, regulasi, media, dan dokumen hanya boleh digunakan sesuai visibility, lisensi, status publikasi, dan otorisasi yang diberikan sistem.</p>
        <h3>Pembelajaran dan sertifikat</h3><p>Enrollment, assessment, nilai, feedback, dan penerbitan sertifikat mengikuti status kelas, batas percobaan, eligibility, serta workflow yang berlaku.</p>
        <h3>Perubahan layanan</h3><p>Sistem dapat diperbarui untuk kebutuhan keamanan, perbaikan layanan, atau penyesuaian kebijakan tanpa mengurangi kewajiban perlindungan data pengguna.</p>
    </div></div></div></div></div>
</section>
<?= $this->endSection() ?>