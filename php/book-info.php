<?php
    require_once "HTML/Template/ITX.php";
    include './config.php';

    // trata todas las cadenas recibidas mediante el post
    $user = implode("\'", explode("'", implode("\\\\", explode("\\", $_POST['username']))));
    $pass = implode("\'", explode("'", implode("\\\\", explode("\\", $_POST['password']))));
    $id = implode("\'", explode("'", implode("\\\\", explode("\\", $_POST['id']))));
    $origin = implode("\'", explode("'", implode("\\\\", explode("\\", $_POST['origin']))));

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
        // carga la plantilla del libro

        $query_book = "SELECT * FROM v_info_libro WHERE id_libro = $id LIMIT 1";
        $result_book = mysqli_query($link, $query_book);
        
        if($line_users['admin_p']) { // si el usuario es administrador
            $template->loadTemplatefile("./admin/admin-book-info.html", true, true);
            $template->touchBLock("A");

            if($line_book = mysqli_fetch_assoc($result_book)) {
                $template->setVariable("TITLE_V", $line_book['libro']);
                $template->setVariable("EDITORIAL_V", $line_book['editorial']);
                $template->setVariable("RESUME_V", $line_book['sinopsis']);
                $template->setVariable("STOCK_V", $line_book['disponibles']);
                $template->setVariable("BOOK_ID", $line_book['id_libro']);
                
                mysqli_free_result($result_books); // libera memoria
            } else {
                print("No hay nada"); // missing template
            }

        } else {
            // carga el archivo que contriene el formato para presentar libros
            $template->loadTemplatefile("book-info.html", true, true);

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

                    $template->touchBlock("DISABLED_BUTTON");
                } else if($available == 1) {
                    $available = "$available disponible";

                    $template->setCurrentBlock("ENABLED_BUTTON");
                    $template->setVariable("ID_R", $book_id);
                    $template->parseCurrentBlock();
                } else {
                    $available = "$available disponibles";

                    $template->setCurrentBlock("ENABLED_BUTTON");
                    $template->setVariable("ID_R", $book_id);
                    $template->parseCurrentBlock();
                }
                
                $template->setCurrentBlock();
                // coloca toda la información del libro
                $template->setVariable("ORIGIN", "$origin");
                $template->setVariable("ORIGIN_1", "$origin");
                $template->setVariable("ORIGIN_2", "$origin");
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

                $query_rating = "SELECT * FROM b_calificaciones WHERE id_cuenta = $this_user_id AND id_libro = $book_id";
                $result_rating = mysqli_query($link, $query_rating);

                if($line_rating = mysqli_fetch_assoc($result_rating)) {
                    $template->setCurrentBlock("ALREADY_GRADED");

                    $template->setVariable("USER_GRADE", $line_rating['rating']);

                    $template->parseCurrentBlock();
                    mysqli_free_result($result_rating);
                } else {
                    $template->setCurrentBlock("NOT_GRADED");
                    $template->setVariable("ID_G", $book_id);
                    $template->setVariable("ORIGIN_G", $origin);
                    $template->parseCurrentBlock();
                }

                $template->setCurrentBlock("FRIENDS");

                $query_friends = "SELECT id_cuenta, usuario FROM b_cuentas WHERE (id_cuenta IN (SELECT id_cuenta FROM b_usuario_usuario WHERE id_amigo = $this_user_id) OR id_cuenta IN (SELECT id_amigo FROM b_usuario_usuario WHERE id_cuenta = $this_user_id)) AND id_cuenta != $this_user_id";
                $result_friends = mysqli_query($link, $query_friends);

                $i = 0;

                while($line_friends = mysqli_fetch_assoc($result_friends)) {
                    $friend_id = $line_friends['id_cuenta'];

                    $query_already_recomended = "SELECT * FROM b_recomendaciones WHERE id_libro = $book_id AND id_destino = $friend_id";
                    $result_already_recomended = mysqli_query($link, $query_already_recomended);

                    if($line_already_recomended = mysqli_fetch_assoc($result_already_recomended)) {
                        mysqli_free_result($result_already_recomended);
                    } else {
                        $template->setCurrentBlock("FRIENDS");

                        $template->setVariable("FRIEND_ID", $friend_id);
                        $template->setVariable("FRIEND_NAME", $line_friends['usuario']);

                        $template->parseCurrentBlock();
                    }

                    $i++;
                }

                if($i) {
                    mysqli_free_result($result_friends);
                }
                
                $template->setCurrentBlock("LISTS");

                $query_lists = "SELECT * FROM b_listas WHERE id_cuenta = $this_user_id";
                $result_lists = mysqli_query($link, $query_lists);

                $i = 0;

                while($line_lists = mysqli_fetch_assoc($result_lists)) {
                    $list_id = $line_lists['id_lista'];

                    $query_already_saved = "SELECT * FROM b_listas_libros WHERE id_libro = $book_id AND id_lista = $list_id";
                    $result_already_saved = mysqli_query($link, $query_already_saved);

                    if($line_already_saved = mysqli_fetch_assoc($result_already_saved)) {
                        mysqli_free_result($result_already_saved);
                    } else {
                        $template->setCurrentBlock("LISTS");

                        $template->setVariable("LIST_ID", $list_id);
                        $template->setVariable("LIST_NAME", $line_lists['nombre']);

                        $template->parseCurrentBlock();
                    }

                    $i++;
                }

                if($i) {
                    mysqli_free_result($result_friends);
                }

                $template->parseCurrentBlock();

                mysqli_free_result($result_books); // libera memoria
            } else {
                print("No hay nada"); // missing template
            }
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