# MASTER PLAN: SISTEM AUDIT OTOMATIS & DETEKSI ANOMALI RUP (UNIVERSAL SIRUP COMPLIANCE ENGINE)

> [!WARNING]
> **DOKUMEN HISTORIS TELAH DIARSIPKAN (SUPERSEDED OLEH VERSI 4.0)**  
> Dokumen ini merupakan spesifikasi historis Versi 3.1 / 3.4. Seluruh arsitektur, disiplin 12 kolom CSV, katalog lengkap 40 rules, substitusi formula RCI ke *RUP Risk Attention Score*, dan kebijakan *Zero Database Migration* resmi Begawi Pengadaan saat ini telah dimutakhirkan dan distandarisasi secara definitif ke **Versi 4.0** pada dokumen:
> 1. [master_plan_sistem_audit_rup_v4_0_csv_only.md](file:///c:/laragon/www/begawipengadaan/master_plan_sistem_audit_rup_v4_0_csv_only.md) — Master Plan Sistem Audit RUP v4.0 (CSV-Only Architecture)
> 2. [pedoman_rules_dan_logika_audit_rup_v4_0_verifikasi_manual.md](file:///c:/laragon/www/begawipengadaan/pedoman_rules_dan_logika_audit_rup_v4_0_verifikasi_manual.md) — Katalog Lengkap 40 Rules, Logika Algoritma & Dasar Regulasi Audit RUP Otomatis (Versi 4.0)

**Versi Dokumen:** 3.4 (Arsip Historis / Legacy Specification)  
**Status Dokumen:** Telah Digantikan oleh Spesifikasi Definitif Versi 4.0 (CSV-Only & Zero-Migration)

---

## DAFTAR BASIS REGULASI UTAMA

1. **Peraturan Presiden Nomor 46 Tahun 2025** tentang Perubahan Kedua atas Perpres Nomor 16 Tahun 2018 tentang Pengadaan Barang/Jasa Pemerintah.
2. **Peraturan Presiden Nomor 12 Tahun 2021** tentang Perubahan atas Perpres Nomor 16 Tahun 2018 (Berlaku untuk paket dengan Tahun Anggaran $\le 2024$).
3. **Undang-Undang Nomor 20 Tahun 2023** tentang Aparatur Sipil Negara (Pasal 66 — Penataan dan Larangan Pengangkatan Pegawai Non-ASN / Honorer).
4. **Instruksi Presiden Nomor 2 Tahun 2022** tentang Percepatan Peningkatan Penggunaan Produk Dalam Negeri dan Produk Usaha Mikro, Usaha Kecil, dan Koperasi.
5. **Peraturan Pemerintah Nomor 12 Tahun 2019** dan **Permendagri Nomor 77 Tahun 2020** tentang Pedoman Teknis Pengelolaan Keuangan Daerah (Sinkronisasi Kode Rekening APBD/SIPD, Hibah, Bansos, dan BLUD).
6. **Peraturan LKPP Nomor 3 Tahun 2021** tentang Pedoman Swakelola (Klasifikasi dan Penyelenggaraan Swakelola Tipe I, II, III, dan IV).
7. **Peraturan LKPP Nomor 5 Tahun 2021** tentang Pedoman Pengadaan Barang/Jasa yang Dikecualikan pada Pengadaan Barang/Jasa Pemerintah (BLUD, Tarif Resmi, dan Praktik Bisnis yang Mapan).
8. **Peraturan LKPP Nomor 11 Tahun 2021** tentang Pedoman Perencanaan Pengadaan Barang/Jasa Pemerintah.
9. **Peraturan LKPP Nomor 3 Tahun 2026** tentang Pedoman Persiapan dan Pelaksanaan Pengadaan Barang/Jasa Pemerintah melalui Penyedia (diundangkan 22 September 2026; **Pasal 11 mencabut dan menyatakan tidak berlaku Peraturan LKPP Nomor 12 Tahun 2021 jo. Nomor 4 Tahun 2024**).
10. **Keputusan Kepala LKPP Nomor 177 Tahun 2024** (beserta perubahannya) tentang Penyelenggaraan Katalog Elektronik Versi 6 (INAPROC).
11. **Pedoman Indeks Tata Kelola Pengadaan (ITKP) LKPP & Indikator Monitoring Center for Prevention (MCP) KPK — Stranas PK** (Kepatuhan Batas Waktu Pengumuman RUP $\le 31\text{ Maret}$ dan Rasio Keterisian RUP terhadap APBD/DPA).

---

## RINGKASAN PEMUTAKHIRAN & KALIBRASI VERSI 3.4 (MULTI-PROVINCE & MULTI-YEAR CALIBRATION)

Versi 3.4 merupakan hasil kalibrasi mendalam dan *stress-test* terhadap **$49.275$ paket RUP riil (Total Pagu $\text{Rp}22,19\text{ Triliun}$)** lintas Provinsi Lampung (TA 2024, 2025, 2026) dan Provinsi Jawa Barat (TA 2026) dengan prinsip *Zero Database Migration*:

1. **De-Obfuskasi Leetspeak & Normalisasi Typo Pengadaan Lapangan (`normalizeTyposAndLeetspeak`):**
   * Mengonversi substitusi angka di dalam kata huruf (`4 -> a`, `0 -> o`, `1 -> i`, `3 -> e`, `5 -> s`) untuk membongkar obfuskasi seperti `"Belanj4 Jasa N0n Person1l"` dengan tetap melindungi akronim resmi/teknis (`a4`, `f4`, `s1`, `s2`, `s3`, `d1`, `d3`, `d4`, `p3d`, `p3dw`, `p3jb`, `p3k`, `tps3r`, `sp3or`, `o2sn`, `lp2b`, `sp2d`, `h2m`, `pd3i`, `co2`, `4g`, `5g`, `k1`, `k2`, `k3`, `r2`, `r4`, `l300`).
   * Menormalisasi kata salah ketik umum: `'belanaja' -> 'belanja'`, `'runag' -> 'ruang'`, `'frakssi' -> 'fraksi'`, `'oprassional' -> 'operasional'`, `'opersional' -> 'operasional'`, `'pemeliharan' -> 'pemeliharaan'`, `'boerorientasi' -> 'berorientasi'`, `'penysunan' -> 'penyusunan'`, `'interiro' -> 'interior'`.
2. **Perluasan Pemotongan Klausa Konteks Kegiatan pada `extractPrimaryObject`:**
   * Memotong frasa konteks: `\b(sub\s+kegiatan|sub\s+bagian|dalam\s+rangka|pada\s+kegiatan|kegiatan\s+pelaksanaan|guna\s+kegiatan)\b` jika teks objek di depannya sudah memiliki $\ge 10$ karakter alfanumerik.
3. **Kalibrasi Presisi Penamaan Paket Kabur / *Opaque Naming* (`R-34`):**
   * Mencegah *false positive* pada nama komoditas tunggal spesifik (`"Belanja Excavator"`, `"Pengadaan Laptop"`, `"Pengadaan Mebel"`, `"Belanja Cartridge"`).
   * Memicu `R-34` HANYA JIKA: (a) panjang objek $< 6$ karakter atau `token_count == 0`; (b) cocok dengan `REGEX_DICT_12_OPAQUE`; atau (c) cocok dengan daftar generik non-komoditas (`LIST_OPAQUE_GENERIC`).
4. **Pemisahan Tegas PPPK Paruh Waktu (ASN Sah) vs Honorer PTHL / TKS (`R-11`):**
   * Menambahkan `DICT_PPPK_DAN_IURAN_ASN` (`pppk`, `pegawai pemerintah dengan perjanjian kerja`, `paruh waktu`, `iuran jaminan kesehatan/kecelakaan/kematian`) ke dalam pengecualian `R-11`, sehingga pengadaan PPPK Paruh Waktu secara Swakelola tidak memicu pelanggaran.
   * Memasukkan istilah honorer non-ASN (`pthl`, `tks`, `tenaga kerja sukarela`, `tenaga harian lepas`, `tenaga pendukung`, `jaga leuweung`) ke dalam `DICT_02_OUTSOURCING` agar ditindak tegas oleh `R-11` (KRITIS).
5. **Kesadaran Rezim Regulasi Berbasis Tahun Anggaran (*Time-Aware Threshold*) pada `R-05` & `R-27`:**
   * Jika `tahun_anggaran <= 2024` (Rezim Perpres 12/2021): Batas PL Konstruksi adalah $\text{Rp}200.000.000$ (dan *Threshold Hugging* `R-27` pada $\text{Rp}190\text{M} - \text{Rp}200\text{M}$).
   * Jika `tahun_anggaran >= 2025` (Rezim Perpres 46/2025 jo. Perlem LKPP 3/2026): Batas PL Konstruksi adalah $\text{Rp}400.000.000$ (dengan validasi UMK dan teknologi sederhana pada `R-07`).
6. **Penajaman Rule `R-33` (Penyalahgunaan Jenis `Terintegrasi`):**
   * Melarang keras metode Pengadaan Langsung untuk Pekerjaan Terintegrasi (`is_integrated_package` atau `jenis_pengadaan == 'Terintegrasi'`).
   * Menindak paket berjenis `Terintegrasi` yang objeknya berupa komoditas Barang atau Jasa Lainnya biasa (seperti sewa tenda, kursi, ATK).
7. **Pengecualian Metode `Dikecualikan` pada `R-13` & `R-17`:**
   * Tidak memicu `R-13` atau `R-17` jika paket telah menggunakan metode `Dikecualikan`.
8. **Pembersihan Evaluasi *Splitting* (`R-20`) di `RupGraphClusterer`:**
   * Mengecualikan komoditas tarif resmi, utilitas, dan PPPK (`DICT_01_DIKECUALIKAN`) dari klaster *Splitting* `R-20` karena merupakan komoditas terikat tarif statutori yang diarahkan oleh `R-10` ke metode `Dikecualikan`.
9. **Pengecualian `DICT_PPPK_DAN_IURAN_ASN` & Tunda Bayar pada Afirmasi UMK (`R-24` & `R-26.1`):**
   * Mengecualikan belanja PPPK Paruh Waktu, Iuran ASN, dan Tunda Bayar dari evaluasi `R-24` serta dari penyebut pagu *eligible* UMK pada `R-26.1`.

---

## RINGKASAN PEMUTAKHIRAN & KALIBRASI VERSI 3.3 (INTEGRASI PENUH PERLEM LKPP NO. 3 TAHUN 2026)

Versi 3.3 menyelaraskan seluruh logika analitik engine dengan batang tubuh serta Lampiran I & II **Peraturan LKPP Nomor 3 Tahun 2026**:

1. **Pembersihan Total Rujukan Perlem LKPP 12/2021 (Pasal 11 Perlem LKPP 3/2026):**
   * Menggantikan seluruh rujukan teknis pemilihan penyedia ke Peraturan LKPP Nomor 3 Tahun 2026 dan menyelaraskan aturan pengawasan konstruksi (`R-32`) dengan Pasal 7 ayat (1) dan (5) Perlem LKPP 3/2026 & PP 16/2021.
2. **Sub-Rule `R-04b` (Fitur Transaksional SPSE & Reviu APIP Jasa Lainnya $> \text{Rp}50\text{ Juta}$):**
   * Menegakkan Pasal 3 ayat (3), (4) huruf b, dan ayat (5) Perlem LKPP 3/2026 bahwa Pengadaan Langsung Jasa Lainnya $> \text{Rp}50\text{ Juta}$ wajib melalui Fitur Transaksional SPSE (bentuk kontrak SPK).
   * Pengecualian Fitur Pencatatan untuk penyedia perorangan operasional publik wajib mengantongi Berita Acara Reviu APIP.
   * **Anti-False Positive Filter:** Evaluasi berbasis `nama_paket_objek` dan dilengkapi *Negative Lookahead Exclusion* untuk pemeliharaan kendaraan perorangan, daya/tenaga listrik, dan alat angkutan (`EXCLUSION_R04B`).
3. **Penajaman `R-07` (PL Konstruksi $\text{Rp}200\text{ Juta} - \text{Rp}400\text{ Juta}$):**
   * Wajib Usaha Kecil (`status_umk = Ya`) **DAN** berteknologi sederhana / berisiko kecil (tidak memuat kata kunci `DICT_KONSTRUKSI_KOMPLEKS`).
4. **Pemisahan Kewenangan Pejabat Pengadaan vs Pokja Pemilihan pada Penunjukan Langsung (`R-08`):**
   * Batas kewenangan Pejabat Pengadaan: $\le \text{Rp}200\text{ Juta}$ (Barang/Konstruksi/Jasa Lainnya) dan $\le \text{Rp}100\text{ Juta}$ (Konsultansi). Penunjukan Langsung di atas nilai tersebut wajib dilaksanakan oleh **Pokja Pemilihan UKPBJ** (Lampiran I & II Angka 1.4).
5. **Penegakan 5 Syarat Kumulatif *Repeat Order* (`R-08b`):**
   * Sesuai Lampiran I Angka 5.2.1 Perlem LKPP 3/2026: (1) paket awal hasil Tender/Seleksi; (2) HPS RO $\le$ kontrak awal; (3) maksimal 2x dalam 3 tahun; (4) kinerja penyedia Sangat Baik; (5) Ketua Tim (*Team Leader*) konsultan tetap sama.
6. **Segmentasi Usaha Jasa Konsultansi Konstruksi $\le \text{Rp}1\text{ Miliar}$ (`R-24` & `R-26.1`):**
   * Sesuai Pasal 4 ayat (2) Perlem LKPP 3/2026, batas Usaha Kecil untuk Jasa Konsultansi Konstruksi adalah $\le \text{Rp}1.000.000.000$ (bukan Rp15 Miliar). Paket $> \text{Rp}1\text{ Miliar}$ wajib Usaha Menengah/Besar dan otomatis dikecualikan dari penyebut *eligible* UMK pada kalkulasi indikator makro.
7. **Injeksi Subkualifikasi K1, K2, K3 & Afirmasi Domisili Lokal Konstruksi:**
   * Pekerjaan Konstruksi: K1 ($\le \text{Rp}2\text{M}$ prioritas Kab/Kota), K2 ($> \text{Rp}2\text{M}-\text{Rp}7,5\text{M}$ prioritas Provinsi), K3 ($> \text{Rp}7,5\text{M}-\text{Rp}15\text{M}$), Menengah ($> \text{Rp}15\text{M}-\text{Rp}50\text{M}$, wajib subkontrak jika $> \text{Rp}25\text{M}$), dan Besar (Pasal 4 ayat (1) & Lampiran II Angka 1.1).
8. **Klausul Mandatori Evaluasi Pemilik Manfaat (*Beneficial Ownership*) per 1 November 2026:**
   * Sesuai Pasal 8 Perlem LKPP 3/2026, untuk paket yang diumumkan mulai 1 November 2026, evaluasi data Pemilik Manfaat di SPSE bersifat mandatori; peserta tanpa data BO atau memiliki kesamaan BO wajib digugurkan.

---

## RINGKASAN PEMUTAKHIRAN & KALIBRASI VERSI 3.1 (HASIL UJI POPULASI DATA RIIL)

Versi 3.1 menyempurnakan arsitektur Versi 3.0 berdasarkan temuan pengujian populasi penuh (*full-population stress test*) pada $7.803$ paket RUP lintas $49$ OPD:

1. **Resolusi Paradoks Kuota $40\%$ UMK (`R-26.1`) vs Kewajiban Usaha Non-Kecil $> \text{Rp}15\text{ Miliar}$ (`R-24`):**
   * Menyamakan semesta (*universe*) pembilang dan penyebut `R-26.1` pada paket `Penyedia` di luar metode `Dikecualikan`, sehingga rasio UMK selalu terkunci akurat pada rentang $0\% - 100\%$.
   * Menambahkan metrik pendamping **`persen_umk_eligible_le15m`** (rasio UMK khusus pada paket Penyedia bernilai $\le \text{Rp}15\text{ Miliar}$). OPD yang memiliki porsi paket konstruksi/strategis $> \text{Rp}15\text{ Miliar}$ sangat besar (yang menurut Pasal 65 ayat (4) Perpres 46/2025 memang wajib Usaha Non-Kecil) tidak dijatuhi penalti RCI apabila alokasi UMK pada paket $\le \text{Rp}15\text{ Miliar}$ telah mencapai $\ge 80\%$.
2. **Ekstraksi Objek Utama Paket (`nama_paket_objek`) untuk Mencegah Kontaminasi Sub-Kegiatan (`R-13`, `R-16`, `R-17`):**
   * Memisahkan objek belanja utama dari keterangan nama Sub-Kegiatan di dalam tanda kurung `(...)` atau setelah klausa `"Sub Kegiatan ..."`. Hal ini mencegah paket pembelian Makan Minum Rapat atau ATK yang berada di bawah Sub-Kegiatan `"Sosialisasi..."`, `"(Perencanaan Teknis...)"`, atau `"(Rehabilitasi Jembatan...)"` salah terdeteksi sebagai Swakelola, Jasa Konsultansi, atau Pekerjaan Konstruksi.
3. **Perluasan *Negative Lookahead* pada Kamus Pekerjaan Konstruksi (`DICT_05_MURNI_KONSTRUKSI`):**
   * Mengecualikan frasa publikasi media/iklan pembangunan (`"publikasi"`, `"iklan"`, `"tv"`, `"radio"`), pengadaan alat kesehatan (`"rehabilitasi medis"`, `"rehabilitasi sosial"`), belanja sewa (`"sewa "`), serta pembelian material bangunan murni (`"bahan bangunan"`, `"bahan baku"`, `"material"`) dari deteksi Pekerjaan Konstruksi.
4. **Kalibrasi Aturan Ambang $\text{Rp}15\text{ Miliar}$ (`R-24`) untuk Metode `Dikecualikan`, `E-Purchasing` & Dana `BOSP`:**
   * Mengecualikan paket tarif resmi pemerintah/utilitas (`Dikecualikan`) dari kewajiban pencadangan UMK pada bagian (1), serta membatasi larangan UMK untuk paket $> \text{Rp}15\text{ Miliar}$ pada bagian (2) khusus untuk metode kontrak tunggal (`Tender`, `Tender Cepat`, `Seleksi`), sehingga pagu agregat `E-Purchasing` Obat Katalog dan dana `BOSP-BOS Reguler` tidak menimbulkan *false positive*.
5. **Pemisahan Rule Duplikasi Teknis (`R-21a`) vs Indikasi *Double Counting* Lintas Kode RUP (`R-21b`):**
   * Memisahkan validasi tabrakan `kode_rup` / kerusakan *scientific notation* (`R-21a`) dengan deteksi paket identik beda `kode_rup` bernilai signifikan $\ge \text{Rp}50.000.000$ (`R-21b`).
6. **Dukungan Nilai Enum `"Terintegrasi"` pada `jenis_pengadaan` (`R-19` & `R-33`):**
   * Menambahkan `"Terintegrasi"` ke dalam skema `jenis_pengadaan` untuk menangkap kesalahan input paket barang/jasa sederhana yang dilabeli sebagai pekerjaan terintegrasi.
7. **Ekstraksi Kode Satker SIPD Otomatis (`kode_satker_sipd`):**
   * Memisahkan nama OPD bersih (`nama_opd_clean`) dan kode organisasi $22$ digit SIPD (`kode_satker_sipd`) dari kolom `nama_satuan_kerja` untuk integrasi langsung dengan tabel DPA/SIPD.

---

## BAGIAN 1: MASTER PLAN (ARSITEKTUR & CAKUPAN FITUR UNIVERSAL)

### 1.1 Tujuan & Ruang Lingkup Sistem
Dokumen ini merupakan spesifikasi teknis dan fungsional (*System Blueprint*) untuk membangun **Mesin Audit Kepatuhan RUP Otomatis (Universal SIRUP Compliance Engine)**. Sistem dirancang secara agnostik terhadap instansi sehingga mampu memproses unggahan data CSV/Excel/JSON API RUP dari **OPD/Satuan Kerja mana pun** (Dinas, Badan, Biro, RSUD/Puskesmas BLUD, Sekretariat, Kecamatan, hingga tingkat Pemerintah Provinsi/Kabupaten/Kota atau Kementerian/Lembaga secara agregat).

Tujuan utama sistem ini adalah:
1. **Otomatisasi Audit Kepatuhan ($100\%$ Populasi):** Memeriksa puluhan ribu baris paket RUP lintas OPD secara instan tanpa melalui *sampling* manual.
2. **Penegakan Regulasi Terbaru (Perpres 46/2025 & UU 20/2023):** Memastikan perubahan ambang batas (*threshold*) baru, kewajiban E-Purchasing Katalog V6, pembatasan ketat Penunjukan Langsung dan Tender Cepat, larangan *outsourcing* berkedok Swakelola, serta afirmasi PDN dan UMK-Koperasi diterapkan secara konsisten.
3. **Pencegahan Fraud, *Splitting* & *Threshold Hugging*:** Mendeteksi pemecahan paket (*splitting*) baik melalui kemiripan teks maupun pola pagu mepet batas (*cliff-edge/threshold hugging*), salah klasifikasi jenis pengadaan untuk mengakali batas pagu, penggunaan metode *Kontes/Penunjukan Langsung/Tender Cepat* yang tidak tepat, penamaan paket kabur (*opaque naming*), hingga pekerjaan konstruksi tanpa pengawasan.
4. **Sinkronisasi Indikator MCP KPK & ITKP LKPP:** Mengukur rasio keterisian RUP terhadap pagu pengadaan DPA/SIPD serta kepatuhan batas waktu pengumuman RUP ($\le 31\text{ Maret}$).
5. **Standarisasi Tindak Lanjut Pengawasan (APIP & UKPBJ):** Menghasilkan kertas kerja koreksi RUP yang seragam, terukur, dilengkapi rekomendasi perubahan per paket, serta fitur pelacakan perubahan (*Diff-Tracking*) antar-versi unggahan RUP.

### 1.2 Arsitektur 6 Modul Utama
Sistem dibangun menggunakan arsitektur pemrosesan berlapis (*layered pipeline architecture*) yang terdiri atas:

1. **Modul 1: Universal CSV/API Ingestion & Dual-Mode Schema Mapper**
   * Menerima unggahan *Single CSV* ($1$ OPD), *Multi-CSV / Full-Region CSV* (seluruh OPD dalam satu Pemerintah Daerah), maupun penarikan langsung dari *Open API INAPROC/SIRUP*.
   * Mendeteksi otomatis karakter pemisah (*delimiter* `,` atau `;`), *encoding* (`UTF-8`), mengekstrak `kode_satker_sipd`, dan mendukung **Dual-Mode Schema**: $12$ atribut inti wajib SIRUP ditambah $8$ atribut ekstensi opsional.
2. **Modul 2: Data Sanitizer, Multi-Value Normalizer & Object Extractor**
   * Menormalisasi format angka, teks, dan memecah kolom bernilai jamak (*multi-value semicolon-separated*) pada `jenis_pengadaan`, `metode_pengadaan`, dan `sumber_dana`.
   * Mengekstrak **Objek Utama Pengadaan (`nama_paket_objek`)**, **Inti Komoditas (`nama_paket_core`)**, dan **Entitas Lokasi (`lokasi_extracted`)** serta mendeteksi kerusakan data teknis seperti pagu $\le \text{Rp}0$ atau pemotongan digit `kode_rup` (`"00"`).
3. **Modul 3: NLP, Synonym Mapper & 12-Class Keyword Taxonomy Engine**
   * Menganalisis teks `nama_paket_objek` dan `nama_paket_clean` menggunakan kamus normalisasi sinonim dan **$12$ kamus taksonomi komoditas pemerintah daerah** yang dilengkapi *Negative Lookahead* terkalibrasi.
4. **Modul 4: Multi-Tier Regulatory Rule Engine (34 Master Rules)**
   * **Tier 1 (Row-Level Validation — Rules `R-01` s.d. `R-19`, `R-27` s.d. `R-31`, `R-33`, `R-34`):** Menguji validitas setiap baris paket secara individual beserta konteks sumber dananya (`APBD`, `APBDP`, `BLUD`, `DAK`, `BOSP`).
   * **Tier 2 (Cluster/Group-Level Validation — Rules `R-20`, `R-21a`, `R-21b`, `R-22`, `R-23`, `R-32`):** Mengelompokkan paket sejenis menggunakan *Graph-Based Fuzzy Clustering* per siklus anggaran (`MURNI` vs `PERUBAHAN`) dan lokasi untuk mendeteksi *splitting*, duplikasi, fragmentasi, inkonsistensi, serta paket konstruksi yatim (*orphan construction*).
   * **Tier 3 (Entity/OPD-Level Macro Validation — Rules `R-24` s.d. `R-26`):** Menguji kepatuhan agregat per OPD dan per Pemerintah Daerah (kuota minimal $40\%$ UMK-Koperasi beserta rasio *eligible* $\le \text{Rp}15\text{ Miliar}$, rasio PDN, rasio E-Purchasing, rasio Penunjukan Langsung, dan rasio keterisian RUP terhadap DPA).
5. **Modul 5: Risk Scoring & RUP Compliance Index (RCI v3.1)**
   * Menghitung skor kepatuhan **RUP Compliance Index (RCI)** skala $0-100$ dengan proteksi pembagian nol (*zero-division guard*), deduplikasi eksposur pagu per baris, dan penalti indikator makro terkalibrasi.
6. **Modul 6: Actionable Reporting & Stateful Diff-Tracking Engine**
   * Menghasilkan dashboard eksekutif, tabel rincian pelanggaran beserta instruksi koreksi SIRUP, matriks konsolidasi paket, dokumen Berita Acara PDF, Excel $5$-Sheet, serta fitur **Diff-Check** otomatis antar-unggah data.

---

## BAGIAN 2: MASTER PROCESS (ALUR PEMROSESAN END-TO-END)

### 2.1 Diagram Alur Pemrosesan Sistem

    [INPUT: CSV RUP (12 Core + 8 Extended Cols) + Optional DPA Reference + Previous Batch]
                                           |
                                           v
    +--------------------------------------------------------------------------+
    | STAGE 1: INGESTION & DUAL-MODE SCHEMA VALIDATION                         |
    | 1. Validasi 12 kolom wajib SIRUP & deteksi otomatis 8 kolom ekstensi     |
    | 2. Pisahkan `nama_opd_clean` dan `kode_satker_sipd` dari Satuan Kerja    |
    +--------------------------------------------------------------------------+
                                           |
                                           v
    +--------------------------------------------------------------------------+
    | STAGE 2: DATA SANITIZATION, MULTI-VALUE PARSING & NLP EXTRACTION         |
    | 1. Paksa `kode_rup` sebagai String & deteksi anomali akhiran "00"        |
    | 2. Parse multi-value array: `jenis_arr`, `metode_arr`, `sumber_dana_arr` |
    | 3. Ekstrak siklus anggaran (`MURNI` vs `PERUBAHAN`) & flag `BLUD`/`DAK`  |
    | 4. Bentuk `nama_paket_clean`, `nama_paket_objek`, `nama_paket_core`      |
    | 5. Klasifikasi substansi paket melalui 12 Kamus Taksonomi (DICT_01..12)  |
    +--------------------------------------------------------------------------+
                                           |
                                           v
    +--------------------------------------------------------------------------+
    | STAGE 3: MULTI-TIER RULE ENGINE EXECUTION (34 MASTER RULES)              |
    | +-> PASS 1 (Row Rules): Cek Integritas, Pagu, Metode, Cara, Jenis,       |
    | |   Threshold Hugging (95%-100%), Over-Method, BLUD/DAK, Jadwal, Opaque  |
    | +-> PASS 2 (Cluster Rules): Connected-Component Clustering per Satker    |
    | |   & Siklus Anggaran -> Cek Splitting, Duplikat, Konsolidasi, Orphan PK |
    | +-> PASS 3 (Macro Rules): Agregasi per Satker -> Cek 40% UMK (Total      |
    |     Penyedia & Eligible <=15M), PDN, Rasio Non-Kompetitif, & Cakupan DPA |
    +--------------------------------------------------------------------------+
                                           |
                                           v
    +--------------------------------------------------------------------------+
    | STAGE 4: RCI v3.1 SCORING, DIFF-TRACKING & RECOMMENDATION SYNTHESIS      |
    | 1. Deduplikasi Pagu Terdampak per baris (ambil severity tertinggi)       |
    | 2. Hitung Skor RCI (0-100) dengan Zero-Division Guard & Penalti Makro    |
    | 3. Bandingkan dengan Batch sebelumnya (FIXED, PERSISTENT, NEW, DELETED)  |
    +--------------------------------------------------------------------------+
                                           |
                                           v
       [OUTPUT: JSON API v3.1, Dashboard Multi-OPD, Excel 5-Sheet, Berita Acara PDF]

### 2.2 Spesifikasi Kontrak Data Input (Dual-Mode CSV Schema)

#### A. 12 Kolom Inti Wajib (*Core Mandatory Columns*)
Kompatibel penuh dengan format ekspor tabel standar SIRUP:

| No | Tag Kolom CSV | Tipe Data | Aturan Sanitasi, Parsing & Normalisasi Sistem (Stage 1 & Stage 2) |
| :--- | :--- | :--- | :--- |
| 1 | `nama_instansi` | `String` | *Trim* spasi, konversi ke *Uppercase*. Digunakan sebagai identitas entitas Pemerintah Daerah/K/L. |
| 2 | `nama_satuan_kerja` | `String` | *Trim* spasi, konversi ke *Uppercase*. Ekstrak pola `\s*-\s*([0-9\.]+)$` menjadi `kode_satker_sipd` (kode $22$ digit SIPD) dan sisanya sebagai `nama_opd_clean` (**Primary Partition Key**). |
| 3 | `tahun_anggaran` | `String/Int` | Ekstrak $4$ digit tahun utama. Jika terdapat multi-tahun (misal `2026;2027`), tandai bendera `is_multiyears = true`. |
| 4 | `cara_pengadaan` | `Enum` | Nilai standar: `Penyedia` atau `Swakelola`. |
| 5 | `metode_pengadaan` | `String` | Simpan teks asli dan pecah dengan delimiter `;` menjadi *array* `metode_arr`. Nilai standar Penyedia: `E-Purchasing`, `Pengadaan Langsung`, `Penunjukan Langsung`, `Tender Cepat`, `Tender`, `Seleksi`, `Kontes`, `Dikecualikan`. Nilai Swakelola: `-`. |
| 6 | `jenis_pengadaan` | `String` | Simpan teks asli dan pecah dengan delimiter `;` menjadi *array* `jenis_arr`. Nilai standar Penyedia: `Barang`, `Pekerjaan Konstruksi`, `Jasa Konsultansi`, `Jasa Lainnya`, `Terintegrasi`. Jika `LEN(jenis_arr) > 1` atau bernilai `Terintegrasi`, set `is_integrated_package = true`. |
| 7 | `nama_paket` | `String` | Simpan teks asli dan bentuk $5$ atribut turunan:<br>1) `nama_paket_clean`: *lowercase*, hapus simbol non-alfanumerik, satukan spasi.<br>2) `nama_paket_objek`: hapus teks di dalam tanda kurung `\([^\)]*\)` dan potong klausa konteks setelah `"sub kegiatan ..."`, `"sub bagian ..."`, atau `"dalam rangka ..."` (jika klausa objek di depannya $\ge 10$ karakter).<br>3) `nama_paket_core`: hapus *stopwords* pengadaan, lokasi, tahap, triwulan, dan tahun untuk *clustering*.<br>4) `lokasi_extracted`: ekstrak entitas lokasi dari sufiks `di ...` atau `pada ...`.<br>5) `token_count`: jumlah kata bermakna di luar *stopwords*. |
| 8 | `kode_rup` | `String` | Wajib diproses sebagai `String` dan hapus `.0`. Jika panjang digit $\ge 8$ dan proporsi baris berakhiran `"00"` dalam satu OPD $> 50\%$, set `is_scientific_truncated = true`. |
| 9 | `sumber_dana` | `String` | Pecah dengan delimiter `;` menjadi `sumber_dana_arr` (`APBD`, `APBDP`, `BLUD`, `DAK`, `APBN`, `BOK`, `BOSP`). Bentuk bendera: `budget_cycle = "PERUBAHAN"` jika mengandung `APBDP`/`APBD-P`, selain itu `"MURNI"`; set `is_blud = true` jika mengandung `BLUD`; set `is_dak = true` jika mengandung `DAK`/`BOK`. |
| 10 | `produk_dalam_negeri` | `Enum` | Nilai standar: `Ya`, `Tidak`, atau `-`. |
| 11 | `status_umk` | `Enum` | Nilai standar: `Ya`, `Tidak`, atau `-`. |
| 12 | `total_nilai` | `Numeric` | Konversi ke `BigInt / Float64` (bersihkan titik/koma ribuan dan simbol `Rp`). Nilai kosong, `NaN`, atau $\le 0$ memicu `R-01`. |

