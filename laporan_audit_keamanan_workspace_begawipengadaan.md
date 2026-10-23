# Laporan Audit Keamanan (Security Audit) - Workspace BegawiPengadaan

> **Tanggal Audit:** 23 Oktober 2026
> **Lingkup:** Ekosistem `/workspace/*` - 11 controller, filter, route, view, service, model
> **Lingkungan:** Local Laragon / PHP 8.x / MySQL / CodeIgniter v4.7.4
> **Metode:** Static analysis read-only + verifikasi skema DB (`information_schema`) + inspeksi konfigurasi web server
> **Perubahan Kode:** **NOL.** Audit ini sepenuhnya read-only.
> **Catatan:** Laporan ini melengkapi (bukan menggantikan) `laporan_audit_workspace_begawipengadaan.md`, yang merupakan **smoke-test fungsional/rute** ("100% PASS") - bukan audit keamanan.

---

## 1. Ringkasan Eksekutif

### 1.1 Health Score: **5.5 / 10 - perlu tindakan segera**

Yang **benar** secara keamanan: praktik ownership di service layer (`LearningService`), proteksi unduhan sertifikat, konsistensi CSRF, dan filename acak pada sebagian besar upload. Hygiene IDOR di lapisan service justru **lebih baik** daripada di lapisan controller.

Yang **rusak**: satu celah **Remote Code Execution**, aturan batas waktu kuis yang **tidak pernah ditegakkan**, dan **gerbang kelayakan sertifikat yang dapat dilewati sepenuhnya** oleh operator.

### 1.2 Distribusi Temuan

| Severity | Jumlah | Ringkasan |
|---|---|---|
| **CRITICAL** | 1 | Unrestricted file upload ke webroot (RCE) |
| **HIGH** | 8 | Batas waktu kuis, kelayakan sertifikat, segregation of duties, transaksi, stale session, brute-force presensi, privilege escalation |
| **MEDIUM** | 10 | Race condition submit, N+1, upload tanpa validasi, IDOR materi event, log forensik rusak |
| **LOW** | 7 | Existence oracle, HTTP 500, hardcoded user ID, dan lainnya |

### 1.3 Tiga Rantai Eksploitasi Terpenting

**Rantai 1 - RCE (C-1):**

1. Trainer login.
2. POST `cover_image=@shell.php` ke `/workspace/trainer/kelas/{id}/ubah`.
3. Berkas mendarat di `public/uploads/learning/covers/class_<ts>_<hex>.php`.
4. Apache mengeksekusi `.php` di webroot (tidak ada `.htaccess` penghalang) -> **RCE penuh**.

**Rantai 2 - Batas waktu (H-1):**

1. Peserta memulai `post_test` berdurasi 10 menit.
2. Menunggu / mengubah jam di OS.
3. POST langsung ke `/workspace/peserta/quiz/attempt/{id}/kirim`.
4. `LearningService::submitQuiz` tidak pernah mengecek deadline -> tetap dinilai, `evaluateCompletion()` dijalankan.

**Rantai 3 - Sertifikat palsu (H-2):**

1. Operator login.
2. POST `enrollment_ids[]` berisi ID enrollment peserta yang tidak pernah hadir.
3. `issueSingleCertificate()` tidak mengecek progress / nilai / kelulusan.
4. Enrollment dipaksa `completed` + `certificate_eligible_at`; sertifikat resmi terbit dalam satu klik.

> **H-1 dan H-2 saling menguatkan.** Skor post-test yang diperoleh lewat bypass durasi menjadi input `evaluateCompletion` -> kelulusan, dan pada agenda self-paced menjadi lanjutan ke penerbitan sertifikat.

---

## 2. Temuan CRITICAL

### C-1 - Unrestricted File Upload leading to Remote Code Execution

| | |
|---|---|
| **File** | `app/Controllers/Workspace/TrainerController.php:2225-2232` |
| **Method** | `updateClass(string $classId)` (mulai line 2152) |
| **Severity** | **CRITICAL** |
| **CVSS est.** | 8.8 (AV:N/AC:L/PR:L/UI:N/S:C/C:H/I:H/A:H) |
| **Prasyarat** | Akun **trainer** yang ditugaskan pada minimal 1 kelas (`assertTrainerOf`, line 2156) |

**Bukti - kode:**

    // TrainerController.php:2225-2232
    $coverFile = $this->request->getFile('cover_image');
    if ($coverFile && $coverFile->isValid() && ! $coverFile->hasMoved()) {  // isValid() = error PHP saja
        $targetDir = FCPATH . 'uploads/learning/covers/';                  // WEBROOT
        ...
        $newFilename = 'class_' . time() . '_' . bin2hex(random_bytes(4))
                     . '.' . $coverFile->getClientExtension();             // EKSTENSI DARI ATTACKER
        $coverFile->move($targetDir, $newFilename);
    }

**Rantai kelayakan - sudah diverifikasi:**

