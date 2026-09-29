# BegawiPBJ RBAC Matrix

## Role-Based Access Control Platform Ekosistem Digital PBJ Pemerintah Provinsi Lampung

Dokumen ini menetapkan matriks Role-Based Access Control (RBAC) untuk BegawiPBJ. RBAC mengatur siapa yang dapat melihat, membuat, mengubah, menyetujui, menerbitkan, menghapus, mengunduh, dan mengekspor data pada setiap area aplikasi.

Kontrol akses BegawiPBJ menerapkan empat lapisan:

```text
Authentication
↓
Account Status Validation
↓
Role Validation
↓
Permission Validation
```

Visibilitas menu tidak menggantikan authorization. Setiap route, controller action, endpoint AJAX/API, download file, dan service sensitif wajib memverifikasi akses di sisi server.

---

# 1. Role Sistem

| Code | Role | Area Utama | Fungsi Ringkas |
|---|---|---|---|
| `super_admin` | Super Admin | Dashboard | Akses penuh administrasi, konfigurasi, role, permission, audit, dan seluruh modul |
| `admin` | Admin | Dashboard | Pengelolaan konten, pembelajaran, regulasi, event, sertifikat, approval, laporan, dan statistik internal |
| `operator` | Operator | Main Workspace | Verifikasi data, administrasi, enrollment, dan approval delegasi |
| `trainer` | Trainer | Main Workspace | Pengelolaan pembelajaran dan kelas yang ditugaskan |
| `evaluator` | Evaluator | Main Workspace | Penilaian manual dan feedback kelas yang ditugaskan |
| `participant` | Peserta | Main Workspace | Mengikuti pembelajaran, assessment, event, dan mengakses data sendiri |
| `public_user` | Public User | Landingpage | Informasi publik dan verifikasi sertifikat |

## Aturan role

- Satu user dapat memiliki lebih dari satu role.
- Role menentukan area antarmuka awal, tetapi permission menentukan tindakan spesifik.
- Role `super_admin`, `admin`, dan `operator` hanya dapat diberikan kepada user dengan kategori profil `asn`.
- Trainer dan Evaluator harus memiliki penugasan kelas untuk mengakses data kelas tertentu, kecuali memiliki permission administrasi yang lebih tinggi.
- Public User tidak memiliki akses Workspace atau Dashboard.

---

# 2. Status Akun

| Status | Login | Landingpage | Workspace | Dashboard | Keterangan |
|---|---:|---:|---:|---:|---|
| `pending` | Tidak | Ya | Tidak | Tidak | Menunggu verifikasi/approval akun |
| `active` | Ya | Ya | Sesuai role | Sesuai role | Akun aktif |
| `inactive` | Tidak | Ya | Tidak | Tidak | Akun dinonaktifkan |
| `rejected` | Tidak | Ya | Tidak | Tidak | Registrasi ditolak |
| `suspended` | Tidak | Ya | Tidak | Tidak | Akses ditangguhkan |

`AccountStatusFilter` diterapkan setelah autentikasi. Akun nonaktif tidak boleh mengakses URL Workspace atau Dashboard meskipun memiliki session lama yang belum kedaluwarsa.

---

# 3. Area Akses

| Area | URL Prefix | Role yang Diizinkan | Filter Minimum |
|---|---|---|---|
| Landingpage publik | `/`, `/arena-belajar/*`, `/bunga-rampai/*`, `/regulasi/*`, `/berita/*`, `/agenda/*` | Semua | Public/optional rate limit |
| Verifikasi sertifikat | `/sertifikat/verifikasi/*` | Semua | Rate limit + validation |
| Authentication | `/login`, `/register`, `/password/*` | Guest | Guest + rate limit + CSRF |
| Workspace | `/workspace/*` | Operator, Trainer, Evaluator, Peserta | Auth + accountStatus + workspace + role/permission |
| Dashboard | `/admin/*` | Super Admin, Admin | Auth + accountStatus + admin + permission |
| Manajemen pembelajaran | `/learning/*` | Berdasarkan permission | Auth + accountStatus + permission |
| Manajemen pengetahuan | `/knowledge/*` | Berdasarkan permission | Auth + accountStatus + permission |
| Manajemen regulasi | `/regulation/*` | Berdasarkan permission | Auth + accountStatus + permission |
| Manajemen komunikasi | `/communication/*` | Berdasarkan permission | Auth + accountStatus + permission |
| Manajemen sertifikat | `/certificates/*` | Berdasarkan permission | Auth + accountStatus + permission |
| Intelligence internal | `/intelligence/*` | Berdasarkan permission | Auth + accountStatus + permission |

