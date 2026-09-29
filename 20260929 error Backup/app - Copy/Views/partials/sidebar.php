<?php
$roles       = session()->get('roles') ?? [];
$permissions = session()->get('permissions') ?? [];
$isLoggedIn  = (bool) session()->get('isLoggedIn');
?>
<aside class="sidebar">
    <button type="button" class="sidebar-close-btn">
        <iconify-icon icon="radix-icons:cross-2"></iconify-icon>
    </button>
    <div>
        <a href="<?= route_to('index') ?>" class="sidebar-logo">
            <img src="<?= base_url('assets/images/logo.png') ?>" alt="site logo" class="light-logo">
            <img src="<?= base_url('assets/images/logo-light.png') ?>" alt="site logo" class="dark-logo">
            <img src="<?= base_url('assets/images/logo-icon.png') ?>" alt="site logo" class="logo-icon">
        </a>
    </div>
    <div class="sidebar-menu-area">
        <ul class="sidebar-menu" id="sidebar-menu">

            <?php if ($isLoggedIn): ?>
                <li>
                    <a href="<?= route_to('dashboard.pbj') ?>">
                        <iconify-icon icon="solar:home-smile-angle-outline" class="menu-icon"></iconify-icon>
                        <span>Dashboard</span>
                    </a>
                </li>
            <?php endif; ?>

            <?php if (array_intersect($roles, ['super_administrator', 'administrator', 'operator', 'trainer', 'evaluator'])): ?>
                <li class="sidebar-menu-group-title">Manajemen Pengguna</li>

                <?php if (array_intersect($roles, ['super_administrator', 'administrator', 'operator'])): ?>
                    <li>
                        <a href="<?= route_to('usersList') ?>">
                            <iconify-icon icon="flowbite:users-group-outline" class="menu-icon"></iconify-icon>
                            <span>Daftar Pengguna</span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if (array_intersect($roles, ['super_administrator', 'administrator'])): ?>
                    <li class="dropdown">
                        <a href="#role-access-menu">
                            <i class="ri-user-settings-line text-xl me-6 d-flex w-auto"></i>
                            <span>Role & Hak Akses</span>
                        </a>
                        <ul id="role-access-menu" class="sidebar-submenu">
                            <li><a href="<?= route_to('roleAaccess') ?>"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Role & Access</a></li>
                            <li><a href="<?= route_to('assignRole') ?>"><i class="ri-circle-fill circle-icon text-warning-main w-auto"></i> Assign Role</a></li>
                            <li><a href="<?= route_to('admin.audit.index') ?>"><i class="ri-circle-fill circle-icon text-neutral-600 w-auto"></i> Audit Log</a></li>
                        </ul>
                    </li>
                <?php endif; ?>
            <?php endif; ?>

            <?php if ($isLoggedIn): ?>
                <li class="sidebar-menu-group-title">Pembelajaran (LMS)</li>

                <?php if (array_intersect($roles, ['super_administrator', 'administrator', 'trainer'])): ?>
                    <li>
                        <a href="<?= route_to('learning.courses') ?>">
                            <iconify-icon icon="solar:notebook-outline" class="menu-icon"></iconify-icon>
                            <span>Program Pelatihan</span>
                        </a>
                    </li>
                    <?php if (in_array('enrollment.verify', $permissions, true)): ?>
                        <li>
                            <a href="<?= route_to('learning.enrollments.pending') ?>">
                                <iconify-icon icon="mdi:clipboard-check-outline" class="menu-icon"></iconify-icon>
                                <span>Persetujuan Pendaftaran</span>
                            </a>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>
                <li>
                    <a href="<?= route_to('learning.enrollments.browse') ?>">
                        <iconify-icon icon="mdi:magnify" class="menu-icon"></iconify-icon>
                        <span>Cari Kelas</span>
                    </a>
                </li>
                <li>
                    <a href="<?= route_to('learning.study.myClasses') ?>">
                        <iconify-icon icon="mdi:school-outline" class="menu-icon"></iconify-icon>
                        <span>Kelas Saya</span>
                    </a>
                </li>
                <?php if (in_array('assessment.evaluate', $permissions, true)): ?>
                    <li>
                        <a href="<?= route_to('learning.evaluator.pending') ?>">
                            <iconify-icon icon="mdi:clipboard-text-search-outline" class="menu-icon"></iconify-icon>
                            <span>Penilaian Assessment</span>
                        </a>
                    </li>
                <?php endif; ?>
            <?php endif; ?>

            <li class="sidebar-menu-group-title">Pengetahuan (KMS)</li>

            <?php $canManageKms = in_array('kms.manage', session()->get('permissions') ?? [], true); ?>
            <li>
                <a href="<?= route_to($canManageKms ? 'knowledge.articles.manage' : 'knowledge.articles.browse') ?>">
                    <iconify-icon icon="mdi:file-document-outline" class="menu-icon"></iconify-icon>
                    <span><?= $canManageKms ? 'Kelola Artikel' : 'Artikel' ?></span>
                </a>
            </li>
            <li>
                <a href="<?= route_to('knowledge.faq') ?>">
                    <iconify-icon icon="mdi:frequently-asked-questions" class="menu-icon"></iconify-icon>
                    <span>FAQ</span>
                </a>
            </li>
            <li>
                <a href="<?= route_to('knowledge.caseStudies.browse') ?>">
                    <iconify-icon icon="mdi:briefcase-outline" class="menu-icon"></iconify-icon>
                    <span>Studi Kasus</span>
                </a>
            </li>
            <li>
                <a href="<?= route_to('knowledge.templates.browse') ?>">
                    <iconify-icon icon="mdi:file-download-outline" class="menu-icon"></iconify-icon>
                    <span>Template Dokumen</span>
                </a>
            </li>
            <?php if (array_intersect($roles, ['super_administrator', 'administrator', 'trainer'])): ?>
                <li>
                    <a href="<?= route_to('knowledge.categories') ?>">
                        <iconify-icon icon="mdi:shape-outline" class="menu-icon"></iconify-icon>
                        <span>Kategori KMS</span>
                    </a>
                </li>
            <?php endif; ?>
            <?php if ($isLoggedIn): ?>
                <li>
                    <a href="<?= route_to('knowledge.bookmarks') ?>">
                        <iconify-icon icon="mdi:bookmark-outline" class="menu-icon"></iconify-icon>
                        <span>Bookmark Saya</span>
                    </a>
                </li>
            <?php endif; ?>

            <li class="sidebar-menu-group-title">Regulasi</li>

            <li>
                <a href="<?= route_to('regulation.browse') ?>">
                    <iconify-icon icon="mdi:gavel" class="menu-icon"></iconify-icon>
                    <span>Repository Regulasi</span>
                </a>
            </li>
            <?php if (array_intersect($roles, ['super_administrator', 'administrator', 'operator'])): ?>
                <li>
                    <a href="<?= route_to('regulation.create') ?>">
                        <iconify-icon icon="mdi:upload-outline" class="menu-icon"></iconify-icon>
                        <span>Tambah Regulasi</span>
                    </a>
                </li>
            <?php endif; ?>

            <?php if ($isLoggedIn): ?>
                <li class="sidebar-menu-group-title">Sertifikat</li>

                <li>
                    <a href="<?= route_to('certificate.my') ?>">
                        <iconify-icon icon="mdi:certificate-outline" class="menu-icon"></iconify-icon>
                        <span>Sertifikat Saya</span>
                    </a>
                </li>
                <?php if (array_intersect($roles, ['super_administrator', 'administrator', 'evaluator'])): ?>
                    <li>
                        <a href="<?= route_to('certificate.pending') ?>">
                            <iconify-icon icon="mdi:clipboard-check-outline" class="menu-icon"></iconify-icon>
                            <span>Persetujuan Sertifikat</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= route_to('certificate.manage') ?>">
                            <iconify-icon icon="mdi:certificate" class="menu-icon"></iconify-icon>
                            <span>Kelola Sertifikat</span>
                        </a>
                    </li>
                <?php endif; ?>
            <?php endif; ?>

        </ul>
    </div>
</aside>