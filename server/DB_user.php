<?php

include_once('DBConnector.php');

class DB_user
{
	/* properties */
	private $table = 'user';
	private $db;

	/* constructors & destructors */
	public function __construct($db = NULL)
	{
		$this->db = $db == NULL ? new DBConnector() : $db;
	}
	public function __destruct() {}

	/* actions */
	public function insert($name)
	{
		$sql = 'INSERT INTO '.$this->table.' (`name`) VALUES (:name);';
	
		$vars = array(
			array('name' => $name)
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
		$sql = 'DELETE FROM user WHERE '.$key.' = "'.$value.'";';
		return $this->db->delete($sql);
	}
}

?>
