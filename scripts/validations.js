function validateLogin() {
    var username = document.getElementById("username-input").value;
    var password = document.getElementById("password-input").value;

    console.log(username);
    console.log(password);

    return false;
}

function validateRegister() {
    var username = document.getElementById("username-input").value;

    var password = document.getElementById("password-input").value;
    var passwordConfirm = document.getElementById("password-confirm").value;

    var day = document.getElementById("day").value;
    var month = document.getElementById("month").value;
    var year = document.getElementById("year").value;

    console.log(username);
    console.log(password);
    console.log(passwordConfirm);
    console.log(`${day}/${month}/${year}`);

    return false;
}