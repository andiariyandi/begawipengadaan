# BegawiPBJ Documentation and Database File Register

## Register Dokumentasi dan Artefak Database Platform Ekosistem Digital PBJ Pemerintah Provinsi Lampung

Dokumen ini adalah register resmi untuk dokumentasi, artefak database, source template UI, reference data, migration, seeder, dan file operasional terkait proyek **BegawiPBJ**.

Tujuan register adalah memastikan setiap file/direktori penting memiliki lokasi, status, klasifikasi data, fungsi, aturan penggunaan, dan keterkaitan yang jelas. Register ini tidak menggantikan isi dokumentasi teknis; register menunjukkan **file apa yang harus ada, berada di mana, dan bagaimana file tersebut dikelola**.

Dokumen ini berlaku untuk baseline fresh project CodeIgniter 4 AppStarter BegawiPBJ.

---

# 1. Status dan Konvensi Register

## 1.1 Status file

| Status | Arti |
|---|---|
| `required` | Wajib tersedia untuk baseline aplikasi/operasi terkait |
| `planned` | Direncanakan; dibuat sebelum fitur/proses terkait digunakan |
| `generated` | Dibuat aplikasi/build/deployment; tidak diedit manual kecuali prosedur menyatakan lain |
| `external` | Dikelola di luar repository atau berasal dari sumber resmi eksternal |
| `deprecated` | Tidak dipakai lagi; tidak boleh menjadi sumber kebenaran baru |
| `forbidden` | Tidak boleh disimpan atau di-commit pada repository |

## 1.2 Klasifikasi data

| Klasifikasi | Arti |
|---|---|
| `public` | Aman dipublikasikan atau di-commit |
| `internal` | Untuk tim proyek/instansi; tidak untuk publik tanpa persetujuan |
| `restricted` | Berisi/berpotensi memuat data pribadi, konfigurasi terbatas, atau bukti operasional |
| `secret` | Credential, key, token, password, atau rahasia sistem |
| `generated` | Artefak runtime/build dengan retensi yang diatur sistem |

## 1.3 Aturan perubahan

- Dokumentasi baseline diperbarui bersamaan dengan perubahan arsitektur, database, UI boundary, atau aturan bisnis terkait.
- Migration yang sudah diterapkan pada environment bersama tidak boleh diubah destruktif; perubahan schema memakai migration baru.
- Seeder development tidak boleh mengubah master wilayah resmi.
- File `secret`, data production, data ASN terbatas, dan upload user tidak boleh di-commit.
- Source template FiStudy dan WowDash dipertahankan sebagai katalog HTML/asset lengkap; route produksi tidak merender source reference secara langsung.
- Asset produksi merupakan hasil pemilihan, penyalinan, dan adaptasi dari source template ke `public/assets/main/` atau `public/assets/dashboard/`.

---

# 2. Register Dokumen Root

| No. | Lokasi file | Status | Klasifikasi | Fungsi | Keterkaitan/aturan |
|---:|---|---|---|---|---|
| 1 | `README.md` | required | public | Ringkasan produk, UI foundation, instalasi, modul, keamanan, dan operasi dasar | Harus konsisten dengan enam dokumen baseline lainnya |
| 2 | `begawipbj-directory-architecture.md` | required | internal | Arsitektur direktori, namespace, UI boundary, source template, service, filter, asset, testing | Sumber acuan penempatan kode/file produksi |
| 3 | `assessment-and-certification-rules.md` | required | internal | Aturan pre-test, quiz, assignment, post-test, nilai akhir, completion, feedback, eligibility, dan sertifikat | Sumber aturan bisnis assessment/certification |
| 4 | `begawipbj-database-blueprint.md` | required | internal | Prinsip, standar, domain, kebijakan data, migration, dan seeder database | Sinkron dengan ERD dan data dictionary |
| 5 | `begawipbj-database-erd.md` | required | internal | ERD dan relasi lintas domain | Sinkron dengan migration/data dictionary |
| 6 | `begawipbj-data-dictionary.md` | required | internal | Definisi tabel, kolom, tipe data, key, constraint, status, dan integritas | Referensi schema detail |
| 7 | `begawipbj-documentation-and-database-file-register.md` | required | internal | Register dokumentasi dan artefak database/source UI | Dokumen ini |
| 8 | `.env.example` | required | public | Template environment tanpa rahasia | Tidak memuat credential nyata, token, atau encryption key produksi |
| 9 | `composer.json` | required | public | Definisi dependency PHP dan metadata project | Perubahan dependency direview |
| 10 | `composer.lock` | required | public | Lock dependency PHP | Di-commit untuk reproducible build |
| 11 | `phpunit.xml.dist` | required | public | Konfigurasi dasar test | Tidak memuat credential production |
| 12 | `LICENSE` | planned | public | Lisensi aplikasi/komponen proyek | Ditentukan pemilik resmi BegawiPBJ/Pemerintah Provinsi Lampung |
| 13 | `.gitignore` | required | public | Melindungi file lokal, runtime, secret, dan artefak tidak boleh di-commit | Mencakup `.env`, writable runtime, upload, log, export, dump sensitif |

---

# 3. Register Dokumentasi Arsitektur

Direktori:

```text
docs/architecture/
```

