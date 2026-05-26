# Product Requirements Document (PRD)
# Sistem Informasi Buku Tamu Elektronik
# Dinas Komunikasi dan Informatika Kabupaten Grobogan

---

## 1. Latar Belakang

Dalam rangka meningkatkan kualitas pelayanan publik, tertib administrasi, keamanan data kunjungan, serta mendukung transformasi digital di lingkungan Dinas Komunikasi dan Informatika Kabupaten Grobogan, diperlukan sebuah Sistem Informasi Buku Tamu Elektronik yang mampu menggantikan proses pencatatan tamu secara manual.

Sistem ini digunakan untuk mencatat identitas tamu, tujuan kunjungan, pegawai/ruangan yang dituju, waktu kedatangan, serta menghasilkan laporan dan statistik kunjungan secara digital, cepat, dan terintegrasi.

---

## 2. Tujuan Sistem

**Tujuan Utama:**
- Digitalisasi proses buku tamu
- Mempermudah pencatatan dan pencarian data tamu
- Menyediakan laporan kunjungan secara realtime
- Mendukung monitoring tamu berdasarkan bagian/subbagian
- Mengurangi penggunaan buku tamu manual

**Tujuan Tambahan:**
- Menyediakan dashboard statistik kunjungan
- Mempermudah audit dan histori kunjungan
- Mendukung pelayanan tamu yang lebih profesional

---

## 3. Ruang Lingkup Sistem

Modul utama sistem meliputi:

| No | Modul |
|----|-------|
| 1 | Login dan Manajemen Hak Akses |
| 2 | Pencatatan Buku Tamu |
| 3 | Manajemen OPD, Bagian, dan Subbagian |
| 4 | Manajemen Pegawai |
| 5 | Dashboard Statistik |
| 6 | Laporan dan Export Data |
| 7 | Manajemen Pengguna |
| 8 | Manajemen Agenda / Acara |
| 9 | Form Tamu Mandiri (QR Code) |
| 10 | Pengaturan Sistem |

---

## 4. Role dan Hak Akses

### 4.1 Superadmin

| Hak Akses |
|-----------|
| Kelola seluruh data tamu (termasuk hapus permanen) |
| Kelola seluruh admin |
| Kelola master OPD |
| Kelola master bagian |
| Kelola master subbagian |
| Kelola master pegawai |
| Melihat seluruh laporan |
| Export seluruh data |
| Dashboard keseluruhan |
| Pengaturan aplikasi |

### 4.2 Admin

Admin dibatasi berdasarkan:
- `kode_opd`
- `kode_bagian`
- `kode_subbagian` *(opsional)*

| Hak Akses |
|-----------|
| Melihat data tamu pada unit kerjanya |
| Input/edit data tamu unit kerja sendiri |
| Melihat laporan unit kerja sendiri |
| Export laporan unit kerja sendiri |
| Dashboard statistik unit kerja sendiri |
| Membuat dan mengelola agenda unit kerja sendiri |

> **Catatan:** Admin **tidak dapat menghapus** data tamu secara permanen. Penghapusan data hanya dapat dilakukan oleh Superadmin untuk menjaga integritas audit.

---

## 5. Struktur Organisasi

### 5.1 Tabel OPD

| Field | Keterangan |
|-------|------------|
| `kode_opd` | Kode unik OPD |
| `nama_opd` | Nama OPD |

**Contoh:**
```
05 | Dinas Komunikasi dan Informatika
```

### 5.2 Tabel Bagian

| Field | Keterangan |
|-------|------------|
| `kode_opd` | FK ke tabel OPD |
| `kode_bagian` | Kode unik bagian |
| `nama_bagian` | Nama bagian |

**Contoh:**
```
05 | 01 | Sekretariat
05 | 02 | Infrastruktur TIK
```

### 5.3 Tabel Subbagian

| Field | Keterangan |
|-------|------------|
| `kode_opd` | FK ke tabel OPD |
| `kode_bagian` | FK ke tabel Bagian |
| `kode_subbagian` | Kode unik subbagian |
| `nama_subbagian` | Nama subbagian |

**Contoh:**
```
05 | 01 | 001 | Umum dan Kepegawaian
05 | 01 | 002 | Perencanaan dan Keuangan
```

