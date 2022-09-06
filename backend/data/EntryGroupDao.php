<?php
	include_once __dir__ . "/BaseDao.php";

	class EntryGroupDao extends BaseDao {
		function create() {
			$sql = "INSERT INTO db.entry_groups() VALUES()";
			
			$this->execute($sql);
			return $this->conn->lastInsertId();
		}
	}