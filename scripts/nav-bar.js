function selectItem(item_id, run) {
    var user = document.getElementById("username-holder").value; //información del usuario
    var password = document.getElementById("password-holder").value; // información del usuario

    var info = `username=${user}&password=${password}`; // elementos a enviar
    var dir = "./php/error.php"; // código php que se mandará

    var nav_bar = document.querySelector(".nav-bar-wrapper"); // menú de opciones

    var run_php = false; // para evitar enviar solicitudes si el elemento ya está seleccionado

    if(run) { // en caso de que se desee regresar a la vista general
        run_php = true;
    }

    for(const child of nav_bar.children) { // para cada elemento del menu de opciones
        if(item_id !== child.id) {
            child.className = "nav-bar-item"; // si no es el elemento seleccionado, le elimina el estilo de seleccionado
        } else if(!child.className.includes('selected')) {
            child.className = `${child.className} selected`; // si el elemento seleccionado no está ya seleccionado
            run_php = true; // realizará la solicitud
        }
    }

    // establece la ruta del php según el elemento seleccionado
    switch(item_id) {
        case "books":
            dir = "./php/books.php";
            break;
        case "users":
            dir = "./php/users.php";
            break;
        case "stats":
            dir = "./php/stats.php";
            break;
        case "loans":
            dir = "./php/loans.php";
            break;
        case "friends":
            dir = "./php/friends.php";
            break;
        case "recomendations":
            dir = "./php/recomendations.php";
            break;
        case "lists":
            dir = "./php/lists.php";
            break;
        case "my-user":
            dir = "./php/my-user.php";
            run_php = true; // necesario porque no está en la nav-bar
            break;
    }

    if(run_php) { // si se debe ejecutar, ejecuta el ajax
        $.ajax({
            url: dir,
            dataType: 'html',
            type: 'POST',
            async: true,
            data: info,
            success: displayContent,
            error: eFnction
        });
    }
}

function returnTo(section_id, origin_id) {
    if(!origin_id) {
        selectItem(section_id, true);
    } else {
        userInfo(origin_id, section_id);
    }
}

function displayContent(result, status, xhr) {
    $("#section-start").html(result); // muestra el resultado de la ejecución del php
    
    window.location.href = "#section-start"; // redirige al inicio de la página 
}

function eFnction(xhr, status, error) {
    console.log(xhr, status, error);
}