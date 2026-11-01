# PEDOMAN VERIFIKASI MANUAL: KATALOG LENGKAP 40 RULES, LOGIKA ALGORITMA & DASAR REGULASI AUDIT RUP OTOMATIS (VERSI 4.0)

**Nama Sistem:** RUP-Only Compliance & Risk Screening Engine v4.0 (Begawi Pengadaan)  
**Fungsi Dokumen:** Panduan Referensi & Verifikasi Silang Manual (*Manual Cross-Checking Guide*) bagi Auditor Inspektorat (APIP), Biro Pengadaan Barang dan Jasa (UKPBJ), serta PPK/Admin SIRUP OPD.  
**Prinsip Utama:** Disiplin Ketat 12 Kolom CSV RUP (*Strict 12-Column CSV Discipline*). Data RUP adalah objek audit analitik; Regulasi Pengadaan Pemerintah adalah *Single Source of Truth*. Fakta di luar CSV tidak diasumsikan.  
**Dataset Kalibrasi Baseline:** `data_dashboard_rup_table_20261031_063857_0986d8ff.csv` — $7.982$ baris paket, $49$ OPD, Seluruh TA 2026.  
**Status Uji Klinis:** $50/50$ Pengujian Kanonikal & Regresi Lolos ($100\%$ PASSED via `php spark test:rup-audit`).

---

## BAGIAN 1: LANDASAN REGULASI & PARAMETER KETENTUAN YANG BERLAKU

Seluruh logika pemeriksaan di dalam mesin audit didasarkan secara ketat pada hierarki peraturan perundang-undangan berikut:

1. **Peraturan Presiden Nomor 16 Tahun 2018 jo. Peraturan Presiden Nomor 12 Tahun 2021 jo. Peraturan Presiden Nomor 46 Tahun 2025** tentang Pengadaan Barang/Jasa Pemerintah (menggunakan Naskah Konsolidasi Resmi LKPP).
2. **Peraturan Lembaga Kebijakan Pengadaan Barang/Jasa Pemerintah (LKPP) Nomor 3 Tahun 2026** tentang Pedoman Persiapan dan Pelaksanaan Pengadaan Barang/Jasa Pemerintah melalui Penyedia (mencabut Peraturan LKPP Nomor 12 Tahun 2021 jo. Peraturan LKPP Nomor 4 Tahun 2024 sesuai Pasal 11).
3. **Surat Edaran Kepala LKPP Nomor 3 Tahun 2026** tentang Petunjuk Teknis Pelaksanaan Pengadaan Barang/Jasa Pemerintah.
4. **Undang-Undang Nomor 20 Tahun 2023** tentang Aparatur Sipil Negara (khususnya Pasal 65 dan Pasal 66 mengenai penataan dan larangan pengangkatan pegawai non-ASN/honorer).
5. **Peraturan LKPP Nomor 2 Tahun 2026** tentang Penyelenggaraan Katalog Elektronik dan E-Purchasing.
6. **Peraturan LKPP Nomor 11 Tahun 2021** tentang Pedoman Perencanaan Pengadaan Barang/Jasa Pemerintah.
7. **Peraturan LKPP Nomor 3 Tahun 2021** tentang Pedoman Swakelola (Penyelenggaraan Swakelola Tipe I, II, III, dan IV).
8. **Peraturan LKPP Nomor 5 Tahun 2021** tentang Pedoman Pengadaan Barang/Jasa yang Dikecualikan pada Pengadaan Barang/Jasa Pemerintah (BLUD, Tarif Resmi Pemerintah, dan Praktik Bisnis yang Mapan).
9. **Instruksi Presiden Nomor 2 Tahun 2022** tentang Percepatan Peningkatan Penggunaan Produk Dalam Negeri dan Produk Usaha Mikro, Usaha Kecil, dan Koperasi.
10. **Peraturan Pemerintah Nomor 12 Tahun 2019** dan **Permendagri Nomor 77 Tahun 2020** tentang Pedoman Teknis Pengelolaan Keuangan Daerah.

---

### Matriks Parameter Ambang Berdasarkan Tahun Anggaran (`tahun_anggaran`)

Ketika melakukan *cross-check* manual, perhatikan tahun anggaran pada kolom `tahun_anggaran` karena sistem menerapkan penyesuaian otomatis sesuai **ketentuan regulasi yang berlaku**:

| Parameter Regulasi | Ketentuan TA $\le 2024$ (Perpres 12/2021) | Ketentuan Transisi TA 2025 (Pasal 93A Perpres 46/2025) | Ketentuan TA $\ge 2026$ (Perpres 46/2025 jo. Perlem LKPP 3/2026) |
| :--- | :--- | :--- | :--- |
| **Batas Pengadaan Langsung Barang / Jasa Lainnya** | $\le \text{Rp}200.000.000$ | $\le \text{Rp}200.000.000$ | $\le \text{Rp}200.000.000$ (Operator ketat `pagu > batas`) |
| **Batas Pengadaan Langsung Pekerjaan Konstruksi** | $\le \text{Rp}200.000.000$ | $\text{Rp}200\text{M} - \text{Rp}400\text{M}$: `TIDAK_DAPAT_DINILAI_DARI_CSV` (`R-37`) karena bergantung tanggal proses pemilihan | $\le \text{Rp}400.000.000$ (Pasal 38 ayat (3) huruf b Perpres 46/2025) |
| **Batas Pengadaan Langsung Jasa Konsultansi** | $\le \text{Rp}100.000.000$ | $\le \text{Rp}100.000.000$ | $\le \text{Rp}100.000.000$ (Pasal 41 ayat (3) Perpres 46/2025) |
| **Pencadangan Usaha Kecil (Pasal 65 ayat (3))** | $\le \text{Rp}15.000.000.000$ | $\le \text{Rp}15.000.000.000$ | $\le \text{Rp}15.000.000.000$ (Wajib Barang, Konstruksi, Jasa Lainnya; Konsultansi tidak termasuk) |
| **Rentang *Threshold Hugging* Konstruksi (`R-27`)** | $\text{Rp}190\text{ Jt} - \text{Rp}200\text{ Jt}$ | $\text{Rp}190\text{ Jt} - \text{Rp}200\text{ Jt}$ | Tepat Ambang: $\text{Rp}400\text{ Jt}$; Rentang Dekat Ambang: $\text{Rp}380\text{ Jt} - \text{Rp}400\text{ Jt}$ |

---

## BAGIAN 2: LOGIKA PRA-PEMROSESAN DATA 12 KOLOM & EKSTRAKSI NLP

Sistem v4.0 menerapkan **Strict 12-Column CSV Discipline**. Mesin hanya menerima dan memproses 12 header kolom standar SIRUP tanpa mengasumsikan fakta eksternal:

$$\{\text{nama\_instansi, nama\_satuan\_kerja, tahun\_anggaran, cara\_pengadaan, metode\_pengadaan,}$$
$$\text{jenis\_pengadaan, nama\_paket, kode\_rup, sumber\_dana, produk\_dalam\_negeri, status\_umk, total\_nilai}\}$$

### 2.1 Pemisahan Satker & Kode SIPD 22 Digit
* Dari kolom `nama_satuan_kerja` (misal: `"DINAS BINA MARGA DAN BINA KONSTRUKSI - 1.03.0.00.0.00.06.0000"`), diekstrak:
  * `nama_opd_clean` = `"DINAS BINA MARGA DAN BINA KONSTRUKSI"` (kunci pengelompokan OPD).
  * `kode_satker_sipd` = `"1.03.0.00.0.00.06.0000"`.

### 2.2 Pemeliharaan Profil Sumber Dana Jamak (*Multi-Tag Funding Profile*)
* Label sumber dana multi-tag seperti `"APBD;APBDP"` (terdapat $262$ baris pada data riil) atau `"APBD;BLUD"` ($2$ baris) **TIDAK dipecah** menjadi dua baris data dan **TIDAK dipaksakan** menjadi siklus APBD Perubahan semata.
* Sistem membentuk atribut `source_funding_profile`:
  * `"APBD"` $\rightarrow$ `APBD` (Siklus Murni)
  * `"APBDP"` $\rightarrow$ `APBDP` (Siklus Perubahan)
  * `"APBD;APBDP"` $\rightarrow$ `APBD+APBDP` (Profil Multi-Sumber dengan flag `is_multi_source = 1`)
  * `"APBD;BLUD"` $\rightarrow$ `APBD+BLUD`

