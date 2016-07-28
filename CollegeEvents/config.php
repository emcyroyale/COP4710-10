<?php

$root_url = 'Location: ';
if (!empty($_SERVER['HTTPS']) && ('on' == $_SERVER['HTTPS'])) {
    $root_url .= 'https://';
} else {
    $root_url .= 'http://';
}

$root_url .= $_SERVER['HTTP_HOST'];
$root_url .= '/CollegeEvents';
//HTML tags for error messages
$err = "<h4 class='error'>";
$suc = "<h4 class='form-signin-success'>";
$end = "</h4>";

$errName = $err."Only letters, digits, and {!, @, #, &} characters are 
                    allowed and it must be between 4 to 20 characters long.".$end;

$errPW = $err."Password must be at least 6 characters long, and only contain 
                letters, digits, and {!, @, #, &} symbols.".$end;

$regexp_pw = "/^[a-zA-Z0-9!@#&]{6,20}$/";
$regexp = "/^[a-zA-Z0-9!@#&]{4,20}$/";

$loc_lat_default = "28.6024";
$loc_lng_default = "-81.2001";

//process input data
function trim_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}