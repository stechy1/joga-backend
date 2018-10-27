import { Component, OnInit } from '@angular/core';
import { HttpClient } from '@angular/common/http';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css']
})
export class AppComponent implements OnInit {
  title = 'joga-frontend';

  constructor(private http: HttpClient) {}

  ngOnInit(): void {
    this.http.delete('/api/fun/article')
        .toPromise()
        .then(value => {
          console.log(value);
        });
  }
}
