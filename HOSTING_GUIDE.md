# Panduan Hosting Laravel (Subdomain) untuk Pemula

Panduan ini dirancang untuk memudahkan Anda melakukan deployment aplikasi **MCI Front Office** ke subdomain menggunakan hosting (biasanya CPanel).

## 1. Membuat Subdomain di CPanel
1. Login ke CPanel Anda.
2. Cari menu **Subdomains**.
3. Masukkan nama subdomain (misal: `antrian`).
4. **PENTING**: Pastikan **Document Root** mengarah ke folder `/public` di dalam folder subdomain Anda.
   - Contoh: `public_html/antrian/public` (BUKAN hanya `public_html/antrian`).
   - Ini adalah standar keamanan Laravel agar file inti tidak bisa diakses publik.

## 2. Deploy Kode dari GitHub
Ada dua cara umum:
- **Cara A (Terminal/Git)**: Jika hosting mendukung SSH, jalankan `git clone https://github.com/ilhamoeba22/mci-front-office.git`.
- **Cara B (Git Version Control di CPanel)**: Gunakan fitur "Git™ Version Control" di CPanel untuk menghubungkan repository Anda.

## 3. Instalasi Dependency (Melalui Terminal SSH)
Buka terminal di CPanel atau lewat SSH Client (seperti Putty):
1. Masuk ke folder subdomain: `cd public_html/antrian`
2. Install PHP Package: `composer install --optimize-autoloader --no-dev`
3. Install JS Package: `npm install`
4. Build Aset Frontend: `npm run build`

## 4. Konfigurasi Database & Environment
1. Buat database baru dan user database di menu **MySQL® Databases** CPanel.
2. Edit file `.env` (biasanya tersembunyi, klik "Settings" -> "Show Hidden Files" di File Manager):
   - `APP_ENV=production`
   - `APP_DEBUG=false`
   - `APP_URL=https://antrian.domainanda.com`
   - Isi `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD` sesuai yang baru dibuat.

## 5. Finalisasi (Migrasi & Link)
Kembali ke terminal:
1. Generate Key: `php artisan key:generate`
2. Jalankan Migrasi: `php artisan migrate --force`
3. Jalankan Seeder Utama: `php artisan db:seed --class=DatabaseSeeder`
4. Tautkan Storage: `php artisan storage:link`

## 💡 Tips Troubleshooting
- **Error 500?** Cek versi PHP di CPanel (pastikan minimal PHP 8.2).
- **CSS Slip Berantakan?** Pastikan sudah menjalankan `npm run build` di server.
- **Symlink Error?** Jika `storage:link` gagal di shared hosting, Anda mungkin perlu membuatnya via script PHP sederhana.

---
*Jika Anda mengalami kesulitan pada langkah tertentu, silakan tanyakan bagian mana yang perlu diperjelas!*