| No. | Lokasi file | Status | Klasifikasi | Fungsi | Aturan |
|---:|---|---|---|---|---|
| 1 | `docs/architecture/directory-structure.md` | planned | internal | Publikasi struktur direktori bila dokumentasi dipisah dari root | Sinkron dengan `begawipbj-directory-architecture.md` |
| 2 | `docs/architecture/route-map.md` | planned | internal | Peta route publik, Workspace, Dashboard, dan domain management | Diperbarui saat route/group/filter berubah |
| 3 | `docs/architecture/rbac-matrix.md` | planned | restricted | Matriks role, permission, resource, action, dan scope | Sinkron dengan PermissionSeeder/filter/service |
| 4 | `docs/architecture/workflow-state-machine.md` | planned | internal | State machine approval, enrollment, assignment, certificate, dan event | Diperbarui saat status/transisi berubah |
| 5 | `docs/architecture/data-classification.md` | planned | restricted | Klasifikasi publik, internal, terbatas, dan secret | Acuan query statistik/export |
| 6 | `docs/architecture/assessment-state-machine.md` | planned | internal | State machine pre-test, quiz attempt, assignment revision, post-test, final grade, eligibility | Sinkron dengan assessment rules |
| 7 | `docs/architecture/certificate-lifecycle.md` | planned | internal | Lifecycle certificate: eligibility, pending, issued, expired, revoked, replacement | Sinkron dengan certificate service dan audit |
| 8 | `docs/architecture/ui-boundaries.md` | planned | internal | Boundary FiStudy/Main UI dan WowDash/Dashboard UI | Tidak boleh bertentangan dengan directory architecture |
| 9 | `docs/architecture/ADR-001-feature-folder-architecture.md` | planned | internal | Keputusan penggunaan `App\...` feature/domain folder | ADR append-only |
| 10 | `docs/architecture/ADR-002-ui-foundation-boundary.md` | planned | internal | Keputusan penggunaan FiStudy dan WowDash sebagai source template produksi | Memuat lisensi, asset, dan larangan direct rendering reference |

---

# 4. Register Dokumentasi Database

Direktori:

```text
docs/database/
```

| No. | Lokasi file | Status | Klasifikasi | Fungsi | Aturan |
|---:|---|---|---|---|---|
| 1 | `docs/database/database-blueprint.md` | planned | internal | Publikasi blueprint database | Sinkron dengan `begawipbj-database-blueprint.md` |
| 2 | `docs/database/erd.md` | planned | internal | Publikasi ERD | Sinkron dengan `begawipbj-database-erd.md` |
| 3 | `docs/database/data-dictionary.md` | planned | internal | Publikasi data dictionary | Sinkron dengan `begawipbj-data-dictionary.md` |
| 4 | `docs/database/migration-policy.md` | planned | internal | Aturan migration, rollback, naming, review, deployment schema | Wajib sebelum production deployment pertama |
| 5 | `docs/database/seeder-policy.md` | planned | internal | Pemisahan reference/system/development/import seeder | Wajib memuat larangan dummy seeder menyentuh `wilayah` |
| 6 | `docs/database/master-data-governance.md` | planned | restricted | Tata kelola wilayah, OPD, role, permission, competency, category | Perubahan master resmi terkontrol/auditable |
| 7 | `docs/database/retention-and-purge-policy.md` | planned | restricted | Retensi audit log, file, certificate, export, akun | Tidak menghapus bukti historis tanpa persetujuan |
| 8 | `docs/database/database-restore-runbook.md` | planned | restricted | Prosedur restore dan validasi pasca-restore | Diuji berkala non-production |
| 9 | `docs/database/query-performance-guidelines.md` | planned | internal | Panduan index, pagination, dashboard query, full-text search | Diperbarui berdasarkan profiling aktual |

---

# 5. Register Deployment dan Operasi

Direktori:

```text
docs/deployment/
```

| No. | Lokasi file | Status | Klasifikasi | Fungsi | Aturan |
|---:|---|---|---|---|---|
| 1 | `docs/deployment/environment.md` | planned | restricted | Daftar environment variable dan provisioning tanpa nilai rahasia | Tidak memuat nilai secret aktual |
| 2 | `docs/deployment/security-checklist.md` | planned | restricted | Checklist HTTPS, document root, permission, CSP, backup, logging, hardening | Ditinjau sebelum production deployment |
| 3 | `docs/deployment/backup-recovery.md` | planned | restricted | Backup database/storage dan recovery | Database dan uploads dibackup terpisah |
| 4 | `docs/deployment/release-runbook.md` | planned | internal | Release, migration, cache clear, health check, rollback | Migration production mengikuti policy database |
| 5 | `docs/deployment/incident-response.md` | planned | restricted | Respons insiden keamanan/kebocoran/kegagalan sertifikat | Akses terbatas |
| 6 | `docs/deployment/scheduled-jobs.md` | planned | internal | Notification retry, certificate expiry, temporary cleanup, backup verification | Setiap job mempunyai owner dan monitoring |

---

# 6. Register API dan Integrasi

Direktori:

```text
docs/api/
```

| No. | Lokasi file | Status | Klasifikasi | Fungsi | Aturan |
|---:|---|---|---|---|---|
| 1 | `docs/api/api-conventions.md` | planned | internal | Format response, pagination, error code, versioning, auth API | Tidak membuka endpoint internal tanpa review |
| 2 | `docs/api/openapi.yaml` | planned | internal | Kontrak OpenAPI untuk API yang disetujui | Dibuat bila API eksternal tersedia |
| 3 | `docs/api/integration-register.md` | planned | restricted | Register integrasi, owner, data, auth, SLA, risiko | Tidak memuat token/secret |
| 4 | `docs/api/webhook-policy.md` | planned | restricted | Signature, replay protection, retry, audit webhook | Hanya bila webhook digunakan |

---

# 7. Register Source Template UI dan Asset

## 7.1 Prinsip source template

FiStudy dan WowDash adalah source template lengkap. Keduanya berisi HTML siap pakai dan asset pendukung yang dapat diadaptasi untuk BegawiPBJ.

```text
FiStudy
→ Main UI: Landing Page, Public Area, authentication Main UI, Workspace

WowDash
→ Dashboard UI: authentication Dashboard, back-office Super Admin/Admin
```

