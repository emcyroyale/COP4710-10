<?php
    require_once ('check_session.php');
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
        <h2>UNIVERSITY EVENTS DASHBOARD</h2>
        <?php
            echo "<h2>Welcome, ".$_SESSION["username"]."</h2>";
        ?>
        <!-- Create Event Link -->
        <form class="form-signin" role="form" action="createEvent.php">
            <button class = "btn btn-lg btn-primary btn-block" type = "submit" name = "register">Create Event</button>
        </form>
    </div>
</body>
</html>