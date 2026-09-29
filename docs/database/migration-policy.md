# BegawiPBJ Migration Policy

## Kebijakan Migration Database Platform Ekosistem Digital PBJ Pemerintah Provinsi Lampung

Dokumen ini menetapkan standar pembuatan, penamaan, pengujian, pelaksanaan, rollback, dan pengelolaan migration database BegawiPBJ.

Migration adalah sumber perubahan schema yang terkontrol. Setiap perubahan tabel, kolom, indeks, foreign key, constraint, atau data referensi yang terkait schema harus direncanakan, direview, diuji, dan didokumentasikan.

---

# 1. Tujuan

Kebijakan migration bertujuan untuk:

- Menjaga schema database konsisten pada development, testing, staging, dan production.
- Menjamin perubahan database dapat dilacak melalui source control.
- Mengurangi risiko kehilangan data, kegagalan deployment, dan schema drift.
- Memastikan perubahan schema kompatibel dengan code aplikasi.
- Menjaga integritas relasi, constraint, auditability, dan keamanan data.
- Menyediakan prosedur rollback dan pemulihan yang jelas.

---

# 2. Prinsip Utama

## 2.1 Migration adalah sumber schema

Schema database BegawiPBJ dibangun melalui migration CodeIgniter 4.

```text
app/Database/Migrations/
```

Dokumen database seperti blueprint, ERD, dan data dictionary menjadi acuan desain. Migration aktif menjadi implementasi teknis yang dijalankan pada environment aplikasi.

## 2.2 Append-only

Migration yang telah diterapkan pada environment bersama atau production bersifat **append-only**.

```text
Jangan:
- Mengubah isi migration yang telah dijalankan.
- Mengganti nama file migration yang telah dijalankan.
- Mengubah timestamp migration yang telah dijalankan.
- Menghapus migration dari source control.

Gunakan:
- Migration baru untuk setiap perubahan schema berikutnya.
```

## 2.3 Satu tanggung jawab

Setiap migration harus memiliki satu tanggung jawab domain yang jelas.

Contoh baik:

```text
CreateIdentityAndRbacTables
CreateOrganizationAndWilayahTables
CreateLearningCoreTables
CreateLearningAssessmentTables
AddCertificateExpiryIndex
AddEventRegistrationWaitlistStatus
```

Contoh yang harus dihindari:

```text
UpdateDatabase
FixEverything
MigrationBaru
CreateAllTables
```

## 2.4 Aman terhadap data

Migration tidak boleh menghapus, mengubah tipe, atau memindahkan data penting tanpa:

1. Analisis dampak.
2. Backup terverifikasi.
3. Rencana migrasi data.
4. Rencana rollback atau recovery.
5. Uji pada salinan data yang representatif.
6. Persetujuan pihak berwenang bila production terdampak.

---

# 3. Struktur Migration

```text
app/Database/Migrations/
├── 2026-10-01-000001_CreateIdentityAndRbacTables.php
├── 2026-10-01-000002_CreateOrganizationAndWilayahTables.php
├── 2026-10-01-000003_CreateSharedWorkflowAuditFileTables.php
├── 2026-10-01-000004_CreateLearningCoreTables.php
├── 2026-10-01-000005_CreateLearningAssessmentTables.php
├── 2026-10-01-000006_CreateKnowledgeTables.php
├── 2026-10-01-000007_CreateRegulationTables.php
├── 2026-10-01-000008_CreateCommunicationTables.php
├── 2026-10-01-000009_CreateCertificateTables.php
├── 2026-10-01-000010_CreateGovernmentIntelligenceTables.php
└── 2026-10-01-000011_AddPerformanceIndexesAndConstraints.php
```

## 3.1 Format nama file

CodeIgniter 4 menggunakan pola berikut:

```text
YYYY-MM-DD-HHMMSS_DescriptiveMigrationName.php
```

Contoh:

```text
2026-10-01-090000_CreateLearningCoreTables.php
2026-10-02-103000_AddEventAttendanceIndexes.php
2026-10-03-140500_AddCertificateRevocationReason.php
```

Aturan nama:

- Gunakan PascalCase setelah underscore.
- Gunakan kata kerja di awal: `Create`, `Add`, `Alter`, `Drop`, `Rename`, atau `Backfill`.
- Nama harus menjelaskan domain dan perubahan.
- Hindari singkatan yang tidak jelas.

## 3.2 Namespace migration

Gunakan namespace aplikasi CodeIgniter:

```php
namespace App\Database\Migrations;
```

Class name harus sesuai dengan nama migration:

```php
class CreateLearningCoreTables extends Migration
{
    // ...
}
```

---

# 4. Urutan Domain

Dependency migration BegawiPBJ harus mengikuti urutan berikut:

```text
1. Identity dan RBAC
2. Wilayah dan Organisasi
3. Shared: File Asset, Approval, Audit, Notification
4. Arena Belajar Core
5. Arena Belajar Assessment dan Grading
6. Bunga Rampai
7. Regulasi PBJ
8. Berita, Agenda, dan Event
9. Sertifikat Digital
10. Government Intelligence PBJ
11. Indeks, Constraint, dan Optimasi Tambahan
```

## 4.1 Alasan urutan

| Domain | Bergantung pada |
|---|---|
| Identity/RBAC | Tidak ada domain aplikasi lain |
| Wilayah/Organisasi | Identity untuk relasi organisasi user |
| Shared | Users untuk uploader, actor, reviewer, notification recipient |
| Learning | Users dan file assets |
| Assessment | Learning core dan enrollment |
| Knowledge | Users dan file assets |
| Regulation | Users dan file assets |
| Communication | Users dan file assets |
| Certificate | Users, file assets, enrollment, event registration |
| Intelligence | Users, organisasi, file assets |

---

# 5. Standar Implementasi Migration

## 5.1 Engine, charset, dan collation

Semua tabel aplikasi menggunakan:

```php
$this->forge->createTable('table_name', true, [
    'ENGINE'  => 'InnoDB',
    'DEFAULT CHARACTER SET' => 'utf8mb4',
    'COLLATE' => 'utf8mb4_0900_ai_ci',
]);
```

Jika konfigurasi database sudah menetapkan charset dan collation standar, migration tetap harus konsisten dan tidak memperkenalkan engine MyISAM.

## 5.2 Primary key dan foreign key

Gunakan `BIGINT UNSIGNED` untuk identifier aplikasi.

```php
'id' => [
    'type'           => 'BIGINT',
    'constraint'     => 20,
    'unsigned'       => true,
    'auto_increment' => true,
],
```

Foreign key harus memiliki tipe yang sama dengan key target:

```php
'user_id' => [
    'type'       => 'BIGINT',
    'constraint' => 20,
    'unsigned'   => true,
],
```

Jangan mencampur `INT`, `BIGINT`, signed, dan unsigned pada relasi yang sama.

## 5.3 Timestamps

Gunakan standar:

```php
'created_at' => [
    'type' => 'DATETIME',
    'null' => false,
],
'updated_at' => [
    'type' => 'DATETIME',
    'null' => true,
],
'deleted_at' => [
    'type' => 'DATETIME',
    'null' => true,
],
```

`deleted_at` hanya ditambahkan pada entitas yang membutuhkan soft delete, misalnya user, program, course, artikel, studi kasus, template, regulasi, berita, event, dan metadata file.

## 5.4 Status

Gunakan `VARCHAR(32)` untuk status yang mungkin berkembang.

```php
'workflow_status' => [
    'type'       => 'VARCHAR',
    'constraint' => 32,
    'default'    => 'draft',
],
```

Validasi nilai status dilakukan pada service dan state machine, bukan semata-mata pada database enum.

## 5.5 JSON

Gunakan `JSON` hanya untuk data fleksibel yang tidak memerlukan join/filter utama.

Contoh penggunaan yang benar:

```text
audit_logs.before_data
audit_logs.after_data
audit_logs.context_data
notifications.data
quiz_answers.answer_snapshot
```

Jangan gunakan JSON untuk relasi inti seperti role user, enrollment peserta, atau metadata regulasi yang perlu dicari/filter secara terstruktur.

---

# 6. Master Wilayah

Master wilayah BegawiPBJ menggunakan struktur resmi berikut:

