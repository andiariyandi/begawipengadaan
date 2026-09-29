# BegawiPBJ Backup and Recovery

SOP ini mengatur pencadangan, pemeriksaan, pemulihan, dan pengujian database MySQL serta file `writable/uploads/` BegawiPBJ. Keduanya harus konsisten: metadata `file_assets`, regulasi, materi, submission, dan PDF sertifikat di database merujuk ke berkas fisik.

## Tujuan dan pemilik

- Menjaga data akun, RBAC, pembelajaran, nilai, approval, audit, sertifikat, OPD, kompetensi, dan dokumen.
- Menyediakan kemampuan pulih dari kesalahan operator, kegagalan disk, gangguan server, dan insiden keamanan.
- Pemilik teknis: administrator server/database yang ditunjuk. Pemilik keputusan restore production: pengelola layanan BegawiPBJ sesuai prosedur instansi.
- RPO (toleransi kehilangan data) dan RTO (batas waktu pemulihan) **harus disetujui pemilik layanan** sebelum produksi; angka baku tidak diasumsikan dalam dokumen ini.

## Cakupan backup

| Komponen | Wajib | Isi |
|---|---:|---|
| Database `db_begawipbj` | Ya | Semua tabel aplikasi dan CI4 migrations table |
| `writable/uploads/` | Ya | Materi, submission, regulasi, template, PDF sertifikat, bukti kompetensi |
| `.env`/secret | Ya, melalui mekanisme aman terpisah | Kredensial DB, encryption key, SMTP, config integrasi |
| Versi aplikasi | Ya | Tag/commit deployment, `composer.lock`, migration status |
| `writable/logs/` | Menurut kebijakan | Bukti investigasi/retensi, bukan syarat pemulihan data utama |
| Cache/session/export sementara | Tidak selalu | Dapat dibuat kembali kecuali kebijakan menetapkan lain |

Master `wilayah(kode,nama)` dan daftar OPD juga ada dalam database backup. File sumber master referensi pada repository bukan pengganti backup data operasional.

## Strategi backup

- Buat backup berkala sesuai RPO yang disetujui; siapkan backup sebelum setiap migration berisiko dan deployment besar.
- Simpan setidaknya satu salinan di lokasi berbeda dari server utama, dengan akses terbatas dan enkripsi yang disetujui.
- Pisahkan akses akun aplikasi, akun backup, dan akun restore.
- Atur retensi, rotasi, dan penghancuran backup menurut kebijakan resmi instansi; jangan mengasumsikan durasi tertentu.
- Pastikan seluruh file hasil backup memiliki checksum dan manifest yang mengaitkan waktu DB, snapshot upload, release code, serta migration status.
- Pantau kegagalan job backup; backup dianggap berhasil setelah file dibuat **dan** dapat dibaca/diuji.

## Konsistensi database dan file

MySQL InnoDB dapat di-dump secara konsisten dengan `--single-transaction`, tetapi transaksi SQL tidak membekukan file upload pada filesystem. Untuk mendapatkan pasangan DB+file yang serasi, gunakan salah satu pola yang disetujui:

1. Maintenance/read-only window: hentikan sementara penulisan upload dan perubahan DB yang menambah referensi file, lakukan dump, salin uploads, lalu buka layanan lagi.
2. Snapshot storage dan database yang dikoordinasikan oleh infrastruktur; verifikasi titik waktunya.
3. Object storage dengan versioning dan backup yang terkoordinasi dengan snapshot DB.

Untuk backup manual awal, pola maintenance/read-only paling mudah diaudit. Setelah restore, lakukan rekonsiliasi metadata `file_assets.path` terhadap file fisik. `path IS NULL` adalah placeholder yang sah, bukan file hilang.

## Prosedur backup manual

1. Pastikan target backup tidak berada di `public/`, tidak bisa diunduh HTTP, dan kapasitas cukup.
2. Catat waktu UTC, versi release/commit, database name, migration status, dan operator.
3. Aktifkan mode maintenance/read-only bila perlu untuk konsistensi upload.
4. Jalankan dump database dengan akun backup berizin:

```bash
mysqldump --single-transaction --quick --routines --triggers \
  -u <backup_user> -p db_begawipbj > begawipbj-db-<timestamp>.sql
```

