<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$activeTab         = $activeTab ?? 'learning';
$learningClasses   = $learningClasses ?? [];
$events            = $events ?? [];
$learningTemplates = $learningTemplates ?? [];
$eventTemplates    = $eventTemplates ?? [];
$batchSummary      = session()->getFlashdata('batch_summary');
?>

<!-- Header -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Batch Certificate Generator</h6>
        <p class="text-secondary-light mb-0">Konsol penerbitan sertifikat massal untuk kelas pembelajaran LMS dan agenda event PBJ.</p>
    </div>
</div>

<!-- Sub Navigation -->
<?= view('dashboard/certificates/_nav_certificate', ['activeSub' => 'batch']) ?>

<!-- Batch Summary Alert if just processed synchronously -->
<?php if (! empty($batchSummary) && is_array($batchSummary)): ?>
    <div class="card radius-12 border bg-base shadow-none mb-24 border-primary-600">
        <div class="card-body p-20">
            <div class="d-flex align-items-center justify-content-between mb-16">
                <div class="d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:check-circle-bold" class="text-2xl text-success-main"></iconify-icon>
                    <h6 class="mb-0 fw-semibold">Hasil Eksekusi Batch Sertifikat</h6>
                </div>
            </div>
            <div class="row row-cols-md-4 row-cols-2 g-3">
                <div class="col">
                    <div class="p-16 radius-8 bg-neutral-100">
                        <span class="text-xs text-secondary-light d-block mb-1">TOTAL TARGET</span>
                        <h4 class="fw-bold mb-0 text-primary-light"><?= (int) ($batchSummary['target'] ?? 0) ?></h4>
                    </div>
                </div>
                <div class="col">
                    <div class="p-16 radius-8 bg-success-50 text-success-600">
                        <span class="text-xs text-success-600 d-block mb-1">BERHASIL DITERBITKAN</span>
                        <h4 class="fw-bold mb-0 text-success-main"><?= (int) ($batchSummary['success'] ?? 0) ?></h4>
                    </div>
                </div>
                <div class="col">
                    <div class="p-16 radius-8 bg-warning-50 text-warning-600">
                        <span class="text-xs text-warning-600 d-block mb-1">DILEWATI (SUDAH ADA)</span>
                        <h4 class="fw-bold mb-0 text-warning-main"><?= (int) ($batchSummary['skipped'] ?? 0) ?></h4>
                    </div>
                </div>
                <div class="col">
                    <div class="p-16 radius-8 bg-danger-50 text-danger-600">
                        <span class="text-xs text-danger-600 d-block mb-1">GAGAL</span>
                        <h4 class="fw-bold mb-0 text-danger-main"><?= (int) ($batchSummary['failed'] ?? 0) ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- 2 Tab Console -->
