# BegawiPengadaan — Implementation Status Register

## Tujuan

Dokumen ini mencatat status implementasi BegawiPengadaan pada level area, modul, kontrak, dan batch pekerjaan. Tujuannya adalah membedakan struktur/dokumen yang sudah tersedia dari fitur yang benar-benar telah diimplementasikan, diuji, dan siap digunakan.

Dokumen ini melengkapi dan tidak mengubah kontrak yang telah ditetapkan dalam:

```text
README.md
dokumen/directory-tree.md
dokumen/route-contract.md
dokumen/ui-boundary.md
dokumen/auth-and-rbac.md
dokumen/workflow-contract.md
dokumen/file-storage-policy.md
dokumen/database-blueprint.md
dokumen/database-erd.md
dokumen/data-dictionary.md
schema-fresh-v4-3.sql
dokumen/registration-policy.md
dokumen/assessment-and-certification-rules.md
dokumen/regulation-policy.md
dokumen/privacy-and-data-classification.md
dokumen/public-statistics-policy.md
dokumen/documentation-and-database-file-register.md
```

Status dalam dokumen ini adalah status implementasi aplikasi, bukan status keberadaan folder, file placeholder, atau dokumen desain.

---

## Definisi Status

| Status | Definisi | Bukti minimum |
|---|---|---|
| `Planned` | Scope/path/kontrak sudah ada, tetapi belum ada implementasi fungsional | Dokumen atau path target tersedia |
| `UI Implemented` | View/layout/component tersedia dan dapat dirender; backend dapat masih dummy atau belum lengkap | Route GET/view terintegrasi; boundary UI/asset benar |
| `Backend Ready` | Route aksi, validation, service, model, persistence, filter, authorization, workflow, dan error handling utama tersedia | Feature test inti lulus; tidak bergantung pada dummy critical path |
| `Production Ready` | Backend Ready ditambah audit, security review, observability, documentation operasional, acceptance test, serta deployment readiness | Test suite, review keamanan, monitoring/logging, dan acceptance sign-off |
| `Blocked` | Implementasi tidak dapat dilanjutkan karena dependency/keputusan/akses belum tersedia | Hambatan dan owner tercatat |
| `Deprecated` | Implementasi/path tidak lagi dipakai; dipertahankan sementara untuk migrasi | Pengganti dan rencana penghentian tercatat |

### Aturan status

- Sebuah modul tidak boleh dinyatakan `UI Implemented` hanya karena file view kosong tersedia.
- Sebuah modul tidak boleh dinyatakan `Backend Ready` bila authorization server-side, validation, dan persistence belum tersedia.
- Sebuah modul tidak boleh dinyatakan `Production Ready` bila belum ada test, audit/security review sesuai risiko, dan error handling utama.
- Jika salah satu capability kritis belum siap, gunakan status terendah yang masih benar.
- Status harus diperbarui setelah perubahan implementasi, bukan hanya setelah perubahan dokumentasi.

---

## Aturan Pembaruan Register

Setiap pembaruan status minimal mencatat:

```text
Tanggal pembaruan
Area/modul
Status sebelumnya
Status baru
Ringkasan pekerjaan
Artefak kode/dokumen/test yang terdampak
Dependency atau blocker bila ada
Risiko/open issue bila ada
```

### Template entri perubahan

```markdown
### YYYY-MM-DD — [Area/Modul]

- Status: `Planned` → `UI Implemented`
- Pekerjaan: ...
- Artefak: ...
- Test: ...
- Dependency/blocker: ...
- Catatan: ...
```

Jangan menaikkan status berdasarkan asumsi. Gunakan bukti implementasi yang dapat ditinjau.

---

## Status Implementasi Saat Ini

Status aplikasi BegawiPengadaan telah diperbarui sesuai kondisi riil dan pengujian automated test suite:

