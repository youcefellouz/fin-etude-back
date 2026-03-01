import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router } from '@angular/router';
import { CartService } from '../../core/services/cart.service';
import { AuthService } from '../../core/services/auth.service';
import { ApiService } from '../../core/services/api.service';

@Component({
  selector: 'app-checkout',
  standalone: true,
  imports: [CommonModule, FormsModule],
  template: `
    <div class="container mt-4 mb-5">
      <h2 class="mb-4"><i class="fas fa-credit-card"></i> إتمام الشراء</h2>

      <div class="row">
        <div class="col-lg-8">
          <!-- Shipping Information -->
          <div class="form-section">
            <h4><i class="fas fa-map-marker-alt"></i> معلومات الشحن</h4>
            
            <form>
              <div class="form-row">
                <div class="form-group">
                  <label>الاسم الكامل</label>
                  <input type="text" [(ngModel)]="shippingInfo.name" name="name" class="form-control">
                </div>
                <div class="form-group">
                  <label>رقم الهاتف</label>
                  <input type="tel" [(ngModel)]="shippingInfo.phone" name="phone" class="form-control">
                </div>
              </div>

              <div class="form-group">
                <label>البريد الإلكتروني</label>
                <input type="email" [(ngModel)]="shippingInfo.email" name="email" class="form-control">
              </div>

              <div class="form-group">
                <label>العنوان</label>
                <input type="text" [(ngModel)]="shippingInfo.address" name="address" class="form-control">
              </div>

              <div class="form-row">
                <div class="form-group">
                  <label>المدينة</label>
                  <input type="text" [(ngModel)]="shippingInfo.city" name="city" class="form-control">
                </div>
                <div class="form-group">
                  <label>الرمز البريدي</label>
                  <input type="text" [(ngModel)]="shippingInfo.postalCode" name="postalCode" class="form-control">
                </div>
              </div>
            </form>
          </div>

          <!-- Shipping Method -->
          <div class="form-section">
            <h4><i class="fas fa-truck"></i> طريقة الشحن</h4>
            
            <div class="shipping-options">
              <div class="shipping-option">
                <input type="radio" id="fast" value="fast" [(ngModel)]="shippingMethod" name="shipping">
                <label for="fast">
                  <strong>شحن سريع (24 ساعة)</strong>
                  <p>50 ر.س</p>
                </label>
              </div>
              <div class="shipping-option">
                <input type="radio" id="standard" value="standard" [(ngModel)]="shippingMethod" name="shipping">
                <label for="standard">
                  <strong>شحن عادي (3-5 أيام)</strong>
                  <p>30 ر.س</p>
                </label>
              </div>
            </div>
          </div>

          <!-- Payment Method -->
          <div class="form-section">
            <h4><i class="fas fa-credit-card"></i> طريقة الدفع</h4>
            
            <div class="payment-options">
              <div class="payment-option">
                <input type="radio" id="credit" value="credit" [(ngModel)]="paymentMethod" name="payment">
                <label for="credit">
                  <i class="fas fa-credit-card"></i>
                  <strong>بطاقة ائتمان</strong>
                </label>
              </div>
              <div class="payment-option">
                <input type="radio" id="cod" value="cod" [(ngModel)]="paymentMethod" name="payment">
                <label for="cod">
                  <i class="fas fa-money-bill-wave"></i>
                  <strong>الدفع عند الاستقبال</strong>
                </label>
              </div>
            </div>
          </div>

          <!-- Credit Card Form -->
          <div class="form-section" *ngIf="paymentMethod === 'credit'">
            <h4>معلومات البطاقة</h4>
            
            <form>
              <div class="form-group">
                <label>رقم البطاقة</label>
                <input type="text" [(ngModel)]="cardInfo.cardNumber" name="cardNumber" 
                       placeholder="1234 5678 9012 3456" class="form-control">
              </div>

              <div class="form-row">
                <div class="form-group">
                  <label>تاريخ الانتهاء</label>
                  <input type="text" [(ngModel)]="cardInfo.expiryDate" name="expiryDate" 
                         placeholder="MM/YY" class="form-control">
                </div>
                <div class="form-group">
                  <label>CVV</label>
                  <input type="text" [(ngModel)]="cardInfo.cvv" name="cvv" 
                         placeholder="123" class="form-control">
                </div>
              </div>
            </form>
          </div>
        </div>

        <!-- Order Summary -->
        <div class="col-lg-4">
          <div class="order-summary">
            <h4>ملخص الطلب</h4>

            <div class="summary-items">
              <div class="summary-item" *ngFor="let item of cartItems">
                <span>{{ item.name }} x{{ item.quantity }}</span>
                <span>{{ item.price * item.quantity }} ر.س</span>
              </div>
            </div>

            <div class="summary-row">
              <span>المجموع الفرعي:</span>
              <strong>{{ getSubtotal() }} ر.س</strong>
            </div>

            <div class="summary-row">
              <span>الشحن:</span>
              <strong>{{ getShippingCost() }} ر.س</strong>
            </div>

            <div class="summary-row">
              <span>الضريبة:</span>
              <strong>{{ getTax() }} ر.س</strong>
            </div>

            <div class="summary-row total">
              <span>الإجمالي:</span>
              <strong>{{ getTotal() }} ر.س</strong>
            </div>

            <button (click)="placeOrder()" class="btn btn-primary btn-lg w-100" [disabled]="placing">
              <span *ngIf="!placing"><i class="fas fa-check"></i> تأكيد الطلب</span>
              <span *ngIf="placing">جاري المعالجة...</span>
            </button>

            <div *ngIf="error" class="alert alert-danger mt-3">
              {{ error }}
            </div>

            <div *ngIf="success" class="alert alert-success mt-3">
              {{ success }}
            </div>
          </div>
        </div>
      </div>
    </div>
  `,
  styleUrl: './checkout.component.scss'
})
export class CheckoutComponent implements OnInit {
  cartItems: any[] = [];
  
