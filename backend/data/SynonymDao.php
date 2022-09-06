<?php
	include_once __dir__ . "/BaseDao.php";

	class SynonymDao extends BaseDao {
		function create($word_id, $translation_id) {
			$sql = "INSERT INTO db.synonym(word, translation)
					VALUES(:word_id, :translation_id)";
			
			$this->execute($sql, [
				':word_id' => $word_id,
				':translation_id' => $translation_id
			]);

			return $this->conn->lastInsertId();
		}
	}