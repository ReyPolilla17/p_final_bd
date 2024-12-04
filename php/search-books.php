<?php
    require_once "HTML/Template/ITX.php";
    include './config.php';

    $user = implode("\'", explode("'", implode("\\\\", explode("\\", $_POST['username']))));
    $pass = implode("\'", explode("'", implode("\\\\", explode("\\", $_POST['password']))));
    $search = implode("\'", explode("'", implode("\\\\", explode("\\", $_POST['search']))));
    $atribute = implode("\'", explode("'", implode("\\\\", explode("\\", $_POST['atribute']))));

    $link = mysqli_connect($cfgServer['host'], $cfgServer['user'], $cfgServer['password']);
        mysqli_select_db($link, $cfgServer['dbname']);

    $template = new HTML_Template_ITX('../templates');
    
    $query_users = "SELECT * FROM b_cuentas WHERE usuario = '$user' AND contrasena = '$pass' LIMIT 1";
    $result_users = mysqli_query($link, $query_users);
    
    if($line_users = mysqli_fetch_assoc($result_users)) {
        $template->loadTemplatefile("./collections/book-collection.html", true, true);

        $search_query = "SELECT * FROM v_libros_general";

        switch($atribute) {
            case '1':
                $search_query = "SELECT * FROM v_libros_general WHERE libro LIKE '%$search%'";
                break;
            case '2':
                $search_query = "SELECT * FROM v_libros_general WHERE id_libro IN (SELECT DISTINCT id_libro FROM b_libros_autores la LEFT JOIN b_autores a USING(id_autor) WHERE a.nombre LIKE '%$search%')";
                break;
            case '3':
                $search_query = "SELECT * FROM v_libros_general WHERE id_libro IN (SELECT DISTINCT id_libro FROM b_libros_generos lg LEFT JOIN b_generos g USING(id_genero) WHERE g.nombre LIKE '%$search%')";
                break;
            case '4':
                $search_query = "SELECT * FROM v_libros_general WHERE editorial LIKE '%$search%'";
                break;
            case '5':
                $search_query = "SELECT * FROM v_libros_general WHERE id_libro LIKE '%$search%'";
                break;
        }

        $search_result = mysqli_query($link, $search_query);

        $i = 0;

        while($line_result = mysqli_fetch_assoc($search_result)) {
            $j = 0;

            // información que recibe un tratamiento esecífico
            $book_id = $line_result['id_libro'];

            $rating = $line_result['rating'];
            $available = $line_result['disponibles'];
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

            // $template->addBlockfile("COLLECTION", "BOOK_COLLECTION", "./collections/book-collection.html");
            $template->setCurrentBlock("BOOKS");

            $template->setVariable("ID", $book_id);
            $template->setVariable("IMAGE", $line_result['imagen']);
            $template->setVariable("TITLE", $line_result['libro']);
            $template->setVariable("EDITORIAL", $line_result['editorial']);
            $template->setVariable("AVAILABLE", $available);
            $template->setVariable("RATING", "$rating");
            $template->setVariable("SUMMARY", $line_result['resumen']);

            $query_authors = "SELECT * FROM v_libros_autores WHERE id_libro = '$book_id'";
            $result_authors = mysqli_query($link, $query_authors);

            while($line_authors = mysqli_fetch_assoc($result_authors)) {
                $author = $line_authors['autor'];

                if(!$j) {
                    $authors = $author;
                } else {
                    $authors = "$authors, $author";
                }

                $j++;
            }
            
            if($j) {
                mysqli_free_result($result_authors);
            }

            $j = 0;
            
            $template->setVariable("AUTHOR", $authors);

            $query_genres = "SELECT * FROM v_libros_generos WHERE id_libro = '$book_id'";
            $result_genres = mysqli_query($link, $query_genres);

            while($line_genres = mysqli_fetch_assoc($result_genres)) {
                $template->setCurrentBlock("GENRES");

                $template->setVariable("GENRE", $line_genres['genero']);

                $template->parseCurrentBlock();

                $j++;
            }

            if($j) {
                mysqli_free_result($result_genres);
            }
            
            $template->setCurrentBlock("BOOKS");
            $template->parseCurrentBlock();

            $i++;
        }

        if($i) {
            mysqli_free_result($search_result);
        } else {
            $template->setCurrentBlock("EMPTY");
            $template->setVariable("BOOKS_EMPTY", "No se encontraron libros.");
            $template->parseCurrentBlock();
        }

        mysqli_free_result($result_users);
    } else {
        $template->loadTemplatefile("backrooms.html", true, true);
        $template->touchBlock("PAGE");
    }

    $template->show();
    @mysqli_close($link);
?>