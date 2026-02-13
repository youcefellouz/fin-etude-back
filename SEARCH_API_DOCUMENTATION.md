# 🔍 Unified Search API Documentation

## مقدمة

هذه الوثائق تشرح **Unified Search Endpoint** الذي يدعم ثلاث أنواع من البحث:

1. **نص فقط** (Text Search)
2. **صورة فقط** (Image Search)
3. **هجين** (Hybrid Search - نص + صورة)

---

## 📍 Base URL

```
POST /api/search/unified
```

---

## 🎯 الاستخدام الأساسي

### **1️⃣ البحث عن طريق النص فقط**

```bash
curl -X POST http://localhost:8000/api/search/unified \
  -H "Content-Type: application/json" \
  -d '{
    "query": "laptop"
  }'
```

#### **Response:**
```json
{
  "status": "success",
  "search_type": "text",
  "count": 5,
  "limit": 15,
  "offset": 0,
  "weights": {
    "text": 1.0,
    "image": 0.0
  },
  "data": [
    {
      "id": 1,
      "name": "Dell Laptop XPS 13",
      "description": "High-performance laptop",
      "price": 999.99,
      "price_after_discount": 899.99,
      "image_url": "https://example.com/laptop.jpg",
      "category": {
        "id": 1,
        "name": "Electronics"
      },
      "brand": {
        "id": 5,
        "name": "Dell"
      },
      "created_at": "2026-02-13T10:30:00Z",
      "updated_at": "2026-02-13T10:30:00Z"
    }
  ]
}
```

---

### **2️⃣ البحث عن طريق الصورة فقط**

```bash
curl -X POST http://localhost:8000/api/search/unified \
  -F "image=@/path/to/laptop.jpg"
```

#### **Response:**
```json
{
  "status": "success",
  "search_type": "image",
  "count": 3,
  "limit": 15,
  "offset": 0,
  "weights": {
    "text": 0.0,
    "image": 1.0
  },
  "data": [
    {
      "id": 1,
      "name": "Dell Laptop XPS 13",
      "description": "High-performance laptop",
      "price": 999.99,
      "price_after_discount": 899.99,
      "image_url": "https://example.com/laptop.jpg",
      "category": {
        "id": 1,
        "name": "Electronics"
      },
      "brand": {
        "id": 5,
        "name": "Dell"
      },
      "created_at": "2026-02-13T10:30:00Z",
      "updated_at": "2026-02-13T10:30:00Z"
    }
  ]
}
```

---

### **3️⃣ البحث الهجين (نص + صورة)**

```bash
curl -X POST http://localhost:8000/api/search/unified \
  -F "query=laptop" \
  -F "image=@/path/to/laptop.jpg" \
  -F "text_weight=0.6" \
  -F "image_weight=0.4"
```

#### **Response:**
```json
{
  "status": "success",
  "search_type": "hybrid",
  "count": 5,
  "limit": 15,
  "offset": 0,
  "weights": {
    "text": 0.6,
    "image": 0.4
  },
  "data": [
    {
      "id": 1,
      "name": "Dell Laptop XPS 13",
      "description": "High-performance laptop",
      "price": 999.99,
      "price_after_discount": 899.99,
      "image_url": "https://example.com/laptop.jpg",
      "category": {
        "id": 1,
        "name": "Electronics"
      },
      "brand": {
        "id": 5,
        "name": "Dell"
      },
      "text_score": 0.95,
      "image_score": 0.87,
      "hybrid_score": 0.92,
      "created_at": "2026-02-13T10:30:00Z",
      "updated_at": "2026-02-13T10:30:00Z"
    }
  ]
}
```

---

## 📋 معاملات الطلب (Request Parameters)

| المعامل | النوع | الإجباري | الوصف | القيم الافتراضية |
|--------|------|--------|-------|-----------------|
| `query` | string | اختياري | النص المراد البحث عنه | - |
| `image` | file | اختياري | الصورة المراد البحث بها | - |
| `limit` | integer | اختياري | عدد النتائج | 15 |
| `offset` | integer | اختياري | رقم الصفحة | 0 |
| `text_weight` | float | اختياري | وزن البحث النصي (0-1) | 0.5 |
| `image_weight` | float | اختياري | وزن البحث بالصورة (0-1) | 0.5 |

### **ملاحظات مهمة:**

- ✅ يجب تقديم **على الأقل واحد** من: `query` أو `image`
- ✅ يجب أن يكون مجموع الأوزان > 0
- ✅ دعم الصيغ: `jpeg`, `png`, `jpg`, `gif`, `webp`
- ✅ الحد الأقصى لحجم الصورة: **5MB**

