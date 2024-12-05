function searchBook() {
    var search = document.getElementById("book-search").value; // el valor a buscar

    var info = `search=${search}`; // información a enviar
    var dir = "./aa.php"; // archivo a ejecutar

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

function eFnction(xhr, status, error) {
    console.log(xhr, status, error);
}