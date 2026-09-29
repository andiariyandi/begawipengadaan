# BegawiPBJ UAT Scenarios

Dokumen ini menyediakan skenario User Acceptance Testing (UAT) BegawiPBJ bagi pengguna operasional Pemerintah Provinsi Lampung. Ini adalah template uji dan **bukan** bukti bahwa fitur telah tersedia atau lulus.

## Cara menggunakan

Untuk setiap skenario isi hasil aktual, status, ID defect, tester, waktu, dan bukti. Jalankan dengan data QA sintetik, bukan data ASN nyata. Awali dari database testing/staging terisolasi dengan master wilayah asli `wilayah(kode,nama)` dan kode provinsi Lampung `18`.

| Field pencatatan | Isi |
|---|---|
| ID dan judul | ID pada tabel di bawah |
| Tester/role | Nama atau ID tim UAT dan role uji |
| Build/environment | Tag release, staging URL, tanggal/waktu |
| Langkah dan input | Skenario aktual yang dilakukan |
| Expected result | Perilaku yang seharusnya |
| Actual result | Perilaku nyata, termasuk pesan error |
| Status | `Pass`, `Fail`, `Blocked`, `Not Run` |
| Bukti | Screenshot/video/log yang sudah disanitasi |
| Defect ID | Link/nomor issue, severity, pemilik, status retest |

## Prasyarat umum

- Akun aktif untuk Super Admin, Admin, Operator, Trainer, Evaluator, Peserta; akun Public User/guest.
- Akun pending, inactive, rejected, suspended untuk negative testing.
- Course/konten published dan draft; kelas ongoing dan scheduled; quiz, tugas, regulasi, event, sertifikat contoh.
- Kelas memiliki Trainer dan Evaluator yang ditugaskan; Peserta A dan B berbeda.
- File placeholder memiliki `file_assets.path = NULL` dan tidak ada file fisik.
- Sertifikat valid, revoked, expired, dan token tidak dikenal untuk verifikasi.
- OPD root + 49 unit sesuai daftar yang disetujui; JF PPBJ terikat Biro Pengadaan Barang dan Jasa.
- Data master wilayah resmi sudah diimport; profil uji memakai Provinsi Lampung.

## Skenario Landingpage dan autentikasi

| ID | Actor | Langkah ringkas | Expected result | Prioritas |
|---|---|---|---|---|
| UAT-PUB-01 | Guest | Buka Beranda, Arena Belajar, Bunga Rampai, Regulasi, Berita, Agenda, Statistik PBJ | Hanya konten published dan statistik agregat tampil; navigasi Main UI konsisten | P0 |
| UAT-PUB-02 | Guest | Buka URL konten draft dan URL reference FiStudy/WowDash | Draft/reference tidak tampil sebagai halaman produksi | P0 |
| UAT-AUTH-01 | Public calon user | Registrasi kategori ASN dengan kode wilayah Lampung dan field wajib | Akun pending; notifikasi status; belum dapat Workspace | P0 |
| UAT-AUTH-02 | Public calon user | Registrasi kategori provider, academic_practitioner, public | Form sesuai kategori; data kategori dan profil umum tidak tertukar | P1 |
| UAT-AUTH-03 | Guest | Pilih provinsi Lampung lalu kabupaten/kecamatan/desa pada dropdown | Anak berdasarkan prefix kode wilayah resmi, bukan input bebas | P1 |
| UAT-AUTH-04 | User active | Login lalu logout | Redirect sesuai role; session logout tidak dapat dipakai ulang | P0 |
| UAT-AUTH-05 | User pending/inactive/rejected/suspended | Login atau akses URL Workspace/Admin langsung | Ditolak sesuai status, tanpa membocorkan data | P0 |
| UAT-AUTH-06 | User active | Lupa password dan reset dengan token sekali pakai | Password berubah; token kedaluwarsa/dipakai ulang ditolak | P1 |

## Skenario Peserta

