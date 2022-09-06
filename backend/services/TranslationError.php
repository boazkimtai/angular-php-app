<?php

	class TranslationError extends Exception {
		public $errors;

		function __construct($errors) {
			$this->errors = $errors;
		}
	}