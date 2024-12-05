<?php
    include '../config.php';

    // trata todas las cadenas recibidas mediante el post
    $user = implode("\'", explode("'", implode("\\\\", explode("\\", $_POST['username']))));
    $pass = implode("\'", explode("'", implode("\\\\", explode("\\", $_POST['password']))));
    $book_id = implode("\'", explode("'", implode("\\\\", explode("\\", $_POST['book_id']))));

    // se conecta a la base de datos
    $link = mysqli_connect($cfgServer['host'], $cfgServer['user'], $cfgServer['password']);
        mysqli_select_db($link, $cfgServer['dbname']);

    // Realiza un query vara verificar que el usuario exista
    $query_users = "SELECT * FROM b_cuentas WHERE usuario = '$user' AND contrasena = '$pass' LIMIT 1";
    $result_users = mysqli_query($link, $query_users);

    if($line_users = mysqli_fetch_assoc($result_users)) { // Si el usuario existe
        $this_user_id = $line_users['id_cuenta'];
        
        $query_available = "SELECT * FROM b_inventario WHERE (id_libro, id_copia) NOT IN (SELECT id_libro, id_copia FROM b_reservaciones WHERE fecha_devolucion IS NULL) AND id_libro = $book_id";
        $result_available = mysqli_query($link, $query_available);

        if($line_available = mysqli_fetch_assoc($result_available)) {
            $copy_id = $line_available['id_copia'];

            $query_accept = "INSERT INTO b_reservaciones (fecha_prestamo, id_copia, id_libro, id_cuenta) VALUES (CURDATE(), $copy_id, $book_id, $this_user_id)";
            mysqli_query($link, $query_accept);

            mysqli_free_result($result_available);
        }
        
        mysqli_free_result($result_users);
    }

    print("loans");
    
    // cierra la conexión a la base de datos
    @mysqli_close($link);
?>