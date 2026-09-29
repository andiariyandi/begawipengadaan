# BegawiPBJ

## Platform Ekosistem Digital Pengadaan Barang/Jasa Pemerintah Provinsi Lampung

BegawiPBJ adalah platform digital terpadu untuk mendukung ekosistem Pengadaan Barang/Jasa Pemerintah (PBJ) di lingkungan Pemerintah Provinsi Lampung.

Platform ini menyatukan pembelajaran digital, pusat pengetahuan PBJ, repository regulasi, berita dan event, sertifikat digital, data kompetensi SDM PBJ, statistik publik, serta Government Intelligence PBJ dalam satu aplikasi yang aman, terstruktur, dapat diaudit, dan berbasis peran.

BegawiPBJ dibangun sebagai **fresh monolithic full-stack web application** menggunakan CodeIgniter 4 AppStarter. Antarmuka produksi menggunakan dua source template HTML lengkap yang diadaptasi secara terstruktur:

- **FiStudy** sebagai source template lengkap untuk Landing Page, Public Area, dan Workspace Peserta/Trainer/Evaluator/Operator.
- **WowDash** sebagai source template lengkap untuk Auth Layout terpadu serta Dashboard Back-office Super Admin/Admin.

Seluruh proses autentikasi menggunakan satu sumber antarmuka berbasis **WowDash**, termasuk login, registrasi multi-step, pemulihan kata sandi, verifikasi akun, dan perubahan kata sandi. Landing Page FiStudy selalu mengarahkan pengguna ke route autentikasi dengan prefix `/dashboard`.

FiStudy dan WowDash memuat halaman HTML, CSS, JavaScript, font, icon, image, library vendor, komponen responsif, serta—pada WowDash—SCSS. Source template dipertahankan pada `app/Views/reference/` sebagai katalog sumber. Route/controller produksi tidak boleh merender file source reference secara langsung; hasil integrasi ditempatkan pada view dan asset produksi yang sesuai.

---

## Tujuan

BegawiPBJ dirancang untuk:

- Menjadi pusat pembelajaran PBJ berbasis digital.
- Meningkatkan kompetensi SDM PBJ melalui program, course, class, materi, assessment, dan sertifikat terukur.
- Menyediakan Bunga Rampai sebagai pusat artikel, FAQ, studi kasus, template, media visual, video, dan praktik baik PBJ.
- Mengelola regulasi PBJ secara terstruktur, berversi, mudah dicari, dapat dipreview/diunduh, dan dapat ditelusuri status serta hubungan hukumnya.
- Menyediakan berita, agenda, webinar, sosialisasi, workshop, pelatihan, dan kegiatan PBJ lainnya.
- Menerbitkan sertifikat digital yang dapat diverifikasi publik melalui token dan QR code.
- Menyediakan statistik pembelajaran, kegiatan, kompetensi, serta kondisi SDM PBJ secara aman.
- Mendukung pengambilan keputusan berbasis data melalui Government Intelligence PBJ.
- Menjalankan workflow persetujuan, audit trail, file storage terproteksi, serta kontrol akses granular.

---

## Ruang Lingkup

```text
+ Arena Belajar
+ Bunga Rampai
+ Regulasi PBJ
+ Berita, Agenda, dan Event
+ Sertifikat Digital
+ Government Intelligence PBJ
+ Statistik Publik PBJ
+ Identity, RBAC, Approval, Audit, Notification, Bookmark, dan File Storage
```

Government Intelligence PBJ berfokus pada data organisasi Pemerintah Provinsi Lampung, termasuk OPD/unit, SDM JF PPBJ, pemilik sertifikat kompetensi PBJ, distribusi kompetensi, kebutuhan SDM, dan gap kompetensi.

Statistik publik hanya menggunakan data agregat. Statistik tersebut tidak boleh menampilkan NIP, NIK, nama individu ASN, email, nomor telepon, dokumen bukti, identifier personal, maupun informasi terbatas lainnya.

---

## Antarmuka Produksi

BegawiPBJ memiliki dua kelompok antarmuka produksi yang dipisahkan dari sisi layout, asset, view, route, dan batas akses. Namun, seluruh halaman autentikasi menggunakan satu sumber UI berbasis WowDash agar pengalaman masuk, registrasi, pemulihan kata sandi, dan verifikasi akun konsisten.

| Antarmuka | Source template | Pengguna | View produksi | Asset produksi | Fungsi utama |
|---|---|---|---|---|---|
| Landing/Public UI | FiStudy | Pengunjung publik dan Public User | `app/Views/landing/` | `public/assets/main/` | Landing Page, katalog publik, regulasi publik, berita, event, statistik agregat, dan verifikasi sertifikat |
| Workspace UI | FiStudy | Peserta, Trainer, Evaluator, Operator | `app/Views/main/workspace/` | `public/assets/main/` | Pembelajaran, kelas, penilaian, tugas, bookmark, notifikasi, verifikasi operasional, dan aktivitas berbasis role |
| Auth UI Terpadu | WowDash | Semua pengguna | `app/Views/dashboard/auth/` | `public/assets/dashboard/` | Login, registrasi multi-step, lupa/reset kata sandi, verifikasi akun, dan status akun |
| Dashboard Back-office UI | WowDash | Super Admin dan Admin | `app/Views/dashboard/` | `public/assets/dashboard/` | Administrasi, RBAC, approval, audit, pengaturan, laporan, statistik internal, dan pengelolaan data |

### Kontrak asset

```text
FiStudy / Landing Page, Workspace
→ public/assets/main/

WowDash / Auth, Dashboard
→ public/assets/dashboard/
```

Tidak boleh menggunakan asset FiStudy pada view dashboard/auth, dan tidak boleh menggunakan asset WowDash pada landing/public UI, kecuali ada keputusan integrasi eksplisit yang didokumentasikan.

### Kontrak view

```text
Landing/Public UI
→ app/Views/landing/

Auth UI Terpadu
→ app/Views/dashboard/auth/

Dashboard UI
→ app/Views/dashboard/

Workspace UI
→ app/Views/main/workspace/
```


### Alur akses utama

```text
Pengunjung Publik
↓
Landing Page dan Public Area FiStudy
↓
/dashboard/masuk atau /dashboard/daftar
↓
Auth UI Terpadu WowDash
↓
Authentication + Account Status + Role + Permission Validation
↓
Workspace FiStudy atau Dashboard WowDash sesuai role, permission, assignment, dan status akun
```

```text
Public User / Participant / Trainer / Evaluator / Operator
↓
Auth UI Terpadu WowDash
↓
Workspace FiStudy
↓
public/assets/main/
↓
app/Views/main/workspace/
```

```text
Super Admin / Admin
↓
Auth UI Terpadu WowDash
↓
Dashboard Back-office WowDash
↓
public/assets/dashboard/
↓
app/Views/dashboard/
```

### Source template dan integrasi

