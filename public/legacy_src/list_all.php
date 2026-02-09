<?php 
    $id         = $_GET['id'];
    $id_ex      = explode("-",$id);
    $jenis      = $id_ex[0];
    require 'connection.php';
    
    if($jenis == "ST"){
        $all_data = "SELECT id_setor from tbl_setor where token = '$id' ";
        $result = $conn->query($all_data);
        while($row = $result->fetch_assoc()) { 
            $id = $row['id_setor']; 
        } header("Location:list_setor.php?id=$id");

    }elseif($jenis == "TT"){
        $all_data = "SELECT id_tarik from tbl_tarik where token = '$id' ";
        $result = $conn->query($all_data);
        while($row = $result->fetch_assoc()) { 
            $id = $row['id_tarik']; 
        } header("Location:list_tarik.php?id=$id");

    }elseif($jenis == "CS"){
        $sql = "SELECT id_antrian from tbl_antrian where kode='$id';"; 
        $result = $conn->query($sql);
        while($row = $result->fetch_assoc()) { 
            $id = $row['id_antrian']; 
        } header("Location:list_cs.php?id=$id");

    }else{
        $all_data = "SELECT id_trf from tbl_transfer where token = '$id' ";
        $result = $conn->query($all_data);
        while($row = $result->fetch_assoc()) { 
            $id = $row['id_trf']; 
        } header("Location:list_trf.php?id=$id");
    }  
    
    
?>