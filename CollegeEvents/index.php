<?php
	// Start session
	ob_start();
	session_start();

	require_once('config.php');

	// Check if the session has an active user and redirect to dashboard
	// Otherwise redirect to login page
	if(!isset($_SESSION["username"])){
		header($root_url.'/login.php');
	} else {
		header($root_url . '/dashboard.php');
	}
?>
