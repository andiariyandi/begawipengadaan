<?php
/**
 * Lembar Daftar Hadir Resmi Event PBJ (Print-Ready Roster)
 * Standar Administrasi Biro PBJ Sekretariat Daerah Pemprov Lampung
 */
$e = $event ?? [];
$participants = $participants ?? [];
$stats = $stats ?? [];
$operatorName = $operatorName ?? 'Operator PBJ Pemprov Lampung';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Hadir Resmi: <?= esc($e['title'] ?? 'Event PBJ') ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            background-color: #f8fafc;
            color: #1e293b;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .roster-sheet {
            max-width: 1050px;
            margin: 2rem auto;
            background: #ffffff;
            padding: 3rem;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        }
        .kop-instansi {
            border-bottom: 3px double #0f172a;
            padding-bottom: 12px;
            margin-bottom: 24px;
        }
        .kop-logo {
            width: 80px;
            height: auto;
        }
        .table-roster th, .table-roster td {
            border: 1px solid #334155 !important;
            padding: 8px 10px;
            vertical-align: middle;
            font-size: 0.85rem;
        }
        .table-roster th {
            background-color: #f1f5f9 !important;
            font-weight: 700;
            text-align: center;
        }
        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            .roster-sheet {
                box-shadow: none !important;
                padding: 0 !important;
                margin: 0 !important;
                max-width: 100% !important;
                border-radius: 0 !important;
            }
            @page {
                size: A4 landscape;
                margin: 15mm 12mm 15mm 12mm;
            }
        }
    </style>
</head>
<body>

