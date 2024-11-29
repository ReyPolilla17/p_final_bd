<?php
    include "config.php";

    // trata todas las cadenas que recibe
    $user = implode("\'", explode("'", implode("\\\\", explode("\\", $_POST['username']))));
    $pass = implode("\'", explode("'", implode("\\\\", explode("\\", $_POST['password']))));
    $date = implode("\'", explode("'", implode("\\\\", explode("\\", $_POST['date']))));

    // se conecta a la base de datos
    $link = mysqli_connect($cfgServer['host'], $cfgServer['user'], $cfgServer['password']);
        mysqli_select_db($link, $cfgServer['dbname']);
    
    // verifica si el usuario existe
    $query = "SELECT * FROM b_cuentas WHERE usuario = '$user' LIMIT 1";
    $result = mysqli_query($link, $query);

    // si el usuario existe, no permite crear la cuenta y regresa error
    if($line = mysqli_fetch_assoc($result)) {
        print('0');
        
        mysqli_free_result($result);
    } else { // de lo contrario, crea el usuario
        $reg_query = "INSERT INTO b_cuentas (usuario, contrasena, nacimiento, creacion) VALUES ('$user', '$pass', '$date', CURDATE())";
        mysqli_query($link, $reg_query);
        
        print('1');
    }

    // cierra la conexion con la base de datos
    @mysqli_close($link);
?>