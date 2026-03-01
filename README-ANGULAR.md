# Tech Hub - متجر معدات الحاسوب

## نظرة عامة

تطبيق **e-commerce** احترافي متخصص في بيع معدات الحاسوب (Hardware)، مبني باستخدام **Angular 21** كـ frontend و **Laravel** كـ backend.

## الميزات الرئيسية

✨ **المصادقة والحسابات**
- تسجيل الدخول والتسجيل
- التحقق من البريد الإلكتروني
- استرجاع كلمة المرور
- إدارة الملف الشخصي

🛍️ **التسوق**
- عرض المنتجات مع تصفية متقدمة
- تفاصيل المنتج الكاملة
- عربة التسوق الديناميكية
- نظام الخصم وأكواد التخفيف

💳 **الدفع والطلبات**
- خيارات دفع متعددة (بطاقة ائتمان، الدفع عند الاستقبال)
- طرق شحن متنوعة
- متابعة الطلبات

📊 **التقييمات والمراجعات**
- عرض تقييمات المنتجات
- إضافة تقييمات جديدة
- نظام التقييمات الخماسي

🎨 **واجهة حديثة**
- تصميم مستجيب (Responsive)
- واجهة عربية (RTL)
- تجربة مستخدم سلسة

## البنية الهندسية

```
src/
├── app/
│   ├── core/
│   │   ├── services/
│   │   │   ├── api.service.ts          # اتصالات API
│   │   │   ├── auth.service.ts         # المصادقة
│   │   │   └── cart.service.ts         # إدارة السلة
│   │   └── interceptors/
│   │       └── auth.interceptor.ts     # إضافة التوكن
│   ├── shared/
│   │   └── layout/
│   │       ├── header/                 # الرأس
│   │       └── footer/                 # التذييل
│   ├── pages/
│   │   ├── home/                       # الصفحة الرئيسية
│   │   ├── products/                   # المنتجات
│   │   ├── product-detail/             # تفاصيل المنتج
│   │   ├── cart/                       # السلة
│   │   ├── checkout/                   # الدفع
│   │   ├── auth/
│   │   │   ├── login/                  # تسجيل الدخول
│   │   │   └── register/               # التسجيل
│   │   ├── profile/                    # الملف الشخصي
│   │   └── orders/                     # الطلبات
│   ├── app.component.ts                # المكون الرئيسي
│   ├── app.routes.ts                   # المسارات
│   └── app.component.scss              # الأنماط العام
├── main.ts                             # نقطة الدخول
├── styles.scss                         # الأنماط العام
└── index.html                          # HTML الرئيسي
```

## المتطلبات

- **Node.js** 18+ 
- **npm** أو **yarn** أو **pnpm**
- **Angular CLI** 21

## التثبيت والتشغيل

### 1. تثبيت الاعتمادات

```bash
npm install
# أو
pnpm install
```

### 2. تشغيل خادم التطوير

```bash
ng serve
# أو
npm start
```

التطبيق سيكون متاح على: http://localhost:4200

### 3. البناء للإنتاج

```bash
ng build --configuration production
```

## إعداد الاتصال بـ Backend

1. افتح ملف `src/app/core/services/api.service.ts`
2. عدّل `apiUrl` إلى عنوان backend الخاص بك:

```typescript
private apiUrl = 'http://your-backend-url/api';
```

## الاستخدام

### المسارات الرئيسية

| المسار | الوصف |
|---------|--------|
| `/` | الصفحة الرئيسية |
| `/products` | قائمة المنتجات |
| `/products/:id` | تفاصيل المنتج |
| `/cart` | سلة التسوق |
| `/checkout` | الدفع |
| `/login` | تسجيل الدخول |
| `/register` | التسجيل |
| `/profile` | الملف الشخصي |
| `/orders` | طلباتي |

### أمثلة API

```typescript
// الحصول على المنتجات
this.apiService.getArticles().subscribe(data => { ... });

// الحصول على منتج واحد
this.apiService.getArticle(id).subscribe(data => { ... });

// إنشاء طلب
this.apiService.createOrder(orderData).subscribe(data => { ... });

// تسجيل الدخول
this.authService.login(email, password).subscribe(data => { ... });
```

## الأنماط والألوان

### نظام الألوان
- **Primary**: #2563eb (أزرق)
- **Secondary**: #1e40af (أزرق داكن)
- **Success**: #10b981 (أخضر)
- **Danger**: #ef4444 (أحمر)
- **Warning**: #f59e0b (برتقالي)

### الخطوط
- **Header**: Segoe UI
- **Body**: Segoe UI

## الميزات المتقدمة

### 1. إدارة السلة
```typescript
// إضافة منتج
cartService.addItem(product);

// إزالة منتج
cartService.removeItem(itemId);

// تحديث الكمية
cartService.updateQuantity(itemId, quantity);
```

### 2. المصادقة
```typescript
// تسجيل الدخول
authService.login(email, password);

// التحقق من المصادقة
authService.isAuthenticated();

// الحصول على المستخدم
authService.getCurrentUser();
```

### 3. الاتصالات الآمنة
يتم إضافة التوكن تلقائياً في رأس `Authorization` عبر `auth.interceptor`

## دعم اللغات

التطبيق يدعم اللغة **العربية** بشكل كامل مع واجهة RTL

## معلومات إضافية

### الحجم الأولي
- HTML: صغير جداً
- JavaScript: محسّن
- CSS: مدرج في ملفات الأنماط

### الأداء
- Lazy loading للمكونات
- Caching للبيانات
- Optimized images

## المساعدة والدعم

للمزيد من المعلومات:
- [Angular Documentation](https://angular.io)
- [Bootstrap Documentation](https://getbootstrap.com)
- [RxJS Documentation](https://rxjs.dev)

## الترخيص

MIT License - جميع الحقوق محفوظة

---

**تم الإنشاء بواسطة:** v0  
**التاريخ:** 2024  
**النسخة:** 1.0.0