```text
app/Views/reference/fistudy/
→ Flat HTML pages + assets/ + js/ + inc/
→ Source adaptasi Main UI

app/Views/reference/wowdash/
→ Flat HTML pages + assets/
→ Source adaptasi Dashboard UI
```

Aturan integrasi:

- File HTML source tidak mempunyai route produksi.
- Controller produksi dilarang merender `view('reference/...')`.
- HTML source dipecah dan diadaptasi menjadi layout, component, partial, dan domain view CodeIgniter di `app/Views/main/` atau `app/Views/dashboard/`.
- Asset yang benar-benar digunakan dipindahkan/disalin dan disesuaikan ke `public/assets/main/` atau `public/assets/dashboard/`.
- Tidak seluruh source asset/template harus dibawa ke produksi; hanya komponen yang benar-benar digunakan oleh fitur BegawiPBJ.
- Route produksi memakai kontrak bisnis BegawiPBJ, bukan nama file HTML source seperti `course.html`, `table-data.html`, atau `index-12.html`.
- Folder `app/Views/reference/fistudy/inc/`, termasuk `sendemail.php` dan PHPMailer bawaan, tidak digunakan sebagai mekanisme email produksi. Email produksi memakai layanan CodeIgniter 4 serta dependency Composer proyek.
- Lisensi template, font, icon, image, dan library pihak ketiga wajib ditinjau serta dicatat sebelum deployment.

| Source template | Contoh halaman | Tujuan adaptasi BegawiPBJ |
|---|---|---|
| FiStudy | `index.html`, `course*.html`, `events*.html`, `blog*.html`, `faq.html`, `login.html`, `sign-up.html`, `gallery.html` | Landing Page, katalog pembelajaran, event, Bunga Rampai, galeri, FAQ, autentikasi Main UI |
| WowDash | `index*.html`, `sign-in.html`, `users-*.html`, `role-access.html`, `form*.html`, `table-*.html`, `*-chart.html`, `widgets.html` | Dashboard, login Dashboard, user/RBAC management, form, data table, chart, KPI internal |

Rincian struktur source template tercatat dalam `begawipbj-directory-architecture.md` dan `begawipbj-documentation-and-database-file-register.md`.

---

## Identity dan Akses

Empat konsep berikut harus dipisahkan secara tegas:

| Konsep | Ditetapkan oleh | Dapat diubah sendiri? | Contoh |
|---|---|---:|---|
| Tipe profil | Pengguna saat registrasi; diverifikasi Operator/Admin | Tidak langsung; melalui pengajuan perubahan setelah aktif | ASN, Penyedia Barang/Jasa, Akademisi, Masyarakat Umum |
| Status akun | Sistem dan Operator/Admin | Tidak | `pending`, `revision_required`, `active`, `rejected`, `inactive`, `suspended` |
| Role/hak akses | Admin/Super Admin | Tidak | `public_user`, `participant`, `trainer`, `evaluator`, `operator`, `admin`, `super_admin` |
| Permission | Sistem dan Admin/Super Admin | Tidak | `learning.study`, `learning.assignment.grade`, `certificate.issue` |

Tipe profil bukan role. Memilih profil ASN, Penyedia Barang/Jasa, Akademisi, atau Masyarakat Umum tidak memberi kewenangan administratif secara otomatis.

Akses efektif selalu diperiksa melalui:

```text
Authentication
+ Account Status
+ Role
+ Permission
+ Ownership atau Class Assignment
+ Status workflow/domain
```

Menyembunyikan menu bukan mekanisme keamanan. Seluruh route, controller action, service sensitif, unduhan file privat, dan operasi perubahan data wajib memeriksa authorization pada sisi server.

### Role sistem

| Role | Area utama | Hak akses utama |
|---|---|---|
| Super Admin | Dashboard | Konfigurasi strategis, seluruh modul, user, role, permission, monitoring, audit |
| Admin | Dashboard | Manajemen konten, pembelajaran, regulasi, event, sertifikat, approval, laporan sesuai permission |
| Operator | Workspace/operasional | Verifikasi data, dukungan administrasi, enrollment, absensi, dan approval terdelegasi |
| Trainer | Workspace | Kelas yang ditugaskan, materi, peserta, quiz, assignment, dan progres |
| Evaluator | Workspace | Penilaian manual, feedback, revisi, serta final grading sesuai penugasan |
| Participant | Workspace | Enrollment, pembelajaran, quiz, tugas, progres, bookmark, feedback, sertifikat pribadi |
| Public User | Landing/Public Area | Informasi publik, katalog, konten publik, agenda, statistik agregat, verifikasi sertifikat |

Satu akun dapat memiliki lebih dari satu role. Role tidak menggantikan permission, assignment, ataupun ownership.

---

## Registrasi dan Approval

### Tipe profil registrasi

Pengguna memilih satu tipe profil pada langkah akun dasar:

```text
ASN
Penyedia Barang/Jasa
Akademisi
Masyarakat Umum
```

Tipe profil menentukan field lanjutan yang wajib diisi pada langkah profil spesifik. Tipe profil bukan role dan tidak memberikan kewenangan administratif otomatis.

### Alur registrasi

```text
Langkah 1: Akun dasar + tipe profil
↓
Langkah 2: Wilayah dan alamat ringkas
↓
Langkah 3: Profil spesifik berdasarkan tipe akun
↓
Langkah 4: Tinjau data + persetujuan syarat penggunaan + kebijakan privasi
↓
Submit registrasi
↓
account_status = pending
↓
Verifikasi email
↓
Verifikasi Operator/Admin
↓
Jika disetujui: account_status = active
↓
Role awal: public_user + participant
```

Jika data perlu diperbaiki, gunakan status `revision_required` agar pengguna dapat melengkapi atau memperbaiki data tanpa membuat akun baru. Status `rejected` digunakan jika pengajuan tidak dapat diterima sesuai kebijakan.

### Akun dasar untuk semua tipe

| Field | Wajib | Ketentuan |
|---|---:|---|
| Nama lengkap | Ya | Sesuai identitas pengguna |
| Email | Ya | Unik dan valid; dipakai untuk login/verifikasi |
| Username | Ya | Unik; hanya karakter yang diizinkan sistem |
| Password | Ya | Minimal 12 karakter, huruf besar, huruf kecil, angka, simbol |
| Konfirmasi password | Ya | Harus sama dengan password |
| Nomor telepon | Ya | Format nomor Indonesia yang valid |
| Tipe profil | Ya | ASN, Penyedia Barang/Jasa, Akademisi, atau Masyarakat Umum |
| Provinsi | Ya | Menggunakan master `wilayah` |
| Kabupaten/Kota | Ya | Konsisten dengan provinsi yang dipilih |
| Kecamatan | Ya | Konsisten dengan kabupaten/kota yang dipilih |
| Desa/Kelurahan | Opsional | Menggunakan master `wilayah` bila diisi |
| Alamat ringkas | Ya | Detail alamat administratif seperlunya |
| Persetujuan syarat penggunaan | Ya | Checkbox |
| Persetujuan kebijakan privasi | Ya | Checkbox |
| CAPTCHA/rate limiting | Ya | Proteksi spam dan automated registration |

