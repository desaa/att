<?php

use App\Models\AuditLogModel;

if (! function_exists('log_activity')) {
    function log_activity(string $aktivitas, ?string $tabelTerkait = null, ?int $idRecord = null): void
    {
        $auditLogModel = new AuditLogModel();
        
        // auth()->id() returns the logged in user ID, or null for public actions (guest registrations)
        $userId = auth()->id();
        $ipAddress = service('request')->getIPAddress();

        $auditLogModel->save([
            'user_id'       => $userId,
            'aktivitas'     => $aktivitas,
            'tabel_terkait' => $tabelTerkait,
            'id_record'     => $idRecord,
            'ip_address'    => $ipAddress,
        ]);
    }
}
