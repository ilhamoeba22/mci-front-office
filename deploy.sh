#!/bin/bash

# Deployment Script for MCI Front Office
# Force LF (Linux) Line Endings

echo "--- Memulai Update Produksi ---"

# 1. Pull latest code from GitHub
echo "[1/3] Menarik kode terbaru dari GitHub..."
git reset --hard origin/main
git pull origin main

# 2. Optimalisasi Laravel
echo "[2/4] Membersihkan dan membuat cache baru..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force

# 3. Frontend Build (Direct on Hosting)
echo "[3/4] Melakukan build frontend (NPM)..."
if command -v npm &> /dev/null
then
    echo "NPM ditemukan, memulai build..."
    npm install && npm run build
else
    echo "Warning: NPM tidak ditemukan di server. Lewati build frontend."
fi

# 4. Selesai
echo "[4/4] Selesai! Website sudah terupdate."
echo "--- Update Berhasil ---"
