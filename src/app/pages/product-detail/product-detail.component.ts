import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute, RouterLink } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { ApiService } from '../../core/services/api.service';
import { CartService } from '../../core/services/cart.service';

@Component({
  selector: 'app-product-detail',
  standalone: true,
  imports: [CommonModule, RouterLink, FormsModule],
  template: `
    <div class="container mt-4 mb-5" *ngIf="product">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="/">الرئيسية</a></li>
          <li class="breadcrumb-item"><a routerLink="/products">المنتجات</a></li>
          <li class="breadcrumb-item active">{{ product.name }}</li>
        </ol>
      </nav>

      <div class="row">
        <div class="col-md-5 mb-4">
          <div class="product-gallery">
            <img [src]="product.image || 'https://via.placeholder.com/500x500'" 
                 [alt]="product.name"
                 class="main-image"
                 (error)="onImageError($event)">
            
            <div class="gallery-thumbs" *ngIf="product.images">
              <img *ngFor="let img of product.images" 
                   [src]="img" 
                   (click)="changeImage(img)"
                   class="thumb-image">
            </div>
          </div>
        </div>

        <div class="col-md-7">
          <div class="product-details">
            <h1>{{ product.name }}</h1>
            
            <div class="product-category">
              <span class="badge bg-primary">{{ product.category_name }}</span>
              <span class="badge bg-secondary">{{ product.brand_name }}</span>
            </div>

            <div class="product-rating mt-3">
              <div class="stars">
                <i class="fas fa-star" *ngFor="let i of [1,2,3,4,5]"></i>
              </div>
              <span class="rating-text">{{ product.rating || 0 }} / 5 ({{ product.reviews_count || 0 }} تقييم)</span>
            </div>

            <div class="product-price mt-3">
              <span *ngIf="product.original_price" class="original-price">{{ product.original_price }} ر.س</span>
              <span class="current-price">{{ product.price }} ر.س</span>
              <span *ngIf="product.discount" class="discount-badge">خصم {{ product.discount }}%</span>
            </div>

            <div class="product-stock mt-3">
              <span *ngIf="product.stock > 5" class="stock-available">
                <i class="fas fa-check-circle"></i> متوفر بالمخزون
              </span>
              <span *ngIf="product.stock > 0 && product.stock <= 5" class="stock-low">
                <i class="fas fa-exclamation-triangle"></i> عدد محدود ({{ product.stock }} متبقي)
              </span>
              <span *ngIf="product.stock === 0" class="stock-out">
                <i class="fas fa-times-circle"></i> نفذ من المخزون
              </span>
            </div>

            <div class="product-actions mt-4">
              <div class="quantity-selector">
                <label>الكمية:</label>
                <input type="number" [(ngModel)]="quantity" min="1" [max]="product.stock" 
                       [disabled]="product.stock === 0" class="form-control">
              </div>
              
              <button (click)="addToCart()" 
                      [disabled]="product.stock === 0"
                      class="btn btn-primary btn-lg">
                <i class="fas fa-shopping-cart"></i> أضف إلى السلة
              </button>
              
              <button class="btn btn-outline-secondary btn-lg">
                <i class="fas fa-heart"></i> أضف للمفضلة
              </button>
            </div>

            <div class="product-description mt-4">
              <h5>الوصف</h5>
              <p>{{ product.description || 'لا توجد معلومات إضافية' }}</p>
            </div>

            <div class="product-specs mt-4">
              <h5>المواصفات</h5>
              <table class="specs-table">
                <tr>
                  <td>SKU:</td>
                  <td>{{ product.sku }}</td>
                </tr>
                <tr>
                  <td>الحالة:</td>
                  <td>جديد</td>
                </tr>
                <tr>
                  <td>الضمان:</td>
                  <td>سنة واحدة</td>
                </tr>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- Reviews Section -->
      <div class="reviews-section mt-5">
        <h3>التقييمات والآراء</h3>
        <div class="review-card" *ngFor="let review of reviews">
          <div class="review-header">
            <div>
              <strong>{{ review.user_name }}</strong>
              <div class="review-rating">
                <i class="fas fa-star" *ngFor="let i of [1,2,3,4,5]"></i>
              </div>
            </div>
            <small>{{ review.created_at | date: 'short' }}</small>
          </div>
          <p class="review-text">{{ review.comment }}</p>
        </div>
      </div>
    </div>

    <div *ngIf="!product" class="container mt-4">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">جاري التحميل...</span>
      </div>
    </div>
  `,
  styleUrl: './product-detail.component.scss'
})
export class ProductDetailComponent implements OnInit {
  product: any = null;
  reviews: any[] = [];
  quantity = 1;

  constructor(
    private route: ActivatedRoute,
    private apiService: ApiService,
    private cartService: CartService
  ) {}

  ngOnInit(): void {
    this.route.params.subscribe(params => {
      const id = params['id'];
      this.loadProduct(id);
    });
  }

  loadProduct(id: number): void {
    this.apiService.getArticle(id).subscribe({
      next: (response) => {
        this.product = response;
        this.loadReviews(id);
      },
      error: (error) => console.error('Error loading product:', error)
    });
  }

  loadReviews(articleId: number): void {
    this.apiService.getReviews(articleId).subscribe({
      next: (response) => {
        this.reviews = response;
      },
      error: (error) => console.error('Error loading reviews:', error)
    });
  }

  addToCart(): void {
    if (this.product && this.quantity > 0) {
      this.cartService.addItem({
        id: this.product.id,
        name: this.product.name,
        price: this.product.price,
        image: this.product.image,
        quantity: this.quantity
      });
      
      // Show success message
      alert('تم إضافة المنتج إلى السلة بنجاح!');
      this.quantity = 1;
    }
  }

  changeImage(image: string): void {
    if (this.product) {
      this.product.image = image;
    }
  }

  onImageError(event: any): void {
    event.target.src = 'https://via.placeholder.com/500x500?text=No+Image';
  }
}