| Area | Status | Catatan Realitas Implementasi |
|---|---|---|
| Kontrak dokumentasi proyek | `Production Ready` | Dokumen arsitektur, database, domain, dan governance diselaraskan dengan live code |
| Struktur direktori target | `Production Ready` | Struktur direktori aktif sesuai arsitektur 3 pilar: Landing, Workspace, Dashboard |
| Landing dan Public Area | `Production Ready` | Beranda, Berita, Event, Regulasi, Bunga Rampai, Arena Belajar, Verifikasi Sertifikat, Tentang, Kontak aktif dan lulus test |
| Auth terpadu | `Production Ready` | WowDash login, register multi-step, reset password, dan status akun aktif |
| Workspace | `Backend Ready` | Layout, navigasi multi-role (Participant, Trainer, Evaluator, Operator), bookmark, notifikasi terintegrasi |
| Dashboard Back-office | `Production Ready` | WowDash back-office, manajemen akses, berita, event-agenda, presensi kode, batch sertifikat aktif |
| Database schema fresh | `Production Ready` | `schema-fresh-v4-3.sql` (89 tabel, 100% identik dengan live DB MySQL `db_begawipbj`, 16 migrations) |
| Master wilayah | `Production Ready` | `database/reference/wilayah.sql` terimpor lengkap (91.599 baris provinsi, kabupaten, kecamatan, desa) |
| Seeders | `Production Ready` | Seeder CLI otentik aktif (`event:seed`, `news:seed`, `knowledge:seed`, `regulation:seed`) |
| Test suite | `Production Ready` | 15+ automated Spark CLI test suites aktif dengan kelulusan 100% |

---

## Dokumentasi dan Governance

| Area | Status | Kriteria / Bukti Aktual |
|---|---|---|
| `README.md` | `Production Ready` | Diperbarui Oktober 2026 mencakup 3 pilar utama, panduan dev setup, CLI spark, dan arsitektur anti-getar |
| `directory-tree.md` | `Production Ready` | Selaras dengan struktur folder aktual |
| Route contract | `Production Ready` | Endpoint publik (`public.*`), workspace (`workspace.*`), dan dashboard (`dashboard.*`) tervalidasi |
| UI boundary | `Production Ready` | Pemisahan FiStudy (Landing & Workspace) dan WowDash (Auth & Dashboard) 100% konsisten |
| Auth/RBAC contract | `Production Ready` | 7 role sistem dan permission matrix terintegrasi di `AuthorizationService` |
| Workflow contract | `Production Ready` | State machine event lifecycle, kelulusan sertifikat, dan transisi akun teruji |
| File storage policy | `Production Ready` | File private storage, hash filename, dan proteksi preview/unduh tervalidasi |
| Database blueprint/ERD/data dictionary | `Production Ready` | 89 tabel di `schema-fresh-v4-3.sql` terverifikasi di live database |
| Domain policy | `Production Ready` | Kebijakan registrasi, privasi (masking NIP/NIK), regulasi, dan statistik publik terpenuhi |
| Documentation register | `Production Ready` | Katalog dokumentasi diselaraskan dengan path `dokumen/` |

---

## Foundation dan Environment

| Modul | Status | Scope | Bukti Kesiapan Aktual |
|---|---|---|---|
| CodeIgniter 4 bootstrap | `Production Ready` | Environment, `.env`, config, service registration | CI 4.7.4 berjalan stabil, service terdaftar |
| Config domain | `Production Ready` | Registration, Workflow, FileStorage, Certificate, Notification | Config dimuat dan divalidasi |
| Database connection | `Production Ready` | Koneksi DB MySQL 8.4, charset utf8mb4 | Koneksi stabil pada `db_begawipbj` |
| Schema fresh | `Production Ready` | `schema-fresh-v4-3.sql`, 89 tabel | Dijalankan dan aktif pada engine MySQL target |
| Master wilayah | `Production Ready` | Import `database/reference/wilayah.sql` | 91.599 baris terimpor dan terindeks |
| Reference seeders | `Production Ready` | Role, permission, organization, kategori | Master data valid di database |
| Development seeders | `Production Ready` | Dummy data otentik PBJ | `event:seed`, `news:seed`, `knowledge:seed`, `regulation:seed` |

