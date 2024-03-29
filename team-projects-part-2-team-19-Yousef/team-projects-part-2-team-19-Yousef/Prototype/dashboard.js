//Function to open the tab
function openTab(event, tabName) {
    var i, tabcontent, tabbuttons;
    tabcontent = document.getElementsByClassName("tab-content");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }
    tabbuttons = document.getElementsByClassName("tab-button");
    for (i = 0; i < tabbuttons.length; i++) {
        tabbuttons[i].className = tabbuttons[i].className.replace(" active", "");
    }
    document.getElementById(tabName).style.display = "flex";
    event.currentTarget.className += " active";
}

function getQueryStringParameter(name) {
    name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
    var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
    var results = regex.exec(location.href);
    return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
}


var currentSelectedId;
//Waits for DOM to be fully loaded first
document.addEventListener('DOMContentLoaded', async () => {

    const httpRequestDash = new XMLHttpRequest();


    const response = await fetch('dashboard_start.php');
    const data = await response.json();

    const permission = data.perm;
    const username = data.username;
    const userId = data.id;

    //retreive data from database by sending an httpRequest
    //console.log("Permission:", permission); // Log the permission for verification
    //console.log("Username", username); // Log the permission for verification

    httpRequestDash.open("POST", "dashboard.php", true);
    httpRequestDash.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

    var dataToSend;

    if (permission === "Team Leader") {

        dataToSend = "Username=" + encodeURIComponent(username);


    }else if (permission === "Manager") {
        var projectID = getQueryStringParameter('id');
        dataToSend = "ProjectID=" + encodeURIComponent(projectID);

    } else if (permission === 'Team Member') {
        logout();
        return;
    }

    //console.log(dataToSend)


    httpRequestDash.send(dataToSend);

    httpRequestDash.onreadystatechange = function () { //Response from server, contains posts received from database

        const empList = [];
        /*
        if (this.readyState == 4 && this.status == 200) {
            todos = this.responseText;
            todos = todos.replace(/<\/?[^>]+(>|$)/g, "");
            todosArray = todos.split("},");

            for (let i = 0; i < todosArray.length - 1; i++) {
                todosArray[i] = todosArray[i] + "}";
                todosArray[i] = todosArray[i].replace("[", '');
                todosArray[i] = todosArray[i].replace("]", '');
            }

            todosArray[todosArray.length - 1] = todosArray[todosArray.length - 1].replace("]", '');

            for (let i = 0; i < todosArray.length; i++) {
                var todo = JSON.parse(todosArray[i]);
                console.log(todo);
                empList.push(todo[`Username`]);
            } */

        if (this.readyState == 4 && this.status == 200) {
            //console.log(this.responseText);
            //if (this.responseText == "[]") {
            //    console.log('Invalid Project');
            //    return;
            //}
            const todos = JSON.parse(this.responseText);
            const empList = todos.map(todo => todo.Username);

            /*const employees = empList.map((name, i) => ({
                id: i,
                name: name,
                tasks: 12
            }));*/


            //Populating employeeList from the employees data
            const employeeList = document.getElementById('employeeList');

            todos.forEach(employee => {
                const listItem = document.createElement('div');
                listItem.classList.add('employee-list-item');
                listItem.id = "employee-"+ employee.EmployeeID;

                const profileImage = document.createElement('img');
                profileImage.src = 'Assets/ProfilePic.png';
                listItem.appendChild(profileImage);

                const name = document.createElement('p');
                name.innerText = employee.Username;
                listItem.appendChild(name);

                listItem.onclick = () => {
                    openTaskForm(employee.EmployeeID);
                    currentSelectedId = employee.EmployeeID;
                    event.stopPropagation();
                }
                employeeList.appendChild(listItem);
            });
            if (permission === 'Manager') {
                var div = document.createElement("div");
                var assignGlobalTask = document.createElement("button");
                assignGlobalTask.classList.add("btn");
                assignGlobalTask.classList.add("mr-3");
                assignGlobalTask.id = "assignGlobalTask";
                assignGlobalTask.innerHTML = "New Member";
                div.appendChild(assignGlobalTask);
                employeeList.appendChild(div);

                assignGlobalTask.addEventListener("click", async function() {
                    await getEmployees();
                    $('#assignEmployeeOverlay').fadeIn();
                });

                var createTaskButton = document.createElement("button");
                createTaskButton.classList.add("btn");
                createTaskButton.classList.add("createTaskOption");
                createTaskButton.setAttribute("style", "border: 2px solid #8DB8B1;");
                createTaskButton.innerHTML = "Create Task";
                div.appendChild(createTaskButton);

                createTaskButton.addEventListener("click", function() {
                    $('#createTaskOverlay').fadeIn();
                })
            }


            $.ajax({
                url: "fetch.php",
                type: "GET",
                data: {
                    action: "memberTaskTotals",
                    projectId: encodeURIComponent(projectID)
                },
                success: function (data) {
                    console.log(data);
                    var generalData = JSON.parse(data);
                    var employeeNames = [];
                    var employeeTaskTotal = [];
                    var employeeFinishTotal = [];
                    var taskSum = 0;
                    var totalTasks = 0;

                    for (let employee of generalData) {
                        employeeNames.push(employee['EmployeeName']);
                        employeeTaskTotal.push( employee['TotalTasks'] );
                        employeeFinishTotal.push(employee['FinishedTasks']);
                        taskSum += parseInt(employee['FinishedTasks']);
                        totalTasks += parseInt(employee['TotalTasks']);
                    }
                    //Creating colour palette for the chart
                    colorPalette = [
                        'rgba(0, 128, 0, 0.2)',
                        'rgba(255, 255, 0, 0.2)',
                        'rgba(255, 0, 0, 0.2)',
                        'rgba(128, 0, 128, 0.2)'
                    ];

                    // Generate a colors array based on the length of the data array
                    const colors = employeeNames.map((_, index) => colorPalette[index % colorPalette.length]);
                    const borderColors = employeeNames.map((_, index) => colorPalette[index % colorPalette.length].replace('0.2', '1'));

                    //Creating first chart - bar
                    const ctx = document.getElementById('employeeChart').getContext('2d');
                    const employeeChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: employeeNames,
                            datasets: [{
                                label: 'No. of Tasks Assigned',
                                data: employeeTaskTotal,
                                backgroundColor: colors,
                                borderColor: borderColors,
                                borderWidth: 1
                            }]
                        },
                        options: {
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }

                    });

                    //Creating second chart - pie
                    const ctx2 = document.getElementById('secondEmployeeChart').getContext('2d');
                    const secondEmployeeChart = new Chart(ctx2, {
                        type: 'pie',
                        data: {
                            labels: employeeNames,
                            datasets: [{
                                label: 'No. of Tasks Completed',
                                data: employeeFinishTotal,
                                backgroundColor: colors,
                                borderColor: borderColors,
                                borderWidth: 1
                            }]
                        },
                        options: {
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }

                    });

                    //Updating number of tasks completed on the progress bar
                    const taskSpan = document.getElementById('taskSpan');
                    taskSpan.innerText = taskSum + '/' + totalTasks + ' tasks completed';
                    $('#taskProgress').attr('max', totalTasks);
                    $('#taskProgress').attr('value', taskSum);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log("Error: " + errorThrown);
                }
            });
        }

    };

    const employeeList = document.getElementById('team-list-container');
    const taskFormContainer = document.getElementById('taskFormContainer');
    const taskForm = document.getElementById('taskForm');

    const openTaskForm = (employeeId) => {
        document.getElementById('employeeId').value = employeeId;
        employeeList.style.transform = 'translateX(-60%)';
        taskFormContainer.style.right = '0';
        dashboardSearchFilter('', employeeId);
    };

    const closeTaskForm = () => {
        employeeList.style.transform = 'translateX(0)';
        taskFormContainer.style.right = '-60%';
    };

    document.getElementById('taskFormContainer').addEventListener('click', function() {
        event.stopPropagation();
    });
    document.getElementById('employeePage').addEventListener('click', closeTaskForm);


    taskForm.addEventListener('submit', (event) => {
        event.preventDefault();
        const formData = new FormData(event.target);
        console.log('Form data:', formData.get('employeeId'), formData.get('taskTitle'), formData.get('taskDescription'));

        $.ajax({
            url: 'fetch.php',
            type: 'POST',
            data: {
                action: 'createTask',
                title: formData.get('taskTitle'),
                desc: formData.get('taskDescription'),
                deadline: formData.get('taskDeadline'),

            },
            dataType: 'json',
            success: function(response) {
                // Handle success
                console.log("Server response:", response);
                alert("Login successful!");
            },
            error: function(xhr, status, error) {
                // Handle errors
                console.error("Error:", status, error);
            }
        });
    });
    await fetchTeamTasks(encodeURIComponent(projectID));
    dashboardSearchFilter();
    $.ajax({
        url: "fetch.php",
        type: "GET",
        data: {
            action: "fetchProjectName",
            projectId: encodeURIComponent(projectID)
        },
        success: function (data) {
            console.log(data);
            var stuff = JSON.parse(data);
            $('.team-name h1').text(stuff['ProjectName']);
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log(errorThrown);
        }
    });

});

