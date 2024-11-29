<?php
    require_once "HTML/Template/ITX.php";
    include './php/config.php';

    $user = implode("\'", explode("'", implode("\\\\", explode("\\", $_POST['username']))));
    $pass = implode("\'", explode("'", implode("\\\\", explode("\\", $_POST['password']))));

    $origin = $_POST["origin"];

    $link = mysqli_connect($cfgServer['host'], $cfgServer['user'], $cfgServer['password']);
        mysqli_select_db($link, $cfgServer['dbname']);
    
    $query = "SELECT * FROM b_cuentas WHERE usuario = '$user' AND contrasena = '$pass' LIMIT 1";

    $result = mysqli_query($link, $query);

    $template = new HTML_Template_ITX('./templates');
    
    if($line = mysqli_fetch_assoc($result)) {
        $template->loadTemplatefile("dashboard.html", true, true);
        $template->setVariable("USERNAME", $_POST['username']);
        $template->setVariable("PASSWORD", $_POST['password']);
        $template->setVariable("U_IMAGE", $line['imagen']);

        if($line['admin_p']) {    
            $template->setVariable("SECTION_NAME", 'Panel de Administrador');

            $template->addBlockfile("NAV_BAR", "ADMIN_BAR", "./admin/admin-nav-bar.html");
            $template->touchBlock("ADMIN_BAR");

            $template->addBlockfile("SECTION", "SECTION", "./admin/default-admin.html");
            $template->touchBlock("SECTION");

            $template->parseCurrentBlock("ADMINSECTION");
            // cargar template de admin
        }
        else {
            $template->setVariable("SECTION_NAME", 'El Archivo del Diodo');

            $template->addBlockfile("NAV_BAR", "USER_BAR", "./user/user-nav-bar.html");
            $template->touchBlock("USER_BAR");
            // cargar template de usuario
        }

        mysqli_free_result($result);
    } else {
        print("<h1>Como llegaste hasta aqui?</h1>");
    }

    $template->show();

    @mysqli_close($link);
?>