<div class="card radius-12 border shadow-none">
    <div class="card-header border-bottom p-0">
        <ul class="nav nav-tabs border-0" role="tablist">
            <li class="nav-item">
                <a class="nav-link px-24 py-16 fw-semibold text-sm d-flex align-items-center gap-2 <?= $activeTab === 'learning' ? 'active text-primary-600 border-bottom border-primary-600 border-2' : 'text-secondary-light' ?>" href="<?= site_url('dashboard/sertifikat/batch?tab=learning') ?>">
                    <iconify-icon icon="solar:square-academic-cap-2-bold" class="text-lg"></iconify-icon>
                    Batch Kelas Pembelajaran (Learning)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link px-24 py-16 fw-semibold text-sm d-flex align-items-center gap-2 <?= $activeTab === 'event' ? 'active text-info-600 border-bottom border-info-600 border-2' : 'text-secondary-light' ?>" href="<?= site_url('dashboard/sertifikat/batch?tab=event') ?>">
                    <iconify-icon icon="solar:calendar-mark-bold" class="text-lg"></iconify-icon>
                    Batch Event &amp; Agenda
                </a>
            </li>
        </ul>
    </div>

    <div class="card-body p-24">
        <?php if ($activeTab === 'learning'): ?>
            <!-- Tab 1: Learning Class Batch -->
            <form id="formBatchLearning" method="post" action="<?= site_url('dashboard/sertifikat/batch/learning') ?>">
                <?= csrf_field() ?>
                <div class="row g-4">
                    <div class="col-lg-8">
                        <h6 class="text-md fw-semibold mb-3">Pilih Kelas Pembelajaran Target</h6>
                        <div class="mb-20">
                            <label class="form-label text-sm fw-medium mb-1">Kelas Pembelajaran <span class="text-danger-main">*</span></label>
                            <select name="class_id" id="learningClassSelect" class="form-select radius-8 py-10" required>
                                <option value="">-- Pilih Kelas Pembelajaran --</option>
                                <?php foreach ($learningClasses as $lc): ?>
                                    <?php
                                    $eligible = (int) ($lc['total_eligible'] ?? 0);
                                    $certified = (int) ($lc['total_certified'] ?? 0);
                                    $pending = max(0, $eligible - $certified);
                                    ?>
                                    <option value="<?= $lc['id'] ?>" data-title="<?= esc($lc['class_name'] ?? '') ?>" data-pending="<?= $pending ?>">
                                        <?= esc($lc['program_title'] ?? '') ?> &bull; <?= esc($lc['course_title'] ?? '') ?> — <?= esc($lc['class_name'] ?? '') ?>
                                        (Layak: <?= $eligible ?>, Terbit: <?= $certified ?>, Menunggu: <?= $pending ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <span class="text-xs text-secondary-light mt-1 d-block">
                                Hanya peserta dengan kelulusan status <code>completed</code> atau progress 100% yang akan diproses.
                            </span>
                        </div>

                        <div class="mb-20">
                            <label class="form-label text-sm fw-medium mb-1">Template Sertifikat</label>
                            <select name="template_id" id="learningTemplateSelect" class="form-select radius-8 py-10">
                                <option value="">Gunakan Template Default Pembelajaran</option>
                                <?php foreach ($learningTemplates as $tpl): ?>
                                    <option value="<?= $tpl['id'] ?>">
                                        <?= esc($tpl['name']) ?> (ID: <?= $tpl['id'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <span class="text-xs text-secondary-light mt-1 d-block">
                                Template sertifikat dapat diatur dan dikustomisasi pada menu Template Sertifikat.
                            </span>
                        </div>

                        <div class="p-16 radius-8 bg-neutral-100 mb-24">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <iconify-icon icon="solar:info-circle-bold" class="text-primary-600 text-lg"></iconify-icon>
                                <span class="fw-semibold text-sm text-primary-light">Aturan Idempotensi &amp; Eksekusi Stay-Alive:</span>
                            </div>
                            <ul class="text-xs text-secondary-light mb-0 ps-20">
                                <li>Pemrosesan berjalan secara <strong>berkala (chunked 5 peserta/bagian)</strong> dengan stay-alive auto-continuation tanpa takut timeout atau 504.</li>
                                <li>Peserta yang sudah memiliki sertifikat pada kelas ini akan otomatis <strong>dilewati</strong> (tidak ada duplikasi sertifikat).</li>
                                <li>Nomor sertifikat berformat atomik resmi: <code>SERT-{number}/AB-{ID}/05/{YEAR}</code>.</li>
                                <li>QR code verifikasi publik dan file PDF sertifikat langsung dibuat dan disimpan.</li>
                            </ul>
                        </div>

                        <button type="submit" id="btnSubmitLearningBatch" class="btn btn-primary-600 radius-8 px-24 py-12 d-inline-flex align-items-center gap-2">
                            <iconify-icon icon="solar:play-circle-bold" class="text-lg"></iconify-icon>
                            Generate Batch Sertifikat Kelas
                        </button>
                    </div>

                    <div class="col-lg-4">
                        <div class="card p-20 radius-12 border bg-base shadow-none">
                            <h6 class="text-sm fw-semibold mb-3">Statistik Kelas Belajar</h6>
                            <div class="d-flex align-items-center justify-content-between py-10 border-bottom">
                                <span class="text-xs text-secondary-light">Total Kelas Aktif</span>
                                <span class="fw-bold text-sm text-primary-light"><?= count($learningClasses) ?></span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between py-10 border-bottom">
                                <span class="text-xs text-secondary-light">Template Learning Aktif</span>
                                <span class="fw-bold text-sm text-primary-light"><?= count($learningTemplates) ?></span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between py-10">
                                <span class="text-xs text-secondary-light">Format Nomor</span>
                                <span class="font-monospace text-xs text-primary-600 fw-bold">SERT-XXXXX/AB-{ID}/05/<?= date('Y') ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        <?php else: ?>
            <!-- Tab 2: Event Batch -->
            <form id="formBatchEvent" method="post" action="<?= site_url('dashboard/sertifikat/batch/event') ?>">
                <?= csrf_field() ?>
                <div class="row g-4">
                    <div class="col-lg-8">
                        <h6 class="text-md fw-semibold mb-3">Pilih Event / Agenda Target</h6>
                        <div class="mb-20">
                            <label class="form-label text-sm fw-medium mb-1">Event / Agenda <span class="text-danger-main">*</span></label>
                            <select name="event_id" id="eventSelect" class="form-select radius-8 py-10" required>
                                <option value="">-- Pilih Event / Agenda --</option>
                                <?php foreach ($events as $ev): ?>
                                    <?php
                                    $present   = (int) ($ev['total_present'] ?? 0);
                                    $certified = (int) ($ev['total_certified'] ?? 0);
                                    $pending   = max(0, $present - $certified);
                                    ?>
                                    <option value="<?= $ev['id'] ?>" data-title="<?= esc($ev['title']) ?>" data-pending="<?= $pending ?>">
                                        <?= esc($ev['title']) ?> &bull; <?= ! empty($ev['start_at']) ? date('d M Y', strtotime($ev['start_at'])) : '' ?>
                                        (Hadir: <?= $present ?>, Terbit: <?= $certified ?>, Menunggu: <?= $pending ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <span class="text-xs text-secondary-light mt-1 d-block">
                                Hanya peserta dengan status kehadiran <strong class="text-success-main">Hadir (present)</strong> yang akan diproses.
                            </span>
                        </div>

                        <div class="mb-20">
                            <label class="form-label text-sm fw-medium mb-1">Template Sertifikat</label>
                            <select name="template_id" id="eventTemplateSelect" class="form-select radius-8 py-10">
                                <option value="">Gunakan Template Default Event</option>
                                <?php foreach ($eventTemplates as $tpl): ?>
                                    <option value="<?= $tpl['id'] ?>">
                                        <?= esc($tpl['name']) ?> (ID: <?= $tpl['id'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <span class="text-xs text-secondary-light mt-1 d-block">
                                Template sertifikat dapat diatur dan dikustomisasi pada menu Template Sertifikat.
                            </span>
                        </div>

                        <div class="p-16 radius-8 bg-neutral-100 mb-24">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <iconify-icon icon="solar:info-circle-bold" class="text-info-600 text-lg"></iconify-icon>
                                <span class="fw-semibold text-sm text-primary-light">Aturan Presensi &amp; Eksekusi Stay-Alive:</span>
                            </div>
                            <ul class="text-xs text-secondary-light mb-0 ps-20">
                                <li>Pemrosesan berjalan secara <strong>berkala (chunked 5 peserta/bagian)</strong> dengan stay-alive auto-continuation tanpa takut timeout atau 504.</li>
                                <li>Peserta yang absen / tidak hadir <strong>TIDAK AKAN</strong> mendapatkan sertifikat.</li>
                                <li>Peserta yang sudah diterbitkan sertifikatnya otomatis dilewati.</li>
                                <li>Nomor sertifikat event resmi: <code>SERT-{number}/{TIPE}-{ID}/05/{YEAR}</code>.</li>
                            </ul>
                        </div>

                        <button type="submit" id="btnSubmitEventBatch" class="btn btn-info-600 text-white radius-8 px-24 py-12 d-inline-flex align-items-center gap-2">
                            <iconify-icon icon="solar:play-circle-bold" class="text-lg"></iconify-icon>
                            Generate Batch Sertifikat Event
                        </button>
                    </div>

                    <div class="col-lg-4">
                        <div class="card p-20 radius-12 border bg-base shadow-none">
                            <h6 class="text-sm fw-semibold mb-3">Statistik Event PBJ</h6>
                            <div class="d-flex align-items-center justify-content-between py-10 border-bottom">
                                <span class="text-xs text-secondary-light">Total Event Terdata</span>
                                <span class="fw-bold text-sm text-primary-light"><?= count($events) ?></span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between py-10 border-bottom">
                                <span class="text-xs text-secondary-light">Template Event Aktif</span>
                                <span class="fw-bold text-sm text-primary-light"><?= count($eventTemplates) ?></span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between py-10">
                                <span class="text-xs text-secondary-light">Format Nomor</span>
                                <span class="font-monospace text-xs text-info-600 fw-bold">SERT-XXXXX/{TIPE}-{ID}/05/<?= date('Y') ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('pageScripts') ?>
<script src="<?= base_url('assets/dashboard/js/begawi-batch-runner.js') ?>"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Handler Form Batch Kelas Pembelajaran
    const formLearning = document.getElementById('formBatchLearning');
    if (formLearning) {
        formLearning.addEventListener('submit', function (e) {
            e.preventDefault();
            const classSelect = document.getElementById('learningClassSelect');
            const templateSelect = document.getElementById('learningTemplateSelect');
            const classId = classSelect ? classSelect.value : '';
            const templateId = templateSelect ? templateSelect.value : '';

            if (!classId) {
                alert('Silakan pilih kelas pembelajaran terlebih dahulu.');
                return;
            }

            const selectedOpt = classSelect.options[classSelect.selectedIndex];
            const className = selectedOpt ? (selectedOpt.getAttribute('data-title') || selectedOpt.text) : 'Kelas';

            if (!confirm(`Mulai eksekusi batch sertifikat untuk kelas "${className}" secara bertahap (Stay-Alive)?`)) {
                return;
            }

            BegawiBatchRunner.start({
                title: 'Batch Kelas: ' + className,
                subtitle: 'Memproses penerbitan sertifikat peserta kelulusan (chunked stay-alive).',
                prepareUrl: '<?= site_url('dashboard/sertifikat/batch/learning') ?>',
                prepareData: { class_id: classId, template_id: templateId },
                chunkUrl: '<?= site_url('dashboard/sertifikat/batch/learning') ?>',
                chunkDataBuilder: (chunkIds) => ({
                    action: 'chunk',
                    class_id: classId,
                    template_id: templateId,
                    chunk_ids: chunkIds
                }),
                chunkSize: 5,
                onComplete: () => {
                    window.location.reload();
                }
            });
        });
    }

    // 2. Handler Form Batch Event & Agenda
    const formEvent = document.getElementById('formBatchEvent');
    if (formEvent) {
        formEvent.addEventListener('submit', function (e) {
            e.preventDefault();
            const eventSelect = document.getElementById('eventSelect');
            const templateSelect = document.getElementById('eventTemplateSelect');
            const eventId = eventSelect ? eventSelect.value : '';
            const templateId = templateSelect ? templateSelect.value : '';

            if (!eventId) {
                alert('Silakan pilih event / agenda terlebih dahulu.');
                return;
            }

            const selectedOpt = eventSelect.options[eventSelect.selectedIndex];
            const eventTitle = selectedOpt ? (selectedOpt.getAttribute('data-title') || selectedOpt.text) : 'Event';

            if (!confirm(`Mulai eksekusi batch sertifikat untuk event "${eventTitle}" secara bertahap (Stay-Alive)?`)) {
                return;
            }

            BegawiBatchRunner.start({
                title: 'Batch Event: ' + eventTitle,
                subtitle: 'Memproses penerbitan sertifikat peserta hadir (chunked stay-alive).',
                prepareUrl: '<?= site_url('dashboard/sertifikat/batch/event') ?>',
                prepareData: { event_id: eventId, template_id: templateId },
                chunkUrl: '<?= site_url('dashboard/sertifikat/batch/event') ?>',
                chunkDataBuilder: (chunkIds) => ({
                    action: 'chunk',
                    event_id: eventId,
                    template_id: templateId,
                    chunk_ids: chunkIds
                }),
                chunkSize: 5,
                onComplete: () => {
                    window.location.reload();
                }
            });
        });
    }
});
</script>
<?= $this->endSection() ?>
