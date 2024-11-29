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
    var select = null;

    var v = true;

    if(username === '') {
        usr_m.innerText = "Debes introducir tu usuario";
        usr_m.style.opacity = 1;

        v = false;
    }

    if(!redirect && !v) {
        redirect = "username-m";
        select = "username-input";
    }
    
    if(password === '') {
        pswd_m.style.opacity = 1;
        
        v = false;

    }

    if(!redirect && !v) {
        redirect = "password-m";
        select = "password-input";
    }

    if(v) {
        $.ajax({
            url: './php/logincheck.php',
            dataType: 'html',
            type: 'POST',
            async: false,
            data: `username=${username}&password=${password}`,
            success: function(result, status, xhr) { 
                if(parseInt(result) === 0) {
                    usr_m.innerText = "Usuario o contraseña incorrectos";
                    usr_m.style.opacity = 1;

                    redirect = "$username-m";
                    select = "username-input";
                    v = false
                } 
            },
            error: eFnction
        });
    }

    if(redirect) {
        window.location.href = `#${redirect}`;
    }
    
    if(select) {
        document.getElementById(select).focus();
    }
    
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
    var select = null;

    var v = true;

    if(username === '') {
        usr_m.innerText = "Este campo es obligatorio. *";
        
        v = false;
    } else if(hasProhibitedChars(username)) {
        usr_m.innerText = "Este campo solo puede contener: *\nguión bajo, letras y numeros.";
        
        v = false;
    } else if(username.length > 50) {
        usr_m.innerText = "Este campo debe ser menor a 50 caracteres. *";
        
        v = false;        
    }
    
    if(!redirect && !v) {
        redirect = "username-m";
        select = "username-input";
    }
    
    if(password === '') {
        pswd_m.innerText = "Este campo es obligatorio. *";
        
        v = false;
    } else if(password.length > 50) {
        usr_m.innerText = "Este campo debe ser menor a 50 caracteres. *";
        
        v = false;        
    }
    
    if(!redirect && !v) {
        redirect = "password-m";
        select = "password-input";
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
        select = "password-confirm";
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
    
    if(v) {
        console.log(v);
        $.ajax({
            url: './php/registercheck.php',
            dataType: 'html',
            type: 'POST',
            async: false,
            data: `username=${username}&password=${password}&date=${year}-${month}-${day}`,
            success: function(result, status, xhr) { 
                if(parseInt(result) === 0) {
                    usr_m.innerText = "Este nombre ya está en uso. *";

                    redirect = "username-m";
                    select = "username-input";
                    v = false
                }
            },
            error: eFnction
        });
    }

    if(redirect) {
        window.location.href = `#${redirect}`;
    }
    
    if(select) {
        document.getElementById(select).focus();
    }

    return v;
}

function hasProhibitedChars(username) {
    var allowedChars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890_".split("");

    allowedChars.forEach(char => {
        username = username.replaceAll(char, "");
    });

    return username.length;
}

function eFnction(xhr, status, error) {
    console.log(xhr, status, error);
}