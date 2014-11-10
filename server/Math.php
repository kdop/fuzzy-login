<?php

class Math {
	public function __construct() {}
    public function __destruct() {}
	
	public function calculate_avg($keystrokes) {
		# Validate if $keystrokes is array.
		if(! is_array($keystrokes) || !isset($keystrokes)) {
			return array('success' => false);
		}
		
		$sum = 0;
		foreach($keystrokes as $k) {
			if(! is_object($k) || get_class($k) != 'Keystroke') {
				echo get_class($k);
				return array('success' => false);
			}
			
			$sum += $k->tstamp;
		}
	
		return array('success' => true, 'result' => $sum/count($keystrokes));
	}


	public function calculate_delta($keystrokes) {
		# Validate if $keystrokes is array.
		if(! is_array($keystrokes) || !isset($keystrokes)) {
			return array('success' => false);
		}
	
		/* todo: better validation here */
		$avg = $this->calculate_avg($keystrokes)['result'];
		
		$sum = 0;
		foreach($keystrokes as $k) {
			if(! is_object($k) || get_class($k) != 'Keystroke') {
				return array('success' => false);
			}
			
			$tmp = $k->tstamp - $avg;
			$sum += ($tmp*$tmp);
		}
		
		$delta = sqrt($sum/count($keystrokes));	// define delta
		
		return array('success' => true, 'result' => $delta);
	}

	// average - d_multi*d < value < average + d_multi*d
	public function is_value_accepted($avg, $d, $d_multi, $value) {
		if(!isset($avg) || !isset($d) || !isset($d_multi) || !isset($value)) {
			return array('success' => false);
		}
		
		/* todo: further validation for correct type */
	
		if(($value >= $avg - ($d*$d_multi)) && ($value <= $avg + ($d*$d_multi))) {
			//$msg = '[avg:'.$avg.', δ:'.$d.' '.'μ:'.$d_multi.'] :: '.($avg - ($d*$d_multi)).' <= '.$value.' <= '.($avg + ($d*$d_multi));
			$msg = array(($avg - ($d*$d_multi)).' <= '.$value.' <= '.($avg + ($d*$d_multi)));
			return array('success' => true, 'result' => true, 'msg' => $msg);
		}
		
		//$msg = '!!! [avg:'.$avg.', δ:'.$d.' '.'μ:'.$d_multi.'] :: '.($avg - ($d*$d_multi)).' <= '.$value.' <= '.($avg + ($d*$d_multi));
		$msg = array('! '.($avg - ($d*$d_multi)).' <= '.$value.' <= '.($avg + ($d*$d_multi)));
		return array('success' => true, 'result' => false, 'msg' => $msg);
	}
}

?>