5. Salin `writable/uploads/` ke storage backup dengan metadata/perizinan yang diperlukan. Contoh Linux:

```bash
tar -C writable -czf begawipbj-uploads-<timestamp>.tar.gz uploads
```

6. Buat checksum file backup:

```bash
sha256sum begawipbj-db-<timestamp>.sql begawipbj-uploads-<timestamp>.tar.gz \
  > begawipbj-backup-<timestamp>.sha256
```

7. Catat manifest (timestamp, DB dump, upload archive, checksum, release, migration batch). Amankan salinan secret secara terpisah.
8. Uji checksum dan minimal inspeksi dump. Tutup maintenance setelah pasangan backup selesai.

Contoh ini perlu disesuaikan pada Windows/Laragon dan infrastruktur deployment aktual. Jangan menaruh password pada command line atau script repository.

## Frekuensi dan retensi

| Parameter | Yang harus ditetapkan pemilik layanan |
|---|---|
| Frekuensi full/incremental backup | Berdasarkan RPO dan volume perubahan |
| Retensi backup harian/mingguan/bulanan | Kebijakan data instansi |
| Lokasi salinan offsite | Keamanan fisik dan akses |
| Jadwal restore drill | Teratur dan setelah perubahan infrastruktur |
| RTO | Batas pemulihan yang disepakati |
| Penanganan backup insiden | Isolasi dan chain of custody |

## Prosedur pemulihan

1. Konfirmasi insiden, tentukan titik pemulihan, dan dapatkan persetujuan pemilik layanan.
2. Isolasi/maintenance sistem untuk mencegah write baru; simpan bukti insiden dan backup kondisi terkini bila aman.
3. Verifikasi checksum dump, arsip uploads, dan versi release yang cocok.
4. Siapkan server/database target yang bersih; jangan restore langsung menimpa data tanpa persetujuan.
5. Pulihkan schema+data dari dump yang dipilih:

```bash
mysql -u <restore_user> -p db_begawipbj < begawipbj-db-<timestamp>.sql
```

6. Pulihkan arsip `uploads/` ke `writable/` dengan izin file yang benar. Jangan meletakkan uploads di `public/assets/`.
7. Pulihkan konfigurasi secret melalui mekanisme aman; gunakan `encryption.key` yang sesuai dengan data terenkripsi pada dump.
8. Deploy release aplikasi yang cocok dan cek `php spark migrate:status`. Jangan sembarang menjalankan `migrate:fresh`.
9. Verifikasi jumlah record inti, FK, halaman login, RBAC, kelas, regulasi, event, sertifikat, serta metadata-file consistency.
10. Lakukan smoke test per role, cek log, lalu buka layanan setelah persetujuan.
11. Catat waktu pemulihan, data yang hilang relatif terhadap RPO, serta tindak lanjut insiden.

## Rekonsiliasi file pasca-restore

- `file_assets.path IS NULL`: status belum tersedia; download dinonaktifkan.
- `path` terisi tetapi file tidak ada: broken reference; dokumentasikan ID dan tindakan pemulihan. Jangan mengarang file baru dengan isi salah.
- File fisik tanpa record metadata: orphan; karantina sampai pemilik data memutuskan.
- Periksa beberapa contoh per domain: materi, template, regulasi per versi, submission, sertifikat PDF, bukti kompetensi.
- Periksa hak akses download setelah restore; file privat tidak boleh menjadi public akibat konfigurasi server.

## Restore drill dan sign-off

| Pemeriksaan | Kriteria lulus |
|---|---|
| Checksum | Cocok dengan manifest |
| DB restore | Selesai tanpa error, migration status dapat dibaca |
| File restore | Arsip diekstrak dan permission aman |
| Konsistensi referensi | Tidak ada missing file baru di luar placeholder yang diharapkan |
| Access control | Guest/Peserta/Trainer/Evaluator/Operator/Admin terbatas sesuai RBAC |
| Sertifikat | Verifikasi valid/revoked/expired akurat; PDF owner-only |
| Privasi publik | Statistik agregat tanpa identifier individu |
| Operasional | RTO/RPO terukur dan dibandingkan target |

Hasil drill dicatat di `docs/deployment/restore-drills/` atau sistem tiket yang disetujui; jangan menyimpan log yang memuat data pribadi dalam repository publik.
