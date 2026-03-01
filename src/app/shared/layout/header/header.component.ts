import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterLink, RouterLinkActive } from '@angular/router';
import { AuthService } from '../../../core/services/auth.service';
import { CartService } from '../../../core/services/cart.service';
import { Observable } from 'rxjs';

@Component({
  selector: 'app-header',
  standalone: true,
  imports: [CommonModule, RouterLink, RouterLinkActive],
  template: `
    <nav class="navbar navbar-expand-lg navbar-light sticky-top">
      <div class="container-fluid">
        <a class="navbar-brand" routerLink="/">
          <i class="fas fa-microchip"></i> Tech Hub
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav ms-auto">
            <li class="nav-item">
              <a class="nav-link" routerLink="/" routerLinkActive="active" [routerLinkActiveOptions]="{exact: true}">
                <i class="fas fa-home"></i> الرئيسية
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" routerLink="/products" routerLinkActive="active">
                <i class="fas fa-laptop"></i> المنتجات
              </a>
            </li>

            <li class="nav-item cart-item">
              <a class="nav-link position-relative" routerLink="/cart">
                <i class="fas fa-shopping-cart"></i> السلة
                <span class="badge position-absolute top-0 start-100 translate-middle badge-danger" 
                      *ngIf="cartCount$ | async as count">
                  {{ count }}
                </span>
              </a>
            </li>

            <li class="nav-item dropdown" *ngIf="(isAuthenticated$ | async)">
              <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                <i class="fas fa-user"></i> حسابي
              </a>
              <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                <li><a class="dropdown-item" routerLink="/profile">الملف الشخصي</a></li>
                <li><a class="dropdown-item" routerLink="/orders">طلباتي</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" (click)="logout()">تسجيل الخروج</a></li>
              </ul>
            </li>

            <li class="nav-item" *ngIf="!(isAuthenticated$ | async)">
              <a class="nav-link" routerLink="/login">
                <i class="fas fa-sign-in-alt"></i> دخول
              </a>
            </li>
            <li class="nav-item" *ngIf="!(isAuthenticated$ | async)">
              <a class="nav-link" routerLink="/register">
                <i class="fas fa-user-plus"></i> تسجيل
              </a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
  `,
  styleUrl: './header.component.scss'
})
export class HeaderComponent implements OnInit {
  isAuthenticated$!: Observable<boolean>;
  cartCount$!: Observable<number>;

  constructor(
    private authService: AuthService,
    private cartService: CartService
  ) {}

  ngOnInit(): void {
    this.isAuthenticated$ = new Observable(observer => {
      observer.next(this.authService.isAuthenticated());
      this.authService.currentUser$.subscribe(() => {
        observer.next(this.authService.isAuthenticated());
      });
    });

    this.cartCount$ = new Observable(observer => {
      this.cartService.cart$.subscribe(() => {
        observer.next(this.cartService.getCartItemsCount());
      });
    });
  }

  logout(): void {
    this.authService.logout().subscribe({
      next: () => {
        window.location.href = '/login';
      },
      error: (err) => console.error('Logout error:', err)
    });
  }
}
