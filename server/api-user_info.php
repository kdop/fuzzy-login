<?php

session_start();

header('Cache-Control: no-cache, must-revalidate');
header('Content-type: application/json');

include_once('DB_user.php');
include_once('DB_password.php');
include_once('DB_login.php');
include_once('DB_keystroke.php');
include_once('Keystroke.php');
include_once('Key_Statistics.php');
include_once('Utilities.php');
include_once('Math.php');
include_once('Fuzzy_Algorithm.php');

$validated = true;
$response = array('success' => true, 'value' => 'Login successfully added to database.');

# Validate that input parameters are not empty.
if( empty($_POST['username']) ) {
	$validated = false;
	$response = array('success' => false, 'value' => 'Input argumets are wrong.');
}

# Validate parameter 'name'.
if($validated) {
	$data = (new DB_user())->select('*', 'name', $_POST['username']);
	if(count($data) != 1) {
		$validated = false;
		$response = array('success' => false, 'value' => 'This user doesn\'t exist.');
	}
	else {		
		$_SESSION['name'] = $data[0];
		$user = $data[0];
	}
}

# Load and validate password.
if($validated) {
	$data = (new DB_password())->select('*', 'user_id', $user->user_id);
	if(count($data) == 0) {
		$validated = false;
		$response = array('success' => false, 'value' => 'Passwords don\'t match.');
	} else { $password = $data[0]; }
}

# Load and validate that there are login data in the database.
if($validated) {
	$data = (new DBConnector())->select('SELECT * FROM login WHERE `password_id` = '.$password->password_id.' ORDER BY tstamp DESC;');
	if(count($data) == 0) {
		$validated = false;
		$response = array('success' => false, 'value' => 'No login data available.');
	} else { $logins = $data; }
}

# Load and validate that there are keystroke data in the database.
if($validated) {
	$data = (new DB_keystroke())->select('*', 'login_id', $logins[0]->login_id);
	if(count($data) == 0) {
		$validated = false;
		$response = array('success' => false, 'value' => 'No keystroke data available.');
	}
	else { $db_keystrokes = $data; }
}	

# Calculate statistics from previous data.
$stats = array();
if($validated) {
	$m = new Math();
	foreach($db_keystrokes as $k) {
		$data = (new DBConnector())->select('SELECT * FROM keystroke WHERE `keystroke_index` = '.$k->keystroke_index.' AND login_id IN (SELECT login_id FROM login WHERE `password_id` = '.$password->password_id.' ORDER BY tstamp DESC) ORDER BY tstamp DESC;');
			
		$data_o = array();
		foreach($data as $d) { $data_o[] = new Keystroke($d->key, $d->action, $d->tstamp); }
			
		$avg = $m->calculate_avg($data_o);
		$delta = $m->calculate_delta($data_o);
			
		if(! $avg['success'] || ! $delta['success']) {
			$validated = false;
			$response = array('success' => false, 'value' => 'Problem in Math library.');
		}
		else {
			$stats[] = new Key_Statistics($avg['result'] , $delta['result']);
			
			$fa = new Fuzzy_Algorithm();
			$no_of_logins = count($logins);
			$t_since_last =  (strtotime('now') * 1000) - (strtotime($logins[0]->tstamp) * 1000);
			$tmp = $fa->fuzzyTest($t_since_last, $no_of_logins);
			$success_rate = $tmp['tolerance'];
			$success_rate = $success_rate/100;
			
			$rules = $tmp['rules'];
			
			$response = array('success' => true, 'value' => array('password' => $password->password, 'keystrokes' => $db_keystrokes, 'statistics' => $stats), 'fuzzy' => $rules, 'no_of_logins' => $no_of_logins, 'dt' => $t_since_last, 'success_rate' => $success_rate );
		}
	}
}

echo json_encode($response);

?>
