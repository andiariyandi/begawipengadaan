# BegawiPBJ Data Classification

Dokumen ini menetapkan klasifikasi, pembatasan akses, tampilan publik, penyimpanan, ekspor, dan perlindungan data BegawiPBJ. Cakupan Government Intelligence hanya Pemerintah Provinsi Lampung; akses statistik publik hanya pada informasi agregat.

## Kelas data

| Kelas | Definisi | Contoh | Default akses |
|---|---|---|---|
| Publik | Disetujui untuk dipublikasikan | Berita published, metadata regulasi published, agenda, agregat PBJ, status sertifikat minimum | Semua pengunjung |
| Internal | Diperlukan untuk operasional instansi | Draft, antrean approval, roster pelatihan, konfigurasi non-secret, metrik internal | Akun aktif dengan izin |
| Terbatas | Data pribadi atau bukti operasional yang berisiko bila terekspos | Nama individual ASN, NIP, NIK, email, telepon, riwayat JF PPBJ, skor individu, submission, bukti kompetensi, sertifikat PDF | Owner dan role dengan permission/scope |
| Rahasia sistem | Secret dan kredensial yang tidak boleh diungkap | Password hash, encryption key, reset token, secret API, credential DB | Komponen server dan operator berwenang sesuai tugas |

Klasifikasi berlaku untuk kolom database **dan** turunannya: file, export, API JSON, cache, audit payload, screenshot UAT, backup, dan notifikasi.

## Pemetaan tabel/kolom

| Data | Kelas | Aturan |
|---|---|---|
| `users.username` | Internal | Tidak ditampilkan pada statistik publik |
| `users.email`, `user_profiles.phone` | Terbatas | Hanya pemilik dan admin/operator berizin |
| `users.password_hash` | Rahasia sistem | Tidak masuk response/export/audit before-after |
| `user_profiles.full_name` | Terbatas | Publik hanya jika ada kebijakan/verifikasi minimum yang disetujui |
| `user_asn_profiles.nip`, `user_public_profiles.identity_number` | Terbatas | Tidak masuk URL, QR, statistik, atau log umum |
| `organization_units.name` | Publik/internal | Nama OPD dapat publik; informasi personel tidak otomatis publik |
| `enrollments`, `learning_progress`, `final_grades` | Terbatas | Peserta pemilik; trainer/evaluator terikat penugasan; admin berizin |
| Artikel/FAQ/studi kasus/template published | Publik untuk metadata dan konten yang disetujui | Dokumen file tetap perlu penilaian visibility dan ketersediaan |
| Regulasi published | Publik untuk metadata dan versi yang disetujui | Status hukum revoked tetap terlihat jelas |
| `event_registrations`, `event_attendances` | Terbatas | Jangan publikasikan daftar peserta tanpa kebijakan |
| Statistik agregat PBJ | Publik bila disetujui | Hindari sel kecil yang memungkinkan identifikasi individu |
| `jf_ppbj_personnel` dan histori | Terbatas | Nama, identifier, status personel hanya untuk role berizin |
| `competency_certificates` individual | Terbatas | Bukti dan nomor individual tidak ada pada statistik publik |
| `audit_logs` | Internal/Terbatas | Payload dapat mengandung data terbatas; mask secret |
| `file_assets.path`, `stored_name` | Internal | Jangan diberikan melalui API publik |
| `certificates.verification_token` | Rahasia operasional | Token bearer untuk verifikasi; jangan echo token pada respons publik |
| `certificate_number`, issued status | Publik terbatas | Hanya melalui kebijakan verifikasi yang ditetapkan |

## Akses berdasarkan konteks UI

| Konteks | Yang boleh tampil | Yang tidak boleh tampil |
|---|---|---|
| Landingpage | Konten published, daftar agenda, regulasi, statistik agregat | Draft, skor individu, NIP/NIK, daftar individu ASN |
| Workspace Peserta | Profil, kelas, progress, submission, nilai, sertifikat sendiri | Data peserta lain |
| Workspace Trainer | Roster/progress kelas yang ditugaskan dan data yang diperlukan mengajar | NIP/identifier tanpa kebutuhan sah; kelas lain |
| Workspace Evaluator | Submission dan data minimum untuk menilai kelas yang ditugaskan | Data pribadi yang tidak relevan |
| Workspace Operator | Data verifikasi dan tugas yang didelegasikan | Konfigurasi inti, audit global, ekspor bebas |
| Dashboard Admin | Data sesuai permission dan tugas | Secret sistem, akses tanpa alasan bisnis |
| Dashboard Super Admin | Kontrol penuh aplikasi | Secret tidak perlu ditampilkan mentah pada UI |

## Aturan statistik publik

- Hanya angka agregat: jumlah JF PPBJ, distribusi jenjang, jumlah sertifikat, partisipasi pelatihan, dan gap kompetensi sesuai persetujuan pemilik data.
- Jangan menampilkan nama, NIP/NIK, email, telepon, nomor dokumen bukti, atau row-level personel.
- Jika kelompok agregat sangat kecil dan dapat mengidentifikasi individu, gabungkan kategori atau sembunyikan sesuai ambang yang ditetapkan pemilik data. Ambang numerik harus diputuskan oleh pemilik data sebelum produksi, bukan diasumsikan.
- OPD dalam statistik hanya Pemerintah Provinsi Lampung; kabupaten/kota tidak masuk dataset intelligence organisasi.
- Query publik harus memakai DTO/serializer khusus dengan whitelist field, bukan `SELECT *` dari tabel personel.
- Export publik, bila tersedia, menggunakan aturan yang sama dengan tampilan publik.

