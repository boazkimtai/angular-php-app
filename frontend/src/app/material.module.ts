import { NgModule } from '@angular/core';
import { MatButtonModule } from '@angular/material/button';
import { MatFormFieldModule, MAT_FORM_FIELD_DEFAULT_OPTIONS } from '@angular/material/form-field';
import { MatSelectModule } from  '@angular/material/select';
import { MatInputModule } from '@angular/material/input';
import { MatAutocompleteModule } from '@angular/material/autocomplete';
import { MatRadioModule, MatRadioButton } from '@angular/material/radio';
import { MatIconModule } from '@angular/material/icon';
import { MatSnackBarModule, MAT_SNACK_BAR_DEFAULT_OPTIONS } from '@angular/material/snack-bar';
import { MatDialogModule } from '@angular/material/dialog';
import { MatProgressSpinnerModule } from '@angular/material/progress-spinner';

@NgModule({
	exports: [
		MatButtonModule,
		MatFormFieldModule,
		MatSelectModule,
		MatInputModule,
		MatAutocompleteModule,
		MatIconModule,
		MatSnackBarModule,
		MatDialogModule,
		MatRadioModule,
		MatProgressSpinnerModule
	],
	providers: [
		{provide: MAT_SNACK_BAR_DEFAULT_OPTIONS, useValue: {duration: 3000}},
		{provide: MAT_FORM_FIELD_DEFAULT_OPTIONS, useValue: {appearance: 'outline'}}
	]
})
export class MaterialModule {
};