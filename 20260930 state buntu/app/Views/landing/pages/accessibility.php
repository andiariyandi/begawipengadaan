<?= $this->extend('landing/layouts/public') ?>

<?= $this->section('content') ?>
<?= view('landing/components/page_header', [
    'title' => 'Aksesibilitas',
    'breadcrumbs' => [['label' => 'Aksesibilitas']],
]) ?>

<section class="blog-details">
    <div class="container"><div class="row justify-content-center"><div class="col-xl-9 col-lg-10"><div class="blog-details__left"><div class="blog-details__content">
        <h2 class="blog-details__title">Komitmen Aksesibilitas</h2>
        <p>BegawiPBJ berupaya menyediakan pengalaman digital yang dapat digunakan oleh berbagai pengguna, perangkat, dan kebutuhan akses.</p>
        <h3>Fitur dan praktik</h3>
        <ul>
            <li>Struktur heading dan navigasi yang konsisten.</li>
            <li>Label pada form dan kontrol interaktif.</li>
            <li>Alternatif teks untuk media informatif saat konten dipublikasikan.</li>
            <li>Dukungan penggunaan keyboard untuk elemen interaktif utama.</li>
            <li>Pesan error, empty state, dan status proses yang jelas.</li>
        </ul>
        <h3>Umpan balik</h3><p>Jika Anda menemukan kendala aksesibilitas, silakan kirimkan informasi melalui halaman <a href="<?= site_url('kontak') ?>">Kontak</a>.</p>
    </div></div></div></div></div>
</section>
<?= $this->endSection() ?>