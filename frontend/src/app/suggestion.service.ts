
import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';

@Injectable({
  providedIn: 'root'
})
export class SuggestionService {
  url = 'http://localhost/suggestions';

  constructor(private http: HttpClient) {
  };

  find(q: string, lang_id: string) {
    return this.http.get(this.url, {
      params: {q, lang_id}
    });
  };
};