---

## 6. Fitur Utama Sistem

### 6.1 Login Sistem

- Login username/password
- Logout
- Session management *(timeout idle: **30 menit**)*
- Role-based access control

**Validasi:**
- Password terenkripsi (bcrypt/Argon2)
- Pembatasan akses berdasarkan role
- Proteksi brute-force login (lockout setelah percobaan gagal berulang)

### 6.2 Dashboard

**Superadmin:**
- Total kunjungan hari ini
- Total kunjungan bulan ini
- Statistik per OPD
- Statistik per bagian
- Grafik kunjungan (harian/bulanan/tahunan)
- Daftar tamu terbaru

**Admin:**
- Statistik kunjungan unit kerja sendiri
- Jumlah tamu hari ini
- Daftar tamu terbaru unit kerja

### 6.3 Buku Tamu Elektronik

Data yang dicatat:

| Field | Keterangan | Wajib |
|-------|------------|-------|
| Nama Tamu | Nama lengkap tamu | ✅ |
| NIK | 16 digit, divalidasi format | ✅ |
| Instansi/Asal | Nama instansi atau asal tamu | ✅ |
| Nomor HP | Format nomor telepon valid | ✅ |
| Alamat | Alamat lengkap tamu | ✅ |
| Keperluan | Tujuan kunjungan | ✅ |
| Pegawai yang Dituju | Dipilih dari master pegawai | ✅ |
| OPD Tujuan | FK ke tabel OPD | ✅ |
| Bagian Tujuan | FK ke tabel Bagian | ✅ |
| Subbagian Tujuan | FK ke tabel Subbagian | ❌ |
| Waktu Datang | Timestamp otomatis | ✅ |
| Waktu Pulang | Diisi saat tamu keluar | ❌ |
| Foto Tamu | Upload gambar (JPG/PNG) | ❌ |
| Tanda Tangan Digital | Canvas/upload | ❌ |
| Status Kunjungan | `menunggu` / `berlangsung` / `selesai` / `batal` | ✅ |
| ID Agenda | FK ke tabel Agenda (jika via QR) | ❌ |

### 6.4 Data Tamu

**Fitur:**
- List data tamu dengan paginasi
- Detail tamu
- Edit data tamu
- Hapus data *(Superadmin only)*
- Pencarian data

**Filter:**
- Tanggal / rentang tanggal
- OPD, Bagian, Subbagian
- Nama tamu / instansi
- Pegawai yang dituju
- Status kunjungan
- ID agenda

### 6.5 Reporting dan Laporan

**Jenis Laporan:**

| No | Laporan |
|----|---------|
| 1 | Laporan Harian |
| 2 | Laporan Bulanan |
| 3 | Laporan Tahunan |
| 4 | Statistik per Bagian |
| 5 | Statistik per Pegawai yang Dituju |
| 6 | Rekapitulasi Kunjungan |

**Format Export:** PDF, Excel (.xlsx)

**Filter Laporan:**
- Rentang tanggal
- OPD / Bagian / Subbagian
- Status kunjungan
- Agenda

### 6.6 Manajemen User

Superadmin dapat:
- Tambah admin
- Edit data admin
- Reset password
- Aktivasi/nonaktifkan user

**Validasi:**
- Username unik
- Password minimal 8 karakter
- Satu admin wajib terikat ke minimal satu OPD

### 6.7 Manajemen Agenda / Acara

**Fitur:**
- Admin membuat agenda/acara
- Generate QR Code otomatis per agenda
- Pengaturan masa aktif QR Code (tanggal mulai – tanggal berakhir)
- Penentuan lokasi kegiatan
- Penentuan unit kerja penyelenggara
- Monitoring jumlah peserta/tamu per agenda

**Data Agenda:**

