// Encapsulated constant that contains extracted/fetched tasks from the database. Encapsulated for security reasons
let allTasks = {};

function dropdownSearchFilter(type, selected = '') {
    var selectedTasks = [];
    document.getElementById('taskList').innerHTML = '';
    if (type == 'filter') {
        document.getElementById('dropdownButtonFilter').innerHTML = selected;
        var searchType = document.getElementById('dropdownButtonSearch').innerHTML.trim();
        var searchQuery = document.getElementById('taskSearch').value.trim();
        selectedTasks = allTasks.searchTasks(searchQuery, selected, searchType);
    }
    else if (type == 'search') {
        document.getElementById('dropdownButtonSearch').innerHTML = selected;
        var filterType = document.getElementById('dropdownButtonFilter').innerHTML.trim();
        var searchQuery = document.getElementById('taskSearch').value.trim();
        selectedTasks = allTasks.searchTasks(searchQuery, filterType, selected);
    }
    else if (type == 'neither') {
        var searchType = document.getElementById('dropdownButtonSearch').innerHTML.trim();
        var filterType = document.getElementById('dropdownButtonFilter').innerHTML.trim();
        var searchQuery = document.getElementById('taskSearch').value.trim();
        selectedTasks = allTasks.searchTasks(searchQuery, filterType, searchType);
    }

    for (let task of selectedTasks) {
        newElement2(task.id, task.title, task.duedate, task.pin, task.status);
    }
}

function employeeTasks(listOfTasks, id) {
    return listOfTasks.filter(task => task.assignedTo == id);
}

async function fetchTasks() {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: "fetch.php",
            type: "GET",
            data: {
                action: "loadTasks"
            },
            success: function (data) {
                allTasks = (function () {
                    var tasks = JSON.parse(data);

                    // Variable to store tasks
                    function pinnedFirstSubmittedLast(listOfTasks) {
                        let pinnedDue = [];
                        let unpinnedDue = [];
                        let pinnedSubmitted = [];
                        let unpinnedSubmitted = [];

                        // Orders tasks by pinned normal, then pinned submitted (greyed out), then unpinned normal, then unpinned submitted
                        for (let task of listOfTasks) {
                            if (task.pin === "1") {
                                if (task.status === '1') {
                                    pinnedSubmitted.push(task);
                                }
                                else {
                                    pinnedDue.push(task);
                                }
                            }
                            else {
                                if (task.status === '1') {
                                    unpinnedSubmitted.push(task);
                                }
                                else {
                                    unpinnedDue.push(task);
                                }
                            }
                        }
                        // Concatenate the arrays and return the result
                        let result = pinnedDue.concat(pinnedSubmitted).concat(unpinnedDue).concat(unpinnedSubmitted);
                        return result
                    }

                    // Private function to filter tasks
                    function filterTasks(condition) {
                        if (condition == "All" || condition == "Show") return pinnedFirstSubmittedLast(getTasks());
                        var currentDate = new Date();
                        return pinnedFirstSubmittedLast(getTasks()).filter(task => {
                            // Convert the due date string to a JavaScript Date object
                            var dueDate = new Date(task.duedate);

                            // Overdue case
                            if (condition == "Overdue") return dueDate <= currentDate;

                            var until = new Date();

                            // Every other duedate case
                            if (condition == "Next 7 days") until.setDate(currentDate.getDate() + 7);
                            else if (condition == "Next 30 days") until.setDate(currentDate.getDate() + 30);
                            else if (condition == "Next 3 months") until.setDate(currentDate.getDate() + 90);
                            else if (condition == "Next 6 months") until.setDate(currentDate.getDate() + 180);
                            else console.log("wtf");

                            // Check if the due date is within the next week
                            return dueDate >= currentDate && dueDate <= (until);
                        });
                    }

                    function getTasks() {
                        return tasks;
                    }

                    function searchTasks(query, filter = 'All', type) {
                        const filteredTasks = pinnedFirstSubmittedLast(filterTasks(filter));
                        //var searchInput = document.getElementById('taskSearch');
                        var searchInput = query;

                        if (type == 'Title' || type == 'Search By')
                            return filteredTasks.filter(task => task.title.toLowerCase().includes(searchInput.toLowerCase()));
                        else if (type == 'Project')
                            return filteredTasks.filter(task => task.project.toLowerCase().includes(searchInput.toLowerCase()));
                    }

                    function changePin(id, status) {
                        for (let task of tasks) {
                            if (task.id === id) {
                                task.pin = status;
                            }
                        }
                    }
                    function changeStatus(id) {
                        for (let task of tasks) {
                            if (task.id === id) {
                                task.status = '1';
                            }
                        }
                    }

                    return {
                        getTasks,
                        searchTasks,
                        filterTasks,
                        changeStatus,
                        changePin
                    };
                })();
                resolve(1);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log("Error: " + errorThrown);
                reject(0);
            }
        });
    })
};

