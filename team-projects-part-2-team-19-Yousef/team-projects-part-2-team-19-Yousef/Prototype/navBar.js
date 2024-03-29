var div = document.createElement("navBar");
div.innerHTML = `

    <nav class = "navbar sticky-top navbar-expand-lg d-print" style = "background-color: #D2D2D2">

    <img src = "Assets/HamburgerIcon.png" class = "burgerImg clickable-image" />

    <a href = "home.html">
        <img src = "Assets/Logo.png" class = "LogoImg" width = "240" height = "60" />
    </a>

    <p class = "navbar-toggler"></p>

    <div class = "collapse navbar-collapse" id = "navbarSupportedContent">

        <ul class = "navbar-nav mr-auto"></ul>

        <div id = "userProfileStuff">

            <img src = "Assets/ProfilePic.png" width = "45px" height = "45px" />
            <a class = "font-weight-light" id = "Member"> &nbsp;&nbsp;&nbsp;&nbsp;</a>

        </div>

        <div class = "dropdown show">

            <a href = "#" role = "button" class = "hoverable-icon" id = "dropdownMenuLink" data-toggle = "dropdown" style = "position:relative; right:15px;" aria-haspopup = "true" aria-expanded = "false">
                    
                <object data = "Assets/svg-icons/settings.svg" type = "image/svg+xml" class = "clickable-image" style = "pointer-events: none;" width = "35px" height = "35px">
                <img src = "Assets/svg-icons/settings.svg" />
                </object>

            </a>

            <div class = "dropdown-menu dropdown-menu-right" aria-labelledby = "dropdownMenuLink">

                <a class = "dropdown-item" href="invite.html"><img src = "Assets/svg-icons/invite-user.png" alt = "InviteIcon" width = "20" height = "20">&nbsp;&nbsp;Invite</a>
                <hr style = "padding-top: 0px; padding-bottom: 0px; border: 1px solid #eaeaea;">
                <a class = "dropdown-item" onclick="logout();"><img src = "Assets/svg-icons/sign-out.svg" alt = "SignoutIcon" width = "20" height = "20">&nbsp;&nbsp;Logout</a>
                
            </div>

        </div>

    </div>

    </nav>

    <div id = "mySidenav" class = "d-flex flex-column flex-shrink-0 text-white bg-dark sidenav">

        <div class = "static">

            <a class = "closebtn">
                <img src = "Assets/svg-icons/arrow-left.svg" class = "clickable-image" alt = "CollapseIcon" width = "20" height = "20" style = "fill: #ffffff">
            </a>

            <hr>
            <ul class = "nav nav-pills flex-column mb-auto navbar-icon">

                <li>
                    <a href = "home.html" class = "nav-link text-white">
                    <p><img src="Assets/homepage.png" width="28" height="28" style="filter: invert(1); margin-right: 4px;">Home</p>

                    <div class = "progress" role = "progressbar" aria-label = "Success example" aria-valuemin = "0" aria-valuemax = "100">
                    <div class = "progress-bar bg-success" style = "width: 0%"></div>
                    </div>
                    </a>
                </li>

                <li>
                    <a href = "todo.html" class = "nav-link text-white">
                        <p><img src = "Assets/check.png" width = "28" height= "28" style = "filter: invert(1); margin-right: 4px;">To-Do List</p>

                        <div class = "progress" role = "progressbar" aria-label = "Darker blue example" aria-valuemin = "0" aria-valuemax = "100">
                            <div class = "progress-bar" style = "width: 0%; background-color: #004e9a;"></div>
                        </div>
                    </a>
                </li>

                <li>
                    <a href = "tasks.html" class = "nav-link text-white">
                    <p><img src = "Assets/svg-icons/list-check.svg" alt = "checklistIcon" width = "25" height = "25">&nbsp;Task Manager</p>

                    <div class = "progress" role = "progressbar" aria-label = "Warning example" aria-valuemin = "0" aria-valuemax = "100">
                    <div class = "progress-bar bg-warning" style = "width: 0%"></div>
                    </div>
                    </a>
                </li>

                <li>
                    <a href = "information.html" class = "nav-link text-white">
                    <p><img src = "Assets/svg-icons/info.svg" alt = "infoIcon" width = "25" height = "25">&nbsp;Information</p>

                    <div class = "progress" role = "progressbar" aria-label = "Danger example" aria-valuemin = "0" aria-valuemax = "100">
                    <div class = "progress-bar bg-danger" style = "width: 0%"></div>
                    </div>
                    </a>
                </li>

                <li id="dashboardNav">
                    <a id="dashboardNavLink" href = "dashboard.html" class = "nav-link text-white">
                    <p><img src = "Assets/svg-icons/teams.svg" alt = "teamsIcon" width = "25" height = "25">&nbsp;Dashboard</p>

                    <div class = "progress" role = "progressbar" aria-label = "Purple example" aria-valuemin = "0" aria-valuemax = "100">
                    <div class = "progress-bar bg-purple" style = "width: 0%"></div>
                    </div>
                    </a>
                </li>

            </ul>
            <hr>

        </div>

    </div>
`;

document.body.appendChild(div);
checkSession();
loadPage();
