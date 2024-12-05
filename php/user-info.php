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

        $query_user = "SELECT * FROM b_cuentas WHERE id_cuenta = $id LIMIT 1";
        $result_user = mysqli_query($link, $query_user);

        if($line_user = mysqli_fetch_assoc($result_user)) {
            $user_id = $line_user['id_cuenta'];

            $query_lists = "SELECT * FROM v_listas_general WHERE id_cuenta = $user_id";

            if($line_users['admin_p']) { // si el usuario es administrador
                $template->loadTemplatefile("my-user.html", true, true);
            } else {
                $query_lists = "$query_lists AND privada = 0";
                
                $template->loadTemplatefile("user-info.html", true, true);
            }

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

            $query_are_friends = "SELECT * FROM b_usuario_usuario WHERE (id_cuenta = $user_id OR id_amigo = $user_id) AND (id_cuenta = $this_user_id OR id_amigo = $this_user_id)";
            $result_are_friends = mysqli_query($link, $query_are_friends);
            
            $query_request_sent = "SELECT * FROM b_solicitudes WHERE id_origen = $this_user_id AND id_destino = $user_id";
            $result_request_sent = mysqli_query($link, $query_request_sent);
            
            $query_request_recieved = "SELECT * FROM b_solicitudes WHERE id_origen = $user_id AND id_destino = $this_user_id";
            $result_request_recieved = mysqli_query($link, $query_request_recieved);
            
            if($line_are_friends = mysqli_fetch_assoc($result_are_friends)) {
                $template->setVariable("ALTER_FRIENDSHIP", "Terminar Amistad");
                $template->setVariable("FUNCTION", "removeFriend($user_id, '$origin', false)");
                
                $friend_since = $line_are_friends['fecha_inicio'];
                $friend_since = "Amigos desde $friend_since";
                
                $template->setVariable("FRIEND", $friend_since);
                
                mysqli_free_result($result_are_friends); // libera memoria
            } else if($line_request_sent = mysqli_fetch_assoc($result_request_sent)) {
                $template->setVariable("ALTER_FRIENDSHIP", "Solicitud Enviada");
                $template->setVariable("DISABLED", "disabled");
                
                mysqli_free_result($result_request_sent); // libera memoria
            } else if($line_request_recieved = mysqli_fetch_assoc($result_request_recieved)) {
                $template->setVariable("ALTER_FRIENDSHIP", "Aceptar Solicitud");
                $template->setVariable("FUNCTION", "acceptFriendrequest($user_id, '$origin', false)");
                
                mysqli_free_result($result_request_recieved); // libera memoria
            } else {
                $template->setVariable("ALTER_FRIENDSHIP", "Enviar Solicitud");
                $template->setVariable("FUNCTION", "sendFriendrequest($user_id, '$origin', false)");
            }

            $template->setVariable("ORIGIN", $origin);
            $template->setVariable("IMAGE", $line_user['imagen']);
            $template->setVariable("USER", $line_user['usuario']);
            $template->setVariable("JOIN", $line_user['creacion']);
            $template->setVariable("BIRTH", $line_user['nacimiento']);
            $template->setVariable("FRIEND_COUNT", $friends);

            $result_lists = mysqli_query($link, $query_lists);

            $i = 0;

            while($line_lists = mysqli_fetch_assoc($result_lists)) {
                $book_count = $line_lists['libros'];

                if(!$i) {
                    $template->setCurrentBlock("LISTS");
                }

                $template->setCurrentBlock("LIST_ELEMENT");

                $template->setVariable("ID", $line_lists['id_lista']);
                $template->setVariable("NAME", $line_lists['nombre']);

                if($book_count) {
                    $book_count = "$book_count Libros";
                } else {
                    $book_count = "Sin libros guardados";
                }

                $template->setVariable("BOOK_COUNT", $book_count);

                $template->parseCurrentBlock();

                $i++;
            }

            if($i) {

            } else {

                $template->touchBlock("EMPTY_LISTS");
            }
            
            mysqli_free_result($result_user); // libera memoria
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