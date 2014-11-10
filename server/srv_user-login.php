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

if( empty($_POST['username']) || empty($_POST['password']) || empty($_POST['keystrokes']) ) {
	$validated = false;
	$response = array('status' => false, 'message' => 'Input argumets are wrong.');
}

if($validated) {
	$name = $_POST['username'];
	$_SESSION['name'] = $_POST['username']; // This works without validation only if we use the combo box.
	
	$p_current = $_POST['password'];
	$k_current = array();
	
	try {
		$index = 0;
		foreach($_POST['keystrokes'] as $k) {
			$k_current[] = new Keystroke($k['id'], $k['action'], $k['timestamp'], $index);
			$index++;
		}
	}
	catch(Exception $e) {
		$validated = false;
		$response = array('status' => false, 'message' => 'Keystrokes parameter is invalid.');
	}
}

# Validate that user exist.
if($validated) {
	$data = (new DB_user())->select('*', 'name', $name);
	if(count($data) != 1) {
		$validated = false;
		$response = array('status' => false, 'message' => 'This user doesn\'t exist.');
	}
	else {		
		$user = $data[0];
	}
}

# Validate that password strings match.
if($validated) {
	$data = (new DB_password())->select('*', 'user_id', $user->user_id);
	if(count($data) != 1 || $data[0]->password != $p_current) {
		$validated = false;
		$response = array('status' => false, 'message' => 'Passwords don\'t match.');
	} else { $password = $data[0]; }
}

# Validate that the keystrokes are in correct format.
if($validated) {
	if( !(new Utilities())->validate_keystrokes($k_current) ) {
		$validated = false;
		$response = array('status' => false, 'message' => 'Keystrokes sequence is invalid.');
	}
}

# Validate that the keystrokes can be normalized.
if($validated) {
	$k_current_norm = (new Utilities())->normalize_keystrokes($k_current);
	if(count($k_current_norm) == 0) {
		$validated = false;
		$response = array('status' => false, 'message' => 'Keystrokes couldn\'t be normalized.');
	}
}

//print_r($k_current_norm);

# Validate that there are login data in the database.
if($validated) {
	//$logins = (new DB_login())->select('*', 'password_id', $passwords[0]->password_id);
	$data = (new DBConnector())->select('SELECT * FROM login WHERE `password_id` = '.$password->password_id.' ORDER BY tstamp DESC;');
	if(count($data) == 0) {
		$validated = false;
		$response = array('status' => false, 'message' => 'No login data available.');
	} else { $logins = $data; }
}

# Validate that there are keystroke data in the database.
$stats = array();
//$output = '';
if($validated) {
	$data = (new DB_keystroke())->select('*', 'login_id', $logins[0]->login_id);
	if(count($data) == 0) {
		$validated = false;
		$response = array('status' => false, 'message' => 'No keystroke data available.');
	}
	else {
		$db_keystrokes = $data;
		$m = new Math();
		foreach($db_keystrokes as $k) {
			$data = (new DBConnector())->select('SELECT * FROM keystroke WHERE `keystroke_index` = '.$k->keystroke_index.' AND login_id IN (SELECT login_id FROM login WHERE `password_id` = '.$password->password_id.' ORDER BY tstamp DESC);');
			
			$data_o = array();
			foreach($data as $d) { $data_o[] = new Keystroke($d->key, $d->action, $d->tstamp); }
			
			//print_r($data_o);
			
			$avg = $m->calculate_avg($data_o);
			$delta = $m->calculate_delta($data_o);
			
			if(! $avg['success'] || ! $delta['success']) {
				$validated = false;
				$response = array('status' => false, 'message' => 'Problem in Math library.');
			}
			else {
				$stats[] = new Key_Statistics($avg['result'] , $delta['result']);
			}
		}
	}
}

//echo $validated;

# Validate that keystrokes match.
$current_error_r = 0;
if($validated) {
	$error_counter = 0;
	if(count($db_keystrokes) != count($k_current_norm)) {
		$validated = false;
		$response = array('status' => false, 'message' => 'Different number of keystrokes than expected.');
	}
	else {
		//echo 'ola kala 3';
		for ($i = 0; $i < count($db_keystrokes); $i++) {
			if($db_keystrokes[i]->id != $k_current_norm[$i]->id || $db_keystrokes[$i]->action != $k_current_norm[$i]->action ) {
				$validated = false;
				$response = array('status' => false, 'message' => 'Keystrokes don\'t match.');
				break;
			}
			
			$acceptedKeys[$k_current_norm[$i]->index] = (new Math())->is_value_accepted($stats[$i]->average, $stats[$i]->delta, 3, $k_current_norm[$i]->tstamp);
			
			//print_r($acceptedKeys);
			
			if(!$acceptedKeys[$k_current_norm[$i]->index]['success'] || !$acceptedKeys[$k_current_norm[$i]->index]['result']) {
				$error_counter++;
			}
		}
		
		//echo '-tstamp->' . $logins[0]->tstamp;
		//echo '-tstamp(millis)->' . strtotime($logins[0]->tstamp);
		//echo '-size->' . count($logins);
		
		$no_of_logins = count($logins);
		$t_since_last =  (strtotime('now') * 1000) - (strtotime($logins[0]->tstamp) * 1000);
		//echo $t_since_last;
		
		$fa = new Fuzzy_Algorithm();
		$success_rate = $fa->fuzzyTest($t_since_last, $no_of_logins)['tolerance'];
		
		$success_rate = $success_rate/100;
		
		$current_error_r = $error_counter/count($db_keystrokes);
		
		if($current_error_r > 1 - $success_rate) {
			$validated = false;
			//$response = array('status' => false, 'message' => 'Pattern don\'t match.');
			$response = array('status' => false, 'message' => 'Pattern don\'t match.', 'keystats' => $acceptedKeys, 'error_level' => $current_error_r);
		}
	}
}

if($validated) {
	$db = new DBConnector();
	$db->getDBHandler()->beginTransaction();

	try {
		$login_id = (new DB_login($db))->insert($password->password_id, date('Y-m-d H:i:s'))[0];
		$max = count($k_current_norm);
		for ($i = 0; $i < $max; $i++) {
			(new DB_keystroke($db))->insert($login_id, $i, $k_current_norm[$i]->key, $k_current_norm[$i]->action, $k_current_norm[$i]->tstamp)[0];
		}
				
		$db->getDBHandler()->commit();
	}
	catch(PDOException $e)
	{
		$db->getDBHandler()->rollBack();
		$response = array('status' => false, 'message' => $e->getMessage());
	}
}

if($validated) {
	$response = array('status' => true, 'message' => 'Login successfully added to database.', 'keystats' => $acceptedKeys, 'error_level' => $current_error_r);
}

echo json_encode($response);

?>
