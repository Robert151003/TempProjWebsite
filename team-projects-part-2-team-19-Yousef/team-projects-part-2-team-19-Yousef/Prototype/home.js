function fetchHomePosts() {
    $.ajax({
        url: "fetch.php",
        type: "GET",
        data: {
            action: "fetchHomePosts",
        },
        success: function (data) {
            var postsArray = JSON.parse(data);
            const carouselInner = document.getElementById("carousel-inner");

            // Clear existing carousel items
            carouselInner.innerHTML = '';

            // Create and append carousel items for each post
            postsArray.forEach((post, index) => {
                CreatePost(post.Title, post.Content);

                // Set the first post as active
                if (index === 0) {
                    const carouselItems = carouselInner.querySelectorAll('.carousel-item');
                    carouselItems[index].classList.add('active');

                }
            });
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log(errorThrown);
        }
    });
}

function fetchHomeTasks() {
    $.ajax({
        url: "fetch.php",
        type: "GET",
        data: {
            action: "fetchHomeTasks",
        },
        success: function (data) {
            var tasksArray = JSON.parse(data);
            const pinnedTasksList = $("#pinnedTasks");
            pinnedTasksList.html("");

            tasksArray.forEach((task, index) => {
                var li = $(document.createElement("li"));
                var taskString = task.title + " | " + task.project + " | " + task.duedate;
                li.text(taskString);
                pinnedTasksList.append(li);
            })},
        error: function (jqXHR, textStatus, errorThrown) {
            console.log(errorThrown);
        }
    });
}