//-------------------------------- Task Extract for dashboard ------------------------------//

function loadTeamTasks() {
    $.ajax({
        url: "fetch.php",
        type: "GET",
        data: {
            action: "loadTeamTasks",
            projectId: 1
        },
        success: function (data) {
            console.log(data);
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log(errorThrown);
        }
    });
}


function dashboardSearchFilter(selected = '', userid = -1) {
    var selectedTasks = [];
    var assigned = 1;
    document.getElementById('taskList').innerHTML = '';
    var searchQuery = document.getElementById('taskSearch').value.trim();
    selectedTasks = allTasks.searchTasks(searchQuery, 'Show', 'Title');
    console.log("ran");

    if (userid != -1 && userid != -2) {
        selectedTasks = employeeTasks(selectedTasks, userid);
    } else if (userid == -2) {
        selectedTasks = selectedTasks.filter(task => task.assignedTo == null);
        console.log(selectedTasks);
        assigned = 0;
    }

    for (let task of selectedTasks) {
        newElement3(task.id, task.title, task.duedate, task.pin, task.status, 100, assigned);
    }
}


async function fetchTeamTasks(projectId) {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: "fetch.php",
            type: "GET",
            data: {
                action: "loadTeamTasks",
                projectId: projectId
            },
            success: function (data) {

                allTasks = (function () {
                    var tasks = JSON.parse(data);
                    console.log(tasks);
                    // set highlighted user if they have submitted a task
                    var employees = $(".employee-list-item");
                    for (let task of tasks) {
                        if (task.status == 1) {
                            for (let employee of employees) {
                                if ((employee).id.replace("employee-", "") == task.assignedTo) {
                                    $(employee).css("border", "2px #1FA604 solid");
                                    console.log(employee.id);
                                };
                            }
                        }
                    }

                    // Variable to store tasks
                    function submittedFirst(listOfTasks) {
                        let submitted = [];
                        let unsubmitted = [];
                        let finished = [];

                        // Orders tasks by pinned normal, then pinned submitted (greyed out), then unpinned normal, then unpinned submitted
                        for (let task of listOfTasks) {
                            if (task.status === "1") {
                                submitted.push(task);
                            }
                            else if (task.status === "0"){
                                unsubmitted.push(task);
                            }
                            else {
                                finished.push(task);
                            }
                        }
                        // Concatenate the arrays and return the result
                        let result = submitted.concat(unsubmitted).concat(finished);
                        return result
                    }

                    // Private function to filter tasks
                    function filterTasks(condition) {
                        if (condition == "All" || condition == "Show") return submittedFirst(getTasks());
                        var currentDate = new Date();
                        return submittedFirst(getTasks()).filter(task => {
                            // Convert the due date string to a JavaScript Date object
                            var dueDate = new Date(task.duedate);

                            // Overdue case
                            if (condition == "Overdue") return dueDate <= currentDate;

                            var until = new Date();

                            // Every other duedate case
                            if (condition == "Next 7 days") until.setDate(currentDate.getDate() + 7);
                            else if (condition == "Next 30 days") until.setDate(currentDate.getDate() + 30);
                            else if (condition == "Next 3 months") until.setDate(currentDate.getDate() + 90);
                            else if (condition == "Next 6 months") until.setDate(currentDate.getDate() + 180);
                            else console.log("wtf");

                            // Check if the due date is within the next week
                            return dueDate >= currentDate && dueDate <= (until);
                        });
                    }

                    function getTasks() {
                        return tasks;
                    }

                    function searchTasks(query, filter = 'All', type = "Title") {
                        const filteredTasks = submittedFirst(filterTasks(filter));
                        //var searchInput = document.getElementById('taskSearch');
                        var searchInput = query;

                        if (type == 'Title' || type == 'Search By')
                            return filteredTasks.filter(task => task.title.toLowerCase().includes(searchInput.toLowerCase()));
                        else if (type == 'Project')
                            return filteredTasks.filter(task => task.project.toLowerCase().includes(searchInput.toLowerCase()));
                    }

                    function changePin(id, status) {
                        for (let task of tasks) {
                            if (task.id === id) {
                                task.pin = status;
                            }
                        }
                    }
                    function changeStatus(id, status) {
                        for (let task of tasks) {
                            if (task.id === id) {
                                task.status = status;
                            }
                        }
                    }

                    return {
                        getTasks,
                        searchTasks,
                        filterTasks,
                        changeStatus,
                        changePin
                    };
                })();
                resolve(1);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log("Error: " + errorThrown);
                reject(0);
            }
        });
    })
};