1. Blok validasi method (line 2164-2201) **tidak memuat validasi berkas sama sekali** - tidak ada allowlist ekstensi, cek MIME, maupun batas ukuran.
2. `FCPATH` = `public/` = document root Apache (dikonfirmasi: `public/.htaccess` dan `index.php` berada di sana).
3. `public/uploads/learning/covers/` **tidak memiliki `.htaccess`** - diverifikasi rekursif, tidak ada satu pun di seluruh `public/uploads/`.
4. `public/.htaccess:32` -> `RewriteCond %{REQUEST_FILENAME} !-f` -> berkas yang **ada** dilayani langsung oleh Apache, bukan ke front controller. Artinya `.php` di `uploads/` **dieksekusi**.
5. `public/.htaccess:53` -> `upload_max_filesize = 128M` (batas besar, sekaligus menjadi vektor resource exhaustion).

**Bandingkan dengan implementasi benar di file yang sama** (`guide_file`, line 1672-1685) - bukti bahwa pola ini sudah dikenal, hanya terlewat di sini:

    $ext = strtolower($guideFile->getClientExtension());
    $mime = $guideFile->getClientMimeType();
    if ($ext !== 'pdf' && ! str_contains($mime, 'pdf')) {   // validasi ada
        return redirect()->back()->withInput()->with('error', '...wajib berformat PDF.');
    }
    $newFilename = 'guide_' . time() . '_' . bin2hex(random_bytes(4)) . '.pdf';  // ekstensi di-hardcode

**Skenario eksploitasi:**

1. Trainer terautentikasi membuka form **Ubah Detail Kelas**.
2. Craft request `cover_image=@webshell.php` via `multipart/form-data`.
3. Berkas mendarat di `https://host/uploads/learning/covers/class_<unix_ts>_<8hex>.php`.
4. `GET https://host/uploads/learning/covers/class_<ts>_<hex>.php` -> **eksekusi PHP**.
5. Dampak: takeover server, baca `app/Config/Database.php` / `.env` (kredensial DB), pivot ke database.

**Tambahan di method yang sama:** tidak ada transaksi antara insert `file_assets` (line 2235) dan update `learning_classes` (line 2249-2250) - kegagalan di tengah menyisakan record yatim.

**Remediasi:**

1. **Segera:** taruh `.htaccess` di `public/uploads/` dengan `php_flag engine off` + handler untuk `php|php3|php4|php5|php7|phtml|phar|pl|py|cgi|asp|aspx|jsp|sh`. Ini mitigasi berlapis yang **menutup sekaligus** ketiga titik upload, termasuk yang belum terinventarisasi.
2. **Segera:** validasi allowlist ketat di `updateClass()`:

       $ext = strtolower($coverFile->getClientExtension());
       if (! in_array($ext, ['jpg','jpeg','png','webp'], true)) {
           return redirect()->back()->withInput()->with('error', 'Format sampul harus JPG/PNG/WEBP.');
       }
       if ($coverFile->getSize() > 2 * 1024 * 1024) {
           return redirect()->back()->withInput()->with('error', 'Ukuran sampul maksimal 2 MB.');
       }
       $mime = $coverFile->getMimeType();   // MIME server-side, BUKAN getClientMimeType()
       if (! in_array($mime, ['image/jpeg','image/png','image/webp'], true)) {
           return redirect()->back()->withInput()->with('error', 'Berkas bukan gambar yang valid.');
       }
       $newFilename = 'class_' . time() . '_' . bin2hex(random_bytes(8)) . '.' . $ext;  // dari allowlist

3. **Jangka menengah:** pindahkan seluruh upload yang terkendali user keluar dari document root (`WRITEPATH . 'uploads/...'` + controller streaming), dan layarkan lewat `FileStorageService` yang sudah ada (`app/Services/FileStorageService.php`).

---

## 3. Temuan HIGH

### H-1 - Batas Waktu Kuis Tidak Pernah Ditegakkan di Server

| | |
|---|---|
| **File** | `app/Services/LearningService.php:218-288`, `app/Controllers/Workspace/ParticipantController.php:1846-1904` |
| **Route** | `POST workspace/peserta/quiz/attempt/(:segment)/kirim` (`Routes.php:175`) |

**Bukti.** `duration_minutes` hanya diperiksa di `startQuiz` (line 177-186) dan di auto-submit pada GET `takeQuiz` (`ParticipantController.php:1756-1764`). Verifikasi grep terhadap seluruh `LearningService.php`: kemunculan `duration` / `deadline` **hanya** di line 177-180 - tidak satu pun di dalam `submitQuiz`.

`ParticipantController::submitQuiz` (1846-1866) juga tidak memeriksa apa pun selain `status !== 'started'`.

**Skenario eksploitasi.** Peserta memulai post-test berdurasi 10 menit. Alih-alih menunggu, ia mengirim request langsung (curl / Postman / devtools) ke endpoint kirim. Karena tidak ada validasi waktu di server, jawaban dinilai normal, attempt di-set `graded`, lalu `evaluateCompletion()` dieksekusi (line 285) - membuka jalan ke kelulusan dan, pada agenda self-paced, ke penerbitan sertifikat.

**Dampak.** Aturan `duration_minutes` sepenuhnya kosmetik. Timer di view (`quiz_take.php:456-816`) murni client-side.

