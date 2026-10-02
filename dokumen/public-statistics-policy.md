# BegawiPBJ — Public Statistics Policy

## Tujuan

Dokumen ini menetapkan kebijakan Statistik Publik PBJ: sumber data, indikator, agregasi, batas disclosure, suppression, grouping, threshold, filter, visualisasi, publikasi, audit, serta pemisahan dari Government Intelligence internal.

Dokumen ini melengkapi dan tidak mengubah kontrak yang telah ditetapkan dalam:

```text
README.md
directory-tree.md
docs/architecture/route-contract.md
docs/architecture/ui-boundary.md
docs/architecture/auth-and-rbac.md
docs/architecture/workflow-contract.md
docs/architecture/file-storage-policy.md
docs/database/database-blueprint.md
docs/database/database-erd.md
docs/database/data-dictionary.md
docs/database/schema-fresh-v4.sql
docs/domain/registration-policy.md
docs/domain/assessment-and-certification-rules.md
docs/domain/regulation-policy.md
docs/domain/privacy-and-data-classification.md
```

---

## Prinsip Statistik Publik

Statistik Publik PBJ menyajikan indikator agregat mengenai ekosistem PBJ tanpa membuka data pribadi, data restricted, atau informasi yang dapat digunakan untuk mengidentifikasi individu secara langsung maupun melalui kombinasi atribut.

Prinsip wajib:

- Statistik publik hanya menggunakan data agregat yang telah melewati disclosure control.
- Statistik publik dipisahkan dari Government Intelligence PBJ internal.
- Data personal, restricted, dokumen bukti, nilai individual, dan daftar individu tidak boleh muncul pada statistik publik.
- Public dashboard tidak melakukan query detail individu lalu menyembunyikan kolom pada UI; agregasi dilakukan sebelum response dikirim ke Public Area.
- Semua filter publik harus dievaluasi terhadap risiko re-identification sebelum hasil disajikan.
- Indikator harus memiliki definisi, periode, satuan, sumber, cakupan, metode perhitungan, dan waktu pembaruan yang terdokumentasi.
- Data yang tidak memenuhi threshold minimum harus disuppress, digabungkan, atau tidak ditampilkan.
- Statistik tidak boleh digunakan untuk menilai kinerja individu atau mengungkap status personal ASN/peserta.

---

## Boundary Antarmuka

### Public Area

Statistik Publik ditampilkan melalui FiStudy:

```text
Controller
app/Controllers/Landing/StatisticPublicController.php

View
app/Views/landing/statistics/index.php

Layout
app/Views/landing/layouts/public.php

Asset
public/assets/main/

Route
GET /statistik
GET /statistik/data
```

### Internal Intelligence

Government Intelligence dan statistik rinci internal ditampilkan melalui Dashboard Back-office WowDash:

```text
Controller
app/Controllers/Intelligence/
app/Controllers/Dashboard/InternalReportController.php

View
app/Views/dashboard/intelligence/
app/Views/dashboard/reports/

Asset
public/assets/dashboard/
```

Statistik Publik tidak boleh menggunakan controller/view/data response Internal Intelligence secara langsung.

---

## Klasifikasi Data

| Kelas data | Boleh masuk Statistik Publik | Ketentuan |
|---|---:|---|
| Public (`P`) | Ya | Tetap mengikuti indikator dan aggregation rule |
| Internal (`I`) | Ya, hanya sebagai sumber agregasi | Tidak mengirim raw internal data ke Public Area |
| Restricted (`R`) | Tidak sebagai data mentah | Hanya boleh memengaruhi agregat setelah disclosure control |
| System (`S`) | Terbatas | Hanya metadata indikator/periode/update yang aman |

### Data yang dilarang tampil publik

```text
NIP/NIPPK
NIK
NIB
Nama individu
Email
Nomor telepon
Alamat
Username
Identifier SDM
Dokumen bukti
File private
Nilai individual
Jawaban quiz
Assignment/submission
Feedback individual
Attendance individual
Status account individual
Role/permission individual
Token verifikasi/reset
Path file private
Audit log
Filter export internal
```

---

## Sumber Data Indikator

Statistik publik dapat menggunakan sumber data berikut setelah agregasi dan disclosure control:

