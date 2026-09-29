# BegawiPBJ — Complete Asset Manifest

## Status

```text
Status: Canonical asset manifest
Scope: Asset produksi Frontend UI
Source: daftar nama folder dan file path assets.txt, tree-fistudy.txt,
        tree-wowdash.txt, dan source HTML Batch 1
```

---

## Root Asset Produksi

```text
FiStudy
public/assets/main/
URL root: assets/main/

WowDash
public/assets/dashboard/
URL root: assets/dashboard/
```

```text
DILARANG
assets/main/assets/
assets/dashboard/assets/
public/assets/workspace/
```

---

## Boundary Asset

| Boundary | Template | Root yang boleh dipakai |
|---|---|---|
| Landing/Public | FiStudy | `assets/main/` |
| Workspace | FiStudy | `assets/main/` |
| Auth terpadu | WowDash | `assets/dashboard/` |
| Dashboard Back-office | WowDash | `assets/dashboard/` |
| Error minimal | Minimal/custom | Tanpa dependency asset, kecuali asset yang diverifikasi dan diperlukan |
| Reference | Non-production | Tidak boleh dipanggil route/view produksi |

---

# FiStudy

## Root FiStudy

```text
public/assets/main/
├── css/
├── fonts/
├── images/
├── js/
└── uploads-public/
```

---

## CSS Global FiStudy

```text
css/animate.min.css
css/aos.css
css/bootstrap.min.css
css/custom-animate.css
css/dark.css
css/flaticon.css
css/font-awesome-all.css
css/jarallax.css
css/jquery-ui.css
css/jquery.magnific-popup.css
css/nice-select.css
css/odometer.min.css
css/owl.carousel.min.css
css/owl.theme.default.min.css
css/responsive.css
css/style.css
css/swiper.min.css
css/vegas.min.css
```

### Pemakaian dasar Landing/Workspace

```php
<link rel="stylesheet" href="<?= base_url('assets/main/css/bootstrap.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/main/css/animate.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/main/css/aos.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/main/css/flaticon.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/main/css/font-awesome-all.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/main/css/jquery.magnific-popup.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/main/css/nice-select.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/main/css/owl.carousel.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/main/css/swiper.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/main/css/style.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/main/css/responsive.css') ?>">
```

### Asset CSS bersyarat

| Kebutuhan | File CSS |
|---|---|
| Counter odometer | `css/odometer.min.css` |
| Slider background Vegas | `css/vegas.min.css` |
| Parallax Jarallax | `css/jarallax.css` |
| Filter/tanggal JQuery UI | `css/jquery-ui.css` |
| Carousel Owl tambahan | `css/owl.theme.default.min.css` |
| Tema gelap referensi | `css/dark.css` |
| Animasi kustom | `css/custom-animate.css` |

---

## CSS Modular FiStudy

```text
css/module-css/about.css
css/module-css/banner.css
css/module-css/become-a-teacher.css
css/module-css/blog.css
css/module-css/category.css
css/module-css/coming-soon.css
css/module-css/contact.css
css/module-css/counter.css
css/module-css/courses-search.css
css/module-css/courses.css
css/module-css/error.css
css/module-css/event.css
css/module-css/faq.css
css/module-css/footer.css
css/module-css/gallery.css
css/module-css/live-class.css
css/module-css/newsletter.css
css/module-css/page-header.css
css/module-css/pricing.css
css/module-css/process.css
css/module-css/shop.css
css/module-css/skill.css
css/module-css/slider.css
css/module-css/sliding-text.css
css/module-css/team.css
css/module-css/testimonial.css
css/module-css/video-one.css
css/module-css/why-choose.css
```

### Mapping CSS modular per halaman

| Halaman/komponen | CSS modular minimum |
|---|---|
| Landing home | `banner.css`, `category.css`, `courses.css`, `counter.css`, `about.css`, `footer.css`, `slider.css`, `why-choose.css` bila markup dipakai |
| About | `about.css`, `page-header.css`, `why-choose.css`, `footer.css` |
| Contact | `contact.css`, `page-header.css`, `footer.css` |
| Course/program list | `courses.css`, `courses-search.css`, `page-header.css`, `footer.css` |
| Course detail | `courses.css`, `page-header.css`, `video-one.css`, `footer.css` |
| Instructor list/detail | `team.css`, `page-header.css`, `footer.css` |
| Event list/detail | `event.css`, `page-header.css`, `footer.css` |
| Artikel/Bunga Rampai | `blog.css`, `page-header.css`, `footer.css` |
| FAQ | `faq.css`, `page-header.css`, `footer.css` |
| Media/gallery | `gallery.css`, `page-header.css`, `footer.css` |
| Statistik publik | `counter.css`, `page-header.css`, `footer.css` |
| Error FiStudy | `error.css` |
| Workspace learning | `courses.css`, `page-header.css`, `footer.css` |

---

## Font FiStudy

```text
fonts/fa-brands-400.eot
fonts/fa-brands-400.ttf
fonts/fa-brands-400.woff
fonts/fa-brands-400.woff2
fonts/fa-light-300.woff
fonts/fa-light-300.woff2
fonts/fa-regular-400.eot
fonts/fa-regular-400.woff
fonts/fa-regular-400.woff2
fonts/fa-solid-900.woff
fonts/fa-solid-900.woff2
fonts/icomoon.eot
fonts/icomoon.svg
fonts/icomoon.ttf
fonts/icomoon.woff
```

