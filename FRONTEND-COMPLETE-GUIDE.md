# دليل الـ Frontend الكامل - متجر إلكتروني معدات الحاسوب

## 📁 بنية المشروع الكاملة

```
fin-etude-front/
├── src/
│   ├── main.ts                          # نقطة الدخول الرئيسية
│   ├── index.html                       # صفحة HTML الرئيسية
│   ├── styles.scss                      # الأنماط العامة
│   ├── app/
│   │   ├── app.component.ts             # المكون الرئيسي
│   │   ├── app.component.scss           # أنماط المكون الرئيسي
│   │   ├── app.routes.ts                # التوجيه (Routing)
│   │   ├── core/                        # الخدمات والـ Interceptors
│   │   │   ├── services/
│   │   │   │   ├── api.service.ts       # خدمة الـ API
│   │   │   │   ├── auth.service.ts      # خدمة المصادقة
│   │   │   │   └── cart.service.ts      # خدمة السلة
│   │   │   └── interceptors/
│   │   │       └── auth.interceptor.ts  # إضافة التوكن تلقائياً
│   │   ├── pages/                       # الصفحات الرئيسية
│   │   │   ├── home/                    # الصفحة الرئيسية
│   │   │   ├── products/                # قائمة المنتجات
│   │   │   ├── product-detail/          # تفاصيل المنتج
│   │   │   ├── cart/                    # سلة التسوق
│   │   │   ├── checkout/                # صفحة الدفع
│   │   │   ├── auth/
│   │   │   │   ├── login/               # صفحة تسجيل الدخول
│   │   │   │   └── register/            # صفحة التسجيل
│   │   │   ├── profile/                 # الملف الشخصي
│   │   │   └── orders/                  # الطلبات السابقة
│   │   └── shared/                      # مكونات مشتركة
│   │       └── layout/
│   │           ├── header/              # الرأس (Navigation)
│   │           └── footer/              # التذييل
├── angular.json                         # إعدادات Angular
├── tsconfig.json                        # إعدادات TypeScript
├── package.json                         # الاعتمادات
└── README-ANGULAR.md                    # التوثيق
```

---

## 🔧 الخدمات الأساسية (Core Services)

### 1️⃣ API Service (`api.service.ts`)

```typescript
// استخدام الخدمة في المكونات
export class ProductsComponent {
  constructor(private apiService: ApiService) {}
  
  ngOnInit() {
    // جلب المنتجات
    this.apiService.getArticles().subscribe(data => {
      this.products = data;
    });
  }
}
```

**الدوال الرئيسية:**
- `getArticles(page, search, filters)` - جلب المنتجات
- `getArticle(id)` - جلب تفاصيل منتج
- `getCategories()` - جلب الفئات
- `getBrands()` - جلب العلامات التجارية
- `createOrder(data)` - إنشاء طلب
- `login(credentials)` - تسجيل الدخول
- `register(data)` - التسجيل

---

### 2️⃣ Auth Service (`auth.service.ts`)

```typescript
// التحقق من حالة المستخدم
export class HeaderComponent {
  isLoggedIn$ = this.authService.isLoggedIn$;
  currentUser$ = this.authService.currentUser$;
  
  constructor(private authService: AuthService) {}
  
  logout() {
    this.authService.logout();
  }
}
```

**الميزات:**
- BehaviorSubject لحالة المستخدم الحية
- حفظ التوكن في localStorage
- دالة تحديث بيانات المستخدم

---

### 3️⃣ Cart Service (`cart.service.ts`)

```typescript
// إدارة السلة
export class CartComponent {
  cartItems$ = this.cartService.cartItems$;
  cartTotal$ = this.cartService.cartTotal$;
  
  constructor(private cartService: CartService) {}
  
  addToCart(product: any, quantity: number) {
    this.cartService.addToCart(product, quantity);
  }
  
  removeFromCart(productId: number) {
    this.cartService.removeFromCart(productId);
  }
}
```

**الوظائف:**
- إضافة/حذف من السلة
- تحديث الكميات
- حساب الإجمالي
- Persistent storage مع localStorage

---

## 📄 الصفحات الرئيسية

### 🏠 الصفحة الرئيسية (Home)
**المسار:** `/`
- عرض أفضل المنتجات
- فئات المنتجات
- صور معروضة
- أيقونات الميزات