| Field | Keterangan |
|-------|------------|
| `id_agenda` | Primary key unik |
| `nama_agenda` | Nama acara/kegiatan |
| `deskripsi` | Deskripsi kegiatan |
| `tanggal_mulai` | Tanggal mulai kegiatan |
| `tanggal_selesai` | Tanggal berakhir kegiatan |
| `lokasi` | Lokasi penyelenggaraan |
| `penanggung_jawab` | Nama/ID pegawai PJ |
| `kode_opd` | FK ke tabel OPD |
| `kode_bagian` | FK ke tabel Bagian |
| `qr_code` | String/token unik untuk QR |
| `status` | `aktif` / `nonaktif` / `selesai` |
| `created_by` | ID user pembuat |
| `created_at` | Timestamp pembuatan |

### 6.8 Form Tamu Mandiri (Self-Service via QR Code)

Tamu mengisi data secara mandiri melalui scan QR Code.

**Field Tambahan:**
- Upload foto/selfie
- Upload surat tugas/dokumen pendukung

**Format Dokumen yang Diizinkan:** PDF, JPG, PNG

**Validasi:**
- Ukuran maksimal upload: configurable di pengaturan sistem
- Validasi format file
- QR Code hanya aktif sesuai masa berlaku agenda
- NIK: 16 digit, divalidasi format

**Konfirmasi ke Tamu:**
- Tampilkan halaman konfirmasi sukses setelah data tersimpan
- Tampilkan nomor referensi kunjungan

---

## 7. Alur Bisnis Sistem

```
1. Admin membuat agenda/acara kunjungan pada sistem
         ↓
2. Sistem menghasilkan QR Code unik untuk agenda/acara
         ↓
3. QR Code ditempatkan pada lokasi kegiatan / meja pelayanan
         ↓
4. Tamu scan QR Code menggunakan perangkat masing-masing
         ↓
5. Sistem membuka form buku tamu sesuai agenda (validasi masa aktif)
         ↓
6. Tamu mengisi data diri secara mandiri
         ↓
7. Tamu mengisi keperluan kunjungan
         ↓
8. Tamu upload foto/selfie (opsional)
         ↓
9. Tamu upload dokumen pendukung/surat tugas (opsional)
         ↓
10. Sistem menyimpan data kunjungan, status: "menunggu"
         ↓
11. Sistem menampilkan halaman konfirmasi + nomor referensi kunjungan
         ↓
12. Data tampil pada dashboard dan laporan realtime
         ↓
13. Admin melakukan verifikasi, update status kunjungan
         ↓
14. Tamu keluar → Admin/petugas update waktu pulang + status "selesai"
```

---

## 8. Kebutuhan Fungsional

| Kode | Deskripsi |
|------|-----------|
| F01 | Sistem menyediakan login dengan autentikasi username/password |
| F02 | Sistem membatasi akses berdasarkan role |
| F03 | Sistem mencatat data tamu |
| F04 | Sistem menampilkan histori kunjungan |
| F05 | Sistem menyediakan pencarian dan filter data |
| F06 | Sistem menyediakan export PDF dan Excel |
| F07 | Sistem menyediakan dashboard statistik |
| F08 | Sistem mencatat waktu datang dan waktu pulang |
| F09 | Sistem mendukung multi OPD |
| F10 | Sistem menyimpan audit log aktivitas |
| F11 | Sistem mengelola agenda dan generate QR Code |
| F12 | Sistem menyediakan form tamu mandiri via QR Code |
| F13 | Sistem mengelola master pegawai sebagai referensi tujuan kunjungan |
| F14 | Sistem memvalidasi NIK 16 digit |
| F15 | Sistem menampilkan konfirmasi dan nomor referensi kunjungan ke tamu |

---

## 9. Kebutuhan Non Fungsional

**Keamanan:**
- Password hashing (bcrypt/Argon2)
- Session timeout: 30 menit idle
- Role-based access control
- Validasi seluruh input (server-side)
- Proteksi CSRF
- Validasi format NIK (16 digit numerik)

**Performa:**
- Load dashboard < 3 detik
- Mendukung minimal 50 user simultan
- Response form tamu mandiri < 2 detik

**Backup:**
- Backup database harian (otomatis)
- Retensi backup minimal 30 hari

**Kompatibilitas:**
- Responsive web (desktop, tablet, dan mobile)
- Mendukung browser modern (Chrome, Firefox, Edge, Safari)

---

## 10. Struktur Database

### 10.1 Tabel `users`

