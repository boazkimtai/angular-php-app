<?php

	class Response {
		function status($code) {
			header("http/1 ${code}");
			return $this;
		}

		function header($key, $value) {
			$this->withHeaders([$key=>$value]);
			return $this;
		}

		function withHeaders($headers = []) {
			foreach ($headers as $key => $value) {
				header("${key}:${value}");
			}
			return $this;
		}

		function json($data, $code, $headers = []) {
			$response = new self;

			$response->status($code)
			->withHeaders($headers)
			->header("content-type", "application/json");
			
			echo json_encode($data);
		}
	}

	function Response($content = null, $code = null) {
		$response = new Response;
		$response->status($code);

		if (isset($content)) {
			echo $content;
		}
		return $response;
	}