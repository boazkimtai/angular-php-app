<?php
	
	class BaseDao {
		protected $conn;

		function __construct($conn) {
			$this->conn = $conn;
		}

		protected function execute($sql, $values = []) {
			$s = $this->conn->prepare($sql);
			$s->execute($values);
			return $s;
		}

		protected function _findAll($sql, $values, $fetchColumns = null) {
			return $this->execute($sql, $values)
			->fetchAll($fetchColumns ? PDO::FETCH_COLUMN : null);
		}

		protected function _find($sql, $values, $fetchColumns = null) {
			return $this->execute($sql, $values)
			->fetch($fetchColumns ? PDO::FETCH_COLUMN : null);
		}
	}