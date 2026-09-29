<?= $this->extend('landing/layouts/public') ?>

<?= $this->section('content') ?>
<section class="page__title-area pt-120 pb-90" data-background="<?= base_url('assets/main/assets/img/page-title/page-title.jpg') ?>">
    <div class="page__title-shape"><img class="page-title-shape-5" src="<?= base_url('assets/main/assets/img/page-title/page-title-shape-1.png') ?>" alt=""></div>
    <div class="container"><div class="row"><div class="col-xxl-12"><div class="page__title-content text-center"><h1 class="page__title">Privasi</h1><?= view('landing/components/breadcrumb', ['breadcrumbs' => [['label' => 'Privasi']]]) ?></div></div></div></div>
</section>

<section class="blog__area pt-115 pb-120">
    <div class="container"><div class="row justify-content-center"><div class="col-xxl-9 col-xl-9 col-lg-10"><article class="blog__wrapper"><div class="blog__text mb-40">
        <h2>Kebijakan Privasi BegawiPBJ</h2>
        <p>BegawiPBJ berkomitmen melindungi data pribadi dan informasi pengguna sesuai klasifikasi data, kebutuhan layanan, dan prinsip akses minimum.</p>
        <h3>Data yang diproses</h3>
        <p>Data diproses untuk registrasi, autentikasi, pembelajaran, sertifikasi, notifikasi, administrasi layanan, dan pelaporan sesuai kebutuhan yang sah.</p>
        <h3>Penggunaan dan akses</h3>
        <p>Akses terhadap data dibatasi berdasarkan role, permission, ownership, assignment, scope, serta status workflow. Data restricted tidak ditampilkan melalui halaman publik.</p>
        <h3>Penyimpanan dan keamanan</h3>
        <p>File internal dan restricted disimpan pada storage private. Sistem menerapkan prinsip validasi, audit, masking, retention, dan pengendalian akses sesuai kebijakan proyek.</p>
        <h3>Statistik publik</h3>
        <p>Statistik publik hanya menampilkan data agregat yang telah melalui disclosure control, threshold, suppression, atau grouping bila diperlukan.</p>
        <p class="mb-0">Dokumen kebijakan lengkap mengacu pada kebijakan privasi dan klasifikasi data proyek BegawiPBJ.</p>
    </div></article></div></div></div>
</section>
<?= $this->endSection() ?>