<?php
    ob_start();
    session_start();
    if (session_status() == PHP_SESSION_NONE) {
        require_once('index.php');
       header($uri . '/index.php');
    }

?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="College Events Home">
    <link rel = "stylesheet" href = "http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <div class="container">
    <h1>WELCOME HOME</h1>
    <?php
        echo "<h1>USERNAME: ".$_SESSION["username"]."<br>";
    ?>
    </div>
</body>
</html>