# ❓ الأسئلة الشائعة والإجابات

## 🆘 أسئلة عامة

### س: هل التطبيق يعمل بدون Backend؟
**ج:** لا، التطبيق يحتاج Backend مع API. تأكد من وجود backend قيد التشغيل وأن عنوان API صحيح في `api.service.ts`.

### س: كيف أغير الألوان والتصميم؟
**ج:** عدّل متغيرات الألوان في `src/styles.scss`:
```scss
$primary-blue: #0066cc;  // غير هذا إلى لون جديد
$success-green: #28a745;
```

### س: هل يدعم الغة العربية (RTL)؟
**ج:** نعم! التطبيق مدعوم كاملاً للغة العربية مع RTL (Right-to-Left).

### س: كيف أضيف ميزة جديدة؟
**ج:** اتبع هذه الخطوات:
1. أنشئ مكون جديد
2. أضف service إذا لزم الأمر
3. أضف المسار في `app.routes.ts`
4. اربط المكون بالـ header أو قائمة التنقل

---

## 🔐 أسئلة الأمان والمصادقة

### س: كيف يتم حفظ كلمة المرور؟
**ج:** لا يتم حفظ كلمة المرور في Frontend. يتم إرسالها عبر HTTPS إلى Backend حيث تُحفظ بشكل آمن (مشفرة).

### س: أين يتم حفظ التوكن؟
**ج:** يتم حفظ التوكن في `localStorage`. يُمكنك تغيير هذا في `auth.service.ts` إلى `sessionStorage` إذا أردت.

### س: هل التوكن آمن؟
**ج:** التوكن محفوظ محلياً والتطبيق يرسله عبر HTTPS. للأمان الأقصى، استخدم httpOnly cookies (يحتاج تعديل في Backend).

### س: ماذا لو انتهت جلسة المستخدم؟
**ج:** عندما ينتهي التوكن، يتم إعادة توجيه المستخدم لصفحة تسجيل الدخول تلقائياً.

---

## 🐛 أسئلة التصحيح والمشاكل

### س: التطبيق لا يعمل. ماذا أفعل؟
**ج:** اتبع هذه الخطوات:
```bash
# 1. تأكد من تثبيت الاعتمادات
npm install

# 2. امسح الـ cache
rm -rf node_modules dist
npm install

# 3. شغل التطبيق من جديد
npm start

# 4. افتح DevTools (F12) وشاهد الأخطاء
```

### س: الـ API غير مستجيب. ماذا أفعل؟
**ج:** 
- تأكد من تشغيل Backend
- تأكد من عنوان API صحيح
- تحقق من CORS في Backend
- شاهد الأخطاء في Network tab (F12)

### س: المنتجات لا تظهر
**ج:**
- تحقق من أن Backend يرد بيانات صحيحة
- شاهد Network tab لرؤية الـ response
- تحقق من اسم الـ API endpoint

### س: السلة لا تعمل
**ج:**
- تحقق من browser console للأخطاء
- تأكد من تفعيل localStorage
- جرب حذف localStorage وأعد التشغيل

### س: صفحة التسجيل لا تعمل
**ج:**
- تحقق من validation rules
- شاهد الأخطاء في browser console
- تأكد من أن Backend يقبل البيانات

---

## 📱 أسئلة التصميم والـ Responsive

### س: التطبيق لا يبدو جيداً على الموبايل
**ج:** 
- تأكد من فتح DevTools وتفعيل "Toggle device toolbar" (Ctrl+Shift+M)
- تحقق من أن CSS media queries صحيح
- جرب تحديث الصفحة (Ctrl+Shift+R)

### س: كيف أغير حجم الخط؟
**ج:** في `src/styles.scss`:
```scss
body {
  font-size: 16px;  // غير هذا
}
```

### س: كيف أضيف صور جديدة؟
**ج:**
- ضع الصور في مجلد `public/` في Backend
- أو استخدم URL صور خارجي
- أو استخدم base64 مباشرة

---

## 🚀 أسئلة النشر والإنتاج

### س: كيف أنشر على الإنتاج؟
**ج:** ثلاث خيارات:

**1. Vercel (الأسهل):**
```bash
npm install -g vercel
vercel
```

**2. Netlify:**
```bash
npm install -g netlify-cli
netlify deploy
```

**3. خادم عادي:**
```bash
npm run build
# انسخ محتوى dist/ إلى خادمك
```

### س: ما الـ environment variables؟
**ج:** استخدم ملفات environment:
```typescript
// src/environments/environment.ts (تطوير)
export const environment = {
  production: false,
  apiUrl: 'http://localhost:8000/api'
};

// src/environments/environment.prod.ts (إنتاج)
export const environment = {
  production: true,
  apiUrl: 'https://your-domain.com/api'
};
```

### س: كيف أتعامل مع CORS؟
**ج:** CORS يتم تعامله في Backend. يجب تفعيل:
```php
// في Laravel
config/cors.php
'paths' => ['api/*'],
'allowed_methods' => ['*'],
'allowed_origins' => ['*'], // غير هذا للأمان
```

### س: ماذا بعد النشر؟
**ج:**
- ✅ اختبر جميع الصفحات
- ✅ اختبر الدفع
- ✅ اختبر تسجيل المستخدمين
- ✅ فعّل HTTPS
- ✅ اختبر الأداء

---

## ⚡ أسئلة الأداء والتحسين

### س: التطبيق بطيء. كيف أسرعه؟
**ج:**
- استخدم Chrome DevTools للـ profiling
- فعّل lazy loading للصور
- استخدم pagination للمنتجات
- قلل عدد الـ API calls