Gunakan class icon yang sudah dipakai source template setelah CSS berikut dimuat:

```text
css/font-awesome-all.css
css/flaticon.css
```

---

## Image FiStudy — Background

```text
images/backgrounds/coming-soon-page-bg.jpg
images/backgrounds/counter-one-bg.jpg
images/backgrounds/course-details-video-link-bg.jpg
images/backgrounds/slider-1-1.jpg
images/backgrounds/slider-1-2.jpg
images/backgrounds/slider-1-3.jpg
```

### Mapping

| Kebutuhan | Path |
|---|---|
| Hero/slider landing | `images/backgrounds/slider-1-1.jpg`, `slider-1-2.jpg`, `slider-1-3.jpg` |
| Counter/statistik | `images/backgrounds/counter-one-bg.jpg` |
| Course detail/video area | `images/backgrounds/course-details-video-link-bg.jpg` |
| Coming soon | `images/backgrounds/coming-soon-page-bg.jpg` |

---

## Image FiStudy — Brand dan Favicon

```text
images/brand/brand-1-1.png
images/brand/brand-1-2.png
images/brand/brand-1-3.png
images/brand/brand-1-4.png
images/brand/brand-1-5.png
images/brand/category-one-brand-1-1.png
images/brand/category-one-brand-1-2.png
images/brand/category-one-brand-1-3.png
images/brand/category-one-brand-1-4.png

images/favicons/android-chrome-192x192.png
images/favicons/android-chrome-512x512.png
images/favicons/apple-touch-icon.png
images/favicons/favicon-16x16.png
images/favicons/favicon-32x32.png
images/favicons/favicon.ico
images/favicons/site.webmanifest
```

---

## Image FiStudy — Resource Umum

```text
images/resources/abou-one-img-1.jpg
images/resources/abou-one-img-2.jpg
images/resources/about-three-img-1.jpg
images/resources/about-three-img-2.jpg
images/resources/about-two-client-img-1.png
images/resources/about-two-client-two-img-1.jpg
images/resources/about-two-client-two-img-1.png
images/resources/about-two-img-1.png
images/resources/banner-one-student-trained-img-1-1.jpg
images/resources/banner-one-student-trained-img-1-2.jpg
images/resources/banner-one-udemy-review-img.jpg
images/resources/banner-one-udemy-review-logo.png
images/resources/banner-two-content-one-img-1.jpg
images/resources/banner-two-content-two-img-1.jpg
images/resources/banner-two-course-discount-img-1.jpg
images/resources/banner-two-img-1.png
images/resources/banner-two-success-student-1-1.jpg
images/resources/banner-two-success-student-1-2.jpg
images/resources/banner-two-success-student-1-3.jpg
images/resources/become-a-teacher-img-1.jpg
images/resources/category-one-img-1.png
images/resources/contact-three-img-1.jpg
images/resources/counter-2-1.jpg
images/resources/counter-2-2.jpg
images/resources/counter-2-3.jpg
images/resources/counter-2-4.jpg
images/resources/counter-one-single-hover-img.jpg
images/resources/course-details-client-img-1.jpg
images/resources/course-details-img-1.jpg
images/resources/course-details-Instructor-img.jpg
images/resources/course-grid-discount-img-1.png
images/resources/course-list-client-img-1.jpg
images/resources/course-list-client-img-2.jpg
images/resources/course-list-client-img-3.jpg
images/resources/course-list-client-img-4.jpg
images/resources/course-list-client-img-5.jpg
images/resources/course-list-img-1.jpg
images/resources/course-list-img-2.jpg
images/resources/course-list-img-3.jpg
images/resources/course-list-img-4.jpg
images/resources/course-list-img-5.jpg
images/resources/courses-1-1.jpg
images/resources/courses-1-2.jpg
images/resources/courses-1-3.jpg
images/resources/courses-2-1.jpg
images/resources/courses-2-2.jpg
images/resources/courses-2-3.jpg
images/resources/courses-2-4.jpg
images/resources/courses-2-5.jpg
images/resources/courses-2-6.jpg
images/resources/courses-3-1.jpg
images/resources/courses-3-2.jpg
images/resources/courses-3-3.jpg
images/resources/courses-3-4.jpg
images/resources/courses-two-client-img-1.jpg
images/resources/courses-two-client-img-2.jpg
images/resources/courses-two-client-img-3.jpg
images/resources/courses-two-client-img-4.jpg
images/resources/courses-two-client-img-5.jpg
images/resources/courses-two-client-img-6.jpg
images/resources/error-page-img1.png
images/resources/live-class-1-1.jpg
images/resources/live-class-1-2.jpg
images/resources/live-class-1-3.jpg
images/resources/live-class-two-img-1.jpg
images/resources/logo-1.png
images/resources/logo-2.png
images/resources/logo-3.png
images/resources/logo-4.png
images/resources/newsletter-one-img-1.png
images/resources/newsletter-two-img-1.png
images/resources/page-header-img-1.png
images/resources/site-footer-logo-1.png
images/resources/site-footer-logo-2.png
images/resources/skill-one-img-1.jpg
images/resources/skill-one-img-2.jpg
images/resources/video-one-img-1.jpg
images/resources/why-choose-one-client-img-1.jpg
images/resources/why-choose-one-client-img-2.jpg
images/resources/why-choose-one-client-img-3.jpg
images/resources/why-choose-one-img-1.jpg
images/resources/why-choose-one-img-2.jpg
images/resources/why-choose-three-ceo-img-two.jpg
images/resources/why-choose-three-ceo-img.jpg
images/resources/why-choose-two-right-content-one-user-details-img-1.jpg
images/resources/why-choose-two-right-content-one-user-details-top-img.png
images/resources/why-choose-two-right-content-two-user-details-top-img.png
images/resources/why-choose-two-student-trained-img-1-1.jpg
images/resources/why-choose-two-student-trained-img-1-2.jpg
images/resources/why-choose-two-student-trained-img-1-3.jpg
images/resources/why-choose-two-student-trained-img-1-4.jpg
images/resources/why-choose-two-udemy-review-img.jpg
images/resources/why-choose-two-udemy-review-logo.png
```

