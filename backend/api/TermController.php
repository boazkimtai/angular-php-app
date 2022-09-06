<?php
	
	class TermController {
		private $termService;

		function __construct($termService) {
			$this->termService = $termService;
		}

		function findTerms() {
			$word 				= Request::query('query');
			$language_id 	= Request::query('lang_id');
			$type 				= strtolower(Request::query('type'));

			try {
				$terms = $this->termService->findTerms($word, $language_id, $type);
				ApiResponse::success($terms);
			} catch(Exception $e) {
				return ApiResponse::error(null, 500);
			}
		}
	}

	function TermController($termService) {
		return new TermController($termService);
	}