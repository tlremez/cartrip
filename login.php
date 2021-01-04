<?php
//start sessiont
session_start();
//connect to the database
include('connection.php'); 
//check user inputs
	//define error messages
$missingEmail = '<p><strong>Please enter your email address.</strong></p>';
$missingPassword = '<p><strong>Please enter your password.</strong></p>';
	//get email and password
	//store errors in errors variables
if(empty($_POST["loginemail"])){
    $errors .= $missingEmail;   
}else{
    $email = filter_var($_POST["loginemail"], FILTER_SANITIZE_EMAIL);
    
}
if(empty($_POST["loginpassword"])){
    $errors .= $missingPassword;   
}else{
    $password = filter_var($_POST["loginpassword"], FILTER_SANITIZE_STRING);
    
}
	
	//if there are any errors
if ($errors) {
	//print error message
	$resultMessage = '<div class="alert alert-danger">' . $errors .'</div>';
    echo $resultMessage;
}else{
	//else: no errors
	//prepare variables to the query
	$email = mysqli_real_escape_string($link, $email);
	$password = mysqli_real_escape_string($link, $password);
	$password = hash('sha256', $password);

		//run query: check combination of email&password exists
$sql = "SELECT * FROM users WHERE email='$email' AND password = '$password' AND activation = 'activated'";
$result = mysqli_query($link, $sql);
if(!$result){
    echo '<div class="alert alert-danger">Error running the query!</div>';
//    echo '<div class="alert alert-danger">' . mysqli_error($link) . '</div>';
    exit;
}
		//if email and password don't match print error
$count = mysqli_num_rows($result);
if ($count !== 1){
	echo '<div class="alert alert-danger">Wrong username or password!</div>';
}else{
	//else
		//log the user in: set session variables
	$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	$_SESSION['user_id']=$row['user_id'];
	$_SESSION['username']=$row['username'];
	$_SESSION['email']=$row['email'];

//if remember me is not checked
	if (empty($_POST['rememberme'])) {
		//print "success"
		echo "success";
	}else{
		//else
				//create two variables $authenticator1 and $authenticator2
		$authentificator1 = bin2hex(openssl_random_pseudo_bytes(10));
		//2*2*...*2
		$authentificator2 = openssl_random_pseudo_bytes(20);

		//store them in a coockie
		function f1($a, $b){
			$c = $a.",".bin2hex($b);
			return $c;
		}
		$coockieValue = f1($authentificator1, $authentificator2);
		setcookie(
			"rememberme",
			$coockieValue,
			time() + 1296000//15*24*60*60
		);

		//run query to store them in rememberme table
		function f2($a){
			$b = hash('sha256', $a);
			return $b;
		}
		$f2authentificator2 = f2($authentificator2);
		$user_id = $_SESSION['user_id'];
		$expiration = date('Y-m-d H:i:s', time() + 1296000);
		$sql = "INSERT INTO rememberme (`authentificator1`, `f2authentificator2`, `user_id`, `expires`) VALUES ('$authentificator1', '$f2authentificator2', '$user_id', '$expiration')";
		$result = mysqli_query($link, $sql);
		if (!$result) {
			echo '<div class="alert alert-danger">There was an erroe storing data to remember you next time.</div>';
		}else{
			echo "success";
		}
	}
}
		
}			

?>