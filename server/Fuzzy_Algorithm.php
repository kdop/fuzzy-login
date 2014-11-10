<?php


class Fuzzy_Algorithm
{

//General algorithm:
//1. Calculate Membership degree for each level of every variable for current login
//2. Calculate Firing Strength of each rule
//3. Calculate Center of Gravity

//Data:
//For each variable, define a multidimensional array
//Dimension 1 : Linguistic term for level
//Dimension 2 :
	// - left_zero_limit : Left limit at which membership degree is 0
	// - left_one_limit : Left limit at which membership degree is 1
	// - right_one_limit : Right limit at which membership degree is 1
	// - right_zero_limit : Right limit at which membership degree is 0


//In seconds

public $time;
public $logins;
public $value;	//Received from login


public function __construct()
{
	$this->time = array(
		"short_time" => array (
			"left_zero_limit" => -PHP_INT_MAX,	//This is to show that membership function starts immediately from 0 and onwards
			"left_one_limit" => 0,
			"right_one_limit" => (2 * 24 * 60 * 60 * 1000),
			"right_zero_limit" => (6 * 24 * 60 * 60 * 1000)
		),
		
		"some_time" => array (
			"left_zero_limit" => (2 * 24 * 60 * 60 * 1000),
			"left_one_limit" => (6 * 24 * 60 * 60 * 1000),
			"right_one_limit" => (8 * 24 * 60 * 60 * 1000), 
			"right_zero_limit" => (12 * 24 * 60 * 60 * 1000)
		),
		
		"long_time" => array (
			"left_zero_limit" => (8 * 24 * 60 * 60 * 1000),
			"left_one_limit" => (12 * 24 * 60 * 60 * 1000),
			"right_one_limit" => (16 * 24 * 60 * 60 * 1000), 
			"right_zero_limit" => (24 * 24 * 60 * 60 * 1000)
		),
		
		"very_long_time" => array (
			"left_zero_limit" => (16 * 24 * 60 * 60 * 1000),
			"left_one_limit" => (24 * 24 * 60 * 60 * 1000),
			"right_one_limit" => PHP_INT_MAX, 
			"right_zero_limit" => PHP_INT_MAX
		)
	);
	
	$this->logins = array(
		"few_logins" => array (
			"left_zero_limit" => -PHP_INT_MAX,	//This is to show that membership function starts immediately from 0 and onwards
			"left_one_limit" => 0,
			"right_one_limit" => 6,
			"right_zero_limit" => 12
		),
		
		"some_logins" => array (
			"left_zero_limit" => 6,
			"left_one_limit" => 12,
			"right_one_limit" => 16, 
			"right_zero_limit" => 22
		),
		
		"many_logins" => array (
			"left_zero_limit" => 16,
			"left_one_limit" => 22,
			"right_one_limit" => 26, 
			"right_zero_limit" => 32
		),
		
		"alot_logins" => array (
			"left_zero_limit" => 26,	
			"left_one_limit" => 32,
			"right_one_limit" => PHP_INT_MAX, 
			"right_zero_limit" => PHP_INT_MAX
		)
	);
	
	$this->tolerance = array(
		"very_tolerant" => array (
			"left_zero_limit" => 60,
			"left_one_limit" => 65,
			"right_one_limit" => 65,
			"right_zero_limit" => 70
		),
		
		"tolerant" => array (
			"left_zero_limit" => 65,
			"left_one_limit" => 70,
			"right_one_limit" => 70,
			"right_zero_limit" => 75
		),
		
		"normal" => array (
			"left_zero_limit" => 70,
			"left_one_limit" => 75,
			"right_one_limit" => 75,
			"right_zero_limit" => 80
		),
		
		"strict" => array (
			"left_zero_limit" => 75,	
			"left_one_limit" => 80,
			"right_one_limit" => 80,
			"right_zero_limit" => 85
		),
		
		"very_strict" => array (
			"left_zero_limit" => 80,	
			"left_one_limit" => 85,
			"right_one_limit" => 85,
			"right_zero_limit" => 90
		)
	);
}
public function __destruct() {}


//Step 1: Calculate Membership Degree
//Note: All intervals are open from the left and closed from the right => (x,y]

public function calculateMembershipDegree($variable, $value){	//Variable is array ($time,$logins etc.) , Value is current value
	//If it is not within bounds

	if(!(($value > $variable['left_zero_limit']) && ($value <= $variable['right_zero_limit']))){
		return $membership = 0;
	}
	else{
		//If it is in middle part
		if(($value > $variable['left_one_limit']) && ($value <= $variable['right_one_limit'])){
			return $membership = 1;
		}
		//If it is in left part
		else if($value <= $variable['left_one_limit']){
			//Calculate tangent
			$tangent = 1/($variable['left_one_limit']-$variable['left_zero_limit']);
			//Calculate membership	
			return $membership = (($value-$variable['left_zero_limit'])*$tangent);
		}
		//If it is in the right part
		else{
			//Calculate tangent
			$tangent = 1/($variable['right_zero_limit']-$variable['right_one_limit']);
			//Calculate membership
			return $membership = (($variable['right_zero_limit']-$value)*$tangent);
		}
	}
}

# $time_fll = time from last login.
# $logins = number of logins.
public function fuzzyTest($time_fll, $login_number) {

//Get membership degrees
$time_membership = array();
$login_membership = array();
$tolerance_membership = array();

$time_membership['short_time'] = $this->calculateMembershipDegree($this->time['short_time'], $time_fll);
$time_membership['some_time'] = $this->calculateMembershipDegree($this->time['some_time'], $time_fll);
$time_membership['long_time'] = $this->calculateMembershipDegree($this->time['long_time'], $time_fll);
$time_membership['very_long_time'] = $this->calculateMembershipDegree($this->time['very_long_time'], $time_fll);

$login_membership['few_logins'] = $this->calculateMembershipDegree($this->logins['few_logins'], $login_number);
$login_membership['some_logins'] = $this->calculateMembershipDegree($this->logins['some_logins'], $login_number);
$login_membership['many_logins'] = $this->calculateMembershipDegree($this->logins['many_logins'], $login_number);
$login_membership['alot_logins'] = $this->calculateMembershipDegree($this->logins['alot_logins'], $login_number);

//Step 2: Calculate Firing Strength of each rule

//Model all rules as if-statements and calculate firing strength (tolerance_membership)
//Note: Taking into account the possibility of multiple rules activating with the same level of tolerance
//Note: Firing strength calculated by total

//IF time is SHORT_TIME AND login is FEW_LOGINS THEN tolerance is VERY_TOLERANT
if($time_membership['short_time']!=0 && $login_membership['few_logins']!=0){
	$tolerance_membership['very_tolerant'][] = $time_membership['short_time']*$login_membership['few_logins'];
}

//IF time is SHORT_TIME AND login is SOME_LOGINS THEN tolerance is NORMAL
if($time_membership['short_time']!=0 && $login_membership['some_logins']!=0){
	$tolerance_membership['normal'][] = $time_membership['short_time']*$login_membership['some_logins'];
}

//...
if($time_membership['short_time']!=0 && $login_membership['many_logins']!=0){
	$tolerance_membership['strict'][] = $time_membership['short_time']*$login_membership['many_logins'];
}

//...
if($time_membership['short_time']!=0 && $login_membership['alot_logins']!=0){
	$tolerance_membership['very_strict'][] = $time_membership['short_time']*$login_membership['alot_logins'];
}

//...
if($time_membership['some_time']!=0 && $login_membership['few_logins']!=0){
	$tolerance_membership['very_tolerant'][] = $time_membership['some_time']*$login_membership['few_logins'];
}

//...
if($time_membership['some_time']!=0 && $login_membership['some_logins']!=0){
	$tolerance_membership['tolerant'][] = $time_membership['some_time']*$login_membership['some_logins'];
}

//...
if($time_membership['some_time']!=0 && $login_membership['many_logins']!=0){
	$tolerance_membership['normal'][] = $time_membership['some_time']*$login_membership['many_logins'];
}

//...
if($time_membership['some_time']!=0 && $login_membership['alot_logins']!=0){
	$tolerance_membership['very_strict'][] = $time_membership['some_time']*$login_membership['alot_logins'];
}

//...
if($time_membership['long_time']!=0 && $login_membership['few_logins']!=0){
	$tolerance_membership['very_tolerant'][] = $time_membership['long_time']*$login_membership['few_logins'];
}

//...
if($time_membership['long_time']!=0 && $login_membership['some_logins']!=0){
	$tolerance_membership['tolerant'][] = $time_membership['long_time']*$login_membership['some_logins'];
}

//...
if($time_membership['long_time']!=0 && $login_membership['many_logins']!=0){
	$tolerance_membership['normal'][] = $time_membership['long_time']*$login_membership['many_logins'];
}

//...
if($time_membership['long_time']!=0 && $login_membership['alot_logins']!=0){
	$tolerance_membership['strict'][] = $time_membership['long_time']*$login_membership['alot_logins'];
}

//...
if($time_membership['very_long_time']!=0 && $login_membership['few_logins']!=0){
	$tolerance_membership['very_tolerant'][] = $time_membership['very_long_time']*$login_membership['few_logins'];
}

//...
if($time_membership['very_long_time']!=0 && $login_membership['some_logins']!=0){
	$tolerance_membership['very_tolerant'][] = $time_membership['very_long_time']*$login_membership['some_logins'];
}

//...
if($time_membership['very_long_time']!=0 && $login_membership['many_logins']!=0){
	$tolerance_membership['tolerant'][] = $time_membership['very_long_time']*$login_membership['many_logins'];
}

//...
if($time_membership['very_long_time']!=0 && $login_membership['alot_logins']!=0){
	$tolerance_membership['normal'][] = $time_membership['very_long_time']*$login_membership['alot_logins'];
}

//Step 3: Calculate Center of Gravity

$nominator = 0;
$denominator = 0;

foreach($tolerance_membership as $key => $rule_activated){
	//Calculate center
	$center = ($this->tolerance[$key]['left_zero_limit']+$this->tolerance[$key]['right_zero_limit'])/2;
	//Calculate area:
	//Large side:
	$large_side = $this->tolerance[$key]['right_zero_limit']-$this->tolerance[$key]['left_zero_limit'];
	
	foreach($tolerance_membership[$key] as $val){
		$membership = $val;
			
		$tangent = 1/($this->tolerance[$key]['left_one_limit']-$this->tolerance[$key]['left_zero_limit']);
		$small_side = $large_side-(2*$membership/$tangent);
		
		$area = (($large_side+$small_side)*$membership)/2;
		//Add to sums
		
		$nominator += $center*$area;
		$denominator += $area;
	}

}

return array('tolerance' => $nominator/$denominator, 'rules' => array( 'time' => $time_membership, 'login' => $login_membership, 'tolerance' => $tolerance_membership));

}

}

?>