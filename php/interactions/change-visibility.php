<?php
    include '../config.php';

    // trata todas las cadenas recibidas mediante el post
    $user = implode("\'", explode("'", implode("\\\\", explode("\\", $_POST['username']))));
    $pass = implode("\'", explode("'", implode("\\\\", explode("\\", $_POST['password']))));
    $list_id = implode("\'", explode("'", implode("\\\\", explode("\\", $_POST['list_id']))));
    $visibility = implode("\'", explode("'", implode("\\\\", explode("\\", $_POST['visibility']))));

    // se conecta a la base de datos
    $link = mysqli_connect($cfgServer['host'], $cfgServer['user'], $cfgServer['password']);
        mysqli_select_db($link, $cfgServer['dbname']);

    // Realiza un query vara verificar que el usuario exista
    $query_users = "SELECT * FROM b_cuentas WHERE usuario = '$user' AND contrasena = '$pass' LIMIT 1";
    $result_users = mysqli_query($link, $query_users);

    if($line_users = mysqli_fetch_assoc($result_users)) { // Si el usuario existe
        $user_id = $line_users['id_cuenta'];
        $query_lists = "SELECT * FROM b_listas WHERE id_lista = $list_id AND id_cuenta = $user_id LIMIT 1";
        $result_lists = mysqli_query($link, $query_lists);

        if($line_lists = mysqli_fetch_assoc($result_lists)) {
            $query_vis = "UPDATE b_listas SET privada = $visibility WHERE id_lista = $list_id";
            mysqli_query($link, $query_vis);

            mysqli_free_result($result_lists);
        }
        
        mysqli_free_result($result_users);
    }

    print("lists");

    // cierra la conexión a la base de datos
    @mysqli_close($link);
?>