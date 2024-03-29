function allowDrop(event) {

    event.preventDefault();

}

function drag(event) {

    event.dataTransfer.setData("text", event.target.id);

}

function drop(event) {
    event.preventDefault();
    var data = event.dataTransfer.getData("text");
    var draggedElement = document.getElementById(data);
    var targetList = event.currentTarget.querySelector("ul");
    if (targetList) {
        targetList.appendChild(draggedElement);
        // Get the task ID and the new status
        var taskId = draggedElement.id;
        var newStatus = event.currentTarget.id; // Assuming the list IDs match the status
        // Make an AJAX call to update the task status
        $.ajax({
            type: "POST",
            url: "To-DoListTaskMover.php", 
            data: {
                task_id: taskId,
                status: newStatus

            },
            success: function(response) {
                // Handle the response from the server if needed
                console.log("Task status updated:", response);
            },
            error: function(xhr, status, error) {
                // Handle errors
                console.error("Update failed:", error);
            }
        });
    } else {
        console.error("UL element not found in drop target");
    }
}




function moveElement() {
    var extractedInfoStatus = "InProgress"; // For example, get the status from user input or application logic

    var postData = {
        status: extractedInfoStatus
    };

    // Send the data to the server using AJAX
    $.ajax({
        type: "POST",
        url: "To-DoListTaskMover.php",
        data: postData,
        success: function (response) {
            // Handle the response from the server if needed
            console.log("Update successful:", response);
        },
        error: function (xhr, status, error) {
            // Handle errors
            console.error("Update failed:", error);
        }
    });

    $('#createTaskOverlay').fadeOut();
    clearOverlay();
}
