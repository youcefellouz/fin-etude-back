# البدء السريع - تشغيل المتجر الإلكتروني 🚀

## ✅ المتطلبات
- Node.js 16 أو أحدث
- npm أو pnpm
- متصفح حديث (Chrome, Firefox, Safari)

---

## 📥 الخطوة 1: تثبيت الاعتمادات

```bash
npm install
```

أو إذا كنت تستخدم pnpm:
```bash
pnpm install
```

---

## 🔧 الخطوة 2: تعديل عنوان API (مهم جداً!)

افتح الملف:
```
src/app/core/services/api.service.ts
```

ابحث عن السطر:
```typescript
private apiUrl = 'http://localhost:8000/api';
```

غيره إلى عنوان backend الخاص بك:
```typescript
// مثال:
private apiUrl = 'https://your-domain.com/api';
// أو
private apiUrl = 'http://192.168.1.100:8000/api';
```

---

## 🎯 الخطوة 3: تشغيل خادم التطوير

```bash
npm start
```

أو:
```bash
ng serve
```

ستظهر هذه الرسالة:
```
Angular Live Development Server is listening on localhost:4200
```

---

## 🌐 الخطوة 4: الوصول للتطبيق

اذهب إلى المتصفح واكتب:
```
http://localhost:4200
```

---

## 📱 الصفحات المتاحة

بعد التشغيل، ستتمكن من زيارة:

| الصفحة | الرابط | الوصف |
|-------|--------|-------|
| الصفحة الرئيسية | `/` | عرض المنتجات المميزة |
| المنتجات | `/products` | قائمة كاملة للمنتجات |
| تفاصيل المنتج | `/product/{id}` | تفاصيل منتج واحد |
| السلة | `/cart` | سلة التسوق |
| الدفع | `/checkout` | صفحة الدفع والشحن |
| تسجيل الدخول | `/login` | تسجيل دخول المستخدم |
| التسجيل | `/register` | إنشاء حساب جديد |
| الملف الشخصي | `/profile` | بيانات المستخدم |
| الطلبات | `/orders` | الطلبات السابقة |

---

## 🧪 اختبار التطبيق

### 1. اختبر الصفحة الرئيسية
- تحقق من ظهور المنتجات
- اختبر الروابط في القائمة العلوية

### 2. اختبر قائمة المنتجات
- جرب البحث
- جرب التصفية
- جرب الترتيب

### 3. اختبر السلة
- أضف منتج للسلة
- غير الكمية
- احذف منتج

### 4. اختبر تسجيل الدخول
- انشئ حساب جديد
- سجل دخول
- قم بتسجيل خروج

### 5. اختبر الطلبات
- أنشئ طلب جديد
- عرض الطلبات السابقة

---

## 🐛 حل المشاكل الشائعة

### المشكلة: "Cannot find module"
```bash
# الحل: أعد تثبيت الاعتمادات
rm -rf node_modules
npm install
```

### المشكلة: "API not responding"
تحقق من:
- ✓ عنوان API صحيح في `api.service.ts`
- ✓ Backend قيد التشغيل
- ✓ CORS مفعل في Backend

### المشكلة: "Port 4200 already in use"
```bash
# استخدم منفذ مختلف
ng serve --port 4300
```

### المشكلة: "الصور لا تظهر"
تحقق من:
- ✓ عنوان صور API صحيح
- ✓ الصور موجودة في Backend

---

## 📦 البناء للإنتاج

عندما تكون مستعداً للنشر:

```bash
npm run build
```

سيتم إنشاء مجلد `dist/` يحتوي على:
- ملفات HTML مصغرة
- ملفات JavaScript مصغرة
- ملفات CSS محسنة

---

## 🚀 النشر على الإنتاج

### على Vercel
```bash
# تثبيت Vercel CLI
npm install -g vercel

# نشر المشروع
vercel
```

### على Netlify
```bash
# تثبيت Netlify CLI
npm install -g netlify-cli

# نشر المشروع
netlify deploy
```

### على أي خادم
انسخ محتوى `dist/` إلى خادمك

---

## 🔐 الأمان للإنتاج

قبل النشر، تأكد من:

1. ✅ تغيير API URL إلى HTTPS
```typescript
private apiUrl = 'https://your-domain.com/api';
```

2. ✅ تفعيل CORS بشكل آمن في Backend

3. ✅ استخدام متغيرات البيئة
```typescript
// استخدم environment variables
import { environment } from './environments/environment';
private apiUrl = environment.apiUrl;
```

4. ✅ تفعيل HTTPS في الخادم

---

## 📊 مراقبة الأداء

استخدم Angular DevTools:
1. افتح Chrome DevTools (F12)
2. اذهب إلى Profiler
3. اختبر أداء التطبيق

---

## 💾 حفظ البيانات المحلية

البيانات التالية تُحفظ في `localStorage`:
- 🔐 التوكن (عند تسجيل الدخول)
- 🛒 السلة
- 👤 بيانات المستخدم

---

## 📝 ملفات الإعدادات

### `environment.ts` (التطوير)
```typescript
export const environment = {
  production: false,
  apiUrl: 'http://localhost:8000/api'
};
```

### `environment.prod.ts` (الإنتاج)
```typescript
export const environment = {
  production: true,
  apiUrl: 'https://your-domain.com/api'
};
```

---

## 🆘 الحصول على الدعم

إذا واجهت مشكلة:

1. 📖 اقرأ `FRONTEND-COMPLETE-GUIDE.md`
2. 📚 اقرأ `USAGE-EXAMPLES.md`
3. 🔍 افتح Chrome DevTools (F12)
4. 📋 شاهد رسائل الخطأ في Console
5. 🐛 استخدم Angular DevTools

---

## ⚡ أوامر مفيدة

```bash
# تشغيل الخادم
npm start

# بناء للإنتاج
npm run build

# تشغيل الاختبارات
npm test

# تفعيل Watch mode
npm run watch

# مسح البناء السابق
rm -rf dist/
```

---

## ✨ التالي

بعد التشغيل الناجح:

1. 🎨 اعدل الألوان والتصميم
2. 🖼️ أضف شعار الشركة
3. 📝 اكتب محتوى الموقع
4. 🧪 اختبر جميع الميزات
5. 🚀 انشر على الإنتاج

---

**تم إنشاؤه بواسطة v0 - Angular 21 E-Commerce Frontend**
**آخر تحديث:** 2026-03-01

🎉 مبروك! أنت الآن جاهز للبدء!
