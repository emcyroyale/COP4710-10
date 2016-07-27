<?php
    ob_start();

    //  End current session
    if (session_status() == PHP_SESSION_ACTIVE) {
        require_once ('index.php');
        header($uri.'/logout.php');;
    }
?>

<html>
<head>
    <meta charset="UTF-8">
    <meta name="description" content="College Events Registration">
    <link rel = "stylesheet" href = "http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <div class="container form-sigin">
        <!-- Process POST data in this HTML page -->
        <?php

            //HTML tags for error messages
            $err = "<h4 class=\"error\">";
            $suc = "<h4 class=\"form-signin-success\">";
            $end = "</h4>";

        // define variables and set to empty values
            $success = $usernameErr = $passwordErr = $passwordErr2 = $emailErr = $universityErr = "";
            $username = $password = $password2 = $email = $test = $university = "";

            $missing_data = array();

            // Check Universities
            $name = [];
            $ids = [];
            require_once('connect.php');
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

                if (empty($_POST["password2"]))
                {
                    $missing_data[] = "password2";
                    $passwordErr2 = $err."Password is required".$end;
                } else {
                    $password2 = trim_input($_POST["password2"]);
                    if (strcmp($password, $password2) != 0) {
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
                    if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
                        $missing_data[] = "email";
                        $emailErr = $err."Invalid email format".$end;
                    }
                }

                if (empty($_POST["universitySel"]))
                {
                    $missing_data[] = "university";
                    $universityErr = $err."University is required".$end;
                } else {
                    $university = trim_input($_POST["universitySel"]);
                }

                // If no data is missing and it's been validated
                // ADD NEW USER TO DATABASE
                if (empty($missing_data)) {
                    require_once('connect.php');

                    // Add New User
                    $query = "INSERT INTO users (userid, password, email, user_type) VALUES (?, ?, ?, 's')";
                    $stmt = mysqli_prepare($database, $query);

                    mysqli_stmt_bind_param($stmt, "sss", $username, $password, $email);
                    mysqli_stmt_execute($stmt);

                    //  Add New Student
                    $query = "INSERT INTO student (student_id, university) VALUES (?, ?)";
                    $stmt2 = mysqli_prepare($database, $query);
                    mysqli_stmt_bind_param($stmt2, "si", $username, $university);
                    mysqli_stmt_execute($stmt2);

                    $affected_rows = mysqli_stmt_affected_rows($stmt);
                    if ($affected_rows == 1) {
                        mysqli_stmt_close($stmt);
                        mysqli_close($database);
                        $success = $suc."User has been created".$end;;
                        require_once('index.php');
                        header($uri.'/registered.html');

                    } else {
                        $success = $err."Username already exists".$end;
                        mysqli_stmt_close($stmt);
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

    <!-- Registration Form -->
    <div class="container">
        <!-- Title -->
        <h2>UNIVERSITY EVENT<p>REGISTRATION</h2>
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

            <!-- University -->
            <?php echo $universityErr ?>
            <select class="form-control" name="universitySel">
                <?php
                for($x = 0; $x <= count($ids); $x++){
                    echo "<option value=" . $ids[$x] . ">" . $name[$x]  . "</option>";
                }
                ?>
            </select><br /><br />

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