| ID | Langkah ringkas | Expected result | Prioritas |
|---|---|---|---|
| UAT-PST-01 | Login Peserta A, buka Workspace, kelas saya, progres, sertifikat | Main UI; hanya data A tampil, bukan B | P0 |
| UAT-PST-02 | Daftar kelas yang dibuka dan memiliki kuota | Enrollment tercatat sekali, status benar; duplikat ditolak | P0 |
| UAT-PST-03 | Buka lesson teks, video/link, dokumen dan tandai selesai | Progress sesuai lesson/enrollment; file placeholder ditandai belum tersedia | P1 |
| UAT-PST-04 | Mulai quiz, isi pilihan tunggal, multiple response, true/false, submit | Attempt tercatat dan auto-score sesuai kunci; jawaban tak bisa diubah setelah submit | P0 |
| UAT-PST-05 | Ulangi quiz melampaui attempt_limit atau setelah deadline | Ditolak tanpa attempt ekstra | P1 |
| UAT-PST-06 | Tulis essay dan unggah lampiran assignment, submit | Submission disimpan; MIME/ukuran tidak sah ditolak | P0 |
| UAT-PST-07 | Lihat feedback dan final grade | Skor sesuai bobot kelas; passed/failed sesuai passing grade | P0 |
| UAT-PST-08 | Peserta lulus membuka dan mengunduh sertifikat miliknya | Status issued benar; PDF hanya bila file fisik tersedia, placeholder tidak dapat diunduh | P0 |
| UAT-PST-09 | Ubah URL submission/enrollment/sertifikat ke ID milik Peserta B | Akses ditolak tanpa membocorkan data B | P0 |
| UAT-PST-10 | Bookmark artikel, studi kasus, dan template published | Bookmark muncul untuk A saja, duplikasi ditolak | P2 |

## Skenario Trainer dan Evaluator

| ID | Actor | Langkah ringkas | Expected result | Prioritas |
|---|---|---|---|---|
| UAT-TRN-01 | Trainer | Buka Workspace, kelola kelas yang ditugaskan | Main UI; jadwal, roster, materi, dan progress kelas benar | P0 |
| UAT-TRN-02 | Trainer | Buat/update module, lesson, quiz, assignment, ajukan konten | Validasi, draft/submit, histori workflow dan audit sesuai izin | P1 |
| UAT-TRN-03 | Trainer | Coba akses kelas yang tidak ditugaskan | Akses ditolak meskipun mengetahui URL | P0 |
| UAT-EVL-01 | Evaluator | Buka antrean assignment kelas yang ditugaskan | Hanya submission yang berhak dinilai terlihat | P0 |
| UAT-EVL-02 | Evaluator | Beri nilai dan feedback; coba skor di atas maksimum | Nilai valid disimpan dan diaudit; skor tidak sah ditolak | P0 |
| UAT-EVL-03 | Evaluator | Coba nilai tugas kelas lain atau ubah materi | Ditolak oleh service/route | P0 |
| UAT-EVL-04 | Evaluator/Admin | Finalisasi nilai kelas | Final grade tunggal per enrollment; perubahan setelah finalisasi butuh audit/alasan | P0 |

## Skenario Operator dan Admin

| ID | Actor | Langkah ringkas | Expected result | Prioritas |
|---|---|---|---|---|
| UAT-OPR-01 | Operator | Lihat dan putuskan registrasi pending yang berada dalam mandat | Status active/rejected benar, alasan penolakan wajib, audit ada | P0 |
| UAT-OPR-02 | Operator | Proses enrollment sesuai delegasi | Kuota terjaga, peserta menerima status/notifikasi | P0 |
| UAT-OPR-03 | Operator | Buka `/admin`, ubah role/permission, lihat audit global | Semua ditolak | P0 |
| UAT-ADM-01 | Admin | Login ke `/admin`, lihat Dashboard dan approval queue | Hanya Admin/Super Admin; asset WowDash produksi terpisah dari Main | P0 |
| UAT-ADM-02 | Admin | Approve/reject/return revision untuk konten | Transition sah, catatan wajib, approval history tercatat | P0 |
| UAT-ADM-03 | Admin | Kelola user dan organisasi sesuai permission | Perubahan akun/OPD valid dan diaudit | P1 |
| UAT-ADM-04 | Admin tanpa permission tertentu | Akses action dengan URL langsung | Ditolak 403; menu bukan satu-satunya pengaman | P0 |
| UAT-SAD-01 | Super Admin | Kelola role/permission dan konfigurasi inti | Akses sesuai role; perubahan tercatat audit | P0 |

## Skenario Bunga Rampai dan regulasi

