# BegawiPengadaan — Kebijakan Privasi & Klasifikasi Data

> **Metadata Dokumen:**
> - **Versi:** 4.2 (Production Ready)
> - **Tanggal Sinkronisasi:** 7 November 2026
> - **Sumber Kebenaran:** MySQL Live `db_begawipbj` (95 Tabel, 1.127 Kolom, 22 CHECK Constraints, 17 Migrasi) & Codebase Aktual

---

## 1. Klasifikasi Data Ekosistem BegawiPengadaan

| Tingkat Klasifikasi | Lingkup Data | Contoh Entitas | Kontrak Proteksi |
|---|---|---|---|
| **Publik (Tier 1)** | Dapat diakses bebas oleh masyarakat tanpa autentikasi | Berita terbit, regulasi aktif, silabus kursus, info agenda kegiatan, statistik agregat | Dilindungi oleh `PublicVisibilityGuard` agar draft/internal tidak bocor. |
| **Internal (Tier 2)** | Terbatas untuk pengguna terdaftar yang berwenang | Materi kelas modul privat, draf naskah Bunga Rampai, riwayat presensi saya, nilai tugas | Memerlukan login sesi aktif dan role-scoped access control. |
| **Rahasia / PII (Tier 3)** | Data pribadi sensitif (*Personally Identifiable Information*) | NIK, NIP, nomor telepon, alamat rumah, password hash, IP log pengunjung | Enkripsi saat transmisi, masking pada tampilan publik, pembatasan akses ketat. |

---

## 2. Kebijakan Minimum Disclosure pada Verifikasi Sertifikat Publik

Halaman publik `/verifikasi-sertifikat` dirancang dengan prinsip *Privacy by Design*:
- Pencarian sertifikat hanya dapat dilakukan menggunakan **Nomor Sertifikat** atau **Token Kriptografis** unik.
- Seluruh direktori publik dan nomor terbit resmi disembunyikan untuk mencegah penambangan data (*scraping*).
- Hasil verifikasi **TIDAK PERNAH** menampilkan NIK atau NIP pemilik sertifikat secara utuh (maksimal masked atau hanya menampilkan Nama Lengkap, Judul Kegiatan, Tanggal Terbit, dan Status Keabsahan).
- Formulir verifikasi dilindungi oleh fitur anti-spam captcha dan rate limiting throttler.

---

## 3. Sanitasi Konten & Mitigasi Stored XSS

Seluruh masukan teks kaya (*rich text*) pada berita, artikel, dan pesan masuk disanitasi oleh `CommunicationService::sanitizeHtmlContent()` pada saat penyimpanan (*write-time*) untuk menetralkan skrip jahat (`<script>`, event handler `onerror/onload`, dan URI `javascript:`).
