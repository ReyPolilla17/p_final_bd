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

        $template->loadTemplatefile("./collections/user-collection.html", true, true);

        $search_query = "SELECT * FROM b_cuentas WHERE (usuario LIKE '%$search%' OR id_cuenta = '$search') AND id_cuenta != $this_user_id";
        
        if(!$line_user['admin_p']) {
            $search_query = "$search_query AND admin_p = 0";
        }

        $search_result = mysqli_query($link, $search_query);

        $i = 0;

        while($line_result = mysqli_fetch_assoc($search_result)) {
            $user_id = $line_result['id_cuenta'];

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

            $query_are_friends = "SELECT COUNT(*) amigos FROM b_usuario_usuario WHERE (id_cuenta = $user_id OR id_amigo = $user_id) AND (id_cuenta = $this_user_id OR id_amigo = $this_user_id)";
            $result_are_friends = mysqli_query($link, $query_are_friends);

            $line_are_friends = mysqli_fetch_assoc($result_are_friends);
            $are_friends = $line_are_friends['amigos'];

            mysqli_free_result($result_are_friends); // libera memoria

            $template->setCurrentBlock("USERS");
            
            if($are_friends) {
                $template->setVariable("YOUR_FRIEND", "Son amigos");
            }

            // coloca toda la información del libro
            $template->setVariable("ID", $user_id);
            $template->setVariable("IMAGE", $line_result['imagen']);
            $template->setVariable("NAME", $line_result['usuario']);
            $template->setVariable("FRIENDS", $friends);
            
            $template->parseCurrentBlock();

            $i++;
        }

        if($i) {
            mysqli_free_result($search_result);
        } else {
            print("No se encontraron usuarios"); // missing template
        }

        mysqli_free_result($result_user);
    } else {
        print("User validation error.");
    }

    $template->show();
    @mysqli_close($link);
?>