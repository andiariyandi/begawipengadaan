<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$items       = $items ?? [];
$status      = $filterStatus ?? null;
$keyword     = $filterKeyword ?? null;
$stats       = $stats ?? ['total' => count($items), 'open' => 0, 'coming_soon' => 0, 'finished' => 0];
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Event & Agenda PBJ</h6>
        <p class="text-secondary-light mb-0">Kelola agenda seminar, workshop pengadaan, sosialisasi regulasi, dan bimbingan teknis.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="<?= route_to('dashboard.communication.events.create') ?>" class="btn btn-primary-600 radius-8 px-20 py-11 d-flex align-items-center gap-2">
            <iconify-icon icon="solar:add-circle-outline" class="text-xl"></iconify-icon>
            Tambah Event
        </a>
    </div>
</div>

<!-- KPI Cards -->
<div class="row row-cols-xxl-4 row-cols-md-2 row-cols-1 g-3 mb-24">
    <div class="col">
        <div class="card p-20 radius-12 border bg-base shadow-none">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-secondary-light text-sm mb-1 d-block">Total Agenda</span>
                    <h5 class="fw-semibold mb-0"><?= number_format($stats['total'] ?? 0) ?></h5>
                </div>
                <div class="w-48-px h-48-px bg-info-50 text-info-600 rounded-circle d-flex align-items-center justify-content-center text-2xl">
                    <iconify-icon icon="solar:calendar-outline"></iconify-icon>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card p-20 radius-12 border bg-base shadow-none">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-secondary-light text-sm mb-1 d-block">Pendaftaran Dibuka</span>
                    <h5 class="fw-semibold text-success-main mb-0"><?= number_format($stats['open'] ?? 0) ?></h5>
                </div>
                <div class="w-48-px h-48-px bg-success-50 text-success-600 rounded-circle d-flex align-items-center justify-content-center text-2xl">
                    <iconify-icon icon="solar:user-plus-outline"></iconify-icon>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card p-20 radius-12 border bg-base shadow-none">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-secondary-light text-sm mb-1 d-block">Segera Hadir</span>
                    <h5 class="fw-semibold text-warning-main mb-0"><?= number_format($stats['coming_soon'] ?? 0) ?></h5>
                </div>
                <div class="w-48-px h-48-px bg-warning-50 text-warning-600 rounded-circle d-flex align-items-center justify-content-center text-2xl">
                    <iconify-icon icon="solar:clock-circle-outline"></iconify-icon>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card p-20 radius-12 border bg-base shadow-none">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-secondary-light text-sm mb-1 d-block">Telah Selesai</span>
                    <h5 class="fw-semibold text-neutral-600 mb-0"><?= number_format($stats['finished'] ?? 0) ?></h5>
                </div>
                <div class="w-48-px h-48-px bg-neutral-100 text-neutral-600 rounded-circle d-flex align-items-center justify-content-center text-2xl">
                    <iconify-icon icon="solar:check-read-outline"></iconify-icon>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Toolbar -->
<div class="card radius-12 mb-24 border">
    <div class="card-body p-20">
        <form method="get" action="<?= route_to('dashboard.communication.events') ?>">
            <div class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label class="form-label text-sm fw-medium mb-1">Cari Agenda</label>
                    <div class="position-relative">
                        <input type="text" name="q" class="form-control radius-8 ps-40" value="<?= esc($keyword ?? '') ?>" placeholder="Cari judul kegiatan atau seminar...">
                        <iconify-icon icon="solar:magnifer-linear" class="position-absolute top-50 start-0 translate-middle-y ms-16 text-secondary-light text-lg"></iconify-icon>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label text-sm fw-medium mb-1">Status Kegiatan</label>
                    <select name="status" class="form-select radius-8">
                        <option value="">Semua Status</option>
                        <option value="open_registration" <?= $status === 'open_registration' ? 'selected' : '' ?>>Pendaftaran Dibuka</option>
                        <option value="coming_soon" <?= $status === 'coming_soon' ? 'selected' : '' ?>>Akan Datang</option>
                        <option value="published" <?= $status === 'published' ? 'selected' : '' ?>>Dipublikasikan</option>
                        <option value="full" <?= $status === 'full' ? 'selected' : '' ?>>Kuota Penuh</option>
                        <option value="finished" <?= $status === 'finished' ? 'selected' : '' ?>>Selesai</option>
                        <option value="cancelled" <?= $status === 'cancelled' ? 'selected' : '' ?>>Dibatalkan</option>
                        <option value="draft" <?= $status === 'draft' ? 'selected' : '' ?>>Draft</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary-600 radius-8 flex-grow-1 d-flex align-items-center justify-content-center gap-1">
                        <iconify-icon icon="solar:filter-outline"></iconify-icon> Filter
                    </button>
                    <a href="<?= route_to('dashboard.communication.events') ?>" class="btn btn-outline-neutral-300 radius-8" title="Reset">
                        <iconify-icon icon="solar:restart-outline"></iconify-icon>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>


