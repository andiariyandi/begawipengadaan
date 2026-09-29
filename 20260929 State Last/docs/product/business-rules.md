# BegawiPBJ Business Rules

Dokumen ini menetapkan aturan bisnis BegawiPBJ untuk proses pengguna, pembelajaran, pengetahuan, regulasi, event, sertifikat, dan intelligence Pemerintah Provinsi Lampung. Aturan ini melengkapi blueprint database, ERD, data dictionary, RBAC matrix, workflow state machine, route map, dan test plan.

## Status keputusan

- `Ditetapkan`: berasal dari scope/keputusan BegawiPBJ dan dapat menjadi kontrak implementasi.
- `Perlu keputusan pemilik proses`: parameter/tata kelola yang belum ditetapkan. Jangan mengarang angka atau memaksakan perilaku production.
- Semua perubahan aturan perlu dicatat pada dokumen ini, diikuti pembaruan service, permission, migration bila perlu, dan test.

## BR-01 Identitas, profil, wilayah

| ID | Aturan | Status |
|---|---|---|
| BR-01.01 | User mempunyai akun dengan username/email unik dan satu profil umum. | Ditetapkan |
| BR-01.02 | Kategori profil `asn`, `provider`, `academic_practitioner`, atau `public` berbeda dari role aplikasi. | Ditetapkan |
| BR-01.03 | User dapat memiliki lebih dari satu role melalui `user_roles`; role Super Admin/Admin/Operator hanya untuk profil ASN. | Ditetapkan |
| BR-01.04 | Akun `pending`, `inactive`, `rejected`, dan `suspended` tidak dapat memakai Workspace/Dashboard. | Ditetapkan |
| BR-01.05 | Master wilayah menggunakan `wilayah(kode,nama)`, kode provinsi Lampung `18`; hierarki berdasarkan prefix dan panjang kode 2/5/8/13. | Ditetapkan |
| BR-01.06 | Kode wilayah profil harus ada pada master dan konsisten antarlevel; kode anak bukan input nama bebas. | Ditetapkan |
| BR-01.07 | Persyaratan dokumen identitas untuk verifikasi setiap kategori profil ditentukan pemilik proses. | Perlu keputusan pemilik proses |

## BR-02 UI, role, dan scope

| ID | Aturan | Status |
|---|---|---|
| BR-02.01 | Landingpage dan Workspace menggunakan Main UI/template/asset yang sama; Dashboard memakai UI/asset terpisah. | Ditetapkan |
| BR-02.02 | Hanya Super Admin dan Admin yang dapat mengakses `/admin/*`; role operasional menggunakan `/workspace/*`. | Ditetapkan |
| BR-02.03 | FiStudy dan WowDash hanya referensi desain, tidak memiliki route produksi. | Ditetapkan |
| BR-02.04 | Permission memutuskan tindakan; scope/ownership memutuskan record yang boleh disentuh. | Ditetapkan |
| BR-02.05 | Trainer dibatasi kelas pada `class_trainers`; Evaluator dibatasi kelas pada `class_evaluators`; Peserta dibatasi data sendiri. | Ditetapkan |
| BR-02.06 | Operator menangani administrasi dan approval hanya berdasarkan delegasi dan permission, bukan akses dashboard. | Ditetapkan |

## BR-03 Konten dan approval

| ID | Aturan | Status |
|---|---|---|
| BR-03.01 | Konten yang memerlukan review mengikuti `draft → submitted → in_review → approved → published`. | Ditetapkan |
| BR-03.02 | `revision_required`, `rejected`, `archived`, `cancelled` ditangani oleh transition resmi; alasan wajib untuk penolakan/revisi/cancel. | Ditetapkan |
| BR-03.03 | Konten hanya tampil publik ketika `published`, kecuali regulasi revoked yang tetap published dengan label status hukum yang jelas. | Ditetapkan |
| BR-03.04 | Setiap transition menyimpan actor, waktu, state asal/tujuan, dan histori; aksi sensitif juga masuk audit log. | Ditetapkan |
| BR-03.05 | Publish memerlukan permission domain dan tidak otomatis mengikuti izin manage/edit. | Ditetapkan |
| BR-03.06 | Daftar konten yang boleh memakai workflow singkat ditetapkan oleh pemilik konten, tetapi tetap memerlukan audit dan izin publish. | Perlu keputusan pemilik proses |

## BR-04 Arena Belajar

