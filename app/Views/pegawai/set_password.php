<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>Atur Password Pegawai<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h2 class="fw-bold text-dark mb-1">Atur Password Login Pegawai</h2>
            <p class="text-secondary mb-0">Password ini akan digunakan pegawai untuk login ke Portal Pegawai.</p>
        </div>
        <a href="<?= base_url('pegawai') ?>" class="btn btn-light border rounded-pill px-3">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="glass-card">
            <div class="glass-card-header">
                <h5 class="fw-bold text-dark mb-0"><i class="bi bi-key-fill me-2 text-warning"></i>Set Password</h5>
            </div>
            <div class="glass-card-body">
                <!-- Pegawai Info -->
                <div class="alert alert-indigo border-0 d-flex align-items-start mb-4" style="border-radius: 0.75rem;">
                    <i class="bi bi-person-circle fs-3 me-3 text-indigo"></i>
                    <div>
                        <h6 class="fw-bold mb-1 text-dark"><?= esc($pegawai['nama']) ?></h6>
                        <div class="small text-secondary">NIP: <?= esc($pegawai['nip']) ?></div>
                        <div class="small text-secondary">Jabatan: <?= esc($pegawai['jabatan']) ?></div>
                        <?php if (!empty($pegawai['password'])): ?>
                            <span class="badge bg-success mt-1"><i class="bi bi-check-circle me-1"></i>Password sudah diatur</span>
                        <?php else: ?>
                            <span class="badge bg-secondary mt-1"><i class="bi bi-x-circle me-1"></i>Password belum diatur</span>
                        <?php endif; ?>
                    </div>
                </div>

                <form action="<?= base_url('pegawai/save-password/' . encode_id($pegawai['id'])) ?>" method="POST">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold">Password Baru <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password" name="password" placeholder="Minimal 6 karakter" required minlength="6">
                            <button class="btn btn-outline-secondary toggle-password" type="button" data-target="#password">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="password_confirm" class="form-label fw-semibold">Konfirmasi Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password_confirm" name="password_confirm" placeholder="Ulangi password" required minlength="6">
                            <button class="btn btn-outline-secondary toggle-password" type="button" data-target="#password_confirm">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end pt-3 border-top">
                        <button type="submit" class="btn btn-primary rounded-pill px-4">
                            <i class="bi bi-key me-2"></i> Simpan Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        $('.toggle-password').on('click', function() {
            const targetSelector = $(this).data('target');
            const passwordInput = $(targetSelector);
            const type = passwordInput.attr('type') === 'password' ? 'text' : 'password';
            passwordInput.attr('type', type);
            $(this).find('i').toggleClass('bi-eye bi-eye-slash');
        });
    });
</script>
<?= $this->endSection() ?>
