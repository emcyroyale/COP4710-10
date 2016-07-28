<?php
require_once ('config.php');
require_once ('check_session.php');
$curUser = $_SESSION['username']
?>

<html>
	<head>
		<meta charset="UTF-8">
		<meta name="description" content="Creates a new University by a Super Admin">
			<title>Create Event</title>
			<link rel = "stylesheet" href = "http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
			<link rel="stylesheet" type="text/css" href="styles.css">

	</head>

	<body>

	<script type="text/javascript">
		function change(obj) {

			var selectBox = obj;
			var selected = selectBox.options[selectBox.selectedIndex].value;
			var element = document.getElementById("rsoDiv");

			if(selected === 'RSO')
			{
				element.style.display = "block";
			}
			else
			{
				element.style.display = "none";
			}
		}
	</script>

    <div class="container form-sigin">
        <?php
        //HTML tags for error messages
        $err = "<h4 class=\"form-signin-error\">";
        $suc = "<h4 class=\"form-signin-success\">";
        $end = "</h4>";
        $success = $event = "";
		$selectLoc = "";
        $rsoErr =  $nameErr = $locErr = $posErr = "";
        $missing_data = [];
        $name = [];
        $RSOname = [];
        $ids = [];
        //Populates dropdown
        require_once('connect.php');

        $queryDB = "SELECT * FROM location";
        $result = mysqli_query($database, $queryDB);
        if(mysqli_num_rows($result) > 0){
            while($row = mysqli_fetch_assoc($result)){
                array_push($name,$row['name']);
            }
        }

		$queryDB = "SELECT * FROM rso WHERE owned_by = '$curUser'";
		$result = mysqli_query($database, $queryDB);
		if(mysqli_num_rows($result) > 0){
				while($row = mysqli_fetch_assoc($result)){
					array_push($RSOname,$row['name']);
				}
		}
        // Check for each required input data that has been POSTed through Request Method
        if ($_SERVER["REQUEST_METHOD"] == "POST")
        {
            $success = $err."Event wasn't created".$end;

			if (empty($_POST["nameTXT"]))
			{
				$missing_data[] = "name";
				$nameErr = $err."Name is required".$end;
			}

			// Creating overrides selecting a location
			if (empty($_POST["locnameTXT"]))
			{
				if (empty($_POST["locSel"]))
				{
					$missing_data[] = "Location";
					$locErr = $err."Location is required".$end;
				}
				else {
					$selectLoc = $_POST["locSel"];
				}
			}
			else
			{

				if (empty($_POST["latTXT"]) || empty($_POST["longTXT"]))
				{
					$missing_data[] = "Position";
					$posErr = $err."Position is required for location creation".$end;
				}
			}

            if (empty($missing_data)) {
                require_once('connect.php');

				if($_POST["typeSELECT"] == "Public"){
					//Event


					$query = "INSERT INTO event (name, date, category, time, phone, email, description,
					location_id, event_type, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
					$stmt = mysqli_prepare($database, $query);
					mysqli_stmt_bind_param($stmt, "ssssssssss", $_POST["nameTXT"], $_POST["dateSELECT"],$_POST["categoryTXT"],
						$_POST["timeSELECT"],$_POST["phoneTXT"],$_POST["emailTXT"], $_POST["descTXTAREA"],
						$selectLoc, $_POST["typeSELECT"], $_SESSION['username']);
					mysqli_stmt_execute($stmt);
					$affected_rows = mysqli_stmt_affected_rows($stmt);
					if ($affected_rows == 1) {
						mysqli_stmt_close($stmt);
						mysqli_close($database);
						$success = $suc."You've create a Public Event".$end;
					}
					else {
						$success = $err."Public Event wasn't created".$end;
						mysqli_stmt_close($stmt);
						mysqli_close($database);
					}
				}
				elseif($_POST["typeSELECT"] == "Private"){
					//Event

					$query = "INSERT INTO event (name, date, category, time, phone, email,
					description, location, event_type, university_id, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
					$stmt = mysqli_prepare($database, $query);
					mysqli_stmt_bind_param($stmt, "sssssssssis", $_POST["nameTXT"], $_POST["dateSELECT"],$_POST["categoryTXT"],
						$_POST["timeSELECT"],$_POST["phoneTXT"],$_POST["emailTXT"], $_POST["descTXTAREA"],$selectLoc, $_POST["typeSELECT"],
						$_SESSION['university'], $_SESSION['username']);
					mysqli_stmt_execute($stmt);
					$affected_rows = mysqli_stmt_affected_rows($stmt);
					if ($affected_rows == 1) {
						mysqli_stmt_close($stmt);
						mysqli_close($database);
						$success = $suc."You've create a Private Event".$end;
					}
					else {
						$success = $err."Private Event wasn't created".$end;
						mysqli_stmt_close($stmt);
						mysqli_close($database);
					}
				}
				else{
					//Event
					$query = "INSERT INTO event (name, date, category, time, phone, email,
					description, location, event_type, rso_id, created_by, App_by_A) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)";
					$stmt = mysqli_prepare($database, $query);
					mysqli_stmt_bind_param($stmt, "sssssssssss", $_POST["nameTXT"], $_POST["dateSELECT"],$_POST["categoryTXT"],
						$_POST["timeSELECT"],$_POST["phoneTXT"],$_POST["emailTXT"], $_POST["descTXTAREA"],$selectLoc, $_POST["typeSELECT"],
						$_POST["rsoSel"], $_SESSION['username']);
					mysqli_stmt_execute($stmt);
					$affected_rows = mysqli_stmt_affected_rows($stmt);
					if ($affected_rows == 1) {
						mysqli_stmt_close($stmt);
						mysqli_close($database);
						$success = $suc."You've create a RSO Event".$end;
					}
					else {
						$success = $err."RSO Event wasn't created".$end;
						mysqli_stmt_close($stmt);
						mysqli_close($database);
					}
				}

            }
        }
        ?>
    </div>
    <div class="flex-container">
        <header>
            <h1> CREATE EVENT </h1>
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
                <form class="form-signin" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
                    <?php echo $success; ?>
					<?php echo $nameErr?>
                    <b>Name </b>
                    <input class="form-control" type="text" name="nameTXT" size=20></input><br />
                    <b>Date </b>
                    <input class="form-control" type="date" name="dateSELECT"></input><br />
                    <b>Time </b>
                    <input class="form-control" type="time" name="timeSELECT"></input><br />
					<b>Phone </b>
                    <input class="form-control" type="text" name="phoneTXT" size=20></input><br /><br />
                    <b>Email </b>
                    <input class="form-control" type="text" name="emailTXT" size=20></input><br /><br />
					<b>Description </b>
                    <textarea class="form-control" col= 200 row=10 name="descTXTAREA"></textarea><br />

					<div class="solid">
						<b>Type </b>
						<select class="form-control" name="typeSELECT" onchange="change(this)">
							<option value="Public"> Public </option>
							<option value="Private"> Private </option>
							<option value="RSO"> RSO </option>
						</select><br /><br />

						<?php echo $rsoErr ?>
						<div id ="rsoDiv" style="display:none">
							<b>RSO </b>
							<select class="form-control" name="rsoSel">
							<?php
									for($x = 0; $x <= count($RSOname); $x++){
										$temp = $RSOname[$x];
										echo "<option value=\"$temp \">" . $RSOname[$x]  . "</option>";
									}
							?>
							</select>
						</div>
					</div>

                    <b>Category </b>
                    <input class="form-control" type="text" name="categoryTXT" size=20></input><br /><br />

					<div class="solid">
						<b  >Location </b>
						<?php echo $locErr?>
						<select class="form-control" name="locSel">
							<?php
							for($x = 0; $x <= count($name); $x++){
								echo "<option value=" . $name[$x] . ">" . $name[$x]  . "</option>";
							}
							?>
						</select><br />
					</div>
					<input class = "btn btn-lg btn-primary btn-block" type="submit" value="Create"></input><br />
				</form>
				<b> Didn't find a location you had in mind? </b><br /><br />
				<!-- GO TO CREATE LOCATION -->
				<!-- Add New Location -->
				<form class="form-signin" action="createloc.php" method="post">
					<input class = "btn btn-lg btn-primary btn-block" type="submit" name="submit" value="Add New Location"><br />
				</form>
        </article>
        <div>
    </body>
</html>
