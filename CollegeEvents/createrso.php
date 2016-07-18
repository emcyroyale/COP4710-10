<?php
    ob_start();
    session_start();
    if (session_status() == PHP_SESSION_NONE) {
        require_once('index.php');
       header($uri . '/index.php');
    }

?>
<html>
	<head>
		<meta charset="UTF-8">
		<meta name="description" content="Creates a new University by a Super Admin">
			<title>Create University</title>
			<link rel = "stylesheet" href = "http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
			<link rel="stylesheet" type="text/css" href="styles.css">
			
	</head>
	
	<body>
		<div class="container form-sigin">
			<?php       
				//HTML tags for error messages
				$err = "<h4 class=\"form-signin-error\">";
				$suc = "<h4 class=\"form-signin-success\">";
				$end = "</h4>";
				$success = $name1 = $name2 = $name3 = $name4 = $rso =  "";
				$nameErr = $userErr = $uniErr= $rsoErr = "";
				$missing_data = [];
				$univerRSO = [];
				$users = [];
				$curUser = $_SESSION['username'];
				
				
				// Check for each required input data that has been POSTed through Request Method
				if ($_SERVER["REQUEST_METHOD"] == "POST")
				{
					
					
					if (empty($_POST["name1TXT"]) || empty($_POST["name2TXT"]) || empty($_POST["name3TXT"]) ||
					empty($_POST["name4TXT"])) {
						$missing_data[] = "name";
						$nameErr = $err."Name is required".$end;
					} else {
						$name1 = trim_input($_POST["name1TXT"]);
						$name2 = trim_input($_POST["name2TXT"]);
						$name3 = trim_input($_POST["name3TXT"]);
						$name4 = trim_input($_POST["name4TXT"]);
						array_push($users, $curUser, $name1, $name2, $name3, $name4);
						// check if username only contains letters and whitespace
						if ((!preg_match("/^[a-zA-Z0-9!@#&]*$/", $name1)) || (!preg_match("/^[a-zA-Z0-9!@#&]*$/", $name2)) ||
						(!preg_match("/^[a-zA-Z0-9!@#&]*$/", $name3)) || (!preg_match("/^[a-zA-Z0-9!@#&]*$/", $name4))){
							$missing_data[] = "name";
							$nameErr = $err."Only letters, digits, and {!, @, #, &} characters are allowed.".$end;
						}
					}
					
					if (empty($_POST["rsoName"]))
					{
						$missing_data[] = "rso";
						$rsoErr = $err."RSO name is required".$end;
					} else {
						$rso = trim_input($_POST["rsoName"]);
					}
					
					require_once('connect.php');
					foreach($users as $uRSO)
						{
							$queryUR = "SELECT * FROM users WHERE userid = '$uRSO'";
							$resultUR = mysqli_query($database, $queryUR);
							if(mysqli_num_rows($resultUR) != 1)
							{	
								$missing_data[] = "user";
								$userErr = $err."A user is not in the database".$end;
							}
							else
							{
								if($_SESSION['user_type'] =='s')
								{
									$queryUR = "SELECT * FROM student WHERE student_id = '$uRSO'";
									$resultUR = mysqli_query($database, $queryUR);
									$rowUR = mysqli_fetch_assoc($resultUR);
									array_push($univerRSO,$rowUR['university']);
									
								}
								elseif($_SESSION['user_type'] =='a')
								{
									$queryUR = "SELECT * FROM admin WHERE student_id = '$uRSO'";
									$resultUR = mysqli_query($database, $queryUR);
									$rowUR = mysqli_fetch_assoc($resultUR);
									array_push($univerRSO, $rowUR['university']);
								}
							}
						}
						$curUni = array_shift($univerRSO);
						for($x = 1; $x <=4; $x++)
						{
							$stuCur = array_pop($univerRSO);
							if($curUni != $stuCur)
							{
								$missing_data[] = "university";
								$userErr = $err."Not all the same university".$end;
							}
						}
					
					//------------------------------	
					if (empty($missing_data)) {
						require_once('connect.php');
						
						$queryAd = "SELECT * FROM admin WHERE admin_id = '$curUser'";
						$resultAd = mysqli_query($database, $queryAd);
						
						//If not admin already
						if(mysqli_num_rows($resultAd) == 0)
						{
							//echo "Not Admin";
							$queryCA = "INSERT INTO admin (admin_id, university) VALUES (?, ?)";
							$stmt = mysqli_prepare($database, $queryCA);
							mysqli_stmt_bind_param($stmt, "si", $curUser, $curUni);
							mysqli_stmt_execute($stmt);
						}
						//If admin already
						else{
							//echo "Already Admin";
						}
						
						$query = "INSERT INTO rso (name, owned_by) VALUES (?, ?)";
						$stmt = mysqli_prepare($database, $query);
						mysqli_stmt_bind_param($stmt, "ss", $rso, $curUser);
						mysqli_stmt_execute($stmt);
						$affected_rows = mysqli_stmt_affected_rows($stmt);
						if ($affected_rows == 1) {
							mysqli_stmt_close($stmt);
							mysqli_close($database);
							$success = $suc."RSO has been created".$end;
						} 
						else {
							$success = $err."RSO already exists".$end;
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
		<div class="flex-container">
			<header> 
				<h1> CREATE RSO </h1>
				<span><b><?php echo "Welcome ". $_SESSION['username'] . "<br />";
						if($_SESSION['user_type']=='s'){ echo "Student Account";}
						elseif($_SESSION['user_type']=='a'){ echo "Admin Account";}
						elseif($_SESSION['user_type']=='sa'){ echo "Super Admin Account";}?></b></span><br />
				
						
				<a href="LOGOUT.php" target="_self"> Log Out</a><br />
			</header>
			<nav class="nav">
				<ul>
					
					<?php
						if($_SESSION['user_type']== 's'){
							echo " 	<li><b> <a class = \"btn btn-mg btn-primary btn-block\" href=\"dashboard.php\" target=\"_self\"> Dashboard</a></b></li> 
									<li><b> <a class = \"btn btn-mg btn-primary btn-block\" href=\"joinRSO.php\" target=\"_self\"> Join RSO</a></b></li> 
									<li><b> <a class = \"btn btn-mg btn-primary btn-block\" href=\"createrso.php\" target=\"_self\"> Create RSO</a><br /></b></li>";
						}
						elseif($_SESSION['user_type']== 'a'){
							echo " 	<li><b> <a class = \"btn btn-mg btn-primary btn-block\" href=\"dashboard.php\" target=\"_self\"> Dashboard</a></b></li> 
									<li><b> <a class = \"btn btn-mg btn-primary btn-block\" href=\"createEvent.html\" target=\"_self\"> Create Event</a><br /></b></li>
									<li><b> <a class = \"btn btn-mg btn-primary btn-block\" href=\"joinRSO.php\" target=\"_self\"> Join RSO</a></b></li>
									<li><b> <a class = \"btn btn-mg btn-primary btn-block\" href=\"createrso.php\" target=\"_self\"> Create RSO</a><br /></b></li>";
						}
						elseif($_SESSION['user_type']== 'sa'){
							echo " 	<li><b> <a class = \"btn btn-mg btn-primary btn-block\" href=\"dashboard.php\" target=\"_self\"> Dashboard</a></b></li> 
									<li><b> <a class = \"btn btn-mg btn-primary btn-block\" href=\"createuniversity.php\" target=\"_self\"> Create University</a></b></li>";
						}
						?>
				</ul>
			</nav>
			<article class="article">
				<div class="container">
					<form class="form-signin" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
						<?php echo $success; ?>
						<?php echo $rsoErr?>
						<input class="form-control" type="text" name="rsoName" placeholder="RSO Name" required value="<?php echo $rso;?>" size=20></input>
						
						<b  >Usernames </b><br />
						<?php echo $nameErr . "<br />". $userErr ?>
						<input class="form-control" type="text" name="name1TXT" placeholder="user 1" required value="<?php echo $name1;?>" size=20></input>
						<input class="form-control" type="text" name="name2TXT" placeholder="user 2" required value="<?php echo $name2;?>" size=20></input>
						<input class="form-control" type="text" name="name3TXT" placeholder="user 3" required value="<?php echo $name3;?>" size=20></input>
						<input class="form-control" type="text" name="name4TXT" placeholder="user 4" required value="<?php echo $name4;?>" size=20></input>
						<input class = "btn btn-lg btn-primary btn-block" type="submit" value="Create"></input><br />
					</form>
				</div>
			</article>
		<div>
	</body>
	
</html>