### 📦 قائمة المنتجات (Products)
**المسار:** `/products`
- عرض جميع المنتجات
- **تصفية متقدمة:**
  - حسب السعر (Min/Max)
  - حسب الفئة
  - حسب العلامة التجارية
  - البحث بالاسم
- **ترتيب:**
  - السعر (الأقل/الأعلى)
  - الأحدث
  - الأكثر تقيماً
- **التصفح:** Pagination

### 🔍 تفاصيل المنتج (Product Detail)
**المسار:** `/product/:id`
- صور المنتج
- الوصف الكامل
- السعر والخصم
- التقييمات والآراء
- زر الإضافة للسلة
- منتجات مشابهة

### 🛒 سلة التسوق (Cart)
**المسار:** `/cart`
- عرض جميع المنتجات المختارة
- تحديث الكميات
- حذف المنتجات
- ملخص الطلب (السعر الإجمالي، الضريبة، الشحن)
- زر المتابعة للدفع

### 💳 صفحة الدفع (Checkout)
**المسار:** `/checkout`
- معلومات الشحن
- اختيار طريقة الشحن
- معلومات الدفع
- ملخص الطلب النهائي
- تأكيد الطلب

### 🔐 تسجيل الدخول (Login)
**المسار:** `/login`
- حقول البريد وكلمة المرور
- خيار "تذكرني"
- رابط استرجاع كلمة المرور
- رابط للتسجيل الجديد

### 📝 التسجيل (Register)
**المسار:** `/register`
- نموذج تسجيل شامل
- التحقق من البيانات
- رسائل خطأ واضحة
- رابط تسجيل الدخول

### 👤 الملف الشخصي (Profile)
**المسار:** `/profile` (يتطلب تسجيل دخول)
- تعديل البيانات الشخصية
- تغيير كلمة المرور
- عناوين الشحن

### 📋 الطلبات (Orders)
**المسار:** `/orders` (يتطلب تسجيل دخول)
- عرض جميع الطلبات السابقة
- حالة كل طلب
- تفاصيل الطلب
- تتبع الشحنة

---

## 🎨 نظام الألوان والتصميم

### الألوان الأساسية
```scss
// Primary Colors
$primary-blue: #0066cc;
$secondary-blue: #003d99;
$light-blue: #e6f0ff;

// Neutrals
$white: #ffffff;
$light-gray: #f5f5f5;
$medium-gray: #999999;
$dark-gray: #333333;
$black: #000000;

// Accents
$success-green: #28a745;
$warning-orange: #ff9800;
$danger-red: #dc3545;
```

### الخطوط
- **العناوين:** Segoe UI Bold
- **النص الأساسي:** Segoe UI Regular
- **الأكواد:** Monaco, Courier

### الحد الأدنى للقيم
- **Border Radius:** 8px
- **Box Shadow:** 0 2px 8px rgba(0,0,0,0.1)
- **Transition:** 0.3s ease

---

## 📱 التوافقية والـ Responsive

المتجر يعمل على:
- ✅ الهواتف الذكية (Mobile)
- ✅ الأجهزة اللوحية (Tablet)
- ✅ أجهزة سطح المكتب (Desktop)
- ✅ الشاشات الكبيرة (Large Screens)

**Breakpoints:**
- Mobile: < 576px
- Tablet: 576px - 992px
- Desktop: > 992px

---

## 🚀 تثبيت وتشغيل

### المتطلبات
- Node.js 16+
- npm أو pnpm

### خطوات التثبيت
```bash
# 1. تثبيت الاعتمادات
npm install

# 2. تغيير عنوان API (إن لزم)
# في src/app/core/services/api.service.ts
// private apiUrl = 'http://localhost:8000/api'; // غير هذا

# 3. تشغيل خادم التطوير
npm start

# 4. الوصول إلى التطبيق
# اذهب إلى http://localhost:4200
```

### البناء للإنتاج
```bash
npm run build
```

---

## 🔗 API Endpoints المستخدمة

