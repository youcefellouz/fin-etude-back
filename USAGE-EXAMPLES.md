# أمثلة استخدام عملية

## 1️⃣ استخدام API Service

### جلب المنتجات
```typescript
import { Component, OnInit } from '@angular/core';
import { ApiService } from '../core/services/api.service';

@Component({
  selector: 'app-products',
  template: `...`
})
export class ProductsComponent implements OnInit {
  products: any[] = [];
  loading = false;
  
  constructor(private apiService: ApiService) {}
  
  ngOnInit() {
    this.loadProducts();
  }
  
  loadProducts() {
    this.loading = true;
    this.apiService.getArticles(1, '', {}).subscribe({
      next: (data) => {
        this.products = data.data;
        this.loading = false;
      },
      error: (err) => {
        console.error('Error loading products:', err);
        this.loading = false;
      }
    });
  }
  
  filterByPrice(minPrice: number, maxPrice: number) {
    this.apiService.getArticles(1, '', { 
      min_price: minPrice,
      max_price: maxPrice 
    }).subscribe(data => {
      this.products = data.data;
    });
  }
}
```

### جلب تفاصيل منتج واحد
```typescript
import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { ApiService } from '../core/services/api.service';

@Component({
  selector: 'app-product-detail',
  template: `...`
})
export class ProductDetailComponent implements OnInit {
  product: any;
  reviews: any[] = [];
  
  constructor(
    private apiService: ApiService,
    private route: ActivatedRoute
  ) {}
  
  ngOnInit() {
    const id = this.route.snapshot.paramMap.get('id');
    this.loadProductDetail(id);
  }
  
  loadProductDetail(id: string) {
    this.apiService.getArticle(id).subscribe(data => {
      this.product = data.data;
      this.loadReviews(id);
    });
  }
  
  loadReviews(productId: string) {
    this.apiService.getReviews(productId).subscribe(data => {
      this.reviews = data.data;
    });
  }
}
```

---

## 2️⃣ استخدام Auth Service

### تسجيل المستخدم الجديد
```typescript
import { Component } from '@angular/core';
import { AuthService } from '../core/services/auth.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-register',
  template: `
    <form (ngSubmit)="register()">
      <input [(ngModel)]="formData.name" name="name" placeholder="الاسم">
      <input [(ngModel)]="formData.email" name="email" placeholder="البريد">
      <input [(ngModel)]="formData.password" name="password" type="password" placeholder="كلمة المرور">
      <button type="submit">تسجيل</button>
    </form>
  `
})
export class RegisterComponent {
  formData = {
    name: '',
    email: '',
    password: '',
    password_confirmation: ''
  };
  
  constructor(
    private authService: AuthService,
    private router: Router
  ) {}
  
  register() {
    this.authService.register(this.formData).subscribe({
      next: (response) => {
        console.log('Registration successful');
        this.router.navigate(['/login']);
      },
      error: (err) => {
        console.error('Registration error:', err);
      }
    });
  }
}
```

### تسجيل الدخول
```typescript
import { Component } from '@angular/core';
import { AuthService } from '../core/services/auth.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-login',
  template: `
    <form (ngSubmit)="login()">
      <input [(ngModel)]="email" name="email" placeholder="البريد">
      <input [(ngModel)]="password" name="password" type="password" placeholder="كلمة المرور">
      <button type="submit">دخول</button>
    </form>
  `
})
export class LoginComponent {
  email = '';
  password = '';
  
  constructor(
    private authService: AuthService,
    private router: Router
  ) {}
  
  login() {
    this.authService.login(this.email, this.password).subscribe({
      next: (response) => {
        console.log('Login successful');
        this.router.navigate(['/']);
      },
      error: (err) => {
        console.error('Login error:', err);
      }
    });
  }
}
```

### التحقق من حالة تسجيل الدخول
```typescript
import { Component } from '@angular/core';
import { AuthService } from '../core/services/auth.service';

@Component({
  selector: 'app-header',
  template: `
    <nav>
      <div *ngIf="isLoggedIn$ | async as isLoggedIn">
        <div *ngIf="!isLoggedIn">
          <a routerLink="/login">دخول</a>
          <a routerLink="/register">تسجيل</a>
        </div>
        
        <div *ngIf="isLoggedIn">
          <span>{{ (currentUser$ | async)?.name }}</span>
          <button (click)="logout()">خروج</button>
        </div>
      </div>
    </nav>
  `
})
export class HeaderComponent {
  isLoggedIn$ = this.authService.isLoggedIn$;
  currentUser$ = this.authService.currentUser$;
  
  constructor(private authService: AuthService) {}
  
  logout() {
    this.authService.logout();
  }
}
```

---

## 3️⃣ استخدام Cart Service

### إضافة منتج للسلة
```typescript
import { Component } from '@angular/core';
import { CartService } from '../core/services/cart.service';

@Component({
  selector: 'app-product-detail',
  template: `
    <div>
      <h1>{{ product.name }}</h1>
      <p>{{ product.price }}</p>
      <input type="number" [(ngModel)]="quantity" [value]="1">
      <button (click)="addToCart()">أضف للسلة</button>
    </div>
  `
})
export class ProductDetailComponent {
  product: any;
  quantity = 1;
  
  constructor(private cartService: CartService) {}
  
  addToCart() {
    this.cartService.addToCart(this.product, this.quantity);
    alert('تمت الإضافة للسلة');
  }
}
```