### Profil ASN

ASN menggunakan NIP atau NIPPK sebagai identitas utama. ASN tidak wajib mengisi NIK dan tidak wajib mengunggah Surat Tugas pada registrasi awal.

Pada awal form ASN, pengguna memilih asal instansi:

```text
ASN Pemprov Lampung
ASN Luar Pemprov Lampung
```

Nilai teknis asal instansi:

```text
pemprov_lampung
kementerian_lembaga
pemerintah_daerah_lain
pemerintah_desa
bumn_bumd
lainnya
```

#### ASN Pemprov Lampung

| Field | Wajib | Ketentuan |
|---|---:|---|
| NIP/NIPPK | Ya | Unik; tervalidasi format/kebijakan |
| Status kepegawaian | Ya | PNS atau PPPK |
| Instansi asal | Ya | Pemerintah Daerah Provinsi Lampung |
| OPD/unit kerja | Ya | Dropdown dari `organization_units` |
| Jabatan utama | Ya | Input terkontrol atau master jabatan |
| Pangkat/golongan | Opsional | Master atau input terkontrol |
| Email kedinasan | Disarankan | Email institusi bila tersedia |
| Memiliki peran pengadaan | Ya | Pilihan Ya atau Tidak |
| Peran pengadaan | Bersyarat | Multi-select jika memilih Ya |

#### ASN Luar Pemprov Lampung

| Field | Wajib | Ketentuan |
|---|---:|---|
| NIP/NIPPK | Ya | Unik; tervalidasi format/kebijakan |
| Status kepegawaian | Ya | PNS atau PPPK |
| Kategori instansi asal | Ya | K/L, Pemda lain, Pemerintah Desa, BUMN/BUMD, atau lainnya |
| Nama instansi induk | Ya | Diisi manual |
| Nama OPD/unit kerja | Ya | Diisi manual |
| Jabatan utama | Ya | Input terkontrol |
| Pangkat/golongan | Opsional | Input terkontrol |
| Provinsi instansi | Ya | Master wilayah |
| Kabupaten/Kota instansi | Bersyarat | Wajib untuk Pemda lain atau Pemerintah Desa |
| Kecamatan/Desa | Bersyarat | Wajib sesuai kebutuhan Pemerintah Desa |
| Email kedinasan | Disarankan | Email institusi bila tersedia |
| Memiliki peran pengadaan | Ya | Pilihan Ya atau Tidak |
| Peran pengadaan | Bersyarat | Multi-select jika memilih Ya |

Perangkat Desa yang tidak mempunyai NIP/NIPPK tidak boleh dipaksa menggunakan tipe ASN. Mereka menggunakan tipe Masyarakat Umum dengan pekerjaan/status `Perangkat Desa`, kecuali kebijakan masa depan menambahkan tipe profil khusus.

### Peran dalam pengadaan

Peran pengadaan bersifat opsional dan satu pengguna dapat mempunyai lebih dari satu peran:

```text
PA                         Pengguna Anggaran
KPA                        Kuasa Pengguna Anggaran
PPK                        Pejabat Pembuat Komitmen
PP                         Pejabat Pengadaan
POKJA_PEMILIHAN            Kelompok Kerja Pemilihan
PENGELOLA_PENGADAAN        Pengelola Pengadaan Barang/Jasa
PPTK                       Pejabat Pelaksana Teknis Kegiatan
PEJABAT_PENERIMA_HASIL     Pejabat/Panitia Penerima Hasil Pekerjaan
TIM_TEKNIS                 Tim Teknis
TIM_PENDUKUNG              Tim Pendukung
PENGAWAS_KONTRAK           Pengawas Kontrak
APIP                       Aparat Pengawasan Intern Pemerintah
LAINNYA                    Peran lainnya
```

Pada registrasi awal, cukup mengambil jawaban Ya/Tidak dan daftar peran. Periode penugasan, nomor SK, bukti penugasan, dan verifikasi lanjutan dapat dilengkapi setelah akun aktif melalui pengajuan perubahan profil.

### Profil Penyedia Barang/Jasa

| Field | Wajib | Ketentuan |
|---|---:|---|
| NIK | Ya | 16 digit; data restricted |
| Nama badan usaha | Ya | Nama perusahaan/badan usaha |
| Bentuk badan usaha | Ya | Perseorangan, CV, PT, Koperasi, Firma, atau lainnya |
| Jabatan pendaftar | Ya | Contoh: Direktur, Pemilik, Staf Tender |
| Nama penanggung jawab | Ya | Kontak badan usaha |
| NIB | Disarankan | Wajib hanya bila kebijakan penyedia resmi menerapkannya |
| Email badan usaha | Disarankan | Email institusional |
| Alamat badan usaha | Opsional | Informasi administratif |

Tidak ada upload dokumen wajib pada registrasi awal.

### Profil Akademisi

| Field | Wajib | Ketentuan |
|---|---:|---|
| NIK | Ya | 16 digit; data restricted |
| Nama institusi | Ya | Perguruan tinggi, lembaga riset, lembaga pelatihan, organisasi profesi, atau lainnya |
| Jenis institusi | Ya | Dropdown terkontrol |
| Jabatan/peran | Ya | Dosen, peneliti, praktisi, konsultan, atau lainnya |
| Bidang keahlian | Ya | Multi-select/input terkontrol |
| Email institusi | Disarankan | Email institusional |
| NIDN/NUPTK/identitas profesi | Opsional | Verifikasi tambahan bila diperlukan |
| Portofolio URL | Opsional | URL aman HTTPS |

CV dan bukti dukung tidak diwajibkan saat registrasi. Bukti kompetensi dapat diminta ketika pengguna mengajukan role Trainer atau Evaluator.

### Profil Masyarakat Umum

| Field | Wajib | Ketentuan |
|---|---:|---|
| NIK | Ya | 16 digit; data restricted |
| Pekerjaan/status saat ini | Ya | Mahasiswa, pegawai swasta, wiraswasta, pencari kerja, perangkat desa, masyarakat, atau lainnya |
| Institusi/organisasi | Opsional | Bila ada |
| Bidang minat PBJ | Opsional | Untuk personalisasi konten |
| Tujuan penggunaan | Opsional | Belajar, event, regulasi, referensi dokumen, atau lainnya |

Tidak ada upload dokumen wajib pada registrasi awal.

---

## Role Awal dan Pengajuan Akses

Setelah registrasi disetujui, role default adalah:

```text
public_user
participant
```

Role `participant` tidak otomatis membuka materi kelas. Materi hanya dapat diakses setelah user mempunyai enrollment sah pada class terkait.

Pengguna dapat mengajukan role tambahan:

```text
trainer
evaluator
```

Pengguna tidak dapat mengajukan atau menetapkan sendiri:

```text
operator
admin
super_admin
```

