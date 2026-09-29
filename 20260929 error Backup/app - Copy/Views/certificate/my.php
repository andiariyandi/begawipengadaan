<?= $this->extend('layout/layout1'); ?>

<?= $this->section('content'); ?>

<div class="card h-100 p-0 radius-12">
    <div class="card-header border-bottom bg-base py-16 px-24">
        <h6 class="mb-0">Sertifikat Saya</h6>
    </div>
    <div class="card-body p-24">
        <?php if (empty($certificates)): ?>
            <p class="text-secondary-light">Anda belum memiliki sertifikat. Selesaikan pelatihan hingga lulus quiz untuk mendapat sertifikat.</p>
        <?php else: ?>
            <div class="table-responsive scroll-sm">
                <table class="table bordered-table sm-table mb-0">
                    <thead>
                        <tr>
                            <th>Program</th>
                            <th>Nomor Sertifikat</th>
                            <th class="text-center">Tanggal Terbit</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($certificates as $cert): ?>
                            <tr>
                                <td><?= esc($cert['course_title']) ?></td>
                                <td><?= esc($cert['certificate_number']) ?></td>
                                <td class="text-center"><?= esc(date('d M Y', strtotime($cert['issued_date']))) ?></td>
                                <td class="text-center">
                                    <span class="<?= $cert['status'] === 'issued' ? 'bg-success-focus text-success-600 border-success-main' : 'bg-danger-focus text-danger-600 border-danger-main' ?> border px-16 py-4 radius-4 text-sm fw-medium">
                                        <?= $cert['status'] === 'issued' ? 'Aktif' : 'Dicabut' ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <?php if ($cert['status'] === 'issued'): ?>
                                        <a href="<?= route_to('certificate.download', $cert['id']) ?>" class="bg-info-focus text-info-600 w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle mx-auto">
                                            <iconify-icon icon="mdi:download"></iconify-icon>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection(); ?>