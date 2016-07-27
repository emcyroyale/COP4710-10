<?php

// Connect to College Events Database

$user = 'root';
$pass = '1H20LL&W5';
$database = "event_db";

$database = @mysqli_connect('localhost', $user, $pass, $database) or die("Unable to connect.");




