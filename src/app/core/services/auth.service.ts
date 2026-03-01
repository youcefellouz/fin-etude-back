import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { BehaviorSubject, Observable } from 'rxjs';
import { tap } from 'rxjs/operators';
import { ApiService } from './api.service';

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  private currentUserSubject = new BehaviorSubject<any>(null);
  public currentUser$ = this.currentUserSubject.asObservable();

  private tokenSubject = new BehaviorSubject<string | null>(this.getToken());
  public token$ = this.tokenSubject.asObservable();

  constructor(private apiService: ApiService) {
    this.loadUser();
  }

  login(email: string, password: string): Observable<any> {
    return this.apiService.login(email, password).pipe(
      tap(response => {
        if (response.token) {
          this.setToken(response.token);
          this.currentUserSubject.next(response.user);
          localStorage.setItem('user', JSON.stringify(response.user));
        }
      })
    );
  }

  register(data: any): Observable<any> {
    return this.apiService.register(data);
  }

  verifyCode(email: string, code: string): Observable<any> {
    return this.apiService.verifyCode(email, code).pipe(
      tap(response => {
        if (response.token) {
          this.setToken(response.token);
          this.currentUserSubject.next(response.user);
          localStorage.setItem('user', JSON.stringify(response.user));
        }
      })
    );
  }

  forgotPassword(email: string): Observable<any> {
    return this.apiService.forgotPassword(email);
  }

  logout(): Observable<any> {
    return this.apiService.logout().pipe(
      tap(() => {
        this.clearAuth();
      })
    );
  }

  setToken(token: string): void {
    localStorage.setItem('auth_token', token);
    this.tokenSubject.next(token);
  }

  getToken(): string | null {
    return localStorage.getItem('auth_token');
  }

  isAuthenticated(): boolean {
    return !!this.getToken();
  }

  getCurrentUser(): any {
    return this.currentUserSubject.value;
  }

  private loadUser(): void {
    const user = localStorage.getItem('user');
    if (user) {
      this.currentUserSubject.next(JSON.parse(user));
    }
  }

  private clearAuth(): void {
    localStorage.removeItem('auth_token');
    localStorage.removeItem('user');
    this.tokenSubject.next(null);
    this.currentUserSubject.next(null);
  }
}
