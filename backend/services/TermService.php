<?php	
	class TermService {
		private $termDao;

		function __construct($termDao){
			$this->termDao = $termDao;
		}

		function findTerms($word, $language_id, $type) {
			return $this->termDao->findByWordLanguageType($word, $language_id, $type);
		}
	}