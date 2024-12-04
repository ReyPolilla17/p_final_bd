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
        $query_users = "SELECT * FROM b_cuentas WHERE (id_cuenta IN (SELECT id_cuenta FROM b_usuario_usuario WHERE (id_cuenta = $this_user_id OR id_amigo = $this_user_id)) OR id_cuenta IN (SELECT id_amigo FROM b_usuario_usuario WHERE (id_cuenta = $this_user_id OR id_amigo = $this_user_id))) AND id_cuenta != $this_user_id";

        $template->loadTemplatefile("users.html", true, true);
        $template->setVariable("FUNCTION", "friend");

        if($line_user['admin_p']) {
            $template->setVariable("USERS_SECTION", "Gestión de Usuarios"); // cambia el titulo de la sección
        } else {
            $template->setVariable("USERS_SECTION", "Busca Amigos"); // cambia el titulo de la sección
            $query_users = "$query_users AND admin_p != 1";
        }

        // carga el archivo que contriene el formato para presentar libros
        $template->addBlockfile("COLLECTION", "USER_COLLECTION", "./collections/user-collection.html");

        $i = 0;

        // obtiene la información de los libros en la base de datos
        $result_users = mysqli_query($link, $query_users);

        // para cada resultado del query
        while($line_users = mysqli_fetch_assoc($result_users)) {
            $user_id = $line_users['id_cuenta'];

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

            $template->setCurrentBlock("USERS");

            // coloca toda la información del libro
            $template->setVariable("ORIGIN", 'friends');
            $template->setVariable("ID", $user_id);
            $template->setVariable("IMAGE", $line_users['imagen']);
            $template->setVariable("NAME", $line_users['usuario']);
            $template->setVariable("FRIENDS", $friends);
            
            $template->parseCurrentBlock();

            $i++;
        }

        // si no hay usuarios en la base de datos
        if(!$i) {
            $template->setCurrentBlock("EMPTY");
            $template->setVariable("USERS_EMPTY", "Aún no tienes amigos.");
            $template->parseCurrentBlock();
        } else {
            mysqli_free_result($result_users); // libera memoria
        }

        $template->touchBlock("REQUESTS");
        
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