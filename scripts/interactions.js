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
        success: reloadSection,
        error: eFnction
    });
}

function changeVisibility(list_id, new_visibility) {
    var user = document.getElementById("username-holder").value; //información del usuario
    var password = document.getElementById("password-holder").value; // información del usuario
    
    var info = `username=${user}&password=${password}&list_id=${list_id}&visibility=${new_visibility}`;

    $.ajax({
        url: './php/interactions/change-visibility.php',
        dataType: 'html',
        type: 'POST',
        async: true,
        data: info,
        success: reloadSection,
        error: eFnction
    });
}

function reloadSection(result, status, xhr) {
    selectItem(result, true);
}