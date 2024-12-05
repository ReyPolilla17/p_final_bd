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

function sendFriendrequest(friend_id, origin_page, section) {
    var user = document.getElementById("username-holder").value; //información del usuario
    var password = document.getElementById("password-holder").value; // información del usuario
    
    var info = `username=${user}&password=${password}&friend_id=${friend_id}&origin_page=${origin_page}`;

    if(section) {
        $.ajax({
            url: './php/interactions/send-friend-request.php',
            dataType: 'html',
            type: 'POST',
            async: true,
            data: info,
            success: reloadSection,
            error: eFnction
        });
    } else {
        $.ajax({
            url: './php/interactions/send-friend-request.php',
            dataType: 'html',
            type: 'POST',
            async: true,
            data: info,
            success: reloadInfoSection,
            error: eFnction
        });
    }
}

function acceptFriendrequest(friend_id, origin_page, section) {
    var user = document.getElementById("username-holder").value; //información del usuario
    var password = document.getElementById("password-holder").value; // información del usuario
    
    var info = `username=${user}&password=${password}&friend_id=${friend_id}&origin_page=${origin_page}`;

    if(section) {
        $.ajax({
            url: './php/interactions/accept-friend-request.php',
            dataType: 'html',
            type: 'POST',
            async: true,
            data: info,
            success: reloadSection,
            error: eFnction
        });
    } else {
        $.ajax({
            url: './php/interactions/accept-friend-request.php',
            dataType: 'html',
            type: 'POST',
            async: true,
            data: info,
            success: reloadInfoSection,
            error: eFnction
        });
    }
}

function declineFriendrequest(friend_id) {
    var user = document.getElementById("username-holder").value; //información del usuario
    var password = document.getElementById("password-holder").value; // información del usuario
    
    var info = `username=${user}&password=${password}&friend_id=${friend_id}`;

    $.ajax({
        url: './php/interactions/decline-friend-request.php',
        dataType: 'html',
        type: 'POST',
        async: true,
        data: info,
        success: reloadSection,
        error: eFnction
    });
}

function removeFriend(friend_id, origin_page, section) {
    var user = document.getElementById("username-holder").value; //información del usuario
    var password = document.getElementById("password-holder").value; // información del usuario
    
    var info = `username=${user}&password=${password}&friend_id=${friend_id}&origin_page=${origin_page}`;

    if(section) {
        $.ajax({
            url: './php/interactions/remove-friend.php',
            dataType: 'html',
            type: 'POST',
            async: true,
            data: info,
            success: reloadSection,
            error: eFnction
        });
    } else {
        $.ajax({
            url: './php/interactions/remove-friend.php',
            dataType: 'html',
            type: 'POST',
            async: true,
            data: info,
            success: reloadInfoSection,
            error: eFnction
        });
    }
}

function recomendList(list_id, friend_id) {
    var user = document.getElementById("username-holder").value; //información del usuario
    var password = document.getElementById("password-holder").value; // información del usuario
    
    var info = `username=${user}&password=${password}&list_id=${list_id}&friend_id=${friend_id}`;

    $.ajax({
        url: './php/interactions/recomend-list.php',
        dataType: 'html',
        type: 'POST',
        async: true,
        data: info,
        success: reloadSection,
        error: eFnction
    });
}

function recomendBook(book_id, friend_id, origin_page) {
    var user = document.getElementById("username-holder").value; //información del usuario
    var password = document.getElementById("password-holder").value; // información del usuario
    
    var info = `username=${user}&password=${password}&book_id=${book_id}&friend_id=${friend_id}&origin_page=${origin_page}`;

    $.ajax({
        url: './php/interactions/recomend-book.php',
        dataType: 'html',
        type: 'POST',
        async: true,
        data: info,
        success: reloadInfoSection,
        error: eFnction
    });
}

function saveBook(book_id, list_id, origin_page) {
    var user = document.getElementById("username-holder").value; //información del usuario
    var password = document.getElementById("password-holder").value; // información del usuario
    
    var info = `username=${user}&password=${password}&book_id=${book_id}&list_id=${list_id}&origin_page=${origin_page}`;

    $.ajax({
        url: './php/interactions/save-book.php',
        dataType: 'html',
        type: 'POST',
        async: true,
        data: info,
        success: reloadInfoSection,
        error: eFnction
    });
}

function rateBook(book_id, rating, origin_page) {
    var user = document.getElementById("username-holder").value; //información del usuario
    var password = document.getElementById("password-holder").value; // información del usuario
    
    var info = `username=${user}&password=${password}&book_id=${book_id}&rating=${rating}&origin_page=${origin_page}`;

    $.ajax({
        url: './php/interactions/rate-book.php',
        dataType: 'html',
        type: 'POST',
        async: true,
        data: info,
        success: reloadInfoSection,
        error: eFnction
    });
}

function requestBook(book_id) {
    var user = document.getElementById("username-holder").value; //información del usuario
    var password = document.getElementById("password-holder").value; // información del usuario
    
    var info = `username=${user}&password=${password}&book_id=${book_id}`;

    $.ajax({
        url: './php/interactions/request-book.php',
        dataType: 'html',
        type: 'POST',
        async: true,
        data: info,
        success: reloadSection,
        error: eFnction
    });
}

function reloadSection(result, status, xhr) {
    var elements = result.split("/");
    var origin = elements[0];
    
    selectItem(origin, true);
}

function reloadInfoSection(result, status, xhr) {
    var elements = result.split("/");
    var origin = elements[0];
    var option = elements[1];
    var id = elements[2];

    console.log(elements);

    switch(option) {
        case 'books':
            bookInfo(id, origin);
            break;
        case 'users':
            userInfo(id, origin);
            break;
        case 'lists':
            listInfo(id, origin);
            break;
    }
}

function newBookTemplate() {
    var user = document.getElementById("username-holder").value; //información del usuario
    var password = document.getElementById("password-holder").value; // información del usuario
    
    var info = `username=${user}&password=${password}`;

    $.ajax({
        url: './php/edition/new-book-template.php',
        dataType: 'html',
        type: 'POST',
        async: true,
        data: info,
        success: displayNewSection,
        error: eFnction
    });
}

function displayNewSection(result, status, xhr) {
    $("#section-start").html(result); // muestra los resultados de la búsqueda
}

function createBook(book_id=null) {
    var user = document.getElementById("username-holder").value; //información del usuario
    var password = document.getElementById("password-holder").value; // información del usuario
    var title = document.getElementById("book-title").value;
    var editorial = document.getElementById("book-editorial").value;
    var resume = document.getElementById("book-resume").value;
    var stock = document.getElementById("book-stock").value;
    
    var info;

    if(book_id) {
        info = `username=${user}&password=${password}&title=${title}&editorial=${editorial}&resume=${resume}&stock=${stock}&book_id=${book_id}`;
    } else {
        info = `username=${user}&password=${password}&title=${title}&editorial=${editorial}&resume=${resume}&stock=${stock}`;
    }

    $.ajax({
        url: './php/edition/edit-book.php',
        dataType: 'html',
        type: 'POST',
        async: true,
        data: info,
        success: reloadSection,
        error: eFnction
    });
}