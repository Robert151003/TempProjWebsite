

function showDetails(projectID) {
    //Navigate to a new page with project details
    window.location.href = `dashboard.html?id=${projectID}`; 
}

$(document).ready(function() {
    $('#teamLeader').select2({
        placeholder: "Select a team leader",
        allowClear: true,
        ajax: {
            url: 'get_team_member_list.php', 
            dataType: 'json',
            delay: 250,
            processResults: function (data) {
                var modifiedData = $.map(data, function (obj) {
                    return {
                        id: obj.EmployeeID || "", // Use EmployeeID or a unique identifier
                        text: obj.Username || "" // Use Username or the actual text you want to show
                    };
                });
            
                // Prepend "None" option
                modifiedData.unshift({ id: 0, text: "None" });
                console.log(modifiedData);
            
                return {
                    results: modifiedData
                };
            },
            cache: true
        }
    });

    //Pre-select "None"
    $('#teamLeader').val("").trigger('change');

    //Handle form submission
    $('#projectForm').on('submit', function(e) {
        e.preventDefault(); // Prevent default form submission

        //Serialize the form data. If teamLeader is 'None', send null value
        var formData = $(this).serializeArray();
        formData = formData.map(function(item) {
            if (item.name === 'teamLeader' && item.value === 'None') {
                item.value = null;
            }
            return item;
        });

        //Send the form data using AJAX
        $.ajax({
            type: "POST",
            url: "manager_add_project.php",// Your PHP file to handle the insertion
            data: formData,
            success: function(response) {
                //Handle success (maybe redirect or display a success message)
                console.log("Project added successfully", response);
                $("#createTaskOverlay").fadeOut();

            },
            error: function(xhr, status, error) {
                //Handle errors
                console.error("An error occurred", xhr, status, error);
            }
        });
    });


    $("#closeCreateTask").click(function(){

        $("#createTaskOverlay").fadeOut();

    });

    

});

function keepInfoOverlay(title, desc) {

    document.getElementById("projectName").value = title;
    document.getElementById("createTaskDesc").value = desc;

};
function clearOverlay() {


    const projectNameInput = document.getElementById("projectName");
    const projectDescriptionInput = document.getElementById("createTaskDesc");

    // Check if the elements exist before setting their values
    if (projectNameInput !== null) {
        projectNameInput.value = '';
    } else {
        console.error('Element with ID "project-name" not found');
    }

    if (projectDescriptionInput !== null) {
        projectDescriptionInput.value = '';
    } else {
        console.error('Element with ID "project-description" not found');
    }
    //document.getElementById("project-deadline").value = null;

};

var activeId;
console.log($('.project-card'));
$('.project-card').click(function(event) {
    activeId = this.id;
    console.log(activeId);
});

//Ajax request to edit a project and update the database
function editElement(){

    var title = document.getElementById("projectName").value
    var description = document.getElementById("createTaskDesc").value
    var teamLeader = document.getElementById("teamLeader");
    var selectedOption = teamLeader.options[teamLeader.selectedIndex];
    var id = selectedOption.value;
    var date = document.getElementById("projectDeadline").value.toString();


    $("#createTaskOverlay").fadeOut();



    $.ajax({
        url: "manager_edit_project.php",
        type: "POST",
        data: {
            project_title: title,
            project_description: description,
            project_teamLeader: id,
            project_date: date,
            project_id: activeId
        },
    success: function(data) {
        console.log(data);
        },
    error: function(jqXHR, textStatus, errorThrown) {
        console.log("Error: " + errorThrown);
        }
    });

    window.location.reload();
}

//Ajax request to delete a project and update the database
function deleteElement() {

    $("#createTaskOverlay").fadeOut();

    $.ajax({
        url: "manager_delete_project.php",
        type: "POST",
        data: {
            project_id: activeId
        },
        success: function (data) {
            console.log(data);
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log("Error: " + errorThrown);
        }
    });

    window.location.reload();
}

