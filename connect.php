<?php
    $server ='localhost';
    $user ='root';
    $pass ='';
    $database = 'web_gym';

    $conn = new mysqLi($server,$user,$pass,$database);
    if ($conn){
    mysqLi_query($conn, "SET NAMES 'utf8' ");
     //echo 'connected successfully <br>';
    }else{
        echo 'connected failed';
    }
?>