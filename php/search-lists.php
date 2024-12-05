<?php
    require_once "HTML/Template/ITX.php";
    include './config.php';

    $user = implode("\'", explode("'", implode("\\\\", explode("\\", $_POST['username']))));
    $pass = implode("\'", explode("'", implode("\\\\", explode("\\", $_POST['password']))));
    $search = implode("\'", explode("'", implode("\\\\", explode("\\", $_POST['search']))));

    $link = mysqli_connect($cfgServer['host'], $cfgServer['user'], $cfgServer['password']);
        mysqli_select_db($link, $cfgServer['dbname']);

    $template = new HTML_Template_ITX('../templates');
    
    $query_user = "SELECT * FROM b_cuentas WHERE usuario = '$user' AND contrasena = '$pass' LIMIT 1";
    $result_user = mysqli_query($link, $query_user);
    
    if($line_user = mysqli_fetch_assoc($result_user)) {
        $this_user_id = $line_user['id_cuenta'];

        $template->loadTemplatefile("./collections/list-collection.html", true, true);

        $search_query = "SELECT * FROM v_listas_general WHERE (nombre LIKE '%$search%' OR id_lista = '$search') AND id_cuenta = $this_user_id";
        $search_result = mysqli_query($link, $search_query);

        $i = 0;

        while($line_result = mysqli_fetch_assoc($search_result)) {
            $template->setCurrentBlock("LIST");

            $template->setVariable("ID", $line_result['id_lista']);
            $template->setVariable("NAME", $line_result['nombre']);
            
            if($line_result['libros']) {
                $book_count = $line_result['libros'];
                $book_count = "$book_count Libros";

                $template->setVariable("BOOK_COUNT", $book_count);
            } else {
                $template->setVariable("BOOK_COUNT", "Sin libros guardados");
            }

            if($line_result['privada']) {
                $template->setVariable("DISABLED", "disabled");

                $template->setCurrentBlock("PRIVATE");
            } else {
                $template->setCurrentBlock("FRIENDS");

                $query_friends = "SELECT id_cuenta, usuario FROM b_cuentas WHERE (id_cuenta IN (SELECT id_cuenta FROM b_usuario_usuario WHERE id_amigo = $this_user_id) OR id_cuenta IN (SELECT id_amigo FROM b_usuario_usuario WHERE id_cuenta = $this_user_id)) AND id_cuenta != $this_user_id";
                $result_friends = mysqli_query($link, $query_friends);

                $i = 0;

                while($line_friends = mysqli_fetch_assoc($result_friends)) {
                    $template->setCurrentBlock("FRIENDS");

                    $template->setVariable("FRIEND_ID", $line_friends['id_cuenta']);
                    $template->setVariable("FRIEND_NAME", $line_friends['usuario']);

                    $template->parseCurrentBlock();

                    $i++;
                }

                if($i) {
                    mysqli_free_result($result_friends);
                }

                $template->setCurrentBlock("PUBLIC");
            }

            $template->setVariable("ID_P", $line_result['id_lista']);
            $template->parseCurrentBlock();
                
            $template->setCurrentBlock("LIST");
            $template->parseCurrentBlock();

            $i++;
        }

        if($i) {
            mysqli_free_result($search_result);
        } else {
            $template->setCurrentBlock("EMPTY");
            $template->setVariable("LISTS_EMPTY", "No se encontraron listas.");
            $template->parseCurrentBlock();
        }

        mysqli_free_result($result_user);
    } else {
        $template->loadTemplatefile("backrooms.html", true, true);
        $template->touchBlock("PAGE");
    }

    $template->show();
    @mysqli_close($link);
?>