function pinTask(id, togglePin) {
    $.ajax({
        url: "fetch.php",
        type: "GET",
        data: {
            action: "pinTask",
            id: id,
            togglePin: togglePin
        },
        success: function (data) {
            if (togglePin) togglePin = '1'
            else togglePin = '0';
            allTasks.changePin(id, togglePin);
            dropdownSearchFilter('neither');
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log(errorThrown);
        }
    });
}

let currentSelectedTask = "";
function confirmTask(final = 0) {
    $('#confirmButton').prop('disabled', true);
    $.ajax({
        url: "fetch.php",
        type: "GET",
        data: {
            action: "confirmTask",
            id: currentSelectedTask,
            final: final
        },
        success: function (data) {
            if (data.trim() == "SUCCESS") {
                showCheckmark();
                $('#confirmTaskOverlay').fadeOut(500, function () {
                    $('#confirmButton').prop('disabled', false)
                });
                $('#viewTaskOverlay').fadeOut(500, function () {
                    $('#confirmButton').prop('disabled', false)
                });

                $('#confirmLoading').hide();
                if (final) {
                    allTasks.changeStatus(currentSelectedTask, 2);
                    (async () => {
                        const urlParams = new URLSearchParams(window.location.search);
                        const projectId = urlParams.get('id');
                        await fetchTeamTasks(projectId);
                        dashboardSearchFilter('', currentSelectedId);
                    })();
                } else {
                    allTasks.changeStatus(currentSelectedTask, 1);
                    dropdownSearchFilter('neither');
                }
            }
            else { console.log(data);}
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log(errorThrown);
        }
    });
}