Role tersebut hanya diberikan melalui penetapan internal Admin/Super Admin dan harus tercatat dalam audit log.

---

## Privasi Identitas dan Data Restricted

Data berikut adalah restricted:

```text
NIP/NIPPK
NIK
NIB
Nomor telepon
Email
Identifier SDM
Dokumen bukti
```

Ketentuan penggunaan data:

- Tidak ditampilkan penuh pada daftar pengguna.
- Ditampilkan dengan masking bila diperlukan.
- Tidak dimasukkan ke audit snapshot dalam bentuk penuh.
- Tidak dikirim lewat notifikasi biasa.
- Tidak ditampilkan pada Statistik Publik PBJ.
- Export dibatasi oleh permission dan dicatat dalam audit trail.

---

## Modul Utama

### Arena Belajar

Arena Belajar adalah Learning Management System (LMS) BegawiPBJ.

```text
Program
↓
Course
↓
Class
↓
Module
↓
Lesson
↓
Pre-test / Lesson Quiz / Assignment / Post-test
↓
Grading dan Completion
↓
Feedback
↓
Certificate Eligibility dan Certificate
```

Fitur inti:

- Program, course, dan class pembelajaran.
- Enrollment peserta, kuota, periode pendaftaran, jadwal, Trainer, dan Evaluator.
- Module dan lesson dengan materi teks, dokumen, video, tautan, dan file pembelajaran.
- Progress tracking berdasarkan penyelesaian lesson dan komponen wajib.
- Pre-test satu kali untuk baseline kemampuan awal.
- Quiz per lesson dengan auto-grading dan nilai attempt terbaik.
- Assignment essay dan/atau upload berkas, jika diaktifkan pada class.
- Manual grading, feedback, status revisi, dan upload ulang assignment.
- Post-test dengan auto-grading dan best attempt.
- Nilai akhir, passing grade, kelulusan, feedback peserta, serta certificate eligibility.

Aturan evaluasi formal terdapat pada `assessment-and-certification-rules.md`.

| Komponen | Status | Mekanisme nilai | Kontribusi nilai akhir |
|---|---|---|---|
| Pre-test | Wajib satu kali bila diaktifkan | Auto-grading 0–100 | Tidak dihitung |
| Quiz per lesson | Sesuai konfigurasi | Auto-grading; best attempt | Mengikuti `quiz_weight` class |
| Assignment | Opsional pada class; wajib jika tersedia | Manual grading; revisi/upload ulang | Mengikuti `assignment_weight` class |
| Post-test | Wajib bila diaktifkan | Auto-grading; best attempt | 60% |
| Feedback peserta | Wajib untuk sertifikat bila dikonfigurasi | Feedback/rating peserta | Tidak bernilai |

Formula nilai akhir:

\[
final\_score =
(quiz\_score \times quiz\_weight / 100) +
(assignment\_score \times assignment\_weight / 100) +
(post\_test\_score \times 60 / 100)
\]

```text
post_test_weight = 60
quiz_weight + assignment_weight = 40
passing_grade minimum = 70
```

Jika assignment tidak tersedia:

```text
assignment_enabled = false
assignment_weight = 0
quiz_weight = 40
post_test_weight = 60
```

### Bunga Rampai

Bunga Rampai adalah pusat pengetahuan dan publikasi multimedia PBJ.

Konten yang didukung:

```text
Artikel
FAQ
Studi kasus
Template dokumen
Panduan teknis
Best practice
Poster
Flyer
Infografik
Foto kegiatan
Gallery
Video upload
Video YouTube
Link sosial media
Tautan eksternal resmi
```

Fitur inti:

- Kategori bertingkat dan tag.
- Artikel, FAQ, studi kasus, serta template dokumen.
- Konten media untuk poster, flyer, infografik, gallery, video, YouTube, sosial media, dan external link.
- Search, filter, konten terkait, dan bookmark.
- Workflow `draft`, `submitted`, `in_review`, `approved`, `published`, `revision_required`, `rejected`, serta `archived`.
- Metadata dan file asset sesuai status publikasi/permission.
- Konten unggulan (`featured`) dan carousel terkelola (`carousel_enabled`, urutan carousel).

Halaman Bunga Rampai dapat memuat:

```text
Hero Bunga Rampai
↓
Search dan filter kategori/tag
↓
Konten unggulan
↓
Poster dan flyer terbaru
↓
Video PBJ pilihan
↓
Artikel dan best practice terbaru
↓
Infografik dan gallery kegiatan
↓
Template dokumen populer
↓
FAQ PBJ
↓
Kanal resmi dan tautan sosial media
```

#### Carousel dan konten visual

Carousel/ulir digunakan untuk konten visual unggulan, tetapi tidak boleh menjadi satu-satunya cara menemukan konten.

| Area | Tampilan yang disarankan |
|---|---|
| Konten unggulan | Hero carousel satu slide aktif |
| Poster/flyer | Horizontal carousel 3–5 kartu |
| Video PBJ | Video card carousel dengan thumbnail dan tombol play |
| Artikel populer | Carousel kartu dengan tombol next/prev serta tautan Lihat Semua |
| Gallery | Carousel untuk highlight dan grid untuk seluruh koleksi |
| Mobile | Horizontal touch scroll/snap yang responsif |

Aturan carousel:

- Tidak menggunakan autoplay cepat.
- Autoplay, bila digunakan, berhenti saat hover, focus, atau interaksi pengguna.
- Mendukung keyboard, tombol sebelumnya/berikutnya, serta indikator slide.
- Setiap gambar mempunyai `alt_text`.
- Setiap carousel menyediakan tombol **Lihat Semua** ke halaman list/filter.
- Gambar/video thumbnail menggunakan lazy loading.

#### Poster, flyer, infografik, dan gallery

- Poster/flyer tampil sebagai kartu visual dengan badge tipe, judul, kategori, tanggal, dan preview.
- Preview memakai modal/lightbox atau halaman detail.
- Infografik panjang mendukung zoom/preview resolusi tinggi.
- Gallery mendukung banyak item media dengan caption, alt text, dan urutan.
- Download hanya aktif jika file tersedia dan pengguna memiliki hak akses.

#### Video dan sosial media

- Daftar video menggunakan thumbnail; iframe/player dimuat hanya setelah pengguna memilih play atau membuka detail.
- URL YouTube divalidasi terhadap domain resmi sebelum diubah menjadi URL embed.
- Sistem tidak menyimpan HTML iframe bebas dari pengguna.
- Kanal sosial media memakai link card sebagai baseline; embed provider hanya digunakan jika tervalidasi dan sesuai kebijakan privasi.
- Link eksternal dibuka aman dengan `target="_blank"` dan `rel="noopener noreferrer"`.

### Regulasi PBJ

Regulasi PBJ adalah repository regulasi dan dokumen rujukan pengadaan berbasis metadata, relasi hukum, versioning file, PDF preview, dan download terkontrol.

Fitur inti:

- Kategori regulasi.
- Nomor, tahun, instansi penerbit, tanggal resmi, tanggal berlaku, ringkasan, serta sumber resmi.
- Status hukum: `active`, `amended`, `revoked`, atau `expired`.
- Tahapan PBJ, kompetensi, kata kunci, bookmark, dan pencarian/filter.
- Versioning dokumen, changelog, dan penanda current version.
- PDF preview inline, buka di tab baru, serta download PDF terkontrol.
- Log download regulasi.
- Relasi hukum antarregulasi, misalnya diubah oleh, mencabut, digantikan oleh, mengimplementasikan, atau mereferensikan.

#### Prinsip publikasi regulasi

Semua regulasi yang sudah dipublikasikan tetap tampil dan dapat diakses, termasuk regulasi yang berlaku, telah diubah, dicabut, atau berakhir.

```text
workflow_status = published
→ regulasi tampil di repository publik
→ legal_status hanya menjadi penanda status hukum
→ dokumen tetap dapat dipreview/download jika file tersedia dan visibility mengizinkan
```

| `legal_status` | Label UI | Tampil publik | Preview/download | Keterangan |
|---|---|---:|---:|---|
| `active` | Berlaku | Ya | Ya | Regulasi masih berlaku |
| `amended` | Telah Diubah | Ya | Ya | Tampilkan regulasi/dokumen perubahan terkait |
| `revoked` | Tidak Berlaku / Dicabut | Ya | Ya | Tampilkan regulasi pencabut/pengganti bila ada |
| `expired` | Berakhir | Ya | Ya | Tampil sebagai referensi historis |

Status workflow berbeda dengan status hukum:

```text
workflow_status:
draft
submitted
in_review
approved
published
revision_required
rejected
archived
```

`draft` tidak tampil pada Public Area. `archived` dapat tetap tampil sebagai arsip historis bila pernah dipublikasikan dan kebijakan arsip mengizinkan.

#### Relasi hukum regulasi

Relasi hukum perlu disimpan sebagai hubungan terstruktur, bukan hanya teks bebas.

```text
regulation_relations
- source_regulation_id
- target_regulation_id
- relation_type
- notes
- created_by
- created_at
```

Tipe relasi:

```text
amends
amended_by
revokes
revoked_by
replaces
replaced_by
implements
implemented_by
references
referenced_by
```

Halaman detail regulasi menampilkan:

```text
Metadata hukum
↓
Badge status hukum
↓
Ringkasan
↓
Keterkaitan regulasi
↓
Dokumen PDF current
↓
Preview / Buka di Tab Baru / Download
↓
Riwayat versi file dan changelog
↓
Regulasi terkait
↓
Bookmark
```

#### PDF viewer dan download regulasi

Setiap versi regulasi yang berkasnya tersedia mendukung:

```text
Preview PDF inline
Buka PDF di tab baru
Download PDF
Bookmark regulasi
Log download
```

Jika file metadata tersedia tetapi `file_assets.path = NULL`:

```text
Status file: File belum tersedia
→ Metadata regulasi tetap tampil
→ PDF viewer tidak dimuat
→ Preview/download dinonaktifkan
```

### Berita, Agenda, dan Event

Ruang lingkup ini adalah pusat komunikasi dan manajemen kegiatan PBJ.

Fitur inti:

- Kategori berita seperti Pengumuman, Kegiatan, Kebijakan, atau Informasi PBJ.
- Berita dengan judul, slug, cover, ringkasan, konten, penulis, workflow, waktu terbit, dan view count.
- Agenda publik dan kalender kegiatan.
- Event: webinar, sosialisasi, workshop, pelatihan, dan kegiatan lain.
- Mode event: `online`, `offline`, dan `hybrid`.
- Informasi jadwal, lokasi, meeting URL, periode registrasi, kuota, dan status.
- Registrasi event: `registered`, `waitlisted`, `cancelled`, atau `rejected`.
- Pengendalian kuota dan promosi waitlist melalui transaction.
- Absensi: `present`, `absent`, atau `excused`.
- Sertifikat event jika diaktifkan dan peserta memenuhi syarat.
- Notifikasi pendaftaran, waitlist, perubahan jadwal, pembatalan, absensi, serta sertifikat.

Status event:

```text
draft
coming_soon
open_registration
full
finished
cancelled
```

### Sertifikat Digital

Sertifikat Digital menerbitkan, mengelola, memverifikasi, dan membatalkan sertifikat pembelajaran maupun event.

Fitur inti:

- Template sertifikat untuk `learning` dan `event`.
- Validasi eligibility sebelum penerbitan.
- Nomor sertifikat unik berbasis scope/tahun.
- Generate PDF sertifikat.
- QR code dan verification token unik.
- Halaman verifikasi publik.
- Riwayat sertifikat peserta.
- Status `pending`, `issued`, `revoked`, dan `expired`.
- Expiry bila masa berlaku digunakan.
- Revocation tanpa menghapus record sertifikat.
- Audit trail untuk issue, regenerate, verify, expiry, revoke, dan replacement.

Satu sumber hanya dapat menghasilkan satu sertifikat resmi:

```text
UNIQUE(source_type, source_id)
```

Sumber sertifikat:

```text
learning_enrollment
event_registration
```

Syarat sertifikat learning:

```text
class.certificate_enabled
+ enrollment sah dan tidak withdrawn/cancelled
+ seluruh lesson wajib selesai
+ pre-test selesai bila aktif
+ quiz wajib selesai
+ assignment wajib graded bila aktif
+ post-test selesai bila aktif
+ final_score >= passing_grade
+ feedback peserta selesai bila diwajibkan
+ tidak ada administrative hold
+ belum ada certificate dari enrollment yang sama
```

Syarat minimum sertifikat event:

```text
event.certificate_enabled
+ event.status = finished
+ registration.status = registered
+ attendance.status = present
+ belum ada certificate dari event registration yang sama
```

### Government Intelligence PBJ

Government Intelligence PBJ adalah area internal untuk pengelolaan dan analisis data strategis SDM PBJ Pemerintah Provinsi Lampung.

Fitur inti:

- Master organisasi Pemerintah Daerah Provinsi Lampung dan OPD/unit.
- Klasifikasi unit: `province`, `agency`, `bureau`, `department`, `inspectorate`, `hospital`, dan `secretariat`.
- Data JF PPBJ dan jenjang `PERTAMA`, `MUDA`, `MADYA`, `UTAMA`.
- Riwayat perubahan JF PPBJ: penambahan, promosi, mutasi masuk/keluar, pensiun, penonaktifan, pembaruan.
- Sertifikat kompetensi PBJ yang berbeda dari sertifikat digital BegawiPBJ.
- Jenis sertifikat awal: `PBJ_LEVEL_1`, `PPK_TYPE_A`, `PPK_TYPE_B`, dan `PPK_TYPE_C`.
- Verifikasi bukti sertifikat kompetensi, status, masa berlaku, dan verifikator.
- Snapshot gap kompetensi per organisasi, periode, dan metrik.
- Dashboard internal, filter, serta export terbatas yang diaudit.

