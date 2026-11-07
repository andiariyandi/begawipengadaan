# BegawiPengadaan — Kebijakan Pendaftaran & Verifikasi Akun 9 Jalur

> **Metadata Dokumen:**
> - **Versi:** 4.2 (Production Ready)
> - **Tanggal Sinkronisasi:** 7 November 2026
> - **Sumber Kebenaran:** MySQL Live `db_begawipbj` (95 Tabel, 1.127 Kolom, 22 CHECK Constraints, 17 Migrasi) & Codebase Aktual

---

## 1. Prinsip & Sembilan Jalur Pendaftaran

BegawiPengadaan melayani pendaftaran multi-profil melalui `RegistrationService`, `ProfileVerificationService`, dan `ProfileChangeRequestService`. Setiap pendaftar wajib memilih tipe profil yang sesuai dengan asal instansi/identitasnya:

1. **ASN Pemprov Lampung:** Wajib mengisi NIP valid (18 digit), NIK (16 digit), Perangkat Daerah (OPD), dan Jabatan.
2. **ASN Pemerintah Daerah Lain:** Memilih asal Pemda, NIP, NIK, dan instansi asal.
3. **ASN Kementerian / Lembaga:** Memilih nama K/L, NIP, NIK, dan unit kerja.
4. **Aparatur Pemerintah Desa:** Memilih Kabupaten, Kecamatan, Desa, NIK, dan jabatan perangkat desa.
5. **BUMN / BUMD:** Memilih instansi korporasi BUMN/BUMD, NIK, dan unit kerja.
6. **Pelaku Usaha / Penyedia PBJ:** Memasukkan NIB (Nomor Induk Berusaha), nama badan usaha/CV/PT, dan NPWP.
7. **Perguruan Tinggi / Akademisi:** Memasukkan nama universitas/institut, NIDN/NIM, dan program studi.
8. **Masyarakat Umum / Non-ASN:** Pendaftaran akun publik menggunakan NIK dan identitas kependudukan.
9. **Lainnya:** Jalur instansi kemitraan khusus.

---

## 2. Alur Verifikasi & Pengujian Kepatuhan (13 Skenario `test:registration`)

Seluruh jalur pendaftaran diuji secara otomatis oleh suite `php spark test:registration` mencakup:
- Sanitasi input form (anti-XSS dan validasi format regex).
- Isolasi profil ganda: satu akun hanya terasosiasi dengan satu profil aktif.
- Transisi `account_status`: dari `submitted` -> `pending_verification` -> `active` (atau `revision_required` / `rejected`).
- Perlindungan data kependudukan (NIK/NIP dienkripsi pada session dan disamarkan pada view publik).
