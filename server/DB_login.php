<?php

include_once('DBConnector.php');

class DB_login
{
	/* properties */
	private $table = 'login';

	/* constructors & destructors */
	public function __construct($db = NULL)
	{
		$this->db = $db == NULL ? new DBConnector() : $db;
	}
	public function __destruct() {}

	/* actions */
	public function insert($password_id, $tstamp)
	{
		$sql = '
			INSERT INTO '.$this->table.' (`password_id`, `tstamp`) VALUES (:password_id, :tstamp);
		';
	
		$vars = array(
			array(
				'password_id' => $password_id,
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