### 2.3 De-Obfuskasi Leetspeak & Normalisasi Typo (28 Akronim Dilindungi)
* Konversi angka di tengah kata: `4` $\rightarrow$ `a`, `0` $\rightarrow$ `o`, `1` $\rightarrow$ `i`, `3` $\rightarrow$ `e`, `5` $\rightarrow$ `s`.
* Perlindungan 28 akronim teknis: `a4`, `f4`, `s1`, `s2`, `s3`, `d1`, `d3`, `d4`, `p3d`, `p3dw`, `p3jb`, `p3k`, `tps3r`, `sp3or`, `o2sn`, `lp2b`, `sp2d`, `h2m`, `pd3i`, `co2`, `4g`, `5g`, `k1`, `k2`, `k3`, `r2`, `r4`, `l300`.
* Normalisasi typo umum: `belanaja` $\rightarrow$ `belanja`, `runag` $\rightarrow$ `ruang`, `pemeliharan` $\rightarrow$ `pemeliharaan`, `penysunan` $\rightarrow$ `penyusunan`, `interiro` $\rightarrow$ `interior`.

### 2.4 Representasi Ganda Teks Paket: Objek Utama vs Teks Utuh
* `nama_paket_clean`: Teks hasil normalisasi karakter khusus dan leetspeak.
* `nama_paket_objek`: Teks inti belanja setelah membuang judul Sub-Kegiatan di dalam kurung `(...)` atau setelah frasa `"sub kegiatan"`, `"dalam rangka"`, `"pada kegiatan"`. Contoh: *"Belanja Makanan dan Minuman (Sosialisasi Perda)"* $\rightarrow$ *"Belanja Makanan dan Minuman"*.

### 2.5 Ekstraksi Seri Paket, Identitas Lokasi Fisik & Inti Komoditas
* **Nomor Seri Paket (`serial_sequence_number`):** Mendeteksi pola seperti `Paket 1`, `Paket 2`, `Tahap 1`, `Tahap 2`, `TW I`, `TW II`.
* **Identitas Lokasi Fisik (`location_identity_key` & `lokasi_extracted`):** Khusus pekerjaan konstruksi dan pemeliharaan jalan, sistem mengekstrak nomor ruas link (misal `Link 094`), nama kecamatan, desa, jembatan, atau UPTD. Kunci ini memisahkan klaster pemaketan fisik agar tidak terjadi tuduhan palsu pemecahan paket (*false positive splitting*).
* **Inti Komoditas (`nama_paket_core`):** Himpunan kata unik setelah membuang kata awalan generik (`belanja`, `pengadaan`, `pekerjaan`, `biaya`) dan keterangan nomor/lokasi, digunakan untuk perhitungan kemiripan Jaccard.

### 2.6 Sanitasi Kode RUP Notasi Ilmiah & String Murni
* Kolom `kode_rup` selalu diperlakukan sebagai **String Murni**. Sistem mendeteksi kerusakan akibat konversi format ilmiah di Microsoft Excel (seperti `6.84E+07` atau akhiran `.0` / `"00"` berlebihan) dan menandainya sebagai `R-36` (`ANOMALI_DATA`).

---

## BAGIAN 3: REFERENSI 12 KAMUS TAKSONOMI REGULASI

Kamus kata kunci dan pengecualian (*Negative Lookahead*) yang dikalibrasi pada `RupTaxonomy.php`:

1. **`DICT_01_DIKECUALIKAN` (Tarif Resmi Pemerintah & Utilitas):**
   * *Kata Kunci:* tagihan listrik, tagihan air, pdam, pln, telkom, internet, pembayaran pajak, stnk, bpkb, retribusi, benda pos, materai, meterai, pnbp, tera ulang, iuran jaminan kesehatan, iuran bpjs, pppk paruh waktu.
2. **`DICT_02_OUTSOURCING` (Jasa Tenaga Alih Daya / Honorer Non-ASN):**
   * *Kata Kunci:* tenaga pelayanan umum, tenaga kebersihan, cleaning service, tenaga keamanan, satpam, penjaga malam, tenaga sopir, pengemudi, pramubakti, caraka, tenaga administrasi, tenaga teknis, alih daya, outsourcing, pthl, tks, tenaga harian lepas.
   * *Pengecualian:* pppk, pegawai pemerintah dengan perjanjian kerja, iuran jaminan asn.
3. **`DICT_03_SWAKELOLA_MURNI` (Belanja Pegawai & Internal):**
   * *Kata Kunci:* perjalanan dinas, uang harian, honorarium, uang lembur, uang saku, gaji dan tunjangan, bimbingan teknis internal.
   * *Pengecualian:* event organizer, hotel, akomodasi rapat, sewa, pihak ketiga, paket meeting fullboard.
4. **`DICT_04_KATALOG_STANDAR` (Komoditas Standar E-Purchasing):**
   * *Kata Kunci:* alat tulis kantor, atk, bahan cetak, kertas, makan dan minuman, katering, bahan bakar, bbm, pelumas, sewa kendaraan, pemeliharaan kendaraan, pemeliharaan ac, laptop, pc, printer, obat-obatan, bmhp, aspal, beton ready mix, seragam dinas.
5. **`DICT_05_MURNI_KONSTRUKSI` (Pekerjaan Fisik Konstruksi):**
   * *Kata Kunci:* pembangunan, rehabilitasi gedung/jalan, renovasi, rekonstruksi, peningkatan jalan, pemeliharaan berkala jalan, irigasi, drainase, talud, bronjong, sumur bor, jalan dan jembatan.
   * *Pengecualian:* konsultansi, konsultan, perencanaan, pengawasan, manajemen konstruksi, ded, studi, suku cadang, sewa alat, bahan hotmix, rehabilitasi medis, rehabilitasi sosial.
6. **`DICT_06_MURNI_KONSULTANSI` (Jasa Konsultansi):**
   * *Kata Kunci:* jasa konsultansi, perencanaan teknis, pengawasan teknis, manajemen konstruksi, studi kelayakan, feasibility study, ded, masterplan, kajian, naskah akademik, amdal, andalalin, appraisal.
   * *Pengecualian:* penyelenggaraan acara, event organizer, mebel, pelatihan narasumber, kit surveilans, reagen.
7. **`DICT_07_MURNI_JASA_LAINNYA` (Jasa Non-Konsultansi / Operasional):**
   * *Kata Kunci:* belanja sewa, pemeliharaan alat, jasa kebersihan, jasa keamanan, jasa kalibrasi, event organizer, publikasi, iklan, ekspedisi, asuransi, medical check up.
8. **`DICT_08_MURNI_BARANG` (Aset Modal Peralatan & Barang Habis Pakai):**
   * *Kata Kunci:* belanja modal alat, modal komputer, modal kendaraan, suku cadang, bahan kimia, bahan bibit, pupuk, meubelair, alat peraga.
9. **`DICT_09_HIBAH_BANSOS` (Belanja Hibah & Bansos):**
   * *Kata Kunci:* belanja hibah barang, hibah uang, bantuan sosial, bansos, diserahkan kepada masyarakat.
10. **`DICT_10_TEKNOLOGI_TINGGI_IMPOR` (Dominan Impor / Teknologi Khusus):**
    * *Kata Kunci:* bahan kimia laboratorium, reagen khusus, alat laboratorium canggih, server berkapasitas besar, lisensi perangkat lunak luar negeri, mri, ct scan, cathlab.
11. **`DICT_11_PENUNJUKAN_LANGSUNG_SAH` (Kriteria Keadaan Tertentu):**
    * *Kata Kunci:* repeat order, sewa hotel/ruang rapat, penanganan darurat bencana, hak paten, penyedia tunggal, saksi ahli, bantuan hukum.
12. **`DICT_12_OPAQUE_GENERIC` (Penamaan Kabur Tanpa Spesifikasi):**
    * *Kata Kunci:* belanja barang, belanja jasa, belanja modal, belanja operasional, tunda bayar, belanja bahan-bahan lainnya, bopd.

---

## BAGIAN 4: KATALOG LENGKAP 40 ATURAN AUDIT KEPATUHAN & SKRINING RISIKO (`R-01` s.d. `R-40`)

Struktur temuan v4.0 memisahkan secara tegas antara status temuan (`finding_status`), tingkat keparahan (*severity*), dan tingkat keyakinan (*confidence*).

```
finding_status:
  POTENSI_TIDAK_SESUAI        -> Dugaan ketidaksesuaian rencana terhadap batas numerik/larangan regulasi
  PERLU_VERIFIKASI            -> Butuh dokumen pendukung di luar CSV (alasan PLg, katalog live, tipe swakelola)
  SINYAL_RISIKO               -> Indikator pemaketan atau anomali pola yang patut direviu probity
  ANOMALI_DATA                -> Kesalahan format data, angka nol, notasi ilmiah, atribut kosong
  TIDAK_DAPAT_DINILAI_DARI_CSV-> Unsur hukum memerlukan fakta di luar 12 kolom CSV
  INFORMATIF                  -> Penjelasan konteks profil dana campuran, sinyal pengawasan, peluang konsolidasi
```