| Domain | Sumber utama | Contoh indikator publik |
|---|---|---|
| Arena Belajar | `programs`, `courses`, `learning_classes`, `enrollments`, `learning_progress`, `certificates` | Jumlah program/course/class, enrollment agregat, completion agregat, kelulusan agregat, sertifikat pembelajaran agregat |
| Bunga Rampai | `knowledge_articles`, `knowledge_media` | Jumlah konten published, konten per kategori, distribusi tipe konten |
| Regulasi | `regulations`, `regulation_categories` | Jumlah regulasi published, distribusi status hukum, kategori regulasi |
| Berita/Event | `news`, `events`, `event_registrations`, `attendances`, `certificates` | Jumlah berita/event, event per mode, registrasi/keterisian agregat, attendance agregat, sertifikat event agregat |
| Sertifikat Digital | `certificates` | Jumlah sertifikat issued/revoked/expired per periode/sumber |
| Government Intelligence | `organization_units`, `jf_ppbj_records`, `competency_certificates`, `competency_gap_snapshots` | Distribusi JF PPBJ agregat, sertifikat kompetensi agregat, gap kompetensi agregat |

Data sumber tidak boleh dikirim langsung sebagai record detail ke endpoint publik.

---

## Indikator Publik Minimum

### Arena Belajar

| Kode indikator | Nama | Definisi | Satuan | Sumber agregat |
|---|---|---|---|---|
| `learning.program_count` | Jumlah Program | Program yang tersedia/published sesuai periode | Program | `programs` |
| `learning.course_count` | Jumlah Course | Course yang tersedia/published | Course | `courses` |
| `learning.class_count` | Jumlah Class | Class dalam cakupan periode/status | Class | `learning_classes` |
| `learning.enrollment_count` | Jumlah Enrollment | Enrollment agregat, bukan daftar peserta | Enrollment | `enrollments` |
| `learning.completion_rate` | Tingkat Completion | Enrollment completed dibagi enrollment aktif/selesai sesuai definisi periode | Persen | `enrollments` |
| `learning.pass_rate` | Tingkat Kelulusan | Enrollment lulus dibagi enrollment dengan nilai final valid | Persen | `enrollments` |
| `learning.certificate_count` | Sertifikat Pembelajaran | Sertifikat source `learning_enrollment` berdasarkan status/periode | Sertifikat | `certificates` |

### Bunga Rampai

| Kode indikator | Nama | Definisi | Satuan | Sumber agregat |
|---|---|---|---|---|
| `knowledge.published_count` | Konten Published | Konten knowledge berstatus published/public | Konten | `knowledge_articles` |
| `knowledge.category_distribution` | Distribusi Konten per Kategori | Jumlah konten published/public per kategori | Konten | Knowledge category pivot |
| `knowledge.media_distribution` | Distribusi Media | Jumlah media published/public per tipe | Media | `knowledge_media` |

### Regulasi PBJ

| Kode indikator | Nama | Definisi | Satuan | Sumber agregat |
|---|---|---|---|---|
| `regulation.published_count` | Regulasi Published | Regulasi workflow published/public | Regulasi | `regulations` |
| `regulation.legal_status_distribution` | Status Hukum Regulasi | Distribusi active/amended/revoked/expired | Regulasi | `regulations` |
| `regulation.category_distribution` | Regulasi per Kategori | Distribusi regulasi published/public per kategori | Regulasi | `regulations` |

### Berita, Agenda, dan Event

| Kode indikator | Nama | Definisi | Satuan | Sumber agregat |
|---|---|---|---|---|
| `communication.news_count` | Berita Published | Berita published/public | Berita | `news` |
| `event.count` | Jumlah Event | Event berdasarkan status/periode/mode | Event | `events` |
| `event.mode_distribution` | Distribusi Mode Event | Online/offline/hybrid | Event | `events` |
| `event.registration_count` | Registrasi Event | Pendaftaran event dalam agregasi aman | Registrasi | `event_registrations` |
| `event.attendance_rate` | Tingkat Kehadiran | Present dibagi registered sesuai cakupan event selesai | Persen | `attendances`, `event_registrations` |
| `event.certificate_count` | Sertifikat Event | Sertifikat source `event_registration` | Sertifikat | `certificates` |

### Sertifikat Digital

| Kode indikator | Nama | Definisi | Satuan | Sumber agregat |
|---|---|---|---|---|
| `certificate.issued_count` | Sertifikat Diterbitkan | Sertifikat status issued | Sertifikat | `certificates` |
| `certificate.status_distribution` | Distribusi Status Sertifikat | Pending/issued/revoked/expired secara agregat | Sertifikat | `certificates` |
| `certificate.source_distribution` | Sumber Sertifikat | Learning enrollment/event registration | Sertifikat | `certificates` |

