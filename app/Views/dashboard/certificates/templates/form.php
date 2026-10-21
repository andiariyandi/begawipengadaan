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

$bgAsset = $backgroundAsset ?? null;
$placeholdersGrouped = $availablePlaceholders ?? \App\Services\CertificateTemplateRenderer::getAvailablePlaceholders();
?>

<!-- Breadcrumb & Header -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Template Sertifikat') ?></h6>
        <p class="text-secondary-light mb-0">
            <?= $isCreate
                ? 'Rancang template sertifikat baru dengan mengunggah gambar background dan menyusun placeholder.'
                : 'Sesuaikan tata letak, gambar background, pejabat penanda tangan, dan placeholder sertifikat.' ?>
        </p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= site_url('dashboard/sertifikat/template') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">
            <iconify-icon icon="solar:arrow-left-linear" class="me-1"></iconify-icon> Kembali
        </a>
        <?php if (! $isCreate && ! empty($t['id'])): ?>
            <a href="<?= site_url('dashboard/sertifikat/template/' . (int) $t['id'] . '/preview') ?>" target="_blank"
               class="btn btn-outline-primary-600 radius-8 px-16 py-11 d-flex align-items-center gap-1">
                <iconify-icon icon="solar:eye-bold"></iconify-icon> Pratinjau HTML
            </a>
            <a href="<?= site_url('dashboard/sertifikat/template/' . (int) $t['id'] . '/preview-pdf') ?>" target="_blank"
               class="btn btn-outline-danger-600 radius-8 px-16 py-11 d-flex align-items-center gap-1">
                <iconify-icon icon="solar:file-download-bold"></iconify-icon> Unduh Sampel PDF
            </a>
        <?php endif; ?>
    </div>
</div>

