<?php
    //  Destroys active session
    //  And re-directs to login page
    if(session_status() == PHP_SESSION_ACTIVE ) {
        session_destroy();
    }
    if(isset($_SESSION["username"])){
        unset($_SESSION["username"]);
    }
    require_once ("index.php");
    header($uri.'/login.php');
?>