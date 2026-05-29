<?= $this->extend('layouts/pegawai') ?>

<?= $this->section('title') ?>Ganti Password - Portal Pegawai<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row mb-4">
    <div class="col-12">
        <h2 class="fw-bold text-dark">Ganti Password</h2>
        <p class="text-secondary">Ubah kata sandi Portal Pegawai Anda secara mandiri.</p>
    </div>
</div>

<div class="row justify-content-start">
    <div class="col-12 col-md-6 col-lg-5">
        <div class="glass-card">
            <div class="glass-card-header bg-light">
                <span class="fw-bold text-dark"><i class="bi bi-key-fill text-warning me-2"></i>Ubah Password Portal</span>
            </div>
            <div class="glass-card-body">
                <form action="<?= base_url('pegawai-portal/ganti-password') ?>" method="POST">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label for="old_password" class="form-label fw-semibold">Password Saat Ini <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="old_password" name="old_password" placeholder="Masukkan password saat ini" required>
                            <button class="btn btn-outline-secondary toggle-password" type="button" data-target="#old_password">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="new_password" class="form-label fw-semibold">Password Baru <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="new_password" name="new_password" placeholder="Minimal 6 karakter" required minlength="6">
                            <button class="btn btn-outline-secondary toggle-password" type="button" data-target="#new_password">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <div class="small text-muted mt-1">Gunakan minimal 6 karakter.</div>
                    </div>

                    <div class="mb-4">
                        <label for="confirm_password" class="form-label fw-semibold">Ulangi Password Baru <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Masukkan kembali password baru" required>
                            <button class="btn btn-outline-secondary toggle-password" type="button" data-target="#confirm_password">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 border-top pt-4">
                        <a href="<?= base_url('pegawai-portal/dashboard') ?>" class="btn btn-light rounded-pill px-4">Batal</a>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">Update Password</button>
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
