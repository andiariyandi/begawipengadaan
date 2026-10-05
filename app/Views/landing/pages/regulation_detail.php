<?= $this->extend('landing/layouts/public') ?>

<?= $this->section('content') ?>

<?= view('landing/components/page_header', ['page_title' => $pageTitle ?? 'Detail Regulasi']) ?>

<?php
$regulation = $regulation ?? null;
$versions   = $versions ?? [];
$relations  = $relations ?? [];
$competencies = $competencies ?? [];
$categories  = $categories ?? [];
$latestVersion = ! empty($versions) ? $versions[0] : null;
?>

<?php if ($msg = session()->getFlashdata('success')): ?>
    <div class="alert alert-success radius-8 mb-3 container"><?= esc($msg) ?></div>
<?php endif; ?>
<?php if ($msg = session()->getFlashdata('error')): ?>
    <div class="alert alert-danger radius-8 mb-3 container"><?= esc($msg) ?></div>
<?php endif; ?>

<?php if (empty($regulation)): ?>
    <section class="about-three learning-detail">
        <div class="container">
            <p class="text-center text-secondary-light">Regulasi tidak ditemukan.</p>
        </div>
    </section>
<?php else: ?>

<section class="about-three learning-detail">
    <div class="container">
        <div class="row">
            <div class="col-xl-8">
                <div class="about-three__right">
                    <div class="section-title-two text-left">
                        <div class="section-title-two__tagline-box"><span class="section-title-two__tagline">Regulasi PBJ</span></div>
                        <h2 class="section-title-two__title"><?= esc($regulation['document_number'] ?? '') ?></h2>
                        <h4 class="mt-12 text-secondary-light fw-medium"><?= esc($regulation['title'] ?? '') ?></h4>
                    </div>

                    <?php if (! empty($regulation['summary'])): ?>
                        <p class="learning-detail__lead"><?= nl2br(esc($regulation['summary'])) ?></p>
                    <?php endif; ?>

                    <!-- Dokumen & Aksi -->
                    <div class="mt-24 d-flex gap-12 flex-wrap">
                        <?php if (! empty($latestVersion['file_id'])): ?>
                            <a href="<?= route_to('public.regulations.download', $regulation['slug']) ?>" class="thm-btn"><span class="icon-download"></span> Unduh Versi Aktif</a>
                        <?php else: ?>
                            <span class="thm-btn thm-btn--disabled" aria-disabled="true"><span class="icon-info"></span> Dokumen belum tersedia</span>
                        <?php endif; ?>
                        <a href="<?= route_to('public.regulations.versions', $regulation['slug']) ?>" class="thm-btn thm-btn--outline"><span class="icon-clock"></span> Riwayat Versi</a>
                        <a href="<?= route_to('public.regulations.relations', $regulation['slug']) ?>" class="thm-btn thm-btn--outline"><span class="icon-link"></span> Relasi Regulasi</a>
                    </div>

                    <!-- Versi Regulasi -->
                    <?php if (! empty($versions)): ?>
                        <div class="mt-32">
                            <h3 class="text-xl mb-16">Riwayat Versi</h3>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Versi</th>
                                            <th>Tanggal</th>
                                            <th>Changelog</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($versions as $v): ?>
                                            <tr>
                                                <td>v<?= (int) $v['version_number'] ?><?= ((int) $v['is_current'] === 1) ? ' <span class="badge bg-success">Aktif</span>' : '' ?></td>
                                                <td><?= esc(date('d M Y', strtotime($v['created_at'] ?? 'now'))) ?></td>
                                                <td><?= esc($v['changelog'] ?? '-') ?></td>
                                                <td><?= ((int) $v['is_current'] === 1) ? 'Berlaku' : 'Tidak berlaku' ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Relasi Regulasi -->
                    <?php if (! empty($relations)): ?>
                        <div class="mt-32">
                            <h3 class="text-xl mb-16">Relasi Regulasi</h3>
                            <ul class="list-unstyled">
                                <?php foreach ($relations as $rel): ?>
                                    <li>
                                        <a href="<?= site_url('regulasi/' . esc($rel['related_slug'] ?? '')) ?>" class="thm-btn-two thm-btn-two--sm">
                                            <span><?= esc(ucwords(str_replace('_', ' ', $rel['relation_type'] ?? '-'))) ?></span>
                                            &nbsp;→&nbsp;<?= esc($rel['related_document_number'] ?? $rel['related_title'] ?? '-') ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <!-- Kompetensi Terkait -->
                    <?php if (! empty($competencies)): ?>
                        <div class="mt-32">
                            <h3 class="text-xl mb-16">Kompetensi Terkait</h3>
                            <ul class="list-unstyled">
                                <?php foreach ($competencies as $comp): ?>
                                    <li class="badge bg-info-50 text-info-700 px-12 py-8 radius-8 me-6 mb-6">
                                        <strong><?= esc($comp['key']) ?></strong> · <?= esc($comp['name']) ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Sidebar Metadata -->
            <div class="col-xl-4">
                <div class="learning-detail__card">
                    <h3>Informasi Regulasi</h3>

                    <p>Nomor Dokumen</p>
                    <strong><?= esc($regulation['document_number'] ?? '-') ?></strong>

                    <p>Tahun</p>
                    <strong><?= esc($regulation['document_year'] ?? '-') ?></strong>

                    <?php if (! empty($regulation['issuer_name'])): ?>
                        <p>Penerbit</p>
                        <strong><?= esc($regulation['issuer_name']) ?></strong>
                    <?php endif; ?>

                    <?php if (! empty($regulation['category_name'])): ?>
                        <p>Kategori</p>
                        <strong><?= esc($regulation['category_name']) ?></strong>
                    <?php endif; ?>

                    <?php if (! empty($regulation['official_date'])): ?>
                        <p>Tanggal Resmi</p>
                        <strong><?= esc(date('d M Y', strtotime($regulation['official_date']))) ?></strong>
                    <?php endif; ?>

                    <?php if (! empty($regulation['effective_date'])): ?>
                        <p>Tanggal Berlaku</p>
                        <strong><?= esc(date('d M Y', strtotime($regulation['effective_date']))) ?></strong>
                    <?php endif; ?>

                    <p>Status Legal</p>
                    <strong><?= esc(ucfirst((string) ($regulation['legal_status'] ?? '-'))) ?></strong>

                    <p>Status Workflow</p>
                    <strong><?= esc(ucfirst(str_replace('_', ' ', (string) ($regulation['workflow_status'] ?? '-')))) ?></strong>

                    <p>Versi Aktif</p>
                    <strong>v<?= esc($regulation['current_version_number'] ?? '0') ?></strong>

                    <?php if (! empty($latestVersion)): ?>
                        <p>File</p>
                        <strong><?= esc($latestVersion['file_original_name'] ?? '-') ?></strong>
                        <small class="d-block text-muted"><?= esc(strtoupper((string) ($latestVersion['mime_type'] ?? ''))) ?>
                            <?php if (! empty($latestVersion['file_size'])): ?>
                                · <?= number_format((float) $latestVersion['file_size'] / 1024, 1) ?> KB
                            <?php endif; ?>
                        </small>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Related regulations -->
<?php if (! empty($related ?? [])): ?>
<section class="related-one">
    <div class="container">
        <div class="section-title text-center">
            <div class="section-title__tagline-box"><div class="section-title__tagline-shape"></div><p class="section-title__tagline">Regulasi Serupa</p></div>
            <h2 class="section-title__title">Regulasi Terkait</h2>
        </div>
        <div class="row">
            <?php foreach (($related ?? []) as $rel): ?>
                <div class="col-xl-4 col-lg-6 col-md-6 mb-3">
                    <article class="blog-two__single h-100">
                        <div class="blog-two__content">
                            <h4 class="blog-two__title">
                                <a href="<?= site_url('regulasi/' . esc($rel['slug'] ?? '')) ?>"><?= esc($rel['title'] ?? '-') ?></a>
                            </h4>
                            <p class="blog-two__text"><?= esc($rel['document_number'] ?? '-') ?> · <?= esc($rel['category_name'] ?? '-') ?></p>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php endif; ?>

<?= $this->endSection() ?>