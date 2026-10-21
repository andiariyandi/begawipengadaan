<?= $this->extend('main/workspace/layouts/workspace') ?>

<?= $this->section('pageStyles') ?>
<style>
/* Styling Cetak Khusus Transkrip Akademik Resmi */
@media print {
    body {
        background: #ffffff !important;
        color: #000000 !important;
        font-size: 11pt !important;
    }
    .ws-header, .ws-sidebar, .ws-footer, .no-print, .btn, .breadcrumb, nav {
        display: none !important;
    }
    .dashboard-main-body, .dashboard-main, .container-fluid, .content-wrapper {
        margin: 0 !important;
        padding: 0 !important;
        width: 100% !important;
        box-shadow: none !important;
    }
    .transcript-sheet {
        box-shadow: none !important;
        border: none !important;
        padding: 0 !important;
        margin: 0 !important;
    }
    .transcript-table th, .transcript-table td {
        border-color: #333333 !important;
    }
}

.transcript-sheet {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 36px 42px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
}

.transcript-kop {
    border-bottom: 3px double #0f172a;
    padding-bottom: 16px;
    margin-bottom: 24px;
}

.transcript-table {
    width: 100%;
    margin-bottom: 20px;
    font-size: 13px;
}

.transcript-table th {
    background: #f8fafc;
    color: #1e293b;
    font-weight: 700;
    border-bottom: 2px solid #cbd5e1;
    padding: 10px 12px;
}

.transcript-table td {
    padding: 9px 12px;
    border-bottom: 1px solid #e2e8f0;
    color: #334155;
}

.score-badge-pill {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 6px;
    font-weight: 700;
    font-size: 12px;
}

.grade-box-highlight {
    border-radius: 12px;
    padding: 20px;
    border: 2px dashed #0c5aa6;
    background: #f0f7ff;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$fmtDate = static function (?string $date): string {
    return ! empty($date) ? date('d F Y', strtotime($date)) : '—';
};
?>

