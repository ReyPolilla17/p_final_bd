<?php
    require_once "HTML/Template/ITX.php";
    include './config.php';

    $user = implode("\'", explode("'", implode("\\\\", explode("\\", $_POST['username']))));
    $pass = implode("\'", explode("'", implode("\\\\", explode("\\", $_POST['password']))));

    $link = mysqli_connect($cfgServer['host'], $cfgServer['user'], $cfgServer['password']);
        mysqli_select_db($link, $cfgServer['dbname']);
            
    $template = new HTML_Template_ITX('../templates');
    
    $query = "SELECT * FROM b_cuentas WHERE usuario = '$user' AND contrasena = '$pass' LIMIT 1";
    
    $result = mysqli_query($link, $query);
    
    if($line = mysqli_fetch_assoc($result)) {
        $template->loadTemplatefile("books.html", true, true);

        if($line['admin_p']) {
            $template->addBlockfile("ADMIN_ADD", "ADMIN_SECTION", "./admin/add-book.html");
            $template->touchBlock("ADMIN_SECTION");

            $books_query = "SELECT * FROM v_libros_general";
            $books_result = mysqli_query($link, $books_query);

            $i = 1;

            while($l = mysqli_fetch_assoc($books_result)) {
                $rating = $l['rating'];

                if(!$rating) {
                    $rating = 'N/A';
                }

                $template->setCurrentBlock("BOOK");

                $template->setVariable("LINK", $l['imagen']);
                $template->setVariable("TITLE", $l['libro']);
                // $template->setVariable("AUTHOR", $l['nombre']);
                $template->setVariable("EDITORIAL", $l['editorial']);
                $template->setVariable("AVIABLE", $l['disponibles']);
                $template->setVariable("RATING", $rating);
                $template->setVariable("RESUME", $l['resumen']);


                $template->parseCurrentBlock("BOOK");
                // $template->setVariable("TITLE", $line['nombre']);
            }

            $template->setCurrentBlock("COLLECTION_ITEM");
            $template->parseCurrentBlock("ADMINSECTION");
        } else {
            $template->setVariable("COLLECTION_ITEM", 'Panel de Usuario');

        }
        
        mysqli_free_result($result);
    } else {
        print("LMAO");
    }
    
    $template->parseCurrentBlock();
    $template->show();

    @mysqli_close($link);

// get all user info from a query
// check if admin, else, return another template
?>