<?php
require 'connection.php';

// 1. Ambil data dari POST dengan proteksi
$nama           = $_POST['nama'] ?? '';
$no_rek         = $_POST['no_rek'] ?? '';
$tgl            = $_POST['tgl'] ?? '';
$nominal        = $_POST['nominal1'] ?? 0;
$terbilang      = $_POST['terbilang1'] ?? '';
$tujuan         = $_POST['tujuan'] ?? '';
$nama_penyetor  = $_POST['nama_penyetor'] ?? '';
$hp_penyetor    = $_POST['hp_penyetor'] ?? '';
$alamat_        = $_POST['alamat_'] ?? '';

$nama_tujuan    = $_POST['nama_tujuan'] ?? '';
$no_rek_tujuan  = $_POST['no_rek_tujuan'] ?? '';
$bank_tujuan1   = $_POST['bank_tujuan1'] ?? '';
$berita_tujuan  = $_POST['berita_tujuan'] ?? '';
$jenis_trf      = $_POST['jenis_trf'] ?? '';
$hp_penerima    = $_POST['hp_penerima'] ?? '';
$alamat_tujuan  = "-";
$kota_tujuan    = "-";

// 2. Logika Explode Bank & Biaya (dengan pengaman)
$bank_tujuan = "-";
$biaya_trf   = 0;
if (!empty($bank_tujuan1) && strpos($bank_tujuan1, '/') !== false) {
    $pieces = explode("/", $bank_tujuan1);
    $bank_tujuan = $pieces[0];
    $biaya_trf   = $pieces[1];
}

date_default_timezone_set("Asia/Jakarta");
$token = "ON-" . date("ymdHis");
$today = date("ymd");

// Aktifkan laporan error strict untuk mysqli
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // Mulai Transaksi Database
    $conn->begin_transaction();

    // --- 1. INSERT KE tbl_transfer ---
    $sql_trf = "INSERT INTO tbl_transfer (token, nama, no_rek, tgl, nominal, terbilang, tujuan, nama_penyetor, hp_penyetor, 
                nama_tujuan, no_rek_tujuan, bank_tujuan, berita_tujuan, alamat_tujuan, kota_tujuan, biaya_trf, jenis_trf, hp_penerima, alamat_) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt1 = $conn->prepare($sql_trf);
    // Bind 19 parameter (semua string "s")
    $stmt1->bind_param("sssssssssssssssssss", 
        $token, $nama, $no_rek, $tgl, $nominal, $terbilang, $tujuan, $nama_penyetor, $hp_penyetor,
        $nama_tujuan, $no_rek_tujuan, $bank_tujuan, $berita_tujuan, $alamat_tujuan, $kota_tujuan, 
        $biaya_trf, $jenis_trf, $hp_penerima, $alamat_
    );
    $stmt1->execute();

    // --- 2. HITUNG ANTRIAN ---
    $sql_count = "SELECT count(id_antrian) as max_id FROM tbl_antrian WHERE type='Teller' AND tgl_antri=?";
    $stmt2 = $conn->prepare($sql_count);
    $stmt2->bind_param("s", $today);
    $stmt2->execute();
    $res2 = $stmt2->get_result();
    $row = $res2->fetch_assoc();
    
    $next = ($row['max_id'] ?? 0) + 1;
    $next_id = "TL-" . str_pad($next, 2, "0", STR_PAD_LEFT);

    // --- 3. INSERT KE tbl_antrian ---
    $sql_antri = "INSERT INTO tbl_antrian (tgl_antri, nama_antrian, type, antrian, kode) VALUES (?, ?, 'Teller', ?, ?)";
    $stmt3 = $conn->prepare($sql_antri);
    $stmt3->bind_param("ssss", $today, $nama, $next_id, $token);
    $stmt3->execute();

    // Simpan semua perubahan
    $conn->commit();
    session_start();

    // Pilihan A: Hanya hapus data transfer (Nasabah tetap login)
    if (isset($_SESSION['transfer_data'])) {
        unset($_SESSION['transfer_data']);
    }
    // Redirect aman tanpa mencetak query ke layar (keamanan info)
    header("Location: output.php?id=" . urlencode($token));
    exit();

} catch (Exception $e) {
    // Batalkan semua jika ada error
    $conn->rollback();
    
    error_log("Gagal Transfer Online: " . $e->getMessage());
    die("Sistem sedang sibuk. Mohon maaf, data tidak dapat diproses.");
}

$conn->close();
?>