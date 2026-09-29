# BegawiPBJ Security Checklist

## Checklist Keamanan dan Production Deployment Platform Ekosistem Digital PBJ Pemerintah Provinsi Lampung

Dokumen ini menetapkan checklist keamanan sebelum, selama, dan setelah deployment BegawiPBJ ke environment production.

BegawiPBJ mengelola pembelajaran, regulasi, dokumen, sertifikat digital, data pengguna, data kompetensi SDM PBJ, serta intelligence dashboard. Karena itu, deployment production wajib menerapkan prinsip keamanan, privasi, ketersediaan, auditability, dan pemulihan layanan.

---

# 1. Prinsip Keamanan

BegawiPBJ menerapkan prinsip berikut:

```text
Security by Design
Least Privilege
Defense in Depth
Secure Defaults
Privacy by Default
Auditability
Availability and Recoverability
```

## 1.1 Tujuan keamanan

- Melindungi akun, session, dan akses pengguna.
- Melindungi NIP, NIK, email, telepon, identifier SDM, dan dokumen bukti.
- Melindungi materi pembelajaran, submission tugas, regulasi, template, dan sertifikat.
- Menjamin integritas nomor sertifikat, token verifikasi, nilai, approval, dan audit log.
- Mencegah akses lintas role atau lintas ownership data.
- Menjaga ketersediaan aplikasi dan kemampuan pemulihan saat terjadi gangguan.

---

# 2. Klasifikasi Deployment

| Environment | Tujuan | Data | Akses |
|---|---|---|---|
| Local Development | Pengembangan lokal | Dummy/sanitized | Developer |
| Testing | Automated test dan QA | Fixture/dummy | Tim internal |
| Staging | Validasi sebelum production | Data tersanitasi atau terbatas | Tim release/UAT |
| Production | Layanan resmi | Data operasional resmi | Pengguna berwenang |

Aturan:

- Data production tidak digunakan pada local development tanpa persetujuan dan sanitasi.
- Development dummy seeder tidak boleh dijalankan pada production.
- Credential production tidak boleh digunakan pada development atau staging.
- Setiap environment memakai `.env` dan credential database yang berbeda.

---

# 3. Checklist Pra-Deployment

## 3.1 Source code dan dependency

- [ ] Source code berasal dari branch/release yang disetujui.
- [ ] Commit atau tag release dicatat.
- [ ] Tidak ada file debug, test credential, secret, atau dump production dalam release.
- [ ] `composer.lock` tersedia dan sesuai dengan `composer.json`.
- [ ] Dependency diinstal melalui:

```bash
composer install --no-dev --optimize-autoloader
```

- [ ] Dependency diperiksa terhadap vulnerability yang diketahui.
- [ ] Debug code seperti `var_dump`, `dd`, endpoint test, dan route dummy telah dihapus/dinonaktifkan.
- [ ] Folder `app/Views/reference/fistudy/` dan `app/Views/reference/wowdash/` tidak memiliki route produksi.
- [ ] Semua test yang relevan lulus.

## 3.2 Konfigurasi aplikasi

- [ ] `CI_ENVIRONMENT = production`.
- [ ] `app.baseURL` memakai domain resmi dan HTTPS.
- [ ] `app.forceGlobalSecureRequests = true` atau konfigurasi HTTPS setara diterapkan.
- [ ] Debug toolbar tidak aktif pada production.
- [ ] Error detail tidak ditampilkan kepada pengguna.
- [ ] `database.default.DBDebug = false`.
- [ ] Timezone aplikasi ditetapkan dengan jelas; timestamp database disimpan UTC.
- [ ] `encryption.key` telah dibuat dengan key yang kuat.
- [ ] `encryption.key` tidak ada di source code atau repository.
- [ ] Konfigurasi email, notifikasi, storage, dan QR certificate sudah divalidasi.

Contoh nilai production `.env`:

```ini
CI_ENVIRONMENT = production

app.baseURL = 'https://begawipbj.lampungprov.go.id/'
app.forceGlobalSecureRequests = true

database.default.DBDebug = false
```

## 3.3 Secret dan environment variable

- [ ] File `.env` tidak di-commit.
- [ ] `.env.example` tidak memuat secret nyata.
- [ ] Password database production tidak menggunakan user `root`.
- [ ] Encryption key tidak kosong dan tidak menggunakan nilai default.
- [ ] Password SMTP, API key, token service, dan secret eksternal disimpan di environment/secret manager.
- [ ] Credential Super Admin awal tidak hardcode pada seeder.
- [ ] Initial Super Admin credential diganti setelah instalasi pertama.
- [ ] Rotasi credential telah memiliki prosedur terdokumentasi.