### عرض السلة والمجموع
```typescript
import { Component } from '@angular/core';
import { CartService } from '../core/services/cart.service';

@Component({
  selector: 'app-cart',
  template: `
    <div>
      <h1>سلة التسوق</h1>
      
      <div *ngFor="let item of cartItems$ | async as items; let i = index">
        <div>
          <h3>{{ item.product.name }}</h3>
          <p>السعر: {{ item.product.price }}</p>
          <input 
            type="number" 
            [(ngModel)]="item.quantity"
            (change)="updateQuantity(item.product.id, item.quantity)">
          <button (click)="removeFromCart(item.product.id)">حذف</button>
        </div>
      </div>
      
      <h2>الإجمالي: {{ cartTotal$ | async | currency }}</h2>
      <button routerLink="/checkout">متابعة الشراء</button>
    </div>
  `
})
export class CartComponent {
  cartItems$ = this.cartService.cartItems$;
  cartTotal$ = this.cartService.cartTotal$;
  
  constructor(private cartService: CartService) {}
  
  removeFromCart(productId: number) {
    this.cartService.removeFromCart(productId);
  }
  
  updateQuantity(productId: number, quantity: number) {
    if (quantity > 0) {
      this.cartService.updateQuantity(productId, quantity);
    }
  }
}
```

---

## 4️⃣ إنشاء طلب (Order)

```typescript
import { Component } from '@angular/core';
import { ApiService } from '../core/services/api.service';
import { CartService } from '../core/services/cart.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-checkout',
  template: `
    <form (ngSubmit)="createOrder()">
      <h2>معلومات الشحن</h2>
      
      <input 
        [(ngModel)]="orderData.shipping_address" 
        name="address" 
        placeholder="العنوان">
      
      <input 
        [(ngModel)]="orderData.phone" 
        name="phone" 
        placeholder="الهاتف">
      
      <h2>طريقة الدفع</h2>
      <select [(ngModel)]="orderData.payment_method" name="payment">
        <option value="credit_card">بطاقة ائتمان</option>
        <option value="bank_transfer">تحويل بنكي</option>
        <option value="cash">الدفع عند التسليم</option>
      </select>
      
      <button type="submit" [disabled]="loading">{{ loading ? 'جاري المعالجة...' : 'تأكيد الطلب' }}</button>
    </form>
  `
})
export class CheckoutComponent {
  orderData: any = {
    shipping_address: '',
    phone: '',
    payment_method: 'credit_card'
  };
  loading = false;
  
  constructor(
    private apiService: ApiService,
    private cartService: CartService,
    private router: Router
  ) {}
  
  createOrder() {
    this.loading = true;
    this.apiService.createOrder(this.orderData).subscribe({
      next: (response) => {
        console.log('Order created:', response);
        this.cartService.clearCart();
        this.router.navigate(['/orders']);
        alert('تم إنشاء الطلب بنجاح');
        this.loading = false;
      },
      error: (err) => {
        console.error('Order error:', err);
        alert('فشل إنشاء الطلب');
        this.loading = false;
      }
    });
  }
}
```

---

## 5️⃣ عرض الطلبات

```typescript
import { Component, OnInit } from '@angular/core';
import { ApiService } from '../core/services/api.service';

@Component({
  selector: 'app-orders',
  template: `
    <div>
      <h1>طلباتي</h1>
      
      <div *ngIf="orders.length === 0">
        <p>لا توجد طلبات</p>
        <a routerLink="/products">ابدأ التسوق</a>
      </div>
      
      <div *ngFor="let order of orders" class="order-card">
        <h3>الطلب #{{ order.id }}</h3>
        <p>التاريخ: {{ order.created_at | date }}</p>
        <p>الحالة: <span class="badge">{{ order.status }}</span></p>
        <p>الإجمالي: {{ order.total_price | currency }}</p>
        <button (click)="viewOrderDetail(order.id)">عرض التفاصيل</button>
      </div>
    </div>
  `
})
export class OrdersComponent implements OnInit {
  orders: any[] = [];
  loading = false;
  
  constructor(private apiService: ApiService) {}
  
  ngOnInit() {
    this.loadOrders();
  }
  
  loadOrders() {
    this.loading = true;
    this.apiService.getOrders().subscribe({
      next: (response) => {
        this.orders = response.data;
        this.loading = false;
      },
      error: (err) => {
        console.error('Error loading orders:', err);
        this.loading = false;
      }
    });
  }
  
  viewOrderDetail(orderId: number) {
    // navigate to order detail page
  }
}
```

---

## 6️⃣ إضافة تقييم للمنتج

