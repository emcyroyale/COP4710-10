<?php
    require_once('config.php');
    ob_start();

    //  End current session
    if (session_status() == PHP_SESSION_ACTIVE) {
        header($root_url.'/logout.php');;
    }
?>

<html>
<head>
    <meta charset="UTF-8">
    <meta name="description" content="College Events Super Admin Registration">
    <link rel = "stylesheet" href = "http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <div class="container form-sigin">
        <!-- Process POST data in this HTML page -->
        <?php
            require_once('connect.php');
            // define variables and set to empty values
            $success = $usernameErr = $passwordErr = $passwordErr2 = $emailErr = $universityErr = "";
            $username = $password = $password2 = $email = $test = $university = "";

            $missing_data = array();

            // Check Universities
            $name = [];
            $ids = [];
            $queryDB = "SELECT * FROM university";
            $result = mysqli_query($database, $queryDB);
            $test = mysqli_num_rows($result);
            if(mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    array_push($ids, $row['university_id']);
                    array_push($name, $row['name']);
                }
            }

            // Check for each required input data that has been POSTed through Request Method
            if ($_SERVER["REQUEST_METHOD"] == "POST")
            {
                if (empty($_POST["username"])) {
                    $missing_data[] = "username";
                    $usernameErr = $err."Name is required".$end;
                } else {
                    $username = trim_input($_POST["username"]);
                    $usernameErr = "";
                    require_once('config.php');
                    // check if username only contains letters and whitespace
                    if( !preg_match("/^[a-zA-Z0-9!@#&]{4,20}$/", $username) ){
                        $missing_data[] = "username";
                        $usernameErr = $errName;
                    }
                }

                if (empty($_POST["password"]))
                {
                    $missing_data[] = "password";
                    $passwordErr = $err."Password is required".$end;
                } else {
                    $password = trim_input($_POST["password"]);
                    $passwordErr = "";
                    if(!preg_match("/^[a-zA-Z0-9!@#&]{6,20}$/", $password)){
                        $missing_data[] = "password";
                        $passwordErr = $errPW;
                    }
                }

                if (empty($_POST["password2"]))
                {
                    $missing_data[] = "password2";
                    $passwordErr2 = $err."Password is required".$end;
                } else {
                    $password2 = trim_input($_POST["password2"]);
                    $passwordErr2 = "";

                    // Note: "===" is better at comparing strings than strcmp()
                    if ( !($password === $password2)) {
                        $missing_data[] = "password";
                        $missing_data[] = "password2";
                        $passwordErr2 = $err."Passwords did not match.".$end;
                    }
                }

                if (empty($_POST["email"]))
                {
                    $missing_data[] = "email";
                    $emailErr = $err."Email is required".$end;
                } else {
                    $email = trim_input($_POST["email"]);
                    $emailErr = "";
                    if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
                        $missing_data[] = "email";
                        $emailErr = $err."Invalid email format".$end;
                    }
                }

                // If no data is missing and it's been validated
                // ADD NEW USER TO DATABASE
                if (empty($missing_data)) {
                    //  Add New User
                    $query = "INSERT INTO users (userid, password, email, user_type) VALUES (?, ?, ?, 'sa')";
                    $stmt = mysqli_prepare($database, $query);

                    mysqli_stmt_bind_param($stmt, "sss", $username, $password, $email);
                    mysqli_stmt_execute($stmt);

                    // Add Super Admin
                    $query = "INSERT INTO super_admin (sadmin_id) VALUES (?)";
                    $stmt = mysqli_prepare($database, $query);
                    mysqli_stmt_bind_param($stmt, "s", $username);
                    mysqli_stmt_execute($stmt);

                    // If SQL insertion was successful redirect user to success page
                    $affected_rows = mysqli_stmt_affected_rows($stmt);
                    if ($affected_rows == 1) {
                        mysqli_stmt_close($stmt);
                        mysqli_close($database);
                        $success = $suc."User has been created".$end;
                        header($root_url.'/registered.html');

                    // Insertion Failure, check if its because email was unique
                    } else {
                        $query = "SELECT * FROM users WHERE email = '$email'";
                        $check_email = mysqli_query($database, $query);
                        if(mysqli_num_rows($check_email)>= 1){
                            $row = mysqli_fetch_assoc($check_email);
                            if( $row['email'] === $email )
                                $success = $err."Email is already in use.".$end;
                        } else {
                            $success = $err . "Username already exists" . $end;
                        }
                        mysqli_stmt_close($stmt);
                    }
                }
            }
            mysqli_close($database);
        ?>
    </div>

    <!-- Registration Form -->
    <div class="container">
        <!-- Title -->
        <h2>SUPER ADMIN<p>REGISTRATION</h2>
        <form class="form-signin" role="form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <?php echo $success; ?>

            <!-- Username -->
            <?php echo $usernameErr ?>
            <input class="form-control" type="text" name="username" placeholder="Username"
                   required autofocus value="<?php echo $username;?>"></br>

            <!-- Password -->
            <?php echo $passwordErr ?>
            <input class="form-control" type="password" name="password" placeholder="Password"
                   required value="<?php echo $password;?>"></br>

            <!-- Confirm Password -->
            <?php echo $passwordErr2 ?>
            <input class="form-control" type="password" name="password2" placeholder="Confirm Password"
                   required value="<?php echo $password2;?>"></br>

            <!-- Email -->
            <?php echo $emailErr ?>
            <input class="form-control" type="email" name="email" placeholder="Email"
                   required value="<?php echo $email;?>"></br>

            <!-- Submit Button -->
            <button class = "btn btn-lg btn-primary btn-block" type = "submit" name = "login">Register</button>
        </form>

        <!-- Login Link -->
        <form class="form-signin" role="form" action="logout.php">
            <button class = "btn btn-lg btn-primary btn-block" type = "submit" name = "register">Log In</button>
        </form>
    </div>
</body>
</html>
