<?php
	require __dir__ . '/TranslationError.php';

	class TranslationFactory {
		private $languageDao;
		private $partsOfSpeech;

		function __construct($languageDao, $partsOfSpeech) {
			$this->languageDao 		= $languageDao;
			$this->partsOfSpeech 	= $partsOfSpeech;
		}

		function create($req) {
			$lang1_id 			= $req["lang1_id"] ?? null;
			$lang2_id 			= $req["lang2_id"] ?? null;
			$lang1_phrase 	= $req["lang1_phrase"] ?? null;
			$lang2_phrase 	= $req["lang2_phrase"] ?? null;
			$term 					= $req['term'] ?? null;
			$part_of_speech = $req["part_of_speech"] ?? null;
			$singular 			= $req['singular'] ?? null;
			$plural 				= $req['plural'] ?? null;
			$verbs 					= $req["verbs"] ?? null;
			$lang1_def 			= $req["lang1_def"] ?? null;
			$lang2_def 			= $req["lang2_def"] ?? null;
			$lang1_examples = $req["lang1_examples"] ?? null;
			$lang2_examples = $req["lang2_examples"] ?? null;
			$synonyms 			= $req["synonyms"] ?? null;
			$translation 		= [];
			$errors 				= [];

			if (!$lang1_id) {
				array_push($errors, 'lang1_id is required');
			} else {
				$lang1 = $this->languageDao->findById($lang1_id);

				if (!$lang1) {
					array_push($errors, "lang1_id does not exist");
				} else {
					$translation['lang1'] 		= $lang1;
					$translation['lang1_def'] = $lang1_def;
				}
			}

			if (!$lang2_id) {
				array_push($errors, 'lang2_id is required');
			} else {
				$lang2 = $this->languageDao->findById($lang2_id);

				if (!$lang2) {
					array_push($errors, "lang2_id does not exist");
				} else {
					$translation['lang2'] 		= $lang2;
					$translation['lang2_def'] = $lang2_def;
				}
			}

			if ($lang1_id && $lang1_id == $lang2_id) {
				array_push($errors, 'lang1_id and lang2_id cannot be equal');
			}

			if (!$lang1_phrase) {
				array_push($errors, 'lang1_phrase is required');
			} else {
				$translation['lang1_phrase'] = $lang1_phrase;
			}

			if (!$lang2_phrase) {
				array_push($errors, 'lang2_phrase is required');
			} else {
				$translation['lang2_phrase'] = $lang2_phrase;
			}

			if (!$term) {
				$eng_lang_id		= $this->languageDao->findByName('english')['id'];

				if ($lang1_id != $eng_lang_id && $lang2_id != $eng_lang_id) {
					array_push($errors, 'term is required');
				}
			} else {
				$translation['term'] = $term;
			}

			if (!$part_of_speech) {
				array_push($errors, 'part_of_speech is required');
			} else if (!in_array($part_of_speech, $this->partsOfSpeech)) {
				array_push($errors, 'part_of_speech is invalid');
			} else {
				$translation['part_of_speech'] = $part_of_speech;
			}

			if ($part_of_speech == 'NOUN') {
				$translation['singular'] 	= $singular;
				$translation['plural'] 		= $plural;
			}

			if ($part_of_speech == 'VERB') {
				if (is_array($verbs)) {
					$past							= $verbs['past'] ?? null;
					$past_participle	= $verbs['past_participle'] ?? null;
				}

				if (!$past || !$past_participle) {
					array_push($errors, 'verbs is invalid');
				} else {
					$translation['verbs'] = $verbs;
				}
			}

			if ($lang1_examples && !$this->createExamples($lang1_examples)) {
				array_push($errors, 'lang1_examples is invalid');
			} else {
				$translation['lang1_examples'] = $lang1_examples;
			}

			if ($lang2_examples && !$this->createExamples($lang2_examples)) {
				array_push($errors, 'lang2_examples is invalid');
			} else {
				$translation['lang2_examples'] = $lang2_examples;
			}

			if ($synonyms) {
				if (!is_array($synonyms)) {
					array_push($errors, "synonyms must be an array");
				} else {
					foreach ($synonyms as $key => $value) {
						if (is_array($value)) {
							$lang_id 	= $value['language_id'] ?? null;
							$phrase 	= $value['phrase'] ?? null;
							$meaning	= $value['def'] ?? null;
							$examples = $value['examples'] ?? null;

							if (!$this->createSynonym($lang_id, $phrase, $meaning, $examples) || 
								$examples && !$this->createExamples($examples)) {
								array_push($errors, 'synonyms is invalid');
								break;
							}
						} else {
							array_push($errors, 'synonyms is invalid');
						}
					}
					
					$translation['synonyms'] = $synonyms;
				}
			}

			if (count($errors) > 0) {
				throw new TranslationError($errors);
			}
			
			return $translation;
		}

		function createSynonym($language_id, $phrase, $meaning, $examples) {
			$language = $this->languageDao->findById($language_id);

			if (!$language || !$phrase || ($examples && !is_array($examples))) {
				return null;
			}

			return func_get_args();
		}

		function createExamples($examples) {
			if (!is_array($examples)) {
				return null;
			} else {
				foreach ($examples as $key => $value) {
					if (!$value) {
						return null;
						break;
					}
				}

				return $examples;
			}
		}
	}