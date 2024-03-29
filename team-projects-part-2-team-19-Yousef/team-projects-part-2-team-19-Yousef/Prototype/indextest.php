<!DOCTYPE html>
<html lang = "en">

<script>

</script>

<head>

    <meta charset = "utf-8">
    <meta name = "viewport" content = "width = device-width, initial-scale = 1">

    <!-- External scripts such as Bootstrap -->
    <link rel = "stylesheet" href = "https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src = "https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src = "https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

    <!-- External CSS and JavaScript files -->
    <script src = "scripting.js"></script>
    <link rel = "stylesheet" type = "text/css" href = "stylesheet.css">

    <title> Make-It-All </title>
    <link rel = "icon" href = "Assets/Logo.ico" type = "image/x-icon">

</head>

<body class = "index">

<div class = "TopMenu">

    <img src = "Assets/Logo.png" class = "LogoImg">

</div>

<div class = "Login">

    <div class = "username">

        <label for = "username" class = "form-label">Username</label>
        <input type = "text" class = "form-control" id = "username">

    </div>

    <div class = "password">

        <label for = "password" class = "form-label">Password</label>
        <input type = "password" class = "form-control" id = "password">

    </div>

    <div class = "loginBtn">

        <input type = "button" value = "Login" id = "loginButton" onclick = "submitLogin();" class = "btn btn-primary"><br>

    </div>

    <p id = "incorrect" style = "visibility: hidden; color: red">Incorrect Username or Password</p><br />

</div>

</body>

<script>
    //Allows user to press enter to login
    document.addEventListener("DOMContentLoaded", function () {

        const loginButton = document.getElementById("loginButton");

        document.addEventListener("keydown", function (event) {

            if (event.key === "Enter") {
                loginButton.click();
            }

        });

    });
</script>

</html>