# MCI Front Office (Manajemen Antrian & Transaksi)

Aplikasi berbasis web untuk mengelola antrian dan slip transaksi perbankan di BPRS HIK MCI. Dirancang untuk kecepatan, keamanan, dan presisi tinggi dalam pencetakan slip transaksi asli.

## 🚀 Fitur Utama

- **Manajemen Antrian Real-time**: Memanggil, memproses, dan memantau status antrian untuk Teller dan CS.
- **Validasi Transaksi Backend**: Pengecekan saldo otomatis untuk penarikan dan transfer guna mencegah kesalahan transaksi.
- **Sistem Cetak Presisi Tinggi (Definitive)**: 
  - Menggunakan template backend (Blade/HTML) yang disesuaikan secara milimetris dengan formulir fisik.
  - Mendukung cetak 2 halaman (Duplex) untuk slip Tarik Tunai.
  - Mendukung cetak Landscape untuk slip Setor dan Transfer.
- **Laporan Riwayat Transaksi**: Generasi laporan A4 profesional untuk audit dan arsip.
- **Dashboard TV Display**: Antarmuka khusus untuk tampilan antrian di ruang tunggu.

## 🛠 Tech Stack

- **Backend**: Laravel 10 (PHP 8.2+)
- **Frontend**: Vue.js 3, Tailwind CSS
- **Database**: MariaDB / MySQL
- **Build Tool**: Vite

## ⚙️ Instalasi (Lokal/Development)

1. Clone repository.
2. Jalankan `composer install`.
3. Jalankan `npm install`.
4. Salin `.env.example` ke `.env` dan sesuaikan pengaturan database.
5. Jalankan `php artisan key:generate`.
6. Jalankan `php artisan migrate --seed` (Hanya untuk `DatabaseSeeder` utama).
7. Jalankan `npm run dev` atau `npm run build`.

## 🌐 Deployment (Subdomain)

1. Pastikan folder `legacy_src` dan `TransactionSeeder.php` tidak disertakan dalam commit (sudah ada di `.gitignore`).
2. Jalankan `composer install --optimize-autoloader --no-dev` di server.
3. Jalankan `npm run build` untuk mengompilasi aset produksi.
4. Sesuaikan `.env` server (Set `APP_DEBUG=false`).

## 📁 Struktur Penting

- `app/Http/Controllers/TransactionPrintController.php`: Otak dari sistem pencetakan slip.
- `resources/views/print/`: Folder berisi template Blade untuk slip fisik (Setor, Tarik, Transfer).
- `resources/js/pages/QueueManagement.vue`: Antarmuka operasional Teller/CS.

---
© 2026 BPRS HIK MCI
