# BegawiPengadaan — Documentation and Database File Register

> **Metadata Dokumen:**
> - **Versi:** 4.2 (Production Ready)
> - **Tanggal Sinkronisasi:** 7 November 2026
> - **Sumber Kebenaran:** MySQL Live `db_begawipbj` (95 Tabel, 1.127 Kolom, 22 CHECK Constraints, 17 Migrasi) & Codebase Aktual

---

## 1. Tujuan & Ruang Lingkup

Dokumen ini adalah register inventarisasi resmi untuk seluruh artefak dokumentasi arsitektur teknis, berkas skema DDL, berkas migrasi database resmi (17 file), seeder, naskah SQL referensi wilayah, dan katalog izin RBAC pada proyek BegawiPengadaan. Dokumen ini memastikan ketertelusuran aset, mencegah duplikasi path, dan menjaga keselarasan mutlak antara kode produksi, skema database riil, dan dokumentasi tertulis.

---

## 2. Register 18 Dokumen Teknis Arsitektur (`dokumen/*.md`)

| No | Path Kanonikal | Kelompok | Status | Deskripsi Fungsi Utama |
|---|---|---|:---:|---|
| 1 | `dokumen/database-blueprint.md` | A. Basis Data | Aktif (V4.2) | Arsitektur lengkap 95 tabel, 58 CHECK constraints, 157 Foreign Keys, dan strategi indexing. |
| 2 | `dokumen/data-dictionary.md` | A. Basis Data | Aktif (V4.2) | Kamus data lengkap 95 tabel dan 1.127 kolom hasil ekstraksi langsung information_schema. |
| 3 | `dokumen/database-erd.md` | A. Basis Data | Aktif (V4.2) | 9 Diagram relasi entitas Mermaid interaktif per domain bisnis. |
| 4 | `dokumen/documentation-and-database-file-register.md` | A. Basis Data | Aktif (V4.2) | Register inventarisasi resmi seluruh dokumen dan berkas database. |
| 5 | `dokumen/auth-and-rbac.md` | B. Otorisasi & Privasi | Aktif (V4.2) | Arsitektur otorisasi 90 permissions, zoneMap filter, Zero Stale Session, dan Dynamic Mutation Guard. |
| 6 | `dokumen/auth-migration-plan.md` | B. Otorisasi & Privasi | Aktif (V4.2) | Dokumentasi arsitektur autentikasi terpadu (login/register 9 jalur/reset) di WowDash. |
| 7 | `dokumen/registration-policy.md` | B. Otorisasi & Privasi | Aktif (V4.2) | Kebijakan validasi dan verifikasi 9 jalur registrasi pengguna ekosistem PBJ. |
| 8 | `dokumen/privacy-and-data-classification.md` | B. Otorisasi & Privasi | Aktif (V4.2) | Klasifikasi data publik/internal/rahasia, perlindungan PII NIK/NIP, dan minimum disclosure. |
| 9 | `dokumen/file-storage-policy.md` | B. Otorisasi & Privasi | Aktif (V4.2) | Standarisasi 13 kolom file_assets, Atomic File Rollback, dan path traversal containment. |
| 10 | `dokumen/route-contract.md` | C. Kontrak & Alur | Aktif (V4.2) | Pemetaan 388 rute aktif, metode HTTP, controller handler, named route, dan filter. |
| 11 | `dokumen/workflow-contract.md` | C. Kontrak & Alur | Aktif (V4.2) | State machine siklus hidup events, kuis timeout, approvals, dan artikel terbit. |
| 12 | `dokumen/assessment-and-certification-rules.md` | C. Kontrak & Alur | Aktif (V4.2) | Aturan pembobotan nilai LMS, quiz re-attempt, kelayakan, dan TTE e-sertifikat. |
| 13 | `dokumen/regulation-policy.md` | C. Kontrak & Alur | Aktif (V4.2) | Hierarki peraturan perundang-undangan PBJ, relasi hukum, dan manajemen versi berkas. |
| 14 | `dokumen/public-statistics-policy.md` | C. Kontrak & Alur | Aktif (V4.2) | Metodologi perhitungan KPI beranda, pelacakan IP visitor, dan survei kepuasan. |
| 15 | `dokumen/ui-boundary.md` | D. UI & Implementasi | Aktif (V4.2) | Isolasi mutlak FiStudy (assets/main) vs WowDash (assets/dashboard) & audit:ui-boundary. |
| 16 | `dokumen/directory-tree.md` | D. UI & Implementasi | Aktif (V4.2) | Pohon direktori aktual produksi (78 Model, 49 Service, 20 Test/Audit commands). |
| 17 | `dokumen/directory-tree-create.md` | D. UI & Implementasi | Aktif (V4.2) | Panduan standar scaffolding & arsitektur saat membuat fitur baru. |
| 18 | `dokumen/implementation-status.md` | D. UI & Implementasi | Aktif (V4.2) | Matriks kesiapan produksi 9 pilar modul dan bukti kelulusan 20 Spark CLI test. |

