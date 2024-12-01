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
        $user_id = $line_users['id_cuenta'];
        $template->loadTemplatefile("loans.html", true, true);

        $query_loans = "SELECT * FROM v_libros_reservaciones WHERE id_cuenta = $user_id";
        $result_loans = mysqli_query($link, $query_loans);

        $i = 0;

        while($line_loans = mysqli_fetch_assoc($result_loans)) {
            $book_id = $line_loans['id_libro'];
            $has_active = 0;
            $has_inactive = 0;

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
            
            $template->setVariable("TITLE", $line_loans['nombre']);
            $template->setVariable("AUTHORS", $authors);
            $template->setVariable("COPY", $line_loans['id_copia']);
            $template->setVariable("L_DATE", $line_loans['fecha_prestamo']);
            
            $template->parseCurrentBlock();

            $i++;
        }

        if(!$has_inactive) {
            $template->setVariable("INACTIVE_EMPTY", "Nada por aqui."); // missing template
        }

        if(!$has_active) {
            $template->setVariable("ACTIVE_EMPTY", "Nada por aqui."); // missing template
        }

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