  shippingInfo = {
    name: '',
    phone: '',
    email: '',
    address: '',
    city: '',
    postalCode: ''
  };

  shippingMethod = 'fast';
  paymentMethod = 'cod';
  
  cardInfo = {
    cardNumber: '',
    expiryDate: '',
    cvv: ''
  };

  placing = false;
  error = '';
  success = '';

  constructor(
    private cartService: CartService,
    private authService: AuthService,
    private apiService: ApiService,
    private router: Router
  ) {}

  ngOnInit(): void {
    this.cartService.cart$.subscribe(items => {
      this.cartItems = items;
    });

    const user = this.authService.getCurrentUser();
    if (user) {
      this.shippingInfo.name = user.name;
      this.shippingInfo.email = user.email;
    }
  }

  getSubtotal(): number {
    return this.cartItems.reduce((total, item) => total + (item.price * item.quantity), 0);
  }

  getShippingCost(): number {
    return this.shippingMethod === 'fast' ? 50 : 30;
  }

  getTax(): number {
    return Math.round(this.getSubtotal() * 0.15);
  }

  getTotal(): number {
    return this.getSubtotal() + this.getShippingCost() + this.getTax();
  }

  placeOrder(): void {
    if (!this.validateForm()) {
      return;
    }

    this.placing = true;
    this.error = '';

    const articles = this.cartItems.map(item => ({
      article_id: item.id,
      quantity: item.quantity
    }));

    const orderData = {
      articles,
      guest_name: this.shippingInfo.name,
      guest_phone: this.shippingInfo.phone,
      shipping_address: this.shippingInfo.address,
      city: this.shippingInfo.city,
      postal_code: this.shippingInfo.postalCode
    };

    this.apiService.createOrder(orderData).subscribe({
      next: (response) => {
        this.placing = false;
        this.success = 'تم إنشاء الطلب بنجاح!';
        this.cartService.clearCart();
        
        setTimeout(() => {
          if (this.paymentMethod === 'credit') {
            // Process credit card payment
            this.processPayment(response.id);
          } else {
            // COD - order is complete
            this.router.navigate(['/orders']);
          }
        }, 1500);
      },
      error: (err) => {
        this.placing = false;
        this.error = err.error?.message || 'حدث خطأ في إنشاء الطلب';
      }
    });
  }

  processPayment(orderId: number): void {
    this.apiService.payOrder(orderId, {
      payment_method: this.paymentMethod,
      card_number: this.cardInfo.cardNumber
    }).subscribe({
      next: () => {
        this.router.navigate(['/orders']);
      },
      error: (err) => {
        this.error = err.error?.message || 'حدث خطأ في معالجة الدفع';
      }
    });
  }

  validateForm(): boolean {
    if (!this.shippingInfo.name || !this.shippingInfo.phone || 
        !this.shippingInfo.email || !this.shippingInfo.address) {
      this.error = 'الرجاء ملء جميع معلومات الشحن';
      return false;
    }

    if (this.paymentMethod === 'credit') {
      if (!this.cardInfo.cardNumber || !this.cardInfo.expiryDate || !this.cardInfo.cvv) {
        this.error = 'الرجاء ملء جميع بيانات البطاقة';
        return false;
      }
    }

    return true;
  }
}
