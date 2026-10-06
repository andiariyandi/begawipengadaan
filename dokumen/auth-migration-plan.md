# Auth Migration Plan — Step 2+ Forward Step

> **Status:** Plan only. No migrations, controllers, or files outside `app/Config/Routes.php` have been modified yet.
>
> **Step 1 (route unblock)** — done in `app/Config/Routes.php`: the legacy `App\Controllers\Auth` namespace group is bypassed (commented out with explanatory header). `php spark routes` now runs without crashing. Legacy files and controllers were not deleted.
>
> **Step 2+ (this document)** — proposed. Not executed.

---

## 1. Current State After Step 1

### Active Auth Routes (verified via `php spark routes`)

| Method | Route | Notes |
|---|---|---|
| GET / POST | `dashboard/masuk` | Production login (`Dashboard\Auth\LoginController`) |
| GET | `dashboard/keluar` | Logout preview (calls `logout()`) |
| POST | `dashboard/keluar` | Production logout |
| GET / POST | `dashboard/daftar` | Multi-step registration (currently delegated to `Admin\AdminAuthController`) |
| GET / POST | `dashboard/daftar/wilayah` | Step 2 |
| GET | `dashboard/daftar/wilayah/options[/{code}]` | Dependent wilayah fetch |
| GET / POST | `dashboard/daftar/profil/{asn|penyedia|akademisi|masyarakat}` | Step 3 |
| GET / POST | `dashboard/daftar/tinjau` | Step 4 review |
| GET | `dashboard/daftar/berhasil` | Post-submit UI |
| GET | `dashboard/status-akun` | Account status page |
| GET / POST | `dashboard/lupa-kata-sandi` | Password reset request |
| GET / POST | `dashboard/reset-kata-sandi/{token}` | Password reset submit |
| GET | `dashboard/verifikasi/{token}` | **Email verification (forbidden by CLAUDE.md §9)** |

### Disabled Routes

All `auth.*` named routes under `App\Controllers\Auth` namespace are disabled:

```
auth.login, auth.logout.preview,
auth.register.account, auth.register.profile-type, auth.register.profile.{asn|provider|academic|public},
auth.register.review, auth.register.success,
auth.email-verification, auth.email-verification.sent, auth.email-verification.success,
auth.pending-verification, auth.revision-required,
auth.password.{forgot|reset|change|success}
```

These routes are NOT removed from the project (per CLAUDE.md §29). Re-enabling requires either restoring the deleted `app/Controllers/Auth/RegisterController.php` or accepting the broken references.

### Two Live Login Implementations

| Implementation | Path | Status |
|---|---|---|
| `Dashboard\Auth\LoginController` | Production — on `POST /dashboard/masuk` | Active |
| `Auth\LoginController` | Legacy — not on any route, file present | Inert, not deleted |

### Two Live Registration Implementations

| Implementation | Path | Status |
|---|---|---|
| `Admin\AdminAuthController` | Multi-step (akun/wilayah/profil/tinjau) | Active on `dashboard/daftar/*` |
| `Dashboard\Auth\RegisterController` | Single-attempt, calls `RegistrationService` | Not wired to any route |

---

## 2. Schema Drift Inventory (audit before any migration)

Source: `app/Database/Migrations/2026-09-13-000001_RebuildBegawiSchema.php` (the only populated migration; the `2026-09-28-*` set is empty files).

