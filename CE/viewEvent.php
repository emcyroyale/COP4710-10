<?php
    require_once ('check_session.php');
    $curUser = $_SESSION['username'];
	 
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
			
			<!-- EVENT TABLE -->
			<article class="article">
				<?php
					$latitude = $longitude = "";
					$queryDB = "SELECT * FROM event WHERE name=\"{$event_name}\"";
					$result = mysqli_query($database, $queryDB);
					

					//  Check Result
					if(mysqli_num_rows($result) == 0){
						require_once ('index.php');
						header($uri.'/dashboard.php');
					}

					$row = mysqli_fetch_assoc($result);
					
					$eventloc = $row['location'];
					$queryDB = "SELECT * FROM location WHERE name=\"{$eventloc}\"";
					$result = mysqli_query($database, $queryDB);
					$row2 = mysqli_fetch_assoc($result);
					$latitude = $row2['latitude'];
					$longitude = $row2['longitude'];
					
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
					echo " <div>
									<b>Title: </b>" . ucfirst($row['name']) .
									"<br /><b>Date: </b>" . ucfirst($row['date']) .
									"<br /><b>Time: </b>" . ucfirst($row['time']) .
									"<br /><b>Category: </b>" . ucfirst($row['category']) . 
									"<br /><b>Location: </b>" . ucfirst($row['location']) .
									"<br /><b>Description: </b>" . ucfirst($row['description']) .
									"<br /><b>Phone: </b>" . ucfirst($row['phone']) .
									"<br /><b>Email: </b>" . ucfirst($row['email']) .
									
							"<br /></div>
								 ";

				?>

				<!-- Enter New Comment -->
				<!-- new comment php code -->
				<?php
					$err = "<h4 class=\"form-signin-error\">";
					$suc = "<h4 class=\"form-signin-success\">";
					$end = "</h4>";
					$comment = $commentErr = $ratingErr= $tblErr = $ratingAvg = "";   
					$success = $stuRating = "";
					$missing_data = [];
					
					require_once('connect.php');
					
					$queryDB = "SELECT * FROM rating WHERE student_id = '$curUser' AND event_id = '$event_name'";
					$result = mysqli_query($database, $queryDB);
					if(mysqli_num_rows($result) > 0){
						$row = mysqli_fetch_assoc($result);
						$stuRating = $row['rating'];
							
					}
					
					$queryDB = "SELECT AVG(rating) AS AVG FROM rating WHERE event_id = '$event_name'";
					$result = mysqli_query($database, $queryDB);
					if(mysqli_num_rows($result) > 0){
						$row = mysqli_fetch_assoc($result);
						$ratingAvg = $row['AVG'];
							
					}
					else{
						$ratingAvg = "-";
					}

					
					
					
					if ($_SERVER["REQUEST_METHOD"] == "POST")
					{
						
						$addRating = false;
						if(!empty($_POST['ratingSel']))
						{
							$queryUni = "select * from rating where student_id = '$curUser' AND event_id = '$event_name'";
							$resultUni1 = mysqli_query($database, $queryUni);
							if(mysqli_num_rows($resultUni1) == 0){
								$query = "INSERT INTO rating (student_id, event_id, rating) VALUES (?, ?, ?)";
								$stmt = mysqli_prepare($database, $query);
								mysqli_stmt_bind_param($stmt, "sss", $curUser, $event_name, $_POST['ratingSel']);
								mysqli_stmt_execute($stmt);
								$affected_rows = mysqli_stmt_affected_rows($stmt);
								if ($affected_rows == 1) {
									mysqli_stmt_close($stmt);
									mysqli_close($database);
									$ratingErr = $suc."Rating has been added".$end;;
									require_once('index.php');
									header($uri.'/viewEvent.php');

								} else {
									$ratingErr = $err."Rating input error".$end;
									mysqli_stmt_close($stmt);
									mysqli_close($database);
								}
							}
							else
							{
								$ratingUp = 
								$query = "UPDATE rating SET rating = ? WHERE student_id = ? AND event_id = ?";
								$stmt = mysqli_prepare($database, $query);
								mysqli_stmt_bind_param($stmt, "sss", $_POST['ratingSel'], $curUser, $event_name);
								mysqli_stmt_execute($stmt);
								$affected_rows = mysqli_stmt_affected_rows($stmt);
								if ($affected_rows == 1) {
									mysqli_stmt_close($stmt);
									mysqli_close($database);
									$ratingErr = $suc."Rating has been changed".$end;;
									require_once('index.php');
									header($uri.'/viewEvent.php');

								} else {
									$ratingErr = $err."Rating change error".$end;
									mysqli_stmt_close($stmt);
									mysqli_close($database);
								}
							}
						}
						
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
						
						//Delete
						if(isset($_POST['deleteCmt']))
						{
							require_once('connect.php');
							$query = "DELETE FROM comments WHERE time = ? AND student_id = ? AND event = ?";
							$stmt = mysqli_prepare($database, $query);

							mysqli_stmt_bind_param($stmt, "sss", $_POST['deleteCmt'],$curUser, $event_name);
							mysqli_stmt_execute($stmt);
							
							$affected_rows = mysqli_stmt_affected_rows($stmt);
							if ($affected_rows == 1) {
								mysqli_stmt_close($stmt);
								mysqli_close($database);
								$tblErr = $suc."Comment has been deleted".$end;;
								require_once('index.php');
								header($uri.'/viewEvent.php');

							} else {
								$tblErr = $err."Comment hasn't been deleted".$end;
								mysqli_stmt_close($stmt);
								mysqli_close($database);
							}
						}
					}
				?>
				
				<!-- Map -->
				<!DOCTYPE html>
				<html>
				  <head>
					<style>
					  #map {
						width: 50%;
						height: 500px;
					  }
					</style>
				  </head>
				  <body>
					<div id="map"></div>
					<script>
					  function initMap() {
						var mapDiv = document.getElementById('map');
						var map = new google.maps.Map(mapDiv, {
							center: {lat: <?php echo $longitude;?> , lng: <?php echo $latitude;?>},
							zoom: 14
						});
					  }
					</script>
					<script async defer
						src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBFi-DLVYrNTd0dDMaeXbCaGvu2twbZTKU&callback=initMap">
					</script>
				  </body>
				</html>		
				
				<!-- Comments -->
				<form role = "form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
				  
					<div class="fb-share-button"  data-layout="button_count" data-size="small" data-mobile-iframe="true"><a class="fb-xfbml-parse-ignore" 
					target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=https%3A%2F%2Fdevelopers.facebook.com%2Fdocs%2Fplugins%2F&amp;src=sdkpreparse">Share</a></div>
					
					<div>
						  <b>Average Rating: </b>  <?php echo $ratingAvg; ?>
						  <b>Your Rating: </b>  <?php echo $stuRating; ?>
					</div>
					
					<?php echo $ratingErr ?>
					<select class="form-control" name="ratingSel">
							<option value="">   </option>
							<option value="1">  ★  </option>
							<option value="2">  ★★  </option>
							<option value="3">  ★★★  </option>
							<option value="4">  ★★★★ </option>
							<option value="5">  ★★★★★  </option>
										
								
					</select>
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
				
					
				<?php echo $tblErr ?>
				  <table>
						<tr>
							<th class="tblHead"> Time </th>
							<th class="tblHead"> User </th>
							<th class="tblHead"> Comment </th>
							<th class="tblHead"> Edit </th>
							<th class="tblHead"> Delete </th>
						</tr>
						<div>
							<?php
							$queryDB = "SELECT * FROM comments WHERE event='$event_name' ORDER BY time";
							$result = mysqli_query($database, $queryDB);

							if(mysqli_num_rows($result) > 0){
								while($row3 = mysqli_fetch_assoc($result)){
									$prefix = $prefix2 = $suffix = '';
									$prefix =  "<form class=\"form-signin\" role=\"form\" action=\"editCmt.php\" method=\"post\">";
									$prefix2 = "<form class=\"form-signin\" role=\"form\" action=\"\" method=\"post\">";
									$prefix .= "<button class = \"btn btn-md btn-primary btn-block\" type = \"submit\" 
										name=\"editCmt\" value=\"{$row3['time']}\">";
									$prefix2 .= "<button class = \"btn btn-md btn-primary btn-block\" type = \"submit\" 
										name=\"deleteCmt\" value=\"{$row3['time']}\">";
									$suffix = "</button></form>";
									
									echo "<tr>
													<th>" . date("D M j, Y", strtotime($row3['time'])). "
													<p>" . date("G:i:s", strtotime($row3['time'])). "</th>
													<th>" . $row3['student_id'] ."</th>
													<th>" . $row3['text'] ."</th>";
													
												if($curUser == $row3['student_id']){	
													echo"<th>" . $prefix . "Edit" . $suffix . "</th>
														<th>" . $prefix2 . "Delete" . $suffix . "</th>";
												}
												else{
													echo"<th></th><th></th>";
												}
												
												echo "</tr> ";
								}
							}
							?>
						</div>
					</table>
					
				
			</article>
		</div>
	</body>
</html>