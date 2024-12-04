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
        // carga la plantilla de libros
        $template->loadTemplatefile("books.html", true, true);
        
        if($line_users['admin_p']) { // si el usuario es administrador
            $template->setVariable("BOOKS_SECTION", "Gestión de Libros"); // cambia el titulo de la sección
            
            // agrega la opción de agregar libros
            $template->addBlockfile("ADMIN_ADD", "ADMIN_SECTION", "./admin/add-book.html");
            $template->touchBlock("ADMIN_SECTION");
        } else {
            $template->setVariable("BOOKS_SECTION", "Busca Libros"); // cambia el título de la sección
        }
        
        // carga el archivo que contriene el formato para presentar libros
        $template->addBlockfile("COLLECTION", "BOOK_COLLECTION", "./collections/book-collection.html");

        $i = 0;

        // obtiene la información de los libros en la base de datos
        $query_books = "SELECT * FROM v_libros_general";
        $result_books = mysqli_query($link, $query_books);

        // para cada resultado del query
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
            $template->setVariable("ORIGIN", 'books');
            $template->setVariable("ID", $book_id);
            $template->setVariable("IMAGE", $line_books['imagen']);
            $template->setVariable("TITLE", $line_books['libro']);
            $template->setVariable("EDITORIAL", $line_books['editorial']);
            $template->setVariable("AVAILABLE", $available);
            $template->setVariable("RATING", "$rating");
            $template->setVariable("SUMMARY", $line_books['resumen']);

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

        // si no hay libros en la base de datos
        if(!$i) {
            $template->setCurrentBlock("EMPTY");
            $template->setVariable("BOOKS_EMPTY", "No hay libros en la base de datos.");
            $template->parseCurrentBlock();
        } else {
            mysqli_free_result($result_books); // libera memoria
        }
        
        mysqli_free_result($result_users); // libera memoria
    } else {
        $template->loadTemplatefile("backrooms.html", true, true);
        $template->touchBlock("PAGE");
    }
    
    // muestra la pantalla
    $template->show();

    // cierra la conexión a la base de datos
    @mysqli_close($link);
?>