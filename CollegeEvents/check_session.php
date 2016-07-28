<?php
    ob_start();
    session_start();
    if (!isset($_SESSION["username"])){
        require_once('config.php');
        header($root_url . '/index.php');
    } else {
        $curUser = $_SESSION["username"];
    }
?>