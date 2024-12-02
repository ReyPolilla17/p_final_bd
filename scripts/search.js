function searchBook() {
    var user = document.getElementById("username-holder").value; // información del ususario
    var password = document.getElementById("password-holder").value; // información del ususario

    var search = document.getElementById("book-search").value; // el valor a buscar
    var atribute = parseInt(document.getElementById("book-search-options").value); // el filtro de búsqueda

    var info = `username=${user}&password=${password}&search=${search}&atribute=${atribute}`; // información a enviar
    var dir = "./php/search-books.php"; // archivo a ejecutar

    // ejecuta el archivo
    $.ajax({
        url: dir,
		dataType: 'html',
		type: 'POST',
		async: true,
		data: info,
		success: displayBooks,
		error: eFnction
    });
}

function displayBooks(result, status, xhr) {
    $("#book-list").html(result); // muestra los resultados de la búsqueda
}

function bookInfo(id, origin) {
    var user = document.getElementById("username-holder").value; // información del ususario
    var password = document.getElementById("password-holder").value; // información del ususario

    var info = `username=${user}&password=${password}&id=${id}&origin=${origin}`;

    $.ajax({
        url: './php/book-info.php',
        dataType: 'html',
        type: 'POST',
        async: true,
        data: info,
        success: displayBookInfo,
        error: eFnction
    });
}

function displayBookInfo(result, status, xhr) {
    $("#section-start").html(result); // muestra los resultados de la búsqueda

    window.location.href = "#section-start"; // redirige al inicio de la página 
}

function searchUser(user_type) {
    var user = document.getElementById("username-holder").value; // información del ususario
    var password = document.getElementById("password-holder").value; // información del ususario

    var search = document.getElementById("user-search").value; // el valor a buscar

    var info = `username=${user}&password=${password}&search=${search}`; // información a enviar
    var dir = "./php/search-users.php"; // archivo a ejecutar
    
    if(user_type === "friend") {
        dir = "./php/search-friends.php"; // archivo a ejecutar
    }

    // ejecuta el archivo
    $.ajax({
        url: dir,
		dataType: 'html',
		type: 'POST',
		async: true,
		data: info,
		success: displayUsers,
		error: eFnction
    });
}

function displayUsers(result, status, xhr) {
    $("#user-list").html(result); // muestra los resultados de la búsqueda
}

function userInfo(id, origin) {
    var user = document.getElementById("username-holder").value; // información del ususario
    var password = document.getElementById("password-holder").value; // información del ususario

    var info = `username=${user}&password=${password}&id=${id}&origin=${origin}`;
    
    $.ajax({
        url: './php/user-info.php',
        dataType: 'html',
        type: 'POST',
        async: true,
        data: info,
        success: displayUserInfo,
        error: eFnction
    });
}

function displayUserInfo(result, status, xhr) {
    $("#section-start").html(result); // muestra los resultados de la búsqueda

    window.location.href = "#section-start"; // redirige al inicio de la página 
}