| ID | Aturan | Status |
|---|---|---|
| BR-04.01 | Hirarki pembelajaran: Program → Course → Class, Course → Module → Lesson; assessment dan hasil melekat pada kelas/enrollment. | Ditetapkan |
| BR-04.02 | Satu peserta hanya satu enrollment per class: `UNIQUE(user_id,learning_class_id)`. | Ditetapkan |
| BR-04.03 | Pendaftaran kelas membutuhkan kelas eligible, periode dibuka, kapasitas tersedia, dan akun aktif; kuota diproses dalam transaction. | Ditetapkan |
| BR-04.04 | Quiz objektif mendukung `multiple_choice`, `multiple_response`, dan `true_false`; auto-grading, attempt limit, passing grade. | Ditetapkan |
| BR-04.05 | Quiz attempt hanya dibuat untuk enrollment sah dan dalam periode aktif; jawaban dikunci setelah submit/expired. | Ditetapkan |
| BR-04.06 | Assignment mendukung essay dan upload; manual grading oleh Evaluator sesuai penugasan. | Ditetapkan |
| BR-04.07 | Nilai assignment harus dalam rentang 0 sampai `max_score`; koreksi final grade memerlukan alasan, audit, dan permission. | Ditetapkan |
| BR-04.08 | Bobot kelas `quiz_weight + assignment_weight = 100`; `final_score = (quiz_score × quiz_weight + assignment_score × assignment_weight) / 100` saat bobot disimpan sebagai angka persen. | Ditetapkan |
| BR-04.09 | `passed` bila final_score >= passing_grade dan seluruh syarat belajar yang diwajibkan terpenuhi; nilai akhir disimpan sekali per enrollment. | Ditetapkan |
| BR-04.10 | Cara memilih attempt terbaik/terakhir, agregasi beberapa quiz/tugas, dan ambang penyelesaian lesson wajib ditetapkan per kelas/program. | Perlu keputusan pemilik proses |
| BR-04.11 | Arti `quota = 0` (tidak terbatas atau pendaftaran tertutup) harus ditetapkan dan diterapkan konsisten. | Perlu keputusan pemilik proses |

Catatan implementasi: untuk bobot 50/50, skor 90 dan 80 menghasilkan `final_score = 85`, bukan 8.500. Formulasi di dokumen lain yang menulis `quiz_score × quiz_weight + assignment_score × assignment_weight` mengasumsikan bobot proporsi 0–1; aplikasi wajib memilih satu representasi dan konsisten. Schema saat ini memakai 0–100 dengan `CHECK` jumlah 100, sehingga service membagi 100.

## BR-05 Bunga Rampai

| ID | Aturan | Status |
|---|---|---|
| BR-05.01 | Artikel, FAQ, studi kasus, template memakai kategori/tag/metadata sesuai jenis konten. | Ditetapkan |
| BR-05.02 | Bookmark unik per user dan objek; type hanya `article`, `case_study`, `document_template`. | Ditetapkan |
| BR-05.03 | Template published tidak boleh ditawarkan untuk download bila file asset `path = NULL` atau file fisik hilang. | Ditetapkan |
| BR-05.04 | Metadata publikasi dan attachment perlu valid sebelum approval/publish sesuai kebijakan konten. | Ditetapkan |

## BR-06 Regulasi PBJ

| ID | Aturan | Status |
|---|---|---|
| BR-06.01 | Identitas regulasi unik berdasarkan kategori, nomor, dan tahun. | Ditetapkan |
| BR-06.02 | Regulasi memiliki metadata kategori, status hukum, tahap PBJ, kompetensi, keyword, dan versi dokumen. | Ditetapkan |
| BR-06.03 | Hanya satu `regulation_versions.is_current = 1` per regulasi; pergantian current dilakukan atomik. | Ditetapkan |
| BR-06.04 | Status hukum `active`, `revoked`, `amended`, `expired` terpisah dari workflow publish. | Ditetapkan |
| BR-06.05 | Hanya versi published dan file tersedia yang dapat diunduh publik; unduhan tercatat sesuai kebijakan. | Ditetapkan |
| BR-06.06 | Link sumber regulasi hanya dinyatakan resmi setelah diverifikasi Admin/pengelola. | Ditetapkan |

## BR-07 Berita dan event

| ID | Aturan | Status |
|---|---|
| BR-07.01 | Berita publik hanya bila `published`. | Ditetapkan |
| BR-07.02 | Event mendukung `coming_soon`, `open_registration`, `full`, `finished`, `cancelled`; workflow publish berdiri terpisah. | Ditetapkan |
| BR-07.03 | Registrasi unik per event-user; kuota diperiksa secara atomik sehingga tidak oversubscribe. | Ditetapkan |
| BR-07.04 | Satu event registration maksimal satu attendance record. | Ditetapkan |
| BR-07.05 | `present`, `absent`, `excused` dibedakan; eligibility sertifikat event mensyaratkan event selesai dan kehadiran sesuai kebijakan. | Ditetapkan |
| BR-07.06 | Aturan waitlist, batas pembatalan, definisi `quota = 0`, serta apakah `excused` layak sertifikat harus disahkan pemilik event. | Perlu keputusan pemilik proses |
| BR-07.07 | Pembatalan event memerlukan alasan dan pemberitahuan kepada registran. | Ditetapkan |

## BR-08 Sertifikat digital

