<?php
	include_once('server/DB_user.php');
 ?>
<html>
	<head>
		<title>Typing Recognition Login System</title>
		<script src="jquery-1.9.1.js" type="text/javascript"></script>
		<script src="capture-useradd.js" type="text/javascript"></script>
		<link rel="stylesheet" href="login.css" type="text/css" />
	</head>
	<body>
		<div id="menu">
			<a href="index.php">Home</a> | <a href="users.php">Users</a> | <a href="login.php">Login</a>
			<hr />
		</div>
		
		<form id="addForm">
			<table border="0">
				<tr><td>Username:</td><td><input type="text" id="username" name="username" /></td></tr>
				<tr><td>Password:</td><td><input type="password" id="password" name="password" /></td></tr>
				<tr><td>Repeat password:</td><td><input type="password" id="password2" name="password2" /></td></tr>
				<tr><td colspan="2" align="right"><input id="login" type="submit" name="login" value="Login" /></td></tr>
			</table>
		</form>
	</body>

</html>
