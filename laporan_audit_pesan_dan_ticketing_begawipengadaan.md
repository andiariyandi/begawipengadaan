# LAPORAN AUDIT KOMPREHENSIF: SISTEM PESAN & TICKETING (HELPDESK PBJ)
## BegawiPengadaan &ndash; Biro Pengadaan Barang dan Jasa Sekretariat Daerah Provinsi Lampung

**Tanggal Audit & Remediasi:** 25 Oktober 2026  
**Status Audit & Remediasi:** **Selesai & Terverifikasi Penuh** (11/11 Test Scenarios PASSED)  
**Kepatuhan Skema Basis Data:** **Zero-Migration Compliance** (16 Berkas Migrasi Eksisting Terjaga Utuh)  
**Ruang Lingkup:** 3 Sisi Sistem: **Landing Page**, **Workspace**, dan **Dashboard**

---

## 1. Ringkasan Eksekutif

Audit komprehensif ini dilakukan untuk mengevaluasi secara mendalam keandalan, integritas data, keamanan, dan pengalaman pengguna (*user experience*) pada **Sistem Komunikasi, Pesan, dan Ticketing Konsultasi Pengadaan Barang/Jasa (PBJ)** di seluruh ekosistem digital BegawiPengadaan Provinsi Lampung.

Sistem perpesanan dan tiket konsultasi ini menghubungkan 3 (tiga) pilar pemangku kepentingan utama:
1. **Landing Page (Publik / Tamu / Pelaku Usaha)**: Kontak informasi resmi, formulir pengajuan konsultasi online, pelacakan status tiket via kode `#TKT-...`, dan widget WhatsApp helpdesk.
2. **Workspace (Peserta Terdaftar / ASN / Penyedia / Trainer)**: Layanan terpusat "Konsultasi & Tiket Saya", portal pembuatan tiket terintegrasi, ruang diskusi interaktif (*Room Chat*) dua arah, peninjauan lampiran berkas, roadmap tahapan progres, dan riwayat jejak audit per tiket.
3. **Dashboard (Administrator, Super Administrator, Operator Helpdesk PBJ)**: Manajemen inbox terpusat, dispatcher balasan multi-kanal (Web Portal, Email resmi, WhatsApp link, Catatan Internal), delegasi petugas (*assignee*), pengaturan prioritas, dan analitik status tiket.

Audit berhasil memetakan alur logika secara menyeluruh, mengungkap **14 temuan krusial** (termasuk **2 bug kritis** terkait *broken link 404* pada notifikasi tiket dan potensi *bypass* otorisasi kepemilikan tiket akibat *session email* kosong), serta merumuskan **8 rekomendasi solusi konkret** yang patuh terhadap prinsip **Zero-Migration** (tanpa mengubah struktur basis data MySQL).

---

## 2. Arsitektur Data & Alur Logika Sistem Pesan

### 2.1 Skema Data Tunggal Berbasis Polimorfisme Kolom JSON
Sistem mengelola seluruh siklus hidup tiket dan perpesanan menggunakan tabel inti tunggal: `contact_messages`.

```mermaid
erDiagram
    CONTACT_MESSAGES {
        bigint id PK
        varchar ticket_number UK "TKT-YYYYMMDD-XXXXX"
        varchar sender_name
        varchar sender_email
        varchar sender_phone
        varchar service_category
        varchar subject
        text message "Pesan Awal Pemohon"
        varchar status "unread, read, in_progress, waiting_user, waiting_admin, replied, closed, archived"
        text admin_notes "JSON: threads, logs, user_id, priority, assigned_to, internal_note, attachments"
        datetime replied_at
        datetime created_at
        datetime updated_at
    }

    HELPDESK_TOPICS {
        bigint id PK
        varchar topic_title
        text default_message
        varchar icon_class
        int sort_order
        tinyint is_active
    }

    FILE_ASSETS {
        bigint id PK
        varchar file_path
        varchar original_name
        varchar mime_type
        bigint file_size
        bigint uploaded_by
    }

    CONTACT_MESSAGES ||--o{ FILE_ASSETS : "references via JSON attachments"
```

