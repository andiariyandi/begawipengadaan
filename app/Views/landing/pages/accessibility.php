<?= $this->extend('landing/layouts/public') ?>

<?= $this->section('content') ?>
<section class="page__title-area pt-120 pb-90" data-background="<?= base_url('assets/main/assets/img/page-title/page-title.jpg') ?>">
    <div class="page__title-shape"><img class="page-title-shape-5" src="<?= base_url('assets/main/assets/img/page-title/page-title-shape-1.png') ?>" alt=""></div>
    <div class="container"><div class="row"><div class="col-xxl-12"><div class="page__title-content text-center"><h1 class="page__title">Aksesibilitas</h1><?= view('landing/components/breadcrumb', ['breadcrumbs' => [['label' => 'Aksesibilitas']]]) ?></div></div></div></div>
</section>

<section class="blog__area pt-115 pb-120">
    <div class="container"><div class="row justify-content-center"><div class="col-xxl-9 col-xl-9 col-lg-10"><article class="blog__wrapper"><div class="blog__text mb-40">
        <h2>Komitmen Aksesibilitas</h2>
        <p>BegawiPBJ berupaya menyediakan pengalaman digital yang dapat digunakan oleh berbagai pengguna, perangkat, dan kebutuhan akses.</p>
        <h3>Fitur dan praktik</h3>
        <ul>
            <li>Struktur heading dan navigasi yang konsisten.</li>
            <li>Label pada form dan kontrol interaktif.</li>
            <li>Kontras visual yang mengikuti kemampuan template yang digunakan.</li>
            <li>Alternatif teks untuk media informatif saat konten dipublikasikan.</li>
            <li>Dukungan penggunaan keyboard untuk elemen interaktif utama.</li>
            <li>Pesan error, empty state, dan status proses yang jelas.</li>
        </ul>
        <h3>Umpan balik</h3>
        <p>Jika Anda menemukan kendala aksesibilitas, silakan kirimkan informasi melalui halaman <a href="<?= site_url('kontak') ?>">Kontak</a>.</p>
    </div></article></div></div></div>
</section>
<?= $this->endSection() ?>