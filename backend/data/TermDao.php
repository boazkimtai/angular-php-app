<?php
	include_once __dir__ . "/BaseDao.php";

	class TermDao extends BaseDao {
		function create($name, $type) {
			$sql = "INSERT INTO db.term(name, type) VALUES(:name, :type)";
			
			$this->execute($sql, [':name' => $name, ':type' => $type]);
			return $this->conn->lastInsertId();
		}

		function findByNameType($name, $type) {
			return parent::_find(
				"SELECT term.name, term.id as id FROM db.term 
				WHERE name=:name AND type=:type", 
				[':name' => $name, ':type' => $type]
			);
		}

		function findByWordLanguageType($word, $language_id, $type) {
			$sql = "
				SELECT term.name, term.id, term.type FROM db.word 
				INNER JOIN db.synonym ON synonym.word_id = word.id 
				INNER JOIN db.translation ON translation.id = synonym.translation_id AND translation.language_id = :language_id 
				INNER JOIN db.term ON term.id = translation.term_id
					AND term.type=:type
				WHERE word.name=:word
			";

			return parent::_findAll($sql, [
				':word' => $word, ':language_id' => $language_id, ':type' => $type
			]);
		}
	}