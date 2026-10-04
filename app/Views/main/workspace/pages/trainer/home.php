<div class="bg-white border rounded p-4 p-lg-5 mb-4">
    <p class="text-uppercase text-muted small mb-2">Trainer</p>
    <h1 class="mb-3">Dashboard Trainer</h1>
    <p class="mb-0">Kelola kelas yang Anda ampu, modul, lesson, dan ujian.</p>
</div>

<div class="row g-3">
    <div class="col-md-6">
        <a href="<?= route_to('workspace.trainer.classes') ?>" class="text-decoration-none">
            <article class="bg-white border rounded p-4 h-100">
                <h2 class="h5 mb-2 text-body">Kelas Saya</h2>
                <p class="small text-muted mb-0">Daftar kelas yang Anda ampu.</p>
            </article>
        </a>
    </div>
    <div class="col-md-6">
        <article class="bg-white border rounded p-4 h-100">
            <h2 class="h5 mb-2 text-muted">Modul & Lesson</h2>
            <p class="small text-muted mb-0">Buka salah satu kelas, lalu kelola modul, lesson, dan ujian.</p>
        </article>
    </div>
</div>