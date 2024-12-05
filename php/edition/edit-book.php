<?php
    require_once "HTML/Template/ITX.php";
    include '../config.php';

    // trata todas las cadenas recibidas mediante el post
    $user = implode("\'", explode("'", implode("\\\\", explode("\\", $_POST['username']))));
    $pass = implode("\'", explode("'", implode("\\\\", explode("\\", $_POST['password']))));
    $title = implode("\'", explode("'", implode("\\\\", explode("\\", $_POST['title']))));
    $editorial = implode("\'", explode("'", implode("\\\\", explode("\\", $_POST['editorial']))));
    $resume = implode("\'", explode("'", implode("\\\\", explode("\\", $_POST['resume']))));
    $stock = implode("\'", explode("'", implode("\\\\", explode("\\", $_POST['stock']))));
    $book_id = implode("\'", explode("'", implode("\\\\", explode("\\", $_POST['book_id']))));

    // se conecta a la base de datos
    $link = mysqli_connect($cfgServer['host'], $cfgServer['user'], $cfgServer['password']);
        mysqli_select_db($link, $cfgServer['dbname']);

    // Realiza un query vara verificar que el usuario exista
    $query_users = "SELECT * FROM b_cuentas WHERE usuario = '$user' AND contrasena = '$pass' AND admin_p = 1 LIMIT 1";
    $result_users = mysqli_query($link, $query_users);

    if($line_users = mysqli_fetch_assoc($result_users)) { // Si el usuario existe
        $editorial_id = 0;

        if(!$book_id) {
            $book_id = "NULL";
        }
        
        $query_editorial = "SELECT * FROM b_editoriales WHERE nombre = '$editorial'";
        $result_editorial = mysqli_query($link, $query_editorial);
        
        if($line_editorial = mysqli_fetch_assoc($result_editorial)) {
            $editorial_id = $line_editorial['id_editorial'];
            mysqli_free_result($result_editorial);
        } else {
            $query_create_editorial = "INSERT INTO b_editoriales (nombre) VALUES ('$editorial')";
            mysqli_query($link, $query_create_editorial);

            $query_editorial = "SELECT * FROM b_editoriales WHERE nombre = '$editorial'";
            $result_editorial = mysqli_query($link, $query_editorial);
            $line_editorial = mysqli_fetch_assoc($result_editorial);
            $editorial_id = $line_editorial['id_editorial'];
            mysqli_free_result($result_editorial);
        }
        
        $query_update = "CALL ActualizarOInsertarLibro($book_id, '$title', '$resume', $editorial_id)";
        mysqli_query($link, $query_update);
        
        if($book_id == "NULL") {
            $query_books = "SELECT * FROM b_libros WHERE nombre = '$title'";
            $result_books = mysqli_query($link, $query_books);
            $line_books = mysqli_fetch_assoc($result_books);

            $book_id = $line_books['id_libro'];
            mysqli_free_result($result_books);
        }

        $query_update2 = "CALL ActualizarInventarioLibros($book_id, $stock)";

        mysqli_query($link, $query_update2);
    }

    print("books");
    
    // cierra la conexión a la base de datos
    @mysqli_close($link);
?>