<?php
session_start();
if(!isset($_SESSION['user_id'])){
    header("location: index.php");
}
include('connection.php');

$user_id = $_SESSION['user_id'];

//get username and email
$sql = "SELECT * FROM users WHERE user_id='$user_id'";
$result = mysqli_query($link,$sql);
$count = mysqli_num_rows($result);
if ($count == 1) {
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
    $username = $row['username'];
    $email = $row['email'];
    $picture = $row['profilepicture'];
}else{
    echo "<div>There was an error</div>";
}
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Trips</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
      <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
      <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
      <script src="js/bootstrap.min.js"></script>
      <link href="css/bootstrap.min.css" rel="stylesheet">
      <link href="styling.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Arvo&display=swap" rel="stylesheet">
      <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCOVsS4RBItWLpuIzq9gKIukNi7KSPpH7U&libraries=places" type="text/javascript"></script>
          
    <style>
        #container{
            margin-top: 120px;
        }
        #notePad, #allNotes, #done, .delete{
            display: none;
        }
        .buttons{
            margin-bottom: 20px;
        }
        textarea{
            width: 100%;
            max-width: 100%;
            font-size: 16px;
            line-height: 1.5em;
            border-left-width: 20px;

        }

        .noteheader{
          border: 1px solid grey;
          border-radius: 10px;
          margin-bottom: 10px;
          cursor: pointer;
          padding: 0 10px;
          background: linear-gradient(#FFFFFF,#ECEAE7);
        }
        .text{
          font-size: 20px;
          overflow: hidden;
          white-space: nowrap;
          text-overflow: ellipsis;
        }

        .timetext{
          overflow: hidden;
          white-space: nowrap;
          text-overflow: ellipsis;
        }
        .modal{
            margin-top: 100px;
            z-index: 20;
        }
        .modal-backdrop{
            z-index: 10;
        }
        .time{
            margin-top: 10px;
        }
        .trip{
            border: 1px solid grey;
            padding: 10px;
            border-radius: 3px;
            margin: 3px;
            background: linear-gradient(#ECE9E6,#FFFFFF);
        }
        .departure, .destination, .seatsavailable{
            font-size: 1.5em;
        }
        .price{
            font-size: 2em;
        }
        #myTrips{
            margin-top: 20px;
            margin-bottom: 100px;
        }
        

    </style>
  </head>
  <body>
<!-- Navigation bar-->

<nav role="navigation" class="navbar navbar-custom navbar-fixed-top">
      
          <div class="container-fluid">
            
              <div class="navbar-header">
              
                  <a class="navbar-brand">Car Sharing</a>
                  <button type="button" class="navbar-toggle" data-target="#navbarCollapse" data-toggle="collapse">
                      <span class="sr-only">Toggle navigation</span>
                      <span class="icon-bar"></span>
                      <span class="icon-bar"></span>
                      <span class="icon-bar"></span>
                  
                  </button>
              </div>
              <div class="navbar-collapse collapse" id="navbarCollapse">
                  <ul class="nav navbar-nav">
                    <li><a href="index.php">Search</a></li>
                    <li><a href="profile.php">Profile</a></li>
                    <li><a href="#">Help</a></li>
                    <li><a href="#">Contact us</a></li>
                    <li class="active"><a href="#">My Trips</a></li>
                  </ul>
                  <ul class="nav navbar-nav navbar-right">
                    <li><a href="#"><div data-toggle="modal" data-target="#updatepicture">
                        <?php
                        if(empty($picture)){
                            echo "<img class='preview' src='profilepicture/empty.PNG'>";
                        }else{
                            echo "<img class='preview' src='$picture'>";
                        }
                        ?>
                        </div></a></li>
                    <li><a href="#"><b><?php echo $username ?></b></a></li>
                    <li><a href="index.php?logout=1">Log out</a></li>
                  </ul>
              
              </div>
          </div>
      
      </nav>
<!-- container -->
<div class="container" id="container">
    <div class="row">
        <div class="col-sm-8 col-sm-offset-2">
            <div>
                <button type="button" class="btn btn-lg btn-info" data-toggle="modal" data-target="#addtripModal">Add Trip</button>
            </div>
            <div id="myTrips" class="trips">
<!--           Ajax call to PHP file -->
            </div>
        
        </div>
    </div>
</div>
      
<!-- Add trip form -->
<form method="post" id="addtripform">
    <div class="modal" id="addtripModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button class="close" data-dismiss="modal">&times;</button>
                        <h4 id="myModalLabel">New trip:</h4>
                    </div>
                    <div class="modal-body">
<!--                        Add trip message-->
                        <div id="addtripmessage"></div>
                        
<!--                        Google Map-->
                        <div id="googleMap"></div>

                        <div class="form-group">
                            <label for="departure" class="sr-only">Departure</label>
                            <input class="form-control" type="text" name="departure" id="departure" placeholder="Departure">
                        </div>
                        <div class="form-group">
                            <label for="destination" class="sr-only">Destination</label>
                            <input class="form-control" type="text" name="destination" id="destination" placeholder="Destination">
                        </div>
                        <div class="form-group">
                            <label for="price" class="sr-only">Price</label>
                            <input class="form-control" type="number_format" name="price" id="price" placeholder="Price">
                        </div>
                        <div class="form-group">
                            <label for="seatsavailable" class="sr-only">Seats Available</label>
                            <input class="form-control" type="number_format" name="seatsavailable" id="seatsavailable" placeholder="Seats Available">
                        </div>
                        <div class="form-group">
                            <label><input type="radio" name="regular" id="yes" value="Y"> Regular</label>
                            <label><input type="radio" name="regular" id="no" value="N"> One-off</label>
                        </div>
                        <div class="checkbox checkbox-inline regular">
                            <label><input type="checkbox" name="monday" id="monday" value="1">Monday</label>
                            <label><input type="checkbox" name="tuesday" id="tuesday" value="1">Tuesday</label>
                            <label><input type="checkbox" name="wednesday" id="wednesday" value="1">Wednesday</label>
                            <label><input type="checkbox" name="thursday" id="thursday" value="1">Thursday</label>
                            <label><input type="checkbox" name="friday" id="friday" value="1">Friday</label>
                            <label><input type="checkbox" name="saturday" id="saturday" value="1">Saturday</label>
                            <label><input type="checkbox" name="sunday" id="sunday" value="1">Sunday</label>
                        </div>
                        <div class="form-group one-off">
                            <label for="date" class="sr-only">Date</label>
                            <input class="form-control" readonly="readonly" name="date" id="date">
                        </div>
                        <div class="form-group regular one-off time">
                            <label for="time" class="sr-only">Time:</label>
                            <input class="form-control" type="time" name="time" id="time">
                        </div>
                        
                    <div class="modal-footer">
                        <input class="btn btn-primary" type="submit" name="createTrip" value="Create Trip">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                
                    </div>
                
                </div>
            </div>
        </div>
    </div>
</form>
      
      <!-- Edit trip form -->
<form method="post" id="edittripform">
    <div class="modal" id="edittripModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button class="close" data-dismiss="modal">&times;</button>
                        <h4 id="myModalLabel">Edit trip:</h4>
                    </div>
                    <div class="modal-body">
<!--                        Add trip message-->
                        <div id="edittripmessage"></div>

                        <div class="form-group">
                            <label for="departure2" class="sr-only">Departure:</label>
                            <input class="form-control" type="text" name="departure2" id="departure2" placeholder="Departure">
                        </div>
                        <div class="form-group">
                            <label for="destination2" class="sr-only">Destination:</label>
                            <input class="form-control" type="text" name="destination2" id="destination2" placeholder="Destination">
                        </div>
                        <div class="form-group">
                            <label for="price2" class="sr-only">Price</label>
                            <input class="form-control" type="number_format" name="price2" id="price2" placeholder="Price">
                        </div>
                        <div class="form-group">
                            <label for="seatsavailable2" class="sr-only">Seats Available</label>
                            <input class="form-control" type="number_format" name="seatsavailable2" id="seatsavailable2" placeholder="Seats Available">
                        </div>
                        <div class="form-group">
                            <label><input type="radio" name="regular2" id="yes2" value="Y"> Regular</label>
                            <label><input type="radio" name="regular2" id="no2" value="N"> One-off</label>
                        </div>
                        <div class="checkbox checkbox-inline regular2">
                            <label><input type="checkbox" name="monday2" id="monday2" value="1">Monday</label>
                            <label><input type="checkbox" name="tuesday2" id="tuesday2" value="1">Tuesday</label>
                            <label><input type="checkbox" name="wednesday2" id="wednesday2" value="1">Wednesday</label>
                            <label><input type="checkbox" name="thursday2" id="thursday2" value="1">Thursday</label>
                            <label><input type="checkbox" name="friday2" id="friday2" value="1">Friday</label>
                            <label><input type="checkbox" name="saturday2" id="saturday2" value="1">Saturday</label>
                            <label><input type="checkbox" name="sunday2" id="sunday2" value="1">Sunday</label>
                        </div>
                        <div class="form-group one-off2">
                            <label for="date2" class="sr-only">Date</label>
                            <input class="form-control" readonly="readonly" name="date2" id="date2">
                        </div>
                        <div class="form-group regular2 one-off2 time">
                            <label for="time2" class="sr-only">Time:</label>
                            <input class="form-control" type="time" name="time2" id="time2">
                        </div>
                        
                    <div class="modal-footer">
                        <input class="btn btn-primary" type="submit" name="editTrip" value="Edit Trip">
                        <input class="btn btn-danger" name="delete" value="Delete" id="deleteTrip" type="button">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                
                    </div>
                
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Footer -->
<div class="footer">
    <div class="container">
        <p>TLR Copyright &copy; <?php $today=date("Y"); echo $today?></p>
        
    </div>
    
</div>
      <!--      spinner-->
      <div id="spinner">
        <img src="ajax-loader.gif" width="64" height="64">
          <br/>Loading...
      </div>
      
    <script src="map.js"></script>
    <script src="mytrips.js"></script>
  </body>
</html>