#### B. 8 Kolom Ekstensi Opsional (*Extended SIRUP & SIPD Columns*)
Jika tersedia pada file CSV atau API, sistem secara otomatis mengaktifkan validasi lanjutan:

| No | Tag Kolom Opsional | Tipe Data | Fungsi Validasi Tambahan pada Mesin Audit |
| :--- | :--- | :--- | :--- |
| 13 | `tanggal_umumkan` | `Date` | Menguji kepatuhan batas waktu pengumuman RUP $\le 31\text{ Maret}$ untuk APBD Murni (`R-31`). |
| 14 | `jadwal_pemilihan_awal` | `Date` | Menguji konsistensi kronologis jadwal pemilihan vs jadwal pelaksanaan kontrak (`R-31`). |
| 15 | `jadwal_kontrak_awal` | `Date` | Mendeteksi jadwal kontrak yang mendahului jadwal pemilihan (`R-31`). |
| 16 | `jadwal_kontrak_akhir` | `Date` | Mendeteksi kontrak tahun tunggal yang melampaui $31\text{ Desember}$ tahun berjalan (`R-31`). |
| 17 | `tipe_swakelola` | `Enum` | Nilai: `Tipe 1`, `Tipe 2`, `Tipe 3`, `Tipe 4`. Mempertajam validasi Swakelola (`R-12`). |
| 18 | `lokasi_pekerjaan` | `String` | Mengisi `lokasi_extracted` secara eksplisit untuk membedakan pemaketan konstruksi beda lokasi (`R-20`). |
| 19 | `kode_rekening_mak` | `String` | Memvalidasi kesesuaian Kode Rekening SIPD (`5.1.02` Barang/Jasa vs `5.2` Belanja Modal) terhadap `jenis_pengadaan`. |
| 20 | `pagu_pengadaan_dpa` | `Numeric` | Pagu referensi per OPD dari DPA/SIPD untuk menghitung **Rasio Keterisian RUP** pada `R-26.4`. |