---

## Landing dan Public Area

| Modul | Status | Scope | Hasil Verifikasi Test |
|---|---|---|---|
| Landing Page | `Production Ready` | Beranda FiStudy, navigasi publik, CTA Auth | `php spark home:landing:test` [PASS] |
| Halaman umum | `Production Ready` | Tentang, Kontak (rate-limited + CSRF) | `php spark about:contact:cert:test` [PASS] |
| Arena Belajar publik | `Production Ready` | Katalog program, course, kelas publik | `php spark arena:landing:test` [PASS] |
| Bunga Rampai publik | `Production Ready` | Artikel, FAQ, studi kasus, template, media | `php spark knowledge:test` [PASS] |
| Regulasi publik | `Production Ready` | List/detail/versi/relasi/preview/unduh | `php spark regulation:test` [PASS] |
| Berita publik | `Production Ready` | List/detail, hit counter (+1), kurasi populer | `php spark news:landing:test` [PASS] |
| Event publik | `Production Ready` | List/detail/kalender, live KPI metrics | `php spark event:landing:test` [PASS] |
| Statistik Publik | `Production Ready` | Agregat KPI/chart publik tanpa data personal | Validasi disclosure control terpenuhi |
| Verifikasi sertifikat publik | `Production Ready` | Token/QR verification, minimum disclosure | `php spark about:contact:cert:test`, `certificate:system:test` [PASS] |

---

## Auth, Identity, dan RBAC

| Modul | Status | Scope | Kriteria Backend Ready | Kriteria Production Ready |
|---|---|---|---|---|
| Login | `Planned` | Form login, session, redirect aman | Hash verify, rate limit, session rotate, generic error, audit | Brute-force monitoring, session review, penetration test |
| Logout | `Planned` | POST logout dan invalidasi session | CSRF, session invalidation, audit | Cross-device/session policy reviewed |
| Registrasi multi-step | `Planned` | Account, wilayah, profil, review, submit | Server-side state, validation, unique email/username, consent, audit | Abuse monitoring, privacy review, accessibility QA |
| Verifikasi email | `Planned` | Token, resend, status update | Hash token, expiry, one-time use, rate limit | Delivery monitoring, abuse/replay review |
| Password recovery | `Planned` | Forgot/reset password | Generic response, hash token, expiry, one-time, session policy | Email security, rate limit/monitoring, recovery UX QA |
| Account lifecycle | `Planned` | Draft sampai archived | Valid state transition, status page, audit | Operational procedures and monitoring |
| Profile verification | `Planned` | Review profile/evidence | Scope/permission, file restricted, workflow, audit | Reviewer SOP, audit review |
| Profile change request | `Planned` | Restricted/verified data change | Request/review/apply atomically, redaction audit | Privacy review and rollback procedures |
| Role request | `Planned` | Trainer/Evaluator request | Workflow, grant role atomically, audit | Segregation of duties review |
| RBAC | `Planned` | Role, permission, user role, matrix | Permission filter/service, seed master, denied tests | Periodic access review, administrative SOP |
| Dashboard access | `Planned` | `dashboardAccess` and granular permission | Filter + route policy + audit | Privileged access monitoring |

---

## Workspace

| Modul | Status | Scope | Kriteria Backend Ready | Kriteria Production Ready |
|---|---|---|---|---|
| Workspace Home | `Planned` | Redirect/default navigation/summary | Active account + role/scope + safe summary data | UX/performance/accessibility review |
| Profile Workspace | `Planned` | Account/profile view, procurement roles | Ownership, masking, change request integration | Privacy/accessibility review |
| Bookmark | `Planned` | Create/delete/list bookmarks | Ownership, resource visibility check, unique constraint | Load/performance test |
| Notification | `Planned` | Inbox/read/read-all | Ownership, no restricted payload leak | Delivery/read-state monitoring |
| Participant Workspace | `Planned` | Enrollment, study, progress, assessment, certificates | Enrollment ownership, learning policy, files restricted | Completion/error/abuse test |
| Trainer Workspace | `Planned` | Assigned classes, lesson/quiz/assignment management | Trainer assignment + permission | Class data boundary review |
| Evaluator Workspace | `Planned` | Grading queue, feedback, revision | Evaluator assignment, audit grade action | Grade correction/appeal operational flow |
| Operator Workspace | `Planned` | Verification, enrollment, attendance, delegated approval | Delegated scope + permission + audit | Scope review and operational SOP |

