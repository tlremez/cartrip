$(function(){
    $("#updateusername").on('shown.bs.modal', function(){
        $("#updateusernamenmessage").empty();
        $("#updateusernameform")[0].reset();
    });
    $("#updateemail").on('shown.bs.modal', function(){
        $("#updateemailform")[0].reset();
        $("#updateemailmessage").empty();
    });
    $("#updatepassword").on('shown.bs.modal', function(){
        $("#updatepasswordform")[0].reset();
        $("#updatepasswordmessage").empty();
    });
    $("#updatepicture").on('shown.bs.modal', function(){
        $("#updatepictureform")[0].reset();
        $("#updatepicturemessage").empty();
    });
});

//ajax call to updateusername.php
$("#updateusernameform").submit(function(){
	//prevent default php processing
	event.preventDefault();
	//collect user inputs
	var datatopost = $(this).serializeArray();
	// console.log(datatopost);
	//send them to updateusername.php using Ajax
	$.ajax({
		url: "updateusername.php",
		type: "POST",
		data: datatopost,
		success: function(data){
			if (data) {
				$("#updateusernamenmessage").html(data);
			}else{
				location.reload();
			}
		},
		error: function(){
			$("#updateusernamenmessage").html("<div class='alert alert-danger'>There was an error with the Ajax Call. Please try again later.</div>");
		}
	});

});

//ajax call to updatepassword.php
$("#updatepasswordform").submit(function(){
	//prevent default php processing
	event.preventDefault();
	//collect user inputs
	var datatopost = $(this).serializeArray();
	// console.log(datatopost);
	//send them to updateusername.php using Ajax
	$.ajax({
		url: "updatepassword.php",
		type: "POST",
		data: datatopost,
		success: function(data){
			if (data) {
				$("#updatepasswordmessage").html(data);
			}
		},
		error: function(){
			$("#updatepasswordmessage").html("<div class='alert alert-danger'>There was an error with the Ajax Call. Please try again later.</div>");
		}
	});

});

//ajax call to updateemail.php
$("#updateemailform").submit(function(){
	//prevent default php processing
	event.preventDefault();
	//collect user inputs
	var datatopost = $(this).serializeArray();
	// console.log(datatopost);
	//send them to updateusername.php using Ajax
	$.ajax({
		url: "updateemail.php",
		type: "POST",
		data: datatopost,
		success: function(data){
			if (data) {
				$("#updateemailmessage").html(data);
			}
		},
		error: function(){
			$("#updateemailmessage").html("<div class='alert alert-danger'>There was an error with the Ajax Call. Please try again later.</div>");
		}
	});

});

//update the picture preview
var file;
var imageType;
var imageSize;
var wrongType;
$("#picture").change(function(){
    file = this.files[0];
//    console.log(file);
    imageType = file.type;
    imageSize = file.size;
    
    //check image type
    var acceptableTypes = ["image/jpeg","image/jpg","image/png"];
    wrongType = $.inArray(imageType,acceptableTypes) == -1;
    if(wrongType){
        $("#updatepicturemessage").html("<div class='alert alert-danger'>Only jpeg,png, jpg images are accepted!</div>");
        return false;
    }
    //check image size
    if(imageSize>3*1024*1024){
        $("#updatepicturemessage").html("<div class='alert alert-danger'>The image is too big. Please upload an image less than 3 Mo!</div>");
        return false;
    }
    
    //the FileReader object will be used to convert  our image to a binary string
    var reader = new FileReader();
    //callback
    reader.onload = updatePreview;
    //start the read operation -> convert the content into a data URL which is pased to a callback
    reader.readAsDataURL(file);
    
});

function updatePreview(event){
//    console.log(event);
    $("#preview2").attr("src",event.target.result);
}

//update picture
$("#updatepictureform").submit(function(){
    event.preventDefault();
    //file missing
    if(!file){
        $("#updatepicturemessage").html("<div class='alert alert-danger'>Please upload the picture!</div>");
        return false;
       }
    
    //wrong type
    if(wrongType){
        $("#updatepicturemessage").html("<div class='alert alert-danger'>Only jpeg,png, jpg images are accepted!</div>");
        return false;
    }
    
    //file too big
    if(imageSize>3*1024*1024){
        $("#updatepicturemessage").html("<div class='alert alert-danger'>The image is too big. Please upload an image less than 3 Mo!</div>");
        return false;
    }
    
//    var test = new FormData(this);
//    console.log(test.get("picture"));
    
    //send Ajax Call to updatepicture.php
	$.ajax({
		url: "updatepicture.php", 
                type: "POST",             
                data: new FormData(this), 
                contentType: false,       // The content type used when sending data to the server.
                cache: false,             // To unable request pages to be cached
                processData:false,        // To send DOMDocument or non processed data file it is set to false
                success: function(data){
			if(data){
                $("#updatepicturemessage").html(data);
            }else{
                location.reload();
            }
		},
		error: function(){
			$("#updatepicturemessage").html("<div class='alert alert-danger'>There was an error with the Ajax Call. Please try again later.</div>");
		}
	});
    
})


