#!/bin/bash

# Deployment Script for MCI Front Office
# Force LF (Linux) Line Endings

echo "--- Memulai Update Produksi ---"

# 1. Pull latest code from GitHub
echo "[1/3] Menarik kode terbaru dari GitHub..."
git pull origin main

# 2. Build Assets (Vite)
echo "[2/4] Menginstal dependensi dan membuat build asset..."
npm install
npm run build

# 3. Optimalisasi Laravel
echo "[3/4] Membersihkan dan membuat cache baru..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force

# 4. Selesai
echo "[4/4] Selesai! Website sudah terupdate."
echo "--- Update Berhasil ---"