---

### KELOMPOK A: Validasi Integritas Data Dasar & Transparansi Penamaan

#### 1. Rule `R-01` — Pagu Anggaran Nol atau Negatif
* **Status:** `ANOMALI_DATA` | **Severity:** `TINGGI` | **Confidence:** `TINGGI`
* **Dasar Regulasi:** Pasal 11 Peraturan LKPP Nomor 11 Tahun 2021.
* **Logika:** $\text{total\_nilai} \le 0 \lor \text{total\_nilai IS NULL}$.
* **Batas Inferensi:** Menguji angka pagu tercantum pada CSV; tidak membuktikan pembatalan DPA aktual.
* **Rekomendasi:** Lengkapi pagu RUP sesuai DPA atau hapus baris paket jika tidak dilaksanakan.

#### 2. Rule `R-02` — Atribut Paket Penyedia Tidak Lengkap
* **Status:** `ANOMALI_DATA` | **Severity:** `TINGGI` | **Confidence:** `TINGGI`
* **Dasar Regulasi:** Pasal 22 Perpres 16/2018 jo. Perpres 46/2025.
* **Logika:** $\text{cara\_pengadaan} = \text{"Penyedia"} \land (\text{metode} \in \{\text{"-"}, \text{""}\} \lor \text{jenis} \in \{\text{"-"}, \text{""}\})$.
* **Rekomendasi:** Tentukan metode pemilihan dan jenis pengadaan yang definitif pada SIRUP.

#### 3. Rule `R-03` — Anomali Pagu Ekstrem Rendah ($< \text{Rp}10.000$)
* **Status:** `ANOMALI_DATA` | **Severity:** `SEDANG` | **Confidence:** `TINGGI`
* **Dasar Regulasi:** Prinsip Akuntabilitas Data RUP (Perlem LKPP 11/2021).
* **Logika:** $0 < \text{total\_nilai} < 10.000$.
* **Rekomendasi:** Periksa kesalahan pengetikan nominal (misal nilai juta hanya diketik satuan).

#### 4. Rule `R-34` — Penamaan Paket Terlalu Kabur / Generik (*Opaque Naming*)
* **Status:** `ANOMALI_DATA` | **Severity:** `SEDANG` | **Confidence:** `SEDANG`
* **Dasar Regulasi:** Prinsip Transparansi Pasal 6 Perpres PBJ & Perlem LKPP 11/2021.
* **Logika:** $\text{LEN}(\text{nama\_paket\_objek}) < 6 \lor \text{MATCH}(\text{nama\_paket\_objek}, \text{DICT\_12\_OPAQUE})$.
* **Pengecualian:** Nama singkat yang memuat kata benda komoditas spesifik (Laptop, Mebel, Excavator, Ambulance) tidak melanggar.
* **Rekomendasi:** Perjelas uraian paket dengan menyebutkan spesifikasi komoditas atau output pekerjaan.

#### 5. Rule `R-35` — Nilai Enum Kolom CSV Tidak Dikenal
* **Status:** `ANOMALI_DATA` | **Severity:** `SEDANG` | **Confidence:** `TINGGI`
* **Dasar Regulasi:** Standar Skema Ekspor SIRUP LKPP.
* **Logika:** Nilai `cara_pengadaan`, `produk_dalam_negeri`, atau `status_umk` di luar himpunan baku.
* **Rekomendasi:** Perbaiki opsi dropdown atribut pada aplikasi SIRUP.

#### 6. Rule `R-36` — Duplikasi atau Kerusakan Kode RUP Format Ilmiah
* **Status:** `ANOMALI_DATA` | **Severity:** `TINGGI` | **Confidence:** `TINGGI`
* **Dasar Regulasi:** Standar Integritas ID Paket INAPROC.
* **Logika:** `kode_rup` terpotong notasi `E+`, berakhiran `.0`, atau duplikat lintas baris berbeda.
* **Rekomendasi:** Format kolom Kode RUP sebagai teks (string) saat ekspor CSV di spreadsheet.

---

### KELOMPOK B: Ambang Batas Pengadaan Langsung, Kesesuaian Metode & Ketentuan Regulasi

#### 7. Rule `R-04` — Pelampauan Batas Pengadaan Langsung Barang/Jasa Lainnya ($> \text{Rp}200\text{ Juta}$)
* **Status:** `POTENSI_TIDAK_SESUAI` | **Severity:** `TINGGI` | **Confidence:** `TINGGI`
* **Dasar Regulasi:** Pasal 38 ayat (3) huruf a Perpres 16/2018 jo. Perpres 46/2025.
* **Logika:** $\text{"Pengadaan Langsung"} \in \text{metode\_arr} \land (\text{Barang} \lor \text{Jasa Lainnya}) \land \text{Konstruksi} \notin \text{jenis\_arr} \land \text{total\_nilai} > 200.000.000$. Operator ketat `total_nilai > batas`. Paket BLUD tetap dievaluasi disertai konteks `R-30`.
* **Kalibrasi Real Data:** Tepat $5\text{ paket Barang}$ ($\text{Rp}1.793.521.200$) dan $38\text{ paket Jasa Lainnya}$ ($\text{Rp}19.194.102.856$).
* **Batas Inferensi:** Menguji rencana pada RUP; bukan bukti bahwa kontrak telah diterbitkan via Pengadaan Langsung.
* **Rekomendasi:** Ubah metode menjadi **E-Purchasing** (Katalog V6) atau **Tender / Tender Cepat**.

#### 8. Sub-Rule `R-04b` — Sinyal SPK Transaksional SPSE Jasa Lainnya Perorangan ($> \text{Rp}50\text{ Juta}$)
* **Status:** `PERLU_VERIFIKASI` | **Severity:** `INFO` | **Confidence:** `TINGGI`
* **Dasar Regulasi:** Pasal 3 ayat (3) dan ayat (5) Peraturan LKPP Nomor 3 Tahun 2026.
* **Logika:** Pengadaan Langsung Jasa Lainnya $\text{Rp}50\text{ Juta} < \text{total\_nilai} \le \text{Rp}200\text{ Juta}$ untuk jasa tenaga/perorangan.
* **Batas Inferensi:** CSV tidak memuat bukti transaksi SPSE atau berita acara reviu APIP.
* **Rekomendasi:** Pastikan menggunakan Fitur Transaksional SPSE dan mengantongi Reviu APIP jika berbentuk jasa perorangan.

#### 9. Rule `R-05` — Pelampauan Batas Pengadaan Langsung Pekerjaan Konstruksi ($> \text{Rp}400\text{ Juta}$)
* **Status:** `POTENSI_TIDAK_SESUAI` | **Severity:** `TINGGI` | **Confidence:** `TINGGI`
* **Dasar Regulasi:** Pasal 38 ayat (3) huruf b Perpres 46/2025 (TA $\ge 2025$) atau Pasal 38 Perpres 12/2021 (TA $\le 2024$).
* **Logika:** $\text{"Pengadaan Langsung"} \in \text{metode\_arr} \land \text{"Pekerjaan Konstruksi"} \in \text{jenis\_arr} \land \text{total\_nilai} > \text{TH\_BATAS\_PL}$.
* **Kalibrasi Real Data:** Tepat $2\text{ paket Konstruksi}$ ($\text{Rp}1.840.069.500$) pada RSJ Lampung (termasuk 1 paket BLUD Rp1,37M).
* **Rekomendasi:** Ubah metode pemilihan menjadi **Tender** atau **E-Purchasing Konstruksi**.

#### 10. Rule `R-06` — Pelampauan Batas Pengadaan Langsung Jasa Konsultansi ($> \text{Rp}100\text{ Juta}$)
* **Status:** `POTENSI_TIDAK_SESUAI` | **Severity:** `TINGGI` | **Confidence:** `TINGGI`
* **Dasar Regulasi:** Pasal 41 ayat (3) Perpres 16/2018 jo. Perpres 46/2025.
* **Logika:** $\text{"Pengadaan Langsung"} \in \text{metode\_arr} \land \text{"Jasa Konsultansi"} \in \text{jenis\_arr} \land \text{total\_nilai} > 100.000.000$.
* **Kalibrasi Real Data:** Tepat $18\text{ paket Konsultansi}$ ($\text{Rp}4.879.867.650$).
* **Rekomendasi:** Ubah metode menjadi **Seleksi** atau **E-Purchasing Konsultansi**.

