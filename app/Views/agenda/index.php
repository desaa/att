<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>Manajemen Agenda<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row mb-4 align-items-center">
    <div class="col-sm-6">
        <h2 class="fw-bold text-dark">Manajemen Agenda</h2>
        <p class="text-secondary mb-0">Kelola agenda kegiatan dan generate QR Code pendaftaran mandiri.</p>
    </div>
    <div class="col-sm-6 text-sm-end mt-3 mt-sm-0">
        <a href="<?= base_url('agenda/create') ?>" class="btn btn-primary rounded-pill px-4">
            <i class="bi bi-plus-lg me-2"></i>Buat Agenda
        </a>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="glass-card">
            <div class="glass-card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="agendaTable">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4" style="width: 50px;">No</th>
                                <th>Nama Agenda</th>
                                <th>Masa Aktif</th>
                                <th>Lokasi &amp; PJ</th>
                                <th>Unit Kerja</th>
                                <th>Status</th>
                                <th class="text-end pe-4 text-nowrap" style="width: 240px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach ($agendas as $agenda): ?>
                            <tr>
                                <td class="ps-4"><?= $no++ ?></td>
                                <td>
                                    <div class="fw-bold text-dark"><?= esc($agenda['nama_agenda']) ?></div>
                                    <div class="small text-muted text-truncate" style="max-width: 250px;"><?= esc($agenda['deskripsi'] ?: 'Tanpa deskripsi') ?></div>
                                </td>
                                <td>
                                    <div class="small fw-semibold text-dark"><i class="bi bi-calendar-event me-1"></i>Mulai: <?= date('Y-m-d H:i', strtotime($agenda['tanggal_mulai'])) ?></div>
                                    <div class="small text-secondary"><i class="bi bi-calendar-x me-1"></i>Selesai: <?= date('Y-m-d H:i', strtotime($agenda['tanggal_selesai'])) ?></div>
                                </td>
                                <td>
                                    <div class="small fw-semibold text-dark"><i class="bi bi-geo-alt me-1"></i><?= esc($agenda['lokasi']) ?></div>
                                    <div class="small text-secondary"><i class="bi bi-person me-1"></i>PJ: <?= esc($agenda['penanggung_jawab']) ?></div>
                                </td>
                                <td>
                                    <div class="small fw-semibold text-secondary"><?= esc($agenda['nama_bagian']) ?></div>
                                    <div class="text-muted small" style="font-size: 0.725rem;"><?= esc($agenda['nama_opd']) ?></div>
                                </td>
                                <td>
                                    <span class="badge rounded-pill bg-<?= $agenda['status'] === 'aktif' ? 'success' : ($agenda['status'] === 'selesai' ? 'primary' : 'danger') ?>-subtle text-<?= $agenda['status'] === 'aktif' ? 'success' : ($agenda['status'] === 'selesai' ? 'primary' : 'danger') ?> px-3">
                                        <?= esc($agenda['status']) ?>
                                    </span>
                                </td>
                                <td class="text-end pe-4 text-nowrap">
                                    <button class="btn btn-sm btn-info text-white border me-1" 
                                            onclick="showQrModal('<?= esc($agenda['nama_agenda'], 'js') ?>', '<?= esc($agenda['nama_bagian'], 'js') ?>', '<?= esc($agenda['qr_image']) ?>', '<?= base_url('tamu/agenda/' . $agenda['qr_code']) ?>')" 
                                            title="Tampilkan QR Code">
                                        <i class="bi bi-qr-code"></i> QR
                                    </button>
                                    <?php if ($agenda['status'] === 'aktif'): ?>
                                    <button class="btn btn-sm btn-success text-white border btn-complete me-1" 
                                            data-url="<?= base_url('agenda/complete/' . encode_id($agenda['id_agenda'])) ?>" 
                                            data-name="<?= esc($agenda['nama_agenda']) ?>"
                                            title="Selesaikan Agenda">
                                        <i class="bi bi-check-circle-fill"></i> Selesai
                                    </button>
                                    <?php endif; ?>
                                    <a href="<?= base_url('agenda/edit/' . encode_id($agenda['id_agenda'])) ?>" class="btn btn-sm btn-light border me-1" title="Ubah">
                                        <i class="bi bi-pencil-fill text-warning"></i>
                                    </a>
                                    <button class="btn btn-sm btn-light border btn-delete" 
                                            data-url="<?= base_url('agenda/delete/' . encode_id($agenda['id_agenda'])) ?>" 
                                            title="Hapus">
                                        <i class="bi bi-trash3-fill text-danger"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- QR Code Modal -->