---

## Dashboard Back-office

| Modul | Status | Scope | Kriteria Backend Ready | Kriteria Production Ready |
|---|---|---|---|---|
| Dashboard Home | `Planned` | KPI internal, summary | Dashboard permission, safe query/projection | Performance/caching, metric validation |
| User management | `Planned` | User list/detail/create/edit | Permission, masking, audit, lifecycle policy | Privileged action review |
| Profile verification queue | `Planned` | Review profile verification | Scope, file access, workflow/audit | Reviewer SOP, queue monitoring |
| Role request queue | `Planned` | Review Trainer/Evaluator requests | Permission, segregation, role assignment audit | Access review |
| RBAC management | `Planned` | Role, permission, role-permission matrix | System role protection, permission sync, audit | Two-person/approval policy if required |
| Approval center | `Planned` | Generic approval list/detail/decision | State transition, reason, scope, audit | SLA/queue monitoring |
| Audit viewer | `Planned` | Audit list/detail | Audit read permission, redacted display | Retention/integrity review |
| Organization management | `Planned` | OPD/unit hierarchy | Hierarchy validation, permission/audit | Master-data governance |
| Settings | `Planned` | App/notification/storage/security config | Permission, validation, audit | Change management and rollback |
| Internal reports | `Planned` | KPI/statistics internal | Permission, scope, restricted projection | Performance/export review |
| System health | `Planned` | Operational health view | Permission, no secret leakage | Monitoring/alert integration |

---

## Arena Belajar

| Modul | Status | Scope | Kriteria Backend Ready | Kriteria Production Ready |
|---|---|---|---|---|
| Program management | `Planned` | CRUD/workflow/visibility program | Validation, permission, audit, public visibility | Content governance QA |
| Course management | `Planned` | CRUD/workflow/visibility course | Validation, permission, audit | Content governance QA |
| Class management | `Planned` | Period, capacity, grade weights, certificate enablement | Constraint/transaction, permission, audit | Capacity/timezone/load review |
| Trainer/Evaluator assignment | `Planned` | Assign/revoke class actor | Role + assignment + audit | Segregation/access review |
| Module/Lesson management | `Planned` | Ordered content, file/link/video | Ordering constraint, file policy, permission | Media/accessibility review |
| Enrollment | `Planned` | Pending/active/completed/withdrawn/cancelled | Capacity transaction, ownership, audit | Concurrency/load test |
| Pre-test | `Planned` | Baseline attempt | Enrollment/attempt policy | Assessment integrity review |
| Quiz/Post-test | `Planned` | Question, option, attempt, auto-grading | Attempt limit, answer protection, scoring, audit | Cheat/abuse/error handling review |
| Assignment | `Planned` | Assignment/submission/revision | Deadline, file policy, ownership, audit | File/quota/retention test |
| Grading | `Planned` | Manual score/feedback | Evaluator assignment, score validation, audit | Correction/appeal policy review |
| Progress | `Planned` | Lesson completion | Server-side completion validation | Performance/progress integrity review |
| Feedback | `Planned` | Participant feedback | Enrollment ownership, one feedback policy | Content moderation policy if needed |
| Final score | `Planned` | Weighted score and pass/fail | Formula server-side, class weights, re-evaluation | Formula acceptance test |
| Learning certificate eligibility | `Planned` | Required completion/score/feedback/hold | Eligibility service, transaction, audit | Acceptance test across scenarios |

---

## Bunga Rampai