```typescript
import { Component } from '@angular/core';
import { ApiService } from '../core/services/api.service';

@Component({
  selector: 'app-review-form',
  template: `
    <form (ngSubmit)="submitReview()">
      <h3>أضف تقييماً</h3>
      
      <div>
        <label>التقييم:</label>
        <select [(ngModel)]="review.rating" name="rating">
          <option [value]="1">⭐ سيء</option>
          <option [value]="2">⭐⭐ متوسط</option>
          <option [value]="3">⭐⭐⭐ جيد</option>
          <option [value]="4">⭐⭐⭐⭐ جيد جداً</option>
          <option [value]="5">⭐⭐⭐⭐⭐ ممتاز</option>
        </select>
      </div>
      
      <textarea 
        [(ngModel)]="review.comment" 
        name="comment" 
        placeholder="أضف تعليقك..."></textarea>
      
      <button type="submit">إرسال التقييم</button>
    </form>
  `
})
export class ReviewFormComponent {
  productId: number;
  review = {
    rating: 5,
    comment: ''
  };
  
  constructor(private apiService: ApiService) {}
  
  submitReview() {
    const data = {
      article_id: this.productId,
      ...this.review
    };
    
    this.apiService.createReview(data).subscribe({
      next: (response) => {
        console.log('Review added:', response);
        alert('شكراً على تقييمك');
        this.review = { rating: 5, comment: '' };
      },
      error: (err) => {
        console.error('Review error:', err);
      }
    });
  }
}
```

---

## 7️⃣ معالجة الأخطاء

```typescript
import { Component } from '@angular/core';
import { ApiService } from '../core/services/api.service';

@Component({
  selector: 'app-safe-component',
  template: `
    <div *ngIf="loading">جاري التحميل...</div>
    <div *ngIf="error" class="alert alert-danger">{{ error }}</div>
    <div *ngIf="data">{{ data }}</div>
  `
})
export class SafeComponent {
  loading = false;
  error = '';
  data: any;
  
  constructor(private apiService: ApiService) {}
  
  loadData() {
    this.loading = true;
    this.error = '';
    
    this.apiService.getArticles(1, '', {}).subscribe({
      next: (response) => {
        this.data = response.data;
        this.loading = false;
      },
      error: (err) => {
        this.error = 'حدث خطأ في تحميل البيانات. يرجى المحاولة لاحقاً';
        console.error('API Error:', err);
        this.loading = false;
        
        // معالجة أخطاء محددة
        if (err.status === 401) {
          this.error = 'يجب تسجيل الدخول أولاً';
        } else if (err.status === 404) {
          this.error = 'لم يتم العثور على البيانات';
        } else if (err.status === 500) {
          this.error = 'خطأ في الخادم. يرجى محاولة لاحقاً';
        }
      }
    });
  }
}
```

---

## 8️⃣ استخدام Async Pipe

```typescript
import { Component } from '@angular/core';
import { ApiService } from '../core/services/api.service';
import { Observable } from 'rxjs';

@Component({
  selector: 'app-products-list',
  template: `
    <div *ngFor="let product of (products$ | async) as products">
      <h3>{{ product.name }}</h3>
      <p>{{ product.price | currency }}</p>
    </div>
  `
})
export class ProductsListComponent {
  products$: Observable<any>;
  
  constructor(private apiService: ApiService) {
    this.products$ = this.apiService.getArticles(1, '', {});
  }
}
```

---

## 9️⃣ Custom Validation

```typescript
import { Component } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';

@Component({
  selector: 'app-form-example',
  template: `
    <form [formGroup]="form" (ngSubmit)="onSubmit()">
      <input formControlName="email" placeholder="البريد">
      <span *ngIf="form.get('email')?.invalid && form.get('email')?.touched">
        البريد غير صحيح
      </span>
      
      <input formControlName="password" type="password" placeholder="كلمة المرور">
      <span *ngIf="form.get('password')?.invalid && form.get('password')?.touched">
        كلمة المرور يجب أن تكون 8 أحرف على الأقل
      </span>
      
      <button type="submit" [disabled]="form.invalid">إرسال</button>
    </form>
  `
})
export class FormExampleComponent {
  form: FormGroup;
  
  constructor(private fb: FormBuilder) {
    this.form = this.fb.group({
      email: ['', [Validators.required, Validators.email]],
      password: ['', [Validators.required, Validators.minLength(8)]]
    });
  }
  
  onSubmit() {
    if (this.form.valid) {
      console.log(this.form.value);
    }
  }
}
```

---

## 🔟 استخدام Interceptor

```typescript
// يتم تطبيقه تلقائياً على جميع الطلبات
// لا تحتاج لإضافة التوكن يدوياً

import { Injectable } from '@angular/core';
import { HttpInterceptor, HttpRequest, HttpHandler, HttpEvent } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable()
export class AuthInterceptor implements HttpInterceptor {
  intercept(req: HttpRequest<any>, next: HttpHandler): Observable<HttpEvent<any>> {
    const token = localStorage.getItem('auth_token');
    
    if (token) {
      req = req.clone({
        setHeaders: {
          Authorization: `Bearer ${token}`
        }
      });
    }
    
    return next.handle(req);
  }
}
```

---

## 📝 الخلاصة

جميع هذه الأمثلة جاهزة للاستخدام مباشرة. اختر ما تحتاجه وأضفه إلى مشروعك!
