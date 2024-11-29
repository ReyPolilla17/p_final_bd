<?php
    require_once "HTML/Template/ITX.php";
    include './config.php';

    $user = implode("\'", explode("'", implode("\\\\", explode("\\", $_POST['username']))));
    $pass = implode("\'", explode("'", implode("\\\\", explode("\\", $_POST['password']))));

    $link = mysqli_connect($cfgServer['host'], $cfgServer['user'], $cfgServer['password']);
        mysqli_select_db($link, $cfgServer['dbname']);
            
    $template = new HTML_Template_ITX('../templates');
    
    $query_users = "SELECT * FROM b_cuentas WHERE usuario = '$user' AND contrasena = '$pass' LIMIT 1";
    
    $result_users = mysqli_query($link, $query_users);
    
    if($line_users = mysqli_fetch_assoc($result_users)) {
        $template->loadTemplatefile("books.html", true, true);

        if($line_users['admin_p']) {
            $template->setVariable("BOOKS_SECTION", "Gestión de Libros");
            $template->addBlockfile("ADMIN_ADD", "ADMIN_SECTION", "./admin/add-book.html");
            $template->touchBlock("ADMIN_SECTION");
        } else {
            $template->setVariable("BOOKS_SECTION", "Busca Libros");
            // print("AAA");
        }

        $i = 0;

        $query_books = "SELECT * FROM v_libros_general";
        $result_books = mysqli_query($link, $query_books);

        while($line_books = mysqli_fetch_assoc($result_books)) {
            $j = 0;

            $book_id = $line_books['id_libro'];

            $rating = $line_books['rating'];
            $available = $line_books['disponibles'];
            $authors = "Sin autores registrados";

            if($rating) {
                $rating = "Calificación: $rating";
            } else {
                $rating = "Sin Calificaciones";
            }

            if(!$available) {
                $available = "Sin disponibilidad.";
            } else if($available == 1) {
                $available = "$available disponible";
            } else {
                $available = "$available disponibles";
            }

            $template->setCurrentBlock("BOOKS");

            $template->setVariable("IMAGE", $line_books['imagen']);
            $template->setVariable("TITLE", $line_books['libro']);
            $template->setVariable("EDITORIAL", $line_books['editorial']);
            $template->setVariable("AVAILABLE", $available);
            $template->setVariable("RATING", "$rating");
            $template->setVariable("SUMMARY", $line_books['resumen']);

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

            $template->setVariable("AUTHOR", $authors);

            if($j) {
                mysqli_free_result($result_authors);
            }
            
            $j = 0;

            $query_genres = "SELECT * FROM v_libros_generos WHERE id_libro = '$book_id'";
            $result_genres = mysqli_query($link, $query_genres);

            while($line_genres = mysqli_fetch_assoc($result_genres)) {
                $template->setCurrentBlock("GENRES");

                $template->setVariable("GENRE", $line_genres['genero']);

                $template->parseCurrentBlock("GENRES");

                $j++;
            }

            if($j) {
                mysqli_free_result($result_genres);
            }
            
            $template->setCurrentBlock("BOOKS");
            $template->parseCurrentBlock("BOOKS");

            $i++;
        }

        if(!$i) {
            print("No hay nada");
        } else {
            mysqli_free_result($result_books);
        }
        
        mysqli_free_result($result_users);
    } else {
        print("LMAO");
    }
    
    $template->parseCurrentBlock();
    $template->show();

    @mysqli_close($link);

// get all user info from a query
// check if admin, else, return another template
?>