<div class="modal fade" id="qrModal" tabindex="-1" aria-labelledby="qrModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 1rem;">
            <div class="modal-header border-bottom-0 pt-4 px-4">
                <h5 class="modal-title fw-bold" id="qrModalLabel">QR Code Agenda</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4 text-center">
                <!-- Printable QR Container -->
                <div id="printArea" class="p-3 bg-white border rounded">
                    <h4 class="fw-bold text-dark mb-1" id="qrAgendaName">Nama Agenda</h4>
                    <p class="text-muted small mb-3" id="qrOpdName">Nama Unit Kerja</p>
                    
                    <img id="qrImage" src="" alt="QR Code" class="img-fluid mb-3" style="width: 250px; height: 250px;">
                    
                    <p class="text-secondary small mb-0 fw-semibold">SCAN DENGAN HP ANDA</p>
                    <p class="text-muted" style="font-size: 0.75rem;">Untuk Mengisi Buku Tamu Mandiri</p>
                </div>
                
                <div class="mt-4 mb-2">
                    <label class="form-label small fw-semibold text-secondary">Tautan Publik (URL):</label>
                    <div class="input-group">
                        <input type="text" class="form-control form-control-sm font-monospace bg-light" id="qrUrl" readonly>
                        <button class="btn btn-sm btn-outline-primary" onclick="copyUrl()"><i class="bi bi-copy"></i> Salin</button>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top-0 pb-4 px-4 justify-content-center gap-2">
                <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary rounded-pill px-4" onclick="printQr()"><i class="bi bi-printer-fill me-1"></i> Cetak</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        $('#agendaTable').DataTable({
            responsive: true,
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Lanjut",
                    previous: "Kembali"
                }
            }
        });

        // Delete confirmation
        $('.btn-delete').on('click', function(e) {
            e.preventDefault();
            const deleteUrl = $(this).data('url');
            
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data Agenda akan dihapus! Tamu tidak dapat melakukan registrasi menggunakan QR Code agenda ini lagi.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#cbd5e1',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal',
                background: '#ffffff',
                customClass: {
                    confirmButton: 'btn btn-danger rounded-pill px-4 me-2',
                    cancelButton: 'btn btn-light rounded-pill px-4'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = deleteUrl;
                    
                    const csrf = document.createElement('input');
                    csrf.type = 'hidden';
                    csrf.name = $('#csrf-token').attr('name');
                    csrf.value = $('#csrf-token').attr('value');
                    
                    form.appendChild(csrf);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });

        // Complete confirmation
        $('.btn-complete').on('click', function(e) {
            e.preventDefault();
            const completeUrl = $(this).data('url');
            const agendaName = $(this).data('name');
            
            Swal.fire({
                title: 'Selesaikan Agenda?',
                text: `Apakah Anda yakin ingin menyelesaikan agenda "${agendaName}"? Semua tamu yang terdaftar di agenda ini akan diselesaikan secara otomatis dan jam pulang mereka akan diisi.`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#cbd5e1',
                confirmButtonText: 'Ya, selesaikan!',
                cancelButtonText: 'Batal',
                background: '#ffffff',
                customClass: {
                    confirmButton: 'btn btn-success rounded-pill px-4 me-2',
                    cancelButton: 'btn btn-light rounded-pill px-4'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = completeUrl;
                }
            });
        });
    });

    function showQrModal(name, opd, qrImgSrc, url) {
        $('#qrAgendaName').text(name);
        $('#qrOpdName').text(opd);
        $('#qrImage').attr('src', qrImgSrc);
        $('#qrUrl').val(url);
        $('#qrModal').modal('show');
    }

    function copyUrl() {
        const urlInput = document.getElementById("qrUrl");
        urlInput.select();
        urlInput.setSelectionRange(0, 99999); // For mobile devices
        
        navigator.clipboard.writeText(urlInput.value).then(() => {
            showAppToast('success', 'Tautan berhasil disalin!');
        }, () => {
            showAppToast('error', 'Gagal menyalin tautan.');
        });
    }

    function printQr() {
        const printContent = document.getElementById('printArea').innerHTML;
        const originalContent = document.body.innerHTML;
        
        // Simple printable window
        const printWindow = window.open('', '_blank', 'height=600,width=800');
        printWindow.document.write('<html><head><title>Cetak QR Code</title>');
        printWindow.document.write('<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">');
        printWindow.document.write('<style>body{display:flex;align-items:center;justify-content:center;height:100vh;text-align:center;} #printArea{border:none !important;}</style>');
        printWindow.document.write('</head><body>');
        printWindow.document.write('<div id="printArea" class="text-center">' + printContent + '</div>');
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        
        // Wait for CSS/Image to load before print
        setTimeout(function() {
            printWindow.print();
            printWindow.close();
        }, 500);
    }
</script>
<?= $this->endSection() ?>
