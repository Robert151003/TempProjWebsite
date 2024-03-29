<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Dashboard</title>
    <link rel="stylesheet" href="managerDashboard.css">
    <link rel="stylesheet" href="stylesheet.css">

    <!-- External scripts such as Bootstrap -->
    <link rel = "stylesheet" href = "https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity = "sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin = "anonymous">

    <script src = "https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src = "https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js" integrity = "sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin = "anonymous"></script>
    <script src = "https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js" integrity = "sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin = "anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <link rel = "stylesheet" href = "https://site-assets.fontawesome.com/releases/v6.4.0/css/all.css">
    <link rel = "stylesheet" href = "https://site-assets.fontawesome.com/releases/v6.4.0/css/sharp-solid.css">
    <link rel = "stylesheet" href = "https://site-assets.fontawesome.com/releases/v6.4.0/css/sharp-regular.css">
    <link rel = "stylesheet" href = "https://site-assets.fontawesome.com/releases/v6.4.0/css/sharp-light.css">
    <link rel = "stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" />

    <link rel = "icon" href = "Assets/Logo.ico" type = "image/x-icon">

    
</head>
<body>

        <script src = "scripting.js"></script>
        <script src = "navBar.js"></script>
        <script src = "managerDashboard.js" defer></script>

        <?php session_start(); 
	if ($_SESSION['perm'] != 'Manager') {
	    echo "invalid access";
	    return;
	}
	?>

        <!-- Header for the dashboard page -->
        <div class="manager-header-container">

            <!-- Search Bar -->
            <div class = "input-group-prepend">

                <button class = "btn btn-light" id = "searchPostButton" onclick = "LoadPosts()">

                    <span class = "input-group-text" id = "basic-addon1">

                        <img src = "Assets/search.png" width = "25" height = "25">

                    </span>

                </button>

                <input type = "text" id = "searchPost" class = "form-control" placeholder = "Search projects...">

            </div>

            <h1 class="projects-title">Projects</h1>

            <!-- Add Projects Button -->
            <div class="add-project-button-container">
                <button class="add-project-button" 
                    onclick="$('#createTaskButton').show(); 
                    $('#editTaskButton').hide();
                    $('#deleteTaskButton').hide();  
                    $('#createTaskOverlay').fadeIn();
                    clearOverlay(); 
                    columnType('todoList');" value="+Element">
                 Add Project
                </button>
            </div>

        </div>

        <!-- Project Container with dynamically increasing project list -->
        <div class="project-container">
            <?php
include 'get_project_list.php';
$projects = getProjects();

/* Creating each project card in the list */
if (count($projects) > 0) {
    foreach ($projects as $project) {
        $projectID = $project['ProjectID'];

        echo "<div id='$projectID' class='project-card' onclick=\"$('#createTaskOverlay').fadeIn(); $('#createTaskButton').hide(); $('#editTaskButton').show(); $('#deleteTaskButton').show(); clearOverlay(); keepInfoOverlay('{$project["ProjectName"]}', '{$project["Description"]}')\">";
        echo "<div class='project-header'>";
        echo "<h2>" . htmlspecialchars($project["ProjectName"]) . "</h2>";
        echo "<button class='details-btn' onclick='showDetails(" . $project["ProjectID"] . ")'>Details</button>";
        echo "</div>";
        echo "<p class='project-description'>" . htmlspecialchars($project["Description"]) . "</p>";
        echo "<p class='project-deadline'>Deadline: " . $project["Deadline"] . "</p>";
	echo "<p class='project-leader'>Team Leader: " . $project['Username'] . "</p>";
        echo "</div>";
    }
} else {
    echo "No projects found";
}
?>
        </div>

        <!-- Project Creation Overlay -->
        <div id = "createTaskOverlay" class = "todoOverlay">

            <div>
                <form method="post" id = "projectForm" action="manager_add_project.php">
                    <div id="createTask">

                        <div class="input-group mb-3">

                            <h5 style="margin: 25px 0px 10px 20px; width: 90%;"> Project Name </h5>
                            <a id="closeCreateTask" style="width: 5%;">
                                <img src="Assets/svg-icons/arrow-left.svg" class="clickable-image" alt="CollapseIcon" width="20" height="20" style="filter: invert(1); margin: auto;">
                            </a>
                            <input type="text" id="projectName" name="title" class="form-control" maxlength="50" style="margin: 0px 40px 15px 20px; width: 700px" aria-label="Default" aria-describedby="inputGroup-sizing-default">
                            <h5 style="margin: 0px 0px 10px 20px; width: 100%;"> Project Description </h5>
                            <textarea id="createTaskDesc" name="description" style="margin: 0px 40px 10px 20px; width: 740px; height: 150px; resize: none;" aria-label="With textarea"></textarea>

                            <h5 style="margin: 0px 0px 10px 20px; width: 100%;"> Team Leader </h5>
                            <div class="input-group mb-3" style="margin: 0px 0px 10px 20px; width: 50%; z-index: 9999;">
                                <select id="teamLeader" name="teamLeader" style="width: 100%;" required></select>
                            </div>

                            <h5 style="margin: 0px 0px 10px 20px; width: 100%;"> Due Date </h5>
                            <input id="projectDeadline" name="date" style="margin: 0px 40px 10px 20px; width: 300px;" type="date" />


                            <button id="createTaskButton" type="submit" class="btn btn-success" style="margin:auto; display: block;" /*onclick ="newElement(columnName);*/">Create Project</button>
                            <button id="editTaskButton" type="button" class="btn btn-success" style="margin:auto;" onclick="editElement();">Edit Project</button>
                            <button id="deleteTaskButton" type="button" class="btn btn-danger" style="margin:auto;" onclick="deleteElement();">Delete Project</button>
                        </div>

                    </div>
                </form>
            </div>

        </div>


</body>
</html>

