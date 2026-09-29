# BegawiPBJ Environment Configuration

Dokumen ini menetapkan konfigurasi environment untuk BegawiPBJ berbasis CodeIgniter 4 dan MySQL 8.4+. Konfigurasi dipisahkan menurut local, testing, staging, dan production; source aplikasi sama, nilai environment berbeda.

## Prinsip

- `.env` bersifat rahasia, tidak di-commit. `.env.example` berisi nama variabel dan contoh non-secret saja.
- Document root mengarah ke `public/`; `app/`, `writable/`, `vendor/`, `database/`, dan `docs/` berada di luar akses langsung web.
- Database, SMTP, file storage, encryption key, dan credential awal berbeda per environment.
- Staging menggunakan data sintetik atau tersanitasi. Seeder development tidak dijalankan pada production.
- Timestamp database UTC; tampilan mengikuti WIB/`Asia/Jakarta` kecuali kebutuhan pengguna menentukan lain.
- Pastikan nilai environment yang benar-benar dibaca aplikasi cocok dengan class `app/Config/*`; variabel tambahan di bawah memerlukan implementasi pembaca config masing-masing.

## Matriks environment

| Parameter | Local | Testing | Staging | Production |
|---|---|---|---|---|
| `CI_ENVIRONMENT` | development | testing | production-like | production |
| URL | localhost HTTPS bila ada | test runner | HTTPS staging | HTTPS resmi |
| DB | lokal | disposable/isolated | terpisah dan sanitasi | terpisah resmi |
| `DBDebug` | true | false atau test | false | false |
| Email | mail catcher | fake/disabled | sandbox | SMTP resmi |
| QR verification | URL lokal | fixture | staging | domain resmi |
| Data dummy | Diizinkan | Fixture | Sebaiknya tidak | Dilarang |
| Debug toolbar | Lokal saja | Tidak | Tidak | Tidak |

`CI_ENVIRONMENT` staging dapat diset `production` untuk menguji perilaku produksi; label `production-like` di tabel adalah deskripsi, bukan nilai yang otomatis dikenal CodeIgniter.

## Template `.env.example`

Contoh berikut adalah panduan; sinkronkan nama kunci custom dengan config aplikasi.

```ini
CI_ENVIRONMENT = development

app.baseURL = 'http://localhost:8080/'
app.forceGlobalSecureRequests = false

# Database CI4
database.default.hostname = localhost
database.default.database = db_begawipbj
database.default.username = begawipbj_local
database.default.password =
database.default.DBDriver = MySQLi
database.default.DBDebug = true

# Generate key secara lokal: php spark key:generate
encryption.key =

# Custom app settings: hanya efektif bila dibaca App\\Config kelas terkait
BEGAWI_FILE_STORAGE_DISK = local_private
BEGAWI_FILE_MAX_MB = 10
BEGAWI_CERTIFICATE_VERIFY_BASE_URL = 'http://localhost:8080/sertifikat/verifikasi/'
BEGAWI_CERTIFICATE_NUMBER_PREFIX = 'BGPBJ'
BEGAWI_PUBLIC_STATISTICS_CACHE_SECONDS = 900
BEGAWI_LOGIN_RATE_LIMIT = 5
BEGAWI_VERIFICATION_RATE_LIMIT = 30

# Hanya untuk bootstrap development terkontrol; jangan taruh credential nyata di template
INITIAL_SUPERADMIN_NAME =
INITIAL_SUPERADMIN_EMAIL =
INITIAL_SUPERADMIN_USERNAME =
INITIAL_SUPERADMIN_PASSWORD =
```

Jangan menganggap environment variable custom langsung diterapkan CI4. Misalnya `BEGAWI_FILE_MAX_MB` baru efektif bila `Config/FileStorage.php` membaca dan memvalidasinya.

## Local setup

```bash
composer install
cp .env.example .env
php spark key:generate
php spark migrate
```

Import master wilayah resmi sebelum membuat profil yang mereferensikan kode `wilayah.kode`:

```bash
mysql -u <user> -p db_begawipbj < database/reference/wilayah.sql
php spark db:seed DatabaseSeeder
```

Catatan: jika `DatabaseSeeder` juga mengimpor wilayah, jangan menjalankan import dua kali; pilih satu mekanisme dan dokumentasikan. Jangan menjalankan SQL schema fresh dan CI4 migration pembuat tabel yang sama dalam satu database.

