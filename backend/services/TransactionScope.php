<?php
	
	class TransactionScope {
		private $conn;

		function __construct($conn) {
			$this->conn = $conn;
		}
		
		function beginTransaction() {
			$this->conn->beginTransaction();
		}

		function commit() {
			$this->conn->commit();
		}

		function rollback() {
			$this->conn->rollback();
		}
	}