//Index page
//button to submit login details
function deleteAllCookies() {
    const cookies = document.cookie.split(";");
    for (let i = 0; i < cookies.length; i++) {
        const cookie = cookies[i];
        const eqPos = cookie.indexOf("=");
        const name = eqPos > -1 ? cookie.substr(0, eqPos) : cookie;
        document.cookie = name + "=;expires=Thu, 01 Jan 1970 00:00:00 GMT";
    }

}

function setCookie(name, value) {
    document.cookie = name + "=" + value;
}



function openNav() {
    document.getElementById("mySidenav").style.width = "220px";

}

function closeNav() {

    document.getElementById("mySidenav").style.width = "0px";
    $(".static").fadeOut();  
                      
}


//NAV Menu Bootstrap progress bars animation
var progressBars = document.querySelectorAll('.progress-bar');
progressBars.forEach(function(bar) {

    bar.style.animationPlayState = 'running';

});

//Allowing Drag and Drop







// Hides neccesary things for every page (Navbar, giving burger icon ability to open navbar, etc)
$(document).ready(function(){

    $(".nav-link").hover(function(){

        $(this).css("background-color", "#424A52");
    }, function(){

        $(this).css("background-color", "#343A40");

    });

    $(".closebtn").click(function(){

        $(".static").fadeOut();
        closeNav();

    });

    $(".burgerImg").click(function(){

        $(".static").fadeIn();
        openNav();

    });

    $("#closeCreatePost").click(function(){

        $("#createTaskOverlay").fadeOut();

    });

});
//--------------- Task Page Functions ---------------------//

function dateToString(dateObject, format = "ddmmyyyy") {
    if (format === "ddmmyyyy") {
        var day = dateObject.getDate();
        var month = dateObject.getMonth() + 1;
        var year = dateObject.getFullYear();
        var dateString = day + "/" + month + "/" + year;
        return dateString;
    }
}


//------------------------- Login Functions ----------------------------//

/* checkSession: checks whether t he php session is set at the beginning of the page, while also checking if its valid and returning relevant information to be used by other functions
  author: yousefhurani
 */
function checkSession() {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: "check_session.php",
            type: "GET",
            data: {
                ajax: 1
            },
            success: function (data) {
                if (data === "false") {
                    window.location.href = 'index.html';
                } else {
                    console.log(data);
                    var sessionInfo = JSON.parse(data);
                    resolve(sessionInfo);  // Resolve the Promise with sessionInfo
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                reject(errorThrown);  // Reject the Promise with the error message
            }
        });
    });
}


/*
logout: Handles logging out by destroying the session and sending user to the index page immedietly after
author: yousefhurani
 */
function logout() {
    console.log("tried");
    $.ajax({
        url: "fetch.php",
        type: "GET",
        data: {
            action: "handleLogout",
        },
        success: function (data) {
            window.location.href = 'index.html';
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log("Error: " + errorThrown);
        }
    });
}

//------------------------- TESTING Functions ----------------------------//

// loadPage: loads the page by displaying the correct divs on the SideBar, while updating the user name on the top bar
async function loadPage() {
    var checkSessionResponse = await checkSession();
    var id = checkSessionResponse[0];
    var username = checkSessionResponse[1];
    var permission = checkSessionResponse[2];
    document.getElementById("Member").textContent = username;

    if (permission == "Team Member") {
        $('#dashboardNav').remove();
    } else if (permission == "Team Leader") {
        $('#dashboardNavLink').attr("href", "dashboard.html");
    } else if (permission == "Manager") {
        $('#dashboardNavLink').attr("href", "managerDashboard.php");
    }

}

function showCheckmark(callBack) {
    $('#checkmarkOverlay').fadeIn(1000, function () {
        $('#checkmarkOverlay').attr('style', 'display: flex;');
        $('.checkmark').show(10);
        setTimeout(function() {
            $('#checkmarkOverlay').fadeOut();
            $('.checkmark').fadeOut(1000, function() {
                if (callBack && typeof callBack === 'function') {
                    callBack();
                }
            });
        }, 1500);
    });
}