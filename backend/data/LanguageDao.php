<?php
	
	include_once __dir__ . "/BaseDao.php";

	class LanguageDao extends BaseDao {
		function findAll() {
			return parent::_findAll("SELECT * FROM db.language", []);
		}

		function findById($id) {
			return parent::_find(
				"SELECT * FROM db.language WHERE language=:id",
				[':id' => $id]
			);
		}

		function findByName($name) {
			return parent::_find(
				"SELECT * FROM db.language WHERE name=:name",
				[':name' => $name]
			);
		}
	}