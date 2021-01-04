var data;
var departureLongitude;
var departureLatitude;
var destinationLongitude;
var destinationLatitude;
var trip;

//get trips
getTrips();

//create a geocoder object to use geocode
var geocoder = new google.maps.Geocoder();

$(function(){
//fix map
    $("#addtripModal").on('shown.bs.modal', function(){
        $("#addtripmessage").empty();
        google.maps.event.trigger(map,"resize");
    });
    
    
});

//hide all date-time-checkbox inputs
$('.regular').hide();
$('.one-off').hide();
$('.regular2').hide();
$('.one-off2').hide();

var myRadio = $('input[name="regular"]');

myRadio.click(function(){
    if($(this).is(':checked')){
        if($(this).val() == "Y"){
            $('.one-off').hide();
            $('.regular').show();
        }else{
            $('.regular').hide();
            $('.one-off').show();
        }
    }
});

var myRadio = $('input[name="regular2"]');

myRadio.click(function(){
    if($(this).is(':checked')){
        if($(this).val() == "Y"){
            $('.one-off2').hide();
            $('.regular2').show();
        }else{
            $('.regular2').hide();
            $('.one-off2').show();
        }
    }
});

//calendar
$('input[name="date"], input[name="date2"]').datepicker({
    numberOfMonths: 1,
    showAnim: "fadeIn",
    dateFormat: "D d M, yy",
    minDate: +1,
    maxDate: "+12M",
    showWeek: true
});



//Click on create trip button
$("#addtripform").submit(function(event){
    $("#spinner").show();
    $("#addtripmessage").hide();
    
    event.preventDefault();
    data = $(this).serializeArray();
    getAddTripDepartureCoordinates();
});

//define function
function getAddTripDepartureCoordinates(){
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
                    getAddTripDestinationCoordinates();
                }else{
                    getAddTripDestinationCoordinates();
                }

            }
        );
    }

function getAddTripDestinationCoordinates(){
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
                    submitAddTripRequest();
                }else{
                    submitAddTripRequest();
                }

            }
        );

    }

function submitAddTripRequest(){
        $.ajax({
            url: "addtrips.php",
            data: data,
            type: "POST",
            success: function(returnedData){
                $("#spinner").hide();
                $('#addtripmessage').hide();
                if(returnedData){
                
                $('#addtripmessage').html(returnedData);
                $('#addtripmessage').slideDown();
            }else{
                //hide modat
                $("#addtripModal").modal('hide');
                //reset form
                $("#addtripform")[0].reset();
                //hide regular and one-off elements
                $(".regular").hide();
                $(".one-off").hide();
                //load trips
                getTrips();
            }
		},
		error: function(){
            $("#spinner").hide();
			$('#addtripmessage').html("<div class='alert alert-danger'>There was an error with the Ajax Call. Please try again later.</div>");
            $('#addtripmessage').slideDown();
		}
	});
}

function formatModal(){
    $('#departure2').val(trip['departure']);
    $('#destination2').val(trip['destination']);
    $('#price2').val(trip['price']);
    $('#seatsavailable2').val(trip['seatsavailable']);
    if(trip['regular'] == "Y"){
       $('#yes2').prop('checked', true);
        $('#monday2').prop('checked', trip['monday']=="1"? true:false);
        $('#tuesday2').prop('checked', trip['tuesday']=="1"? true:false);
        $('#wednesday2').prop('checked', trip['wednesday']=="1"? true:false);
        $('#thursday2').prop('checked', trip['thursday']=="1"? true:false);
        $('#friday2').prop('checked', trip['friday']=="1"? true:false);
        $('#saturday2').prop('checked', trip['saturday']=="1"? true:false);
        $('#sunday2').prop('checked', trip['sunday']=="1"? true:false);
        $('#time2').val(trip['time']);
        $('.one-off2').hide();
        $('.regular2').show();
        
}else{
    $('#no2').prop('checked', true);
    $('#date2').val(trip['date']);
    $('#time2').val(trip['time']);
    $('.regular2').hide();
    $('.one-off2').show();
    
}
}

