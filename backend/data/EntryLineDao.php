<?php
	include_once __dir__ . "/BaseDao.php";

	class EntryLineDao extends BaseDao {
		function create($type, $ref_id, $group_id) {
			$sql = "INSERT INTO db.entry_lines(type, ref_id, group_id) 
			VALUES(:type, :ref_id, :group_id)";
			
			$this->execute($sql, [
				':type' => $type, ':ref_id' => $ref_id, ':group_id' => $group_id
			]);
			
			return $this->conn->lastInsertId();
		}
	}