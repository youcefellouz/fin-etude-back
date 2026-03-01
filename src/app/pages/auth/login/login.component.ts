import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { RouterLink, Router } from '@angular/router';
import { AuthService } from '../../../core/services/auth.service';

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterLink],
  template: `
    <div class="login-container">
      <div class="login-card">
        <div class="login-header">
          <i class="fas fa-microchip"></i>
          <h2>Tech Hub</h2>
          <p>تسجيل الدخول</p>
        </div>

        <form (ngSubmit)="login()" *ngIf="!resetMode">
          <div class="form-group">
            <label for="email">البريد الإلكتروني</label>
            <input 
              type="email" 
              id="email"
              [(ngModel)]="email" 
              name="email"
              class="form-control" 
              placeholder="أدخل بريدك الإلكتروني"
              required>
          </div>

          <div class="form-group">
            <label for="password">كلمة المرور</label>
            <input 
              type="password" 
              id="password"
              [(ngModel)]="password" 
              name="password"
              class="form-control" 
              placeholder="أدخل كلمة المرور"
              required>
          </div>

          <button type="submit" class="btn btn-primary btn-lg w-100" [disabled]="loading">
            <span *ngIf="!loading"><i class="fas fa-sign-in-alt"></i> دخول</span>
            <span *ngIf="loading">
              <span class="spinner-border spinner-border-sm me-2"></span>جاري التحميل...
            </span>
          </button>

          <button type="button" (click)="toggleReset()" class="btn btn-link btn-block">
            هل نسيت كلمة المرور؟
          </button>
        </form>

        <!-- Forgot Password Form -->
        <form (ngSubmit)="forgotPassword()" *ngIf="resetMode">
          <div class="form-group">
            <label for="reset-email">البريد الإلكتروني</label>
            <input 
              type="email" 
              id="reset-email"
              [(ngModel)]="resetEmail" 
              name="resetEmail"
              class="form-control" 
              placeholder="أدخل بريدك الإلكتروني"
              required>
          </div>

          <button type="submit" class="btn btn-primary btn-lg w-100" [disabled]="loading">
            <span *ngIf="!loading"><i class="fas fa-redo"></i> إرسال كود التحقق</span>
            <span *ngIf="loading">جاري التحميل...</span>
          </button>

          <button type="button" (click)="toggleReset()" class="btn btn-link btn-block">
            العودة إلى التسجيل
          </button>
        </form>

        <div *ngIf="error" class="alert alert-danger mt-3">
          {{ error }}
        </div>

        <div *ngIf="success" class="alert alert-success mt-3">
          {{ success }}
        </div>

        <div class="login-footer">
          <p>ليس لديك حساب؟ <a routerLink="/register">إنشاء حساب جديد</a></p>
        </div>
      </div>
    </div>
  `,
  styleUrl: './login.component.scss'
})
export class LoginComponent {
  email = '';
  password = '';
  resetEmail = '';
  resetMode = false;
  loading = false;
  error = '';
  success = '';

  constructor(
    private authService: AuthService,
    private router: Router
  ) {}

  login(): void {
    if (!this.email || !this.password) {
      this.error = 'الرجاء ملء جميع الحقول';
      return;
    }

    this.loading = true;
    this.error = '';

    this.authService.login(this.email, this.password).subscribe({
      next: () => {
        this.loading = false;
        this.router.navigate(['/profile']);
      },
      error: (err) => {
        this.loading = false;
        this.error = err.error?.message || 'حدث خطأ في التسجيل';
      }
    });
  }

  forgotPassword(): void {
    if (!this.resetEmail) {
      this.error = 'الرجاء إدخال البريد الإلكتروني';
      return;
    }

    this.loading = true;
    this.error = '';

    this.authService.forgotPassword(this.resetEmail).subscribe({
      next: () => {
        this.loading = false;
        this.success = 'تم إرسال كود التحقق إلى بريدك الإلكتروني';
      },
      error: (err) => {
        this.loading = false;
        this.error = err.error?.message || 'حدث خطأ';
      }
    });
  }

  toggleReset(): void {
    this.resetMode = !this.resetMode;
    this.error = '';
    this.success = '';
  }
}
