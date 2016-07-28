<?php
    ob_start();

    require_once ('config.php');
    //  Start new session if there is none
    if (session_status() == PHP_SESSION_NONE) {
        session_start();

    //Otherwise, logout of current session
    } else {
        header($root_url.'/logout.php');;
    }
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="College Events Login">
    <link rel = "stylesheet" href = "http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <div class = "container form-signin">
        <!-- Process POST data in this HTML page -->
        <?php
        //HTML tags for error messages
        $err = "<h4 class=\"error\">";
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

                $query = "SELECT * FROM users WHERE userid = '$username' AND password = '$password'";
                $check_user = mysqli_query($database, $query);

                // USER SUCCESS, START SESSION
                if(mysqli_num_rows($check_user)>= 1){
                    $row = mysqli_fetch_assoc($check_user);
                    $success = $err."SUCCESS!".$end;
                    $_SESSION['valid'] = true;
                    $_SESSION['timeout'] = time();
                    $_SESSION['username'] = $username;
                    $_SESSION['password'] = $password;
                    $_SESSION['email'] = $row['email'];
                    $_SESSION['user_type'] = $row['user_type'];

                    mysqli_close($database);
                    require_once('config.php');
                    header($root_url.'/dashboard.php');
                } else {
                    $success = $err."Username/Password is incorrect".$end;
                    mysqli_close($database);
                }
            }
        }
        ?>
    </div>

    <!-- Login Form -->
    <div class = "container">
        <!-- Title -->
        <h2>UNIVERSITY EVENT<p>LOGIN</h2>
        <form class = "form-signin" role = "form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <?php echo $success ?>

            <!-- Username -->
            <?php echo $usernameErr ?>
            <input class="form-control" type="text" name="username" placeholder="Username"
                   required autofocus value="<?php echo $username;?>"></br>

            <!-- Password -->
            <?php echo $passwordErr ?>
            <input class="form-control" type="password" name="password" placeholder="Password"
                   required value="<?php echo $password;?>"></br>

            <!-- Submit Button -->
            <button class = "btn btn-lg btn-primary btn-block" type = "submit" name = "login">Log In</button>
        </form>

        <!-- Registration Link -->
        <form class="form-signin" role="form" action="register.php">
            <button class = "btn btn-lg btn-primary btn-block" type = "submit" name = "register">Register</button>
        </form>
    </div>
</body>
</html>