---

# 4. Permission Catalog

Permission memakai format:

```text
<domain>.<resource>.<action>
```

Contoh:

```text
learning.course.publish
regulation.version.upload
certificate.issue
```

## 4.1 Identity dan Access Control

| Permission | Deskripsi |
|---|---|
| `users.view` | Melihat daftar dan detail pengguna |
| `users.create` | Membuat akun pengguna secara administratif |
| `users.update` | Mengubah data akun dan status pengguna |
| `users.assign_role` | Memberikan atau mencabut role pengguna |
| `roles.manage` | Mengelola role |
| `permissions.manage` | Mengelola katalog permission dan mapping role |
| `audit.view` | Melihat audit log |
| `settings.manage` | Mengubah konfigurasi aplikasi |

## 4.2 Arena Belajar

| Permission | Deskripsi |
|---|---|
| `learning.program.manage` | Membuat dan mengelola program |
| `learning.course.manage` | Membuat dan mengelola course |
| `learning.course.publish` | Menyetujui/publish course |
| `learning.class.manage` | Membuat dan mengelola kelas |
| `learning.lesson.manage` | Mengelola module, lesson, dan materi |
| `learning.quiz.manage` | Mengelola quiz, pertanyaan, dan opsi |
| `learning.assignment.manage` | Mengelola assignment |
| `learning.assignment.grade` | Memberi nilai assignment dan feedback |
| `learning.enrollment.manage` | Meninjau dan mengelola enrollment |
| `learning.grade.finalize` | Menghitung/menetapkan nilai akhir dan kelulusan |
| `learning.progress.view` | Melihat progres kelas/peserta sesuai scope |

## 4.3 Bunga Rampai

| Permission | Deskripsi |
|---|---|
| `knowledge.article.manage` | Membuat dan mengelola artikel |
| `knowledge.article.publish` | Menyetujui/publish artikel |
| `knowledge.faq.manage` | Mengelola FAQ |
| `knowledge.case_study.manage` | Mengelola studi kasus |
| `knowledge.template.manage` | Mengelola template dokumen |
| `knowledge.category.manage` | Mengelola kategori dan tag |

## 4.4 Regulasi PBJ

| Permission | Deskripsi |
|---|---|
| `regulation.manage` | Membuat dan mengelola metadata regulasi |
| `regulation.publish` | Menyetujui/publish regulasi |
| `regulation.version.upload` | Mengunggah versi dokumen regulasi |
| `regulation.category.manage` | Mengelola kategori regulasi |
| `regulation.metadata.manage` | Mengelola tahap PBJ, kompetensi, keyword, dan metadata |
| `regulation.download.log.view` | Melihat log download regulasi |

## 4.5 Berita dan Event

| Permission | Deskripsi |
|---|---|
| `communication.news.manage` | Mengelola berita |
| `communication.news.publish` | Menyetujui/publish berita |
| `communication.event.manage` | Mengelola event dan agenda |
| `communication.event.publish` | Menyetujui/publish event |
| `communication.event.registration.manage` | Mengelola registrasi dan waitlist event |
| `communication.event.attendance` | Mengelola absensi event |
| `communication.event.certificate.manage` | Mengelola eligibility sertifikat event |

## 4.6 Sertifikat Digital

| Permission | Deskripsi |
|---|---|
| `certificate.view` | Melihat daftar sertifikat sesuai scope |
| `certificate.issue` | Menerbitkan sertifikat |
| `certificate.reissue` | Membuat ulang file sertifikat pada record yang sama |
| `certificate.revoke` | Membatalkan sertifikat |
| `certificate.verify` | Mengakses proses verifikasi internal/publik |
| `certificate.template.manage` | Mengelola template sertifikat |
| `certificate.sequence.manage` | Mengelola format/sequence nomor sertifikat |

## 4.7 Government Intelligence PBJ

| Permission | Deskripsi |
|---|---|
| `intelligence.view` | Melihat dashboard intelligence internal |
| `intelligence.export` | Mengunduh laporan intelligence |
| `intelligence.opd.manage` | Mengelola master OPD/unit organisasi |
| `intelligence.jf_ppbj.manage` | Mengelola SDM dan riwayat JF PPBJ |
| `intelligence.competency.manage` | Mengelola sertifikat kompetensi PBJ |
| `intelligence.snapshot.generate` | Membuat snapshot gap kompetensi |

