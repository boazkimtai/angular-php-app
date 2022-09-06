import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, of } from 'rxjs';
import { ServerResponse, Language } from './types';

@Injectable({
  providedIn: 'root'
})
export class LanguageService {
  url = "http://localhost/languages";

  constructor(private http: HttpClient) {
  };

  getLanguages(): Observable<ServerResponse<Language[]>> {
  	return this.http.get<ServerResponse<Language[]>>(this.url);
  };
};