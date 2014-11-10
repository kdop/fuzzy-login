<?php

class Keystroke {
    public $key;
    public $action;
    public $tstamp;
	public $index;
   
    public function __construct($key, $action, $tstamp, $index = null) {
    	$this->key = $key;
    	$this->action = $action;
    	$this->tstamp = $tstamp;
		$this->index = $index;
    }
    
    public function __destruct() {
    	$key = '';
    	$action = '';
    	$tstamp = '';
    }
}

?>