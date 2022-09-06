import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, of } from 'rxjs';
import { ServerResponse, Term } from './types';

@Injectable({
  providedIn: 'root'
})
export class TermService {
  url = "http://localhost/k/terms";

  constructor(private http: HttpClient) {
  };

  findTerms(
  	{query, lang_id, type}: {query?: any, lang_id?: any, type?: any}
  	): Observable<ServerResponse<Term[]>> {
  	return this.http.get<ServerResponse<Term[]>>(this.url, {
  		params : {query, lang_id, type}
  	});
  };
};