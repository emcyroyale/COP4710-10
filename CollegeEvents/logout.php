<?php
    //  Destroys active session
    //  And re-directs to login page
    if(session_status() == PHP_SESSION_ACTIVE ) {
        session_destroy();
    }
    require_once ("index.php");
    header($uri.'/login.php');
?>