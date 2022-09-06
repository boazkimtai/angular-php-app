<?php
	include_once __dir__ . "/BaseDao.php";
	
	class TranslationDao extends BaseDao {
		function create($term_id, $language_id, $meaning) {
			$sql = "INSERT INTO db.translation.term_id_id, language_id, meaning)
				VALUES(:term_id, :language_id, :meaning)";

			$this->execute($sql, [
				':term_id' => $term_id,
				':language_id' => $language_id,
				':meaning' => $meaning
			]);
			
			return $this->conn->lastInsertId();
		}

		function findByWordLanguageTerm($word, $language_id, $term_id) {
			$sql = "
				SELECT translation.id as id, translation.term_id as term_id FROM db.word 
				INNER JOIN db.synonym ON synonym.word_id=word.id 
				INNER JOIN db.translation ON translation.id=synonym.translation_id AND translation.language_id=:language_id AND translation.term_id=:term_id
				WHERE word.name=:word";

			return parent::_find($sql, [
				':word' => $word,
				':language_id' => $language_id,
				':term_id' => $term_id
			]);
		}

		function findByWordLanguage($phrase, $language_id) {
			$sql = "
				SELECT word.name, translation.meaning, term.type FROM db.word
				INNER JOIN db.synonym ON synonym.word_id=word.id
				INNER JOIN db.translation ON translation.id=synonym.translation_id 
				AND translation.language_id=:language_id
				INNER JOIN db.term ON term.id=translation.term_id
				WHERE word.name=:phrase";

			return parent::_findAll($sql, [
				':language_id' => $language_id,
				':phrase' => $phrase
			]);
		}

		function findTranslations($phrase, $lang1_id, $lang2_id) {
			$sql = "
				SELECT word.name, translation.meaning FROM db.translation
				INNER JOIN db.term ON term.id=translation.term_id
				INNER JOIN db.synonym ON synonym.translation_id=translation.id
				INNER JOIN db.word ON word.id=synonym.word_id
				WHERE translation.term_id IN (
					SELECT translation.term_id FROM db.word
					INNER JOIN db.synonym ON synonym.word_id=word.id
					INNER JOIN db.translation ON translation.id=synonym.translation_id AND translation.language_id=:lang1_id
					WHERE word.name=:phrase
				)
				AND translation.language_id=:lang2_id LIMIT 20";

			return parent::_findAll($sql, [
				':phrase' => $phrase,
				':lang1_id' => $lang1_id,
				':lang2_id' => $lang2_id
			]);
		}
	}