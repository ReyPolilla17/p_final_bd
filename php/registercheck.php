<?php
    include "config.php";

    $user = implode("\'", explode("'", implode("\\\\", explode("\\", $_POST['username']))));
    $pass = implode("\'", explode("'", implode("\\\\", explode("\\", $_POST['password']))));
    $date = $_POST['date'];

    $link = mysqli_connect($cfgServer['host'], $cfgServer['user'], $cfgServer['password']);
        mysqli_select_db($link, $cfgServer['dbname']);
    
    $query = "SELECT * FROM b_cuentas WHERE usuario = '$user' LIMIT 1";

    $result = mysqli_query($link, $query);

    if($line = mysqli_fetch_assoc($result)) {
        print('0');
    } else {
        $reg_query = "INSERT INTO b_cuentas (usuario, contrasena, nacimiento, creacion) VALUES ('$user', '$pass', '$date', CURDATE())";
        mysqli_query($link, $reg_query);
        
        print('1');
    }
?>