| Table | Schema column | Code that writes/expects | Status |
|---|---|---|---|
| `users` | `status` ENUM(`pending,active,inactive,rejected`) | `Auth\LoginController` reads `$user['status']` | OK for legacy |
| `users` | — (`account_status` does not exist) | `UserModel->allowedFields`, `AuthenticationService`, `Dashboard\Auth\RegisterController`, `RegistrationService` | **MISSING** |
| `users` | `user_type` ENUM(`asn,umum`) | Legacy only | Default `umumumum` |
| `users` | `nip`, `instansi` | Legacy (`AdminAuthController::registerReviewAttempt` writes via old code path) | OK |
| `users` | `email_verified_at` | `AdminAuthController::verifyEmail` writes | OK; **but CLAUDE.md §9 forbids use as gate** |
| `users` | `name` (legacy) vs `full_name` (in `user_profiles`) | `RegistrationService` writes `name` on `users` (no such column) | **MISSING on `users`** |
| `users` | `phone` | Schema lacks; `UserModel->allowedFields`, `RegistrationService` write `phone` | **MISSING** |
| `users` | `username` | OK | |
| `user_profiles` | `full_name`, `title_prefix`, `title_suffix`, `phone`, `province`, `regency`, `district`, `province_code`, `regency_code`, `district_code`, `village_code` | `AdminAuthController::registerReviewAttempt` writes `address_line` (column doesn't exist) | **MISSING `address_line`** |
| `user_profiles` | — | `RegistrationService` writes `address_line`, `profile_completion_status` | **MISSING both** |
| `asn_profiles` | `identity_number`, `identity_type`, `asn_status`, `institution`, `work_unit`, `position`, `grade`, `pbj_role` | `RegistrationService` writes `employee_identifier`, `employment_status`, `institution_name`, `unit_name`, `position_name`, `rank_grade`, `origin_type`, `official_email`, `organization_unit_id` | **MOSTLY MISSING** |
| `provider_profiles` | `nib`, `business_name`, `responsible_person`, `company_email`, `company_phone`, `province`, `regency` | `RegistrationService` writes via raw array (no field allowlist) | Unknown until service fills allowedFields |
| `academic_profiles` | `institution`, `expertise`, `email_contact`, `phone_contact`, `province`, `regency` | `RegistrationService` writes via raw array | Unknown |
| `public_profiles` | `nik`, `email_contact`, `phone_contact`, `province`, `regency` | `RegistrationService` writes via raw array | Unknown |
| `roles` | `slug` | `UserModel::getRoles` selects `roles.key` | **MISSING `key`** |
| `user_roles` | `user_id`, `role_id`, `created_at` | `UserModel::getRoles/getPermissions` filter `user_roles.status='active'` | **MISSING `status`** |
| `audit_logs` | `user_id`, `action`, `module`, `target_type`, `target_id`, `ip_address`, `metadata`, `created_at` | `AuditService` writes `actor_id`, `resource_type`, `resource_id`, `result`, `reason`, `ip_hash`, `user_agent_hash`, `request_id` | **MOSTLY MISSING — AuditService will crash on insert** |
| `wilayah` | `kode`, `nama` | OK | |
| `email_verifications` | — | `AdminAuthController::issueToken` writes to `email_verifications` | **TABLE MISSING entirely** |
| `profile_verifications` | — | `RegistrationService` writes to `profile_verifications` | **TABLE MISSING entirely** |
| `auth_tokens` | Migration file exists; table content not in baseline migration | `AdminAuthController::findToken` reads/writes | Schema not authoritative — must inspect DB |

### Critical Findings

1. **`AuditService` will crash on every login/register/logout event** because the columns it writes do not exist in `audit_logs`. Any login attempt should already be failing or producing an error. This is the most urgent silent breakage.
2. **`AuthenticationService` will crash on login** if `$user['account_status']` is read (column absent), but PHP returns null on missing array key and the comparison `!== 'active'` succeeds, allowing login. Status gate is currently inert.
3. **`RegistrationService::register()`** writes `users.name`, `users.phone`, `asn_profiles.employee_identifier`, `asn_profiles.employment_status`, `asn_profiles.institution_name`, `asn_profiles.unit_name`, `asn_profiles.position_name`, `asn_profiles.rank_grade`, `asn_profiles.origin_type`, `user_profiles.address_line`, `user_profiles.profile_completion_status`, `profile_verifications` — most of these columns/tables do not exist. Registration via the new service would fail.
4. **`AdminAuthController::registerReviewAttempt`** writes `users.created_at`/`updated_at` (timestamps managed by model) and `user_profiles.address_line` (no such column). Would fail on insert.

---

## 3. Proposed Migration Strategy

Two options for resolving schema drift. Pick **exactly one**. Do not mix.

##### Option A — Migration aligns schema to production code (recommended)

The new `Dashboard\Auth\RegisterController` + `RegistrationService` + `AuthenticationService` are closer to CLAUDE.md intent. Migrate schema forward.

1. Add columns to `users`: `account_status`, `phone`, `name`. Keep `status`, `email_verified_at`, `user_type`, `nip`, `instansi` for legacy transition.
2. Add columns to `user_profiles`: `address_line`, `profile_completion_status`.
3. Recreate `asn_profiles`, `provider_profiles`, `academic_profiles`, `public_profiles` to match CLAUDE.md §11 fields. Migrate legacy `user_asn_profiles` etc. data if any.
4. Rename or add `roles.key` (keep `slug`).
5. Add `user_roles.status` column.
6. Restructure `audit_logs` to match `AuditService` (`actor_id`, `resource_type`, `resource_id`, `result`, `reason`, `ip_hash`, `user_agent_hash`, `request_id`) OR change `AuditService` to write legacy columns (`user_id`, `module`, `target_type`, `target_id`, `ip_address`, `metadata`).
7. Add `profile_verifications` table.
8. Drop `email_verifications` (CLAUDE.md §9 forbids email-token gate) OR keep table but stop issuing tokens.

##### Option B — Migration aligns code to legacy schema

Roll back `Dashboard\Auth` controllers and services to use legacy column names. Slower admin auth weight, since legacy `users.status` enum is limited (`pending|active|inactive|rejected`) and does not match CLAUDE.md §9 state set.

**Not recommended.** Conflicts with CLAUDE.md.

---

## 4. Code Migration Plan (after schema)

### Step 2 — Single Auth Login Path

- Keep `Dashboard\Auth\LoginController` and `Services/Identity/AuthenticationService` as canonical.
- Leave `Auth\LoginController` and `Auth\ProfileController` files in place but document as deprecated. Do not delete (CLAUDE.md §29).
- Decide session key for roles: `key` (new code) or `slug` (legacy). Pick `key`, update `Auth\ProfileController` if it ever becomes routed again.

### Step 3 — Single Registration Path

- Move the multi-step methods out of `Admin\AdminAuthController` into `Dashboard\Auth\RegisterController` (and create `Dashboard\Auth\RegisterProfileController` for `profil/{type}`).
- Rewrite each method to call `RegistrationService` instead of inline DB writes.
- Update `app/Config/Routes.php` `dashboard/daftar/*` group to point at `Dashboard\Auth\RegisterController::register`, `registerAddress`, etc.
- Keep `Admin\AdminAuthController` for login/password/email-verify until full migration; then delete per CLAUDE.md §29.

### Step 4 — Disable Forbidden Email-Verification Gate

- Remove `AdminAuthController::verifyEmail` route from `Routes.php` (line 195).
- Remove `email_verifications` writes from `AdminAuthController::registerReviewAttempt`.
- Do not implement `EmailVerificationController` (CLAUDE.md §9).

### Step 5 — Disable Legacy `Auth` Namespace Group Permanently

- Leave the commented-out block in `Routes.php` (already done in Step 1).
- Once `Dashboard\Auth` registration is fully wired and `Admin\AdminAuthController` registration methods are deleted, the legacy `Auth\*` files become permanently unreachable.

### Step 6 — Delete Legacy Files

Only after all of: (a) no production route references them, (b) no service references them, (c) audit log confirms zero hits in last 7 days.

- `app/Controllers/Auth/LoginController.php`
- `app/Controllers/Auth/ProfileController.php` (currently the only file mounted on `/profil/*` routes — handle those routes separately first)
- `app/Controllers/Auth/RegisterProfileController.php` (empty stub)
- `app/Controllers/Auth/EmailVerificationController.php` (empty stub)
- `app/Controllers/Auth/PasswordController.php` (empty stub)
- `app/Controllers/Auth/RoleRequestController.php` (empty stub)
- `app/Controllers/Auth/ProfileChangeRequestController.php` (empty stub)
- `app/Controllers/Admin/AdminAuthController.php` (after extracting register methods)

### Step 7 — Seeder for Admin/Super Admin

- Create `app/Database/Seeds/AdminUserSeeder.php` (or add to existing seeder).
- Insert one `super_admin` user with known password.
- Insert roles: `super_admin`, `admin`, `operator`, `participant`, `trainer`, `evaluator`, `public_user`.
- Insert baseline permissions per CLAUDE.md §14.
- Wire `user_roles.status='active'` after the Step 2 schema alignment.

---

## 5. Required Schema Migration File List (Option A)

Drafted names only — do not create yet.

```
app/Database/Migrations/2026-10-XX-000001_AlignUsersSchemaForProduction.php
app/Database/Migrations/2026-10-XX-000002_AlignUserProfilesSchema.php
app/Database/Migrations/2026-10-XX-000003_RebuildProfileSpecificTables.php
app/Database/Migrations/2026-10-XX-000004_AlignRbacSchema.php
app/Database/Migrations/2026-10-XX-000005_AlignAuditLogsSchema.php
app/Database/Migrations/2026-10-XX-000006_AddProfileVerificationTable.php
app/Database/Migrations/2026-10-XX-000007_RemoveEmailVerificationTable.php
app/Database/Migrations/2026-10-XX-000008_SeedBaselineRolesPermissions.php
```

Each must include `down()` that preserves data (use rename, not drop, until verified).

---

## 6. Validation Checklist (before declaring each step done)

```bash
php spark routes                                # must succeed and show only intended routes
php spark cache:clear
php spark migrate:status                        # only after migrations are written
```

Manual flow tests per CLAUDE.md §30:

- [ ] GET `dashboard/masuk` renders
- [ ] POST `dashboard/masuk` valid active user → redirect to `dashboard`
- [ ] POST `dashboard/masuk` valid non-active user → blocked with generic message
- [ ] POST `dashboard/masuk` invalid → blocked with generic message
- [ ] POST `dashboard/keluar` invalidates session
- [ ] GET `dashboard/daftar` renders step 1
- [ ] POST `dashboard/daftar` saves step 1 to draft
- [ ] GET `dashboard/daftar/wilayah` requires step 1, dependent dropdowns work
- [ ] POST `dashboard/daftar/wilayah` saves step 2
- [ ] GET `dashboard/daftar/profil/{tipe}` requires step 2
- [ ] POST `dashboard/daftar/profil/{tipe}` saves step 3
- [ ] GET `dashboard/daftar/tinjau` requires all previous steps
- [ ] POST `dashboard/daftar/tinjau` writes atomically (users, user_profiles, profile-specific, profile_verifications)
- [ ] `audit_logs` has rows for: login, login_failed, login_blocked, logout, register
- [ ] Admin/Operator can approve profile verification → `account_status = 'active'`
- [ ] User can login after approval

---

## 7. Remaining Blockers (cannot proceed without decisions)

1. **Decision: Option A vs Option B schema strategy.** Without this, no migration can be authored.
3. **Actual DB inspection.** Schema above is from migration file; production DB may differ. Run `SHOW CREATE TABLE` for each affected table before authoring migrations.
4. **Session key decision** (`roles.key` vs `roles.slug`). Affects `UserModel`, `AuthenticationService`, `AuthorizationService`.
5. **Where to put multi-step registration methods** — `Dashboard\Auth\RegisterController` (single controller, multiple methods) or split into `RegisterController` + `RegisterProfileController` + `RegisterReviewController`. CLAUDE.md §9 names `RegisterController`, `RegisterProfileController`, `AccountStatusController`.
6. **No seeder exists** for `super_admin`/`admin`. Login cannot be tested end-to-end until one is created.
7. **Audit log column direction** — schema (`user_id`, `module`, `target_type`, `target_id`, `ip_address`, `metadata`) vs code (`actor_id`, `resource_type`, `resource_id`, `result`, `reason`, `ip_hash`, `user_agent_hash`, `request_id`). The current `AuditService` is broken either way; pick one and align both sides.
9. **Profile pages routes (`/profil/*`)** still in `Routes.php` lines 161-163 reference `Auth\ProfileController` (modified in working tree). Out of scope for auth cleanup but will block deletion of that file.
10. **Empty stub Auth controllers** (`EmailVerificationController`, `PasswordController`, `RegisterProfileController`, `RoleRequestController`, `ProfileChangeRequestController`) — all 1-line files. Decide: leave inert, or delete before audit, or implement.