---

## Image FiStudy — Course/Learning Mapping

| UI | Asset yang dipilih dari inventaris nyata |
|---|---|
| Program/Course grid | `images/resources/courses-1-1.jpg` sampai `courses-1-3.jpg` |
| Course grid alternatif | `images/resources/courses-2-1.jpg` sampai `courses-2-6.jpg` |
| Course grid alternatif 2 | `images/resources/courses-3-1.jpg` sampai `courses-3-4.jpg` |
| Course list horizontal | `images/resources/course-list-img-1.jpg` sampai `course-list-img-5.jpg` |
| Course detail hero | `images/resources/course-details-img-1.jpg` |
| Course detail instructor | `images/resources/course-details-Instructor-img.jpg` |
| Course detail testimonial/client | `images/resources/course-details-client-img-1.jpg` |
| Course list avatars | `images/resources/course-list-client-img-1.jpg` sampai `course-list-client-img-5.jpg` |
| Discount/promo course | `images/resources/course-grid-discount-img-1.png` |
| Live class | `images/resources/live-class-1-1.jpg` sampai `live-class-1-3.jpg` |

---

## Image FiStudy — Team/Instructor

```text
images/team/team-1-1.jpg
images/team/team-1-2.jpg
images/team/team-1-3.jpg
images/team/team-1-4.jpg
images/team/team-1-5.jpg
images/team/team-1-6.jpg
images/team/team-1-7.jpg
images/team/team-1-8.jpg
images/team/team-2-1.jpg
images/team/team-2-1.png
images/team/team-2-2.jpg
images/team/team-2-3.jpg
images/team/team-details-img-1.jpg
images/team/team-three-img-1.jpg
images/team/team-three-img-2.jpg
```

---

## Image FiStudy — Event

```text
images/event/event-1-1.jpg
images/event/event-1-2.jpg
images/event/event-1-3.jpg
images/event/event-1-4.jpg
images/event/event-1-5.jpg
images/event/event-1-6.jpg
images/event/event-details-top-img-1.jpg
```

---

## Image FiStudy — Blog/Bunga Rampai

```text
images/blog/blog-1-1.jpg
images/blog/blog-1-2.jpg
images/blog/blog-1-3.jpg
images/blog/blog-2-1.jpg
images/blog/blog-2-2.jpg
images/blog/blog-2-3.jpg
images/blog/blog-2-4.jpg
images/blog/blog-2-5.jpg
images/blog/blog-2-6.jpg
images/blog/blog-2-7.jpg
images/blog/blog-2-8.jpg
images/blog/blog-2-9.jpg
images/blog/blog-details-client-img-1.jpg
images/blog/blog-details-client-img-2.jpg
images/blog/blog-details-img-1.jpg
images/blog/blog-details-img-box-img-1.jpg
images/blog/blog-details-img-box-img-2.jpg
images/blog/blog-list-1-1.jpg
images/blog/blog-list-1-3.jpg
images/blog/blog-list-client-img-1.jpg
images/blog/blog-list-client-img-2.jpg
images/blog/blog-list-client-img-3.jpg
images/blog/blog-lp-1.jpg
images/blog/blog-lp-2.jpg
images/blog/blog-lp-3.jpg
images/blog/blog-one-user-img-1.jpg
images/blog/blog-one-user-img-2.jpg
images/blog/blog-one-user-img-3.jpg
images/blog/comment-1-1.jpg
images/blog/comment-1-2.jpg
images/blog/comment-1-3.jpg
```

---

## Image FiStudy — Gallery

```text
images/gallery/gallery-page-1-1.jpg
images/gallery/gallery-page-1-2.jpg
images/gallery/gallery-page-1-3.jpg
images/gallery/gallery-page-1-4.jpg
images/gallery/gallery-page-1-5.jpg
images/gallery/gallery-page-1-6.jpg
images/gallery/gallery-page-1-7.jpg
images/gallery/gallery-page-1-8.jpg
images/gallery/gallery-page-1-9.jpg
```

---

## Image FiStudy — Icon

