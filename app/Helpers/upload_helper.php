<?php
// app/Helpers/upload_helper.php

if (!function_exists('getUploadPath')) {
    function getUploadPath(string $type = 'foto'): string
    {
        $allowed = ['foto', 'ttd', 'file'];
        if (!in_array($type, $allowed, true)) {
            throw new \InvalidArgumentException("Tipe upload tidak valid: '{$type}'. Gunakan: " . implode(', ', $allowed));
        }
        $year  = date('Y');
        $month = date('m');
        $uploadPath = dirname(FCPATH)
            . DIRECTORY_SEPARATOR . 'uploads'
            . DIRECTORY_SEPARATOR . $year
            . DIRECTORY_SEPARATOR . $month
            . DIRECTORY_SEPARATOR . $type
            . DIRECTORY_SEPARATOR;
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }
        return $uploadPath;
    }
}

if (!function_exists('getUploadUrl')) {
    function getUploadUrl(string $filename, string $type = 'foto', string $year = '', string $month = ''): string
    {
        if (empty($filename)) {
            return '';
        }
        $year  = $year  ?: date('Y');
        $month = $month ?: date('m');
        return site_url("tamu/uploads/{$type}/{$year}/{$month}/" . rawurlencode($filename));
    }
}

if (!function_exists('getUploadUrlFromRecord')) {
    /**
     * Generate URL upload berdasarkan timestamp record (waktu_datang / created_at).
     * Otomatis ekstrak year/month dari timestamp agar URL selalu tepat
     * meski file diupload bulan/tahun berbeda.
     *
     * Penggunaan di view:
     *   getUploadUrlFromRecord($tamu['foto'],           'foto', $tamu['waktu_datang'])
     *   getUploadUrlFromRecord($tamu['tanda_tangan'],   'ttd',  $tamu['waktu_datang'])
     *   getUploadUrlFromRecord($tamu['dokumen_pendukung'], 'file', $tamu['waktu_datang'])
     */
    function getUploadUrlFromRecord(?string $filename, string $type, ?string $timestamp): string
    {
        if (empty($filename)) return '';
        $year  = $timestamp ? date('Y', strtotime($timestamp)) : date('Y');
        $month = $timestamp ? date('m', strtotime($timestamp)) : date('m');
        return getUploadUrl($filename, $type, $year, $month);
    }
}

if (!function_exists('getUploadInfo')) {
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
    function getUploadUrlFromDb(?string $filename, string $type = 'foto', string $year = '', string $month = ''): string
    {
        if (empty($filename)) {
            return '';
        }
        return getUploadUrl($filename, $type, $year, $month);
    }
}