---

## BAGIAN 3: MASTER LOGIC (TAKSONOMI, KATALOG 34 RULES & ALGORITMA)

### 3.1 Parameter Ambang Batas Regulasi (*Configurable Regulatory Constants*)

| Kode Konstanta | Nilai Parameter | Referensi Regulasi Terbaru |
| :--- | :--- | :--- |
| `TH_PL_BARANG_JASALAIN` | $\text{Rp}200.000.000$ | Pasal 1 angka 40 Perpres 46/2025 |
| `TH_PL_KONSTRUKSI` | $\text{Rp}400.000.000$ | Pasal 1 angka 40 Perpres 46/2025 |
| `TH_PL_KONSTRUKSI_NON_UMK` | $\text{Rp}200.000.000$ | Pasal 1 angka 40 Perpres 46/2025 (Di atas $\text{Rp}200\text{ Juta}$ s.d. $\text{Rp}400\text{ Juta}$ wajib Usaha Kecil) |
| `TH_PL_KONSULTANSI` | $\text{Rp}100.000.000$ | Pasal 1 angka 41 Perpres 46/2025 |
| `TH_HUGGING_LOWER_RATIO` | $0,95$ ($95\%$) | Ambang Bawah Deteksi Pagu Mepet Batas PL (*Threshold Hugging*) |
| `TH_MAX_USAHA_KECIL` | $\text{Rp}15.000.000.000$ | Pasal 65 ayat (4) Perpres 46/2025 |
| `TH_MIN_UMK_RATIO_OPD` | $40,0\%$ | Pasal 65 ayat (2) Perpres 46/2025 & Inpres 2/2022 |
| `TH_MIN_UMK_ELIGIBLE_LE15M` | $80,0\%$ | Parameter Kalibrasi Kepatuhan UMK untuk OPD Berporsi Paket Strategis $> \text{Rp}15\text{ Miliar}$ Tinggi |
| `TH_MIN_EPURCHASING_RATIO` | $30,0\%$ | Mandat Percepatan Katalog Elektronik V6 (INAPROC) |
| `TH_MAX_PENUNJUKAN_LANGSUNG_RATIO` | $5,0\%$ | Indikator Pengendalian Risiko Non-Kompetitif APIP |
| `TH_MIN_SIRUP_COVERAGE_RATIO` | $95,0\%$ | Indikator Kepatuhan Perencanaan MCP KPK & ITKP LKPP |
| `DATE_BATAS_UMUMKAN_APBD_MURNI` | `YYYY-03-31` | Batas Waktu Pengumuman RUP APBD Murni ($31\text{ Maret}$ Tahun Berjalan) |
| `TH_DOUBLE_COUNTING_MIN_VALUE` | $\text{Rp}50.000.000$ | Ambang Nilai Minimum Deteksi *Double Counting* Lintas Kode RUP (`R-21b`) |
| `TH_PAKET_MIKRO_TIDAK_WAJAR` | $\text{Rp}500.000$ | Prinsip Efisiensi Pasal 6 & Konsolidasi Pasal 21 |
| `TH_PAKET_ANOMALI_EKSTREM` | $\text{Rp}10.000$ | Validasi Kewajaran Harga Satuan Minimum |
| `TH_FREKUENSI_FRAGMENTASI` | $3\text{ Paket}$ | Pasal 21 Perpres 46/2025 (Kewajiban Konsolidasi) |
| `TH_JACCARD_SIMILARITY` | $0,78$ ($78\%$) | Ambang Batas Kemiripan Teks untuk *Graph-Based Fuzzy Splitting* |

---

### 3.2 Master Taksonomi Kata Kunci (*12 Kamus Klasifikasi Komoditas Terkalibrasi*)
Untuk mendeteksi ketidaksesuaian antara substansi paket dengan **Cara Pengadaan**, **Jenis Pengadaan**, dan **Metode Pemilihan**, sistem mengevaluasi `nama_paket_objek` (atau `nama_paket_clean` untuk kamus konteks umum) menggunakan $12$ kamus pencocokan pola:

1. **`DICT_01_DIKECUALIKAN` (Tarif Resmi Pemerintah & Kewajiban Statutori):**
   * *Pola Kata:* `"tagihan listrik"`, `"tagihan air"`, `"tagihan telepon"`, `"langganan daya"`, `"kawat/faksimili"`, `"internet"`, `"pembayaran pajak"`, `"pajak kendaraan"`, `"stnk"`, `"bpkb"`, `"bea, dan perizinan"`, `"bea dan perizinan"`, `"retribusi"`, `"benda pos"`, `"materai"`, `"penerimaan negara bukan pajak"`, `"pnbp"`, `"tera ulang"`, `"publikasi media massa pemerintah"`.
2. **`DICT_02_OUTSOURCING` (Jasa Tenaga Alih Daya / Pendukung Non-ASN):**
   * *Pola Kata:* `"tenaga pelayanan umum"`, `"tenaga kebersihan"`, `"cleaning service"`, `"tenaga keamanan"`, `"satpam"`, `"penjaga malam"`, `"tenaga operator"`, `"tenaga sopir"`, `"pengemudi"`, `"pramubakti"`, `"caraka"`, `"tenaga administrasi"`, `"tenaga teknis"`, `"juru masak"`, `"alih daya"`, `"outsourcing"`, `"jasa tenaga kerja"`, `"petugas taman"`.
3. **`DICT_03_SWAKELOLA_MURNI` (Belanja Pegawai, Honorarium & Administrasi Internal — dievaluasi pada `nama_paket_objek`):**
   * *Pola Kata:* `"perjalanan dinas"`, `"uang harian"`, `"honorarium"`, `"uang lembur"`, `"tunjangan"`, `"kursus singkat"`, `"pelatihan"`, `"bimbingan teknis"`, `"bimtek"`, `"sosialisasi"`, `"kontribusi"`, `"iuran asosiasi"`, `"uang saku"`, `"jasa pelayanan kesehatan bagi asn"`, `"gaji dan tunjangan"`, `"insentif pemungutan"`.
   * *Pengecualian Wajib (Negative Lookahead):* TIDAK mengandung kata `"makan"`, `"minum"`, `"cetak"`, `"alat tulis"`, `"atk"`, `"spanduk"`, `"umbul"`, `"event organizer"`, `"hotel"`, `"akomodasi"`, `"sewa"`, `"pihak ketiga"`.
