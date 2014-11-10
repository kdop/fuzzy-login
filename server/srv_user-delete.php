<?php

header('Cache-Control: no-cache, must-revalidate');
header('Content-type: application/json');

include_once('DB_user.php');
include_once('DB_password.php');
include_once('DB_login.php');
include_once('DB_keystroke.php');

$validated = true;
$response = array('status' => 1, 'message' => 'User successfully deleted from the database.');;

if( empty($_POST['user_id']) ) {
	$validated = false;
}

$user_id = $_POST['user_id'];

if($validated) {
	$db = new DBConnector();

	try {
		$user = (new DB_user($db))->select('*', 'user_id', $user_id);
		if(count($user) == 1) {
			(new DB_user($db))->delete('user_id', $user[0]->user_id);
		}
	}
	catch(Exception $e)
	{
		$response = array('status' => false, 'message' => $e->getMessage());
	}
}

echo json_encode($response);

?>
