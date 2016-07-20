<?php
    require_once ('check_session.php');

    if(!isset($_POST['eventName'])){
        if(!isset($_SESSION['eventName'])) {
            require_once('index.php');
            header($uri . '/dashboard.php');
        } else {
            $event_name = $_SESSION['eventName'];
        }
    } else {
        $event_name = $_POST['eventName'];
        $_SESSION['eventName'] = $event_name;
    }
?>

<html>
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Dashboard for the Event DB">
    <title>Event Dashboard</title>
    <link rel = "stylesheet" href = "http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
<div class="container dashboard-tbl">
    <?php
    require_once('connect.php');
    $curUser = $_SESSION['username'];
    $ismember = $userUni = "";

    //Gets users university if applicable
    if($_SESSION['user_type']=='s'){
        $queryUni = "select * from student where student_id = '$curUser'";
        $resultUni1 = mysqli_query($database, $queryUni);
        $row1 = mysqli_fetch_assoc($resultUni1);
        $userUni = $row1['university'];
    }
    elseif($_SESSION['user_type']=='a'){
        $queryUni = "select * from admin where admin_id = '$curUser'";
        $resultUni2 = mysqli_query($database, $queryUni);
        $row2 = mysqli_fetch_assoc($resultUni2);
        $userUni = $row2['university'];
    }
    ?>