function showCheckmark() {
    $('#checkmarkOverlay').fadeIn(1000, function () {
        $('#checkmarkOverlay').attr('style', 'display: flex;');
        $('.checkmark').show(10);
        setTimeout(function() {
            $('#checkmarkOverlay').fadeOut();
            $('.checkmark').fadeOut(1000);
        }, 1500);
    });
}

function getCookie(name) {

    const cookieName = name + "=";
    const decodedCookie = decodeURIComponent(document.cookie);
    console.log(decodedCookie);
    const cookieArray = decodedCookie.split(';');

    for (let i = 0; i < cookieArray.length; i++) {
        let cookie = cookieArray[i];

        while (cookie.charAt(0) === ' ') {
            cookie = cookie.substring(1);
        }

        if (cookie.indexOf(cookieName) === 0) {
            const value = cookie.substring(cookieName.length, cookie.length);
            console.log(`Found cookie "${name}" with value "${value}"`);
            return value;
        }

    }

    console.log(`Cookie "${name}" not found`);
    return "InvalidUser";

}

function createTask() {
    var taskTitle = $('#createTaskTitle').val();
    var taskDesc = $('#createTaskDesc').val();
    var taskDue = $('#createTaskDue').val();
    if (taskDue == "") {
        console.log("Non-selected duedate");
        return;
    }
    const urlParams = new URLSearchParams(window.location.search);
    const projectId = urlParams.get('id');

    $.ajax({
        url: "fetch.php",
        type: "GET",
        data: {
            action: "createTask",
            title: taskTitle,
            desc: taskDesc,
            deadline: taskDue,
            projectId: projectId
        },
        success: function (data) {
            if (data.trim() == "1") {
                showCheckmark();
                $('#createTaskOverlay').fadeOut(500);
                (async () => {
                    const urlParams = new URLSearchParams(window.location.search);
                    const projectId = urlParams.get('id');
                    await fetchTeamTasks(projectId);
                    dashboardSearchFilter('', currentSelectedId);
                })();
            }
            else { console.log(data);}
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log(errorThrown);
        }
    });

}
function assignTask() {
    console.log(currentSelectedId);
    $.ajax({
        url: "fetch.php",
        type: "GET",
        data: {
            action: "assignTask",
            taskId: currentSelectedTask,
            assignedId: currentSelectedId
        },
        success: function (data) {
            if (data.trim() == "1") {
                showCheckmark();
                $('#assignTaskOverlay').fadeOut(500);
                $('#assignEmployeeOverlay').fadeOut(500);
                const urlParams = new URLSearchParams(window.location.search);
                const projectId = urlParams.get('id');
                (async () => {
                    const urlParams = new URLSearchParams(window.location.search);
                    const projectId = urlParams.get('id');
                    await fetchTeamTasks(projectId);
                    dashboardSearchFilter('', currentSelectedId);
                })();
            }
            else { console.log(data);}
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log(errorThrown);
        }
    });

}

