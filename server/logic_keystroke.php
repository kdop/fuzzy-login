<?php

function calc_avg_all($keystroke_array) {
	$sum = 0;

	for ($keystroke_array : $key) {
		$sum += $key->getTstamp();
	}
	
	return $sum/length($keystroke_array);
}

function calc_delta_all($keystroke_array) {
	$avg = calc_avg_all($keystroke_array);	
	$sum = 0;
	
	for ($keystroke_array : $key) {
		$tmp = $key->getTstamp() - $avg;
		$sum += $tmp*$tmp;
	}
	$delta = sqrt($sum/length($keystroke_array));	// define delta
	
	return $delta;
}

// average - d_multi*d < value < average + d_multi*d
function check_acceptance($avg, $d, $d_multi, $value) {
	if(($value <= $avg + ($d*$d_multi)) && ($value >= $avg - ($d*$d_multi))) {
		return true;
	}
	return false;
}

?>