Formula gap:

\[
gap\_count = required\_count - available\_count
\]

Data individual seperti NIP, identifier, nama personel, serta bukti sertifikat hanya tersedia pada area internal dengan permission yang sesuai.

### Statistik Publik PBJ

Statistik Publik PBJ menampilkan indikator agregat ekosistem PBJ tanpa membuka data pribadi atau bukti individual.

Fitur inti:

- Jumlah dan distribusi JF PPBJ.
- Statistik sertifikat kompetensi secara agregat.
- Kebutuhan, ketersediaan, dan gap kompetensi per OPD dalam bentuk agregat.
- Statistik program/course/class, enrollment, completion, kelulusan, dan sertifikat pembelajaran.
- Statistik event, registrasi, kehadiran, dan sertifikat event.
- Statistik sertifikat digital berdasarkan status.
- KPI, chart, tren periode, filter periode, dan filter organisasi agregat.

Kolom yang dilarang muncul pada Statistik Publik:

```text
NIP
NIK
email
nomor telepon
employee_identifier
holder_identifier
nama individual ASN
nama pemilik sertifikat kompetensi
dokumen bukti
file privat
data profil personal
```

---

## Bookmark Lintas Konten

Pengguna login dapat menyimpan konten yang dapat diaksesnya untuk dibuka kembali. Bookmark bersifat personal dan tidak boleh digunakan untuk melewati authorization konten asal.

Jenis konten yang dapat dibookmark:

```text
program
course
learning_class
lesson
article
faq
case_study
document_template
knowledge_media_content
regulation
regulation_version
news
event
certificate
```

Struktur dasar:

```text
bookmarks
- id
- user_id
- bookmarkable_type
- bookmarkable_id
- created_at

UNIQUE(user_id, bookmarkable_type, bookmarkable_id)
```

Urutan default bookmark:

```sql
ORDER BY bookmarks.created_at DESC
```

Tampilan bookmark di Workspace dapat dikelompokkan sebagai berikut:

```text
Semua Bookmark
Pembelajaran
Bunga Rampai
Regulasi
Berita dan Event
Sertifikat
```

Jika konten diarsipkan, dicabut, dihapus soft delete, atau akses pengguna berubah, bookmark tetap ada sebagai riwayat pribadi tetapi tidak boleh membuka konten tanpa lolos authorization.

---

## File Preview dan Download

### Prinsip umum

Preview/download file tidak hanya untuk regulasi. Seluruh PDF yang tersedia dapat dipreview atau diunduh jika pengguna memiliki hak akses.

Domain yang mendukung PDF preview/download:

```text
Materi lesson dan lampiran
Submission assignment
Template Bunga Rampai
Poster/flyer/infografik PDF
Dokumen regulasi dan versi regulasi
Lampiran berita/event
Bukti sertifikat kompetensi
Sertifikat digital PDF
Dokumen export laporan
```

Akses file selalu melalui controller/service; file tidak boleh diakses langsung dari `writable/uploads/`.

Route generik yang direkomendasikan:

```text
/files/{fileAssetId}/preview
/files/{fileAssetId}/download
```

Aturan preview PDF:

```text
mime_type = application/pdf
AND file_assets.path IS NOT NULL
AND file fisik tersedia
AND pengguna authorized
→ kirim PDF inline
```

Aturan download:

```text
file_assets.path IS NOT NULL
AND file fisik tersedia
AND pengguna authorized
→ kirim file sebagai attachment
```

### File placeholder

```text
file_assets.path = NULL
→ Metadata file tersedia, tetapi file fisik belum tersedia
→ UI/API menampilkan “File belum tersedia”
→ Preview/download dinonaktifkan
```

### Validasi upload PDF

Setiap upload PDF harus melalui:

```text
Validasi ukuran file
+ validasi ekstensi .pdf
+ validasi MIME server-side application/pdf
+ validasi file signature %PDF-
+ nama fisik UUID/hash
+ private/protected storage
+ checksum SHA-256
+ malware scan bila infrastruktur mendukung
+ audit untuk operasi sensitif
```

Nama file pada response browser memakai `original_name` yang disanitasi, bukan `stored_name` atau path fisik.

---

## Teknologi

| Komponen | Teknologi/ketentuan |
|---|---|
| Project starter | CodeIgniter 4 AppStarter |
| Framework | CodeIgniter 4 |
| Bahasa | PHP 8.3+ |
| Database | MySQL 8.4+ |
| Database engine | InnoDB |
| Character set/collation | `utf8mb4` dan collation Unicode konsisten |
| Arsitektur | Monolithic full-stack web application |
| Organisasi kode | Feature/domain folder di atas fondasi MVC CodeIgniter |
| Authentication | Session-based login |
| Authorization | RBAC, permission granular, ownership, dan assignment scope |
| Source Main UI | FiStudy: flat HTML, CSS/module CSS, JS/GSAP, font, image, vendor |
| Source Dashboard UI | WowDash: flat HTML, CSS/SCSS, JS, font/webfont, image, vendor |
| PDF sertifikat | Dompdf |
| QR code | Endroid QR Code |
| Development server | Laragon atau server PHP kompatibel |

---

## Struktur Direktori

Rincian lengkap tersedia pada `begawipbj-directory-architecture.md`.

```text
begawipbj/
├── app/
│   ├── Config/
│   ├── Controllers/
│   ├── Database/
│   ├── Filters/
│   ├── Helpers/
│   ├── Language/
│   ├── Libraries/
│   ├── Models/
│   ├── Services/
│   └── Views/
│       ├── main/                     # Produksi Main UI hasil adaptasi FiStudy
│       ├── dashboard/                # Produksi Dashboard UI hasil adaptasi WowDash
│       ├── errors/
│       └── reference/
│           ├── fistudy/              # Source FiStudy lengkap, non-production
│           └── wowdash/              # Source WowDash lengkap, non-production
├── public/
│   ├── index.php
│   └── assets/
│       ├── main/                     # Asset produksi hasil adaptasi FiStudy
│       ├── dashboard/                # Asset produksi hasil adaptasi WowDash
│       └── shared/
├── writable/
│   ├── cache/
│   ├── logs/
│   ├── session/
│   ├── uploads/
│   └── exports/
├── tests/
├── database/
│   ├── reference/
│   │   └── wilayah.sql
│   └── archive/
├── docs/
├── .env.example
├── composer.json
├── phpunit.xml.dist
├── README.md
└── spark
```

---

## Database dan Master Wilayah

BegawiPBJ menggunakan MySQL 8.4+ dengan InnoDB, `utf8mb4`, serta primary key aplikasi berbasis `BIGINT UNSIGNED`, kecuali kode bisnis/master yang menjadi primary key eksplisit.

Dokumentasi database:

```text
begawipbj-database-blueprint.md
begawipbj-database-erd.md
begawipbj-data-dictionary.md
begawipbj-schema-fresh-v3.sql
begawipbj-dummy-data-v3.sql
```

