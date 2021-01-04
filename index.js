//Ajax coll for the sign up form
//Once the form is submited
$("#signupform").submit(function(event){
    //show spinner
    $("#spinner").show();
    //hide results
    $("#signupmessage").hide();
	//prevent default php processing
	event.preventDefault();
	//collect user inputs
	var datatopost = $(this).serializeArray();
	// console.log(datatopost);
	//send them to signup.php using Ajax
	$.ajax({
		url: "signup.php",
		type: "POST",
		data: datatopost,
		success: function(data){
			if (data) {
                $("#spinner").hide();
				$("#signupmessage").html(data);
                $("#signupmessage").slideDown();
                
			}
		},
		error: function(){
            $("#spinner").hide();
			$("#signupmessage").html("<div class='alert alert-danger'>There was an error with the Ajax Call. Please try again later.</div>");
            $("#signupmessage").slideDown();
		}
	});
});

$(function(){
    $("#loginModal").on('shown.bs.modal', function(){
        $("#loginmessage").empty();
        $("#loginform")[0].reset();
    });
    $("#signupModal").on('shown.bs.modal', function(){
        $("#signupform")[0].reset();
        $("#signupmessage").empty();
    });
    $("#forgotpasswordModal").on('shown.bs.modal', function(){
        $("#forgotpasswordform")[0].reset();
        $("#forgotpasswordmessage").empty();
    });
});
	
//Ajax Call for the login form
//Once the form is submitted
$("#loginform").submit(function(event){
    //show spinner
    $("#spinner").show();
    //hide results
    $("#loginmessage").hide();
    //prevent default php processing
    event.preventDefault();
    //collect user inputs
    var datatopost = $(this).serializeArray();
//    console.log(datatopost);
    //send them to login.php using AJAX
    $.ajax({
        url: "login.php",
        type: "POST",
        data: datatopost,
        success: function(data){
            if(data == "success"){
                window.location = "mainpageloggedin.php";
            }else{
                $("#spinner").hide();
                $('#loginmessage').html(data); 
                $("#loginmessage").slideDown();
            }
        },
        error: function(){
            $("#spinner").hide();
            $("#loginmessage").html("<div class='alert alert-danger'>There was an error with the Ajax Call. Please try again later.</div>");
            $("#loginmessage").slideDown();
            
        }
    
    });

});

//Ajax Call for the forgot password form
//Once the form is submitted
$("#forgotpasswordform").submit(function(event){
    //show spinner
    $("#spinner").show();
    //hide results
    $("#forgotpasswordmessage").hide();
    //prevent default php processing
    event.preventDefault();
    //collect user inputs
    var datatopost = $(this).serializeArray();
//    console.log(datatopost);
    //send them to forgotpassword.php using AJAX
    $.ajax({
        url: "forgotpassword.php",
        type: "POST",
        data: datatopost,
        success: function(data){
            $("#spinner").hide();
            $('#forgotpasswordmessage').html(data);
            $("#forgotpasswordmessage").slideDown();
        },
        error: function(){
            $("#spinner").hide();
            $("#forgotpasswordmessage").html("<div class='alert alert-danger'>There was an error with the Ajax Call. Please try again later.</div>");
            $("#forgotpasswordmessage").slideDown();
            
        }
    
    });

});


//create a geocoder object to use geocode
var geocoder = new google.maps.Geocoder();

var data;
//submit the search form
$("#searchForm").submit(function(){
    //show spinner
    $("#spinner").show();
    //hide results
    $("#searchResults").fadeOut();
    event.preventDefault();
    data = $(this).serializeArray();
    getSearchDepartureCoordinates()
});

//define function
function getSearchDepartureCoordinates(){
        geocoder.geocode(
            {
                'address' : document.getElementById("departure").value
            },
            function(results, status){
                if(status == google.maps.GeocoderStatus.OK){
                    departureLongitude = results[0].geometry.location.lng();
                    departureLatitude = results[0].geometry.location.lat();
                    data.push({name:'departureLongitude', value: departureLongitude});
                    data.push({name:'departureLatitude', value: departureLatitude});
                    getSearchDestinationCoordinates();
                }else{
                    getSearchDestinationCoordinates();
                }

            }
        );
    }

function getSearchDestinationCoordinates(){
        geocoder.geocode(
            {
                'address' : document.getElementById("destination").value
            },
            function(results, status){
                if(status == google.maps.GeocoderStatus.OK){
                    destinationLongitude = results[0].geometry.location.lng();
                    destinationLatitude = results[0].geometry.location.lat();
                    data.push({name:'destinationLongitude', value: destinationLongitude});
                    data.push({name:'destinationLatitude', value: destinationLatitude});
                    submitSearchRequest();
                }else{
                    submitSearchRequest();
                }

            }
        );

    }

function submitSearchRequest(){
        $.ajax({
            url: "search.php",
            data: data,
            type: "POST",
            success: function(returnedData){
                //hide spinner
                $("#spinner").hide();
                
                $("#searchResults").html(returnedData);
                $("#tripResults").accordion({
                    active: false,
                    collapsible: true,
                    heightStyle: "content",
                    icon: false
                });
                //show results
                $("#searchResults").fadeIn();
		},
		error: function(){
            //hide spinner
            $("#spinner").hide();
            
			$('#searchResults').html("<div class='alert alert-danger'>There was an error with the Ajax Call. Please try again later.</div>");
            //show results
            $("#searchResults").fadeIn();
		}
	});
}