function getEditTripDepartureCoordinates(){
        geocoder.geocode(
            {
                'address' : document.getElementById("departure2").value
            },
            function(results, status){
                if(status == google.maps.GeocoderStatus.OK){
                    departureLongitude = results[0].geometry.location.lng();
                    departureLatitude = results[0].geometry.location.lat();
                    data.push({name:'departureLongitude', value: departureLongitude});
                    data.push({name:'departureLatitude', value: departureLatitude});
                    getEditTripDestinationCoordinates();
                }else{
                    getEditTripDestinationCoordinates();
                }

            }
        );
    }

function getEditTripDestinationCoordinates(){
        geocoder.geocode(
            {
                'address' : document.getElementById("destination2").value
            },
            function(results, status){
                if(status == google.maps.GeocoderStatus.OK){
                    destinationLongitude = results[0].geometry.location.lng();
                    destinationLatitude = results[0].geometry.location.lat();
                    data.push({name:'destinationLongitude', value: destinationLongitude});
                    data.push({name:'destinationLatitude', value: destinationLatitude});
                    submitEditTripRequest();
                }else{
                    submitEditTripRequest();
                }

            }
        );

    }

function submitEditTripRequest(){
        $.ajax({
            url: "updatetrips.php",
            data: data,
            type: "POST",
            success: function(returnedData){
                $("#spinner").hide();
                if(returnedData){
                
                $('#edittripmessage').html(returnedData);
                $('#edittripmessage').slideDown();
                
            }else{
                //hide modat
                $("#edittripModal").modal('hide');
                //reset form
                $("#edittripform")[0].reset();
                //load trips
                getTrips();
            }
		},
		error: function(){
            $("#spinner").hide();
			$('#edittripmessage').html("<div class='alert alert-danger'>There was an error with the Ajax Call. Please try again later.</div>");
            $('#edittripmessage').slideDown();
		}
	});
}

//get trips
function getTrips(){
    //show spinner
    $("#spinner").show();
    $.ajax({
            url: "gettrips.php",
            data: data,
            success: function(returnedData){
                $("#spinner").hide();
                $("#myTrips").hide();
                $("#myTrips").html(returnedData);
                $("#myTrips").fadeIn();
		},
		error: function(){
            $("#spinner").hide();
            $("#myTrips").hide();
			$('#myTrips').html("<div class='alert alert-danger'>There was an error with the Ajax Call. Please try again later.</div>");
            $("#myTrips").fadeIn();
		}
	});
}



//click on edit button inside a trip
$("#edittripModal").on('show.bs.modal', function(event){
    $("#edittripmessage").empty();
    
    //button which open the modal
    var invoker = $(event.relatedTarget);
    
    //ajax call to get details of the trip
    $.ajax({
            url: "gettripdetails.php",
            method: "POST",
            data: {trip_id: invoker.data('trip_id')},
            success: function(returnedData){
                if(returnedData == "error"){
                    
			         $('#edittripmessage').html("<div class='alert alert-danger'>There was an error with the Ajax Call. Please try again later.</div>");
                    
                }else{
                    trip = JSON.parse(returnedData);
                    //fill edit trip form using the JSON parsed data
                    formatModal();
                }
		},
		error: function(){
			$('#edittripmessage').html("<div class='alert alert-danger'>There was an error with the Ajax Call. Please try again later.</div>");
		}
	});
    
    //submit Edit Trip form
    $('#edittripform').submit(function(event){
        $("#spinner").show();
        $("#edittripmessage").hide();
//        $("#edittripmessage").empty();
        event.preventDefault();
        data = $(this).serializeArray();
        data.push({name:'trip_id', value: invoker.data('trip_id')});
        getEditTripDepartureCoordinates();
    });
    
    //delete trip
    $("#deleteTrip").click(function(){
        $("#spinner").show();
        $("#edittripmessage").hide();
        $.ajax({
            url: "deletetrips.php",
            method: "POST",
            data: {trip_id: invoker.data('trip_id')},
            success: function(returnedData){
                $("#spinner").hide();
                if(returnedData == "error"){
			         $('#edittripmessage').html("<div class='alert alert-danger'>The trip could not be deleted.Please try again.</div>");
                    $('#edittripmessage').slideDown();
                }else{
                    $("#edittripModal").modal('hide');
                    getTrips();
                }
		},
		error: function(){
            $("#spinner").show();
			$('#edittripmessage').html("<div class='alert alert-danger'>There was an error with the Ajax Call. Please try again later.</div>");
            $('#edittripmessage').slideDown();
		}
	});
    });
    
});





























