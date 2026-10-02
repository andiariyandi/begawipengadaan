<aside class="col-lg-3 col-xl-3">
    <div class="bg-dark text-white rounded p-4 h-100">
    <div class="border-bottom border-secondary pb-3 mb-4">
        <p class="text-uppercase text-white-50 small mb-2">Workspace User</p>
        <strong class="d-block fs-5"><?= esc($userName ?? 'Pengguna') ?></strong>
        <span class="text-white-50"><?= esc($roleLabel ?? 'Pengguna') ?></span>
    </div>
    <nav aria-label="Menu Workspace">
        <?php foreach (($menuItems ?? []) as $group): ?>
            <section class="border-bottom border-secondary pb-2 mb-3">
                <h2 class="text-uppercase text-white-50 small mb-2"><?= esc($group['label']) ?></h2>
                <ul class="list-unstyled mb-0">
                    <?php foreach ($group['items'] as $item): ?>
                        <li>
                            <?php if (! empty($item['url'])): ?>
                                <a class="d-block text-white text-decoration-none py-2" href="<?= esc($item['url']) ?>"><?= esc($item['label']) ?></a>
                            <?php else: ?>
                                <span class="d-block text-white-50 py-2" aria-disabled="true"><?= esc($item['label']) ?></span>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </section>
        <?php endforeach; ?>
    </nav>
    </div>
</aside>
