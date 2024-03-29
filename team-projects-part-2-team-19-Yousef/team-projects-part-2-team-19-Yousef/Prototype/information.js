const httpRequest = new XMLHttpRequest();
const topicRequest = new XMLHttpRequest();
var savedPosts = []; //Stores all loaded posts
var myPosts = false;
var userPromise = checkSession(); //Login, UserData = [id,username,permission]
var searchTerm = "";
var editPostID = 0;
var userData = [];
var availableTopics = [];
var currentTopic = "all";

userPromise.then(function(data) {
    //This function will be called when the promise is resolved
    userData = data;
}).catch(function(error) {
    //This function will be called if there is an error in the promise chain
    console.error("Error:", error);
});


function CreatePost(subject, description, postNum, employeeID, username) {

    //Create div
    const postDiv = document.createElement("div");
    postDiv.className = "post";
    postDiv.style.cursor = "pointer";
	
    //Create post title (subject)
    const postTitle = document.createElement("h2");
    const titleText = document.createTextNode(subject);
	postTitle.style.display = "inline";

	//Create post author
    const postAuthor = document.createElement("p");
    const authorText = document.createTextNode(username + "\n");

    //Create post description
    const postDescription = document.createElement("p");
    const descriptionText = document.createTextNode(description+".....");

    //Join elements together and add to html
	
    postTitle.appendChild(titleText);
	postAuthor.appendChild(authorText);
    postDescription.appendChild(descriptionText);
    postDiv.appendChild(postTitle);
	//Create edit Button
	if (employeeID == userData[0]){ //userData['id']
		const editButton = document.createElement("Button");
		editButton.style.cssFloat = "right";
		editButton.classList.add("btn","btn-primary");
		const editButtonText = document.createTextNode("edit");
	
		editButton.addEventListener('click', function(event) {
			SetEditPostVis(true);
			document.getElementById("editPostTitle").value = subject;	
			document.getElementById("editPostDesc").value = description;
			editPostID = postNum;
		})
		editButton.appendChild(editButtonText);
		postDiv.appendChild(editButton);
	}
	
	postDiv.appendChild(postAuthor);
    postDiv.appendChild(postDescription);
    postTitle.style.textDecoration = "underline";

    const postsBlock = document.getElementById("postsBlock");

    //Set the maximum width to match the width of the parent container
    const parentWidth = window.getComputedStyle(postsBlock).getPropertyValue("width");
    postDiv.style.maxWidth = parentWidth;

    //Allow text to break and wrap to a new line
    postDescription.style.wordWrap = "break-word";

    //Post page url
    postDiv.addEventListener('click', function(event) {
    //Check if the clicked element is not a button
    if (event.target.tagName !== 'BUTTON') { //Exclude button from post click event
        //Execute your desired action here
        window.location.href = 'post.html?post='+postNum;
    }
});

    //Append the postDiv to the postsBlock
    postsBlock.appendChild(postDiv);

}

function SetCreateTopicVis(visible) {
    document.getElementById("createTopicOverlay").style.display = (visible) ? "block" : "none";
}

function SetCreatePostVis(visible) {
    document.getElementById("createPostOverlay").style.display = (visible) ? "block" : "none";
}

function SetEditPostVis(visible) {
    document.getElementById("editPostOverlay").style.display = (visible) ? "block" : "none";
}

function LoadPosts(){ //Called when web page refreshed, retreives all posts from database
	searchTerm = document.getElementById("searchPost").value;
    //retreive data from database by sending an httpRequest
    httpRequest.open("POST", "informationServer.php", true);
    httpRequest.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	httpRequest.send("TITLE="+searchTerm+"&DESC=none"+"&ACTION=getPosts"+"&EMPLOYEEID="+(myPosts ? userData[0] : "none")+"&CURRENTTOPIC="+currentTopic);
}

document.getElementById("submitPostButton").addEventListener("click", function() { //add post

    var title = document.getElementById("postTitle").value;
    var desc = document.getElementById("postDesc").value;
	var topic = document.getElementById("topicDropdownButton").innerText;
    if (title == "" || desc == ""){
		alert("Title and contents must both be filled in!");
		return;
	}
	SetCreatePostVis(false);
    //add to database by sending an httpRequest
    httpRequest.open("POST", "informationServer.php", true);
    httpRequest.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    httpRequest.send("TITLE=" +searchTerm+"&DESC="+desc+"&ACTION=addPost"+"&EMPLOYEEID="+(myPosts ? userData[0] : "none")
	+"&NEWTITLE="+title+"&CURRENTTOPIC="+currentTopic+"&POSTTOPIC="+topic);

});

