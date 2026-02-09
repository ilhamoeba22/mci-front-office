<?php
session_start();

// Pilihan A: Hanya hapus data transfer (Nasabah tetap login)
if (isset($_SESSION['transfer_data'])) {
    unset($_SESSION['transfer_data']);
}

// Pilihan B: Hapus SEMUA session (Log out total)
// session_destroy(); 

// Redirect kembali ke halaman input rekening atau index
header("Location: index.php?msg=Data session berhasil dihapus");
exit();