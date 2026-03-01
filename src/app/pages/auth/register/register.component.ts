import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { RouterLink, Router } from '@angular/router';
import { AuthService } from '../../../core/services/auth.service';

@Component({
  selector: 'app-register',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterLink],
  template: `
    <div class="register-container">
      <div class="register-card">
        <div class="register-header">
          <i class="fas fa-user-plus"></i>
          <h2>إنشاء حساب جديد</h2>
          <p>انضم إلى Tech Hub</p>
        </div>

        <form (ngSubmit)="register()" *ngIf="!verificationMode">
          <div class="form-group">
            <label for="name">الاسم الكامل</label>
            <input 
              type="text" 
              id="name"
              [(ngModel)]="name" 
              name="name"
              class="form-control" 
              placeholder="أدخل اسمك الكامل"
              required>
          </div>

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
              placeholder="كلمة مرور قوية (8 أحرف على الأقل)"
              required>
            <small class="form-text text-muted">
              يجب أن تحتوي على حرف كبير ورمز (!@#إلخ)
            </small>
          </div>

          <div class="form-group">
            <label for="password-confirm">تأكيد كلمة المرور</label>
            <input 
              type="password" 
              id="password-confirm"
              [(ngModel)]="passwordConfirm" 
              name="passwordConfirm"
              class="form-control" 
              placeholder="أعد إدخال كلمة المرور"
              required>
          </div>

          <div class="form-check mb-3">
            <input 
              type="checkbox" 
              id="terms"
              [(ngModel)]="agreeTerms" 
              name="agreeTerms"
              class="form-check-input" 
              required>
            <label class="form-check-label" for="terms">
              أوافق على <a href="#terms">شروط الاستخدام</a>
            </label>
          </div>

          <button type="submit" class="btn btn-primary btn-lg w-100" [disabled]="loading">
            <span *ngIf="!loading"><i class="fas fa-user-plus"></i> إنشاء حساب</span>
            <span *ngIf="loading">جاري الإنشاء...</span>
          </button>
        </form>

        <!-- Verification Form -->
        <form (ngSubmit)="verifyCode()" *ngIf="verificationMode">
          <p class="text-muted text-center mb-3">
            تم إرسال كود التحقق إلى {{ email }}
          </p>

          <div class="form-group">
            <label for="code">كود التحقق</label>
            <input 
              type="text" 
              id="code"
              [(ngModel)]="verificationCode" 
              name="verificationCode"
              class="form-control" 
              placeholder="أدخل الكود من الرسالة (6 أرقام)"
              maxlength="6"
              required>
          </div>

          <small class="form-text text-muted d-block mb-3">
            ينتهي الكود خلال 5 دقائق
          </small>

          <button type="submit" class="btn btn-primary btn-lg w-100" [disabled]="loading">
            <span *ngIf="!loading">تحقق</span>
            <span *ngIf="loading">جاري التحقق...</span>
          </button>

          <button type="button" (click)="goBack()" class="btn btn-outline-secondary btn-lg w-100 mt-2">
            عودة
          </button>

          <button type="button" (click)="resendCode()" class="btn btn-link btn-block">
            إعادة إرسال الكود
          </button>
        </form>

        <div *ngIf="error" class="alert alert-danger mt-3">
          {{ error }}
        </div>

        <div *ngIf="success" class="alert alert-success mt-3">
          {{ success }}
        </div>

        <div class="register-footer" *ngIf="!verificationMode">
          <p>هل لديك حساب بالفعل؟ <a routerLink="/login">تسجيل الدخول</a></p>
        </div>
      </div>
    </div>
  `,
  styleUrl: './register.component.scss'
})
export class RegisterComponent {
  name = '';
  email = '';
  password = '';
  passwordConfirm = '';
  agreeTerms = false;
  verificationCode = '';
  
  verificationMode = false;
  loading = false;
  error = '';
  success = '';

  constructor(
    private authService: AuthService,
    private router: Router
  ) {}

  register(): void {
    if (!this.name || !this.email || !this.password || !this.passwordConfirm) {
      this.error = 'الرجاء ملء جميع الحقول';
      return;
    }

    if (this.password !== this.passwordConfirm) {
      this.error = 'كلمات المرور غير متطابقة';
      return;
    }

    if (!this.agreeTerms) {
      this.error = 'يجب الموافقة على شروط الاستخدام';
      return;
    }

    this.loading = true;
    this.error = '';

    this.authService.register({
      name: this.name,
      email: this.email,
      password: this.password,
      password_confirmation: this.passwordConfirm
    }).subscribe({
      next: () => {
        this.loading = false;
        this.verificationMode = true;
        this.success = 'تم إرسال كود التحقق إلى بريدك الإلكتروني';
      },
      error: (err) => {
        this.loading = false;
        this.error = err.error?.message || 'حدث خطأ في التسجيل';
      }
    });
  }

  verifyCode(): void {
    if (!this.verificationCode || this.verificationCode.length !== 6) {
      this.error = 'الرجاء إدخال كود صحيح';
      return;
    }

    this.loading = true;
    this.error = '';

    this.authService.verifyCode(this.email, this.verificationCode).subscribe({
      next: () => {
        this.loading = false;
        this.success = 'تم التحقق بنجاح! جاري نقلك...';
        setTimeout(() => {
          this.router.navigate(['/profile']);
        }, 1500);
      },
      error: (err) => {
        this.loading = false;
        this.error = err.error?.message || 'كود التحقق غير صحيح';
      }
    });
  }

  resendCode(): void {
    // Call resend code API if available
    alert('تم إعادة إرسال الكود');
  }

  goBack(): void {
    this.verificationMode = false;
    this.verificationCode = '';
    this.error = '';
  }
}
