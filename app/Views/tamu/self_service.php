<?= $this->extend('layouts/public') ?>

<?= $this->section('title') ?>Pendaftaran Tamu Mandiri<?= $this->endSection() ?>

<?= $this->section('subtitle') ?>
    <h5 class="text-indigo fw-bold mb-1">
        <?php 
            if (!empty($agenda['nama_subbagian'])) {
                echo esc($agenda['nama_subbagian']);
            } elseif (!empty($agenda['nama_bagian'])) {
                echo esc($agenda['nama_bagian']);
            } else {
                echo esc($agenda['nama_opd']);
            }
        ?>
    </h5>
    <?php if (!empty($agenda['nama_subbagian']) || !empty($agenda['nama_bagian'])): ?>
        <p class="text-muted small mb-0"><?= esc($agenda['nama_opd']) ?></p>
    <?php endif; ?>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .signature-container {
        border: 2px dashed #cbd5e1;
        border-radius: 0.5rem;
        background: #ffffff;
        position: relative;
    }
    .signature-pad-canvas {
        width: 100%;
        height: 180px;
        cursor: crosshair;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="glass-card shadow-lg border-0" style="border-radius: 1.25rem;">
    <div class="glass-card-header bg-primary text-white border-0 py-3 d-flex justify-content-between align-items-center" style="border-top-left-radius: 1.25rem; border-top-right-radius: 1.25rem;">
        <h5 class="fw-bold mb-0 text-white"><i class="bi bi-journal-bookmark-fill me-2"></i>Buku Tamu Mandiri</h5>
        <span class="badge bg-white text-primary rounded-pill px-3" style="font-weight: 600;">Agenda Aktif</span>
    </div>
    
    <div class="glass-card-body p-4">
        <div class="alert alert-indigo border-0 d-flex align-items-start mb-4" style="border-radius: 0.75rem;">
            <i class="bi bi-calendar-event-fill fs-4 me-3 text-indigo"></i>
            <div>
                <h6 class="fw-bold mb-1 text-dark" style="font-size: 0.95rem;"><?= esc($agenda['nama_agenda']) ?></h6>
                <p class="small text-secondary mb-0">
                    Penyelenggara: <?= esc($agenda['nama_bagian']) ?> - <?= esc($agenda['nama_opd']) ?>
                </p>
            </div>
        </div>

        <?php if (session()->has('error')): ?>
            <div class="alert alert-danger border-0 mb-4" style="border-radius: 0.75rem;">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <?= session('error') ?>
            </div>
        <?php endif; ?>

        <form action="<?= site_url('tamu/agenda/' . esc($token, 'url') . '/store') ?>" method="POST" id="guestForm">
            <?= csrf_field() ?>
            
            <!-- Hidden: base64 tanda tangan -->
            <input type="hidden" name="tanda_tangan" id="tanda_tangan">

            <h5 class="fw-bold text-dark mb-3">Isi Data Kehadiran Anda</h5>
            
            <div class="mb-3">
                <label for="nik" class="form-label fw-semibold">
                    NIP / NIK (No. KTP) <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                    <input type="text"
                           class="form-control font-monospace"
                           id="nik"
                           name="nik"
                           value="<?= esc(old('nik')) ?>"
                           placeholder="16 s.d 18 Digit NIP / NIK"
                           autocomplete="off"
                           required>
                    <button type="button" class="btn btn-outline-primary" id="btn-cari-pegawai">
                        <i class="bi bi-search"></i> Cari
                    </button>
                </div>
                <small class="text-muted mt-1 d-block">
                    Ketik NIP/NIK Anda untuk melengkapi data secara otomatis dari database pegawai.
                </small>
            </div>

            <div class="row mb-3">
                <div class="col-md-6 mb-3 mb-md-0">
                    <label for="nama_tamu" class="form-label fw-semibold">
                        Nama Lengkap <span class="text-danger">*</span>
                    </label>
                    <input type="text"
                           class="form-control"
                           id="nama_tamu"
                           name="nama_tamu"
                           value="<?= esc(old('nama_tamu')) ?>"
                           placeholder="Nama Lengkap"
                           required>
                </div>
                <div class="col-md-6">
                    <label for="no_hp" class="form-label fw-semibold">
                        No. HP (WhatsApp) <span class="text-danger">*</span>
                    </label>
                    <input type="text"
                           class="form-control"
                           id="no_hp"
                           name="no_hp"
                           value="<?= esc(old('no_hp')) ?>"
                           placeholder="Contoh: 081234567890"
                           required>
                </div>
            </div>

            <div class="mb-4">
                <label for="instansi" class="form-label fw-semibold">
                    Instansi / Asal <span class="text-danger">*</span>
                </label>
                <input type="text"
                       class="form-control"
                       id="instansi"
                       name="instansi"
                       value="<?= esc(old('instansi')) ?>"
                       placeholder="Nama Kantor, Sekolah, RT/RW, Swasta atau Umum"
                       required>
            </div>

            <!-- Tanda Tangan Digital -->
            <div class="mb-4">
                <label class="form-label fw-semibold">
                    Tanda Tangan Digital <span class="text-danger">*</span>
                </label>
                <p class="small text-muted mb-2">
                    Gunakan jari atau stylus untuk membubuhkan tanda tangan Anda di kotak di bawah ini.
                </p>
                <div class="signature-container mb-2">
                    <canvas id="sig-pad" class="signature-pad-canvas"></canvas>
                </div>
                <div class="d-flex justify-content-start">
                    <button type="button"
                            class="btn btn-sm btn-outline-danger rounded-pill px-3"
                            onclick="clearSignature()">
                        <i class="bi bi-eraser-fill me-1"></i> Bersihkan Canvas
                    </button>
                </div>
            </div>

            <div class="d-flex justify-content-end pt-3 border-top">
                <button type="submit" class="btn btn-success rounded-pill px-5" id="btn-submit">
                    <i class="bi bi-check-circle-fill me-2"></i> Kirim Buku Tamu
                </button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    let sigPad;

    $(document).ready(function () {

        // Inputmask
        Inputmask({ mask: "9999999999999999[99]", placeholder: "" }).mask(document.getElementById("nik"));
        Inputmask({ mask: "0999999999999",        placeholder: "" }).mask(document.getElementById("no_hp"));

        // ── Signature Pad ──────────────────────────────────────────────
        const sigCanvas = document.getElementById('sig-pad');

        function resizeCanvas() {
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            sigCanvas.width  = sigCanvas.offsetWidth  * ratio;
            sigCanvas.height = sigCanvas.offsetHeight * ratio;
            sigCanvas.getContext("2d").scale(ratio, ratio);
            if (sigPad) sigPad.clear(); // bersihkan setelah resize
        }

        window.addEventListener("resize", resizeCanvas);

        // Delay sedikit agar layout selesai render
        setTimeout(function () {
            resizeCanvas();
            sigPad = new SignaturePad(sigCanvas, {
                backgroundColor: 'rgb(255, 255, 255)',
                penColor:        'rgb(0, 0, 0)'
            });
        }, 300);

        // ── Cari Pegawai ───────────────────────────────────────────────
        function cariPegawai() {
            const nikVal = $('#nik').val().replace(/\s+/g, '');
            if (nikVal.length < 16) {
                showAppToast('warning', 'Masukkan 16 s.d 18 digit NIP / NIK terlebih dahulu.');
                return;
            }

            $('#btn-cari-pegawai').prop('disabled', true)
                .html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');

            $.ajax({
                url:      '<?= site_url("api/pegawai-by-nip") ?>/' + nikVal,
                type:     'GET',
                dataType: 'json',
                success: function (response) {
                    $('#btn-cari-pegawai').prop('disabled', false)
                        .html('<i class="bi bi-search"></i> Cari');

                    if (response.status === 'success') {
                        $('#nama_tamu').val(response.data.nama);
                        if (response.data.instansi) {
                            $('#instansi').val(response.data.instansi);
                        }
                        showAppToast('success', 'Data pegawai ditemukan: ' + response.data.nama);
                    } else {
                        showAppToast('info', 'NIP/NIK tidak terdaftar di database pegawai, silakan ketik nama secara manual.');
                    }
                },
                error: function () {
                    $('#btn-cari-pegawai').prop('disabled', false)
                        .html('<i class="bi bi-search"></i> Cari');
                    showAppToast('error', 'Gagal menghubungi server untuk pencarian pegawai.');
                }
            });
        }

        $('#btn-cari-pegawai').on('click', cariPegawai);

        $('#nik').on('change blur', function () {
            const val = $(this).val().replace(/\s+/g, '');
            if (val.length === 16 || val.length === 18) {
                cariPegawai();
            }
        });

        // ── Form Submit ────────────────────────────────────────────────
        $('#guestForm').on('submit', function (e) {

            // Validasi tanda tangan
            if (!sigPad || sigPad.isEmpty()) {
                e.preventDefault();
                Swal.fire({
                    icon:               'warning',
                    title:              'Tanda Tangan Wajib!',
                    text:               'Silakan bubuhkan tanda tangan Anda di canvas yang disediakan.',
                    confirmButtonColor: '#4f46e5'
                });
                return false;
            }

            // Salin base64 ke hidden input
            $('#tanda_tangan').val(sigPad.toDataURL('image/png'));

            // Cegah double submit
            $('#btn-submit').prop('disabled', true)
                .html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Mengirim...');
        });
    });

    function clearSignature() {
        if (sigPad) sigPad.clear();
    }
</script>
<?= $this->endSection() ?>