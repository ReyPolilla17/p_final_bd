<?php
    include '../config.php';

    // trata todas las cadenas recibidas mediante el post
    $user = implode("\'", explode("'", implode("\\\\", explode("\\", $_POST['username']))));
    $pass = implode("\'", explode("'", implode("\\\\", explode("\\", $_POST['password']))));
    $book_id = implode("\'", explode("'", implode("\\\\", explode("\\", $_POST['book_id']))));
    $list_id = implode("\'", explode("'", implode("\\\\", explode("\\", $_POST['list_id']))));
    $origin_page = implode("\'", explode("'", implode("\\\\", explode("\\", $_POST['origin_page']))));

    // se conecta a la base de datos
    $link = mysqli_connect($cfgServer['host'], $cfgServer['user'], $cfgServer['password']);
        mysqli_select_db($link, $cfgServer['dbname']);

    // Realiza un query vara verificar que el usuario exista
    $query_users = "SELECT * FROM b_cuentas WHERE usuario = '$user' AND contrasena = '$pass' LIMIT 1";
    $result_users = mysqli_query($link, $query_users);

    if($line_users = mysqli_fetch_assoc($result_users)) { // Si el usuario existe
        $query_save = "SELECT * FROM b_listas_libros WHERE id_lista = $list_id AND id_libro = $book_id";
        $result_save = mysqli_query($link, $query_save);

        if($line_recomend = mysqli_fetch_assoc($result_save)) {
            mysqli_free_result($result_save);
        } else {
            $query_accept = "INSERT INTO b_listas_libros (id_lista, id_libro) VALUES ($list_id, $book_id)";
            mysqli_query($link, $query_accept);
        }
        
        mysqli_free_result($result_users);
    }

    print("$origin_page/books/$book_id");
    
    // cierra la conexión a la base de datos
    @mysqli_close($link);
?>