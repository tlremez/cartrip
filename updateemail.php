<?php
session_start();
include('connection.php');

//define error messages
$missingEmail = '<p><strong>Please enter the new email address!</strong></p>';
$invalidEmail = '<p><strong>Please enter a valid email address!</strong></p>';

if(empty($_POST["email"])){
    $errors .= $missingEmail;   
}else{
    $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $errors .= $invalidEmail;   
    }
}
if($errors){
    $resultMessage = '<div class="alert alert-danger">' . $errors .'</div>';
    echo $resultMessage;
    exit;
}

//get user_id and new email sent through ajax
$user_id = $_SESSION['user_id'];
$newemail = $_POST['email'];

//check if the new email exists
$sql = "SELECT * FROM users WHERE email='$newemail'";
$result = mysqli_query($link,$sql);
$count = mysqli_num_rows($result);
if ($count>0) {
	echo "<div class='alert alert-danger'>The user with the same email alresdy exists. Please choose another one.</div>"; exit;
}

//get the current email 
$sql = "SELECT * FROM users WHERE user_id='$user_id'";
$result = mysqli_query($link,$sql);
$count = mysqli_num_rows($result);
if ($count == 1) {
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
    $email = $row['email'];
}else{
    echo "<div>There was an error retrieving the email from the database.</div>"; exit;
}

//create a unique activation code
$activationKey = bin2hex(openssl_random_pseudo_bytes(16));

//insert the new activation code in the users table
$sql = "UPDATE users SET activation2='$activationKey' WHERE user_id='$user_id'";
$result = mysqli_query($link,$sql);
if (!$result) {
	echo "<div>There was an error inserting the user details in the database.</div>"; exit;
}else{
	//send email with link to activatenewemail.php with current email, new email and activation code
	 $message = "Please click on the link to prove that you own this email:\n\n";
//	echo "Please click on the link to prove that you own this email:\n\n";
	$message .= "http://tanyalr.host20.uk/CarShare/activatenewemail.php?email=".urlencode($email)."&newemail=".urlencode($newemail)."&key=$activationKey";
	 if (mail($newemail, 'Email Update', $message, 'From:'.'admin@tanyalr.host20.uk')) {
	 	echo "<div class='alert alert-success'>Thank for your registration! A confirmation email has been sent to $newemail. Please click on the link to prove that you own this email.</div>";
	 }
//	echo "<a href='http://tanyalr.host20.uk/CarShare/activatenewemail.php?email=".urlencode($email)."&newemail=".urlencode($newemail)."&key=$activationKey'>$message</a>";

}

?>