<form method="post"
      action="<?= esc($isCreate
                  ? site_url('dashboard/sertifikat/template/simpan')
                  : site_url('dashboard/sertifikat/template/' . (int)($t['id'] ?? 0) . '/ubah')) ?>"
      enctype="multipart/form-data"
      novalidate>
    <?= csrf_field() ?>

    <div class="row gy-4">
        <!-- Kolom Kiri: Form Utama & Editor Template -->
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
                        <div class="col-12" id="learningScopeSection">
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
                                            $isLocked = isset($hierarchyOptions['lockInfo']['locked_programs'][(int)$prog['id']]);
                                            $lockText = $isLocked ? ' (Sudah Memiliki Template: ' . esc($hierarchyOptions['lockInfo']['locked_programs'][(int)$prog['id']]['template_name']) . ')' : '';
                                            ?>
                                            <option value="<?= (int)$prog['id'] ?>" <?= $selProgId === (int)$prog['id'] ? 'selected' : '' ?> <?= ($isLocked && $selProgId !== (int)$prog['id']) ? 'disabled class="text-danger"' : '' ?>>
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
                                            $cId = (int)$co['id'];
                                            $isLockedByProg = isset($hierarchyOptions['lockInfo']['course_locks'][$cId]) && $hierarchyOptions['lockInfo']['course_locks'][$cId]['locked_by'] === 'program';
                                            $isLockedSelf   = isset($hierarchyOptions['lockInfo']['locked_courses'][$cId]);
                                            $lockLabel = '';
                                            if ($isLockedByProg) {
                                                $lockLabel = ' [Terkunci: Program memiliki template aktif]';
                                            } elseif ($isLockedSelf && $selCourseId !== $cId) {
                                                $lockLabel = ' [Sudah Memiliki Template: ' . esc($hierarchyOptions['lockInfo']['locked_courses'][$cId]['template_name']) . ']';
                                            }
                                            $shouldDisable = ($isLockedByProg || ($isLockedSelf && $selCourseId !== $cId));
                                            ?>
                                            <option value="<?= $cId ?>" data-program-id="<?= (int)($co['program_id'] ?? 0) ?>" <?= $selCourseId === $cId ? 'selected' : '' ?> <?= $shouldDisable ? 'disabled class="text-danger"' : '' ?>>
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
                                            $clId = (int)$cl['id'];
                                            $lockData = $hierarchyOptions['lockInfo']['class_locks'][$clId] ?? null;
                                            $isLocked = false;
                                            $lockReason = '';
                                            if ($lockData) {
                                                if ($lockData['locked_by'] === 'program') {
                                                    $isLocked = true;
                                                    $lockReason = ' [Terkunci oleh Template Program]';
                                                } elseif ($lockData['locked_by'] === 'course') {
                                                    $isLocked = true;
                                                    $lockReason = ' [Terkunci oleh Template Course]';
                                                } elseif ($lockData['locked_by'] === 'class' && $selClassId !== $clId) {
                                                    $isLocked = true;
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
                                        $evId = (int) $ev['id'];
                                        $assignedTpl = $hierarchyOptions['assignedEvents'][$evId] ?? null;
                                        $isAssignedOther = ($assignedTpl && $assignedTpl['template_id'] !== (int)($t['id'] ?? 0));
                                        $evNote = $isAssignedOther ? ' [Sudah Memiliki Template: ' . esc($assignedTpl['template_name']) . ']' : '';
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

            <!-- Card 2: Upload File Template Background -->
            <div class="card mb-24">
                <div class="card-header border-bottom py-16 px-24">
                    <h6 class="text-lg fw-semibold mb-0 d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:gallery-bold" class="text-primary-600 text-xl"></iconify-icon>
                        2. Desain Gambar Background Sertifikat (PNG / JPG / PDF)
                    </h6>
                </div>
                <div class="card-body p-24">
                    <p class="text-secondary-light text-sm mb-16">
                        Unggah file desain template sertifikat Anda yang telah dirancang dari <strong>Canva</strong>, <strong>Figma</strong>, atau <strong>Photoshop</strong>.
                        Sistem akan meletakkan teks nama, nomor sertifikat, NIP, gelar, tanggal, jam pelajaran, dan QR code langsung di atas background ini.
                    </p>

                    <!-- Preview Jika Background Sudah Terpasang -->
                    <?php if (! empty($payload['background_path']) || ! empty($bgAsset)): ?>
                        <div class="p-16 radius-8 bg-neutral-50 border mb-20">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="radius-8 overflow-hidden border bg-white shadow-xs" style="width: 140px; height: 95px;">
                                        <?php
                                        $bgRel = $payload['background_path'] ?? ($bgAsset['path'] ?? '');
                                        $bgUrl = base_url(ltrim($bgRel, '/'));
                                        $ext   = strtolower(pathinfo($bgRel, PATHINFO_EXTENSION));
                                        ?>
                                        <?php if (in_array($ext, ['png', 'jpg', 'jpeg', 'webp'], true)): ?>
                                            <img src="<?= esc($bgUrl) ?>" alt="Background Preview" class="w-100 h-100 object-fit-cover">
                                        <?php else: ?>
                                            <div class="w-100 h-100 d-flex flex-column align-items-center justify-content-center text-danger">
                                                <iconify-icon icon="solar:file-text-bold" class="text-3xl"></iconify-icon>
                                                <span class="text-xs fw-semibold mt-1">File PDF</span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <h6 class="text-sm fw-semibold mb-1 text-primary-light">
                                            <?= esc($payload['background_original_name'] ?? ($bgAsset['original_name'] ?? basename($bgRel))) ?>
                                        </h6>
                                        <p class="text-xs text-secondary-light mb-1">
                                            Path: <code><?= esc($bgRel) ?></code>
                                        </p>
                                        <span class="badge bg-success-50 text-success-600 px-8 py-3 radius-4 text-xs fw-semibold">
                                            Background Aktif Terpasang
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <div class="form-check">
                                        <input class="form-check-input text-danger" type="checkbox" name="remove_background" value="1" id="removeBgCheck">
                                        <label class="form-check-label text-sm text-danger fw-semibold cursor-pointer" for="removeBgCheck">
                                            Hapus background ini
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="mb-16">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                            <?= (! empty($payload['background_path']) || ! empty($bgAsset)) ? 'Ganti File Background (Opsional)' : 'Unggah File Background Desain' ?>
                        </label>
                        <input type="file" name="background_file" id="backgroundFileInput" class="form-control radius-8"
                               accept=".png,.jpg,.jpeg,.pdf,image/png,image/jpeg,application/pdf">
                        <small class="text-secondary-light d-block mt-6">
                            Format yang didukung: <strong>PNG, JPG, JPEG</strong> (Sangat direkomendasikan resolusi <strong>3508 &times; 2480 px</strong> / A4 Landscape) atau <strong>PDF</strong>.
                        </small>
                    </div>

                    <!-- Client-side Image Preview Box -->
                    <div id="newFilePreviewBox" class="d-none mt-16 p-16 radius-8 border bg-neutral-50">
                        <p class="text-xs fw-semibold text-primary-light mb-8">Pratinjau File Pilihan Baru:</p>
                        <div class="radius-8 overflow-hidden border bg-white" style="max-width: 320px; max-height: 220px;">
                            <img id="newFilePreviewImg" src="" alt="Preview File Baru" class="w-100 h-100 object-fit-contain">
                        </div>
                    </div>
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
                    <div class="row gy-20">
                        <div class="col-md-12">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Nama Instansi Penerbit</label>
                            <input type="text" name="payload[issuer_name]" class="form-control radius-8"
                                   placeholder="Pemerintah Provinsi Lampung — Biro Pengadaan Barang dan Jasa"
                                   value="<?= esc(old('payload.issuer_name', $payload['issuer_name'] ?? 'Pemerintah Provinsi Lampung — Biro Pengadaan Barang dan Jasa')) ?>">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Nama Pejabat</label>
                            <input type="text" name="payload[signer_name]" class="form-control radius-8"
                                   placeholder="Dr. H. Rahmad Hidayat, S.E., M.Si."
                                   value="<?= esc(old('payload.signer_name', $payload['signer_name'] ?? 'Dr. H. Rahmad Hidayat, S.E., M.Si.')) ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Jabatan Pejabat</label>
                            <input type="text" name="payload[signer_position]" class="form-control radius-8"
                                   placeholder="Kepala Biro Pengadaan Barang dan Jasa"
                                   value="<?= esc(old('payload.signer_position', $payload['signer_position'] ?? 'Kepala Biro Pengadaan Barang dan Jasa')) ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">NIP Pejabat</label>
                            <input type="text" name="payload[signer_nip]" class="form-control radius-8"
                                   placeholder="19750812 199903 1 004"
                                   value="<?= esc(old('payload.signer_nip', $payload['signer_nip'] ?? '19750812 199903 1 004')) ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 4: Tata Letak Body HTML & Preset -->
            <div class="card mb-24">
                <div class="card-header border-bottom py-16 px-24 d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <h6 class="text-lg fw-semibold mb-0 d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:code-bold" class="text-primary-600 text-xl"></iconify-icon>
                        4. Tata Letak (Body HTML) &amp; Penempatan Placeholder
                    </h6>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-primary-600 px-12 py-6 text-xs fw-semibold" id="btnPresetOverlay" title="Muat template posisi overlay di atas background">
                            <iconify-icon icon="solar:layers-bold" class="me-1"></iconify-icon> Preset Overlay Background
                        </button>
                        <button type="button" class="btn btn-outline-neutral-300 px-12 py-6 text-xs fw-semibold text-neutral-700" id="btnPresetClassic" title="Muat template standar dengan bingkai klasik">
                            <iconify-icon icon="solar:frame-bold" class="me-1"></iconify-icon> Preset Sertifikat Standar
                        </button>
                        <button type="button" class="btn btn-outline-neutral-300 px-12 py-6 text-xs fw-semibold text-neutral-700" id="btnPresetClear" title="Kosongkan editor">
                            <iconify-icon icon="solar:trash-bin-trash-bold" class="me-1"></iconify-icon> Kosongkan
                        </button>
                    </div>
                </div>
                <div class="card-body p-24">
                    <div class="d-flex justify-content-between align-items-center mb-10">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-0">
                            Struktur HTML Sertifikat (Gunakan Placeholder):
                        </label>
                        <span class="text-xs text-secondary-light">
                            Klik placeholder di kolom kanan untuk menyalin/menyisipkan
                        </span>
                    </div>

                    <textarea name="payload[body_html]" id="bodyHtmlEditor" class="form-control font-monospace radius-8" rows="12"
                              style="font-size: 13px; line-height: 1.6; background: #fafafa;"
                              placeholder="Biarkan kosong untuk menggunakan template bawaan sistem otomatis, atau susun penempatan placeholder kustom Anda di sini..."><?= esc(old('payload.body_html', $payload['body_html'] ?? '')) ?></textarea>
                    
                    <div class="row mt-20 gy-20">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">CSS Tambahan Kustom (Opsional)</label>
                            <textarea name="payload[css]" class="form-control font-monospace radius-8" rows="4"
                                      style="font-size: 12px; background: #fafafa;"
                                      placeholder=".cert-overlay-name { font-size: 32px; color: #166534; }"><?= esc(old('payload.css', $payload['css'] ?? '')) ?></textarea>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Posisi QR Code</label>
                            <select name="payload[qr_position]" class="form-select radius-8">
                                <?php $currentQrPos = old('payload.qr_position', $payload['qr_position'] ?? 'right'); ?>
                                <option value="right" <?= $currentQrPos === 'right'  ? 'selected' : '' ?>>Kanan Bawah (Default)</option>
                                <option value="bottom" <?= $currentQrPos === 'bottom' ? 'selected' : '' ?>>Tengah Bawah</option>
                            </select>
                            <small class="text-secondary-light">Otomatis diganti QR verifikasi digital.</small>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Status Template</label>
                            <select name="is_active" class="form-select radius-8">
                                <?php $currentActive = (string) (int) old('is_active', $t['is_active'] ?? 1); ?>
                                <option value="1" <?= $currentActive === '1' ? 'selected' : '' ?>>Aktif (Dipakai)</option>
                                <option value="0" <?= $currentActive === '0' ? 'selected' : '' ?>>Nonaktif</option>
                            </select>
                            <small class="text-secondary-light">Hanya template aktif yang dipakai generator.</small>
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
                    <button type="submit" class="btn btn-primary-600 radius-8 px-28 py-11 fw-semibold d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:disk-bold" class="text-lg"></iconify-icon>
                        <?= $isCreate ? 'Simpan &amp; Aktifkan Template' : 'Perbarui Perubahan Template' ?>
                    </button>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Panduan Dimensi & Palet Placeholder Interaktif -->
        <div class="col-xl-4">
            <!-- Card Panduan Ukuran & Dimensi Desain -->
            <div class="card mb-24 border-primary-200">
                <div class="card-header bg-primary-50 py-16 px-20 border-bottom border-primary-100">
                    <h6 class="text-md fw-bold mb-0 text-primary-700 d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:ruler-angular-bold" class="text-lg"></iconify-icon>
                        Panduan Dimensi Desain Template
                    </h6>
                </div>
                <div class="card-body p-20">
                    <div class="alert alert-primary-50 border-0 p-12 radius-8 mb-16 text-xs text-primary-800">
                        <iconify-icon icon="solar:info-circle-bold" class="text-base align-middle me-1"></iconify-icon>
                        Standar penerbitan sertifikat resmi BegawiPengadaan menggunakan format <strong>A4 Landscape (Mendatar)</strong>.
                    </div>

                    <table class="table table-sm text-xs mb-16">
                        <tbody>
                            <tr>
                                <td class="text-secondary-light fw-medium py-6">Format Kertas:</td>
                                <td class="fw-bold py-6 text-end">A4 Landscape</td>
                            </tr>
                            <tr>
                                <td class="text-secondary-light fw-medium py-6">Dimensi Fisik:</td>
                                <td class="fw-bold py-6 text-end">297 mm &times; 210 mm</td>
                            </tr>
                            <tr>
                                <td class="text-secondary-light fw-medium py-6">Rasio Desain:</td>
                                <td class="fw-bold py-6 text-end">1.414 : 1 (&radic;2)</td>
                            </tr>
                            <tr class="table-success">
                                <td class="text-success-800 fw-bold py-6">Resolusi 300 DPI (Cetak):</td>
                                <td class="text-success-800 fw-bold py-6 text-end">3508 &times; 2480 px</td>
                            </tr>
                            <tr>
                                <td class="text-secondary-light fw-medium py-6">Resolusi 150 DPI (Web/PDF):</td>
                                <td class="fw-bold py-6 text-end">1754 &times; 1240 px</td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="border-top pt-12">
                        <h6 class="text-xs fw-bold text-neutral-800 mb-8">💡 Langkah Membuat di Canva / Figma:</h6>
                        <ol class="text-xs text-secondary-light ps-16 mb-0" style="line-height: 1.6;">
                            <li>Buka Canva / Figma, buat Custom Size: <strong>3508 &times; 2480 px</strong>.</li>
                            <li>Rancang bingkai ornamen, logo Pemprov Lampung, dan logo Biro PBJ.</li>
                            <li><strong>Kosongkan area tengah</strong> untuk penempatan otomatis Nama Peserta, NIP, dan Judul Kegiatan.</li>
                            <li><strong>Kosongkan area bawah</strong> (kiri untuk tanda tangan pejabat &amp; kanan untuk QR Code).</li>
                            <li>Ekspor hasil desain sebagai <strong>PNG (High Quality)</strong> lalu unggah di form ini.</li>
                        </ol>
                    </div>
                </div>
            </div>

            <!-- Card Palet Placeholder Interaktif (Click to Copy) -->
            <div class="card sticky-top" style="top: 80px; z-index: 5;">
                <div class="card-header border-bottom py-16 px-20 d-flex align-items-center justify-content-between">
                    <h6 class="text-md fw-bold mb-0 d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:tag-bold" class="text-primary-600 text-lg"></iconify-icon>
                        Daftar Placeholder Resmi
                    </h6>
                    <span class="badge bg-neutral-100 text-neutral-700 px-8 py-4 radius-4 text-xs">Klik untuk Salin</span>
                </div>
                <div class="card-body p-20" style="max-height: 520px; overflow-y: auto;">
                    <p class="text-xs text-secondary-light mb-16">
                        Klik pada badge placeholder di bawah untuk menyalin kode tag langsung ke clipboard atau menyisipkannya ke editor.
                    </p>

                    <div id="copyToast" class="alert alert-success py-8 px-12 radius-8 text-xs mb-16 d-none align-items-center gap-2">
                        <iconify-icon icon="solar:check-circle-bold" class="text-base"></iconify-icon>
                        <span id="copyToastText">Placeholder berhasil disalin!</span>
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
                                         title="Klik untuk menyalin: <?= esc($item['tag']) ?>">
                                        <div>
                                            <code class="fw-bold text-primary-600 text-xs"><?= esc($item['tag']) ?></code>
                                            <div class="text-xs text-secondary-light mt-1"><?= esc($item['label']) ?></div>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-icon btn-outline-neutral-300 radius-4 p-4 text-neutral-600">
                                            <iconify-icon icon="solar:copy-bold" class="text-sm"></iconify-icon>
                                        </button>
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

<!-- Preset Layout Strings for JavaScript Insertion -->
<div id="presetOverlayContent" class="d-none"><?= esc((new \App\Services\CertificateTemplateRenderer())->overlayBody()) ?></div>
<div id="presetClassicContent" class="d-none"><?= esc('<div class="cert-frame-default">
  <div class="cert-frame-inner">
    <h1>SERTIFIKAT KELULUSAN</h1>
    <p class="cert-intro">Diberikan secara resmi kepada:</p>
    <div class="cert-participant-name">{{nama_peserta}}</div>
    <p class="cert-participant-nip">NIP: {{nip}} &bull; {{instansi}}</p>

    <p class="cert-event-title">
      Telah memenuhi standar evaluasi dan dinyatakan <strong>{{predikat}}</strong> dalam kegiatan:<br>
      <strong>{{judul_kegiatan}}</strong>
    </p>

    <p class="cert-event-meta">
      Beban Belajar: <strong>{{jam_pelajaran}}</strong> &bull; Periode: {{periode_kegiatan}}
    </p>

    <div class="cert-meta">
      <div class="cert-meta-cell label">
        <p class="cert-meta-label">Nomor Sertifikat</p>
        <p class="cert-meta-value cert-number">{{nomor_sertifikat}}</p>
        <p class="cert-meta-label">Tanggal Terbit</p>
        <p class="cert-meta-value">{{tanggal_terbit}}</p>
        <p class="cert-meta-label">Pejabat Pengesah</p>
        <p class="cert-meta-value"><strong>{{penandatangan_nama}}</strong><br><small>{{penandatangan_jabatan}}</small></p>
      </div>
      <div class="cert-meta-cell qr">
        __QR_BLOCK__
        <p class="cert-verify">Verifikasi Keabsahan Digital:<br><a href="{{url_verifikasi}}">{{url_verifikasi}}</a></p>
      </div>
    </div>
  </div>
</div>') ?></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const editor = document.getElementById('bodyHtmlEditor');
    const toast = document.getElementById('copyToast');
    const toastText = document.getElementById('copyToastText');
    let toastTimeout = null;

    // 1. File Upload Image Preview
    const fileInput = document.getElementById('backgroundFileInput');
    const previewBox = document.getElementById('newFilePreviewBox');
    const previewImg = document.getElementById('newFilePreviewImg');

    if (fileInput && previewBox && previewImg) {
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(evt) {
                    previewImg.src = evt.target.result;
                    previewBox.classList.remove('d-none');
                };
                reader.readAsDataURL(file);
            } else {
                previewBox.classList.add('d-none');
            }
        });
    }

    // 2. Interactive Click-to-Copy Placeholder Palette
    document.querySelectorAll('.placeholder-item').forEach(function(item) {
        item.addEventListener('click', function() {
            const tag = this.getAttribute('data-tag');
            if (!tag) return;

            // Salin ke Clipboard
            navigator.clipboard.writeText(tag).then(function() {
                showToast('Placeholder ' + tag + ' disalin ke clipboard!');
            }).catch(function() {
                showToast('Gagal menyalin ' + tag);
            });

            // Jika editor fokus atau ada, sisipkan di posisi kursor
            if (editor) {
                const start = editor.selectionStart || 0;
                const end = editor.selectionEnd || 0;
                const text = editor.value;
                editor.value = text.substring(0, start) + tag + text.substring(end);
                editor.selectionStart = editor.selectionEnd = start + tag.length;
                editor.focus();
            }
        });
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

    // Dynamic Scope & Event switching
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

    if (sourceTypeSelect) {
        sourceTypeSelect.addEventListener('change', updateScopeVisibility);
    }
    scopeRadios.forEach(function(r) {
        r.addEventListener('change', updateScopeVisibility);
    });

    if (selectEvent && templateNameInput) {
        selectEvent.addEventListener('change', function() {
            const opt = selectEvent.options[selectEvent.selectedIndex];
            if (opt && opt.dataset.title && (templateNameInput.value.trim() === '' || templateNameInput.value.indexOf('Template Sertifikat -') === 0)) {
                templateNameInput.value = 'Template Sertifikat - ' + opt.dataset.title;
            }
        });
    }

    // 3. Preset Buttons
    const btnOverlay = document.getElementById('btnPresetOverlay');
    const btnClassic = document.getElementById('btnPresetClassic');
    const btnClear = document.getElementById('btnPresetClear');

    const overlayContent = document.getElementById('presetOverlayContent')?.textContent || '';
    const classicContent = document.getElementById('presetClassicContent')?.textContent || '';

    if (btnOverlay && editor) {
        btnOverlay.addEventListener('click', function() {
            if (editor.value.trim() !== '' && !confirm('Muat Preset Overlay? Isi editor saat ini akan ditimpa.')) {
                return;
            }
            editor.value = overlayContent.trim();
            showToast('Preset Overlay Background berhasil dimuat.');
        });
    }

    if (btnClassic && editor) {
        btnClassic.addEventListener('click', function() {
            if (editor.value.trim() !== '' && !confirm('Muat Preset Sertifikat Standar? Isi editor saat ini akan ditimpa.')) {
                return;
            }
            editor.value = classicContent.trim();
            showToast('Preset Sertifikat Standar berhasil dimuat.');
        });
    }

    if (btnClear && editor) {
        btnClear.addEventListener('click', function() {
            if (confirm('Kosongkan isi body HTML template? (Sistem akan menggunakan layout otomatis bawaan)')) {
                editor.value = '';
                showToast('Editor dikosongkan.');
            }
        });
    }
});
</script>
<?= $this->endSection() ?>