---

## 3. Register 17 Berkas Migrasi Database Resmi (`app/Database/Migrations/`)

Seluruh berkas migrasi berikut dijaga dalam kebijakan **Zero Migration Drift** (tepat 17 berkas migrasi):

| No | Nama Berkas Migrasi | Batch | Deskripsi Penambahan Skema |
|---|---|:---:|---|
| 1 | `2026-10-05-000001_AddProcurementStagesAndPivotTables.php` | 1 | Tabel tahapan pengadaan dan tabel pivot regulasi-tahapan |
| 2 | `2026-10-09-000001_AddCertificateSequencesTable.php` | 2 | Tabel certificate_sequences untuk nomor urut sertifikat resmi |
| 3 | `2026-10-10-000001_AddAttendanceCodeToEvents.php` | 3 | Kolom attendance_code dan attendance_code_hash pada events |
| 4 | `2026-10-11-000001_AddLearningEnhancementFields.php` | 4 | Peningkatan kolom penunjang modul dan kelas pembelajaran |
| 5 | `2026-10-12-000001_AddKnowledgeEnhancementFields.php` | 5 | Peningkatan metadata pustaka media dan naskah pengetahuan |
| 6 | `2026-10-12-000002_AddRegulationEnhancementFields.php` | 6 | Peningkatan metadata riwayat versi dan relasi hukum regulasi |
| 7 | `2026-10-14-000001_AddNewsEnhancementFields.php` | 7 | Peningkatan fitur berita dan integrasi media berita PBJ |
| 8 | `2026-10-14-000002_CreateVisitorStatisticsTable.php` | 8 | Tabel visitor_statistics dan visitor_ip_logs untuk analitik kunjungan |
| 9 | `2026-10-18-000001_CreateSatisfactionSurveysTable.php` | 9 | Tabel satisfaction_surveys dan survey_aspects untuk survei kepuasan publik |
| 10 | `2026-10-19-000001_AddDurationMinutesToQuizzes.php` | 10 | Kolom duration_minutes pada tabel quizzes untuk server-side exam timer |
| 11 | `2026-10-19-000002_CreateLandingCmsTables.php` | 11 | Tabel CMS beranda (home_sliders, testimonials, team_members, dsb.) |
| 12 | `2026-10-19-000003_AddClassCoverAndSpotlightFields.php` | 12 | Kolom cover_file_id dan spotlight pada modul kelas PBJ |
| 13 | `2026-10-19-000004_AddKnowledgeMediaSlidesAndSpotlightFields.php` | 13 | Kolom carousel dan spotlight pada pustaka media Bunga Rampai |
| 14 | `2026-10-19-000005_AddRegulationCarouselFields.php` | 14 | Kolom carousel_enabled dan carousel_order pada regulasi |
| 15 | `2026-10-19-000006_AddNewsAndEventCarouselFields.php` | 15 | Kolom carousel_enabled dan carousel_order pada berita dan agenda event |
| 16 | `2026-10-19-000007_AddLearningCarouselFields.php` | 16 | Kolom carousel_enabled dan carousel_order pada program dan kursus |
| 17 | `2026-10-29-000001_CreateRupAuditTables.php` | 17 | Tabel Automated RUP Audit (rup_audit_batches, packages, clusters, violations, summaries) |

---

## 4. Register Berkas Seeder Database (`app/Database/Seeds/`)

| No | Nama Berkas Seeder | Fungsi Operasional |
|---|---|---|
| 1 | `PermissionCatalogSeeder.php` | Mengisi dan menyinkronkan 90 katalog permission ke tabel permissions & role_permissions |
| 2 | `LandingCmsSeeder.php` | Memuat konten awal rekomendasi CMS landing page (slider, testimoni, portal eksternal) |
| 3 | `AuditRemediationSeeder.php` | Menyisipkan data perbaikan hasil audit forensik dan relasi unit organisasi |

---

## 5. Register Berkas Basis Data Utama & Referensi

| No | Path Berkas | Tipe | Fungsi |
|---|---|---|---|
| 1 | `schema-fresh-v4-3.sql` | SQL DDL Dump | Skema DDL lengkap produksi mencakup seluruh 95 tabel dan constraint dasar |
| 2 | `database/reference/wilayah.sql` | SQL Data Dump | Data master wilayah administratif provinsi, kab/kota, kecamatan, desa se-Indonesia |
| 3 | `database/permissions_catalog.json` | JSON Catalog | Sumber kebenaran (Ground Truth) 90 permission lintas 12 domain sistem |
| 4 | `database/permissions_sync.sql` | SQL Seed Script | Naskah SQL untuk eksekusi cepat pembaruan permission pada lingkungan server |