function clearOverlay() {

    document.getElementById("postTitle").value = "";
    document.getElementById("postDesc").value = "";

};
httpRequest.onreadystatechange = function() { //Response from server, contains posts received from database
    if (this.readyState == 4 && this.status == 200) {
        var posts = this.responseText;
		if (posts == ""){
			return;
		}
		posts = JSON.parse(posts);
		const postsBlock = document.getElementById("postsBlock");
		postsBlock.innerHTML = "";
		posts.forEach(function(post) {
            //Access each JSON object
            CreatePost(post.Title,post.Content,post.PostID,post.EmployeeID,post.Username);
        });
		GetTopics();
		document.getElementById("filterDisplayText").innerText = (myPosts ? "My Posts" : "All Posts") + "/" + currentTopic;
    };
};

topicRequest.onreadystatechange = function() { //Response from server, contains topics received from database
    if (this.readyState == 4 && this.status == 200) {
        var topics = this.responseText;
		if (topics == ""){
			return;
		}
		topics = JSON.parse(topics);
		topics.forEach(function(topicJSON) {
            //Access each JSON object
            availableTopics.push(topicJSON.topic);
        });
		DisplayTopics();
    };
};

function SetMyPosts(newValue){
	myPosts = newValue;
	LoadPosts();
};

var searchInput = document.getElementById("searchPost");

//Add event listener for the keydown event
searchInput.addEventListener("keydown", function(event) {
    //Check if the pressed key is the Enter key (key code 13)
    if (event.keyCode === 13) {
        LoadPosts();
    }
});

function DeletePost(){
	httpRequest.open("POST", "informationServer.php", true);
    httpRequest.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	httpRequest.send("TITLE="+searchTerm+"&DESC="+"&ACTION=deletePost"+"&EMPLOYEEID="+(myPosts ? userData[0] : "none")+"&POSTID="+editPostID+"&CURRENTTOPIC="+currentTopic);
	SetEditPostVis(false);
}

function EditPost(){
	description = document.getElementById("editPostDesc").value;
	title = document.getElementById("editPostTitle").value;
	httpRequest.open("POST", "informationServer.php", true);
    httpRequest.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	httpRequest.send("TITLE="+searchTerm+"&DESC="+description+"&ACTION=editPost"+"&EMPLOYEEID="+(myPosts ? userData[0] : "none")
	+"&POSTID="+editPostID+"&NEWTITLE="+title+"&CURRENTTOPIC="+currentTopic);
	SetEditPostVis(false);
}

function GetTopics(){
	availableTopics = [];
	topicRequest.open("POST", "topic.php", true);
    topicRequest.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    topicRequest.send("ACTION=GetTopics");
};

function DisplayTopics(){
	topicsDiv = document.getElementById("addTopicsDiv");
	topicsDropdown = document.getElementById("topicDropdown");
	topicsDiv.innerHTML = "";
	topicsDropdown.innerHTML = "";
	
	//Topics on side
	const li = document.createElement('li');
	const buttonlbl = document.createElement('button');
	const underline = document.createElement('u');
	const buttonText = document.createTextNode("All");
	
	buttonlbl.addEventListener('click', function() {
			currentTopic = "all";
			LoadPosts();
	});
	//Add the text node to the <u> element
	underline.appendChild(buttonText);
	buttonlbl.appendChild(underline);
	li.appendChild(buttonlbl);
	topicsDiv.appendChild(li);
	
	//Topics in create post dropdown
	const li2 = document.createElement('li');
	const a = document.createElement('a');
	a.setAttribute('href', '#');
	a.textContent = "No Topic";
	li2.appendChild(a);
	a.addEventListener('click', function() {
		SetDropdownTopic("No Topic");
	});
	topicsDropdown.appendChild(li2);
	for (const topic of availableTopics){
		
		//Topics on side
		const li = document.createElement('li');
		const buttonlbl2 = document.createElement('button');
		const underline = document.createElement('u');
		const buttonText = document.createTextNode(topic);
		buttonlbl2.addEventListener('click', function() {
			currentTopic = topic;
			LoadPosts();
		});
		//Add the text node to the <u> element
		underline.appendChild(buttonText);
		buttonlbl2.appendChild(underline);
		li.appendChild(buttonlbl2);
		topicsDiv.appendChild(li);
		
		//Topics in create post dropdown
		const li2 = document.createElement('li');
		const a = document.createElement('a');
		a.setAttribute('href', '#');
		a.textContent = topic;
		li2.appendChild(a);
		a.addEventListener('click', function() {
			SetDropdownTopic(topic);
		});
		topicsDropdown.appendChild(li2);
}
}

function SetDropdownTopic(topic){
	document.getElementById("topicDropdownButton").innerText = topic;
}

function SubmitTopic(){
	SetCreateTopicVis(false);
	var newTopic = document.getElementById("topicInput").value;
	availableTopics = [];
	topicRequest.open("POST", "topic.php", true);
    topicRequest.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    topicRequest.send("ACTION=AddTopic&TOPIC="+newTopic);
}
