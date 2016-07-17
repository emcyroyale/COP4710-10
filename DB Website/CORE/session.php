<?php
	include('../connect.php');
	session_start();
	$current_user = $_SESSION['login_user'];
	
	$query = "SELECT user_type FROM users
					WHERE userid = '$current_user'";	
	$check_query = @mysqli_query($dbc, $query);
	$ifROW = mysqli_fetch_assoc($check_query);
	if(!isset($ifROW))
	{
		mysqli_close($dbc);
		header('Location:loginform.php');
	}
?>
