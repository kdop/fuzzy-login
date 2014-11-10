<?php
	include_once('server/DB_user.php');
	include_once('server/DB_password.php');
	include_once('server/DB_login.php');
	include_once('server/DB_keystroke.php');
	
	session_start();
 ?>
<html>
	<head>
		<title>Typing Recognition Login System</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<script src="jquery-1.9.1.js" type="text/javascript"></script>
		<script src="keyboard.js" type="text/javascript"></script>
		<script src="capture.js" type="text/javascript"></script>
		<link rel="stylesheet" href="login.css" type="text/css" />
	</head>
	<body>
		<div id="menu">
			<a href="index.php">Home</a> | <a href="users.php">Users</a> | <a href="login.php">Login</a>
			<hr />
		</div>
		<?php
			$users = (new DB_user())->select('*');
			if(count($users) > 0) {
		?>
		<form id="loginForm">
			<table class="loginform_table">
			<tr>
				<td class="logintable_fl">username:</td>
				<td>
					<select id="username">
				<?php					
					foreach($users as $user) {
					?>
						<option value="<?php echo $user->name; ?>" <?php echo ($user->name == $_SESSION["name"] ? 'selected':''); ?>><?php echo $user->name; ?></option>
					<?php
					}
				?>
						
				</select>
				</td>
			</tr>
			<tr>
				<td>password:</td>
				<td><input type="password" id="password" name="password" /></td>
				<td><div id="pass_hint"></div></td>
			</tr>
			<tr>
				<td></td>
				<td>
					<input id="login" type="submit" name="login" value="Login" />
					<!--<input id="reset" type="button" name="login" value="Reset" />-->
				</td>
			</tr>
		</table>
		</form>
		<br/>
		<a id="secret_switch" href="">Show/Hide details</a>
		<div id='secret'>
		<br/>
		<fieldset>
			<legend class="hd">
				<span class="text">Captured keystrokes:</span>
			</legend>
			<div id='current'></div>
		</fieldset>
		<br/>
		<fieldset>
			<legend class="hd">
				<span class="text">Keystroke Statistics:</span>
			</legend>
			<div id='stats'></div>
		</fieldset>
		<br/>
		<fieldset>
			<legend class="hd">
				<span class="text">Fuzzy Logic:</span>
			</legend>
			<div id='fuzzy'></div>
			<div id='end'></div>
		</fieldset>
		</div>
<?php
		}
		else {
?>
		There are no users in the system.
<?php
		}
?>
	</body>
</html>
