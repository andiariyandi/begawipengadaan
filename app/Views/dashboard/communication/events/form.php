<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$isEdit = ($formMode ?? 'create') === 'edit';
$item = $item ?? [];
?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Event') ?></h6>
        <p class="text-secondary-light mb-0">Form event PBJ.</p>
    </div>
    <a href="<?= site_url('dashboard/komunikasi/event-agenda') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Kembali</a>
</div>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger radius-8 mb-16"><?= esc(session()->getFlashdata('error')) ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-body p-24">
        <form action="<?= $isEdit ? site_url('dashboard/komunikasi/event-agenda/' . (int) ($eventId ?? 0) . '/ubah') : site_url('dashboard/komunikasi/event-agenda/tambah') ?>" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="row gy-20">
                <div class="col-md-8">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Nama Event <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control radius-8" value="<?= esc(old('title', $item['title'] ?? '')) ?>" required maxlength="255">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Tipe Event</label>
                    <select class="form-select radius-8" name="type">
                        <option value="online" <?= ($item['type'] ?? 'online') === 'online' ? 'selected' : '' ?>>Online</option>
                        <option value="offline" <?= ($item['type'] ?? '') === 'offline' ? 'selected' : '' ?>>Offline</option>
                        <option value="hybrid" <?= ($item['type'] ?? '') === 'hybrid' ? 'selected' : '' ?>>Hybrid</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Mode Pendaftaran</label>
                    <select class="form-select radius-8" name="mode">
                        <option value="open" <?= ($item['mode'] ?? 'open') === 'open' ? 'selected' : '' ?>>Terbuka</option>
                        <option value="invitation" <?= ($item['mode'] ?? '') === 'invitation' ? 'selected' : '' ?>>Undangan</option>
                        <option value="closed" <?= ($item['mode'] ?? '') === 'closed' ? 'selected' : '' ?>>Tertutup</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Status</label>
                    <select class="form-select radius-8" name="status">
                        <option value="draft" <?= ($item['status'] ?? 'draft') === 'draft' ? 'selected' : '' ?>>Draft</option>
                        <option value="coming_soon" <?= ($item['status'] ?? '') === 'coming_soon' ? 'selected' : '' ?>>Akan Datang</option>
                        <option value="open_registration" <?= ($item['status'] ?? '') === 'open_registration' ? 'selected' : '' ?>>Pendaftaran Dibuka</option>
                        <option value="full" <?= ($item['status'] ?? '') === 'full' ? 'selected' : '' ?>>Penuh</option>
                        <option value="finished" <?= ($item['status'] ?? '') === 'finished' ? 'selected' : '' ?>>Selesai</option>
                        <option value="cancelled" <?= ($item['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Dibatalkan</option>
                        <option value="archived" <?= ($item['status'] ?? '') === 'archived' ? 'selected' : '' ?>>Diarsipkan</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Visibilitas</label>
                    <select class="form-select radius-8" name="visibility">
                        <option value="public" <?= ($item['visibility'] ?? 'public') === 'public' ? 'selected' : '' ?>>Publik</option>
                        <option value="internal" <?= ($item['visibility'] ?? '') === 'internal' ? 'selected' : '' ?>>Internal</option>
                        <option value="restricted" <?= ($item['visibility'] ?? '') === 'restricted' ? 'selected' : '' ?>>Restricted</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Mulai</label>
                    <input type="datetime-local" name="start_at" class="form-control radius-8"
                           value="<?= esc(! empty($item['start_at']) ? date('Y-m-d\TH:i', strtotime($item['start_at'])) : old('start_at')) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Selesai</label>
                    <input type="datetime-local" name="end_at" class="form-control radius-8"
                           value="<?= esc(! empty($item['end_at']) ? date('Y-m-d\TH:i', strtotime($item['end_at'])) : old('end_at')) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Lokasi</label>
                    <input type="text" name="location" class="form-control radius-8"
                           value="<?= esc(old('location', $item['location'] ?? '')) ?>" placeholder="Zoom Meeting atau Bandar Lampung">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Meeting URL</label>
                    <input type="url" name="meeting_url" class="form-control radius-8"
                           value="<?= esc(old('meeting_url', $item['meeting_url'] ?? '')) ?>" placeholder="https://...">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Pendaftaran Dibuka</label>
                    <input type="datetime-local" name="registration_open_at" class="form-control radius-8"
                           value="<?= esc(! empty($item['registration_open_at']) ? date('Y-m-d\TH:i', strtotime($item['registration_open_at'])) : old('registration_open_at')) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Pendaftaran Ditutup</label>
                    <input type="datetime-local" name="registration_close_at" class="form-control radius-8"
                           value="<?= esc(! empty($item['registration_close_at']) ? date('Y-m-d\TH:i', strtotime($item['registration_close_at'])) : old('registration_close_at')) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Kapasitas</label>
                    <input type="number" min="0" name="capacity" class="form-control radius-8"
                           value="<?= esc(old('capacity', $item['capacity'] ?? '')) ?>" placeholder="100">
                </div>
                <div class="col-md-6 d-flex align-items-end gap-12">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="waitlist_enabled" id="waitlist" value="1" <?= ! empty($item['waitlist_enabled']) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="waitlist">Waitlist</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="certificate_enabled" id="cert" value="1" <?= ! empty($item['certificate_enabled']) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="cert">Sertifikat</label>
                    </div>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Ringkasan</label>
                    <textarea name="summary" class="form-control radius-8" rows="2"><?= esc(old('summary', $item['summary'] ?? '')) ?></textarea>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Deskripsi</label>
                    <textarea name="description" class="form-control radius-8" rows="5"><?= esc(old('description', $item['description'] ?? '')) ?></textarea>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Cover</label>
                    <input type="file" name="cover" class="form-control radius-8" accept="image/*">
                </div>
                <div class="col-12 d-flex justify-content-end gap-12">
                    <a href="<?= site_url('dashboard/komunikasi/event-agenda') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Batal</a>
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11"><?= $isEdit ? 'Simpan Perubahan' : 'Simpan Event' ?></button>
                </div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