---

# 4. Server dan Web Server

## 4.1 Web root dan path

- [ ] Web server document root mengarah hanya ke folder `public/`.
- [ ] Folder `app/`, `writable/`, `vendor/`, `database/`, `docs/`, dan `.env` tidak dapat diakses langsung dari web.
- [ ] Directory listing dinonaktifkan.
- [ ] File `.env`, `.git`, `composer.json`, log, SQL dump, dan backup tidak dapat diunduh melalui HTTP.
- [ ] Tidak ada alias web server yang mengarah ke `writable/uploads/` tanpa kontrol akses aplikasi.

Contoh struktur aman:

```text
/webroot/begawipbj/public      → document root
/webroot/begawipbj/app         → non-public
/webroot/begawipbj/writable    → non-public
/webroot/begawipbj/vendor      → non-public
```

## 4.2 HTTPS dan TLS

- [ ] HTTPS aktif untuk seluruh halaman.
- [ ] HTTP otomatis redirect ke HTTPS.
- [ ] Sertifikat TLS valid, tidak kedaluwarsa, dan menggunakan hostname resmi.
- [ ] TLS 1.2 atau lebih baru digunakan.
- [ ] Cipher suite lemah dinonaktifkan oleh konfigurasi server.
- [ ] HSTS dipertimbangkan setelah HTTPS stabil.
- [ ] Mixed content tidak ada pada CSS, JS, image, font, iframe, atau asset eksternal.

## 4.3 Header keamanan

Setiap respons web perlu menerapkan header keamanan sesuai kebutuhan aplikasi:

```text
Content-Security-Policy
X-Content-Type-Options: nosniff
X-Frame-Options: DENY atau SAMEORIGIN
Referrer-Policy
Permissions-Policy
Strict-Transport-Security
```

Checklist:

- [ ] Content Security Policy diuji agar tidak memblokir asset legitimate.
- [ ] Aplikasi tidak dapat di-embed secara tidak sah pada iframe pihak ketiga.
- [ ] MIME sniffing dinonaktifkan.
- [ ] Referrer tidak membocorkan token atau path sensitif.
- [ ] Header keamanan diterapkan melalui `SecurityHeadersFilter` atau web server.

---

# 5. File Permission dan Storage

## 5.1 Permission filesystem

- [ ] Source code dimiliki user deployment, bukan user web server bila memungkinkan.
- [ ] Web server hanya memiliki hak tulis pada folder yang diperlukan.
- [ ] `writable/` dapat ditulis oleh user web server.
- [ ] `writable/uploads/` tidak executable.
- [ ] Folder upload tidak dapat menjalankan file PHP/script.
- [ ] Permission file dan folder mengikuti prinsip least privilege.
- [ ] Folder cache, logs, session, uploads, dan exports diperiksa setelah deployment.

Panduan umum:

```text
Folder aplikasi non-writable   : read-only untuk web server bila memungkinkan
writable/                      : writable untuk web server
writable/uploads/              : writable, non-executable, non-public
public/assets/                 : read-only setelah deploy
```

## 5.2 File upload

- [ ] Validasi ekstensi diterapkan.
- [ ] Validasi MIME type diterapkan di server.
- [ ] Batas ukuran file ditetapkan per domain.
- [ ] Nama file fisik memakai UUID/hash, bukan nama file user.
- [ ] Path file tidak dibentuk dari input user.
- [ ] File upload disimpan di `writable/uploads/`.
- [ ] File private/protected tidak dilayani langsung dari folder storage.
- [ ] Download menggunakan controller yang memeriksa authorization.
- [ ] File dengan `file_assets.path = NULL` tidak dapat diunduh.
- [ ] File dengan metadata tetapi fisik tidak tersedia memberikan respons aman tanpa path disclosure.
- [ ] Scan malware/antivirus dipertimbangkan bila infrastruktur tersedia.
- [ ] Upload executable, script, archive berbahaya, dan MIME mismatch ditolak.

## 5.3 File visibility

| Visibility | Contoh | Kebijakan |
|---|---|---|
| `public` | Cover artikel, regulasi public published | Tetap melalui URL/endpoint terkendali sesuai kebijakan |
| `protected` | Materi, template, dokumen regulasi terbatas | Login + permission/scope diperlukan |
| `private` | Submission tugas, sertifikat individu | Owner atau role berizin saja |

---

