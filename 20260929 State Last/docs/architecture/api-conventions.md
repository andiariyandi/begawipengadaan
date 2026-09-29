# BegawiPBJ API Conventions

Dokumen ini menetapkan kontrak endpoint JSON/AJAX BegawiPBJ. Antarmuka utama memakai server-rendered CodeIgniter 4; API hanya dipakai bila interaksi asinkron dibutuhkan (wilayah bertingkat, pencarian, tabel, notifikasi, quiz, upload, dan verifikasi). Kontrak di sini tidak mengubah batas UI: Landingpage + Workspace memakai Main UI, Dashboard khusus Admin/Super Admin.

## Prinsip

- Semua endpoint terdaftar eksplisit dengan HTTP method, filter, permission, ownership, dan rate limit bila perlu.
- Hindari auto-routing untuk action sensitif; jangan mengandalkan menu tersembunyi.
- Gunakan HTTPS pada production. Form session memakai cookie dan CSRF; tidak perlu token API publik tambahan tanpa kebutuhan nyata.
- Respons JSON tidak pernah berisi stack trace, SQL, path fisik, encryption key, hash password, NIK/NIP, atau data ASN terbatas pada endpoint publik.
- Simpan timestamp di UTC; keluarkan ISO 8601 dengan offset yang terdokumentasi. Waktu tampilan WIB dikonversi di UI.
- Setiap write memakai validasi input, transaksi untuk perubahan lintas tabel, dan audit bila sensitif.

## Format sukses

```json
{
  "success": true,
  "message": "Data berhasil disimpan.",
  "data": { "id": 42, "status": "submitted" },
  "meta": { "request_id": "req-example", "timestamp": "2026-09-28T00:00:00Z" }
}
```

Daftar berhalaman:

```json
{
  "success": true,
  "message": "Data ditemukan.",
  "data": [{ "id": 1, "title": "Course PBJ" }],
  "meta": {
    "page": 1,
    "per_page": 20,
    "total": 72,
    "total_pages": 4,
    "request_id": "req-example"
  }
}
```

## Format error

```json
{
  "success": false,
  "message": "Validasi gagal.",
  "errors": { "title": ["Judul wajib diisi."] },
  "meta": { "request_id": "req-example" }
}
```

Untuk kesalahan yang bukan validasi, `errors` dapat berupa objek kosong. Jangan kembalikan detail sistem ke klien; log internal menyimpan detail dengan `request_id` yang sama.

## Status HTTP

| Status | Kondisi | Contoh |
|---:|---|---|
| 200 | GET atau aksi idempotent sukses | Daftar kelas, verifikasi sertifikat valid |
| 201 | Record dibuat | Registrasi event, submission baru |
| 204 | Sukses tanpa body | Aksi yang memang tidak membutuhkan payload |
| 400 | Permintaan rusak | Format parameter tidak dapat dibaca |
| 401 | Belum login | Session hilang/kedaluwarsa |
| 403 | Tidak berizin | Role, permission, ownership, atau status akun melarang |
| 404 | Resource tidak ada/tidak boleh diungkap | ID tidak ditemukan atau file tidak tersedia |
| 409 | Konflik state/unique | Duplikasi enrollment, event penuh, sertifikat sudah ada |
| 413 | Upload terlalu besar | Melebihi batas domain |
| 415 | Tipe file tidak didukung | MIME/ekstensi salah |
| 422 | Validasi bisnis gagal | Transition workflow tidak sah, field wajib kosong |
| 429 | Terlalu banyak request | Login/QR verification/search rate limit |
| 500 | Error internal | Pesan aman + request ID; detail hanya di log |

Untuk user tak berwenang, pilih 404 jika keberadaan objek sendiri perlu disembunyikan; jika objek boleh diketahui tetapi aksi tidak diizinkan, gunakan 403.

## Query dan pagination

| Parameter | Aturan |
|---|---|
| `page` | Integer minimal 1, default 1 |
| `per_page` | Default 20; maksimum 100 kecuali ekspor khusus |
| `q` | Panjang dibatasi dan di-escape saat ditampilkan |
| `sort` | Whitelist kolom per endpoint, bukan input SQL mentah |
| `direction` | Hanya `asc` atau `desc` |
| `status` | Whitelist status domain |
| `from`, `to` | Tanggal ISO; `from <= to`; batas periode untuk query berat |
| `opd_id` | Hanya untuk permission internal; FK dan scope diverifikasi |

Indeks dan filter harus diselaraskan dengan `begawipbj-database-blueprint.md` dan data dictionary.