4. **`DICT_04_KATALOG_STANDAR` (Komoditas Wajib E-Purchasing Katalog V6 — dievaluasi pada `nama_paket_objek`):**
   * *Pola Kata:* `"alat tulis kantor"`, `"atk"`, `"bahan cetak"`, `"kertas dan cover"`, `"alat listrik"`, `"makan dan minuman"`, `"makanan dan minuman"`, `"makan minuman"`, `"jamuan"`, `"katering"`, `"bahan bakar"`, `"pelumas"`, `"bbm"`, `"sewa kendaraan"`, `"pemeliharaan kendaraan"`, `"pemeliharaan alat angkutan"`, `"pemeliharaan komputer"`, `"pemeliharaan printer"`, `"pemeliharaan ac"`, `"alat pendingin"`, `"laptop"`, `"personal computer"`, `"printer"`, `"obat-obatan"`, `"bahan medis habis pakai"`, `"bmhp"`, `"daya tahan tubuh"`, `"vitamin"`, `"aspal"`, `"hotmix"`, `"beton ready mix"`, `"pakaian olahraga"`, `"pakaian kerja"`, `"pakaian dinas"`, `"seragam"`, `"meubelair"`, `"mebel"`.
5. **`DICT_05_MURNI_KONSTRUKSI` (Pekerjaan Fisik Konstruksi — dievaluasi pada `nama_paket_objek`):**
   * *Pola Kata:* `"pembangunan "`, `"rehabilitasi "`, `"renovasi "`, `"rekonstruksi "`, `"peningkatan jalan"`, `"pemeliharaan rutin jalan"`, `"pemeliharaan berkala jalan"`, `"lapis penetrasi"`, `"hotmix"`, `"irigasi"`, `"jaringan irigasi"`, `"drainase"`, `"talud"`, `"bronjong"`, `"sumur bor"`, `"instalasi pengolahan air"`, `"gedung dan bangunan"`, `"pekerjaan konstruksi"`, `"jalan dan jembatan"`, `"pengerukan"`, `"normalisasi sungai"`.
   * *Pengecualian Wajib (Negative Lookahead Terkalibrasi v3.1):* TIDAK mengandung kata `"konsultansi"`, `"konsultan"`, `"perencanaan"`, `"pengawasan"`, `"manajemen konstruksi"`, `"studi"`, `"ded"`, `"suku cadang"`, `"alat angkutan"`, `"kendaraan"`, `"sistem informasi"`, `"aplikasi"`, `"publikasi"`, `"iklan"`, `"media"`, `"tv"`, `"radio"`, `"advetorial"`, `"rehabilitasi medis"`, `"rehabilitasi sosial"`, `"alat kesehatan"`, `"sewa "`, `"bahan bangunan"`, `"bahan material"`, `"bahan baku"`, `"pertukangan"`, `"alat tulis"`, `"bahan cetak"`, `"makan"`, `"penyusunan"`, `"dokumen"`.
6. **`DICT_06_MURNI_KONSULTANSI` (Jasa Konsultansi Konstruksi & Non-Konstruksi — dievaluasi pada `nama_paket_objek`):**
   * *Pola Kata:* `"jasa konsultansi"`, `"jasa konsultasi"`, `"tenaga ahli"`, `"perencanaan konstruksi"`, `"perencanaan teknis"`, `"pengawasan konstruksi"`, `"pengawasan teknis"`, `"manajemen konstruksi"`, `"studi kelayakan"`, `"feasibility study"`, `"ded "`, `"detail engineering"`, `"penyusunan dokumen"`, `"masterplan"`, `"kajian"`, `"naskah akademik"`, `"audit"`, `"survailance"`, `"surveilans"`, `"appraisal"`, `"penilaian aset"`, `"survey kepuasan"`, `"amdal"`, `"ukl-upl"`, `"andalalin"`.
   * *Pengecualian Wajib:* TIDAK mengandung kata `"alat tulis"`, `"bahan cetak"`, `"makan"`, `"minum"`, `"honorarium"`.
7. **`DICT_07_MURNI_JASA_LAINNYA` (Jasa Non-Konsultansi / Operasional — dievaluasi pada `nama_paket_objek`):**
   * *Pola Kata:* `"belanja sewa"`, `"sewa "`, `"pemeliharaan alat"`, `"pemeliharaan kendaraan"`, `"pemeliharaan peralatan"`, `"perawatan kendaraan"`, `"jasa kebersihan"`, `"jasa keamanan"`, `"jasa kalibrasi"`, `"jasa pengolahan"`, `"jasa pencucian"`, `"jasa penjahitan"`, `"event organizer"`, `"penyelenggaraan acara"`, `"publikasi"`, `"iklan"`, `"pengiriman"`, `"ekspedisi"`, `"asuransi"`, `"medical check up"`, `"langganan jurnal"`, `"jasa pembasmian"`, `"pest control"`.
   * *Pengecualian Wajib:* TIDAK mengandung kata `"belanja modal"`, `"pengadaan alat"`.
8. **`DICT_08_MURNI_BARANG` (Aset Modal Peralatan/Mesin & Barang Habis Pakai — dievaluasi pada `nama_paket_objek`):**
   * *Pola Kata:* `"belanja modal alat"`, `"belanja modal komputer"`, `"belanja modal kendaraan"`, `"belanja modal mebel"`, `"belanja peralatan dan mesin"`, `"suku cadang"`, `"bahan kimia"`, `"bahan bangunan"`, `"bahan bibit"`, `"bahan pakan"`, `"obat-obatan"`, `"alat tulis kantor"`, `"kertas dan cover"`, `"alat listrik"`, `"souvenir"`, `"cinderamata"`, `"hadiah"`, `"pupuk"`, `"alat peraga"`.
   * *Pengecualian Wajib:* TIDAK mengandung kata `"sewa"`, `"pemeliharaan"`, `"perbaikan"`, `"kalibrasi"`, `"jasa"`.
9. **`DICT_09_HIBAH_BANSOS` (Belanja Hibah / Bantuan Sosial Barang/Uang):**
   * *Pola Kata:* `"belanja hibah"`, `"hibah barang"`, `"hibah uang"`, `"bantuan sosial"`, `"bansos"`, `"bantuan keuangan"`, `"diserahkan kepada masyarakat"`, `"diserahkan kepada pihak ketiga"`.
10. **`DICT_10_TEKNOLOGI_TINGGI_IMPOR` (Komoditas Spesifik / Dominan Impor):**
    * *Pola Kata:* `"bahan kimia"`, `"reagen"`, `"alat laboratorium"`, `"instrumen analisis"`, `"alat kesehatan"`, `"alat kedokteran"`, `"server"`, `"lisensi"`, `"spektrofotometer"`, `"kromatografi"`, `"radiologi"`, `"hemodialisa"`, `"mri"`, `"ct scan"`, `"cathlab"`.
11. **`DICT_11_PENUNJUKAN_LANGSUNG_SAH` (Komoditas Keadaan Tertentu / Hotel / RO sesuai Perpres 46/2025):**
    * *Pola Kata:* `"repeat order"`, `"sewa hotel"`, `"penginapan"`, `"ruang rapat hotel"`, `"akomodasi rapat"`, `"darurat bencana"`, `"penanganan darurat"`, `"hak paten"`, `"penyedia tunggal"`, `"penugasan presiden"`, `"penugasan pemerintah"`, `"rahasia negara"`, `"saksi ahli"`, `"bantuan hukum"`.
12. **`DICT_12_OPAQUE_GENERIC` (Penamaan Paket Kabur / Terlalu Generik):**
    * *Pola Regex Eksak:* `^(belanja|pengadaan|penyediaan)\s+(barang|jasa|barang dan jasa|modal|operasional|rutin|kebutuhan kantor|peralatan|perlengkapan)$`.

---

### 3.3 Katalog Lengkap 34 Master Rules & Logic (Universal v3.1)

> **Catatan Konvensi Operator Multi-Value:**  
> Fungsi `HAS_JENIS(X)` mengevaluasi apakah elemen `X` terdapat di dalam *array* `jenis_arr`. Fungsi `ONLY_JENIS(X)` mengevaluasi apakah `jenis_arr` hanya memiliki $1$ elemen yaitu `X`. Fungsi `HAS_SUMBER(X)` mengevaluasi keberadaan `X` di dalam `sumber_dana_arr`.

#### KELOMPOK A: Validasi Kelengkapan, Integritas Data Dasar & Transparansi Penamaan

| Kode Rule | Tingkat Risiko | Logika Kondisi (Pseudocode) | Dasar Regulasi | Deskripsi Bentuk Pelanggaran | Rekomendasi Tindak Lanjut Sistem |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **`R-01`** | **KRITIS** | `total_nilai IS NULL OR total_nilai <= 0` | Pasal 11 Perlem LKPP 11/2021 | **Pagu Anggaran Nol atau Negatif:** Paket RUP diumumkan tanpa nilai pagu yang valid ($\le \text{Rp}0$). | Lengkapi nilai pagu sesuai DPA/RKA atau hapus paket jika batal dilaksanakan. |
| **`R-02`** | **KRITIS** | `cara_pengadaan == "Penyedia" AND (metode_pengadaan IN ("-", "", NULL) OR jenis_pengadaan IN ("-", "", NULL))` | Pasal 22 Perpres 16/2018 jo. Perpres 46/2025 | **Atribut Paket Penyedia Tidak Lengkap:** Paket `Penyedia` tidak mencantumkan `metode_pengadaan` atau `jenis_pengadaan`. | Lengkapi metode pemilihan penyedia dan jenis pengadaan pada SIRUP sebelum paket dieksekusi. |
| **`R-03`** | **SEDANG** | `total_nilai > 0 AND total_nilai < 10000` | Prinsip Akuntabilitas Data SIRUP | **Anomali Nilai Pagu Ekstrem Rendah ($< \text{Rp}10.000$):** Mengindikasikan kesalahan input satuan (misal ribuan/jutaan) atau salah ketik. | Verifikasi kembali kesesuaian digit nilai pagu terhadap DPA. |
| **`R-34`** | **SEDANG** | `LEN(nama_paket_objek) < 6 OR token_count == 0 OR MATCH(nama_paket_clean, DICT_12_OPAQUE_GENERIC) OR IN_ARRAY(nama_paket_objek, LIST_OPAQUE_GENERIC)` | Pasal 6 (Prinsip Transparansi) Perpres PBJ & Perlem LKPP 11/2021 | **Penamaan Paket Kabur / Terlalu Generik (*Opaque Naming* — Kalibrasi v3.4):** Nama paket tidak menyebutkan objek komoditas secara spesifik. Dikecualikan untuk komoditas barang/aset tunggal spesifik (`"Belanja Excavator"`, `"Pengadaan Laptop"`, `"Pengadaan Mebel"`). | Perjelas `nama_paket` sesuai rincian objek belanja/komoditas pada DPA dan lengkapi kolom spesifikasi pekerjaan. |

---

#### KELOMPOK B: Pelanggaran Batas Nilai (*Threshold*) & Metode Pemilihan Penyedia

