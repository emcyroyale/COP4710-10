<?php
require_once ('check_session.php');
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
		<?php
			//HTML tags for error messages
			$err = "<h4 class=\"form-signin-error\">";
			$suc = "<h4 class=\"form-signin-success\">";
			$end = "</h4>";
			$success = $uname = $location = $descV = "";
			$nameErr = $locationErr = "";
			$missing_data = [];
			$name = [];
			$ids = [];
			//Populates dropdown
			require_once('connect.php');

			$queryDB = "SELECT * FROM location";
			$result = mysqli_query($database, $queryDB);
			if(mysqli_num_rows($result) > 0){
					while($row = mysqli_fetch_assoc($result)){
						array_push($ids,$row['location_id']);
						array_push($name,$row['name']);
					}
			}

			// Check for each required input data that has been POSTed through Request Method
			if ($_SERVER["REQUEST_METHOD"] == "POST")
			{
				if (empty($_POST["nameTXT"])) {
					$missing_data[] = "name";
					$nameErr = $err."Name is required".$end;
				} else {
					$uname = trim_input($_POST["nameTXT"]);
					// check if username only contains letters and whitespace
					if (!preg_match("/^[a-zA-Z0-9!@#& ]*$/", $uname)){
						$missing_data[] = "name";
						$nameErr = $err."Only letters, digits, and {!, @, #, &} characters are allowed.".$end;
					}
				}
				if (empty($_POST["locSel"]))
				{
					$missing_data[] = "location";
					$locationErr = $err."Location is required".$end;
				}
				else {
					$location = trim_input($_POST["locSel"]);
				}

				if (!empty($_POST["descTA"]))
				{
					$descV = trim_input($_POST["descTA"]);
				}

				if (empty($missing_data)) {
					require_once('connect.php');


					$query = "INSERT INTO university (name, location, description,created_by) VALUES (?, ?, ?, ?)";
					$stmt = mysqli_prepare($database, $query);

					mysqli_stmt_bind_param($stmt, "siss", $uname, $locSel, $descV, $_SESSION['username']);
					mysqli_stmt_execute($stmt);
					$affected_rows = mysqli_stmt_affected_rows($stmt);
					if ($affected_rows == 1) {
						mysqli_stmt_close($stmt);
						mysqli_close($database);
						$success = $suc."University has been created".$end;
					}
					else {
						$success = $err."University already exists".$end;
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
		<div class="flex-container">
			<header>
				<h1> CREATE UNIVERSITY </h1>
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
				<form role="form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
					<?php echo $success; ?>
					<b  >Name </b>
					<?php echo $nameErr ?>
					<input  class="form-control" type="text" name="nameTXT" size=20></input><br />
					<b  >Location </b>
					<?php echo $locationErr ?>
					<select class="form-control" name="locSel">
					<?php
							for($x = 0; $x <= count($ids); $x++){
								echo "<option value=" . $ids[$x] . ">" . $name[$x]  . "</option>";
							}
					?>
					</select><br />
					<b  >Description </b>
					<textarea  class="form-control" col= 200 row=10 name="descTA"></textarea><br /><br />
					<input class = "btn btn-lg btn-primary btn-block" type="submit" value="Create"></input><br />
				</form>

			</article>
		<div>
	</body>
	
</html>