| Field | Tipe | Keterangan |
|-------|------|------------|
| `id` | INT (PK) | Auto increment |
| `nama` | VARCHAR | Nama lengkap |
| `username` | VARCHAR (UNIQUE) | Username login |
| `password` | VARCHAR | Hash bcrypt |
| `role` | ENUM | `superadmin`, `admin` |
| `kode_opd` | VARCHAR (FK) | Referensi ke tabel OPD |
| `kode_bagian` | VARCHAR (FK) | Referensi ke tabel Bagian |
| `kode_subbagian` | VARCHAR (FK) | Referensi ke tabel Subbagian (nullable) |
| `status` | ENUM | `aktif`, `nonaktif` |
| `created_at` | DATETIME | Timestamp pembuatan |

### 10.2 Tabel `opd`

| Field | Tipe | Keterangan |
|-------|------|------------|
| `kode_opd` | VARCHAR (PK) | Kode OPD |
| `nama_opd` | VARCHAR | Nama OPD |

### 10.3 Tabel `bagian`

| Field | Tipe | Keterangan |
|-------|------|------------|
| `kode_opd` | VARCHAR (FK) | Referensi ke OPD |
| `kode_bagian` | VARCHAR | Kode bagian |
| `nama_bagian` | VARCHAR | Nama bagian |

*PK: (`kode_opd`, `kode_bagian`)*

### 10.4 Tabel `subbagian`

| Field | Tipe | Keterangan |
|-------|------|------------|
| `kode_opd` | VARCHAR (FK) | Referensi ke OPD |
| `kode_bagian` | VARCHAR (FK) | Referensi ke Bagian |
| `kode_subbagian` | VARCHAR | Kode subbagian |
| `nama_subbagian` | VARCHAR | Nama subbagian |

*PK: (`kode_opd`, `kode_bagian`, `kode_subbagian`)*

### 10.5 Tabel `pegawai` *(BARU)*

| Field | Tipe | Keterangan |
|-------|------|------------|
| `id` | INT (PK) | Auto increment |
| `nip` | VARCHAR (UNIQUE) | Nomor Induk Pegawai |
| `nama` | VARCHAR | Nama lengkap pegawai |
| `kode_opd` | VARCHAR (FK) | Referensi ke OPD |
| `kode_bagian` | VARCHAR (FK) | Referensi ke Bagian |
| `kode_subbagian` | VARCHAR (FK) | Referensi ke Subbagian (nullable) |
| `jabatan` | VARCHAR | Jabatan pegawai |
| `status` | ENUM | `aktif`, `nonaktif` |

### 10.6 Tabel `agenda` *(BARU)*

| Field | Tipe | Keterangan |
|-------|------|------------|
| `id_agenda` | INT (PK) | Auto increment |
| `nama_agenda` | VARCHAR | Nama acara/kegiatan |
| `deskripsi` | TEXT | Deskripsi kegiatan |
| `tanggal_mulai` | DATETIME | Tanggal mulai |
| `tanggal_selesai` | DATETIME | Tanggal berakhir |
| `lokasi` | VARCHAR | Lokasi kegiatan |
| `penanggung_jawab` | VARCHAR | Nama PJ kegiatan |
| `kode_opd` | VARCHAR (FK) | Referensi ke OPD |
| `kode_bagian` | VARCHAR (FK) | Referensi ke Bagian |
| `qr_code` | VARCHAR (UNIQUE) | Token unik QR Code |
| `status` | ENUM | `aktif`, `nonaktif`, `selesai` |
| `created_by` | INT (FK) | Referensi ke users |
| `created_at` | DATETIME | Timestamp pembuatan |

### 10.7 Tabel `buku_tamu`

