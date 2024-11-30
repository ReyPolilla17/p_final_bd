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