Source template berada di `app/Views/reference/` sebagai katalog/adaptation source. File source reference tidak dirender oleh controller/route produksi. Hasil adaptasi ditempatkan pada `app/Views/main/`, `app/Views/dashboard/`, `public/assets/main/`, dan `public/assets/dashboard/`.

## 7.2 Source FiStudy aktual

| No. | Lokasi file/direktori | Status | Klasifikasi | Fungsi | Aturan |
|---:|---|---|---|---|---|
| 1 | `app/Views/reference/fistudy/` | required | internal | Root source template FiStudy; halaman HTML datar dan asset lengkap | Non-production; tidak dirender langsung route/controller |
| 2 | `app/Views/reference/fistudy/index.html` | required | internal | Home/landing template utama | Sumber adaptasi Landing Page |
| 3 | `app/Views/reference/fistudy/index-dark.html` | required | internal | Variasi landing dark | Adaptasi hanya bila tema dark diperlukan |
| 4 | `app/Views/reference/fistudy/index-one-page.html` | required | internal | Landing one-page | Sumber section/landing alternatif |
| 5 | `app/Views/reference/fistudy/index2.html` | required | internal | Variasi homepage kedua | Sumber adaptasi section/layout |
| 6 | `app/Views/reference/fistudy/index2-one-page.html` | required | internal | Variasi homepage kedua one-page | Sumber adaptasi section/layout |
| 7 | `app/Views/reference/fistudy/index3.html` | required | internal | Variasi homepage ketiga | Sumber adaptasi section/layout |
| 8 | `app/Views/reference/fistudy/index3-one-page.html` | required | internal | Variasi homepage ketiga one-page | Sumber adaptasi section/layout |
| 9 | `app/Views/reference/fistudy/course.html` | required | internal | Katalog course grid | Sumber Arena Belajar publik |
| 10 | `app/Views/reference/fistudy/course-list.html` | required | internal | Katalog course list | Sumber daftar/filter pembelajaran |
| 11 | `app/Views/reference/fistudy/course-carousel.html` | required | internal | Carousel course | Sumber section featured course |
| 12 | `app/Views/reference/fistudy/course-details.html` | required | internal | Detail course | Sumber detail course/class publik |
| 13 | `app/Views/reference/fistudy/events.html` | required | internal | Daftar event | Sumber agenda/event publik |
| 14 | `app/Views/reference/fistudy/events-carousel.html` | required | internal | Carousel event | Sumber event unggulan |
| 15 | `app/Views/reference/fistudy/event-details.html` | required | internal | Detail event | Sumber detail event dan CTA registrasi |
| 16 | `app/Views/reference/fistudy/blog.html` | required | internal | Blog grid | Sumber berita/Bunga Rampai |
| 17 | `app/Views/reference/fistudy/blog-list.html` | required | internal | Blog list | Sumber daftar berita/artikel |
| 18 | `app/Views/reference/fistudy/blog-carousel.html` | required | internal | Blog carousel | Sumber konten unggulan |
| 19 | `app/Views/reference/fistudy/blog-details.html` | required | internal | Detail blog | Sumber detail berita/artikel |
| 20 | `app/Views/reference/fistudy/faq.html` | required | internal | FAQ page | Sumber FAQ publik |
| 21 | `app/Views/reference/fistudy/login.html` | required | internal | Login template | Sumber auth Main UI |
| 22 | `app/Views/reference/fistudy/sign-up.html` | required | internal | Registrasi template | Sumber register Main UI |
| 23 | `app/Views/reference/fistudy/instructor.html` | required | internal | Daftar instructor | Sumber trainer/fasilitator bila relevan |
| 24 | `app/Views/reference/fistudy/instructor-carousel.html` | required | internal | Carousel instructor | Sumber trainer unggulan |
| 25 | `app/Views/reference/fistudy/instructor-details.html` | required | internal | Detail instructor | Sumber detail trainer bila diperlukan |
| 26 | `app/Views/reference/fistudy/about.html` | required | internal | About page | Sumber halaman tentang BegawiPBJ |
| 27 | `app/Views/reference/fistudy/contact.html` | required | internal | Contact page | Sumber kontak publik |
| 28 | `app/Views/reference/fistudy/gallery.html` | required | internal | Gallery | Sumber galeri kegiatan bila diperlukan |
| 29 | `app/Views/reference/fistudy/testimonials.html` | required | internal | Testimonials | Sumber testimoni bila diperlukan |
| 30 | `app/Views/reference/fistudy/testimonials-carousel.html` | required | internal | Carousel testimonials | Sumber section testimoni |
| 31 | `app/Views/reference/fistudy/404.html` | required | internal | Error page | Sumber adaptasi error Main UI |
| 32 | `app/Views/reference/fistudy/coming-soon.html` | required | internal | Coming soon page | Sumber adaptasi maintenance/pending page |
| 33 | `app/Views/reference/fistudy/become-a-teacher.html` | required | internal | CTA instructor | Sumber pendaftaran trainer bila kebijakan membutuhkan |
| 34 | `app/Views/reference/fistudy/pricing.html` | required | internal | Pricing template | Tidak menjadi route default; digunakan ulang hanya bila diperlukan |
| 35 | `app/Views/reference/fistudy/cart.html` | required | internal | Cart template | Tidak menjadi route default BegawiPBJ |
| 36 | `app/Views/reference/fistudy/checkout.html` | required | internal | Checkout template | Tidak menjadi route default BegawiPBJ |
| 37 | `app/Views/reference/fistudy/products.html` | required | internal | Product template | Tidak menjadi route default BegawiPBJ |
| 38 | `app/Views/reference/fistudy/product-details.html` | required | internal | Product detail template | Tidak menjadi route default BegawiPBJ |
| 39 | `app/Views/reference/fistudy/wishlist.html` | required | internal | Wishlist template | Tidak menjadi route default BegawiPBJ |
| 40 | `app/Views/reference/fistudy/assets/` | required | internal | Semua asset FiStudy | Source asset; jangan dipanggil langsung production |
| 41 | `app/Views/reference/fistudy/assets/css/` | required | internal | CSS utama/vendor FiStudy | Source adaptasi ke `public/assets/main/css/` |
| 42 | `app/Views/reference/fistudy/assets/css/module-css/` | required | internal | CSS modul FiStudy | Source adaptasi ke `public/assets/main/css/module-css/` |
| 43 | `app/Views/reference/fistudy/assets/fonts/` | required | internal | Font/icon font FiStudy | Source adaptasi ke `public/assets/main/fonts/` |
| 44 | `app/Views/reference/fistudy/assets/images/` | required | internal | Image, shape, background, favicon, resource FiStudy | Source adaptasi ke `public/assets/main/images/` |
| 45 | `app/Views/reference/fistudy/js/` | required | internal | JavaScript FiStudy | Source adaptasi ke `public/assets/main/js/` |
| 46 | `app/Views/reference/fistudy/js/gsap/` | required | internal | GSAP dan plugin | Source adaptasi ke `public/assets/main/js/gsap/` bila dipakai |
| 47 | `app/Views/reference/fistudy/inc/sendemail.php` | required | restricted | Source demo pengiriman email FiStudy | Tidak digunakan production; BegawiPBJ memakai CI4 Email/service sendiri |
| 48 | `app/Views/reference/fistudy/inc/PHPMailer/` | required | internal | Dependency/source PHPMailer bawaan template | Tidak digunakan langsung production; dependency dikelola Composer bila dibutuhkan |

