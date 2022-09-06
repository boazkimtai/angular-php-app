<?php

	class WordController {
		private $wordService;

		function __construct($wordService) {
			$this->wordService = $wordService;
		}

		function findSuggestions() {
			$phrase 			= Request::query('q');
			$language_id 	= Request::query('lang_id');

			try {
				$results = $this->wordService->findSuggestions($phrase, $language_id);

				ApiResponse::success($results);
			} catch(Exception $e) {
				print_r($e);
				ApiResponse::error(null, 500);
			}
		}
	}

	function WordController($wordService) {
		return new WordController($wordService);
	}