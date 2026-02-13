# 🧪 أمثلة اختبار البحث الموحد

## معلومات عن الاختبار

هذا الملف يحتوي على أمثلة محددة لاختبار كل نوع من أنواع البحث.

---

## 1️⃣ البحث النصي فقط

### cURL

```bash
curl -X POST http://localhost:8000/api/search/unified \
  -H "Content-Type: application/json" \
  -d '{
    "query": "laptop",
    "limit": 15,
    "offset": 0
  }'
```

### JavaScript (Fetch API)

```javascript
async function textSearch() {
  const response = await fetch('http://localhost:8000/api/search/unified', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({
      query: 'laptop',
      limit: 15,
      offset: 0,
    }),
  });

  const data = await response.json();
  console.log(data);
  return data;
}

textSearch();
```

### Python

```python
import requests
import json

def text_search():
    url = 'http://localhost:8000/api/search/unified'
    payload = {
        'query': 'laptop',
        'limit': 15,
        'offset': 0,
    }
    headers = {'Content-Type': 'application/json'}
    
    response = requests.post(url, json=payload, headers=headers)
    print(json.dumps(response.json(), indent=2))
    return response.json()

text_search()
```

### Response

```json
{
  "status": "success",
  "search_type": "text",
  "count": 3,
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
      "description": "High-performance laptop with 16GB RAM",
      "price": 999.99,
      "price_after_discount": 899.99,
      "image_url": "https://example.com/laptop-1.jpg",
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

## 2️⃣ البحث بالصورة فقط

### cURL

```bash
curl -X POST http://localhost:8000/api/search/unified \
  -F "image=@./laptop.jpg" \
  -F "limit=15" \
  -F "offset=0"
```

### JavaScript (FormData)

```javascript
async function imageSearch(imageFile) {
  const formData = new FormData();
  formData.append('image', imageFile);
  formData.append('limit', 15);
  formData.append('offset', 0);

  const response = await fetch('http://localhost:8000/api/search/unified', {
    method: 'POST',
    body: formData,
  });

  const data = await response.json();
  console.log(data);
  return data;
}

// استخدام مع HTML input
document.getElementById('imageInput').addEventListener('change', (e) => {
  imageSearch(e.target.files[0]);
});
```

### Python

```python
import requests

def image_search(image_path):
    url = 'http://localhost:8000/api/search/unified'
    
    with open(image_path, 'rb') as img_file:
        files = {'image': img_file}
        data = {'limit': 15, 'offset': 0}
        
        response = requests.post(url, files=files, data=data)
        print(response.json())
        return response.json()