| Kode Rule | Tingkat Risiko | Logika Kondisi (Pseudocode) | Dasar Regulasi | Deskripsi Bentuk Pelanggaran | Rekomendasi Tindak Lanjut Sistem |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **`R-04`** | **KRITIS** | `cara_pengadaan == "Penyedia" AND NOT is_blud AND "Pengadaan Langsung" IN metode_arr AND (HAS_JENIS("Barang") OR HAS_JENIS("Jasa Lainnya")) AND NOT HAS_JENIS("Pekerjaan Konstruksi") AND total_nilai > 200000000` | Pasal 1 angka 40 & Pasal 38 ayat (3) Perpres 46/2025 jo. Perlem LKPP 3/2026 | **Pelanggaran Batas Pagu Pengadaan Langsung Barang/Jasa Lainnya ($> \text{Rp}200\text{ Juta}$):** Nilai paket melampaui batas tertinggi Pengadaan Langsung ($\text{Rp}200.000.000$). | Ubah metode pemilihan menjadi **E-Purchasing** (jika tersedia di Katalog V6) atau **Tender / Tender Cepat**. |
| **`R-04b`** | **RENDAH** | `cara_pengadaan == "Penyedia" AND "Pengadaan Langsung" IN metode_arr AND HAS_JENIS("Jasa Lainnya") AND total_nilai > 50000000 AND (MATCH(nama_paket_objek, DICT_02_OUTSOURCING) OR CONTAINS(nama_paket_objek, "perorangan") OR CONTAINS(nama_paket_objek, "tenaga")) AND NOT CONTAINS_ANY(nama_paket_objek, EXCLUSION_R04B)` | Pasal 3 ayat (3), (4) huruf b, dan ayat (5) Perlem LKPP 3/2026 | **Peringatan Fitur Transaksional SPSE & Reviu APIP Jasa Lainnya $> \text{Rp}50\text{ Juta}$:** Pengadaan Langsung Jasa Lainnya $> \text{Rp}50\text{ Juta}$ wajib dilaksanakan melalui Fitur Transaksional SPSE (bentuk kontrak SPK). Pengecualian penggunaan Fitur Pencatatan untuk penyedia Jasa Lainnya Perorangan pendukung layanan publik WAJIB mendapatkan Reviu APIP (Inspektorat Daerah) sebelum pemilihan Penyedia. Dikecualikan (*anti-false positive*) untuk pemeliharaan kendaraan dinas perorangan, daya/tenaga listrik, dan alat angkutan (`EXCLUSION_R04B`). | Gunakan Fitur Transaksional SPSE dengan bentuk kontrak SPK, atau pastikan telah memperoleh Berita Acara Reviu APIP jika menggunakan skema Jasa Lainnya Perorangan via Fitur Pencatatan. |
| **`R-05`** | **KRITIS** | `cara_pengadaan == "Penyedia" AND NOT is_blud AND "Pengadaan Langsung" IN metode_arr AND HAS_JENIS("Pekerjaan Konstruksi") AND NOT HAS_JENIS("Jasa Konsultansi") AND ((tahun_anggaran <= 2024 AND total_nilai > 200000000) OR (tahun_anggaran >= 2025 AND total_nilai > 400000000))` | Pasal 1 angka 40 Perpres 12/2021 (TA $\le 2024$) / Perpres 46/2025 jo. Perlem LKPP 3/2026 (TA $\ge 2025$) | **Pelanggaran Batas Pagu Pengadaan Langsung Konstruksi (*Time-Aware*):** Nilai paket melampaui batas tertinggi PL Konstruksi ($\text{Rp}200.000.000$ pada rezim Perpres 12/2021 / TA $\le 2024$, atau $\text{Rp}400.000.000$ pada rezim Perpres 46/2025 / TA $\ge 2025$). | Ubah metode pemilihan menjadi **Tender** atau **E-Purchasing Katalog Konstruksi**. |
| **`R-06`** | **KRITIS** | `cara_pengadaan == "Penyedia" AND NOT is_blud AND "Pengadaan Langsung" IN metode_arr AND HAS_JENIS("Jasa Konsultansi") AND total_nilai > 100000000` | Pasal 1 angka 41 & Pasal 41 ayat (3) Perpres 46/2025 jo. Perlem LKPP 3/2026 | **Pelanggaran Batas Pagu Pengadaan Langsung Jasa Konsultansi ($> \text{Rp}100\text{ Juta}$):** Nilai paket melampaui batas tertinggi Pengadaan Langsung Jasa Konsultansi ($\text{Rp}100.000.000$). | Ubah metode pemilihan menjadi **Seleksi** atau **E-Purchasing Katalog Jasa Konsultansi**. |
| **`R-07`** | **TINGGI** | `cara_pengadaan == "Penyedia" AND NOT is_blud AND "Pengadaan Langsung" IN metode_arr AND HAS_JENIS("Pekerjaan Konstruksi") AND total_nilai > 200000000 AND total_nilai <= 400000000 AND (status_umk != "Ya" OR MATCH(nama_paket_objek, DICT_KONSTRUKSI_KOMPLEKS)) AND (tahun_anggaran >= 2025 OR tahun_anggaran == 0)` | Pasal 1 angka 40 Perpres 46/2025 jo. Perlem LKPP 3/2026 | **Pelanggaran Syarat PL Konstruksi $\text{Rp}200\text{ Juta} - \text{Rp}400\text{ Juta}$ (Wajib UMK & Teknologi Sederhana):** Pengadaan Langsung Konstruksi di atas $\text{Rp}200\text{ Juta}$ s.d. $\text{Rp}400\text{ Juta}$ hanya sah apabila menggunakan Usaha Kecil (`status_umk = Ya`) DAN berteknologi sederhana / risiko kecil (tidak memuat kata kunci konstruksi kompleks). Paket $\le \text{Rp}2\text{ Miliar}$ masuk subkualifikasi K1 (prioritas Kabupaten/Kota). | Ubah `status_umk` menjadi **`Ya`** dan pastikan kualifikasi usaha kecil (K1), atau ubah metode menjadi **Tender / E-Purchasing Katalog Konstruksi** jika berteknologi kompleks / non-kecil. |
| **`R-08`** | **TINGGI** | `cara_pengadaan == "Penyedia" AND "Penunjukan Langsung" IN metode_arr AND NOT MATCH(nama_paket_clean, DICT_01_DIKECUALIKAN) AND NOT MATCH(nama_paket_clean, DICT_11_PENUNJUKAN_LANGSUNG_SAH)` | Pasal 38 ayat (5), Pasal 38A, Pasal 41 ayat (5) & Pasal 41A Perpres 46/2025 jo. Lampiran I & II Angka 1.4 Perlem LKPP 3/2026 | **Penggunaan Metode Penunjukan Langsung Tanpa Indikasi Keadaan Tertentu & Pemisahan Kewenangan:** Nama paket reguler tanpa kriteria Keadaan Tertentu. Sesuai Lampiran I & II Angka 1.4, Penunjukan Langsung $> \text{Rp}200\text{ Juta}$ (Barang/Konstruksi/Jasa Lainnya) atau $> \text{Rp}100\text{ Juta}$ (Konsultansi) wajib dilaksanakan oleh Pokja Pemilihan UKPBJ, bukan Pejabat Pengadaan. | UKPBJ & PPK wajib memverifikasi Berita Acara Justifikasi Keadaan Tertentu. Jika pengadaan reguler, ubah ke **E-Purchasing / Tender / Seleksi**. Jika terjustifikasi, proses pemilihan wajib memperhatikan batas kewenangan Pejabat Pengadaan vs Pokja Pemilihan UKPBJ. |
| **`R-08b`** | **RENDAH** | `cara_pengadaan == "Penyedia" AND "Penunjukan Langsung" IN metode_arr AND CONTAINS(nama_paket_clean, "repeat order")` | Pasal 38 ayat (5) Perpres 46/2025 jo. Lampiran I Angka 5.2.1 Perlem LKPP 3/2026 | **Verifikasi 5 Syarat Kumulatif Repeat Order (RO):** (1) Paket awal hasil Tender/Seleksi; (2) HPS RO $\le$ nilai kontrak akhir paket awal; (3) Maksimal 2x dalam 3 tahun sejak BAST akhir; (4) Kinerja penyedia Sangat Baik pada paket awal (dan Sangat Baik pada RO-1 jika RO-2); (5) Ketua Tim konsultan wajib tetap sama. | Lakukan verifikasi berkas: kontrak awal hasil Tender/Seleksi, HPS $\le$ kontrak awal, riwayat perulangan (maksimal ke-2), nilai kinerja SIKaP Sangat Baik, dan kesamaan personel Team Leader (jika Konsultansi). |
| **`R-09`** | **TINGGI** | `cara_pengadaan == "Penyedia" AND (("Seleksi" IN metode_arr AND NOT HAS_JENIS("Jasa Konsultansi")) OR (("Tender" IN metode_arr OR "Tender Cepat" IN metode_arr) AND ONLY_JENIS("Jasa Konsultansi")) OR ("Kontes" IN metode_arr AND MATCH(nama_paket_objek, DICT_04_KATALOG_STANDAR)))` | Pasal 38 & Pasal 41 Perpres 46/2025 jo. Perlem LKPP 3/2026 | **Inkompatibilitas Metode Pemilihan terhadap Jenis Pengadaan:**<br>1) `Seleksi` dipakai untuk non-Konsultansi;<br>2) `Tender`/`Tender Cepat` dipakai untuk murni Jasa Konsultansi; atau<br>3) `Kontes` dipakai untuk barang/jasa standar. | Sesuaikan metode pemilihan: gunakan **Seleksi** untuk Jasa Konsultansi $> \text{Rp}100\text{ Juta}$, dan **E-Purchasing / Pengadaan Langsung / Tender** untuk Barang/Konstruksi/Jasa Lainnya. |
| **`R-27`** | **TINGGI** | `cara_pengadaan == "Penyedia" AND NOT is_blud AND "Pengadaan Langsung" IN metode_arr AND total_nilai BETWEEN (0.95 * BATAS_PL) AND BATAS_PL` | Pasal 20 ayat (2) Perpres 16/2018 jo. Perpres 46/2025 & Perlem LKPP 3/2026 (Pencegahan Penghindaran Tender) | **Anomali Pagu Mepet Batas Atas Pengadaan Langsung (*Threshold Hugging* $95\% - 100\%$ — Time-Aware):** Nilai pagu paket berada tepat di bibir batas maksimal Pengadaan Langsung (Konstruksi TA $\le 2024$: $\text{Rp}190\text{M}-\text{Rp}200\text{M}$; TA $\ge 2025$: $\text{Rp}380\text{M}-\text{Rp}400\text{M}$; Barang/Jasa: $\text{Rp}190\text{M}-\text{Rp}200\text{M}$; Konsultansi: $\text{Rp}95\text{M}-\text{Rp}100\text{M}$). | Lakukan reviu kertas kerja penyusunan HPS/RAB dan pastikan tidak terdapat paket sejenis lain pada kegiatan yang sama; utamakan **E-Purchasing** atau konsolidasikan ke **Tender/Seleksi**. |
| **`R-28`** | **SEDANG** | `cara_pengadaan == "Penyedia" AND (("Tender" IN metode_arr AND ONLY_JENIS("Barang") AND total_nilai <= 200000000) OR ("Tender" IN metode_arr AND ONLY_JENIS("Jasa Lainnya") AND total_nilai <= 200000000) OR ("Tender" IN metode_arr AND ONLY_JENIS("Pekerjaan Konstruksi") AND total_nilai <= 200000000) OR ("Seleksi" IN metode_arr AND ONLY_JENIS("Jasa Konsultansi") AND total_nilai <= 100000000))` | Prinsip Efisiensi Pasal 6 & Pasal 38 Perpres 46/2025 jo. Perlem LKPP 3/2026 | **Inefisiensi Metode Kompetitif pada Pagu Kecil (*Over-Method Anomaly*):** Paket bernilai di bawah batas Pengadaan Langsung tetapi diinput menggunakan metode `Tender` atau `Seleksi` (membebani Pokja UKPBJ). | Ubah metode pemilihan menjadi **E-Purchasing** atau **Pengadaan Langsung** oleh Pejabat Pengadaan, kecuali merupakan paket konsolidasi lintas satker. |
| **`R-29`** | **TINGGI** | `cara_pengadaan == "Penyedia" AND "Tender Cepat" IN metode_arr AND (HAS_JENIS("Pekerjaan Konstruksi") OR HAS_JENIS("Jasa Konsultansi") OR total_nilai <= 200000000)` | Pasal 38 ayat (4) & Pasal 50 ayat (4) Perpres 16/2018 jo. Perpres 46/2025 & Perlem LKPP 3/2026 | **Ketidaksesuaian Penggunaan Metode `Tender Cepat`:** Metode `Tender Cepat` dipilih untuk Pekerjaan Konstruksi, Jasa Konsultansi, atau paket bernilai $\le \text{Rp}200\text{ Juta}$ yang tidak memenuhi syarat spesifikasi terstandar di SIKaP. | Ubah menjadi **E-Purchasing** (jika tersedia di Katalog V6), **Pengadaan Langsung** (jika $\le$ batas PL), atau **Tender Reguler / Seleksi**. |
| **`R-33`** | **TINGGI** | `cara_pengadaan == "Penyedia" AND (("Pengadaan Langsung" IN metode_arr AND (is_integrated_package == true OR jenis_pengadaan == "Terintegrasi")) OR (jenis_pengadaan == "Terintegrasi" AND MATCH(nama_paket_objek, DICT_BARANG_JASA_STANDAR)))` | Pasal 54 Perpres 16/2018 jo. Perpres 46/2025 & Perlem LKPP 3/2026 | **Penyalahgunaan Jenis Terintegrasi & Pengadaan Langsung Terintegrasi (Kalibrasi v3.4):** Pekerjaan Terintegrasi dilarang menggunakan Pengadaan Langsung, atau jenis pengadaan `Terintegrasi` disalahgunakan untuk komoditas Barang/Jasa Lainnya standar (sewa tenda, kursi, ATK). | Ubah jenis pengadaan menjadi **Barang / Jasa Lainnya** sesuai objek riil atau gunakan metode **Tender/Seleksi**. |

---

#### KELOMPOK C: Validasi Cara Pengadaan (Penyedia vs Swakelola vs Dikecualikan vs Hibah vs BLUD)

