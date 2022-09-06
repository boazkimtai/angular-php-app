<?php
	class LanguageService {
		private $languageDao;

		function __construct($languageDao){
			$this->languageDao = $languageDao;
		}

		function findLanguages() {
			return $this->languageDao->findAll();
		}

		function findLanguageById($id) {
			return $this->languageDao->findById($id);
		}
	}