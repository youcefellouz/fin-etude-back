import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { AuthService } from '../../core/services/auth.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-profile',
  standalone: true,
  imports: [CommonModule, FormsModule],
  template: `
    <div class="container mt-4 mb-5" *ngIf="user">
      <div class="row">
        <div class="col-lg-3">
          <div class="profile-sidebar">
            <div class="profile-avatar">
              <i class="fas fa-user-circle"></i>
            </div>
            <h4>{{ user.name }}</h4>
            <p class="text-muted">{{ user.email }}</p>
            
            <div class="profile-menu">
              <a href="#profile" class="menu-item active">
                <i class="fas fa-user"></i> البيانات الشخصية
              </a>
              <a href="#orders" class="menu-item">
                <i class="fas fa-shopping-bag"></i> طلباتي
              </a>
              <a href="#security" class="menu-item">
                <i class="fas fa-lock"></i> الأمان
              </a>
              <a href="#logout" (click)="logout()" class="menu-item danger">
                <i class="fas fa-sign-out-alt"></i> تسجيل الخروج
              </a>
            </div>
          </div>
        </div>

        <div class="col-lg-9">
          <div class="profile-content">
            <h3><i class="fas fa-user"></i> البيانات الشخصية</h3>
            
            <form (ngSubmit)="updateProfile()" class="profile-form">
              <div class="form-group">
                <label>الاسم الكامل</label>
                <input type="text" [(ngModel)]="user.name" name="name" class="form-control">
              </div>

              <div class="form-group">
                <label>البريد الإلكتروني</label>
                <input type="email" [(ngModel)]="user.email" name="email" class="form-control" disabled>
              </div>

              <div class="form-group">
                <label>رقم الهاتف</label>
                <input type="tel" [(ngModel)]="user.phone" name="phone" class="form-control">
              </div>

              <div class="form-group">
                <label>العنوان</label>
                <textarea [(ngModel)]="user.address" name="address" class="form-control" rows="3"></textarea>
              </div>

              <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> حفظ التغييرات
              </button>
            </form>

            <div *ngIf="success" class="alert alert-success mt-3">
              {{ success }}
            </div>
            <div *ngIf="error" class="alert alert-danger mt-3">
              {{ error }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <div *ngIf="!user" class="container mt-4">
      <p class="text-center">جاري التحميل...</p>
    </div>
  `,
  styleUrl: './profile.component.scss'
})
export class ProfileComponent implements OnInit {
  user: any = null;
  success = '';
  error = '';

  constructor(
    private authService: AuthService,
    private router: Router
  ) {}

  ngOnInit(): void {
    this.user = this.authService.getCurrentUser();
    if (!this.user) {
      this.router.navigate(['/login']);
    }
  }

  updateProfile(): void {
    if (!this.user) return;

    // Mock update
    this.success = 'تم تحديث البيانات بنجاح!';
    setTimeout(() => {
      this.success = '';
    }, 3000);
  }

  logout(): void {
    this.authService.logout().subscribe({
      next: () => {
        this.router.navigate(['/login']);
      }
    });
  }
}