| Kode Rule | Tingkat Risiko | Logika Kondisi (Pseudocode Terkalibrasi v3.1) | Dasar Regulasi | Deskripsi Bentuk Pelanggaran | Rekomendasi Tindak Lanjut Sistem |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **`R-10`** | **TINGGI** | `cara_pengadaan == "Penyedia" AND NOT ("Dikecualikan" IN metode_arr) AND MATCH(nama_paket_objek, DICT_01_DIKECUALIKAN)` | Pasal 62 ayat (1) Perpres PBJ & Perlem LKPP 5/2021 | **Salah Metode untuk Komoditas Tarif Resmi/Pajak/Utilitas:** Tagihan listrik, air, telepon, internet langganan, pajak/STNK, bea perizinan, atau materai diinput sebagai `Pengadaan Langsung`/`Penunjukan Langsung`. | Ubah menjadi metode **Dikecualikan** atau catat pada mekanisme **Swakelola (Administrasi Pembayaran)**. |
| **`R-11`** | **KRITIS** | `cara_pengadaan == "Swakelola" AND MATCH(nama_paket_objek, DICT_02_OUTSOURCING, NEG_LOOKAHEAD_02)` | Pasal 66 UU 20/2023 (ASN) & Pasal 16–18 Perpres PBJ | **Pengadaan Jasa Tenaga Alih Daya (*Outsourcing*) / Honorer PTHL/TKS Diinput sebagai Swakelola (Kalibrasi v3.4):** Jasa kebersihan, keamanan, sopir, tenaga pendukung, PTHL, atau TKS diinput sebagai Swakelola. Berisiko melanggar larangan pengangkatan tenaga non-ASN/honorer. PPPK Paruh Waktu dikecualikan sebagai ASN sah. | Ubah `cara_pengadaan` menjadi **`Penyedia`**, `jenis_pengadaan` = **`Jasa Lainnya`**, dengan metode **E-Purchasing / Tender** melalui badan usaha penyedia jasa pekerja. |
| **`R-12`** | **TINGGI** | `cara_pengadaan == "Swakelola" AND (tipe_swakelola IN ("Tipe 1", "-", "", NULL)) AND (MATCH(nama_paket_objek, DICT_06_MURNI_KONSULTANSI) OR MATCH(nama_paket_objek, DICT_05_MURNI_KONSTRUKSI) OR CONTAINS(nama_paket_objek, "pihak ketiga") OR CONTAINS(nama_paket_objek, "kalibrasi") OR CONTAINS(nama_paket_objek, "belanja modal"))` | Pasal 18 Perpres 12/2021 jo. Perpres 46/2025 & Perlem LKPP 3/2021 | **Pekerjaan Penyedia/Modal/Konstruksi/Konsultansi Masuk ke dalam Swakelola:** Paket bernomenklatur Belanja Modal, Pekerjaan Konstruksi, Jasa Konsultansi, Kalibrasi, atau Pihak Ketiga diinput sebagai Swakelola tanpa penunjukan Tipe II/III/IV yang sah. | Jika menggunakan PTN/Instansi Lain/Ormas/Pokmas, lengkapi `tipe_swakelola` (`Tipe 2/3/4`). Jika dilaksanakan oleh pelaku usaha/vendor swasta, wajib diubah menjadi **Penyedia**. |
| **`R-13`** | **TINGGI** | `cara_pengadaan == "Penyedia" AND NOT ("Dikecualikan" IN metode_arr) AND MATCH(nama_paket_objek, DICT_03_SWAKELOLA_MURNI, NEG_LOOKAHEAD_03)` | Perlem LKPP 11/2021 tentang Perencanaan PBJ | **Belanja Pegawai/Administrasi Internal Diinput sebagai Penyedia (Kalibrasi v3.4):** Objek utama paket berupa perjalanan dinas biasa, honorarium narasumber/tim, uang lembur, atau kontribusi iuran diinput sebagai pemilihan Penyedia. Dikecualikan jika menggunakan metode Dikecualikan atau paket meeting/akomodasi. | Pindahkan pencatatan paket dari `Penyedia` ke **Swakelola** (kecuali jika merupakan kontrak paket *fullboard meeting* hotel atau *Event Organizer* melalui Katalog). |
| **`R-14`** | **SEDANG** | `MATCH(nama_paket_clean, DICT_09_HIBAH_BANSOS) AND ((CONTAINS(nama_paket_clean, "barang") AND cara_pengadaan == "Swakelola") OR (CONTAINS(nama_paket_clean, "uang") AND cara_pengadaan == "Penyedia"))` | Permendagri 77/2020 & Perpres PBJ | **Ketidaksesuaian Mekanisme Belanja Hibah / Bantuan Sosial (Barang vs Uang):** Hibah Barang kepada masyarakat diinput sebagai Swakelola, atau sebaliknya Hibah Uang (NPHD) diinput sebagai Penyedia. | **Hibah Barang/Jasa** yang diadakan OPD wajib melalui **Penyedia (E-Purchasing/Tender)**. **Hibah Uang** langsung ke rekening penerima (NPHD) dicatat dalam **Swakelola**. |
| **`R-30`** | **TINGGI** | `(is_blud == true AND cara_pengadaan == "Penyedia" AND "Pengadaan Langsung" IN metode_arr AND total_nilai > TH_BATAS_PL(jenis_arr)) OR (is_dak == true AND "Pengadaan Langsung" IN metode_arr AND HAS_JENIS("Pekerjaan Konstruksi") AND total_nilai > 200000000)` | Pasal 61 Perpres 46/2025, Perlem LKPP 5/2021 & Juknis DAK Fisik | **Validasi Khusus Pengadaan Sumber Dana BLUD & DAK:**<br>1) Paket BLUD di atas batas PL Perpres masih memakai label `Pengadaan Langsung` (bukan `Dikecualikan`/`E-Purchasing`); atau<br>2) Paket DAK Fisik Konstruksi $> \text{Rp}200\text{ Juta}$ menggunakan Pengadaan Langsung tanpa verifikasi Juknis K/L. | Untuk BLUD yang menggunakan Peraturan Pimpinan BLUD di atas batas PL Perpres, gunakan metode **Dikecualikan** atau **E-Purchasing**. Untuk DAK Fisik, pastikan kepatuhan terhadap Juknis K/L pengampu. |

---

#### KELOMPOK D: Kewajiban E-Purchasing, Ketepatan Jenis Pengadaan & Kronologi Jadwal

| Kode Rule | Tingkat Risiko | Logika Kondisi (Pseudocode Terkalibrasi v3.1) | Dasar Regulasi | Deskripsi Bentuk Pelanggaran | Rekomendasi Tindak Lanjut Sistem |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **`R-15`** | **SEDANG** | `cara_pengadaan == "Penyedia" AND ("Pengadaan Langsung" IN metode_arr OR "Tender" IN metode_arr OR "Tender Cepat" IN metode_arr) AND MATCH(nama_paket_objek, DICT_04_KATALOG_STANDAR)` | Pasal 50 ayat (5), (5a), dan (5b) Perpres 46/2025 jo. Perlem LKPP 3/2026 | **Penghindaran Kewajiban E-Purchasing Katalog Elektronik V6:** Komoditas standar yang telah tayang di Katalog V6 masih direncanakan dengan Pengadaan Langsung atau Tender. | Ubah metode pemilihan menjadi **E-Purchasing Katalog V6 (atau Mini-Competition Katalog)**, kecuali PPK telah menyusun Dokumen Penilaian Tertulis pengecualian E-Purchasing sesuai Pasal 50 ayat (5a) Perpres 46/2025 jo. Perlem LKPP 3/2026. |
| **`R-16`** | **TINGGI** | `cara_pengadaan == "Penyedia" AND NOT HAS_JENIS("Jasa Konsultansi") AND MATCH(nama_paket_objek, DICT_06_MURNI_KONSULTANSI)` | Pasal 3 & Pasal 41 Perpres 46/2025 jo. Perlem LKPP 3/2026 | **Salah Klasifikasi Jenis Pengadaan pada Jasa Konsultansi:** Objek paket berupa perencanaan teknis, pengawasan konstruksi, kajian, atau tenaga ahli diinput sebagai non-Konsultansi (berisiko menyamarkan batas wajib Seleksi $\text{Rp}100\text{ Juta}$). | Ubah `jenis_pengadaan` menjadi **`Jasa Konsultansi`** dan pastikan paket $> \text{Rp}100\text{ Juta}$ menggunakan metode **Seleksi** atau **E-Purchasing**. |
| **`R-17`** | **TINGGI** | `cara_pengadaan == "Penyedia" AND NOT HAS_JENIS("Pekerjaan Konstruksi") AND NOT ("Dikecualikan" IN metode_arr) AND MATCH(nama_paket_objek, DICT_05_MURNI_KONSTRUKSI, NEG_LOOKAHEAD_05)` | Pasal 3 Perpres 16/2018 jo. Perpres 46/2025 & Perlem LKPP 3/2026 | **Salah Klasifikasi Jenis Pengadaan pada Pekerjaan Konstruksi (Kalibrasi v3.4):** Objek paket berupa pembangunan/rehabilitasi/renovasi fisik gedung, jalan, jembatan, pagar diinput sebagai `Barang` atau `Jasa Lainnya`. Dikecualikan untuk belanja bahan/material hotmix pemeliharaan jalan UPTD. | Ubah `jenis_pengadaan` menjadi **`Pekerjaan Konstruksi`** agar mengikuti standar kontrak konstruksi, SBU/SKK, dan masa pemeliharaan. |
| **`R-18`** | **SEDANG** | `cara_pengadaan == "Penyedia" AND ONLY_JENIS("Barang") AND MATCH(nama_paket_objek, DICT_07_MURNI_JASA_LAINNYA)` | Pasal 3 Perpres 16/2018 jo. Perpres 46/2025 & Perlem LKPP 3/2026 | **Salah Klasifikasi Jenis Pengadaan (Jasa Lainnya tertulis Barang):** Objek paket berupa sewa, pemeliharaan, jasa kebersihan/keamanan, publikasi iklan, atau kalibrasi diinput sebagai pengadaan `Barang`. | Ubah `jenis_pengadaan` dari `Barang` menjadi **`Jasa Lainnya`**. |
| **`R-19`** | **SEDANG** | `cara_pengadaan == "Penyedia" AND NOT HAS_JENIS("Barang") AND MATCH(nama_paket_objek, DICT_08_MURNI_BARANG)` | Pasal 3 Perpres 16/2018 jo. Perpres 46/2025 & Perlem LKPP 3/2026 | **Salah Klasifikasi Jenis Pengadaan (Barang tertulis Jasa Lainnya/Konstruksi/Terintegrasi):** Pembelian peralatan/mesin, modal alat, suku cadang, atau bahan habis pakai murni diinput sebagai non-`Barang`. | Ubah `jenis_pengadaan` menjadi **`Barang`**. |
| **`R-31`** | **TINGGI** | **Jika Kolom Jadwal Tersedia:**<br>1) `budget_cycle == "MURNI" AND tanggal_umumkan > DATE(tahun_anggaran, 3, 31)`<br>2) `jadwal_pemilihan_awal > jadwal_kontrak_awal`<br>3) `is_multiyears == false AND YEAR(jadwal_kontrak_akhir) > tahun_anggaran` | Pasal 22 Perpres PBJ, Indikator MCP KPK & ITKP LKPP | **Anomali Kronologis Jadwal & Keterlambatan Pengumuman RUP:**<br>1) RUP APBD Murni diumumkan lewat $31\text{ Maret}$;<br>2) Jadwal pemilihan penyedia lebih akhir dari mulai kontrak; atau<br>3) Kontrak tahun tunggal melewati $31\text{ Desember}$. | Perbaiki penjadwalan paket pada SIRUP agar selaras dengan kalender anggaran dan timeline pemilihan UKPBJ. |

---

#### KELOMPOK E: Deteksi Pemecahan Paket (*Splitting*), Duplikasi, Konsolidasi & Kelengkapan Konstruksi (Cluster Rules per OPD)

| Kode Rule | Tingkat Risiko | Logika Agregasi Klaster (Pseudocode Terkalibrasi v3.1) | Dasar Regulasi | Deskripsi Bentuk Pelanggaran | Rekomendasi Tindak Lanjut Sistem |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **`R-20`** | **KRITIS** | `GROUP BY (nama_opd_clean, budget_cycle, cluster_komoditas, jenis_pengadaan, lokasi_cluster)` WHERE `"Pengadaan Langsung" IN metode_arr AND NOT is_blud AND NOT MATCH(nama_paket_objek, DICT_01_DIKECUALIKAN)`:<br>`COUNT(*) > 1` AND `SUM(total_nilai) > TH_BATAS_PL(jenis_arr)`<br>*(Catatan: Komoditas tarif resmi/utilitas/PPPK di-exclude karena wajib Dikecualikan sesuai R-10; Jika `jenis_pengadaan != "Pekerjaan Konstruksi"`, `lokasi_cluster` diset `"ALL"`)* | Pasal 20 ayat (2) & Pasal 21 Perpres 46/2025 jo. Perlem LKPP 3/2026 | **Indikasi Kuat Pemecahan Paket (*Splitting*) untuk Menghindari Tender/Seleksi (Kalibrasi v3.4):** Ditemukan beberapa paket Pengadaan Langsung sejenis pada siklus anggaran yang sama (`MURNI` atau `PERUBAHAN`) yang nilai masing-masing paketnya di bawah batas PL, namun nilai akumulasinya melampaui batas wajib Tender/Seleksi. | Gabungkan (*konsolidasikan*) paket-paket tersebut menjadi satu paket dan laksanakan melalui metode **Tender / Seleksi** atau **E-Purchasing**. |
| **`R-21`** | **TINGGI** | **1) `R-21a` (Duplikasi Teknis / Kerusakan Kode RUP):** `COUNT(*) > 1` pada `kode_rup` yang sama, atau `is_scientific_truncated == true`.<br>**2) `R-21b` (Indikasi *Double Counting* Paket Identik Lintas Kode RUP):** `COUNT(*) > 1` pada `(nama_opd_clean, nama_paket_clean, sumber_dana, total_nilai)` dengan `kode_rup` berbeda dan `total_nilai >= 50000000`. | Validitas Data SIRUP Nasional (INAPROC) | **Duplikasi Input Paket (*Double Counting*) & Kerusakan Kode RUP:** Terdapat paket bernilai $\ge \text{Rp}50\text{ Juta}$ yang diinput ganda secara identik dengan kode RUP berbeda (menggelembungkan pagu RUP OPD) dan/atau kerusakan pemotongan digit `kode_rup` menjadi `"00"`. | Verifikasi apakah merupakan *double input* pada sub-kegiatan yang sama; hapus entri ganda pada SIRUP dan pastikan ekspor CSV memformat `kode_rup` sebagai `String`. |
| **`R-22`** | **SEDANG** | `GROUP BY (nama_opd_clean, budget_cycle, cluster_komoditas)` WHERE `cara_pengadaan == "Penyedia"`:<br>`COUNT(*) >= 3` OR `MIN(total_nilai) < 500000` | Pasal 21 Perpres 46/2025 (Kewajiban Konsolidasi PBJ) jo. Perlem LKPP 3/2026 | **Fragmentasi Paket Berlebihan & Paket Mikro Tidak Efisien:** Komoditas yang sama dipecah menjadi $\ge 3$ paket terpisah dalam satu OPD pada siklus anggaran yang sama atau terdapat paket bernilai mikro ($< \text{Rp}500.000$). | PA/KPA/PPK wajib melakukan **Konsolidasi Paket Pengadaan** sejenis untuk efisiensi kontrak dan mendapatkan potongan harga volume (*bulk discount*). |
| **`R-23`** | **SEDANG** | `GROUP BY (nama_opd_clean, nama_paket_clean)` WHERE `cara_pengadaan == "Penyedia"`:<br>`COUNT(DISTINCT metode_pengadaan) > 1` OR `COUNT(DISTINCT jenis_pengadaan) > 1` | Prinsip Konsistensi Perencanaan PBJ (Perlem LKPP 11/2021) | **Inkonsistensi Metode/Jenis Pengadaan pada Paket Identik:** Paket dengan nama yang sama persis di dalam satu OPD diinput dengan jenis pengadaan atau metode pemilihan yang berbeda-beda antar-bidang. | Seragamkan `jenis_pengadaan` dan utamakan metode **E-Purchasing** secara konsisten di seluruh sub-kegiatan OPD. |
| **`R-32`** | **SEDANG** | **Evaluasi Tingkat OPD:**<br>`SUM(total_nilai WHERE HAS_JENIS("Pekerjaan Konstruksi")) > 400000000` AND `COUNT(WHERE HAS_JENIS("Jasa Konsultansi") AND MATCH(nama_paket_clean, "pengawasan|perencanaan|manajemen konstruksi|ded")) == 0` | Pasal 7 ayat (1) dan ayat (5) Peraturan LKPP Nomor 3 Tahun 2026 & PP 16/2021 | **Paket Pekerjaan Konstruksi Tanpa Pasangan Jasa Konsultansi Perencanaan/Pengawasan (*Orphan Construction*):** OPD merencanakan pekerjaan fisik konstruksi bernilai $> \text{Rp}400\text{ Juta}$, namun tidak ditemukan satu pun paket Jasa Konsultansi Perencanaan/Pengawasan di RUP OPD tersebut. | Pastikan paket Jasa Konsultansi Pengawasan/Manajemen Konstruksi telah diinput ke SIRUP, atau tetapkan Tim/Tenaga Teknis internal yang wajib menyusun laporan pengawasan konstruksi sesuai Pasal 7 ayat (5) Perlem LKPP 3/2026. |

