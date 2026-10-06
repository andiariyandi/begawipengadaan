<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= esc($pageTitle ?? $title ?? 'BegawiPBJ') ?></title>

    <link rel="apple-touch-icon" sizes="180x180" href="<?= base_url('assets/main/images/favicons/apple-touch-icon.png') ?>" />
    <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('assets/main/images/favicons/favicon-32x32.png') ?>" />
    <link rel="icon" type="image/png" sizes="16x16" href="<?= base_url('assets/main/images/favicons/favicon-16x16.png') ?>" />
    <link rel="manifest" href="<?= base_url('assets/main/images/favicons/site.webmanifest') ?>" />
    <meta name="description" content="<?= esc($metaDescription ?? 'BegawiPBJ - Platform informasi, pembelajaran, dan pengadaan barang/jasa Pemerintah Provinsi Lampung.') ?>" />
    <meta name="keywords" content="<?= esc($metaKeywords ?? 'BegawiPBJ, PBJ, pengadaan barang jasa, Provinsi Lampung') ?>" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Serif:ital,opsz,wght@0,8..144,100..900;1,8..144,100..900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="<?= base_url('assets/main/css/bootstrap.min.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/animate.min.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/custom-animate.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/swiper.min.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/font-awesome-all.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/jarallax.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/jquery.magnific-popup.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/odometer.min.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/flaticon.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/owl.carousel.min.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/owl.theme.default.min.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/nice-select.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/jquery-ui.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/aos.css') ?>" />

    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/banner.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/slider.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/footer.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/sliding-text.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/category.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/about.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/courses.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/why-choose.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/live-class.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/video-one.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/blog.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/counter.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/team.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/newsletter.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/testimonial.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/contact.css') ?>" />

    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/page-header.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/courses-search.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/event.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/faq.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/gallery.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/error.css') ?>" />

    <link rel="stylesheet" href="<?= base_url('assets/main/css/style.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/responsive.css') ?>" />

    <style>
        :root {
            --begawi-primary: #0c5aa6;
            --begawi-dark: #082f5c;
            --begawi-accent: #f3a20b;
        }

        .main-header {
            background: linear-gradient(180deg, rgba(5, 34, 68, .96) 0%, rgba(5, 34, 68, .88) 58%, rgba(5, 34, 68, .72) 100%);
        }

        .main-header .main-menu__wrapper {
            border-bottom: 1px solid rgba(255, 255, 255, .22);
        }

        .main-header .main-menu__wrapper::before {
            display: none;
        }

        .main-header .main-menu .main-menu__list > li > a,
        .main-header .main-menu__search {
            color: #fff;
        }

        .main-header .main-menu__list > li.current > a,
        .main-header .main-menu__list > li:hover > a,
        .main-header .main-menu__search:hover {
            color: var(--begawi-accent);
        }

        .main-menu__logo img { max-height: 58px; width: auto; }
        .main-menu__wrapper-inner {
            flex-wrap: nowrap;
        }

        .main-menu__left {
            gap: 24px;
            flex-shrink: 0;
        }

        .main-menu__main-menu-box {
            flex: 1 1 auto;
            min-width: 0;
        }

        .main-menu__list {
            justify-content: center;
        }

        .main-menu .main-menu__list > li + li {
            margin-left: 18px;
        }

        .main-menu .main-menu__list > li > a {
            font-size: 15px;
        }

        .main-menu__right {
            gap: 14px;
            flex-shrink: 0;
        }

        .main-menu__btn-boxes {
            gap: 7px;
        }

        .main-menu__btn-box-1 .thm-btn,
        .main-menu__btn-box-2 .thm-btn {
            padding-left: 14px;
            padding-right: 14px;
            font-size: 14px;
        }

        .main-menu__btn-box-1 .thm-btn,
        .main-menu__btn-box-2 .thm-btn { min-width: auto; }
        .main-header .main-menu__btn-box-1 .thm-btn { background: transparent; border: 1px solid rgba(255, 255, 255, .8); color: #fff; }
        .main-header .main-menu__btn-box-1 .thm-btn:hover { background: #fff; color: var(--begawi-dark); }
        .main-header .main-menu__btn-box-2 .thm-btn { background: var(--begawi-accent); color: var(--begawi-dark); }
        .main-header .main-menu__btn-box-2 .thm-btn:hover { background: #fff; color: var(--begawi-dark); }
        .main-menu__list > li.current > a,
        .main-menu__list > li:hover > a { color: var(--begawi-primary); }
        .main-menu__category-btn p { font-size: 15px; }
        .site-footer__copyright-text a { color: var(--begawi-accent); }
        .page-header__inner h2 { text-transform: none; }
        .pagination-wrapper .disabled { opacity: .45; pointer-events: none; }

        .category-one .section-title__tagline,
        .category-one .section-title__title {
            color: #fff;
        }

        .category-one .section-title__tagline {
            text-shadow: 0 1px 2px rgba(0, 0, 0, .2);
        }

        .section-title__tagline,
        .section-title-two__tagline {
            line-height: 1.4;
        }

        .section-title__title,
        .section-title-two__title {
            line-height: 1.25;
        }

        .thm-btn,
        .category-one__btn,
        .courses-one__btn,
        .why-choose-one__btn {
            min-height: 48px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .main-slider__btn-box,
        .about-one__btn-box,
        .why-choose-one__btn-box,
        .courses-one__btn-box {
            margin-top: 28px;
        }

        .about-three__right .section-title-two {
            margin-bottom: 28px;
        }

        .about-three__text-1,
        .about-three__mission-vission-text {
            color: #475569;
            line-height: 1.8;
        }

        .landing-internal .page-header__inner h2,
        .landing-internal .thm-breadcrumb li,
        .landing-internal .thm-breadcrumb li a {
            text-shadow: 0 1px 2px rgba(0, 0, 0, .14);
        }

        .landing-internal .category-one {
            padding-top: 112px !important;
            padding-bottom: 104px !important;
        }

        .landing-internal .category-one .section-title {
            margin-bottom: 48px;
        }

        .landing-internal .category-one__single {
            min-height: 310px;
            padding: 32px 24px 26px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 12px 30px rgba(8, 47, 92, .16);
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .landing-internal .category-one .row,
        .landing-internal .knowledge-blog,
        .landing-internal .knowledge-events,
        .landing-internal .knowledge-gallery,
        .landing-internal .course-list__right,
        .landing-internal .knowledge-blog-list {
            display: flex;
            flex-wrap: wrap;
            row-gap: 28px;
        }

        .landing-internal .category-one .row > [class*="col-"],
        .landing-internal .knowledge-blog > [class*="col-"],
        .landing-internal .knowledge-events > [class*="col-"],
        .landing-internal .knowledge-gallery > [class*="col-"] {
            display: flex;
            flex-direction: column;
        }

        .landing-internal .course-list__right > [class*="col-"],
        .landing-internal .knowledge-blog-list > [class*="col-"] {
            display: flex;
            flex-direction: column;
        }

        .landing-internal .category-one .row > [class*="col-"] > *,
        .landing-internal .knowledge-blog > [class*="col-"] > *,
        .landing-internal .knowledge-events > [class*="col-"] > *,
        .landing-internal .knowledge-gallery > [class*="col-"] > * {
            width: 100%;
            height: 100%;
        }

        .landing-internal .course-list__right > [class*="col-"] > *,
        .landing-internal .knowledge-blog-list > [class*="col-"] > * {
            width: 100%;
            height: 100%;
        }

        @media (min-width: 1200px) {
            .landing-internal .course-list__right > [class*="col-"] {
                flex: 0 0 50%;
                max-width: 50%;
            }

            .landing-internal .learning-catalog > .container > .row:not(.course-list__right) > [class*="col-"] {
                flex: 0 0 25%;
                max-width: 25%;
            }

            .landing-internal .knowledge-blog-list > [class*="col-"] {
                flex: 0 0 50%;
                max-width: 50%;
            }
        }

        @media (min-width: 768px) and (max-width: 1199px) {
            .landing-internal .course-list__right > [class*="col-"],
            .landing-internal .learning-catalog > .container > .row:not(.course-list__right) > [class*="col-"],
            .landing-internal .knowledge-blog-list > [class*="col-"] {
                flex: 0 0 50%;
                max-width: 50%;
            }
        }

        @media (max-width: 767px) {
            .landing-internal .course-list__right > [class*="col-"],
            .landing-internal .learning-catalog > .container > .row:not(.course-list__right) > [class*="col-"],
            .landing-internal .knowledge-blog-list > [class*="col-"] {
                flex: 0 0 100%;
                max-width: 100%;
            }
        }

        .landing-internal .course-list__single,
        .landing-internal .knowledge-blog-list .blog-list__single {
            height: 100%;
        }

        .landing-internal .course-list__single {
            align-items: stretch;
            flex-direction: column;
            gap: 0;
            padding: 0;
            overflow: hidden;
        }

        .landing-internal .course-list__img-box {
            max-width: none;
        }

        .landing-internal .course-list__img img,
        .landing-internal .knowledge-blog-list .blog-list__img img {
            width: 100%;
            height: 230px;
            object-fit: cover;
        }

        .landing-internal .course-list__content,
        .landing-internal .knowledge-blog-list .blog-list__content {
            display: flex;
            flex: 1;
            flex-direction: column;
            padding: 24px;
        }

        .landing-internal .course-list__btn-and-client-info,
        .landing-internal .knowledge-blog-list .blog-list__btn-box {
            margin-top: auto;
        }

        .landing-internal .category-one__icon {
            width: 72px;
            height: 72px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            border-radius: 50%;
            background: #e9f2ff;
        }

        .landing-internal .category-one__icon img {
            width: 42px;
            height: 42px;
            object-fit: contain;
        }

        .landing-internal .category-one__title {
            margin: 0 0 12px;
            font-size: 21px;
            line-height: 1.3;
        }

        .landing-internal .category-one__title a {
            color: var(--begawi-dark);
        }

        .landing-internal .category-one__title a:hover {
            color: var(--begawi-primary);
        }

        .landing-internal .category-one__text {
            color: #475569;
            font-size: 15px;
            line-height: 1.7;
            margin: 0 0 24px;
        }

        .landing-internal .category-one__btn {
            margin-top: auto;
            padding: 11px 18px;
            border-radius: 6px;
            background: var(--begawi-primary);
            color: #fff;
            font-size: 14px;
            line-height: 1.2;
            transition: background-color .2s ease, transform .2s ease;
        }

        .landing-internal .category-one__btn:hover {
            background: var(--begawi-dark);
            color: #fff;
            transform: translateY(-2px);
        }

        .learning-catalog__intro {
            max-width: 720px;
            margin: 18px auto 0;
            color: rgba(255, 255, 255, .88);
            line-height: 1.75;
        }

        .learning-catalog__card {
            background: #ffffff;
            border-radius: 14px;
            box-shadow: 0 8px 24px rgba(8, 47, 92, .08);
            border: 1px solid #e6ebf2;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: transform .2s ease, box-shadow .2s ease;
        }
        .learning-catalog__card:hover { transform: translateY(-2px); box-shadow: 0 14px 32px rgba(8, 47, 92, .12); }

        .learning-catalog__thumb {
            position: relative;
            aspect-ratio: 16 / 9;
            overflow: hidden;
            background: #eef2f7;
        }
        .learning-catalog__thumb img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .learning-catalog__thumb--circle img { aspect-ratio: 1 / 1; border-radius: 50%; max-width: 160px; margin: 24px auto 0; }

        .learning-catalog__body { padding: 22px 22px 24px; display: flex; flex-direction: column; gap: 10px; flex: 1 1 auto; }
        .learning-catalog__badges { margin-bottom: 4px; }
        .learning-catalog__eyebrow { color: var(--begawi-primary); font-size: 12px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; }
        .learning-catalog__title { margin: 0; font-size: 20px; line-height: 1.35; color: var(--begawi-dark); }
        .learning-catalog__title a { color: inherit; text-decoration: none; }
        .learning-catalog__title a:hover { color: var(--begawi-primary); }
        .learning-catalog__text { margin: 0; color: #475569; font-size: 14px; line-height: 1.6; }
        .learning-catalog__meta { display: flex; flex-wrap: wrap; gap: 8px 14px; padding: 0; margin: 6px 0 0; font-size: 13px; color: #42526b; }
        .learning-catalog__meta li { display: inline-flex; align-items: center; gap: 6px; }
        .learning-catalog__actions { margin-top: auto; padding-top: 14px; display: flex; flex-wrap: wrap; gap: 8px; }
        .learning-catalog__enroll { margin: 0; }

        .learning-detail {
            padding: 112px 0;
        }

        .learning-detail__lead {
            max-width: 680px;
            color: #475569;
            font-size: 17px;
            line-height: 1.8;
            margin-bottom: 28px;
        }

        .learning-detail__list {
            margin: 0 0 30px;
        }

        .learning-detail__list li {
            display: flex;
            gap: 14px;
            align-items: flex-start;
        }

        .learning-detail__list li + li {
            margin-top: 20px;
        }

        .learning-detail__list > li > span {
            flex: 0 0 28px;
            width: 28px;
            height: 28px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: #e9f2ff;
            color: var(--begawi-primary);
        }

        .learning-detail__list h4 {
            margin: 0 0 5px;
            color: var(--begawi-dark);
            font-size: 18px;
        }

        .learning-detail__list p {
            margin: 0;
            color: #475569;
            line-height: 1.7;
        }

        .learning-detail__card {
            padding: 34px;
            border-radius: 14px;
            background: #f1f6ff;
            border: 1px solid #d7e6fb;
        }

        .learning-detail__card h3 {
            margin: 0 0 24px;
            color: var(--begawi-dark);
            font-size: 24px;
        }

        .learning-detail__card p {
            margin: 16px 0 4px;
            color: #64748b;
        }

        .learning-detail__card strong {
            color: var(--begawi-dark);
            font-size: 17px;
        }

        .knowledge-document-list {
            display: grid;
            gap: 18px;
            max-width: 980px;
            margin: 0 auto;
        }

        .knowledge-document {
            display: flex;
            align-items: center;
            gap: 22px;
            padding: 24px 28px;
            background: #fff;
            border: 1px solid #d7e6fb;
            border-radius: 12px;
            box-shadow: 0 8px 22px rgba(8, 47, 92, .08);
        }

        .knowledge-document > div:nth-child(2) {
            flex: 1;
        }

        .knowledge-document__icon {
            width: 54px;
            height: 54px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            background: #e9f2ff;
            color: var(--begawi-primary);
            font-size: 24px;
        }

        .knowledge-document h3,
        .knowledge-gallery__title {
            margin: 0 0 6px;
            color: var(--begawi-dark);
            font-size: 19px;
        }

        .knowledge-document p {
            margin: 0 0 6px;
            color: #475569;
            line-height: 1.6;
        }

        .knowledge-document small {
            color: #64748b;
        }

        .knowledge-document .thm-btn {
            flex: 0 0 auto;
            padding: 10px 16px;
        }

        .knowledge-faq-list {
            row-gap: 18px;
        }

        .knowledge-faq {
            background: #fff;
            border-radius: 10px;
            border: 1px solid #d7e6fb;
            box-shadow: 0 8px 22px rgba(8, 47, 92, .08);
        }

        .knowledge-faq summary {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            padding: 22px 24px;
            color: var(--begawi-dark);
            font-weight: 600;
            cursor: pointer;
            list-style: none;
        }

        .knowledge-faq summary::-webkit-details-marker {
            display: none;
        }

        .knowledge-faq summary span {
            color: var(--begawi-primary);
        }

        .knowledge-faq p {
            padding: 0 24px 22px;
            margin: 0;
            color: #475569;
            line-height: 1.75;
        }

        .knowledge-gallery .gallery-page__single {
            background: #fff;
            padding: 12px;
            border-radius: 12px;
        }

        .knowledge-gallery__title {
            padding: 14px 6px 4px;
            font-size: 16px;
        }

        .knowledge-blog .blog-two__single {
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 8px 22px rgba(8, 47, 92, .08);
        }

        .knowledge-blog .blog-two__single {
            display: flex;
            flex-direction: column;
        }

        .knowledge-blog .blog-two__img img {
            width: 100%;
            height: 230px;
            object-fit: cover;
        }

        .knowledge-blog .blog-two__content {
            padding: 24px;
            display: flex;
            flex: 1;
            flex-direction: column;
        }

        .knowledge-blog .blog-two__content .thm-btn {
            align-self: flex-start;
            margin-top: auto;
        }

        .knowledge-blog .blog-two__meta {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            color: #64748b;
        }

        .knowledge-blog .blog-two__text {
            color: #475569;
            line-height: 1.7;
            margin-bottom: 22px;
        }

        .knowledge-events .event-one__single {
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .knowledge-events .event-one__img img {
            width: 100%;
            height: 230px;
            object-fit: cover;
        }

        .knowledge-events .event-one__content {
            display: flex;
            flex: 1;
            flex-direction: column;
        }

        .knowledge-events .event-one__content .thm-btn {
            align-self: flex-start;
            margin-top: auto;
        }

        .knowledge-gallery .gallery-page__single {
            height: 100%;
        }

        .knowledge-gallery .gallery-page__img-box img {
            width: 100%;
            height: 220px;
            object-fit: cover;
        }

        .pagination-wrapper {
            clear: both;
            padding-top: 28px;
        }

        .pagination-wrapper .pg-pagination {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .pagination-wrapper .pg-pagination li a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 38px;
            height: 38px;
            border-radius: 6px;
            background: #fff;
            border: 1px solid #d7e6fb;
            color: var(--begawi-dark);
        }

        .pagination-wrapper .pg-pagination li.active a,
        .pagination-wrapper .pg-pagination li a:hover {
            background: var(--begawi-primary);
            border-color: var(--begawi-primary);
            color: #fff;
        }

        .landing-internal .why-choose-one {
            padding-top: 108px;
            padding-bottom: 108px;
        }

        .landing-internal .why-choose-one__text {
            color: #475569;
            line-height: 1.8;
            margin-bottom: 28px;
        }

        .landing-internal .why-choose-one__list li + li {
            margin-top: 24px;
        }

        .page-header__social {
            top: 320px;
            z-index: 2;
        }

        .landing-internal .main-header {
            background: transparent;
        }

        .landing-internal .main-header .main-menu__wrapper {
            background: #fff;
            border-bottom-color: rgba(8, 47, 92, .12);
        }

        .landing-internal .main-header .main-menu .main-menu__list > li > a,
        .landing-internal .main-header .main-menu__search {
            color: var(--begawi-dark);
        }

        .landing-internal .main-header .main-menu__list > li.current > a,
        .landing-internal .main-header .main-menu__list > li:hover > a,
        .landing-internal .main-header .main-menu__search:hover {
            color: var(--begawi-primary);
        }

        .landing-internal .main-header .main-menu__btn-box-1 .thm-btn {
            border-color: var(--begawi-primary);
            color: var(--begawi-primary);
        }

        .landing-internal .main-header .main-menu__btn-box-1 .thm-btn:hover {
            background: var(--begawi-primary);
            color: #fff;
        }

        .landing-internal .main-header .main-menu__btn-box-2 .thm-btn {
            background: var(--begawi-primary);
            color: #fff;
        }

        .landing-internal .main-header .main-menu__btn-box-2 .thm-btn:hover {
            background: var(--begawi-dark);
        }

        .main-slider .item {
            padding-top: 220px;
        }

        .page-header__inner {
            padding-top: 250px;
        }

        @media (max-width: 1500px) {
            .main-menu__category-box {
                display: none;
            }

            .main-menu__left {
                gap: 0;
            }

            .main-menu .main-menu__list > li + li {
                margin-left: 14px;
            }
        }

        @media (min-width: 768px) and (max-width: 1199px) {
            .main-slider .item {
                padding-top: 180px;
            }

            .page-header__inner {
                padding-top: 205px;
            }
        }

        @media (max-width: 1199px) {
            .main-menu__wrapper-inner {
                justify-content: space-between;
            }

            .main-menu__main-menu-box {
                flex: 0 0 auto;
            }

            .main-menu__right {
                display: none;
            }
        }

        @media (max-width: 991px) {
            .main-menu__wrapper-inner {
                min-height: 78px;
            }

            .main-menu__left {
                padding: 14px 0;
            }
        }

        @media (max-width: 767px) {
            .page-header__social {
                display: none;
            }

            .main-slider .item {
                padding-top: 108px;
            }

            .page-header__inner {
                padding-top: 112px;
            }

            .landing-internal .category-one {
                padding-top: 76px !important;
                padding-bottom: 72px !important;
            }

            .landing-internal .category-one__single {
                min-height: 0;
                margin-bottom: 32px;
            }

            .landing-internal .why-choose-one {
                padding-top: 72px;
                padding-bottom: 72px;
            }

            .learning-detail {
                padding: 72px 0;
            }

            .learning-detail__card {
                margin-top: 40px;
                padding: 26px;
            }

            .knowledge-document {
                align-items: flex-start;
                flex-wrap: wrap;
                padding: 20px;
            }

            .knowledge-document > div:nth-child(2) {
                min-width: calc(100% - 76px);
            }

            .knowledge-document .thm-btn {
                margin-left: 76px;
            }

            .main-menu__left {
                padding: 10px 0;
            }

            .main-menu__logo img {
                max-height: 48px;
            }
        }
    </style>

    <?= $this->renderSection('pageStyles') ?>
</head>

<?php $currentPath = trim(service('uri')->getPath(), '/'); ?>
<?php $isLandingHome = $currentPath === '' || $currentPath === 'index.php'; ?>
<body class="custom-cursor <?= $isLandingHome ? 'landing-home' : 'landing-internal' ?>">
    <div class="custom-cursor__cursor"></div>
    <div class="custom-cursor__cursor-two"></div>

    <div class="loader js-preloader">
        <div></div>
        <div></div>
        <div></div>
    </div>

    <div class="page-wrapper">
        <?= $this->include('landing/components/header') ?>

        <div class="stricky-header stricked-menu main-menu">
            <div class="sticky-header__content"></div>
        </div>

        <?= $this->renderSection('content') ?>

        <?= $this->include('landing/components/footer') ?>
    </div>

    <?= $this->include('landing/components/mobile_menu') ?>
    <?= $this->include('landing/components/search_popup') ?>

    <a href="#" data-target="html" class="scroll-to-target scroll-to-top">
        <span class="scroll-to-top__wrapper"><span class="scroll-to-top__inner"></span></span>
        <span class="scroll-to-top__text">Kembali ke Atas</span>
    </a>

    <script src="<?= base_url('assets/main/js/jquery-3.6.0.min.js') ?>"></script>
    <script src="<?= base_url('assets/main/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= base_url('assets/main/js/jarallax.min.js') ?>"></script>
    <script src="<?= base_url('assets/main/js/jquery.ajaxchimp.min.js') ?>"></script>
    <script src="<?= base_url('assets/main/js/jquery.appear.min.js') ?>"></script>
    <script src="<?= base_url('assets/main/js/swiper.min.js') ?>"></script>
    <script src="<?= base_url('assets/main/js/jquery.magnific-popup.min.js') ?>"></script>
    <script src="<?= base_url('assets/main/js/jquery.validate.min.js') ?>"></script>
    <script src="<?= base_url('assets/main/js/odometer.min.js') ?>"></script>
    <script src="<?= base_url('assets/main/js/wNumb.min.js') ?>"></script>
    <script src="<?= base_url('assets/main/js/wow.js') ?>"></script>
    <script src="<?= base_url('assets/main/js/isotope.js') ?>"></script>
    <script src="<?= base_url('assets/main/js/owl.carousel.min.js') ?>"></script>
    <script src="<?= base_url('assets/main/js/jquery-ui.js') ?>"></script>
    <script src="<?= base_url('assets/main/js/jquery.nice-select.min.js') ?>"></script>
    <script src="<?= base_url('assets/main/js/marquee.min.js') ?>"></script>
    <script src="<?= base_url('assets/main/js/aos.js') ?>"></script>
    <script src="<?= base_url('assets/main/js/gsap/gsap.js') ?>"></script>
    <script src="<?= base_url('assets/main/js/gsap/ScrollTrigger.js') ?>"></script>
    <script src="<?= base_url('assets/main/js/gsap/SplitText.js') ?>"></script>
    <script src="<?= base_url('assets/main/js/script.js') ?>"></script>

    <?= $this->renderSection('pageScripts') ?>
</body>
</html>
