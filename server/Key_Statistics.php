<?php

class Key_Statistics {
	public $average;
	public $delta;

	public function __construct($average, $delta) {
		$this->average = $average;
		$this->delta = $delta;
	}
    public function __destruct() {}
}

?>