---

#### KELOMPOK F: Kepatuhan Afirmasi PDN, UMK-Koperasi & Indikator Makro OPD (Terkalibrasi v3.1)

| Kode Rule | Tingkat Risiko | Logika Evaluasi (Pseudocode Terkalibrasi v3.1) | Dasar Regulasi | Deskripsi Bentuk Pelanggaran | Rekomendasi Tindak Lanjut Sistem |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **`R-24`** | **TINGGI** | **Kecualikan PPPK Paruh Waktu, Iuran Jaminan ASN, dan Tunda Bayar**<br>**1) Jasa Konsultansi Konstruksi:**<br>• `total_nilai <= 1000000000 AND status_umk != "Ya"` $\rightarrow$ Wajib Usaha Kecil (Pasal 4 ayat (2) huruf a Perlem LKPP 3/2026).<br>• `total_nilai > 1000000000 AND status_umk == "Ya"` $\rightarrow$ Wajib Usaha Non-Kecil (Menengah/Besar).<br>**2) Pengadaan Umum Non-Konsultansi Konstruksi:**<br>• `total_nilai <= 15000000000 AND status_umk != "Ya"` $\rightarrow$ Wajib Usaha Kecil (K1: $\le \text{Rp}2\text{M}$ prioritas Kab/Kota; K2: $\text{Rp}2\text{M}-\text{Rp}7,5\text{M}$ prioritas Provinsi; K3: $\text{Rp}7,5\text{M}-\text{Rp}15\text{M}$).<br>• `total_nilai > 15000000000 AND status_umk == "Ya"` $\rightarrow$ Wajib Usaha Non-Kecil (Menengah/Besar). | Pasal 65 ayat (4) Perpres 46/2025 jo. Pasal 4 Perlem LKPP 3/2026 | **Ketidaksesuaian Pencadangan Paket Usaha Kecil/Koperasi (Kalibrasi v3.4):** Paket Jasa Konsultansi Konstruksi $\le \text{Rp}1\text{ Miliar}$ atau paket umum $\le \text{Rp}15\text{ Miliar}$ wajib dicadangkan bagi Usaha Kecil. Paket di atas nilai tersebut diperuntukkan bagi Usaha Non-Kecil. Belanja PPPK Paruh Waktu, Iuran ASN, dan Tunda Bayar dikecualikan dari evaluasi R-24. | Sesuaikan `status_umk` pada SIRUP, lengkapi kualifikasi usaha (K1/K2/K3 untuk konstruksi), atau siapkan justifikasi teknis tertulis jika paket menuntut kompetensi yang tidak dapat dipenuhi UMK. |
| **`R-25`** | **RENDAH** | `cara_pengadaan == "Penyedia" AND NOT ("Dikecualikan" IN metode_arr) AND (produk_dalam_negeri != "Ya" OR (status_umk == "Ya" AND total_nilai > 200000000 AND MATCH(nama_paket_objek, DICT_10_TEKNOLOGI_TINGGI_IMPOR)))` | Pasal 66 & Pasal 72A Perpres 46/2025 jo. Perlem LKPP 3/2026 | **Verifikasi Kewajiban PDN & Evaluasi Sekuensial Produk Impor:** Paket tidak mencantumkan PDN (`Tidak`/`-`) atau merupakan paket instrumen laboratorium/alkes/reagen kimia bernilai besar yang ditandai UMK & PDN secara otomatis. | PPK wajib menerapkan urutan prioritas pembelian PDN ber-TKDN $\ge 25\%$ terlebih dahulu sebelum membeli produk impor sesuai Pasal 72A Perpres 46/2025. |
| **`R-26`** | **KRITIS (Level OPD)** | **Evaluasi 4 Indikator Makro per Satker (Semesta Penyedia Non-Dikecualikan $U_{\text{P}}$):**<br>1) **`R-26.1` (UMK):**<br>$\text{Rasio UMK}_{\text{Penyedia}} = \frac{\sum_{i \in U_{\text{P}} \land \text{UMK=Ya}} \text{Nilai}_i}{\sum_{i \in U_{\text{P}}} \text{Nilai}_i} \times 100\%$<br>$\text{Rasio UMK}_{\le 15\text{M}} = \frac{\sum_{i \in U_{\text{P}} \land \text{Nilai} \le 15\text{M} \land \text{UMK=Ya}} \text{Nilai}_i}{\sum_{i \in U_{\text{P}} \land \text{Nilai} \le 15\text{M}} \text{Nilai}_i} \times 100\%$<br>*Trigger Pelanggaran `R-26.1`:* $\text{Rasio UMK}_{\text{Penyedia}} < 40\%$ **AND** $\text{Rasio UMK}_{\le 15\text{M}} < 80\%$<br>2) **`R-26.2` (PLg):** $\text{Rasio PLg}_{\text{OPD}} = \frac{\sum \text{Nilai}_{\text{Penunjukan Langsung}}}{\sum \text{Nilai}_{\text{Penyedia}}} \times 100\% > 5\%$<br>3) **`R-26.3` (E-Purch):** $\text{Rasio EPurch}_{\text{OPD}} = \frac{\sum \text{Nilai}_{\text{E-Purchasing}}}{\sum \text{Nilai}_{\text{Penyedia}}} \times 100\% < 30\%$<br>4) **`R-26.4` (Cakupan DPA):** *Jika `pagu_pengadaan_dpa` tersedia:* $\text{Rasio Cakupan}_{\text{SIRUP}} = \frac{\sum \text{Nilai}_{\text{Total RUP OPD}}}{\text{Pagu Pengadaan DPA OPD}} \times 100\% < 95\%$ | Pasal 65 ayat (2), Pasal 65 ayat (4), Pasal 50 ayat (5) Perpres 46/2025, MCP KPK & ITKP LKPP | **Ketidakpatuhan Indikator Makro OPD:**<br>1) Alokasi belanja UMK-Koperasi di bawah $40\%$ (dan alokasi pada paket *eligible* $\le \text{Rp}15\text{ Miliar}$ belum mencapai $80\%$);<br>2) Porsi Penunjukan Langsung $> 5\%$;<br>3) Pemanfaatan E-Purchasing $< 30\%$; atau<br>4) Keterisian RUP terhadap DPA $< 95\%$. | PA/KPA wajib merealokasi paket pengadaan $\le \text{Rp}15\text{ Miliar}$ kepada UMK-Koperasi Dalam Negeri, mengonversi paket ke E-Purchasing Katalog V6, dan mengumumkan sisa paket DPA ke SIRUP. |

---

### 3.4 Algoritma *Graph-Based Fuzzy Clustering* & Deteksi *Splitting*
Untuk memastikan pengelompokan komoditas bekerja secara deterministik (tidak bergantung pada urutan baris CSV) dan tidak menghasilkan *false positive* lintas siklus anggaran, sistem menjalankan algoritma $4$ tahap:

1. **Tahap 1 — Normalisasi Sinonim & *Stopword Stripping* (`nama_paket_core`):**
   * Petakan sinonim baku terlebih dahulu: `"mamin" -> "makan minum"`, `"atk" -> "alat tulis kantor"`, `"bmhp" -> "bahan medis habis pakai"`, `"ac" -> "alat pendingin"`, `"pc" -> "komputer"`.
   * Hapus prefiks generik dari `nama_paket_objek`: `^(belanja|pengadaan|biaya|pekerjaan|penyediaan|sub kegiatan)\s+(modal|barang|jasa|operasional)?\s*`.
   * Ekstrak sufiks lokasi ke atribut `lokasi_extracted` menggunakan pola `\s+(pada|di|lokasi|kecamatan|kelurahan|desa|ruas)\s+([a-z0-9\s]+)$`, lalu hapus sufiks lokasi, tahap, triwulan, dan tahun anggaran dari `nama_paket_core`.
2. **Tahap 2 — Pembentukan Graf Kemiripan (*Jaccard Similarity Graph*):**
   * Di dalam partisi `(nama_opd_clean, budget_cycle, jenis_pengadaan)`, setiap paket $i$ direpresentasikan sebagai himpunan token unik $T_i$ dari `nama_paket_core`.
   * Buat sisi (*edge*) tak berarah antara paket $A$ dan paket $B$ apabila:
     $$
     \text{Similarity}(T_A, T_B) = \frac{|T_A \cap T_B|}{|T_A \cup T_B|} \ge 0,78
     $$
3. **Tahap 3 — Ekstraksi Komponen Terhubung (*Connected Components / Transitive Closure*):**
   * Jalankan algoritma *Breadth-First Search (BFS) / Disjoint-Set Union (DSU)* pada graf kemiripan untuk menemukan seluruh komponen terhubung. Setiap komponen terhubung membentuk satu `cluster_komoditas` yang konsisten.
4. **Tahap 4 — Evaluasi *Splitting* Berbasis Lokasi (`R-20`) & Konsolidasi (`R-22`):**
   * Untuk `Barang`, `Jasa Lainnya`, dan `Jasa Konsultansi`, seluruh paket dalam satu `(nama_opd_clean, budget_cycle, cluster_komoditas)` digabung tanpa memandang sub-lokasi karena dapat dikonsolidasikan dalam satu kontrak payung/pengiriman.
   * Khusus untuk `Pekerjaan Konstruksi`, evaluasi `R-20` dilakukan pada tingkat `lokasi_cluster` yang sama (atau jika lokasi tidak dicantumkan/identik), sedangkan jika tersebar di lokasi berbeda namun sejenis, diarahkan ke rekomendasi konsolidasi `R-22`.

---

### 3.5 Formula Perhitungan **RUP Compliance Index (RCI v3.1)** Universal
Untuk menilai dan membandingkan tingkat kepatuhan antar-OPD secara objektif tanpa celah pembagian nol (*zero-division*), sistem menggunakan formula **RCI v3.1**:

1. **Proteksi Validitas Dasar (*Zero-Division Guard*):**
   * Hitung $\text{Pagu}_{\text{valid}} = \sum_{\text{baris } i \text{ unik}} \max(0, \text{total\_nilai}_i)$.
   * Jika $\text{Total Paket OPD} == 0$ atau $\text{Pagu}_{\text{valid}} == 0$, maka $\text{RCI}_{\text{OPD}} = 0,00$ (Zona **MERAH**).

2. **Perhitungan Rasio Eksposur Pagu Berisiko Tinggi ($E_{\text{pagu}}$):**
   * Setiap baris paket $i$ yang melanggar $\ge 1$ rule hanya dihitung **satu kali** berdasarkan tingkat risiko (*severity*) tertinggi pada baris tersebut (`max_severity_i`):
     $$
     E_{\text{pagu}} = \min\left(100, \left( \frac{\sum_{i \in \text{KRITIS}} \text{Nilai}_i + 0,50 \times \sum_{j \in \text{TINGGI}} \text{Nilai}_j + 0,15 \times \sum_{k \in \text{SEDANG}} \text{Nilai}_k}{\text{Pagu}_{\text{valid}}} \right) \times 100\right)
     $$

3. **Perhitungan Rasio Kepadatan Pelanggaran Paket ($E_{\text{paket}}$):**
   * Berdasarkan jumlah paket unik menurut `max_severity` ($N_{\text{KRITIS}}, N_{\text{TINGGI}}, N_{\text{SEDANG}}, N_{\text{RENDAH}}$):
     $$
     E_{\text{paket}} = \min\left(100, \frac{(10 \times N_{\text{KRITIS}}) + (5 \times N_{\text{TINGGI}}) + (2 \times N_{\text{SEDANG}}) + (0,5 \times N_{\text{RENDAH}})}{\text{Total Paket RUP OPD}} \times 10\right)
     $$

