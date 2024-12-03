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
        $template->loadTemplatefile("loans.html", true, true);

        $active_empty = "No se encontraron préstamos activos.";
        $inactive_empty = "No se ha realizado ninguna reservación.";
        
        $query_loans = "SELECT * FROM v_libros_reservaciones";
        
        if(!$line_users['admin_p']) {
            $query_loans = "$query_loans WHERE id_cuenta = $this_user_id";
            
            $active_empty = "No tienes préstamos activos.";
            $inactive_empty = "No has realizado ninguna reservación.";
        }

        $result_loans = mysqli_query($link, $query_loans);

        $i = 0;
        $has_active = 0;
        $has_inactive = 0;

        while($line_loans = mysqli_fetch_assoc($result_loans)) {
            $book_id = $line_loans['id_libro'];
            $user_id = $line_loans['id_cuenta'];

            $authors = "Sin autores registrados";

            // busca a los autores del libro
            $query_authors = "SELECT * FROM v_libros_autores WHERE id_libro = '$book_id'";
            $result_authors = mysqli_query($link, $query_authors);

            $j = 0;
            
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

            if($line_loans['fecha_devolucion']) {
                $has_inactive = 1;
                
                $template->setCurrentBlock("INACTIVE");
                $template->setVariable("R_DATE", $line_loans['fecha_devolucion']);
            } else {
                $has_active = 1;

                $template->setCurrentBlock("ACTIVE");
            }

            if($line_users['admin_p']) {
                $query_user = "SELECT * FROM b_cuentas WHERE id_cuenta = $user_id LIMIT 1";
                $result_user = mysqli_query($link, $query_user);

                if($line_user = mysqli_fetch_assoc($result_user)) {
                    $template->setVariable("USER", $line_user['usuario']);
                    
                    mysqli_free_result($result_user);
                } else {
                    $template->setVariable("USER", "Usuario no encontrado");
                }
            }
            
            $template->setVariable("TITLE", $line_loans['nombre']);
            $template->setVariable("AUTHORS", $authors);
            $template->setVariable("COPY", $line_loans['id_copia']);
            $template->setVariable("USER_ID", $user_id);
            $template->setVariable("BOOK_ID", $book_id);
            $template->setVariable("L_ID", $line_loans['id_reservacion']);
            $template->setVariable("L_DATE", $line_loans['fecha_prestamo']);
            
            $template->parseCurrentBlock();

            $i++;
        }

        $template->setCurrentBlock();
        
        if(!$has_active) {
            $template->setCurrentBlock("EMPTY_ACTIVE");
            $template->setVariable("ACTIVE_EMPTY", $active_empty);
            $template->parseCurrentBlock();
        } else if($line_users['admin_p']) {
            $template->setCurrentBlock("ACTIVE_LABELS");
            $template->setVariable("USER_ID_PLACEHOLDER", "Usuario");
            $template->parseCurrentBlock();
        } else {
            $template->touchBlock("ACTIVE_LABELS");
        }
        
        $template->setCurrentBlock();
        
        if(!$has_inactive) {
            $template->setCurrentBlock("EMPTY_INACTIVE");
            $template->setVariable("INACTIVE_EMPTY", $inactive_empty);
            $template->parseCurrentBlock();
        } else if($line_users['admin_p']) {
            $template->setCurrentBlock("INACTIVE_LABELS");
            $template->setVariable("USER_ID_PLACEHOLDER", "Usuario");
            $template->parseCurrentBlock();
        } else {
            $template->touchBlock("INACTIVE_LABELS");
        }
        
        $template->setCurrentBlock();

        if($i) {
            mysqli_free_result($result_loans);
        }
        
        $template->parseCurrentBlock();
        
        mysqli_free_result($result_users);
    } else {
        print("User verification error..."); // missing template
    }

    // muestra la pantalla
    $template->show();

    // cierra la conexión a la base de datos
    @mysqli_close($link);
?>