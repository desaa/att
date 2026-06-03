# Revisi Alur Kerja & Filtering Role E-GuestBook

Berikut adalah rincian revisi alur kerja dan sistem filter berdasarkan role pada aplikasi E-GuestBook:

## 1. Role: Tamu
- **Form Registrasi Tamu Umum**:
  - Tambahkan dropdown/bidang **Bidang** dan **Subbidang** pada form registrasi.
  - Jika data tamu ditemukan di database pegawai (pencarian via NIK/NIP), maka data **Nama**, **Instansi**, **Bidang**, dan **Subbidang** akan otomatis terisi.
  - Jika data tidak ditemukan di database, tamu dapat mengetik **Instansi secara manual**, sedangkan pengisian **Bidang** dan **Subbidang** bersifat **opsional (tidak wajib)**.
- **Status Awal Tamu**:
  - Setelah melakukan registrasi, status awal kunjungan tamu diset menjadi **Menunggu Verifikasi** (`status_kunjungan = 'menunggu'`).
  - Status ini nantinya hanya dapat diubah menjadi **"berlangsung"** atau **"selesai"** oleh pegawai yang akan ditemui tamu tersebut.

## 2. Role: Super Administrator (`superadmin`)
- **Dashboard**:
  - Pada tabel **Kunjungan Terbaru**, tambahkan filter dropdown **Bulan Kunjungan** (secara default menampilkan bulan berjalan/ini).
- **Menu Daftar Tamu**:
  - Tambahkan filter cascading **OPD**, **Bidang**, dan **Subbidang**.
  - Ketentuan filtering:
    - Jika hanya memilih **OPD**, tampilkan semua tamu yang berkunjung ke OPD tersebut.
    - Jika memilih **OPD dan Bidang**, tampilkan semua tamu yang berkunjung ke Bidang tersebut.
    - Jika memilih **OPD, Bidang, dan Subbidang**, tampilkan semua tamu yang berkunjung ke Subbidang tersebut.
- **Menu Laporan & Export**:
  - Tambahkan filter cascading **OPD**, **Bidang**, dan **Subbidang** dengan ketentuan filtering yang sama seperti pada Menu Daftar Tamu.
- **Manajemen User**:
  - Untuk selain user superadmin, tampilan data tabel pada kolom **Unit Kerja** harus menampilkan hierarki penuh: **OPD**, **Bidang**, dan **Subbidang** (contoh: Dinas Komunikasi dan Informatika -> Bidang Sistem Informasi Layanan E-Government dan Statistik -> Seksi Layanan E-Government dan Smart City).

## 3. Role: Admin (`admin`)
Sesuaikan query pengambilan data dan hak akses menu untuk masing-masing tingkatan Admin:
1. **Admin OPD**: Sesuai dengan alur saat ini (melihat semua data tamu yang berkunjung ke OPD-nya).
2. **Admin Bidang**:
   - **Dashboard**: Hanya menampilkan data card statistik, grafik kunjungan, dan tabel kunjungan terbaru untuk Bidang tersebut.
   - **Data Tamu**: Dropdown pilihan pegawai hanya menampilkan pegawai pada Bidang tersebut. Data tamu yang muncul hanya tamu dengan tujuan ke Bidang tersebut atau pegawai di dalamnya.
   - **QR Tamu**: Hanya dapat membuat QR Code mandiri untuk Bidang tersebut sendiri maupun Subbidang di bawahnya.
   - **Manajemen Agenda**: Hanya dapat mengelola Agenda untuk Bidang tersebut sendiri maupun Subbidang di bawahnya.
   - **Laporan & Export**: Hanya dapat mengelola laporan dan ekspor untuk Bidang tersebut sendiri maupun Subbidang di bawahnya. Dropdown filter agenda dan pegawai hanya menampilkan agenda/pegawai pada Bidang tersebut atau Subbidang di bawahnya.
3. **Admin Subbidang**:
   - **Dashboard**: Hanya menampilkan data card statistik, grafik kunjungan, dan tabel kunjungan terbaru untuk Subbidang tersebut.
   - **Data Tamu**: Dropdown pilihan pegawai hanya menampilkan pegawai pada Subbidang tersebut. Data tamu yang muncul hanya tamu dengan tujuan ke Subbidang tersebut.
   - **QR Tamu**: Hanya dapat membuat QR Code mandiri untuk Subbidang tersebut.
   - **Manajemen Agenda**: Hanya dapat mengelola Agenda untuk Subbidang tersebut.
   - **Laporan & Export**: Hanya dapat mengelola laporan dan ekspor untuk Subbidang tersebut. Dropdown filter agenda dan pegawai hanya menampilkan agenda/pegawai pada Subbidang tersebut.

## 4. Role: Pegawai
- **Dashboard**: Hanya menampilkan data tamu yang memiliki tujuan langsung kepada pegawai bersangkutan.
- **Daftar Tamu**:
  - Menampilkan tamu dengan tujuan langsung pegawai bersangkutan **ATAU** tamu umum pada OPD tersebut yang tidak memilih tujuan pegawai, tidak memilih bagian/bidang, dan tidak memilih subbagian/subbidang (tamu umum OPD).
  - Pilihan dropdown agenda hanya memuat agenda yang bersangkutan/diikuti oleh pegawai tersebut.
  - Perbaiki bug foto selfie dan tanda tangan digital tamu yang saat ini tidak muncul pada halaman detail tamu di portal pegawai.

## 5. Tampilan Umum (Revisi Tampilan)
- **Manajemen Agenda**: Pindahkan posisi input/pilihan **Penanggung Jawab (PJ)** menjadi di bawah dropdown/input **Subbagian / Subbidang Penyelenggara**.