## 7.3 Source WowDash aktual

| No. | Lokasi file/direktori | Status | Klasifikasi | Fungsi | Aturan |
|---:|---|---|---|---|---|
| 1 | `app/Views/reference/wowdash/` | required | internal | Root source template WowDash; halaman HTML datar dan asset lengkap | Non-production; tidak dirender langsung route/controller |
| 2 | `app/Views/reference/wowdash/index.html` | required | internal | Dashboard utama | Sumber Dashboard KPI internal |
| 3 | `app/Views/reference/wowdash/index-2.html` sampai `index-20.html` | required | internal | Variasi dashboard | Sumber chart/widget/layout sesuai kebutuhan |
| 4 | `app/Views/reference/wowdash/sign-in.html` | required | internal | Login dashboard | Sumber auth Dashboard UI |
| 5 | `app/Views/reference/wowdash/sign-up.html` | required | internal | Sign-up dashboard | Tidak menjadi route default; adaptasi bila dibutuhkan |
| 6 | `app/Views/reference/wowdash/forgot-password.html` | required | internal | Forgot password | Sumber reset password Dashboard |
| 7 | `app/Views/reference/wowdash/users-grid.html` | required | internal | User grid | Sumber user management |
| 8 | `app/Views/reference/wowdash/users-list.html` | required | internal | User list | Sumber user management |
| 9 | `app/Views/reference/wowdash/add-user.html` | required | internal | Add user form | Sumber form user internal |
| 10 | `app/Views/reference/wowdash/view-profile.html` | required | internal | View profile | Sumber profile detail internal |
| 11 | `app/Views/reference/wowdash/veiw-details.html` | required | internal | Detail template dengan nama source typo | Source tetap; hasil produksi memakai ejaan yang benar |
| 12 | `app/Views/reference/wowdash/assign-role.html` | required | internal | Assign role | Sumber assignment role |
| 13 | `app/Views/reference/wowdash/role-access.html` | required | internal | Role access | Sumber role/permission management |
| 14 | `app/Views/reference/wowdash/users-role-permission.html` | required | internal | User role/permission | Sumber RBAC management |
| 15 | `app/Views/reference/wowdash/form.html` | required | internal | Form controls | Sumber form component |
| 16 | `app/Views/reference/wowdash/form-layout.html` | required | internal | Form layout | Sumber form layout |
| 17 | `app/Views/reference/wowdash/form-validation.html` | required | internal | Form validation UI | Sumber validation presentation; validasi server tetap wajib |
| 18 | `app/Views/reference/wowdash/wizard.html` | required | internal | Multi-step form | Sumber wizard/admin flow |
| 19 | `app/Views/reference/wowdash/image-upload.html` | required | internal | Upload UI | Sumber upload presentation; secure upload tetap di service |
| 20 | `app/Views/reference/wowdash/table-basic.html` | required | internal | Table dasar | Sumber table component |
| 21 | `app/Views/reference/wowdash/table-data.html` | required | internal | Data table | Sumber data table/pagination/filter |
| 22 | `app/Views/reference/wowdash/pagination.html` | required | internal | Pagination | Sumber pagination component |
| 23 | `app/Views/reference/wowdash/column-chart.html` | required | internal | Chart kolom | Sumber chart internal |
| 24 | `app/Views/reference/wowdash/line-chart.html` | required | internal | Chart garis | Sumber chart internal |
| 25 | `app/Views/reference/wowdash/pie-chart.html` | required | internal | Chart pie | Sumber chart internal |
| 26 | `app/Views/reference/wowdash/calendar.html` | required | internal | Calendar | Sumber kalender internal |
| 27 | `app/Views/reference/wowdash/calendar-main.html` | required | internal | Calendar utama | Sumber agenda internal |
| 28 | `app/Views/reference/wowdash/kanban.html` | required | internal | Kanban | Sumber workflow/task visual bila diperlukan |
| 29 | `app/Views/reference/wowdash/notification.html` | required | internal | Notification page | Sumber notifikasi internal |
| 30 | `app/Views/reference/wowdash/notification-alert.html` | required | internal | Alert notification | Sumber alert component |
| 31 | `app/Views/reference/wowdash/invoice-add.html` | required | internal | Invoice add | Sumber dokumen administratif bila diperlukan |
| 32 | `app/Views/reference/wowdash/invoice-edit.html` | required | internal | Invoice edit | Sumber dokumen administratif bila diperlukan |
| 33 | `app/Views/reference/wowdash/invoice-list.html` | required | internal | Invoice list | Sumber daftar dokumen bila diperlukan |
| 34 | `app/Views/reference/wowdash/invoice-preview.html` | required | internal | Invoice preview | Sumber print/preview bila diperlukan |
| 35 | `app/Views/reference/wowdash/access-denied.html` | required | internal | Access denied | Sumber error Dashboard |
| 36 | `app/Views/reference/wowdash/bad-request.html` | required | internal | Bad request | Sumber error Dashboard |
| 37 | `app/Views/reference/wowdash/forbidden.html` | required | internal | Forbidden | Sumber error Dashboard |
| 38 | `app/Views/reference/wowdash/internal-server.html` | required | internal | Internal server error | Sumber error Dashboard |
| 39 | `app/Views/reference/wowdash/service-unavailable.html` | required | internal | Service unavailable | Sumber error Dashboard |
| 40 | `app/Views/reference/wowdash/error.html` | required | internal | Error template | Sumber error Dashboard |
| 41 | `app/Views/reference/wowdash/maintenance.html` | required | internal | Maintenance | Sumber maintenance Dashboard |
| 42 | `app/Views/reference/wowdash/coming-soon.html` | required | internal | Coming soon | Sumber status page Dashboard |
| 43 | `app/Views/reference/wowdash/blank-page.html` | required | internal | Blank page | Sumber starting point page internal |
| 44 | `app/Views/reference/wowdash/card.html` | required | internal | Card components | Sumber card component |
| 45 | `app/Views/reference/wowdash/widgets.html` | required | internal | Dashboard widgets | Sumber KPI widgets |
| 46 | `app/Views/reference/wowdash/alert.html` | required | internal | Alert component | Sumber feedback status UI |
| 47 | `app/Views/reference/wowdash/badges.html` | required | internal | Badge component | Sumber status badge |
| 48 | `app/Views/reference/wowdash/button.html` | required | internal | Button component | Sumber tombol UI |
| 49 | `app/Views/reference/wowdash/dropdown.html` | required | internal | Dropdown component | Sumber dropdown UI |
| 50 | `app/Views/reference/wowdash/progress.html` | required | internal | Progress component | Sumber progress UI |
| 51 | `app/Views/reference/wowdash/tabs.html` | required | internal | Tabs component | Sumber tabs UI |
| 52 | `app/Views/reference/wowdash/tooltip.html` | required | internal | Tooltip component | Sumber tooltip UI |
| 53 | `app/Views/reference/wowdash/assets/` | required | internal | Semua asset WowDash | Source asset; jangan dipanggil langsung production |
| 54 | `app/Views/reference/wowdash/assets/css/` | required | internal | CSS utama WowDash | Source adaptasi ke `public/assets/dashboard/css/` |
| 55 | `app/Views/reference/wowdash/assets/css/lib/` | required | internal | CSS vendor library | Source adaptasi ke `public/assets/dashboard/css/lib/` atau vendor build |
| 56 | `app/Views/reference/wowdash/assets/fonts/` | required | internal | Remixicon/font WowDash | Source adaptasi ke `public/assets/dashboard/fonts/` |
| 57 | `app/Views/reference/wowdash/assets/images/` | required | internal | Semua image WowDash | Source adaptasi ke `public/assets/dashboard/images/` |
| 58 | `app/Views/reference/wowdash/assets/js/` | required | internal | JS utama/chart/page WowDash | Source adaptasi ke `public/assets/dashboard/js/` |
| 59 | `app/Views/reference/wowdash/assets/js/lib/` | required | internal | JS vendor | Source adaptasi ke `public/assets/dashboard/js/lib/` atau vendor build |
| 60 | `app/Views/reference/wowdash/assets/sass/` | required | internal | SCSS source WowDash | Dipakai sebagai source build bila workflow SCSS diaktifkan |
| 61 | `app/Views/reference/wowdash/assets/sass/abstracts/` | required | internal | Variable/mixin/function SCSS | Source build internal |
| 62 | `app/Views/reference/wowdash/assets/sass/components/` | required | internal | Component SCSS | Source build internal |
| 63 | `app/Views/reference/wowdash/assets/sass/layout/` | required | internal | Layout SCSS | Source build internal |
| 64 | `app/Views/reference/wowdash/assets/sass/utilities/` | required | internal | Utility SCSS | Source build internal |
| 65 | `app/Views/reference/wowdash/assets/webfonts/` | required | internal | Font Awesome webfont | Source adaptasi ke `public/assets/dashboard/webfonts/` |

