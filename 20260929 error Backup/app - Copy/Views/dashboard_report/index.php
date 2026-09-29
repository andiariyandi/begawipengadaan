<?= $this->extend('layout/layout1'); ?>

<?= $this->section('content'); ?>

<div class="mb-24 d-flex align-items-center justify-content-between flex-wrap gap-3">
    <h6 class="mb-0">Dashboard PBJ</h6>
    <?php if (in_array('dashboard.view', session()->get('permissions') ?? [], true)): ?>
        <div class="d-flex gap-2">
            <?php $exportQuery = http_build_query(array_filter(['from' => $from ?? null, 'to' => $to ?? null], static fn ($value) => $value !== null && $value !== '')); ?>
            <a href="<?= route_to('dashboard.export.pdf') . ($exportQuery ? '?' . esc($exportQuery) : '') ?>" class="btn btn-outline-primary btn-sm px-16 py-8 radius-8">Export PDF</a>
            <a href="<?= route_to('dashboard.export.excel') . ($exportQuery ? '?' . esc($exportQuery) : '') ?>" class="btn btn-outline-primary btn-sm px-16 py-8 radius-8">Export Excel</a>
        </div>
    <?php endif; ?>
</div>

<div class="card p-24 radius-12 mb-24">
    <form method="get" class="row g-3">
        <div class="col-md-4">
            <label class="form-label">Dari Tanggal</label>
            <input type="date" name="from" class="form-control" value="<?= esc($from ?? '') ?>">
        </div>
        <div class="col-md-4">
            <label class="form-label">Sampai Tanggal</label>
            <input type="date" name="to" class="form-control" value="<?= esc($to ?? '') ?>">
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <button type="submit" class="btn btn-primary px-24 py-12 radius-8">Terapkan Filter</button>
        </div>
    </form>
    <p class="text-secondary-light text-sm mb-0 mt-8">Filter tanggal berlaku untuk statistik pelatihan dan nilai quiz.</p>
</div>

<div class="row g-4 mb-24">
    <div class="col-md-3">
        <div class="card p-24 radius-12 text-center">
            <p class="text-secondary-light mb-4">Total Pengguna</p>
            <h4 class="mb-0"><?= esc($userStats['total']) ?></h4>
            <p class="text-secondary-light text-sm mb-0">ASN: <?= esc($userStats['asn']) ?> · Umum: <?= esc($userStats['umum']) ?></p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-24 radius-12 text-center">
            <p class="text-secondary-light mb-4">Peserta Aktif</p>
            <h4 class="mb-0"><?= esc($training['active_participants']) ?></h4>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-24 radius-12 text-center">
            <p class="text-secondary-light mb-4">Tingkat Kelulusan</p>
            <h4 class="mb-0"><?= esc($training['pass_rate']) ?>%</h4>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-24 radius-12 text-center">
            <p class="text-secondary-light mb-4">Sertifikat Aktif</p>
            <h4 class="mb-0"><?= esc($certificate['active']) ?></h4>
            <p class="text-secondary-light text-sm mb-0">dari <?= esc($certificate['total']) ?> total</p>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card p-24 radius-12 mb-24">
            <h6 class="mb-16">Distribusi Role</h6>
            <?php foreach ($userStats['by_role'] as $r): ?>
                <div class="d-flex justify-content-between border-bottom py-8">
                    <span><?= esc($r['name']) ?></span>
                    <span class="fw-medium"><?= esc($r['total']) ?></span>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="card p-24 radius-12">
            <h6 class="mb-16">Statistik Pelatihan</h6>
            <div class="d-flex justify-content-between border-bottom py-8"><span>Total Program</span><span class="fw-medium"><?= esc($training['total_courses']) ?></span></div>
            <div class="d-flex justify-content-between border-bottom py-8"><span>Total Kelas</span><span class="fw-medium"><?= esc($training['total_classes']) ?></span></div>
            <div class="d-flex justify-content-between py-8"><span>Nilai Rata-rata Quiz</span><span class="fw-medium"><?= esc($training['average_score']) ?></span></div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card p-24 radius-12 mb-24">
            <h6 class="mb-16">Regulasi per Kategori</h6>
            <?php foreach ($regulation['by_category'] as $r): ?>
                <div class="d-flex justify-content-between border-bottom py-8">
                    <span><?= esc($r['name']) ?></span>
                    <span class="fw-medium"><?= esc($r['total']) ?></span>
                </div>
            <?php endforeach; ?>
            <p class="text-secondary-light text-sm mt-12 mb-0">Total: <?= esc($regulation['total']) ?> dokumen</p>
        </div>

        <div class="card p-24 radius-12">
            <h6 class="mb-16">Regulasi Terbaru</h6>
            <?php foreach ($regulation['latest'] as $r): ?>
                <div class="border-bottom py-8">
                    <p class="mb-0 fw-medium"><?= esc($r['title']) ?></p>
                    <p class="text-secondary-light text-sm mb-0"><?= esc($r['nomor_regulasi']) ?> &middot; <?= esc(date('d M Y', strtotime($r['created_at']))) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>