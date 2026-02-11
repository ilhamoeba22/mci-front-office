<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Withdrawal;
use App\Models\Transfer;
use App\Models\Transaction;
use App\Models\Queue;
use Carbon\Carbon;

// Manually load FPDF Library (Relocated from legacy)
require_once(app_path('Libraries/FPDF/fancyrow.php'));

class PrintController extends Controller
{
    /**
     * Print Professional Transaction Report (A4)
     * This report gives a high-level summary of the queue and transaction details.
     */
    public function printReport($token)
    {
        $type = "";
        $data = null;
        $title = "";

        if (str_starts_with($token, 'ST-')) {
            $data = Transaction::where('token', $token)->firstOrFail();
            $type = "SETOR TUNAI";
            $title = "LAPORAN TRANSAKSI SETOR TUNAI";
        } elseif (str_starts_with($token, 'TT-')) {
            $data = Withdrawal::where('token', $token)->firstOrFail();
            $type = "TARIK TUNAI";
            $title = "LAPORAN TRANSAKSI TARIK TUNAI";
        } elseif (str_starts_with($token, 'ON-')) {
            $data = Transfer::where('token', $token)->firstOrFail();
            $type = "TRANSFER";
            $title = "LAPORAN TRANSAKSI TRANSFER ONLINE";
        } else {
            // Fallback to Queue search if prefix unknown
            $queue = Queue::where('kode', $token)->firstOrFail();
            if ($queue->tx_type == 'Setor Tunai') {
                $data = Transaction::where('token', $token)->first();
                $type = "SETOR TUNAI";
            } elseif ($queue->tx_type == 'Tarik Tunai') {
                $data = Withdrawal::where('token', $token)->first();
                $type = "TARIK TUNAI";
            } elseif ($queue->tx_type == 'Transfer') {
                $data = Transfer::where('token', $token)->first();
                $type = "TRANSFER";
            }
            if (!$data) abort(404, "Transaction data not found.");
            $title = "LAPORAN TRANSAKSI " . strtoupper($type);
        }

        $pdf = new \PDF_FancyRow('P', 'mm', 'A4');
        $pdf->SetMargins(20, 20, 20);
        $pdf->AddPage();

        // --- HEADER ---
        $pdf->Image(public_path('img/logo_mci.png'), 15, 12, 45);
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->SetTextColor(0, 51, 102); // Dark Blue
        $pdf->Cell(0, 7, 'BPRS HIK MCI', 0, 1, 'R');
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(0, 4, 'Jl. Kaliurang KM 9, Yogyakarta', 0, 1, 'R');
        $pdf->Cell(0, 4, 'Telp: (0274) 123456 | www.mci-hik.com', 0, 1, 'R');
        
        $pdf->Ln(2);
        $pdf->SetDrawColor(0, 51, 102);
        $pdf->SetLineWidth(0.8);
        $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
        $pdf->Ln(4);

        // --- TITLE ---
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetFillColor(230, 240, 255);
        $pdf->Cell(0, 10, $title, 0, 1, 'C', true);
        $pdf->Ln(2);

        // --- INFO TRANSAKSI ---
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(35, 6, 'Nomor Referensi', 0, 0);
        $pdf->Cell(5, 6, ':', 0, 0);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(60, 6, $token, 0, 0);
        
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(35, 6, 'Tanggal Transaksi', 0, 0);
        $pdf->Cell(5, 6, ':', 0, 0);
        $pdf->Cell(0, 6, date('d/m/Y H:i', strtotime($data->created)), 0, 1);

        $pdf->Cell(35, 6, 'Jenis Layanan', 0, 0);
        $pdf->Cell(5, 6, ':', 0, 0);
        $pdf->Cell(60, 6, $type, 0, 0);

        $pdf->Cell(35, 6, 'Status', 0, 0);
        $pdf->Cell(5, 6, ':', 0, 0);
        $pdf->SetTextColor(0, 128, 0);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(0, 6, 'BERHASIL/DONE', 0, 1);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', '', 10);

        // --- BOX: ACCOUNT INFO ---
        $pdf->Ln(3);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(0, 7, '  DETAIL REKENING NASABAH', 0, 1, 'L', true);
        $pdf->SetFont('Arial', '', 10);
        
        $pdf->Ln(1);
        $pdf->Cell(40, 6, 'Nomor Rekening', 0, 0);
        $pdf->Cell(5, 6, ':', 0, 0);
        $pdf->Cell(0, 6, $data->no_rek, 0, 1);
        
        $pdf->Cell(40, 6, 'Nama Pemilik', 0, 0);
        $pdf->Cell(5, 6, ':', 0, 0);
        $pdf->Cell(0, 6, $data->nama, 0, 1);

        if (isset($data->jenis_rekening)) {
            $pdf->Cell(40, 6, 'Produk', 0, 0);
            $pdf->Cell(5, 6, ':', 0, 0);
            $pdf->Cell(0, 6, $data->jenis_rekening, 0, 1);
        }

        // --- BOX: RECIPIENT/SENDER INFO ---
        if ($type == 'TRANSFER') {
            $pdf->Ln(3);
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(0, 7, '  INFORMASI PENERIMA TRANSFER', 0, 1, 'L', true);
            $pdf->SetFont('Arial', '', 10);
            
            $pdf->Ln(1);
            $pdf->Cell(40, 6, 'Nama Bank', 0, 0);
            $pdf->Cell(5, 6, ':', 0, 0);
            $pdf->Cell(0, 6, $data->bank_tujuan . ' (Kode: ' . ($data->kode_bank ?? '-') . ')', 0, 1);
            
            $pdf->Cell(40, 6, 'Nomor Rekening', 0, 0);
            $pdf->Cell(5, 6, ':', 0, 0);
            $pdf->Cell(0, 6, $data->no_rek_tujuan, 0, 1);
            
            $pdf->Cell(40, 6, 'Nama Penerima', 0, 0);
            $pdf->Cell(5, 6, ':', 0, 0);
            $pdf->Cell(0, 6, $data->nama_tujuan, 0, 1);

            if (isset($data->alamat_tujuan) && $data->alamat_tujuan) {
                $pdf->Cell(40, 6, 'Alamat & Kota', 0, 0);
                $pdf->Cell(5, 6, ':', 0, 0);
                $pdf->Cell(0, 6, $data->alamat_tujuan . ' - ' . ($data->kota_tujuan ?? ''), 0, 1);
            }

            if (isset($data->hp_penerima) && $data->hp_penerima) {
                $pdf->Cell(40, 6, 'No. HP Penerima', 0, 0);
                $pdf->Cell(5, 6, ':', 0, 0);
                $pdf->Cell(0, 6, $data->hp_penerima, 0, 1);
            }

            $pdf->Cell(40, 6, 'Jenis Transfer', 0, 0);
            $pdf->Cell(5, 6, ':', 0, 0);
            $pdf->Cell(0, 6, $data->jenis_trf ?? 'ONLINE', 0, 1);

            $pdf->Cell(40, 6, 'Berita / Pesan', 0, 0);
            $pdf->Cell(5, 6, ':', 0, 0);
            $pdf->Cell(0, 6, $data->berita_tujuan ?? $data->tujuan ?? '-', 0, 1);

        } else {
            $pdf->Ln(3);
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(0, 7, '  INFORMASI ' . ($type == 'SETOR TUNAI' ? 'PENYETOR' : 'PENARIK'), 0, 1, 'L', true);
            $pdf->SetFont('Arial', '', 10);
            
            $pdf->Ln(1);
            $namaActor = ($type == 'SETOR TUNAI') ? $data->nama_penyetor : $data->nama_penarik;
            $noidActor = ($type == 'SETOR TUNAI') ? $data->noid_penyetor : $data->noid_penarik;
            $hpActor = ($type == 'SETOR TUNAI') ? $data->hp_penyetor : $data->hp_penarik;
            $alamatActor = ($type == 'SETOR TUNAI') ? $data->alamat_penyetor : $data->alamat_penarik;
            
            $pdf->Cell(40, 6, 'Nama Pelaku', 0, 0);
            $pdf->Cell(5, 6, ':', 0, 0);
            $pdf->Cell(0, 6, $namaActor, 0, 1);
            
            $pdf->Cell(40, 6, 'No. Identitas', 0, 0);
            $pdf->Cell(5, 6, ':', 0, 0);
            $pdf->Cell(0, 6, $noidActor, 0, 1);

            $pdf->Cell(40, 6, 'No. HP', 0, 0);
            $pdf->Cell(5, 6, ':', 0, 0);
            $pdf->Cell(0, 6, $hpActor, 0, 1);

            $pdf->Cell(40, 6, 'Alamat', 0, 0);
            $pdf->Cell(5, 6, ':', 0, 0);
            $pdf->MultiCell(0, 6, $alamatActor, 0, 'L');

            $pdf->Cell(40, 6, 'Keperluan', 0, 0);
            $pdf->Cell(5, 6, ':', 0, 0);
            $pdf->Cell(0, 6, $data->tujuan ?? $data->berita ?? '-', 0, 1);
        }

        // --- BOX: AMOUNT ---
        $pdf->Ln(4);
        if ($pdf->GetY() > 230) $pdf->AddPage(); // Safeguard

        $pdf->SetDrawColor(0, 0, 0);
        $pdf->SetLineWidth(0.3);
        $nominal = (float) $data->nominal;
        $fee = ($type == 'TRANSFER') ? (float) $data->biaya_trf : 0;
        
        // Compact height calculation
        $heightBox = ($fee > 0) ? 28 : 20; 
        $pdf->Rect(20, $pdf->GetY(), 170, $heightBox);
        
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(40, 7, '   JUMLAH NOMINAL', 0, 0);
        $pdf->Cell(5, 7, ':', 0, 0);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 7, 'Rp ' . number_format($nominal, 0, ',', '.'), 0, 1);
        