Halaman WowDash lainnya—misalnya `avatar.html`, `carousel.html`, `chat-*.html`, `code-generator*.html`, `colors.html`, `company.html`, `currencies.html`, `email.html`, `faq.html`, `gallery*.html`, `language.html`, `list.html`, `marketplace*.html`, `payment-gateway.html`, `portfolio.html`, `pricing.html`, `radio.html`, `star-rating.html`, `starred.html`, `switch.html`, `tags.html`, `terms-condition.html`, `testimonials.html`, `text-generator*.html`, `theme.html`, `typography.html`, `video-generator.html`, `videos.html`, `voice-generator.html`, dan `wallet.html`—tetap merupakan bagian source template yang tersedia untuk dipakai ulang/adaptasi jika fitur proyek membutuhkannya. Halaman tersebut tidak otomatis menjadi route produksi BegawiPBJ.

## 7.4 Asset produksi hasil integrasi

| No. | Direktori | Status | Klasifikasi | Fungsi | Aturan |
|---:|---|---|---|---|---|
| 1 | `public/assets/main/` | required | public | Asset produksi Main UI dari FiStudy | Dipakai Landing/Public/auth Main/Workspace |
| 2 | `public/assets/main/css/` | required | public | CSS FiStudy terpilih dan hasil penyesuaian | Gunakan path CI4/base URL |
| 3 | `public/assets/main/css/module-css/` | required | public | Module CSS FiStudy terpilih | Hanya salin modul yang digunakan |
| 4 | `public/assets/main/js/` | required | public | JavaScript FiStudy terpilih dan script BegawiPBJ | Hindari library tidak digunakan |
| 5 | `public/assets/main/js/gsap/` | planned | public | GSAP/plugin bila dipakai | Hanya jika feature membutuhkannya |
| 6 | `public/assets/main/fonts/` | required | public | Font/icon FiStudy yang digunakan | Lisensi wajib dicatat |
| 7 | `public/assets/main/images/` | required | public | Image/favicons/shapes FiStudy yang digunakan | Ganti branding/template image bila diperlukan |
| 8 | `public/assets/dashboard/` | required | public | Asset produksi Dashboard dari WowDash | Dipakai auth Dashboard dan back-office |
| 9 | `public/assets/dashboard/css/` | required | public | CSS WowDash terpilih/hasil build | Termasuk `style.css` dan custom output bila dipakai |
| 10 | `public/assets/dashboard/css/lib/` | required | public | CSS vendor dashboard | Hanya library yang digunakan |
| 11 | `public/assets/dashboard/js/` | required | public | JavaScript WowDash terpilih dan script BegawiPBJ | Page/chart script dimuat sesuai halaman |
| 12 | `public/assets/dashboard/js/lib/` | required | public | JS vendor dashboard | Hanya library yang digunakan |
| 13 | `public/assets/dashboard/fonts/` | required | public | Remixicon/font WowDash | Lisensi wajib dicatat |
| 14 | `public/assets/dashboard/webfonts/` | required | public | Webfont Font Awesome bila digunakan | Hanya file yang dibutuhkan |
| 15 | `public/assets/dashboard/images/` | required | public | Image WowDash yang digunakan | Ganti placeholder/template image bila perlu |
| 16 | `public/assets/shared/` | required | public | Logo/favicons/asset identitas yang lintas UI | Tidak menyimpan upload runtime |
| 17 | `public/assets/main/THIRD_PARTY_NOTICES.md` | planned | public | Attribution/lisensi asset pihak ketiga Main UI | Dibuat bila lisensi memerlukannya |
| 18 | `public/assets/dashboard/THIRD_PARTY_NOTICES.md` | planned | public | Attribution/lisensi asset pihak ketiga Dashboard UI | Dibuat bila lisensi memerlukannya |

