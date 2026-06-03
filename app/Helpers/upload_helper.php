<?php

// app/Helpers/upload_helper.php

if (!function_exists('getUploadPath')) {
    /**
     * Get upload directory path with year/month structure.
     * Folder uploads/ berada 1 level di atas public/ (sejajar dengan public/).
     *
     * Struktur:
     *   <root>/
     *   ├── public/          ← FCPATH
     *   └── uploads/         ← di sini
     *       └── 2025/
     *           └── 06/
     *               ├── foto/
     *               ├── ttd/
     *               └── file/
     *
     * @param string $type 'foto' | 'ttd' | 'file'
     * @return string Full absolute path WITH trailing slash
     */
    function getUploadPath(string $type = 'foto'): string
    {
        $allowed = ['foto', 'ttd', 'file'];
        if (!in_array($type, $allowed, true)) {
            throw new \InvalidArgumentException("Tipe upload tidak valid: '{$type}'. Gunakan: " . implode(', ', $allowed));
        }

        $year  = date('Y');
        $month = date('m');

        // dirname(FCPATH) = 1 level di atas public/
        $uploadPath = dirname(FCPATH)
            . DIRECTORY_SEPARATOR . 'uploads'
            . DIRECTORY_SEPARATOR . $year
            . DIRECTORY_SEPARATOR . $month
            . DIRECTORY_SEPARATOR . $type
            . DIRECTORY_SEPARATOR;

        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true); // 0755 lebih aman dari 0777
        }

        return $uploadPath;
    }
}

if (!function_exists('getUploadUrl')) {
    /**
     * Get URL untuk mengakses file upload melalui route proxy.
     * Karena uploads/ di luar public/, file diakses via controller
     * bukan direct URL — sehingga aman dan tidak perlu symlink.
     *
     * URL format: /uploads/serve/foto/2025/06/namafile.png
     *
     * @param string $filename  Nama file (dari kolom database)
     * @param string $type      'foto' | 'ttd' | 'file'
     * @param string $year      Tahun upload (default: tahun sekarang)
     * @param string $month     Bulan upload (default: bulan sekarang)
     * @return string URL lengkap atau string kosong jika filename kosong
     */
    function getUploadUrl(string $filename, string $type = 'foto', string $year = '', string $month = ''): string
    {
        if (empty($filename)) {
            return '';
        }

        $year  = $year  ?: date('Y');
        $month = $month ?: date('m');

        // Route: tamu/uploads/{type}/{year}/{month}/{filename}
        return site_url("tamu/uploads/{$type}/{$year}/{$month}/" . rawurlencode($filename));
    }
}

if (!function_exists('getUploadInfo')) {
    /**
     * Get upload path dan URL prefix sekaligus.
     *
     * @param string $type 'foto' | 'ttd' | 'file'
     * @return array{path: string, url_prefix: string}
     */
    function getUploadInfo(string $type = 'foto'): array
    {
        $year  = date('Y');
        $month = date('m');

        return [
            'path'       => getUploadPath($type),
            'url_prefix' => site_url("tamu/uploads/{$type}/{$year}/{$month}/"),
        ];
    }
}

if (!function_exists('getUploadUrlFromDb')) {
    /**
     * Helper untuk view — ambil URL file dari data yang tersimpan di DB.
     * Mendukung nama file lama (dari folder attendance/) dan baru.
     *
     * Gunakan ini di view untuk menampilkan foto/ttd/dokumen:
     *   <img src="<?= getUploadUrlFromDb($tamu['foto'], 'foto') ?>">
     *
     * @param string|null $filename
     * @param string      $type
     * @param string      $year   Tahun saat file diupload (dari created_at jika tersedia)
     * @param string      $month  Bulan saat file diupload
     * @return string
     */
    function getUploadUrlFromDb(?string $filename, string $type = 'foto', string $year = '', string $month = ''): string
    {
        if (empty($filename)) {
            return '';
        }

        return getUploadUrl($filename, $type, $year, $month);
    }
}