</div>
<div class="flex-container">
    <header>
        <h1> UNIVERSITY EVENTS </h1>
        <span><b><?php echo "Welcome ". $_SESSION['username'] . "<br />";
                if($_SESSION['user_type']=='s'){ echo "Student Account";}
                elseif($_SESSION['user_type']=='a'){ echo "Admin Account";}
                elseif($_SESSION['user_type']=='sa'){ echo "Super Admin Account";}?></b></span><br />
        <a class="btn btn-xs btn-primary " href="logout.php" target="_self">Log Out</a><br />
    </header>
    <nav class="nav">
        <ul>
            <?php
            if($_SESSION['user_type']== 's'){
                echo " 	<li><b> <a class = \"btn btn-mg btn-primary btn-block\" href=\"dashboard.php\" target=\"_self\">Dashboard</a></b></li> 
									<li><b> <a class = \"btn btn-mg btn-primary btn-block\" href=\"joinRSO.php\" target=\"_self\"> Join RSO</a></b></li> 
									<li><b> <a class = \"btn btn-mg btn-primary btn-block\" href=\"createRSO.php\" target=\"_self\"> Create RSO</a><br /></b></li>";
            }
            elseif($_SESSION['user_type']== 'a'){
                echo " 	<li><b> <a class = \"btn btn-mg btn-primary btn-block\" href=\"dashboard.php\" target=\"_self\">Dashboard</a></b></li> 
									<li><b> <a class = \"btn btn-mg btn-primary btn-block\" href=\"createEvent.php\" target=\"_self\"> Create Event</a><br /></b></li>
									<li><b> <a class = \"btn btn-mg btn-primary btn-block\" href=\"joinRSO.php\" target=\"_self\"> Join RSO</a></b></li>
									<li><b> <a class = \"btn bt n-mg btn-primary btn-block\" href=\"createRSO.php\" target=\"_self\"> Create RSO</a><br /></b></li>";
            }
            elseif($_SESSION['user_type']== 'sa'){
                echo " 	<li><b> <a class = \"btn btn-mg btn-primary btn-block\" href=\"dashboard.php\" target=\"_self\">Dashboard</a></b></li> 
									<li><b> <a class = \"btn btn-mg btn-primary btn-block\" href=\"createuniversity.php\" target=\"_self\"> Create University</a></b></li>";
            }
            ?>
        </ul>
    </nav>
    
    <!-- EVENT TABLE -->
    <article class="article">
        <?php
            $queryDB = "SELECT * FROM event WHERE name=\"{$event_name}\"";
            $result = mysqli_query($database, $queryDB);

            //  Check Result
            if(mysqli_num_rows($result) == 0){
                require_once ('index.php');
                header($uri.'/dashboard.php');
            }

            $row = mysqli_fetch_assoc($result);
            //Check if part of rso
            if($row['rso_id']!=NULL)
            {
                $rsoCur = $row['rso_id'];
                $queryDB = "select * from member where student_id = '$curUser' and rso = '$rsoCur'";
                $resultRSO = mysqli_query($database, $queryDB);
                $resultRSO = mysqli_num_rows($resultRSO);
                $ismember = $resultRSO>0;
            }

            // Get RSO name if its an RSO event
            $rso_name = "";
            if($row['rso_id'] != NULL){
                $rso_name = "<h1><b>RSO: </b>" . ucfirst($row['rso_id']) . "</h1>";
            }

            //  DISPLAY EVENT
            echo "
                            <h1>" . "<b>Title: </b>" . ucfirst($row['name']) . "</h1>
                            <h1>" . "<b>Date: </b>" . ucfirst($row['date']) ."</h1>
                            <h1>" . "<b>Time: </b>" . ucfirst($row['time']) ."</h1>
                            <h1>" . "<b>Category: </b>" . ucfirst($row['category']) . "</h1>
                                           ".$rso_name."
                            <h1>" . "<b>Location: </b>" . ucfirst($row['location']) ."</h1>
                            <h1>" . "<b>Description: </b>" . ucfirst($row['description']) ."</h1>
                            <h1>" . "<b>Phone: </b>" . ucfirst($row['phone']) ."</h1>
                            <h1>" . "<b>Email: </b>" . ucfirst($row['email']) ."</h1>
                         ";

        ?>

        <!-- Enter New Comment -->
        <!-- new comment php code -->
        <?php
            $err = "<h4 class=\"error\">";
            $suc = "<h4 class=\"form-signin-success\">";
            $end = "</h4>";
            $comment = $commentErr = "";

            if ($_SERVER["REQUEST_METHOD"] == "POST")
            {
                // Check that text are isn't empty
                if(!empty($_POST['comment_text'])) {
                    $comment = trim($_POST['comment_text']);

                    // Ensure comment is 150 characters or less
                    if (strlen($comment) > 150) {
                        $commentErr = $err."Comments must be 150 characters or less".$end;

                    } else {
                        require_once('connect.php');
                        // Add New User
                        $query = "INSERT INTO comments (time, student_id, event, text) VALUES (CURRENT_TIMESTAMP(), ?, ?, ?)";
                        $stmt = mysqli_prepare($database, $query);

                        mysqli_stmt_bind_param($stmt, "sss", $curUser, $event_name, $comment);
                        mysqli_stmt_execute($stmt);

                        $affected_rows = mysqli_stmt_affected_rows($stmt);
                        if ($affected_rows == 1) {
                            mysqli_stmt_close($stmt);
                            mysqli_close($database);
                            $commentErr = $suc."Comment has been added".$end;;
                            require_once('index.php');
                            header($uri.'/viewEvent.php');

                        } else {
                            $commentErr = $err."Comment already exists".$end;
                            mysqli_stmt_close($stmt);
                            mysqli_close($database);
                        }
                    }
                }
            }
        ?>

        <!-- Comments -->
        <form role = "form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <table>
                <tr>
                    <th class="tblHead"> Time </th>
                    <th class="tblHead"> User </th>
                    <th class="tblHead"> Comment </th>
                </tr><br />
                <div class="tblBody" >
                    <?php
                    $queryDB = "SELECT * FROM comments WHERE event='$event_name' ORDER BY Time";
                    $result = mysqli_query($database, $queryDB);

                    if(mysqli_num_rows($result) > 0){
                        while($row3 = mysqli_fetch_assoc($result)){
                            echo "<tr>
                                            <th>" . date("D M j, Y", strtotime($row3['Time'])). "
                                            <p>" . date("G:i:s", strtotime($row3['Time'])). "</th>
                                            <th>" . $row3['student_id'] ."</th>
                                            <th>" . $row3['text'] ."</th>
                                          </tr><br />
                                         ";
                        }
                    }
                    ?>
                </div>
            </table>

            <!-- Comment Text Box -->
            <div class="form-group">
                <br>
                <label for="comment">Comment:</label>
                <?php echo $commentErr ?>
                <textarea class="form-control" rows="3" placeholder="Enter Comment"
                          name="comment_text" value="<?php echo $comment;?>"></textarea>
            </div>

            <!-- Submit Button -->
            <button class = "btn btn-lg btn-primary btn-block" type="submit" name="submit">Submit</button>
        </form>
    </article>
</div>
</body>
</html