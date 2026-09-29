<?= $this->extend('layout/layout1'); ?>

<?= $this->section('content'); ?>

<div class="mb-24">
    <h6 class="mb-4">Modul & Materi — <?= esc($class['course_title']) ?></h6>
    <p class="text-secondary-light text-sm mb-0">
        Trainer: <?= esc($class['trainer_name']) ?> &middot;
        <?= esc(date('d M Y', strtotime($class['start_date']))) ?> &ndash; <?= esc(date('d M Y', strtotime($class['end_date']))) ?>
    </p>
</div>

<?php if (session()->getFlashdata('message')): ?>
    <div class="alert alert-success radius-12 mb-16"><?= esc(session()->getFlashdata('message')) ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger radius-12 mb-16"><?= esc(session()->getFlashdata('error')) ?></div>
<?php endif; ?>

<div class="card p-24 radius-12 mb-24">
    <h6 class="mb-16">Tambah Modul Baru</h6>
    <form action="<?= route_to('learning.modules.store', $class['id']) ?>" method="post" class="d-flex gap-3">
        <?= csrf_field() ?>
        <input type="text" name="title" class="form-control" placeholder="Judul modul, mis. Modul 1 - Dasar PBJ">
        <button type="submit" class="btn btn-primary px-24 py-12 radius-8 text-nowrap">Tambah Modul</button>
    </form>
</div>

<?php if (empty($modules)): ?>
    <p class="text-secondary-light">Belum ada modul untuk kelas ini.</p>
<?php else: ?>
    <?php foreach ($modules as $module): ?>
        <div class="card p-0 radius-12 mb-24">
            <div class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center justify-content-between">
                <h6 class="mb-0">Modul <?= esc($module['sequence_order']) ?>: <?= esc($module['title']) ?></h6>
                <a href="<?= route_to('learning.assessments', $module['id']) ?>" class="btn btn-outline-primary btn-sm px-16 py-8 radius-8">Kelola Assessment</a>
            </div>
            <div class="card-body p-24">

                <?php if (empty($module['lessons'])): ?>
                    <p class="text-secondary-light text-sm">Belum ada materi di modul ini.</p>
                <?php else: ?>
                    <div class="table-responsive scroll-sm mb-24">
                        <table class="table bordered-table sm-table mb-0">
                            <thead>
                                <tr>
                                    <th>Urutan</th>
                                    <th>Judul Materi</th>
                                    <th class="text-center">Tipe</th>
                                    <th class="text-center">Konten</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($module['lessons'] as $lesson): ?>
                                    <tr>
                                        <td><?= esc($lesson['sequence_order']) ?></td>
                                        <td><?= esc($lesson['title']) ?></td>
                                        <td class="text-center">
                                            <span class="bg-neutral-200 text-neutral-600 border border-neutral-400 px-16 py-4 radius-4 text-sm">
                                                <?= esc(ucfirst($lesson['content_type'])) ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($lesson['content_type'] === 'text'): ?>
                                                <span class="text-secondary-light text-sm">Teks langsung</span>
                                            <?php elseif (! empty($lesson['file_path'])): ?>
                                                <span class="text-success-600 text-sm"><iconify-icon icon="mdi:check-circle-outline"></iconify-icon> Terunggah</span>
                                            <?php else: ?>
                                                <span class="text-secondary-light text-sm">-</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

                <form action="<?= route_to('learning.lessons.store', $class['id'], $module['id']) ?>" method="post" enctype="multipart/form-data" class="row g-3 align-items-end">
                    <?= csrf_field() ?>
                    <div class="col-md-4">
                        <label class="form-label">Judul Materi</label>
                        <input type="text" name="title" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tipe Konten</label>
                        <select name="content_type" class="form-select" onchange="toggleLessonInput(this)">
                            <option value="document">Dokumen</option>
                            <option value="video">Video</option>
                            <option value="text">Teks</option>
                        </select>
                    </div>
                    <div class="col-md-3 lesson-file-field">
                        <label class="form-label">File</label>
                        <input type="file" name="file" class="form-control">
                    </div>
                    <div class="col-md-3 lesson-text-field d-none">
                        <label class="form-label">Isi Teks</label>
                        <textarea name="content_text" class="form-control" rows="1"></textarea>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary px-24 py-12 radius-8 w-100">Tambah</button>
                    </div>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?= $this->endSection(); ?>

<?= $this->section('script'); ?>
<script>
    function toggleLessonInput(select) {
        const form = select.closest('form');
        const fileField = form.querySelector('.lesson-file-field');
        const textField = form.querySelector('.lesson-text-field');
        if (select.value === 'text') {
            fileField.classList.add('d-none');
            textField.classList.remove('d-none');
        } else {
            fileField.classList.remove('d-none');
            textField.classList.add('d-none');
        }
    }
</script>
<?= $this->endSection(); ?>