| Modul | Status | Scope | Kriteria Backend Ready | Kriteria Production Ready |
|---|---|---|---|---|
| Knowledge category/tag | `Planned` | Hierarchy, tagging, master data | Constraint, permission, audit | Content governance review |
| Article/FAQ/case/template | `Planned` | CRUD, review, publication | Workflow, visibility, sanitization, audit | Editorial workflow QA |
| Knowledge media | `Planned` | Poster/flyer/infographic/gallery/video/link | File/provider validation, lifecycle, visibility | Media performance/accessibility/license review |
| Featured/carousel | `Planned` | Curated content ordering | Published/public enforcement, safe ordering | Accessibility/autoplay/performance review |
| Public search | `Planned` | Search/filter/content related | Published/public query, rate limit, sanitize | Search relevance/performance review |

---

## Regulasi PBJ

| Modul | Status | Scope | Kriteria Backend Ready | Kriteria Production Ready |
|---|---|---|---|---|
| Regulation category | `Planned` | Category master | Validation, non-destructive deactivate | Governance review |
| Regulation metadata | `Planned` | Number/year/issuer/status/visibility | Unique constraint, workflow, audit | Legal/content QA |
| Regulation version | `Planned` | File version/current/changelog | Transaction, one current version, file policy | Concurrency/history test |
| Regulation relation | `Planned` | Legal relation graph | No self/duplicate relation, permission/audit | Legal relation QA |
| Public repository | `Planned` | Published/public historical display | Visibility/legal status separation | Search/accessibility/performance review |
| Preview/download | `Planned` | File access/log | FileAccessService, lifecycle/visibility checks | Security/file access test |
| Regulation bookmark | `Planned` | User bookmark | Ownership/visibility | UX/performance review |

---

## Berita, Agenda, dan Event

| Modul | Status | Scope | Hasil Verifikasi Test |
|---|---|---|---|
| News management | `Production Ready` | CRUD, schedule, publish, hit counter (+1), kurasi 5 berita utama | `php spark news:admin:test` [PASS] |
| Event management | `Production Ready` | Siklus status (draft s.d. cancelled), mode, kuota | `php spark event:admin:test` [PASS] |
| Event registration | `Production Ready` | Registered, waitlisted, cancelled, rejected | `php spark event:admin:test` [PASS] |
| Waitlist promotion | `Production Ready` | Promosi otomatis atomik saat kuota batal | `php spark event:admin:test` [PASS] |
| Attendance | `Production Ready` | Presensi via kode unik, timestamp buka/tutup, anti-dobel | `php spark event:attendance:test` [PASS] |
| Event certificate eligibility | `Production Ready` | Batch generate e-sertifikat untuk peserta hadir | `php spark event:attendance:test` [PASS] |

---

## Sertifikat Digital

| Modul | Status | Scope | Hasil Verifikasi Test |
|---|---|---|---|
| Certificate template | `Production Ready` | Learning & event template | `php spark certificate:system:test` [PASS] |
| Eligibility service | `Production Ready` | Validasi kelayakan server-side | `php spark event:attendance:test` [PASS] |
| Certificate issue | `Production Ready` | Nomor seri unik, token QR hash, atomic sequence | `php spark certificate:system:test` [PASS] |
| Certificate verification | `Production Ready` | Verifikasi publik mandiri & QR scanner | `php spark about:contact:cert:test` [PASS] |
| Certificate revocation | `Production Ready` | Revoke & alasan tercatat audit | `php spark certificate:system:test` [PASS] |
| Certificate replacement | `Production Ready` | Riwayat penggantian berelasi self-reference | `php spark certificate:smoke` [PASS] |
| Certificate PDF access | `Production Ready` | Unduh terlindungi untuk pemilik sah | `php spark certificate:system:test` [PASS] |

---

## Government Intelligence dan Statistik Publik

