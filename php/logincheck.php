<?php
    include "config.php";

    $user = implode("\'", explode("'", implode("\\\\", explode("\\", $_POST['username']))));
    $pass = implode("\'", explode("'", implode("\\\\", explode("\\", $_POST['password']))));

    $link = mysqli_connect($cfgServer['host'], $cfgServer['user'], $cfgServer['password']);
        mysqli_select_db($link, $cfgServer['dbname']);
    
    $query = "SELECT * FROM b_cuentas WHERE usuario = '$user' AND contrasena = '$pass' LIMIT 1";

    $result = mysqli_query($link, $query);

    if($line = mysqli_fetch_assoc($result)) {
        print("1");
    } else {
        print('0');
    }
?>