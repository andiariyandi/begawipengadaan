# BegawiPengadaan — Kebijakan Tata Kelola Regulasi Hukum PBJ

> **Metadata Dokumen:**
> - **Versi:** 4.2 (Production Ready)
> - **Tanggal Sinkronisasi:** 7 November 2026
> - **Sumber Kebenaran:** MySQL Live `db_begawipbj` (95 Tabel, 1.127 Kolom, 22 CHECK Constraints, 17 Migrasi) & Codebase Aktual

---

## 1. Hierarki Produk Hukum PBJ

Modul Regulasi mengelola dokumentasi hukum pengadaan barang dan jasa dari tingkat nasional hingga daerah:
1. **Undang-Undang (UU):** Landasan hukum primer (misal: UU No. 1/2004).
2. **Peraturan Pemerintah (PP):** Kebijakan turunan undang-undang.
3. **Peraturan Presiden (Perpres):** Regulasi payung nasional (Perpres 16/2018 jo. Perpres 12/2021).
4. **Peraturan LKPP:** Pedoman teknis operasional pengadaan nasional.
5. **Surat Edaran LKPP:** Penegasan dan instruksi teknis berkala.
6. **Peraturan Daerah (Perda) & Peraturan Gubernur (Pergub) Lampung:** Regulasi tata kelola PBJ di lingkungan Pemprov Lampung.

---

## 2. Status Hukum & Relasi Peraturan (`legal_status` & `regulation_relations`)

Setiap naskah regulasi memiliki atribut `legal_status` sesuai CHECK constraint `regulations_legal_status_chk`:
- `active`: Regulasi berlaku aktif dan menjadi pedoman sah saat ini.
- `amended`: Regulasi telah diubah sebagian oleh regulasi yang lebih baru.
- `revoked`: Regulasi telah dicabut/tidak berlaku lagi.
- `expired`: Regulasi telah kedaluwarsa masa berlakunya.

Tabel `regulation_relations` memetakan relasi timbal-balik antar-produk hukum dengan tipe relasi sah:
- `amends` / `amended_by` (mengubah / diubah oleh)
- `revokes` / `revoked_by` (mencabut / dicabut oleh)
- `replaces` / `replaced_by` (menggantikan / digantikan oleh)
- `implements` / `implemented_by` (melaksanakan / dilaksanakan oleh)
- `references` / `referenced_by` (merujuk / dirujuk oleh)

---

## 3. Manajemen Versi Berkas (`regulation_versions`)

Pembaruan dokumen fisik regulasi tidak menimpa berkas lama, melainkan dicatat pada `regulation_versions`:
- Menyimpan nomor versi (misal: `v1.0`, `v1.1`), catatan perubahan, dan referensi berkas PDF pada `file_assets`.
- Mengatur `current_version_id` pada tabel `regulations` untuk menampilkan berkas mutakhir pada katalog publik.