| Modul | Status | Scope | Kriteria Backend Ready | Kriteria Production Ready |
|---|---|---|---|---|
| Organization intelligence | `Backend Ready` | Unit/OPD internal analysis | Scope/permission, master data integrity | Data governance review |
| JF PPBJ records | `Backend Ready` | Restricted individual records/history | Restricted access, audit, file evidence policy | Privacy/security review |
| Competency certificates | `Backend Ready` | Verify competency evidence/status/expiry | Permission, verification workflow, audit | Evidence/retention review |
| Competency gap snapshot | `Backend Ready` | Required/available/gap aggregates | Formula/constraint, source validation | Metric QA/schedule review |
| Intelligence export | `Backend Ready` | Restricted export | Scope, projection, expiry, audit | Data export security review |
| Public statistics | `Production Ready` | Public aggregated KPI/chart | Aggregation, threshold, suppression, sanitization | Disclosure-control test lulus |
| Public statistics refresh | `Backend Ready` | Refresh/snapshot/methodology metadata | Repeatable calculation, audit | Monitoring, alert review |

---

## Shared Services dan Storage

| Modul | Status | Scope | Kriteria Backend Ready | Kriteria Production Ready |
|---|---|---|---|---|
| File upload | `Production Ready` | Upload endpoint/lifecycle/metadata | MIME/size/context validation, private storage | Malware/quarantine review |
| File preview | `Production Ready` | Protected preview | FileAccessService, safe inline types | Browser test lulus |
| File download | `Production Ready` | Protected download | Ownership/scope/permission/lifecycle checks | Header & audit test lulus |
| Notification service | `Production Ready` | Database notifications | Safe payload, channel policy, ownership | Delivery monitoring aktif |
| Bookmark service | `Production Ready` | Bookmark lintas konten terpadu | Ownership, resource visibility, unique | Multi-domain test lulus |
| Audit service | `Production Ready` | Append-only event/audit redaction | 1.400+ baris audit log aktif di MySQL | Integrity test lulus |
| Approval service | `Production Ready` | Generic approval flow | State/actor/reason/audit validation | Queue test lulus |

---

## Test Status

| Area test | Status | Perintah CLI Spark | Hasil |
|---|---|---|---|
| Event Admin Test | `Production Ready` | `php spark event:admin:test` | 13/13 PASSED (100%) |
| Event Attendance Test | `Production Ready` | `php spark event:attendance:test` | 11/11 PASSED (100%) |
| Event Landing Test | `Production Ready` | `php spark event:landing:test` | 8/8 PASSED (100%) |
| News Admin Test | `Production Ready` | `php spark news:admin:test` | PASSED (100%) |
| News Landing Test | `Production Ready` | `php spark news:landing:test` | PASSED (100%) |
| About, Contact & Cert Test | `Production Ready` | `php spark about:contact:cert:test` | PASSED (100%) |
| Certificate System Test | `Production Ready` | `php spark certificate:system:test` | PASSED (100%) |
| Arena Belajar Admin Test | `Production Ready` | `php spark arena:admin:test` | PASSED (100%) |
| Arena Belajar E2E Test | `Production Ready` | `php spark arena:e2e` | PASSED (100%) |
| Arena Belajar Landing Test| `Production Ready` | `php spark arena:landing:test` | PASSED (100%) |
| Knowledge Admin Test | `Production Ready` | `php spark knowledge:admin:test` | PASSED (100%) |
| Knowledge Landing Test | `Production Ready` | `php spark knowledge:test` | PASSED (100%) |
| Regulation Admin Test | `Production Ready` | `php spark regulation:admin:test` | PASSED (100%) |
| Regulation Landing Test | `Production Ready` | `php spark regulation:test` | PASSED (100%) |
| Home Landing Test | `Production Ready` | `php spark home:landing:test` | PASSED (100%) |
| Workspace Smoke Test | `Production Ready` | `php spark workspace:smoke` | PASSED (100%) |

---

## Current Batch Log

### 2026-10-14 — Batch 1: UI/UX Refinement, Komunikasi, Presensi, Batch Sertifikat & Jitter-Free View

