import { Component, OnInit } from '@angular/core';
import { Language } from './types';
import { LanguageService } from './language.service';
import { PARTS_OF_SPEECH } from './app.constants';

@Component( {
    selector: "app-root",
    templateUrl: "./app.component.html",
    styleUrls: [
    	"./app.css"
    ]
})
export class AppComponent implements OnInit{
	languages: Language[] = [];
	partsOfSpeech = PARTS_OF_SPEECH;
	page = 0;

	constructor(public languageService: LanguageService) {
	};

	ngOnInit() {
		this.languageService.getLanguages()
		.subscribe(res => {
			if (res.data) {
				this.languages = res.data;
			};
		});
	};
};