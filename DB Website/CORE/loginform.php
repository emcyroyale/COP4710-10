<?php
	include('LOGIN.php');
	if(isset($_SESSION['login_user'])){
		header("location: dashboard.php");
		//session_destroy();
		//echo "destroyed <br />";
	}
	if(isset($_SESSION['set'])){
		//echo $_SESSION['set']; 
	}
?>
	<html>
		<head>
			<meta charset="UTF-8">
			<meta name="description" content="Login to UCF events viewer">
			<title>Login University Event DB </title>
			<link rel="stylesheet" href="styles.css">
				
		</head>
		
		<body>
			<h1> UNIVERSITY EVENT LOGIN </h1>
			
			
			<form action="" method="post">
				<b class="left" >Username </b>
				<input  class="right" type="text" name="usernameTXT" size=20></input><br />
				<b class="left" >Password </b>
				<input class="right" type="password" name="passwordTXT" size=20></input><br />
				<input type="submit" name ="subLogin" value="Login"></input><br />
			</form>
			<a href="register.html" target="_self"> Register</a><br />
			<span><?php echo '<b>'.$error.'</b>'; ?></span>
		</body>
		
	</html>