## 4.8 Shared Workflow dan File

| Permission | Deskripsi |
|---|---|
| `approval.review` | Meninjau approval dan mengambil keputusan workflow |
| `file.download.protected` | Mengunduh file protected sesuai scope |
| `file.download.private` | Mengunduh file private sesuai ownership/permission |
| `report.export` | Mengunduh laporan PDF/Excel lintas modul |

---

# 5. Matriks Permission Role

Legenda:

```text
✓  = diberikan secara default
◐  = diberikan dengan scope/penugasan tertentu
—  = tidak diberikan
```

## 5.1 Administrasi dan Sistem

| Permission | Super Admin | Admin | Operator | Trainer | Evaluator | Peserta | Public User |
|---|:---:|:---:|:---:|:---:|:---:|:---:|:---:|
| `users.view` | ✓ | ✓ | ◐ | — | — | — | — |
| `users.create` | ✓ | ✓ | — | — | — | — | — |
| `users.update` | ✓ | ✓ | ◐ | — | — | — | — |
| `users.assign_role` | ✓ | ✓ | — | — | — | — | — |
| `roles.manage` | ✓ | — | — | — | — | — | — |
| `permissions.manage` | ✓ | — | — | — | — | — | — |
| `approval.review` | ✓ | ✓ | ◐ | — | — | — | — |
| `audit.view` | ✓ | ✓ | — | — | — | — | — |
| `settings.manage` | ✓ | ◐ | — | — | — | — | — |
| `report.export` | ✓ | ✓ | ◐ | — | — | — | — |

## 5.2 Arena Belajar

| Permission | Super Admin | Admin | Operator | Trainer | Evaluator | Peserta | Public User |
|---|:---:|:---:|:---:|:---:|:---:|:---:|:---:|
| `learning.program.manage` | ✓ | ✓ | — | ◐ | — | — | — |
| `learning.course.manage` | ✓ | ✓ | — | ◐ | — | — | — |
| `learning.course.publish` | ✓ | ✓ | — | — | — | — | — |
| `learning.class.manage` | ✓ | ✓ | ◐ | ◐ | — | — | — |
| `learning.lesson.manage` | ✓ | ✓ | — | ◐ | — | — | — |
| `learning.quiz.manage` | ✓ | ✓ | — | ◐ | — | — | — |
| `learning.assignment.manage` | ✓ | ✓ | — | ◐ | — | — | — |
| `learning.assignment.grade` | ✓ | ✓ | — | — | ◐ | — | — |
| `learning.enrollment.manage` | ✓ | ✓ | ◐ | ◐ | — | — | — |
| `learning.grade.finalize` | ✓ | ✓ | — | — | ◐ | — | — |
| `learning.progress.view` | ✓ | ✓ | ◐ | ◐ | ◐ | ◐ | — |

## 5.3 Bunga Rampai dan Regulasi

| Permission | Super Admin | Admin | Operator | Trainer | Evaluator | Peserta | Public User |
|---|:---:|:---:|:---:|:---:|:---:|:---:|:---:|
| `knowledge.article.manage` | ✓ | ✓ | ◐ | ◐ | — | — | — |
| `knowledge.article.publish` | ✓ | ✓ | — | — | — | — | — |
| `knowledge.faq.manage` | ✓ | ✓ | ◐ | — | — | — | — |
| `knowledge.case_study.manage` | ✓ | ✓ | ◐ | ◐ | — | — | — |
| `knowledge.template.manage` | ✓ | ✓ | ◐ | ◐ | — | — | — |
| `knowledge.category.manage` | ✓ | ✓ | — | — | — | — | — |
| `regulation.manage` | ✓ | ✓ | ◐ | — | — | — | — |
| `regulation.publish` | ✓ | ✓ | — | — | — | — | — |
| `regulation.version.upload` | ✓ | ✓ | ◐ | — | — | — | — |
| `regulation.category.manage` | ✓ | ✓ | — | — | — | — | — |
| `regulation.metadata.manage` | ✓ | ✓ | ◐ | — | — | — | — |
| `regulation.download.log.view` | ✓ | ✓ | ◐ | — | — | — | — |

## 5.4 Komunikasi dan Sertifikat

