<?php
    require_once "HTML/Template/ITX.php";
    include './config.php';

    // trata todas las cadenas recibidas mediante el post
    $user = implode("\'", explode("'", implode("\\\\", explode("\\", $_POST['username']))));
    $pass = implode("\'", explode("'", implode("\\\\", explode("\\", $_POST['password']))));
    $id = implode("\'", explode("'", implode("\\\\", explode("\\", $_POST['id']))));

    // se conecta a la base de datos
    $link = mysqli_connect($cfgServer['host'], $cfgServer['user'], $cfgServer['password']);
        mysqli_select_db($link, $cfgServer['dbname']);
    
    // carga las plantillas (creo)
    $template = new HTML_Template_ITX('../templates');
    
    // Realiza un query vara verificar que el usuario exista
    $query_users = "SELECT * FROM b_cuentas WHERE usuario = '$user' AND contrasena = '$pass' LIMIT 1";
    $result_users = mysqli_query($link, $query_users);

    if($line_users = mysqli_fetch_assoc($result_users)) { // Si el usuario existe
        // carga la plantilla del libro
        $template->loadTemplatefile("book-info.html", true, true);
        
        if($line_users['admin_p']) { // si el usuario es administrador
            // $template->setVariable("BOOKS_SECTION", "Gestión de Libros"); // cambia el titulo de la sección
            
            // // agrega la opción de agregar libros
            // $template->addBlockfile("ADMIN_ADD", "ADMIN_SECTION", "./admin/add-book.html");
            // $template->touchBlock("ADMIN_SECTION");
            // Opciones de administrador!!!
        } else {
            // $template->setVariable("BOOKS_SECTION", "Busca Libros"); // cambia el título de la sección
            // Opciones de usuario!!!
        }
        
        // carga el archivo que contriene el formato para presentar libros

        // obtiene la información de los libros en la base de datos
        $query_book = "SELECT * FROM v_info_libro WHERE id_libro = $id LIMIT 1";
        $result_book = mysqli_query($link, $query_book);

        if($line_book = mysqli_fetch_assoc($result_book)) {
            $j = 0;

            // información que recibe un tratamiento esecífico
            $book_id = $line_book['id_libro'];

            $rating = $line_book['rating'];
            $available = $line_book['disponibles'];
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
            
            // coloca toda la información del libro
            $template->setVariable("ID", $book_id);
            $template->setVariable("IMAGE", $line_book['imagen']);
            $template->setVariable("TITLE", $line_book['libro']);
            $template->setVariable("EDITORIAL", $line_book['editorial']);
            $template->setVariable("AVAILABLE", $available);
            $template->setVariable("RATING", "$rating");
            $template->setVariable("SUMMARY", $line_book['sinopsis']);

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

            $template->parseCurrentBlock();

            mysqli_free_result($result_books); // libera memoria
        } else {
            print("No hay nada"); // missing template
        }

        mysqli_free_result($result_users); // libera memoria
    } else {
        print("User verification error..."); // missing template
    }
    
    // muestra la pantalla
    $template->show();

    // cierra la conexión a la base de datos
    @mysqli_close($link);
?>