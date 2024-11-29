function searchBook() {
    var user = document.getElementById("username-holder").value;
    var password = document.getElementById("password-holder").value;

    var search = document.getElementById("book-search").value;
    var atribute = parseInt(document.getElementById("book-search-options").value);

    var info = `username=${user}&password=${password}&search=${search}&atribute=${atribute}`;
    var dir = "./php/search-books.php";

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
    $("#book-list").html(result);
}