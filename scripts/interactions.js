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