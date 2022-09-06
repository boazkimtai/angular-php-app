import { Component, Inject } from '@angular/core';
import { MatDialogRef, MAT_DIALOG_DATA } from '@angular/material/dialog';

@Component({
	selector: 'select-term-dialog',
	templateUrl: 'select-term-dialog.html',
	styleUrls: ['./select-term.css']
})
export class SelectTermComponent {
	step 					= 0;
	isSearching		= false;
	term 					= '';
	createdTerm 	= '';
	
	constructor(
		public dialogRef: MatDialogRef<SelectTermComponent>,
		@Inject(MAT_DIALOG_DATA) public data: any) {
	};

	cancel() {
		this.dialogRef.close();
	};

	save(value: any) {
		this.dialogRef.close(value.trim());
	};
}