### Government Intelligence PBJ

| Kode indikator | Nama | Definisi | Satuan | Sumber agregat |
|---|---|---|---|---|
| `intelligence.jf_ppbj_distribution` | Distribusi JF PPBJ | Jumlah record JF PPBJ agregat per level/organisasi yang aman | SDM agregat | `jf_ppbj_records`, `jf_ppbj_levels` |
| `intelligence.competency_certificate_distribution` | Distribusi Sertifikat Kompetensi | Jumlah sertifikat kompetensi agregat per tipe/status | Sertifikat agregat | `competency_certificates` |
| `intelligence.competency_gap` | Gap Kompetensi | Required, available, dan gap berdasarkan snapshot agregat | Jumlah agregat | `competency_gap_snapshots` |

---

## Definisi Perhitungan

### Completion rate

\[
completion\_rate =
\frac{completed\_enrollments}{eligible\_enrollments} \times 100
\]

Keterangan:

```text
completed_enrollments
= enrollment berstatus completed dalam cakupan periode.

eligible_enrollments
= enrollment yang active/completed dalam cakupan yang sama,
dengan definisi period dan class yang konsisten.
```

Jika penyebut nol, response harus menyatakan nilai tidak tersedia atau 0 sesuai definisi indikator yang dipublikasikan; jangan membagi nol.

### Pass rate

\[
pass\_rate =
\frac{passed\_enrollments}{graded\_enrollments} \times 100
\]

Keterangan:

```text
passed_enrollments
= enrollment dengan passed_at terisi atau final_score >= passing_grade.

graded_enrollments
= enrollment dengan final_score valid dan lengkap sesuai policy class.
```

### Attendance rate

\[
attendance\_rate =
\frac{present\_registrations}{eligible\_registrations} \times 100
\]

Keterangan:

```text
present_registrations
= attendance.status = present.

eligible_registrations
= registration.status = registered pada event yang selesai,
dengan attendance record sesuai cakupan period.
```

### Competency gap

\[
gap\_count = required\_count - available\_count
\]

`gap_count` mengikuti snapshot internal yang telah tervalidasi. Statistik publik hanya dapat menampilkan agregatnya jika disclosure control terpenuhi.

---

## Dimensi dan Filter Publik

### Dimensi yang dapat dipertimbangkan

| Dimensi | Boleh publik | Ketentuan |
|---|---:|---|
| Periode | Ya | Harian/bulanan/triwulanan/tahunan sesuai indikator |
| Domain/modul | Ya | Learning, regulation, event, certificate, knowledge |
| Kategori konten/regulasi | Ya | Hanya kategori public/published |
| Status hukum regulasi | Ya | Bukan data personal |
| Mode event | Ya | Online/offline/hybrid |
| Jenis sertifikat/sumber | Ya | Hanya agregat |
| Level JF PPBJ | Ya dengan threshold | Harus melewati disclosure control |
| Organisasi/OPD | Ya dengan threshold | Hanya agregat aman; tidak boleh memudahkan identifikasi personel |
| Wilayah | Ya dengan threshold | Agregat; tidak boleh mengarah ke identitas personal |
| Tipe profil user | Terbatas | Hanya bila kelompok memenuhi threshold dan policy mengizinkan |

### Dimensi yang tidak boleh menjadi filter publik

```text
Nama individu
NIP/NIPPK
NIK
Email
Nomor telepon
Username
ID user
ID enrollment
ID submission
ID attendance
ID certificate owner
Dokumen/file identifier private
Role/permission individual
Status account individual
```

### Kombinasi filter

Walaupun masing-masing dimensi dapat aman secara sendiri-sendiri, kombinasi filter dapat meningkatkan risiko re-identification. Sistem harus mengevaluasi hasil akhir setelah seluruh filter diterapkan, bukan hanya memeriksa daftar filter yang diizinkan.

---

## Disclosure Control

### Threshold minimum

Nilai threshold final ditetapkan oleh konfigurasi/policy operasional. Baseline konservatif:

```text
minimum_group_size = 10
```

Artinya, suatu sel statistik publik tidak boleh menampilkan count, rate, atau breakdown jika kelompok dasar yang digunakan untuk menghitung indikator kurang dari 10, kecuali indikator tersebut tidak berhubungan dengan individu atau telah melalui metode pengamanan lain yang setara.