4. **Penalti Kepatuhan Makro Terkalibrasi ($P_{\text{makro}}$):**
   * Melanggar `R-26.1` ($\text{Rasio UMK}_{\text{Penyedia}} < 40\%$ **dan** $\text{Rasio UMK}_{\le 15\text{M}} < 80\%$): $+15\text{ poin penalti}$.
   * Melanggar `R-26.2` (Rasio Penunjukan Langsung $> 5\%$): $+10\text{ poin penalti}$.
   * Melanggar `R-26.3` (Rasio E-Purchasing $< 30\%$): $+5\text{ poin penalti}$.
   * Melanggar `R-26.4` (Rasio Keterisian RUP terhadap DPA $< 95\%$, jika data DPA tersedia): $+15\text{ poin penalti}$.

5. **Skor Akhir RUP Compliance Index ($\text{RCI}$):**
   $$
   \text{RCI}_{\text{OPD}} = \text{round}\left(\max\left(0, 100 - \left(0,50 \times E_{\text{pagu}} + 0,35 \times E_{\text{paket}} + P_{\text{makro}}\right)\right), 2\right)
   $$

| Rentang Skor RCI | Zona Warna | Predikat Kepatuhan OPD | Tindak Lanjut Sistem & Pengawasan |
| :--- | :--- | :--- | :--- |
| $85,00 - 100,00$ | **HIJAU** | **Sangat Patuh (Risiko Rendah)** | RUP dapat langsung dieksekusi; pemantauan berkala. |
| $70,00 - 84,99$ | **KUNING** | **Cukup Patuh (Risiko Sedang)** | Perlu konsolidasi paket & penyesuaian jenis/metode pengadaan oleh PPK. |
| $50,00 - 69,99$ | **ORANYE** | **Kurang Patuh (Risiko Tinggi)** | Wajib asistensi dan koreksi RUP bersama UKPBJ sebelum pelaksanaan pemilihan. |
| $< 50,00$ | **MERAH** | **Tidak Patuh (Risiko Kritis)** | **Peringatan Keras / Rekomendasi Audit Probity APIP (Inspektorat)**; revisi RUP wajib dilakukan segera sebelum eksekusi anggaran. |

---

## BAGIAN 4: MASTER OUTPUT (SPESIFIKASI PAYLOAD API v3.1, DIFF-TRACKING & LAPORAN)

### 4.1 Kontrak JSON Output Universal (Backend Response Schema v3.1)

    {
      "meta": {
        "batch_id": "STRING (UUIDv4)",
        "previous_batch_id": "STRING | NULL",
        "audited_at": "ISO-8601 DATETIME",
        "engine_version": "3.1-Perpres46-2025-Calibrated",
        "schema_mode": "CORE_12_COLS | EXTENDED_20_COLS",
        "nama_instansi": "STRING (Nama Pemda / K/L)",
        "tahun_anggaran": 2026,
        "total_opd_teraudit": 49,
        "total_paket_keseluruhan": 7803,
        "total_pagu_rup_keseluruhan": 3647625614779,
        "total_pagu_dpa_referensi": 0,
        "persen_cakupan_rup_terhadap_dpa": 100.0
      },
      "macro_institution_summary": {
        "pagu_penyedia": 2851958811251,
        "pagu_swakelola": 795666803528,
        "persen_e_purchasing_penyedia": 33.4,
        "persen_pdn_penyedia": 98.1,
        "persen_umk_terhadap_penyedia_non_dikecualikan": 60.1,
        "persen_umk_eligible_le15m": 99.3,
        "persen_umk_terhadap_total_rup": 47.0,
        "distribusi_temuan_unik": {
          "kritis": { "jumlah_paket": 0, "total_pagu_terdampak": 0 },
          "tinggi": { "jumlah_paket": 0, "total_pagu_terdampak": 0 },
          "sedang": { "jumlah_paket": 0, "total_pagu_terdampak": 0 },
          "rendah": { "jumlah_paket": 0, "total_pagu_terdampak": 0 }
        },
        "rata_rata_rci_instansi": 85.1
      },
      "diff_tracking_summary": {
        "is_comparison_active": true,
        "total_anomali_batch_lama": 0,
        "total_anomali_telah_diperbaiki_fixed": 0,
        "total_anomali_masih_bertahan_persistent": 0,
        "total_anomali_baru_muncul_new": 0,
        "delta_skor_rci_instansi": "+0.0"
      },
      "opd_leaderboard": [
        {
          "kode_satker_sipd": "STRING (22 Digit Kode Organisasi SIPD)",
          "nama_opd_clean": "STRING (Nama Bersih OPD)",
          "total_paket": 0,
          "total_pagu_rup": 0,
          "pagu_dpa_opd": 0,
          "persen_cakupan_dpa": 100.0,
          "pagu_penyedia": 0,
          "pagu_swakelola": 0,
          "persen_epurchasing": 0.0,
          "persen_umk_penyedia": 0.0,
          "persen_umk_eligible_le15m": 0.0,
          "persen_umk_total_rup": 0.0,
          "persen_pdn": 0.0,
          "temuan_kritis": 0,
          "temuan_tinggi": 0,
          "temuan_sedang": 0,
          "pagu_bermasalah_kritis_tinggi": 0,
          "skor_rci": 0.0,
          "predikat_risiko": "HIJAU | KUNING | ORANYE | MERAH",
          "macro_flags": ["R-26.1", "R-26.2", "R-26.3", "R-26.4", "R-32"]
        }
      ],
      "detailed_anomalies": [
        {
          "anomaly_id": "STRING",
          "row_index_csv": 0,
          "diff_status": "NEW | PERSISTENT | FIXED",
          "kode_satker_sipd": "STRING",
          "nama_opd_clean": "STRING",
          "budget_cycle": "MURNI | PERUBAHAN",
          "kode_rup": "STRING",
          "nama_paket": "STRING",
          "nama_paket_objek": "STRING",
          "lokasi_extracted": "STRING | NULL",
          "sumber_dana": "STRING",
          "cara_pengadaan_awal": "STRING",
          "metode_pengadaan_awal": "STRING",
          "jenis_pengadaan_awal": "STRING",
          "status_umk_awal": "STRING",
          "pdn_awal": "STRING",
          "total_nilai": 0,
          "max_severity": "KRITIS | TINGGI | SEDANG | RENDAH",
          "violations": [
            {
              "rule_id": "R-XX",
              "severity": "STRING",
              "regulasi": "STRING",
              "deskripsi_pelanggaran": "STRING",
              "rekomendasi_perbaikan": "STRING",
              "target_perubahan": {
                "cara_pengadaan_saran": "STRING",
                "metode_pengadaan_saran": "STRING",
                "jenis_pengadaan_saran": "STRING",
                "status_umk_saran": "STRING",
                "pdn_saran": "STRING"
              }
            }
          ]
        }
      ],
      "consolidation_and_splitting_matrix": [
        {
          "cluster_id": "STRING",
          "nama_opd_clean": "STRING",
          "budget_cycle": "MURNI | PERUBAHAN",
          "nama_komoditas_klaster": "STRING",
          "lokasi_klaster": "STRING",
          "jenis_pengadaan": "STRING",
          "metode_dominan": "STRING",
          "jumlah_paket_terpecah": 0,
          "nilai_paket_terkecil": 0,
          "nilai_paket_terbesar": 0,
          "total_pagu_klaster": 0,
          "batas_regulasi_pl": 0,
          "kategori_temuan": "INDIKASI_SPLITTING (R-20) | THRESHOLD_HUGGING (R-27) | FRAGMENTASI_PAKET (R-22) | INKONSISTENSI (R-23)",
          "severity": "KRITIS | TINGGI | SEDANG",
          "daftar_kode_rup": ["STRING"],
          "instruksi_konsolidasi": "STRING"
        }
      ]
    }

---

### 4.2 Standar Desain 5 Tampilan Output Sistem (UI & Export Template)

1. **Tampilan Utama: *Multi-OPD Executive Compliance Leaderboard***
   * **Kartu Ringkasan Atas (*KPI Cards*):** Total OPD Teraudit, Total Paket & Pagu RUP vs Cakupan DPA ($\%$), Persentase Capaian UMK (Basis Penyedia Non-Dikecualikan & Basis Paket Eligible $\le \text{Rp}15\text{ Miliar}$) & E-Purchasing, Total Pagu Terdampak Pelanggaran Kritis/Tinggi, dan Rata-rata Skor RCI Instansi.
   * **Tabel Leaderboard OPD:** Menampilkan peringkat OPD dari skor RCI terendah lengkap dengan `kode_satker_sipd` dan `nama_opd_clean`.
2. **Tampilan Kertas Kerja Audit per Baris Paket (*Row-Level Action Sheet*)**
   * Dilengkapi filter cepat berdasarkan Nama OPD, Siklus Anggaran (`MURNI` / `PERUBAHAN`), Status Diff (`NEW`, `PERSISTENT`, `FIXED`), Tingkat Risiko, Kode Rule (`R-01` s.d. `R-34`), dan Sumber Dana.
3. **Tampilan Matriks Deteksi *Splitting*, *Threshold Hugging* & Konsolidasi Paket (*Cluster View*)**
   * **Blok Merah (`R-20` & `R-27`):** Kelompok paket Pengadaan Langsung sejenis yang melampaui ambang batas PL atau berada pada zona $95\% - 100\%$ batas atas PL.
   * **Blok Kuning (`R-22`, `R-23` & `R-32`):** Kelompok komoditas rutin yang terpecah-pecah beserta peringatan paket konstruksi tanpa konsultan pengawasan.
4. **Tampilan *Diff-Tracking & Follow-Up Monitor***
   * Memantau progres tindak lanjut revisi RUP antar-waktu berdasarkan pencocokan `kode_rup` dan kemiripan paket (`FIXED`, `PERSISTENT`, `NEW`, `DELETED`).
5. **Format Ekspor Dokumen Resmi (Excel 5-Sheet & Berita Acara PDF)**
   * Menghasilkan file kerja `Hasil_Audit_RUP_[Nama_Instansi]_[Tanggal].xlsx` ($5$ Sheet) dan `Berita_Acara_Audit_RUP_[Nama_OPD].pdf` siap tanda tangan Kepala UKPBJ / Inspektur Daerah dan Pengguna Anggaran.

---

## BAGIAN 5: LAMPIRAN BENCHMARK KALIBRASI DATA RIIL (7.803 PAKET PROVINSI LAMPUNG TA 2026)

Tabel berikut mendokumentasikan hasil peningkatan presisi mesin audit setelah kalibrasi **Versi 3.1** dibandingkan **Versi 3.0** pada dataset riil Pemerintah Provinsi Lampung Tahun Anggaran 2026:

| Kode Rule / Indikator | Hasil Sebelum Kalibrasi (v3.0) | Hasil Setelah Kalibrasi (v3.1) | Dampak Peningkatan Akurasi Audit |
| :--- | :--- | :--- | :--- |
| **`R-26.1` (Kuota $40\%$ UMK OPD)** | $2$ OPD terkena penalti (Dinas BMBK $30,35\%$ & Biro Kesra $38,98\%$) | **$0$ OPD terkena penalti semu** ($\text{UMK}_{\le 15\text{M}} = 100,00\%$) | Menghilangkan penalti tidak adil pada OPD yang memiliki porsi paket konstruksi/strategis $> \text{Rp}15\text{ Miliar}$ tinggi (wajib Usaha Non-Kecil). |
| **`R-24` (Ambang $\text{Rp}15\text{ Miliar}$ UMK)** | $54$ Paket ($\text{Rp}655,13\text{ Miliar}$) | **$25$ Paket Murni ($\text{Rp}11,69\text{ Miliar}$)** | Mengeliminasi *false positive* sebesar $\text{Rp}643,44\text{ Miliar}$ dari tagihan utilitas `Dikecualikan`, `E-Purchasing` Obat Katalog, dan dana `BOSP-BOS Reguler`. |
| **`R-13` (Swakelola Masuk Penyedia)** | $64$ Paket ($\text{Rp}64,43\text{ Miliar}$) | **$50$ Paket Murni ($\text{Rp}6,05\text{ Miliar}$)** | Mengeliminasi *false positive* sebesar $\text{Rp}58,38\text{ Miliar}$ pada paket Makan Minum Rapat & Cetak yang berada di bawah Sub-Kegiatan `"Sosialisasi..."`. |
| **`R-17` (Salah Jenis Konstruksi)** | $59$ Paket ($\text{Rp}14,52\text{ Miliar}$) | **$12$ Paket Murni ($\text{Rp}1,46\text{ Miliar}$)** | Mengeliminasi *false positive* sebesar $\text{Rp}13,06\text{ Miliar}$ dari paket Publikasi Media Pembangunan TV/Radio dan keterangan dalam kurung `(Rehabilitasi...)`. |
| **`R-16` (Salah Jenis Konsultansi)** | $87$ Paket ($\text{Rp}3,01\text{ Miliar}$) | **$30$ Paket Murni ($\text{Rp}2,40\text{ Miliar}$)** | Mengeliminasi $57$ paket *false positive* pembelian ATK/Makan Minum yang mencantumkan nama kegiatan `(Perencanaan/Pengawasan Teknis...)` di dalam kurung. |
| **`R-21` (Duplikasi Input Paket)** | $279$ Paket ($\text{Rp}11,81\text{ Miliar}$) | **$40$ Paket Signifikan ($\text{Rp}10,32\text{ Miliar}$)** | Memfokuskan temuan `R-21b` pada paket identik beda `kode_rup` bernilai signifikan ($\ge \text{Rp}50\text{ Juta}$) sehingga $87,4\%$ nilai eksposur duplikasi tetap tertangkap tanpa derau paket mikro. |
| **`R-19` (Salah Jenis Barang)** | $23$ Paket ($\text{Rp}366,84\text{ Juta}$) | **$24$ Paket Murni ($\text{Rp}375,44\text{ Juta}$)** | Menangkap paket pembelian ATK yang salah diinput menggunakan jenis pengadaan `"Terintegrasi"`. |