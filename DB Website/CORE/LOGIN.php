<?php
	session_start();
	$error='';
	$u_name='';
	$p_word='';
	if(isset($_POST['subLogin'])){
		if(empty($_POST['usernameTXT']) || empty($_POST['passwordTXT'])){
			$error = "Username or Password is empty";
		}
		else{
			$u_name = stripslashes($_POST['usernameTXT']);
			$p_word = stripslashes($_POST['passwordTXT']);
			//$u_name = @mysqli_real_escape_string($u_name);
			//$p_word = @mysqli_real_escape_string($p_word);
			
			$_SESSION['set'] =  $u_name. " ". $p_word;
			//require_once('../connect.php');
			include('../connect.php');
			
			$query = "SELECT user_type FROM users
					WHERE userid = '$u_name' AND password = '$p_word'";		
		
			$result = @mysqli_query($dbc, $query);
			if(mysqli_num_rows($result)==1)
			{
				$_SESSION['login_user'] = $u_name;
				header("location:DASHBOARD.php");
			}
			else
			{
				$error = "Username or Password is invalid";
			}
			mysqli_close($dbc);
		}
	}
?>