function hideWarning(warningId) {
    var warning = document.getElementById(warningId); // el elemento a ocultar

    warning.style.opacity = 0; // oculta el elemento
}

function resetWarning(warningId) {
    var warning = document.getElementById(warningId); // el elemento a reestablecer

    warning.innerText = "*"; // establece el valor por defecto
}

function validateLogin() {
    var username = document.getElementById("username-input").value; // usuario
    var password = document.getElementById("password-input").value; // contraseña

    var usr_m = document.getElementById("username-m"); // nota de campo obligatorio
    var pswd_m = document.getElementById("password-m"); // nota de campo obligatorio

    var redirect = null; // a que parte de la forma refirigir
    var select = null; // que parte de la forma seleccionar

    var v = true; // la forma es válida

    if(username === '') { // si no se introduce el usuario
        usr_m.innerText = "Debes introducir tu usuario";
        usr_m.style.opacity = 1;

        v = false;
    }

    if(!redirect && !v) { // redirigir a usuario si el campo está vacío
        redirect = "username-m";
        select = "username-input";
    }
    
    if(password === '') { // si no se introduce la contraseña
        pswd_m.style.opacity = 1;
        
        v = false;
    }

    if(!redirect && !v) { // redirigir a contraseña si no se ha establecido a donde redirigir
        redirect = "password-m";
        select = "password-input";
    }

    if(v) { // si la forma es válida
        $.ajax({
            url: './php/logincheck.php', // revisa que el ususario exista
            dataType: 'html',
            type: 'POST',
            async: false,
            data: `username=${username}&password=${password}`,
            success: function(result, status, xhr) { // forma que encontré de validar que el usuario exista sin tener que acceder a otra función
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

    if(redirect) { // redirige a una parte de la forma de ser necesario
        window.location.href = `#${redirect}`;
    }
    
    if(select) { // selecciona un campo de la forma de ser necesario
        document.getElementById(select).focus();
    }
    
    return v;
}

function validateRegister() {
    var username = document.getElementById("username-input").value; // nombre propuesto

    var password = document.getElementById("password-input").value; // contraseña
    var password_confirm = document.getElementById("password-confirm").value; // confirmación de contraseña

    var day = parseInt(document.getElementById("day").value, 10); // día seleccionado como entero
    var month = parseInt(document.getElementById("month").value, 10); // mes seleccionado como entero
    var year = parseInt(document.getElementById("year").value, 10); // año seleccionado como entero

    var usr_m = document.getElementById("username-m"); // nota de campo obligatorio
    var pswd_m = document.getElementById("password-m"); // nota de campo obligatorio
    var pswd_c_m = document.getElementById("password-confirm-m"); // nota de campo obligatorio
    var date_m = document.getElementById("birth-date-m"); // nota de campo obligatorio

    var birthDate = new Date(year, month - 1, day); // crea la fecha seleccionada
    var curDate = new Date(); // obtiene la fecha actual
    var minDate = new Date(curDate.getFullYear() - 15, curDate.getMonth(), curDate.getDate()); // edad minima
    var maxDate = new Date(curDate.getFullYear() - 200, curDate.getMonth(), curDate.getDate()); // edad máxima

    // validaciones
    var redirect = null;
    var select = null;

    var v = true;

    // validaciones de nombre de usuario
    if(username === '') { // campo llenado
        usr_m.innerText = "Este campo es obligatorio. *";
        
        v = false;
    } else if(hasProhibitedChars(username)) { // solo caracteres permitidos
        usr_m.innerText = "Este campo solo puede contener: *\nguión bajo, letras y numeros.";
        
        v = false;
    } else if(username.length > 50) { // longitud apropiada
        usr_m.innerText = "Este campo debe ser menor a 50 caracteres. *";
        
        v = false;        
    }
    
    if(!redirect && !v) {
        redirect = "username-m";
        select = "username-input";
    }
    
    // validaciones de contraseña
    if(password === '') { // campo llenado
        pswd_m.innerText = "Este campo es obligatorio. *";
        
        v = false;
    } else if(password.length > 50) { // longitud apropiada
        usr_m.innerText = "Este campo debe ser menor a 50 caracteres. *";
        
        v = false;        
    }
    
    if(!redirect && !v) {
        redirect = "password-m";
        select = "password-input";
    }

    if(password_confirm === '') { // campo llenada
        pswd_c_m.innerText = "Este campo es obligatorio. *";
        
        v = false;
    } else if(password !== password_confirm) { // las contraseñas coiniciden
        pswd_c_m.innerText = "Las contraseñas no coinciden. *";
        
        v = false;
    }

    if(!redirect && !v) {
        redirect = "password-confirm-m";
        select = "password-confirm";
    }

    // confirmación de edad
    if(!day || !month || !year) { // campo llenado
        date_m.innerText = "Este campo es obligatorio. *";
        
        v = false;
    } else if(birthDate > curDate) { // fecha del futuro
        date_m.innerText = "No se admiten viajeros del futuro. *";
        
        v = false;
    } else if(birthDate > minDate) { // menor a edad permitida
        date_m.innerText = "No se admiten menores a 15 años. *";
        
        v = false;
    } else if(birthDate < maxDate || year < curDate.getFullYear() - 200) { // mayor de 200 años
        date_m.innerText = "No se admiten viajeros del pasado. *";
        
        v = false;
    }

    if(!redirect && !v) {
        redirect = "birth-date-m";
    }
    
    // si los campos son válidos
    if(v) {
        // determina si el nombre de usuario está en uso, si no lo está, crea la cuenta y redirige al portal
        $.ajax({
            url: './php/registercheck.php',
            dataType: 'html',
            type: 'POST',
            async: false,
            data: `username=${username}&password=${password}&date=${year}-${month}-${day}`,
            success: function(result, status, xhr) { 
                if(parseInt(result) === 0) { // si el usuario está en uso
                    usr_m.innerText = "Este nombre ya está en uso. *";

                    redirect = "username-m";
                    select = "username-input";
                    v = false
                }
            },
            error: eFnction
        });
    }

    // redirige y selecciona de ser necesario
    if(redirect) {
        window.location.href = `#${redirect}`;
    }
    
    if(select) {
        document.getElementById(select).focus();
    }

    return v;
}

function hasProhibitedChars(username) {
    var allowedChars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890_".split(""); // los caracteres que puede tener un nombre de usuario

    allowedChars.forEach(char => { // elimina todos los caracteres del nombre propuesto que se encuentren en la lista de caracteres validos
        username = username.replaceAll(char, "");
    });

    return username.length; // regresa un conteo de caracteres
}

function eFnction(xhr, status, error) {
    console.log(xhr, status, error);
}