#### 11. Rule `R-07` — Penelaahan PL Konstruksi $\text{Rp}200\text{ Juta} - \text{Rp}400\text{ Juta}$ Struktur Berat
* **Status:** `PERLU_VERIFIKASI` | **Severity:** `SEDANG` | **Confidence:** `SEDANG`
* **Dasar Regulasi:** Pasal 38 ayat (3) huruf b Perpres 46/2025 jo. Bagian 5.3 Master Plan v4.0.
* **Catatan Kalibrasi:** Seluruh 70 paket PL Konstruksi Rp200–400 Jt pada baseline Lampung berstatus `UMK = Ya` dan **tidak melanggar** ambang batas. Rule `R-07` hanya memicu catatan verifikasi apabila objek paket eksplisit menyebut struktur berat (`jembatan`, `bendung`, `tiang pancang`, `bore pile`, `bunker`).
* **Rekomendasi:** Konfirmasi apakah pekerjaan termasuk kategori risiko kecil dan teknologi sederhana.

#### 12. Rule `R-08` — Penunjukan Langsung Perlu Verifikasi Dokumen Kriteria Keadaan Tertentu
* **Status:** `PERLU_VERIFIKASI` | **Severity:** `SEDANG` | **Confidence:** `SEDANG`
* **Dasar Regulasi:** Pasal 38 ayat (4)-(5) & Pasal 41 ayat (4)-(5) Perpres 46/2025 jo. Lampiran I Perlem LKPP 3/2026.
* **Logika:** Metode `Penunjukan Langsung` di luar kamus tarif resmi `DICT_01`.
* **Batas Inferensi:** CSV tidak memiliki kolom alasan; sistem **tidak menuduh pelanggaran hukum**, melainkan meminta verifikasi kriteria keadaan tertentu pada dokumen internal PPK.
* **Kewenangan Pelaksana:** Pagu $\le \text{Rp}200\text{ Jt}$ (B/PK/JL) / $\le \text{Rp}100\text{ Jt}$ (JK) oleh Pejabat Pengadaan; di atasnya wajib Pokja Pemilihan UKPBJ.

#### 13. Sub-Rule `R-08b` — Syarat Uji Repeat Order Penunjukan Langsung
* **Status:** `TIDAK_DAPAT_DINILAI_DARI_CSV` | **Severity:** `INFO` | **Confidence:** `TINGGI`
* **Dasar Regulasi:** Pasal 38A & Pasal 41A Perpres 46/2025 jo. Lampiran I Perlem LKPP 3/2026.
* **Logika:** Terpicu bila nama paket memuat teks *"repeat order"*.
* **Batas Inferensi:** Lima syarat kumulatif RO (tender awal, nilai kontrak awal, kinerja Sangat Baik di SIKaP, batas 3 tahun, kesamaan tim leader) tidak dapat diuji dari CSV.

#### 14. Rule `R-09` — Inkompatibilitas Metode Pemilihan terhadap Jenis Pengadaan
* **Status:** `POTENSI_TIDAK_SESUAI` | **Severity:** `TINGGI` | **Confidence:** `TINGGI`
* **Dasar Regulasi:** Pasal 38 & Pasal 41 Perpres 46/2025.
* **Logika:** Seleksi pada non-Konsultansi, Tender pada murni Konsultansi, atau Kontes pada komoditas standar.
* **Rekomendasi:** Sesuaikan metode kompetitif: **Seleksi** untuk Konsultansi, **Tender** untuk Barang/Konstruksi/Jasa Lainnya.

#### 15. Rule `R-27a` — Pagu Pengadaan Langsung Tepat pada Ambang Batas (*Exact Threshold*)
* **Status:** `SINYAL_RISIKO` | **Severity:** `SEDANG` | **Confidence:** `TINGGI`
* **Dasar Regulasi:** Pasal 20 ayat (2) Perpres 16/2018 jo. Perpres 46/2025.
* **Logika:** $\text{total\_nilai} == 200.000.000$ (Barang/JL), $== 400.000.000$ (Konstruksi), $== 100.000.000$ (Konsultansi).
* **Kalibrasi Real Data:** Tepat $182\text{ paket}$ di dataset Lampung berada tepat di batas ambang.
* **Batas Inferensi:** Sinyal risiko probity penetapan pagu; bukan bukti pemecahan paket jika berdiri sendiri.

#### 16. Rule `R-27b` — Pagu Mepet Batas Atas Pengadaan Langsung (*Threshold Hugging* $95\% - <100\%$)
* **Status:** `SINYAL_RISIKO` | **Severity:** `RENDAH` | **Confidence:** `TINGGI`
* **Dasar Regulasi:** Pasal 20 ayat (2) Perpres 16/2018 jo. Perpres 46/2025.
* **Logika:** $95\% \times \text{Batas} \le \text{total\_nilai} < \text{Batas}$.
* **Kalibrasi Real Data:** Tepat $27\text{ paket}$ (Total 209 paket bersama R-27a).
* **Rekomendasi:** Periksa HPS dan rincian kertas kerja anggaran untuk memastikan tidak dipatok sengaja demi menghindari tender.

#### 16b. Rule `R-27c` — Sub-Threshold Hugging Konstruksi TA $\ge 2025$ ($\text{Rp}190\text{ Jt} - \text{Rp}200\text{ Jt}$)
* **Status:** `SINYAL_RISIKO` | **Severity:** `RENDAH` | **Confidence:** `TINGGI`
* **Dasar Regulasi:** Pasal 20 ayat (2) Perpres 16/2018 jo. Perpres 46/2025.
* **Logika:** $\text{tahun\_anggaran} \ge 2025 \land \text{metode} == \text{"Pengadaan Langsung"} \land \text{jenis} == \text{"Pekerjaan Konstruksi"} \land \text{total\_nilai} \in [\text{Rp}190.000.000, \text{Rp}200.000.000]$.
* **Kalibrasi Real Data:** Menangkap tepat **$142\text{ paket}$ senilai $\text{Rp}28.072.331.946$** (terkonsentrasi di Dinas BMBK dan Dinas PKPCK) yang dipatok tepat di bawah batas lama regulasi Perpres 12/2021 ($\text{Rp}200\text{ Juta}$), meskipun pada TA 2026 batas Pengadaan Langsung Pekerjaan Konstruksi telah dinaikkan menjadi $\text{Rp}400\text{ Juta}$.
* **Rekomendasi:** Periksa apakah pemaketan didasarkan pada kebiasaan pagu lama atau indikasi pemecahan paket pekerjaan fisik.

#### 17. Rule `R-28` — Inefisiensi Metode Pemilihan Kompetitif pada Pagu Kecil
* **Status:** `SINYAL_RISIKO` | **Severity:** `RENDAH` | **Confidence:** `SEDANG`
* **Dasar Regulasi:** Prinsip Efisiensi Pengadaan (Pasal 6 Perpres PBJ).
* **Logika:** Tender $\le \text{Rp}200\text{ Jt}$ atau Seleksi $\le \text{Rp}100\text{ Jt}$.
* **Rekomendasi:** Pertimbangkan penyederhanaan melalui Pengadaan Langsung atau E-Purchasing.

#### 18. Rule `R-29` — Penelaahan Penggunaan Metode Tender Cepat
* **Status:** `POTENSI_TIDAK_SESUAI` | **Severity:** `TINGGI` | **Confidence:** `TINGGI`
* **Dasar Regulasi:** Pasal 38 ayat (4) Perpres 46/2025 jo. Perlem LKPP 3/2026.
* **Logika:** Tender Cepat pada Pekerjaan Konstruksi atau Jasa Konsultansi.
* **Rekomendasi:** Gunakan **Tender** biasa (Konstruksi) atau **Seleksi** (Konsultansi).

#### 19. Rule `R-33` — Penelaahan Jenis Pengadaan `Terintegrasi`
* **Status:** `PERLU_VERIFIKASI` | **Severity:** `SEDANG` | **Confidence:** `TINGGI`
* **Dasar Regulasi:** Pasal 54 Perpres 46/2025.
* **Logika:** Terintegrasi memakai Pengadaan Langsung, atau komoditas standar (ATK/Mamin) salah diklik sebagai Terintegrasi.
* **Rekomendasi:** Koreksi jenis pengadaan ke jenis tunggal yang sesuai (Barang/Konstruksi).

#### 20. Rule `R-37` — Ketentuan Regulasi Transisi TA 2025 PL Konstruksi $\text{Rp}200\text{M} - \text{Rp}400\text{M}$
* **Status:** `TIDAK_DAPAT_DINILAI_DARI_CSV` | **Severity:** `INFO` | **Confidence:** `TINGGI`
* **Dasar Regulasi:** Pasal 93A Perpres 46/2025 & Ketentuan Peralihan.
* **Logika:** TA 2025, Konstruksi, Pengadaan Langsung, $\text{Rp}200\text{ Jt} < \text{total\_nilai} \le \text{Rp}400\text{ Jt}$.
* **Batas Inferensi:** Keabsahannya bergantung apakah proses pengadaan dimulai sebelum atau sesudah berlakunya Perpres 46/2025 tanggal 30 April 2025. Tanggal proses tidak tersedia di CSV.

