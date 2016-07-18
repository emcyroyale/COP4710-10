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
				$success = $rso = "";
				$rsoErr =  "";
				$missing_data = [];
				$name = [];
				//Populates dropdown
				require_once('connect.php');
				
				$queryDB = "SELECT * FROM rso";
				$result = mysqli_query($database, $queryDB);
				if(mysqli_num_rows($result) > 0){
						while($row = mysqli_fetch_assoc($result)){
							array_push($name,$row['name']);
						}
				}
				
				if (empty($_POST["rsoSel"]))
					{
						$missing_data[] = "RSO";
						$rsoErr = $err."RSO is required".$end;
					} 
					else {
						$rso = trim_input($_POST["rsoSel"]);
					}
				// Check for each required input data that has been POSTed through Request Method
				if ($_SERVER["REQUEST_METHOD"] == "POST")
				{
					if (empty($missing_data)) {
						require_once('connect.php');
						
						
						$query = "INSERT INTO member (student_id, rso) VALUES (?, ?)";
						$stmt = mysqli_prepare($database, $query);

						mysqli_stmt_bind_param($stmt, "ss", $_SESSION['username'], $rso);
						mysqli_stmt_execute($stmt);
						$affected_rows = mysqli_stmt_affected_rows($stmt);
						if ($affected_rows == 1) {
							mysqli_stmt_close($stmt);
							mysqli_close($database);
							$success = $suc."You've join an RSO".$end;
						} 
						else {
							$success = $err."Please reselect a RSO".$end;
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
				<h1> JOIN RSO </h1>
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
						<?php echo $rsoErr ?>
						<select class="form-control" name="rsoSel">
						<?php
								for($x = 0; $x <= count($name); $x++){
									echo "<option value=" . $name[$x] . ">" . $name[$x]  . "</option>";
								}	
						?>
						<input class = "btn btn-lg btn-primary btn-block" type="submit" value="Join"></input><br />
					</form>
				</div>
			</article>
		<div>
	</body>
	
</html>
