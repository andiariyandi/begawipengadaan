<div class="modal fade" id="<?= esc($modalId ?? 'pdfViewerModal') ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content radius-16">
            <div class="modal-header">
                <h5 class="modal-title"><?= esc($modalTitle ?? 'Pratinjau Dokumen') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <iframe id="<?= esc($modalId ?? 'pdfViewerModal') ?>Frame" src="<?= esc($pdfUrl ?? 'about:blank') ?>" class="w-100" style="height: 75vh; border: none;"></iframe>
            </div>
        </div>
    </div>
</div>