```sql
CREATE TABLE wilayah (
    kode VARCHAR(13) NOT NULL,
    nama VARCHAR(100) NOT NULL,
    PRIMARY KEY (kode),
    KEY wilayah_name_idx (nama)
) ENGINE=InnoDB;
```

## 6.1 Aturan migration wilayah

- `wilayah.kode` adalah primary key `VARCHAR(13)`.
- `wilayah.nama` menggunakan `VARCHAR(100)`.
- Tidak menggunakan kolom `parent_code`, `level`, atau `is_active`.
- Tabel wajib menggunakan InnoDB agar dapat direferensikan oleh foreign key profil user.
- Data wilayah lengkap diimpor dari `database/reference/wilayah.sql` atau `WilayahImportSeeder`.

## 6.2 Foreign key wilayah

Kolom berikut pada `user_profiles` merujuk ke `wilayah.kode`:

```text
province_code
regency_code
district_code
village_code
```

Contoh constraint:

```php
$this->forge->addForeignKey('province_code', 'wilayah', 'kode', 'RESTRICT', 'RESTRICT', 'fk_profile_province');
$this->forge->addForeignKey('regency_code', 'wilayah', 'kode', 'RESTRICT', 'RESTRICT', 'fk_profile_regency');
$this->forge->addForeignKey('district_code', 'wilayah', 'kode', 'RESTRICT', 'RESTRICT', 'fk_profile_district');
$this->forge->addForeignKey('village_code', 'wilayah', 'kode', 'RESTRICT', 'RESTRICT', 'fk_profile_village');
```

---

# 7. Foreign Key Policy

## 7.1 Default delete behavior

BegawiPBJ mengutamakan histori, auditability, dan bukti pembelajaran. Kebijakan default untuk relasi bisnis adalah:

```text
ON DELETE RESTRICT
ON UPDATE RESTRICT
```

Gunakan `SET NULL` hanya untuk referensi aktor/histori yang boleh kehilangan relasi langsung saat akun tidak aktif/dihapus secara administratif.

## 7.2 Matriks delete policy

| Relasi | Delete policy | Alasan |
|---|---|---|
| User → profile/role/enrollment | RESTRICT | Histori pembelajaran dan akses harus tetap terjaga |
| User sebagai audit actor | SET NULL | Audit harus tetap disimpan |
| User sebagai uploader | SET NULL | Metadata file tetap valid |
| Course → class/module | RESTRICT | Histori kelas dan pembelajaran dilindungi |
| Class → enrollment | RESTRICT | Enrollment dan nilai tidak boleh hilang |
| Enrollment → final grade | RESTRICT | Bukti kelulusan dilindungi |
| Certificate → verification/revocation | RESTRICT | Histori sertifikat tidak boleh hilang |
| Regulation → versions | RESTRICT | Versioning regulasi dipertahankan |
| File asset → entity | RESTRICT | File tidak dihapus ketika masih direferensikan |
| Approval → histories | RESTRICT | Histori workflow dipertahankan |

## 7.3 Larangan cascade delete

`ON DELETE CASCADE` tidak digunakan pada relasi berikut:

```text
users → audit_logs
users → certificates
learning_classes → enrollments
enrollments → final_grades
certificates → certificate_revocations
regulations → regulation_versions
```

---

# 8. Unique Constraint Policy

Unique constraint digunakan untuk melindungi integritas bisnis dan idempotency.

| Tabel | Constraint | Tujuan |
|---|---|---|
| `users` | username, email | Identitas akun unik |
| `roles` | code | Role unik |
| `permissions` | code | Permission unik |
| `user_roles` | user_id + role_id | Role user tidak ganda |
| `role_permissions` | role_id + permission_id | Mapping role-permission tidak ganda |
| `enrollments` | user_id + learning_class_id | Peserta tidak mendaftar dua kali |
| `learning_progress` | enrollment_id + lesson_id | Satu progress per lesson |
| `quiz_attempts` | quiz_id + enrollment_id + attempt_number | Attempt unik |
| `quiz_answers` | quiz_attempt_id + question_id | Satu jawaban per soal per attempt |
| `assignment_submissions` | assignment_id + enrollment_id + attempt_number | Submission unik |
| `final_grades` | enrollment_id | Satu nilai akhir |
| `articles`, `courses`, `events` | slug | URL publik unik |
| `regulations` | category + number + year | Identitas regulasi unik |
| `regulation_versions` | regulation_id + version_number | Versi regulasi unik |
| `event_registrations` | event_id + user_id | Registrasi event unik |
| `event_attendances` | event_registration_id | Satu absensi per registrasi |
| `certificates` | certificate_number | Nomor sertifikat unik |
| `certificates` | verification_token | Token QR unik |
| `certificates` | source_type + source_id | Satu sumber satu sertifikat resmi |
| `certificate_sequences` | sequence_scope + year | Sequence unik per scope/tahun |
| `certificate_revocations` | certificate_id | Satu revocation per sertifikat |
| `competency_certificates` | type + certificate_number | Nomor kompetensi unik per jenis |
| `competency_gap_snapshots` | unit + period + metric | Snapshot unik |

