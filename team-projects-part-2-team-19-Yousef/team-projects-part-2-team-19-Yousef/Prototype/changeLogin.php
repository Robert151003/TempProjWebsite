<!DOCTYPE html>
<html lang="en">


<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width = device-width, initial-scale = 1">

    <!-- External scripts such as Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.0/css/all.css">
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.0/css/sharp-solid.css">
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.0/css/sharp-regular.css">
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.0/css/sharp-light.css">

    <!-- External CSS and JavaScript files -->
    <script src="scripting.js"></script>
    <script src="index.js"></script>
    <link rel="stylesheet" type="text/css" href="stylesheet.css">

    <title> Login </title>
    <link rel="icon" href="Assets/Logo.ico" type="image/x-icon">

</head>

<body class="index">

    <div class="TopMenu">

        <img src="Assets/Logo.png" class="LogoImg">

    </div>
    <div class="Login" style="width: 35%; height: 65%;">
        <div class="tasks-header">
            <p style="margin-bottom: 0px; color: #FFFFFF"><b>Change Password</b></p>
        </div>
        <div style="background-color: #F0F0F0; width: 100%; height: 100%; border-radius: 0px 0px 5px 5px; border: 2px lightgrey solid;">
            <div class="username" style="margin: 7% 0% 5% 0%;">
                <label for="newPassword" class="form-label">New Password</label>
                <div class="input-group mx-auto" style="width: 85%;">
                    <input type="password" id="newPassword" class="form-control mx-auto">
                    <div class="input-group-prepend">
                    <span class="input-group-text toggle-password" id="basic-addon1" data-target="newPassword">
                        <img src="Assets/eye.png" width="25" height="25">
                    </span>
                    </div>
                </div>
            </div>

            <div class="password" style="margin: 5% 0% 5% 0%;">
                <label for="newPassword2" class="form-label">Confirm Password</label>
                <div class="input-group mx-auto" style="width: 85%;">
                    <input type="password" id="newPassword2" class="form-control">
                    <div class="input-group-prepend">
                    <span class="input-group-text toggle-password" id="basic-addon2" data-target="newPassword2">
                        <img src="Assets/eye.png" width="25" height="25">
                    </span>
                    </div>
                </div>
            </div>

            <div class="loginBtn" style="margin: 15% 0% 5% 0%;">
                <!--<input type="button" value="Login" id="loginButton" onclick="submit(); window.location.href = 'home.html';" class="btn btn-primary"><br>-->
                <button id="changeButton" type="submit" class="btn btn-secondary" style="width: 20%;">Confirm</button>
            </div>
            <p id="info" style="color: red; font-size: 14px;"></p><br/>

        </div>
    </div>

    <div id="checkmarkOverlay" class="overlay" style="justify-content: center;">
        <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52"> <circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none"/> <path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
        </svg>
    </div>

</body>

</html>

<script>
    $(document).ready(function() {
        $('.toggle-password').mousedown(function() {
            var inputId = $(this).attr('data-target');
            $('#' + inputId).attr('type', 'text');
        });

        $('.toggle-password').mouseup(function() {
            var inputId = $(this).attr('data-target');
            $('#' + inputId).attr('type', 'password');
        });

        $('.toggle-password').mouseout(function() {
            var inputId = $(this).attr('data-target');
            $('#' + inputId).attr('type', 'password');
        });
        $('#changeButton').click(function() {
            changePassword();
        });
    });

    function changePassword() {

        var password1 = $('#newPassword').val();
        var password2 = $('#newPassword2').val();
        
        const url = window.location.search;
        const urlParams = new URLSearchParams(url);
        const token = urlParams.get('token');

        $.ajax({
            url: "resetPassword.php",
            type: "GET",
            data: {
                pass1: password1,
                pass2: password2,
                token: token
            },
            success: function(data) {
                var informationText = $('#info');
                switch (parseInt(data)) {
                    case 1:
                        informationText.html("<a href='index.html'>Password changed successfully! Try logging in again!<a>");
                        informationText.css("color", "#90EE90");
                        showCheckmark(function() {
                            window.location.href = "index.html";
                        });

                        break;
                    case 0:
                        informationText.html("Both passwords aren't matching! Check and try again!");
                        informationText.css("color", "#cc0000");
                        break;
                    case -1:
                        informationText.html("Invalid request. Please try requesting again.");
                        informationText.css("color", "#cc0000");
                        break;
                    case -2:
                        informationText.html("Expired request. Please try requesting again.");
                        informationText.css("color", "#cc0000");
                        break;
                    case -3:
                        informationText.html("Password must be atleast 6 characters. Check and try again!");
                        informationText.css("color", "#cc0000");
                        break;
                    case -4:
                        informationText.html("Password cannot be longer than characters. Check and try again!");
                        informationText.css("color", "#cc0000");
                        break;
                    case -5:
                        informationText.html("Password must contain atleast one capital letter. Check and try again!");
                        informationText.css("color", "#cc0000");
                        break;
                    case -6:
                        informationText.html("Password must contain atleast one number. Check and try again!");
                        informationText.css("color", "#cc0000");
                        break;
                    case -7:
                        informationText.html("Password must contain atleast one special character. Check and try again!");
                        informationText.css("color", "#cc0000");
                        break;
                    case -8:
                        informationText.html("Unknown error occured. Try again later.");
                        informationText.css("color", "#cc0000");
                        break;
                    default:
                        console.log(data == 1);
                    // Default code if data doesn't match any case
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log("Error: " + errorThrown);
            }
        });
    }
</script>

<?php
?>