```
POST   /api/auth/register          # التسجيل
POST   /api/auth/login             # تسجيل الدخول
POST   /api/auth/logout            # تسجيل الخروج
POST   /api/auth/refresh-password  # تحديث كلمة المرور

GET    /api/articles               # جلب المنتجات
GET    /api/articles/{id}          # تفاصيل منتج
GET    /api/categories             # الفئات
GET    /api/brands                 # العلامات التجارية

POST   /api/orders                 # إنشاء طلب
GET    /api/orders                 # الطلبات الخاصة بي
GET    /api/orders/{id}            # تفاصيل طلب

POST   /api/reviews                # إضافة تقييم
GET    /api/reviews/{articleId}    # تقييمات المنتج

GET    /api/profile                # بيانات المستخدم
PUT    /api/profile                # تحديث البيانات
```

---

## 🔐 نظام الأمان

### Authentication Flow
1. المستخدم يدخل بيانات التسجيل
2. الخادم يرد بـ Token (JWT)
3. يتم حفظ Token في localStorage
4. Interceptor يضيف Token في كل طلب API
5. عند انتهاء الجلسة، يتم حذف Token

### CORS Configuration
يتم التعامل مع CORS من جانب Laravel Backend

---

## 📊 هيكل البيانات الرئيسي

### Product (المنتج)
```typescript
interface Product {
  id: number;
  name: string;
  description: string;
  price: number;
  discount_price?: number;
  image: string;
  category_id: number;
  brand_id: number;
  stock: number;
  rating: number;
  reviews_count: number;
}
```

### Order (الطلب)
```typescript
interface Order {
  id: number;
  user_id: number;
  total_price: number;
  status: 'pending' | 'processing' | 'shipped' | 'delivered';
  shipping_address: string;
  payment_method: string;
  created_at: string;
  items: OrderItem[];
}
```

### User (المستخدم)
```typescript
interface User {
  id: number;
  name: string;
  email: string;
  phone: string;
  address?: string;
  city?: string;
  country?: string;
}
```

---

## 🛠️ المكونات المساعدة

### Header Component
- شريط التنقل
- قائمة الفئات
- عرض السلة (عدد المنتجات)
- قائمة المستخدم (تسجيل دخول/خروج)

### Footer Component
- روابط سريعة
- معلومات الاتصال
- الروابط الاجتماعية
- حقوق الملكية

---

## 🐛 التصحيح (Debugging)

### استخدام Console
```typescript
// أضف هذا في أي مكون
console.log("[v0] Component loaded", this.data);
```

### استخدام Angular DevTools
```
chrome://extensions/
```
ثم ابحث عن "Angular DevTools"

---

## 📦 الاعتمادات الرئيسية

```json
{
  "@angular/core": "^21.0.0",
  "@angular/forms": "^21.0.0",
  "@angular/router": "^21.0.0",
  "@angular/animations": "^21.0.0",
  "bootstrap": "^5.3.0",
  "axios": "^1.7.0",
  "sweetalert2": "^11.10.0",
  "rxjs": "^7.8.0"
}
```

---

## ✨ الميزات المتقدمة

### ✔️ Reactive Programming
- استخدام RxJS و Observables
- BehaviorSubject للحالة الحية

### ✔️ Smart Filtering
- تصفية متعددة الطبقات
- بحث فوري
- ترتيب ديناميكي

### ✔️ State Management
- Services-based State Management
- Centralized Cart Management
- User Authentication State

### ✔️ Error Handling
- معالجة أخطاء API شاملة
- رسائل خطأ صديقة للمستخدم
- Toast Notifications

### ✔️ Performance
- Lazy Loading للصور
- Pagination للمنتجات
- Caching الذكي

---

## 📞 الدعم والمساعدة

إذا واجهت مشكلة:

1. تحقق من عنوان API في `api.service.ts`
2. تأكد من تثبيت جميع الاعتمادات: `npm install`
3. تحقق من وصل الـ Backend
4. افتح الـ Browser Console لرؤية الأخطاء
5. استخدم Angular DevTools للتصحيح

---

## 🎯 الخطوات التالية

1. ✅ تثبيت المشروع
2. ✅ تشغيل الخادم
3. ✅ اختبار جميع الصفحات
4. ✅ تخصيص الألوان والشعار
5. ✅ نشر على الإنتاج

---

**تم إنشاؤه بواسطة v0 - Angular 21 E-Commerce Frontend**
**آخر تحديث:** 2026-03-01