---

# 9. Index Policy

## 9.1 Index wajib

Setiap foreign key harus memiliki index. Tambahkan composite index untuk pola filter dan urut yang sering digunakan.

Contoh indeks utama:

```text
users(account_status, created_at)
enrollments(learning_class_id, enrollment_status)
enrollments(user_id, final_status)
learning_progress(enrollment_id, completed_at)
quiz_attempts(enrollment_id, quiz_id)
assignment_submissions(enrollment_id, assignment_id, status)
articles(workflow_status, published_at)
regulations(legal_status, year)
regulations(workflow_status, published_at)
events(event_status, start_at)
event_registrations(event_id, registration_status)
certificates(recipient_user_id, status)
certificates(verification_token)
approvals(subject_type, subject_id, workflow_status)
audit_logs(subject_type, subject_id, created_at)
notifications(user_id, read_at, created_at)
competency_gap_snapshots(organization_unit_id, period_date, metric_code)
```

## 9.2 Kriteria index baru

Tambahkan index baru bila:

- Query digunakan pada daftar data besar.
- Kolom rutin digunakan pada `WHERE`, `JOIN`, `ORDER BY`, atau `GROUP BY`.
- Query dashboard membutuhkan agregasi berulang.
- Query sudah diukur lambat melalui query log/profiling.

Jangan menambahkan index tanpa alasan karena index memperbesar storage dan biaya `INSERT`/`UPDATE`.

---

# 10. Migration `up()` dan `down()`

## 10.1 `up()`

Method `up()` membuat perubahan schema ke versi baru.

```php
public function up()
{
    $this->forge->addField([
        'id' => [
            'type'           => 'BIGINT',
            'constraint'     => 20,
            'unsigned'       => true,
            'auto_increment' => true,
        ],
        'code' => [
            'type'       => 'VARCHAR',
            'constraint' => 64,
        ],
        'created_at' => [
            'type' => 'DATETIME',
            'null' => false,
        ],
    ]);

    $this->forge->addKey('id', true);
    $this->forge->addUniqueKey('code', 'uq_example_code');
    $this->forge->createTable('example_entities', true);
}
```

## 10.2 `down()`

Method `down()` digunakan untuk rollback pada development/testing atau pada perubahan yang telah diuji aman.

```php
public function down()
{
    $this->forge->dropTable('example_entities', true);
}
```

`down()` harus:

- Menghapus objek dalam urutan aman.
- Menghapus foreign key/index sebelum kolom atau tabel bila diperlukan.
- Tidak menghapus data production tanpa prosedur recovery yang disetujui.
- Diuji dengan rollback pada database development.

## 10.3 Rollback terbatas

Untuk perubahan destruktif pada production, rollback schema otomatis tidak selalu aman. Dalam kondisi tersebut:

```text
- Gunakan migration forward-only.
- Siapkan backup sebelum deployment.
- Siapkan migration korektif baru bila diperlukan.
- Dokumentasikan langkah recovery data.
```

---

# 11. Perubahan Schema Aman

## 11.1 Menambah tabel

Menambah tabel baru adalah perubahan relatif aman bila dependency sudah tersedia.

Checklist:

- Nama tabel domain jelas.
- PK/FK konsisten.
- Index FK dibuat.
- Constraint unik ditetapkan bila diperlukan.
- Seeder/test disiapkan.
- Data dictionary dan ERD diperbarui.