### س: كيف أقلل حجم البناء؟
**ج:**
```bash
# استخدم production build
npm run build -- --optimization=true
```

### س: كيف أفعّل caching؟
**ج:** استخدم HttpClient مع caching:
```typescript
import { HttpClient } from '@angular/common/http';

this.http.get(url, {
  headers: {
    'Cache-Control': 'public, max-age=3600'
  }
}).subscribe(data => {
  // تُحفظ البيانات للـ 1 ساعة
});
```

---

## 🛠️ أسئلة التطوير والتخصيص

### س: كيف أضيف مكون جديد؟
**ج:**
```bash
# استخدم Angular CLI
ng generate component pages/new-page

# أو أنشئ يدوياً:
# src/app/pages/new-page/new-page.component.ts
# src/app/pages/new-page/new-page.component.scss
# src/app/pages/new-page/new-page.component.html
```

### س: كيف أضيف خدمة جديدة؟
**ج:**
```bash
ng generate service core/services/new-service

# أو أنشئ يدوياً:
# src/app/core/services/new.service.ts
```

### س: كيف أغير المسارات؟
**ج:** عدّل `src/app/app.routes.ts`:
```typescript
{
  path: 'new-page',
  component: NewPageComponent
}
```

### س: كيف أضيف guards للحماية؟
**ج:**
```typescript
// src/app/core/guards/auth.guard.ts
import { Injectable } from '@angular/core';
import { CanActivate } from '@angular/router';
import { AuthService } from '../services/auth.service';

@Injectable()
export class AuthGuard implements CanActivate {
  constructor(private authService: AuthService) {}
  
  canActivate(): boolean {
    return this.authService.isLoggedIn();
  }
}

// استخدم في الـ routes:
{
  path: 'protected',
  component: ProtectedComponent,
  canActivate: [AuthGuard]
}
```

---

## 📊 أسئلة البيانات والـ API

### س: كيف أتعامل مع الأخطاء من API؟
**ج:** استخدم error handling في الـ subscription:
```typescript
this.apiService.getArticles().subscribe({
  next: (data) => {
    // نجح
  },
  error: (err) => {
    // خطأ - تعامل معه
    if (err.status === 401) {
      // غير مصرح
    } else if (err.status === 500) {
      // خطأ في الخادم
    }
  }
});
```

### س: كيف أضيف معادلة صور جديدة؟
**ج:** عدّل `api.service.ts`:
```typescript
getImageUrl(imageName: string): string {
  return `${this.apiUrl}/images/${imageName}`;
}
```

### س: كيف أتعامل مع البيانات الكبيرة؟
**ج:**
- استخدم pagination
- استخدم infinite scroll
- قلل عدد الحقول المطلوبة من API

### س: كيف أضيف فلترة جديدة؟
**ج:**
```typescript
// في products.component.ts
filters = {
  category: '',
  brand: '',
  minPrice: 0,
  maxPrice: 10000
};

applyFilters() {
  this.apiService.getArticles(1, '', this.filters)
    .subscribe(data => {
      this.products = data.data;
    });
}
```

---

## 💳 أسئلة الدفع والطلبات

### س: كيف أضيف طرق دفع جديدة؟
**ج:** عدّل `checkout.component.ts`:
```typescript
paymentMethods = [
  { id: 'credit_card', name: 'بطاقة ائتمان' },
  { id: 'paypal', name: 'PayPal' },
  { id: 'apple_pay', name: 'Apple Pay' }
];
```

### س: كيف أتتبع الطلبات؟
**ج:** استخدم orders service:
```typescript
this.apiService.getOrder(orderId).subscribe(data => {
  this.order = data;
  // عرض الحالة والمسار
});
```

### س: كيف أضيف خصومات؟
**ج:** في checkout:
```typescript
applyDiscount(code: string) {
  // تحقق من الكود
  // احسب الخصم
  this.total -= discount;
}
```

---

## 🌐 أسئلة الترجمة والـ i18n

### س: هل يدعم لغات متعددة؟
**ج:** نعم، يمكنك إضافة دعم i18n:
```bash
ng add @angular/localize
```

### س: كيف أترجم المحتوى؟
**ج:** استخدم ngx-translate:
```bash
npm install ngx-translate/core ngx-translate/http-loader
```

---

## 🆘 أسئلة الدعم

### س: أين أجد المساعدة؟
**ج:** 
1. اقرأ `FRONTEND-COMPLETE-GUIDE.md`
2. اقرأ `USAGE-EXAMPLES.md`
3. شاهد `QUICK-START.md`
4. افتح Browser DevTools (F12)
5. استخدم Angular DevTools

### س: كيف أبلّغ عن مشكلة؟
**ج:** 
- استخدم Browser Console (F12)
- انسخ الخطأ الكامل
- وصّف ما تفعل عند حدوث الخطأ
- شارك صورة شاشة إن أمكن

### س: هل هناك تحديثات قادمة؟
**ج:** المشروع جاهز ومكتمل! يمكنك التطوير عليه بحرية.

---

## 📚 مراجع مفيدة

- [Angular Documentation](https://angular.io)
- [TypeScript Documentation](https://typescriptlang.org)
- [RxJS Documentation](https://rxjs.dev)
- [Bootstrap Documentation](https://getbootstrap.com)
- [SCSS Documentation](https://sass-lang.com)

---

**تم آخر تحديث:** 2026-03-01
**إذا لم تجد إجابتك، تواصل معنا!** 📧
