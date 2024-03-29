function submitLogin() {

    //If login is successful, set a cookie
    //"loggedIn" is the cookie name, "true" is the value, and 30 is the number of days the cookie will be valid.

    var username = document.getElementById("username").value;
    var password = document.getElementById("password").value;

    $.ajax({
        url: "fetch.php",
        type: "GET",
        data: {
            action: "handleLogin",
            username: username,
            password: password
        },
        success: function(data) {

            if (data.trim() == "success") {
                window.location.href = 'home.html';
            } else {
                document.getElementById("incorrect").style.visibility = 'visible';
                console.log(data);
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log("Error: " + errorThrown);
        }
    });

}

function submitRequest(email) {
    $.ajax({
        url: "fetch.php",
        type: "GET",
        data: {
            action: "sendForgotEmail",
            email: email
        },
        success: function (data) {
            if (data == "-2") {
                $('#incorrectParams').css("visibility", "visible");
            } else {
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log(errorThrown);
        }
    });
}
//Allows user to press enter to login
document.addEventListener("DOMContentLoaded", function () {

    const loginButton = document.getElementById("loginButton");

    document.addEventListener("keydown", function (event) {

        if (event.key === "Enter") {
            loginButton.click();
        }

    });

});