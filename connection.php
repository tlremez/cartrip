<?php
// <!-- connect to the database -->
$link = mysqli_connect("localhost","tanyalrh_carshare","fcbarca","tanyalrh_carshare");
if (mysqli_connect_error()) {
	die("ERROR: Unable to connect:".mysqli_connect_error());
}
?>