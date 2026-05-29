<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Shield\Entities\User;

class Users extends BaseController
{
    public function index()
    {
        $userModel = new UserModel();
        
        $data['users'] = $userModel->select('users.*, auth_identities.secret as email, auth_groups_users.group, opd.nama_opd, bagian.nama_bagian')
                                   ->join('auth_identities', 'auth_identities.user_id = users.id AND auth_identities.type = "email_password"', 'left')
                                   ->join('auth_groups_users', 'auth_groups_users.user_id = users.id', 'left')
                                   ->join('opd', 'opd.kode_opd = users.kode_opd', 'left')
                                   ->join('bagian', 'bagian.kode_opd = users.kode_opd AND bagian.kode_bagian = users.kode_bagian', 'left')
                                   ->orderBy('users.username', 'ASC')
                                   ->findAll();

        return view('users/index', $data);
    }

    public function create()
    {
        $db = \Config\Database::connect('simpelgan');
        $data['opds'] = $db->table('master_opd')->where('id_gov', 'P2300001')->orderBy('kode_opd', 'ASC')->get()->getResultArray();
        $data['bagians'] = [];
        $data['subbagians'] = [];
        return view('users/create', $data);
    }

    public function store()
    {
        $userModel = new UserModel();

        $rules = [
            'username'     => 'required|is_unique[users.username]|alpha_numeric_punct|min_length[3]|max_length[30]',
            'email'        => 'required|valid_email|is_unique[auth_identities.secret]',
            'password'     => 'required|min_length[8]',
            'nama'         => 'required|max_length[255]',
            'kode_opd'     => 'required',
            'kode_bagian'  => 'permit_empty',
            'status_akun'  => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode('<br>', $this->validator->getErrors()));
        }

        // Create user entity
        $user = new User([
            'username'    => $this->request->getPost('username'),
            'email'       => $this->request->getPost('email'),
            'password'    => $this->request->getPost('password'),
            'nama'        => $this->request->getPost('nama'),
            'kode_opd'    => $this->request->getPost('kode_opd'),
            'kode_bagian' => $this->request->getPost('kode_bagian'),
            'kode_subbagian' => $this->request->getPost('kode_subbagian') ?: null,
            'status_akun' => $this->request->getPost('status_akun'),
        ]);

        $userModel->save($user);
        
        $newUserId = $userModel->getInsertID();
        $newUser = $userModel->findById($newUserId);
        $newUser->addGroup('admin'); // All users created here are Unit Admins

        log_activity('Membuat admin baru: ' . $newUser->username, 'users', $newUserId);

        return redirect()->to('users')->with('success', 'User Admin berhasil dibuat!');
    }

    public function edit($id)
    {
        $userModel = new UserModel();
        
        // Find user with email joined
        $user = $userModel->select('users.*, auth_identities.secret as email')
                          ->join('auth_identities', 'auth_identities.user_id = users.id AND auth_identities.type = "email_password"', 'left')
                          ->find($id);

        if (!$user) {
            return redirect()->to('users')->with('error', 'User tidak ditemukan.');
        }

        $db = \Config\Database::connect('simpelgan');

        $data['opds']     = $db->table('master_opd')->where('id_gov', 'P2300001')->orderBy('kode_opd', 'ASC')->get()->getResultArray();
        $data['bagians']  = $db->table('master_bagian')->where('id_gov', 'P2300001')->where('kode_opd', $user->kode_opd)->orderBy('nama_bagian', 'ASC')->get()->getResultArray();
        
        if ($user->kode_bagian) {
            $data['subbagians'] = $db->table('master_subbagian')
                                     ->where('id_gov', 'P2300001')
                                     ->where('kode_opd', $user->kode_opd)
                                     ->where('kode_bagian', $user->kode_bagian)
                                     ->orderBy('nama_subbagian', 'ASC')
                                     ->get()->getResultArray();
        } else {
            $data['subbagians'] = [];
        }

        $data['user'] = $user;

        return view('users/edit', $data);
    }

    public function update($id)
    {
        $userModel = new UserModel();
        
        $rules = [
            'email'        => "required|valid_email|is_unique[auth_identities.secret,user_id,$id]",
            'nama'         => 'required|max_length[255]',
            'kode_opd'     => 'required',
            'kode_bagian'  => 'permit_empty',
            'status_akun'  => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode('<br>', $this->validator->getErrors()));
        }

        // Get Shield User entity
        $user = $userModel->findById($id);
        if (!$user) {
            return redirect()->to('users')->with('error', 'User tidak ditemukan.');
        }

        // Update fields
        $user->fill([
            'email'       => $this->request->getPost('email'),
            'nama'        => $this->request->getPost('nama'),
            'kode_opd'    => $this->request->getPost('kode_opd'),
            'kode_bagian' => $this->request->getPost('kode_bagian'),
            'kode_subbagian' => $this->request->getPost('kode_subbagian') ?: null,
            'status_akun' => $this->request->getPost('status_akun'),
        ]);

        $userModel->save($user);

        log_activity('Memperbarui profil admin: ' . $user->username, 'users', $id);

        return redirect()->to('users')->with('success', 'User Admin berhasil diperbarui!');
    }

    public function resetPassword($id)
    {
        $userModel = new UserModel();
        $user = $userModel->find($id);

        if (!$user) {
            return redirect()->to('users')->with('error', 'User tidak ditemukan.');
        }

        $data['user'] = $user;
        return view('users/reset_password', $data);
    }

    public function saveResetPassword($id)
    {
        $userModel = new UserModel();
        
        $rules = [
            'password' => 'required|min_length[8]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode('<br>', $this->validator->getErrors()));
        }

        $user = $userModel->findById($id);
        if (!$user) {
            return redirect()->to('users')->with('error', 'User tidak ditemukan.');
        }

        // Update password using Shield entity
        $user->fill([
            'password' => $this->request->getPost('password'),
        ]);
        $userModel->save($user);

        log_activity('Mereset password untuk admin: ' . $user->username, 'users', $id);

        return redirect()->to('users')->with('success', 'Password berhasil direset!');
    }

    public function toggleStatus($id)
    {
        $userModel = new UserModel();
        $user = $userModel->findById($id);

        if (!$user) {
            return redirect()->to('users')->with('error', 'User tidak ditemukan.');
        }

        // Prevent self disabling
        if ($user->id === auth()->id()) {
            return redirect()->to('users')->with('error', 'Anda tidak dapat menonaktifkan akun sendiri.');
        }

        $newStatus = $user->status_akun === 'aktif' ? 'nonaktif' : 'aktif';
        
        $user->fill([
            'status_akun' => $newStatus
        ]);
        $userModel->save($user);

        log_activity("Mengubah status admin $user->username menjadi: $newStatus", 'users', $id);

        return redirect()->to('users')->with('success', 'Status user berhasil diubah!');
    }
}