## 11.2 Menambah kolom nullable

Menambah kolom nullable umumnya aman:

```php
$this->forge->addColumn('events', [
    'speaker_name' => [
        'type'       => 'VARCHAR',
        'constraint' => 150,
        'null'       => true,
        'after'      => 'description',
    ],
]);
```

## 11.3 Menambah kolom wajib

Untuk kolom baru yang wajib pada tabel berisi data:

```text
1. Tambahkan kolom nullable atau dengan default aman.
2. Backfill data melalui command/migration terpisah.
3. Validasi data yang belum terisi.
4. Ubah menjadi NOT NULL melalui migration berikutnya.
```

## 11.4 Rename kolom atau tabel

Gunakan pola kompatibilitas bertahap:

```text
1. Tambahkan kolom/tabel baru.
2. Update aplikasi untuk menulis data ke struktur baru.
3. Backfill data lama.
4. Validasi hasil.
5. Pindahkan pembacaan ke struktur baru.
6. Hapus struktur lama hanya melalui migration setelah masa kompatibilitas selesai.
```

## 11.5 Mengubah tipe kolom

Sebelum mengubah tipe kolom:

- Periksa data aktual yang ada.
- Uji nilai batas, nilai null, dan format yang tidak valid.
- Periksa seluruh index dan FK terkait.
- Uji pada salinan data.
- Pastikan perubahan tidak mengunci tabel terlalu lama pada production.

---

# 12. Perubahan Destruktif

Perubahan berikut dianggap destruktif:

```text
DROP TABLE
DROP COLUMN
TRUNCATE TABLE
Mengurangi panjang VARCHAR
Mengubah nullable menjadi NOT NULL tanpa backfill
Menghapus enum/status yang masih digunakan
Menghapus index unik
Mengubah foreign key/delete policy
Menghapus atau mengganti data master
```

## 12.1 Prosedur perubahan destruktif

1. Buat dokumen dampak perubahan.
2. Identifikasi tabel, kolom, data, service, route, report, dan test yang terdampak.
3. Buat backup database dan verifikasi backup dapat direstore.
4. Uji migration pada database salinan.
5. Siapkan migration data/backfill bila diperlukan.
6. Siapkan rencana rollback atau recovery.
7. Jalankan pada maintenance window bila diperlukan.
8. Verifikasi schema, data, log aplikasi, dan fitur kritis setelah deployment.

---

# 13. Data Migration dan Backfill

## 13.1 Pemisahan schema dan data

Perubahan schema dan perubahan data besar sebaiknya dipisahkan:

```text
Migration schema
↓
Command/Service backfill data
↓
Verification command
↓
Migration constraint final
```

Contoh:

```text
AddCertificateExpiryColumn
↓
BackfillCertificateExpiryCommand
↓
VerifyCertificateExpiryCommand
↓
AddCertificateExpiryIndex
```

## 13.2 Aturan backfill

- Backfill harus idempotent bila memungkinkan.
- Backfill harus berjalan per batch pada dataset besar.
- Backfill harus mencatat progress dan error.
- Backfill tidak boleh memperlihatkan data pribadi pada log umum.
- Backfill harus dapat dihentikan dan dilanjutkan secara aman.
- Backfill besar dijalankan oleh command CLI/queue, bukan web request.

## 13.3 Data master

Data master disediakan melalui seeder atau import terkontrol:

```text
Role dan permission         → Reference seeder
Organisasi/OPD              → OrganizationSeeder/OpdImportSeeder
Wilayah                     → database/reference/wilayah.sql atau WilayahImportSeeder
Kategori regulasi           → RegulationCategorySeeder
Tahapan PBJ                 → ProcurementStageSeeder
Kompetensi                  → CompetencySeeder
Jenjang JF PPBJ             → JfPpbjLevelSeeder
Jenis sertifikat kompetensi → CompetencyCertificateTypeSeeder
```

Migration tidak digunakan untuk memasukkan data master dalam jumlah besar, kecuali nilai minimum yang benar-benar diperlukan agar foreign key/schema dapat dibuat.

---

# 14. Seeder Policy

## 14.1 Struktur seeder