| ID | Actor | Langkah ringkas | Expected result | Prioritas |
|---|---|---|---|---|
| UAT-KNW-01 | Guest | Cari artikel/FAQ/studi kasus/template published | Filter dan pagination berfungsi; draft tidak tampil | P1 |
| UAT-KNW-02 | Author/Admin | Ajukan artikel dan template untuk review; terima revisi | State dan riwayat transition tepat | P1 |
| UAT-KNW-03 | Guest/Peserta | Buka template published dengan `path NULL` | Metadata boleh tampil sesuai status; unduh dinonaktifkan | P0 |
| UAT-REG-01 | Guest | Cari regulasi menurut jenis, tahun, tahapan, kompetensi | Metadata tepat; published revoked diberi label dicabut | P1 |
| UAT-REG-02 | Admin | Tambah versi regulasi dan tandai current | Versi lama tetap ada; hanya satu current | P0 |
| UAT-REG-03 | Guest | Unduh versi published dengan file ada/tidak ada | Hanya versi yang diizinkan dan berkas tersedia dapat diunduh; log benar | P0 |

## Skenario event dan sertifikat

| ID | Actor | Langkah ringkas | Expected result | Prioritas |
|---|---|---|---|---|
| UAT-EVT-01 | Guest | Lihat coming soon, open, full, finished, cancelled | Label sesuai status; registrasi hanya saat boleh | P1 |
| UAT-EVT-02 | User active | Daftar event yang terbuka; daftar kedua kalinya | Satu registrasi per event-user; kuota tidak terlampaui | P0 |
| UAT-EVT-03 | Operator | Catat present, absent, excused | Satu absensi per registrasi; hanya present/aturan terpilih eligible sertifikat | P0 |
| UAT-CERT-01 | Admin | Terbitkan sertifikat untuk peserta lulus dan event selesai yang hadir | Nomor/token unik, satu source satu sertifikat, audit ada | P0 |
| UAT-CERT-02 | Admin | Coba issue ulang source sama dan issue peserta gagal | Tidak ada sertifikat ganda/tidak sah | P0 |
| UAT-CERT-03 | Admin | Reissue PDF dan revoke sertifikat dengan alasan | Identitas source sama, alasan/history tersimpan, publik menunjukkan revoked | P0 |
| UAT-CERT-04 | Guest | Verifikasi token valid, revoked, expired, tidak dikenal | Status benar, rate limit, tanpa NIP/NIK/email/token mentah dalam hasil | P0 |

## Skenario Intelligence dan privasi

| ID | Actor | Langkah ringkas | Expected result | Prioritas |
|---|---|---|---|---|
| UAT-INT-01 | Admin berizin | Lihat unit Provinsi Lampung dan data JF PPBJ di Biro PBJ | Struktur master root + 49 unit, data personel sesuai scope | P0 |
| UAT-INT-02 | Admin berizin | Tambah personel tanpa akun lalu catat promosi, mutasi, pensiun | `user_id` opsional; histori dan tanggal efektif tercatat | P1 |
| UAT-INT-03 | Admin berizin | Input sertifikat PBJ Level 1 dan PPK A/B/C pada OPD | Jenis, nomor unik per jenis, status aktif/expired benar | P1 |
| UAT-INT-04 | Admin berizin | Generate snapshot kebutuhan vs ketersediaan | `gap = required - available`; unik per OPD/periode/metrik | P1 |
| UAT-INT-05 | Guest | Buka statistik publik dan periksa data response/export | Hanya agregat Provinsi Lampung; tidak ada personel, NIP, email, file path | P0 |
| UAT-INT-06 | Operator tanpa export permission | Coba export intelligence | Ditolak dan dicatat bila relevan | P0 |

## Catatan defect dan keputusan rilis

| Defect ID | Scenario ID | Severity | Deskripsi | Owner | Status | Retest |
|---|---|---|---|---|---|---|
| — | — | — | — | — | — | — |

Kriteria sign-off: seluruh skenario P0 lulus; skenario P1 lulus atau memiliki pengecualian tertulis yang disetujui pemilik layanan; tidak ada kebocoran data atau kegagalan constraint/ownership. Skenario P2 yang tertunda harus memiliki owner dan tanggal target yang disetujui.

| Persetujuan | Nama/Jabatan | Tanggal | Keputusan/Catatan |
|---|---|---|---|
| Pemilik proses PBJ | — | — | — |
| Admin aplikasi | — | — | — |
| QA/UAT lead | — | — | — |
| Keamanan/privasi | — | — | — |
