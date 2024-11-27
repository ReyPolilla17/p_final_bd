function selectItem(item_id) {
    var user = document.getElementById("username-holder").value;
    var password = document.getElementById("password-holder").value;

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
            break;
        case "users":
            break;
        case "stats":
            break;
        case "my-user":
            break;
    }
}