---

### KELOMPOK C: Validasi Cara Pengadaan, Larangan Non-ASN & Sumber Dana

#### 21. Rule `R-10` — Salah Metode untuk Komoditas Tarif Resmi / Pajak / Utilitas
* **Status:** `PERLU_VERIFIKASI` | **Severity:** `SEDANG` | **Confidence:** `TINGGI`
* **Dasar Regulasi:** Pasal 62 Perpres 16/2018 jo. Perpres 46/2025 & Peraturan LKPP Nomor 5 Tahun 2021.
* **Logika:** Listrik, air, telepon, internet langganan, pajak kendaraan, bea izin, materai diinput memakai Pengadaan Langsung/Tender.
* **Rekomendasi:** Ubah metode menjadi **Dikecualikan** atau catat di Swakelola (Pembayaran Utilitas).

#### 22. Rule `R-11` — Pengadaan Jasa Tenaga / Honorer di Swakelola (Review Kepatuhan UU ASN)
* **Status:** `PERLU_VERIFIKASI` | **Severity:** `SEDANG` | **Confidence:** `TINGGI`
* **Dasar Regulasi:** **Pasal 65–66 UU Nomor 20 Tahun 2023 tentang ASN** & Peraturan LKPP Nomor 3 Tahun 2021.
* **Logika:** Cara Swakelola memuat kata kunci alih daya/tenaga kerja (satpam, sopir, kebersihan, caraka, tenaga administrasi non-ASN, PTHL, TKS). Pengecualian: PPPK Paruh Waktu tidak melanggar.
* **Batas Inferensi:** Hubungan kerja dan SK kepegawaian tidak ada di CSV; sistem tidak menyatakan pelanggaran hukum pidana/administrasi mutlak.
* **Rekomendasi:** Jika menggunakan pihak ketiga penyedia tenaga alih daya, ubah menjadi **Penyedia (Jasa Lainnya)**.

#### 23. Rule `R-12` — Pekerjaan Pihak Ketiga / Belanja Modal Masuk Swakelola
* **Status:** `PERLU_VERIFIKASI` | **Severity:** `SEDANG` | **Confidence:** `SEDANG`
* **Dasar Regulasi:** Peraturan LKPP Nomor 3 Tahun 2021 tentang Pedoman Swakelola.
* **Logika:** Swakelola memuat pekerjaan fisik konstruksi, konsultansi, belanja modal pihak ketiga tanpa atribut tipe swakelola.
* **Rekomendasi:** Lengkapi tipe swakelola (Tipe II/III/IV) atau pindahkan ke penyedia.

#### 24. Rule `R-13` — Belanja Pegawai / Internal Masuk Pemilihan Penyedia
* **Status:** `PERLU_VERIFIKASI` | **Severity:** `SEDANG` | **Confidence:** `SEDANG`
* **Dasar Regulasi:** Peraturan LKPP Nomor 11 Tahun 2021.
* **Logika:** Perjalanan dinas biasa, honorarium internal, atau uang lembur diinput di Penyedia tanpa metode Dikecualikan.
* **Rekomendasi:** Pindahkan ke mekanisme Swakelola atau metode Dikecualikan.

#### 25. Rule `R-14` — Validasi Belanja Hibah / Bantuan Sosial (Barang vs Uang)
* **Status:** `PERLU_VERIFIKASI` | **Severity:** `SEDANG` | **Confidence:** `SEDANG`
* **Dasar Regulasi:** Permendagri Nomor 77 Tahun 2020.
* **Logika:** Hibah barang dicatat di Swakelola, atau hibah uang transfer NPHD dicatat di Penyedia.
* **Rekomendasi:** Hibah barang diadakan via **Penyedia**; hibah uang dicatat di **Swakelola**.

#### 26. Rule `R-30` — Konteks Khusus Pagu Sumber Dana BLUD & DAK
* **Status:** `PERLU_VERIFIKASI` | **Severity:** `INFO` | **Confidence:** `TINGGI`
* **Dasar Regulasi:** Pasal 61 Perpres 46/2025 jo. Peraturan LKPP Nomor 5 Tahun 2021.
* **Logika:** Paket berlabel BLUD yang memakai metode Pengadaan Langsung di atas ambang regulasi umum.
* **Batas Inferensi:** Perlu verifikasi apakah RSUD/BLUD bersangkutan memiliki Perkada Jenjang Nilai Pengadaan Khusus BLUD.

---

### KELOMPOK D: E-Purchasing Katalog, Klasifikasi Jenis & Disiplin Inferensi CSV

#### 27. Rule `R-15` — Potensi Komoditas Tersedia pada Katalog Elektronik V6
* **Status:** `PERLU_VERIFIKASI` | **Severity:** `INFO` | **Confidence:** `SEDANG`
* **Dasar Regulasi:** Pasal 38 ayat (2) & Pasal 50 ayat (5) Perpres 46/2025 jo. PerLKPP 2/2026.
* **Logika:** Komoditas rutin umum (ATK, cetak, mamin rapat, BBM, pemeliharaan kendaraan/AC, obat/BMHP) memakai PL/Tender.
* **Batas Inferensi:** Sistem tidak memiliki data ketersediaan tayang produk katalog aktual pada CSV.
* **Rekomendasi:** Periksa etalase Katalog Elektronik V6; utamakan E-Purchasing jika barang/jasa tersedia.

#### 28. Rule `R-16` — Salah Klasifikasi Jasa Konsultansi & Modus Penghindaran Seleksi
* **Status:** `POTENSI_TIDAK_SESUAI` / `PERLU_VERIFIKASI` | **Severity:** `TINGGI` (bila PL $> \text{Rp}100\text{ Jt}$) / `SEDANG` | **Confidence:** `TINGGI`
* **Dasar Regulasi:** Pasal 3, Pasal 38, & Pasal 41 Perpres 16/2018 jo. Perpres 46/2025 jo. Perlem LKPP 3/2026.
* **Logika:** Perencanaan teknis, pengawasan konstruksi, DED, kajian, studi kelayakan diinput sebagai non-Konsultansi (Barang/JL/Konstruksi).
* **Eskalasi Presisi (Patch v4.0.1):** Jika metode pengadaan memuat `'Pengadaan Langsung'` **DAN** $\text{total\_nilai} > \text{Rp}100.000.000$, rule otomatis dinaikkan statusnya menjadi **`POTENSI_TIDAK_SESUAI` (`severity = TINGGI`, `confidence = TINGGI`)**. Hal ini menutup celah pelolosan di mana paket konsultansi murni disamarkan sebagai 'Jasa Lainnya' agar lolos dari batasan PL Konsultansi Rp100 Juta dan lolos dari batasan PL Jasa Lainnya Rp200 Juta (menangkap **4 paket senilai $\text{Rp}1.015.334.000$** di SIRUP).
* **Rekomendasi:** Batalkan Pengadaan Langsung dan koreksi jenis menjadi **Jasa Konsultansi** serta gunakan metode **Seleksi**.

#### 29. Rule `R-17` — Salah Klasifikasi Pekerjaan Konstruksi Tertulis Barang/JL/Konsultansi
* **Status:** `POTENSI_TIDAK_SESUAI` | **Severity:** `TINGGI` | **Confidence:** `TINGGI`
* **Dasar Regulasi:** Pasal 3 Perpres 16/2018 jo. Perpres 46/2025.
* **Logika:** Pembangunan, rehabilitasi fisik gedung/jalan/irigasi tertulis Barang/JL.
* **Rekomendasi:** Koreksi jenis pengadaan menjadi **Pekerjaan Konstruksi**.

#### 30. Rule `R-18` — Salah Klasifikasi Jasa Lainnya (Sewa Alat, Pemeliharaan, Kebersihan)
* **Status:** `PERLU_VERIFIKASI` | **Severity:** `SEDANG` | **Confidence:** `TINGGI`
* **Dasar Regulasi:** Pasal 3 Perpres 16/2018 jo. Perpres 46/2025.
* **Logika:** Objek berupa jasa non-intelektual (sewa alat, pemeliharaan, kebersihan, keamanan) pada paket Penyedia non-Dikecualikan yang diinput dengan jenis **bukan Jasa Lainnya** (baik tertulis Barang maupun Pekerjaan Konstruksi).
* **Kalibrasi Real Data (Patch v4.0.1):** Menangkap **143 paket senilai $\text{Rp}66,31\text{ Miliar}$**, termasuk secara spesifik **52 paket senilai $\text{Rp}6,96\text{ Miliar}$** berupa *"Pengadaan Sewa Alat Pemeliharaan Rutin Jalan... (Swakelola)"* di Dinas BMBK yang keliru diinput sebagai Pekerjaan Konstruksi.
* **Rekomendasi:** Koreksi jenis pengadaan menjadi **Jasa Lainnya** (ambang batas PL tetap $\text{Rp}200\text{ Juta}$).