function getEmployees() {

    $.ajax({
        url: "fetch.php",
        type: "GET",
        data: {
            action: "getAllEmployees",
        },
        success: function (data) {
            console.log(data);
            var employees = JSON.parse(data);
            const employeeList = document.getElementById('assignEmployeeList');

            // Function to filter employees based on search input
            function filterEmployees(searchInput) {
                const filteredEmployees = employees.filter(employee => {
                    return employee.Username.toLowerCase().includes(searchInput.toLowerCase());
                });
                
                employeeList.innerHTML = '';

                filteredEmployees.forEach(employee => {
                    const listItem = document.createElement('div');
                    listItem.classList.add('employee-list-item');
                    listItem.id = "employee-" + employee.EmployeeID;

                    const profileImage = document.createElement('img');
                    profileImage.src = 'Assets/ProfilePic.png';
                    listItem.appendChild(profileImage);

                    const name = document.createElement('p');
                    name.innerText = employee.Username;
                    listItem.appendChild(name);

                    listItem.onclick = async () => {
                        currentSelectedId = employee.EmployeeID;
                        event.stopPropagation();
                        $('#assigntaskList').text('');
                        dashboardSearchFilter('', -2);
                        $('#assignTaskOverlay').fadeIn();
                    };
                    employeeList.appendChild(listItem);
                });
            }

            filterEmployees('');
            $('#assignEmployeeSearch').on('input', function() {
                const searchInput = $(this).val();
                filterEmployees(searchInput);
            });
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log(errorThrown);
        }
    });

}

function changeTeamLeader(selectedUser, selectedProject) {
    console.log(currentSelectedId);
    $.ajax({
        url: "fetch.php",
        type: "GET",
        data: {
            action: "changeTeamLead",
            userid: selectedUser,
            projectId: selectedProject
        },
        success: function (data) {
            if (data.trim() == "1") {
                showCheckmark();
            }
            else { console.log(data);}
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log(errorThrown);
        }
    });

}