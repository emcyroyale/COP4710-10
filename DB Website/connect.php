<?php

DEFINE ('DB_USER', 'root');
DEFINE ('DB_PASSWORD', '1H20LL&W5');
DEFINE ('DB_HOST', 'localhost');
DEFINE ('DB_NAME', 'event_db');

$dbc = @mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
OR die('Couldnt connect' . mysqli_connect_error());




?>