---

## 🌍 تكوين خدمات الصور

### **متطلبات الإعداد:**

يجب إضافة بيانات الاعتماد في ملف `.env`:

#### **Google Vision API:**
```env
IMAGE_ANALYSIS_PROVIDER=google
GOOGLE_VISION_API_KEY=your_google_api_key_here
```

#### **AWS Rekognition:**
```env
IMAGE_ANALYSIS_PROVIDER=aws
AWS_ACCESS_KEY_ID=your_access_key
AWS_SECRET_ACCESS_KEY=your_secret_key
AWS_DEFAULT_REGION=us-east-1
```

#### **Azure Computer Vision:**
```env
IMAGE_ANALYSIS_PROVIDER=azure
AZURE_VISION_API_KEY=your_azure_api_key
AZURE_VISION_ENDPOINT=https://your-resource.cognitiveservices.azure.com
```

---

## 🔄 خطوات البحث الهجين

```
1. إرسال النص والصورة
   ↓
2. البحث النصي في قاعدة البيانات
   ↓
3. تحليل الصورة واستخراج كلمات مفتاحية
   ↓
4. البحث عن الكلمات المفتاحية في قاعدة البيانات
   ↓
5. دمج النتائج حسب الأوزان
   ↓
6. ترتيب النتائج حسب درجة التشابه
   ↓
7. إرجاع النتائج المرتبة
```

---

## 📊 شرح النقاط (Scores)

في البحث الهجين، كل مقالة تحصل على ثلاث نقاط:

- **`text_score`** (0-1): درجة تطابق البحث النصي
- **`image_score`** (0-1): درجة تطابق البحث بالصورة
- **`hybrid_score`** (0-1): الدرجة النهائية المحسوبة كالتالي:

```
hybrid_score = (text_score × text_weight) + (image_score × image_weight)
```

---

## 🚀 أمثلة استخدام عملية

### **مثال 1: البحث عن منتج معين**

```javascript
const response = await fetch('http://localhost:8000/api/search/unified', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
  },
  body: JSON.stringify({
    query: 'laptop',
    limit: 20,
    offset: 0,
  }),
});

const data = await response.json();
console.log(data);
```

---

### **مثال 2: البحث بصورة**

```javascript
const formData = new FormData();
formData.append('image', fileInput.files[0]);
formData.append('limit', 10);

const response = await fetch('http://localhost:8000/api/search/unified', {
  method: 'POST',
  body: formData,
});

const data = await response.json();
console.log(data);
```

---

### **مثال 3: البحث الهجين مع أوزان مخصصة**

```javascript
const formData = new FormData();
formData.append('query', 'laptop');
formData.append('image', fileInput.files[0]);
formData.append('text_weight', 0.7);
formData.append('image_weight', 0.3);
formData.append('limit', 15);

const response = await fetch('http://localhost:8000/api/search/unified', {
  method: 'POST',
  body: formData,
});

const data = await response.json();
console.log(data);
```

---

## ❌ رموز الخطأ

| الكود | الرسالة | الحل |
|------|--------|------|
| 400 | لا يوجد query أو image | قدم نص أو صورة على الأقل |
| 400 | الأوزان غير صحيحة | تأكد من أن الأوزان > 0 |
| 413 | الصورة كبيرة جداً | استخدم صورة أقل من 5MB |
| 422 | صيغة الصورة غير مدعومة | استخدم jpeg, png, jpg, gif, webp |
| 500 | خطأ في البحث | تحقق من logs |

---

## 💡 نصائح مفيدة

1. **للأداء الأفضل:** استخدم `limit` مناسب (10-30)
2. **للدقة العالية:** في البحث الهجين، اجعل `text_weight` أعلى إذا كان النص أهم
3. **تحسين البحث:** استخدم كلمات مفتاحية واضحة في `query`
4. **تخزين مؤقت:** فكر في تخزين النتائج مؤقتاً للبحث المتكرر

---

## 📝 ملاحظات إضافية

- جميع الطلبات تستخدم `multipart/form-data` عند إرسال صور
- الاستجابة دائماً بصيغة JSON
- جميع الأوقات بصيغة ISO 8601
- يمكن استخدام المصفوفات الفارغة إذا لم تجد نتائج

---

## 🔗 روابط مفيدة

- [Google Vision API](https://cloud.google.com/vision/docs)
- [AWS Rekognition](https://docs.aws.amazon.com/rekognition/)
- [Azure Computer Vision](https://learn.microsoft.com/en-us/azure/cognitive-services/computer-vision/)
