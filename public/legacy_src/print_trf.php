<?php 
        require_once('fancyrow.php');
        $iid         = $_GET['iid'];
        $id_ex      = explode("-",$iid);
        $jenis      = $id_ex[0];
        require 'connection.php';
            $sql = "select * from tbl_transfer where token = '$iid'"; 
            $result = $conn->query($sql);
            while($row = $result->fetch_assoc()) { 
                $no_rek     = $row['no_rek']; 
                $nama       = $row['nama']; 
                $p1         = $row['nama_penyetor'];
                $p2         = $row['hp_penyetor']; 
				$p3         = $row['nominal']; 
                $p4         = $row['terbilang'];
                $p5         = $row['tujuan'];
				$p6         = $row['berita_tujuan'];
				$p7         = $row['created'];
				$alamat_         = $row['alamat_'];
				$nama_tujuan        = $row['nama_tujuan'];
				$alamat_tujuan      = $row['alamat_tujuan'];
				$no_rek_tujuan      = $row['no_rek_tujuan'];
				$bank_tujuan       	= $row['bank_tujuan'];
				$biaya_trf       	= $row['biaya_trf'];
            }

        

        
        $pdf=new PDF_FancyRow('l','mm',array(150,210));
        $pdf->SetAutoPageBreak(false, 0.1);
        $pdf->SetLeftMargin(2);
        $pdf->SetRightMargin(1);
        $pdf->AddPage();
        $pdf->Ln(13);
		$date = date_create($p7);
		$tgl  = date_format($date,"d / m / Y");
        $pdf->SetFont('Arial','b',8);
		$pdf->cell(35,4,"",'0','0','L',false);
        $pdf->cell(50,4,$tgl,'0','1','L',false);
		$pdf->Ln(9);
		$pdf->SetFont('Arial','b',8);
        $pdf->cell(53,4,"",'0','0','L',false);
        $pdf->cell(60,4,"".$nama_tujuan,'0','1','L',false);
		$pdf->Ln(1);
		$pdf->cell(53,4,"",'0','0','L',false);
        $pdf->cell(60,4,"".$alamat_tujuan,'0','1','L',false);
		$pdf->cell(168,4,"",'0','0','L',false);
		$pdf->SetFont('Arial','b',8);
        $pdf->cell(20,4,number_format($p3,0,",","."),'0','1','R',false);
		$pdf->SetFont('Arial','b',8);
		$pdf->Ln(2);
		$pdf->cell(53,4,"",'0','0','L',false);
		$pdf->SetFont('Arial','b',10);
        $pdf->cell(60,4,"".$no_rek_tujuan,'0','1','L',false);
		$pdf->Ln(1);
		$pdf->SetFont('Arial','b',8);
		$pdf->cell(53,4,"",'0','0','L',false);
        $pdf->cell(60,4,"".$bank_tujuan,'0','1','L',false);
		$pdf->Ln(1);
		$pdf->cell(55,4,"",'0','0','L',false);
        $pdf->cell(60,4,"",'0','0','L',false);
		$pdf->cell(53,4,"",'0','0','L',false);
        $pdf->cell(20,4,number_format($biaya_trf,0,",","."),'0','1','R',false);
		$pdf->Ln(3);
		$pdf->SetFont('Arial','b',8);
		$pdf->cell(55,4,"",'0','0','L',false);
        $pdf->cell(60,4,"",'0','0','L',false);
		$pdf->cell(53,4,"",'0','0','L',false);
        $pdf->cell(20,4,number_format($p3,0,",","."),'0','1','R',false);
		
		$pdf->cell(53,4,"",'0','0','L',false);
        $pdf->cell(60,4,"Indonesia",'0','0','L',false);
        $pdf->cell(20,4,"",'0','0','R',false);
		$pdf->MultiCell(60,4, $p4, '0','1');
        
		
		$pdf->Ln(3);
		$pdf->SetFont('Arial','b',7);
		$pdf->cell(53,4,"",'0','0','L',false);
        $pdf->cell(60,4,"".$nama,'0','1','L',false);
		$pdf->cell(53,4,"",'0','0','L',false);
		$pdf->SetFont('Arial','b',6);
        $pdf->MultiCell(50,4, $alamat_, '0','1');
		$pdf->SetFont('Arial','b',5);
		$pdf->Ln(4);
		$pdf->SetFont('Arial','b',9);
		$pdf->cell(53,4,"",'0','0','L',false);
        $pdf->cell(60,4,"".$p2,'0','1','L',false);
		$pdf->Ln(1);
		$pdf->SetFont('Arial','b',9);
		$pdf->cell(70,4,"",'0','0','L',false);
        $pdf->cell(60,4,"".$no_rek,'0','1','L',false);
		$pdf->Ln(6);
		$pdf->SetFont('Arial','b',7);
		$pdf->cell(53,4,"",'0','0','L',false);
        $pdf->cell(60,4,"",'0','0','L',false);
		$pdf->cell(5,4,"",'0','0','L',false);
        $pdf->cell(60,4,"".$p6,'0','1','L',false);
		
        
        date_default_timezone_set("asia/jakarta");
        $pdf->SetFont('Arial','b',7);
        $pdf->cell(0,3,'-' ,'0','1','R',false);
        $pdf->Output();
?>