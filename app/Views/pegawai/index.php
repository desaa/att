<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>Master Pegawai<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row mb-4 align-items-center">
    <div class="col-sm-6">
        <h2 class="fw-bold text-dark">Master Pegawai</h2>
        <p class="text-secondary mb-0">Daftar Pegawai yang menjadi target kunjungan tamu.</p>
    </div>
    <div class="col-sm-6 text-sm-end mt-3 mt-sm-0">
        <a href="<?= base_url('pegawai/create') ?>" class="btn btn-primary rounded-pill px-4">
            <i class="bi bi-plus-lg me-2"></i>Tambah Pegawai
        </a>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="glass-card">
            <div class="glass-card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="pegawaiTable">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4" style="width: 80px;">No</th>
                                <th>NIP</th>
                                <th>Nama Pegawai</th>
                                <th>Jabatan</th>
                                <th>Unit Kerja</th>
                                <th>Status</th>
                                <th class="text-end pe-4" style="width: 150px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach ($pegawais as $pegawai): ?>
                            <tr>
                                <td class="ps-4"><?= $no++ ?></td>
                                <td><span class="font-monospace text-primary fw-semibold"><?= esc($pegawai['nip']) ?></span></td>
                                <td class="fw-semibold text-dark"><?= esc($pegawai['nama']) ?></td>
                                <td><?= esc($pegawai['jabatan']) ?></td>
                                <td>
                                    <div class="fw-semibold text-secondary small"><?= esc($pegawai['nama_bagian']) ?></div>
                                    <div class="text-muted small" style="font-size: 0.75rem;"><?= esc($pegawai['nama_opd']) ?></div>
                                    <?php if ($pegawai['nama_subbagian']): ?>
                                        <div class="text-muted small" style="font-size: 0.725rem;"><i class="bi bi-arrow-return-right me-1"></i><?= esc($pegawai['nama_subbagian']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge rounded-pill bg-<?= $pegawai['status'] === 'aktif' ? 'success' : 'danger' ?>-subtle text-<?= $pegawai['status'] === 'aktif' ? 'success' : 'danger' ?> px-3">
                                        <?= esc($pegawai['status']) ?>
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="<?= base_url('pegawai/edit/' . $pegawai['id']) ?>" class="btn btn-sm btn-light border me-1" title="Ubah">
                                        <i class="bi bi-pencil-fill text-warning"></i>
                                    </a>
                                    <button class="btn btn-sm btn-light border btn-delete" 
                                            data-url="<?= base_url('pegawai/delete/' . $pegawai['id']) ?>" 
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
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        $('#pegawaiTable').DataTable({
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
                text: "Data Pegawai akan dihapus dari sistem master!",
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
    });
</script>
<?= $this->endSection() ?>
