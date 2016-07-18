<?php
    require_once ('check_session.php');
?>

<html>
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Creates a new University by a Super Admin">
    <link rel = "stylesheet" href = "http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>

<body>
    <h2> CREATE UNIVERSITY </h2>
    <form class = "form-signin" role = "form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <b class="left" >Name </b>
        <input class="right" type="text" name="usernameTXT" size=20></input><br />
        <b class="left" >Location </b>
        <select class="right"name="locationSELECT">
            <option value="option1"> phpPLACEHOLDER1 </option>
            <option value="option2"> phpPLACEHOLDER2 </option>
            <option value="option3"> phpPLACEHOLDER3 </option>
        </select><br />
        <b class="left" >Description </b>
        <textarea class="right" col= 200 row=10 name="descTXTAREA"></textarea><br /><br />
        <input type="submit" value="Create"></input><br />
    </form>
</body>

</html>