**Remediasi.** Tambahkan pemeriksaan deadline di dalam `submitQuiz` **sebelum** transaksi dimulai, dan **toleran** karena auto-submit dari `takeQuiz` juga melewati jalur ini:

    $duration = (int) ($context['quiz']['duration_minutes'] ?? 0);
    if ($duration > 0 && ! empty($attempt['started_at'])) {
        $deadline = strtotime($attempt['started_at']) + $duration * 60;
        if (time() > $deadline + 120) {          // grace period 120 detik
            throw new RuntimeException('Waktu pengerjaan ujian telah habis.');
        }
    }

Karena `submitQuiz` sudah dibungkus transaksi dan sudah melempar exception sebelum menulis apa pun bila gagal, penempatan di atas `$this->db->transStart()` (line 234) aman.

---

### H-2 - Penerbitan Sertifikat Operator Melewati Seluruh Aturan Kelayakan

| | |
|---|---|
| **File** | `app/Controllers/Workspace/OperatorController.php:1304-1358` (`issueSingleCertificate`) |
| **Dipanggil dari** | `issueCertificate` (1264), `bulkIssueCertificates` (1280) |

**Bukti.** `issueSingleCertificate` **tidak memuat satu pun** pemeriksaan kelayakan: tidak cek `learning_progress` 100%, tidak cek skor quiz vs `passing_grade`, tidak cek lesson wajib selesai, tidak cek tugas sudah dinilai, tidak cek status enrollment.

Kemudian baris 1347-1353, enrollment **dipaksa**:

    $this->db->table('enrollments')->where('id', $eid)->update([
        'status'                  => 'completed',
        'completed_at'            => $enrollment['completed_at'] ?? $now,
        'passed_at'               => $enrollment['passed_at'] ?? $now,
        'certificate_eligible_at' => $enrollment['certificate_eligible_at'] ?? $now,
        'updated_at'              => $now,
    ]);

**Bandingkan** dengan `LearningService::evaluateCompletion` (line 384-470+) yang merupakan **mesin aturan ketat 5 syarat** (lesson wajib, post-test, quiz modul, tugas, nilai minimal passing grade). Mesin aturan yang dirancang cermat itu dapat dilewati total oleh satu request POST operator.

`enrollmentId` juga diambil mentah dari POST (line 1267, 1285) tanpa scoping ke kelas atau kewenangan operator.

**Skenario eksploitasi.** Operator (atau akun operator yang dikompromikan) mengirim `enrollment_ids[]` berisi ID enrollment peserta mana pun - peserta yang tidak pernah bergabung, tidak menjawab soal, tidak pernah hadir. Sistem menerbitkan sertifikat bernomor urut resmi `status='issued'` yang dapat diverifikasi publik melalui `/verifikasi-sertifikat`. Tidak ada dual control, tidak ada rate limit, tidak ada laporan ineligibility.

**Dampak.** Sertifikat yang **publik dan otentik**. Perbaikan system-wide setelah insiden menjadi nyaris mustahil.

**Remediasi.**

1. Panggil `LearningService::evaluateCompletion($eid)` (atau services ruleset yang setara) **sebelum** menerbitkan, dan **tolak** penerbitan bila ruleset tidak terpenuhi - dengan parameter `force` khusus yang eksplisit.
2. Tambahkan field `justification` wajib + minimal length pada setiap penerbitan manual, dan simpan `eligibility_snapshot` JSON berisi hasil penilaian saat penerbitan untuk keperluan audit.
3. Terapkan **dual control**: penerbitan manual di atas ambang tertentu (mis. override nilai) memerlukan persetujuan role kedua.
4. Batasi `issueCertificate` / `bulkIssueCertificates` pada enrollment dalam lingkup kewenangan operator.

---

### H-3 - Override Nilai Sertifikat: Bypass yang Sama, Tanpa Verifikasi Kelayakan

**File:** `app/Controllers/Workspace/OperatorController.php:1556-1603`

Mengatur `final_score` (di-clamp 0-100) dan - bila `status='completed'` - juga `passed_at` + `certificate_eligible_at` (line 1580-1584) untuk enrollment **mana pun**, **nilai berapa pun**. Tidak ada verifikasi kelayakan, tidak ada persetujuan kedua.

**Posisi positif:** endpoint ini **sudah** mencatat audit log lengkap dengan nilai lama/baru dan alasan (line 1588-1600) - jauh lebih baik dari `issueSingleCertificate` yang sama sekali tidak mencatat ineligibility.

**Remediasi.** Sama seperti H-2: justifikasi terstruktur + dual control + `eligibility_snapshot`. Pastikan `final_score` yang di-override masuk ke perhitungan `calculateEnrollmentProgress` secara konsisten, atau beri atribut distinct agar tidak tercampur dengan nilai otomatik.

---

### H-4 - Tidak Ada Segregation of Duties pada Approval

**File:** `app/Controllers/Workspace/OperatorController.php:339-370` (`approveApproval`), `280-337` (`bulkApproval`)

**Dua masalah independen:**

