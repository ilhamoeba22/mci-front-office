<?php 
        require_once('fancyrow.php');
        $id         = $_POST['id'];
        $id_ex      = explode("-",$id);
        $jenis      = $id_ex[0];
        require 'connection.php';
        $sql = "select * from tbl_antrian where kode=$id;"; 
        $result = $conn->query($sql);
        while($row = $result->fetch_assoc()) { 
            $antri = $row['antrian']; 
        }
        // echo $jenis."/". $id;  
        if($jenis == "'ST"){
            // $all_data = $this->db->query("select * from tbl_setor where token = '$id' ");
            $jn = "SETOR TUNAI";
        }elseif($jenis == "'TT"){
            // $all_data = $this->db->query("select * from tbl_tarik where token = '$id' ");
            $jn = "TARIK TUNAI";
        }elseif($jenis == "'CS"){
            $jn = "Customer Service";
            // $all_data = $this->db->query("select * from tbl_transfer where token = '$id' ");
        }else{
            $jn = "TRANSFER";
            // $all_data = $this->db->query("select * from tbl_transfer where token = '$id' ");
        }

        

        
        $pdf=new PDF_FancyRow('p','mm',array(100,50));
        $pdf->SetAutoPageBreak(false, 0.1);
        $pdf->SetLeftMargin(0);
        $pdf->SetRightMargin(5);
		$pdf->SetTopMargin(2);
        $pdf->AddPage();
        //$foto1 = $_SERVER["DOCUMENT_ROOT"].'/mci_paperless/assets/img/bprs_ok.png';
        //$pdf->Image($foto1, 0, 1, 45, 15);
        //$pdf->SetFont('Arial','B',9.5);
        // $pdf->Ln(1);
        // $pdf->cell(0,10,'Transaksi Bank Syariah M C I','0','1','C',false);
        //$pdf->Ln(6);
        //$pdf->Ln(1);
		$pdf->SetFont('Arial','b',8);
        $pdf->cell(0,6,'PT. Bank Pembiayaan Rakyat','0','1','C',false);
		$pdf->SetFont('Arial','b',10);
		$pdf->cell(0,6,'Syariah','0','1','C',false);
		$pdf->SetFont('Arial','b',10);
		$pdf->cell(0,6,'Mitra Cahaya Indonesia','0','1','C',false);
        $pdf->cell(46,0.5,'','0','2','C',true);
        $pdf->Ln(1);
        $pdf->cell(46,0.2,'','0','1','C',true);
        $pdf->Ln(3);
        $pdf->SetFont('Arial','b',12);
        $pdf->cell(25,6,"No Antrian  ",'0','1','L',false);
        $pdf->Ln(5);
        $pdf->SetFont('Arial','b',24);
        $pdf->cell(0,6,$antri,'0','1','C',false);
        $pdf->Ln(3);
        $pdf->Ln(3);
        $pdf->SetFont('Arial','b',9);
        $pdf->cell(10,6,"KODE  ",'0','0','L',false);
        $pdf->cell(0,6," : ".$id,'0','1','L',false);
        $pdf->cell(10,6,"JENIS  ",'0','0','L',false);
        $pdf->cell(0,6," : ".$jn,'0','1','L',false);
        date_default_timezone_set("asia/jakarta");
        $pdf->SetFont('Arial','b',6);
        $pdf->Ln(8);
		$pdf->Ln(5);
        $foto2 = $_SERVER["DOCUMENT_ROOT"].'/mci_paperless/assets/img/back1.png';
        $pdf->Image($foto2, 3, 60, 45, 10);
        
		$pdf->SetFont('Arial','b',8);
		$pdf->cell(0,3,'Melangkah Pasti...','0','1','C',false);
		$pdf->cell(0,3,'Melayani Negeri...','0','1','R',false);
		$pdf->Ln(5);
		$pdf->SetFont('Arial','b',6);
		
		$pdf->cell(0,3, date("d - m - Y H:i:s") ,'0','1','R',false);
		$pdf->cell(0,3,'Website : https://bprsmci.co.id/','0','1','R',false);
		$pdf->Ln(5);
        $pdf->Output();
?>