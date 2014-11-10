<?php
	include_once('server/DB_user.php');
 ?>
<html>
	<head>
		<title>Typing Recognition Login System</title>
		<link rel="stylesheet" href="login.css" type="text/css" />
		<script src="jquery-1.9.1.js" type="text/javascript"></script>
		<script>
			function delete_user(id) {
				//Server request.
				var request = $.ajax({
					url: "server/srv_user-delete.php",
					type: "POST",
					data: {
						user_id : id
					},
					dataType: "json"
				});

				request.done(function(response) {
					alert((response.status ? "Success: " : "Error: ") + response.message);
					window.location.href = 'users.php';
				});
	
				request.fail(function(jqXHR, textStatus) {
					alert("Error: ajax request failed: " + textStatus);
				});
			}
		</script>
	</head>
	<body>
		<div id="menu">
			<a href="index.php">Home</a> | <a href="users.php">Users</a> | <a href="login.php">Login</a>
			<hr />
		</div>
		<a href="user-add.php">Add User</a>
		<br />
		<br />
		<table>
<?php					
	$users = (new DB_user())->select('*');
					
	foreach($users as $user) {
?>
			<tr>
				<td>
					<a href="user-info.php?user_id=<?php echo $user->user_id; ?>"><?php echo $user->name; ?></a>
				</td>
				<td>
					<input type="button" id="delete_button" name="delete_button" value="Delete" onclick="delete_user(<?php echo $user->user_id; ?>);" />
				</td>
			</tr>
<?php
	}
?>
		</table>
	</body>
</html>