1. **Tidak ada filter status.** `bulkApproval:299` memakai `->whereIn('id', $ids)` dan `approveApproval:345` memakai `->where('id', $approvalId)`. Keduanya **tidak** membatasi `status = 'pending'`. Operator dapat **membalik keputusan** pada record yang sudah `approved` atau `rejected`.
2. **Tidak ada pemisahan pemohon-pemberi persetujuan.** Tidak ada perbandingan `requested_by` vs `$actorId`. Operator dapat **menyetujui permohonannya sendiri** (mis. permintaan perubahan profil miliknya).

**Remediasi.**

    $approval = $this->db->table('approvals')
        ->where('id', $approvalId)
        ->where('status', 'pending')                      // guard status
        ->get()->getRowArray();
    if (! $approval) {
        return redirect()->back()->with('error', 'Pengajuan sudah diproses sebelumnya.');
    }
    if ((int) ($approval['requested_by'] ?? 0) === $actorId) {
        return redirect()->back()->with('error', 'Anda tidak dapat menyetujui pengajuan sendiri.');
    }

Terapkan filter `status='pending'` yang sama pada `bulkApproval:299`.

---

### H-5 - `OperatorController` Sepenuhnya Tanpa Transaksi

**File:** `app/Controllers/Workspace/OperatorController.php` (1.772 baris, 45 method)

Verifikasi grep terhadap `transStart|transBegin|transCommit|transRollback|transStatus|transStrictMode` -> **nol hasil**. Tidak ada satu pun transaksi di seluruh controller.

**Kasus terburuk - `bulkApproval` (317-328):**

    $this->db->table('approvals')->where('id', $appId)->update($updateData);  // 317
    $successCount++;                                                           // 318

    if (... === 'certificate' && ... === 'issue_certificate') {
        try {
            $generator->generateFromApproval($appId, $actorId);
            $certCount++;
        } catch (\Throwable $e) {
            log_message('error', '...');       // 326 - kegagalan DITEKAN
        }
    }

Jika pembuatan sertifikat gagal, approval **tetap** berstatus `approved`, `$successCount` tetap naik, dan pesan sukses (line 331-333) melaporkan "Berhasil memproses N" - sementara peserta **tidak mendapatkan sertifikat**. Ketidakkonsistenan data **senyap**: tidak ada yang tahu dari UI.

**Remediasi.** Bungkus setiap operasi bulk dalam `transStart()/transComplete()` + cek `transStatus()`. Untuk partial failure yang memang disengaja, lebih baik: kumpulkan hasil per-item dan **laporkan nominal kegagalan** pada pesan sukses, bukan menelannya.

---

### H-6 - Hak Akses Stale di Session

**File:** `app/Filters/WorkspaceAuthFilter.php:29`, `app/Controllers/Workspace/OperatorController.php:71`

