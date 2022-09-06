import { Inject, Component, OnInit, ViewChild, Input, Output, EventEmitter } from '@angular/core';
import { 
	FormGroup, FormArray, FormControl, AbstractControl, Validators, FormGroupDirective 
} from '@angular/forms';
import { Language, Term } from './types';
import { TranslationService } from './translation.service';
import { TermService } from './term.service';
import { SelectTermComponent } from './select-term.component';
import { pipe, throwError, combineLatest } from 'rxjs';
import { catchError, debounceTime } from 'rxjs/operators';
import { MatSnackBar } from '@angular/material/snack-bar';
import { MatDialog, MatDialogRef, MAT_DIALOG_DATA } from '@angular/material/dialog';

@Component({
  selector: "translate-form",
  templateUrl: "./translate-form.html",
  styleUrls: ["./translate-form.css"]
})
export class TranslateFormComponent  implements OnInit {
	step 						= 0;
	isSaving 				= false;
	isInputFocused 	= false;
	form 						= new FormGroup({});

	@Input() languages: Language[] 		= [];
	@Input() partsOfSpeech: string[] 	= [];
	@Input() page: number;
	@Output() pageChange = new EventEmitter<number>();
	@ViewChild(FormGroupDirective) formDirective: FormGroupDirective;
	
  constructor(
  	private translationService: TranslationService,
  	private termService: TermService,
  	private snackBar: MatSnackBar,
  	private dialog: MatDialog) {
  };

  ngOnInit() {
  	this.form = new FormGroup({
			term: new FormControl('', []),
			part_of_speech: new FormControl('', [Validators.required]),
			lang1_id: new FormControl('', [Validators.required]),
			lang2_id: new FormControl('', [Validators.required]),
			lang1_phrase: new FormControl('', {
				validators: [Validators.required],
				updateOn: 'blur'
			}),
			lang2_phrase: new FormControl('', {
				validators: [Validators.required],
				updateOn: 'blur'
			}),
			lang1_def: new FormControl('', []),
			lang2_def: new FormControl('', []),
			singular: new FormControl('', []),
			plural: new FormControl('', []),
			verbs: new FormGroup({
				past: new FormControl('', [this.requiredIf(() => {
					return this.form.value.part_of_speech == 'VERB' && this.englishSelected();
				})]),
				past_participle: new FormControl('', [this.requiredIf(() => {
					return this.form.value.part_of_speech == 'VERB' && this.englishSelected();
				})])
			}),
			lang1_examples: new FormArray([]),
			lang2_examples: new FormArray([]),
			synonyms: new FormArray([])
		});

		this.synonyms.valueChanges.subscribe(value => {
			if (value.length == 0) {
				this.addSynonym();
			};
		});

  	this.addSynonym();
  	this.addExample(this.lang1_examples);
  	this.addExample(this.lang2_examples);

  	combineLatest([
  		this.form.get('lang1_id')?.valueChanges,
  		this.form.get('lang2_id')?.valueChanges,
  		this.form.get('lang1_phrase')?.valueChanges,
  		this.form.get('lang2_phrase')?.valueChanges,
  		this.form.get('part_of_speech')?.valueChanges,
  	])
  	.pipe(debounceTime(500))
  	.subscribe(async(values: any) => {
  		if (values.every((value: any) => !!value.trim()) &&
  			!this.englishSelected() && !this.isInputFocused) {
	  		this.showSelectTermDialog();
  		};
  	});
  };

  requiredIf(fn: Function) {
		return (formControl: AbstractControl) => {
			if (fn()) {
				return Validators.required(formControl);
			};
			return null;
		};
	};

	synonymRequiredIf() {
		return (formControl: AbstractControl) => {
			const value = formControl.value;

			if (!!value.language_id && !value.phrase) {
				return {phraseRequired: true};
			}  else if (!value.language_id && !!value.phrase) {
				return {languageRequired: true};
			};
			return null;
		};
	};

	get synonyms() {
		return this.form.get('synonyms') as FormArray;
	};

	get lang1_examples() {
		return this.form.get('lang1_examples') as FormArray;
	};

	get lang2_examples() {
		return this.form.get('lang2_examples') as FormArray;
	};

	addSynonym() {
		let synonym = new FormGroup({
			language_id: new FormControl('', []),
			phrase: new FormControl('', []),
			def: new FormControl('', []),
			examples: new FormArray([])
		}, {validators: this.synonymRequiredIf()});
		let index = this.synonyms.controls.length;

		this.synonyms.push(synonym);
		this.addExample(this.getSynExamplesControl(index));
	};

	removeSynonym(index: number) {
		this.synonyms.removeAt(index);
	};

	getSynExamplesControl(index: number) {
		return this.synonyms.controls[index].get('examples') as FormArray;
	};

