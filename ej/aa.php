<?php
    require_once "HTML/Template/ITX.php";
    include './config.php';

    $search = $_POST['search'];

    $link = mysqli_connect($cfgServer['host'], $cfgServer['user'], $cfgServer['password']);
        mysqli_select_db($link, $cfgServer['dbname']);

    $template = new HTML_Template_ITX('./templates');
    $template->loadTemplatefile("./bb.html", true, true);

    $search_query = "SELECT * FROM v_libros_general WHERE libro LIKE '%$search%'"; // vista con la información que se quiere mostrar, aplicando un "filtro" por nombre
    $search_result = mysqli_query($link, $search_query);

    $i = 0;

    // para todos los resultados
    while($line_result = mysqli_fetch_assoc($search_result)) {
        $j = 0;

        // información que recibe un tratamiento esecífico
        $book_id = $line_result['id_libro'];

        $rating = $line_result['rating'];
        $available = $line_result['disponibles'];
        $authors = "Sin autores registrados";

        // Como se muestran las calificaciones
        if($rating) {
            $rating = "Calificación: $rating";
        } else {
            $rating = "Sin Calificaciones";
        }

        // como se muestran los libros disponibles
        if(!$available) {
            $available = "Sin disponibilidad.";
        } else if($available == 1) {
            $available = "$available disponible";
        } else {
            $available = "$available disponibles";
        }

        // bloque para mostrar la información del libro
        $template->setCurrentBlock("BOOKS");

        // variables que se colocan en el bloque
        $template->setVariable("ID", $book_id);
        $template->setVariable("TITLE", $line_result['libro']);
        $template->setVariable("EDITORIAL", $line_result['editorial']);
        $template->setVariable("AVAILABLE", $available);
        $template->setVariable("RATING", "$rating");
        $template->setVariable("SUMMARY", $line_result['resumen']);

        // para mostrar todos los autores del libro de la forma "autor, autor, etc."
        $query_authors = "SELECT * FROM v_libros_autores WHERE id_libro = '$book_id'";
        $result_authors = mysqli_query($link, $query_authors);

        // concatena cada autor
        while($line_authors = mysqli_fetch_assoc($result_authors)) {
            $author = $line_authors['autor'];

            if(!$j) {
                $authors = $author;
            } else {
                $authors = "$authors, $author";
            }

            $j++;
        }
        
        // si hay autores, libera el resutlado
        if($j) {
            mysqli_free_result($result_authors);
        }

        $template->setVariable("AUTHOR", $authors); // muestra el resultado
        
        $j = 0;

        // para mostrar todos los generos del libro
        $query_genres = "SELECT * FROM v_libros_generos WHERE id_libro = '$book_id'";
        $result_genres = mysqli_query($link, $query_genres);

        // cada genero se coloca en un bloque nuevo
        while($line_genres = mysqli_fetch_assoc($result_genres)) {
            $template->setCurrentBlock("GENRES");

            $template->setVariable("GENRE", $line_genres['genero']);

            $template->parseCurrentBlock();

            $j++;
        }

        // libera memoria
        if($j) {
            mysqli_free_result($result_genres);
        }
        
        // "guarda" el bloque del libro
        $template->setCurrentBlock("BOOKS");
        $template->parseCurrentBlock();

        $i++;
    }

    // si hay libros, libera memoria, de lo contrario, muestra un mensaje de vacío
    if($i) {
        mysqli_free_result($search_result);
    } else {
        $template->setCurrentBlock("EMPTY");
        $template->setVariable("BOOKS_EMPTY", "No se encontraron libros.");
        $template->parseCurrentBlock();
    }

    // muestra el template y cierra la conexion a sql
    $template->show();
    @mysqli_close($link);
?>