---

# 8. Register Migration Database

Direktori:

```text
app/Database/Migrations/
```

| Urutan | Nama file baseline | Status | Klasifikasi | Fungsi utama | Dependensi |
|---:|---|---|---|---|---|
| 1 | `CreateIdentityAndRbacTables.php` | required | internal | Users, profiles, RBAC, reset token, login attempt | Tidak ada domain bisnis lain |
| 2 | `CreateOrganizationTables.php` | required | internal | Organization units, user organizations, struktur wilayah bila dikelola aplikasi | Identity/RBAC |
| 3 | `CreateSharedWorkflowAuditFileTables.php` | required | internal | File assets, approvals, histories, audit logs, notifications | Users |
| 4 | `CreateLearningCoreTables.php` | required | internal | Program/course/class, trainer/evaluator, module/lesson/material, enrollment/progress | Users, file assets |
| 5 | `CreateLearningAssessmentTables.php` | required | internal | Assessment config, quiz, pre-test, assignment, post-test, feedback, final grade | Learning core |
| 6 | `CreateKnowledgeTables.php` | required | internal | Knowledge category/tag/article/FAQ/case/template/bookmark | Users, file assets |
| 7 | `CreateRegulationTables.php` | required | internal | Regulation category, metadata, version, stage, competency, keyword, download log | Users, file assets |
| 8 | `CreateCommunicationTables.php` | required | internal | News, event, registration, attendance | Users, file assets |
| 9 | `CreateCertificateTables.php` | required | internal | Template, sequence, certificates, verification log, revocation | Users, file assets, enrollment/event registration |
| 10 | `CreateGovernmentIntelligenceTables.php` | required | internal | JF level/personnel/history, competency certificates, gap snapshot | Users, organization, file assets |
| 11 | `AddPerformanceIndexesAndConstraints.php` | required | internal | Index/constraint tambahan dan optimasi query | Semua domain terkait |

---

# 9. Register Seeder dan Import

```text
app/Database/Seeds/
├── DatabaseSeeder.php
├── Reference/
├── System/
├── Development/
└── Import/
```

