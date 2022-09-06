
import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';

@Injectable({
  providedIn: 'root'
})
export class TranslationService {
  url = 'http://localhost/translations';

  constructor(private http: HttpClient) {
  };

  create(data: any) {
    return this.http
    .post(this.url, data);
  };

  find(q: string, l1: string, l2: string) {
    return this.http.get(this.url, {
      params: {q, l1, l2}
    });
  };
};