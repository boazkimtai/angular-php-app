<?php
	require __dir__ . '/TranslationFactory.php';

	class TranslationService {
		private $transactionScope;
		private $termDao;
		private $wordDao;
		private $languageDao;
		private $translationDao;
		private $synonymDao;
		private $exampleDao;
		private $entryGroupDao;
		private $entryLineDao;
		private $partsOfSpeech;

		function __construct(
			$transactionScope,
			$termDao,
			$wordDao,
			$languageDao,
			$translationDao,
			$synonymDao,
			$exampleDao,
			$entryGroupDao,
			$entryLineDao,
			$partsOfSpeech
		) {
			$this->transactionScope = $transactionScope;
			$this->termDao 					= $termDao;
			$this->wordDao 					= $wordDao;
			$this->languageDao			=	$languageDao;
			$this->translationDao 	= $translationDao;
			$this->synonymDao 			= $synonymDao;
			$this->exampleDao				= $exampleDao;
			$this->entryGroupDao		= $entryGroupDao;
			$this->entryLineDao			= $entryLineDao;
			$this->partsOfSpeech		= $partsOfSpeech;
		}

		function create($req) {
			$translationFactory = new TranslationFactory($this->languageDao, $this->partsOfSpeech);

			$translation = $translationFactory->create($req);

			if ($translation) {
				$eng_lang_id		= $this->languageDao->findByName('english')['id'];
				$eng_def				= null;
				$term 					= null;
				$term_id 				= null;
				$term_name			= '';
				$part_of_speech  	= strtolower($translation['part_of_speech']);
				$eng_examples 	= null;
				$entries 				= [];

				$this->transactionScope->beginTransaction();

				try {
					if ($translation['lang1']['id'] == $eng_lang_id) {
						$eng_def			= $translation['lang1_def'];
						$term_name		= $translation['lang1_phrase'];
						$eng_examples = $translation['lang1_examples'];

						$term 				= $this->termDao->findByNameType($term_name, $part_of_speech);
					} else if ($translation['lang2']['id'] == $eng_lang_id) {
						$eng_def			= $translation['lang2_def'];
						$term_name		= $translation['lang2_phrase'];
						$eng_examples = $translation['lang2_examples'];

						$term 				= $this->termDao->findByNameType($term_name, $part_of_speech);
					} else {
						$term_name = $translation['term'];

						$term = $this->termDao->findByNameType($translation['term'], $part_of_speech);
					}

					if ($term) {
						$term_id = $term["id"];
					} else {
						$term_id = $this->termDao->create($term_name, $part_of_speech);
						
						$ids = $this->createSynonym(
							$term_id,
							$eng_lang_id,
							$term_name,
							$eng_def
						);

						array_push($entries, ['term', $term_id]);
						$entries = array_merge($entries, $this->getEntriesFromIds($ids));

						if ($eng_examples) {
							$this->createExamplesEntries(
								$eng_examples,
								$ids['translation_id'],
								$entries
							);
						}
					}

					if ($translation['lang1']['id'] != $eng_lang_id) {
						$ids = $this->createSynonym(
							$term_id,
							$translation['lang1']['id'],
							$translation['lang1_phrase'],
							$translation['lang1_def']
						);
						
						$entries = array_merge($entries, $this->getEntriesFromIds($ids));

						if ($translation['lang1_examples']) {
							$this->createExamplesEntries(
								$translation['lang1_examples'],
								$ids['translation_id'],
								$entries
							);
						}
					}

					if ($translation['lang2']['id'] != $eng_lang_id) {
						$ids = $this->createSynonym(
							$term_id,
							$translation['lang2']['id'],
							$translation['lang2_phrase'],
							$translation['lang2_def']
						);
						
						$entries = array_merge($entries, $this->getEntriesFromIds($ids));

						if ($translation['lang2_examples']) {
							$this->createExamplesEntries(
								$translation['lang2_examples'],
								$ids['translation_id'],
								$entries
							);
						}
					}

					if ($translation['synonyms'] ?? null) {
						foreach ($translation['synonyms'] as $key => $value) {
							$ids = $this->createSynonym(
								$term_id,
								$value['language_id'],
								$value['phrase'],
								$value['def']
							);

							$entries = array_merge($entries, $this->getEntriesFromIds($ids));

							if ($value['examples']) {
								$this->createExamplesEntries(
									$value['examples'],
									$ids['translation_id'],
									$entries
								);
							}
						}
					}

					if ($translation['singular'] ?? null) {
						$ids = $this->createSynonym(
							$term_id,
							$eng_lang_id,
							$translation['singular'],
							'',
							'noun',
							'singular'
						);

						$entries = array_merge($entries, $this->getEntriesFromIds($ids));
					}

					if ($translation['plural'] ?? null) {
						$ids = $this->createSynonym(
							$term_id,
							$eng_lang_id,
							$translation['plural'],
							'',
							'noun',
							'plural'
						);

						$entries = array_merge($entries, $this->getEntriesFromIds($ids));
					}

					if ($translation['verbs'] ?? null) {
						$verbs = $translation['verbs'];

						$ids = $this->createSynonym(
							$term_id,
							$eng_lang_id,
							$verbs['past'],
							'',
							'verb',
							'past'
						);

						$entries = array_merge($entries, $this->getEntriesFromIds($ids));

						$ids = $this->createSynonym(
							$term_id,
							$eng_lang_id,
							$verbs['past_participle'],
							'',
							'verb',
							'past_participle'
						);

						$entries = array_merge($entries, $this->getEntriesFromIds($ids));
					}
					
					$this->createEntries($entries);
					$this->transactionScope->commit();
				} catch (Exception $e) {
					$this->transactionScope->rollback();
				}
			}
		}

		function createSynonym($term_id, $lang_id, $phrase, $meaning, $category = null, $nature = null) {
			$translation 	= $this->translationDao->findByWordLanguageTerm(
				$phrase, $lang_id, $term_id
			);
			$is_linked 		= !!$translation;

			if (!$is_linked) {
				$trans_id		= $this->translationDao->create($term_id, $lang_id, $meaning);
				$word_id		= $this->wordDao->create($phrase, $category, $nature);
				$synonym_id	= $this->synonymDao->create($word_id, $trans_id);
				return [
					'translation_id' => $trans_id,
					'word_id' => $word_id,
					'synonym_id' => $synonym_id
				];
			} else {
				return ['translation_id' => $translation['id']];
			}
		}

		function createExamplesEntries($examples, $trans_id, $entries) {
			foreach ($examples as $key => $example) {
				$id = $this->exampleDao->create($example, $trans_id);
				
				array_push($entries, ['example', $id]);
			}
		}

		function getEntriesFromIds($ids) {
			if (!isset($ids['word_id']) || !isset($ids['synonym_id'])) {
				return [];
			}
			
			return [
				['translation', $ids['translation_id']],
				['synonym', $ids['synonym_id']],
				['word', $ids['word_id']]
			];
		}

		function createEntries($entries) {
			$group_id = $this->entryGroupDao->create();

			foreach ($entries as $key => $value) {
				$this->entryLineDao->create($value[0], $value[1], $group_id);
			}
		}

		function findTranslations($phrase, $lang1_id, $lang2_id) {
			$results 	= [];
			$words 		= $this->translationDao->findByWordLanguage($phrase, $lang1_id);

			foreach ($words as $key => $value) {
				$translations = $this->translationDao
				->findTranslations($phrase, $lang1_id, $lang2_id);

				$value['translations'] = $translations;
				array_push($results, $value);
			}

			return $results;
		}
	}