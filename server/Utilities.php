<?php

class Utilities {
  
    public function __construct() {}
    public function __destruct() {}
	
	public function validate_keystrokes($keystrokes) {
		$down_events = array();
		$index = 0;
		
		# Validate if $keystrokes is array.
		if(! is_array($keystrokes)) {
			return false;
		}
		
		foreach($keystrokes as $k) {
			# Validate if $keystrokes is array of Keystrokes.
			if(! is_object($k) || get_class($k) != 'Keystroke') {
				return false;
			}
			
			# Up event can't be before down
			if($k->action == 'u') {
				//echo '{'.$k->key.','.$k->action.'}';
				if($down_events[$k->key] == 0) {
					return false;
				}
				else {
					// if there are u events before d, in the end $down_events > 0.
					$down_events[$k->key]--;
				}
				//print_r($down_events);
			}
			
			if($k->action == 'd') {
				//echo '{'.$k->key.','.$k->action.'}';
				
				/*if($previous_action == $k->action && previous_id == $k->key) {
					// Repeated key down.
				}	
				else {
					$down_events[$k->key]++;
				}*/
				
				$down_events[$k->key]++;
				//print_r($down_events);
				
				//$previous_action = $k->action;
				//$previous_id = $k->key;
			}
			
		}
		
		//print_r($keystrokes);
		
		foreach($down_events as $d_events) {
			if($d_events > 0) { return false; }
		}
		
		return true;
	}
	
	public function normalize_keystrokes($keystrokes) {
		# Validate if $keystrokes is array.
		if(! is_array($keystrokes)) {
			return array();
		}
		
		$normalized = array();
		
		$arr_size = count($keystrokes);
		for($i = 0; $i < $arr_size; $i++) {
			# Validate if $keystrokes is array of Keystrokes.
			if(! is_object($keystrokes[$i]) || get_class($keystrokes[$i]) != 'Keystroke') {
				//echo 'failed no object';
				return array();
			}
			
			//echo 'current {'.$keystrokes[$i]->key.','.$keystrokes[$i]->action.'}';
			
			if($keystrokes[$i]->action == 'd') {
				$normalized[] = $keystrokes[$i];
				$found_up_event = false;
				//echo 'found: {'.$keystrokes[$i]->key.','.$keystrokes[$i]->action.'}';
				for($j = $i; $j < $arr_size; $j++) {
					//echo 'looking: {'.$keystrokes[$j]->key.','.$keystrokes[$j]->action.'}';
					
					if($keystrokes[$j]->key == $keystrokes[$i]->key && $keystrokes[$j]->action == 'u') {
						//echo "found a match";
						$found_up_event = true;
						$normalized[] = $keystrokes[$j];
						break;
					}
				}
				
				# Is it really needed to check again if there is a d without u.
				//if(! $found_up_event ) { return array(); }
			}
		}
		
		return $normalized;
	}
}

?>