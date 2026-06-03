<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>Manajemen User<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row mb-4 align-items-center">
    <div class="col-sm-6">
        <h2 class="fw-bold text-dark">Manajemen User</h2>
        <p class="text-secondary mb-0">Kelola akun administrator unit kerja / OPD.</p>
    </div>
    <div class="col-sm-6 text-sm-end mt-3 mt-sm-0">
        <a href="<?= base_url('users/create') ?>" class="btn btn-primary rounded-pill px-4">
            <i class="bi bi-person-plus me-2"></i>Tambah Admin
        </a>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="glass-card">
            <div class="glass-card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="usersTable">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4" style="width: 80px;">No</th>
                                <th>Username</th>
                                <th>Nama Lengkap</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Unit Kerja</th>
                                <th>Status</th>
                                <th class="text-end pe-4" style="width: 200px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach ($users as $u): ?>
                            <tr>
                                <td class="ps-4"><?= $no++ ?></td>
                                <td><span class="fw-semibold text-indigo">@<?= esc($u['username']) ?></span></td>
                                <td class="fw-semibold text-dark"><?= esc($u['nama'] ?? '-') ?></td>
                                <td><?= esc($u['email'] ?? '') ?></td>
                                <td>
                                    <span class="badge rounded-pill bg-<?= $u['group'] === 'superadmin' ? 'dark' : 'primary-subtle text-primary' ?> px-3 text-capitalize">
                                        <?= esc($u['group'] ?? 'admin') ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($u['group'] === 'superadmin'): ?>
                                        <span class="text-muted small">Semua Unit (Super)</span>
                                    <?php else: ?>
                                        <?php if ($u['nama_subbagian'] !== '-'): ?>
                                            <div class="fw-semibold text-secondary small"><?= esc($u['nama_subbagian']) ?></div>
                                            <div class="text-muted small" style="font-size: 0.75rem;"><?= esc($u['nama_bagian']) ?></div>
                                        <?php else: ?>
                                            <div class="fw-semibold text-secondary small"><?= esc($u['nama_bagian'] !== '-' ? $u['nama_bagian'] : ($u['kode_opd'] ?? '-')) ?></div>
                                        <?php endif; ?>
                                        <div class="text-muted small" style="font-size: 0.75rem;"><?= esc($u['nama_opd'] ?? '-') ?></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge rounded-pill bg-<?= $u['status_akun'] === 'aktif' ? 'success' : 'danger' ?>-subtle text-<?= $u['status_akun'] === 'aktif' ? 'success' : 'danger' ?> px-3">
                                        <?= esc($u['status_akun']) ?>
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <?php if ($u['group'] !== 'superadmin'): ?>
                                        <a href="<?= base_url('users/edit/' . encode_id($u['id'])) ?>" class="btn btn-sm btn-light border me-1" title="Ubah User">
                                            <i class="bi bi-pencil-fill text-warning"></i>
                                        </a>
                                        <a href="<?= base_url('users/reset-password/' . encode_id($u['id'])) ?>" class="btn btn-sm btn-light border me-1" title="Reset Sandi">
                                            <i class="bi bi-key-fill text-primary"></i>
                                        </a>
                                        <button class="btn btn-sm btn-light border btn-toggle-status" 
                                                data-url="<?= base_url('users/toggle-status/' . encode_id($u['id'])) ?>" 
                                                data-username="<?= esc($u['username']) ?>"
                                                title="<?= $u['status_akun'] === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' ?>">
                                            <i class="bi bi-power text-<?= $u['status_akun'] === 'aktif' ? 'danger' : 'success' ?>"></i>
                                        </button>
                                    <?php else: ?>
                                        <span class="text-muted small italic">Sistem Utama</span>
                                    <?php endif; ?>
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
        $('#usersTable').DataTable({
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

        // Toggle Status confirmation
        $('.btn-toggle-status').on('click', function(e) {
            e.preventDefault();
            const actionUrl = $(this).data('url');
            const username = $(this).data('username');
            
            Swal.fire({
                title: 'Ubah Status Akun?',
                text: `Apakah Anda yakin ingin mengubah status aktif/nonaktif akun @${username}?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#4f46e5',
                cancelButtonColor: '#cbd5e1',
                confirmButtonText: 'Ya, ubah!',
                cancelButtonText: 'Batal',
                background: '#ffffff',
                customClass: {
                    confirmButton: 'btn btn-primary rounded-pill px-4 me-2',
                    cancelButton: 'btn btn-light rounded-pill px-4'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = actionUrl;
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>