| Permission | Super Admin | Admin | Operator | Trainer | Evaluator | Peserta | Public User |
|---|:---:|:---:|:---:|:---:|:---:|:---:|:---:|
| `communication.news.manage` | ✓ | ✓ | ◐ | — | — | — | — |
| `communication.news.publish` | ✓ | ✓ | — | — | — | — | — |
| `communication.event.manage` | ✓ | ✓ | ◐ | ◐ | — | — | — |
| `communication.event.publish` | ✓ | ✓ | — | — | — | — | — |
| `communication.event.registration.manage` | ✓ | ✓ | ◐ | ◐ | — | — | — |
| `communication.event.attendance` | ✓ | ✓ | ◐ | ◐ | — | — | — |
| `communication.event.certificate.manage` | ✓ | ✓ | ◐ | — | — | — | — |
| `certificate.view` | ✓ | ✓ | ◐ | ◐ | ◐ | ◐ | — |
| `certificate.issue` | ✓ | ✓ | ◐ | — | — | — | — |
| `certificate.reissue` | ✓ | ✓ | ◐ | — | — | — | — |
| `certificate.revoke` | ✓ | ✓ | — | — | — | — | — |
| `certificate.verify` | ✓ | ✓ | ◐ | ◐ | ◐ | ◐ | ✓ |
| `certificate.template.manage` | ✓ | ✓ | — | — | — | — | — |
| `certificate.sequence.manage` | ✓ | ◐ | — | — | — | — | — |

## 5.5 Government Intelligence dan File

| Permission | Super Admin | Admin | Operator | Trainer | Evaluator | Peserta | Public User |
|---|:---:|:---:|:---:|:---:|:---:|:---:|:---:|
| `intelligence.view` | ✓ | ✓ | ◐ | — | — | — | — |
| `intelligence.export` | ✓ | ✓ | — | — | — | — | — |
| `intelligence.opd.manage` | ✓ | ✓ | — | — | — | — | — |
| `intelligence.jf_ppbj.manage` | ✓ | ✓ | ◐ | — | — | — | — |
| `intelligence.competency.manage` | ✓ | ✓ | ◐ | — | — | — | — |
| `intelligence.snapshot.generate` | ✓ | ✓ | — | — | — | — | — |
| `file.download.protected` | ✓ | ✓ | ◐ | ◐ | ◐ | ◐ | — |
| `file.download.private` | ✓ | ✓ | ◐ | ◐ | ◐ | ◐ | — |

---

# 6. Scope Data per Role

Permission menjawab apakah suatu aksi diperbolehkan. Scope data menjawab data mana yang boleh diakses.

| Role | Scope Default |
|---|---|
| Super Admin | Seluruh data aplikasi sesuai kebutuhan operasional dan audit |
| Admin | Seluruh data domain yang dikelola; konfigurasi inti dibatasi bila diperlukan |
| Operator | Data registrasi, enrollment, validasi, dan tugas yang didelegasikan |
| Trainer | Hanya kelas yang ditugaskan melalui `class_trainers` |
| Evaluator | Hanya kelas/submission yang ditugaskan melalui `class_evaluators` |
| Peserta | Hanya profil, enrollment, progress, submission, bookmark, dan sertifikat sendiri |
| Public User | Hanya data published, statistik agregat, dan hasil verifikasi sertifikat minimum |

## Aturan ownership

### Peserta

Peserta hanya dapat:

```text
- Membaca enrollment sendiri.
- Membuka lesson pada kelas yang enrollment-nya disetujui.
- Mengisi quiz pada kelas sendiri.
- Mengirim/edit submission sesuai status dan deadline.
- Melihat nilai, progress, dan sertifikat sendiri.
- Mengelola bookmark sendiri.
- Mengunduh file yang memang diizinkan untuk kelas/konten sendiri.
```

### Trainer

Trainer hanya dapat:

```text
- Mengelola kelas yang ada pada class_trainers.
- Mengelola module/lesson/quiz/assignment sesuai scope course/class yang ditugaskan.
- Melihat roster dan progres peserta pada kelas yang ditugaskan.
- Tidak dapat menetapkan role user, mengakses audit log global, atau mengubah sertifikat resmi.
```

### Evaluator

Evaluator hanya dapat:

```text
- Membuka antrean submission pada kelas yang ada pada class_evaluators.
- Memberi nilai dan feedback pada assignment submission yang ditugaskan.
- Melihat data minimum peserta yang diperlukan untuk penilaian.
- Tidak dapat mengubah materi, quiz, enrollment, atau konfigurasi kelas.
```

### Operator

Operator hanya dapat:

```text
- Meninjau registrasi akun dan kelengkapan data.
- Menangani enrollment sesuai mandat.
- Menjalankan tugas approval/delegasi yang diberikan.
- Mengelola data pendukung sesuai permission.
- Tidak mengubah role/permission sistem atau pengaturan inti tanpa permission admin.
```

