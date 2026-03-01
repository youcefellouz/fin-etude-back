import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterLink } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { ApiService } from '../../core/services/api.service';

@Component({
  selector: 'app-products',
  standalone: true,
  imports: [CommonModule, RouterLink, FormsModule],
  template: `
    <div class="container mt-4">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="/">الرئيسية</a></li>
          <li class="breadcrumb-item active">المنتجات</li>
        </ol>
      </nav>

      <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3 mb-4">
          <div class="filter-card">
            <h5><i class="fas fa-filter"></i> تصفية المنتجات</h5>

            <!-- Search -->
            <div class="filter-section">
              <label>البحث</label>
              <input type="text" [(ngModel)]="searchTerm" (change)="filterProducts()" 
                     class="form-control" placeholder="ابحث عن منتج...">
            </div>

            <!-- Categories -->
            <div class="filter-section">
              <label><strong>الفئات</strong></label>
              <div *ngFor="let category of categories">
                <input type="checkbox" [id]="'cat_' + category.id" 
                       (change)="filterProducts()" class="form-check-input">
                <label [for]="'cat_' + category.id" class="form-check-label">
                  {{ category.name }}
                </label>
              </div>
            </div>

            <!-- Price Range -->
            <div class="filter-section">
              <label><strong>السعر</strong></label>
              <div class="price-range">
                <input type="range" min="0" max="10000" [(ngModel)]="priceRange[0]" 
                       (change)="filterProducts()" class="form-range">
                <input type="range" min="0" max="10000" [(ngModel)]="priceRange[1]" 
                       (change)="filterProducts()" class="form-range">
              </div>
              <div class="price-display">
                من {{ priceRange[0] }} إلى {{ priceRange[1] }} ر.س
              </div>
            </div>

            <!-- Sort -->
            <div class="filter-section">
              <label><strong>الترتيب</strong></label>
              <select [(ngModel)]="sortBy" (change)="filterProducts()" class="form-select">
                <option value="">الافتراضي</option>
                <option value="price_asc">السعر (من الأقل إلى الأعلى)</option>
                <option value="price_desc">السعر (من الأعلى إلى الأقل)</option>
                <option value="name_asc">الاسم (أ-ي)</option>
                <option value="name_desc">الاسم (ي-أ)</option>
              </select>
            </div>

            <button (click)="resetFilters()" class="btn btn-outline-primary w-100">
              <i class="fas fa-redo"></i> إعادة تعيين
            </button>
          </div>
        </div>

        <!-- Products -->
        <div class="col-lg-9">
          <div class="products-header">
            <h3>المنتجات ({{ filteredProducts.length }})</h3>
          </div>

          <div class="products-grid">
            <div class="product-card" *ngFor="let product of filteredProducts">
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
                <p class="product-category">{{ product.category_name }}</p>
                
                <div class="product-price">
                  <span class="original-price" *ngIf="product.original_price">
                    {{ product.original_price }} ر.س
                  </span>
                  <span class="current-price">{{ product.price }} ر.س</span>
                </div>

                <div class="product-rating" *ngIf="product.rating">
                  <i class="fas fa-star"></i>
                  {{ product.rating }} / 5
                </div>

                <div class="product-stock" [class.low-stock]="product.stock < 5">
                  <span *ngIf="product.stock > 0">المخزون: {{ product.stock }}</span>
                  <span *ngIf="product.stock === 0" class="text-danger">نفذ من المخزون</span>
                </div>

                <div class="product-actions">
                  <a [routerLink]="['/products', product.id]" class="btn btn-primary btn-sm flex-grow-1">
                    <i class="fas fa-eye"></i> تفاصيل
                  </a>
                </div>
              </div>
            </div>
          </div>

          <div *ngIf="filteredProducts.length === 0" class="alert alert-info text-center">
            <i class="fas fa-search"></i> لم يتم العثور على منتجات
          </div>
        </div>
      </div>
    </div>
  `,
  styleUrl: './products.component.scss'
})
export class ProductsComponent implements OnInit {
  products: any[] = [];
  filteredProducts: any[] = [];
  categories: any[] = [];
  
  searchTerm = '';
  priceRange = [0, 10000];
  sortBy = '';

  constructor(private apiService: ApiService) {}

  ngOnInit(): void {
    this.loadProducts();
    this.loadCategories();
  }

  loadProducts(): void {
    this.apiService.getArticles().subscribe({
      next: (response) => {
        this.products = response;
        this.filteredProducts = response;
      },
      error: (error) => console.error('Error loading products:', error)
    });
  }

  loadCategories(): void {
    this.apiService.getCategories().subscribe({
      next: (response) => {
        this.categories = response;
      },
      error: (error) => console.error('Error loading categories:', error)
    });
  }

  filterProducts(): void {
    this.filteredProducts = this.products.filter(product => {
      const matchSearch = !this.searchTerm || 
        product.name.toLowerCase().includes(this.searchTerm.toLowerCase());
      const matchPrice = product.price >= this.priceRange[0] && 
        product.price <= this.priceRange[1];

      return matchSearch && matchPrice;
    });

    this.sortProducts();
  }

  sortProducts(): void {
    switch (this.sortBy) {
      case 'price_asc':
        this.filteredProducts.sort((a, b) => a.price - b.price);
        break;
      case 'price_desc':
        this.filteredProducts.sort((a, b) => b.price - a.price);
        break;
      case 'name_asc':
        this.filteredProducts.sort((a, b) => a.name.localeCompare(b.name));
        break;
      case 'name_desc':
        this.filteredProducts.sort((a, b) => b.name.localeCompare(a.name));
        break;
    }
  }

  resetFilters(): void {
    this.searchTerm = '';
    this.priceRange = [0, 10000];
    this.sortBy = '';
    this.filteredProducts = [...this.products];
  }

  onImageError(event: any): void {
    event.target.src = 'https://via.placeholder.com/250x250?text=No+Image';
  }
}
