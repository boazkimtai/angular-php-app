<?php
	
	class ApiResponse {
		function success($data, $code = null, $headers = []) {
			$body = [
				"success" => true,
				"data" => $data
			];

			Response::json($body, $code, $headers);
		}

		function error($errors, $code = null, $headers = []) {
			$body = [
				"success" => false,
				"errors" => $errors
			];

			Response::json($body, $code, $headers);
		}
	}