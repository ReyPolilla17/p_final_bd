<?php
    include './php/config.php';

    $user = implode("\'", explode("'", implode("\\\\", explode("\\", $_POST['username']))));
    $pass = implode("\'", explode("'", implode("\\\\", explode("\\", $_POST['password']))));

    $origin = $_POST["origin"];

    $link = mysqli_connect($cfgServer['host'], $cfgServer['user'], $cfgServer['password']);
        mysqli_select_db($link, $cfgServer['dbname']);
    
    $query = "SELECT * FROM b_cuentas WHERE usuario = '$user' AND contrasena = '$pass' LIMIT 1";

    $result = mysqli_query($link, $query);

    if($line = mysqli_fetch_assoc($result)) {
        if($line['admin_p']) {
            print("<ul><li>Admin</li><li>$user</li><li>$pass</li></ul>");
            // cargar template de admin
        }
        else {
            print("<ul><li>User</li><li>$user</li><li>$pass</li></ul>");
            // cargar template de usuario
        }
    } else {
        print("<h1>Como llegaste hasta aqui?</h1>");
    }

?>