#### 31. Rule `R-19` — Salah Klasifikasi Barang (Bahan Rutin, Material Fisik, Komputer)
* **Status:** `PERLU_VERIFIKASI` | **Severity:** `SEDANG` | **Confidence:** `TINGGI`
* **Dasar Regulasi:** Pasal 3 Perpres 16/2018 jo. Perpres 46/2025.
* **Logika:** Pembelian aset komoditas fisik, material bangunan/jalan, bahan rutin, komputer, ATK pada paket Penyedia non-Dikecualikan yang diinput dengan jenis **bukan Barang** (termasuk Pekerjaan Konstruksi atau Jasa Lainnya).
* **Kalibrasi Real Data (Patch v4.0.1):** Menangkap **229 paket senilai $\text{Rp}33,63\text{ Miliar}$**, termasuk secara spesifik **170 s.d. 205 paket senilai $\text{Rp}29,30 - 35,13\text{ Miliar}$** berupa *"Pengadaan Bahan Rutin / Bahan Material... (Swakelola)"* di Dinas BMBK yang keliru diinput sebagai Pekerjaan Konstruksi.
* **Rekomendasi:** Koreksi jenis pengadaan menjadi **Barang** (ambang batas PL adalah $\text{Rp}200\text{ Juta}$, bukan batas konstruksi $\text{Rp}400\text{ Juta}$).

#### 32. Rule `R-31` — Kronologi Jadwal Pengumuman & Kontrak (`NON-AKTIF`)
* **Status:** `TIDAK_DAPAT_DINILAI_DARI_CSV` | **Severity:** `INFO` | **Confidence:** `TINGGI`
* **Batas Inferensi:** 12 kolom inti CSV RUP tidak memuat kolom tanggal pengumuman, pemilihan, atau kontrak. Dinonaktifkan penuh demi kepatuhan terhadap kontrak data.

#### 33. Rule `R-40` — Filter Konflik Klasifikasi Kata Kunci (Penurun Confidence)
* **Status:** `INFORMATIF` | **Severity:** `INFO` | **Confidence:** `TINGGI`
* **Logika:** Menurunkan confidence temuan klasifikasi apabila nama paket mengandung kata kunci ambigu lintas kamus yang kontradiktif.

---

### KELOMPOK E: Deteksi Pemecahan Paket (*Splitting*), Duplikasi & Klastering Graf

#### 34. Rule `R-20` — Indikasi Pemecahan Paket (*Serial Splitting*) Melampaui Batas Tender/Seleksi
* **Status:** `SINYAL_RISIKO` | **Severity:** `TINGGI` | **Confidence:** `TINGGI`
* **Dasar Regulasi:** Pasal 20 ayat (2) & Pasal 21 Perpres 16/2018 jo. Perpres 46/2025.
* **Syarat Mutlak Terpicu (8 Kondisi Kumulatif):**
  1. Satker (OPD) sama;
  2. Jenis pengadaan sama;
  3. Cara pengadaan Penyedia dan metode masing-masing paket sama-sama `Pengadaan Langsung`;
  4. Profil sumber dana sama (`source_funding_profile`);
  5. Objek komoditas inti sangat mirip ($\text{Jaccard Similarity} \ge 0,78$);
  6. **Lolos Uji Centroid & Homogenitas:** Rerata kemiripan anggota klaster $\ge 0,70$ untuk mencegah rantai transitif $A-B-C$ yang tidak seragam;
  7. **Pembeda Lokasi Fisik Terisolasi:** Khusus konstruksi, paket beda ruas/lokasi fisik dipisahkan oleh `location_identity_key`;
  8. Akumulasi pagu seluruh paket dalam klaster **melampaui ambang Pengadaan Langsung** ($\text{Rp}200\text{M}$ Barang/JL, $\text{Rp}400\text{M}$ Konstruksi, $\text{Rp}100\text{M}$ Konsultansi).
* **Batas Inferensi:** Output berbunyi *"indikasi pemaketan yang memerlukan reviu"*; bukan kesimpulan pidana/pelanggaran mutlak karena justifikasi teknis dan waktu pelaksanaan tidak ada di CSV.
* **Rekomendasi:** Lakukan konsolidasi pengadaan menjadi paket kompetitif (Tender/Seleksi) atau E-Purchasing pada tahun anggaran berikutnya.

#### 35. Sub-Rule `R-21a` — Duplikasi Teknis Kode RUP & Kerusakan Format Angka
* **Status:** `ANOMALI_DATA` | **Severity:** `TINGGI` | **Confidence:** `TINGGI`
* **Logika:** $>1$ baris menggunakan Kode RUP yang persis sama pada satu batch.

#### 36. Sub-Rule `R-21b` — Kandidat Duplikasi Paket Identik Lintas Kode RUP
* **Status:** `SINYAL_RISIKO` | **Severity:** `SEDANG` | **Confidence:** `SEDANG`
* **Logika:** Paket bernilai $\ge \text{Rp}50\text{ Juta}$ di OPD yang sama dengan nama paket dan nilai pagu persis sama namun Kode RUP berbeda.
* **Rekomendasi:** Konfirmasi apakah merupakan dobel input data di SIRUP atau paket lanjutan sah.

#### 37. Rule `R-22` — Peluang Konsolidasi Paket Sejenis ($\ge 3$ Paket Rutin)
* **Status:** `INFORMATIF` | **Severity:** `INFO` | **Confidence:** `TINGGI`
* **Dasar Regulasi:** Pasal 21 Perpres 46/2025 jo. Perlem LKPP 3/2026.
* **Logika:** Muncul pada kelompok $\ge 3$ paket rutin bernilai kecil tanpa indikasi pemecahan pagu di atas ambang. Menghasilkan saran penggabungan demi diskon volume (*bulk purchase*).

#### 38. Rule `R-23` — Inkonsistensi Klasifikasi Jenis/Metode pada Paket Bernama Serupa
* **Status:** `PERLU_VERIFIKASI` | **Severity:** `SEDANG` | **Confidence:** `SEDANG`
* **Logika:** Paket dengan nama normalisasi identik di satu OPD diinput dengan metode/jenis yang berbeda-beda.

#### 39. Rule `R-32` — Sinyal Pengawasan Konstruksi Mandiri / Tanpa Konsultan Pengawas
* **Status:** `INFORMATIF` | **Severity:** `INFO` | **Confidence:** `TINGGI`
* **Dasar Regulasi:** Pasal 7 ayat (1) dan ayat (5) Peraturan LKPP Nomor 3 Tahun 2026.
* **Logika:** OPD memiliki pekerjaan konstruksi signifikan namun tidak tercatat paket Jasa Konsultansi Pengawasan pada CSV.
* **Batas Inferensi:** Menginformasikan kewajiban SK Tim Teknis Pengawas Internal jika pengawasan tidak dipihakketigakan; bukan tuduhan pekerjaan tanpa pengawasan.

#### 40. Rule `R-38` — Deteksi Konteks Serial & Lokasi Pemaketan Bertahap
* **Status:** `SINYAL_RISIKO` | **Severity:** `SEDANG` | **Confidence:** `TINGGI`
* **Logika:** Terpicu bila klaster paket memuat penomoran urut (`Paket 1`, `Paket 2`) atau tahapan yang perlu diverifikasi kesatuan perencanaannya.

#### 41. Rule `R-39` — Nama Paket Berulang Lintas Profil Sumber Dana
* **Status:** `SINYAL_RISIKO` | **Severity:** `SEDANG` | **Confidence:** `TINGGI`
* **Logika:** Paket dengan nama serupa muncul berulang kali di sumber dana berbeda (misal di APBD Murni dan APBD Perubahan).

---

### KELOMPOK F: Afirmasi Usaha Kecil (UMK), PDN & Indikator Makro OPD