```text
images/icon/3d-alarm.png
images/icon/about-one-experience-icon.png
images/icon/about-three-experience-icon.png
images/icon/about-two-class-day-icon.png
images/icon/about-two-points-icon-1.png
images/icon/about-two-points-icon-2.png
images/icon/about-two-points-icon-3.png
images/icon/apple-icon-2.png
images/icon/apple-icon.png
images/icon/banner-two-book-icon.png
images/icon/banner-two-edu-icon.png
images/icon/banner-two-experience-icon.png
images/icon/card-icon-1.png
images/icon/card-icon-2.png
images/icon/card-icon-3.png
images/icon/category-one-hover-icon-1-1.png
images/icon/categoyr-two-icon-1.png
images/icon/categoyr-two-icon-2.png
images/icon/categoyr-two-icon-3.png
images/icon/categoyr-two-icon-4.png
images/icon/categoyr-two-icon-5.png
images/icon/categoyr-two-icon-6.png
images/icon/categoyr-two-icon-7.png
images/icon/categoyr-two-icon-8.png
images/icon/check-icon.png
images/icon/contact-two-icon-1.png
images/icon/contact-two-icon-2.png
images/icon/contact-two-icon-3.png
images/icon/contact-two-icon-4.png
images/icon/course-details-points-list-icon-1.png
images/icon/course-details-points-list-icon-2.png
images/icon/course-details-points-list-icon-3.png
images/icon/course-details-points-list-icon-4.png
images/icon/google-play-icon-2.png
images/icon/google-play-icon.png
images/icon/icon-facebook.png
images/icon/icon-google-2.png
images/icon/idea-bulb.png
images/icon/linke-icon.png
images/icon/mission-icon.png
images/icon/sidebar-title-icon.png
images/icon/skill-one-experience-icon.png
images/icon/team-details-points-icon.png
images/icon/testimonial-two-shape-icon-1.png
images/icon/testimonial-two-shape-icon-2.png
images/icon/vision-icon.png
images/icon/why-choose-one-icon-1.png
images/icon/why-choose-one-icon-2.png
images/icon/why-choose-one-icon-3.png
images/icon/why-choose-one-icon-4.png
images/icon/why-choose-three-feature-1.png
images/icon/why-choose-three-feature-2.png
images/icon/why-choose-three-feature-3.png
images/icon/why-choose-three-feature-4.png
images/icon/why-choose-two-icon-1.png
images/icon/why-choose-two-icon-2.png
images/icon/why-choose-two-icon-3.png
images/icon/why-choose-two-right-content-two-user-details-icon.png
```

---

## Image FiStudy — Shapes

```text
images/shapes/about-one-img-shape-1.png
images/shapes/about-one-shape-1.png
images/shapes/about-one-shape-2.png
images/shapes/about-three-shape-1.png
images/shapes/about-two-bg-shape.png
images/shapes/about-two-shape-1.png
images/shapes/about-two-shape-2.png
images/shapes/about-two-shape-3.png
images/shapes/banner-one-bg-shape-1.png
images/shapes/banner-one-shape-1.png
images/shapes/banner-one-shape-2.png
images/shapes/banner-one-shape-3.png
images/shapes/banner-one-shape-4.png
images/shapes/banner-one-student-trained-shape-1.png
images/shapes/banner-one-title-box-shape-1.png
images/shapes/banner-two-shape-2.png
images/shapes/banner-two-shape-3.png
images/shapes/banner-two-shape-4.png
images/shapes/banner-two-shape-5.png
images/shapes/banner-two-shape-6.png
images/shapes/banner-two-shape-bg.png
images/shapes/category-one-bg-shape.png
images/shapes/category-one-shape-1.png
images/shapes/category-one-shape-2.png
images/shapes/category-one-shape-3.png
images/shapes/category-one-social-media-shape-1.png
images/shapes/counter-one-shape-1.png
images/shapes/counter-one-video-shape-1.png
images/shapes/counter-two-shape-1.png
images/shapes/course-grid-discount-shape-bg.png
images/shapes/course-grid-title-shape-1.png
images/shapes/courses-search-shape-bg.jpg
images/shapes/live-class-shape-1.png
images/shapes/live-class-two-shape-1.png
images/shapes/live-class-two-shape-2.png
images/shapes/newsletter-one-bg-shape.png
images/shapes/newsletter-one-shape-1.png
images/shapes/newsletter-one-shape-2.png
images/shapes/newsletter-two-like.png
images/shapes/newsletter-two-shape-bg.png
images/shapes/page-header-bg-shape.png
images/shapes/page-header-shape-1.png
images/shapes/page-header-shape-2.png
images/shapes/page-header-shape-3.png
images/shapes/page-header-shape-4.png
images/shapes/page-header-shape-5.png
images/shapes/section-title-shape-1.png
images/shapes/section-title-shape-2.png
images/shapes/section-title-two-shape-1.png
images/shapes/section-title-two-shape-2.png
images/shapes/site-footer-shape-bg.png
images/shapes/site-footer-star.png
images/shapes/skill-one-shape-1.png
images/shapes/sliding-text-five-shape-1.png
images/shapes/sliding-text-shape-1.png
images/shapes/sliding-text-three-shape-1.png
images/shapes/sliding-text-three-shape.png
images/shapes/sliding-text-two-shape-1.png
images/shapes/team-one-content-shape-1.png
images/shapes/team-one-content-shape-2.png
images/shapes/team-one-shape-1.png
images/shapes/team-one-shape-2.png
images/shapes/team-one-shape-3.png
images/shapes/team-one-shape-4.png
images/shapes/team-one-single-bg-shape.png
images/shapes/team-three-shape-1.png
images/shapes/team-three-shape-2.png
images/shapes/team-two-shape-1.png
images/shapes/team-two-shape-2.png
images/shapes/testimonial-one-shape-1.png
images/shapes/testimonial-one-shape-2.png
images/shapes/testimonial-two-shape-1.png
images/shapes/testimonial-two-shape-2.png
images/shapes/testimonial-two-shape-3.png
images/shapes/testimonial-two-shape-4.png
images/shapes/testimonial-two-shape-6.png
images/shapes/video-one-shape-3.png
images/shapes/video-one-shape-4.png
images/shapes/video-one-video-shape-1.png
images/shapes/why-choose-one-shape-1.png
images/shapes/why-choose-one-shape-2.png
images/shapes/why-choose-one-shape-3.png
images/shapes/why-choose-one-shape-4.png
images/shapes/why-choose-one-shape-5.png
images/shapes/why-choose-one-shape-6.png
images/shapes/why-choose-one-shape-7.png
images/shapes/why-choose-two-dot-1.png
images/shapes/why-choose-two-dot-2.png
images/shapes/why-choose-two-right-content-one-user-details-top-shape-bg.png
images/shapes/why-choose-two-shape-4.png
images/shapes/why-choose-two-shape-5.png
```

