<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Pusat Siaran Email & Antrean') ?></h6>
        <p class="text-secondary-light mb-0">Kelola siaran massal, pengiriman personal, draf terjadwal, dan pantau log pengiriman Google Mail.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="<?= site_url('dashboard/sistem/pengaturan/notification') ?>" class="btn btn-outline-primary-600 radius-8 px-16 py-9 d-inline-flex align-items-center gap-2 text-sm">
            <iconify-icon icon="solar:settings-minimalistic-outline" class="text-lg"></iconify-icon>
            Pengaturan Akun Gmail
        </a>
    </div>
</div>

<!-- Overview Cards -->
<div class="row row-cols-xxxl-4 row-cols-lg-4 row-cols-sm-2 row-cols-1 gy-4 mb-24">
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-1 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Akun Pengirim Gmail</p>
                        <h6 class="mb-0 text-sm fw-semibold text-truncate" style="max-width: 220px;" title="<?= esc($config['smtp_user'] ?: 'Belum Dikonfigurasi') ?>">
                            <?= esc($config['smtp_user'] ?: 'Belum Dikonfigurasi') ?>
                        </h6>
                    </div>
                    <div class="w-44-px h-44-px bg-primary-subtle text-primary-600 rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:letter-bold" class="text-xl"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-xs text-secondary-light mt-12 mb-0 d-flex align-items-center gap-1">
                    <?php if (! empty($config['smtp_user']) && ! empty($config['smtp_pass'])): ?>
                        <span class="badge bg-success-focus text-success-main radius-4 px-8 py-2">✓ Siap Digunakan</span>
                    <?php else: ?>
                        <span class="badge bg-danger-focus text-danger-main radius-4 px-8 py-2">Perlu Sandi Aplikasi</span>
                    <?php endif; ?>
                    <span class="text-secondary-light">smtp.gmail.com:587</span>
                </p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-2 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Antrean Belum Terkirim</p>
                        <h6 class="mb-0 text-2xl fw-bold text-warning-main"><?= number_format($totalPending) ?></h6>
                    </div>
                    <div class="w-44-px h-44-px bg-warning-subtle text-warning-main rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:clock-circle-bold" class="text-xl"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-xs text-secondary-light mt-12 mb-0">
                    <span class="text-primary-600 fw-semibold"><?= number_format($totalReady) ?></span> email telah jatuh tempo / siap kirim
                </p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-3 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Total Riwayat Log</p>
                        <h6 class="mb-0 text-2xl fw-bold text-primary-600"><?= number_format($totalLogs) ?></h6>
                    </div>
                    <div class="w-44-px h-44-px bg-info-subtle text-info-main rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:history-bold" class="text-xl"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-xs text-secondary-light mt-12 mb-0">
                    Tercatat di sistem notifikasi BegawiPengadaan
                </p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-4 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Metode Operasional</p>
                        <h6 class="mb-0 text-sm fw-bold text-secondary-dark">100% Manual Dashboard</h6>
                    </div>
                    <div class="w-44-px h-44-px bg-success-subtle text-success-main rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:shield-check-bold" class="text-xl"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-xs text-secondary-light mt-12 mb-0">
                    Aman dari pemblokiran & kuota Gmail terjaga
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Main Tabs Card -->
<div class="card border radius-12 overflow-hidden">
    <div class="card-header bg-base border-bottom p-0">
        <ul class="nav nav-tabs nav-tabs-primary border-0 px-20 pt-12 gap-12" id="emailTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link text-sm fw-semibold pb-14 px-16 d-flex align-items-center gap-2 <?= $activeTab === 'broadcast' ? 'active' : '' ?>" href="<?= site_url('dashboard/komunikasi/email?tab=broadcast') ?>">
                    <iconify-icon icon="solar:users-group-rounded-linear" class="text-lg"></iconify-icon>
                    1. Susun Siaran Email (Send to Many)
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link text-sm fw-semibold pb-14 px-16 d-flex align-items-center gap-2 <?= $activeTab === 'send_one' ? 'active' : '' ?>" href="<?= site_url('dashboard/komunikasi/email?tab=send_one') ?>">
                    <iconify-icon icon="solar:user-speak-rounded-linear" class="text-lg"></iconify-icon>
                    2. Kirim Email Personal (Send to One)
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link text-sm fw-semibold pb-14 px-16 d-flex align-items-center gap-2 <?= $activeTab === 'queue' ? 'active' : '' ?>" href="<?= site_url('dashboard/komunikasi/email?tab=queue') ?>">
                    <iconify-icon icon="solar:clock-circle-outline" class="text-lg"></iconify-icon>
                    3. Antrean &amp; Terjadwal
                    <?php if ($totalPending > 0): ?>
                        <span class="badge bg-warning-focus text-warning-main radius-4 px-6 py-2 text-xs"><?= $totalPending ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link text-sm fw-semibold pb-14 px-16 d-flex align-items-center gap-2 <?= $activeTab === 'logs' ? 'active' : '' ?>" href="<?= site_url('dashboard/komunikasi/email?tab=logs') ?>">
                    <iconify-icon icon="solar:notes-outline" class="text-lg"></iconify-icon>
                    4. Log Riwayat Pengiriman
                </a>
            </li>
        </ul>
    </div>

    <div class="card-body p-24">
        <!-- =================================================================== -->
        <!-- TAB 1: COMPOSE BROADCAST (SEND TO MANY)                             -->
        <!-- =================================================================== -->
        <?php if ($activeTab === 'broadcast'): ?>
            <form action="<?= site_url('dashboard/komunikasi/email/broadcast') ?>" method="post" id="formBroadcast">
                <?= csrf_field() ?>
                <div class="row gy-20">
                    <div class="col-12">
                        <div class="alert alert-primary bg-primary-50 border-primary-100 text-primary-900 radius-8 p-16 d-flex align-items-start gap-12 mb-0">
                            <iconify-icon icon="solar:info-circle-bold" class="text-2xl flex-shrink-0 text-primary-600 mt-2"></iconify-icon>
                            <div class="text-sm">
                                <strong>Panduan Siaran Email Massal:</strong> Pesan akan dibungkus otomatis dengan kop resmi Pemerintah Provinsi Lampung dan dikirimkan via akun Google Mail resmi. Sesuai kebijakan batas harian Google (~500 email/hari), sistem memproses paket pengiriman secara bertahap dan transparan.
                            </div>
                        </div>
                    </div>

                    <!-- Target Audience -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-sm mb-6" for="target_group">Target Audiens Penerima <span class="text-danger-600">*</span></label>
                        <select name="target_group" id="target_group" class="form-select radius-8" required onchange="handleAudienceChange()">
                            <option value="all">Semua Pengguna Terdaftar &amp; Aktif</option>
                            <option value="asn">Aparatur Sipil Negara (ASN)</option>
                            <option value="provider">Penyedia Barang dan Jasa</option>
                            <option value="academic">Akademisi &amp; Mahasiswa</option>
                            <option value="public">Masyarakat Umum</option>
                            <option value="newsletter">Pelanggan Newsletter Resmi</option>
                            <option value="class">Peserta Kelas Pembelajaran Tertentu</option>
                            <option value="event">Peserta Agenda / Event Tertentu</option>
                        </select>
                        <div class="d-flex align-items-center gap-2 mt-8">
                            <span class="badge bg-primary-focus text-primary-600 radius-4 px-8 py-4 text-xs font-monospace" id="audienceCountPill">
                                Menghitung estimasi penerima...
                            </span>
                        </div>
                    </div>

                    <!-- Conditional Class Selector -->
                    <div class="col-md-6 d-none" id="classSelectorWrapper">
                        <label class="form-label fw-semibold text-sm mb-6" for="class_id">Pilih Kelas Pembelajaran <span class="text-danger-600">*</span></label>
                        <select name="class_id" id="class_id" class="form-select radius-8" onchange="fetchAudienceCount()">
                            <option value="">-- Pilih Kelas --</option>
                            <?php foreach ($classes as $c): ?>
                                <option value="<?= (int) $c['id'] ?>"><?= esc($c['name']) ?> (<?= esc($c['code'] ?: 'PBJ') ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Conditional Event Selector -->
                    <div class="col-md-6 d-none" id="eventSelectorWrapper">
                        <label class="form-label fw-semibold text-sm mb-6" for="event_id">Pilih Agenda / Event <span class="text-danger-600">*</span></label>
                        <select name="event_id" id="event_id" class="form-select radius-8" onchange="fetchAudienceCount()">
                            <option value="">-- Pilih Event --</option>
                            <?php foreach ($events as $ev): ?>
                                <option value="<?= (int) $ev['id'] ?>"><?= esc($ev['title']) ?> (<?= date('d M Y', strtotime($ev['start_at'])) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Subject -->
                    <div class="col-12">
                        <label class="form-label fw-semibold text-sm mb-6" for="subject">Subjek Siaran Email <span class="text-danger-600">*</span></label>
                        <input type="text" name="subject" id="subject" class="form-control radius-8" placeholder="Contoh: Pengumuman Jadwal Pelatihan Kompetensi PBJ 2026" required value="<?= old('subject') ?>">
                    </div>

                    <!-- Action Button CTA (Optional) -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-sm mb-6" for="action_text">Teks Tombol Aksi (Opsional)</label>
                        <input type="text" name="action_text" id="action_text" class="form-control radius-8" placeholder="Contoh: Lihat Detail Pengumuman" value="<?= old('action_text') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-sm mb-6" for="action_url">Tautan Tombol Aksi (Opsional)</label>
                        <input type="url" name="action_url" id="action_url" class="form-control radius-8" placeholder="https://begawipbj.lampungprov.go.id/..." value="<?= old('action_url') ?>">
                    </div>

                    <!-- Rich Body Message -->
                    <div class="col-12">
                        <label class="form-label fw-semibold text-sm mb-6" for="body_message">Isi Pesan Siaran <span class="text-danger-600">*</span></label>
                        <textarea name="body_message" id="body_message" rows="8" class="form-control radius-8" placeholder="Tuliskan isi pengumuman atau informasi resmi di sini..." required><?= old('body_message') ?></textarea>
                        <span class="text-xs text-secondary-light mt-4 d-block">Mendukung format paragraf dan HTML standar. Layout resmi Pemprov Lampung akan otomatis dibungkus di sekeliling pesan ini.</span>
                    </div>

                    <!-- Send Mode (Now vs Schedule) -->
                    <div class="col-12">
                        <label class="form-label fw-semibold text-sm mb-8">Pilihan Waktu Eksekusi Pengiriman <span class="text-danger-600">*</span></label>
                        <div class="d-flex flex-wrap gap-20">
                            <div class="form-check d-flex align-items-center gap-2">
                                <input class="form-check-input" type="radio" name="send_mode" id="modeNow" value="now" checked onchange="toggleScheduleFields()">
                                <label class="form-check-label text-sm fw-medium" for="modeNow">
                                    <strong>Kirim Sekarang (Eksekusi Manual Langsung)</strong>
                                    <span class="text-xs text-secondary-light d-block">Sistem langsung memproses pengiriman batch pertama saat tombol ditekan.</span>
                                </label>
                            </div>
                            <div class="form-check d-flex align-items-center gap-2">
                                <input class="form-check-input" type="radio" name="send_mode" id="modeSchedule" value="schedule" onchange="toggleScheduleFields()">
                                <label class="form-check-label text-sm fw-medium" for="modeSchedule">
                                    <strong>Simpan Sebagai Email Terjadwal</strong>
                                    <span class="text-xs text-secondary-light d-block">Disimpan sebagai draf terjadwal dan dapat dieksekusi manual dari tab Antrean kapan saja.</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Schedule Date & Time (Conditional) -->
                    <div class="col-12 d-none" id="scheduleWrapper">
                        <div class="border border-warning-200 bg-warning-50 radius-8 p-16">
                            <h6 class="text-sm font-semibold text-warning-900 mb-8 d-flex align-items-center gap-2">
                                <iconify-icon icon="solar:calendar-date-bold" class="text-lg"></iconify-icon>
                                Tentukan Waktu Terjadwal yang Ditargetkan
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label text-xs fw-semibold text-secondary-dark" for="scheduled_date">Tanggal Kirim Target</label>
                                    <input type="date" name="scheduled_date" id="scheduled_date" class="form-control radius-8 bg-white" min="<?= date('Y-m-d') ?>" value="<?= date('Y-m-d') ?>">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label text-xs fw-semibold text-secondary-dark" for="scheduled_time">Jam Kirim Target (WIB)</label>
                                    <input type="time" name="scheduled_time" id="scheduled_time" class="form-control radius-8 bg-white" value="08:00">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="col-12 d-flex flex-wrap align-items-center justify-content-between gap-3 pt-12 border-top mt-12">
                        <button type="button" class="btn btn-outline-secondary radius-8 px-20 py-11 d-inline-flex align-items-center gap-2" onclick="previewEmailModal()">
                            <iconify-icon icon="solar:eye-outline" class="text-lg"></iconify-icon>
                            Pratinjau Tampilan Email
                        </button>
                        <button type="submit" class="btn btn-primary-600 radius-8 px-24 py-11 d-inline-flex align-items-center gap-2" id="btnSubmitBroadcast">
                            <iconify-icon icon="solar:plain-bold" class="text-lg"></iconify-icon>
                            Proses &amp; Eksekusi Siaran
                        </button>
                    </div>
                </div>
            </form>

        <!-- =================================================================== -->
        <!-- TAB 2: SEND TO ONE (PERSONAL / SPECIFIC EMAIL)                     -->
        <!-- =================================================================== -->
        <?php elseif ($activeTab === 'send_one'): ?>
            <form action="<?= site_url('dashboard/komunikasi/email/send-one') ?>" method="post">
                <?= csrf_field() ?>
                <div class="row gy-20">
                    <div class="col-12">
                        <div class="alert alert-info bg-info-50 border-info-100 text-info-900 radius-8 p-16 d-flex align-items-start gap-12 mb-0">
                            <iconify-icon icon="solar:letter-opened-bold" class="text-2xl flex-shrink-0 text-info-600 mt-2"></iconify-icon>
                            <div class="text-sm">
                                <strong>Kirim Pesan Personal Manual:</strong> Gunakan form ini untuk mengirimkan surat pemberitahuan khusus, klarifikasi dokumen pengadaan, atau konfirmasi resmi langsung ke satu alamat email penerima.
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-sm mb-6" for="recipient_email">Alamat Email Penerima <span class="text-danger-600">*</span></label>
                        <input type="email" name="recipient_email" id="recipient_email" class="form-control radius-8" placeholder="nama@instansi.go.id" required value="<?= old('recipient_email') ?>">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-sm mb-6" for="recipient_name">Nama Lengkap / Instansi Penerima <span class="text-danger-600">*</span></label>
                        <input type="text" name="recipient_name" id="recipient_name" class="form-control radius-8" placeholder="Contoh: Budi Santoso, S.T. / PT Maju Jaya" required value="<?= old('recipient_name') ?>">
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold text-sm mb-6" for="one_subject">Subjek Email <span class="text-danger-600">*</span></label>
                        <input type="text" name="subject" id="one_subject" class="form-control radius-8" placeholder="Contoh: Pemberitahuan Kelengkapan Dokumen Pengadaan" required value="<?= old('subject') ?>">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-sm mb-6" for="one_action_text">Teks Tombol Aksi (Opsional)</label>
                        <input type="text" name="action_text" id="one_action_text" class="form-control radius-8" placeholder="Contoh: Buka Ruang Kerja" value="<?= old('action_text') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-sm mb-6" for="one_action_url">Tautan Tombol Aksi (Opsional)</label>
                        <input type="url" name="action_url" id="one_action_url" class="form-control radius-8" placeholder="https://begawipbj.lampungprov.go.id/..." value="<?= old('action_url') ?>">
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold text-sm mb-6" for="one_message">Isi Pesan Surat Elektronik <span class="text-danger-600">*</span></label>
                        <textarea name="message" id="one_message" rows="8" class="form-control radius-8" placeholder="Tuliskan isi pesan resmi yang ingin disampaikan..." required><?= old('message') ?></textarea>
                    </div>

                    <div class="col-12 d-flex justify-content-end gap-3 pt-12 border-top mt-12">
                        <button type="submit" class="btn btn-primary-600 radius-8 px-24 py-11 d-inline-flex align-items-center gap-2">
                            <iconify-icon icon="solar:plain-bold" class="text-lg"></iconify-icon>
                            Kirimkan Email Sekarang
                        </button>
                    </div>
                </div>
            </form>

        <!-- =================================================================== -->
        <!-- TAB 3: SCHEDULED QUEUE & PENDING (MANUAL DISPATCHER)                -->
        <!-- =================================================================== -->
        <?php elseif ($activeTab === 'queue'): ?>
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-20">
                <div>
                    <h6 class="text-base fw-bold mb-1">Daftar Antrean &amp; Draf Email Terjadwal</h6>
                    <p class="text-xs text-secondary-light mb-0">Total antrean pending: <strong><?= count($queueItems) ?></strong> email. Seluruh pengiriman diproses manual saat Anda menekan tombol di bawah.</p>
                </div>
                <?php if ($totalReady > 0): ?>
                    <form action="<?= site_url('dashboard/komunikasi/email/dispatch-ready') ?>" method="post">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-warning-600 text-white radius-8 px-20 py-10 d-inline-flex align-items-center gap-2 text-sm shadow-sm" onclick="return confirm('Mulai proses pengiriman untuk semua email yang telah jatuh tempo?')">
                            <iconify-icon icon="solar:bolt-bold" class="text-lg"></iconify-icon>
                            ⚡ Proses <?= $totalReady ?> Email yang Siap Kirim (Batch)
                        </button>
                    </form>
                <?php endif; ?>
            </div>

            <div class="table-responsive">
                <table class="table bordered-table mb-0 text-sm align-middle">
                    <thead>
                        <tr class="bg-neutral-50 text-secondary-light">
                            <th scope="col" style="width: 50px;">ID</th>
                            <th scope="col">Penerima</th>
                            <th scope="col">Subjek Email</th>
                            <th scope="col">Tipe / Audiens</th>
                            <th scope="col">Waktu Jadwal</th>
                            <th scope="col" class="text-center">Status</th>
                            <th scope="col" class="text-end" style="width: 160px;">Aksi Manual</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($queueItems)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-40 text-secondary-light">
                                    <iconify-icon icon="solar:check-read-outline" class="text-4xl text-success-main mb-8"></iconify-icon>
                                    <p class="mb-0 fw-medium">Tidak ada antrean email pending saat ini. Semua email telah terkirim.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($queueItems as $qItem): ?>
                                <tr>
                                    <td class="font-monospace text-xs">#<?= (int) $qItem['id'] ?></td>
                                    <td>
                                        <div class="fw-semibold text-primary-light"><?= esc($qItem['recipient_name']) ?></div>
                                        <span class="text-xs text-secondary-light"><?= esc($qItem['recipient_email']) ?></span>
                                    </td>
                                    <td>
                                        <div class="fw-medium text-primary-dark text-truncate" style="max-width: 280px;" title="<?= esc($qItem['title']) ?>">
                                            <?= esc($qItem['title']) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-neutral-100 text-secondary-dark radius-4 px-8 py-4 text-xs">
                                            <?= esc($qItem['audience_label'] ?: ucfirst($qItem['type'])) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if (! empty($qItem['scheduled_at'])): ?>
                                            <div class="text-xs fw-semibold"><?= date('d/m/Y H:i', strtotime($qItem['scheduled_at'])) ?> WIB</div>
                                            <?php if ($qItem['is_due']): ?>
                                                <span class="text-xs text-success-main fw-semibold">● Telah Jatuh Tempo</span>
                                            <?php else: ?>
                                                <span class="text-xs text-secondary-light">○ Terjadwal</span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="text-xs text-secondary-light">Langsung (Pending)</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($qItem['is_due']): ?>
                                            <span class="badge bg-success-focus text-success-main radius-4 px-8 py-4 text-xs fw-semibold">Siap Dikirim</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning-focus text-warning-main radius-4 px-8 py-4 text-xs fw-semibold">Menunggu Jadwal</span>
                                        <?php endif; ?>
                                        <?php if ($qItem['attempts'] > 0): ?>
                                            <span class="d-block text-xxs text-danger-main mt-2">Coba: <?= (int) $qItem['attempts'] ?>x</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex justify-content-end gap-1">
                                            <form action="<?= site_url('dashboard/komunikasi/email/dispatch-item/' . $qItem['id']) ?>" method="post" class="d-inline">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-sm btn-primary-600 radius-6 px-10 py-6 text-xs d-inline-flex align-items-center gap-1" title="Kirimkan Sekarang">
                                                    <iconify-icon icon="solar:plain-bold"></iconify-icon>
                                                    Kirim
                                                </button>
                                            </form>
                                            <form action="<?= site_url('dashboard/komunikasi/email/delete/' . $qItem['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus antrean email ini?')">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-sm btn-outline-danger radius-6 px-8 py-6 text-xs" title="Hapus">
                                                    <iconify-icon icon="solar:trash-bin-trash-bold"></iconify-icon>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        <!-- =================================================================== -->
        <!-- TAB 4: DELIVERY LOGS & AUDIT TRAIL                                  -->
        <!-- =================================================================== -->
        <?php elseif ($activeTab === 'logs'): ?>
            <!-- Filter Bar -->
            <form action="<?= site_url('dashboard/komunikasi/email') ?>" method="get" class="mb-20">
                <input type="hidden" name="tab" value="logs">
                <div class="row g-2 align-items-center">
                    <div class="col-md-5">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 radius-start-8"><iconify-icon icon="solar:magnifer-linear"></iconify-icon></span>
                            <input type="text" name="q" class="form-control radius-end-8 border-start-0" placeholder="Cari subjek, pesan, atau email penerima..." value="<?= esc($filters['q']) ?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select radius-8">
                            <option value="all">Semua Status Pengiriman</option>
                            <option value="sent" <?= $filters['status'] === 'sent' ? 'selected' : '' ?>>Terkirim Berhasil</option>
                            <option value="pending" <?= $filters['status'] === 'pending' ? 'selected' : '' ?>>Gagal / Pending</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary-600 radius-8 px-16 py-9 w-100 text-sm">Filter</button>
                    </div>
                    <?php if (! empty($filters['q']) || ! empty($filters['status'])): ?>
                        <div class="col-md-2">
                            <a href="<?= site_url('dashboard/komunikasi/email?tab=logs') ?>" class="btn btn-outline-neutral-300 radius-8 px-16 py-9 w-100 text-sm text-center">Reset</a>
                        </div>
                    <?php endif; ?>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table bordered-table mb-0 text-sm align-middle">
                    <thead>
                        <tr class="bg-neutral-50 text-secondary-light">
                            <th scope="col" style="width: 50px;">ID</th>
                            <th scope="col">Waktu Eksekusi</th>
                            <th scope="col">Penerima Email</th>
                            <th scope="col">Subjek</th>
                            <th scope="col">Tipe</th>
                            <th scope="col" class="text-center">Status</th>
                            <th scope="col" class="text-end" style="width: 140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($logs)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-32 text-secondary-light">
                                    Belum ada data riwayat log pengiriman email.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($logs as $log): ?>
                                <tr>
                                    <td class="font-monospace text-xs">#<?= (int) $log['id'] ?></td>
                                    <td>
                                        <div class="text-xs fw-semibold"><?= date('d/m/Y H:i', strtotime($log['sent_at'] ?: $log['created_at'])) ?> WIB</div>
                                        <span class="text-xxs text-secondary-light"><?= ! empty($log['sent_at']) ? 'Terkirim' : 'Dibuat' ?></span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-primary-light"><?= esc($log['recipient_name']) ?></div>
                                        <span class="text-xs text-secondary-light"><?= esc($log['recipient_email']) ?></span>
                                    </td>
                                    <td>
                                        <div class="fw-medium text-primary-dark text-truncate" style="max-width: 260px;" title="<?= esc($log['title']) ?>">
                                            <?= esc($log['title']) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-neutral-100 text-secondary-dark radius-4 px-6 py-2 text-xxs">
                                            <?= esc($log['type']) ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <?php if (! empty($log['sent_at'])): ?>
                                            <span class="badge bg-success-focus text-success-main radius-4 px-8 py-3 text-xs fw-semibold">✓ Terkirim</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger-focus text-danger-main radius-4 px-8 py-3 text-xs fw-semibold" title="<?= esc($log['last_error'] ?? 'Gagal terkirim') ?>">✕ Gagal</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex justify-content-end gap-1">
                                            <?php if (empty($log['sent_at'])): ?>
                                                <form action="<?= site_url('dashboard/komunikasi/email/retry/' . $log['id']) ?>" method="post" class="d-inline">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="btn btn-sm btn-outline-warning radius-6 px-8 py-4 text-xs" title="Coba Kirim Ulang (Retry)">
                                                        <iconify-icon icon="solar:restart-bold"></iconify-icon> Retry
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                            <form action="<?= site_url('dashboard/komunikasi/email/delete/' . $log['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus log email ini?')">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-sm btn-outline-danger radius-6 px-8 py-4 text-xs" title="Hapus Log">
                                                    <iconify-icon icon="solar:trash-bin-trash-bold"></iconify-icon>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mt-24">
                <span class="text-sm text-secondary-light">
                    Menampilkan <?= count($logs) ?> dari <?= number_format($totalLogs) ?> total riwayat
                </span>
                <?php if ($totalLogs > 15): ?>
                    <?= $pager->makeLinks(max(1, (int) ($filters['page'] ?? 1)), 15, $totalLogs, 'default_full', 0, 'email_logs') ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Pratinjau Email -->
<div class="modal fade" id="previewEmailModal" tabindex="-1" aria-labelledby="previewEmailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content radius-12 border-0">
            <div class="modal-header border-bottom py-16 px-24 bg-neutral-50">
                <h6 class="modal-title fs-16 fw-bold" id="previewEmailModalLabel">Pratinjau Tampilan Email Resmi</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body p-0" style="background-color: #f1f5f9;">
                <iframe id="previewEmailFrame" style="width: 100%; height: 500px; border: none;"></iframe>
            </div>
            <div class="modal-footer border-top py-12 px-24">
                <button type="button" class="btn btn-secondary radius-8 px-20 py-8 text-sm" data-bs-dismiss="modal">Tutup Pratinjau</button>
            </div>
        </div>
    </div>
</div>

<script>
function handleAudienceChange() {
    const val = document.getElementById('target_group').value;
    const classWrap = document.getElementById('classSelectorWrapper');
    const eventWrap = document.getElementById('eventSelectorWrapper');

    if (val === 'class') {
        classWrap.classList.remove('d-none');
        eventWrap.classList.add('d-none');
    } else if (val === 'event') {
        eventWrap.classList.remove('d-none');
        classWrap.classList.add('d-none');
    } else {
        classWrap.classList.add('d-none');
        eventWrap.classList.add('d-none');
    }

    fetchAudienceCount();
}

function fetchAudienceCount() {
    const val = document.getElementById('target_group').value;
    let targetId = 0;
    if (val === 'class') {
        targetId = document.getElementById('class_id').value;
    } else if (val === 'event') {
        targetId = document.getElementById('event_id').value;
    }

    const pill = document.getElementById('audienceCountPill');
    pill.innerText = 'Menghitung estimasi...';

    fetch('<?= site_url('dashboard/komunikasi/email/count-audience') ?>?group=' + val + '&target_id=' + targetId)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                pill.innerText = 'Estimasi Penerima: ' + data.count + ' orang';
            }
        })
        .catch(() => {
            pill.innerText = 'Penerima siap didaftarkan';
        });
}

