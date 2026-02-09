<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Withdrawal;
use App\Models\Transfer;
use Carbon\Carbon;

// Manually load FPDF Library (Relocated from legacy)
require_once(app_path('Libraries/FPDF/fancyrow.php'));

class PrintController extends Controller
{
    /**
     * Print Tarik Tunai PDF (2 Pages)
     */
    public function printTarik($token)
    {
        $withdrawal = Withdrawal::where('token', $token)->firstOrFail();
        
        $p3 = $withdrawal->nominal;
        $p4 = $withdrawal->terbilang;
        $p5 = $withdrawal->tujuan;
        $p7 = $withdrawal->created;
        $nama = $withdrawal->nama;
        $no_rek = $withdrawal->no_rek;
        
        $nama_penarik = $withdrawal->nama_penarik;
        $noid_penarik = $withdrawal->noid_penarik;
        $hp_penarik = $withdrawal->hp_penarik;
        $alamat_penarik = $withdrawal->alamat_penarik;

        $pdf = new \PDF_FancyRow('l', 'mm', array(100, 210));
        $pdf->SetAutoPageBreak(false, 0.1);
        $pdf->SetLeftMargin(2);
        $pdf->SetRightMargin(1);
        
        // PAGE 1: DEPAN
        $pdf->AddPage();
        $pdf->Ln(25); // Moved down 10mm per user request (Base 10 + 10)
        
        $date = Carbon::parse($p7);
        $tgl = $date->format('d/m/Y');
        
        $pdf->SetFont('Arial', 'B', 8);
        
        // Tanggal: Adjusted Left (36mm) - matches user request
        $pdf->cell(32, 4, "", '0', '0', 'L', false);
        $pdf->cell(50, 4, $tgl, '0', '1', 'L', false);
        
        $pdf->Ln(3);
        // Nama & No Rek
        $pdf->cell(50, 4, "", '0', '0', 'L', false);
        $pdf->cell(60, 4, $nama, '0', '0', 'L', false);
        $pdf->cell(35, 4, "", '0', '0', 'L', false);
        $pdf->cell(50, 4, $no_rek, '0', '1', 'L', false);
        
        $pdf->Ln(3); // Pushed down for alignment
        // Nominal & Terbilang
        $pdf->cell(47, 4, "", '0', '0', 'L', false);
        $pdf->cell(50, 4, number_format($p3, 2, ",", "."), '0', '0', 'L', false);
        $pdf->cell(35, 4, "", '0', '0', 'L', false);
        $pdf->MultiCell(70, 4, $p4, '0', '1');
        
        // Tujuan (Raised by removing Ln)
        $pdf->cell(35, 4, "", '0', '0', 'L', false);
        $pdf->cell(50, 4, $p5, '0', '0', 'L', false);
        
        // PAGE 2: BELAKANG
        $pdf->AddPage();
        $pdf->Ln(11); // Moved down +3mm per user request
        
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->cell(140, 4, "", '0', '0', 'L', false);
        $pdf->cell(50, 4, $nama_penarik, '0', '1', 'L', false);
        
        $pdf->Ln(7);
        $pdf->cell(140, 4, "", '0', '0', 'L', false);
        $pdf->cell(50, 4, $noid_penarik, '0', '1', 'L', false);
        
        $pdf->Ln(7);
        $pdf->cell(140, 4, "", '0', '0', 'L', false);
        $pdf->cell(50, 4, $hp_penarik, '0', '1', 'L', false);
        
        $pdf->Ln(8);
        $pdf->cell(140, 4, "", '0', '0', 'L', false);
        
        // Alamat: Smaller font & tighter spacing
        $pdf->SetFont('Arial', 'B', 7);
        $pdf->MultiCell(70, 3, $alamat_penarik, '0', '1');

        return response($pdf->Output('S'))->header('Content-Type', 'application/pdf');
    }