| Field | Tipe | Keterangan |
|-------|------|------------|
| `id` | INT (PK) | Auto increment |
| `id_agenda` | INT (FK, nullable) | Referensi ke agenda (jika via QR) |
| `nama_tamu` | VARCHAR | Nama lengkap tamu |
| `nik` | CHAR(16) | NIK 16 digit |
| `instansi` | VARCHAR | Instansi/asal tamu |
| `no_hp` | VARCHAR | Nomor HP |
| `alamat` | TEXT | Alamat tamu |
| `keperluan` | TEXT | Keperluan kunjungan |
| `id_pegawai_tujuan` | INT (FK) | Referensi ke tabel pegawai |
| `kode_opd` | VARCHAR (FK) | Referensi ke OPD |
| `kode_bagian` | VARCHAR (FK) | Referensi ke Bagian |
| `kode_subbagian` | VARCHAR (FK, nullable) | Referensi ke Subbagian |
| `waktu_datang` | DATETIME | Waktu kedatangan |
| `waktu_pulang` | DATETIME (nullable) | Waktu kepulangan |
| `foto` | VARCHAR | Path file foto tamu |
| `tanda_tangan` | VARCHAR (nullable) | Path file tanda tangan |
| `dokumen_pendukung` | VARCHAR (nullable) | Path file dokumen |
| `no_referensi` | VARCHAR (UNIQUE) | Nomor referensi kunjungan |
| `status_kunjungan` | ENUM | `menunggu`, `berlangsung`, `selesai`, `batal` |
| `created_by` | INT (FK, nullable) | User yang menginput (null jika mandiri) |
| `created_at` | DATETIME | Timestamp pembuatan |

### 10.8 Tabel `audit_log`

| Field | Tipe | Keterangan |
|-------|------|------------|
| `id` | INT (PK) | Auto increment |
| `user_id` | INT (FK) | Referensi ke users |
| `aktivitas` | VARCHAR | Deskripsi aktivitas |
| `tabel_terkait` | VARCHAR | Nama tabel yang diubah |
| `id_record` | INT (nullable) | ID record yang diubah |
| `ip_address` | VARCHAR | Alamat IP pengguna |
| `created_at` | DATETIME | Timestamp aktivitas |

---

## 11. Relasi Database

```
opd.kode_opd
    ├── bagian.kode_opd
    │       └── subbagian.(kode_opd + kode_bagian)
    ├── users.kode_opd
    ├── pegawai.kode_opd
    ├── agenda.kode_opd
    └── buku_tamu.kode_opd

agenda.id_agenda
    └── buku_tamu.id_agenda

pegawai.id
    └── buku_tamu.id_pegawai_tujuan

users.id
    ├── buku_tamu.created_by
    ├── agenda.created_by
    └── audit_log.user_id
```

---

## 12. Dashboard Statistik

| Statistik | Superadmin | Admin |
|-----------|-----------|-------|
| Jumlah tamu hari ini | ✅ (seluruh OPD) | ✅ (unit kerja sendiri) |
| Jumlah tamu bulan ini | ✅ | ✅ |
| OPD paling banyak dikunjungi | ✅ | ❌ |
| Bagian paling banyak dikunjungi | ✅ | ✅ |
| Pegawai paling banyak dituju | ✅ | ✅ |
| Grafik tren kunjungan | ✅ | ✅ |
| Daftar kunjungan terbaru | ✅ | ✅ |
| Jumlah agenda aktif | ✅ | ✅ |

---

## 13. Teknologi yang Digunakan

### 13.1 Stack Utama

| Layer | Teknologi | Versi |
|-------|-----------|-------|
| Backend Framework | CodeIgniter 4 (CI4) | ^4.5 |
| Bahasa Pemrograman | PHP | ^8.1 |
| Frontend Framework | Bootstrap 5 | ^5.3 |
| Stylesheet Tambahan | Custom CSS (per modul) | - |
| Interaktivitas | JavaScript / jQuery | jQuery ^3.7 |
| Database | MySQL / MariaDB | MySQL ^8.0 / MariaDB ^10.6 |
| Web Server | Apache / Nginx | - |
| OS Server | Linux (Ubuntu 22.04 LTS) / Windows Server | - |

---

### 13.2 Library Backend (PHP / Composer)

#### 🔐 Autentikasi & Keamanan

| Library | Package Composer | Fungsi |
|---------|-----------------|--------|
| **CI4 Shield** | `codeigniter4/shield` | Autentikasi resmi CI4: login, logout, session, hashing password (bcrypt), remember me, throttling brute-force |
| **CI4 Shield** (Groups & Permissions) | *(bawaan Shield)* | Role-based access control (RBAC) — group `superadmin`, `admin` |