**Bukti.** Docblock filter sendiri (line 13-18) mengklaim ia memeriksa `users.account_status = 'active'`, tetapi implementasinya hanya membaca **session**:

    // WorkspaceAuthFilter.php:29
    if (session()->get('account_status') !== 'active') {   // SESSION, bukan DB

    // OperatorController.php:71
    $roles = (array) session()->get('roles');             // SESSION, bukan DB

Nilai session **hanya** ditulis saat login: `app/Services/Identity/AuthenticationService.php:41` (`regenerate`) dan `:52-58` (`session()->set([... 'roles' => $roleKeys ...])`), serta `app/Filters/AuthenticationFilter.php:50`. **Tidak ada refresh berkala.**

**Asimetri yang terbukti.** `AuthenticationFilter` (untuk `/dashboard/*`) **melakukan** query DB tiap request (line 38-47: `UserModel->find()` lalu cek `account_status !== 'active'`). `WorkspaceAuthFilter` tidak. Akibatnya:

| Skenario | `/dashboard/*` | `/workspace/*` |
|---|---|---|
| Akun disuspend admin | Akses langsung hilang | **Tetap penuh** sampai session berakhir |
| Operator diturunkan jadi peserta | Ditolak via `hasDashboardAccess()` | **`assertOperatorAccess()` tetap lolos** sampai re-login |

**Remediasi.** Samakan dengan `AuthenticationFilter` - query DB di `WorkspaceAuthFilter::before()`, dan validasi role dari DB (bukan session) di `assertOperatorAccess()`. Bila performa menjadi concern, tambahkan TTL pendek (mis. 60 detik) pada cache role/status.

---

### H-7 - Brute-Force Kode Presensi + Penyimpanan Kode dalam Teks Biasa

**File:** `app/Services/Communication/CommunicationService.php:458+` (`validateAndCheckIn`), dipanggil dari `ParticipantController::submitAttendanceCode` (1384-1408)

**Bukti (dua-duanya):**

1. **Tidak ada rate limiting / lockout / penundaan.** Pencocokan adalah persamaan eksak: `->where('attendance_code', $code)` (line 469). Tidak ada penghitung percobaan, tidak ada backoff, tidak ada CAPTCHA. Peserta terdaftar cukup mencoba kata demi kata.
2. **Disimpan plaintext.** Skema DB terverifikasi: `events.attendance_code` bertipe `varchar(64)`. Karena service memakai `=` (bukan `WHERE hash = bcrypt(...)`), kode **wajib** plaintext. Siapa pun dengan akses baca DB memperoleh seluruh kode presensi sekaligus.

**Dampak.** Kode presensi adalah satu-satunya pembeda antara `present` dan `absent`, dan status itu menjadi input untuk `claimSelfPacedCertificate`.

**Remediasi.** Hash kode dengan bcrypt saat penyimpanan dan verifikasi via `password_verify`; tambahkan penghitung kegagalan per user plus cooldown escalating; samakan pesan galat agar tidak membedakan "kode tidak ada" vs "presensi belum dibuka".

---

### H-8 - `assignTrainer`: Escalation Privilege

**File:** `app/Controllers/Workspace/OperatorController.php:793-836`

`user_id` diambil mentah dari POST (line 797) lalu di-`insert` ke `class_trainers` (line 824-830) **tanpa verifikasi bahwa user tersebut benar-benar memegang role `trainer`**, tanpa cek `account_status`, tanpa cek verifikasi profil.

**Skenario eksploitasi.** Operator mengirim `user_id` milik sendiri. Baris masuk ke `class_trainers`. Sekarang user tersebut lolos `assertTrainerOf()` di seluruh `TrainerController` - mendapat akses baca/tulis penuh ke kelas tersebut, termasuk **menyalahgunakan C-1** pada kelas itu.

**Remediasi.** Verifikasi role `trainer` dan `account_status='active'` pada `$userId` **sebelum** insert; tolak dengan pesan jelas bila tidak memenuhi.

---

## 4. Temuan MEDIUM

### M-1 - Race Condition Double-Submit Kuis

**File:** `app/Services/LearningService.php:218-288`

`submitQuiz` membaca `status === 'started'` (line 220-223), lalu di dalam `transStart()` melakukan insert `quiz_answers` (258-266) dan `UPDATE quiz_attempts` (271-278). **UPDATE-nya tidak memiliki syarat `WHERE status = 'started'`** dan tidak memeriksa jumlah baris terpengaruh.

Dua POST bersamaan (double-klik, atau crafted parallel request) keduanya lolos pemeriksaan awal -> keduanya insert baris `quiz_answers` -> `evaluateCompletion()` dieksekusi dua kali. Tabel `quiz_answers` tidak punya unique index pada `(attempt_id, question_id)` (diverifikasi via `information_schema`), sehingga tidak ada pengaman.

**Remediasi.** Buat UPDATE bersifat kondisional + verifikasi affected rows:

    $updated = $this->db->table('quiz_attempts')
        ->where('id', $attemptId)
        ->where('status', 'started')          // pengaman
        ->update([...]);
    if ($updated === 0) {
        $this->db->transRollback();
        throw new RuntimeException('Percobaan sudah diproses oleh permintaan lain.');
    }

Tambahkan unique index `(attempt_id, question_id)` sebagai pagar kedua.

---

### M-2 - Race Condition Nomor Percobaan pada `startQuiz`

**File:** `app/Services/LearningService.php:193-212`

Hitung jumlah attempt (193-195), lalu insert `attempt_number = $attempts + 1` (202-210) - **tanpa transaksi**. Index unik `quiz_attempts_unique(quiz_id, enrollment_id, attempt_number)` (diverifikasi) mengubah konkurensi menjadi **duplicate-key error** mentah, dan tidak ada `try/catch` -> HTTP 500.

**Remediasi.** Bungkus dalam transaksi + `catch` duplicate-key yang diterjemahkan menjadi pesan ramah pengguna.

---

### M-3 - `bulkIssueCertificates` Tanpa Isolasi Error per Item

**File:** `app/Controllers/Workspace/OperatorController.php:1290-1298`

Loop tanpa `try/catch`. Index unik `certificates_source_unique(source_type, source_id)` (diverifikasi) menyebabkan **satu konflik membatalkan seluruh batch di tengah**, menyisakan penerbitan parsial tanpa laporan mana yang berhasil.

**Remediasi.** Bungkus tiap item dengan `try/catch`, hitung `$successCount` dan `$failedIds`, laporkan keduanya di flash message.

---

### M-4 - N+1 Query pada Halaman Daftar Peserta dan Progress

**File:** `app/Controllers/Workspace/TrainerController.php:862-876`, `905-923`

`allParticipants` (862-876): **2 query per peserta**, dan `total_lessons` (865-867) **diulang untuk setiap peserta dari kelas yang sama** - seharusnya di-cache per kelas. `allProgress` (905-923): **4 query per kelas**.

Pada 200 peserta dalam 3 kelas -> +/- 400+ query untuk satu render halaman. Vektor amplifikasi DoS yang mudah dipicu.

**Remediasi.** Hitung `total_lessons` sekali per kelas ke array asosiatif, dan ganti loop progress dengan satu agregasi `GROUP BY class_id`.

---

### M-5 - Upload Tugas Tanpa Validasi

**File:** `app/Controllers/Workspace/ParticipantController.php:1524-1546`

`submission_file`: **tidak ada** allowlist ekstensi, cek MIME, maupun batas ukuran (`isValid()` hanya memeriksa error PHP). Nama file acak dan penyimpanan di `writable/uploads/` (di luar webroot) sehingga risiko RCE rendah, tetapi tetap memungkinkan penyimpanan berkas sembarang plus habisnya resource (php.ini mengizinkan 128 MB).

**Remediasi.** Allowlist `pdf,doc,docx,xls,xlsx,ppt,pptx,zip`; verifikasi MIME server-side via `getMimeType()`; batas 10 MB; pindahkan ke `FileStorageService`.

---

### M-6 - Download Materi Event Tanpa Otorisasi dan Potensi Header Injection

**File:** `app/Controllers/Workspace/ParticipantController.php:1267-1307`

**Tiga isu:**

1. **Tanpa otorisasi.** Setiap user terautentikasi dapat mengunduh materi event mana pun dengan menebak `{idAtauSlug}/{fileId}`. **Tidak ada** pemeriksaan registrasi event, tidak ada pemeriksaan status event `published`.
2. **Header injection.** Line 1300-1305: `original_name` disisipkan mentah ke header `Content-Disposition` **tanpa sanitasi**. Jika `original_name` memuat `"` atau CR/LF, header rusak.
3. **Path traversal.** Line 1295 hanya `ltrim` separator di depan; `str_replace(['/','\\'], DIRECTORY_SEPARATOR, ...)` justru memperbesar risiko bila `path` di DB dapat dipengaruhi penyerang.

**Remediasi.** Tambahkan join ke `event_registrations` untuk memverifikasi pengguna terdaftar; sanitasi `basename($filename)` dan buang karakter non-alphanumeric sebelum masuk header; gunakan `realpath()` + `str_starts_with()` untuk memastikan path berada di dalam `WRITEPATH`.

---

### M-7 - `deleteQuestion` Menghapus Opsi Lintas Kelas

**File:** `app/Controllers/Workspace/TrainerController.php:1457-1459`

    db_connect()->table('quiz_options')->where('question_id', (int) $questionId)->delete();  // tanpa scoping
    new QuestionModel()->where('id', ...)->where('quiz_id', (int) $quizId)->delete();

Query **pertama** tidak difilter ke `$quizId`. Second query-nya scoped, tapi kerusakan sudah terjadi. Trainer yang mengetahui atau menebak ID question berurutan milik trainer lain dapat **menghapus seluruh opsi soal** milik kelas lain.

**Remediasi.** Ambil `question_id` yang valid untuk `$quizId` lebih dulu, lalu hapus opsi **hanya** untuk ID terverifikasi tersebut.

---

### M-8 - `updateAttendance` Tidak Dibatasi Scope Event

**File:** `app/Controllers/Workspace/OperatorController.php:1113-1141`

Setiap baris `attendances` dapat diubah berdasarkan ID, tanpa cek event scope, tanpa verifikasi registrasi peserta, dan tanpa validasi kewenangan operator atas event tersebut. Mengatur `status='present'` - yang menjadi bagian kelayakan sertifikat self-paced (lihat H-7).

**Remediasi.** Verifikasi `$attendanceId` terhubung ke event dalam lingkup kewenangan operator sebelum update; batasi transisi status yang sah.

---

### M-9 - Log Error Rusak: Tidak Ada Jejak Forensik Anti-Curang

**File:** `app/Controllers/Workspace/ParticipantController.php:1901`

    log_message('error', 'submitQuiz failed: {message}', ['message' => $e->getMessage()]);

Parameter ke-3 pada `log_message()` CI4 adalah **threshold** (level), **bukan** context array. Akibatnya pesan asli **tidak pernah tercatat**; yang tersimpan hanyalah literal `submitQuiz failed: {message}` dengan placeholder yang tidak tergantikan.

**Dampak.** Investigation kecurangan (kecocokan akun, kolusi, menebak) tidak memiliki bukti. Penggunaan benar dengan konkatenasi biasa ada di `LearningService`.

**Remediasi.** `log_message('error', 'submitQuiz failed: ' . $e->getMessage());`

---

### M-10 - Tidak Ada Forensik pada `quiz_attempts`

Skema terverifikasi: tabel `quiz_attempts` **tidak memiliki** kolom `ip_address`, `user_agent`, atau token apa pun - hanya `invalidated_by` / `invalidated_reason` untuk pembatalan manual. Jawaban disimpan **hanya di client** (localStorage) sampai submit final.

**Dampak.** Tidak ada cara mendeteksi kolusi, akun bersama, atau menjawab di luar platform. Kolom `invalidated_*` yang sudah ada menunjukkan niat audit - tinggal diisi.

**Remediasi.** Tambahkan `started_ip`, `submitted_ip`, `user_agent`, dan `duration_actual_seconds`; populate di `startQuiz` dan `submitQuiz`.

---

## 5. Temuan LOW

| ID | Lokasi | Temuan |
|---|---|---|
| **L-1** | `ParticipantController.php:1851-1853` + `LearningService.php:227` | **Existence/ownership oracle.** Pesan "Percobaan quiz tidak ditemukan" (1853) berbeda dari "Percobaan quiz bukan milik enrollment Anda" (service 227), sehingga IDOR dapat dienumerasi. **Ownership sendiri sudah ditegakkan** (226-228); hanya pesannya yang bocor. Samakan pesannya. |
| **L-2** | `CertificateDownloadController.php:117, 134` | `throw new RuntimeException` **tidak tertangani** pada jalur otorisasi gagal -> HTTP 500, bukan 403 bersih. Method `forbiddenResponse()` (line 148) sudah ada tapi tidak terpakai di sini. Bungkus dengan `try/catch` lalu `forbiddenResponse()`. |
| **L-3** | `NotificationController.php:22-30` | `detail()` memanggil `markAsRead()` tanpa cek keberadaan; `userId()` (138-141) hanya baca session tanpa validasi login (bergantung penuh pada filter). Tidak dapat dieksploitasi, tapi perilakunya membingungkan. |
| **L-4** | `EventAssessmentService.php:671` | Fallback hardcoded: `$generatorActor = $attActor ?: (int)($adminUser['id'] ?? 13)`. ID 13 adalah angka ajaib - penerbitan sertifikat bisa teratribusi ke user yang tidak relevan, merusak jejak audit. |
| **L-5** | `EventAssessmentService.php:644-673` | Tanpa transaksi: attendance di-set `present` (649-665) **sebelum** `generateDirect()` (673). Kalau pembuatan sertifikat melempar, peserta tertinggal "hadir" tanpa sertifikat. Bungkus dalam transaksi. |
| **L-6** | `OperatorController.php:285, 343` | Input pengguna (`notes` / `reason`) disimpan ke DB. View tujuan umumnya sudah memakai `esc()`, tapi pastikan konsisten di semua halaman yang merendernya. |
| **L-7** | `CertificateSequenceModel.php:49-52` | `SELECT` setelah upsert atomik **tidak** row-locked -> di bawah READ COMMITTED, dua pemanggil konkuren bisa membaca nilai sama. **Dimitigasi** oleh `certificates_number_unique` (diverifikasi) yang akan melempar error, bukan menghasilkan duplikat. Perbaikan: `SELECT ... FOR UPDATE` atau `LAST_INSERT_ID(last_number+1)`. |

---

## 6. Praktik yang Sudah Benar (Agar Tidak Di-refactor Tanpa Konteks)

| Area | Lokasi | Catatan |
|---|---|---|
| **Ownership attempt kuis** | `LearningService.php:226-228` | Ditegakkan **di service layer** (bandingkan `enrollment_id` attempt dengan enrollment milik user), bukan di controller. Ini praktik benar - justru controller-lah yang rapuh. |
| **Binding opsi ke soal** | `LearningService.php:248-250` | Opsi diverifikasi terhadap `question_id`, mencegah transplantasi jawaban antar soal. |
| **Anti resubmit** | `LearningService.php:221` | Status `started` dicek sebelum penulisan. |
| **Batas percobaan quiz** | `LearningService.php:193-199` | Ditegakkan saat pembuatan attempt. |
| **Submit tugas** | `LearningService.php:295-312` | Ownership enrollment (295), assignment scoped per kelas (300), `max_attempts` (310), dan **transaksi** (317/360). |
| **Unduh sertifikat** | `CertificateDownloadController.php:110-136` | Owner-OR-admin, dengan pengecekan `account_status` untuk pemilik. |
| **Konsultasi** | `ConsultationController.php:102-109, 182-187` | Kepemilikan tiket diverifikasi pada `detail` **dan** `reply`. |
| **Bookmark** | `BookmarkModel.php:80-89` | `remove()` di-scope `user_id`, jadi `BookmarkController::remove` tidak punya IDOR meski menerima POST mentah. |
| **CSRF** | `Config/Filters.php:80`, `Config/Security.php` | `csrfProtection = 'cookie'` global (kecuali `api/*`, `webhook/*`). **8 dari 8** form POST Workspace punya `csrf_field()`. |
| **Upload `guide_file`** | `TrainerController.php:1674-1684, 1771-1785` | Validasi ext + MIME, dan **ekstensi di-hardcode** `.pdf` pada nama tersimpan. |
| **Import soal** | `TrainerController.php:1417-1437` | `assertTrainerOf()` ada + allowlist ekstensi `csv/xlsx/txt`. |
| **Uniqueness DB** | `information_schema` | `enrollments_class_user_unique`, `certificates_source_unique`, `certificates_number_unique`, `attendances_event_registration_unique`, `assignment_submissions_unique` - pagar integritas sudah tersedia. |
| **Anti SQLi** | Query builder | Parameter request yang terobservasi passing lewat bound parameter; tidak ditemukan interpolasi mentah di Workspace. |

---

## 7. Temuan yang Ditolak (False Positive)

Audit ini sempat menandai **"kunci jawaban bocor saat attempt baru aktif"** sebagai Critical, lalu diverifikasi ulang dan **ditolak**. Dicatat agar tidak dikideakan ulang oleh auditor berikutnya.

**Hipotesis awal:** `ParticipantController.php:1796-1802` menetapkan `$reviewAttempt = $bestAttempt` bahkan ketika `$attempt` berstatus `started`, dan view mengirim `optionsByQuestion` (memuat `is_correct`, diambil via `SELECT *` di line 1719-1722) ke template setiap kali.

**Kenapa tidak exploitable:** `app/Views/main/workspace/pages/quiz_take.php` memisahkan keduanya secara tegas. Line 200 membuka cabang IF untuk attempt aktif (form kuis interaktif, line 205-817), lalu line 821 adalah `<?php else: ?>` yang memuat dashboard, riwayat, dan review sheet dengan kunci jawaban (line 1019+).

Saat attempt aktif, cabang IF aktif dan seluruh cabang ELSE - termasuk review sheet dengan `is_correct` (line 1129-1141) - **tidak dirender**. Verifikasi grep atas `is_correct` di `quiz_take.php` (line 1045, 1090, 1129) memastikan **semuanya berada di dalam cabang ELSE**.

**Kesimpulan.** Isolasi if/else di template sudah benar. Yang tersisa hanyalah M-1 (double-submit) dan H-1 (bypass durasi) yang sudah dicatat terpisah.

---

## 8. Rencana Remediasi Prioritas

### Fase 0 - Hari ini

1. **C-1:** taruh `.htaccess` php-off di `public/uploads/` + allowlist ekstensi/MIME/ukuran di `TrainerController::updateClass()`.
2. **H-2 / H-3:** ubah `issueSingleCertificate` dan `overrideEnrollmentScore` agar **menolak** penerbitan bila `evaluateCompletion()` tidak terpenuhi, kecuali dengan parameter `force` eksplisit + justifikasi.
3. **H-4:** tambahkan `where('status','pending')` + guard self-approval pada `approveApproval` / `bulkApproval`.

### Fase 1 - Minggu ini

4. **H-1:** validasi deadline di `LearningService::submitQuiz` (dengan grace period).
5. **H-6:** samakan `WorkspaceAuthFilter` dengan `AuthenticationFilter` - validasi DB per request.
6. **H-8:** validasi role `trainer` pada `assignTrainer`.
7. **H-7:** hash kode presensi + rate limiting.
8. **H-5:** transaksi untuk seluruh operasi bulk Operator + laporkan kegagalan per item.

### Fase 2 - Sprint berikutnya

9. **M-1 / M-2 / M-3:** pagar race condition (conditional update, unique index, transaksi, error isolation).
10. **M-5 / M-6:** validasi upload, otorisasi download materi event, sanitasi header.
11. **M-7 / M-8 / M-10:** scoping `deleteQuestion`, scope event pada `updateAttendance`, kolom forensik `quiz_attempts`.
12. **M-4:** optimalkan N+1 dengan agregasi per kelas.
13. **M-9 / L-4 / L-5:** perbaiki `log_message`, hapus hardcoded user ID `13`, transaksi pada klaim sertifikat.

### Fase 3 - Struktural (preventif)

14. **Pusatkan otorisasi.** Saat ini `TrainerController` mengandalkan `assertTrainerOf()` per-method, `OperatorController` mengandalkan `assertOperatorAccess()` per-method, sedangkan `ParticipantController::userId()` (line 2686-2693) **tidak melakukan cek role sama sekali**. Bangun trait atau base filter yang konsisten agar method baru tidak mungkin lolos tanpa guard.
15. **Pindahkan seluruh upload ke luar webroot** dan layarkan melalui `FileStorageService` yang sudah ada.
16. **Uji regresi** untuk seluruh alur ini - `app/Commands/WorkspaceSmoke.php` dan `WorkspaceFullAudit.php` sudah ada dan dapat dijadikan tempat menambahkan kasus uji keamanan.

---

## 9. Catatan Metodologi

- **Read-only.** Tidak ada file aplikasi yang dimodifikasi. Satu-satunya file yang dibuat adalah dokumen laporan ini.
- **Skema DB diverifikasi langsung** via `information_schema.COLUMNS` dan `information_schema.STATISTICS` terhadap database `db_begawipbj` (kredensial dari `.env`) - dasar untuk M-10 (ketiadaan kolom forensik), M-1 (ketiadaan unique index pada `quiz_answers`), M-2 / M-3 / L-7 / H-2 (keberadaan unique index), dan H-7 (tipe `attendance_code`).
- **Konfigurasi web server diverifikasi** dengan membaca `public/.htaccess` dan mencari rekursif `.htaccess` di dalam `public/uploads/` (tidak ada) - dasar temuan C-1.
- **Semua temuan dibaca langsung pada kodenya**, bukan diasumsikan dari nama method. Klaim negatif (mis. "tidak ada transaksi") dibuat dari grep exhaustif terhadap file terkait.
- **Satu temuan awal ditolak setelah verifikasi** (Bagian 7), dan satu asumsi berhasil dikonfirmasi lewat perbandingan langsung dengan `AuthenticationFilter` (H-6).
- Jalur typo `c:\laragon\www\begawipengadan\` **tidak** digunakan sebagai sumber temuan.
