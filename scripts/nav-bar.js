function selectItem(item_id) {
    var user = document.getElementById("username-holder").value;
    var password = document.getElementById("password-holder").value;

    var info = `user=${user}&password=${password}`;
    var dir = "./php/error.php";

    var nav_bar = document.querySelector(".nav-bar");
    var profile = document.querySelector(".profile-settings");

    for(const child of nav_bar.children) {
        if(item_id !== child.id) {
            child.className = "nav-bar-item";
        } else if(!child.className.includes('selected')) {
            child.className = `${child.className} selected`;
        }
    }
    
    if(item_id !== profile.id) {
        profile.className = "profile-settings";
    } else if(!profile.className.includes('selected')) {
        profile.className = `${profile.className} selected`;
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
}

function eFnction(xhr, status, error) {
    console.log(xhr, status, error);
}