> **Catatan CI4 Shield:**
> Shield menggantikan kebutuhan tabel `users` manual. Tabel bawaan Shield: `users`, `auth_identities`, `auth_groups_users`, `auth_logins`, `auth_token_logins`, `auth_remember_tokens`.
> Tabel `users` pada PRD ini disesuaikan dengan struktur Shield dan di-extend dengan field tambahan (`kode_opd`, `kode_bagian`, `kode_subbagian`, `status`).

---

#### 📄 PDF Export

| Library | Package Composer | Fungsi |
|---------|-----------------|--------|
| **DOMPDF** | `dompdf/dompdf` | Generate laporan PDF dari view HTML/Blade — lebih mudah diintegrasikan dengan CI4 template |

---

#### 📊 Excel Export

| Library | Package Composer | Fungsi |
|---------|-----------------|--------|
| **PhpSpreadsheet** | `phpoffice/phpspreadsheet` | Generate file `.xlsx` untuk export laporan kunjungan |

---

#### 📷 QR Code Generator

| Library | Package Composer | Fungsi |
|---------|-----------------|--------|
| **chillerlan/php-qrcode** | `chillerlan/php-qrcode` | Generate QR Code per agenda dalam format PNG/SVG/Base64 |

---

#### 🖼️ Manipulasi Gambar (Upload Foto)

| Library | Package Composer | Fungsi |
|---------|-----------------|--------|
| **Intervention Image** | `intervention/image` | Resize, compress, dan validasi foto upload dari tamu |

---

#### ✅ Validasi Tambahan

| Library | Package Composer | Fungsi |
|---------|-----------------|--------|
| **CI4 Validation** | *(bawaan CI4)* | Validasi form server-side, termasuk custom rule NIK 16 digit |

---

#### 🔤 Utilities

| Library | Package Composer | Fungsi |
|---------|-----------------|--------|
| **vlucas/phpdotenv** | `vlucas/phpdotenv` | *(opsional)* Manajemen environment variable `.env` — sudah tersedia bawaan CI4 |
| **ramsey/uuid** | `ramsey/uuid` | Generate UUID untuk `no_referensi` kunjungan dan token QR Code yang unik |

---

### 13.3 Library Frontend (CDN / NPM)

#### 🎨 UI & Styling

| Library | Versi | Fungsi |
|---------|-------|--------|
| **Bootstrap 5** | ^5.3 | Grid system, komponen UI (card, modal, table, navbar, form, badge, dll) |
| **Bootstrap Icons** | ^1.11 | Icon set SVG resmi Bootstrap |
| **Custom CSS** | - | Style tambahan per halaman (warna instansi, branding Diskominfo) |

---

#### 📊 Grafik & Chart

| Library | Versi | Fungsi |
|---------|-------|--------|
| **Chart.js** | ^4.4 | Grafik tren kunjungan (line chart, bar chart, doughnut) di dashboard |
| **chartjs-plugin-datalabels** | ^2.2 | Label angka di atas grafik Chart.js |

---

#### 📋 Tabel Interaktif

| Library | Versi | Fungsi |
|---------|-------|--------|
| **DataTables** | ^1.13 | Tabel data tamu dengan fitur pencarian, sorting, paginasi — terintegrasi Bootstrap 5 |
| **DataTables Bootstrap5 extension** | ^1.13 | Styling DataTables agar sesuai Bootstrap 5 |

---

#### 📅 Date & Time Picker

| Library | Versi | Fungsi |
|---------|-------|--------|
| **Flatpickr** | ^4.6 | Date picker dan datetime picker untuk filter laporan dan form input |

---

#### 📤 Upload File

| Library | Versi | Fungsi |
|---------|-------|--------|
| **Dropzone.js** | ^6.0 | Drag & drop upload foto dan dokumen pendukung di form tamu mandiri |

---

#### ✍️ Tanda Tangan Digital

| Library | Versi | Fungsi |
|---------|-------|--------|
| **Signature Pad** | ^4.1 | Canvas tanda tangan digital tamu — output Base64 disimpan ke server |

---

#### 📷 QR Code Scanner (Frontend)

