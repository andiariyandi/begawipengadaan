<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24"><div><h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Detail Audit Trail') ?></h6><p class="text-secondary-light mb-0">ID Audit: <?= esc($auditId ?? '-') ?></p></div><a href="<?= site_url('dashboard/audit-trail') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Kembali</a></div>
<div class="row gy-4"><div class="col-xl-8"><div class="card"><div class="card-body p-24"><h6 class="text-lg mb-20">Informasi Aktivitas</h6><div class="row gy-20"><div class="col-md-6"><p class="text-secondary-light mb-4">Waktu</p><h6 class="mb-0">29 Juni 2026, 10:05:42 WIB</h6></div><div class="col-md-6"><p class="text-secondary-light mb-4">Pengguna</p><h6 class="mb-0">Administrator</h6></div><div class="col-md-6"><p class="text-secondary-light mb-4">Aktivitas</p><h6 class="mb-0">Memperbarui data kelas</h6></div><div class="col-md-6"><p class="text-secondary-light mb-4">Modul</p><h6 class="mb-0">Pembelajaran</h6></div><div class="col-md-6"><p class="text-secondary-light mb-4">Target</p><h6 class="mb-0">Kelas: Dasar PBJ</h6></div><div class="col-md-6"><p class="text-secondary-light mb-4">IP Address</p><h6 class="mb-0">10.10.1.15</h6></div></div><hr class="my-24"><h6 class="text-lg mb-12">Data Perubahan</h6><pre class="bg-neutral-50 border border-neutral-200 radius-8 p-16 text-sm mb-0">{
  "nama_kelas": {
    "sebelum": "Dasar Pengadaan PBJ",
    "sesudah": "Dasar-Dasar Pengadaan Barang/Jasa"
  },
  "status": {
    "sebelum": "draft",
    "sesudah": "published"
  }
}</pre></div></div></div><div class="col-xl-4"><div class="card"><div class="card-body p-24"><h6 class="text-lg mb-20">Konteks Request</h6><div class="d-flex flex-column gap-16"><div><p class="text-secondary-light mb-4">Method</p><h6 class="mb-0">PUT</h6></div><div><p class="text-secondary-light mb-4">URL</p><h6 class="mb-0 text-break">/dashboard/learning/classes/CLS-001</h6></div><div><p class="text-secondary-light mb-4">User Agent</p><p class="mb-0 text-sm text-break">Mozilla/5.0 (Windows NT 10.0; Win64; x64)</p></div></div></div></div></div>
<?= $this->endSection() ?>
