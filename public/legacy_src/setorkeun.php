<?php
require 'connection.php';

// 1. Ambil data dengan proteksi terhadap undefined index
$nama            = $_POST['nama'] ?? '';
$no_rek          = $_POST['no_rek'] ?? '';
$tgl             = $_POST['tgl'] ?? '';
$nominal         = $_POST['nominal1'] ?? 0;
$terbilang       = $_POST['terbilang1'] ?? '';
$berita          = $_POST['tujuan'] ?? ''; // Mengambil dari input 'tujuan' sesuai kode asli Anda
$tujuan          = $_POST['tujuan'] ?? '';
$nama_penyetor   = $_POST['nama_penyetor'] ?? '';
$hp_penyetor     = $_POST['hp_penyetor'] ?? '';
$noid_penyetor   = "";
$alamat_penyetor = "";

date_default_timezone_set("Asia/Jakarta");
$token = "ST-" . date("ymdHis");
$today = date("ymd");

// Aktifkan laporan error strict
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // Mulai transaksi database
    $conn->begin_transaction();

    // --- 1. INSERT tbl_setor ---
    $sql1 = "INSERT INTO tbl_setor (token, nama, no_rek, tgl, nominal, terbilang, berita, tujuan, nama_penyetor, hp_penyetor, noid_penyetor, alamat_penyetor) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt1 = $conn->prepare($sql1);
    // Bind 12 parameter string ("s")
    $stmt1->bind_param("ssssssssssss", 
        $token, $nama, $no_rek, $tgl, $nominal, $terbilang, $berita, $tujuan, $nama_penyetor, $hp_penyetor, $noid_penyetor, $alamat_penyetor
    );
    $stmt1->execute();

    // --- 2. HITUNG ANTRIAN ---
    $sql2 = "SELECT count(id_antrian) as max_id FROM tbl_antrian WHERE type='Teller' AND tgl_antri=?";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("s", $today);
    $stmt2->execute();
    $res2 = $stmt2->get_result();
    $row  = $res2->fetch_assoc();
    
    $next = ($row['max_id'] ?? 0) + 1;
    // Format nomor antrian agar tetap rapi (TL-01, TL-09, TL-10)
    $next_id = "TL-" . str_pad($next, 2, "0", STR_PAD_LEFT);

    // --- 3. INSERT tbl_antrian ---
    $sql3 = "INSERT INTO tbl_antrian (tgl_antri, nama_antrian, type, antrian, kode) VALUES (?, ?, 'Teller', ?, ?)";
    $stmt3 = $conn->prepare($sql3);
    $stmt3->bind_param("ssss", $today, $nama, $next_id, $token);
    $stmt3->execute();

    // Commit jika semua query berhasil
    $conn->commit();
    session_start();

    // Pilihan A: Hanya hapus data transfer (Nasabah tetap login)
    if (isset($_SESSION['transfer_data'])) {
        unset($_SESSION['transfer_data']);
    }
    // Redirect bersih
    header("Location: output.php?id=" . urlencode($token));
    exit();

} catch (Exception $e) {
    // Batalkan semua jika ada satu saja yang gagal
    $conn->rollback();
    
    // Simpan pesan error di log server, jangan tampilkan ke user
    error_log("Error Setoran: " . $e->getMessage());
    die("Maaf, terjadi kesalahan teknis. Silakan coba beberapa saat lagi.");
}

$conn->close();
?>