# 6. Database Security

## 6.1 Konfigurasi database

- [ ] Database menggunakan MySQL 8.4+ atau versi kompatibel yang disetujui.
- [ ] Database memakai InnoDB.
- [ ] Charset dan collation menggunakan `utf8mb4` dan standar yang ditetapkan.
- [ ] Aplikasi menggunakan user MySQL khusus, bukan `root`.
- [ ] User database memiliki privilege minimum.
- [ ] User aplikasi tidak memiliki privilege global seperti `GRANT OPTION` atau `SUPER`.
- [ ] Akses database dibatasi ke jaringan/server aplikasi.
- [ ] Port database tidak diekspos ke internet publik.
- [ ] Koneksi database menggunakan TLS bila infrastruktur mendukung.
- [ ] `DBDebug` nonaktif pada production.

Contoh hak aplikasi minimum:

```text
SELECT
INSERT
UPDATE
DELETE
CREATE
ALTER
INDEX
REFERENCES
```

Privilege dapat disesuaikan setelah schema stabil. Akun deployment/migration dapat dipisahkan dari akun runtime aplikasi untuk menerapkan least privilege lebih kuat.

## 6.2 Schema dan migration

- [ ] Migration telah direview dan diuji.
- [ ] Migration tidak menghapus data tanpa backup serta prosedur pemulihan.
- [ ] `php spark migrate:fresh` tidak dijalankan pada production.
- [ ] Development seeder tidak dijalankan pada production.
- [ ] Master wilayah resmi tersedia sebelum data profil dengan FK wilayah dibuat.
- [ ] Constraint sertifikat `UNIQUE(source_type, source_id)` tersedia.
- [ ] `file_assets.path` dapat bernilai `NULL` untuk placeholder file.
- [ ] Foreign key dan index telah diverifikasi.
- [ ] Tidak ada schema drift antara migration dan database production.

## 6.3 Data privacy

- [ ] NIP, NIK, email, telepon, identifier SDM, dan dokumen bukti diklasifikasikan sebagai data terbatas.
- [ ] Data terbatas tidak tampil di statistik publik.
- [ ] Export data dibatasi dengan permission dan audit log.
- [ ] Query dashboard publik hanya memakai agregat.
- [ ] Data dummy tidak masuk database production.
- [ ] SQL dump yang mengandung data pribadi disimpan terenkripsi dan dibatasi aksesnya.

---

# 7. Authentication dan Session Security

## 7.1 Password

- [ ] Password disimpan hanya menggunakan `password_hash()`.
- [ ] Tidak ada password plaintext dalam database, log, seeder production, atau source code.
- [ ] Kebijakan password minimum ditetapkan.
- [ ] Endpoint login memiliki rate limit.
- [ ] Endpoint lupa/reset password memiliki rate limit dan token kadaluwarsa.
- [ ] Reset password tidak mengungkap apakah email terdaftar.
- [ ] Password lama tidak dikirim kembali melalui email/log/API.

## 7.2 Session

- [ ] Session cookie menggunakan `Secure` pada HTTPS.
- [ ] Session cookie menggunakan `HttpOnly`.
- [ ] `SameSite` disesuaikan setidaknya `Lax`; gunakan `Strict` bila kompatibel.
- [ ] Session ID diregenerasi saat login dan perubahan privilege penting.
- [ ] Session dihapus saat logout.
- [ ] Akun suspended/inactive diperiksa pada setiap request sensitif.
- [ ] Session storage dilindungi dan tidak dapat diakses publik.
- [ ] Timeout session sesuai kebijakan keamanan instansi.

## 7.3 Account status dan role

- [ ] `AccountStatusFilter` aktif pada Workspace dan Dashboard.
- [ ] `AdminFilter` aktif pada seluruh route `/admin/*`.
- [ ] `WorkspaceFilter` aktif pada seluruh route `/workspace/*`.
- [ ] Permission diperiksa pada route/action sensitif.
- [ ] Scope data diperiksa di service/controller, bukan hanya role.
- [ ] Perubahan role dan permission masuk audit log.

---

# 8. Input, Output, dan API Security

## 8.1 Input validation

- [ ] Semua request POST/PUT/PATCH/DELETE divalidasi di server.
- [ ] Validation rule sesuai tipe, panjang, status, date range, dan foreign key bisnis.
- [ ] Input ID diverifikasi ownership/scope-nya.
- [ ] Mass assignment dicegah melalui `allowedFields` Model.
- [ ] Parameter filter/sort dibatasi whitelist.
- [ ] Pagination memiliki maksimum yang aman.
- [ ] File upload mematuhi kebijakan storage.

