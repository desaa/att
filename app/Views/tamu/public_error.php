<?= $this->extend('layouts/public') ?>

<?= $this->section('title') ?>Akses Dibatasi<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="glass-card shadow-lg border-0" style="border-radius: 1.25rem;">
    <div class="glass-card-body p-5 text-center">
        <div class="text-danger mb-4" style="font-size: 4rem;">
            <i class="bi bi-exclamation-triangle-fill"></i>
        </div>
        <h3 class="fw-bold text-dark mb-3">Akses Dibatasi</h3>
        <p class="text-secondary mb-4 leading-relaxed"><?= esc($message) ?></p>
        
        <div class="border-top pt-4">
            <p class="small text-muted mb-0">Jika menurut Anda ini adalah kesalahan, silakan hubungi petugas bagian penerimaan tamu atau admin OPD penyelenggara kegiatan.</p>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
