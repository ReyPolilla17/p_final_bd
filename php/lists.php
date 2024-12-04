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
        $query_lists = "SELECT * FROM v_listas_general WHERE id_cuenta = $this_user_id";

        $template->loadTemplatefile("lists.html", true, true);

        // carga el archivo que contriene el formato para presentar libros
        $template->addBlockfile("COLLECTION", "LIST_COLLECTION", "./collections/list-collection.html");

        $i = 0;

        $result_lists = mysqli_query($link, $query_lists);

        // para cada resultado del query
        while($line_lists = mysqli_fetch_assoc($result_lists)) {
            $template->setCurrentBlock("LIST");

            $template->setVariable("ID", $line_lists['id_lista']);
            $template->setVariable("NAME", $line_lists['nombre']);
            
            if($line_lists['libros']) {
                $book_count = $line_lists['libros'];
                $book_count = "$book_count Libros";

                $template->setVariable("BOOK_COUNT", $book_count);
            } else {
                $template->setVariable("BOOK_COUNT", "Sin libros guardados");
            }

            if($line_lists['privada']) {
                $template->setCurrentBlock("PRIVATE");
            } else {
                $template->setCurrentBlock("PUBLIC");
            }

            $template->setVariable("ID_P", $line_lists['id_lista']);
            $template->parseCurrentBlock();
                
                
            $template->setCurrentBlock("LIST");
            $template->parseCurrentBlock();

            $i++;
        }

        // si el usuario no tiene listas
        if(!$i) {
            $template->setCurrentBlock("EMPTY");
            $template->setVariable("LISTS_EMPTY", "No has creado ninguna lista.");
            $template->parseCurrentBlock();
        } else {
            mysqli_free_result($result_users); // libera memoria
        }
        
        mysqli_free_result($result_user); // libera memoria
    } else {
        $template->loadTemplatefile("backrooms.html", true, true);
        $template->touchBlock("PAGE");
    }
    
    // muestra la pantalla
    $template->show();

    // cierra la conexión a la base de datos
    @mysqli_close($link);
?>