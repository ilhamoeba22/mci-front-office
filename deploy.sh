#!/bin/bash

# Deployment Script for MCI Front Office
# Usage: sh deploy.sh

echo "--- Memulai Update Produksi ---"

# 1. Pull latest code from GitHub
echo "[1/4] Menarik kode terbaru dari GitHub..."
git pull origin main

# 2. Optimalisasi Laravel
echo "[2/4] Membersihkan dan membuat cache baru..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 3. Informasikan Manual Build jika perlu
echo "[3/4] Catatan: Jika ada perubahan CSS/JS, pastikan Anda sudah mengupload folder 'public/build' dari lokal."

# 4. Berhasil
echo "[4/4] Selesai! Website sudah terupdate."
echo "--- Update Berhasil ---"