## 8.2 CSRF

- [ ] CSRF protection aktif untuk form state-changing.
- [ ] Token CSRF disertakan pada form dan AJAX request.
- [ ] Endpoint yang dikecualikan dari CSRF didokumentasikan dan dilindungi mekanisme lain.
- [ ] API token-based, bila ada, menggunakan strategi autentikasi yang sesuai dan tidak mencampur cookie tanpa perlindungan.

## 8.3 Output escaping

- [ ] Data user/konten ditampilkan dengan escaping default view engine.
- [ ] HTML rich text disanitasi dengan whitelist sebelum ditampilkan.
- [ ] URL eksternal divalidasi sebelum dirender sebagai link.
- [ ] Pesan error tidak membocorkan SQL, path server, token, atau stack trace.

## 8.4 SQL injection

- [ ] Gunakan Query Builder atau prepared statement.
- [ ] Jangan menyusun query SQL dari input user tanpa parameter binding.
- [ ] Dynamic order-by dan field selection menggunakan whitelist.
- [ ] Query laporan/export diperiksa terhadap injection dan scope data.

---

# 9. RBAC, Ownership, dan Authorization

## 9.1 Permission enforcement

- [ ] `PermissionFilter` digunakan untuk route manajemen.
- [ ] Service memvalidasi permission untuk aksi bisnis sensitif.
- [ ] Menu dinamis dibangun dari permission, tetapi keamanan tidak hanya di UI.
- [ ] Admin tanpa permission menerima 403 untuk tindakan terlarang.
- [ ] Public User tidak dapat mengakses Workspace atau Dashboard.

## 9.2 Ownership

- [ ] Peserta hanya membaca enrollment, progress, submission, nilai, dan sertifikat sendiri.
- [ ] Trainer hanya mengelola kelas dalam `class_trainers`.
- [ ] Evaluator hanya menilai kelas dalam `class_evaluators`.
- [ ] Operator hanya memproses tugas yang menjadi scope-nya.
- [ ] Sertifikat private hanya dapat diunduh penerima atau admin berizin.
- [ ] File submission hanya dapat diakses peserta pemilik, evaluator yang ditugaskan, trainer sesuai kebijakan, atau admin berizin.

## 9.3 Approval dan workflow

- [ ] State transition divalidasi oleh service.
- [ ] Approval tidak dapat melompati state yang tidak valid.
- [ ] Reject/revision/cancel memiliki alasan wajib.
- [ ] Approval history tidak diubah atau dihapus tanpa kebijakan retention.
- [ ] Publish hanya dilakukan oleh actor berpermission.
- [ ] Transition sertifikat, nilai akhir, dan regulasi memiliki audit log.

---

# 10. Sertifikat dan QR Verification

## 10.1 Penerbitan

- [ ] Eligibility sertifikat diverifikasi sebelum issue.
- [ ] Penerbitan menggunakan database transaction.
- [ ] Nomor sertifikat dibuat dari sequence yang terkunci/atomik.
- [ ] `certificate_number` unik.
- [ ] `verification_token` random, panjang memadai, dan unik.
- [ ] Satu source hanya memiliki satu sertifikat resmi: `UNIQUE(source_type, source_id)`.
- [ ] PDF sertifikat disimpan sebagai file private/protected sesuai kebijakan.
- [ ] Penerbitan dicatat pada audit log.

## 10.2 Verifikasi publik

- [ ] Endpoint verifikasi memiliki rate limit.
- [ ] Token tidak memuat NIP, NIK, email, atau data pribadi lain.
- [ ] Hasil verifikasi menampilkan data minimum.
- [ ] Status `issued`, `revoked`, `expired`, dan `not_found` ditampilkan akurat.
- [ ] Upaya verifikasi dicatat pada `certificate_verification_logs` sesuai kebijakan privasi.
- [ ] File PDF tidak dapat diunduh publik hanya berdasarkan token verifikasi kecuali kebijakan secara eksplisit memperbolehkan.

## 10.3 Revocation

- [ ] Revocation membutuhkan `certificate.revoke`.
- [ ] Alasan revocation wajib.
- [ ] Sertifikat tidak dihapus fisik sebagai pengganti revocation.
- [ ] Public verification langsung menunjukkan status revoked.
- [ ] Penerima dan admin terkait menerima notifikasi.

---

# 11. Audit, Logging, dan Monitoring

## 11.1 Audit log