```text
app/Database/Seeds/
├── DatabaseSeeder.php
├── Reference/
├── System/
├── Development/
└── Import/
```

## 14.2 Kategori seeder

| Kategori | Tujuan | Boleh di production? |
|---|---|---:|
| `Reference` | Role, permission, kategori, tahapan, master dasar | Ya |
| `System` | Pengaturan dasar dan Super Admin awal berbasis environment | Ya, dengan kontrol |
| `Import` | Wilayah, OPD, master resmi | Ya, dengan validasi sumber |
| `Development` | User dummy, demo course, demo regulasi, data testing | Tidak |

## 14.3 Idempotency seeder

Seeder reference harus aman dijalankan lebih dari sekali dengan pola:

```text
- Cari berdasarkan code/slug unik.
- Insert bila belum ada.
- Update field non-kritis bila perlu.
- Jangan menduplikasi mapping role-permission.
```

## 14.4 Credential seeder

Seeder Super Admin awal menggunakan environment variable:

```ini
INITIAL_SUPERADMIN_NAME=
INITIAL_SUPERADMIN_EMAIL=
INITIAL_SUPERADMIN_USERNAME=
INITIAL_SUPERADMIN_PASSWORD=
```

Aturan:

- Jangan hardcode password plaintext di source code.
- Gunakan `password_hash()` saat membuat password.
- Seeder gagal secara jelas jika environment production tidak memiliki credential wajib.
- Ganti credential awal segera setelah instalasi.

---

# 15. Development dan Testing Workflow

## 15.1 Database development baru

```bash
php spark migrate:fresh
php spark db:seed DatabaseSeeder
```

Import wilayah bila menggunakan import SQL terpisah:

```bash
mysql -u root -p db_begawipbj < database/reference/wilayah.sql
```

Pastikan import wilayah dilakukan sebelum seeder/user data yang membuat profil dengan foreign key wilayah.

## 15.2 Menjalankan migration

```bash
php spark migrate
```

## 15.3 Status migration

```bash
php spark migrate:status
```

## 15.4 Rollback development

```bash
php spark migrate:rollback
```

Gunakan rollback hanya pada database development/testing atau prosedur yang telah disetujui.

## 15.5 Test

```bash
php spark test
```

Test database minimal:

- Migration dari database kosong berhasil.
- Rollback batch development berhasil bila `down()` mendukung.
- Seeder reference dapat dijalankan dengan aman.
- Foreign key menolak relasi tidak valid.
- Unique constraint mencegah duplikasi bisnis.
- Permission, workflow, sertifikat, dan file access lulus feature test.

---

# 16. Production Deployment Policy

## 16.1 Sebelum deployment

Checklist wajib:

- Migration telah direview.
- Migration diuji pada database kosong dan salinan data yang relevan.
- Backup database terbaru tersedia.
- Restore backup telah diuji atau prosedurnya tervalidasi.
- Perubahan schema kompatibel dengan code release.
- Backfill dan perubahan destruktif memiliki rencana eksekusi.
- Estimasi lock/impact tabel telah diketahui.
- Maintenance window tersedia bila diperlukan.
- Monitoring aplikasi dan database tersedia.

## 16.2 Urutan deployment

```text
1. Aktifkan maintenance mode bila diperlukan.
2. Buat backup database dan writable/uploads.
3. Deploy code yang kompatibel.
4. Jalankan migration.
5. Jalankan seeder reference bila diperlukan.
6. Jalankan backfill/command terpisah bila diperlukan.
7. Verifikasi migration status.
8. Jalankan smoke test fitur kritis.
9. Periksa log aplikasi/database.
10. Nonaktifkan maintenance mode.
```

## 16.3 Smoke test setelah migration

```text
- Login Admin dan Peserta.
- Landingpage dan statistik publik.
- Dashboard Admin.
- Workspace Peserta/Trainer/Evaluator/Operator.
- Katalog Arena Belajar dan enrollment.
- Upload/download file sesuai permission.
- Regulasi dan versioning.
- Event registration dan attendance.
- Certificate verification.
- Intelligence dashboard internal.
```

---

# 17. Backup dan Recovery

## 17.1 Backup minimum

Backup terdiri dari dua bagian:

```text
1. Database MySQL.
2. Folder writable/uploads/.
```

