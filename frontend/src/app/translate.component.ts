import { Component, OnInit, Input, Output, EventEmitter } from '@angular/core';
import { Subject, pipe, debounceTime } from 'rxjs';
import { Language } from './types';
import { TranslationService } from './translation.service';
import { SuggestionService } from './suggestion.service';

@Component({
	selector: 'translate',
	templateUrl: './translate.component.html',
	styleUrls: [
		'./translate.css'
	]
})
export class TranslateComponent implements OnInit {
	text 				= '';
	phrase 			= '';
	l1_id 			= '';
	l2_id 			= '';
	isSearching = false;
	suggestionResults = [];
	results: {
		type?: string;
		meaning?: string;
		examples?: [];
		translations: {name: string; meaning?: string; examples?: []}[];
	}[] = [];

	modelChanged = new Subject<boolean>();

	@Input() languages: Language[] = [];
	@Input() page: number;
	@Output() pageChange = new EventEmitter<number>();

	constructor(private translationService: TranslationService,
		private suggestionService: SuggestionService) {
	};

	ngOnInit() {
		this.modelChanged.pipe(debounceTime(200))
		.subscribe(() => {
			this.phrase = this.text.trim();

			if (this.phrase && this.l1_id && this.l2_id) {
				this.suggestionService.find(this.text, this.l1_id)
				.subscribe((results: any) => {
					this.suggestionResults 	= results.data;
				});
			};
		});
	};

	findTranslations() {
		this.isSearching = true;

		this.translationService.find(this.phrase, this.l1_id, this.l2_id)
		.subscribe((results: any) => {
			this.isSearching 	= false;
			this.results 			= results.data;
		});
	};

	swapLanguages() {
		var l1_id = this.l1_id;

		this.l1_id = this.l2_id;
		this.l2_id = l1_id;

		this.search();
	};
	
	search() {
		this.results 						= [];
		this.suggestionResults 	= [];

		this.modelChanged.next(true);
	};
}