### Master wilayah

Master `wilayah` adalah master resmi yang tersedia sebelum dummy data pengembangan dijalankan.

```sql
wilayah(
    kode VARCHAR(13) PRIMARY KEY,
    nama VARCHAR(100) NOT NULL
)
```

Kode wilayah bertingkat:

```text
18              Provinsi Lampung
18.01           Kabupaten Lampung Selatan
18.01.04        Kecamatan Natar
18.01.04.2001   Desa/Kelurahan Hajimena
```

Master lengkap disimpan pada:

```text
database/reference/wilayah.sql
```

Aturan:

- Data wilayah resmi diimpor dari source reference yang disetujui.
- Seeder dummy/development tidak boleh membuat, mengubah, menghapus, truncate, atau mengganti data `wilayah`.
- Data profil/organisasi/event yang mengacu wilayah hanya menggunakan `wilayah.kode` yang valid.

---

## Routing dan Filter

### Batas route

```text
/                               Landing Page publik
/login                          Login Main UI
/register                       Registrasi Main UI
/password/*                     Pemulihan password Main UI

/arena-belajar/*                Katalog pembelajaran publik
/bunga-rampai/*                 Bunga Rampai publik
/regulasi/*                     Repository regulasi publik
/berita/*                       Berita publik
/agenda/*                       Agenda/event publik
/statistik-pbj                  Statistik PBJ agregat
/sertifikat/verifikasi/*        Verifikasi sertifikat publik

/workspace/*                    Workspace Peserta, Trainer, Evaluator, Operator
/admin/login                    Authentication Dashboard UI
/admin/*                        Dashboard Super Admin dan Admin

/files/{id}/preview             Preview file PDF/asset yang berizin
/files/{id}/download            Download file yang berizin

/learning/*                     Manajemen Arena Belajar berbasis permission
/knowledge/*                    Manajemen Bunga Rampai berbasis permission
/regulation/*                   Manajemen regulasi berbasis permission
/communication/*                Manajemen berita/event berbasis permission
/certificates/*                 Manajemen sertifikat berbasis permission
/intelligence/*                 Government Intelligence berbasis permission
```

### Filter utama

| Filter | Fungsi |
|---|---|
| `AuthFilter` | Memastikan pengguna telah login |
| `GuestFilter` | Membatasi halaman autentikasi untuk pengguna belum login |
| `AccountStatusFilter` | Memeriksa status akun |
| `WorkspaceFilter` | Membatasi Workspace untuk role operasional sesuai |
| `AdminFilter` | Membatasi Dashboard untuk Super Admin/Admin |
| `RoleFilter` | Validasi role tertentu bila diperlukan |
| `PermissionFilter` | Validasi izin granular per route/action |
| `CsrfGuardFilter` | Perlindungan request perubahan data |
| `RateLimitFilter` | Pembatasan endpoint rawan abuse |
| `AuditRequestFilter` | Pencatatan request administratif terpilih |
| `SecurityHeadersFilter` | CSP dan security headers |

---

## Keamanan dan Privasi

BegawiPBJ menerapkan:

- Password hashing melalui `password_hash()` dan verifikasi `password_verify()`.
- CSRF protection, output escaping, input validation, mitigasi XSS, serta Query Builder/prepared statement.
- Session security dan rotasi session setelah login.
- Authorization berbasis account status, role, permission, ownership, dan penugasan class.
- Secure file upload: MIME type, ekstensi, ukuran, signature, checksum, nama fisik unik, dan path aman.
- File private/protected melalui controller/service authorization.
- Audit log untuk autentikasi sensitif, approval, grading, file, sertifikat, serta tindakan administratif penting.
- Rate limiting untuk login, reset password, upload, registrasi, dan verifikasi sertifikat.
- Security headers dan CSP yang kompatibel dengan asset UI yang digunakan.
- Backup database dan file upload secara terpisah.
- Review lisensi source template dan asset pihak ketiga sebelum deployment.

Data pribadi seperti NIP/NIPPK, NIK, NIB, email, telepon, identifier SDM, dan bukti sertifikat hanya dapat diakses pengguna berizin dan tidak boleh ditampilkan pada statistik publik.

---

## Instalasi Development

### Prasyarat

- PHP 8.3 atau lebih baru.
- MySQL 8.4 atau lebih baru.
- Composer.
- Laragon atau web server kompatibel PHP 8.3.
- Ekstensi PHP: `intl`, `mbstring`, `json`, `mysqli`, `openssl`, `curl`, dan `fileinfo`.

### Membuat proyek fresh

```bash
composer create-project codeigniter4/appstarter begawipbj
cd begawipbj
```

Jika repository resmi telah tersedia:

```bash
git clone <repository-url> begawipbj
cd begawipbj
composer install
```

### Dependency aplikasi

```bash
composer require dompdf/dompdf
composer require endroid/qr-code
```

Jangan memakai `app/Views/reference/fistudy/inc/PHPMailer/` sebagai dependency aplikasi. Gunakan Composer untuk dependency PHP production.

### Konfigurasi environment

```bash
cp .env.example .env
```

```ini
CI_ENVIRONMENT = development

app.baseURL = 'http://localhost:8080/'

database.default.hostname = localhost
database.default.database = db_begawipbj
database.default.username = root
database.default.password =
database.default.DBDriver = MySQLi
database.default.DBDebug = true
```

```bash
php spark key:generate
```

Jangan commit `.env`, kredensial database, encryption key, token, private key, atau secret lainnya.

### Integrasi source template UI

1. Simpan source FiStudy pada `app/Views/reference/fistudy/` sesuai struktur asli: HTML datar, `assets/`, `js/`, dan `inc/`.
2. Simpan source WowDash pada `app/Views/reference/wowdash/` sesuai struktur asli: HTML datar dan `assets/`.
3. Pecah markup yang diperlukan menjadi layout/component/view produksi.
4. Salin asset yang dipakai ke `public/assets/main/` atau `public/assets/dashboard/`.
5. Sesuaikan URL asset melalui helper/base URL CI4.
6. Ganti seluruh link demo, form demo, placeholder, dan endpoint template dengan route/controller BegawiPBJ.
7. Jangan membuat route yang merender file source reference langsung.

### Schema, wilayah, dan dummy data

Untuk schema SQL fresh V3:

```bash
mysql -u root -p < begawipbj-schema-fresh-v3.sql
mysql -u root -p db_begawipbj < database/reference/wilayah.sql
mysql -u root -p db_begawipbj < begawipbj-dummy-data-v3.sql
```

Atau gunakan CodeIgniter migration dan seeder setelah migration/seeder aplikasi telah diimplementasikan:

```bash
php spark migrate
php spark db:seed DatabaseSeeder
```

Data demo development dijalankan terpisah dan tidak boleh memodifikasi tabel `wilayah`.

### Menjalankan aplikasi

```bash
php spark serve
```

Akses:

```text
http://localhost:8080
```

