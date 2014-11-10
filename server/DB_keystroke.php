<?php

include_once('DBConnector.php');

class DB_keystroke
{
	/* properties */
	private $table = 'keystroke';
	private $db;

	/* constructors & destructors */
	public function __construct($db = NULL)
	{
		$this->db = $db == NULL ? new DBConnector() : $db;
	}
	
	public function __destruct() {}

	public function insert($login_id, $keystroke_index, $key, $action, $tstamp)
	{
		$sql = '
			INSERT INTO '.$this->table.' (`login_id`, `keystroke_index`, `key`, `action`, `tstamp`) VALUES (:login_id, :keystroke_index, :key, :action, :tstamp);
		';
	
		$vars = array(
			array(
				'login_id' => $login_id,
				'keystroke_index' => $keystroke_index,
				'key' => $key,
				'action' => $action,
				'tstamp' => $tstamp
			)
		);
		
		return $this->db->insert($sql, $vars);
	}
	
	public function select($columns, $key = null, $value = null)
	{
		$sql = ''; 
		if($key == null || $value == null) {
			$sql = 'SELECT '.$columns.' FROM '.$this->table;
		}
		else {
			$sql = 'SELECT '.$columns.' FROM '.$this->table.' WHERE `'.$key.'` = "'.$value.'";';
		}
		
		return $this->db->select($sql);
	}
	
	public function update($column, $new, $key, $value)
	{
		$sql = 'UPDATE '.$this->table.' SET `'.$column.'` = "'.$new.'" WHERE `'.$key.'` = "'.$value.'";';
		return $this->db->update($sql);
	}
	
	public function delete($key, $value)
	{
		$sql = 'DELETE FROM '.$this->table.' WHERE '.$key.' = "'.$value.'";';
		return $this->db->delete($sql);
	}
}

?>
