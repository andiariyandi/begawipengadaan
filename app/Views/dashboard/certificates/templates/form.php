<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$isCreate = ($formMode ?? 'create') === 'create';
$t        = $template ?? [];
$payload  = $t['template_payload'] ?? [];
if (! is_array($payload)) {
    $decoded = json_decode((string) $payload, true);
    $payload = is_array($decoded) ? $decoded : [];
}

$bgAsset             = $backgroundAsset ?? null;
$placeholdersGrouped = $availablePlaceholders ?? \App\Services\CertificateTemplateRenderer::getAvailablePlaceholders();
$pdfPlaceholders     = $payload['pdf_placeholders'] ?? [];
if (! is_array($pdfPlaceholders)) {
    $pdfPlaceholders = [];
}

$pageWidthMm  = (float) ($payload['page_width_mm'] ?? 297.0);
$pageHeightMm = (float) ($payload['page_height_mm'] ?? 210.0);
$layoutMode   = (string) ($payload['layout_mode'] ?? 'pdf_placeholders');

$existingBgUrl = '';
$existingBgExt = '';
if (! empty($t['id']) && (! empty($payload['background_path']) || ! empty($bgAsset))) {
    $bgRel         = $payload['background_path'] ?? ($bgAsset['path'] ?? '');
    $existingBgExt = strtolower(pathinfo($bgRel, PATHINFO_EXTENSION));
    $existingBgUrl = site_url('dashboard/sertifikat/template/' . (int) $t['id'] . '/background-file');
}
?>

<!-- Breadcrumb & Header -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Template Sertifikat') ?></h6>
        <p class="text-secondary-light mb-0">
            <?= $isCreate
                ? 'Unggah template PDF yang berisi tag {{placeholder}} — sistem otomatis mendeteksi dan menggantinya dengan data peserta.'
                : 'Sesuaikan file template PDF, posisi penggantian placeholder, dan pejabat penanda tangan.' ?>
        </p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="<?= site_url('dashboard/sertifikat/template') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">
            <iconify-icon icon="solar:arrow-left-linear" class="me-1"></iconify-icon> Kembali
        </a>
        <button type="button" id="btnTopLivePdfPreview" class="btn btn-outline-danger-600 radius-8 px-16 py-11 d-flex align-items-center gap-1 fw-semibold">
            <iconify-icon icon="solar:file-download-bold"></iconify-icon> Pratinjau Hasil Generate PDF
        </button>
    </div>
</div>

