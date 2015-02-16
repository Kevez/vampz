<?php

Class Db {
	
	public $host;
	public $username;
	public $password;
	public $dbname;
	public $conn;

	public function __construct() {

		// Connect to Amazon development RDS
		$this->host = 'vampz-rds-dev.cfwbg1pys1mn.us-west-2.rds.amazonaws.com'; // Change to RDS Endpoint when ready
		$this->username = 'vampz001';
		$this->password = 'DxV2Fc21gb2PJAM';
		$this->dbname = 'vampz';

		try {
			$this->conn = new mysqli($this->host, $this->username, $this->password, $this->dbname);
		}
		catch (Exception $e) {
			die('Could not connect to database.');
		}
		
	}
}