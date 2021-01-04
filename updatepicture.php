<?php
session_start();
include('connection.php');

$user_id = $_SESSION["user_id"];

print_r($_FILES);

//file name
$name = $_FILES["picture"]["name"];
$extension = pathinfo($name, PATHINFO_EXTENSION);
$tmp_name = $_FILES["picture"]["tmp_name"];
$fileError = $_FILES["picture"]["error"];

$permanentDestination = 'profilepicture/'.md5(time()).".$extension";

if(move_uploaded_file($tmp_name, $permanentDestination)){
    $sql = "UPDATE users SET profilepicture='$permanentDestination' WHERE user_id='$user_id'";
    $result = mysqli_query($link,$sql);
    if(!$result){
        echo "<div class='alert alert-danger'>Unable to update profile picture1. Please try again later!</div>";
    }
}else{
    echo "<div class='alert alert-danger'>Unable to update profile picture2. Please try again later!</div>";
}
if($fileError>0){
    echo "<div class='alert alert-danger'>Unable to update profile picture. Please try again later! Error code: $fileError</div>";
}

?>