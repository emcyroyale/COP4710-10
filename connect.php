<?php

// Connect to College Events Database

$user = 'root';
$pass = '';
$database = 'events_db';

$database = @mysqli_connect('localhost', $user, $pass, $database) or die("Unable to connect.");