function rejectTask() {
    $('#confirmButton').prop('disabled', true);
    $.ajax({
        url: "fetch.php",
        type: "GET",
        data: {
            action: "rejectTask",
            id: currentSelectedTask
        },
        success: function (data) {
            if (data.trim() == "SUCCESS") {
                showCheckmark();
                $('#confirmTaskOverlay').fadeOut(500, function () {
                    $('#confirmButton').prop('disabled', false)
                });
                $('#viewTaskOverlay').fadeOut(500, function () {
                    $('#confirmButton').prop('disabled', false)
                });

                $('#confirmLoading').hide();
                allTasks.changeStatus(currentSelectedTask, 0);
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

function unassignTask() {
    $('#confirmButton').prop('disabled', true);
    $.ajax({
        url: "fetch.php",
        type: "GET",
        data: {
            action: "unassignTask",
            taskId: currentSelectedTask
        },
        success: function (data) {
            if (data.trim() == "1") {
                showCheckmark();
                $('#confirmTaskOverlay').fadeOut(500, function () {
                    $('#confirmButton').prop('disabled', false)
                });
                $('#viewTaskOverlay').fadeOut(500, function () {
                    $('#confirmButton').prop('disabled', false)
                });

                $('#confirmLoading').hide();
                allTasks.changeStatus(currentSelectedTask, 2);
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
async function loadTaskPage() {

    await loadPage();
    var searchType = document.getElementById('dropdownButtonSearch').innerHTML.trim();
    var filterType = document.getElementById('dropdownButtonFilter').innerHTML.trim();
    var query = document.getElementById('taskSearch').value;

    await fetchTasks();
    displayedTasks = allTasks.searchTasks(query, filterType, searchType);
    console.log(displayedTasks);
    var fadeInDelay = 100;
    for (let task of displayedTasks) {
        console.log(task);
        newElement2(task.id, task.title, task.duedate, task.pin, task.status, fadeInDelay);
        fadeInDelay+=100;
    }
    viewTask('1');
}

function viewTask(id, dashboard = 0) {
    for (let task of allTasks.getTasks()) {
        if (task.id == id) {
            console.log(task);
            document.getElementById("viewTaskTitle").innerText = task.title;
            document.getElementById("viewTaskDesc").innerText   = task.description;
            document.getElementById("viewTaskAssignedBy").innerText = task.assignedBy;
            document.getElementById("viewTaskProject").innerText = task.project;
            document.getElementById("viewTaskDuedate").innerText = task.duedate;
            $('#taskViewEmail p').html(task.email);
            document.getElementById('taskViewEmail').href = 'mailto:' + task.email;
            console.log(dashboard);
            if (dashboard) {
                var status = parseInt(task.status);
                if (status) {
                    console.log(task.status);
                    $('#viewAssignTask').text('Reject Submittion');
                    $('#viewAssignTask').click(function() {
                        $('#confirmTaskOverlayText').text("Reject the submittion of the task?");
                        $('#confirmTaskOverlay').fadeIn();
                        $('#confirmButton').off('click');
                        $('#confirmButton').click(function() {
                            rejectTask();
                            $('#confirmLoading').show();
                        });
                    });

                    $('#viewCheckTask').text('Accept Submittion');
                    $('#viewCheckTask').click(function() {
                        $('#confirmTaskOverlayText').text("Accept the submittion of the task?");
                        $('#confirmTaskOverlay').fadeIn();
                        $('#confirmButton').off('click');
                        $('#confirmButton').click(function() {
                            confirmTask(1);
                            $('#confirmLoading').show();
                        });
                    });

                } else {
                    console.log(0);
                    $('#viewAssignTask').text('Un-assign');
                    $('#viewAssignTask').click(function() {
                        $('#confirmTaskOverlayText').text("Un-assign the task?");
                        $('#confirmTaskOverlay').fadeIn();
                        $('#confirmButton').off('click');
                        $('#confirmButton').click(function() {
                            unassignTask(currentSelectedTask);
                        });
                    });


                    $('#viewCheckTask').text('Check and Submit');
                    $('#viewCheckTask').click(function() {
                        $('#confirmTaskOverlayText').text("Check and accept the submittion of the task?");
                        $('#confirmTaskOverlay').fadeIn();
                        $('#confirmButton').off('click');
                        $('#confirmButton').click(function() {
                            confirmTask(1);
                            $('#confirmLoading').show();
                        });
                    });

                }
            }
        }
    }
}


function formatDate(dateString) {
    const date = new Date(dateString);
    const year = date.getFullYear();
    const month = (date.getMonth() + 1).toString().padStart(2, '0'); // Months are zero-based
    const day = date.getDate().toString().padStart(2, '0');
    return `${year}-${month}-${day}`;
};
function dateToString(dateObject, format = "ddmmyyyy") {
    if (format === "ddmmyyyy") {
        var day = dateObject.getDate();
        var month = dateObject.getMonth() + 1;
        var year = dateObject.getFullYear();
        var dateString = day + "/" + month + "/" + year;
        return dateString;
    }
}

//Adding new tasks
function newElement(idName) {
    var ul = document.getElementById(idName);

    //creates unique id

    var li = document.createElement('button');
    var uniqueId = "task-" + new Date().getTime();
    var extractedInfoTitle;
    var extractedInfoDesc;
    var extractedInfoDate;
    var extractedInfoProject;

    extractedInfoTitle =   $("#createTaskTitle").val();
    extractedInfoDesc =    $("#createTaskDesc").val();
    extractedInfoProject = $("#createTaskProject").val();
    extractedInfoDate =    $("#createTaskDue").val();
    extractedDateObject = new Date(extractedInfoDate);
    currentDate = new Date();

    var taskDiv = document.createElement('div');
    taskDiv.setAttribute("class", "grid-container task-node");
    li.id = uniqueId;
    li.setAttribute("draggable", "true");

    li.style.background = "none";
    li.style.border = "none";

    var taskColour = document.createElement('div');
    taskColour.setAttribute("class", "rectangle");
    if ((extractedDateObject - currentDate) / 86400000 <= 0) {
        taskColour.setAttribute("style", "background-color: red;");
    } else if ((extractedDateObject - currentDate) / 86400000 <= 7) {
        taskColour.setAttribute("style", "background-color: orange;");
    } else {
        taskColour.setAttribute("style", "background-color: green;");
    };
    taskDiv.appendChild(taskColour);

    var taskInnerDiv1 = document.createElement('div');
    taskInnerDiv1.setAttribute("class", "task-div-1");

    var taskTitle = document.createElement('h3');
    taskTitle.innerText = extractedInfoTitle;
    taskInnerDiv1.appendChild(taskTitle);

    var taskDesc = document.createElement('p');
    taskDesc.setAttribute("class", "task-description");
    if (extractedInfoDesc.length < 250) {
        taskDesc.innerText = extractedInfoDesc;
    }
    else {
        taskDesc.innerText = extractedInfoDesc.substring(0, 190) + "...";
    }

    taskInnerDiv1.appendChild(taskDesc);

    /*******/

    var taskInnerDiv2 = document.createElement('div');
    taskInnerDiv2.setAttribute("class", "task-div-2");

    var taskProject = document.createElement('p');
    taskProject.setAttribute("class", "task-project");
    taskProject.innerText = extractedInfoProject;
    taskInnerDiv2.appendChild(taskProject);

    var stopwatchIcon = document.createElement('img');
    stopwatchIcon.setAttribute("src", "Assets/svg-icons/stopwatch.svg");
    stopwatchIcon.setAttribute("width", "25");
    stopwatchIcon.setAttribute("height", "25");
    stopwatchIcon.setAttribute("style", "float: right; margin-right: 8px;");
    taskInnerDiv2.appendChild(stopwatchIcon);

    var taskDue = document.createElement('u');
    /* extractedInfoDate = $("#createTaskDue").val(); */
    taskDue.setAttribute("class", "task-duedate");
    taskDue.innerText = extractedInfoDate.toString();
    taskInnerDiv2.appendChild(taskDue);

    /*******/

    taskDiv.appendChild(taskInnerDiv1);
    taskDiv.appendChild(taskInnerDiv2);
    $("#createTaskOverlay").fadeOut();

    li.addEventListener("click", function () {
        selectedID = this.id;
        console.log(selectedID);

        clearOverlay();

        $('#createTaskOverlay').fadeIn();
        keepInfoOverlay(extractedInfoTitle, extractedInfoDesc, extractedInfoDate, extractedInfoProject);
        $('#createTaskButton').hide();
        $('#editTaskButton').show();
    });
    li.appendChild(taskDiv);
    ul.appendChild(li);
};


function clearOverlay() {
    document.getElementById("createTaskTitle").value = "";
    document.getElementById("createTaskDesc").value = "";
    document.getElementById("createTaskDue").value = null;
    document.getElementById("createTaskProject").value = "0 - No Project";
};
function keepInfoOverlay(title, desc, date, project) {
    document.getElementById("createTaskTitle").value = title;
    document.getElementById("createTaskDesc").value = desc;
    document.getElementById("createTaskProject").value = project;
    document.getElementById("createTaskDue").value = formatDate(date);
};
function editElement() {

    var extractedInfoTitle;
    var extractedInfoDesc;
    var extractedInfoDate;
    var extractedInfoProject;

    extractedInfoTitle = $("#createTaskTitle").val();
    extractedInfoDesc = $("#createTaskDesc").val();
    extractedInfoProject = $("#createTaskProject").val();
    extractedInfoDate = $("#createTaskDue").val().toString();

    console.log($("#"+ selectedID + " h3").text());
    $("#"+ selectedID + " h3").text(extractedInfoTitle);
    $("#"+ selectedID + " .task-description").text(extractedInfoDesc);
    $("#"+ selectedID + " .task-project").text(extractedInfoProject);
    $("#"+ selectedID + " .task-duedate").text(extractedInfoDate);

    $('#createTaskOverlay').fadeOut();
    clearOverlay();

};


//Clear Overlay Elements
function clearOverlay() {
    document.getElementById("createTaskTitle").value = "";
    document.getElementById("createTaskDesc").value = "";
    document.getElementById("createTaskDue").value = null;
    document.getElementById("createTaskProject").value = "0 - No Project";
};

function keepInfoOverlay(title, desc, date, project) {
    document.getElementById("createTaskTitle").value = title;
    document.getElementById("createTaskDesc").value = desc;
    document.getElementById("createTaskProject").value = project;
    document.getElementById("createTaskDue").value = formatDate(date);
};



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