---

## Image FiStudy — Shop

```text
images/shop/cart-page-img-1.jpg
images/shop/cart-page-img-2.jpg
images/shop/paypal-1.jpg
images/shop/product-details-img-1.jpg
images/shop/product-thumb-1.jpg
images/shop/product-thumb-2.jpg
images/shop/product-thumb-3.jpg
images/shop/product-thumb-4.jpg
images/shop/review-1-1.jpg
images/shop/review-1-2.jpg
images/shop/shop-product-1-1.jpg
images/shop/shop-product-1-2.jpg
images/shop/shop-product-1-3.jpg
images/shop/shop-product-1-4.jpg
images/shop/shop-product-1-5.jpg
images/shop/shop-product-1-6.jpg
images/shop/shop-product-1-7.jpg
images/shop/shop-product-1-8.jpg
images/shop/shop-product-1-9.jpg
images/shop/wishlist-page-img-1.jpg
images/shop/wishlist-page-img-2.jpg
images/shop/wishlist-page-img-3.jpg
images/shop/wishlist-page-img-4.jpg
```

---

## Image FiStudy — Testimonial

```text
images/testimonial/testimonial-1-1.jpg
images/testimonial/testimonial-1-2.jpg
images/testimonial/testimonial-1-3.jpg
images/testimonial/testimonial-2-1.jpg
images/testimonial/testimonial-2-2.jpg
images/testimonial/testimonial-2-3.jpg
images/testimonial/testimonial-3-1.jpg
images/testimonial/testimonial-3-2.jpg
images/testimonial/testimonial-3-3.jpg
images/testimonial/testimonial-3-4.jpg
images/testimonial/testimonial-two-client-logo.png
images/testimonial/testimonial-two-shape-img-1.jpg
images/testimonial/testimonial-two-shape-img-2.jpg
images/testimonial/testimonial-two-shape-img-3.jpg
```

---

## JavaScript FiStudy

```text
Status: hanya panggil file yang benar-benar tercatat pada source HTML halaman.

Source referensi yang harus digunakan untuk pemanggilan tepat:
reference/fistudy/index.html
reference/fistudy/about.html
reference/fistudy/contact.html
reference/fistudy/course.html
reference/fistudy/course-details.html
reference/fistudy/event-details.html
reference/fistudy/events.html
reference/fistudy/instructor.html
reference/fistudy/instructor-details.html
reference/fistudy/404.html
```

Jangan menambahkan JavaScript dari asumsi nama plugin. Path dan urutan script harus disalin dari source page terkait kemudian prefixnya diubah menjadi `assets/main/`.

---

# WowDash

## Root WowDash

```text
public/assets/dashboard/
├── css/
├── fonts/
├── images/
└── js/
```

---

## CSS WowDash

```text
css/remixicon.css
css/style.css

css/lib/apexcharts.css
css/lib/audioplayer.css
css/lib/bootstrap.min.css
css/lib/dataTables.min.css
css/lib/editor-katex.min.css
css/lib/editor.atom-one-dark.min.css
css/lib/editor.quill.snow.css
css/lib/file-upload.css
css/lib/flatpickr.min.css
css/lib/full-calendar.css
css/lib/jquery-jvectormap-2.0.5.css
css/lib/magnific-popup.css
css/lib/prism.css
css/lib/slick.css
```

### Minimum Auth/Dashboard

```php
<link rel="stylesheet" href="<?= base_url('assets/dashboard/css/lib/bootstrap.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/dashboard/css/remixicon.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/dashboard/css/style.css') ?>">
```

### CSS bersyarat WowDash

| Kebutuhan | File CSS |
|---|---|
| ApexCharts | `css/lib/apexcharts.css` |
| Data table | `css/lib/dataTables.min.css` |
| Rich text editor | `css/lib/editor-katex.min.css`, `editor.atom-one-dark.min.css`, `editor.quill.snow.css` |
| Upload media | `css/lib/file-upload.css` |
| Date/time picker | `css/lib/flatpickr.min.css` |
| Calendar | `css/lib/full-calendar.css` |
| Map | `css/lib/jquery-jvectormap-2.0.5.css` |
| Modal gallery | `css/lib/magnific-popup.css` |
| Code block | `css/lib/prism.css` |
| Slider | `css/lib/slick.css` |
| Audio preview | `css/lib/audioplayer.css` |