<!-- Event Cards Grid -->
<?php if (empty($items)): ?>
    <?= view('dashboard/components/empty_state', [
        'icon'        => 'solar:calendar-outline',
        'title'       => 'Belum Ada Event',
        'message'     => 'Belum ada agenda seminar atau bimbingan teknis yang sesuai kriteria pencarian.',
        'actionUrl'   => route_to('dashboard.communication.events.create'),
        'actionText'  => 'Tambah Event Baru',
    ]) ?>
<?php else: ?>
    <div class="row gy-4 mb-24">
        <?php foreach ($items as $e): ?>
            <?php
                $statusKey = $e['status'] ?? 'draft';
                $modeKey = strtolower($e['mode'] ?? 'online');
            ?>
            <div class="col-xxl-4 col-md-6">
                <div class="card h-100 p-0 radius-12 border overflow-hidden bg-base d-flex flex-column">
                    <div class="card-body p-20 d-flex flex-column flex-grow-1">
                        <!-- Top Badges -->
                        <div class="d-flex align-items-center justify-content-between gap-2 mb-16">
                            <div class="d-flex align-items-center gap-1">
                                <span class="px-10 py-4 bg-primary-50 text-primary-600 rounded-pill text-xs fw-semibold">
                                    <?= esc(ucfirst($e['type'] ?? 'Workshop')) ?>
                                </span>
                                <span class="px-10 py-4 bg-neutral-100 text-neutral-800 rounded-pill text-xs fw-medium">
                                    <?= esc(ucfirst($modeKey)) ?>
                                </span>
                            </div>
                            <div>
                                <?= view('dashboard/components/status_badge', ['status' => $statusKey]) ?>
                            </div>
                        </div>

                        <!-- Title -->
                        <h6 class="mb-12">
                            <a href="<?= route_to('dashboard.communication.events.detail', (string) $e['id']) ?>" class="text-line-2 text-hover-primary-600 text-lg fw-semibold transition-2">
                                <?= esc($e['title'] ?? '(Tanpa judul event)') ?>
                            </a>
                        </h6>

                        <!-- Schedule & Venue info -->
                        <div class="mb-16">
                            <div class="d-flex align-items-center gap-2 text-sm text-secondary-light mb-2">
                                <iconify-icon icon="solar:calendar-date-outline" class="text-primary-600 text-lg flex-shrink-0"></iconify-icon>
                                <span><?= ! empty($e['start_at']) ? esc(date('d M Y, H:i', strtotime($e['start_at']))) : 'Jadwal belum ditentukan' ?></span>
                            </div>
                            <div class="d-flex align-items-center gap-2 text-sm text-secondary-light">
                                <iconify-icon icon="solar:map-point-outline" class="text-primary-600 text-lg flex-shrink-0"></iconify-icon>
                                <span class="text-line-1"><?= esc($e['location'] ?: ($e['meeting_url'] ?: 'Daring / Online Platform')) ?></span>
                            </div>
                        </div>

                        <!-- Footer Actions -->
                        <div class="pt-16 border-top mt-auto d-flex align-items-center justify-content-between">
                            <span class="text-xs text-secondary-light">
                                Kuota: <strong><?= (int) ($e['quota'] ?? 0) > 0 ? number_format($e['quota']) . ' Peserta' : 'Tak Terbatas' ?></strong>
                            </span>
                            <div class="dropdown">
                                <button type="button" class="btn btn-outline-neutral-200 btn-sm radius-8 px-8 py-4 text-secondary-light" data-bs-toggle="dropdown">
                                    <iconify-icon icon="solar:menu-dots-bold"></iconify-icon>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm radius-8">
                                    <li>
                                        <a class="dropdown-item text-sm d-flex align-items-center gap-2" href="<?= route_to('dashboard.communication.events.detail', (string) $e['id']) ?>">
                                            <iconify-icon icon="solar:eye-outline"></iconify-icon> Lihat Detail & Peserta
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item text-sm d-flex align-items-center gap-2" href="<?= route_to('dashboard.communication.events.edit', (string) $e['id']) ?>">
                                            <iconify-icon icon="solar:pen-2-outline"></iconify-icon> Ubah Event
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider my-1"></li>
                                    <li>
                                        <button type="button" class="dropdown-item text-sm text-danger-main d-flex align-items-center gap-2 border-0 bg-transparent w-100 text-start"
                                                data-bs-toggle="modal"
                                                data-bs-target="#confirmationActionModal"
                                                data-action="<?= route_to('dashboard.communication.events.delete', (string) $e['id']) ?>"
                                                data-method="POST"
                                                data-title="Hapus Event / Agenda"
                                                data-message="Apakah Anda yakin ingin menghapus agenda &quot;<?= esc($e['title'] ?? '') ?>&quot;?"
                                                data-btn-text="Ya, Hapus"
                                                data-btn-class="btn-danger-600"
                                                data-icon="fluent:delete-24-regular"
                                                data-icon-color="danger">
                                            <iconify-icon icon="solar:trash-bin-trash-outline"></iconify-icon>
                                            <span>Hapus</span>
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>