#### 42. Rule `R-24` — Kepatuhan Pencadangan Paket Usaha Kecil / Koperasi ($\le \text{Rp}15\text{ Miliar}$)
* **Status:** `POTENSI_TIDAK_SESUAI` | **Severity:** `TINGGI` | **Confidence:** `TINGGI`
* **Dasar Regulasi:** **Pasal 65 ayat (3) Perpres 16/2018 jo. Perpres 46/2025**.
* **Ketentuan Cakupan:** Wajib berlaku untuk **3 jenis pengadaan**: `Barang`, `Pekerjaan Konstruksi`, dan `Jasa Lainnya` bernilai pagu $\le \text{Rp}15.000.000.000$ (hanya `Jasa Konsultansi` yang tidak termasuk cakupan Pasal 65 ayat (3)). Pengecualian metode `Dikecualikan`.
* **Kondisi Terpicu:**
  $$\text{jenis} \in \{\text{"Barang"}, \text{"Pekerjaan Konstruksi"}, \text{"Jasa Lainnya"}\} \land \text{metode} \neq \text{"Dikecualikan"} \land \text{total\_nilai} \le 15.000.000.000 \land \text{status\_umk} \neq \text{"Ya"}$$
* **Kalibrasi Real Data:** Tepat $24\text{ paket}$ ($\text{Rp}11.450.777.996$) di dataset baseline Lampung. Seluruh paket Pekerjaan Konstruksi $\le \text{Rp}15\text{M}$ pada baseline Lampung telah berstatus `UMK = Ya`.
* **Batas Inferensi:** CSV tidak memuat data justifikasi pengecualian kemampuan teknis Usaha Kecil sesuai Pasal 65 ayat (4); temuan berstatus kandidat pemeriksaan.
* **Rekomendasi:** Periksa apakah paket dapat dicadangkan untuk penyedia kualifikasi Usaha Kecil/Koperasi.

#### 42b. Rule `R-24b` — Segmentasi Kualifikasi Usaha & Afirmasi UMK Konsultansi Konstruksi
* **Status:** `POTENSI_TIDAK_SESUAI` / `PERLU_VERIFIKASI` | **Severity:** `TINGGI` / `SEDANG` | **Confidence:** `TINGGI`
* **Dasar Regulasi:** **Pasal 65 ayat (4) Perpres 46/2025 jo. Pasal 4 ayat (1) & (2) Perlem LKPP 3/2026**.
* **3 Skenario Deteksi Presisi (Patch v4.0.1):**
  1. **Jasa Konsultansi Konstruksi $> \text{Rp}1.000.000.000$ ditandai `UMK = Ya`** (`POTENSI_TIDAK_SESUAI`, `severity = TINGGI`, `confidence = TINGGI`):
     - Berdasarkan Pasal 4 ayat (2) Perlem LKPP 3/2026, paket Konsultansi Konstruksi $> \text{Rp}1\text{ M}$ s.d. $\text{Rp}2,5\text{ M}$ dicadangkan bagi Kualifikasi Usaha Menengah, dan $> \text{Rp}2,5\text{ M}$ dicadangkan bagi Kualifikasi Usaha Besar. Pemberian tanda `status_umk = Ya` adalah anomali regulasi fatal.
     - Menangkap paket Seleksi `[65546342]` ($\text{Rp}9,28\text{ Miliar}$ — wajib Usaha Besar) dan `[67076511]` ($\text{Rp}1,74\text{ Miliar}$ — wajib Usaha Menengah) pada Dinas PKPCK.
  2. **Jasa Konsultansi $\le \text{Rp}1.000.000.000$ (non-Dikecualikan) ditandai `UMK != Ya`** (`PERLU_VERIFIKASI`, `severity = SEDANG`):
     - Wajib dicadangkan bagi Kualifikasi Usaha Kecil sesuai Pasal 4 ayat (2) huruf a Perlem LKPP 3/2026.
     - Menangkap 2 paket riil: `[65107703]` ($\text{Rp}200\text{ Jt}$) dan `[65925834]` ($\text{Rp}26,34\text{ Jt}$) yang berstatus `UMK = Tidak`.
  3. **Seluruh Paket Penyedia $> \text{Rp}15.000.000.000$ ditandai `UMK = Ya`** (`PERLU_VERIFIKASI`, `severity = SEDANG`):
     - Menangkap **17 paket raksasa bernilai total $\text{Rp}639,46\text{ Miliar}$** yang ditandai `UMK = Ya` untuk memastikan apakah merupakan pagu agregat BOSP/Katalog yang terpecah pembelanjaannya atau kesalahan input kualifikasi non-kecil.

#### 43. Rule `R-25` — Penelaahan Prioritas Produk Dalam Negeri (PDN)
* **Status:** `PERLU_VERIFIKASI` | **Severity:** `INFO` | **Confidence:** `TINGGI`
* **Dasar Regulasi:** Pasal 66 & Pasal 72A Perpres 46/2025 jo. Inpres 2/2022.
* **Logika:** Paket Penyedia dengan label `produk_dalam_negeri != "Ya"`.
* **Batas Inferensi:** Label `produk_dalam_negeri = "Ya"` pada RUP bukan bukti sertifikat TKDN aktual; sebaliknya label `"Tidak"` menjadi pengingat kepatuhan prioritas produk dalam negeri.

#### 44. Rule `R-26` — Profil Indikator Pengadaan Tingkat OPD (Tanpa Penalti Hukum)
* **Status:** `INFORMATIF` | **Severity:** `INFO` | **Confidence:** `TINGGI`
* **Ketentuan v4.0:** Rasio E-Purchasing, rasio Penunjukan Langsung, dan rasio UMK disajikan murni sebagai profil pengadaan CSV. Sistem tidak lagi memaksakan denda penalti kepatuhan hukum dari rasio-rasio tersebut.
* **`R-26.4` (Cakupan DPA):** Berstatus `NON-AKTIF` karena berkas ekspor CSV SIRUP tidak menyertakan pagu pengadaan DPA.

#### 45. Mandatori Evaluasi Pemilik Manfaat (*Beneficial Ownership*) Pasal 8 PerLKPP 3/2026
* **Ketentuan Pengawasan:** Untuk seluruh paket pemilihan yang dimulai sejak **1 November 2026 s.d. 31 Desember 2026** (dan seterusnya), Pokja Pemilihan / Pejabat Pengadaan **wajib mengevaluasi data Pemilik Manfaat** pada SPSE. Peserta yang tidak menyampaikan data atau memiliki kesamaan pemilik manfaat dengan peserta lain dalam paket yang sama **wajib digugurkan**.

---

## BAGIAN 5: PENGUKURAN RISIKO OPD: RUP RISK PROFILE & RUP RISK ATTENTION SCORE

Sistem v4.0 **menghentikan penggunaan skor RCI lama sebagai ukuran kepatuhan hukum mutlak**. Formula RCI lama menggabungkan penalti makro dan denominator yang tidak tersedia di CSV.

### 5.1 RUP Risk Attention Score (Substitusi Terbuka)
Untuk memprioritaskan antrean reviu probity APIP/UKPBJ secara transparan, sistem menghitung **RUP Risk Attention Score**:

$$\text{Bobot Pagu} = \text{Pagu}_{\text{Potensi Tidak Sesuai}} + (0,40 \times \text{Pagu}_{\text{Perlu Verifikasi}}) + (0,10 \times \text{Pagu}_{\text{Sinyal Risiko}})$$
$$E_{\text{pagu}} = \min\left(100, \; \left(\frac{\text{Bobot Pagu}}{\text{Total Pagu Valid}}\right) \times 100\right)$$

$$\text{Bobot Paket} = (5,0 \times N_{\text{Potensi Tidak Sesuai}}) + (2,0 \times N_{\text{Perlu Verifikasi}}) + (0,5 \times N_{\text{Sinyal Risiko}})$$
$$E_{\text{paket}} = \min\left(100, \; \left(\frac{\text{Bobot Paket}}{\text{Total Paket}}\right) \times 10\right)$$

$$\text{Deduction} = (0,60 \times E_{\text{pagu}}) + (0,40 \times E_{\text{paket}})$$
$$\text{Risk Attention Score} = \text{round}(\max(0, \; 100 - \text{Deduction}), \; 2)$$

* **Zona Perhatian:**
  * $\text{Score} < 50,00$ $\rightarrow$ **Zona MERAH** (Prioritas Intervensi Reviu Mendesak)
  * $50,00 \le \text{Score} < 70,00$ $\rightarrow$ **Zona ORANYE** (Tingkat Perhatian Tinggi)
  * $70,00 \le \text{Score} < 85,00$ $\rightarrow$ **Zona KUNING** (Tingkat Perhatian Sedang)
  * $\text{Score} \ge 85,00$ $\rightarrow$ **Zona HIJAU** (Risiko Rendah / Profil Wajar)

---

## BAGIAN 6: ARSITEKTUR PERSISTENSI BASIS DATA: ZERO-MIGRATION POLICY

Untuk mendukung alur deployment **Git Auto-Push ke server produksi tanpa hak akses CLI/SSH terminal database**, sistem Begawi Pengadaan beroperasi secara aman di atas skema Batch 16 eksisting (`2026-10-29-000001_CreateRupAuditTables.php`):

