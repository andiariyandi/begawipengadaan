# BegawiPBJ — Implementation Status Register

## Tujuan

Dokumen ini mencatat status implementasi BegawiPBJ pada level area, modul, kontrak, dan batch pekerjaan. Tujuannya adalah membedakan struktur/dokumen yang sudah tersedia dari fitur yang benar-benar telah diimplementasikan, diuji, dan siap digunakan.

Dokumen ini melengkapi dan tidak mengubah kontrak yang telah ditetapkan dalam:

```text
README.md
directory-tree.md
docs/architecture/route-contract.md
docs/architecture/ui-boundary.md
docs/architecture/auth-and-rbac.md
docs/architecture/workflow-contract.md
docs/architecture/file-storage-policy.md
docs/database/database-blueprint.md
docs/database/database-erd.md
docs/database/data-dictionary.md
docs/database/schema-fresh-v4.sql
docs/domain/registration-policy.md
docs/domain/assessment-and-certification-rules.md
docs/domain/regulation-policy.md
docs/domain/privacy-and-data-classification.md
docs/domain/public-statistics-policy.md
docs/project/documentation-and-database-file-register.md
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

## Status Baseline Saat Ini

Baseline ini mencerminkan bahwa kontrak dokumentasi dan struktur target telah tersedia. Status aplikasi masih `Planned` kecuali suatu batch implementasi kemudian dicatat secara eksplisit di bawah ini.

| Area | Status | Catatan baseline |
|---|---|---|
| Kontrak dokumentasi proyek | `Backend Ready` | Dokumen arsitektur, database, domain, dan governance telah tersedia sebagai baseline |
| Struktur direktori target | `Planned` | Struktur ditetapkan; keberadaan path implementasi tidak sama dengan fitur selesai |
| Landing dan Public Area | `Planned` | Kontrak UI/route tersedia; fitur publik belum dinyatakan fungsional |
| Auth terpadu | `Planned` | Route, UI boundary, RBAC, workflow, dan policy tersedia; kode auth belum dinyatakan siap |
| Workspace | `Planned` | Boundary dan route contract tersedia; fitur per role belum dinyatakan siap |
| Dashboard Back-office | `Planned` | Boundary, RBAC, dan route contract tersedia; CRUD/admin belum dinyatakan siap |
| Database schema fresh | `UI Implemented` | Artefak SQL schema telah tersedia; belum dinyatakan teruji pada target engine/deployment |
| Database migrations | `Planned` | Nama/urutan migration ditetapkan; implementation migration belum dinyatakan selesai |
| Seeders | `Planned` | Strategi dan path seeder ditetapkan; seed data belum dinyatakan selesai |
| Test suite | `Planned` | Struktur test ditetapkan; coverage dan hasil test belum dinyatakan tersedia |

---

## Dokumentasi dan Governance

| Area | Status | Kriteria kenaikan status berikutnya |
|---|---|---|
| `README.md` | `Backend Ready` | Diperbarui jika scope/boundary berubah; tidak memerlukan kode untuk tetap valid |
| `directory-tree.md` | `Backend Ready` | Diselaraskan jika path target berubah |
| Route contract | `Backend Ready` | Route aktual divalidasi terhadap contract saat implementasi dimulai |
| UI boundary | `Backend Ready` | Layout/asset aktual diuji sesuai boundary |
| Auth/RBAC contract | `Backend Ready` | Implementation auth/filter/permission diuji terhadap policy |
| Workflow contract | `Backend Ready` | Semua state transition di service/migration/test tervalidasi |
| File storage policy | `Backend Ready` | Upload/access/retention/quarantine diimplementasikan dan diuji |
| Database blueprint/ERD/data dictionary | `Backend Ready` | Migration/schema aktual diverifikasi konsisten |
| Domain policy | `Backend Ready` | Implementasi masing-masing domain mematuhi policy |
| Documentation register | `Backend Ready` | Register diperbarui pada tiap artefak baru/perubahan material |

---

## Foundation dan Environment

| Modul | Status | Scope | Kriteria Backend Ready | Kriteria Production Ready |
|---|---|---|---|---|
| CodeIgniter 4 bootstrap | `Planned` | Environment, `.env`, config, service registration | Aplikasi boot, config environment aman, error environment terkendali | Deployment config, secret management, logging/monitoring siap |
| Config domain | `Planned` | Registration, Workflow, FileStorage, Certificate, Notification, KnowledgeMedia | Config dimuat, tervalidasi, dan dipakai service terkait | Review konfigurasi produksi, change management, observability |
| Database connection | `Planned` | Koneksi DB, charset utf8mb4, UTC, migration setup | Koneksi, migration runner, transaction bekerja | Backup/restore, monitoring, access control DB tervalidasi |
| Schema fresh | `UI Implemented` | `schema-fresh-v4.sql`, tabel aplikasi, tabel `wilayah` kosong | Berhasil dijalankan pada engine target; FK/check/index diverifikasi | Backup/restore drill, migration compatibility, performance smoke test |
| Master wilayah | `Planned` | Import `database/reference/wilayah.sql` | Data diimpor manual; lookup/foreign key valid | Validasi integrity, checksum/version register, deployment procedure siap |
| Reference seeders | `Planned` | Role, permission, organization, kategori, kompetensi, level | Seeder idempotent, data master valid | Review production seed dan audit perubahan master |
| System seeders | `Planned` | Initial Super Admin, system settings | Credential aman dari environment; seed berfungsi | Provisioning deployment aman dan terdokumentasi |
| Development seeders | `Planned` | Dummy data non-production | Data demo dapat dibuat/reset tanpa personal data nyata | Tidak dipakai production |

---

## Landing dan Public Area

| Modul | Status | Scope | Kriteria Backend Ready | Kriteria Production Ready |
|---|---|---|---|---|
| Landing Page | `Planned` | Beranda FiStudy, navigasi publik, CTA Auth | Controller/view/data public/published, error/empty state | SEO, accessibility, performance, content review |
| Halaman umum | `Planned` | Tentang, Kontak, Privasi, Syarat, Aksesibilitas | Route/view/form kontak rate limited + CSRF | Copy legal final, spam monitoring, accessibility review |
| Arena Belajar publik | `Planned` | Program, course, instructor katalog publik | Hanya content public/published, search/filter aman | Performance/caching, SEO, accessibility, content QA |
| Bunga Rampai publik | `Planned` | Artikel, FAQ, case study, template, media | Publication/visibility enforcement | Media policy, search performance, accessibility QA |
| Regulasi publik | `Planned` | List/detail/version/relation/search | Published/public + legal status + file policy | Search tuning, historical policy QA, file access review |
| Berita publik | `Planned` | List/detail berita published | Publication visibility enforcement | SEO, schedule publish, content QA |
| Event publik | `Planned` | List/detail event public | Status event/visibility enforcement | Timezone/event UX, capacity monitoring |
| Statistik Publik | `Planned` | Aggregate KPI/chart publik | Aggregation, threshold, suppression, response sanitization | Disclosure-control review, methodology publication, monitoring |
| Verifikasi sertifikat publik | `Planned` | Token/QR certificate verification | Hash lookup, rate limit, minimum disclosure | Security test, abuse monitoring, copy/status QA |

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

| Modul | Status | Scope | Kriteria Backend Ready | Kriteria Production Ready |
|---|---|---|---|---|
| News management | `Planned` | CRUD, schedule, publish, archive | Workflow, sanitization, permission/audit | Editorial QA, SEO review |
| Event management | `Planned` | Event mode, period, capacity, status | Date/period validation, workflow/audit | Timezone/capacity QA |
| Event registration | `Planned` | Registered/waitlisted/cancelled/rejected | Transaction, capacity, ownership, notification | Concurrency/load test |
| Waitlist promotion | `Planned` | Auto/manual promotion | Atomic promotion/no overbooking | Queue/SLA monitoring |
| Attendance | `Planned` | Present/absent/excused | Operator scope, audit correction | Operational attendance QA |
| Event certificate eligibility | `Planned` | Finished + registered + present | Re-evaluation on correction | End-to-end test |

---

## Sertifikat Digital

| Modul | Status | Scope | Kriteria Backend Ready | Kriteria Production Ready |
|---|---|---|---|---|
| Certificate template | `Planned` | Learning/event template and background | File policy, permission, version/active status | Template QA, render test |
| Eligibility service | `Planned` | Learning/event eligibility | All conditions evaluated server-side | Scenario acceptance test |
| Certificate issue | `Planned` | Number/token/QR/PDF/issued state | Transaction, unique source, hash token, file restricted, audit | Render/load/security review |
| Certificate verification | `Planned` | Public verification by token/QR | Hash lookup, rate limit, minimum disclosure | Abuse/security/accessibility review |
| Certificate revocation | `Planned` | Revoke and reason | Workflow, audit, public status update | Operational/legal SOP |
| Certificate replacement | `Planned` | Replace revoked certificate | History, uniqueness, audit | Edge-case/concurrency test |
| Certificate PDF access | `Planned` | Owner/issuer protected download | File access policy, visibility/lifecycle | Download authorization test |

---

## Government Intelligence dan Statistik Publik

| Modul | Status | Scope | Kriteria Backend Ready | Kriteria Production Ready |
|---|---|---|---|---|
| Organization intelligence | `Planned` | Unit/OPD internal analysis | Scope/permission, master data integrity | Data governance review |
| JF PPBJ records | `Planned` | Restricted individual records/history | Restricted access, audit, file evidence policy | Privacy/security review |
| Competency certificates | `Planned` | Verify competency evidence/status/expiry | Permission, verification workflow, audit | Evidence/retention review |
| Competency gap snapshot | `Planned` | Required/available/gap aggregates | Formula/constraint, source validation | Metric QA/schedule review |
| Intelligence export | `Planned` | Restricted export | Scope, projection, expiry, audit | Data export security review |
| Public statistics | `Planned` | Public aggregated KPI/chart | Aggregation, threshold, suppression, response sanitization | Disclosure-control acceptance test |
| Public statistics refresh | `Planned` | Refresh/snapshot/methodology metadata | Repeatable calculation, audit | Monitoring, alert, methodology review |

---

## Shared Services dan Storage

| Modul | Status | Scope | Kriteria Backend Ready | Kriteria Production Ready |
|---|---|---|---|---|
| File upload | `Planned` | Upload endpoint/lifecycle/metadata | MIME/size/context validation, private storage | Malware/quarantine/abuse review |
| File preview | `Planned` | Protected preview | FileAccessService, safe inline types | Browser/security test |
| File download | `Planned` | Protected download | Ownership/scope/permission/lifecycle checks | Header, audit, performance test |
| File quarantine | `Planned` | Reject/quarantine lifecycle | Access revocation, audit/security event | Incident procedure review |
| File cleanup | `Planned` | Temporary/expired file cleanup | Retention job and safe delete | Monitoring/restore review |
| Export service | `Planned` | Queue/generate/ready/expired/deleted | Scope/projection/private file/expiry | Load/security/retention review |
| Notification service | `Planned` | Database/email notifications | Safe payload, channel policy, ownership | Delivery monitoring and retry policy |
| Search service | `Planned` | Public/internal search | Visibility/scope query policy, rate limit | Relevance/performance review |
| Audit service | `Planned` | Append-only event/audit redaction | Required events, redaction, query access | Integrity/retention/monitoring review |
| Approval service | `Planned` | Generic approval flow | State/actor/reason/audit validation | Queue/SLA/operational review |

---

## Test Status

| Area test | Status | Target minimum |
|---|---|---|
| Migration/database tests | `Planned` | Fresh schema, FK, constraint, index, seed integrity |
| Auth feature tests | `Planned` | Login/logout/session/status/token/rate limit/access denial |
| RBAC feature tests | `Planned` | Role/permission/ownership/assignment/dashboard access |
| Workflow tests | `Planned` | Valid/invalid transition, actor permission, reason/audit |
| File storage tests | `Planned` | Upload validation, lifecycle, visibility, preview/download, quarantine, retention |
| Learning tests | `Planned` | Enrollment, attempt, grading, final score, eligibility |
| Regulation tests | `Planned` | Visibility, legal/workflow state, version/relation/file access |
| Event tests | `Planned` | Capacity, waitlist, attendance, certificate eligibility |
| Certificate tests | `Planned` | Issue uniqueness, verification, revoke, replacement |
| Statistics tests | `Planned` | Aggregate, suppression, threshold, response sanitization |
| Security tests | `Planned` | CSRF, XSS/output encoding, authorization, mass assignment, upload abuse |

---

## Current Batch Log

Tidak ada batch implementasi aplikasi yang dicatat sebagai selesai pada baseline ini. Ketika pekerjaan dimulai, catat perubahan di bawah menggunakan template berikut.

### Template log batch

```markdown
### YYYY-MM-DD — Batch [Nomor]: [Nama]

- Status area: `Planned` → `[Status Baru]`
- Modul terdampak: ...
- Pekerjaan selesai: ...
- Route/controller/view/model/service/migration/test: ...
- Kontrak yang divalidasi: ...
- Test yang dijalankan: ...
- Blocker/risiko/open issue: ...
- Catatan follow-up: ...
```

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
