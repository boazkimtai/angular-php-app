<?php
	
	Response()->withHeaders([
		"Access-Control-Allow-Origin" => "*",
		"Access-Control-Allow-Methods" => "GET, POST, PUT, PATCH, DELETE",
		"Access-Control-Allow-Headers" => "*"
	]);

	Route::get('/languages', [$languageController, 'findAll']);

	Route::get('/terms', [$termController, 'findTerms']);

	Route::get('/suggestions', [$wordController, 'findSuggestions']);
	
	Route::post('/translations', [$translationController, 'create']);
	Route::get('/translations', [$translationController, 'findTranslations']);