<?php
    include '../config.php';

    // trata todas las cadenas recibidas mediante el post
    $user = implode("\'", explode("'", implode("\\\\", explode("\\", $_POST['username']))));
    $pass = implode("\'", explode("'", implode("\\\\", explode("\\", $_POST['password']))));
    $loan_id = implode("\'", explode("'", implode("\\\\", explode("\\", $_POST['loan_id']))));

    // se conecta a la base de datos
    $link = mysqli_connect($cfgServer['host'], $cfgServer['user'], $cfgServer['password']);
        mysqli_select_db($link, $cfgServer['dbname']);

    // Realiza un query vara verificar que el usuario exista
    $query_users = "SELECT * FROM b_cuentas WHERE usuario = '$user' AND contrasena = '$pass' LIMIT 1";
    $result_users = mysqli_query($link, $query_users);

    if($line_users = mysqli_fetch_assoc($result_users)) { // Si el usuario existe
        $query_loans = "SELECT * FROM b_reservaciones WHERE id_reservacion = $loan_id LIMIT 1";
        $result_loans = mysqli_query($link, $query_loans);

        if($line_loans = mysqli_fetch_assoc($result_loans)) {
            $query_return = "UPDATE b_reservaciones SET fecha_devolucion = CURDATE() WHERE id_reservacion = $loan_id";
            mysqli_query($link, $query_return);

            mysqli_free_result($result_loans);
        }
        
        mysqli_free_result($result_users);
    }

    // cierra la conexión a la base de datos
    @mysqli_close($link);
?>