<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>Catat Kunjungan Manual<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row mb-4">
    <div class="col-12">
        <h2 class="fw-bold text-dark">Catat Kunjungan Tamu</h2>
        <p class="text-secondary">Input manual data kunjungan tamu secara langsung dari meja pelayanan.</p>
    </div>
</div>

<div class="row">
    <div class="col-12 col-xl-9">
        <div class="glass-card">
            <div class="glass-card-body">
                <form action="<?= base_url('tamu/input') ?>" method="POST" enctype="multipart/form-data" id="manualGuestForm">
                    <?= csrf_field() ?>
                    
                    <!-- Hidden fields for base64 captures -->
                    <input type="hidden" name="tanda_tangan" id="tanda_tangan">
                    <input type="hidden" name="foto_tamu" id="foto_tamu">

                    <h5 class="fw-bold text-indigo mb-3 border-bottom pb-2">Identitas Tamu</h5>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="nik" class="form-label fw-semibold">NIK (No. KTP) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control font-monospace" id="nik" name="nik" placeholder="16 Digit NIK KTP" required>
                            <div class="invalid-feedback" id="nik-error">NIK harus tepat 16 digit.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="nama_tamu" class="form-label fw-semibold">Nama Lengkap Tamu <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama_tamu" name="nama_tamu" placeholder="Nama Lengkap Tamu" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="no_hp" class="form-label fw-semibold">No. HP / WhatsApp <span class="text-danger">*</span></label>
                            <input type="text" class="form-control font-monospace" id="no_hp" name="no_hp" placeholder="Contoh: 081234567890" required>
                        </div>
                        <div class="col-md-6">
                            <label for="instansi" class="form-label fw-semibold">Instansi / Asal Tamu <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="instansi" name="instansi" placeholder="Nama Instansi, RT/RW, atau Swasta" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="alamat" class="form-label fw-semibold">Alamat Lengkap <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="alamat" name="alamat" rows="2" placeholder="Alamat asal tamu..." required></textarea>
                    </div>

                    <h5 class="fw-bold text-indigo mb-3 border-bottom pb-2">Tujuan Kunjungan</h5>

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
                        <label for="keperluan" class="form-label fw-semibold">Maksud &amp; Keperluan Kunjungan <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="keperluan" name="keperluan" rows="3" placeholder="Jelaskan tujuan tamu berkunjung secara jelas..." required></textarea>
                    </div>

                    <div class="mb-4">
                        <label for="dokumen_pendukung" class="form-label fw-semibold">Unggah Dokumen Lampiran (Opsional)</label>
                        <input class="form-control" type="file" id="dokumen_pendukung" name="dokumen_pendukung" accept=".jpg,.png,.pdf">
                        <div class="small text-muted mt-1">Unggah Surat Tugas, Undangan, atau Dokumen pendukung lain (Format: PDF, JPG, PNG).</div>
                    </div>

                    <h5 class="fw-bold text-indigo mb-3 border-bottom pb-2">Dokumentasi &amp; Tanda Tangan</h5>

                    <div class="row g-4 mb-4">
                        <!-- Camera Capture -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Foto Tamu / Selfie (Opsional)</label>
                            
                            <div class="webcam-container mb-2" id="webcam-wrapper">
                                <video id="webcam" class="webcam-preview" autoplay playsinline muted style="display: none;"></video>
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

                        <!-- Signature Canvas -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tanda Tangan Tamu <span class="text-danger">*</span></label>
                            
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

                    <div class="d-flex justify-content-end gap-2 border-top pt-4">
                        <a href="<?= base_url('tamu') ?>" class="btn btn-light rounded-pill px-4">Batal</a>
                        <button type="submit" class="btn btn-primary rounded-pill px-5" id="btn-submit">
                            <i class="bi bi-check-circle-fill me-2"></i> Simpan Buku Tamu
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
    let sigPad;
    let webcamStream = null;

    $(document).ready(function() {
        // Apply inputmask to NIK & Phone
        Inputmask({ mask: "9999999999999999", placeholder: "" }).mask(document.getElementById("nik"));
        Inputmask({ mask: "0999999999999", placeholder: "" }).mask(document.getElementById("no_hp"));

        // Initialize Select2
        $('.select2-enable').select2({
            theme: 'bootstrap-5'
        });

        // Initialize Signature Pad
        const sigCanvas = document.getElementById('sig-pad');
        
        function resizeCanvas() {
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            sigCanvas.width = sigCanvas.offsetWidth * ratio;
            sigCanvas.height = sigCanvas.offsetHeight * ratio;
            sigCanvas.getContext("2d").scale(ratio, ratio);
        }
        
        window.addEventListener("resize", resizeCanvas);
        setTimeout(function() {
            resizeCanvas();
            sigPad = new SignaturePad(sigCanvas, {
                backgroundColor: 'rgb(255, 255, 255)',
                penColor: 'rgb(0, 0, 0)'
            });
        }, 300);

        // Form Submit Handler
        $('#manualGuestForm').on('submit', function(e) {
            // NIK Validation
            let nik = $('#nik').val();
            if (nik.length !== 16) {
                e.preventDefault();
                $('#nik').addClass('is-invalid');
                $('#nik-error').show();
                Swal.fire({
                    icon: 'warning',
                    title: 'NIK Tidak Valid',
                    text: 'Format NIK wajib terdiri dari 16 digit angka.',
                    confirmButtonColor: '#4f46e5'
                });
                return false;
            } else {
                $('#nik').removeClass('is-invalid');
                $('#nik-error').hide();
            }

            // Signature Validation
            if (sigPad.isEmpty()) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Tanda Tangan Wajib!',
                    text: 'Silakan bubuhkan tanda tangan tamu di canvas yang disediakan.',
                    confirmButtonColor: '#4f46e5'
                });
                return false;
            }

            // Copy signature base64 value
            $('#tanda_tangan').val(sigPad.toDataURL());

            // Disable submit to prevent double click
            $('#btn-submit').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Menyimpan...');
            
            // Stop webcam stream if running
            stopWebcam();
        });
    });

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
                status.innerText = 'Kamera Aktif - Siap mengambil foto';
                status.className = 'position-absolute text-white small p-2 bg-success rounded-bottom bottom-0 start-0 end-0 text-center';
            })
            .catch(function(err) {
                toastr.error('Kamera gagal diakses. Pastikan izin kamera telah diberikan.');
                status.innerText = 'Akses kamera gagal / ditolak.';
                status.className = 'position-absolute text-white small p-2 bg-danger rounded-bottom bottom-0 start-0 end-0 text-center';
            });
    }

    function capturePhoto() {
        const video = document.getElementById('webcam');
        const canvas = document.getElementById('photo-canvas');
        const context = canvas.getContext('2d');
        const preview = document.getElementById('photo-preview');
        const status = document.getElementById('cam-status');

        if (webcamStream) {
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            
            context.drawImage(video, 0, 0, video.videoWidth, video.videoHeight);
            
            const photoUrl = canvas.toDataURL('image/png');
            
            $('#foto_tamu').val(photoUrl);
            preview.src = photoUrl;
            
            video.style.display = 'none';
            preview.style.display = 'block';
            
            $('#btn-snap').prop('disabled', true);
            $('#btn-retake').show();
            status.innerText = 'Foto berhasil diambil!';
            status.className = 'position-absolute text-white small p-2 bg-primary rounded-bottom bottom-0 start-0 end-0 text-center';
            
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
