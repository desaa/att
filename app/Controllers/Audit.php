<?php

namespace App\Controllers;

use App\Models\AuditLogModel;

class Audit extends BaseController
{
    public function index()
    {
        $auditLogModel = new AuditLogModel();
        
        $data['logs'] = $auditLogModel->select('audit_log.*, users.username')
                                      ->join('users', 'users.id = audit_log.user_id', 'left')
                                      ->orderBy('audit_log.created_at', 'DESC')
                                      ->findAll();

        return view('audit/index', $data);
    }
}