| Tabel Fisik Basis Data | Kolom Penampung v4.0 | Metadata v4.0 yang Disimpan (*Serialized JSON*) |
|---|---|---|
| `rup_audit_batches` | `macro_summary` | `file_sha256`, `source_row_count`, `data_quality_summary`, `rule_set_version` |
| `rup_audit_violations` | `target_perubahan` | `finding_status`, `confidence_level`, `evidence_json`, `limitation_note`, `regulatory_basis_json`, `recommendation_text`, `suggested_correction_json` |
| `rup_audit_clusters` | `macro_flags` & `daftar_kode_rup` | `cluster_type`, `cluster_confidence`, `serial_family_count`, `cluster_limitation_note` |
| `rup_audit_opd_summaries` | `macro_flags` & `skor_rci` | `risk_profile_json` dan `skor_rci` (Risk Attention Score v4.0) |

Proses *pack* dan *unpack* data berlangsung transparan di dalam [`RupAuditService.php`](file:///c:/laragon/www/begawipengadaan/app/Services/RupAudit/RupAuditService.php).

---

## BAGIAN 7: BASELINE KALIBRASI DATA RIIL LAMPUNG ($7.982$ BARIS TA 2026)

Angka penerimaan resmi hasil pengujian dataset baseline `data_dashboard_rup_table_20261031_063857_0986d8ff.csv`:

* **Total Baris Dataset:** $7.982$ paket pengadaan, $49$ OPD, seluruh TA 2026.
* **Pagu Anggaran Nol (`R-01`):** $5$ paket.
* **Paket Penyedia Tanpa Jenis Valid (`R-02`):** $1$ paket.
* **Paket Pengadaan Langsung Melampaui Ambang Batas:** Tepat **$63$ paket** ($\text{Rp}27.707.561.206$):
  * Barang ($> \text{Rp}200\text{ Jt}$): $5$ paket ($\text{Rp}1.793.521.200$)
  * Jasa Lainnya ($> \text{Rp}200\text{ Jt}$): $38$ paket ($\text{Rp}19.194.102.856$)
  * Pekerjaan Konstruksi ($> \text{Rp}400\text{ Jt}$): $2$ paket ($\text{Rp}1.840.069.500$)
  * Jasa Konsultansi ($> \text{Rp}100\text{ Jt}$): $18$ paket ($\text{Rp}4.879.867.650$)
* **Threshold Hugging (`R-27`):** Tepat **$209$ paket** ($182$ tepat batas ambang, $27$ dekat batas ambang).
* **PL Konstruksi $\text{Rp}200\text{ Juta} - \text{Rp}400\text{ Juta}$:** $70$ paket (seluruhnya berstatus `UMK = Ya`, tidak memicu pelanggaran ambang batas).
* **Kandidat Pencadangan Usaha Kecil Pasal 65 ayat (3) (`R-24`):** Tepat **$24$ paket** ($\text{Rp}11.450.777.996$).
* **Profil Sumber Dana Multi-Tag:** $262$ paket `APBD;APBDP` dan $2$ paket `APBD;BLUD` dipertahankan sebagai profil multi-sumber tanpa duplikasi baris.
* **Klaster Pemaketan Sejenis:** $588$ klaster komoditas teridentifikasi tanpa *false positive splitting*.

---

## BAGIAN 8: DAFTAR PERIKSA (*CHECKLIST*) VERIFIKASI MANUAL 18 POIN (AUDITOR & POKJA)

| No | Fokus Verifikasi Manual | Aturan Terkait | Status & Standar Kepatuhan v4.0 |
|:---:|---|:---:|---|
| 1 | Apakah ada paket dengan pagu nol, negatif, atau format notasi ilmiah rusak (`6.84E+07`)? | `R-01`, `R-36` | `ANOMALI_DATA` &middot; Wajib diperbaiki formatnya di SIRUP |
| 2 | Apakah ada paket Penyedia yang metode atau jenis pengadaannya masih kosong atau strip (`-`)? | `R-02`, `R-35` | `ANOMALI_DATA` &middot; Wajib dilengkapi sebelum pelaksanaan |
| 3 | Apakah nama paket terlalu kabur seperti hanya *"Belanja Barang dan Jasa"* atau *"Belanja Modal"*? | `R-34` | `ANOMALI_DATA` &middot; Perjelas rincian nama objek komoditas |
| 4 | Apakah ada Pengadaan Langsung Barang/Jasa Lainnya bernilai $> \text{Rp}200\text{ Juta}$? | `R-04` | `POTENSI_TIDAK_SESUAI` &middot; Ubah ke E-Purchasing atau Tender |
| 5 | Apakah ada Pengadaan Langsung Jasa Lainnya perorangan $> \text{Rp}50\text{ Jt}$ s.d. $\text{Rp}200\text{ Jt}$? | `R-04b` | `PERLU_VERIFIKASI` &middot; Wajib SPK Transaksional SPSE & Reviu APIP |
| 6 | Apakah ada Pengadaan Langsung Pekerjaan Konstruksi $> \text{Rp}400\text{ Juta}$ (atau $> \text{Rp}200\text{ Jt}$ untuk TA $\le 2024$)? | `R-05` | `POTENSI_TIDAK_SESUAI` &middot; Ubah ke Tender / E-Purchasing Konstruksi |
| 7 | Apakah PL Konstruksi $\text{Rp}200\text{M} - \text{Rp}400\text{M}$ pada TA 2025 berada pada masa transisi sebelum 30 April 2025? | `R-37` | `TIDAK_DAPAT_DINILAI_DARI_CSV` &middot; Cek tanggal proses di SPSE |
| 8 | Apakah ada Pengadaan Langsung Jasa Konsultansi bernilai $> \text{Rp}100\text{ Juta}$? | `R-06` | `POTENSI_TIDAK_SESUAI` &middot; Ubah ke Seleksi atau E-Purchasing Konsultansi |
| 9 | Apakah paket Penunjukan Langsung memiliki justifikasi kriteria keadaan tertentu yang sah? | `R-08`, `R-08b` | `PERLU_VERIFIKASI` &middot; Cek dokumen justifikasi internal PPK |
| 10 | Apakah tagihan listrik, air, internet, pajak STNK, atau materai masih diinput Pengadaan Langsung? | `R-10` | `PERLU_VERIFIKASI` &middot; Ubah ke metode Dikecualikan |
| 11 | Apakah ada jasa kebersihan, keamanan, sopir, atau tenaga operasional non-ASN di Swakelola? | `R-11` | `PERLU_VERIFIKASI` &middot; Wajib Penyedia Jasa Lainnya (Pasal 66 UU 20/2023) |
| 12 | Apakah ada Belanja Modal peralatan/gedung yang dicatat sebagai Swakelola tanpa tipe yang jelas? | `R-12` | `PERLU_VERIFIKASI` &middot; Pindahkan ke Penyedia atau lengkapi tipe MoU |
| 13 | Apakah komoditas standar (ATK, makan minum rapat, BBM, pemeliharaan kendaraan) masih memakai PL/Tender? | `R-15` | `PERLU_VERIFIKASI` &middot; Utamakan E-Purchasing Katalog Elektronik V6 |
| 14 | Apakah jenis pengadaan telah sesuai (Konsultansi, Konstruksi, Barang, Jasa Lainnya, Terintegrasi)? | `R-16` s.d. `R-19`, `R-33` | `POTENSI_TIDAK_SESUAI` / `PERLU_VERIFIKASI` &middot; Klasifikasi wajib tepat |
| 15 | Apakah terdapat pemecahan paket (*splitting*) sejenis pada lokasi/ruas yang sama melampaui batas PL? | `R-20`, `R-38` | `SINYAL_RISIKO` &middot; Reviu konsolidasi paket kompetitif |
| 16 | Apakah paket bernilai tepat pada batas ambang atau mepet batas atas Pengadaan Langsung ($95\% - 100\%$)? | `R-27a`, `R-27b` | `SINYAL_RISIKO` &middot; Reviu kewajaran HPS/RAB untuk mencegah pematokan pagu |
| 17 | Apakah paket Barang, Konstruksi, dan Jasa Lainnya $\le \text{Rp}15\text{ Miliar}$ telah dicadangkan bagi Usaha Kecil? | `R-24` | `POTENSI_TIDAK_SESUAI` &middot; Cek Pasal 65 ayat (3) & (4) Perpres 46/2025 |
| 18 | Apakah seluruh paket pemilihan yang dimulai sejak 1 November 2026 telah dievaluasi Pemilik Manfaatnya? | BO Pasal 8 Perlem 3/2026 | Mandatori SPSE &middot; Wajib digugurkan jika tidak unggah data BO |
