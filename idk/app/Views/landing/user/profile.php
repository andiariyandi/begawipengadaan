<?= $this->extend('landing/parts/layout/user') ?>
<?= $this->section('content') ?>
<section class="account-page landing-user-page">
    <div class="row landing-user-grid">
        <div class="col-lg-8">
            <div class="landing-user-card landing-user-form">
                <div class="section-title">
                    <p class="section-title__tagline">Akun Anda</p>
                    <h3>Profil Saya</h3>
                </div>
                <form class="landing-form" action="<?= route_to('profile.update') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="row">
                    <?php foreach ([
                        'username' => ['Username','text',$user['username'] ?? ''],
                        'email' => ['Email','email',$user['email'] ?? ''],
                        'full_name' => ['Nama Lengkap','text',$profile['full_name'] ?? ''],
                        'phone' => ['No. HP / WhatsApp','text',$profile['phone'] ?? '']
                    ] as $field => $item): ?>
                        <div class="col-md-6 landing-form__field">
                            <label><?= esc($item[0]) ?></label>
                            <input class="form-control" type="<?= esc($item[1]) ?>" name="<?= esc($field) ?>" value="<?= esc($item[2]) ?>" required>
                        </div>
                    <?php endforeach; ?>
                    </div>
                    <?php foreach (['province_code','regency_code','district_code','village_code'] as $field): ?>
                        <input type="hidden" name="<?= $field ?>" value="<?= esc($profile[$field] ?? '') ?>">
                    <?php endforeach; ?>
                    <?php foreach ($detail as $field => $value): if (in_array($field, ['id', 'user_id', 'created_at', 'updated_at'], true)) continue; ?>
                        <div class="landing-form__field">
                            <label><?= esc(ucwords(str_replace('_', ' ', $field))) ?></label>
                            <input class="form-control" name="<?= esc($field) ?>" value="<?= esc($value) ?>">
                        </div>
                    <?php endforeach; ?>
                    <button class="thm-btn" type="submit">Simpan Profil</button>
                </form>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="landing-user-card landing-user-form">
                <div class="section-title">
                    <p class="section-title__tagline">Keamanan</p>
                    <h3>Ubah Password</h3>
                </div>
                <form class="landing-form" action="<?= route_to('profile.password.update') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="landing-form__field"><label>Password Saat Ini</label><input class="form-control" type="password" name="current_password" required></div>
                    <div class="landing-form__field"><label>Password Baru</label><input class="form-control" type="password" name="password" minlength="8" required></div>
                    <div class="landing-form__field"><label>Konfirmasi Password Baru</label><input class="form-control" type="password" name="password_confirmation" minlength="8" required></div>
                    <button class="thm-btn" type="submit">Ubah Password</button>
                </form>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
