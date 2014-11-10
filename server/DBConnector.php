<?php

class DBConnector
{
	/* properties */
	private $dbh;
	
	private $DB_HOSTNAME = 'localhost';
	private $DB_PREFIX = '';
	private $DB_DATABASE = 'database';
	private $DB_USERNAME = 'username';
	private $DB_PASSWORD = 'password';

	/* constructors & destructors */
	public function __construct()
	{
		try
		{
			$this->dbh = new PDO(
				'mysql:host='.$this->DB_HOSTNAME.';dbname='.$this->DB_PREFIX.$this->DB_DATABASE,
				$this->DB_USERNAME,
				$this->DB_PASSWORD,
				array(
					PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8'
				)
			);
			$this->dbh->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		}
		catch(PDOException $e)
		{
			die($e->getMessage());  
		}
	}

	public function __destruct() { /*$this->dbh = null;*/ }


	/* actions */
	public function getDBHandler()
	{
		return $this->dbh;
	}
	
	public function insert($sql, $vars = null)
	{
		//$dbc = new DBConnector();
		//$dbh = $dbc->getDBHandler();
		
		$stm = $this->dbh->prepare($sql);
		
		//$dbh->beginTransaction();
		//print_r($vars);
		//echo $sql;
		$ids = array();
		foreach($vars as $row)
		{
			//print_r($row);
			$stm->execute($row);
			$ids[] = $this->dbh->lastInsertId();
		}
		//$dbh->commit();
		
		return $ids;
	}
	
	/*
	 * NOTICE !
	 * PDO::FETCH_OBJ creates anonymous objects. As a result the sql
	 * queries affect the structure of those objects. For example the
	 * query "select image as images..." creates objects with an
	 * 'images' variable instead of 'image'.
	*/
	public function select($sql, $vars = null)
	{
		$dbc = new DBConnector();
		$dbh = $dbc->getDBHandler();

		$stm = $dbh->prepare($sql);
		$stm->execute($vars);

		$objects = array();
		$stm->setFetchMode(PDO::FETCH_OBJ);
		foreach( $stm as $object ) { $objects[] = $object; }
		
		return $objects;
	}
	
	public function update($sql, $vars = null)
	{
		$dbc = new DBConnector();
		$dbh = $dbc->getDBHandler();

		$stm = $dbh->prepare($sql);
		$stm->execute($vars);
		
		return 0;
	}
	
	public function delete($sql, $vars = null)
	{
		$dbc = new DBConnector();
		$dbh = $dbc->getDBHandler();

		$stm = $dbh->prepare($sql);
		$stm->execute($vars);
		
		return 0;
	}
}

?>
