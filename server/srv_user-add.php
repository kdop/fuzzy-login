<?php

header('Cache-Control: no-cache, must-revalidate');
header('Content-type: application/json');

include_once('DB_user.php');
include_once('DB_password.php');
include_once('DB_login.php');
include_once('DB_keystroke.php');
include_once('Keystroke.php');
include_once('Utilities.php');

$validated = true;
$response = array('status' => 1, 'message' => 'New user successfully added to database.');;

if( empty($_POST['name']) || empty($_POST['password']) || empty($_POST['password2']) || empty($_POST['keystrokes']) || empty($_POST['keystrokes2']) ) {
	$validated = false;
	$response = array('status' => false, 'message' => 'Input argumets are wrong.');
}

# Assign parameters to variables
if($validated) {
	$name = $_POST['name'];
	$password = $_POST['password'];
	$password2 = $_POST['password2'];

	$keystrokes = array();
	try {
		foreach($_POST['keystrokes'] as $k) {
			$keystrokes[] = new Keystroke($k['id'], $k['action'], $k['timestamp']);
		}
	}
	catch(Exception $e) {
		$validated = false;
		$response = array('status' => false, 'message' => 'First entry of the password is wrong.');
	}

	# Second password entry.
	$keystrokes2 = array();
	try {
		foreach($_POST['keystrokes2'] as $k) {
			$keystrokes2[] = new Keystroke($k['id'], $k['action'], $k['timestamp']);
		}
	}
	catch(Exception $e) {
		$validated = false;
		$response = array('status' => false, 'message' => 'Second entry of the password is wrong.');
	}
}

if($validated && $password != $password2) {
	$validated = false;
	$response = array('status' => false, 'message' => 'Passwords don\'t match.');
}


if($validated && count($keystrokes) != count($keystrokes2) ) {
	$validated = false;
	$response = array('status' => false, 'message' => 'Keystrokes don\'t match.');
}

# Validate that the keystrokes are in correct format.
# equal amount of u to d events. Every d must be followed by a u.
if($validated) {
	if(! (new Utilities())->validate_keystrokes($keystrokes) || !(new Utilities())->validate_keystrokes($keystrokes2) ) {
		$validated = false;
		$response = array('status' => false, 'message' => 'Keystrokes are invalid.');
	}
}

# Validate that the keystrokes can be normalized.
if($validated) {
	$keystrokes = (new Utilities())->normalize_keystrokes($keystrokes);
	$keystrokes2 = (new Utilities())->normalize_keystrokes($keystrokes2);
	if(count($keystrokes) == 0 || count($keystrokes2) == 0 ) {
		$validated = false;
		$response = array('status' => false, 'message' => 'Keystrokes couldn\'t be normalized.');
	}
}

/**/

if($validated) {
	$max = count($keystrokes);
	for ($i = 0; $i < $max; $i++) {
		if($keystrokes[$i]->key != $keystrokes2[$i]->key || $keystrokes[$i]->action != $keystrokes2[$i]->action ) {
			$validated = false;
			$response = array('status' => false, 'message' => 'Keystrokes don\'t match.');
			break;
		}
	}
}

if($validated) {
	$db = new DBConnector();
	$db->getDBHandler()->beginTransaction();

	try {
		$user_id = (new DB_user($db))->insert($name)[0];
		$password_id = (new DB_password($db))->insert($user_id, $password)[0];
		
		$max = count($keystrokes);
		$login_id = (new DB_login($db))->insert($password_id, date('Y-m-d H:i:s'))[0];
		for ($i = 0; $i < $max; $i++) {
			(new DB_keystroke($db))->insert($login_id, $i, $keystrokes[$i]->key, $keystrokes[$i]->action, $keystrokes[$i]->tstamp)[0];
		}
		
		$login_id = (new DB_login($db))->insert($password_id, date('Y-m-d H:i:s'))[0];
		for ($i = 0; $i < $max; $i++) {
			(new DB_keystroke($db))->insert($login_id, $i, $keystrokes2[$i]->key, $keystrokes2[$i]->action, $keystrokes2[$i]->tstamp)[0];
		}
		
		$db->getDBHandler()->commit();
	}
	catch(PDOException $e)
	{
		$db->getDBHandler()->rollBack();
		$response = array('status' => false, 'message' => $e->getMessage());
	}
}

echo json_encode($response);

?>
