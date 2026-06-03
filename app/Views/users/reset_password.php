<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>Reset Kata Sandi<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row mb-4">
    <div class="col-12">
        <h2 class="fw-bold text-dark">Reset Kata Sandi</h2>
        <p class="text-secondary">Reset kata sandi administrator unit kerja.</p>
    </div>
</div>

<div class="row">
    <div class="col-12 col-md-6">
        <div class="glass-card">
            <div class="glass-card-body">
                <form action="<?= base_url('users/reset-password/' . encode_id($user->id)) ?>" method="POST" id="resetForm">
                    <?= csrf_field() ?>
                    
                    <div class="mb-3">
                        <label for="username" class="form-label fw-semibold">Username</label>
                        <input type="text" class="form-control bg-light font-monospace" id="username" value="@<?= esc($user->username) ?>" disabled>
                    </div>

                    <div class="mb-3">
                        <label for="nama" class="form-label fw-semibold">Nama Administrator</label>
                        <input type="text" class="form-control bg-light" id="nama" value="<?= esc($user->nama ?: '-') ?>" disabled>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label fw-semibold">Kata Sandi Baru</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password" name="password" placeholder="Minimal 8 karakter" required>
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <div class="small text-muted mt-1">Masukkan kata sandi baru yang kuat untuk administrator ini.</div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 border-top pt-4">
                        <a href="<?= base_url('users') ?>" class="btn btn-light rounded-pill px-4">Batal</a>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">Reset Sandi</button>
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
        $('#togglePassword').on('click', function() {
            const passwordInput = $('#password');
            const type = passwordInput.attr('type') === 'password' ? 'text' : 'password';
            passwordInput.attr('type', type);
            $(this).find('i').toggleClass('bi-eye bi-eye-slash');
        });
    });
</script>
<?= $this->endSection() ?>
