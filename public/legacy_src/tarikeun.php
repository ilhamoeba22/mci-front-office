<?php
require 'connection.php';

// 1. Ambil dan bersihkan data dasar
$nama           = $_POST['nama'] ?? '';
$no_rek         = $_POST['no_rek'] ?? '';
$tgl            = $_POST['tgl'] ?? '';
$nominal        = $_POST['nominal1'] ?? 0;
$terbilang      = $_POST['terbilang1'] ?? '';
$tujuan         = $_POST['tujuan'] ?? '';
$nama_penyetor  = $_POST['nama_penyetor'] ?? '';
$hp_penyetor    = $_POST['hp_penyetor'] ?? '';
$noid_penyetor  = $_POST['noid_penyetor'] ?? '';
$alamat_penyetor = $_POST['alamat_penyetor'] ?? '';

date_default_timezone_set("Asia/Jakarta");
$token = "TT-" . date("ymdHis");
$today = date("Y-m-d"); // Format tanggal yang lebih standar SQL

try {
    // Mulai Transaksi (Agar jika satu gagal, semua dibatalkan)
    $conn->begin_transaction();

    // --- QUERY 1: INSERT tbl_tarik ---
    $sql1 = "INSERT INTO tbl_tarik (token, nama, no_rek, tgl, nominal, terbilang, tujuan, nama_penarik, hp_penarik, noid_penarik, alamat_penarik) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt1 = $conn->prepare($sql1);
    // "sssssssssss" berarti ada 11 parameter bertipe string
    $stmt1->bind_param("sssssssssss", $token, $nama, $no_rek, $tgl, $nominal, $terbilang, $tujuan, $nama_penyetor, $hp_penyetor, $noid_penyetor, $alamat_penyetor);
    $stmt1->execute();

    // --- QUERY 2: HITUNG ANTRIAN ---
    $sql2 = "SELECT count(id_antrian) as max_id FROM tbl_antrian WHERE type='Teller' AND tgl_antri=?";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("s", $today);
    $stmt2->execute();
    $res2 = $stmt2->get_result();
    $row  = $res2->fetch_assoc();
    
    $next = ($row['max_id'] ?? 0) + 1;
    $next_id = "TL-" . str_pad($next, 2, "0", STR_PAD_LEFT);

    // --- QUERY 3: INSERT tbl_antrian ---
    $sql3 = "INSERT INTO tbl_antrian (tgl_antri, nama_antrian, type, antrian, kode) VALUES (?, ?, 'Teller', ?, ?)";
    $stmt3 = $conn->prepare($sql3);
    $stmt3->bind_param("ssss", $today, $nama, $next_id, $token);
    $stmt3->execute();

    // Jika sampai sini tidak ada error, simpan semua perubahan
    $conn->commit();
    session_start();

    // Pilihan A: Hanya hapus data transfer (Nasabah tetap login)
    if (isset($_SESSION['transfer_data'])) {
        unset($_SESSION['transfer_data']);
    }
    // Redirect aman tanpa tanda kutip di URL
    header("Location: output.php?id=" . urlencode($token));
    exit();

} catch (Exception $e) {
    // Jika ada error, batalkan semua perubahan di database
    $conn->rollback();
    error_log($e->getMessage());
    die("Gagal memproses data. Silakan coba lagi nanti.");
}

$conn->close();
?>