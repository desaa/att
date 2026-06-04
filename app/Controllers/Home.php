<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        if (auth()->loggedIn()) {
            return redirect()->to('dashboard');
        }
        
        if (session()->get('pegawai_logged_in')) {
            return redirect()->to('pegawai-portal/dashboard');
        }
        
        return redirect()->to('pegawai-portal/login');
    }
}
