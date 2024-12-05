<?php
    require_once "HTML/Template/ITX.php";
    include './config.php';

    // trata todas las cadenas recibidas mediante el post
    $user = implode("\'", explode("'", implode("\\\\", explode("\\", $_POST['username']))));
    $pass = implode("\'", explode("'", implode("\\\\", explode("\\", $_POST['password']))));

    // se conecta a la base de datos
    $link = mysqli_connect($cfgServer['host'], $cfgServer['user'], $cfgServer['password']);
        mysqli_select_db($link, $cfgServer['dbname']);
    
    // carga las plantillas (creo)
    $template = new HTML_Template_ITX('../templates');
    
    // Realiza un query vara verificar que el usuario exista
    $query_users = "SELECT * FROM b_cuentas WHERE usuario = '$user' AND contrasena = '$pass' LIMIT 1";
    $result_users = mysqli_query($link, $query_users);
    
    if($line_users = mysqli_fetch_assoc($result_users)) { // Si el usuario existe
        $this_user_id = $line_users['id_cuenta'];

        $template->loadTemplatefile("recomendations.html", true, true);
        
        $query_books = "SELECT * FROM v_recomendaciones_libros WHERE id_destino = $this_user_id";
        $result_books = mysqli_query($link, $query_books);

        $i = 0;

        while($line_books = mysqli_fetch_assoc($result_books)) {
            $j = 0;

            // información que recibe un tratamiento esecífico
            $book_id = $line_books['id_libro'];

            $rating = $line_books['rating'];
            $available = $line_books['disponibles'];
            $authors = "Sin autores registrados";

            // Como se muestran las calificaciones
            if($rating) {
                $rating = "Calificación: $rating";
            } else {
                $rating = "Sin Calificaciones";
            }

            // como se muestran los libros disponibles
            if(!$available) {
                $available = "Sin disponibilidad.";
            } else if($available == 1) {
                $available = "$available disponible";
            } else {
                $available = "$available disponibles";
            }
            
            $template->setCurrentBlock("BOOKS");

            // coloca toda la información del libro
            $template->setVariable("ID", $book_id);
            $template->setVariable("IMAGE", $line_books['imagen']);
            $template->setVariable("TITLE", $line_books['libro']);

            // busca a los autores del libro
            $query_authors = "SELECT * FROM v_libros_autores WHERE id_libro = '$book_id'";
            $result_authors = mysqli_query($link, $query_authors);

            // coloca a todos los autores en una misma cadena
            while($line_authors = mysqli_fetch_assoc($result_authors)) {
                $author = $line_authors['autor'];

                if(!$j) {
                    $authors = $author;
                } else {
                    $authors = "$authors, $author";
                }

                $j++;
            }
            
            // libera memoria
            if($j) {
                mysqli_free_result($result_authors);
            }

            $j = 0;
            
            // muestra a los autores del libro
            $template->setVariable("AUTHOR", $authors);

            // busca los géneros del libro
            $query_genres = "SELECT * FROM v_libros_generos WHERE id_libro = '$book_id'";
            $result_genres = mysqli_query($link, $query_genres);

            // cada genero lo coloca en su propio bloque
            while($line_genres = mysqli_fetch_assoc($result_genres)) {
                $template->setCurrentBlock("GENRES");

                $template->setVariable("GENRE", $line_genres['genero']);

                $template->parseCurrentBlock();

                $j++;
            }

            // libera memoria
            if($j) {
                mysqli_free_result($result_genres);
            }
            
            // regresa al bloque anterior y lo muestra
            $template->setCurrentBlock("BOOKS");
            $template->parseCurrentBlock();

            $i++;
        }

        if($i) {
            mysqli_free_result($result_books);
        } else {
            $template->touchBlock("EMPTY_BOOKS");
        }
        
        $query_lists = "SELECT * FROM v_recomendaciones_listas WHERE id_destino = $this_user_id";
        $result_lists = mysqli_query($link, $query_lists);

        $i = 0;

        while($line_lists = mysqli_fetch_assoc($result_lists)) {
            $book_count = $line_lists['libros'];

            $template->setCurrentBlock("LISTS");

            $template->setVariable("ID", $line_lists['id_lista']);
            $template->setVariable("NAME", $line_lists['lista']);
            $template->setVariable("CREATOR", $line_lists['usuario']);

            if($book_count) {
                $book_count = "$book_count Libros";
            } else {
                $book_count = "Sin libros guardados";
            }

            $template->setVariable("BOOK_COUNT", $book_count);

            $template->parseCurrentBlock();

            $i++;
        }

        if($i) {
            mysqli_free_result($result_lists);
        } else {
            $template->touchBlock("EMPTY_LISTS");
        }
        

        mysqli_free_result($line_users);
    } else {
        $template->loadTemplatefile("backrooms.html", true, true);
        $template->touchBlock("PAGE");
    }
    
    // muestra la pantalla
    $template->show();

    // cierra la conexión a la base de datos
    @mysqli_close($link);
?>