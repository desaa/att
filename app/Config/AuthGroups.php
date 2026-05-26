<?php

declare(strict_types=1);

namespace Config;

use CodeIgniter\Shield\Config\AuthGroups as ShieldAuthGroups;

class AuthGroups extends ShieldAuthGroups
{
    /**
     * --------------------------------------------------------------------
     * Default Group
     * --------------------------------------------------------------------
     * The group that a newly registered user is added to.
     */
    public string $defaultGroup = 'admin';

    /**
     * --------------------------------------------------------------------
     * Groups
     * --------------------------------------------------------------------
     * An associative array of the available groups in the system.
     */
    public array $groups = [
        'superadmin' => [
            'title'       => 'Super Administrator',
            'description' => 'Akses penuh ke seluruh sistem.',
        ],
        'admin' => [
            'title'       => 'Administrator',
            'description' => 'Akses terbatas berdasarkan unit kerja.',
        ],
    ];

    /**
     * --------------------------------------------------------------------
     * Permissions
     * --------------------------------------------------------------------
     * The available permissions in the system.
     */
    public array $permissions = [
        'tamu.view'      => 'Melihat data tamu',
        'tamu.create'    => 'Menambah data tamu',
        'tamu.edit'      => 'Mengedit data tamu',
        'tamu.delete'    => 'Menghapus data tamu (Superadmin only)',
        'laporan.export' => 'Export laporan PDF/Excel',
        'agenda.manage'  => 'Kelola agenda dan QR Code',
        'user.manage'    => 'Kelola user (Superadmin only)',
        'master.manage'  => 'Kelola data master OPD/Bagian/Pegawai',
    ];

    /**
     * --------------------------------------------------------------------
     * Permissions Matrix
     * --------------------------------------------------------------------
     * Maps permissions to groups.
     */
    public array $matrix = [
        'superadmin' => [
            'tamu.*',
            'laporan.*',
            'agenda.*',
            'user.*',
            'master.*',
        ],
        'admin' => [
            'tamu.view',
            'tamu.create',
            'tamu.edit',
            'laporan.export',
            'agenda.manage',
        ],
    ];
}
