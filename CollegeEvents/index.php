<?php
	ob_start();
	session_start();

	$uri = 'Location: ';
	if (!empty($_SERVER['HTTPS']) && ('on' == $_SERVER['HTTPS'])) {
		$uri .= 'https://';
	} else {
		$uri .= 'http://';
	}
	$uri .= $_SERVER['HTTP_HOST'];
	$uri .= '/CollegeEvents';

	if(!isset($_SESSION["username"])){
		header($uri.'/login.php');
	} else {
		header($uri . '/dashboard.php');
	}
?>
