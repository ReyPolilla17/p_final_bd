function selectItem(item_id) {
    var user = document.getElementById("username-holder").value;
    var password = document.getElementById("password-holder").value;

    var info = `username=${user}&password=${password}`;
    var dir = "./php/error.php";

    var nav_bar = document.querySelector(".nav-bar-wrapper");

    for(const child of nav_bar.children) {
        if(item_id !== child.id) {
            child.className = "nav-bar-item";
        } else if(!child.className.includes('selected')) {
            child.className = `${child.className} selected`;
        }
    }

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
            break;
    }

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

function displayContent(result, status, xhr) {
    $("#section-start").html(result);
    
    window.location.href = "#section-start";
}

function eFnction(xhr, status, error) {
    console.log(xhr, status, error);
}