| Library | Versi | Fungsi |
|---------|-------|--------|
| **Html5-QRCode** | ^2.3 | Scan QR Code via kamera browser (untuk pengembangan lanjutan: scan kartu tamu) |

---

#### 🔔 Notifikasi & Alert

| Library | Versi | Fungsi |
|---------|-------|--------|
| **SweetAlert2** | ^11 | Konfirmasi dialog (hapus data, submit form) dan notifikasi sukses/error yang lebih elegan dari alert bawaan browser |
| **Toastr** | ^2.1 | Toast notification ringan untuk feedback aksi AJAX (simpan, edit, update status) |

---

#### 🌐 HTTP Request (AJAX)

| Library | Versi | Fungsi |
|---------|-------|--------|
| **jQuery** | ^3.7 | AJAX request, DOM manipulation, event handling |
| **Axios** | ^1.6 | *(opsional alternatif jQuery AJAX)* HTTP client berbasis Promise untuk request API |

---

#### 🔢 Format & Utilitas

| Library | Versi | Fungsi |
|---------|-------|--------|
| **Select2** | ^4.1 | Dropdown searchable untuk pilih pegawai, OPD, bagian — mendukung AJAX lazy-load |
| **Inputmask** | ^5.0 | Masking input NIK (format 16 digit), nomor HP, dan tanggal |
| **Moment.js** | ^2.29 | Format tanggal/waktu di sisi frontend (label grafik, display waktu kunjungan) |

---

### 13.4 Struktur Instalasi (Composer & NPM)

#### `composer.json` (dependencies utama)

```json
{
  "require": {
    "php": "^8.1",
    "codeigniter4/framework": "^4.5",
    "codeigniter4/shield": "^1.1",
    "dompdf/dompdf": "^2.0",
    "phpoffice/phpspreadsheet": "^2.1",
    "chillerlan/php-qrcode": "^5.0",
    "intervention/image": "^2.7",
    "ramsey/uuid": "^4.7"
  }
}
```

#### CDN Library Frontend (urutan load di layout utama)

```html
<!-- Bootstrap 5 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/css/bootstrap.min.css">
<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11/font/bootstrap-icons.css">
<!-- DataTables Bootstrap5 -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13/css/dataTables.bootstrap5.min.css">
<!-- Flatpickr -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<!-- Select2 -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1/dist/css/select2.min.css">
<!-- Toastr -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastr@2.1/build/toastr.min.css">
<!-- Custom CSS -->
<link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">

<!-- JS (sebelum </body>) -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/toastr@2.1/build/toastr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1/dist/signature_pad.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/inputmask@5/dist/inputmask.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29/moment.min.js"></script>
```

---

### 13.5 Catatan Integrasi CI4 Shield

Shield diinstall via Composer dan dikonfigurasi dengan langkah berikut:

```bash
composer require codeigniter4/shield
php spark shield:setup
```

Konfigurasi yang dibutuhkan:

- **`app/Config/Auth.php`** — pengaturan session, remember me, durasi token
- **`app/Config/AuthGroups.php`** — definisi group: `superadmin`, `admin` dan permission masing-masing
- **Filter Shield** — diterapkan di `app/Config/Filters.php` untuk proteksi route admin

Contoh konfigurasi group di `AuthGroups.php`:

```php
public array $groups = [
    'superadmin' => [
        'title'       => 'Super Administrator',
        'description' => 'Akses penuh ke seluruh sistem',
    ],
    'admin' => [
        'title'       => 'Administrator',
        'description' => 'Akses terbatas berdasarkan unit kerja',
    ],
];

public array $permissions = [
    'tamu.view'   => 'Melihat data tamu',
    'tamu.create' => 'Menambah data tamu',
    'tamu.edit'   => 'Mengedit data tamu',
    'tamu.delete' => 'Menghapus data tamu (Superadmin only)',
    'laporan.export' => 'Export laporan PDF/Excel',
    'agenda.manage'  => 'Kelola agenda dan QR Code',
    'user.manage'    => 'Kelola user (Superadmin only)',
    'master.manage'  => 'Kelola data master OPD/Bagian/Pegawai',
];
```

---

## 14. Pengembangan Lanjutan (Opsional)

