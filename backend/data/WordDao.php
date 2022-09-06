<?php
	include_once __dir__ . "/BaseDao.php";

	class WordDao extends BaseDao {
		function create($name, $category, $nature) {
			$sql = "INSERT INTO db.word(name, category, nature) 
			VALUES(:name, :category, :nature)";
			
			$this->execute($sql, [
				':name' => $name, ':category' => $category, ':nature' => $nature
			]);

			return $this->conn->lastInsertId();
		}

		function findSuggestions($phrase, $language_id) {
			$sql = "
				SELECT word.name FROM db.word
				INNER JOIN db.synonym ON synonym.word_id=word.id
				INNER JOIN db.translation ON translation.id=synonym.translation_id 
				AND translation.language_id=:language_id
				WHERE word.name LIKE :phrase LIMIT 100";

			return parent::_findAll(
				$sql, [':language_id' => $language_id, ':phrase' => $phrase . '%'], true
			);
		}
	}