Untuk Apache, Nginx, atau Laragon, arahkan document root ke `public/`.

---

## Testing

```bash
php spark test
```

Cakupan minimum:

- Login/logout, account status, reset password, session security, dan verifikasi email.
- Registrasi per tipe profil, validasi field bersyarat, serta approval profile/account.
- Validasi NIP/NIPPK ASN tanpa kewajiban NIK; validasi NIK untuk Penyedia, Akademisi, dan Masyarakat Umum.
- Perbedaan ASN Pemprov Lampung dan ASN luar Pemprov Lampung.
- Role default `public_user + participant` setelah approval.
- Larangan user mengubah role, permission, account status, atau data identitas sensitif tanpa approval.
- Dashboard hanya untuk Super Admin/Admin aktif dan berizin.
- Workspace hanya untuk role operasional aktif yang sesuai.
- Permission server-side dan ownership/assignment.
- Enrollment, pre-test, quiz best attempt, assignment revision, manual grading, post-test 60%, final grade, completion, feedback, dan certificate eligibility.
- Bookmark lintas konten, uniqueness bookmark, urutan bookmark terbaru, serta re-check authorization bookmark.
- Bunga Rampai multimedia, validasi URL video/sosial media, featured/carousel, akses media, dan file placeholder.
- Regulasi published dengan seluruh status hukum tetap tampil; relasi hukum, versioning, PDF preview, PDF download, dan log download.
- Penerbitan, verifikasi, expiry, revocation, nomor, token, dan sumber unik sertifikat.
- Registrasi event, kuota, waitlist, pembatalan, dan absensi.
- Upload file, akses file private, validasi PDF, dan kondisi `file_assets.path = NULL`.
- Query statistik publik tanpa data pribadi ASN.
- Integritas referensial dan larangan seeder dummy mengubah `wilayah`.
- Route produksi tidak merender view dari `app/Views/reference/`.

---

## Checklist Deployment

- Tetapkan `CI_ENVIRONMENT = production`.
- Gunakan HTTPS dan domain resmi pada `app.baseURL`.
- Gunakan akun MySQL aplikasi, bukan `root`.
- Tetapkan `database.default.DBDebug = false`.
- Simpan secret hanya di environment server/secret manager.
- Pastikan `.env` tidak masuk repository.
- Pastikan `public/` menjadi satu-satunya document root.
- Pastikan permission `writable/` hanya sebesar kebutuhan web server.
- Pastikan `writable/uploads/` tidak dapat diakses langsung tanpa authorization.
- Ganti/nonaktifkan kredensial administrator awal dan jangan deploy akun dummy.
- Nonaktifkan debug toolbar serta batasi akses log.
- Uji migration pada database salinan sebelum production.
- Uji RBAC, workflow, upload/download, PDF preview, assessment, sertifikat, QR verification, serta statistik publik.
- Uji validasi dan masking data NIP/NIPPK, NIK, NIB, serta data pribadi lain.
- Pastikan tidak ada asset/halaman source template yang tidak berlisensi atau tidak relevan ikut dipublikasikan tanpa review.
- Jadwalkan backup database dan `writable/uploads/` secara terpisah.
- Uji restore berkala.

---

## Konvensi Pengembangan

- Gunakan namespace `App\...` dan feature/domain folder konsisten.
- Controller menangani HTTP; service menangani logika bisnis; model menangani persistence; view menangani presentasi.
- Gunakan migration baru untuk setiap perubahan schema.
- Gunakan transaction untuk proses lintas tabel penting.
- Gunakan named route dan route group/filter.
- Gunakan pagination, filter, index, dan search pada data besar.
- Semua aksi sensitif dapat diaudit.
- File upload dikelola `FileStorageService` atau layanan setara.
- Gunakan `path = NULL` untuk metadata file tanpa berkas fisik dan tangani secara aman di UI/API.
- Jangan menyimpan secret/data production pada repository.
- Jangan merender reference template sebagai halaman produksi.
- Gunakan source HTML FiStudy/WowDash untuk mempercepat adaptasi, tetapi pisahkan menjadi layout, component, partial, serta view domain yang maintainable.
- Pastikan perubahan assessment/certificate mengikuti `assessment-and-certification-rules.md` dan disertai test.
- Pastikan perubahan registrasi, profil, role, permission, bookmark, media, regulasi, atau file PDF disertai update schema, ERD, data dictionary, service, dan test yang relevan.

---

## Dokumentasi Teknis

| Dokumen | Fungsi |
|---|---|
| `README.md` | Ringkasan produk, source UI, registrasi, modul, keamanan, instalasi, dan operasi dasar |
| `begawipbj-directory-architecture.md` | Struktur folder, namespace, UI boundary, source template aktual, service, filter, asset, testing |
| `assessment-and-certification-rules.md` | Aturan pre-test, quiz, assignment, post-test, nilai akhir, kelulusan, feedback, dan sertifikat |
| `begawipbj-database-blueprint.md` | Prinsip, domain, standar, migration, dan rancangan database |
| `begawipbj-database-erd.md` | Entity relationship diagram dan relasi lintas domain |
| `begawipbj-data-dictionary.md` | Kamus tabel, kolom, constraint, status, dan validasi data |
| `begawipbj-documentation-and-database-file-register.md` | Register dokumentasi, source template, asset, dan artefak database resmi |
| `begawipbj-schema-fresh-v3.sql` | Schema SQL fresh V3 untuk database kosong |
| `begawipbj-dummy-data-v3.sql` | Dummy data development/QA; tidak menyentuh master wilayah |
| `docs/architecture/rbac-matrix.md` | Matriks role dan permission |
| `docs/architecture/workflow-state-machine.md` | State machine workflow approval |
| `docs/database/migration-policy.md` | Aturan migration |
| `docs/deployment/security-checklist.md` | Checklist keamanan deployment |

---

## Prinsip Implementasi

BegawiPBJ menerapkan:

- Clean code dan separation of concerns.
- Secure coding dan privacy by design.
- MVC architecture dengan organisasi feature/domain.
- Role-Based Access Control dan server-side authorization.
- Auditability dan traceability.
- Data quality serta referential integrity.
- Maintainability dan scalability.
- Accessibility serta responsive UI.
- Pemisahan jelas Public Area, Workspace, dan Dashboard administratif.
- Pemanfaatan ulang source template FiStudy/WowDash secara terstruktur, legal, dan maintainable.
- Data-driven decision making dengan perlindungan data personal.
- Akses arsip regulasi dan pengetahuan secara berkelanjutan dengan konteks status yang jelas.

---

## Lisensi

Lisensi aplikasi ditentukan oleh Pemerintah Provinsi Lampung atau pemilik resmi sistem BegawiPBJ.

Lisensi FiStudy, WowDash, font, icon, image, JavaScript library, CSS library, dan asset pihak ketiga yang digunakan harus dipatuhi sesuai ketentuan masing-masing sebelum aplikasi didistribusikan atau dideploy.
