function returnBook(loan_id) {
    var user = document.getElementById("username-holder").value; //información del usuario
    var password = document.getElementById("password-holder").value; // información del usuario

    var info = `username=${user}&password=${password}&loan_id=${loan_id}`;

    $.ajax({
        url: './php/interactions/return-book.php',
        dataType: 'html',
        type: 'POST',
        async: true,
        data: info,
        success: reloadLoans,
        error: eFnction
    });
}

function reloadLoans(result, status, xhr) {
    selectItem('loans', true);
}