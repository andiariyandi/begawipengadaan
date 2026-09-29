<?= $this->extend('layout/layout1'); ?>

<?= $this->section('content'); ?>

<div class="card p-24 radius-12">
    <h6 class="mb-24">Tambah Kelas — Program #<?= esc($courseId) ?></h6>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger radius-12 mb-16"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>

    <?php if (empty($trainers)): ?>
        <div class="alert alert-warning radius-12">
            Belum ada user dengan role Trainer. Tambahkan role Trainer ke salah satu user terlebih dahulu di menu
            <a href="<?= route_to('assignRole') ?>">Assign Role</a>.
        </div>
    <?php else: ?>
    <form action="<?= route_to('learning.classes.store', $courseId) ?>" method="post">
        <?= csrf_field() ?>

        <div class="mb-16">
            <label class="form-label">Trainer</label>
            <select name="trainer_id" class="form-select">
                <option value="">-- Pilih Trainer --</option>
                <?php foreach ($trainers as $trainer): ?>
                    <option value="<?= $trainer['id'] ?>" <?= old('trainer_id') == $trainer['id'] ? 'selected' : '' ?>>
                        <?= esc($trainer['username']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="row">
            <div class="col-md-6 mb-16">
                <label class="form-label">Tanggal Mulai</label>
                <input type="date" name="start_date" class="form-control" value="<?= esc(old('start_date')) ?>">
            </div>
            <div class="col-md-6 mb-16">
                <label class="form-label">Tanggal Selesai</label>
                <input type="date" name="end_date" class="form-control" value="<?= esc(old('end_date')) ?>">
            </div>
        </div>

        <div class="mb-16">
            <label class="form-label">Kuota Peserta</label>
            <input type="number" name="quota" min="1" class="form-control" value="<?= esc(old('quota')) ?>">
        </div>

        <button type="submit" class="btn btn-primary px-24 py-12 radius-8">Simpan Kelas</button>
    </form>
    <?php endif; ?>
</div>

<?= $this->endSection(); ?>
