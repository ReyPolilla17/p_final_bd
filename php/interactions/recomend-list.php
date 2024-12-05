<?php
    include '../config.php';

    // trata todas las cadenas recibidas mediante el post
    $user = implode("\'", explode("'", implode("\\\\", explode("\\", $_POST['username']))));
    $pass = implode("\'", explode("'", implode("\\\\", explode("\\", $_POST['password']))));
    $list_id = implode("\'", explode("'", implode("\\\\", explode("\\", $_POST['list_id']))));
    $friend_id = implode("\'", explode("'", implode("\\\\", explode("\\", $_POST['friend_id']))));

    // se conecta a la base de datos
    $link = mysqli_connect($cfgServer['host'], $cfgServer['user'], $cfgServer['password']);
        mysqli_select_db($link, $cfgServer['dbname']);

    // Realiza un query vara verificar que el usuario exista
    $query_users = "SELECT * FROM b_cuentas WHERE usuario = '$user' AND contrasena = '$pass' LIMIT 1";
    $result_users = mysqli_query($link, $query_users);

    if($line_users = mysqli_fetch_assoc($result_users)) { // Si el usuario existe
        $this_user_id = $line_users['id_cuenta'];

        $query_recomend = "SELECT * FROM b_recomendaciones WHERE id_destino = $friend_id AND id_lista = $list_id";
        $result_recomend = mysqli_query($link, $query_recomend);

        if($line_recomend = mysqli_fetch_assoc($result_recomend)) {
            mysqli_free_result($result_friend_request);
        } else {
            $query_accept = "INSERT INTO b_recomendaciones (id_origen, id_destino, id_lista) VALUES ($this_user_id, $friend_id, $list_id)";
            mysqli_query($link, $query_accept);
        }
        
        mysqli_free_result($result_users);
    }

    print("lists");
    
    // cierra la conexión a la base de datos
    @mysqli_close($link);
?>