- [ ] Audit log aktif untuk perubahan role, permission, approval, nilai akhir, sertifikat, regulasi, event, intelligence, dan export data.
- [ ] Audit mencatat actor, action, subject, timestamp, IP, serta context seperlunya.
- [ ] Data sensitif dalam audit JSON diminimalkan atau dimasking.
- [ ] Audit log tidak dapat diakses Public User.
- [ ] Audit log memiliki kebijakan retention dan backup.

## 11.2 Application log

- [ ] Log production tidak menulis password, token, encryption key, NIK, NIP, atau dokumen sensitif.
- [ ] Log error berada di `writable/logs/` dan tidak dapat diakses melalui web.
- [ ] Level log production sesuai kebutuhan operasional.
- [ ] Rotasi log dan kapasitas disk dimonitor.
- [ ] Exception penting memiliki correlation/request ID bila memungkinkan.

## 11.3 Monitoring

- [ ] Uptime monitor tersedia untuk endpoint publik dan login.
- [ ] Disk usage untuk database, logs, uploads, dan backup dimonitor.
- [ ] Error rate, latency, dan kegagalan job/notifikasi dimonitor.
- [ ] Sertifikat TLS dipantau masa berlakunya.
- [ ] Backup job dipantau dan kegagalannya menghasilkan alert.
- [ ] Aktivitas login gagal berulang dapat dipantau untuk indikasi brute force.

---

# 12. Backup, Restore, dan Disaster Recovery

## 12.1 Backup minimum

Backup wajib mencakup:

```text
1. Database MySQL.
2. writable/uploads/.
3. Environment configuration/secret melalui mekanisme aman.
4. Dokumentasi versi aplikasi dan migration status.
```

Contoh backup database:

```bash
mysqldump -u <username> -p <database_name> > backup.sql
```

## 12.2 Checklist backup

- [ ] Backup database dijadwalkan.
- [ ] Backup upload dijadwalkan terpisah.
- [ ] Backup disimpan di lokasi berbeda dari server utama bila memungkinkan.
- [ ] Backup dienkripsi jika memuat data terbatas.
- [ ] Akses backup dibatasi.
- [ ] Retention policy ditetapkan.
- [ ] Backup selesai diverifikasi.
- [ ] Restore diuji secara berkala.

## 12.3 Recovery test

- [ ] Restore database ke environment uji berhasil.
- [ ] Upload file sesuai dengan record database setelah restore.
- [ ] Aplikasi dapat bootstrap setelah restore.
- [ ] Sertifikat, regulasi, materi, dan template dapat ditemukan sesuai metadata.
- [ ] Migration status diverifikasi.
- [ ] Smoke test dilakukan setelah recovery.

---

# 13. Deployment Procedure

## 13.1 Sebelum eksekusi

- [ ] Maintenance window ditentukan bila diperlukan.
- [ ] Backup berhasil dibuat.
- [ ] Stakeholder operasional diberi informasi bila ada downtime.
- [ ] Release note dan migration list tersedia.
- [ ] Rollback/recovery plan tersedia.

## 13.2 Saat deployment

```text
1. Aktifkan maintenance mode bila diperlukan.
2. Backup database dan writable/uploads.
3. Deploy source code release.
4. Install dependency production.
5. Pastikan permission writable benar.
6. Jalankan migration terverifikasi.
7. Jalankan reference seeder bila diperlukan.
8. Jalankan backfill/job terpisah bila diperlukan.
9. Clear cache yang relevan.
10. Jalankan smoke test.
11. Monitor log dan error.
12. Nonaktifkan maintenance mode.
```

## 13.3 Setelah deployment

- [ ] Landingpage dapat dibuka melalui HTTPS.
- [ ] Login dan logout berhasil.
- [ ] Dashboard hanya dapat diakses Admin/Super Admin.
- [ ] Workspace hanya dapat diakses role operasional aktif.
- [ ] Permission test untuk route penting berhasil.
- [ ] Upload/download file terproteksi berfungsi.
- [ ] Placeholder file tidak dapat diunduh.
- [ ] Regulasi public published dapat dicari.
- [ ] Event registration dan attendance berfungsi.
- [ ] Certificate verification berfungsi.
- [ ] Statistik publik tidak mengekspos data pribadi.
- [ ] Log tidak menunjukkan error kritis.

---

# 14. Incident Response

## 14.1 Kategori insiden

