<?php
	include_once __dir__ . "/BaseDao.php";

	class exampleDao extends BaseDao {
		function create($sentence, $translation_id) {
			$sql = "INSERT INTO db.examples(sentence, translation_id) 
			VALUES(:sentence, :translation_id)";
			
			$this->execute($sql, [
				':sentence' => $sentence,
				':translation_id' => $translation_id
			]);

			return $this->conn->lastInsertId();
		}
	}