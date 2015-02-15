<?php

Class Db {
	
	public $host;
	public $username;
	public $password;
	public $dbname;
	public $conn;

	public function __construct() {

		// Connect to Amazon development RDS
		$this->host = 'localhost'; // Change to RDS Endpoint when ready
		$this->username = 'vampz001';
		$this->password = '3cLtmhmovjQN3DZ';
		$this->dbname = 'vampz';

		try {
			$this->conn = new mysqli($this->host, $this->username, $this->password, $this->dbname);
		}
		catch (Exception $e) {
			die('Could not connect to database.');
		}
		
	}
}