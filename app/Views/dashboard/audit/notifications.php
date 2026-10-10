<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Log Notifikasi') ?></h6>
        <p class="text-secondary-light mb-0">Riwayat dan pemantauan pengiriman notifikasi sistem kepada pengguna.</p>
    </div>
</div>

<div class="row row-cols-xxxl-4 row-cols-lg-2 row-cols-1 gy-4">
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-1 h-100">
            <div class="card-body p-20">
                <p class="fw-medium text-primary-light mb-1">Total Notifikasi</p>
                <h6 class="mb-0">1.428</h6>
                <p class="text-sm text-primary-light mt-12 mb-0">Terkirim seluruh kanal</p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-2 h-100">
            <div class="card-body p-20">
                <p class="fw-medium text-primary-light mb-1">Berhasil Terkirim</p>
                <h6 class="mb-0">1.412</h6>
                <p class="text-sm text-primary-light mt-12 mb-0">Tingkat sukses 98.8%</p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-3 h-100">
            <div class="card-body p-20">
                <p class="fw-medium text-primary-light mb-1">Hari Ini</p>
                <h6 class="mb-0">24</h6>
                <p class="text-sm text-primary-light mt-12 mb-0">Notifikasi terjadwal & instan</p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-5 h-100">
            <div class="card-body p-20">
                <p class="fw-medium text-primary-light mb-1">Antrean Tertunda</p>
                <h6 class="mb-0">0</h6>
                <p class="text-sm text-primary-light mt-12 mb-0">Antrean notifikasi bersih</p>
            </div>
        </div>
    </div>
</div>

<div class="card mt-24">
    <div class="card-body p-24">
        <div class="d-flex flex-wrap justify-content-between gap-12 mb-20">
            <h6 class="text-lg mb-0">Daftar Notifikasi Terbaru</h6>
            <div class="d-flex gap-12">
                <select class="form-select form-select-sm w-auto radius-8">
                    <option>Semua Kategori</option>
                    <option>Akun & Verifikasi</option>
                    <option>Kelas & Pembelajaran</option>
                    <option>Event & Agenda</option>
                </select>
                <input type="search" class="form-control form-control-sm w-auto radius-8" placeholder="Cari notifikasi...">
            </div>
        </div>
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0">
                <thead>
                    <tr>
                        <th>Penerima</th>
                        <th>Subjek / Pesan</th>
                        <th>Kategori</th>
                        <th>Kanal</th>
                        <th>Waktu</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ([
                        ['recipient' => 'Budi Santoso', 'subject' => 'Pendaftaran Akun ASN Berhasil Diterima', 'cat' => 'Akun', 'channel' => 'In-App & Email', 'time' => '10 menit yang lalu', 'status' => 'sent'],
                        ['recipient' => 'Dewi Lestari', 'subject' => 'Pengingat Pre-Test Kelas Pengadaan Barang/Jasa', 'cat' => 'Kelas', 'channel' => 'In-App', 'time' => '1 jam yang lalu', 'status' => 'sent'],
                        ['recipient' => 'Ahmad Fauzi', 'subject' => 'Penerbitan Sertifikat Kelulusan Pelatihan', 'cat' => 'Sertifikat', 'channel' => 'In-App & Email', 'time' => '3 jam yang lalu', 'status' => 'sent'],
                    ] as $notif): ?>
                    <tr>
                        <td><h6 class="text-md fw-medium mb-0"><?= esc($notif['recipient']) ?></h6></td>
                        <td><?= esc($notif['subject']) ?></td>
                        <td><span class="badge bg-neutral-100 text-neutral-800 radius-6"><?= esc($notif['cat']) ?></span></td>
                        <td><?= esc($notif['channel']) ?></td>
                        <td><?= esc($notif['time']) ?></td>
                        <td class="text-center">
                            <span class="badge bg-success-focus text-success-main border-success px-10 py-4 radius-6">Terkirim</span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
