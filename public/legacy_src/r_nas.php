<?php 
        require_once('fancyrow.php');
        $id         = $_POST['id'];
        $id_ex      = explode("-",$id);
        $jenis      = $id_ex[0];
        require 'connection.php';
        $sql = "select * from tbl_antrian where kode='$id';"; 
        $result = $conn->query($sql);
        while($row = $result->fetch_assoc()) { 
            $antri = $row['antrian']; 
        }
        // echo $jenis."/". $id;  
        if($jenis == "'ST"){
            // $all_data = $this->db->query("select * from tbl_setor where token = '$id' ");
            $jn = "SETOR TUNAI";
            $tj = "TELLER";
        }elseif($jenis == "'TT"){
            // $all_data = $this->db->query("select * from tbl_tarik where token = '$id' ");
            $jn = "TARIK TUNAI";
            $tj = "TELLER";
        }elseif($jenis == "'CS"){
            $jn = "CS";
            $tj = "CUSTOMER SERVICE";
            // $all_data = $this->db->query("select * from tbl_transfer where token = '$id' ");
        }else{
            $jn = "TRANSFER";
            $tj = "TELLER";
            // $all_data = $this->db->query("select * from tbl_transfer where token = '$id' ");
        }
		
	

        

        
        $pdf=new PDF_FancyRow('p','mm',array(80,140));
        $pdf->SetAutoPageBreak(false, 0.1);
        $pdf->SetLeftMargin(4);
        $pdf->SetRightMargin(3);
        $pdf->AddPage();
        $foto1 = $_SERVER["DOCUMENT_ROOT"].'/mci_paperless/assets/img/logo_400.png';
        $pdf->Image($foto1, 7, 1, 65, 20);
        $pdf->SetFont('Arial','B',9.5);
        // $pdf->Ln(1);
        // $pdf->cell(0,10,'Transaksi Bank Syariah M C I','0','1','C',false);
        $pdf->Ln(15);
        $pdf->cell(73,0.5,'','0','2','C',true);
        $pdf->Ln(1);
        $pdf->cell(73,0.2,'','0','1','C',true);
        $pdf->Ln(3);
        $pdf->Ln(3);
        $pdf->SetFont('Arial','b',16);
        $pdf->cell(0,6,"No Antrian  ",'0','1','C',false);
        $pdf->Ln(12);
        $pdf->SetFont('Arial','b',36);
        $pdf->cell(0,6,$antri,'0','1','C',false);
        $pdf->Ln(6);
        $pdf->Ln(6);
        $pdf->SetFont('Arial','b',12);
        $pdf->cell(17,6,"KODE  ",'0','0','L',false);
        $pdf->cell(0,6," : ".$id,'0','1','L',false);
        $pdf->cell(17,6,"JENIS  ",'0','0','L',false);
        $pdf->cell(0,6," : ".$jn,'0','1','L',false);
        $pdf->SetFont('Arial','b',12);
        $pdf->cell(17,6,"TUJUAN  ",'0','0','L',false);
        $pdf->cell(0,6," : ".$tj,'0','1','L',false);
        date_default_timezone_set("asia/jakarta");
        $pdf->SetFont('Arial','b',5);
        $pdf->Ln(16);
        $pdf->Ln(8);
        $pdf->Ln(13);
        $foto2 = $_SERVER["DOCUMENT_ROOT"].'/mci_paperless/assets/img/back1.png';
        $pdf->Image($foto2, 35, 110, 45, 10);
        $pdf->cell(0,3,'Created On     : '. date("d - F - Y H:i:s")." -" ,'0','1','R',false);
        $pdf->cell(0,3,'https://bprshikmci.co.id/ -' ,'0','1','R',false);
        $pdf->Ln(8);
        $pdf->cell(0,3,'-' ,'0','1','R',false);
		$pdf->AutoPrint();
        $pdf->Output();
?>