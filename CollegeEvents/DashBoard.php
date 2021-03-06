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
    $ismember = $userUni = $appErr = "";
	$err = "<h4 class=\"form-signin-error\">";
	$suc = "<h4 class=\"form-signin-success\">";
	$end = "</h4>";

    //Gets users university if applicable
    if($_SESSION['user_type']=='s'){
        $queryUni = "select * from student where student_id = '$curUser'";
        $resultUni1 = mysqli_query($database, $queryUni);
        $row1 = mysqli_fetch_assoc($resultUni1);
        $userUni = $row1['university'];
		$_SESSION['university'] = $userUni;
    }
    elseif($_SESSION['user_type']=='a'){
        $queryUni = "select * from admin where admin_id = '$curUser'";
        $resultUni2 = mysqli_query($database, $queryUni);
        $row2 = mysqli_fetch_assoc($resultUni2);
        $userUni = $row2['university'];
		$_SESSION['university'] = $userUni;
    }

	if ($_SERVER["REQUEST_METHOD"] == "POST")
		{
			if(!empty($_POST['appEvent'])) {
				require_once('connect.php');
				// Add New User
				$query = "UPDATE event SET App_by_SA = 1 WHERE name = ?";
				$stmt = mysqli_prepare($database, $query);

				mysqli_stmt_bind_param($stmt, "s", $_POST['appEvent']);
				mysqli_stmt_execute($stmt);

				$affected_rows = mysqli_stmt_affected_rows($stmt);
				if ($affected_rows == 1) {
					mysqli_stmt_close($stmt);
					$appErr = $suc."Event has been approved".$end;

				} else {
					$appErr = $err."Event hasn't been approved".$end;
					mysqli_stmt_close($stmt);
				}
			}

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
									<li><b> <a class = \"btn btn-mg btn-primary btn-block\" href=\"leaveRSO.php\" target=\"_self\"> Leave RSO</a></b></li>
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

	<?php echo $appErr ?>
	<table cellpadding="0" cellspacing="0">
			<tr>
                <th class="tblHead"> Name </th>
                <th class="tblHead"> Date </th>
                <th class="tblHead"> Location </th>
                <th class="tblHead"> Description </th>
                <th class="tblHead"> Phone </th>
                <th class="tblHead"> Time </th>
                <th class="tblHead"> Email </th>
                <th class="tblHead"> Type </th>

				<?php
					if($_SESSION['user_type']=='sa'){
						echo "<th class=\"tblHead\"> Approval </th>";
					}

				?>
            </tr>


			<div>
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
                        if($_SESSION['user_type']!='sa' && $row3['event_type'] == "Public" && $row3['App_by_SA'] == NULL){
                            //echo "blic1<br />";
                        }
                        //Private and not approved by super OR approved but not user not part of uni- dont show
                        elseif(($_SESSION['user_type']!='sa' && $row3['event_type'] == "Private" && $row3['App_by_SA'] == NULL)
                            ||($_SESSION['user_type']!='sa' && $row3['event_type'] == "Private" && $row3['App_by_SA'] != NULL
							&& $userUni!=$row3['university'])){
                            //echo "vite2<br />";
                        }
                        //RSO and not part of rso - dont show
                        elseif($_SESSION['user_type']!='sa' && $row3['event_type'] == "RSO" && !$ismember){
                            //echo "mem3<br />";
                        }
                        //show
                        else{
                            $prefix = $prefix2 = $suffix = '';
                            $prefix = "<form class=\"form-signin\" role=\"form\" action=\"viewEvent.php\" method=\"post\">";
                            $prefix2 = "<form class=\"form-signin\" role=\"form\" action=\"\" method=\"post\">";
                            //$prefix .= "< target=\"_self\" onClick=\"form.submit();\" name=\"name\" value=\"{$row['name']}\">";
                            $prefix .= "<button class = \"btn btn-md btn-primary btn-block\" type = \"submit\"
                                name=\"eventName\" value=\"{$row3['name']}\">";
                            $prefix2 .= "<button class = \"btn btn-md btn-primary btn-block\" type = \"submit\"
                                name=\"appEvent\" value=\"{$row3['name']}\">";
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
								<th>" . $row3['event_type'];

								if($row3['event_type'] == "RSO")
								{
									echo " : " . $row3['rso_id'];
								}

							echo "</th>";

							if($_SESSION['user_type']=='sa'){
								echo "<th>";
								  if($_SESSION['user_type']=='sa' && $row3['event_type'] != "RSO" && $row3['App_by_SA'] == NULL)
									{
										echo $prefix2 . "Approve" . $suffix ;
									}
								echo"</th>";
							}
							echo "</tr>";
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
