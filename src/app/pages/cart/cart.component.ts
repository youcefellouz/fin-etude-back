import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterLink } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { CartService, CartItem } from '../../core/services/cart.service';

@Component({
  selector: 'app-cart',
  standalone: true,
  imports: [CommonModule, RouterLink, FormsModule],
  template: `
    <div class="container mt-4 mb-5">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="/">الرئيسية</a></li>
          <li class="breadcrumb-item active">سلة التسوق</li>
        </ol>
      </nav>

      <div class="row">
        <div class="col-lg-8">
          <div class="cart-items-container">
            <h3 class="mb-4"><i class="fas fa-shopping-cart"></i> سلة التسوق</h3>

            <div *ngIf="cartItems.length === 0" class="alert alert-info text-center">
              <i class="fas fa-inbox"></i>
              <p>سلة التسوق فارغة</p>
              <a routerLink="/products" class="btn btn-primary">ابدأ التسوق</a>
            </div>

            <div *ngIf="cartItems.length > 0">
              <div class="cart-item" *ngFor="let item of cartItems">
                <div class="item-image">
                  <img [src]="item.image || 'https://via.placeholder.com/100x100'" 
                       [alt]="item.name">
                </div>

                <div class="item-details">
                  <h5>{{ item.name }}</h5>
                  <p class="item-price">{{ item.price }} ر.س</p>
                </div>

                <div class="item-quantity">
                  <button (click)="decreaseQuantity(item.id)" class="btn-qty">-</button>
                  <input type="number" [(ngModel)]="item.quantity" 
                         (change)="updateQuantity(item.id, item.quantity)"
                         min="1" class="qty-input">
                  <button (click)="increaseQuantity(item.id)" class="btn-qty">+</button>
                </div>

                <div class="item-total">
                  {{ item.price * item.quantity }} ر.س
                </div>

                <button (click)="removeItem(item.id)" class="btn-remove">
                  <i class="fas fa-trash"></i>
                </button>
              </div>

              <div class="continue-shopping mt-4">
                <a routerLink="/products" class="btn btn-outline-primary">
                  <i class="fas fa-arrow-right"></i> متابعة التسوق
                </a>
              </div>
            </div>
          </div>
        </div>

        <!-- Cart Summary -->
        <div class="col-lg-4">
          <div class="cart-summary">
            <h4>ملخص الطلب</h4>

            <div class="summary-row">
              <span>عدد المنتجات:</span>
              <strong>{{ getCartItemsCount() }}</strong>
            </div>

            <div class="summary-row">
              <span>المجموع الفرعي:</span>
              <strong>{{ getSubtotal() }} ر.س</strong>
            </div>

            <div class="summary-row">
              <span>رسوم الشحن:</span>
              <strong>{{ shippingCost }} ر.س</strong>
            </div>

            <div class="summary-row">
              <span>الضريبة (15%):</span>
              <strong>{{ getTax() }} ر.س</strong>
            </div>

            <div class="summary-row total">
              <span>الإجمالي:</span>
              <strong>{{ getTotal() }} ر.س</strong>
            </div>

            <button 
              routerLink="/checkout" 
              [disabled]="cartItems.length === 0"
              class="btn btn-primary btn-lg w-100 mt-3">
              <i class="fas fa-credit-card"></i> متابعة الدفع
            </button>

            <div class="mt-3 text-center">
              <small class="text-muted">
                <i class="fas fa-lock"></i> معاملة آمنة وموثوقة
              </small>
            </div>
          </div>

          <div class="promo-code mt-4">
            <h5>كود الخصم</h5>
            <div class="input-group">
              <input type="text" [(ngModel)]="promoCode" 
                     class="form-control" 
                     placeholder="أدخل كود الخصم">
              <button (click)="applyPromoCode()" class="btn btn-primary">تطبيق</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  `,
  styleUrl: './cart.component.scss'
})
export class CartComponent implements OnInit {
  cartItems: CartItem[] = [];
  shippingCost = 50;
  promoCode = '';
  discountPercent = 0;

  constructor(private cartService: CartService) {}

  ngOnInit(): void {
    this.cartService.cart$.subscribe(items => {
      this.cartItems = items;
    });
  }

  removeItem(itemId: number): void {
    this.cartService.removeItem(itemId);
  }

  updateQuantity(itemId: number, quantity: number): void {
    this.cartService.updateQuantity(itemId, quantity);
  }

  increaseQuantity(itemId: number): void {
    const item = this.cartItems.find(i => i.id === itemId);
    if (item) {
      this.updateQuantity(itemId, item.quantity + 1);
    }
  }

  decreaseQuantity(itemId: number): void {
    const item = this.cartItems.find(i => i.id === itemId);
    if (item && item.quantity > 1) {
      this.updateQuantity(itemId, item.quantity - 1);
    }
  }

  getSubtotal(): number {
    return this.cartItems.reduce((total, item) => total + (item.price * item.quantity), 0);
  }

  getTax(): number {
    return Math.round(this.getSubtotal() * 0.15);
  }

  getTotal(): number {
    const subtotal = this.getSubtotal();
    const discount = (subtotal * this.discountPercent) / 100;
    return subtotal - discount + this.shippingCost + this.getTax();
  }

  getCartItemsCount(): number {
    return this.cartItems.reduce((count, item) => count + item.quantity, 0);
  }

  applyPromoCode(): void {
    // Mock promo codes
    const promoCodes: { [key: string]: number } = {
      'WELCOME10': 10,
      'SAVE20': 20,
      'TECH50': 50
    };

    if (promoCodes[this.promoCode]) {
      this.discountPercent = promoCodes[this.promoCode];
      alert(`تم تطبيق كود الخصم! خصم ${this.discountPercent}%`);
    } else {
      alert('كود الخصم غير صحيح');
      this.discountPercent = 0;
    }
  }
}
