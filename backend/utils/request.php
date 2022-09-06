<?php
	
	class Request {
		static function method() {
			return strtolower($_SERVER['REQUEST_METHOD']);
		}

		static function path() {
			return parse_url($_SERVER["REQUEST_URI"])['path'];
		}

		static function input($key = null) {
			$phpInput = json_decode(file_get_contents('php://input'), 1);
			return $key ? $phpInput[$key] ?? null : $phpInput;
		}

		static function query($key) {
			return $_GET[$key] ?? null;
		}
	}