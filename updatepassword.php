<?php
session_start();
include('connection.php');

//define error messages
$missingCurrentPassword = '<p><strong>Please enter your current password!</strong></p>';
$incorrectCurrentPassword = '<p><strong>The password is incorrect</strong></p>';
$missingPassword = '<p><strong>Please enter a new password</strong></p>';
$invalidPassword = '<p><strong>Your password should be at least 6 characters long and inlcude one capital letter and one number!</strong></p>';
$differentPassword = '<p><strong>Passwords don\'t match!</strong></p>';
$missingPassword2 = '<p><strong>Please confirm your password</strong></p>';

//check for errors
if (empty($_POST['currentpassword'])) {
	$errors .= $missingCurrentPassword;
}else{
	$currentPassword = $_POST['currentpassword'];
	$currentPassword = filter_var($currentPassword, FILTER_SANITIZE_STRING);
	$currentPassword = mysqli_real_escape_string($link,$currentPassword);
	$currentPassword = hash('sha256', $currentPassword);
	//check if the given password is correct
	$user_id = $_SESSION['user_id'];
	$sql = "SELECT password FROM users WHERE user_id='$user_id'";
	$result = mysqli_query($link,$sql);
	$count = mysqli_num_rows($result);
	if ($count !== 1) {
		echo "<div class='alert alert-danger'>There was a problem running the query.</div>";
	}else{
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		if ($currentPassword != $row['password']) {
			$errors .= $incorrectCurrentPassword;
		}
	}
}

if(empty($_POST["choosepassword"])){
    $errors .= $missingPassword; 
}elseif(!(strlen($_POST["choosepassword"])>6
         and preg_match('/[A-Z]/',$_POST["choosepassword"])
         and preg_match('/[0-9]/',$_POST["choosepassword"])
        )
       ){
    $errors .= $invalidPassword; 
}else{
    $choosepassword = filter_var($_POST["choosepassword"], FILTER_SANITIZE_STRING); 
    if(empty($_POST["confirmpassword"])){
        $errors .= $missingPassword2;
    }else{
        $confirmpassword = filter_var($_POST["confirmpassword"], FILTER_SANITIZE_STRING);
        if($choosepassword !== $confirmpassword){
            $errors .= $differentPassword;
        }
    }
}

//if there is an error print error message
if ($errors) {
	$resultMessage = "<div class='alert alert-danger'>$errors</div>";
	echo $resultMessage;
}else{
	$choosepassword = mysqli_real_escape_string($link,$choosepassword);
	$choosepassword = hash('sha256', $choosepassword);
	//else run query and update password
	$sql= "UPDATE users SET password ='$choosepassword' WHERE user_id='$user_id'";
	$result = mysqli_query($link,$sql);
	if (!$result) {
		echo "<div class='alert alert-danger'>The password could not be reset. Please try again later</div>";
	}else{
		echo "<div class='alert alert-success'>The password has been updated successfuly</div>";
	}
}


?>