<?php
	$uri = 'Location: ';
	if (!empty($_SERVER['HTTPS']) && ('on' == $_SERVER['HTTPS'])) {
		$uri .= 'https://';
	} else {
		$uri .= 'http://';
	}
	$uri .= $_SERVER['HTTP_HOST'];
	$uri .= '/CollegeEvents';

	if (session_status() == PHP_SESSION_ACTIVE) {
		header($uri.'/logout.php');
	} else {
		header($uri . '/login.php');
	}
?>
