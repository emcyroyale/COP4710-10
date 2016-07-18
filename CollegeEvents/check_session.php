<?php
    ob_start();
    session_start();
    if (session_status() == PHP_SESSION_NONE) {
        require_once('index.php');
        header($uri . '/index.php');
    }
?>