    /**
     * Print Transfer PDF (Landscape 210x150)
     */
    public function printTransfer($token)
    {
        $transfer = Transfer::where('token', $token)->firstOrFail();
        
        $pdf = new \PDF_FancyRow('l', 'mm', array(150, 210));
        $pdf->SetAutoPageBreak(false, 0.1);
        $pdf->SetLeftMargin(2);
        $pdf->SetRightMargin(1);
        $pdf->AddPage();
        
        $pdf->SetFont('Arial', 'B', 8);
        $tgl = Carbon::parse($transfer->created)->format('d / m / Y');

        // --- SECTION: HEADER ---
        // Tanggal: Y = 23mm (Fixed)
        $pdf->SetXY(45, 23);
        $pdf->cell(50, 4, $tgl, '0', '1', 'L', false);
        
        // --- SECTION: PENERIMA (Absolute Positioning) ---
        // Nama: Naik lagi 3mm (Y: 35 -> 32)
        $pdf->SetXY(55, 34);
        $pdf->cell(60, 4, $transfer->nama_tujuan, '0', '1', 'L', false);
        
        // Alamat: Naik lagi 3mm (Y: 40 -> 37)
        $pdf->SetXY(55, 39);
        $pdf->cell(60, 4, $transfer->alamat_tujuan, '0', '1', 'L', false);
        
        // No Rekening: Y = 51mm (Base 48 + 3mm)
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetXY(55, 50);
        $pdf->cell(60, 4, $transfer->no_rek_tujuan, '0', '1', 'L', false);
        
        // Bank: Y = 56mm (Base 53 + 3mm)
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetXY(55, 55);
        $pdf->cell(60, 4, $transfer->bank_tujuan, '0', '1', 'L', false);
        
        // Negara: Y = 69mm (Base 57 + 12mm)
        $pdf->SetXY(55, 69);
        $pdf->cell(60, 4, "Indonesia", '0', '1', 'L', false);

        // --- SECTION: NOMINAL (Refining per Round 13 request) ---
        // Nominal Dikirim: Naik 5mm lagi (Y: 49 -> 44)
        $pdf->SetXY(170, 44);
        $pdf->cell(20, 4, number_format($transfer->nominal, 0, ",", "."), '0', '1', 'R', false);
        
        // Biaya Admin: Naik 2mm (Y: 64 -> 62)
        $pdf->SetXY(170, 62);
        $pdf->cell(20, 4, number_format($transfer->biaya_trf, 0, ",", "."), '0', '1', 'R', false);
        
        // Total Nominal: Naik 2mm (Y: 70 -> 68)
        $pdf->SetXY(170, 68);
        $pdf->cell(20, 4, number_format($transfer->nominal, 0, ",", "."), '0', '1', 'R', false);
        
        // Terbilang: Tetap (Y: 72)
        $pdf->SetXY(135, 72);
        $pdf->MultiCell(70, 4, $transfer->terbilang, '0', 'L');

        // --- SECTION: PEMOHON / PENGIRIM (Raised another 5mm per user request) ---
        // Base Pemohon Nama: Y = 83mm (Previously 88)
        $pdf->SetXY(55, 83);
        $pdf->SetFont('Arial', 'B', 7);
        $pdf->cell(60, 4, $transfer->nama, '0', '1', 'L', false);
        
        // Alamat
        $pdf->SetXY(55, 87);
        $pdf->SetFont('Arial', 'B', 6);
        $pdf->MultiCell(50, 4, $transfer->alamat_penyetor, '0', 'L');
        
        // HP Penyetor: Y = 96mm (Previously 101)
        $pdf->SetXY(55, 96);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->cell(60, 4, $transfer->hp_penyetor, '0', '1', 'L', false);
        
        // No Rekening Penyetor: Y = 102mm (Previously 107)
        $pdf->SetXY(72, 102);
        $pdf->cell(60, 4, $transfer->no_rek, '0', '1', 'L', false);
        
        // Berita: Y = 116mm (Previously 121)
        $pdf->SetXY(126, 116);
        $pdf->SetFont('Arial', 'B', 7);
        $pdf->cell(60, 4, $transfer->berita_tujuan, '0', '1', 'L', false);
        
        return response($pdf->Output('S'))->header('Content-Type', 'application/pdf');
    }

    /**
     * Print Setor Tunai PDF
     */
    public function printSetor($token)
    {
        $deposit = \App\Models\Transaction::where('token', $token)->firstOrFail();
        
        $pdf = new \PDF_FancyRow('l', 'mm', array(100, 210));
        $pdf->SetAutoPageBreak(false, 0.1);
        $pdf->SetLeftMargin(2);
        $pdf->SetRightMargin(1);
        $pdf->AddPage();
        
        $pdf->Ln(20);
        $pdf->Ln(1);
        
        $tgl = Carbon::parse($deposit->created)->format('d/m/Y');
        
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->cell(30, 4, "", '0', '0', 'L', false);
        $pdf->cell(55, 4, $tgl, '0', '1', 'L', false);
        
        $pdf->Ln(2);
        $pdf->cell(35, 4, "", '0', '0', 'L', false);
        $pdf->cell(65, 4, $deposit->nama, '0', '0', 'L', false);
        $pdf->cell(48, 4, "", '0', '0', 'L', false);
        $pdf->cell(53, 4, $deposit->no_rek, '0', '1', 'L', false);
        
        // Nominal (Raised 2mm by removing Ln)
        $pdf->Ln(1);
        $pdf->cell(53, 2, "", '0', '0', 'L', false);
        $pdf->cell(53, 4, number_format($deposit->nominal, 2, ",", "."), '0', '1', 'L', false);
        
        $pdf->cell(30, 2, "", '0', '0', 'L', false);
        $pdf->MultiCell(150, 4, $deposit->terbilang, '0', '1');
        
        $pdf->Ln(5);
        $pdf->cell(40, 2, "", '0', '0', 'L', false);
        $pdf->cell(40, 4, $deposit->tujuan, '0', '1', 'L', false);
        
        $pdf->Ln(1);
        $pdf->cell(45, 4, "", '0', '0', 'L', false);
        $pdf->cell(40, 4, $deposit->nama_penyetor, '0', '1', 'L', false);
        
        $pdf->Ln(4);
        $pdf->cell(45, 3, "", '0', '0', 'L', false);
        $pdf->cell(50, 3, $deposit->noid_penyetor, '0', '1', 'L', false);
        
        $pdf->cell(45, 3, "", '0', '0', 'L', false);
        $pdf->cell(50, 3, $deposit->alamat_penyetor, '0', '1', 'L', false);
        
        $pdf->cell(45, 4, "", '0', '0', 'L', false);
        $pdf->cell(50, 4, $deposit->hp_penyetor, '0', '1', 'L', false);

        date_default_timezone_set("Asia/Jakarta");
        $pdf->SetFont('Arial', 'B', 7);
        $pdf->cell(0, 3, '-', '0', '1', 'R', false);
        
        return response($pdf->Output('S'))->header('Content-Type', 'application/pdf');
    }
}
