<?php
	
	class DBConnection {
		public $conn;

		function __construct($config) {
			$dsn = "mysql:host:${config['host']};dbname:${config['db']}";
			
			$this->conn = new PDO(
				$dsn,
				$config['username'],
				$config['password'],
				[PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
			);
		}
	}