### Aturan suppression

Jika kelompok kurang dari threshold:

```text
1. Jangan tampilkan nilai.
2. Tampilkan label seperti “Data tidak tersedia” atau “< 10”.
3. Jangan menampilkan numerator/denominator yang memungkinkan nilai asli dihitung balik.
4. Terapkan complementary suppression bila total dan kategori lain dapat mengungkap sel tersembunyi.
5. Pertimbangkan grouping ke kategori/wilayah/periode yang lebih luas.
```

### Aturan grouping

Grouping dapat dilakukan dengan:

```text
Wilayah lebih luas
Periode lebih panjang
Kategori “Lainnya”
Level organisasi lebih tinggi
Penggabungan tipe status yang aman
```

Grouping tidak boleh digunakan untuk menyamarkan nilai kecil tetapi masih memungkinkan deduksi melalui total yang dipublikasikan di chart/tabel lain.

### Larangan re-identification

Statistik publik tidak boleh memungkinkan pihak luar menyimpulkan identitas atau atribut sensitif seseorang melalui:

- Filter berlapis yang menghasilkan kelompok sangat kecil.
- Perbandingan periode sebelum/sesudah satu kejadian.
- Kombinasi total, subtotal, dan sel suppressed.
- Drill-down organisasi/unit terlalu spesifik.
- Nilai ekstrem atau kategori unik.
- Link dari statistik ke daftar/detail individu.

---

## Respons API/Data Publik

Endpoint publik:

```text
GET /statistik
GET /statistik/data
```

### Aturan response

- Hanya mengirim payload agregat yang telah melalui disclosure control.
- Tidak mengirim raw query rows, primary key personal, atau metadata internal.
- Tidak mengirim hidden fields yang disembunyikan hanya oleh frontend.
- Tidak mengirim query/filter internal yang dapat mengungkap resource restricted.
- Menyertakan metadata indikator yang aman:

```text
indicator_key
indicator_label
period_start
period_end
unit
value atau suppressed marker
last_updated_at
methodology_version bila tersedia
```

### Contoh response aman

```json
{
  "indicator_key": "learning.completion_rate",
  "indicator_label": "Tingkat Completion Pembelajaran",
  "period_start": "2026-01-01",
  "period_end": "2026-12-31",
  "unit": "percent",
  "value": 78.25,
  "suppressed": false,
  "last_updated_at": "2026-12-31T00:00:00Z"
}
```

### Contoh response suppressed

```json
{
  "indicator_key": "intelligence.jf_ppbj_distribution",
  "dimension": {
    "organization_group": "Kelompok OPD tertentu",
    "level": "UTAMA"
  },
  "suppressed": true,
  "display_value": "< 10",
  "reason": "minimum_group_size"
}
```

Response suppressed tidak boleh mengirim count asli, numerator, denominator, identifier, atau data pendukung yang memungkinkan nilai direkonstruksi.

---

## Visualisasi Publik

### Aturan chart dan tabel

- Chart menggunakan data agregat yang sudah disuppress/grouped.
- Tooltip tidak boleh memuat raw count pada sel suppressed.
- Label chart tidak boleh mengungkap kategori kecil yang disembunyikan pada tabel.
- Tabel dan chart harus memakai dataset final yang sama agar tidak membuka nilai melalui perbedaan pembulatan/total.
- Pie/donut chart tidak digunakan untuk kategori dengan sel kecil yang berisiko.
- Trend line menggunakan periode yang cukup luas jika periode pendek meningkatkan risiko disclosure.
- Setiap visualisasi mencantumkan periode, satuan, definisi indikator, dan catatan keterbatasan bila relevan.

### Accessibility

- Chart memiliki ringkasan teks alternatif yang hanya berisi hasil agregat aman.
- Tabel alternatif tidak boleh mengungkap data yang disuppress pada chart.
- Warna bukan satu-satunya pembeda status/kategori.

---

## Refresh, Snapshot, dan Reproducibility

### Refresh data

- Statistik publik dapat dihitung on-demand dari aggregate view/materialized snapshot atau melalui job terjadwal sesuai beban dan policy.
- Refresh harus memiliki waktu `last_updated_at` yang dapat ditampilkan publik bila aman.
- Periode dan definisi indikator harus konsisten antar-refresh.
- Perubahan rumus indikator harus didokumentasikan melalui methodology version atau catatan perubahan.

