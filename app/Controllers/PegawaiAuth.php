<?php

namespace App\Controllers;

use App\Models\PegawaiModel;

class PegawaiAuth extends BaseController
{
    public function login()
    {
        // If already logged in as pegawai, redirect to portal
        if (session()->get('pegawai_logged_in')) {
            return redirect()->to('pegawai-portal/dashboard');
        }

        return view('pegawai_portal/login');
    }

    public function attemptLogin()
    {
        $rules = [
            'nip'      => 'required',
            'password' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'NIP dan Password harus diisi.');
        }

        $nip      = $this->request->getPost('nip');
        $password = $this->request->getPost('password');

        // Login hanya mengecek database lokal

        $pegawaiModel = new PegawaiModel();
        $pegawai = $pegawaiModel->where('nip', $nip)->first();

        if (!$pegawai) {
            return redirect()->back()->withInput()->with('error', 'NIP tidak ditemukan.');
        }

        if ($pegawai['status'] !== 'aktif') {
            return redirect()->back()->withInput()->with('error', 'Akun pegawai Anda tidak aktif. Hubungi Admin.');
        }

        if (empty($pegawai['password'])) {
            return redirect()->back()->withInput()->with('error', 'Password belum diatur. Hubungi Admin untuk mengatur password Anda.');
        }

        if (!password_verify($password, $pegawai['password'])) {
            return redirect()->back()->withInput()->with('error', 'Password salah.');
        }

        // Set session
        session()->set([
            'pegawai_logged_in' => true,
            'pegawai_id'        => $pegawai['id'],
            'pegawai_nip'       => $pegawai['nip'],
            'pegawai_nama'      => $pegawai['nama'],
            'pegawai_jabatan'   => $pegawai['jabatan'],
            'pegawai_kode_opd'  => $pegawai['kode_opd'],
            'pegawai_kode_bagian' => $pegawai['kode_bagian'],
        ]);

        return redirect()->to('pegawai-portal/dashboard')->with('success', 'Selamat datang, ' . $pegawai['nama'] . '!');
    }

    public function logout()
    {
        session()->remove([
            'pegawai_logged_in',
            'pegawai_id',
            'pegawai_nip',
            'pegawai_nama',
            'pegawai_jabatan',
            'pegawai_kode_opd',
            'pegawai_kode_bagian',
        ]);

        return redirect()->to('pegawai-portal/login')->with('success', 'Anda berhasil logout.');
    }
}