<div class="container-fluid py-3">
    <!-- Top Action Bar (Screen Only) -->
    <div class="roster-sheet py-3 px-4 mb-3 d-flex justify-content-between align-items-center no-print">
        <a href="<?= route_to('workspace.operator.events.participants', (string) ($e['id'] ?? 0)) ?>" class="btn btn-outline-secondary rounded-pill px-3">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Antrean
        </a>
        <div class="d-flex align-items-center gap-2">
            <button type="button" onclick="window.print()" class="btn btn-primary rounded-pill px-4 fw-semibold shadow-sm">
                <i class="fas fa-print me-1"></i> Cetak Dokumen / Simpan PDF
            </button>
        </div>
    </div>

    <!-- Main Official Sheet -->
    <div class="roster-sheet">
        <!-- Kop Surat Resmi -->
        <div class="kop-instansi d-flex align-items-center gap-4">
            <img src="<?= base_url('assets/main/images/logo/lampung.png') ?>" alt="Logo Pemprov Lampung" class="kop-logo" onerror="this.onerror=null;this.src='https://upload.wikimedia.org/wikipedia/commons/d/dd/Lampung_coa.png';">
            <div class="text-center flex-grow-1">
                <h6 class="text-uppercase fw-bold mb-0 text-muted" style="letter-spacing: 1px; font-size: 0.85rem;">Pemerintah Provinsi Lampung</h6>
                <h4 class="text-uppercase fw-bold mb-0" style="color: #082f5c; letter-spacing: 0.5px;">Sekretariat Daerah</h4>
                <h5 class="text-uppercase fw-bold mb-1" style="color: #082f5c;">Biro Pengadaan Barang dan Jasa</h5>
                <p class="mb-0 small text-muted" style="font-size: 0.75rem;">
                    Jl. Wolter Monginsidi No. 69, Teluk Betung, Kota Bandar Lampung, Lampung 35215 &middot; Laman: lpse.lampungprov.go.id
                </p>
            </div>
        </div>

        <!-- Judul Dokumen -->
        <div class="text-center mb-4">
            <h5 class="fw-bold text-uppercase text-decoration-underline mb-1" style="color: #082f5c;">
                Daftar Hadir Resmi Peserta Kegiatan PBJ
            </h5>
            <small class="text-muted">Nomor Agenda: REG-PBJ/<?= date('Y') ?>/EVT-<?= sprintf('%04d', (int) ($e['id'] ?? 0)) ?></small>
        </div>

        <!-- Metadata Kegiatan -->
        <div class="row g-2 mb-4 p-3 bg-light rounded-3 border" style="font-size: 0.82rem;">
            <div class="col-md-6">
                <div class="d-flex">
                    <span class="text-muted" style="width: 140px;">Nama Kegiatan</span>
                    <strong>: <?= esc($e['title'] ?? '-') ?></strong>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex">
                    <span class="text-muted" style="width: 140px;">Tanggal Pelaksanaan</span>
                    <span>: <?= !empty($e['start_at']) ? date('d F Y', strtotime($e['start_at'])) : '-' ?></span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex">
                    <span class="text-muted" style="width: 140px;">Tipe / Moda</span>
                    <span>: <?= esc(ucfirst((string) ($e['type'] ?? 'Webinar'))) ?> (<?= esc(ucfirst((string) ($e['mode'] ?? 'Online'))) ?>)</span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex">
                    <span class="text-muted" style="width: 140px;">Waktu Kegiatan</span>
                    <span>: <?= !empty($e['start_at']) ? date('H:i', strtotime($e['start_at'])) : '09:00' ?> s.d. <?= !empty($e['end_at']) ? date('H:i', strtotime($e['end_at'])) . ' WIB' : 'Selesai' ?></span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex">
                    <span class="text-muted" style="width: 140px;">Lokasi / Tautan</span>
                    <span class="text-truncate">: <?= esc($e['location_or_link'] ?? 'Portal Belajar PBJ Lampung') ?></span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex">
                    <span class="text-muted" style="width: 140px;">Tingkat Kehadiran</span>
                    <strong class="text-success">: <?= (int) ($stats['present'] ?? 0) ?> / <?= (int) ($stats['total'] ?? 0) ?> Hadir (<?= (float) ($stats['attendance_rate'] ?? 0) ?>%)</strong>
                </div>
            </div>
        </div>

        <!-- Tabel Daftar Hadir -->
        <table class="table table-roster w-100 mb-4">
            <thead>
                <tr>
                    <th style="width: 35px;">No.</th>
                    <th style="width: 180px;">Nama Lengkap</th>
                    <th style="width: 130px;">NIP / Identitas</th>
                    <th style="width: 180px;">Instansi / OPD / Perusahaan</th>
                    <th style="width: 140px;">Jabatan</th>
                    <th style="width: 110px;">Status Presensi</th>
                    <th style="width: 110px;">Waktu Check-In</th>
                    <th style="width: 110px;">Tanda Tangan</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($participants)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">Belum ada peserta terdaftar pada kegiatan ini.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($participants as $idx => $p): ?>
                        <?php
                        $st = $p['attendance_status'] ?? null;
                        $stLabel = match ($st) {
                            'present' => 'Hadir Sah',
                            'excused' => 'Izin Dinas',
                            'absent'  => 'Tidak Hadir',
                            default   => 'Belum Hadir',
                        };
                        ?>
                        <tr>
                            <td class="text-center"><?= $idx + 1 ?></td>
                            <td>
                                <strong><?= esc($p['user_name']) ?></strong>
                                <small class="d-block text-muted" style="font-size: 0.72rem;"><?= esc($p['user_email']) ?></small>
                            </td>
                            <td><?= esc($p['nip'] ?? '-') ?></td>
                            <td><?= esc($p['institution'] ?? '-') ?></td>
                            <td><?= esc($p['position'] ?? '-') ?></td>
                            <td class="text-center">
                                <?= esc($stLabel) ?>
                            </td>
                            <td class="text-center" style="font-size: 0.75rem;">
                                <?= !empty($p['attendance_recorded_at']) ? date('H:i:s', strtotime($p['attendance_recorded_at'])) : '-' ?>
                            </td>
                            <td class="text-center">
                                <?php if ($st === 'present'): ?>
                                    <span class="text-success fw-bold" style="font-size: 0.75rem;">&#x2714; TERVERIFIKASI</span>
                                <?php else: ?>
                                    <span class="text-muted" style="font-size: 0.75rem;">&ndash;</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Kolom Pengesahan Tanda Tangan -->
        <div class="row mt-5 pt-3" style="page-break-inside: avoid; font-size: 0.85rem;">
            <div class="col-6">
                <p class="text-muted mb-1">Catatan Verifikator:</p>
                <div class="p-2 border rounded text-muted small" style="min-height: 70px;">
                    Daftar hadir ini digenerate secara resmi melalui Portal BegawiPengadaan Provinsi Lampung untuk keperluan pertanggungjawaban kegiatan.
                </div>
            </div>
            <div class="col-6 text-end">
                <p class="mb-1">Bandar Lampung, <?= date('d F Y') ?></p>
                <p class="fw-bold mb-5 pb-4">Petugas / Operator Verifikator,</p>
                <strong class="d-block text-decoration-underline text-navy"><?= esc($operatorName) ?></strong>
                <span class="text-muted small">Biro PBJ Sekretariat Daerah Provinsi Lampung</span>
            </div>
        </div>
    </div>
</div>

</body>
</html>
