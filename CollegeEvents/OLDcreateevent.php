<?php
    require_once ('check_session.php');
?>

<html>
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Creates a new Event">
    <link rel = "stylesheet" href = "http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>

<body>
    <div class="container form-signin">
        <!-- Process POST data in this HTML page -->
        <?php
        //HTML tags for error messages
        $err = "<h4 class=\"form-signin-error\">";
        
        $end = "</h4>";
        // define variables and set to empty values
        $success = $usernameErr = $passwordErr = "";
        $username = $password = "";

        $missing_data = array();

        // Check if any input data has been POSTed through Request Method
        if ($_SERVER["REQUEST_METHOD"] == "POST")
        {
            if (empty($_POST["username"])) {
                $missing_data[] = "username";
                $usernameErr = $err."Name is required".$end;
            } else {
                $username = trim_input($_POST["username"]);
                // check if username only contains letters and whitespace
                if (!preg_match("/^[a-zA-Z0-9!@#&]*$/", $username)){
                    $missing_data[] = "username";
                    $usernameErr = $err."Only letters, digits, and {!, @, #, &} characters are allowed.".$end;
                }
            }

            if (empty($_POST["password"]))
            {
                $missing_data[] = "password";
                $passwordErr = $err."Password is required".$end;
            } else {
                $password = trim_input($_POST["password"]);
                if (strlen($password) < 6){
                    $missing_data[] = "password";
                    $passwordErr = $err."Must be at least 6 characters long".$end;
                }
            }

            // If no data is missing and it's been validated
            // CHECK USER AND LOG IN
            if (empty($missing_data)) {
                require_once('connect.php');

                $query = "SELECT userid, email FROM users WHERE userid = '$username' AND password = '$password'";
                $check_user = mysqli_query($database, $query);

                // USER SUCCESS, START SESSION
                if(mysqli_num_rows($check_user)>= 1){
                    $success = $err."SUCCESS!".$end;
                    $_SESSION['valid'] = true;
                    $_SESSION['timeout'] = time();
                    $_SESSION['username'] = $username;
                    $_SESSION['password'] = $password;
                    //$_SESSION['email'] = $email;

                    mysqli_close($database);
                    require_once('index.php');
                    header($uri.'/home.php');
                } else {
                    $success = $err."Username/Password is incorrect".$end;
                    mysqli_close($database);
                }
            }
        }

        //process input data
        function trim_input($data)
        {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            return $data;
        }
        ?>
    </div>
    <!-- Create Events Form -->
    <div class = "container">
    <h1> CREATE EVENT </h1>
        <form class = "form-signin" role = "form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">


        <b class="left" >Name </b>
        <input class="right" type="text" name="usernameTXT" size=20></input><br />

        <b class="left" >Date </b>
        <input class="right" type="date" name="dateSELECT"></input><br />

        <b class="left" >Time </b>
        <input class="right" type="time" name="timeSELECT"></input><br />

        <b class="left" >Phone </b>
        <input class="right" type="text" name="phoneTXT" size=20></input><br /><br />

        <b class="left" >Email </b>
        <input class="right" type="text" name="emailTXT" size=20></input><br /><br />

        <b class="left" >Description </b>
        <textarea class="right" col= 200 row=10 name="descTXTAREA"></textarea><br />

        <b class="left" >Type </b>
        <select class="right"name="typeSELECT">
            <option value="option1"> Public </option>
            <option value="option2"> Private </option>
            <option value="option3"> Registered Student Organization </option>
        </select><br /><br />
        <b class="left" >Category </b>

        <select class="right"name="categorySELECT">
            <option value="option1"> phpPLACEHOLDER1 </option>
            <option value="option2"> phpPLACEHOLDER2 </option>
            <option value="option3"> phpPLACEHOLDER3 </option>
        </select><br /><br />
        <b class="left" >Location </b>
        <select class="right"name="locationSELECT">
            <option value="option1"> phpPLACEHOLDER1 </option>
            <option value="option2"> phpPLACEHOLDER2 </option>
            <option value="option3"> phpPLACEHOLDER3 </option>
        </select><br />
        <input type="submit" value="Create"></input><br />
    </form>
</body>
</html>