---

## Font WowDash

```text
fonts/remixicon.eot
fonts/remixicon.glyph.json
fonts/remixicon.styl
fonts/remixicon.svg
fonts/remixicon.symbol.svg
fonts/remixicon.ttf
fonts/remixicon.woff
fonts/remixicon.woff2
```

---

## Image WowDash — Root

```text
images/arrow-down.png
images/envelope.svg
images/error-img.png
images/faq-img.png
images/favicon.png
images/lang-flag.png
images/logo-icon.png
images/logo-light.png
images/logo.png
images/times.png
images/user.png
images/wow-dash-favicon.png
```

---

## Image WowDash — Auth dan Error

```text
images/auth/auth-img.png
images/auth/envelop-icon.png
images/auth/forgot-pass-img.png

images/error/400-img.png
images/error/400-img1 - Copy.avif
images/error/400-img1 - Copy.png
images/error/400-img1.avif
images/error/400-img1.png
images/error/400-img2.png
images/error/401-img1.avif
images/error/401-img2.png
images/error/403-img.png
images/error/403-img1.png
images/error/403-img2.png
images/error/403-img3.jpg
images/error/403-img3.png
images/error/404-img.png
images/error/404-img1.jpg
images/error/404-img1.png
images/error/404-img2.jpg
images/error/404-img2.png
images/error/404-img3.png
images/error/500-img.png
images/error/500-img1.png
images/error/500-img2.png
images/error/503-img1.png
images/error/503-img2.png
images/error/error-img.png

images/forbidden/forbidden-img.png
images/coming-soon/coming-soon.png
images/coming-soon/maintenance.png
```

---

## Image WowDash — Avatar/User

```text
images/avatar/avatar-group1.png
images/avatar/avatar-group2.png
images/avatar/avatar-group3.png
images/avatar/avatar-group4.png
images/avatar/avatar-group5.png
images/avatar/avatar-group6.png
images/avatar/avatar-shape1.png
images/avatar/avatar-shape2.png
images/avatar/avatar-shape3.png
images/avatar/avatar1.png
images/avatar/avatar2.png
images/avatar/status-avatar.png

images/users/user1.png
images/users/user2.png
images/users/user3.png
images/users/user4.png
images/users/user5.png
images/users/user6.png

images/user-list/user-list1.png
images/user-list/user-list2.png
images/user-list/user-list3.png
images/user-list/user-list4.png
images/user-list/user-list5.png
images/user-list/user-list6.png
images/user-list/user-list7.png
images/user-list/user-list8.png
images/user-list/user-list9.png
images/user-list/user-list10.png

images/user-grid/user-grid-bg1.png
images/user-grid/user-grid-bg2.png
images/user-grid/user-grid-bg3.png
images/user-grid/user-grid-bg4.png
images/user-grid/user-grid-bg5.png
images/user-grid/user-grid-bg6.png
images/user-grid/user-grid-bg7.png
images/user-grid/user-grid-bg8.png
images/user-grid/user-grid-bg9.png
images/user-grid/user-grid-bg10.png
images/user-grid/user-grid-bg11.png
images/user-grid/user-grid-bg12.png
images/user-grid/user-grid-img1.png
images/user-grid/user-grid-img2.png
images/user-grid/user-grid-img3.png
images/user-grid/user-grid-img4.png
images/user-grid/user-grid-img5.png
images/user-grid/user-grid-img6.png
images/user-grid/user-grid-img7.png
images/user-grid/user-grid-img8.png
images/user-grid/user-grid-img9.png
images/user-grid/user-grid-img10.png
images/user-grid/user-grid-img11.png
images/user-grid/user-grid-img12.png
images/user-grid/user-grid-img13.png
images/user-grid/user-grid-img14.png
```

---

## Image WowDash — Component dan Konten

```text
images/asset/asset-img1.png
images/asset/asset-img2.png
images/asset/asset-img3.png
images/asset/asset-img4.png
images/asset/asset-img5.png

images/blog/blog-details.png
images/blog/blog1.png
images/blog/blog2.png
images/blog/blog3.png
images/blog/blog4.png
images/blog/blog5.png
images/blog/blog6.png
images/blog/blog7.png

images/card/card-bg.png
images/card/card-logo.png
images/card/payment-icon.png

images/card-component/card-img1.png
images/card-component/card-img2.png
images/card-component/card-img3.png
images/card-component/card-img4.png
images/card-component/card-overlay-img1.png
images/card-component/card-overlay-img2.png
images/card-component/card-overlay-img3.png
images/card-component/horizontal-card-img1.png
images/card-component/horizontal-card-img2.png
images/card-component/horizontal-card-img3.png
images/card-component/horizontal-card-img4.png

images/carousel/carousel-img1.png
images/carousel/carousel-img2.png
images/carousel/carousel-img3.png
images/carousel/carousel-img4.png
images/carousel/mutiple-carousel-img1.png
images/carousel/mutiple-carousel-img2.png
images/carousel/mutiple-carousel-img3.png
images/carousel/mutiple-carousel-img4.png

images/gallery/gallery-img1.png
images/gallery/gallery-img2.png
images/gallery/gallery-img3.png
images/gallery/gallery-img4.png
images/gallery/gallery-img5.png
images/gallery/gallery-img6.png
images/gallery/gallery-img7.png
images/gallery/gallery-img8.png
images/gallery/gallery-img9.png
images/gallery/gallery-img10.png
images/gallery/gallery-img11.png
images/gallery/gallery-img12.png

images/lists/list-img1.png
images/lists/list-img2.png
images/lists/list-img3.png
images/lists/list-img4.png
images/lists/list-img5.png

images/tabs/tabs-image1.png
images/tabs/tabs-image2.png

images/videos/video-img1.png
images/videos/video-img2.png
images/videos/video-img3.png
images/videos/video-img4.png
```

