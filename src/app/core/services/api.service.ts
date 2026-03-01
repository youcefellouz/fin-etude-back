import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class ApiService {
  private apiUrl = 'http://localhost:8000/api'; // تعديل حسب عنوان backend لديك

  constructor(private http: HttpClient) {}

  // Articles/Products
  getArticles(): Observable<any> {
    return this.http.get(`${this.apiUrl}/articles`);
  }

  getArticle(id: number): Observable<any> {
    return this.http.get(`${this.apiUrl}/articles/${id}`);
  }

  // Categories
  getCategories(): Observable<any> {
    return this.http.get(`${this.apiUrl}/categories`);
  }

  // Brands
  getBrands(): Observable<any> {
    return this.http.get(`${this.apiUrl}/brands`);
  }

  // Orders
  getOrders(): Observable<any> {
    return this.http.get(`${this.apiUrl}/orders`);
  }

  createOrder(data: any): Observable<any> {
    return this.http.post(`${this.apiUrl}/orders`, data);
  }

  payOrder(orderId: number, data: any): Observable<any> {
    return this.http.post(`${this.apiUrl}/orders/${orderId}/pay`, data);
  }

  // Reviews
  getReviews(articleId: number): Observable<any> {
    return this.http.get(`${this.apiUrl}/reviews/article/${articleId}`);
  }

  createReview(data: any): Observable<any> {
    return this.http.post(`${this.apiUrl}/reviews`, data);
  }

  // Auth
  login(email: string, password: string): Observable<any> {
    return this.http.post(`${this.apiUrl}/login`, { email, password });
  }

  register(data: any): Observable<any> {
    return this.http.post(`${this.apiUrl}/register`, data);
  }

  verifyCode(email: string, code: string): Observable<any> {
    return this.http.post(`${this.apiUrl}/verify-code`, { email, code });
  }

  forgotPassword(email: string): Observable<any> {
    return this.http.post(`${this.apiUrl}/forgot-password`, { email });
  }

  resetPassword(data: any): Observable<any> {
    return this.http.post(`${this.apiUrl}/reset-password`, data);
  }

  getProfile(): Observable<any> {
    return this.http.get(`${this.apiUrl}/profile`);
  }

  logout(): Observable<any> {
    return this.http.post(`${this.apiUrl}/logout`, {});
  }
}
