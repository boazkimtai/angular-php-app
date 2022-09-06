<?php
	
	class TranslationController {
		private $translationService;

		function __construct($translationService) {
			$this->translationService = $translationService;
		}

		function create() {
			$req = Request::input();

			try {
				$results = $this->translationService->create($req);

				ApiResponse::success($results);
			} catch(Exception $e) {
				if ($e instanceof TranslationError) {
					return ApiResponse::error($e->errors, 400);
				}
				return ApiResponse::error(null, 500);
			}
		}

		function findTranslations() {
			$phrase 	= Request::query('q');
			$lang1_id = Request::query('l1');
			$lang2_id = Request::query('l2');

			try {
				$results = $this->translationService
				->findTranslations($phrase, $lang1_id, $lang2_id);

				ApiResponse::success($results);
			} catch(Exception $e) {
				return ApiResponse::error(null, 400);
			}
		}
	}

	function TranslationController($translationService) {
		return new TranslationController($translationService);
	}