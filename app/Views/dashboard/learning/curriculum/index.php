<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$classes         = $classes ?? [];
$selectedClass   = $selectedClass ?? null;
$selectedClassId = (int) ($selectedClassId ?? 0);
$modules         = $modules ?? [];
$totalLessons    = (int) ($totalLessons ?? 0);
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Kurikulum Pembelajaran') ?></h6>
        <p class="text-secondary-light mb-0">Kelola silabus, modul berurutan, dan materi belajar (lessons) pada kelas terpilih.</p>
    </div>
    <div class="d-flex flex-wrap align-items-center gap-2">
        <?php if ($selectedClassId > 0): ?>
            <button type="button" class="btn btn-primary-600 radius-8 px-20 py-11 d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#addModuleModal">
                <iconify-icon icon="solar:add-circle-outline" class="text-xl"></iconify-icon>
                <span>Tambah Modul</span>
            </button>
            <a href="<?= site_url('dashboard/pembelajaran/kelas/' . $selectedClassId) ?>" class="btn btn-outline-neutral-300 radius-8 px-16 py-11">
                Detail Kelas
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- Class Switcher Banner -->
<div class="card mb-24">
    <div class="card-body p-20">
        <div class="row gy-3 align-items-center justify-content-between">
            <div class="col-lg-5">
                <label class="form-label text-xs fw-semibold text-secondary-light text-uppercase mb-4">Pilih Kelas Pembelajaran</label>
                <select class="form-select radius-8" onchange="window.location.href='<?= site_url('dashboard/pembelajaran/kurikulum/kelas/') ?>' + this.value;">
                    <?php foreach ($classes as $cls): ?>
                        <option value="<?= esc($cls['id']) ?>" <?= ((int) $cls['id'] === $selectedClassId) ? 'selected' : '' ?>>
                            <?= esc($cls['name']) ?> (<?= esc($cls['code']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php if ($selectedClass): ?>
                <div class="col-lg-7">
                    <div class="d-flex flex-wrap align-items-center justify-content-lg-end gap-3 pt-2 pt-lg-0">
                        <div class="px-16 py-8 radius-8 bg-neutral-50 border text-center">
                            <span class="text-xs text-secondary-light d-block">Course</span>
                            <span class="fw-semibold text-sm text-neutral-900"><?= esc($selectedClass['course_title'] ?? '—') ?></span>
                        </div>
                        <div class="px-16 py-8 radius-8 bg-neutral-50 border text-center">
                            <span class="text-xs text-secondary-light d-block">Total Modul</span>
                            <span class="fw-bold text-sm text-primary-600"><?= count($modules) ?> Modul</span>
                        </div>
                        <div class="px-16 py-8 radius-8 bg-neutral-50 border text-center">
                            <span class="text-xs text-secondary-light d-block">Total Materi</span>
                            <span class="fw-bold text-sm text-info-600"><?= $totalLessons ?> Lesson</span>
                        </div>
                        <div class="px-16 py-8 radius-8 bg-neutral-50 border text-center">
                            <span class="text-xs text-secondary-light d-block">Status Kelas</span>
                            <?= view('dashboard/components/status_badge', ['status' => $selectedClass['status']]) ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modules & Lessons List -->
<?php if (empty($modules)): ?>
    <div class="card">
        <div class="card-body p-32 text-center">
            <?= view('dashboard/components/empty_state', [
                'title'       => 'Belum Ada Modul Pembelajaran',
                'message'     => 'Kelas ini belum memiliki silabus atau modul. Klik tombol "Tambah Modul" untuk mulai menyusun kurikulum.',
                'actionUrl'   => '#',
                'actionLabel' => 'Tambah Modul Pertama',
                'icon'        => 'solar:book-bookmark-outline',
            ]) ?>
        </div>
    </div>
<?php else: ?>
    <div class="d-flex flex-column gap-20">
        <?php foreach ($modules as $index => $mod): ?>
            <?php $lessons = $mod['lessons'] ?? []; ?>
            <div class="card shadow-none border radius-12">
                <div class="card-header bg-base py-16 px-24 border-bottom d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div class="d-flex align-items-center gap-12">
                        <span class="w-32-px h-32-px rounded-circle bg-primary-50 text-primary-600 d-flex justify-content-center align-items-center fw-bold text-sm">
                            <?= esc($mod['sort_order']) ?>
                        </span>
                        <div>
                            <h6 class="text-md fw-semibold mb-0 text-neutral-900"><?= esc($mod['title']) ?></h6>
                            <?php if (! empty($mod['description'])): ?>
                                <p class="text-xs text-secondary-light mb-0 mt-2"><?= esc($mod['description']) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <?php if (! empty($mod['is_required'])): ?>
                            <span class="badge bg-warning-50 text-warning-600 px-8 py-4 radius-4 text-xs">Wajib</span>
                        <?php endif; ?>
                        <?= view('dashboard/components/status_badge', ['status' => $mod['status']]) ?>
                        
                        <button type="button" class="btn btn-sm btn-primary-50 text-primary-600 radius-6 px-12 py-6 d-inline-flex align-items-center gap-1 ms-8" 
                                data-bs-toggle="modal" data-bs-target="#addLessonModal" 
                                onclick="document.getElementById('modal_lesson_module_id').value='<?= $mod['id'] ?>'; document.getElementById('lesson_module_name').innerText='<?= esc(addslashes($mod['title'])) ?>';">
                            <iconify-icon icon="solar:add-circle-outline"></iconify-icon>
                            <span>+ Lesson</span>
                        </button>

                        <button type="button" class="btn btn-sm btn-outline-neutral-300 radius-6 px-8 py-6 text-secondary-light"
                                data-bs-toggle="modal" data-bs-target="#editModuleModal"
                                onclick="populateEditModule(<?= htmlspecialchars(json_encode($mod), ENT_QUOTES, 'UTF-8') ?>)">
                            <iconify-icon icon="solar:pen-new-square-outline"></iconify-icon>
                        </button>

                        <form action="<?= site_url('dashboard/pembelajaran/kurikulum/modul/' . $mod['id'] . '/hapus') ?>" method="post" onsubmit="return confirm('Hapus modul ini beserta pengaturannya?');" class="d-inline">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-sm btn-outline-danger radius-6 px-8 py-6 text-danger-main">
                                <iconify-icon icon="solar:trash-bin-trash-outline"></iconify-icon>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="card-body p-0">
                    <?php if (empty($lessons)): ?>
                        <div class="p-20 text-center text-secondary-light text-sm">
                            Belum ada materi pada modul ini. Klik <strong>+ Lesson</strong> untuk menambahkan materi teks, video, atau dokumen.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive scroll-sm">
                            <table class="table bordered-table sm-table mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;">Urutan</th>
                                        <th>Tipe</th>
                                        <th>Judul Materi (Lesson)</th>
                                        <th>Sifat</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center" style="width: 100px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($lessons as $les): ?>
                                        <tr>
                                            <td class="text-center fw-semibold text-secondary-light"><?= esc($les['sort_order']) ?></td>
                                            <td>
                                                <?php
                                                $icon = match($les['content_type'] ?? 'text') {
                                                    'video' => 'solar:play-circle-outline',
                                                    'document', 'pdf' => 'solar:document-outline',
                                                    default => 'solar:text-square-outline',
                                                };
                                                ?>
                                                <span class="badge bg-neutral-100 text-neutral-800 d-inline-flex align-items-center gap-1 radius-4 px-8 py-4 text-xs font-monospace">
                                                    <iconify-icon icon="<?= $icon ?>"></iconify-icon>
                                                    <?= esc(ucfirst($les['content_type'] ?? 'text')) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <h6 class="text-sm fw-medium mb-0 text-neutral-900"><?= esc($les['title']) ?></h6>
                                                <span class="text-xs text-secondary-light font-monospace"><?= esc($les['slug'] ?? '') ?></span>
                                            </td>
                                            <td>
                                                <?= ! empty($les['is_required']) ? '<span class="text-xs text-warning-main fw-medium">Wajib Selesai</span>' : '<span class="text-xs text-secondary-light">Opsional</span>' ?>
                                            </td>
                                            <td class="text-center">
                                                <?= view('dashboard/components/status_badge', ['status' => $les['status']]) ?>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-inline-flex align-items-center gap-1">
                                                    <button type="button" class="btn btn-sm btn-outline-warning-main radius-4 px-8 py-4"
                                                            data-bs-toggle="modal" data-bs-target="#editLessonModal"
                                                            onclick="populateEditLesson(<?= htmlspecialchars(json_encode($les), ENT_QUOTES, 'UTF-8') ?>)" title="Ubah Lesson">
                                                        <iconify-icon icon="solar:pen-new-square-outline"></iconify-icon>
                                                    </button>
                                                    <form action="<?= site_url('dashboard/pembelajaran/kurikulum/lesson/' . $les['id'] . '/hapus') ?>" method="post" onsubmit="return confirm('Hapus materi ini?');" class="d-inline">
                                                        <?= csrf_field() ?>
                                                        <button type="submit" class="btn btn-sm btn-outline-danger radius-4 px-8 py-4" title="Hapus Lesson">
                                                            <iconify-icon icon="solar:trash-bin-trash-outline"></iconify-icon>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Add Module Modal -->
<div class="modal fade" id="addModuleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content radius-16">
            <div class="modal-header border-bottom py-16 px-24">
                <h6 class="modal-title fw-semibold text-lg">Tambah Modul Baru</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= site_url('dashboard/pembelajaran/kurikulum/modul') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="class_id" value="<?= esc($selectedClassId) ?>">
                <div class="modal-body p-24">
                    <div class="mb-16">
                        <label class="form-label fw-semibold text-sm mb-6" for="mod_title">Nama Modul <span class="text-danger-main">*</span></label>
                        <input id="mod_title" type="text" name="title" class="form-control radius-8" required placeholder="Contoh: Modul 1: Konsep Dasar Pengadaan">
                    </div>
                    <div class="mb-16">
                        <label class="form-label fw-semibold text-sm mb-6" for="mod_description">Deskripsi / Capaian Pembelajaran</label>
                        <textarea id="mod_description" name="description" class="form-control radius-8" rows="3" placeholder="Ringkasan isi modul..."></textarea>
                    </div>
                    <div class="row gy-16 mb-16">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-sm mb-6" for="mod_sort">Urutan Ke-</label>
                            <input id="mod_sort" type="number" min="1" name="sort_order" class="form-control radius-8" value="<?= count($modules) + 1 ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-sm mb-6" for="mod_status">Status</label>
                            <select id="mod_status" name="status" class="form-select radius-8">
                                <option value="published">Published</option>
                                <option value="draft">Draft</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-check">
                        <input id="mod_req" type="checkbox" name="is_required" value="1" class="form-check-input" checked>
                        <label class="form-check-label text-sm fw-medium" for="mod_req">Modul wajib diselesaikan peserta</label>
                    </div>
                </div>
                <div class="modal-footer border-top py-16 px-24">
                    <button type="button" class="btn btn-outline-neutral-300 radius-8 px-20 py-11" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11">Simpan Modul</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Module Modal -->
<div class="modal fade" id="editModuleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content radius-16">
            <div class="modal-header border-bottom py-16 px-24">
                <h6 class="modal-title fw-semibold text-lg">Ubah Modul</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editModuleForm" action="" method="post">
                <?= csrf_field() ?>
                <div class="modal-body p-24">
                    <div class="mb-16">
                        <label class="form-label fw-semibold text-sm mb-6" for="edit_mod_title">Nama Modul <span class="text-danger-main">*</span></label>
                        <input id="edit_mod_title" type="text" name="title" class="form-control radius-8" required>
                    </div>
                    <div class="mb-16">
                        <label class="form-label fw-semibold text-sm mb-6" for="edit_mod_description">Deskripsi</label>
                        <textarea id="edit_mod_description" name="description" class="form-control radius-8" rows="3"></textarea>
                    </div>
                    <div class="row gy-16 mb-16">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-sm mb-6" for="edit_mod_sort">Urutan</label>
                            <input id="edit_mod_sort" type="number" min="1" name="sort_order" class="form-control radius-8">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-sm mb-6" for="edit_mod_status">Status</label>
                            <select id="edit_mod_status" name="status" class="form-select radius-8">
                                <option value="published">Published</option>
                                <option value="draft">Draft</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-check">
                        <input id="edit_mod_req" type="checkbox" name="is_required" value="1" class="form-check-input">
                        <label class="form-check-label text-sm fw-medium" for="edit_mod_req">Modul wajib diselesaikan peserta</label>
                    </div>
                </div>
                <div class="modal-footer border-top py-16 px-24">
                    <button type="button" class="btn btn-outline-neutral-300 radius-8 px-20 py-11" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11">Perbarui Modul</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Lesson Modal -->
<div class="modal fade" id="addLessonModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content radius-16">
            <div class="modal-header border-bottom py-16 px-24">
                <div>
                    <h6 class="modal-title fw-semibold text-lg mb-1">Tambah Materi Baru (Lesson)</h6>
                    <p class="text-xs text-secondary-light mb-0">Pada: <span id="lesson_module_name" class="fw-semibold text-primary-600">Modul</span></p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= site_url('dashboard/pembelajaran/kurikulum/lesson') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" id="modal_lesson_module_id" name="module_id" value="">
                <div class="modal-body p-24">
                    <div class="row gy-16 mb-16">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold text-sm mb-6" for="les_title">Judul Materi <span class="text-danger-main">*</span></label>
                            <input id="les_title" type="text" name="title" class="form-control radius-8" required placeholder="Contoh: Pengenalan SPSE dan E-Katalog">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-sm mb-6" for="les_type">Tipe Konten</label>
                            <select id="les_type" name="content_type" class="form-select radius-8">
                                <option value="text">Artikel / Teks</option>
                                <option value="video">Video</option>
                                <option value="document">Dokumen / PDF</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-16">
                        <label class="form-label fw-semibold text-sm mb-6" for="les_content">Isi Konten / Penjelasan</label>
                        <textarea id="les_content" name="content" class="form-control radius-8" rows="4" placeholder="Tulis uraian materi pembelajaran di sini..."></textarea>
                    </div>
                    <div class="mb-16">
                        <label class="form-label fw-semibold text-sm mb-6" for="les_ext">Tautan Eksternal / Video URL (Opsional)</label>
                        <input id="les_ext" type="url" name="external_url" class="form-control radius-8" placeholder="https://youtube.com/... atau URL link materi">
                    </div>
                    <div class="row gy-16 mb-16">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-sm mb-6" for="les_sort">Urutan Ke-</label>
                            <input id="les_sort" type="number" min="1" name="sort_order" class="form-control radius-8" value="1">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-sm mb-6" for="les_status">Status</label>
                            <select id="les_status" name="status" class="form-select radius-8">
                                <option value="published">Published</option>
                                <option value="draft">Draft</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-check">
                        <input id="les_req" type="checkbox" name="is_required" value="1" class="form-check-input" checked>
                        <label class="form-check-label text-sm fw-medium" for="les_req">Materi ini wajib diselesaikan peserta</label>
                    </div>
                </div>
                <div class="modal-footer border-top py-16 px-24">
                    <button type="button" class="btn btn-outline-neutral-300 radius-8 px-20 py-11" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11">Simpan Materi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Lesson Modal -->
<div class="modal fade" id="editLessonModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content radius-16">
            <div class="modal-header border-bottom py-16 px-24">
                <h6 class="modal-title fw-semibold text-lg">Ubah Materi Pembelajaran</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editLessonForm" action="" method="post">
                <?= csrf_field() ?>
                <div class="modal-body p-24">
                    <div class="row gy-16 mb-16">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold text-sm mb-6" for="edit_les_title">Judul Materi <span class="text-danger-main">*</span></label>
                            <input id="edit_les_title" type="text" name="title" class="form-control radius-8" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-sm mb-6" for="edit_les_type">Tipe Konten</label>
                            <select id="edit_les_type" name="content_type" class="form-select radius-8">
                                <option value="text">Artikel / Teks</option>
                                <option value="video">Video</option>
                                <option value="document">Dokumen / PDF</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-16">
                        <label class="form-label fw-semibold text-sm mb-6" for="edit_les_content">Isi Konten</label>
                        <textarea id="edit_les_content" name="content" class="form-control radius-8" rows="4"></textarea>
                    </div>
                    <div class="mb-16">
                        <label class="form-label fw-semibold text-sm mb-6" for="edit_les_ext">Tautan Eksternal / Video URL</label>
                        <input id="edit_les_ext" type="url" name="external_url" class="form-control radius-8">
                    </div>
                    <div class="row gy-16 mb-16">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-sm mb-6" for="edit_les_sort">Urutan</label>
                            <input id="edit_les_sort" type="number" min="1" name="sort_order" class="form-control radius-8">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-sm mb-6" for="edit_les_status">Status</label>
                            <select id="edit_les_status" name="status" class="form-select radius-8">
                                <option value="published">Published</option>
                                <option value="draft">Draft</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-check">
                        <input id="edit_les_req" type="checkbox" name="is_required" value="1" class="form-check-input">
                        <label class="form-check-label text-sm fw-medium" for="edit_les_req">Materi ini wajib diselesaikan peserta</label>
                    </div>
                </div>
                <div class="modal-footer border-top py-16 px-24">
                    <button type="button" class="btn btn-outline-neutral-300 radius-8 px-20 py-11" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11">Perbarui Materi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function populateEditModule(mod) {
    document.getElementById('editModuleForm').action = '<?= site_url('dashboard/pembelajaran/kurikulum/modul/') ?>' + mod.id + '/ubah';
    document.getElementById('edit_mod_title').value = mod.title || '';
    document.getElementById('edit_mod_description').value = mod.description || '';
    document.getElementById('edit_mod_sort').value = mod.sort_order || 1;
    document.getElementById('edit_mod_status').value = mod.status || 'published';
    document.getElementById('edit_mod_req').checked = (mod.is_required == 1);
}

function populateEditLesson(les) {
    document.getElementById('editLessonForm').action = '<?= site_url('dashboard/pembelajaran/kurikulum/lesson/') ?>' + les.id + '/ubah';
    document.getElementById('edit_les_title').value = les.title || '';
    document.getElementById('edit_les_type').value = les.content_type || 'text';
    document.getElementById('edit_les_content').value = les.content || '';
    document.getElementById('edit_les_ext').value = les.external_url || '';
    document.getElementById('edit_les_sort').value = les.sort_order || 1;
    document.getElementById('edit_les_status').value = les.status || 'published';
    document.getElementById('edit_les_req').checked = (les.is_required == 1);
}
</script>

<?= $this->endSection() ?>
