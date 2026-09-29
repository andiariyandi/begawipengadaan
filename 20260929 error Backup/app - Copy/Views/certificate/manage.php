<?= $this->extend('layout/layout1'); ?>

<?= $this->section('content'); ?>

<div class="card h-100 p-0 radius-12">
    <div class="card-header border-bottom bg-base py-16 px-24">
        <h6 class="mb-0">Kelola Sertifikat</h6>
    </div>
    <div class="card-body p-24">
        <?php if (session()->getFlashdata('message')): ?>
            <div class="alert alert-success radius-12 mb-16"><?= esc(session()->getFlashdata('message')) ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger radius-12 mb-16"><?= esc(session()->getFlashdata('error')) ?></div>
        <?php endif; ?>

        <?php if (empty($certificates)): ?>
            <p class="text-secondary-light">Belum ada sertifikat yang diterbitkan.</p>
        <?php else: ?>
            <div class="table-responsive scroll-sm">
                <table class="table bordered-table sm-table mb-0">
                    <thead>
                        <tr>
                            <th>Peserta</th>
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
                                <td><?= esc($cert['username']) ?></td>
                                <td><?= esc($cert['course_title']) ?></td>
                                <td><?= esc($cert['certificate_number']) ?></td>
                                <td class="text-center"><?= esc(date('d M Y', strtotime($cert['issued_date']))) ?></td>
                                <td class="text-center">
                                    <span class="<?= $cert['status'] === 'issued' ? 'bg-success-focus text-success-600 border-success-main' : 'bg-danger-focus text-danger-600 border-danger-main' ?> border px-16 py-4 radius-4 text-sm fw-medium">
                                        <?= $cert['status'] === 'issued' ? 'Aktif' : 'Dicabut' ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center gap-10 justify-content-center">
                                        <a href="<?= route_to('certificate.download', $cert['id']) ?>" class="bg-info-focus text-info-600 w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle">
                                            <iconify-icon icon="mdi:download"></iconify-icon>
                                        </a>
                                        <?php if ($cert['status'] === 'issued'): ?>
                                            <form action="<?= route_to('certificate.revoke', $cert['id']) ?>" method="post" onsubmit="return promptRevokeReason(this)">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="reason" value="">
                                                <button type="submit" class="bg-danger-focus text-danger-600 w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle border-0">
                                                    <iconify-icon icon="mdi:cancel"></iconify-icon>
                                                </button>
                                            </form>
                                        <?php endif; ?>
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

<?= $this->endSection(); ?>

<?= $this->section('script'); ?>
<script>
    function promptRevokeReason(form) {
        const reason = prompt('Alasan pencabutan sertifikat ini:');
        if (!reason) {
            return false;
        }
        form.querySelector('input[name="reason"]').value = reason;
        return confirm('Yakin mencabut sertifikat ini? Tindakan ini akan tercatat di audit log.');
    }
</script>
<?= $this->endSection(); ?>