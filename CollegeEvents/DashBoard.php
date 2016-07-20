<?php
    require_once ('check_session.php');
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
        <h1> UNIVERSITY EVENT DASHBOARD </h1>
        <span><b><?php echo "Welcome ". $_SESSION['username'] . "<br />";
                        if($_SESSION['user_type']=='s'){ echo "Student Account";}
                        elseif($_SESSION['user_type']=='a'){ echo "Admin Account";}
                        elseif($_SESSION['user_type']=='sa'){ echo "Super Admin Account";}?></b></span><br />
        <a class="btn btn-xs btn-primary " href="logout.php" target="_self"> Log Out</a><br />
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
                echo " 	<li><b> <a class = \"btn btn-mg btn-primary btn-block\" href=\"dashboard.php\" target=\"_self\"> Dashboard</a></b></li> 
									<li><b> <a class = \"btn btn-mg btn-primary btn-block\" href=\"createEvent.php\" target=\"_self\"> Create Event</a><br /></b></li>
									<li><b> <a class = \"btn btn-mg btn-primary btn-block\" href=\"joinRSO.php\" target=\"_self\"> Join RSO</a></b></li>
									<li><b> <a class = \"btn bt n-mg btn-primary btn-block\" href=\"createRSO.php\" target=\"_self\"> Create RSO</a><br /></b></li>";
            }
            elseif($_SESSION['user_type']== 'sa'){
                echo " 	<li><b> <a class = \"btn btn-mg btn-primary btn-block\" href=\"dashboard.php\" target=\"_self\"> Dashboard</a></b></li> 
									<li><b> <a class = \"btn btn-mg btn-primary btn-block\" href=\"createuniversity.php\" target=\"_self\"> Create University</a></b></li>";
            }
            ?>
        </ul>
    </nav>
    <article class="article">
        <table>
            <tr>
                <th class="tblHead"> Name </th>
                <th class="tblHead"> Date </th>
                <th class="tblHead"> Location </th>
                <th class="tblHead"> Description </th>
                <th class="tblHead"> Phone </th>
                <th class="tblHead"> Time </th>
                <th class="tblHead"> Email </th>
            </tr><br />
            <div class="tblBody">
                <?php
                $queryDB = "select * from event";
                $result = mysqli_query($database, $queryDB);
                //echo mysqli_num_rows($result);
                if(mysqli_num_rows($result) > 0){
                    while($row3 = mysqli_fetch_assoc($result)){
                        //Check if part of rso
                        if($row3['rso_id']!=NULL)
                        {
                            $rsoCur = $row3['rso_id'];
                            $queryDB = "select * from member where student_id = '$curUser' and rso = '$rsoCur'";
                            $resultRSO = mysqli_query($database, $queryDB);
                            $resultRSO = mysqli_num_rows($resultRSO);
                            $ismember = $resultRSO>0;
                        }
                        //--------------------------------------------
                        //Public and not approved by super- dont show
                        if($row3['event_type'] == "Public" && $row3['App_by_SA'] == NULL){
                            //echo "blic1<br />";
                        }
                        //Private and not approved by super OR approved but not user not part of uni- dont show
                        elseif(($row3['event_type'] == "Private" && $row3['App_by_SA'] == NULL)
                            ||($row3['event_type'] == "Private" && $row3['App_by_SA'] != NULL && $userUni!=$row3['university_id'])){
                            //echo "vite2<br />";
                        }
                        //RSO and not part of rso - dont show
                        elseif($row3['event_type'] == "RSO" && !$ismember){
                            //echo "mem3<br />";
                        }
                        //show
                        else{
                            $prefix = $suffix = '';
                            $prefix = "<form class=\"form-signin\" role=\"form\" action=\"viewEvent.php\" method=\"post\">";
                            //$prefix .= "< target=\"_self\" onClick=\"form.submit();\" name=\"name\" value=\"{$row['name']}\">";
                            $prefix .= "<button class = \"btn btn-md btn-primary btn-block\" type = \"submit\" 
                                name=\"eventName\" value=\"{$row3['name']}\">";
                            $suffix = "</button></form>";

                            //echo "else4<br />";
                            echo "<tr>
											<th>" . $prefix . $row3['name']. $suffix . "</th>
											<th>" . $row3['date'] ."</th>
											<th>" . $row3['location'] ."</th>
											<th>" . $row3['description'] ."</th>
											<th>" . $row3['phone'] ."</th>
											<th>" . $row3['time'] ."</th>
											<th>" . $row3['email'] ."</th>
										  </tr><br />
										 ";
                        }
                    }
                }
                ?>
            </div>
        </table>
    </article>
</div>
</body>
</html