---

# 7. Matriks Menu

| Menu | Super Admin | Admin | Operator | Trainer | Evaluator | Peserta | Public User |
|---|:---:|:---:|:---:|:---:|:---:|:---:|:---:|
| Beranda | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| Arena Belajar Publik | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| Bunga Rampai | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| Regulasi | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| Berita | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| Agenda/Event | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| Statistik PBJ Publik | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| Verifikasi Sertifikat | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| Workspace Operator | — | — | ✓ | — | — | — | — |
| Workspace Trainer | — | — | — | ✓ | — | — | — |
| Workspace Evaluator | — | — | — | — | ✓ | — | — |
| Workspace Peserta | — | — | — | — | — | ✓ | — |
| Dashboard Admin | ✓ | ✓ | — | — | — | — | — |
| User Management | ✓ | ✓ | ◐ | — | — | — | — |
| Role & Permission | ✓ | ◐ | — | — | — | — | — |
| Approval Center | ✓ | ✓ | ◐ | — | — | — | — |
| Audit Log | ✓ | ✓ | — | — | — | — | — |
| Intelligence Internal | ✓ | ✓ | ◐ | — | — | — | — |
| Settings | ✓ | ◐ | — | — | — | — | — |

---

# 8. Route Protection Matrix

| Route Group | Required Authentication | Required Role/Permission | Scope Validation |
|---|---|---|---|
| `/` | Tidak | Tidak | Konten public published |
| `/login`, `/register` | Guest | Tidak | Rate limit |
| `/sertifikat/verifikasi/*` | Tidak | Tidak | Token validation + rate limit |
| `/workspace/peserta/*` | Ya, active | participant atau permission ekuivalen | `user_id`/enrollment ownership |
| `/workspace/trainer/*` | Ya, active | trainer atau permission ekuivalen | `class_trainers` assignment |
| `/workspace/evaluator/*` | Ya, active | evaluator atau permission ekuivalen | `class_evaluators` assignment |
| `/workspace/operator/*` | Ya, active | operator atau permission ekuivalen | Delegated task scope |
| `/admin/*` | Ya, active | admin/super_admin + permission | Admin scope |
| `/learning/*` | Ya, active | Permission Learning | Course/class ownership/assignment |
| `/knowledge/*` | Ya, active | Permission Knowledge | Content ownership/workflow scope |
| `/regulation/*` | Ya, active | Permission Regulation | Workflow/document scope |
| `/communication/*` | Ya, active | Permission Communication | Event/news ownership/assignment |
| `/certificates/*` | Ya, active | Permission Certificate | Recipient/source/admin scope |
| `/intelligence/*` | Ya, active | Permission Intelligence | Internal scope dan klasifikasi data |

---

# 9. Approval Authority

| Subject Type | Submitter | Reviewer/Approver | Publisher | Catatan |
|---|---|---|---|---|
| `learning_course` | Trainer/Admin | Admin/Super Admin | Admin/Super Admin | Course dapat dipublish setelah approved |
| `learning_class` | Trainer/Admin | Admin/Super Admin | Admin/Super Admin | Kelas dapat dibuka setelah approved |
| `article` | Trainer/Operator/Admin | Admin/Super Admin | Admin/Super Admin | Konten Bunga Rampai |
| `case_study` | Trainer/Operator/Admin | Admin/Super Admin | Admin/Super Admin | Konten Bunga Rampai |
| `document_template` | Trainer/Operator/Admin | Admin/Super Admin | Admin/Super Admin | File harus melalui pemeriksaan akses |
| `regulation` | Operator/Admin | Admin/Super Admin | Admin/Super Admin | Metadata regulasi |
| `regulation_version` | Operator/Admin | Admin/Super Admin | Admin/Super Admin | Versi file regulasi |
| `news` | Operator/Admin | Admin/Super Admin | Admin/Super Admin | Berita publik |
| `event` | Operator/Trainer/Admin | Admin/Super Admin | Admin/Super Admin | Event publik |
| `certificate` | System/Operator/Admin | Admin/Super Admin | System/Admin | Penerbitan berdasarkan eligibility |

Approval authority dapat dipersempit melalui permission dan konfigurasi workflow tanpa memperluas hak akses global.

---

# 10. File Access Policy

