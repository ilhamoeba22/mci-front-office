<?php 
        require_once('fancyrow.php');
        $iid         = $_GET['iid'];
        $id_ex      = explode("-",$iid);
        $jenis      = $id_ex[0];
        require 'connection.php';
            $sql = "select * from tbl_setor where token = '$iid'"; 
            $result = $conn->query($sql);
            $jn = "TANDA BUKTI SETOR TUNAI";
            $tj = "Penyetor";
			$tt = "Teller";
            while($row = $result->fetch_assoc()) { 
                $no_rek     = $row['no_rek']; 
                $nama       = $row['nama']; 
                $p1         = $row['nama_penyetor'];
                $p2         = $row['hp_penyetor']; 
				$p3         = $row['nominal']; 
                $p4         = $row['terbilang'];
                $p5         = $row['tujuan'];
				$p6         = $row['berita'];
				$p7         = $row['created'];
				$noid_penyetor         = $row['noid_penyetor'];
				$alamat_penyetor         = $row['alamat_penyetor'];
            }

        

        
        $pdf=new PDF_FancyRow('l','mm',array(100,210));
        $pdf->SetAutoPageBreak(false, 0.1);
        $pdf->SetLeftMargin(2);
        $pdf->SetRightMargin(1);
        $pdf->AddPage();
        $pdf->Ln(20);
        $pdf->Ln(1);
		$date = date_create($p7);
		$tgl  = date_format($date,"d/m/Y");
        $pdf->SetFont('Arial','b',8);
		$pdf->cell(25,4,"",'0','0','L',false);
        $pdf->cell(50,4,$tgl,'0','1','L',false);
		$pdf->Ln(2);
        $pdf->cell(30,4,"",'0','0','L',false);
        $pdf->cell(60,4,"".$nama,'0','0','L',false);
        $pdf->cell(45,4,"",'0','0','L',false);
        $pdf->cell(50,4,"".$no_rek,'0','1','L',false);
		$pdf->Ln(2);
		$pdf->cell(50,4,"",'0','0','L',false);
        $pdf->cell(50,4,number_format($p3,2,",","."),'0','1','L',false);
		$pdf->cell(30,4,"",'0','0','L',false);
		$pdf->MultiCell(70,4, $p4, '0','1');
		$pdf->Ln(5);
		$pdf->cell(40,4,"",'0','0','L',false);
		$pdf->cell(40,4,$p5,'0','1','L',false);
		$pdf->Ln(1);
		$pdf->cell(45,4,"",'0','0','L',false);
		$pdf->cell(40,4,$p1,'0','1','L',false);
		$pdf->Ln(4);
		$pdf->cell(45,3,"",'0','0','L',false);
		$pdf->cell(50,3,"".$noid_penyetor,'0','1','L',false);
		$pdf->cell(45,3,"",'0','0','L',false);
		$pdf->cell(50,3,"".$alamat_penyetor,'0','1','L',false);
		
		$pdf->cell(45,4,"",'0','0','L',false);
		$pdf->cell(50,4,$p2,'0','0','L',false);
		//$pdf->cell(10,4,"",'0','0','L',false);
		//$pdf->cell(40,4,$p6,'0','1','L',false);
		
        
        date_default_timezone_set("asia/jakarta");
        $pdf->SetFont('Arial','b',7);
        $pdf->cell(0,3,'-' ,'0','1','R',false);
        $pdf->Output();
?>