### Snapshot internal

`competency_gap_snapshots` digunakan sebagai sumber snapshot internal untuk gap kompetensi. Snapshot publik dapat diturunkan hanya setelah data melalui disclosure control.

### Reproducibility

Untuk setiap indikator, sistem internal harus dapat menelusuri:

```text
indicator_key
period
filter dimension
source tables/snapshot
aggregation method
suppression/grouping decision
calculation timestamp
methodology version bila digunakan
```

Metadata internal tersebut tidak harus semuanya ditampilkan kepada publik.

---

## Government Intelligence vs Statistik Publik

| Aspek | Government Intelligence Internal | Statistik Publik |
|---|---|---|
| Boundary UI | Dashboard Back-office WowDash | Landing/Public FiStudy |
| Akses | Permission, scope, role, audit | Publik tanpa login untuk data aman |
| Granularitas | Dapat mencakup record individual sesuai permission | Hanya agregat aman |
| Identifier personal | Dapat tersedia terbatas sesuai need-to-know | Dilarang |
| Export | Terproteksi, scoped, audited | Tidak untuk raw data individual |
| Drill-down | Sesuai permission dan scope | Dibatasi oleh disclosure control |
| File evidence | Restricted | Tidak tersedia |
| Tujuan | Analisis/perencanaan internal | Transparansi indikator aman |

Tidak boleh membuat endpoint publik yang langsung menggunakan query/detail response Government Intelligence internal.

---

## Audit dan Monitoring

### Audit yang dapat dicatat

- Perubahan definisi indikator atau methodology version.
- Perubahan threshold/suppression policy.
- Job refresh statistik internal/public.
- Publish/unpublish dashboard statistic configuration bila tersedia.
- Akses export internal yang menjadi sumber statistik.
- Anomaly query atau percobaan filter yang berpotensi re-identification bila monitoring mendukung.

### Hal yang tidak dicatat secara berlebihan

- Parameter query publik yang dapat menyimpan data personal tidak boleh dikumpulkan tanpa kebutuhan.
- Log tidak boleh menulis raw data hasil agregasi internal yang restricted.
- Audit tidak boleh memuat identifier personal hanya karena statistik dihitung dari data tersebut.

---

## Error dan Edge Case

| Kondisi | Perilaku |
|---|---|
| Kelompok di bawah threshold | Suppress/group; jangan kirim nilai asli |
| Total memungkinkan deduksi sel kecil | Complementary suppression atau jangan tampilkan total/breakdown terkait |
| Penyebut indikator nol | Tampilkan tidak tersedia/0 sesuai definisi yang terdokumentasi; jangan membagi nol |
| Filter tidak diizinkan | Abaikan/tolak dengan response aman; jangan expose query internal |
| Data belum refresh | Tampilkan metadata last updated atau status belum tersedia |
| Snapshot internal tidak valid | Jangan publikasi indikator sampai validasi selesai |
| Chart dan tabel menghasilkan nilai berbeda | Gunakan satu dataset aggregate final yang sama |
| User mencoba parameter personal di endpoint statistik | Tolak/abaikan parameter; rate limit bila diperlukan |

---

## Checklist Implementasi

Sebelum Statistik Publik dinyatakan `Backend Ready`, pastikan:

- [ ] Endpoint publik hanya mengirim data agregat yang telah melewati disclosure control.
- [ ] Tidak ada identifier personal, data restricted, raw rows, atau hidden fields pada response.
- [ ] Indikator memiliki key, label, definisi, unit, periode, sumber, dan metodologi yang jelas.
- [ ] Threshold minimum group size diterapkan setelah seluruh filter/dimensi dikombinasikan.
- [ ] Suppression, complementary suppression, atau grouping mencegah reconstruction nilai kecil.
- [ ] Chart, tabel, tooltip, download publik, dan accessibility summary menggunakan dataset aman yang sama.
- [ ] Statistik Publik dipisahkan dari Government Intelligence internal pada route, controller, view, query, dan access policy.
- [ ] Refresh/snapshot memiliki metadata waktu pembaruan dan audit yang relevan.
- [ ] Perubahan indikator/threshold terdokumentasi dan dapat ditelusuri internal.
- [ ] Test mencakup threshold, suppression, complementary suppression, filter combination, zero denominator, response sanitization, dan access boundary.
