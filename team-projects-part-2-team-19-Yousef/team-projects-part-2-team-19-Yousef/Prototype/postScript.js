const httpRequest = new XMLHttpRequest();
checkSession();
loadPage();
//Get the query string from the URL
var queryString = window.location.search;
//Remove the leading question mark
queryString = queryString.substring(1);

//Split the query string into individual parameters
var queryParams = queryString.split('&')[0];
var paramsArray = queryParams.split('=');
var postNum = paramsArray[1];
function LoadPost(){ //Called when web page refreshed
	//console.log("LOAD POST");
	
	//retreive data from database by sending an httpRequest
	httpRequest.open("POST", "post.php", true);
	httpRequest.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	httpRequest.send("POSTID="+postNum);
}

httpRequest.onreadystatechange = function() { //Response from server, contains Title,Content,Edited
  if (this.readyState == 4 && this.status == 200) {
	  posts = this.responseText;
	  console.log(posts);
	  posts = posts.replace(/<\/?[^>]+(>|$)/g, ""); //Get rid of all html tags
	  console.log(posts);
	  var post = JSON.parse(posts);
    document.getElementById("title").innerHTML = post[0].Title;
	  document.getElementById("postTitle").innerHTML = post[0].Title;
    document.getElementById("breadcrumb").innerHTML = post[0].Title;
    document.getElementById("postAuthor").innerHTML = post[0].EmployeeName + "\n";
	  document.getElementById("postContents").innerHTML = post[0].Content;
  }
};
//post