image_search('./laptop.jpg')
```

### Response

```json
{
  "status": "success",
  "search_type": "image",
  "count": 2,
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
      "description": "High-performance laptop with 16GB RAM",
      "price": 999.99,
      "price_after_discount": 899.99,
      "image_url": "https://example.com/laptop-1.jpg",
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

## 3️⃣ البحث الهجين (نص + صورة)

### cURL

```bash
curl -X POST http://localhost:8000/api/search/unified \
  -F "query=laptop" \
  -F "image=@./laptop.jpg" \
  -F "text_weight=0.6" \
  -F "image_weight=0.4" \
  -F "limit=15" \
  -F "offset=0"
```

### JavaScript

```javascript
async function hybridSearch(query, imageFile) {
  const formData = new FormData();
  formData.append('query', query);
  formData.append('image', imageFile);
  formData.append('text_weight', 0.6);
  formData.append('image_weight', 0.4);
  formData.append('limit', 15);
  formData.append('offset', 0);

  const response = await fetch('http://localhost:8000/api/search/unified', {
    method: 'POST',
    body: formData,
  });

  const data = await response.json();
  console.log(data);
  return data;
}

// استخدام
document.getElementById('searchBtn').addEventListener('click', async () => {
  const query = document.getElementById('queryInput').value;
  const imageFile = document.getElementById('imageInput').files[0];
  
  if (query || imageFile) {
    await hybridSearch(query, imageFile);
  }
});
```

### Python

```python
import requests

def hybrid_search(query, image_path, text_weight=0.6, image_weight=0.4):
    url = 'http://localhost:8000/api/search/unified'
    
    with open(image_path, 'rb') as img_file:
        files = {'image': img_file}
        data = {
            'query': query,
            'text_weight': text_weight,
            'image_weight': image_weight,
            'limit': 15,
            'offset': 0,
        }
        
        response = requests.post(url, files=files, data=data)
        print(response.json())
        return response.json()

hybrid_search('laptop', './laptop.jpg', text_weight=0.6, image_weight=0.4)
```

### Response

```json
{
  "status": "success",
  "search_type": "hybrid",
  "count": 4,
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
      "description": "High-performance laptop with 16GB RAM",
      "price": 999.99,
      "price_after_discount": 899.99,
      "image_url": "https://example.com/laptop-1.jpg",
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
      "hybrid_score": 0.922,
      "created_at": "2026-02-13T10:30:00Z",
      "updated_at": "2026-02-13T10:30:00Z"
    },
    {
      "id": 2,
      "name": "HP Pavilion Laptop",
      "description": "Affordable laptop for students",
      "price": 599.99,
      "price_after_discount": 599.99,
      "image_url": "https://example.com/laptop-2.jpg",
      "category": {
        "id": 1,
        "name": "Electronics"
      },
      "brand": {
        "id": 6,
        "name": "HP"
      },
      "text_score": 0.80,
      "image_score": 0.92,
      "hybrid_score": 0.844,
      "created_at": "2026-02-13T12:00:00Z",
      "updated_at": "2026-02-13T12:00:00Z"
    }
  ]
}
```

---

## 4️⃣ اختبارات متقدمة

### تجربة أوزان مختلفة

```bash
# 70% نص، 30% صورة
curl -X POST http://localhost:8000/api/search/unified \
  -F "query=laptop" \
  -F "image=@./laptop.jpg" \
  -F "text_weight=0.7" \
  -F "image_weight=0.3"

# 30% نص، 70% صورة
curl -X POST http://localhost:8000/api/search/unified \
  -F "query=laptop" \
  -F "image=@./laptop.jpg" \
  -F "text_weight=0.3" \
  -F "image_weight=0.7"
```

### اختبار Pagination

```bash
# الصفحة الأولى
curl -X POST http://localhost:8000/api/search/unified \
  -H "Content-Type: application/json" \
  -d '{
    "query": "laptop",
    "limit": 10,
    "offset": 0
  }'

# الصفحة الثانية
curl -X POST http://localhost:8000/api/search/unified \
  -H "Content-Type: application/json" \
  -d '{
    "query": "laptop",
    "limit": 10,
    "offset": 10
  }'
```

---

## 5️⃣ اختبارات الأخطاء

### بدون query و image

```bash
curl -X POST http://localhost:8000/api/search/unified \
  -H "Content-Type: application/json" \
  -d '{
    "limit": 15
  }'
```

**Response (400):**
```json
{
  "error": "يجب تقديم نص (query) أو صورة (image) على الأقل",
  "status": "failed"
}
```

### صيغة صورة غير مدعومة

```bash
curl -X POST http://localhost:8000/api/search/unified \
  -F "image=@./file.txt"
```

**Response (422):**
```json
{
  "message": "The image field must be an image.",
  "errors": {
    "image": ["The image field must be an image."]
  }
}
```

### صورة كبيرة جداً

```bash
# ملف أكبر من 5MB
curl -X POST http://localhost:8000/api/search/unified \
  -F "image=@./large-image.jpg"
```

**Response (422):**
```json
{
  "message": "The image field must not be greater than 5120 kilobytes.",
  "errors": {
    "image": ["The image field must not be greater than 5120 kilobytes."]
  }
}
```

---

## 📊 مقارنة الأداء

### البحث النصي
- **المميزات:** سريع جداً، دقيق للكلمات المحددة
- **العيوب:** قد لا يجد منتجات متشابهة إذا لم تكن الكلمات متطابقة

### البحث بالصورة
- **المميزات:** يمكن العثور على منتجات مشابهة بصرياً
- **العيوب:** يتطلب API خارجية، قد يكون بطيئاً

### البحث الهجين
- **المميزات:** دقة عالية، يجمع بين مميزات النوعين
- **العيوب:** أبطأ قليلاً من البحث النصي وحده

---

## 🔐 ملاحظات الأمان

1. **التحقق من الصور:** جميع الصور يتم التحقق من نوعها وحجمها
2. **التشفير:** استخدم HTTPS في الإنتاج
3. **المصادقة:** أضف authentication حسب احتياجاتك
4. **Rate Limiting:** يُنصح بتطبيق حد أقصى للطلبات

---

## 💾 حفظ النتائج

```javascript
// حفظ النتائج في LocalStorage
function cacheResults(key, results) {
  localStorage.setItem(key, JSON.stringify(results));
}

// استرجاع النتائج المخزنة
function getCachedResults(key) {
  const cached = localStorage.getItem(key);
  return cached ? JSON.parse(cached) : null;
}

// استخدام
const cacheKey = `search_${query}`;
let results = getCachedResults(cacheKey);

if (!results) {
  results = await textSearch(query);
  cacheResults(cacheKey, results);
}
```
