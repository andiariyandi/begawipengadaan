# BegawiPengadaan — Kebijakan Penyimpanan Berkas & Asset Pipeline

> **Metadata Dokumen:**
> - **Versi:** 4.2 (Production Ready)
> - **Tanggal Sinkronisasi:** 7 November 2026
> - **Sumber Kebenaran:** MySQL Live `db_begawipbj` (95 Tabel, 1.127 Kolom, 22 CHECK Constraints, 17 Migrasi) & Codebase Aktual

---

## 1. Arsitektur Terpusat `FileStorageService::registerFileAsset()`

Mulai versi 4.2, seluruh penyimpanan berkas dan registrasi aset disentralisasi pada `App\Services\FileStorageService`. Tidak diperbolehkan lagi perakitan array `file_assets` secara manual di controller.

### Kontrak 13 Kolom Kanonikal `file_assets`:
1. `disk` (`VARCHAR(32)`): Lokasi penyimpanan (default: `'local'`).
2. `path` (`VARCHAR(500)`): Lokasi relatif berkas dari direktori penyimpanan publik/privat.
3. `original_name` (`VARCHAR(255)`): Nama asli berkas saat diunggah oleh pengguna.
4. `stored_name` (`VARCHAR(255)`): Nama acak berkas terenkripsi di disk (mencegah penimpaan).
5. `extension` (`VARCHAR(16)`): Ekstensi berkas terverifikasi (`pdf`, `png`, `jpg`, `docx`, dsb.).
6. `mime_type` (`VARCHAR(128)`): MIME type resmi berkas.
7. `size_bytes` (`BIGINT UNSIGNED`): Ukuran berkas dalam satuan byte.
8. `checksum` (`CHAR(64)`): Hash SHA-256 berkas untuk memvalidasi integritas data.
9. `visibility` (`VARCHAR(32)`): `'public'`, `'internal'`, atau `'restricted'`.
10. `lifecycle_status` (`VARCHAR(32)`): `'available'`, `'stored'`, `'quarantined'`, dll.
11. `resource_type` (`VARCHAR(64)`): Jenis entitas pemilik (`course`, `event`, `lesson`, `assignment`, dll.).
12. `resource_id` (`BIGINT UNSIGNED`): ID entitas pemilik.
13. `uploaded_by` (`BIGINT UNSIGNED`): ID user yang mengunggah berkas.

---

## 2. Mekanisme Atomic File Rollback

Jika terjadi kegagalan basis data saat menyimpan record metadata ke tabel `file_assets`, `FileStorageService` secara otomatis mengeksekusi **Atomic File Rollback**:
- Berkas fisik yang baru dipindahkan ke disk langsung dihapus menggunakan `@unlink($physicalFilePath)`.
- Insiden kegagalan dicatat ke log sistem melalui `log_message('error', ...)`.
- Mekanisme ini menjamin **0 file yatim piatu (*orphaned files*)** pada media penyimpanan server.

---

## 3. Proteksi Path Traversal & Hardening Direktori Unggahan

1. **Path Containment (`resolveSafePath`):** Pengambilan berkas publik atau materi pembelajaran divalidasi menggunakan fungsi `resolveSafePath` yang memeriksa `realpath` berkas berada di dalam direktori penyimpanan yang sah, memblokir upaya traversal seperti `../../../../`.
2. **Hardening `.htaccess` Direktori `public/uploads/`:** Seluruh sub-direktori unggahan (`uploads/learning`, `uploads/events`, `uploads/knowledge`, `uploads/regulations`) dilindungi file `.htaccess` dengan direktif penonaktifan eksekusi skrip PHP:
   ```apache
   <IfModule mod_php.c>
       php_flag engine off
   </IfModule>
   <IfModule mod_php7.c>
       php_flag engine off
   </IfModule>
   <IfModule mod_php8.c>
       php_flag engine off
   </IfModule>
   Options -ExecCGI
   AddHandler cgi-script .php .php5 .php7 .php8 .phtml .pl .py .jsp .asp .sh .cgi
   ```