function toggleScheduleFields() {
    const isSchedule = document.getElementById('modeSchedule').checked;
    const wrap = document.getElementById('scheduleWrapper');
    if (isSchedule) {
        wrap.classList.remove('d-none');
    } else {
        wrap.classList.add('d-none');
    }
}

function previewEmailModal() {
    const subject = document.getElementById('subject') ? document.getElementById('subject').value : 'Pengumuman Resmi BegawiPengadaan';
    const body = document.getElementById('body_message') ? document.getElementById('body_message').value : '<p>Pratinjau konten pesan siaran...</p>';
    const actionUrl = document.getElementById('action_url') ? document.getElementById('action_url').value : '';
    const actionText = document.getElementById('action_text') ? document.getElementById('action_text').value : '';

    const formData = new FormData();
    formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
    formData.append('subject', subject);
    formData.append('body', body);
    formData.append('action_url', actionUrl);
    formData.append('action_text', actionText);

    fetch('<?= site_url('dashboard/komunikasi/email/preview') ?>', {
        method: 'POST',
        body: formData
    })
    .then(r => r.text())
    .then(html => {
        const frame = document.getElementById('previewEmailFrame');
        frame.srcdoc = html;
        const myModal = new bootstrap.Modal(document.getElementById('previewEmailModal'));
        myModal.show();
    });
}

document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('target_group')) {
        fetchAudienceCount();
    }
});
</script>
<?= $this->endSection() ?>