---

## Image WowDash — Lainnya

```text
images/chat/1.png sampai images/chat/11.png
images/chat/chat-main.png
images/chat/empty-img.png

images/chatgpt/empty-message-icon1.png
images/chatgpt/empty-message-icon2.png
images/chatgpt/image-generator1.png sampai images/chatgpt/image-generator5.png

images/crypto/bitcoin.png
images/crypto/crypto-img1.png sampai images/crypto/crypto-img6.png
images/crypto/paypal.png
images/crypto/wallet-icon1.png sampai images/crypto/wallet-icon4.png

images/currency/crypto-img1.png sampai images/currency/crypto-img5.png

images/flags/flag-tag.png
images/flags/flag1.png sampai images/flags/flag8.png

images/gif/success-img1.gif
images/gif/success-img2.gif
images/gif/success-img3.gif

images/home-eight/doctor-img1.png sampai images/home-eight/doctor-img6.png
images/home-eight/icon-pdf.png
images/home-eight/icon-text.png
images/home-eight/treatment-icon1.png sampai images/home-eight/treatment-icon6.png

images/home-eleven/bg-orange-gradient.png
images/home-eleven/officer-img1.png sampai images/home-eleven/officer-img5.png
images/home-eleven/bg/bg-orange-gradient.png
images/home-eleven/bg/radial-bg1.png
images/home-eleven/bg/radial-bg2.png
images/home-eleven/bg/radial-bg3.png
images/home-eleven/icons/earn-cat-icon1.svg sampai earn-cat-icon4.svg
images/home-eleven/icons/home-eleven-icon1.svg sampai home-eleven-icon4.svg

images/home-fourteen/podcast-img1.png sampai podcast-img4.png
images/home-fourteen/trending-img1.png sampai trending-img5.png

images/home-nine/payment1.png sampai payment4.png
images/home-nine/socials1.png sampai socials5.png
images/home-nine/source-icon1.png sampai source-icon4.png
images/home-nine/ticket1.png sampai ticket3.png
images/home-nine/trail-bg.png

images/home-nineteen/home-card-bg.png
images/home-nineteen/home-png.png
images/home-nineteen/property-img1.png sampai property-img10.png
images/home-nineteen/property-list-img1.png sampai property-list-img4.png
images/home-nineteen/icons/amenities-icon1.png sampai amenities-icon3.png
images/home-nineteen/icons/featured-icon.png
images/home-nineteen/icons/link-icon1.png sampai link-icon3.png

images/home-seventeen/referral-chart.png

images/home-six/category-icon1.png sampai category-icon6.png
images/home-six/Frame.png
images/home-six/student-img1.png sampai student-img6.png

images/home-sixteen/icon/dollar-bag.png
images/home-sixteen/icon/phone-icon1.png sampai phone-icon3.png

images/home-twelve/transaction-img1.png sampai transaction-img5.png
images/home-twelve/travel-img1.png sampai travel-img4.png
images/home-twelve/icons/booking-card-big-icon1.png sampai booking-card-big-icon4.png
images/home-twelve/icons/booking-card-icon1.png sampai booking-card-icon4.png
images/home-twelve/icons/home.png
images/home-twelve/icons/spen-icon1.png sampai spen-icon4.png

images/homeThirteen/podcaster-img1.png sampai podcaster-img5.png
images/homeThirteen/todo-list-img1.png sampai todo-list-img4.png
images/homeThirteen/icon/task-summary-icon1.svg sampai task-summary-icon4.svg
images/homeThirteen/icon/time-icon.svg
images/homeThirteen/shape/moon-shape-border.png

images/kanban/kanban-1.png sampai kanban-4.png
images/notice/board-img1.png sampai board-img3.png
images/notification/profile-1.png sampai profile-7.png
images/payment/payment-gateway1.png
images/payment/payment-gateway2.png
images/payment/payment1.png sampai payment4.png
images/payment/upload-image.png
images/pricing/price-icon1.png sampai price-icon5.png
images/pricing/pricing-shape.png
images/product/product-img1.png sampai product-img5.png
```

---

## JavaScript WowDash

```text
js/app.js
js/columnChartPageChart.js
js/editor.highlighted.min.js
js/editor.katex.min.js
js/editor.quill.js
js/flatpickr.js
js/full-calendar.js
js/homeFiveChart.js
js/homeFourChart.js
js/homeOneChart.js
js/homeThreeChart.js
js/homeTwoChart.js
js/invoice.js
js/lineChartPageChart.js
js/pieChartPageChart.js

js/lib/apexcharts.min.js
js/lib/audioplayer.js
```

---

