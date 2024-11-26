function hideWarning(warningId) {
    var warning = document.getElementById(warningId);

    warning.style.opacity = 0;
}

function resetWarning(warningId) {
    var warning = document.getElementById(warningId);

    warning.innerText = "*";
}

function validateLogin() {
    var username = document.getElementById("username-input").value;
    var password = document.getElementById("password-input").value;

    var usr_m = document.getElementById("username-m");
    var pswd_m = document.getElementById("password-m");

    var redirect = null;

    var v = true;

    if(username === '') {
        usr_m.style.opacity = 1;

        v = false;
    }

    if(!redirect && !v) {
        redirect = "username-m";
    }
    
    if(password === '') {
        pswd_m.style.opacity = 1;
        
        v = false;

    }

    if(!redirect && !v) {
        redirect = "password-m";
    }

    if(redirect) {
        window.location.href = `#${redirect}`;
    }

    // buscar el ususario, si existe, admitirlo

    console.log(username);
    console.log(password);

    return v;
}

function validateRegister() {
    var username = document.getElementById("username-input").value;

    var password = document.getElementById("password-input").value;
    var password_confirm = document.getElementById("password-confirm").value;

    var day = parseInt(document.getElementById("day").value, 10);
    var month = parseInt(document.getElementById("month").value, 10);
    var year = parseInt(document.getElementById("year").value, 10);

    var usr_m = document.getElementById("username-m");
    var pswd_m = document.getElementById("password-m");
    var pswd_c_m = document.getElementById("password-confirm-m");
    var date_m = document.getElementById("birth-date-m");

    var birthDate = new Date(year, month - 1, day);
    var curDate = new Date();
    var minDate = new Date(curDate.getFullYear() - 15, curDate.getMonth(), curDate.getDate());
    var maxDate = new Date(curDate.getFullYear() - 200, curDate.getMonth(), curDate.getDate());

    var redirect = null;

    var v = true;

    if(username === '') {
        usr_m.innerText = "Este campo es obligatorio. *";
        
        v = false;
    } else if(hasProhibitedChars(username)) {
        usr_m.innerText = "El usuario solo puede contener: *\nguión bajo, letras y numeros.";
        
        v = false;
    }

    if(!redirect && !v) {
        redirect = "username-m";
    }
    
    if(password === '') {
        pswd_m.innerText = "Este campo es obligatorio. *";
        
        v = false;
    }

    if(!redirect && !v) {
        redirect = "password-m";
    }

    if(password_confirm === '') {
        pswd_c_m.innerText = "Este campo es obligatorio. *";
        
        v = false;
    } else if(password !== password_confirm) {
        pswd_c_m.innerText = "Las contraseñas no coinciden. *";
        
        v = false;
    }

    if(!redirect && !v) {
        redirect = "password-confirm-m";
    }

    if(!day || !month || !year) {
        date_m.innerText = "Este campo es obligatorio. *";
        
        v = false;
    } else if(birthDate > curDate) {
        date_m.innerText = "No se admiten viajeros del futuro. *";
        
        v = false;
    } else if(birthDate > minDate) {
        date_m.innerText = "No se admiten menores a 15 años. *";
        
        v = false;
    } else if(birthDate < maxDate || year < curDate.getFullYear() - 200) {
        date_m.innerText = "No se admiten viajeros del pasado. *";
        
        v = false;
    }

    if(!redirect && !v) {
        redirect = "birth-date-m";
    }

    if(redirect) {
        window.location.href = `#${redirect}`;
    }

    // validar si el usuario está en uso, si no, inicia sesión
    
    console.log(username);
    console.log(password);
    console.log(password_confirm);
    console.log(`${day}/${month}/${year}`);

    return v;
}

function hasProhibitedChars(username) {
    var allowedChars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890_".split("");

    allowedChars.forEach(char => {
        username = username.replaceAll(char, "");
    });

    return username.length;
}