## Database dan privilege

- Runtime account hanya mendapat privilege yang diperlukan operasi aplikasi; akun deployment/migration dapat dipisah untuk `CREATE`, `ALTER`, dan `INDEX`.
- Jangan gunakan `root` dalam konfigurasi aplikasi production.
- Batasi koneksi database ke host aplikasi dan administrator berwenang.
- Enkripsi transport database bila infrastruktur terpisah mendukung.
- `database.default.DBDebug = false` pada staging dan production.
- Pencocokan encoding: `utf8mb4`, collation target `utf8mb4_0900_ai_ci`, engine InnoDB.

## Secret dan credential

| Secret | Lokasi | Rotasi/perlakuan |
|---|---|---|
| DB password | Secret manager atau `.env` server terbatas | Rotasi tanpa commit |
| `encryption.key` | Environment server | Cadangkan aman; perubahan key dapat membuat data terenkripsi tidak terbaca |
| SMTP password | Environment server | Rotasi ketika bocor/perubahan operator |
| Initial Super Admin password | Input bootstrap sekali | Hapus/rotasi setelah akun aktif |
| Third-party API key | Environment server bila ada | Hanya diberikan ke komponen perlu |

Nilai rahasia tidak ditampilkan dalam halaman pengaturan, log, dump debugging, dokumentasi, atau screenshot UAT. Persyaratan MFA untuk akun admin perlu ditetapkan dalam kebijakan keamanan instansi jika diterapkan.

## Cookies, session, dan proxy

- Pada production HTTPS: cookie `Secure`, `HttpOnly`, dan `SameSite` sesuai alur auth.
- Regenerasi session ID pada login/perubahan privilege; logout menghapus session.
- Jika aplikasi berada di belakang reverse proxy, hanya percayai IP proxy yang dikonfigurasi; validasi `X-Forwarded-*` agar skema/host tidak dipalsukan.
- `app.baseURL` dan URL verifikasi QR harus memakai domain resmi yang sama atau domain yang disetujui.

## Storage dan file

- `public/assets/main/` untuk Landingpage + Workspace; `public/assets/dashboard/` untuk Admin/Super Admin.
- Berkas pengguna: `writable/uploads/{learning,knowledge,regulations,communication,certificates}/`.
- `file_assets.path = NULL` berarti belum ada berkas fisik; tidak buat link download.
- Pastikan user web server dapat menulis hanya pada `writable/` yang diperlukan.
- Ukuran upload aplikasi harus konsisten dengan batas PHP/web server (`upload_max_filesize`, `post_max_size`, batas proxy).
- Pemindahan storage wajib memigrasikan metadata path, file fisik, permission, dan backup bersamaan.

## Email dan notifikasi

- Gunakan SMTP sandbox/mail catcher pada local/staging.
- Template pesan tidak menyertakan NIP/NIK, credential, token mentah, atau dokumen privat.
- Kegagalan email tidak boleh menyebabkan state utama diam-diam dibatalkan setelah commit; gunakan queue/outbox bila dibutuhkan dan retry terukur.
- Sender/from address harus menggunakan identitas instansi yang disetujui.

## Logging dan monitoring

- Production: debug toolbar mati; error tampil sebagai pesan aman + `request_id`.
- Log disimpan di `writable/logs/` dengan akses terbatas, rotasi, dan kapasitas termonitor.
- Jangan log password, cookie, token QR, NIP/NIK, isi assignment, atau file privat.
- Catat kegagalan migration, storage, mail, certificate generation, dan event registration untuk alert operasional.

## Pemeriksaan sebelum go-live

- [ ] `.env` production tersedia di server dan tidak di Git.
- [ ] URL HTTPS, document root, trusted proxy, cookie, dan security headers benar.
- [ ] Database account, privilege, charset, collation, dan backup diverifikasi.
- [ ] Master wilayah dan daftar organisasi yang disetujui tersedia.
- [ ] `encryption.key` tersimpan aman dan mekanisme pemulihannya jelas.
- [ ] Seeder dummy dan route reference template tidak tersedia untuk publik.
- [ ] File private tidak dapat diakses langsung dari web.
- [ ] Akun awal admin sudah diganti credential dan diverifikasi role-nya.
- [ ] Smoke test masing-masing role, QR verification, dan statistik publik selesai.