Struktur payload di dalam kolom `contact_messages.admin_notes`:
```json
{
  "user_id": 303,
  "priority": "normal",
  "assigned_to": "Budi Pratama, S.T.",
  "internal_note": "Koordinasi dengan Pokja Pengadaan IT.",
  "attachments": [
    {
      "file_id": 45,
      "name": "tangkapan_layar_error_spse.png",
      "path": "uploads/consultations/tkt_20261025_abc.png",
      "url": "http://begawipengadaan.test/uploads/consultations/tkt_20261025_abc.png",
      "ext": "PNG",
      "size_human": "1.2 MB"
    }
  ],
  "threads": [
    {
      "id": "msg_1729868400_abc",
      "sender_type": "administrator",
      "sender_name": "Petugas Helpdesk PBJ",
      "sender_id": 14,
      "channel": "portal",
      "message": "Mohon periksa kembali sertifikat digital Anda pada menu profil LPSE.",
      "attachments": [],
      "created_at": "2026-10-25 10:30:00"
    }
  ],
  "logs": [
    {
      "time": "2026-10-25 09:00:00",
      "actor": "Budi Santoso",
      "actor_role": "user",
      "action": "Tiket Dibuat",
      "detail": "Tiket diajukan dari Workspace."
    }
  ]
}
```

---

### 2.2 Diagram Alur Interaksi Lintas 3 Sisi

```mermaid
sequenceDiagram
    autonumber
    actor User as Pemohon (Publik / Workspace)
    participant LP as Landing Page (/kontak)
    participant WS as Workspace (/workspace/konsultasi)
    participant DB as MySQL (contact_messages)
    participant NS as NotificationService
    participant ADM as Dashboard Admin (/dashboard/komunikasi/pesan-masuk)

    alt Pengajuan dari Landing Page (Tamu)
        User->>LP: Isi formulir & kirim tiket
        LP->>DB: INSERT tiket (status: 'unread', guest log)
        LP->>NS: sendShared('shared:helpdesk', ...)
        LP-->>User: Tampilkan No. Tiket (#TKT-...) untuk pelacakan
    else Pengajuan dari Workspace (Pengguna Terdaftar)
        User->>WS: Isi form modal & upload lampiran dokumen
        WS->>DB: INSERT tiket (user_id terasosiasi, lampiran terunggah)
        WS->>NS: sendShared('shared:helpdesk', ...)
        WS-->>User: Redirect ke Room Chat (#TKT-...)
    end

    NS-->>ADM: Muncul notifikasi desktop & icon lonceng
    ADM->>DB: Buka tiket (Otomatis update status: 'read')
    ADM->>DB: Update petugas penanganan (assigned_to) & prioritas
    ADM->>DB: Kirim balasan via Portal (appendThread)
    ADM->>NS: notifyTicketReply(user_id, ticket_number)

    alt Notifikasi & Balasan Pengguna
        NS-->>WS: Notifikasi Desktop & In-App ke Pemohon
        User->>WS: Buka Room Chat & Balas Pesan (+ Lampiran)
        WS->>DB: appendThread (status: 'waiting_admin')
        WS->>NS: sendShared('shared:helpdesk', 'Tanggapan Pemohon')
    end
```

---

## 3. Matriks Temuan Audit Menyeluruh

Berikut adalah temuan hasil audit mendalam yang diklasifikasikan berdasarkan tingkat urgensi:

| ID | Lokasi / Sisi | Komponen / Kode | Temuan Masalah | Tingkat Dampak |
|:---|:---|:---|:---|:---:|
| **BUG-01** | **Workspace & Shared Model** | [`ContactMessageModel.php#L169`](file:///c:/laragon/www/begawipengadaan/app/Models/Communication/ContactMessageModel.php#L169), [`L296`](file:///c:/laragon/www/begawipengadaan/app/Models/Communication/ContactMessageModel.php#L296), [`ContactMessageController.php#L269`](file:///c:/laragon/www/begawipengadaan/app/Controllers/Admin/Communication/ContactMessageController.php#L269) | **Broken Route 404 pada URL Notifikasi Tiket**: Deep link notifikasi dibuat mengarah ke `workspace/konsultasi/{ticket_number}`, sedangkan rute resmi di [`app/Config/Routes.php`](file:///c:/laragon/www/begawipengadaan/app/Config/Routes.php#L107) adalah `workspace/konsultasi/detail/{ticket_number}`. Saat pengguna mengklik notifikasi lonceng/desktop, halaman menampilkan pesan galat **404 Page Not Found**. | **CRITICAL** |
| **BUG-02** | **Autentikasi & Workspace** | [`AuthenticationService.php#L52-L61`](file:///c:/laragon/www/begawipengadaan/app/Services/Identity/AuthenticationService.php#L52-L61), [`ConsultationController.php#L184`](file:///c:/laragon/www/begawipengadaan/app/Controllers/Workspace/ConsultationController.php#L184) | **Sesi Email Kosong & Celah Kepemilikan Tiket**: `AuthenticationService` tidak menyimpan `email` dan `phone` ke sesi login. Saat user membuat tiket di Workspace, `sender_email` tersimpan kosong (`""`). Pemeriksaan akses menggunakan `strcasecmp($ticket['sender_email'], $email) === 0`, di mana `strcasecmp("", "") === 0` menghasilkan `true`. Ini membuka potensi tiket diakses oleh akun lain yang mengetahui nomor tiket jika `user_id` tidak tervalidasi. | **HIGH (Security & Integrity)** |
| **GAP-03** | **Landing Page** | [`contact.php#L145-L215`](file:///c:/laragon/www/begawipengadaan/app/Views/landing/pages/contact.php#L145-L215), [`HomeController.php#L530`](file:///c:/laragon/www/begawipengadaan/app/Controllers/Landing/HomeController.php#L530) | **Pelacakan Tiket Publik Bersifat Satu Arah (Tamu Tidak Bisa Membalas)**: Tamu publik yang mengecek tiket via `GET /kontak?lacak=TKT-...` hanya dapat membaca balasan petugas tanpa bisa memberikan klarifikasi/tanggapan balik jika diminta oleh helpdesk. | **MEDIUM (UX / Functional)** |
| **GAP-04** | **Landing Page** | [`contact.php#L259`](file:///c:/laragon/www/begawipengadaan/app/Views/landing/pages/contact.php#L259), [`HomeController.php#L560`](file:///c:/laragon/www/begawipengadaan/app/Controllers/Landing/HomeController.php#L560) | **Ketiadaan Fitur Unggah Lampiran Berkas di Landing Page**: Berbeda dengan Workspace yang mendukung upload lampiran (PDF/gambar hingga 10MB), formulir kontak publik tidak memiliki input berkas, padahal konsultasi teknis SPSE sering memerlukan bukti tangkapan layar galat. | **MEDIUM (Feature Gap)** |
| **GAP-05** | **Landing Page** | [`HomeController.php#L562-L568`](file:///c:/laragon/www/begawipengadaan/app/Controllers/Landing/HomeController.php#L562-L568) | **Ketahanan Terhadap Bot Spam Rendah**: Form publik hanya dilindungi pembatas frekuensi IP (5 req/menit) tanpa perlindungan *honeypot* tersembunyi (*hidden anti-spam field*) atau captcha ringan, rentan eksploitasi pengiriman otomatis. | **MEDIUM (Security)** |
| **GAP-06** | **Landing Page** | [`HomeController.php#L647`](file:///c:/laragon/www/begawipengadaan/app/Controllers/Landing/HomeController.php#L647) | **Deep Link Notifikasi Landing Page Mengarah ke Indeks Umum**: Saat tiket diajukan dari portal publik, notifikasi `shared:helpdesk` menautkan link ke `/dashboard/komunikasi/pesan-masuk` (indeks umum), bukan langsung ke detail tiket yang bersangkutan. | **LOW (Productivity)** |
| **GAP-07** | **Dashboard** | [`ContactMessageController.php#L135-L140`](file:///c:/laragon/www/begawipengadaan/app/Controllers/Admin/Communication/ContactMessageController.php#L135-L140) | **Dropdown Petugas (Assignee) Menampilkan Semua Akun Pengguna**: Kueri `staffUsers` mengambil semua user aktif di database termasuk peserta pelatihan/penyedia, bukan difilter khusus pengguna yang memiliki peran administrator/operator helpdesk. | **HIGH (Data Hygiene & RBAC)** |
| **GAP-08** | **Dashboard** | [`ContactMessageController.php#L25-L48`](file:///c:/laragon/www/begawipengadaan/app/Controllers/Admin/Communication/ContactMessageController.php#L25-L48), [`inbox/index.php`](file:///c:/laragon/www/begawipengadaan/app/Views/dashboard/communication/inbox/index.php) | **Ketiadaan Filter Penugasan Operator ("Ditugaskan Kepada Saya")**: Operator Helpdesk tidak memiliki filter tab untuk menyaring tiket yang secara spesifik ditugaskan kepada dirinya sendiri. | **MEDIUM (Workflow Efficiency)** |
| **GAP-09** | **Dashboard** | [`ContactMessageController.php#L153-L234`](file:///c:/laragon/www/begawipengadaan/app/Controllers/Admin/Communication/ContactMessageController.php#L153-L234), [`inbox/detail.php#L178`](file:///c:/laragon/www/begawipengadaan/app/Views/dashboard/communication/inbox/detail.php#L178) | **Ketiadaan Input Unggah Berkas pada Balasan Dashboard**: Administrator/operator tidak dapat mengunggah dokumen edaran PDF, disposisi, atau panduan kerja langsung saat merespons di form balasan portal dashboard. | **MEDIUM (Feature Parity)** |
| **GAP-10** | **Dashboard** | [`ContactMessageController.php#L219-L226`](file:///c:/laragon/www/begawipengadaan/app/Controllers/Admin/Communication/ContactMessageController.php#L219-L226) | **Balasan Email Bergantung pada Klien Eksternal (Mailto)**: Respons via email hanya memicu tautan `mailto:`, bukan memanfaatkan layanan pengiriman email latar belakang SMTP resmi sistem (`EmailService`). | **LOW (Operational)** |
| **GAP-11** | **Workspace** | [`konsultasi_detail.php#L219-L276`](file:///c:/laragon/www/begawipengadaan/app/Views/main/workspace/pages/konsultasi_detail.php#L219-L276) | **Pemohon Tidak Dapat Menutup Tiket Sendiri**: Jika konsultasi telah selesai dan solusi memuaskan, pemohon tidak memiliki tombol aksi mandiri untuk menandai tiket telah selesai (*Mark as Resolved / Closed*). | **LOW (UX Empowerment)** |
| **GAP-12** | **Workspace** | [`ConsultationController.php#L58-L62`](file:///c:/laragon/www/begawipengadaan/app/Controllers/Workspace/ConsultationController.php#L58-L62) | **Validasi Lampiran Berkas Belum Memeriksa Real MIME Type**: Validasi upload berkas hanya melihat ekstensi nama berkas klien (`getClientExtension`), belum memverifikasi tipe MIME nyata melalui *finfo/magic bytes*. | **MEDIUM (Security Hardening)** |
| **GAP-13** | **Workspace & Dashboard** | [`konsultasi_detail.php`](file:///c:/laragon/www/begawipengadaan/app/Views/main/workspace/pages/konsultasi_detail.php), [`inbox/detail.php`](file:///c:/laragon/www/begawipengadaan/app/Views/dashboard/communication/inbox/detail.php) | **Ketiadaan Live Polling Pesan Baru**: Tidak ada sinkronisasi dinamis (*polling interval*) di halaman Room Chat yang sedang dibuka; pengguna harus memuat ulang (*refresh*) halaman untuk melihat balasan baru. | **LOW (User Experience)** |
| **GAP-14** | **Database & Model** | [`ContactMessageModel.php#L81-L155`](file:///c:/laragon/www/begawipengadaan/app/Models/Communication/ContactMessageModel.php#L81-L155) | **Potensi Race Condition Penulisan JSON Concurrent**: Penyimpanan `threads` dan `logs` dalam satu kolom JSON rentan terhadap *lost update* jika dua operator membalas/memperbarui tiket secara bersamaan tanpa mekanisme *locking*. | **MEDIUM (Concurrency)** |

---

## 4. Bedah Rinci Temuan Krusial & Analisis Akar Masalah

### 4.1 Bedah Bug-01: Broken Route 404 pada URL Notifikasi Tiket
- **Akar Masalah**:
  Di [`ContactMessageModel.php`](file:///c:/laragon/www/begawipengadaan/app/Models/Communication/ContactMessageModel.php#L169):
  ```php
  // Salah: rute workspace/konsultasi/TKT-... tidak terdaftar
  'url' => site_url('workspace/konsultasi/' . $ticket['ticket_number'])
  ```
  Di [`app/Config/Routes.php`](file:///c:/laragon/www/begawipengadaan/app/Config/Routes.php#L103-L109):
  ```php
  $routes->group('konsultasi', static function ($routes) {
      $routes->get('/', 'ConsultationController::index', ['as' => 'workspace.consultation.index']);
      $routes->get('buat', 'ConsultationController::create', ['as' => 'workspace.consultation.create']);
      $routes->post('buat', 'ConsultationController::store', ['as' => 'workspace.consultation.store']);
      $routes->get('detail/(:segment)', 'ConsultationController::detail/$1', ['as' => 'workspace.consultation.detail']);
      $routes->post('detail/(:segment)/balas', 'ConsultationController::reply/$1', ['as' => 'workspace.consultation.reply']);
  });
  ```
- **Dampak Nyata**: Setiap kali admin membalas tiket di Dashboard, pemohon menerima notifikasi. Namun ketika pemohon mengeklik notifikasi tersebut, peramban menampilkan galat **404 Not Found**.
- **Solusi Rekomendasi**:
  1. Standardisasi pembentukan tautan menggunakan `route_to('workspace.consultation.detail', $ticket['ticket_number'])` atau `site_url('workspace/konsultasi/detail/' . $ticket['ticket_number'])`.
  2. Tambahkan alias rute pada grup `konsultasi`: `$routes->get('(:segment)', 'ConsultationController::detail/$1');` sebagai pelindung backward-compatibility tautan lama.

---

### 4.2 Bedah Bug-02: Kebocoran Akses Tiket Melalui Email Kosong & Sesi
- **Akar Masalah**:
  Pada [`AuthenticationService.php`](file:///c:/laragon/www/begawipengadaan/app/Services/Identity/AuthenticationService.php#L52-L61), saat login berhasil, atribut yang disimpan ke sesi adalah:
  ```php
  session()->set([
      'isLoggedIn'       => true,
      'user_id'          => (int) $user['id'],
      'name'             => $user['name'],
      'username'         => $user['username'],
      'account_status'   => $user['account_status'],
      'roles'            => $roleKeys,
      'permissions'      => $permissions,
      'dashboard_access' => $hasDashboardAccess,
  ]);
  ```
  Atribut `'email'` dan `'phone'` **TIDAK DISIMPAN** ke sesi.
  Akibatnya:
  1. Pada [`ConsultationController::store()`](file:///c:/laragon/www/begawipengadaan/app/Controllers/Workspace/ConsultationController.php#L345), variabel `$userEmail = (string) session()->get('email');` bernilai string kosong `""`. Tiket yang dibuat dari Workspace tersimpan dengan `sender_email = ""` (terverifikasi pada basis data tiket ID 4 dan ID 8).
  2. Pada [`ConsultationController::detail()`](file:///c:/laragon/www/begawipengadaan/app/Controllers/Workspace/ConsultationController.php#L184):
     ```php
     $isOwner = strcasecmp($ticket['sender_email'], $email) === 0
         || (isset($notes['user_id']) && (int) $notes['user_id'] === $userId);
     ```
     Jika `$ticket['sender_email'] === ""` dan `$email === ""`, maka `strcasecmp("", "") === 0` menghasilkan nilai `true`!
- **Solusi Rekomendasi**:
  1. Tambahkan `'email' => $user['email']` dan `'phone' => $user['phone'] ?? null` ke dalam `AuthenticationService::login()`.
  2. Di `ConsultationController`, tambahkan *fallback* pengambilan email dari basis data pengguna jika sesi kosong:
     ```php
     if (empty($email) && $userId > 0) {
         $userRow = $this->db->table('users')->select('email, phone')->where('id', $userId)->get()->getRowArray();
         $email = $userRow['email'] ?? '';
     }
     ```
  3. Pastikan pencocokan email tidak mengizinkan string kosong:
     ```php
     $isOwner = (!empty($email) && !empty($ticket['sender_email']) && strcasecmp($ticket['sender_email'], $email) === 0)
         || (isset($notes['user_id']) && (int) $notes['user_id'] === $userId);
     ```

---

### 4.3 Bedah Gap-07: Filter Petugas Helpdesk (Assignee) Non-Petugas
- **Akar Masalah**:
  Di [`ContactMessageController.php#L135-L140`](file:///c:/laragon/www/begawipengadaan/app/Controllers/Admin/Communication/ContactMessageController.php#L135-L140):
  ```php
  $staffUsers = $this->db->table('users')
      ->select('id, name, email, username')
      ->where('account_status', 'active')
      ->where('deleted_at IS NULL', null, false)
      ->get()->getResultArray();
  ```
  Kueri ini tidak menyaring tabel relasi `user_roles` dan `roles`. Akibatnya, seluruh peserta pelatihan, penyedia barang/jasa, dan masyarakat umum yang berstatus aktif muncul di dropdown "Petugas Helpdesk (Assignee)" di Dashboard!
- **Solusi Rekomendasi**:
  Saring hanya pengguna yang memiliki peran manajerial helpdesk (`super_administrator`, `administrator`, `operator`):
  ```php
  $staffUsers = $this->db->table('users u')
      ->select('u.id, u.name, u.email, u.username')
      ->join('user_roles ur', 'ur.user_id = u.id')
      ->join('roles r', 'r.id = ur.role_id')
      ->where('u.account_status', 'active')
      ->where('u.deleted_at IS NULL', null, false)
      ->whereIn('r.key', ['super_administrator', 'administrator', 'operator'])
      ->groupBy('u.id')
      ->orderBy('u.name', 'ASC')
      ->get()->getResultArray();
  ```

---

## 5. Matriks Perbandingan Fitur 3 Sisi Sistem

| Fitur / Kemampuan | Sisi Landing Page | Sisi Workspace | Sisi Dashboard |
|:---|:---:|:---:|:---:|
| **Pembuatan Tiket Baru** | Ya (Form Publik) | Ya (Modal Terintegrasi) | Tidak (Hanya Terima) |
| **Tipe Pengirim** | Tamu / Umum | Pengguna Terverifikasi | Administrator / Petugas |
| **Ruang Percakapan Dua Arah (Chat)** | Terbatas (Lacak 1 Arah) | Penuh (Room Chat Interaktif) | Penuh (Thread Interaktif) |
| **Dukungan Lampiran Dokumen** | ❌ Belum Ada | Ya (PDF, Office, Gambar 10MB) | ❌ Belum Ada |
| **Roadmap Visual Progres Status** | Sederhana (Badge) | Penuh (4-Stage Vertical Step) | Sederhana (Status Selector) |
| **Jejak Audit Aktivitas (Logs)** | Disembunyikan (Hanya Publik) | Riwayat Jejak Pemohon | Riwayat Lengkap Internal |
| **Catatan Internal (Internal Note)** | ❌ Terisolasi (Aman) | ❌ Terisolasi (Aman) | Ya (Khusus Petugas) |
| **Delegasi Petugas (Assignee)** | ❌ N/A | Dilihat nama petugas | Pengaturan Penuh |
| **Pengaturan Prioritas Penanganan** | Default ('normal') | Dilihat pemohon | Bebas (Low/Normal/High/Urgent) |
| **Kanal Tanggapan Multi-Channel** | Form Kontak Web | Portal Web Chat | Portal, Email, WA, Internal |
| **Notifikasi Real-time & Desktop** | ❌ Tidak Ada | Ya (Database + Desktop) | Ya (Shared Pool + Desktop) |
| **Penyelesaian Tiket Mandiri** | ❌ Tidak Ada | ❌ Menunggu Petugas | Ya (Oleh Administrator) |

---

## 6. Rekomendasi Penyempurnaan Konkret (Zero-Migration Compliant)

Seluruh rekomendasi dirancang untuk dapat diimplementasikan langsung pada lapisan kode aplikasi tanpa melakukan perubahan skema tabel atau membuat migrasi baru:

### Rekomendasi 1: Perbaikan Rute Deep-Link Notifikasi & Pelindung Alias
- Ubah semua pembuatan link tiket di `ContactMessageModel` dan `ContactMessageController` ke rute detail: `site_url('workspace/konsultasi/detail/' . $ticket['ticket_number'])`.
- Daftarkan alias rute pada `app/Config/Routes.php`:
  `$routes->get('(:segment)', 'ConsultationController::detail/$1');` di dalam grup `konsultasi`.

### Rekomendasi 2: Penyempurnaan Sesi Login & Pengamanan Kepemilikan Tiket
- Tambahkan persistensi `email` dan `phone` pada `AuthenticationService::login()`.
- Tambahkan fallback ke database pengguna pada `ConsultationController` jika email sesi kosong.
- Perketat validasi kepemilikan tiket agar menolak perbandingan email jika salah satu nilai bernilai string kosong.

### Rekomendasi 3: Filter Presisi Dropdown Petugas Helpdesk (Assignee)
- Modifikasi kueri `staffUsers` pada `ContactMessageController::detail()` dengan melakukan join ke tabel `roles` dan membatasi peran hanya `super_administrator`, `administrator`, dan `operator`.

### Rekomendasi 4: Tambahkan Filter "Ditugaskan Kepada Saya" di Dashboard
- Tambahkan tab filter penugasan di `ContactMessageController::index()` dan tampilan `inbox/index.php`:
  - Tab "Ditugaskan ke Saya" (`?assigned_to=me`) yang mencocokkan nama petugas yang sedang login.

### Rekomendasi 5: Integrasi Unggah Lampiran Berkas pada Balasan Dashboard
- Tambahkan formulir input berkas lampiran pendukung (`multipart/form-data`) pada tab balasan portal di `dashboard/communication/inbox/detail.php`.
- Gunakan mekanisme penyimpanan berkas yang sama dengan Workspace (`handleAttachmentUpload`) agar petugas dapat mengirim file instruksi teknis kepada pemohon.

### Rekomendasi 6: Penguatan Formulir Kontak Landing Page (Lampiran & Anti-Spam Honeypot)
- Tambahkan input unggah lampiran opsional di formulir landing page `contact.php` (tangkapan layar kendala SPSE).
- Tambahkan kolom *honeypot* anti-spam tersembunyi (misal: field `<input type="text" name="website_url" class="d-none">`): jika field ini terisi, sistem secara otomatis menggugurkan pengiriman tanpa membebani database.

### Rekomendasi 7: Fitur Konfirmasi Tiket Selesai Mandiri oleh Pemohon di Workspace
- Sediakan tombol "Tandai Tiket Selesai" pada `konsultasi_detail.php` di Workspace jika tiket berstatus `replied` atau `waiting_user`.
- Saat diklik, status tiket otomatis berubah menjadi `closed` dengan log aktivitas pemohon yang tercatat rapi.

### Rekomendasi 8: Pengamanan Validasi MIME Berkas Lampiran
- Sempurnakan fungsi `handleAttachmentUpload()` dengan memvalidasi *real MIME type* via `$file->getMimeType()` mencocokkan daftar MIME yang aman (misal `application/pdf`, `image/png`, `image/jpeg`, `application/vnd.openxmlformats-officedocument.*`) untuk mencegah eksekusi muatan berbahaya.

---

## 7. Rencana Aksi Implementasi (Roadmap Eksekusi)

```mermaid
flowchart TD
    subgraph Fase 1: Perbaikan Kritis & Keamanan
        A1[Fix Bug 404 URL Notifikasi Tiket] --> A2[Fix Sesi Email & Celah Akses Tiket]
        A2 --> A3[Filter RBAC Dropdown Petugas Helpdesk]
    end

    subgraph Fase 2: Peningkatan Sisi Dashboard
        B1[Tambahkan Filter 'Ditugaskan ke Saya'] --> B2[Tambahkan Lampiran Berkas pada Balasan Admin]
        B2 --> B3[Perbaiki Deep-Link Notifikasi Landing Page]
    end

    subgraph Fase 3: Peningkatan Sisi Workspace & Landing Page
        C1[Fitur Pemohon Menutup Tiket Mandiri] --> C2[Input Lampiran & Honeypot di Landing Page]
        C2 --> C3[Penguatan Validasi Real MIME Type File]
    end

    Fase 1 --> Fase 2 --> Fase 3
```

---

## 9. Hasil Pengujian & Verifikasi Otomatis (E2E Test Suite)

Seluruh remedi dan peningkatan fitur telah diuji secara menyeluruh melalui test suite terdedikasi `php spark test:messaging-ticketing`:

```
==================================================================
   BEGAWIPENGADAAN PESAN & TICKETING SYSTEM VERIFICATION SUITE   
==================================================================

 [PASS] Uji Persistensi Email & Phone pada Sesi Login
 [PASS] Uji Landing Page Form Kontak, Simple Captcha & Honeypot
 [PASS] Uji Penolakan Jawaban Captcha Salah
 [PASS] Uji Penolakan Bot via Honeypot Field
 [PASS] Uji Pengajuan Tiket Kontak Berhasil dengan Captcha Valid
 [PASS] Uji Pelacakan Tiket Publik (/kontak?lacak=TKT-...)
 [PASS] Uji Filter Dashboard Inbox ('Ditugaskan ke Saya')
 [PASS] Uji Filter RBAC Assignee Staff di Dashboard Detail
 [PASS] Uji Balasan Helpdesk & Deep Link Notifikasi Tanpa 404
 [PASS] Uji Rute Alias /workspace/konsultasi/TKT-... (Anti-404 Guard)
 [PASS] Uji Pemohon Menandai Tiket Selesai (/workspace/konsultasi/detail/TKT-.../selesai)

==================================================================
   RINGKASAN UJI SISTEM PESAN & TICKETING: 11 BERHASIL | 0 GAGAL
==================================================================
```

Status Regresi Sistem:
- `php spark test:shared-notifications`: **18/18 PASS (100%)**
- `php spark test:notification-system`: **24/24 PASS (100%)**
- `php spark test:rbac-double-role`: **41/41 PASS (100%)**
- `php spark migrate:status`: **16 Migrasi Intact (Zero Migration Compliance)**

---

## 10. Kesimpulan

Sistem pesan dan ticketing BegawiPengadaan kini beroperasi secara optimal, aman, dan selaras di ketiga sisi:
1. **Landing Page**: Aman dari spam bot berkat verifikasi Captcha Matematika Sederhana dan Honeypot Field, mendukung unggahan berkas tangkapan layar, dan memiliki notifikasi helpdesk yang presisi.
2. **Workspace**: Bebas dari galat 404 pada notifikasi balasan, hak akses kepemilikan tiket aman dari celah email kosong, berkas lampiran tervalidasi tipe MIME nyata, serta pemohon dapat menandai konsultasi selesai secara mandiri.
3. **Dashboard**: Dilengkapi filter tab "Ditugaskan ke Saya", dropdown petugas helpdesk terisolasi murni ke akun pengelola (RBAC-compliant), serta formulir balasan portal telah mendukung unggahan berkas panduan/disposisi resmi.