## Manifest Layout Minimal

### FiStudy `head.php`

```php
<link rel="icon" href="<?= base_url('assets/main/images/favicons/favicon.ico') ?>">
<link rel="stylesheet" href="<?= base_url('assets/main/css/bootstrap.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/main/css/font-awesome-all.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/main/css/flaticon.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/main/css/animate.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/main/css/jquery.magnific-popup.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/main/css/nice-select.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/main/css/owl.carousel.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/main/css/swiper.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/main/css/style.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/main/css/responsive.css') ?>">
```

### FiStudy module per view

```php
<?php foreach (($pageStyles ?? []) as $style): ?>
    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/' . $style) ?>">
<?php endforeach; ?>
```

Contoh controller/view data:

```php
'pageStyles' => [
    'page-header.css',
    'courses.css',
    'courses-search.css',
    'footer.css',
],
```

### FiStudy footer script

```php
<!-- Path dan urutan final harus mengikuti source HTML halaman yang dipakai. -->
<!-- Jangan membuat nama JavaScript baru atau menambah library tanpa verifikasi. -->
```

### WowDash `head.php`

```php
<link rel="icon" type="image/png" href="<?= base_url('assets/dashboard/images/favicon.png') ?>">
<link rel="stylesheet" href="<?= base_url('assets/dashboard/css/lib/bootstrap.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/dashboard/css/remixicon.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/dashboard/css/style.css') ?>">

<?php foreach (($pageStyles ?? []) as $style): ?>
    <link rel="stylesheet" href="<?= base_url('assets/dashboard/css/lib/' . $style) ?>">
<?php endforeach; ?>
```

### WowDash footer script

```php
<script src="<?= base_url('assets/dashboard/js/app.js') ?>"></script>

<?php foreach (($pageScripts ?? []) as $script): ?>
    <script src="<?= base_url('assets/dashboard/js/' . $script) ?>"></script>
<?php endforeach; ?>
```

Contoh chart:

```php
'pageStyles' => ['apexcharts.css'],
'pageScripts' => ['lib/apexcharts.min.js', 'homeOneChart.js'],
```

---

## Mapping Asset Batch Saat Ini

### Batch 0B — Layout Foundation

```text
Status: HARUS DIREVISI memakai manifest ini.

FiStudy:
- Ganti seluruh assets/main/img/... menjadi assets/main/images/...
- Ganti seluruh nama CSS yang tidak ada menjadi daftar CSS manifest.
- Gunakan logo-1.png/logo-2.png atau teks jika logo belum dipilih.
- Gunakan favicon FiStudy pada images/favicons/.

WowDash:
- Hapus iconly.css.
- Hapus semua path assets/dashboard/assets/.
- Gunakan CSS minimum dan library kondisional.
```

### Batch 1 — Landing dan Halaman Umum

```text
Status: HARUS DIREVISI memakai:
- Landing/banner: images/backgrounds/slider-1-*.jpg dan images/resources/banner-*.jpg
- About: images/resources/abou-one-img-*.jpg atau about-two/about-three asset
- Course: images/resources/courses-*.jpg atau course-list-img-*.jpg
- Counter: images/backgrounds/counter-one-bg.jpg
- Page header: images/resources/page-header-img-1.png + images/shapes/page-header-*.png
- Footer: images/resources/site-footer-logo-*.png
```

### Batch 2 — Public Arena Belajar

```text
Status: HARUS DIREVISI memakai:
- Course/program: images/resources/courses-*.jpg
- Course list: images/resources/course-list-img-*.jpg
- Course detail: images/resources/course-details-img-1.jpg
- Instructor: images/team/team-*.jpg
- Course/sidebar instructor: images/resources/course-details-Instructor-img.jpg
```

---

## Larangan

```text
1. Jangan membuat asset CSS/JS/gambar/font baru.
2. Jangan menebak nama file maupun folder asset.
3. Jangan memakai /img/ sebagai root FiStudy; root nyata adalah /images/.
4. Jangan memakai assets/main/assets/ atau assets/dashboard/assets/.
5. Jangan memuat seluruh plugin pada seluruh layout.
6. Muat CSS/JS hanya jika dipakai page/component bersangkutan.
7. Jangan memuat asset FiStudy pada Auth/Dashboard WowDash.
8. Jangan memuat asset WowDash pada Landing/Workspace FiStudy.
9. Jangan gunakan asset source reference dengan URL relative mentah.
10. Jangan membuat asset root ketiga.
11. Jangan menyimpan file internal/restricted pada public/assets/.
```

---

## Checklist Implementasi View

```text
[ ] Boundary UI telah ditentukan: FiStudy atau WowDash.
[ ] Root URL benar: assets/main atau assets/dashboard.
[ ] Semua CSS berada pada manifest yang sesuai.
[ ] Semua JS berada pada manifest yang sesuai.
[ ] Semua image berada pada manifest yang sesuai.
[ ] Asset tambahan berasal dari source HTML halaman yang sama.
[ ] Tidak ada nama file/path rekaan.
[ ] Tidak ada folder assets ganda.
[ ] CSS modular hanya dimuat bila view membutuhkannya.
[ ] Plugin JS hanya dimuat bila fitur halaman membutuhkannya.
[ ] Tidak ada asset lintas boundary.
[ ] Tidak ada data private/restricted pada uploads-public.
```
