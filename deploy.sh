#!/bin/bash

# Deployment Script for MCI Front Office
# Force LF (Linux) Line Endings

echo "--- Memulai Update Produksi ---"

# 1. Pull latest code from GitHub
echo "[1/3] Menarik kode terbaru dari GitHub..."
git pull origin main

# 2. Optimalisasi Laravel
echo "[2/3] Membersihkan dan membuat cache baru..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force

# 3. Selesai
echo "[3/3] Selesai! Website sudah terupdate."
echo "--- Update Berhasil ---"
