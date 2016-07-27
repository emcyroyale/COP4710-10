<?php
    ob_start();
    session_start();
    if (!isset($_SESSION["username"])){
        require_once('index.php');
        header($uri . '/index.php');
    }
?>