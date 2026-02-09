<?php 
 require 'connection.php';
 $jns_trx           = $_GET['jns'];
 $id_antrian        = $_GET['id']; 


    if($jns_trx == "CS"){
        $all_data = "UPDATE tbl_antrian set st_antrian='2' where id_antrian = '$id_antrian' ";
        if ($conn->query($all_data) === TRUE) {
            header("Location:list.php?jenis=CS");
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }else{
        $all_data = "UPDATE tbl_antrian set st_antrian='2' where id_antrian = '$id_antrian' ";
        if ($conn->query($all_data) === TRUE) {
            header("Location:list.php?jenis=Teller");
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }  
    
    
?>