<?php
	include('session.php');
?>
<html>
	<head>
			<meta charset="UTF-8">
			<meta name="description" content="Dashboard for the Event DB">
			<title>Event Dashboard</title>
			<link rel="stylesheet" href="styles.css">
	</head>
	<body>
		<div class="flex-container">
			<header> 
				<h1> UNIVERSITY EVENT DASHBOARD </h1>
				<span><b><?php echo "Welcome ". $_SESSION['login_user']; ?></b></span><br />
				<a href="LOGOUT.php" target="_self"> Log Out</a><br />
			</header>
			<nav class="nav">
				<ul>
					<li><b> BLAH1 </b></li>
					<li><b> BLAH2 </b></li>
					<li><b> BLAH3 </b></li>
				</ul>
			</nav>
			<article class="article">
				<table>
					<tr>
						<th> Name </th>
						<th> Date </th>
					</tr><br />
									 
					<?php
					
						$queryDB = "select * from event";
						$result = mysqli_query($dbc, $queryDB);
						echo mysqli_num_rows($result);
						if(mysqli_num_rows($result) > 0){
							while($row = mysqli_fetch_assoc($result)){
								echo "<tr>
										<th>" . $row['name'] ."</th>
										<th>" . $row['date'] ."</th>
									  </tr><br />
									 ";
							}
						}
					?>
				</table>
			</article>
		</div>
	</body>
</html>