| Lokasi | Status | Klasifikasi | Fungsi/aturan |
|---|---|---|---|
| `DatabaseSeeder.php` | required | internal | Orchestrator seeder aman; tidak menjalankan demo otomatis |
| `Reference/RoleSeeder.php` | required | internal | Role sistem, idempotent |
| `Reference/PermissionSeeder.php` | required | internal | Permission granular, sinkron RBAC matrix |
| `Reference/RolePermissionSeeder.php` | required | internal | Mapping baseline role-permission |
| `Reference/OrganizationSeeder.php` | planned | internal | Reference OPD/unit disetujui, bukan data personal demo |
| `Reference/KnowledgeCategorySeeder.php` | required | internal | Kategori Bunga Rampai |
| `Reference/RegulationCategorySeeder.php` | required | internal | Kategori regulasi |
| `Reference/ProcurementStageSeeder.php` | required | internal | Tahapan PBJ |
| `Reference/CompetencySeeder.php` | required | internal | Master kompetensi |
| `Reference/JfPpbjLevelSeeder.php` | required | internal | Jenjang JF PPBJ |
| `Reference/CompetencyCertificateTypeSeeder.php` | required | internal | Jenis sertifikat kompetensi |
| `System/InitialSuperAdminSeeder.php` | required | restricted | Super Admin awal dari environment aman, tanpa password hard-code |
| `System/SystemSettingSeeder.php` | required | internal | Pengaturan dasar sistem |
| `Import/WilayahImportSeeder.php` | required | restricted | Import wilayah resmi; bukan data dummy |
| `Import/OpdImportSeeder.php` | planned | restricted | Import OPD resmi dengan validasi/audit |
| `Development/*.php` | planned | internal/restricted | Demo development/testing; tidak boleh memakai data pribadi nyata dan tidak menyentuh `wilayah` |

---

# 10. Register Reference Data dan Sampel Database

```text
database/
├── reference/
├── archive/
└── sanitized-samples/
```

| Lokasi | Status | Klasifikasi | Fungsi/aturan |
|---|---|---|---|
| `database/reference/wilayah.sql` | required | restricted | Master wilayah resmi `wilayah(kode, nama)`; tidak dimodifikasi dummy seeder |
| `database/reference/README.md` | planned | internal | Sumber, versi, checksum, tanggal impor, prosedur import reference data |
| `database/reference/organization-units.csv` | planned | restricted | Reference OPD/unit resmi bila disetujui |
| `database/reference/organization-units.sha256` | planned | internal | Checksum source organisasi |
| `database/archive/legacy/` | planned | restricted | Arsip legacy yang disetujui; bukan dump production |
| `database/sanitized-samples/` | planned | public | Sample anonymized yang aman dibagikan |
| `database/sanitized-samples/schema-smoke-test.sql` | planned | public | SQL smoke test dokumentasi/schema; tidak menggantikan migration |

---

# 11. Register Konfigurasi Database dan Aplikasi

| Lokasi file | Status | Klasifikasi | Fungsi/aturan |
|---|---|---|---|
| `app/Config/Database.php` | required | internal | Koneksi database CI4 berbasis environment; tanpa credential production hard-code |
| `.env` | external | secret | Nilai aktual per environment; tidak pernah di-commit |
| `.env.example` | required | public | Template key environment tanpa secret |
| `app/Config/Validation.php` | required | internal | Konfigurasi validation framework |
| `app/Config/Certificate.php` | required | internal | Konfigurasi format sertifikat non-secret |
| `app/Config/FileStorage.php` | required | internal | Konfigurasi disk/path storage non-secret |
| `app/Config/Workflow.php` | required | internal | Status/transisi workflow |
| `app/Config/Notification.php` | required | internal | Channel notifikasi non-secret |
| `app/Config/Email.php` | required | internal | Konfigurasi email CI4; credential dari environment | 

---

# 12. Register Runtime Database dan Storage

| Lokasi | Status | Klasifikasi | Fungsi/aturan |
|---|---|---|---|
| `writable/cache/` | generated | generated | Cache runtime; tidak di-commit |
| `writable/logs/` | generated | restricted | Log aplikasi; akses/retensi dibatasi |
| `writable/session/` | generated | restricted | Session runtime bila file session digunakan |
| `writable/uploads/learning/` | generated | restricted | Materi/lampiran/assignment; akses melalui authorization |
| `writable/uploads/knowledge/` | generated | restricted | Attachment/template Bunga Rampai |
| `writable/uploads/regulations/` | generated | restricted | Original dan versi dokumen regulasi |
| `writable/uploads/communication/` | generated | restricted | File berita/event |
| `writable/uploads/certificates/` | generated | restricted | Template/PDF sertifikat generated |
| `writable/uploads/temporary/` | generated | restricted | File sementara; wajib cleanup job |
| `writable/exports/reports/` | generated | restricted | Export laporan; permission/audit/retensi |
| `writable/exports/spreadsheets/` | generated | restricted | Export spreadsheet; permission/audit/retensi |

Metadata seluruh file runtime disimpan pada `file_assets`.

```text
file_assets.path = NULL
→ Metadata tersedia, file fisik belum tersedia
→ UI/API menampilkan “File belum tersedia”
→ Download dan preview dinonaktifkan
```

---

# 13. Backup, Export, dan Artefak Terlarang

## 13.1 Backup dan export

| Artefak | Lokasi pengelolaan | Status | Klasifikasi | Aturan |
|---|---|---|---|---|
| Backup database production | Storage backup terenkripsi di luar repository | external | secret | Jadwal, retensi, restore test terdokumentasi |
| Backup uploads production | Storage backup terenkripsi di luar repository | external | restricted | Dipisahkan dari database |
| Export laporan internal | `writable/exports/` atau storage privat | generated | restricted | Permission, audit, dan retensi |
| PDF sertifikat | `writable/uploads/certificates/generated/` atau storage privat | generated | restricted | Bukan asset publik statis |
| Log aplikasi | `writable/logs/` atau platform logging | generated | restricted | Retensi dan akses dibatasi |

## 13.2 File terlarang di repository

| Pola/lokasi | Status | Alasan |
|---|---|---|
| `.env` | forbidden | Secret environment |
| `*.pem`, `*.key`, private certificate | forbidden | Private key/credential |
| Dump database production | forbidden | Data pribadi/secret |
| Data ASN mentah, NIP/NIK, scan bukti | forbidden | Data terbatas/pribadi |
| `writable/uploads/**` | forbidden | Upload runtime/bukti dokumen |
| `writable/logs/**` | forbidden | Log dapat memuat data sensitif |
| `writable/exports/**` | forbidden | Export dapat memuat data terbatas |
| Password/credential hard-code | forbidden | Risiko keamanan |
| Token API/SMTP credential | forbidden | Secret |

