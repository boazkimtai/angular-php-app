<?php	
	require __dir__ . "/ApiResponse.php";
	require './data/DBConnection.php';
	require './data/TermDao.php';
	require './data/WordDao.php';
	require './data/LanguageDao.php';
	require './data/TranslationDao.php';
	require './data/SynonymDao.php';
	require './data/ExampleDao.php';
	require './data/EntryGroupDao.php';
	require './data/EntryLineDao.php';
	require './data/types.php';

	require './services/LanguageService.php';
	require './services/TermService.php';
	require './services/WordService.php';
	require './services/TranslationService.php';
	require './services/TransactionScope.php';

	require __dir__ . '/LanguageController.php';
	require __dir__ . '/TermController.php';
	require __dir__ . '/WordController.php';
	require __dir__ . '/TranslationController.php';

	$dbConnection 					= new DBConnection($config);
	$termDao 								= new TermDao($dbConnection->conn);
	$wordDao 								= new WordDao($dbConnection->conn);
	$languageDao 						= new LanguageDao($dbConnection->conn);
	$translationDao 				= new TranslationDao($dbConnection->conn);
	$synonymDao 						= new SynonymDao($dbConnection->conn);
	$exampleDao 						= new ExampleDao($dbConnection->conn);
	$entryGroupDao 					= new EntryGroupDao($dbConnection->conn);
	$entryLineDao 					= new EntryLineDao($dbConnection->conn);

	$transactionScope 			= new TransactionScope($dbConnection->conn);
	$languageService 				= new LanguageService($languageDao);
	$termService 						= new TermService($termDao);
	$wordService 						= new WordService($wordDao);
	$translationService 		= new TranslationService($transactionScope, $termDao, $wordDao, $languageDao, $translationDao, $synonymDao, $exampleDao, $entryGroupDao, $entryLineDao, constant('partsOfSpeech'));
	
	$languageController 		= LanguageController($languageService);
	$termController 				= TermController($termService);
	$wordController 				= WordController($wordDao);
	$translationController 	= TranslationController($translationService);