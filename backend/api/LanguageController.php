<?php
	
	class LanguageController {
		private $languageService;

		function __construct($languageService) {
			$this->languageService = $languageService;
		}

		function create($name) {
		}

		function findAll() {
			try {
				$languages = $this->languageService->findLanguages();
				ApiResponse::success($languages);
			} catch(Exception $e) {
				return ApiResponse::error(null, 500);
			}
		}
	}

	function LanguageController($languageService) {
		return new LanguageController($languageService);
	}