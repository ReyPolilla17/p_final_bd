<?php
    require_once "HTML/Template/ITX.php";
    include './config.php';

    // trata todas las cadenas recibidas mediante el post
    $user = implode("\'", explode("'", implode("\\\\", explode("\\", $_POST['username']))));
    $pass = implode("\'", explode("'", implode("\\\\", explode("\\", $_POST['password']))));
    $id = implode("\'", explode("'", implode("\\\\", explode("\\", $_POST['id']))));
    $origin = implode("\'", explode("'", implode("\\\\", explode("\\", $_POST['origin']))));
    $user_origin = implode("\'", explode("'", implode("\\\\", explode("\\", $_POST['user_origin']))));

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

        $query_list = "SELECT * FROM b_listas WHERE id_lista = $id AND (id_cuenta = $this_user_id OR privada = 0) LIMIT 1";
        $result_list = mysqli_query($link, $query_list);

        if($line_list = mysqli_fetch_assoc($result_list)) {
            $list_id = $line_list['id_lista'];

            $template->loadTemplatefile("list-info.html", true, true);
            $template->setVariable("SECTION_ORIGIN", $origin);
            $template->setVariable("USER_ORIGIN", $user_origin);
            $template->setVariable("LIST_NAME", $line_list['nombre']);

            $query_books = "SELECT * FROM b_listas_libros LEFT JOIN v_libros_general USING(id_libro) WHERE id_lista = $list_id";
            $result_books = mysqli_query($link, $query_books);

            $i = 0;

            $template->addBlockfile("COLLECTION", "BOOKS_COLLECTION", "./collections/book-collection.html");

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
                $template->setVariable("ORIGIN", $origin);
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

            if($i) {
                mysqli_free_result($result_books); // libera memoria
            } else {
                $template->setCurrentBlock("EMPTY");
                $template->setVariable("BOOKS_EMPTY", "No hay libros en esta lista.");
                $template->parseCurrentBlock();
            }
            
        } else {
            print("Lista inexistente..."); // missing template
        }

    //     if($line_user = mysqli_fetch_assoc($result_user)) {
    //         $user_id = $line_user['id_cuenta'];

    //         $query_lists = "SELECT * FROM v_listas_general WHERE id_cuenta = $user_id";

    //         if($line_users['admin_p']) { // si el usuario es administrador
    //             $template->loadTemplatefile("my-user.html", true, true);
    //         } else {
    //             $query_lists = "$query_lists AND privada = 0";
                
    //             $template->loadTemplatefile("user-info.html", true, true);
    //         }

    //         $query_friends = "SELECT COUNT(*) AS amigos FROM b_usuario_usuario WHERE id_cuenta = $user_id OR id_amigo = $user_id";
    //         $result_friends = mysqli_query($link, $query_friends);
            
    //         $line_friends = mysqli_fetch_assoc($result_friends);
    //         $friends = $line_friends['amigos'];

    //         mysqli_free_result($result_friends); // libera memoria

    //         if(!$friends) {
    //             $friends = "Sin amigos";
    //         } else if($friends == 1) {
    //             $friends = "$friends amigo";
    //         } else {
    //             $friends = "$friends amigos";
    //         }

    //         $query_are_friends = "SELECT * FROM b_usuario_usuario WHERE (id_cuenta = $user_id OR id_amigo = $user_id) AND (id_cuenta = $this_user_id OR id_amigo = $this_user_id)";
    //         $result_are_friends = mysqli_query($link, $query_are_friends);
            
    //         $query_request_sent = "SELECT * FROM b_solicitudes WHERE id_origen = $this_user_id AND id_destino = $user_id";
    //         $result_request_sent = mysqli_query($link, $query_request_sent);
            
    //         $query_request_recieved = "SELECT * FROM b_solicitudes WHERE id_origen = $user_id AND id_destino = $this_user_id";
    //         $result_request_recieved = mysqli_query($link, $query_request_recieved);
            
    //         if($line_are_friends = mysqli_fetch_assoc($result_are_friends)) {
    //             $template->setVariable("ALTER_FRIENDSHIP", "Terminar Amistad");
    //             $template->setVariable("FUNCTION", "removeFriend($user_id, '$origin', false)");
                
    //             $friend_since = $line_are_friends['fecha_inicio'];
    //             $friend_since = "Amigos desde $friend_since";
                
    //             $template->setVariable("FRIEND", $friend_since);
                
    //             mysqli_free_result($result_are_friends); // libera memoria
    //         } else if($line_request_sent = mysqli_fetch_assoc($result_request_sent)) {
    //             $template->setVariable("ALTER_FRIENDSHIP", "Solicitud Enviada");
    //             $template->setVariable("DISABLED", "disabled");
                
    //             mysqli_free_result($result_request_sent); // libera memoria
    //         } else if($line_request_recieved = mysqli_fetch_assoc($result_request_recieved)) {
    //             $template->setVariable("ALTER_FRIENDSHIP", "Aceptar Solicitud");
    //             $template->setVariable("FUNCTION", "acceptFriendrequest($user_id, '$origin', false)");
                
    //             mysqli_free_result($result_request_recieved); // libera memoria
    //         } else {
    //             $template->setVariable("ALTER_FRIENDSHIP", "Enviar Solicitud");
    //             $template->setVariable("FUNCTION", "sendFriendrequest($user_id, '$origin', false)");
    //         }

    //         $template->setVariable("ORIGIN", $origin);
    //         $template->setVariable("IMAGE", $line_user['imagen']);
    //         $template->setVariable("USER", $line_user['usuario']);
    //         $template->setVariable("JOIN", $line_user['creacion']);
    //         $template->setVariable("BIRTH", $line_user['nacimiento']);
    //         $template->setVariable("FRIEND_COUNT", $friends);

    //         $result_lists = mysqli_query($link, $query_lists);

    //         $i = 0;

    //         while($line_lists = mysqli_fetch_assoc($result_lists)) {
    //             $book_count = $line_lists['libros'];

    //             if(!$i) {
    //                 $template->setCurrentBlock("LISTS");
    //             }

    //             $template->setCurrentBlock("LIST_ELEMENT");

    //             $template->setVariable("ID", $line_lists['id_lista']);
    //             $template->setVariable("NAME", $line_lists['nombre']);

    //             if($book_count) {
    //                 $book_count = "$book_count Libros";
    //             } else {
    //                 $book_count = "Sin libros guardados";
    //             }

    //             $template->setVariable("BOOK_COUNT", $book_count);

    //             $template->parseCurrentBlock();

    //             $i++;
    //         }

    //         if($i) {

    //         } else {

    //             $template->touchBlock("EMPTY_LISTS");
    //         }
            
    //         mysqli_free_result($result_user); // libera memoria
    //     }
        
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