## Endpoint inti

| Kebutuhan | Method + path | Akses | Respons minimal |
|---|---|---|---|
| Dropdown wilayah | GET `/api/wilayah?parent=18` | Publik, rate limit | Kode dan nama anak langsung berdasarkan prefix/panjang; tidak membuat/menambah wilayah |
| Pencarian course | GET `/api/arena-belajar` | Publik | Hanya course published, paginated |
| Pencarian regulasi | GET `/api/regulasi` | Publik | Metadata published, legal status, tanpa path privat |
| Pencarian Bunga Rampai | GET `/api/bunga-rampai` | Publik | Konten published saja |
| Statistik publik | GET `/api/statistik-pbj` | Publik, cache | Agregat saja, tanpa identitas |
| Verifikasi sertifikat | GET `/api/sertifikat/verifikasi/{token}` | Publik, rate limit | Status, nomor, detail minimum; no raw token field in body |
| Notifikasi saya | GET `/api/workspace/notifikasi` | Login aktif | Notifikasi user sendiri |
| Progress saya | GET `/api/workspace/progres` | Peserta aktif | Enrollment sendiri |
| Jawaban quiz | POST `/api/workspace/attempt/{id}/jawaban` | Peserta aktif + CSRF | Attempt sendiri dan masih in_progress |
| Filter tabel admin | GET `/api/admin/users` | Admin + `users.view` | Paginated, field sesuai permission |
| Metrics internal | GET `/api/intelligence/metrics` | `intelligence.view` | Agregat internal sesuai scope |

## Upload dan download

Upload memakai `multipart/form-data`. Server memeriksa ekstensi **dan** MIME, ukuran maksimum per domain, ownership/permission, path aman, serta mencatat metadata di `file_assets`. File fisik ditempatkan di `writable/uploads/<domain>/`.

```json
{
  "success": true,
  "message": "File diunggah.",
  "data": { "file_asset_id": 15, "original_name": "materi.pdf", "size_bytes": 81012 },
  "meta": { "request_id": "req-example" }
}
```

- Jangan mengembalikan `disk`, `stored_name`, path fisik, atau URL privat langsung kepada publik.
- `file_assets.path = NULL` berarti file belum tersedia; UI menonaktifkan download dan endpoint menolak akses.
- Download melalui controller yang memeriksa status entity, ownership/enrollment/role/permission, lalu stream file.
- Template, regulasi, materi, submission, dan sertifikat memakai scope yang berbeda menurut `docs/architecture/rbac-matrix.md`.

## Auth, CSRF, dan rate limit

- Session-based browser: CSRF token untuk POST/PUT/PATCH/DELETE.
- Cookie session `Secure`, `HttpOnly`, dan `SameSite` sesuai deployment.
- Redirect HTML login boleh dipakai untuk navigasi halaman; endpoint JSON harus memakai status 401/403, bukan mengirim HTML login dengan status 200.
- Rate limit wajib untuk login, reset password, verifikasi sertifikat; disarankan untuk pencarian publik dan endpoint upload.
- Jangan memasukkan NIP/NIK, email, atau nama ASN lengkap pada log request publik.

## Idempotensi dan konflik

| Operasi | Constraint | Respons ulang |
|---|---|---|
| Enrollment kelas | `UNIQUE(user_id, learning_class_id)` | 409 atau objek yang sudah ada sesuai kontrak endpoint |
| Registrasi event | `UNIQUE(event_id, user_id)` | 409 atau objek yang sudah ada |
| Attempt quiz | `UNIQUE(quiz_id, enrollment_id, attempt_number)` | 409 bila request paralel konflik |
| Penerbitan sertifikat | `UNIQUE(source_type, source_id)` | Kembalikan sertifikat yang sama jika idempotent; jangan buat kedua |
| Upload file | ID file baru setelah berhasil disimpan | Rollback metadata/bersihkan file bila transaksi gagal |

## Kontrak perubahan state

Semua endpoint transisi harus memanggil service domain dan mengikuti `docs/architecture/workflow-state-machine.md`. Validasi dilakukan berurutan: login → status akun → permission → ownership → source ada → transition sah → transaction → audit → notifikasi setelah commit.

## Dokumentasi endpoint

Untuk setiap endpoint baru, catat: method, URL, request field, auth/permission/scope, contoh respons sukses/error, status HTTP, rate limit, efek audit, dan test. Tambahkan route pada `docs/architecture/route-map.md` sebelum dianggap siap rilis.
