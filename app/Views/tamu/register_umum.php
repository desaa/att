<?= $this->extend('layouts/public') ?>

<?= $this->section('title') ?>Pendaftaran Tamu Umum Mandiri<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .step-section {
        display: block;
    }
    .signature-container {
        border: 2px dashed #cbd5e1;
        border-radius: 0.5rem;
        background: #ffffff;
        position: relative;
    }
    .signature-pad-canvas {
        width: 100%;
        height: 200px;
        cursor: crosshair;
    }
    .webcam-container {
        border: 2px dashed #cbd5e1;
        border-radius: 0.5rem;
        background: #000000;
        overflow: hidden;
        position: relative;
        width: 100%;
        aspect-ratio: 4/3;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .webcam-preview {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="glass-card shadow-lg border-0" style="border-radius: 1.25rem;">
    <div class="glass-card-header bg-primary text-white border-0 py-3 d-flex justify-content-between align-items-center" style="border-top-left-radius: 1.25rem; border-top-right-radius: 1.25rem;">
        <h5 class="fw-bold mb-0 text-white"><i class="bi bi-person-workspace me-2"></i>Buku Tamu Mandiri</h5>
        <span class="badge bg-white text-primary rounded-pill px-3" style="font-weight: 600;">Tamu Umum</span>
    </div>
    
    <div class="glass-card-body p-4">
        <!-- Office details banner -->
        <div class="alert alert-indigo border-0 d-flex align-items-start mb-4" style="border-radius: 0.75rem;">
            <i class="bi bi-building-fill fs-4 me-3 text-indigo"></i>
            <div>
                <h6 class="fw-bold mb-1 text-dark" style="font-size: 0.95rem;">
                    <?php 
                        $parts = [];
                        if (!empty($subbagian)) {
                            $parts[] = $subbagian['nama_subbagian'];
                        }
                        if (!empty($bagian)) {
                            $parts[] = $bagian['nama_bagian'];
                        }
                        echo esc(implode(' - ', $parts) ?: $opd['nama_opd']);
                    ?>
                </h6>
                <?php if (!empty($parts)): ?>
                    <p class="small text-secondary mb-0"><?= esc($opd['nama_opd']) ?></p>
                <?php endif; ?>
            </div>
        </div>

        <?php
            $formParams = !empty($qr_token)
                ? ['q' => $qr_token]
                : array_filter(['kode_opd' => $kode_opd, 'kode_bagian' => $kode_bagian, 'kode_subbagian' => $kode_subbagian]);
        ?>
        <form action="<?= base_url('tamu/register-umum/store?' . http_build_query($formParams)) ?>" method="POST" enctype="multipart/form-data" id="guestForm">
            <?= csrf_field() ?>
            
            <!-- Hidden Fields for base64 captures -->
            <input type="hidden" name="tanda_tangan" id="tanda_tangan">
            <input type="hidden" name="foto_tamu" id="foto_tamu">

            <!-- DATA DIRI & TUJUAN -->
            <div class="step-section active" id="step1">
                <h5 class="fw-bold text-dark mb-3">Data Diri &amp; Keperluan</h5>
                
                <div class="mb-3">
                    <label for="nik" class="form-label fw-semibold">NIP / NIK (No. KTP) <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="text" class="form-control font-monospace" id="nik" name="nik" placeholder="16 s.d 18 Digit NIP / NIK" required>
                        <button type="button" class="btn btn-outline-primary" id="btn-cari-pegawai"><i class="bi bi-search"></i> Cari</button>
                    </div>
                    <div class="invalid-feedback" id="nik-error">NIP / NIK harus terdiri dari 16 sampai 18 digit.</div>
                    <small class="text-muted mt-1 d-block">Masukkan NIP 18 digit angka untuk melengkapi nama otomatis jika terdaftar di database pegawai.</small>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="nama_tamu" class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nama_tamu" name="nama_tamu" placeholder="Nama Lengkap" required>
                    </div>
                    <div class="col-md-6">
                        <label for="no_hp" class="form-label fw-semibold">No. HP (WhatsApp) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="no_hp" name="no_hp" placeholder="Contoh: 081234567890" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="instansi" class="form-label fw-semibold">Instansi / Asal <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="instansi" name="instansi" placeholder="Kantor, Instansi, Perusahaan, Sekolah, atau Umum" required>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="bidang" class="form-label fw-semibold">Bidang <span class="text-secondary small">(Opsional)</span></label>
                        <input type="text" class="form-control" id="bidang" name="bidang" placeholder="Nama Bidang (Jika ada)">
                    </div>
                    <div class="col-md-6">
                        <label for="subbidang" class="form-label fw-semibold">Subbidang / Seksi <span class="text-secondary small">(Opsional)</span></label>
                        <input type="text" class="form-control" id="subbidang" name="subbidang" placeholder="Nama Subbidang (Jika ada)">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="alamat" class="form-label fw-semibold">Alamat Lengkap <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="alamat" name="alamat" rows="2" placeholder="Alamat asal atau tempat tinggal" required></textarea>
                </div>

                <div class="row mb-4">
                    <!-- Target Filters -->
                    <input type="hidden" id="kode_opd_val" value="<?= esc($opd['kode_opd']) ?>">
                    
                    <?php if (empty($bagian)): ?>
                        <div class="col-md-6 mb-3">
                            <label for="filter_bagian" class="form-label fw-semibold">Filter Bidang Tujuan <span class="text-secondary small">(Opsional)</span></label>
                            <select class="form-select select2-enable" id="filter_bagian" style="width: 100%;">
                                <option value="">-- Semua Bidang --</option>
                                <?php foreach ($bagians as $b): ?>
                                    <option value="<?= esc($b['kode_bagian']) ?>"><?= esc($b['nama_bagian']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php else: ?>
                        <input type="hidden" id="filter_bagian" value="<?= esc($bagian['kode_bagian']) ?>">
                    <?php endif; ?>

                    <?php if (empty($subbagian)): ?>
                        <div class="col-md-6 mb-3">
                            <label for="filter_subbagian" class="form-label fw-semibold">Filter Subbidang Tujuan <span class="text-secondary small">(Opsional)</span></label>
                            <select class="form-select select2-enable" id="filter_subbagian" style="width: 100%;">
                                <option value="">-- Semua Subbidang --</option>
                                <?php if (!empty($subbagians)): ?>
                                    <?php foreach ($subbagians as $sb): ?>
                                        <option value="<?= esc($sb['kode_subbagian']) ?>"><?= esc($sb['nama_subbagian']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    <?php else: ?>
                        <input type="hidden" id="filter_subbagian" value="<?= esc($subbagian['kode_subbagian']) ?>">
                    <?php endif; ?>

                    <div class="col-md-6">
                        <label for="id_pegawai_tujuan" class="form-label fw-semibold">Pegawai yang Ingin Ditemui <span class="text-secondary small">(Opsional)</span></label>
                        <select class="form-select select2-enable" name="id_pegawai_tujuan" id="id_pegawai_tujuan" style="width: 100%;">
                            <option value="">-- Pilih Pegawai --</option>
                            <?php foreach ($pegawais as $p): ?>
                                <option value="<?= esc($p['id']) ?>"><?= esc($p['nama']) ?> <?= !empty($p['jabatan']) ? '(' . esc($p['jabatan']) . ')' : '' ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="keperluan" class="form-label fw-semibold">Keperluan Kunjungan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="keperluan" name="keperluan" placeholder="Contoh: Koordinasi, Penyerahan Berkas, Konsultasi" required>
                    </div>
                </div>

            </div>

            <!-- DOKUMENTASI & TANDA TANGAN -->
            <div class="step-section" id="step2">
                <h5 class="fw-bold text-dark mb-4">Dokumentasi &amp; Tanda Tangan</h5>

                <div class="row g-4 mb-4">
                    <!-- Photo Capture -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Ambil Foto / Selfie <span class="text-danger">*</span></label>
                        
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
                        
                        <div class="d-flex justify-content-start mb-3">
                            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="clearSignature()">
                                <i class="bi bi-eraser-fill me-1"></i> Bersihkan Canvas
                            </button>
                        </div>

                        <!-- Document Upload (Optional) -->
                        <div class="mt-4">
                            <label for="dokumen_pendukung" class="form-label fw-semibold">Upload Berkas / Lampiran <span class="text-secondary small">(Opsional)</span></label>
                            <input type="file" class="form-control form-control-sm" id="dokumen_pendukung" name="dokumen_pendukung" accept=".pdf,.jpg,.jpeg,.png,.docx">
                            <small class="text-muted" style="font-size: 0.725rem;">Format: PDF, JPG, PNG, DOCX (Max 1MB) jika ada surat tugas atau proposal pendukung.</small>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end pt-3 border-top">
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
        Inputmask({ mask: "9999999999999999[99]", placeholder: "" }).mask(document.getElementById("nik"));
        Inputmask({ mask: "0999999999999", placeholder: "" }).mask(document.getElementById("no_hp"));

        // Initialize select2
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

        // Employee NIP/NIK lookup
        function cariPegawai() {
            let val = $('#nik').val().replace(/\s+/g, '');
            if (/^\d{18}$/.test(val)) {
                $('#btn-cari-pegawai').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
                $.ajax({
                    url: '<?= base_url("api/pegawai-by-nip") ?>/' + val,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        $('#btn-cari-pegawai').prop('disabled', false).html('<i class="bi bi-search"></i> Cari');
                        if (response.status === 'success') {
                            $('#nama_tamu').val(response.data.nama);
                            if (response.data.instansi) {
                                $('#instansi').val(response.data.instansi);
                            }
                            if (response.data.bidang) {
                                $('#bidang').val(response.data.bidang);
                            }
                            if (response.data.subbidang) {
                                $('#subbidang').val(response.data.subbidang);
                            }
                            showAppToast('success', 'Data pegawai ditemukan: ' + response.data.nama);
                        } else {
                            showAppToast('info', 'NIP/NIK tidak terdaftar di database pegawai, silakan ketik nama secara manual.');
                        }
                    },
                    error: function() {
                        $('#btn-cari-pegawai').prop('disabled', false).html('<i class="bi bi-search"></i> Cari');
                        showAppToast('error', 'Gagal menghubungi server untuk pencarian pegawai.');
                    }
                });
            } else {
                showAppToast('warning', 'Pencarian database hanya untuk NIP/NIK angka dengan panjang 18 digit.');
            }
        }

        $('#btn-cari-pegawai').on('click', function() {
            cariPegawai();
        });

        // Cascading Target Employee Filters
        function loadSubbagianTarget(kodeOpd, kodeBagian) {
            let subbagianSelect = $('#filter_subbagian');
            if(subbagianSelect.length === 0) return;
            subbagianSelect.empty().append('<option value="">-- Semua Subbidang --</option>');
            if (!kodeOpd || !kodeBagian) return;

            $.ajax({
                url: '<?= base_url("api/subbagian") ?>/' + kodeOpd + '/' + kodeBagian,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    if (data.length > 0) {
                        $.each(data, function(key, val) {
                            subbagianSelect.append('<option value="' + val.kode_subbagian + '">' + val.nama_subbagian + '</option>');
                        });
                    }
                }
            });
        }

        function loadPegawaiTarget(kodeOpd, kodeBagian, kodeSubbagian) {
            let pegawaiSelect = $('#id_pegawai_tujuan');
            if (!kodeOpd) return; 

            let url = '<?= base_url("api/pegawai") ?>/' + kodeOpd;
            if (kodeBagian) {
                url += '/' + kodeBagian;
                if (kodeSubbagian) {
                    url += '/' + kodeSubbagian;
                }
            }

            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    pegawaiSelect.empty().append('<option value="">-- Pilih Pegawai --</option>');
                    if (data.length > 0) {
                        $.each(data, function(key, val) {
                            pegawaiSelect.append('<option value="' + val.id + '">' + val.nama + (val.jabatan ? ' (' + val.jabatan + ')' : '') + '</option>');
                        });
                    }
                }
            });
        }

        $('#filter_bagian').on('change', function() {
            let kodeOpd = $('#kode_opd_val').val();
            let kodeBagian = $(this).val();
            loadSubbagianTarget(kodeOpd, kodeBagian);
            loadPegawaiTarget(kodeOpd, kodeBagian, '');
        });

        $('#filter_subbagian').on('change', function() {
            let kodeOpd = $('#kode_opd_val').val();
            let kodeBagian = $('#filter_bagian').val();
            let kodeSubbagian = $(this).val();
            loadPegawaiTarget(kodeOpd, kodeBagian, kodeSubbagian);
        });

        $('#nik').on('change blur', function() {
            let val = $(this).val().replace(/\s+/g, '');
            if (/^\d{18}$/.test(val)) {
                cariPegawai();
            }
        });

        // Form Submit Handler
        $('#guestForm').on('submit', function(e) {
            // Validate photo
            if (!$('#foto_tamu').val()) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Foto Wajib!',
                    text: 'Silakan ambil foto / selfie Anda terlebih dahulu menggunakan kamera.',
                    confirmButtonColor: '#4f46e5'
                });
                return false;
            }

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

            // Copy signature value
            $('#tanda_tangan').val(sigPad.toDataURL());

            // Disable submit to prevent double-submitting
            $('#btn-submit').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Mengirim...');
            
            stopWebcam();
        });
    });

    function clearSignature() {
        if (sigPad) {
            sigPad.clear();
        }
    }

    // Webcam Control Logic
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
                $(status).text('Kamera Aktif - Siap mengambil foto');
                $(status).removeClass('bg-dark').addClass('bg-success');
            })
            .catch(function(err) {
                showAppToast('error', 'Kamera gagal diakses. Pastikan izin kamera telah diberikan.');
                $(status).text('Akses kamera gagal / ditolak.');
                $(status).removeClass('bg-dark').addClass('bg-danger');
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
            $(status).text('Foto berhasil diambil!');
            $(status).removeClass('bg-success').addClass('bg-primary');
            
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
