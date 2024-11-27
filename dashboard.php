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
        $template->setVariable("U_IMAGE", $line['imagen']);

        if($line['admin_p']) {    
            $template->setVariable("SECTION_NAME", 'Panel de Administrador');
            // cargar template de admin
        }
        else {
            $template->setVariable("SECTION_NAME", 'El Archivo del Diodo');
            // cargar template de usuario
        }
    } else {
        print("<h1>Como llegaste hasta aqui?</h1>");
    }

    $template->show();
?>