| Kategori | Contoh |
|---|---|
| Availability | Aplikasi tidak dapat diakses, database down, storage penuh |
| Security | Akses tidak sah, brute force, role escalation, file exposure |
| Privacy | Kebocoran NIP/NIK/email/dokumen pribadi |
| Integrity | Nilai berubah tidak sah, sertifikat ganda, approval histori rusak |
| Performance | Lambat ekstrem, timeouts, query berat |

## 14.2 Prosedur minimum

```text
1. Deteksi dan catat waktu insiden.
2. Batasi dampak: disable endpoint/akun/akses bila diperlukan.
3. Lindungi bukti: log, audit, snapshot database, konfigurasi relevan.
4. Analisis akar masalah.
5. Terapkan perbaikan atau rollback/recovery.
6. Verifikasi integritas dan keamanan layanan.
7. Informasikan pihak berwenang sesuai klasifikasi insiden.
8. Dokumentasikan tindakan pencegahan agar tidak berulang.
```

## 14.3 Tindakan segera contoh

| Insiden | Tindakan awal |
|---|---|
| Credential diduga bocor | Rotasi credential, invalidate session bila perlu, audit akses |
| Akun admin disalahgunakan | Suspend akun, review audit log, periksa role/permission |
| File private dapat diakses publik | Nonaktifkan endpoint/route, blok akses storage, audit download |
| Sertifikat tidak valid/ganda | Tahan penerbitan, audit sequence/source, revoke jika perlu |
| Database corruption | Stop write bila perlu, restore ke environment terpisah, verifikasi backup |

---

# 15. Security Testing

Sebelum release production, lakukan pengujian berikut:

## Authentication dan session

- [ ] Percobaan login berulang dibatasi.
- [ ] Session diregenerasi saat login.
- [ ] Logout menghapus session.
- [ ] Akun suspended langsung ditolak.
- [ ] Reset password tidak membocorkan akun terdaftar.

## Authorization

- [ ] Public User tidak dapat mengakses Workspace/Dashboard.
- [ ] Peserta tidak dapat mengakses data peserta lain.
- [ ] Trainer/Evaluator tidak dapat mengakses kelas yang tidak ditugaskan.
- [ ] Operator tidak dapat mengelola role/permission tanpa izin.
- [ ] Admin tanpa permission menerima 403.
- [ ] Endpoint download memeriksa ownership/permission.

## Input dan output

- [ ] Form menolak input tidak valid.
- [ ] Uji XSS pada title, content, feedback, dan metadata.
- [ ] Uji SQL injection pada search, sort, filter, dan ID parameter.
- [ ] Uji upload file dengan ekstensi/MIME palsu.
- [ ] Uji path traversal pada endpoint file.

## Workflow dan data integrity

- [ ] State transition ilegal ditolak.
- [ ] Approval history tidak dapat dimanipulasi.
- [ ] Satu peserta tidak dapat enrollment dua kali pada kelas sama.
- [ ] Satu source tidak dapat menerbitkan sertifikat dua kali.
- [ ] Certificate token tidak dapat diprediksi.
- [ ] Event quota aman pada request paralel.
- [ ] Final score dan kelulusan tidak dapat diubah tanpa permission/audit.

---

# 16. Final Production Sign-Off

Deployment dapat disetujui ketika seluruh bagian berikut terpenuhi:

## Application

- [ ] Release code disetujui.
- [ ] Test otomatis lulus.
- [ ] Configuration production benar.
- [ ] Debug dan development tooling tidak aktif.

## Security

- [ ] HTTPS aktif.
- [ ] Secrets aman.
- [ ] Authentication, RBAC, ownership, dan workflow diuji.
- [ ] File storage aman.
- [ ] Header keamanan diterapkan.

## Database

- [ ] Migration diuji.
- [ ] Backup tersedia.
- [ ] Master wilayah dan OPD tersedia.
- [ ] Foreign key, index, dan constraint tervalidasi.

## Operations

- [ ] Monitoring tersedia.
- [ ] Backup dan restore diuji.
- [ ] Incident contact tersedia.
- [ ] Smoke test production selesai.

---

# 17. Ringkasan

```text
Production Ready
= HTTPS
+ Secure Environment Variables
+ Least-Privilege Database User
+ Protected Files
+ Authentication and RBAC
+ Valid Workflow Controls
+ Certificate Integrity
+ Audit Logging
+ Database and Upload Backups
+ Monitoring
+ Tested Recovery
```

Checklist ini memastikan BegawiPBJ dapat dioperasikan secara aman, terpercaya, dan berkelanjutan sebagai platform ekosistem PBJ Pemerintah Provinsi Lampung.