- Scan QR Code tamu (kamera frontend)
- Cetak kartu tamu
- Notifikasi WhatsApp ke pegawai tujuan saat tamu tiba
- Integrasi webcam untuk foto otomatis
- Integrasi e-Office
- Single Sign On (SSO) dengan sistem kepegawaian daerah
- Notifikasi/konfirmasi ke tamu via SMS atau WhatsApp setelah pendaftaran

---

## 15. Halaman Sistem (Mockup)

| No | Halaman | Akses |
|----|---------|-------|
| 1 | Login | Publik |
| 2 | Dashboard | Superadmin, Admin |
| 3 | Form Buku Tamu (input manual) | Admin |
| 4 | Form Tamu Mandiri (via QR Code) | Publik (tamu) |
| 5 | Data Tamu | Superadmin, Admin |
| 6 | Detail Tamu | Superadmin, Admin |
| 7 | Manajemen Agenda | Superadmin, Admin |
| 8 | Laporan & Export | Superadmin, Admin |
| 9 | Manajemen User | Superadmin |
| 10 | Master OPD / Bagian / Subbagian | Superadmin |
| 11 | Master Pegawai | Superadmin, Admin |
| 12 | Pengaturan Sistem | Superadmin |
| 13 | Halaman Konfirmasi Kunjungan | Publik (tamu) |

---

## 16. Indikator Keberhasilan

| Indikator | Target |
|-----------|--------|
| Pencatatan digital | 100% |
| Pengurangan buku tamu manual | ≥ 90% |
| Kecepatan pencarian data | < 10 detik |
| Ketersediaan laporan realtime | Ya |
| Load dashboard | < 3 detik |
| Ketersediaan sistem (uptime) | ≥ 99% |

---

## 17. Ringkasan Perubahan / Penambahan dari Dokumen Awal

| No | Item | Keterangan |
|----|------|------------|
| 1 | Tabel `agenda` | Ditambahkan di Struktur Database (sebelumnya hanya ada di fitur) |
| 2 | Tabel `pegawai` | Ditambahkan sebagai master data pengganti teks bebas `pegawai_tujuan` |
| 3 | Field `id_agenda` di `buku_tamu` | Menghubungkan kunjungan ke agenda QR Code |
| 4 | Field `no_referensi` di `buku_tamu` | Nomor referensi unik untuk konfirmasi ke tamu |
| 5 | Field `dokumen_pendukung` di `buku_tamu` | Menyimpan path file surat tugas/dokumen |
| 6 | Status kunjungan didefinisikan | `menunggu`, `berlangsung`, `selesai`, `batal` |
| 7 | Session timeout | Ditentukan 30 menit idle |
| 8 | Validasi NIK | 16 digit numerik, divalidasi format |
| 9 | Kebijakan hapus data | Hanya Superadmin yang dapat menghapus permanen |
| 10 | Konfirmasi tamu mandiri | Halaman sukses + nomor referensi setelah submit QR form |
| 11 | Tabel `audit_log` diperluas | Ditambah field `tabel_terkait` dan `id_record` |
| 12 | Modul Manajemen Pegawai | Ditambahkan di Ruang Lingkup Sistem |
| 13 | Stack teknologi diperinci | CI4 Shield, DOMPDF, PhpSpreadsheet, Intervention Image, ramsey/uuid |
| 14 | Library frontend diperinci | Chart.js, DataTables, Flatpickr, Select2, SweetAlert2, Toastr, Signature Pad, Inputmask, Dropzone.js |
| 15 | Panduan instalasi ditambahkan | `composer.json` dependencies, CDN HTML template, konfigurasi CI4 Shield & permissions |

---

## 18. Penutup

Sistem Informasi Buku Tamu Elektronik ini diharapkan menjadi sarana digitalisasi pelayanan tamu di lingkungan Dinas Komunikasi dan Informatika Kabupaten Grobogan yang modern, efektif, akuntabel, dan mendukung implementasi transformasi digital pemerintah daerah.

Dokumen ini bersifat hidup dan dapat diperbarui sesuai dengan kebutuhan yang berkembang selama proses pengembangan.

---

*Dokumen PRD — Versi 1.2*
*Dinas Komunikasi dan Informatika Kabupaten Grobogan*
