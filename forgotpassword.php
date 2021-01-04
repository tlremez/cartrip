<?php
//start session
session_start();
//connect to the database
include('connection.php');

//check user inputs
	//define error messages
$missingEmail = '<p><strong>Please enter your email address!</strong></p>';
$invalidEmail = '<p><strong>Please enter a valid email address!</strong></p>';

	//get email
	//store errors in errors variable
if(empty($_POST["forgotemail"])){
    $errors .= $missingEmail;   
}else{
    $email = filter_var($_POST["forgotemail"], FILTER_SANITIZE_EMAIL);
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $errors .= $invalidEmail;   
    }
}
	
	//if there are any errors
		//print error message
if($errors){
    $resultMessage = '<div class="alert alert-danger">' . $errors .'</div>';
    echo $resultMessage;
    exit;
}

	//else: no errors
		//prepare variables for the query
$email = mysqli_real_escape_string($link, $email);

		//run query to check if the email exists in the users table
$sql = "SELECT * FROM users WHERE email = '$email'";
$result = mysqli_query($link, $sql);
if(!$result){
    echo '<div class="alert alert-danger">Error running the query!</div>'; exit;
}
$count = mysqli_num_rows($result);
//if the email does not exists
//print error message
if(!$count){
    echo '<div class="alert alert-danger">That email does not exists in our database.</div>';  exit;
}
		
		//else
			//get the user id
$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
$user_id = $row['user_id'];

			//create a unique activation code
$key = bin2hex(openssl_random_pseudo_bytes(16));

			//insert user details and activation code in the forgotpassword table
$time = time();
$status = 'pending';
$sql = "INSERT INTO forgotpassword (`user_id`,`rkey`,`time`,`status`) VALUES ('$user_id','$key','$time','$status')";
$result = mysqli_query($link, $sql);
if(!$result){
    echo '<div class="alert alert-danger">There was an error inserting the users details in the database!'.mysqli_error($link).'</div>'; 
    exit;
}
			//send email with link to resetpassword.php with user id and activation code
$message = "Please click on the link to activate your account:\n\n";
//echo "Please click on the link to reset your password:\n\n";
$message .= "http://tanyalr.host20.uk/CarShare/resetpassword.php?user_id=".$user_id."&key=$key";
 if (mail($email, 'Reset your password', $message, 'From:'.'admin@tanyalr.host20.uk')) {
     //if email sent successfuly
				//print success message
 	echo "<div class='alert alert-success'>Thank for your registration! A confirmation email has been sent to $email. Please click on the activation link to activate your account.</div>";
     
 }
//echo "<a href='http://tanyalr.host20.uk/CarShare/resetpassword.php?user_id=".$user_id."&key=$key'>$message</a>";

?>