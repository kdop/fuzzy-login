<?php
	include_once('server/DB_user.php');
	include_once('server/DB_password.php');
	include_once('server/DB_login.php');
	include_once('server/DB_keystroke.php');
 ?>
<html>
	<head>
		<title>Typing Recognition Login System</title>
		<script src="jquery-1.9.1.js" type="text/javascript"></script>
		<link rel="stylesheet" href="login.css" type="text/css" />
	</head>
	<body>
		<div id="menu">
			<a href="index.php">Home</a> | <a href="users.php">Users</a> | <a href="login.php">Login</a>
			<hr />
		</div>
	<?php					
	$users = (new DB_user())->select('*', 'user_id', $_GET['user_id']);
	if(count($users) == 1) {
		$passwords = (new DB_password())->select('*', 'user_id', $users[0]->user_id);
		$logins = (new DB_login())->select('*', 'password_id', $passwords[0]->password_id);
	?>
		username: <?php echo $users[0]->name; ?><br />
		password: <?php echo $passwords[0]->password; ?><br />
		keystrokes:<br />
		<table border="1">
		<?php
		foreach($logins as $login) {
			$keystrokes = (new DB_keystroke())->select('*', 'login_id', $login->login_id);
		?>
			<tr>
		<?php
			foreach($keystrokes as $keystroke) {
		?>
				<td><?= $keystroke->key ?></td>
				<td><?= $keystroke->action ?></td>
				<td><?= $keystroke->tstamp ?></td>
		<?php
			}
		?>
			</tr>
		<?php
		}
		?>
		</table>
	<?php
	}
	else {
	?>
	This user doesn't exist.
	<?php
	}
	?>
	</body>
</html>