	addExample(examplesControl: FormArray) {
		let example 	= new FormControl('', []);
		examplesControl.push(example);
	};

	removeExample(examplesControl: FormArray, index: number) {
		examplesControl.removeAt(index);
	};

	findLanguageById(id: string) {
		return this.languages.find(language => {
			return language.id === id;
		});
	};

	async findTerms() {
		const value 			= this.form.value;
		var terms: Term[] = [];

		var results = await this.termService.findTerms({
			query: value.lang1_phrase,
			lang_id: value.lang1_id,
			type: value.part_of_speech
		})
		.pipe(catchError((responseError) => {
  		this.isSaving = false;
  		this.showSnackBar('Error! Please try again.', 'Close');

  		return throwError(responseError);
  	}))
		.toPromise();

		if (results?.data) {
			terms = terms.concat(results.data);
		};

		results = await this.termService.findTerms({
			query: value.lang2_phrase,
			lang_id: value.lang2_id,
			type: value.part_of_speech
		})
		.pipe(catchError((responseError) => {
  		this.isSaving = false;
  		this.showSnackBar('Error! Please try again.', 'Close');

  		return throwError(responseError);
  	}))
		.toPromise();

		if (results?.data) {
			terms = terms.concat(results.data);
		};

		terms = terms.filter((term, index, terms) => {
			return terms.findIndex(i => i.id == term.id) == index;
		});
		
		return terms;
	};

	englishSelected() {
		return (
			this.findLanguageById(this.form.value.lang1_id)?.name?.toUpperCase() == 'ENGLISH' || 
			this.findLanguageById(this.form.value.lang2_id)?.name?.toUpperCase() == 'ENGLISH'
		);
	}

	showSnackBar(message: string, action: string) {
		this.snackBar.open(message, action);
	};

	async showSelectTermDialog() {
		const dialog = this.dialog.open(SelectTermComponent, {
			width: '480px'
		});

		dialog.componentInstance.isSearching = true;

		dialog.afterClosed().subscribe(value => {
			if (value) {
				this.form.get('term')?.setValue(value);
			};
		});

		const terms 	= await this.findTerms();

		dialog.componentInstance.isSearching 	= false;
		dialog.componentInstance.data 				= terms;
	};

	validateStep(step: number) {
		const value = this.form.value;

		this.form.markAsTouched();
		this.form.markAsDirty();

		if (step == 0) {
			if (!this.englishSelected() && 
				!value.term && value.lang1_id && value.lang2_id &&
				value.lang1_phrase && value.lang2_phrase && value.part_of_speech) {
				this.showSelectTermDialog();
				return;
			};

			if (value.part_of_speech == 'VERB' && 
				(!value.verbs.past || !value.verbs.past_participle)) {
				return;
			};

			if (value.lang1_id && value.lang2_id && 
				value.lang1_phrase && value.lang2_phrase && value.part_of_speech) {
				this.step = 1;
			};
		};

		if (step == 1 && this.synonyms?.valid) {
			this.save();
		};
	};

	sanitizeFormValues(values: object) {
		var copy = Object.assign(values);

		if (copy.part_of_speech == 'NOUN') {
			copy.verbs = null;
		}

		if (copy.part_of_speech == 'VERB') {
			copy.singular = null;
			copy.plural = null;
		}

		copy.lang1_examples = copy.lang1_examples.filter((example: string) => !!example);

		if (copy.lang1_examples.length == 0) {
			copy.lang1_examples = null;
		};

		copy.lang2_examples = copy.lang2_examples.filter((example: string) => !!example);

		if (copy.lang2_examples.length == 0) {
			copy.lang2_examples = null;
		};

		copy.synonyms = copy.synonyms.filter((synonym: any) => {
			return !!synonym.language_id && !!synonym.phrase;
		});

		copy.synonyms.forEach((synonym: {examples: any}) => {
			synonym.examples = synonym.examples.filter((example: string) => !!example);

			if (synonym.examples.length == 0) {
				synonym.examples = null;
			};
		});

		if (copy.synonyms.length == 0) {
			copy.synonyms = null;
		};

		return copy;
	}

  save() {
  	const data = this.sanitizeFormValues(this.form.getRawValue());
  	this.isSaving = true;

  	this.translationService.create(data)
  	.pipe(catchError((responseError) => {
  		this.isSaving = false;
  		this.showSnackBar('Error! Please try again.', 'Close');

  		return throwError(responseError);
  	}))
  	.subscribe(results => {
  		this.isSaving = false;
  		this.step 		= 0;

  		this.formDirective.reset();
  		this.formDirective.resetForm();
  		this.form.reset();
  		this.showSnackBar('Changes saved.', 'Close');
  	});
  };
};