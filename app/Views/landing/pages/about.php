<?= $this->extend('landing/layouts/public') ?>

<?= $this->section('content') ?>
<section class="page__title-area pt-120 pb-90" data-background="<?= base_url('assets/main/assets/img/page-title/page-title.jpg') ?>">
    <div class="page__title-shape"><img class="page-title-shape-5" src="<?= base_url('assets/main/assets/img/page-title/page-title-shape-1.png') ?>" alt=""></div>
    <div class="container"><div class="row"><div class="col-xxl-12"><div class="page__title-content text-center"><h1 class="page__title">Tentang BegawiPBJ</h1><?= view('landing/components/breadcrumb', ['breadcrumbs' => [['label' => 'Tentang BegawiPBJ']]]) ?></div></div></div></div>
</section>

<section class="about__area pt-115 pb-120">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-xxl-6 col-xl-6 col-lg-6">
                <div class="about__thumb-wrapper p-relative">
                    <div class="about__thumb ml-100"><img src="<?= base_url('assets/main/assets/img/about/about.jpg') ?>" alt="BegawiPBJ"></div>
                    <div class="about__banner mt--70"><img src="<?= base_url('assets/main/assets/img/about/about-banner.jpg') ?>" alt="Kolaborasi"></div>
                </div>
            </div>
            <div class="col-xxl-6 col-xl-6 col-lg-6">
                <div class="about__content pl-70 pr-60 pt-25">
                    <div class="section__title-wrapper mb-25">
                        <span class="section__sub-title">BegawiPBJ</span>
                        <h2 class="section__title">Menghubungkan <span class="yellow-bg">pembelajaran</span>, pengetahuan, dan layanan PBJ.</h2>
                        <p>BegawiPBJ dirancang sebagai portal terpadu untuk mendukung ekosistem Pengadaan Barang/Jasa melalui pengalaman belajar, akses referensi, informasi regulasi, agenda, dan layanan sertifikat digital.</p>
                    </div>
                    <div class="about__list mb-40"><ul>
                        <li>Arena Belajar untuk program, course, kelas, dan evaluasi</li>
                        <li>Bunga Rampai untuk artikel, FAQ, studi kasus, template, serta media</li>
                        <li>Repository Regulasi PBJ dengan informasi versi dan relasi dokumen</li>
                        <li>Informasi berita, event, dan sertifikat digital</li>
                    </ul></div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="why__area grey-bg pt-115 pb-85">
    <div class="container">
        <div class="row justify-content-center"><div class="col-xxl-7 col-xl-7 col-lg-8"><div class="section__title-wrapper text-center mb-60"><span class="section__sub-title">Nilai Utama</span><h2 class="section__title">Dibangun untuk ekosistem PBJ yang saling terhubung.</h2></div></div></div>
        <div class="row">
            <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6"><div class="why__item text-center mb-30"><div class="why__icon"><span><i class="flaticon-team"></i></span></div><div class="why__content"><h3>Kolaboratif</h3><p>Menghubungkan peserta, trainer, evaluator, operator, dan pengelola layanan.</p></div></div></div>
            <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6"><div class="why__item text-center mb-30"><div class="why__icon"><span><i class="flaticon-puzzle"></i></span></div><div class="why__content"><h3>Terstruktur</h3><p>Informasi dan pembelajaran dikelola dalam domain yang konsisten serta mudah dijelajahi.</p></div></div></div>
            <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6"><div class="why__item text-center mb-30"><div class="why__icon"><span><i class="flaticon-certificate"></i></span></div><div class="why__content"><h3>Terukur</h3><p>Mendukung progres pembelajaran, sertifikasi, dan statistik publik secara teragregasi.</p></div></div></div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>