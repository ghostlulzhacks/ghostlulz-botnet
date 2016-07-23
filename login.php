<?php 
	require_once('config.php');
	session_start(); 
	
	if(isset($_SESSION['login'])) { header('Location: list.php'); exit(); } // if all ready loged in 

	if(isset($_POST['login'])){
		$user = $_POST['user'];
		$pass = sha1(md5(htmlspecialchars($_POST['pass'])));
		
		if($user == $username && $pass == sha1(md5(htmlspecialchars($password)))){
				$_SESSION['login'] = true;
				$_SESSION['username'] = $user;
				header('Location: list.php');
		}else{
			$error = 'I\'m a noob';
		}
	}
?>

<html>
	<head>
	<title>GhostBot</title>
	<link  type="text/css" rel="stylesheet" href="ghostbot.css" />
	</head>
	<body>
		<header>
				<img src="logo.png" alt="logo pic">
		</header>
		
		<div id="main" >
			<div id = "center">
				<h2>Login</h2>
				<form action="login.php" method="post">
					<p><label>User </label><input type="text" class="tb" name="user" id="search"/></p>
					<p><label>Pass </label><input type="password" class="tb" name="pass" id="search"/></p>
					<p><label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label><input type="submit" name="login" class="button" value="Login" id="button"  /></p>
				</form>
			</div>
		</div>