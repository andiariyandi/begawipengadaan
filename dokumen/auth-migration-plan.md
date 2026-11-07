# BegawiPengadaan — Dokumentasi Arsitektur Autentikasi Terpadu

> **Metadata Dokumen:**
> - **Versi:** 4.2 (Production Ready)
> - **Tanggal Sinkronisasi:** 7 November 2026
> - **Sumber Kebenaran:** MySQL Live `db_begawipbj` (95 Tabel, 1.127 Kolom, 22 CHECK Constraints, 17 Migrasi) & Codebase Aktual

---

## 1. Status Migrasi: SELESAI (Completed State V4.2)

Penyatuan seluruh alur autentikasi telah selesai 100% pada rilis V4.2. Tidak ada lagi fragmentasi controller login warisan di folder publik atau landing page. Seluruh pintu masuk autentikasi kini dipusatkan di `App\Controllers\Dashboard\Auth\` dengan antarmuka WowDash di `app/Views/dashboard/auth/`.

---

## 2. Struktur Controller & Rute Autentikasi Tunggal

| Fungsi | Controller Terpadu | View Path | Rute URL | HTTP Method |
|---|---|---|---|:---:|
| **Login** | `App\Controllers\Dashboard\Auth\LoginController::showLoginForm` | `dashboard/auth/login.php` | `/dashboard/masuk` | `GET` |
| **Proses Login** | `App\Controllers\Dashboard\Auth\LoginController::attemptLogin` | - | `/dashboard/masuk` | `POST` |
| **Pendaftaran** | `App\Controllers\Dashboard\Auth\RegisterController::showRegistrationForm` | `dashboard/auth/register.php` | `/dashboard/daftar` | `GET` |
| **Proses Daftar** | `App\Controllers\Dashboard\Auth\RegisterController::attemptRegister` | - | `/dashboard/daftar` | `POST` |
| **Profil Khusus** | `App\Controllers\Dashboard\Auth\RegisterProfileController::index` | `dashboard/auth/profile_form.php` | `/dashboard/daftar/profil` | `GET/POST` |
| **Lupa Sandi** | `App\Controllers\Dashboard\Auth\PasswordResetController::showForgotPasswordForm` | `dashboard/auth/forgot_password.php` | `/dashboard/lupa-kata-sandi` | `GET/POST` |
| **Reset Sandi** | `App\Controllers\Dashboard\Auth\PasswordResetController::showResetPasswordForm` | `dashboard/auth/reset_password.php` | `/dashboard/reset-kata-sandi/(:segment)` | `GET/POST` |
| **Status Akun** | `App\Controllers\Dashboard\Auth\AccountStatusController::index` | `dashboard/auth/account_status.php` | `/dashboard/status-akun` | `GET` |
| **Keluar** | `App\Controllers\Dashboard\Auth\LogoutController::index` | - | `/dashboard/keluar` | `POST/GET` |

---

## 3. Mekanisme Redirect Cerdas Pascalogin (`getFirstAllowedDashboardRoute`)

Setelah validasi kredensial berhasil, sistem menentukan destinasi pengalihan (*landing redirection*) secara deterministik:
1. Jika user memiliki role `super_administrator` atau `administrator`, diarahkan langsung ke `/dashboard`.
2. Jika user memiliki role modular (misal: `admin_arenabelajar`), diarahkan ke `/dashboard/pembelajaran`.
3. Jika user memiliki role `admin_event`, diarahkan ke `/dashboard/komunikasi/event-agenda`.
4. Jika user memiliki role `admin_regulasi`, diarahkan ke `/dashboard/regulasi/dokumen`.
5. Jika user adalah pengguna non-dashboard (`operator`, `trainer`, `participant`), diarahkan langsung ke `/workspace`.
