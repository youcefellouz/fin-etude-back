import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterLink } from '@angular/router';
import { ApiService } from '../../core/services/api.service';

@Component({
  selector: 'app-home',
  standalone: true,
  imports: [CommonModule, RouterLink],
  template: `
    <div class="hero-section">
      <div class="hero-content">
        <h1>متجر معدات الحاسوب الأفضل</h1>
        <p>أجود المعدات بأفضل الأسعار من أشهر الماركات العالمية</p>
        <a routerLink="/products" class="btn btn-primary btn-lg">
          <i class="fas fa-shopping-bag"></i> ابدأ التسوق
        </a>
      </div>
    </div>

    <section class="featured-section">
      <div class="container">
        <div class="section-header">
          <h2>المنتجات المميزة</h2>
          <p>أفضل المنتجات المختارة بعناية</p>
        </div>

        <div class="products-grid">
          <div class="product-card" *ngFor="let product of featuredProducts">
            <div class="product-image">
              <img [src]="product.image || 'https://via.placeholder.com/250x250'" 
                   [alt]="product.name"
                   (error)="onImageError($event)">
              <div class="product-badge" *ngIf="product.discount">
                خصم {{ product.discount }}%
              </div>
            </div>
            <div class="product-info">
              <h5 class="product-name">{{ product.name }}</h5>
              <p class="product-category" *ngIf="product.category">{{ product.category }}</p>
              
              <div class="product-price">
                <span class="original-price" *ngIf="product.original_price">
                  {{ product.original_price }} ر.س
                </span>
                <span class="current-price">{{ product.price }} ر.س</span>
              </div>

              <div class="product-rating" *ngIf="product.rating">
                <i class="fas fa-star" *ngFor="let i of [1,2,3,4,5]"></i>
                ({{ product.rating }})
              </div>

              <div class="product-actions">
                <a [routerLink]="['/products', product.id]" class="btn btn-outline-primary">
                  <i class="fas fa-eye"></i> عرض التفاصيل
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="categories-section">
      <div class="container">
        <div class="section-header">
          <h2>الفئات</h2>
        </div>
        
        <div class="categories-grid">
          <div class="category-card" *ngFor="let category of categories">
            <div class="category-icon">
              <i [class]="getCategoryIcon(category.name)"></i>
            </div>
            <h5>{{ category.name }}</h5>
            <p>{{ category.description || 'استكشف المنتجات' }}</p>
          </div>
        </div>
      </div>
    </section>

    <section class="features-section">
      <div class="container">
        <div class="row">
          <div class="col-md-3 feature">
            <div class="feature-icon">
              <i class="fas fa-shipping-fast"></i>
            </div>
            <h5>شحن سريع</h5>
            <p>توصيل لجميع أنحاء البلاد في 48 ساعة</p>
          </div>
          <div class="col-md-3 feature">
            <div class="feature-icon">
              <i class="fas fa-shield-alt"></i>
            </div>
            <h5>منتجات أصلية</h5>
            <p>100% ضمان على جميع المنتجات</p>
          </div>
          <div class="col-md-3 feature">
            <div class="feature-icon">
              <i class="fas fa-undo"></i>
            </div>
            <h5>استرجاع آمن</h5>
            <p>سياسة إرجاع سهلة وآمنة</p>
          </div>
          <div class="col-md-3 feature">
            <div class="feature-icon">
              <i class="fas fa-headset"></i>
            </div>
            <h5>دعم العملاء</h5>
            <p>خدمة عملاء 24/7 للمساعدة</p>
          </div>
        </div>
      </div>
    </section>
  `,
  styleUrl: './home.component.scss'
})
export class HomeComponent implements OnInit {
  featuredProducts: any[] = [];
  categories: any[] = [];

  constructor(private apiService: ApiService) {}

  ngOnInit(): void {
    this.loadFeaturedProducts();
    this.loadCategories();
  }

  loadFeaturedProducts(): void {
    this.apiService.getArticles().subscribe({
      next: (response) => {
        this.featuredProducts = response.slice(0, 8);
      },
      error: (error) => console.error('Error loading products:', error)
    });
  }

  loadCategories(): void {
    this.apiService.getCategories().subscribe({
      next: (response) => {
        this.categories = response.slice(0, 6);
      },
      error: (error) => console.error('Error loading categories:', error)
    });
  }

  getCategoryIcon(categoryName: string): string {
    const iconMap: { [key: string]: string } = {
      'معالجات': 'fas fa-microchip',
      'ذاكرة': 'fas fa-memory',
      'أقراص': 'fas fa-hdd',
      'بطاقات رسومات': 'fas fa-video',
      'أمدادات طاقة': 'fas fa-power-off',
      'مبردات': 'fas fa-fan',
      'أخرى': 'fas fa-laptop'
    };
    return iconMap[categoryName] || 'fas fa-laptop';
  }

  onImageError(event: any): void {
    event.target.src = 'https://via.placeholder.com/250x250?text=No+Image';
  }
}
