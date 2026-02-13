# 🚀 دليل إعداد البحث الموحد (Unified Search Setup)

## المتطلبات الأساسية

- PHP 8.1+
- Laravel 11
- Composer
- قاعدة بيانات (SQLite أو MySQL)
- API Key من خدمة الصور (Google Vision / AWS / Azure)

---

## 📦 الخطوة 1: التثبيت الأساسي

```bash
# 1. استنساخ المشروع
git clone https://github.com/youcefellouz/fin-etude-back.git
cd fin-etude-back

# 2. تثبيت المكتبات
composer install

# 3. نسخ .env
cp .env.example .env

# 4. توليد مفتاح التطبيق
php artisan key:generate

# 5. تشغيل قاعدة البيانات
php artisan migrate
```

---

## 🔧 الخطوة 2: تثبيت المكتبات المطلوبة

### لـ Google Vision API:

```bash
composer require google/cloud-vision
```

### لـ AWS Rekognition:

```bash
composer require aws/aws-sdk-php
```

### لـ Guzzle HTTP (للـ API Calls):

```bash
composer require guzzlehttp/guzzle
```

---

## 🔐 الخطوة 3: إعداد بيانات الاعتماد

### خيار 1️⃣: Google Vision API

1. اذهب إلى [Google Cloud Console](https://console.cloud.google.com/)
2. أنشئ مشروع جديد
3. فعّل **Vision API**
4. أنشئ Service Account بصلاحيات `Editor`
5. حمّل الـ JSON Key
6. أضف في `.env`:

```env
IMAGE_ANALYSIS_PROVIDER=google
GOOGLE_VISION_API_KEY=your_api_key_here
```

---

### خيار 2️⃣: AWS Rekognition

1. اذهب إلى [AWS Console](https://console.aws.amazon.com/)
2. انتقل إلى **IAM → Create User**
3. أضف صلاحيات `AmazonRekognitionFullAccess`
4. أنشئ Access Key
5. أضف في `.env`:

```env
IMAGE_ANALYSIS_PROVIDER=aws
AWS_ACCESS_KEY_ID=your_access_key
AWS_SECRET_ACCESS_KEY=your_secret_key
AWS_DEFAULT_REGION=us-east-1
```

---

### خيار 3️⃣: Azure Computer Vision

1. اذهب إلى [Azure Portal](https://portal.azure.com/)
2. أنشئ **Computer Vision Resource**
3. احصل على API Key و Endpoint
4. أضف في `.env`:

```env
IMAGE_ANALYSIS_PROVIDER=azure
AZURE_VISION_API_KEY=your_api_key
AZURE_VISION_ENDPOINT=https://your-resource.cognitiveservices.azure.com
```

---

## 📝 الخطوة 4: التحقق من التثبيت

```bash
# تشغيل السيرفر
php artisan serve

# اختبار Endpoint
curl -X POST http://localhost:8000/api/search/unified \
  -H "Content-Type: application/json" \
  -d '{"query": "laptop"}'
```

---

## 🧪 الخطوة 5: الاختبار

### البحث النصي:

```bash
curl -X POST http://localhost:8000/api/search/unified \
  -H "Content-Type: application/json" \
  -d '{
    "query": "laptop",
    "limit": 10
  }'
```

### البحث بالصورة:

```bash
curl -X POST http://localhost:8000/api/search/unified \
  -F "image=@/path/to/image.jpg" \
  -F "limit=10"
```

### البحث الهجين:

```bash
curl -X POST http://localhost:8000/api/search/unified \
  -F "query=laptop" \
  -F "image=@/path/to/image.jpg" \
  -F "text_weight=0.6" \
  -F "image_weight=0.4"
```

---

## 📚 البنية الملفية

```
app/
├── Http/
│   └── Controllers/
│       └── SearchController.php      # المتحكم الرئيسي
├── Services/
│   ├── SearchService.php             # منطق البحث
│   └── ImageAnalysisService.php      # تحليل الصور
└── Models/
    └── Article.php                   # نموذج المقالة

config/
└── services.php                      # إعدادات الخدمات

routes/
└── api.php                           # الـ Routes

database/
└── migrations/
    └── *_create_articles_table.php   # قاعدة البيانات
```

---

## 🛠️ استكشاف الأخطاء

### Error: "API Key not configured"

**الحل:**
```bash
# تأكد من إضافة API Key في .env
echo "GOOGLE_VISION_API_KEY=your_key" >> .env

# أعد تشغيل السيرفر
php artisan serve
```

---

### Error: "Image file too large"

**الحل:**
الحد الأقصى للصورة هو **5MB**. استخدم صور أصغر حجماً.

---

### Error: "Service not responding"

**الحل:**
```bash
# تحقق من الاتصال بالإنترنت
# تأكد من صحة API Key
# راجع logs:
tail -f storage/logs/laravel.log
```

---

## 📊 معلومات الأداء

| النوع | السرعة | الاستقرار |
|------|--------|----------|
| البحث النصي | ⚡ جداً سريع | ✅ مستقر جداً |
| البحث بالصورة | 🐢 بطيء نسبياً | ⚠️ يعتمد على الـ API |
| البحث الهجين | 🚗 متوسط | ⚠️ يعتمد على الـ API |

---

## 🔄 الصيانة والتحديثات

### التحديثات الدورية:

```bash
# تحديث المكتبات
composer update

# تشغيل الـ Tests
php artisan test

# تنظيف الـ Cache
php artisan cache:clear
php artisan config:clear
```

---

## 🚀 التوسعات المستقبلية

- [ ] إضافة Caching للنتائج
- [ ] دعم البحث الجغرافي
- [ ] تحسين الترتيب بـ Machine Learning
- [ ] واجهة رسومية للبحث
- [ ] إحصائيات الاستخدام

---

## 📞 الدعم

للمساعدة:
- راجع [وثائق API](./SEARCH_API_DOCUMENTATION.md)
- شاهد [أمثلة الاختبار](./SEARCH_API_EXAMPLES.md)
- افتح Issue على GitHub

---

## ✅ تم الإعداد!

يمكنك الآن استخدام البحث الموحد. جرّب الأمثلة المختلفة وقدّم ملاحظاتك!