## Sertifikat dan verifikasi publik

| Field | Halaman verifikasi publik | Catatan |
|---|---|---|
| Status valid/expired/revoked/not found | Ya | Wajib akurat |
| Nomor sertifikat | Ya bila disetujui | Tidak menjadi satu-satunya faktor keamanan |
| Tanggal terbit/masa berlaku | Ya bila relevan | Sesuai jenis sertifikat |
| Nama penerima | Putusan pemilik data | Minimum atau masking bila diperlukan |
| Nama course/event | Ya bila disetujui | Bukan sumber data pribadi tambahan |
| NIP/NIK, email, telepon | Tidak | Tidak ada di QR maupun response |
| Verification token mentah | Tidak di body hasil | Token ada di URL yang diserahkan pemegang; jangan log path penuh |
| PDF sertifikat | Tidak otomatis | Akses download hanya pemilik/admin berizin kecuali diputuskan berbeda |

QR memuat URL/token acak; tidak menyematkan biodata lengkap. Endpoint verifikasi diberi rate limit dan log akses yang meminimalkan IP/user agent sesuai kebijakan retensi.

## Dokumen dan file

| Visibility | Contoh | Akses |
|---|---|---|
| `public` | Cover dan berkas regulasi published yang disetujui | Endpoint terkontrol |
| `protected` | Materi kelas, template terbatas, bukti kompetensi | Akun aktif + enrollment/permission yang relevan |
| `private` | Submission peserta, sertifikat PDF individu | Pemilik atau admin/evaluator terotorisasi sesuai tugas |

- Berkas fisik disimpan di `writable/uploads/<domain>/` dan tidak diekspos sebagai web root.
- `file_assets.path = NULL`: metadata placeholder, tidak boleh ditawarkan untuk download.
- Selalu verifikasi status entity sumber selain visibility file; label `public` tidak cukup bila regulasi masih draft.
- Akses file sensitif dicatat dalam audit log; metadata `original_name` disanitasi saat ditampilkan.

## Pengumpulan dan minimisasi

- Minta data hanya untuk fungsi yang disetujui. NIK pada profil publik opsional dan tidak boleh diwajibkan tanpa kebutuhan yang ditetapkan.
- Data JF PPBJ dan pemilik sertifikat kompetensi boleh tidak memiliki akun (`user_id NULL`); jangan membuat akun hanya untuk menampung master data.
- Bedakan nomor sertifikat digital BegawiPBJ dari nomor sertifikat kompetensi resmi yang dicatat pada intelligence.
- Jangan menduplikasi NIP di berbagai export, notifikasi, atau JSON audit tanpa kebutuhan.
- Data dummy memakai identitas sintetik dan domain email non-operasional.

## Logging, masking, dan backup

- Jangan log password, session cookie, reset token, encryption key, NIK/NIP utuh, atau isi berkas.
- `audit_logs.before_data`/`after_data` memakai field whitelist; mask/remove nilai terbatas atau rahasia.
- Notifikasi email tidak perlu memuat identitas pegawai lengkap atau tautan file privat tanpa autentikasi.
- Backup database dan `writable/uploads/` harus disimpan bersama untuk konsistensi referensi, dengan akses terbatas dan enkripsi bila memungkinkan.
- Dump produksi tidak masuk Git atau lingkungan pengembangan tanpa sanitasi dan persetujuan.

## Retensi dan penghapusan

| Kelompok | Kebijakan awal | Keputusan sebelum produksi |
|---|---|---|
| User dan profil | Gunakan deactivation/soft delete bila histori terkait | Masa retensi, dasar penghapusan, proses permintaan subjek data |
| Submission/nilai/sertifikat | Pertahankan histori sesuai mandat operasional | Jadwal retensi instansi dan bukti kelulusan |
| Approval/audit | Tidak dihapus cascade | Masa retensi, arsip dan akses auditor |
| Log teknis/verifikasi QR | Simpan seperlunya | Durasi, masking IP/user-agent, rotasi |
| Backup | Jadwal/rotasi dan uji restore | RPO/RTO dan masa simpan resmi |

Durasi retensi tidak ditetapkan secara spekulatif; harus disahkan oleh pengelola data/instansi sesuai kebijakan yang berlaku.

## Prosedur perubahan klasifikasi

1. Pemilik domain mengusulkan perubahan dengan data dan tujuan pemakaian.
2. Admin keamanan/privasi menilai dampak publikasi, export, API, cache, notifikasi, dan backup.
3. RBAC matrix, route map, serializers, serta test privasi diperbarui.
4. Reviewer berwenang menyetujui sebelum deployment.
5. Perubahan dicatat di audit/ADR bila memengaruhi kebijakan lintas modul.

## Pengujian minimum

- Guest tidak bisa mengakses dokumen protected/private.
- Peserta tidak bisa membaca nilai, submission, atau sertifikat user lain.
- Trainer/Evaluator hanya melihat kelas yang ditugaskan.
- JSON statistik publik tidak mengandung field identitas.
- Verifikasi QR tidak mengungkap NIP/NIK atau token dalam response body.
- Export internal memerlukan permission dan tercatat.
- File `path NULL` ditolak untuk download.
- Error 403/404 tidak membocorkan path, query, atau identitas internal.
