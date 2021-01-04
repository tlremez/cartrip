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
    <title>Profile</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="styling.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Arvo&display=swap" rel="stylesheet">
    <style>
        #container{
            margin-top: 100px;
        }
        #notePad, #allNotes, #done{
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
        tr{
            cursor: pointer;
        }
        
        .preview2{
            height: auto;
            max-width: 100%;
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
                    <li class="active"><a href="#">Profile</a></li>
                    <li><a href="#">Help</a></li>
                    <li><a href="#">Contact us</a></li>
                    <li><a href="mainpageloggedin.php">My Trips</a></li>
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
                    <li><a href="#"><div data-toggle="modal" data-target="#updateusername"><b><?php echo $username ?></b></div></a></li>
                    <li><a href="index.php?logout=1">Log out</a></li>
                  </ul>
              
              </div>
          </div>
      
      </nav>
<!-- container -->
<div class="container" id="container">
    <div class="row">
        <div class="col-md-offset-3 col-md-6 profile">
            <h2>General Account Settings:</h2>
            <div class="table-responsive">
                <table class="table table-hover table-condensed table-bordered">
                    <tr data-target="#updateusername" data-toggle="modal">
                        <td>Username</td>
                        <td><?php echo $username; ?></td>
                    </tr>
                    <tr data-target="#updateemail" data-toggle="modal">
                        <td>Email</td>
                        <td><?php echo $email ?></td>
                    </tr>
                    <tr data-target="#updatepassword" data-toggle="modal">
                        <td>Password</td>
                        <td>**********</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
 <!-- Update username -->
<form method="post" id="updateusernameform">
    <div class="modal" id="updateusername" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button class="close" data-dismiss="modal">&times;</button>
                        <h4 id="myModalLabel">Edit Username:</h4>
                    </div>
                    <div class="modal-body">
                        <!-- update username message -->
                        <div id="updateusernamenmessage"></div>
                        
                        <div class="form-group">
                            <label for="username" >Username</label>
                            <input class="form-control" type="text" name="username" id="username"  maxlength="30" value="<?php echo $username; ?>">
                        </div>
                        
                        
                    </div>
                    <div class="modal-footer">
                        <input class="btn btn-info" type="submit" name="updateusername" value="Update">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                
                    </div>
                
                </div>
            </div>
        </div>
</form>

<!-- Update Email -->
<form method="post" id="updateemailform">
    <div class="modal" id="updateemail" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button class="close" data-dismiss="modal">&times;</button>
                        <h4 id="myModalLabel">Enter new email:</h4>
                    </div>
                    <div class="modal-body">
                        <div id="updateemailmessage"></div>
                        
                        <div class="form-group">
                            <label for="email" >Email:</label>
                            <input class="form-control" type="email" name="email" id="email"  maxlength="30" value="<?php echo $email; ?>">
                        </div>
                        
                        
                    </div>
                    <div class="modal-footer">
                        <input class="btn btn-info" type="submit" name="updateemail" value="Update">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                
                    </div>
                
                </div>
            </div>
        </div>
</form>

<!-- Update password -->
<form method="post" id="updatepasswordform">
    <div class="modal" id="updatepassword" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button class="close" data-dismiss="modal">&times;</button>
                        <h4 id="myModalLabel">Enter Current and New password:</h4>
                    </div>
                    <div class="modal-body">
                        <!-- update password message -->
                        <div id="updatepasswordmessage"></div>
                        
                        <div class="form-group">
                            <label for="currentpassword" class="sr-only">Your Current Password:</label>
                            <input class="form-control" type="password" name="currentpassword" id="currentpassword"  maxlength="50" placeholder="Your Current Password">
                        </div>
                        <div class="form-group">
                            <label for="choosepassword" class="sr-only">Choose a password:</label>
                            <input class="form-control" type="password" name="choosepassword" id="choosepassword"  maxlength="50" placeholder="Choose a password">
                        </div>
                        <div class="form-group">
                            <label for="confirmpassword" class="sr-only">Confirm password</label>
                            <input class="form-control" type="password" name="confirmpassword" id="confirmpassword"  maxlength="30" placeholder="Confirm password">
                        </div>
                        
                    </div>
                    <div class="modal-footer">
                        <input class="btn btn-info" type="submit" name="updateusername" value="Submit">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                
                    </div>
                
                </div>
            </div>
        </div>
</form>
      
       <!-- Update picture -->
<form method="post" id="updatepictureform" enctype="multipart/form-data">
    <div class="modal" id="updatepicture" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button class="close" data-dismiss="modal">&times;</button>
                        <h4 id="myModalLabel">Upload Picture:</h4>
                    </div>
                    <div class="modal-body">
                        <!-- update picture message -->
                        <div id="updatepicturemessage"></div>
                        
                        <?php
                        if(empty($picture)){
                            echo "<div><img class='preview2' id='preview2' src='profilepicture/empty.PNG'></div>";
                        }else{
                            echo "<div><img class='preview2' id='preview2' src='$picture'></div>";
                        }
                        ?>
                        
                        <div class="form-group">
                            <label for="picture" >Select a Picture</label>
                            <input type="file" name="picture" id="picture">
                        </div>
                        
                        
                    </div>
                    <div class="modal-footer">
                        <input class="btn btn-info" type="submit" name="updateusername" value="Submit">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                
                    </div>
                
                </div>
            </div>
        </div>
</form>

<!-- Footer -->
<div class="footer">
    <div class="container">
        <p>DevelopmentIsland.com Copyright &copy; <?php $today=date("Y"); echo $today?></p>
        
    </div>
    
</div>

<!-- jQuery (necessary for Bootstrap's JavaScript plagins) -->


<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
    <script src="profile.js"></script>

  </body>
</html>