| ID | Aturan | Status |
|---|---|---|
| BR-08.01 | Sumber sertifikat `learning_enrollment` atau `event_registration`; unik pada `(source_type,source_id)`. | Ditetapkan |
| BR-08.02 | Sertifikat pembelajaran hanya terbit untuk enrollment lulus dan eligible. | Ditetapkan |
| BR-08.03 | Sertifikat event hanya terbit untuk registrasi sah pada event selesai dengan kehadiran eligible. | Ditetapkan |
| BR-08.04 | Nomor sertifikat dan verification token harus unik; sequence diambil atomik dalam transaction. | Ditetapkan |
| BR-08.05 | QR/token tidak memuat NIP/NIK/email; verifikasi publik hanya menampilkan status dan data minimum yang disetujui. | Ditetapkan |
| BR-08.06 | Reissue PDF tidak membuat source atau sertifikat kedua; alasan/versi file harus tercatat. | Ditetapkan |
| BR-08.07 | Sertifikat revoked/expired tetap dipertahankan dengan status yang benar pada verifikasi publik. | Ditetapkan |
| BR-08.08 | Masa berlaku sertifikat pembelajaran/event, format nomor resmi, dan tingkat keterbukaan nama penerima perlu diputuskan pengelola. | Perlu keputusan pemilik proses |
| BR-08.09 | `file_assets.path=NULL` berarti PDF belum tersedia; sertifikat record/status dapat diuji tetapi download dinonaktifkan. | Ditetapkan |

## BR-09 Government Intelligence

| ID | Aturan | Status |
|---|---|---|
| BR-09.01 | Cakupan organisasi hanya Pemerintah Provinsi Lampung; master organisasi: root Pemprov dan 49 unit sebagaimana daftar disetujui. | Ditetapkan |
| BR-09.02 | Data personel JF PPBJ berfokus pada Biro Pengadaan Barang dan Jasa; riwayat mutasi/jenjang/pensiun/nonaktif disimpan. | Ditetapkan |
| BR-09.03 | Pemilik sertifikat kompetensi mencakup OPD Pemprov Lampung dan jenis PBJ Level 1, PPK A, PPK B, PPK C. | Ditetapkan |
| BR-09.04 | Personel/pemilik kompetensi dapat tercatat tanpa akun aplikasi: `user_id NULL`. | Ditetapkan |
| BR-09.05 | Nomor sertifikat kompetensi unik per jenis; bukti dokumen protected. | Ditetapkan |
| BR-09.06 | Snapshot gap unik per OPD, periode, metrik; `gap_count = required_count - available_count` dan dapat negatif bila tersedia > kebutuhan. | Ditetapkan |
| BR-09.07 | Definisi setiap metric_code, kebutuhan target, sumber data, frekuensi snapshot, dan penanggung jawab validasi disahkan pemilik data. | Perlu keputusan pemilik proses |

## BR-10 File, privasi, dan audit

| ID | Aturan | Status |
|---|---|---|
| BR-10.01 | Semua file fisik disimpan di `writable/uploads/<domain>/`, metadata pada `file_assets`. | Ditetapkan |
| BR-10.02 | `path NULL` adalah placeholder; hanya path yang terisi, file ada, dan authorization lulus yang bisa di-stream. | Ditetapkan |
| BR-10.03 | `public`, `protected`, `private` adalah klasifikasi file; status parent entity dan ownership juga tetap diperiksa. | Ditetapkan |
| BR-10.04 | Statistik publik hanya agregat dan tidak memuat identitas ASN. | Ditetapkan |
| BR-10.05 | Akses/ubah data terbatas dan ekspor internal harus berpermission; aksi sensitif diaudit. | Ditetapkan |
| BR-10.06 | Retensi, ambang agregat kecil, masking nama verifikasi sertifikat, dan kebijakan data pribadi ditetapkan pengelola data sebelum go-live. | Perlu keputusan pemilik proses |

## Keputusan operasional yang harus ditutup

| Kode | Pertanyaan untuk pemilik proses | Dampak bila belum diputuskan |
|---|---|---|
| DEC-01 | Apakah `quota=0` berarti tidak terbatas atau pendaftaran ditutup (kelas/event)? | Registrasi tidak dapat didefinisikan konsisten |
| DEC-02 | Nilai quiz terbaik/terakhir/rata-rata dan bagaimana beberapa tugas digabung? | Final grade dan kelulusan berbeda antarimplementasi |
| DEC-03 | Apakah seluruh lesson wajib selesai sebelum kelulusan? | Eligibility sertifikat belum final |
| DEC-04 | Apa syarat minimal kehadiran event, termasuk `excused`? | Sertifikat event berisiko tidak sah |
| DEC-05 | Apakah sertifikat punya masa berlaku dan format nomor baku? | Sequence dan verifikasi perlu aturan final |
| DEC-06 | Apa kebijakan ambang kelompok statistik publik dan tampilan nama penerima sertifikat? | Risiko identifikasi individu |
| DEC-07 | Apa sumber angka kebutuhan per OPD dan periode pembaruan? | Gap competence tidak dapat diinterpretasikan |
| DEC-08 | Apa syarat dokumen tiap kategori registrasi serta otoritas verifikasinya? | Approval akun tidak konsisten |

## Keterkaitan test

Setiap BR yang berstatus Ditetapkan harus memiliki minimal satu unit/feature/UAT test. Gunakan ID BR pada nama/deskripsi test agar aturan dapat ditelusuri. Sebuah keputusan operasional yang masih terbuka tidak boleh disamarkan sebagai aturan final dalam service atau seeder production.
