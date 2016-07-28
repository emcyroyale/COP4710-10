<?php
    require_once ('check_session.php');
    $curUser = $_SESSION['username'];

	if(!isset($_POST['eventName'])){
	if(!isset($_SESSION['eventName'])) {
		require_once('config.php');
		header($root_url . '/dashboard.php');
	} else {
		$event_name = $_SESSION['eventName'];
	}
    } else {
        $event_name = $_POST['eventName'];
        $_SESSION['eventName'] = $event_name;
    }

    if(isset($_POST['editCmt'])){
		$_SESSION['editCmt'] =  $_POST['editCmt'];
    }
?>

<html>
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Dashboard for the Event DB">
    <title>View Event</title>
    <link rel = "stylesheet" href = "http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
	<body>

		<div id="fb-root"></div>
		<script>(function(d, s, id) {
		  var js, fjs = d.getElementsByTagName(s)[0];
		  if (d.getElementById(id)) return;
		  js = d.createElement(s); js.id = id;
		  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.7";
		  fjs.parentNode.insertBefore(js, fjs);
		}(document, 'script', 'facebook-jssdk'));</script>


		<div class="container dashboard-tbl">
			<?php
			require_once('connect.php');
			$err = "<h4 class=\"form-signin-error\">";
			$suc = "<h4 class=\"form-signin-success\">";
			$end = "</h4>";
			$curUser = $_SESSION['username'];
			$comment = $ismember = $userUni = $commentErr = "";
			$success =  "";

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


				if ($_SERVER["REQUEST_METHOD"] == "POST")
				{
					// Check that text are isn't empty
					if(!empty($_POST['comment_text'])) {

						$comment = trim($_POST['comment_text']);
						echo $comment . "|" .  $_SESSION['editCmt'] . "|" .  $curUser . "|" .   $event_name;

						// Ensure comment is 150 characters or less
						if (strlen($comment) > 150) {
							$commentErr = $err."Comments must be 150 characters or less".$end;

						} else {
							require_once('connect.php');
							// Add New User
							$query = "UPDATE comments SET text = ? WHERE time = ? AND student_id = ? AND event = ?";
							$stmt = mysqli_prepare($database, $query);

							mysqli_stmt_bind_param($stmt, "ssss", $comment, $_SESSION['editCmt'],$curUser, $event_name);
							mysqli_stmt_execute($stmt);

							$affected_rows = mysqli_stmt_affected_rows($stmt);
							if ($affected_rows == 1) {
								mysqli_stmt_close($stmt);
								mysqli_close($database);
								$commentErr = $suc."Comment has been update".$end;;
								require_once('config.php');
								header($root_url.'/viewEvent.php');

							} else {
								$commentErr = $err."Comment hasn't been updated".$end;
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
				<h1> EDIT COMMENT </h1>
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


				<form role = "form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
					<!-- Comment Text Box -->
					<div class="form-group">
						<br>
						<label for="comment">Edit Comment:</label>
						<?php echo $commentErr ?>
						<textarea class="form-control" rows="3" placeholder="Enter Comment"
								  name="comment_text" value="<?php echo $comment;?>"></textarea>
					</div>

					<!-- Submit Button -->
					<button class = "btn btn-lg btn-primary btn-block" type="submit" name="submit">Submit</button>
				</form>

				<form method="post" action=viewEvent.php>
					<!-- Submit Button -->
					<button class = "btn btn-lg btn-primary btn-block" type="submit"  name="submit">Go Back</button>
				</form>



			</article>
		</div>
	</body>
</html>