<form method="post"
      id="certificateTemplateForm"
      action="<?= esc($isCreate
                  ? site_url('dashboard/sertifikat/template/simpan')
                  : site_url('dashboard/sertifikat/template/' . (int) ($t['id'] ?? 0) . '/ubah')) ?>"
      enctype="multipart/form-data"
      novalidate>
    <?= csrf_field() ?>
    <input type="hidden" name="template_id" id="templateIdInput" value="<?= (int) ($t['id'] ?? 0) ?>">
    <input type="hidden" name="payload[pdf_placeholders_json]" id="pdfPlaceholdersJsonInput" value="<?= esc(json_encode($pdfPlaceholders, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)) ?>">
    <input type="hidden" name="payload[page_width_mm]" id="pageWidthMmInput" value="<?= esc((string) $pageWidthMm) ?>">
    <input type="hidden" name="payload[page_height_mm]" id="pageHeightMmInput" value="<?= esc((string) $pageHeightMm) ?>">

    <div class="row gy-4">
        <!-- Kolom Kiri: Form Utama & Kanvas Penggantian Placeholder PDF -->
        <div class="col-xl-8">
            <!-- Card 1: Identitas & Sumber Template -->
            <div class="card mb-24">
                <div class="card-header border-bottom py-16 px-24 d-flex align-items-center justify-content-between">
                    <h6 class="text-lg fw-semibold mb-0 d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:diploma-bold" class="text-primary-600 text-xl"></iconify-icon>
                        1. Informasi &amp; Sumber Template
                    </h6>
                    <span class="badge bg-primary-50 text-primary-600 px-12 py-6 radius-4 text-xs fw-semibold">
                        Standar Format: SERT-{num}/{tipe}-{ID}/05/{YEAR}
                    </span>
                </div>
                <div class="card-body p-24">
                    <div class="row gy-20">
                        <div class="col-md-7">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                                Nama Template Sertifikat <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name" id="templateNameInput" class="form-control radius-8" required maxlength="255"
                                   placeholder="cth: Template Resmi Kelulusan Arena Belajar PBJ 2026"
                                   value="<?= esc(old('name', $t['name'] ?? '')) ?>">
                            <small class="text-secondary-light">Nama pengenal template untuk pengelolaan di dashboard.</small>
                        </div>

                        <div class="col-md-5">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                                Tipe Sumber Penerbitan <span class="text-danger">*</span>
                            </label>
                            <select name="source_type" id="sourceTypeSelect" class="form-select radius-8" required>
                                <?php $currentType = old('source_type', $t['source_type'] ?? 'learning'); ?>
                                <option value="learning" <?= $currentType === 'learning' ? 'selected' : '' ?>>
                                    Arena Belajar (Kelas / Pembelajaran) &rarr; Kode: AB
                                </option>
                                <option value="event" <?= $currentType === 'event' ? 'selected' : '' ?>>
                                    Event / Agenda (Webinar, Bimtek, Workshop, dll) &rarr; Kode: WB, BT, dll
                                </option>
                            </select>
                            <small class="text-secondary-light">Menentukan format singkatan penomoran otomatis.</small>
                        </div>

                        <!-- SECTION A: LINGKUP & ANTI-TUMPANG-TINDIH ARENA PEMBELAJARAN -->
                        <div class="col-12" id="learningScopeSection" style="<?= $currentType === 'learning' ? '' : 'display:none;' ?>">
                            <div class="p-16 radius-8 border bg-neutral-50">
                                <label class="form-label fw-semibold text-primary-light text-sm mb-8 d-flex align-items-center justify-content-between">
                                    <span>
                                        <iconify-icon icon="solar:layers-minimalistic-bold" class="text-primary-600 me-1"></iconify-icon>
                                        Tingkat Sasaran Template Pembelajaran:
                                    </span>
                                    <span class="badge bg-warning-50 text-warning-700 px-8 py-3 radius-4 text-xs fw-semibold">
                                        Aturan Anti-Tumpang-Tindih Aktif
                                    </span>
                                </label>
                                <?php
                                $currentScope = old('payload.target_scope', $payload['target_scope'] ?? 'global');
                                $selProgId    = (int) old('payload.program_id', $payload['program_id'] ?? 0);
                                $selCourseId  = (int) old('payload.course_id', $payload['course_id'] ?? 0);
                                $selClassId   = (int) old('payload.class_id', $payload['class_id'] ?? 0);
                                ?>
                                <div class="row g-2 mb-16">
                                    <div class="col-sm-6 col-md-3">
                                        <div class="form-check p-12 radius-8 border bg-white h-100">
                                            <input class="form-check-input" type="radio" name="payload[target_scope]" id="scopeProgram" value="program" <?= $currentScope === 'program' ? 'checked' : '' ?>>
                                            <label class="form-check-label text-xs fw-bold cursor-pointer d-block" for="scopeProgram">
                                                Tingkat Program
                                                <small class="d-block text-secondary-light fw-normal mt-1">Mengunci seluruh course &amp; kelas di bawahnya</small>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-3">
                                        <div class="form-check p-12 radius-8 border bg-white h-100">
                                            <input class="form-check-input" type="radio" name="payload[target_scope]" id="scopeCourse" value="course" <?= $currentScope === 'course' ? 'checked' : '' ?>>
                                            <label class="form-check-label text-xs fw-bold cursor-pointer d-block" for="scopeCourse">
                                                Tingkat Course
                                                <small class="d-block text-secondary-light fw-normal mt-1">Mengunci seluruh kelas di bawah course ini</small>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-3">
                                        <div class="form-check p-12 radius-8 border bg-white h-100">
                                            <input class="form-check-input" type="radio" name="payload[target_scope]" id="scopeClass" value="class" <?= $currentScope === 'class' ? 'checked' : '' ?>>
                                            <label class="form-check-label text-xs fw-bold cursor-pointer d-block" for="scopeClass">
                                                Khusus 1 Kelas
                                                <small class="d-block text-secondary-light fw-normal mt-1">Hanya jika Program &amp; Course kosong</small>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-3">
                                        <div class="form-check p-12 radius-8 border bg-white h-100">
                                            <input class="form-check-input" type="radio" name="payload[target_scope]" id="scopeGlobal" value="global" <?= $currentScope === 'global' ? 'checked' : '' ?>>
                                            <label class="form-check-label text-xs fw-bold cursor-pointer d-block" for="scopeGlobal">
                                                Standar / Global
                                                <small class="d-block text-secondary-light fw-normal mt-1">Default arena belajar (AB)</small>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Dynamic Dropdown: Program -->
                                <div class="mb-12 scope-field" id="fieldProgram" style="<?= $currentScope === 'program' ? '' : 'display:none;' ?>">
                                    <label class="form-label text-xs fw-semibold text-primary-light mb-4">Pilih Program Pembelajaran <span class="text-danger">*</span></label>
                                    <select name="payload[program_id]" id="selectProgram" class="form-select radius-8 text-sm">
                                        <option value="">-- Pilih Program PBJ --</option>
                                        <?php foreach (($hierarchyOptions['programs'] ?? []) as $prog): ?>
                                            <?php
                                            $isLocked = isset($hierarchyOptions['lockInfo']['locked_programs'][(int) $prog['id']]);
                                            $lockText = $isLocked ? ' (Sudah Memiliki Template: ' . esc($hierarchyOptions['lockInfo']['locked_programs'][(int) $prog['id']]['template_name']) . ')' : '';
                                            ?>
                                            <option value="<?= (int) $prog['id'] ?>" <?= $selProgId === (int) $prog['id'] ? 'selected' : '' ?> <?= ($isLocked && $selProgId !== (int) $prog['id']) ? 'disabled class="text-danger"' : '' ?>>
                                                <?= esc($prog['title']) ?><?= $lockText ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="alert alert-info py-8 px-12 radius-8 text-xs mt-6 mb-0 d-flex align-items-center gap-2">
                                        <iconify-icon icon="solar:lock-keyhole-bold" class="text-base text-info"></iconify-icon>
                                        <span>Course dan seluruh kelas di bawah program ini akan otomatis <strong>dikunci</strong> dan memakai template sertifikat program ini.</span>
                                    </div>
                                </div>

                                <!-- Dynamic Dropdown: Course -->
                                <div class="mb-12 scope-field" id="fieldCourse" style="<?= $currentScope === 'course' ? '' : 'display:none;' ?>">
                                    <label class="form-label text-xs fw-semibold text-primary-light mb-4">Pilih Course PBJ <span class="text-danger">*</span></label>
                                    <select name="payload[course_id]" id="selectCourse" class="form-select radius-8 text-sm">
                                        <option value="">-- Pilih Course PBJ --</option>
                                        <?php foreach (($hierarchyOptions['courses'] ?? []) as $co): ?>
                                            <?php
                                            $cId            = (int) $co['id'];
                                            $isLockedByProg = isset($hierarchyOptions['lockInfo']['course_locks'][$cId]) && $hierarchyOptions['lockInfo']['course_locks'][$cId]['locked_by'] === 'program';
                                            $isLockedSelf   = isset($hierarchyOptions['lockInfo']['locked_courses'][$cId]);
                                            $lockLabel      = '';
                                            if ($isLockedByProg) {
                                                $lockLabel = ' [Terkunci: Program memiliki template aktif]';
                                            } elseif ($isLockedSelf && $selCourseId !== $cId) {
                                                $lockLabel = ' [Sudah Memiliki Template: ' . esc($hierarchyOptions['lockInfo']['locked_courses'][$cId]['template_name']) . ']';
                                            }
                                            $shouldDisable = ($isLockedByProg || ($isLockedSelf && $selCourseId !== $cId));
                                            ?>
                                            <option value="<?= $cId ?>" data-program-id="<?= (int) ($co['program_id'] ?? 0) ?>" <?= $selCourseId === $cId ? 'selected' : '' ?> <?= $shouldDisable ? 'disabled class="text-danger"' : '' ?>>
                                                <?= esc($co['title']) ?><?= $lockLabel ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="alert alert-primary py-8 px-12 radius-8 text-xs mt-6 mb-0 d-flex align-items-center gap-2">
                                        <iconify-icon icon="solar:shield-check-bold" class="text-base text-primary"></iconify-icon>
                                        <span>Seluruh kelas di bawah course ini akan otomatis <strong>dikunci</strong> dan memakai template ini. Hanya dapat diset jika Program induknya belum diset template.</span>
                                    </div>
                                </div>

                                <!-- Dynamic Dropdown: Class -->
                                <div class="mb-12 scope-field" id="fieldClass" style="<?= $currentScope === 'class' ? '' : 'display:none;' ?>">
                                    <label class="form-label text-xs fw-semibold text-primary-light mb-4">Pilih Kelas Pembelajaran Khusus <span class="text-danger">*</span></label>
                                    <select name="payload[class_id]" id="selectClass" class="form-select radius-8 text-sm">
                                        <option value="">-- Pilih Kelas Pembelajaran --</option>
                                        <?php foreach (($hierarchyOptions['classes'] ?? []) as $cl): ?>
                                            <?php
                                            $clId       = (int) $cl['id'];
                                            $lockData   = $hierarchyOptions['lockInfo']['class_locks'][$clId] ?? null;
                                            $isLocked   = false;
                                            $lockReason = '';
                                            if ($lockData) {
                                                if ($lockData['locked_by'] === 'program') {
                                                    $isLocked   = true;
                                                    $lockReason = ' [Terkunci oleh Template Program]';
                                                } elseif ($lockData['locked_by'] === 'course') {
                                                    $isLocked   = true;
                                                    $lockReason = ' [Terkunci oleh Template Course]';
                                                } elseif ($lockData['locked_by'] === 'class' && $selClassId !== $clId) {
                                                    $isLocked   = true;
                                                    $lockReason = ' [Sudah Memiliki Template: ' . esc($lockData['template_name']) . ']';
                                                }
                                            }
                                            ?>
                                            <option value="<?= $clId ?>" <?= $selClassId === $clId ? 'selected' : '' ?> <?= $isLocked ? 'disabled class="text-danger"' : '' ?>>
                                                <?= esc($cl['name']) ?> (Course: <?= esc($cl['course_title'] ?? '-') ?>)<?= $lockReason ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="alert alert-success py-8 px-12 radius-8 text-xs mt-6 mb-0 d-flex align-items-center gap-2">
                                        <iconify-icon icon="solar:check-circle-bold" class="text-base text-success"></iconify-icon>
                                        <span>Kelas terbuka untuk diset template khusus karena Program dan Course induknya belum memiliki template (kosong).</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SECTION B: TARGET KHUSUS EVENT & AGENDA (1:1 UNIK) -->
                        <div class="col-12" id="eventScopeSection" style="<?= $currentType === 'event' ? '' : 'display:none;' ?>">
                            <div class="p-16 radius-8 border bg-info-50 border-info-200">
                                <label class="form-label fw-semibold text-primary-light text-sm mb-8 d-flex align-items-center justify-content-between">
                                    <span>
                                        <iconify-icon icon="solar:calendar-bold" class="text-info-600 me-1"></iconify-icon>
                                        Pilih Event / Agenda PBJ Terkait <span class="text-danger">*</span>:
                                    </span>
                                    <span class="badge bg-info-600 text-white px-8 py-3 radius-4 text-xs fw-bold">
                                        Wajib Unik 1:1 Tiap Acara
                                    </span>
                                </label>
                                <?php $selEventId = (int) old('payload.event_id', $payload['event_id'] ?? ($selectedEvent['id'] ?? 0)); ?>
                                <select name="payload[event_id]" id="selectEvent" class="form-select radius-8 text-sm bg-white">
                                    <option value="">-- Pilih Event / Agenda --</option>
                                    <?php foreach (($hierarchyOptions['events'] ?? []) as $ev): ?>
                                        <?php
                                        $evId            = (int) $ev['id'];
                                        $assignedTpl     = $hierarchyOptions['assignedEvents'][$evId] ?? null;
                                        $isAssignedOther = ($assignedTpl && $assignedTpl['template_id'] !== (int) ($t['id'] ?? 0));
                                        $evNote          = $isAssignedOther ? ' [Sudah Memiliki Template: ' . esc($assignedTpl['template_name']) . ']' : '';
                                        ?>
                                        <option value="<?= $evId ?>" data-title="<?= esc($ev['title']) ?>" <?= $selEventId === $evId ? 'selected' : '' ?> <?= $isAssignedOther ? 'disabled class="text-danger"' : '' ?>>
                                            <?= esc($ev['title']) ?> (Tipe: <?= strtoupper(esc($ev['type'] ?? 'EVENT')) ?>, <?= esc(date('d M Y', strtotime($ev['start_at']))) ?>)<?= $evNote ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <p class="text-xs text-neutral-600 mt-8 mb-0">
                                    <iconify-icon icon="solar:info-circle-bold" class="text-info-600 me-1"></iconify-icon>
                                    Template sertifikat untuk event dan agenda selalu <strong>berbeda tiap acara</strong> dan tidak bisa dibuat sama antar acara.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Unggah File PDF Template Sertifikat (Auto-Detect Placeholder) -->
            <div class="card mb-24 border-primary-200">
                <div class="card-header border-bottom py-16 px-24 d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <h6 class="text-lg fw-semibold mb-0 d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:file-check-bold" class="text-primary-600 text-xl"></iconify-icon>
                        2. Unggah File PDF Template Sertifikat (Deteksi Otomatis Placeholder)
                    </h6>
                    <span class="badge bg-success-50 text-success-700 border border-success-200 px-10 py-5 radius-6 text-xs fw-bold">
                        Tanpa Perlu Koding HTML
                    </span>
                </div>
                <div class="card-body p-24">
                    <div class="alert alert-primary-50 border border-primary-200 p-14 radius-8 mb-16 text-xs text-primary-900">
                        <div class="d-flex align-items-start gap-2">
                            <iconify-icon icon="solar:magic-stick-3-bold" class="text-lg text-primary-600 flex-shrink-0 mt-1"></iconify-icon>
                            <div>
                                <strong>Cara Kerja Penggantian Placeholder di Dalam PDF:</strong>
                                <ol class="mb-0 mt-4 ps-16" style="line-height: 1.6;">
                                    <li>Buat desain sertifikat di <strong>Canva, Word, PowerPoint, atau Illustrator</strong> dan ketik langsung teks placeholder seperti <code>{{nomor_sertifikat}}</code>, <code>{{nama_peserta}}</code>, <code>{{nip}}</code>, <code>{{instansi}}</code>, <code>{{judul_kegiatan}}</code>, <code>{{tanggal_terbit}}</code>, atau <code>{{qr_code}}</code> pada posisi yang diinginkan.</li>
                                    <li>Simpan/ekspor desain sebagai file <strong>PDF</strong> lalu unggah di bawah ini.</li>
                                    <li>Sistem akan <strong>otomatis membaca &amp; mendeteksi</strong> semua tag <code>{{...}}</code> di dalam PDF Anda, menghapus teks <code>{{...}}</code> saat sertifikat diterbitkan, dan menggantinya dengan data asli peserta!</li>
                                </ol>
                            </div>
                        </div>
                    </div>

                    <?php if (! empty($payload['background_path']) || ! empty($bgAsset)): ?>
                        <div class="p-16 radius-8 bg-neutral-50 border mb-16">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="radius-8 overflow-hidden border bg-white shadow-xs d-flex align-items-center justify-content-center" style="width: 72px; height: 54px;">
                                        <?php
                                        $bgRel = $payload['background_path'] ?? ($bgAsset['path'] ?? '');
                                        $ext   = strtolower(pathinfo($bgRel, PATHINFO_EXTENSION));
                                        ?>
                                        <?php if ($ext === 'pdf'): ?>
                                            <iconify-icon icon="solar:file-check-bold" class="text-3xl text-primary-600"></iconify-icon>
                                        <?php else: ?>
                                            <img src="<?= esc($existingBgUrl) ?>" alt="Background" class="w-100 h-100 object-fit-cover">
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <h6 class="text-sm fw-semibold mb-1 text-primary-light">
                                            <?= esc($payload['background_original_name'] ?? ($bgAsset['original_name'] ?? basename($bgRel))) ?>
                                        </h6>
                                        <span class="badge bg-primary-50 text-primary-700 border border-primary-200 px-8 py-3 radius-4 text-xs fw-semibold">
                                            <?= $ext === 'pdf' ? 'File Template PDF Terpasang' : 'Background Gambar Terpasang' ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input text-danger" type="checkbox" name="remove_background" value="1" id="removeBgCheck">
                                    <label class="form-check-label text-xs text-danger fw-semibold cursor-pointer" for="removeBgCheck">
                                        Hapus file ini
                                    </label>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="row g-3 align-items-end">
                        <div class="col-md-9">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                                <?= (! empty($payload['background_path']) || ! empty($bgAsset)) ? 'Ganti File PDF / Gambar Template (Opsional)' : 'Pilih File PDF Template Sertifikat' ?>
                            </label>
                            <input type="file" name="background_file" id="backgroundFileInput" class="form-control radius-8"
                                   accept=".pdf,.png,.jpg,.jpeg,application/pdf,image/png,image/jpeg">
                        </div>
                        <div class="col-md-3">
                            <button type="button" id="btnRescanPdf" class="btn btn-outline-primary-600 radius-8 w-100 py-10 text-xs fw-semibold d-flex align-items-center justify-content-center gap-1">
                                <iconify-icon icon="solar:scanner-bold" class="text-base"></iconify-icon>
                                Deteksi Placeholder PDF
                            </button>
                        </div>
                    </div>

                    <!-- Status hasil deteksi placeholder dari PDF -->
                    <div id="pdfScanStatusBanner" class="mt-12 d-none"></div>
                </div>
            </div>

            <!-- Card 3: Pejabat Penanda Tangan & Penerbit -->
            <div class="card mb-24">
                <div class="card-header border-bottom py-16 px-24">
                    <h6 class="text-lg fw-semibold mb-0 d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:pen-bold" class="text-primary-600 text-xl"></iconify-icon>
                        3. Pengesahan &amp; Pejabat Penanda Tangan
                    </h6>
                </div>
                <div class="card-body p-24">
                    <div class="row gy-16">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-6">Nama Instansi Penerbit (<code>{{penerbit}}</code>)</label>
                            <input type="text" name="payload[issuer_name]" id="issuerNameInput" class="form-control radius-8"
                                   placeholder="Pemerintah Provinsi Lampung — Biro Pengadaan Barang dan Jasa"
                                   value="<?= esc(old('payload.issuer_name', $payload['issuer_name'] ?? 'Pemerintah Provinsi Lampung — Biro Pengadaan Barang dan Jasa')) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-6">Status Template</label>
                            <select name="is_active" class="form-select radius-8">
                                <?php $currentActive = (string) (int) old('is_active', $t['is_active'] ?? 1); ?>
                                <option value="1" <?= $currentActive === '1' ? 'selected' : '' ?>>Aktif (Dipakai Penerbitan Sertifikat)</option>
                                <option value="0" <?= $currentActive === '0' ? 'selected' : '' ?>>Nonaktif (Draft)</option>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-6">Nama Pejabat (<code>{{penandatangan_nama}}</code>)</label>
                            <input type="text" name="payload[signer_name]" id="signerNameInput" class="form-control radius-8"
                                   placeholder="Dr. H. Rahmad Hidayat, S.E., M.Si."
                                   value="<?= esc(old('payload.signer_name', $payload['signer_name'] ?? 'Dr. H. Rahmad Hidayat, S.E., M.Si.')) ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-6">Jabatan Pejabat (<code>{{penandatangan_jabatan}}</code>)</label>
                            <input type="text" name="payload[signer_position]" id="signerPositionInput" class="form-control radius-8"
                                   placeholder="Kepala Biro Pengadaan Barang dan Jasa"
                                   value="<?= esc(old('payload.signer_position', $payload['signer_position'] ?? 'Kepala Biro Pengadaan Barang dan Jasa')) ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-6">NIP Pejabat (<code>{{penandatangan_nip}}</code>)</label>
                            <input type="text" name="payload[signer_nip]" id="signerNipInput" class="form-control radius-8"
                                   placeholder="19750812 199903 1 004"
                                   value="<?= esc(old('payload.signer_nip', $payload['signer_nip'] ?? '19750812 199903 1 004')) ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 4: Kanvas Visual & Pengaturan Placeholder di Dalam PDF -->
            <div class="card mb-24">
                <div class="card-header border-bottom py-16 px-24 d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <h6 class="text-lg fw-semibold mb-0 d-flex align-items-center gap-2">
                            <iconify-icon icon="solar:layers-bold" class="text-primary-600 text-xl"></iconify-icon>
                            4. Pratinjau &amp; Penataan Placeholder dalam PDF
                        </h6>
                        <small class="text-secondary-light">
                            Geser (drag &amp; drop) kotak placeholder di atas PDF untuk menyesuaikan posisi, atau klik placeholder di kanan untuk menambahkan.
                        </small>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <!-- Mode Tampilan Kanvas: Tag vs Data Sampel -->
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-outline-primary-600 px-12 py-6 text-xs fw-semibold" id="btnViewModeSample">
                                <iconify-icon icon="solar:eye-bold" class="me-1"></iconify-icon> Pratinjau Data Asli
                            </button>
                            <button type="button" class="btn btn-outline-neutral-300 px-12 py-6 text-xs fw-semibold text-neutral-700" id="btnViewModeTags">
                                <iconify-icon icon="solar:code-bold" class="me-1"></iconify-icon> Tampilkan Tag {{...}}
                            </button>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-neutral-300 px-10 py-6 text-xs fw-semibold text-neutral-700" id="btnAddDefaultPlaceholders" title="Tambahkan paket placeholder standar sertifikat ke kanvas">
                            <iconify-icon icon="solar:magic-stick-3-bold" class="me-1"></iconify-icon> Muat Preset Placeholder
                        </button>
                    </div>
                </div>

                <div class="card-body p-24">
                    <!-- Area Kanvas Pratinjau PDF Interaktif -->
                    <div class="p-16 radius-12 bg-neutral-100 border mb-20">
                        <div class="d-flex flex-wrap justify-content-between align-items-center mb-10 gap-2">
                            <span class="text-xs fw-semibold text-neutral-700 d-flex align-items-center gap-1">
                                <iconify-icon icon="solar:document-text-bold" class="text-primary-600"></iconify-icon>
                                <span id="canvasDimensionBadge">Kanvas Sertifikat: <?= esc((string) $pageWidthMm) ?> &times; <?= esc((string) $pageHeightMm) ?> mm</span>
                            </span>
                            <span class="badge bg-white text-neutral-700 border px-10 py-4 radius-4 text-xs" id="activePlaceholderCountBadge">
                                0 Placeholder Terpasang
                            </span>
                        </div>

                        <!-- Wrapper Proporsional A4 Landscape (atau sesuai rasio PDF) -->
                        <div id="pdfStageOuter" class="mx-auto position-relative shadow-sm border bg-white overflow-hidden"
                             style="width: 100%; max-width: 840px; aspect-ratio: <?= esc((string) $pageWidthMm) ?> / <?= esc((string) $pageHeightMm) ?>; user-select: none;">

                            <!-- Canvas tempat PDF.js merender halaman 1 PDF -->
                            <canvas id="pdfRenderCanvas" class="w-100 h-100 d-block" style="position:absolute;top:0;left:0;width:100%;height:100%;z-index:1;"></canvas>

                            <!-- Gambar fallback jika yang diunggah adalah PNG/JPG -->
                            <img id="imageRenderStage" src="<?= ($existingBgExt !== 'pdf' && $existingBgUrl !== '') ? esc($existingBgUrl) : '' ?>"
                                 alt="Template Background"
                                 class="<?= ($existingBgExt !== 'pdf' && $existingBgUrl !== '') ? '' : 'd-none' ?>"
                                 style="position:absolute;top:0;left:0;width:100%;height:100%;object-fit:fill;z-index:1;">

                            <!-- Empty State jika belum ada file PDF yang dipilih -->
                            <div id="pdfEmptyStateOverlay" class="<?= $existingBgUrl !== '' ? 'd-none' : 'd-flex' ?> flex-column align-items-center justify-content-center text-center p-24"
                                 style="position:absolute;top:0;left:0;width:100%;height:100%;z-index:1;pointer-events:none;">
                                <iconify-icon icon="solar:file-send-bold-duotone" class="text-5xl text-neutral-300 mb-2"></iconify-icon>
                                <p class="text-sm fw-semibold text-neutral-500 mb-1">Unggah File PDF Template Sertifikat pada Bagian 2 di Atas</p>
                                <p class="text-xs text-neutral-400 mb-0">Pratinjau halaman PDF beserta titik-titik penggantian <code>{{placeholder}}</code> akan tampil secara otomatis di kanvas ini.</p>
                            </div>

                            <!-- Layer Masking Penutup Teks {{placeholder}} Asli pada Pratinjau Kanvas -->
                            <div id="pdfMaskLayer" style="position:absolute;top:0;left:0;width:100%;height:100%;z-index:2;pointer-events:none;"></div>

                            <!-- Layer Interaktif Drag & Drop Placeholder -->
                            <div id="pdfInteractiveOverlay" style="position:absolute;top:0;left:0;width:100%;height:100%;z-index:3;"></div>
                        </div>
                    </div>

                    <!-- Panel Inspector Placeholder Terpilih -->
                    <div id="placeholderInspectorPanel" class="p-16 radius-8 border bg-primary-50 border-primary-200 mb-20 d-none">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-12 pb-8 border-bottom border-primary-200">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-primary-600 text-white px-8 py-4 radius-4 text-xs font-monospace" id="inspSelectedTag">{{nama_peserta}}</span>
                                <span class="text-xs fw-bold text-primary-900" id="inspSelectedLabel">Pengaturan Placeholder Terpilih</span>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-danger-600 radius-6 px-10 py-4 text-xs d-flex align-items-center gap-1" id="btnDeleteSelectedPh">
                                <iconify-icon icon="solar:trash-bin-trash-bold"></iconify-icon> Hapus Placeholder Ini
                            </button>
                        </div>

                        <div class="row g-3 align-items-end">
                            <div class="col-6 col-sm-2">
                                <label class="form-label text-xs fw-semibold text-neutral-700 mb-4">Posisi X (%)</label>
                                <input type="number" step="0.5" min="0" max="100" id="inspXPct" class="form-control form-control-sm radius-6">
                            </div>
                            <div class="col-6 col-sm-2">
                                <label class="form-label text-xs fw-semibold text-neutral-700 mb-4">Posisi Y (%)</label>
                                <input type="number" step="0.5" min="0" max="100" id="inspYPct" class="form-control form-control-sm radius-6">
                            </div>
                            <div class="col-6 col-sm-2" id="inspFontSizeGroup">
                                <label class="form-label text-xs fw-semibold text-neutral-700 mb-4">Ukuran Font (pt)</label>
                                <input type="number" step="1" min="6" max="80" id="inspFontSize" class="form-control form-control-sm radius-6">
                            </div>
                            <div class="col-6 col-sm-2 d-none" id="inspQrSizeGroup">
                                <label class="form-label text-xs fw-semibold text-neutral-700 mb-4">Ukuran QR (px)</label>
                                <input type="number" step="5" min="40" max="280" id="inspQrSize" class="form-control form-control-sm radius-6">
                            </div>
                            <div class="col-6 col-sm-2" id="inspWeightGroup">
                                <label class="form-label text-xs fw-semibold text-neutral-700 mb-4">Ketebalan</label>
                                <select id="inspFontWeight" class="form-select form-select-sm radius-6">
                                    <option value="bold">Tebal (Bold)</option>
                                    <option value="normal">Normal</option>
                                </select>
                            </div>
                            <div class="col-6 col-sm-2">
                                <label class="form-label text-xs fw-semibold text-neutral-700 mb-4">Perataan Teks</label>
                                <select id="inspAlign" class="form-select form-select-sm radius-6">
                                    <option value="center">Rata Tengah</option>
                                    <option value="left">Rata Kiri</option>
                                    <option value="right">Rata Kanan</option>
                                </select>
                            </div>
                            <div class="col-6 col-sm-2" id="inspColorGroup">
                                <label class="form-label text-xs fw-semibold text-neutral-700 mb-4">Warna Teks</label>
                                <input type="color" id="inspColor" class="form-control form-control-sm form-control-color w-100 radius-6" value="#0f172a">
                            </div>
                            <div class="col-12 pt-4">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                                    <div class="form-check d-flex align-items-center gap-2 mb-0">
                                        <input class="form-check-input" type="checkbox" id="inspMaskBg">
                                        <label class="form-check-label text-xs text-neutral-700 cursor-pointer" for="inspMaskBg">
                                            Tutup area teks asli di PDF dengan kotak warna latar (aktifkan bila teks placeholder di PDF berupa gambar/outline)
                                        </label>
                                    </div>
                                    <div class="d-flex align-items-center gap-2" id="inspMaskColorGroup">
                                        <span class="text-xs text-neutral-600">Warna Penutup:</span>
                                        <input type="color" id="inspMaskColor" class="form-control form-control-sm form-control-color radius-4" style="width:42px;height:26px;" value="#ffffff">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabel Ringkasan Placeholder yang Akan Diganti di PDF -->
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-8">
                            <h6 class="text-sm fw-bold mb-0 text-neutral-800">
                                Daftar Placeholder Aktif yang Akan Diganti pada PDF:
                            </h6>
                            <button type="button" class="btn btn-xs btn-outline-danger-600 radius-4 px-8 py-4 text-xs" id="btnClearAllPlaceholders">
                                Kosongkan Semua
                            </button>
                        </div>
                        <div class="table-responsive border radius-8">
                            <table class="table table-sm table-hover align-middle mb-0 text-xs">
                                <thead class="bg-neutral-50">
                                    <tr>
                                        <th class="py-8 px-12">Tag Placeholder</th>
                                        <th class="py-8 px-12">Contoh Nilai Pengganti</th>
                                        <th class="py-8 px-12 text-center">Posisi (X, Y)</th>
                                        <th class="py-8 px-12 text-center">Gaya / Ukuran</th>
                                        <th class="py-8 px-12 text-center">Perataan</th>
                                        <th class="py-8 px-12 text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="detectedPlaceholdersTbody">
                                    <tr>
                                        <td colspan="6" class="text-center py-16 text-secondary-light">
                                            Belum ada placeholder. Unggah file PDF berisi tag <code>{{...}}</code> atau klik daftar placeholder di panel kanan.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Accordion Opsi Lanjutan: Mode Tata Letak & HTML Kustom -->
                    <div class="mt-20 pt-16 border-top">
                        <a class="text-xs fw-semibold text-secondary-light text-decoration-none d-inline-flex align-items-center gap-1"
                           data-bs-toggle="collapse" href="#advancedHtmlCollapse" role="button" aria-expanded="false">
                            <iconify-icon icon="solar:settings-bold"></iconify-icon>
                            Opsi Lanjutan: Mode Render &amp; Editor Body HTML Manual (Opsional)
                        </a>
                        <div class="collapse mt-12" id="advancedHtmlCollapse">
                            <div class="p-16 radius-8 bg-neutral-50 border">
                                <div class="row gy-16">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-primary-light text-xs mb-4">Mode Penggantian Template</label>
                                        <select name="payload[layout_mode]" id="layoutModeSelect" class="form-select form-select-sm radius-8">
                                            <option value="pdf_placeholders" <?= $layoutMode === 'pdf_placeholders' ? 'selected' : '' ?>>
                                                Ganti Placeholder Langsung pada Koordinat PDF (Direkomendasikan)
                                            </option>
                                            <option value="auto_overlay" <?= $layoutMode === 'auto_overlay' ? 'selected' : '' ?>>
                                                Overlay Bawaan Sistem di Atas Background Gambar
                                            </option>
                                            <option value="custom_html" <?= $layoutMode === 'custom_html' ? 'selected' : '' ?>>
                                                Gunakan Koding Body HTML Manual di Bawah
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-primary-light text-xs mb-4">Posisi QR Code Fallback (Mode HTML Manual)</label>
                                        <select name="payload[qr_position]" class="form-select form-select-sm radius-8">
                                            <?php $currentQrPos = old('payload.qr_position', $payload['qr_position'] ?? 'right'); ?>
                                            <option value="right" <?= $currentQrPos === 'right' ? 'selected' : '' ?>>Kanan Bawah (Default)</option>
                                            <option value="bottom" <?= $currentQrPos === 'bottom' ? 'selected' : '' ?>>Tengah Bawah</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold text-primary-light text-xs mb-4">Body HTML Manual (Hanya dipakai jika memilih Mode HTML Manual)</label>
                                        <textarea name="payload[body_html]" id="bodyHtmlEditor" class="form-control font-monospace radius-8" rows="5"
                                                  style="font-size: 12px; background: #fff;"
                                                  placeholder="Kosongkan bila menggunakan penggantian placeholder langsung di dalam PDF..."><?= esc(old('payload.body_html', $payload['body_html'] ?? '')) ?></textarea>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold text-primary-light text-xs mb-4">CSS Tambahan Kustom (Opsional)</label>
                                        <textarea name="payload[css]" class="form-control font-monospace radius-8" rows="2"
                                                  style="font-size: 12px; background: #fff;"
                                                  placeholder=".cert-ph-text { font-family: 'Times New Roman', serif; }"><?= esc(old('payload.css', $payload['css'] ?? '')) ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button Bar -->
            <div class="card">
                <div class="card-body p-20 d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <a href="<?= site_url('dashboard/sertifikat/template') ?>" class="btn btn-outline-neutral-300 radius-8 px-24 py-11">
                        Batal
                    </a>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" id="btnBottomLivePdfPreview" class="btn btn-outline-danger-600 radius-8 px-20 py-11 fw-semibold d-flex align-items-center gap-2">
                            <iconify-icon icon="solar:file-download-bold" class="text-lg"></iconify-icon>
                            Uji Generate PDF Sekarang
                        </button>
                        <button type="submit" class="btn btn-primary-600 radius-8 px-28 py-11 fw-semibold d-flex align-items-center gap-2">
                            <iconify-icon icon="solar:disk-bold" class="text-lg"></iconify-icon>
                            <?= $isCreate ? 'Simpan &amp; Aktifkan Template' : 'Perbarui Perubahan Template' ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Palet Placeholder Resmi & Panduan PDF -->
        <div class="col-xl-4">
            <!-- Card Palet Placeholder Interaktif (Klik untuk Tambah ke PDF / Salin) -->
            <div class="card mb-24 sticky-top" style="top: 80px; z-index: 5;">
                <div class="card-header border-bottom py-16 px-20 d-flex align-items-center justify-content-between">
                    <h6 class="text-md fw-bold mb-0 d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:tag-bold" class="text-primary-600 text-lg"></iconify-icon>
                        Daftar Placeholder Resmi
                    </h6>
                    <span class="badge bg-primary-50 text-primary-700 px-8 py-4 radius-4 text-xs fw-semibold">Klik: Salin &amp; Tambah ke PDF</span>
                </div>
                <div class="card-body p-20" style="max-height: 620px; overflow-y: auto;">
                    <p class="text-xs text-secondary-light mb-12">
                        Ketik tag di bawah ini di dalam desain PDF Anda (Word / Canva), atau <strong>klik langsung</strong> untuk menyalin sekaligus menambahkannya ke kanvas PDF di samping.
                    </p>

                    <div id="copyToast" class="alert alert-success py-8 px-12 radius-8 text-xs mb-12 d-none align-items-center gap-2">
                        <iconify-icon icon="solar:check-circle-bold" class="text-base"></iconify-icon>
                        <span id="copyToastText">Placeholder berhasil ditambahkan!</span>
                    </div>

                    <?php foreach ($placeholdersGrouped as $groupName => $tags): ?>
                        <div class="mb-16">
                            <h6 class="text-xs fw-bold text-primary-light text-uppercase tracking-wider mb-8 border-bottom pb-4">
                                <?= esc($groupName) ?>
                            </h6>
                            <div class="d-flex flex-column gap-6">
                                <?php foreach ($tags as $item): ?>
                                    <div class="placeholder-item d-flex align-items-center justify-content-between p-8 radius-6 border bg-white hover-bg-neutral-50 cursor-pointer"
                                         data-tag="<?= esc($item['tag']) ?>"
                                         data-label="<?= esc($item['label']) ?>"
                                         data-example="<?= esc($item['example']) ?>"
                                         title="Klik untuk menyalin &amp; menambahkan <?= esc($item['tag']) ?> ke kanvas PDF">
                                        <div>
                                            <code class="fw-bold text-primary-600 text-xs"><?= esc($item['tag']) ?></code>
                                            <div class="text-xs text-secondary-light mt-1"><?= esc($item['label']) ?></div>
                                            <div class="text-xs text-neutral-400 fst-italic">Contoh: <?= esc($item['example']) ?></div>
                                        </div>
                                        <div class="d-flex gap-1 flex-shrink-0 ms-2">
                                            <span class="btn btn-sm btn-icon btn-outline-primary-600 radius-4 p-4" title="Tambah ke Kanvas PDF">
                                                <iconify-icon icon="solar:add-circle-bold" class="text-sm"></iconify-icon>
                                            </span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- PDF.js Library untuk Render Halaman PDF & Deteksi Teks Placeholder Presisi di Browser -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (window.pdfjsLib) {
        window.pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
    }

    const form = document.getElementById('certificateTemplateForm');
    const fileInput = document.getElementById('backgroundFileInput');
    const btnRescanPdf = document.getElementById('btnRescanPdf');
    const scanBanner = document.getElementById('pdfScanStatusBanner');

    const pdfStageOuter = document.getElementById('pdfStageOuter');
    const pdfCanvas = document.getElementById('pdfRenderCanvas');
    const imageRenderStage = document.getElementById('imageRenderStage');
    const emptyStateOverlay = document.getElementById('pdfEmptyStateOverlay');
    const pdfMaskLayer = document.getElementById('pdfMaskLayer');
    const pdfInteractiveOverlay = document.getElementById('pdfInteractiveOverlay');
    const canvasDimensionBadge = document.getElementById('canvasDimensionBadge');
    const activeCountBadge = document.getElementById('activePlaceholderCountBadge');

    const jsonInput = document.getElementById('pdfPlaceholdersJsonInput');
    const pageWidthMmInput = document.getElementById('pageWidthMmInput');
    const pageHeightMmInput = document.getElementById('pageHeightMmInput');

    const inspectorPanel = document.getElementById('placeholderInspectorPanel');
    const inspSelectedTag = document.getElementById('inspSelectedTag');
    const inspSelectedLabel = document.getElementById('inspSelectedLabel');
    const inspXPct = document.getElementById('inspXPct');
    const inspYPct = document.getElementById('inspYPct');
    const inspFontSize = document.getElementById('inspFontSize');
    const inspQrSize = document.getElementById('inspQrSize');
    const inspFontSizeGroup = document.getElementById('inspFontSizeGroup');
    const inspQrSizeGroup = document.getElementById('inspQrSizeGroup');
    const inspWeightGroup = document.getElementById('inspWeightGroup');
    const inspColorGroup = document.getElementById('inspColorGroup');
    const inspFontWeight = document.getElementById('inspFontWeight');
    const inspAlign = document.getElementById('inspAlign');
    const inspColor = document.getElementById('inspColor');
    const inspMaskBg = document.getElementById('inspMaskBg');
    const inspMaskColor = document.getElementById('inspMaskColor');
    const btnDeleteSelectedPh = document.getElementById('btnDeleteSelectedPh');

    const tbody = document.getElementById('detectedPlaceholdersTbody');
    const btnViewModeSample = document.getElementById('btnViewModeSample');
    const btnViewModeTags = document.getElementById('btnViewModeTags');
    const btnAddDefaultPlaceholders = document.getElementById('btnAddDefaultPlaceholders');
    const btnClearAllPlaceholders = document.getElementById('btnClearAllPlaceholders');

    const toast = document.getElementById('copyToast');
    const toastText = document.getElementById('copyToastText');
    let toastTimeout = null;

    // Data sampel dari server untuk pratinjau nilai asli
    const sampleValues = <?= json_encode($sampleValues ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    const sampleQrUri = <?= json_encode($sampleQrUri ?? '', JSON_UNESCAPED_SLASHES) ?>;
    const existingBgUrl = <?= json_encode($existingBgUrl, JSON_UNESCAPED_SLASHES) ?>;
    const existingBgExt = <?= json_encode($existingBgExt) ?>;
    const inspectEndpoint = <?= json_encode(site_url('dashboard/sertifikat/template/inspect-pdf'), JSON_UNESCAPED_SLASHES) ?>;
    const livePreviewEndpoint = <?= json_encode(site_url('dashboard/sertifikat/template/live-preview-pdf'), JSON_UNESCAPED_SLASHES) ?>;

    // State utama daftar placeholder PDF
    let placeholders = [];
    try {
        const parsed = JSON.parse(jsonInput.value || '[]');
        if (Array.isArray(parsed)) placeholders = parsed;
    } catch (e) {
        placeholders = [];
    }

    let selectedId = placeholders.length > 0 ? placeholders[0].id : null;
    let viewMode = 'sample'; // 'sample' | 'tags'
    let currentPdfDoc = null;

    // Sinkronisasi input Pejabat Penanda Tangan dengan sampleValues secara real-time
    const issuerInput = document.getElementById('issuerNameInput');
    const signerNameInput = document.getElementById('signerNameInput');
    const signerPosInput = document.getElementById('signerPositionInput');
    const signerNipInput = document.getElementById('signerNipInput');

    function syncSignerInputsToSample() {
        if (issuerInput) {
            sampleValues['{{penerbit}}'] = issuerInput.value;
            sampleValues['{{issuer_name}}'] = issuerInput.value;
        }
        if (signerNameInput) {
            sampleValues['{{penandatangan_nama}}'] = signerNameInput.value;
            sampleValues['{{signer_name}}'] = signerNameInput.value;
        }
        if (signerPosInput) {
            sampleValues['{{penandatangan_jabatan}}'] = signerPosInput.value;
            sampleValues['{{signer_position}}'] = signerPosInput.value;
        }
        if (signerNipInput) {
            sampleValues['{{penandatangan_nip}}'] = signerNipInput.value;
            sampleValues['{{signer_nip}}'] = signerNipInput.value;
        }
        renderStage();
    }

    [issuerInput, signerNameInput, signerPosInput, signerNipInput].forEach(function(inp) {
        if (inp) inp.addEventListener('input', syncSignerInputsToSample);
    });

    function showToast(msg) {
        if (!toast || !toastText) return;
        toastText.textContent = msg;
        toast.classList.remove('d-none');
        toast.classList.add('d-flex');
        if (toastTimeout) clearTimeout(toastTimeout);
        toastTimeout = setTimeout(function() {
            toast.classList.remove('d-flex');
            toast.classList.add('d-none');
        }, 3000);
    }

    function showScanBanner(type, htmlMsg) {
        if (!scanBanner) return;
        scanBanner.className = 'mt-12 alert py-10 px-14 radius-8 text-xs d-flex align-items-center gap-2 mb-0 alert-' + type;
        scanBanner.innerHTML = htmlMsg;
        scanBanner.classList.remove('d-none');
    }

    function updateStageAspectRatio(wMm, hMm) {
        if (wMm > 10 && hMm > 10) {
            pageWidthMmInput.value = wMm.toFixed(2);
            pageHeightMmInput.value = hMm.toFixed(2);
            pdfStageOuter.style.aspectRatio = wMm + ' / ' + hMm;
            if (canvasDimensionBadge) {
                canvasDimensionBadge.textContent = 'Kanvas Sertifikat: ' + wMm.toFixed(0) + ' × ' + hMm.toFixed(0) + ' mm';
            }
        }
    }

    // =========================================================================
    // Render PDF.js & Ekstraksi Koordinat Placeholder dari PDF
    // =========================================================================

    async function loadPdfIntoCanvas(arrayBuffer, autoDetectPlaceholders) {
        if (!window.pdfjsLib) return [];
        try {
            const loadingTask = window.pdfjsLib.getDocument({ data: arrayBuffer });
            currentPdfDoc = await loadingTask.promise;
            const page = await currentPdfDoc.getPage(1);

            const baseViewport = page.getViewport({ scale: 1.0 });
            const widthPt = baseViewport.width;
            const heightPt = baseViewport.height;
            const widthMm = +(widthPt * 25.4 / 72.0).toFixed(2);
            const heightMm = +(heightPt * 25.4 / 72.0).toFixed(2);
            updateStageAspectRatio(widthMm, heightMm);

            // Render ke <canvas> dengan skala tajam (2.0x)
            const scale = 2.0;
            const viewport = page.getViewport({ scale: scale });
            const ctx = pdfCanvas.getContext('2d');
            pdfCanvas.width = viewport.width;
            pdfCanvas.height = viewport.height;

            await page.render({ canvasContext: ctx, viewport: viewport }).promise;

            emptyStateOverlay.classList.add('d-none');
            emptyStateOverlay.classList.remove('d-flex');
            imageRenderStage.classList.add('d-none');
            pdfCanvas.classList.remove('d-none');

            if (!autoDetectPlaceholders) {
                renderStage();
                return [];
            }

            // Ekstrak teks dan koordinat dari halaman 1 via PDF.js getTextContent()
            const textContent = await page.getTextContent();
            const clientDetected = extractPlaceholdersFromPdfJsTextContent(textContent, widthPt, heightPt, ctx, scale);
            return clientDetected;
        } catch (err) {
            console.warn('PDF.js render warning:', err);
            return [];
        }
    }

    /**
     * Ekstrak {{placeholder}} dari hasil page.getTextContent() milik PDF.js
     * termasuk menggabungkan item teks yang bersebelahan di baris yang sama.
     */
    function extractPlaceholdersFromPdfJsTextContent(textContent, pageWidthPt, pageHeightPt, canvasCtx, canvasScale) {
        if (!textContent || !Array.isArray(textContent.items)) return [];

        const rawRuns = [];
        textContent.items.forEach(function(item) {
            const str = item.str || '';
            if (str === '') return;
            const tr = item.transform || [12, 0, 0, 12, 0, 0];
            const fontSizePt = Math.max(6, Math.round(Math.hypot(tr[2], tr[3]) || Math.hypot(tr[0], tr[1]) || 12));
            const xPt = tr[4];
            const yPt = tr[5];
            const wPt = item.width > 0 ? item.width : (str.length * fontSizePt * 0.52);

            rawRuns.push({
                text: str,
                x_pt: xPt,
                y_pt: yPt,
                width_pt: wPt,
                font_size: fontSizePt,
                font_name: item.fontName || ''
            });
        });

        // Gabungkan item yang berada di baseline Y yang sama
        const mergedLines = [];
        rawRuns.forEach(function(run) {
            if (mergedLines.length === 0) {
                mergedLines.push(Object.assign({}, run));
                return;
            }
            const last = mergedLines[mergedLines.length - 1];
            const sameLine = Math.abs(run.y_pt - last.y_pt) <= Math.max(3.0, last.font_size * 0.3);
            const expectedX = last.x_pt + last.width_pt;
            const gap = run.x_pt - expectedX;

            if (sameLine && gap >= -(last.font_size * 0.9) && gap <= Math.max(22, last.font_size * 1.8)) {
                let spacer = (gap > last.font_size * 0.35 && !last.text.endsWith(' ') && !run.text.startsWith(' ')) ? ' ' : '';
                if (last.text.endsWith('{') || run.text.startsWith('}') || last.text.endsWith('_') || run.text.startsWith('_')) {
                    spacer = '';
                }
                last.text += spacer + run.text;
                last.width_pt = Math.max(last.width_pt, (run.x_pt + run.width_pt) - last.x_pt);
            } else {
                mergedLines.push(Object.assign({}, run));
            }
        });

        const detected = [];
        const regex = /(\{\{\s*([a-zA-Z0-9_.]+)\s*\}\}|__QR_BLOCK__)/g;
        let idx = 1;

        mergedLines.forEach(function(line) {
            let match;
            regex.lastIndex = 0;
            const lineLen = Math.max(1, line.text.length);

            while ((match = regex.exec(line.text)) !== null) {
                const fullMatch = match[1];
                const rawKey = fullMatch === '__QR_BLOCK__' ? 'qr_code' : (match[2] || '').toLowerCase().trim();
                const tag = '{{' + rawKey + '}}';
                const isQr = (rawKey === 'qr_code' || rawKey === 'qr_block');

                const charOffset = match.index;
                const matchLen = fullMatch.length;
                const prefixRatio = charOffset / lineLen;
                const widthRatio = matchLen / lineLen;

                const phXPt = line.x_pt + (line.width_pt * prefixRatio);
                const phWidthPt = Math.max(line.font_size * 2.5, line.width_pt * widthRatio);
                const topPt = Math.max(0, Math.min(pageHeightPt, pageHeightPt - (line.y_pt + line.font_size * 0.85)));

                const origXPct = +((phXPt / pageWidthPt) * 100).toFixed(2);
                const origYPct = +((topPt / pageHeightPt) * 100).toFixed(2);
                const origWPct = +((phWidthPt / pageWidthPt) * 100).toFixed(2);
                const origHPct = +(((line.font_size * 1.35) / pageHeightPt) * 100).toFixed(2);

                const isStandalone = line.text.trim() === fullMatch;
                const centerXPct = origXPct + (origWPct / 2);
                const nearCenter = Math.abs(centerXPct - 50) <= 10;

                let align = 'left';
                let xPct = origXPct;
                if (isQr) {
                    align = 'center';
                    xPct = +centerXPct.toFixed(2);
                } else if (isStandalone && nearCenter) {
                    align = 'center';
                    xPct = 50.0;
                }

                const boldDefaults = ['nama_peserta', 'nama_lengkap', 'participant_name', 'nomor_sertifikat', 'judul_kegiatan', 'penandatangan_nama'];
                const isBold = boldDefaults.includes(rawKey);

                detected.push({
                    id: 'ph_' + (idx++),
                    tag: tag,
                    key: rawKey,
                    label: getPlaceholderLabel(tag),
                    type: isQr ? 'qr' : 'text',
                    x_pct: xPct,
                    y_pct: origYPct,
                    orig_x_pct: origXPct,
                    orig_y_pct: origYPct,
                    orig_w_pct: origWPct,
                    orig_h_pct: origHPct,
                    font_size: line.font_size,
                    font_weight: isBold ? 'bold' : 'normal',
                    color: '#0f172a',
                    align: align,
                    mask_bg: false,
                    mask_color: '#ffffff',
                    qr_size: 110
                });
            }
        });

        return detected;
    }

    function getPlaceholderLabel(tag) {
        const itemEl = document.querySelector('.placeholder-item[data-tag="' + tag + '"]');
        if (itemEl && itemEl.dataset.label) return itemEl.dataset.label;
        return tag.replace(/[{}]/g, '').replace(/_/g, ' ');
    }

    function getSampleValue(tag) {
        if (sampleValues[tag] !== undefined) return sampleValues[tag];
        const itemEl = document.querySelector('.placeholder-item[data-tag="' + tag + '"]');
        if (itemEl && itemEl.dataset.example) return itemEl.dataset.example;
        return tag;
    }

    /**
     * Panggil endpoint server inspect-pdf sekaligus gabungkan dengan deteksi PDF.js client-side.
     */
    async function inspectSelectedPdfFile(file, existingTplId) {
        showScanBanner('info', '<iconify-icon icon="solar:refresh-bold" class="text-base"></iconify-icon> <span>Memindai halaman PDF dan mendeteksi koordinat <code>{{placeholder}}</code>...</span>');

        let clientPlaceholders = [];
        if (file) {
            const arrayBuffer = await file.arrayBuffer();
            clientPlaceholders = await loadPdfIntoCanvas(arrayBuffer, true);
        }

        // Kirim juga ke server inspect-pdf untuk mendapatkan warna teks & validasi stream PDF
        try {
            const fd = new FormData();
            const csrfInput = form.querySelector('input[type="hidden"][name^="csrf_"]');
            if (csrfInput) {
                fd.append(csrfInput.name, csrfInput.value);
            }
            if (file) {
                fd.append('background_file', file);
            } else if (existingTplId > 0) {
                fd.append('template_id', existingTplId);
            }

            const resp = await fetch(inspectEndpoint, {
                method: 'POST',
                body: fd,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            if (resp.ok) {
                const data = await resp.json();
                if (data && data.status === 'ok') {
                    if (data.page_width_mm && data.page_height_mm) {
                        updateStageAspectRatio(parseFloat(data.page_width_mm), parseFloat(data.page_height_mm));
                    }
                    if (Array.isArray(data.placeholders) && data.placeholders.length >= clientPlaceholders.length && data.placeholders.length > 0) {
                        placeholders = data.placeholders;
                    } else if (clientPlaceholders.length > 0) {
                        placeholders = clientPlaceholders;
                    }
                }
            } else if (clientPlaceholders.length > 0) {
                placeholders = clientPlaceholders;
            }
        } catch (err) {
            if (clientPlaceholders.length > 0) {
                placeholders = clientPlaceholders;
            }
        }

        if (placeholders.length > 0) {
            selectedId = placeholders[0].id;
            const tagsList = placeholders.map(function(p) { return '<code>' + p.tag + '</code>'; }).join(', ');
            showScanBanner(
                'success',
                '<iconify-icon icon="solar:check-circle-bold" class="text-lg text-success-600 flex-shrink-0"></iconify-icon>' +
                '<div><strong>Berhasil mendeteksi ' + placeholders.length + ' placeholder di dalam PDF:</strong> ' + tagsList +
                '<div class="text-neutral-600 mt-1">Teks placeholder asli pada PDF akan otomatis diganti dengan data peserta saat sertifikat digenerate.</div></div>'
            );
        } else {
            showScanBanner(
                'warning',
                '<iconify-icon icon="solar:info-circle-bold" class="text-lg text-warning-600 flex-shrink-0"></iconify-icon>' +
                '<div>File PDF berhasil dimuat, namun belum ditemukan teks bertanda <code>{{...}}</code> di dalamnya. ' +
                'Anda dapat menekan tombol <strong>Muat Preset Placeholder</strong> atau klik placeholder di panel kanan untuk menempatkannya di atas PDF.</div>'
            );
        }

        renderStage();
    }

    // Jika sedang mode Edit dan memiliki file PDF existing, muat ke kanvas
    if (existingBgUrl && existingBgExt === 'pdf') {
        fetch(existingBgUrl)
            .then(function(r) { return r.arrayBuffer(); })
            .then(function(buf) {
                loadPdfIntoCanvas(buf, placeholders.length === 0).then(function(detected) {
                    if (placeholders.length === 0 && detected.length > 0) {
                        placeholders = detected;
                        selectedId = placeholders[0].id;
                    }
                    renderStage();
                });
            })
            .catch(function() {
                renderStage();
            });
    }

    // Event saat pengguna memilih file PDF / Gambar baru
    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;

            const fileName = (file.name || '').toLowerCase();
            const isPdf = file.type === 'application/pdf' || fileName.endsWith('.pdf');

            // Isi otomatis nama template bila masih kosong
            const nameInput = document.getElementById('templateNameInput');
            if (nameInput && nameInput.value.trim() === '') {
                const cleanTitle = file.name.replace(/\.[^/.]+$/, '').replace(/[_-]+/g, ' ');
                nameInput.value = 'Template Sertifikat - ' + cleanTitle;
            }

            if (isPdf) {
                inspectSelectedPdfFile(file, 0);
            } else if (file.type.startsWith('image/') || /\.(png|jpe?g|webp)$/i.test(fileName)) {
                const reader = new FileReader();
                reader.onload = function(evt) {
                    pdfCanvas.classList.add('d-none');
                    emptyStateOverlay.classList.add('d-none');
                    emptyStateOverlay.classList.remove('d-flex');
                    imageRenderStage.src = evt.target.result;
                    imageRenderStage.classList.remove('d-none');
                    if (placeholders.length === 0) {
                        loadDefaultPresetPlaceholders();
                    } else {
                        renderStage();
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }

    if (btnRescanPdf) {
        btnRescanPdf.addEventListener('click', function() {
            const file = fileInput && fileInput.files ? fileInput.files[0] : null;
            const tplId = parseInt(document.getElementById('templateIdInput')?.value || '0', 10);
            if (file) {
                inspectSelectedPdfFile(file, 0);
            } else if (tplId > 0 && existingBgExt === 'pdf') {
                inspectSelectedPdfFile(null, tplId);
            } else {
                showToast('Pilih file PDF terlebih dahulu pada Bagian 2.');
            }
        });
    }

    // =========================================================================
    // Render Elemen Interaktif di Atas Kanvas PDF & Tabel
    // =========================================================================

    function syncHiddenJson() {
        jsonInput.value = JSON.stringify(placeholders);
        if (activeCountBadge) {
            activeCountBadge.textContent = placeholders.length + ' Placeholder Terpasang';
        }
    }

    function renderStage() {
        syncHiddenJson();
        pdfMaskLayer.innerHTML = '';
        pdfInteractiveOverlay.innerHTML = '';

        const stageRect = pdfStageOuter.getBoundingClientRect();
        const pageW = parseFloat(pageWidthMmInput.value) || 297;
        // Faktor skala antara ukuran poin PDF (841.89pt) dengan lebar piksel kanvas di layar
        const ptToPxScale = stageRect.width > 0 ? (stageRect.width / (pageW * 72 / 25.4)) : 1.0;

        placeholders.forEach(function(ph) {
            // 1. Pada mode 'sample' atau jika mask_bg diaktifkan, tutup teks {{placeholder}} asli pada kanvas pratinjau
            //    agar pengguna melihat tampilan bersih seperti hasil akhir setelah teks {{...}} dihapus dari stream PDF
            if (viewMode === 'sample' || ph.mask_bg) {
                const maskEl = document.createElement('div');
                const ox = ph.orig_x_pct !== undefined ? ph.orig_x_pct : Math.max(0, ph.x_pct - 10);
                const oy = ph.orig_y_pct !== undefined ? ph.orig_y_pct : ph.y_pct;
                const ow = ph.orig_w_pct !== undefined ? ph.orig_w_pct : 20;
                const oh = ph.orig_h_pct !== undefined ? ph.orig_h_pct : 4.2;

                maskEl.style.position = 'absolute';
                maskEl.style.left = Math.max(0, ox - 0.4) + '%';
                maskEl.style.top = Math.max(0, oy - 0.2) + '%';
                maskEl.style.width = Math.min(100, ow + 0.8) + '%';
                maskEl.style.height = Math.min(50, oh + 0.4) + '%';
                maskEl.style.backgroundColor = ph.mask_color || '#ffffff';
                maskEl.style.borderRadius = '2px';
                pdfMaskLayer.appendChild(maskEl);
            }

            // 2. Render elemen interaktif placeholder
            const el = document.createElement('div');
            const isSelected = ph.id === selectedId;
            el.className = 'pdf-ph-marker';
            el.dataset.id = ph.id;
            el.style.position = 'absolute';
            el.style.top = ph.y_pct + '%';
            el.style.left = ph.x_pct + '%';
            el.style.cursor = 'move';
            el.style.padding = '2px 6px';
            el.style.borderRadius = '4px';
            el.style.whiteSpace = 'nowrap';
            el.style.transition = 'box-shadow 0.12s ease';

            if (ph.align === 'center') {
                el.style.transform = 'translateX(-50%)';
                el.style.textAlign = 'center';
            } else if (ph.align === 'right') {
                el.style.transform = 'translateX(-100%)';
                el.style.textAlign = 'right';
            } else {
                el.style.transform = 'none';
                el.style.textAlign = 'left';
            }

            if (isSelected) {
                el.style.outline = '2px solid #2563eb';
                el.style.backgroundColor = viewMode === 'sample' ? 'rgba(239, 246, 255, 0.85)' : 'rgba(37, 99, 235, 0.92)';
                el.style.boxShadow = '0 4px 12px rgba(37, 99, 235, 0.25)';
                el.style.zIndex = '20';
            } else {
                el.style.outline = '1px dashed rgba(37, 99, 235, 0.65)';
                el.style.backgroundColor = viewMode === 'sample' ? 'rgba(255, 255, 255, 0.55)' : 'rgba(15, 23, 42, 0.82)';
                el.style.zIndex = '10';
            }

            if (ph.type === 'qr') {
                const qrPx = Math.max(36, Math.round((ph.qr_size || 110) * ptToPxScale * 0.75));
                el.style.width = qrPx + 'px';
                el.style.height = qrPx + 'px';
                el.style.padding = '2px';
                el.style.backgroundColor = '#ffffff';
                if (viewMode === 'sample' && sampleQrUri) {
                    el.innerHTML = '<img src="' + sampleQrUri + '" style="width:100%;height:100%;object-fit:contain;pointer-events:none;" alt="QR">';
                } else {
                    el.innerHTML = '<div class="w-100 h-100 d-flex flex-column align-items-center justify-content-center bg-primary-50 text-primary-700" style="font-size:10px;font-weight:700;">' +
                        '<iconify-icon icon="solar:qr-code-bold" class="text-xl"></iconify-icon><span>{{qr_code}}</span></div>';
                }
            } else {
                const scaledFontPx = Math.max(9, (ph.font_size || 16) * ptToPxScale);
                el.style.fontSize = scaledFontPx + 'px';
                el.style.fontWeight = ph.font_weight === 'bold' ? '700' : '400';
                el.style.lineHeight = '1.15';

                if (viewMode === 'sample') {
                    el.style.color = ph.color || '#0f172a';
                    el.textContent = getSampleValue(ph.tag);
                } else {
                    el.style.color = '#ffffff';
                    el.style.fontSize = Math.max(10, Math.min(16, scaledFontPx)) + 'px';
                    el.style.fontFamily = 'monospace';
                    el.textContent = ph.tag;
                }
            }

            // Drag & Drop handler pada kanvas PDF
            el.addEventListener('mousedown', function(evt) {
                evt.stopPropagation();
                selectPlaceholder(ph.id);
                startDragging(evt, ph);
            });

            pdfInteractiveOverlay.appendChild(el);
        });

        renderInspector();
        renderTable();
    }

    function startDragging(startEvt, ph) {
        startEvt.preventDefault();
        const rect = pdfStageOuter.getBoundingClientRect();
        if (rect.width <= 0 || rect.height <= 0) return;

        const startX = startEvt.clientX;
        const startY = startEvt.clientY;
        const initXPct = ph.x_pct;
        const initYPct = ph.y_pct;

        function onMouseMove(moveEvt) {
            const dx = moveEvt.clientX - startX;
            const dy = moveEvt.clientY - startY;
            const dXPct = (dx / rect.width) * 100;
            const dYPct = (dy / rect.height) * 100;

            ph.x_pct = +Math.max(0, Math.min(100, initXPct + dXPct)).toFixed(1);
            ph.y_pct = +Math.max(0, Math.min(100, initYPct + dYPct)).toFixed(1);
            renderStage();
        }

        function onMouseUp() {
            document.removeEventListener('mousemove', onMouseMove);
            document.removeEventListener('mouseup', onMouseUp);
        }

        document.addEventListener('mousemove', onMouseMove);
        document.addEventListener('mouseup', onMouseUp);
    }

    function selectPlaceholder(id) {
        selectedId = id;
        renderStage();
    }

    function renderInspector() {
        const ph = placeholders.find(function(p) { return p.id === selectedId; });
        if (!ph) {
            inspectorPanel.classList.add('d-none');
            return;
        }

        inspectorPanel.classList.remove('d-none');
        inspSelectedTag.textContent = ph.tag;
        inspSelectedLabel.textContent = ph.label || getPlaceholderLabel(ph.tag);
        inspXPct.value = ph.x_pct;
        inspYPct.value = ph.y_pct;
        inspAlign.value = ph.align || 'center';
        inspMaskBg.checked = !!ph.mask_bg;
        inspMaskColor.value = ph.mask_color || '#ffffff';

        if (ph.type === 'qr') {
            inspFontSizeGroup.classList.add('d-none');
            inspWeightGroup.classList.add('d-none');
            inspColorGroup.classList.add('d-none');
            inspQrSizeGroup.classList.remove('d-none');
            inspQrSize.value = ph.qr_size || 110;
        } else {
            inspFontSizeGroup.classList.remove('d-none');
            inspWeightGroup.classList.remove('d-none');
            inspColorGroup.classList.remove('d-none');
            inspQrSizeGroup.classList.add('d-none');
            inspFontSize.value = ph.font_size || 16;
            inspFontWeight.value = ph.font_weight || 'normal';
            inspColor.value = ph.color || '#0f172a';
        }
    }

    function bindInspectorEvents() {
        function updateSelected(updater) {
            const ph = placeholders.find(function(p) { return p.id === selectedId; });
            if (!ph) return;
            updater(ph);
            renderStage();
        }

        inspXPct.addEventListener('input', function() {
            updateSelected(function(ph) { ph.x_pct = parseFloat(inspXPct.value) || 0; });
        });
        inspYPct.addEventListener('input', function() {
            updateSelected(function(ph) { ph.y_pct = parseFloat(inspYPct.value) || 0; });
        });
        inspFontSize.addEventListener('input', function() {
            updateSelected(function(ph) { ph.font_size = parseInt(inspFontSize.value, 10) || 16; });
        });
        inspQrSize.addEventListener('input', function() {
            updateSelected(function(ph) { ph.qr_size = parseInt(inspQrSize.value, 10) || 110; });
        });
        inspFontWeight.addEventListener('change', function() {
            updateSelected(function(ph) { ph.font_weight = inspFontWeight.value; });
        });
        inspAlign.addEventListener('change', function() {
            updateSelected(function(ph) { ph.align = inspAlign.value; });
        });
        inspColor.addEventListener('input', function() {
            updateSelected(function(ph) { ph.color = inspColor.value; });
        });
        inspMaskBg.addEventListener('change', function() {
            updateSelected(function(ph) { ph.mask_bg = inspMaskBg.checked; });
        });
        inspMaskColor.addEventListener('input', function() {
            updateSelected(function(ph) { ph.mask_color = inspMaskColor.value; });
        });

        btnDeleteSelectedPh.addEventListener('click', function() {
            if (!selectedId) return;
            placeholders = placeholders.filter(function(p) { return p.id !== selectedId; });
            selectedId = placeholders.length > 0 ? placeholders[0].id : null;
            renderStage();
        });
    }
    bindInspectorEvents();

    function renderTable() {
        if (!tbody) return;
        if (placeholders.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center py-16 text-secondary-light">' +
                'Belum ada placeholder. Unggah file PDF berisi tag <code>{{...}}</code> atau klik daftar placeholder di panel kanan.' +
                '</td></tr>';
            return;
        }

        tbody.innerHTML = '';
        placeholders.forEach(function(ph) {
            const tr = document.createElement('tr');
            if (ph.id === selectedId) {
                tr.className = 'table-primary';
            }
            tr.style.cursor = 'pointer';

            const sampleText = ph.type === 'qr' ? '[Gambar QR Code Verifikasi]' : getSampleValue(ph.tag);
            const styleInfo = ph.type === 'qr'
                ? (ph.qr_size || 110) + ' px'
                : (ph.font_size || 16) + 'pt, ' + (ph.font_weight === 'bold' ? 'Bold' : 'Normal');
            const alignLabel = ph.align === 'center' ? 'Tengah' : (ph.align === 'right' ? 'Kanan' : 'Kiri');

            tr.innerHTML =
                '<td class="py-8 px-12"><code class="fw-bold text-primary-700">' + ph.tag + '</code></td>' +
                '<td class="py-8 px-12 text-neutral-700 text-truncate" style="max-width:220px;">' + sampleText + '</td>' +
                '<td class="py-8 px-12 text-center font-monospace">' + ph.x_pct + '%, ' + ph.y_pct + '%</td>' +
                '<td class="py-8 px-12 text-center">' + styleInfo + '</td>' +
                '<td class="py-8 px-12 text-center">' + alignLabel + '</td>' +
                '<td class="py-8 px-12 text-end">' +
                    '<button type="button" class="btn btn-xs btn-outline-danger-600 radius-4 px-6 py-2 btn-del-row" data-id="' + ph.id + '" title="Hapus">' +
                        '<iconify-icon icon="solar:trash-bin-trash-bold"></iconify-icon>' +
                    '</button>' +
                '</td>';

            tr.addEventListener('click', function(e) {
                if (e.target.closest('.btn-del-row')) return;
                selectPlaceholder(ph.id);
            });

            const delBtn = tr.querySelector('.btn-del-row');
            if (delBtn) {
                delBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    placeholders = placeholders.filter(function(p) { return p.id !== ph.id; });
                    if (selectedId === ph.id) {
                        selectedId = placeholders.length > 0 ? placeholders[0].id : null;
                    }
                    renderStage();
                });
            }

            tbody.appendChild(tr);
        });
    }

    // Mode Tampilan: Pratinjau Data Asli vs Tag {{...}}
    if (btnViewModeSample && btnViewModeTags) {
        btnViewModeSample.addEventListener('click', function() {
            viewMode = 'sample';
            btnViewModeSample.className = 'btn btn-outline-primary-600 px-12 py-6 text-xs fw-semibold';
            btnViewModeTags.className = 'btn btn-outline-neutral-300 px-12 py-6 text-xs fw-semibold text-neutral-700';
            renderStage();
        });

        btnViewModeTags.addEventListener('click', function() {
            viewMode = 'tags';
            btnViewModeTags.className = 'btn btn-outline-primary-600 px-12 py-6 text-xs fw-semibold';
            btnViewModeSample.className = 'btn btn-outline-neutral-300 px-12 py-6 text-xs fw-semibold text-neutral-700';
            renderStage();
        });
    }

    // Preset Placeholder Standar
    function loadDefaultPresetPlaceholders() {
        placeholders = [
            { id: 'ph_1', tag: '{{nomor_sertifikat}}', key: 'nomor_sertifikat', label: 'Nomor Sertifikat', type: 'text', x_pct: 50, y_pct: 22, orig_x_pct: 38, orig_y_pct: 22, orig_w_pct: 24, orig_h_pct: 3.5, font_size: 13, font_weight: 'bold', color: '#1e3a8a', align: 'center', mask_bg: false, mask_color: '#ffffff', qr_size: 110 },
            { id: 'ph_2', tag: '{{nama_peserta}}', key: 'nama_peserta', label: 'Nama Lengkap Peserta', type: 'text', x_pct: 50, y_pct: 37, orig_x_pct: 32, orig_y_pct: 37, orig_w_pct: 36, orig_h_pct: 5.5, font_size: 26, font_weight: 'bold', color: '#0f172a', align: 'center', mask_bg: false, mask_color: '#ffffff', qr_size: 110 },
            { id: 'ph_3', tag: '{{nip}}', key: 'nip', label: 'NIP Peserta', type: 'text', x_pct: 50, y_pct: 44, orig_x_pct: 40, orig_y_pct: 44, orig_w_pct: 20, orig_h_pct: 3.5, font_size: 12, font_weight: 'normal', color: '#334155', align: 'center', mask_bg: false, mask_color: '#ffffff', qr_size: 110 },
            { id: 'ph_4', tag: '{{instansi}}', key: 'instansi', label: 'Instansi Peserta', type: 'text', x_pct: 50, y_pct: 48, orig_x_pct: 30, orig_y_pct: 48, orig_w_pct: 40, orig_h_pct: 3.5, font_size: 12, font_weight: 'normal', color: '#334155', align: 'center', mask_bg: false, mask_color: '#ffffff', qr_size: 110 },
            { id: 'ph_5', tag: '{{judul_kegiatan}}', key: 'judul_kegiatan', label: 'Judul Kegiatan', type: 'text', x_pct: 50, y_pct: 57, orig_x_pct: 25, orig_y_pct: 57, orig_w_pct: 50, orig_h_pct: 4.5, font_size: 18, font_weight: 'bold', color: '#166534', align: 'center', mask_bg: false, mask_color: '#ffffff', qr_size: 110 },
            { id: 'ph_6', tag: '{{jam_pelajaran}}', key: 'jam_pelajaran', label: 'Jam Pelajaran', type: 'text', x_pct: 50, y_pct: 64, orig_x_pct: 44, orig_y_pct: 64, orig_w_pct: 12, orig_h_pct: 3.5, font_size: 12, font_weight: 'bold', color: '#334155', align: 'center', mask_bg: false, mask_color: '#ffffff', qr_size: 110 },
            { id: 'ph_7', tag: '{{tanggal_terbit}}', key: 'tanggal_terbit', label: 'Tanggal Terbit', type: 'text', x_pct: 25, y_pct: 73, orig_x_pct: 18, orig_y_pct: 73, orig_w_pct: 16, orig_h_pct: 3.5, font_size: 11, font_weight: 'normal', color: '#334155', align: 'center', mask_bg: false, mask_color: '#ffffff', qr_size: 110 },
            { id: 'ph_8', tag: '{{penandatangan_jabatan}}', key: 'penandatangan_jabatan', label: 'Jabatan Penanda Tangan', type: 'text', x_pct: 25, y_pct: 77, orig_x_pct: 12, orig_y_pct: 77, orig_w_pct: 26, orig_h_pct: 3.5, font_size: 11, font_weight: 'bold', color: '#0f172a', align: 'center', mask_bg: false, mask_color: '#ffffff', qr_size: 110 },
            { id: 'ph_9', tag: '{{penandatangan_nama}}', key: 'penandatangan_nama', label: 'Nama Penanda Tangan', type: 'text', x_pct: 25, y_pct: 89, orig_x_pct: 12, orig_y_pct: 89, orig_w_pct: 26, orig_h_pct: 3.5, font_size: 12, font_weight: 'bold', color: '#0f172a', align: 'center', mask_bg: false, mask_color: '#ffffff', qr_size: 110 },
            { id: 'ph_10', tag: '{{penandatangan_nip}}', key: 'penandatangan_nip', label: 'NIP Penanda Tangan', type: 'text', x_pct: 25, y_pct: 93, orig_x_pct: 14, orig_y_pct: 93, orig_w_pct: 22, orig_h_pct: 3.5, font_size: 10, font_weight: 'normal', color: '#475569', align: 'center', mask_bg: false, mask_color: '#ffffff', qr_size: 110 },
            { id: 'ph_11', tag: '{{qr_code}}', key: 'qr_code', label: 'QR Code Verifikasi', type: 'qr', x_pct: 80, y_pct: 74, orig_x_pct: 74, orig_y_pct: 74, orig_w_pct: 12, orig_h_pct: 18, font_size: 12, font_weight: 'normal', color: '#0f172a', align: 'center', mask_bg: false, mask_color: '#ffffff', qr_size: 105 }
        ];
        selectedId = placeholders[0].id;
        renderStage();
    }

    if (btnAddDefaultPlaceholders) {
        btnAddDefaultPlaceholders.addEventListener('click', function() {
            if (placeholders.length > 0 && !confirm('Muat preset placeholder standar? Daftar placeholder saat ini akan diganti.')) {
                return;
            }
            loadDefaultPresetPlaceholders();
            showToast('Preset placeholder standar berhasil dimuat ke kanvas.');
        });
    }

    if (btnClearAllPlaceholders) {
        btnClearAllPlaceholders.addEventListener('click', function() {
            if (placeholders.length === 0) return;
            if (confirm('Hapus semua placeholder dari kanvas?')) {
                placeholders = [];
                selectedId = null;
                renderStage();
            }
        });
    }

    // Klik pada Palet Placeholder di Kolom Kanan -> Salin ke Clipboard + Tambahkan ke Kanvas PDF
    document.querySelectorAll('.placeholder-item').forEach(function(item) {
        item.addEventListener('click', function() {
            const tag = this.getAttribute('data-tag');
            const label = this.getAttribute('data-label') || tag;
            if (!tag) return;

            navigator.clipboard.writeText(tag).catch(function() {});

            // Cek apakah sudah ada di kanvas; jika belum, tambahkan
            const existing = placeholders.find(function(p) { return p.tag === tag; });
            if (existing) {
                selectPlaceholder(existing.id);
                showToast('Placeholder ' + tag + ' disalin & dipilih pada kanvas!');
                return;
            }

            const key = tag.replace(/[{}]/g, '');
            const isQr = (key === 'qr_code' || key === 'qr_block');
            const isName = (key === 'nama_peserta' || key === 'participant_name');
            const isTitle = (key === 'judul_kegiatan' || key === 'certificate_title');
            const newId = 'ph_' + Date.now();
            const yOffset = Math.min(88, 25 + (placeholders.length * 6));

            placeholders.push({
                id: newId,
                tag: tag,
                key: key,
                label: label,
                type: isQr ? 'qr' : 'text',
                x_pct: isQr ? 80 : 50,
                y_pct: isQr ? 74 : yOffset,
                orig_x_pct: isQr ? 74 : 38,
                orig_y_pct: isQr ? 74 : yOffset,
                orig_w_pct: isQr ? 12 : 24,
                orig_h_pct: isQr ? 18 : 4,
                font_size: isName ? 26 : (isTitle ? 18 : 13),
                font_weight: (isName || isTitle || key === 'nomor_sertifikat' || key === 'penandatangan_nama') ? 'bold' : 'normal',
                color: '#0f172a',
                align: 'center',
                mask_bg: false,
                mask_color: '#ffffff',
                qr_size: 110
            });

            selectedId = newId;
            renderStage();
            showToast('Placeholder ' + tag + ' disalin & ditambahkan ke kanvas PDF!');
        });
    });

    // Tombol Uji / Pratinjau Hasil Generate PDF Secara Langsung (Tab Baru)
    function triggerLivePdfPreview() {
        syncHiddenJson();
        const fd = new FormData(form);

        // Buka di tab baru melalui form sementara
        const previewForm = document.createElement('form');
        previewForm.method = 'POST';
        previewForm.action = livePreviewEndpoint;
        previewForm.target = '_blank';
        previewForm.enctype = 'multipart/form-data';
        previewForm.style.display = 'none';

        for (const [k, v] of fd.entries()) {
            if (v instanceof File) continue;
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = k;
            input.value = v;
            previewForm.appendChild(input);
        }

        // Jika ada file baru yang dipilih di fileInput, kirim via fetch + Blob URL agar file ikut terkirim
        const hasNewFile = fileInput && fileInput.files && fileInput.files.length > 0;
        if (hasNewFile) {
            showToast('Sedang merender pratinjau PDF...');
            fetch(livePreviewEndpoint, {
                method: 'POST',
                body: fd
            })
            .then(function(resp) {
                if (!resp.ok) throw new Error('Gagal merender PDF');
                return resp.blob();
            })
            .then(function(blob) {
                const url = URL.createObjectURL(blob);
                window.open(url, '_blank');
            })
            .catch(function(err) {
                alert('Gagal membuat pratinjau PDF: ' + err.message);
            });
            return;
        }

        document.body.appendChild(previewForm);
        previewForm.submit();
        document.body.removeChild(previewForm);
    }

    const btnTopPreview = document.getElementById('btnTopLivePdfPreview');
    const btnBottomPreview = document.getElementById('btnBottomLivePdfPreview');
    if (btnTopPreview) btnTopPreview.addEventListener('click', triggerLivePdfPreview);
    if (btnBottomPreview) btnBottomPreview.addEventListener('click', triggerLivePdfPreview);

    // Dynamic Scope & Event switching (Bagian 1)
    const sourceTypeSelect = document.getElementById('sourceTypeSelect');
    const learningScopeSection = document.getElementById('learningScopeSection');
    const eventScopeSection = document.getElementById('eventScopeSection');
    const scopeRadios = document.querySelectorAll('input[name="payload[target_scope]"]');
    const fieldProgram = document.getElementById('fieldProgram');
    const fieldCourse = document.getElementById('fieldCourse');
    const fieldClass = document.getElementById('fieldClass');
    const selectEvent = document.getElementById('selectEvent');
    const templateNameInput = document.getElementById('templateNameInput');

    function updateScopeVisibility() {
        const isLearning = sourceTypeSelect.value === 'learning';
        if (learningScopeSection) learningScopeSection.style.display = isLearning ? 'block' : 'none';
        if (eventScopeSection) eventScopeSection.style.display = isLearning ? 'none' : 'block';

        if (isLearning) {
            let activeScope = 'global';
            scopeRadios.forEach(function(r) { if (r.checked) activeScope = r.value; });
            if (fieldProgram) fieldProgram.style.display = (activeScope === 'program') ? 'block' : 'none';
            if (fieldCourse)  fieldCourse.style.display  = (activeScope === 'course') ? 'block' : 'none';
            if (fieldClass)   fieldClass.style.display   = (activeScope === 'class') ? 'block' : 'none';
        }
    }

    if (sourceTypeSelect) sourceTypeSelect.addEventListener('change', updateScopeVisibility);
    scopeRadios.forEach(function(r) { r.addEventListener('change', updateScopeVisibility); });

    if (selectEvent && templateNameInput) {
        selectEvent.addEventListener('change', function() {
            const opt = selectEvent.options[selectEvent.selectedIndex];
            if (opt && opt.dataset.title && (templateNameInput.value.trim() === '' || templateNameInput.value.indexOf('Template Sertifikat -') === 0)) {
                templateNameInput.value = 'Template Sertifikat - ' + opt.dataset.title;
            }
        });
    }

    window.addEventListener('resize', function() {
        renderStage();
    });

    // Render awal
    renderStage();
});
</script>
<?= $this->endSection() ?>