Contoh backup database:

```bash
mysqldump -u <username> -p <database_name> > backup.sql
```

## 17.2 Sebelum migration berisiko

- Buat backup database lengkap.
- Catat timestamp, versi code, hash commit, dan migration batch.
- Simpan backup di lokasi aman di luar server utama bila memungkinkan.
- Pastikan backup dapat dibuka/dipulihkan.

## 17.3 Recovery

Recovery dapat mencakup:

```text
- Restore database backup.
- Restore writable/uploads yang sesuai timestamp database.
- Deploy code versi kompatibel.
- Verifikasi migration status.
- Jalankan smoke test.
```

---

# 18. Dokumentasi Perubahan

Setiap migration harus memperbarui dokumen yang relevan:

| Perubahan | Dokumen yang diperbarui |
|---|---|
| Tabel/kolom/relasi baru | Data dictionary dan ERD |
| Domain baru | Database blueprint dan directory architecture |
| Role/permission baru | RBAC matrix |
| Status/transition baru | Workflow state machine |
| Route/API baru | Route map/API conventions |
| Perubahan deployment | Environment dan security checklist |

Untuk perubahan besar, buat Architecture Decision Record:

```text
docs/decisions/ADR-<nomor>-<judul>.md
```

---

# 19. Checklist Review Migration

Sebelum migration disetujui, periksa:

## Schema

- [ ] Nama migration jelas dan unik.
- [ ] Domain dan tanggung jawab migration jelas.
- [ ] Tipe PK/FK konsisten.
- [ ] Engine InnoDB digunakan.
- [ ] Charset/collation konsisten.
- [ ] Index untuk FK tersedia.
- [ ] Unique constraint bisnis tersedia.
- [ ] Delete policy sesuai auditability.
- [ ] `up()` dan `down()` valid untuk environment target.

## Data

- [ ] Tidak ada data pribadi atau credential dalam migration.
- [ ] Perubahan data besar memakai command/backfill yang terpisah.
- [ ] Seeder reference dan demo dipisahkan.
- [ ] Data wilayah tetap menggunakan `wilayah(kode, nama)`.
- [ ] File placeholder menggunakan `file_assets.path = NULL`.

## Security dan operasional

- [ ] Authorization tidak bergantung pada struktur database saja.
- [ ] Audit requirement dipertimbangkan.
- [ ] Backup/recovery tersedia untuk perubahan berisiko.
- [ ] Migration diuji pada database kosong.
- [ ] Migration diuji pada data yang relevan bila production terdampak.
- [ ] Dokumentasi diperbarui.

---

# 20. Larangan

```text
- Jangan membuat tabel manual langsung pada production tanpa migration.
- Jangan mengubah migration yang telah diterapkan pada environment bersama.
- Jangan menjalankan migrate:fresh pada production.
- Jangan menjalankan seeder Development pada production.
- Jangan menyimpan data produksi, password, token, atau dump produksi di repository.
- Jangan menggunakan MyISAM untuk tabel aplikasi yang membutuhkan foreign key/transaksi.
- Jangan menggunakan ON DELETE CASCADE untuk histori, sertifikat, nilai akhir, audit, atau regulasi.
- Jangan menghapus kolom/tabel tanpa rencana data migration dan backup.
- Jangan menonaktifkan foreign key checks pada production sebagai solusi permanen.
- Jangan membuat constraint unik yang tidak sesuai dengan aturan bisnis.
```

---

# 21. Ringkasan

```text
Schema source of truth : CodeIgniter 4 migration
Data reference         : Seeder/import terkontrol
Data demo              : Development seeder terpisah
Production changes     : Append-only, tested, backed up, documented
Foreign key strategy   : BIGINT UNSIGNED + InnoDB + RESTRICT/SET NULL
Wilayah                : wilayah(kode, nama)
Auditability           : Hindari cascade delete pada histori penting
File placeholder       : file_assets.path = NULL
Certificate integrity  : UNIQUE(source_type, source_id)
```

Kebijakan ini memastikan perubahan database BegawiPBJ berjalan konsisten, terukur, aman, dapat dipulihkan, dan sesuai dengan kebutuhan ekosistem PBJ Pemerintah Provinsi Lampung.
