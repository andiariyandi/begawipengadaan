# BegawiPengadaan — Kebijakan Statistik Publik & Analitik Kinerja Eksekutif

> **Metadata Dokumen:**
> - **Versi:** 4.2 (Production Ready)
> - **Tanggal Sinkronisasi:** 7 November 2026
> - **Sumber Kebenaran:** MySQL Live `db_begawipbj` (95 Tabel, 1.127 Kolom, 22 CHECK Constraints, 17 Migrasi) & Codebase Aktual

---

## 1. Sumber Data & Metodologi Perhitungan KPI Publik

Seluruh indikator kinerja utama (KPI) yang ditampilkan pada beranda landing page dan `/statistik` dihitung secara real-time oleh `StatisticService`, `ReportAnalyticsService`, dan `IntelligenceAnalyticsService` langsung dari basis data produksi:

1. **Total Kelas & Pembelajaran Aktif:**
   - Query: `learning_classes lc JOIN courses c ON c.id = lc.course_id`.
   - Filter Wajib: `c.status = 'published'`, `c.visibility = 'public'`, `c.deleted_at IS NULL`, dan `lc.deleted_at IS NULL`.
2. **Total Peserta & Sertifikat Terbit:**
   - Query: `COUNT(id) FROM certificates WHERE status = 'issued' AND deleted_at IS NULL`.
3. **Total Agenda Kegiatan PBJ:**
   - Mengikutsertakan seluruh event publik berstatus valid: `open_registration`, `full`, `coming_soon`, dan `finished`.
4. **Hasil Survei Kepuasan Publik:**
   - Diagregasikan dari tabel `satisfaction_surveys` dan `survey_aspects` menghasilkan Indeks Kepuasan Masyarakat (IKM).

---

## 2. Pelacakan Kunjungan Aman (`visitor_ip_logs` & `VisitorStatisticService`)

Sistem mencatat statistik trafik pengunjung tanpa mengeksekusi DDL runtime (menjamin 0 runtime CREATE/ALTER TABLE):
- Tabel `visitor_statistics`: Menyimpan agregat hit per modul (`home`, `arena`, `bunga`, `regulasi`, `berita`, `event`).
- Tabel `visitor_ip_logs`: Menyimpan jejak IP pengunjung (disamarkan/hashed) untuk mencegah double-counting dalam rentang 24 jam.
- Pembersihan berkala: Log IP dibersihkan secara otomatis via `php spark audit:prune` untuk mencegah pembengkakan memori penyimpanan.