| Visibility | Contoh | Akses |
|---|---|---|
| `public` | Cover artikel, dokumen regulasi public published | Dapat diakses publik melalui endpoint terkontrol |
| `protected` | Materi kelas, template, bukti kompetensi | Login + permission/enrollment/assignment yang sesuai |
| `private` | Submission tugas, PDF sertifikat individu | Owner atau role berizin |

Aturan tambahan:

- `file_assets.path = NULL` berarti file fisik belum tersedia; download ditolak.
- Nama fisik file tidak berasal dari input pengguna.
- Download harus menggunakan endpoint controller yang melakukan authorization.
- Download file sensitif dapat dicatat pada audit log atau `regulation_download_logs` untuk regulasi.

---

# 11. Statistik dan Privasi

| Data | Dashboard Internal | Statistik Publik |
|---|---:|---:|
| Jumlah JF PPBJ | Ya | Ya, agregat |
| Distribusi jenjang JF PPBJ | Ya | Ya, agregat |
| Nama personel | Ya, sesuai permission | Tidak |
| NIP/identifier SDM | Ya, sesuai permission | Tidak |
| Pemilik sertifikat kompetensi per individu | Ya, sesuai permission | Tidak |
| Jumlah pemilik sertifikat kompetensi | Ya | Ya, agregat |
| Gap kompetensi per OPD | Ya | Ya, agregat sesuai kebijakan |
| Email dan telepon pengguna | Ya, sesuai permission | Tidak |
| Audit log | Ya | Tidak |

---

# 12. Implementasi Teknis

## Filter alias yang direkomendasikan

```php
public array $aliases = [
    'auth' => AuthFilter::class,
    'guest' => GuestFilter::class,
    'accountStatus' => AccountStatusFilter::class,
    'workspace' => WorkspaceFilter::class,
    'admin' => AdminFilter::class,
    'role' => RoleFilter::class,
    'permission' => PermissionFilter::class,
    'rateLimit' => RateLimitFilter::class,
    'auditRequest' => AuditRequestFilter::class,
    'securityHeaders' => SecurityHeadersFilter::class,
];
```

## Contoh route group

```php
$routes->group('admin', [
    'filter' => ['auth', 'accountStatus', 'admin', 'securityHeaders'],
], static function ($routes) {
    $routes->get('/', 'Admin\\DashboardController::index', [
        'as' => 'admin.dashboard',
        'filter' => 'permission:intelligence.view',
    ]);
});

$routes->group('workspace/trainer', [
    'filter' => ['auth', 'accountStatus', 'workspace', 'role:trainer'],
], static function ($routes) {
    $routes->get('/', 'Workspace\\TrainerController::index', [
        'as' => 'workspace.trainer.dashboard',
    ]);
});
```

Permission filter hanya memeriksa izin tindakan. Controller dan service tetap wajib memeriksa scope data, misalnya trainer benar-benar ditugaskan pada class yang ingin diubah.

---

# 13. Pengujian RBAC

Test minimum:

- Public User menerima 403/redirect saat mengakses `/workspace/*` dan `/admin/*`.
- Peserta tidak dapat mengakses enrollment, submission, sertifikat, atau progress milik peserta lain.
- Trainer tidak dapat mengubah kelas yang tidak ada dalam `class_trainers`.
- Evaluator tidak dapat memberi nilai pada submission kelas yang tidak ditugaskan.
- Operator tidak dapat mengubah role atau permission sistem.
- Admin tanpa permission tertentu menerima 403 pada route terkait.
- Super Admin dapat mengakses seluruh permission sistem.
- Akun pending, inactive, rejected, dan suspended tidak dapat memakai Workspace/Dashboard.
- Endpoint download file private menolak user tanpa ownership atau permission.
- Statistik publik tidak mengandung data pribadi ASN.

---

# 14. Ringkasan

```text
Public User
→ Landingpage publik + verifikasi sertifikat

Peserta
→ Workspace pembelajaran milik sendiri

Trainer
→ Workspace kelas yang ditugaskan

Evaluator
→ Workspace penilaian yang ditugaskan

Operator
→ Workspace administrasi dan validasi

Admin
→ Dashboard administrasi dan pengelolaan ekosistem

Super Admin
→ Dashboard penuh, konfigurasi, akses, audit, dan governance
```

RBAC BegawiPBJ memastikan setiap pengguna hanya memperoleh akses minimum yang dibutuhkan untuk menjalankan tugasnya, sambil menjaga keamanan data, akuntabilitas proses, dan privasi informasi PBJ Pemerintah Provinsi Lampung.