<!-- Action Bar (Hidden on Print) -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24 no-print">
    <div>
        <h5 class="fw-bold mb-1" style="color: #082f5c;">Transkrip Akademik Pembelajaran</h5>
        <p class="text-muted small mb-0">Rapor evaluasi komprehensif pembelajaran pengadaan barang dan jasa.</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="<?= route_to('workspace.participant.class.detail', (string) $class['id']) ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-2 fw-semibold">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Kelas
        </a>
        <button type="button" class="btn btn-primary btn-sm rounded-pill px-4 py-2 fw-bold shadow-sm" onclick="window.print();">
            <i class="fas fa-print me-1"></i> Cetak / Simpan PDF
        </button>
        <?php if (! empty($certificate['certificate_number'])): ?>
            <a href="<?= route_to('workspace.participant.certificate.download', (string) $certificate['id']) ?>" class="btn btn-success btn-sm rounded-pill px-3 py-2 fw-bold shadow-sm">
                <i class="fas fa-award me-1"></i> Unduh E-Sertifikat
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- Lembar Transkrip Resmi (Official Transcript Sheet) -->
<div class="transcript-sheet">

    <!-- Kop Surat Resmi -->
    <div class="transcript-kop">
        <div class="row align-items-center">
            <div class="col-2 text-center">
                <img src="<?= base_url('assets/main/images/resources/logo-1.png') ?>" alt="Logo Pemprov Lampung" class="img-fluid" style="max-height: 80px;" onerror="this.style.display='none';">
            </div>
            <div class="col-8 text-center">
                <h6 class="text-uppercase fw-bold mb-0 letter-spacing-1" style="font-size: 14px; color: #0f172a;">Pemerintah Provinsi Lampung</h6>
                <h5 class="text-uppercase fw-bold mb-0" style="font-size: 17px; color: #082f5c;">Biro Pengadaan Barang dan Jasa</h5>
                <p class="text-muted small mb-1" style="font-size: 11px;">Gedung Kantor Gubernur Lampung, Jl. Wolter Monginsidi No.69, Teluk Betung, Bandar Lampung</p>
                <div class="d-inline-block px-3 py-1 radius-4 bg-light border border-neutral-300">
                    <strong class="text-uppercase text-dark" style="font-size: 13px; letter-spacing: 0.5px;">KARTU HASIL EVALUASI PEMBELAJARAN (TRANSKRIP AKADEMIK)</strong>
                </div>
            </div>
            <div class="col-2 text-center">
                <div class="p-2 border rounded-3 bg-light text-center">
                    <small class="d-block text-muted" style="font-size: 9px; font-weight: 700;">DOKUMEN RESMI</small>
                    <span class="badge bg-primary text-white rounded-pill px-2 py-1 font-monospace" style="font-size: 10px;">PBJ-<?= date('Y') ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Informasi Peserta & Kelas -->
    <div class="row g-3 mb-24 pb-20 border-bottom">
        <div class="col-md-6">
            <table class="table table-sm table-borderless mb-0 text-sm">
                <tr>
                    <td class="text-muted fw-semibold ps-0 py-1" style="width: 140px;">Nama Peserta</td>
                    <td class="fw-bold text-dark py-1">: <?= esc($student['name'] ?? '—') ?></td>
                </tr>
                <tr>
                    <td class="text-muted fw-semibold ps-0 py-1">NIP / NIK</td>
                    <td class="fw-semibold text-dark py-1 font-monospace">: <?= esc($student['nip'] ?: ($student['nik'] ?: '—')) ?></td>
                </tr>
                <tr>
                    <td class="text-muted fw-semibold ps-0 py-1">Instansi / Unit Kerja</td>
                    <td class="text-dark py-1">: <?= esc($student['instansi'] ?: 'Pemerintah Provinsi Lampung') ?></td>
                </tr>
                <tr>
                    <td class="text-muted fw-semibold ps-0 py-1">Kategori Pengguna</td>
                    <td class="text-dark py-1 text-capitalize">: <?= esc($student['profile_type'] ?? 'ASN') ?></td>
                </tr>
            </table>
        </div>
        <div class="col-md-6">
            <table class="table table-sm table-borderless mb-0 text-sm">
                <tr>
                    <td class="text-muted fw-semibold ps-0 py-1" style="width: 140px;">Nama Kelas</td>
                    <td class="fw-bold text-primary py-1">: <?= esc($class['name'] ?? '—') ?></td>
                </tr>
                <tr>
                    <td class="text-muted fw-semibold ps-0 py-1">Kode Kelas / Course</td>
                    <td class="text-dark py-1">: <span class="font-monospace fw-bold"><?= esc($class['code'] ?? '—') ?></span> &bull; <?= esc($class['course_title'] ?? '—') ?></td>
                </tr>
                <tr>
                    <td class="text-muted fw-semibold ps-0 py-1">Program Induk</td>
                    <td class="text-dark py-1">: <?= esc($class['program_title'] ?: 'Arena Belajar PBJ') ?></td>
                </tr>
                <tr>
                    <td class="text-muted fw-semibold ps-0 py-1">Waktu Pelaksanaan</td>
                    <td class="text-dark py-1">: <?= $fmtDate($class['start_at']) ?> s/d <?= $fmtDate($class['end_at']) ?></td>
                </tr>
            </table>
        </div>
    </div>

    <!-- 1. Evaluasi Formatif: Kuis Modul -->
    <div class="mb-24">
        <h6 class="fw-bold text-dark mb-12 d-flex align-items-center gap-2" style="font-size: 14px;">
            <i class="fas fa-tasks text-primary"></i>
            <span>I. Evaluasi Formatif (Kuis-Kuis Bab &amp; Modul Kurikulum)</span>
        </h6>
        <?php if (! empty($quizRows)): ?>
            <div class="table-responsive">
                <table class="transcript-table table table-bordered mb-0">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">No</th>
                            <th>Modul Kurikulum</th>
                            <th>Judul Kuis Evaluasi</th>
                            <th class="text-center" style="width: 100px;">KKM</th>
                            <th class="text-center" style="width: 100px;">Percobaan</th>
                            <th class="text-center" style="width: 110px;">Nilai Terbaik</th>
                            <th class="text-center" style="width: 130px;">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($quizRows as $idx => $q): ?>
                            <tr>
                                <td class="text-center fw-semibold"><?= $idx + 1 ?></td>
                                <td><?= esc($q['module_title']) ?></td>
                                <strong><?= esc($q['title']) ?></strong></td>
                                <td class="text-center"><?= (int) $q['passing_grade'] ?></td>
                                <td class="text-center"><?= (int) $q['attempts'] ?>x</td>
                                <td class="text-center fw-bold <?= $q['score'] !== null && $q['score'] >= $q['passing_grade'] ? 'text-success' : 'text-danger' ?>">
                                    <?= $q['score'] !== null ? number_format($q['score'], 1) : '—' ?>
                                </td>
                                <td class="text-center">
                                    <span class="score-badge-pill <?= $q['status'] === 'Lulus' ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning-emphasis' ?>">
                                        <?= esc($q['status']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <tr class="bg-light fw-bold">
                            <td colspan="5" class="text-end">Rata-Rata Kuis Modul:</td>
                            <td class="text-center text-primary fs-6"><?= number_format($avgQuiz, 1) ?></td>
                            <td class="text-center text-muted small">Bobot: <?= (int) $wQuiz ?>%</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted small fst-italic mb-0">Tidak ada kuis modul terjadwal pada kelas ini.</p>
        <?php endif; ?>
    </div>

    <!-- 2. Evaluasi Praktik & Tugas Mandiri -->
    <?php if (! empty($asgRows)): ?>
        <div class="mb-24">
            <h6 class="fw-bold text-dark mb-12 d-flex align-items-center gap-2" style="font-size: 14px;">
                <i class="fas fa-file-alt text-info"></i>
                <span>II. Evaluasi Praktik (Penugasan Mandiri &amp; Lembar Kerja PBJ)</span>
            </h6>
            <div class="table-responsive">
                <table class="transcript-table table table-bordered mb-0">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">No</th>
                            <th>Modul &amp; Penugasan</th>
                            <th>Tanggal Pengiriman</th>
                            <th class="text-center" style="width: 110px;">Nilai Praktik</th>
                            <th>Catatan &amp; Evaluasi Pengajar</th>
                            <th class="text-center" style="width: 120px;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($asgRows as $idx => $a): ?>
                            <tr>
                                <td class="text-center fw-semibold"><?= $idx + 1 ?></td>
                                <td>
                                    <strong><?= esc($a['title']) ?></strong>
                                    <small class="text-muted d-block"><?= esc($a['module_title']) ?></small>
                                </td>
                                <td><?= $fmtDate($a['submitted_at']) ?></td>
                                <td class="text-center fw-bold text-primary fs-6">
                                    <?= $a['score'] !== null ? number_format($a['score'], 1) : '—' ?>
                                </td>
                                <td class="small text-muted"><?= esc($a['feedback'] ?: 'Tugas telah ditinjau.') ?></td>
                                <td class="text-center">
                                    <span class="score-badge-pill bg-light border text-dark">
                                        <?= esc($a['status']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <tr class="bg-light fw-bold">
                            <td colspan="3" class="text-end">Rata-Rata Nilai Tugas:</td>
                            <td class="text-center text-primary fs-6"><?= number_format($avgAsg, 1) ?></td>
                            <td colspan="2" class="text-muted small">Bobot: <?= (int) $wAsg ?>%</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>

    <!-- 3. Evaluasi Sumatif (Pre-Test & Post-Test) -->
    <div class="mb-24">
        <h6 class="fw-bold text-dark mb-12 d-flex align-items-center gap-2" style="font-size: 14px;">
            <i class="fas fa-award text-warning"></i>
            <span><?= ! empty($asgRows) ? 'III.' : 'II.' ?> Evaluasi Sumatif (Ujian Masuk Pre-Test &amp; Ujian Akhir Post-Test)</span>
        </h6>
        <div class="table-responsive">
            <table class="transcript-table table table-bordered mb-0">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 50px;">No</th>
                        <th>Komponen Ujian Sumatif</th>
                        <th>Waktu Pengerjaan</th>
                        <th class="text-center" style="width: 100px;">KKM</th>
                        <th class="text-center" style="width: 110px;">Nilai Skor</th>
                        <th class="text-center" style="width: 130px;">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center fw-semibold">1</td>
                        <td>
                            <strong>Pre-Test (Asesmen Kompetensi Awal)</strong>
                            <small class="text-muted d-block">Uji pemahaman prasyarat sebelum memasuki modul pembelajaran</small>
                        </td>
                        <td><?= ! empty($preTest['attempt']['submitted_at']) ? $fmtDate($preTest['attempt']['submitted_at']) : '—' ?></td>
                        <td class="text-center">—</td>
                        <td class="text-center fw-bold text-dark">
                            <?= isset($preTest['attempt']['score']) ? number_format((float)$preTest['attempt']['score'], 1) : '—' ?>
                        </td>
                        <td class="text-center">
                            <span class="score-badge-pill bg-light border text-muted">Formatif Diagnostik</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center fw-semibold">2</td>
                        <td>
                            <strong class="text-primary">Post-Test (Ujian Akhir Kelulusan)</strong>
                            <small class="text-muted d-block">Penentu utama penguasaan kompetensi akhir peserta</small>
                        </td>
                        <td><?= ! empty($postTest['attempt']['submitted_at']) ? $fmtDate($postTest['attempt']['submitted_at']) : '—' ?></td>
                        <td class="text-center fw-semibold"><?= (int) ($class['passing_grade'] ?? 70) ?></td>
                        <td class="text-center fw-bold fs-6 <?= $postScore >= ($class['passing_grade'] ?? 70) ? 'text-success' : 'text-danger' ?>">
                            <?= isset($postTest['attempt']['score']) ? number_format($postScore, 1) : '—' ?>
                        </td>
                        <td class="text-center">
                            <span class="score-badge-pill <?= $postScore >= ($class['passing_grade'] ?? 70) ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' ?>">
                                <?= $postScore >= ($class['passing_grade'] ?? 70) ? 'Lulus KKM' : 'Belum Memenuhi' ?>
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- 4. Rekapitulasi Pembobotan & Hasil Akhir -->
    <div class="grade-box-highlight mb-32">
        <div class="row align-items-center gy-3">
            <div class="col-md-7">
                <h6 class="fw-bold mb-2" style="color: #082f5c;">Rekapitulasi Pembobotan Nilai Akhir</h6>
                <p class="text-muted small mb-3">
                    Rumus Nilai Akhir: <code class="fw-bold text-dark font-monospace">(Kuis &times; <?= (int)$wQuiz ?>%) + (Tugas &times; <?= (int)$wAsg ?>%) + (Post-Test &times; <?= (int)$wPost ?>%)</code>
                </p>
                <div class="d-flex flex-wrap gap-3 text-sm">
                    <div class="bg-white p-2 rounded border">
                        <small class="text-muted d-block">Komponen Kuis (<?= (int)$wQuiz ?>%):</small>
                        <strong class="text-dark"><?= number_format($avgQuiz * ($wQuiz / 100), 2) ?></strong>
                    </div>
                    <?php if ($wAsg > 0): ?>
                        <div class="bg-white p-2 rounded border">
                            <small class="text-muted d-block">Komponen Tugas (<?= (int)$wAsg ?>%):</small>
                            <strong class="text-dark"><?= number_format($avgAsg * ($wAsg / 100), 2) ?></strong>
                        </div>
                    <?php endif; ?>
                    <div class="bg-white p-2 rounded border">
                        <small class="text-muted d-block">Komponen Post-Test (<?= (int)$wPost ?>%):</small>
                        <strong class="text-dark"><?= number_format($postScore * ($wPost / 100), 2) ?></strong>
                    </div>
                </div>
            </div>
            <div class="col-md-5 text-md-end border-start-md ps-md-4">
                <span class="text-muted text-xs text-uppercase fw-bold d-block mb-1">NILAI AKHIR KUMULATIF:</span>
                <div class="display-5 fw-bold <?= $isPassed ? 'text-success' : 'text-danger' ?> mb-1">
                    <?= number_format($finalScore, 1) ?> <span class="fs-5 text-muted fw-normal">/ 100</span>
                </div>
                <div class="mb-2">
                    <span class="badge <?= $isPassed ? 'bg-success' : 'bg-danger' ?> fs-6 px-3 py-1 radius-6">
                        Grade <?= esc($gradeLetter) ?> &bull; <?= esc($predicate) ?>
                    </span>
                </div>
                <small class="text-muted d-block" style="font-size: 11px;">
                    Standar Minimal Kelulusan (KKM): <strong><?= (float) ($class['passing_grade'] ?? 70) ?></strong>
                </small>
            </div>
        </div>
    </div>

    <!-- 5. Tanda Tangan & Verifikasi Keabsahan -->
    <div class="row pt-20 mt-20 border-top align-items-end">
        <div class="col-6">
            <?php if (! empty($certificate['certificate_number'])): ?>
                <div class="p-3 border rounded-3 bg-light" style="max-width: 320px;">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <i class="fas fa-shield-alt text-success fs-5"></i>
                        <strong class="text-dark" style="font-size: 12px;">E-Sertifikat Terverifikasi</strong>
                    </div>
                    <small class="text-muted d-block" style="font-size: 10px;">Nomor Dokumen:</small>
                    <code class="fw-bold text-dark font-monospace" style="font-size: 11px;"><?= esc($certificate['certificate_number']) ?></code>
                    <small class="text-muted d-block mt-1" style="font-size: 10px;">Diterbitkan pada <?= $fmtDate($certificate['issued_at']) ?></small>
                </div>
            <?php else: ?>
                <div class="p-3 border rounded-3 bg-light text-muted" style="max-width: 320px; font-size: 11px;">
                    <i class="fas fa-info-circle me-1"></i> Transkrip resmi internal diterbitkan secara elektronik oleh Sistem Informasi BegawiPengadaan Provinsi Lampung.
                </div>
            <?php endif; ?>
        </div>
        <div class="col-6 text-end">
            <p class="mb-0 text-muted" style="font-size: 12px;">Bandar Lampung, <?= date('d F Y') ?></p>
            <p class="fw-bold text-dark mb-4" style="font-size: 12px;">Pengajar Utama / Penanggung Jawab,</p>
            <div style="height: 45px;"></div>
            <?php
            $leadTrainer = ! empty($trainers) ? $trainers[0]['name'] : 'Biro PBJ Pemprov Lampung';
            ?>
            <strong class="text-dark text-decoration-underline d-block" style="font-size: 13px;"><?= esc($leadTrainer) ?></strong>
            <small class="text-muted" style="font-size: 11px;">Pengajar Pengadaan Barang dan Jasa</small>
        </div>
    </div>

</div>
<?= $this->endSection() ?>