        if ($fee > 0) {
            $pdf->SetFont('Arial', '', 9);
            $pdf->Cell(40, 5, '   BIAYA ADMIN', 0, 0);
            $pdf->Cell(5, 5, ':', 0, 0);
            $pdf->Cell(60, 5, 'Rp ' . number_format($fee, 0, ',', '.'), 0, 0);

            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(30, 5, 'TOTAL DEBET', 0, 0);
            $pdf->Cell(5, 5, ':', 0, 0);
            $pdf->Cell(0, 5, 'Rp ' . number_format($nominal + $fee, 0, ',', '.'), 0, 1);
        }

        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(40, 6, '   TERBILANG', 0, 0);
        $pdf->Cell(5, 6, ':', 0, 0);
        $pdf->SetFont('Arial', 'IB', 8);
        $pdf->MultiCell(120, 6, '# ' . $data->terbilang . ' #', 0, 'L');

        // --- SIGNATURES (Internal Document) ---
        $pdf->Ln(8);
        $pdf->SetFont('Arial', '', 10);
        
        $pdf->Cell(56, 15, 'Teller,', 0, 0, 'C');
        $pdf->Cell(58, 15, 'Checker,', 0, 0, 'C');
        $pdf->Cell(56, 15, 'Kepala Divisi Operasional,', 0, 1, 'C');
        
        $pdf->Ln(15);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(56, 5, '( ____________________ )', 0, 0, 'C');
        $pdf->Cell(58, 5, '( ____________________ )', 0, 0, 'C');
        $pdf->Cell(56, 5, '( ____________________ )', 0, 1, 'C');
        
        // --- FOOTER ---
        $pdf->SetY(-31);
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->SetTextColor(128, 128, 128);
        $pdf->Cell(0, 5, 'Dicetak pada: ' . date('d/m/Y H:i:s') . ' | Oleh: Sistem Antrian MCI', 0, 1, 'C');
        $pdf->SetTextColor(0, 0, 0);

        return response($pdf->Output('S'))->header('Content-Type', 'application/pdf');
    }
}
