<?php
    include '../config.php';

    // trata todas las cadenas recibidas mediante el post
    $user = implode("\'", explode("'", implode("\\\\", explode("\\", $_POST['username']))));
    $pass = implode("\'", explode("'", implode("\\\\", explode("\\", $_POST['password']))));
    $friend_id = implode("\'", explode("'", implode("\\\\", explode("\\", $_POST['friend_id']))));
    $origin_page = implode("\'", explode("'", implode("\\\\", explode("\\", $_POST['origin_page']))));

    // se conecta a la base de datos
    $link = mysqli_connect($cfgServer['host'], $cfgServer['user'], $cfgServer['password']);
        mysqli_select_db($link, $cfgServer['dbname']);

    // Realiza un query vara verificar que el usuario exista
    $query_users = "SELECT * FROM b_cuentas WHERE usuario = '$user' AND contrasena = '$pass' LIMIT 1";
    $result_users = mysqli_query($link, $query_users);

    if($line_users = mysqli_fetch_assoc($result_users)) { // Si el usuario existe
        $this_user_id = $line_users['id_cuenta'];

        $query_friend_request = "SELECT * FROM b_solicitudes WHERE id_origen = $friend_id AND id_destino = $this_user_id";
        $result_friend_request = mysqli_query($link, $query_friend_request);

        if($line_friend_request = mysqli_fetch_assoc($result_friend_request)) {
            $query_accept = "INSERT INTO b_usuario_usuario (id_cuenta, id_amigo) VALUES ($friend_id, $this_user_id)";
            mysqli_query($link, $query_accept);
            
            mysqli_free_result($result_friend_request);
        }
        
        mysqli_free_result($result_users);
    }

    print("$origin_page/users/$friend_id");
    
    // cierra la conexión a la base de datos
    @mysqli_close($link);
?>