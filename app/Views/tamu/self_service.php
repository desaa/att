<?= $this->extend('layouts/public') ?>

<?= $this->section('title') ?>Pendaftaran Tamu Mandiri<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .step-section {
        display: none;
    }
    .step-section.active {
        display: block;
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
                <p class="small text-secondary mb-0">Penyelenggara: <?= esc($agenda['nama_bagian']) ?> - <?= esc($agenda['nama_opd']) ?></p>
            </div>
        </div>

        <form action="<?= base_url('tamu/agenda/' . $token . '/store') ?>" method="POST" enctype="multipart/form-data" id="guestForm">
            <?= csrf_field() ?>
            
            <!-- Hidden Fields for base64 captures -->
            <input type="hidden" name="tanda_tangan" id="tanda_tangan">
            <input type="hidden" name="foto_tamu" id="foto_tamu">

            <!-- STEP 1: DATA DIRI -->
            <div class="step-section active" id="step1">
                <h5 class="fw-bold text-dark mb-3"><span class="badge bg-primary me-2">1</span>Data Diri Tamu</h5>
                
                <div class="mb-3">
                    <label for="nik" class="form-label fw-semibold">NIK (No. KTP) <span class="text-danger">*</span></label>
                    <input type="text" class="form-control font-monospace" id="nik" name="nik" placeholder="16 Digit Angka NIK" required>
                    <div class="invalid-feedback" id="nik-error">NIK harus tepat 16 digit.</div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="nama_tamu" class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nama_tamu" name="nama_tamu" placeholder="Nama Lengkap sesuai KTP" required>
                    </div>
                    <div class="col-md-6">
                        <label for="no_hp" class="form-label fw-semibold">No. HP (WhatsApp) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="no_hp" name="no_hp" placeholder="Contoh: 081234567890" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="instansi" class="form-label fw-semibold">Instansi / Asal <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="instansi" name="instansi" placeholder="Nama Kantor, Sekolah, RT/RW, atau Swasta" required>
                </div>

                <div class="mb-4">
                    <label for="alamat" class="form-label fw-semibold">Alamat Lengkap <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="alamat" name="alamat" rows="2" placeholder="Alamat asal atau tempat tinggal sekarang" required></textarea>
                </div>

                <div class="d-flex justify-content-end pt-3 border-top">
                    <button type="button" class="btn btn-primary rounded-pill px-4" onclick="nextStep(2)">Lanjut <i class="bi bi-arrow-right ms-1"></i></button>
                </div>
            </div>

            <!-- STEP 2: KEPERLUAN & DOKUMEN -->
            <div class="step-section" id="step2">
                <h5 class="fw-bold text-dark mb-3"><span class="badge bg-primary me-2">2</span>Tujuan Kunjungan</h5>

                <div class="mb-3">
                    <label for="id_pegawai_tujuan" class="form-label fw-semibold">Pegawai yang Dituju <span class="text-danger">*</span></label>
                    <select class="form-select select2-enable" name="id_pegawai_tujuan" id="id_pegawai_tujuan" required style="width: 100%">
                        <option value="">-- Pilih Pegawai --</option>
                        <?php foreach ($pegawais as $p): ?>
                            <option value="<?= esc($p['id']) ?>"><?= esc($p['nama']) ?> (<?= esc($p['jabatan']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="keperluan" class="form-label fw-semibold">Keperluan / Tujuan <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="keperluan" name="keperluan" rows="3" placeholder="Jelaskan maksud dan tujuan kunjungan Anda secara detail..." required></textarea>
                </div>

                <div class="mb-4">
                    <label for="dokumen_pendukung" class="form-label fw-semibold">Upload Dokumen Pendukung (Opsional)</label>
                    <input class="form-control" type="file" id="dokumen_pendukung" name="dokumen_pendukung" accept=".jpg,.png,.pdf">
                    <div class="small text-muted mt-1">Upload Surat Tugas, Undangan, atau Dokumen pendukung lain (Format: PDF, JPG, PNG).</div>
                </div>

                <div class="d-flex justify-content-between pt-3 border-top">
                    <button type="button" class="btn btn-light rounded-pill px-4" onclick="prevStep(1)"><i class="bi bi-arrow-left me-1"></i> Kembali</button>
                    <button type="button" class="btn btn-primary rounded-pill px-4" onclick="nextStep(3)">Lanjut <i class="bi bi-arrow-right ms-1"></i></button>
                </div>
            </div>

            <!-- STEP 3: DOKUMENTASI & TANDA TANGAN -->
            <div class="step-section" id="step3">
                <h5 class="fw-bold text-dark mb-4"><span class="badge bg-primary me-2">3</span>Konfirmasi Identitas</h5>

                <div class="row g-4 mb-4">
                    <!-- Photo Capture -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Foto Tamu / Selfie (Opsional)</label>
                        
                        <div class="webcam-container mb-2" id="webcam-wrapper">
                            <video id="webcam" class="webcam-preview" autoplay playsinline muted></video>
                            <canvas id="photo-canvas" style="display: none;"></canvas>
                            <img id="photo-preview" class="webcam-preview" style="display: none;" alt="Photo Preview">
                            <div class="position-absolute text-white small p-2 bg-dark bg-opacity-70 rounded-bottom bottom-0 start-0 end-0 text-center" id="cam-status">
                                Kamera Belum Aktif
                            </div>
                        </div>
                        
                        <div class="d-flex gap-2 justify-content-center">
                            <button type="button" class="btn btn-sm btn-outline-primary" id="btn-start-cam" onclick="startWebcam()">
                                <i class="bi bi-camera-video-fill me-1"></i> Buka Kamera
                            </button>
                            <button type="button" class="btn btn-sm btn-indigo text-white" id="btn-snap" onclick="capturePhoto()" disabled>
                                <i class="bi bi-camera-fill me-1"></i> Ambil Foto
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger" id="btn-retake" onclick="retakePhoto()" style="display: none;">
                                <i class="bi bi-arrow-clockwise me-1"></i> Ulangi
                            </button>
                        </div>
                    </div>

                    <!-- Signature Drawing -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Tanda Tangan Digital <span class="text-danger">*</span></label>
                        
                        <div class="signature-container mb-2">
                            <canvas id="sig-pad" class="signature-pad-canvas"></canvas>
                        </div>
                        
                        <div class="d-flex justify-content-center">
                            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="clearSignature()">
                                <i class="bi bi-eraser-fill me-1"></i> Bersihkan Canvas
                            </button>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between pt-3 border-top">
                    <button type="button" class="btn btn-light rounded-pill px-4" onclick="prevStep(2)"><i class="bi bi-arrow-left me-1"></i> Kembali</button>
                    <button type="submit" class="btn btn-success rounded-pill px-5" id="btn-submit">
                        <i class="bi bi-check-circle-fill me-2"></i> Kirim Buku Tamu
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    let currentStep = 1;
    let sigPad;
    let webcamStream = null;

    $(document).ready(function() {
        // Apply inputmask to NIP / NIK / Phone
        Inputmask({ mask: "9999999999999999", placeholder: "" }).mask(document.getElementById("nik"));
        Inputmask({ mask: "0999999999999", placeholder: "" }).mask(document.getElementById("no_hp"));

        // Initialize Select2 in public form (without dialog parent)
        $('#id_pegawai_tujuan').select2({
            theme: 'bootstrap-5'
        });

        // Initialize Signature Pad
        const sigCanvas = document.getElementById('sig-pad');
        
        // Resize signature canvas helper
        function resizeCanvas() {
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            sigCanvas.width = sigCanvas.offsetWidth * ratio;
            sigCanvas.height = sigCanvas.offsetHeight * ratio;
            sigCanvas.getContext("2d").scale(ratio, ratio);
        }
        
        window.addEventListener("resize", resizeCanvas);
        // Timeout to ensure offsetWidth is loaded
        setTimeout(function() {
            resizeCanvas();
            sigPad = new SignaturePad(sigCanvas, {
                backgroundColor: 'rgb(255, 255, 255)',
                penColor: 'rgb(0, 0, 0)'
            });
        }, 300);

        // Form Submit Handler
        $('#guestForm').on('submit', function(e) {
            // Validate signature
            if (sigPad.isEmpty()) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Tanda Tangan Wajib!',
                    text: 'Silakan bubuhkan tanda tangan Anda di canvas yang disediakan.',
                    confirmButtonColor: '#4f46e5'
                });
                return false;
            }

            // Copy signature base64 value
            $('#tanda_tangan').val(sigPad.toDataURL());

            // Disable submit to prevent double click
            $('#btn-submit').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Mengirim...');
            
            // Stop webcam stream if running
            stopWebcam();
        });
    });

    // Step Navigations
    function nextStep(step) {
        // Simple validations before moving
        if (currentStep === 1) {
            let nik = $('#nik').val();
            let nama = $('#nama_tamu').val();
            let hp = $('#no_hp').val();
            let instansi = $('#instansi').val();
            let alamat = $('#alamat').val();

            if (nik.length !== 16) {
                $('#nik').addClass('is-invalid');
                $('#nik-error').show();
                return;
            } else {
                $('#nik').removeClass('is-invalid');
                $('#nik-error').hide();
            }

            if (!nama || !hp || !instansi || !alamat) {
                Swal.fire({
                    icon: 'error',
                    title: 'Formulir Belum Lengkap',
                    text: 'Silakan isi semua bidang bertanda bintang (*) sebelum melanjutkan.',
                    confirmButtonColor: '#4f46e5'
                });
                return;
            }
        }

        if (currentStep === 2) {
            let pegawai = $('#id_pegawai_tujuan').val();
            let keperluan = $('#keperluan').val();

            if (!pegawai || !keperluan) {
                Swal.fire({
                    icon: 'error',
                    title: 'Tujuan Belum Diisi',
                    text: 'Silakan pilih pegawai yang dituju dan isi keperluan Anda.',
                    confirmButtonColor: '#4f46e5'
                });
                return;
            }
        }

        $('#step' + currentStep).removeClass('active');
        currentStep = step;
        $('#step' + currentStep).addClass('active');

        // Resize signature canvas when step 3 opens
        if (step === 3) {
            setTimeout(function() {
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                const sigCanvas = document.getElementById('sig-pad');
                sigCanvas.width = sigCanvas.offsetWidth * ratio;
                sigCanvas.height = sigCanvas.offsetHeight * ratio;
                sigCanvas.getContext("2d").scale(ratio, ratio);
                sigPad.clear(); // clear resets size cleanly
            }, 100);
        }
    }

    function prevStep(step) {
        $('#step' + currentStep).removeClass('active');
        currentStep = step;
        $('#step' + currentStep).addClass('active');
    }

    // Signature Clean
    function clearSignature() {
        if (sigPad) {
            sigPad.clear();
        }
    }

    // Webcam Logic (HTML5 WebRTC)
    function startWebcam() {
        const video = document.getElementById('webcam');
        const status = document.getElementById('cam-status');

        navigator.mediaDevices.getUserMedia({ video: { facingMode: "user" }, audio: false })
            .then(function(stream) {
                webcamStream = stream;
                video.srcObject = stream;
                video.style.display = 'block';
                $('#photo-preview').hide();
                $('#btn-snap').prop('disabled', false);
                $('#btn-start-cam').hide();
                $('#btn-retake').hide();
                status.text('Kamera Aktif - Siap mengambil foto');
                status.removeClass('bg-dark').addClass('bg-success');
            })
            .catch(function(err) {
                toastr.error('Kamera gagal diakses. Pastikan izin kamera telah diberikan.');
                status.text('Akses kamera gagal / ditolak.');
                status.removeClass('bg-dark').addClass('bg-danger');
            });
    }

    function capturePhoto() {
        const video = document.getElementById('webcam');
        const canvas = document.getElementById('photo-canvas');
        const context = canvas.getContext('2d');
        const preview = document.getElementById('photo-preview');
        const status = document.getElementById('cam-status');

        if (webcamStream) {
            // Set canvas size to video size
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            
            // Draw current frame to canvas
            context.drawImage(video, 0, 0, video.videoWidth, video.videoHeight);
            
            // Convert to base64
            const photoUrl = canvas.toDataURL('image/png');
            
            // Put into hidden input and image preview
            $('#foto_tamu').val(photoUrl);
            preview.src = photoUrl;
            
            // Toggle display
            video.style.display = 'none';
            preview.style.display = 'block';
            
            $('#btn-snap').prop('disabled', true);
            $('#btn-retake').show();
            status.text('Foto berhasil diambil!');
            status.removeClass('bg-success').addClass('bg-primary');
            
            stopWebcam();
        }
    }

    function retakePhoto() {
        $('#foto_tamu').val('');
        $('#photo-preview').hide();
        $('#btn-retake').hide();
        startWebcam();
    }

    function stopWebcam() {
        if (webcamStream) {
            webcamStream.getTracks().forEach(track => track.stop());
            webcamStream = null;
        }
    }
</script>
<?= $this->endSection() ?>