---

# 14. Matriks Keterkaitan Perubahan

| Perubahan | Dokumen/artefak yang minimal diperbarui |
|---|---|
| Menambah tabel/kolom/constraint | Migration baru, data dictionary, ERD, blueprint bila keputusan desain berubah, test |
| Mengubah relasi domain | ERD, data dictionary, migration, service, test |
| Mengubah status/workflow | Workflow state machine, data dictionary, service, test |
| Mengubah bobot/rules assessment | Assessment rules, data dictionary, ERD, migration bila schema berubah, service, test |
| Mengubah certificate eligibility/lifecycle | Assessment rules, certificate lifecycle, ERD, data dictionary, service, test |
| Menambah role/permission | RBAC matrix, PermissionSeeder, RolePermissionSeeder, filter/service, test |
| Menambah route/area UI | Route map, directory architecture, view/asset boundary, filter, test |
| Mengadaptasi halaman/asset FiStudy | Directory architecture, UI boundary/asset notice bila perlu, view/component production, asset production |
| Mengadaptasi halaman/asset WowDash | Directory architecture, UI boundary/asset notice bila perlu, view/component Dashboard, asset Dashboard |
| Menambah file upload domain | File storage config, data dictionary, storage register, authorization test |
| Mengubah master wilayah | Source/reference metadata, import procedure, data governance, audit; bukan development seeder |
| Mengubah statistik publik | Data classification, query/view, privacy test, dokumentasi statistik |

---

# 15. Checklist Kepatuhan Register

## 15.1 Baseline repository

- [ ] Ketujuh dokumen baseline tersedia pada root repository.
- [ ] `.env.example` tersedia tanpa secret.
- [ ] `.gitignore` mencegah `.env`, upload, log, export, dan dump sensitif masuk Git.
- [ ] `composer.lock` di-commit.
- [ ] `docs/`, `database/reference/`, dan `app/Database/` mengikuti struktur register.

## 15.2 Source template dan UI

- [ ] `app/Views/reference/fistudy/` menyimpan source HTML datar, `assets/`, `js/`, serta `inc/` sesuai template aktual.
- [ ] `app/Views/reference/wowdash/` menyimpan source HTML datar dan `assets/` sesuai template aktual.
- [ ] Tidak ada folder source fiktif seperti `reference/*/pages/`, `reference/*/parts/`, atau `reference/*/screenshots/` yang diklaim sebagai struktur template aktual.
- [ ] Route/controller produksi tidak merender view source reference.
- [ ] Hasil integrasi FiStudy berada di `app/Views/main/` dan `public/assets/main/`.
- [ ] Hasil integrasi WowDash berada di `app/Views/dashboard/` dan `public/assets/dashboard/`.
- [ ] `sendemail.php`/PHPMailer bawaan FiStudy tidak dipakai sebagai email production.
- [ ] Lisensi template/asset/vendor dicatat dan dipatuhi.

## 15.3 Database

- [ ] Migration domain tersedia dan urut berdasarkan dependency.
- [ ] Migration yang sudah diterapkan tidak diubah destruktif.
- [ ] `DatabaseSeeder` tidak menjalankan development seeder otomatis.
- [ ] `database/reference/wilayah.sql` tersedia dari sumber resmi/terverifikasi.
- [ ] Development seeder tidak menyentuh tabel `wilayah`.
- [ ] Data dictionary dan ERD sinkron dengan migration.

## 15.4 Assessment dan sertifikat

- [ ] Assessment rules mencatat pre-test satu kali, quiz best attempt, assignment revision, post-test 60%, passing grade minimal 70.
- [ ] Migration/data dictionary mendukung assessment configuration, pre-test, post-test, participant feedback, final grade.
- [ ] Sertifikat memakai `certificates(source_type, source_id)` sebagai source unik.
- [ ] Tidak ada tabel sertifikat event terpisah yang menduplikasi source certificate.
- [ ] Audit/test mencakup eligibility, issuance, verification, expiry, revocation.

## 15.5 Keamanan dan privasi

- [ ] Tidak ada secret, dump production, data ASN mentah, atau upload runtime di repository.
- [ ] File private disimpan pada writable/private storage dan disajikan melalui authorization.
- [ ] Kondisi `file_assets.path = NULL` ditangani sebagai file belum tersedia tanpa download aktif.
- [ ] Statistik publik hanya memakai data agregat tanpa data pribadi ASN.
- [ ] Backup database dan upload dikelola terpisah serta restore terdokumentasi.

---

# 16. Ringkasan Register Baseline

```text
Root baseline documents
├── README.md
├── begawipbj-directory-architecture.md
├── assessment-and-certification-rules.md
├── begawipbj-database-blueprint.md
├── begawipbj-database-erd.md
├── begawipbj-data-dictionary.md
└── begawipbj-documentation-and-database-file-register.md

UI source template (non-production)
├── app/Views/reference/fistudy/
│   ├── flat HTML pages
│   ├── assets/
│   ├── js/
│   └── inc/
└── app/Views/reference/wowdash/
    ├── flat HTML pages
    └── assets/

UI production result
├── app/Views/main/ + public/assets/main/
└── app/Views/dashboard/ + public/assets/dashboard/

Database source of truth
├── app/Database/Migrations/
├── app/Database/Seeds/
├── database/reference/wilayah.sql
└── docs/database/

Runtime only, never source-data repository
├── writable/cache/
├── writable/logs/
├── writable/session/
├── writable/uploads/
└── writable/exports/

Secret/external only
├── .env
├── production database backup
├── production upload backup
├── encryption/private keys
└── external service credentials
```
