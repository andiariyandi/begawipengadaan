# Architecture Gap Register

This register records intentional differences between the target architecture
and the currently deployed fresh v2 runtime. Gaps are handled incrementally;
they are not permission to perform destructive migrations.

| Gap | Current runtime | Target contract | Compatibility decision | Owner / follow-up |
|---|---|---|---|---|
| Auth controller location | `app/Controllers/Admin/AdminAuthController.php` | `app/Controllers/Dashboard/Auth/` | Keep the legacy controller as a delegating adapter. New login/logout target controller uses shared identity service. | Identity migration |
| Dashboard namespace | Back-office controllers use `App\Controllers\Admin` | `App\Controllers\Dashboard` | Do not mass-rename. New implementations belong in Dashboard; legacy routes remain adapters during migration. | Dashboard migration |
| Access route prefix | `/dashboard/akses/...` | Canonical RBAC paths such as `/dashboard/rbac/...` | Preserve existing paths and aliases. Add canonical routes only per capability, after regression coverage. | RBAC migration |
| Role key naming | `roles.code` | Blueprint calls this a role `key` | Treat `code` as the technical role key in models/services. No column rename. | Data compatibility |
| Permission key naming | `permissions.code` | Blueprint calls this a permission `key` | Treat `code` as the technical permission key. Session permissions use `code`, not display `name`. | Data compatibility |
| Audit columns | Existing audit model/service fields differ across schema generations | Blueprint requires append-only audit context | Redact sensitive metadata in the shared service; verify exact deployed columns before further audit migration. | Audit hardening |
| Registration persistence | Existing multi-step flow is in legacy Auth controller | Target requires service-based registration | Leave flow unchanged in this batch; extract only after feature coverage and schema mapping are complete. | Registration migration |
| Authorization filters | Existing filter primarily checks session login | Target requires active account and permission checks | Add reusable authorization service and apply checks conservatively to dashboard access in a later guarded step. | Access hardening |

## Status

This batch is **Compatibility Foundation**, not Production Ready. Registration
service extraction, canonical RBAC route ownership, approval workflow
extraction, and full filter matrix remain blocked until their schema and
regression tests are completed.
