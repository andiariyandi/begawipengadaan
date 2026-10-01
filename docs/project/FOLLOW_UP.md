# Migration Follow-up

## Procurement role management

`jf_ppbj_levels` exists in the runtime schema, but the target route contract
does not define a canonical management endpoint for procurement roles. The
legacy controller also exposes UI-only create/detail surfaces. It is therefore
blocked from this direct migration batch and is intentionally not re-exposed
under a new canonical route. A later batch must define its permission, route,
workflow, and additive schema mapping before implementation.

## Out-of-scope Admin controllers

Other `app/Controllers/Admin/*` controllers remain because they belong to
dashboard domains outside Auth/RBAC/Manajemen Akses. They are not part of this
batch and must not be treated as target architecture ownership.