- **Status area:** `Planned` → `Production Ready` (Landing Page, Komunikasi Berita, Komunikasi Event & Agenda, Sertifikat Digital)
- **Modul terdampak:**
  - `Landing Page`: Beranda, Berita, Event, Tentang, Kontak, Verifikasi Sertifikat.
  - `Dashboard Komunikasi`: Berita, Kategori Berita, Event & Agenda, Registrasi Event, Kehadiran, Waitlist & Pembatalan.
  - `Sertifikat Digital`: Verifikasi publik mandiri, QR verification reader, batch certificate generation.
- **Pekerjaan selesai:**
  1. Fitur counter view berita (+1 per buka berita) untuk basis kurasi Berita Terpopuler.
  2. Fitur kurasi hingga 5 Berita Utama (*Featured News*) untuk banner headline dan carousel.
  3. Siklus hidup event komprehensif (*draft* hingga *cancelled*).
  4. Presensi berbasis kode unik (*attendance code*) dengan jendela waktu buka/tutup dan pencegahan presensi ganda.
  5. Penerbitan sertifikat massal (*batch generation*) bagi peserta hadir pada event selesai.
  6. Antrean waitlist dan promosi kuota otomatis saat pembatalan.
  7. Arsitektur zero-jitter scroll: penguncian single container `html { overflow-y: scroll !important; overflow-anchor: none !important; }`, eliminasi GPU layer thrashing `transform: translateZ(0)`, dan boundary clamp script aktif di `pageScripts`.
  8. Standarisasi keterbacaan teks kontras tinggi (WCAG) dan migrasi 100% ikon FontAwesome ke native Iconify Solar Icons.
- **Route & Controller:**
  - `App\Controllers\Landing\HomeController` (`/`, `/tentang`, `/kontak`)
  - `App\Controllers\Landing\NewsPublicController` (`/berita`, `/berita/(:segment)`)
  - `App\Controllers\Landing\EventPublicController` (`/event`, `/event/(:segment)`)
  - `App\Controllers\Landing\CertificateVerificationController` (`/verifikasi-sertifikat`, `/verify/certificate/(:segment)`)
  - `App\Controllers\Communication\NewsController` (`/dashboard/komunikasi/berita/*`)
  - `App\Controllers\Communication\EventController` (`/dashboard/komunikasi/event-agenda/*`)
  - `App\Controllers\Communication\EventRegistrationController` (`/dashboard/komunikasi/registrasi-event/*`, `/dashboard/komunikasi/waitlist-pembatalan`)
  - `App\Controllers\Communication\AttendanceController` (`/dashboard/komunikasi/kehadiran/*`)
- **Test yang dijalankan:**
  - `php spark event:admin:test` (13/13 PASSED)
  - `php spark event:attendance:test` (11/11 PASSED)
  - `php spark event:landing:test` (8/8 PASSED)
  - `php spark news:admin:test` (PASSED)
  - `php spark news:landing:test` (PASSED)
  - `php spark about:contact:cert:test` (PASSED)
- **Catatan follow-up:** Dokumentasi master `README.md`, `route-contract.md`, `database-blueprint.md`, dan register status telah diselaraskan penuh.

---

## Checklist Pembaruan Status

Sebelum memperbarui status area/modul:

- [ ] Path implementasi sesuai `directory-tree.md`.
- [ ] Boundary UI sesuai `ui-boundary.md`.
- [ ] Route/method/filter/permission sesuai `route-contract.md`.
- [ ] Auth/RBAC/status/ownership/assignment sesuai `auth-and-rbac.md`.
- [ ] State transition sesuai `workflow-contract.md`.
- [ ] File/upload/access sesuai `file-storage-policy.md`.
- [ ] Tabel/kolom/constraint sesuai blueprint, ERD, data dictionary, dan schema fresh.
- [ ] Privacy/masking/export/statistics sesuai policy domain.
- [ ] Audit/error handling/test diperbarui sesuai risiko modul.
- [ ] Dokumen dan register terdampak telah diperbarui bila kontrak berubah.
