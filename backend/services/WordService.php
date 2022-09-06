<?php

	 class WordService {
	 	private $wordDao;

	 	function __construct($wordDao) {
	 		$this->wordDao = $wordDao;
	 	}

	 	function findSuggestions($phrase, $language_id) {
	 		return $this->wordDao->findSuggestions($phrase, $language_id);
	 	}
	 }