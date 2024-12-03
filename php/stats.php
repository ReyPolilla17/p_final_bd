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
    $query_user = "SELECT * FROM b_cuentas WHERE usuario = '$user' AND contrasena = '$pass' LIMIT 1";
    $result_user = mysqli_query($link, $query_user);

    if($line_user = mysqli_fetch_assoc($result_user)) { // Si el usuario existe
        $this_user_id = $line_user['id_cuenta'];

        if($line_user['admin_p']) {
            $template->loadTemplatefile("stats.html", true, true);

            // Mes con más préstamos
            $query_month = "SELECT MONTH(fecha_prestamo) AS mes, COUNT(*) AS prestamos FROM b_reservaciones GROUP BY Mes ORDER BY Prestamos DESC LIMIT 1";
            $result_month = mysqli_query($link, $query_month);

            if($line_month = mysqli_fetch_assoc($result_month)) {
                $month_name = "";

                if($line_month['prestamos']) {
                    $month_loans = $line_month['prestamos'];
                    $month_loans = " ($month_loans)";
                    $template->setVariable("MONTH_LOANS", $month_loans);
                }

                switch($line_month['mes']) {
                    case 1:
                        $month_name = "Enero";
                        break;
                    case 2:
                        $month_name = "Febrero";
                        break;
                    case 3:
                        $month_name = "Marzo";
                        break;
                    case 4:
                        $month_name = "Abril";
                        break;
                    case 5:
                        $month_name = "Mayo";
                        break;
                    case 6:
                        $month_name = "Junio";
                        break;
                    case 7:
                        $month_name = "Julio";
                        break;
                    case 8:
                        $month_name = "Agosto";
                        break;
                    case 9:
                        $month_name = "Septiembre";
                        break;
                    case 10:
                        $month_name = "Octubre";
                        break;
                    case 11:
                        $month_name = "Noviembre";
                        break;
                    case 12:
                        $month_name = "Diciembre";
                        break;
                }

                $template->setVariable("MONTH", $month_name);

                mysqli_free_result($result_month); // libera memoria
            } else {
                $template->setVariable("MONTH", "Sin registro.");
            }

            // Cantidad de libros disponibles
            $query_available = "SELECT COUNT(*) AS disponibles FROM b_inventario i LEFT JOIN b_reservaciones r USING(id_libro, id_copia) WHERE (r.fecha_prestamo IS NULL AND r.fecha_devolucion IS NULL) OR (r.fecha_prestamo IS NOT NULL AND r.fecha_devolucion IS NOT NULL)";
            $result_available = mysqli_query($link, $query_available);

            if($line_available = mysqli_fetch_assoc($result_available)) {
                $template->setVariable("TOTAL_AVAILABLE", $line_available['disponibles']);

                mysqli_free_result($result_available); // libera memoria
            } else {
                $template->setVariable("TOTAL_AVAILABLE", "Sin registro.");
            }
            
            // Cantidad de libros prestados
            $query_loaned = "SELECT COUNT(*) AS prestados FROM b_reservaciones WHERE fecha_devolucion IS NULL";
            $result_loaned = mysqli_query($link, $query_loaned);

            if($line_loaned = mysqli_fetch_assoc($result_loaned)) {
                $template->setVariable("TOTAL_LOANED", $line_loaned['prestados']);

                mysqli_free_result($result_loaned); // libera memoria
            } else {
                $template->setVariable("TOTAL_LOANED", "Sin registro.");
            }

            // Libros más solicitados
            $template->setCurrentBlock();

            $template->setCurrentBlock("LOANED");

            $template->addBlockfile("L_COLLECTION", "LOANED_COLLECTION", "./collections/book-collection.html");
            $template->setCurrentBlock("LOANED_COLLECTION");

            $query_most_loaned = "SELECT * FROM v_libros_general WHERE id_libro IN (SELECT * FROM v_mas_prestados)";
            $result_most_loaned = mysqli_query($link, $query_most_loaned);

            $i = 0;

            while($line_most_loaned = mysqli_fetch_assoc($result_most_loaned)) {
                $j = 0;

                // información que recibe un tratamiento esecífico
                $book_id = $line_most_loaned['id_libro'];

                $rating = $line_most_loaned['rating'];
                $available = $line_most_loaned['disponibles'];
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
                $template->setVariable("ORIGIN", 'stats');
                $template->setVariable("ID", $book_id);
                $template->setVariable("IMAGE", $line_most_loaned['imagen']);
                $template->setVariable("TITLE", $line_most_loaned['libro']);
                $template->setVariable("EDITORIAL", $line_most_loaned['editorial']);
                $template->setVariable("AVAILABLE", $available);
                $template->setVariable("RATING", "$rating");
                $template->setVariable("SUMMARY", $line_most_loaned['resumen']);

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
            
            $template->setCurrentBlock("LOANED");
            $template->parseCurrentBlock();

            if($i) {
                mysqli_free_result($result_most_loaned); // libera memoria
            } else {
                $template->setCurrentBlock("EMPTY_LOANED");
                $template->setVariable("LOANED_EMPTY", "Sin registros.");
                $template->parseCurrentBlock();
            }

            // libros más populares
            $template->setCurrentBlock("POPULAR");

            $template->addBlockfile("P_COLLECTION", "POPULAR_COLLECTION", "./collections/book-collection-b.html");
            $template->setCurrentBlock("POPULAR_COLLECTION");

            $query_most_popular = "SELECT * FROM v_libros_general WHERE id_libro IN (SELECT * FROM v_mas_populares)";
            $result_most_popular = mysqli_query($link, $query_most_popular);

            $i = 0;

            while($line_most_popular = mysqli_fetch_assoc($result_most_popular)) {
                $j = 0;

                // información que recibe un tratamiento esecífico
                $book_id = $line_most_popular['id_libro'];

                $rating = $line_most_popular['rating'];
                $available = $line_most_popular['disponibles'];
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
                
                $template->setCurrentBlock("BOOKS_B");

                // coloca toda la información del libro
                $template->setVariable("ORIGIN_B", 'stats');
                $template->setVariable("ID_B", $book_id);
                $template->setVariable("IMAGE_B", $line_most_popular['imagen']);
                $template->setVariable("TITLE_B", $line_most_popular['libro']);
                $template->setVariable("EDITORIAL_B", $line_most_popular['editorial']);
                $template->setVariable("AVAILABLE_B", $available);
                $template->setVariable("RATING_B", "$rating");
                $template->setVariable("SUMMARY_B", $line_most_popular['resumen']);

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
                $template->setVariable("AUTHOR_B", $authors);

                // busca los géneros del libro
                $query_genres = "SELECT * FROM v_libros_generos WHERE id_libro = '$book_id'";
                $result_genres = mysqli_query($link, $query_genres);

                // cada genero lo coloca en su propio bloque
                while($line_genres = mysqli_fetch_assoc($result_genres)) {
                    $template->setCurrentBlock("GENRES_B");

                    $template->setVariable("GENRE_B", $line_genres['genero']);

                    $template->parseCurrentBlock();

                    $j++;
                }

                // libera memoria
                if($j) {
                    mysqli_free_result($result_genres);
                }
                
                // regresa al bloque anterior y lo muestra
                $template->setCurrentBlock("BOOKS_B");
                $template->parseCurrentBlock();
                
                $i++;
            }

            $template->setCurrentBlock("POPULAR");
            $template->parseCurrentBlock();
            $template->setCurrentBlock();

            if($i) {
                mysqli_free_result($result_most_popular); // libera memoria
            } else {
                $template->setCurrentBlock("EMPTY_POPULAR");
                $template->setVariable("POPULAR_EMPTY", "Sin registros.");
                $template->parseCurrentBlock();
            }

            // Autores más populares
            $query_popular_authors = "SELECT a.id_autor, a.nombre, COUNT(*) reservaciones FROM b_libros_autores la LEFT JOIN b_libros l USING(id_libro) LEFT JOIN b_autores a USING(id_autor) RIGHT JOIN b_reservaciones r USING(id_libro) GROUP BY id_autor ORDER BY reservaciones DESC LIMIT 3";
            $result_popular_authors = mysqli_query($link, $query_popular_authors);

            $i = 0;

            while($line_popular_authors = mysqli_fetch_assoc($result_popular_authors)) {
                $template->setCurrentBlock("A_NAMES");
                $template->setVariable("A_NAME", $line_popular_authors['nombre']);
                $template->parseCurrentBlock();
                
                $i++;
            }

            if($i) {
                mysqli_free_result($result_popular_authors); // libera memoria
            } else {
                $template->setCurrentBlock("EMPTY_AUTHOR");
                $template->setVariable("AUTHOR_EMPTY", "Sin registros.");
                $template->parseCurrentBlock();
            }

            // Usuawios que más solicitan
            $query_loan_users = "SELECT * FROM b_cuentas WHERE id_cuenta IN (SELECT * FROM v_mas_prestados_usuarios)";
            $result_loan_users = mysqli_query($link, $query_loan_users);

            $template->addBlockfile("U_COLLECTION", "USER_COLLECTION", "./collections/user-collection.html");
            $template->setCurrentBlock("USER_COLLECTION");

            $i = 0;

            // para cada resultado del query
            while($line_loan_users = mysqli_fetch_assoc($result_loan_users)) {
                $user_id = $line_loan_users['id_cuenta'];

                $query_friends = "SELECT COUNT(*) AS amigos FROM b_usuario_usuario WHERE id_cuenta = $user_id OR id_amigo = $user_id";
                $result_friends = mysqli_query($link, $query_friends);

                $line_friends = mysqli_fetch_assoc($result_friends);
                $friends = $line_friends['amigos'];

                mysqli_free_result($result_friends); // libera memoria
                
                if(!$friends) {
                    $friends = "Sin amigos";
                } else if($friends == 1) {
                    $friends = "$friends amigo";
                } else {
                    $friends = "$friends amigos";
                }

                // coloca toda la información del libro
                $template->setVariable("ORIGIN", 'stats');
                $template->setVariable("ID", $user_id);
                $template->setVariable("IMAGE", $line_loan_users['imagen']);
                $template->setVariable("NAME", $line_loan_users['usuario']);
                $template->setVariable("FRIENDS", $friends);
                
                $template->parseCurrentBlock();

                $i++;
            }

            // si no hay usuarios en la base de datos
            if($i) {
                mysqli_free_result($result_loan_users); // libera memoria
            } else {
                $template->setCurrentBlock("EMPTY_USER");
                $template->setVariable("USER_EMPTY", "Sin registros.");
                $template->parseCurrentBlock();
            }
        } else {
            print("No deberias poder ver esto..."); // missing template
        }
        
        mysqli_free_result($result_user); // libera memoria
    } else {
        print("User verification error..."); // missing template
    }
    
    // muestra la pantalla
    $template->show();

    // cierra la conexión a la base de datos
    @mysqli_close($link);
?>