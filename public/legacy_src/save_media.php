<?php
require 'connection.php';
 $maxsize = 20242880; // 20MB
 $name = $_FILES['file']['name'];
 $target_dir = "assets/media/";
 $target_file = $target_dir . $_FILES["file"]["name"];

 // Select file type
 $videoFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

 // Valid file extensions
 $extensions_arr = array("mp4","avi","3gp","mov","mpeg");

 // Check extension
 if( in_array($videoFileType,$extensions_arr) ){
    // Check file size
    if(($_FILES['file']['size'] >= $maxsize) || ($_FILES["file"]["size"] == 0)) {
        echo "File too large. File must be less than 5MB.";
    }else{
        if(move_uploaded_file($_FILES['file']['tmp_name'],$target_file)){
            $query = "UPDATE  tbl_set set nama_set='Media_antrian', value='$name', st_set='1' where jenis_set='Video'";
            mysqli_query($conn,$query);
            header("Location: aiueo.php");
        }
    }
 }else{
    echo "Invalid file extension.";
 }
 
 
?>