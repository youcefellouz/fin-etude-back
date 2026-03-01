# Exemples API - Tech Hub Français

## Configuration API

### URL de Base
```
http://votre-api-laravel.com/api
```

### En-têtes Standard
```javascript
{
  'Content-Type': 'application/json',
  'Authorization': 'Bearer token_utilisateur',
  'Accept': 'application/json'
}
```

---

## 🔐 Authentification

### 1. Connexion
**POST** `/auth/login`

**Requête:**
```json
{
  "email": "utilisateur@exemple.fr",
  "password": "motdepasse123"
}
```

**Réponse (200):**
```json
{
  "message": "Connexion réussie",
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "user": {
    "id": 1,
    "name": "Jean Dupont",
    "email": "jean@exemple.fr",
    "phone": "0612345678",
    "role": "client"
  }
}
```

### 2. Inscription
**POST** `/auth/register`

**Requête:**
```json
{
  "name": "Marie Durand",
  "email": "marie@exemple.fr",
  "password": "motdepasse123",
  "password_confirmation": "motdepasse123",
  "phone": "0687654321"
}
```

**Réponse (201):**
```json
{
  "message": "Compte créé avec succès",
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "user": {
    "id": 2,
    "name": "Marie Durand",
    "email": "marie@exemple.fr"
  }
}
```

### 3. Oubli de mot de passe
**POST** `/auth/forgot-password`

**Requête:**
```json
{
  "email": "utilisateur@exemple.fr"
}
```

**Réponse (200):**
```json
{
  "message": "Code de réinitialisation envoyé à votre email"
}
```

---

## 📦 Produits

### 1. Récupérer tous les produits
**GET** `/articles`

**Paramètres (optionnels):**
- `page` - Numéro de page (par défaut: 1)
- `per_page` - Articles par page (par défaut: 10)
- `category_id` - Filtrer par catégorie
- `search` - Recherche par nom
- `sort` - Tri (price_asc, price_desc, name_asc, name_desc)

**Exemple:**
```
GET /articles?page=1&per_page=12&category_id=1&sort=price_asc
```

**Réponse (200):**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Processeur Intel Core i9",
      "description": "Processeur haute performance pour gaming et montage vidéo",
      "price": 499.99,
      "original_price": 599.99,
      "discount": 16,
      "category_id": 1,
      "category": {
        "id": 1,
        "name": "Processeurs"
      },
      "image": "https://via.placeholder.com/300x300",
      "rating": 4.5,
      "reviews_count": 23,
      "stock": 15,
      "in_stock": true
    }
  ],
  "pagination": {
    "current_page": 1,
    "per_page": 10,
    "total": 45,
    "last_page": 5
  }
}
```

### 2. Récupérer un produit spécifique
**GET** `/articles/:id`

**Réponse (200):**
```json
{
  "data": {
    "id": 1,
    "name": "Processeur Intel Core i9",
    "description": "Processeur haute performance...",
    "price": 499.99,
    "original_price": 599.99,
    "discount": 16,
    "category": {
      "id": 1,
      "name": "Processeurs"
    },
    "brand": "Intel",
    "specifications": {
      "cores": 12,
      "threads": 16,
      "frequency": "3.6 GHz",
      "socket": "LGA1700"
    },
    "images": [
      "https://via.placeholder.com/600x600",
      "https://via.placeholder.com/600x600"
    ],
    "rating": 4.5,
    "reviews": [
      {
        "id": 1,
        "user": "Jean Dupont",
        "rating": 5,
        "comment": "Excellent processeur!",
        "date": "2024-01-15"
      }
    ],
    "stock": 15,
    "in_stock": true
  }
}
```

---

## 🛒 Panier

### 1. Ajouter au panier
**POST** `/cart`

**Requête:**
```json
{
  "article_id": 1,
  "quantity": 2
}
```

**Réponse (201):**
```json
{
  "message": "Produit ajouté au panier",
  "cart": {
    "id": 1,
    "items": [
      {
        "id": 1,
        "article_id": 1,
        "name": "Processeur Intel Core i9",
        "price": 499.99,
        "quantity": 2,
        "total": 999.98
      }
    ],
    "subtotal": 999.98,
    "tax": 199.99,
    "total": 1199.97
  }
}
```

### 2. Récupérer le panier
**GET** `/cart`

**Réponse (200):**
```json
{
  "data": {
    "id": 1,
    "items": [
      {
        "id": 1,
        "article_id": 1,
        "name": "Processeur Intel Core i9",
        "price": 499.99,
        "quantity": 2,
        "image": "https://via.placeholder.com/100x100",
        "total": 999.98
      },
      {
        "id": 2,
        "article_id": 5,
        "name": "Carte Graphique RTX 4080",
        "price": 1199.99,
        "quantity": 1,
        "image": "https://via.placeholder.com/100x100",
        "total": 1199.99
      }
    ],
    "subtotal": 2199.97,
    "tax": 439.99,
    "total": 2639.96
  }
}
```

### 3. Modifier la quantité
**PUT** `/cart/:item_id`

**Requête:**
```json
{
  "quantity": 3
}
```

### 4. Supprimer du panier
**DELETE** `/cart/:item_id`

---

## 💳 Commandes

### 1. Créer une commande
**POST** `/orders`

**Requête:**
```json
{
  "shipping_name": "Jean Dupont",
  "shipping_email": "jean@exemple.fr",
  "shipping_phone": "0612345678",
  "shipping_address": "123 Rue de Paris",
  "shipping_city": "Paris",
  "shipping_postal_code": "75001",
  "shipping_country": "France",
  "shipping_method": "express",
  "payment_method": "credit_card",
  "cart_items": [
    {
      "article_id": 1,
      "quantity": 2
    }
  ]
}
```

**Réponse (201):**
```json
{
  "message": "Commande créée avec succès",
  "order": {
    "id": 1001,
    "order_number": "CMD-2024-001",
    "user_id": 1,
    "status": "pending",
    "items": [
      {
        "article_id": 1,
        "name": "Processeur Intel Core i9",
        "quantity": 2,
        "price": 499.99,
        "total": 999.98
      }
    ],
    "subtotal": 999.98,
    "shipping_cost": 50.00,
    "tax": 209.99,
    "total": 1259.97,
    "created_at": "2024-01-20T10:30:00Z",
    "payment_url": "https://paiement.exemple.fr/cmd-1001"
  }
}
```

### 2. Récupérer mes commandes
**GET** `/orders`

**Réponse (200):**
```json
{
  "data": [
    {
      "id": 1001,
      "order_number": "CMD-2024-001",
      "status": "livré",
      "total": 1259.97,
      "items_count": 2,
      "created_at": "2024-01-20",
      "tracking_number": "FR123456789"
    }
  ],
  "pagination": {
    "current_page": 1,
    "per_page": 10,
    "total": 5
  }
}
```

### 3. Récupérer une commande spécifique
**GET** `/orders/:id`

**Réponse (200):**
```json
{
  "data": {
    "id": 1001,
    "order_number": "CMD-2024-001",
    "status": "livré",
    "items": [
      {
        "id": 1,
        "article_id": 1,
        "name": "Processeur Intel Core i9",
        "quantity": 2,
        "price": 499.99,
        "total": 999.98
      }
    ],
    "shipping_info": {
      "name": "Jean Dupont",
      "address": "123 Rue de Paris",
      "city": "Paris",
      "postal_code": "75001",
      "country": "France"
    },
    "tracking_number": "FR123456789",
    "estimated_delivery": "2024-01-25",
    "created_at": "2024-01-20",
    "updated_at": "2024-01-22"
  }
}
```

---

## 👤 Profil Utilisateur

### 1. Récupérer le profil
**GET** `/profile`

**Réponse (200):**
```json
{
  "data": {
    "id": 1,
    "name": "Jean Dupont",
    "email": "jean@exemple.fr",
    "phone": "0612345678",
    "avatar": "https://via.placeholder.com/100x100",
    "created_at": "2023-06-15",
    "addresses": [
      {
        "id": 1,
        "type": "livraison",
        "street": "123 Rue de Paris",
        "city": "Paris",
        "postal_code": "75001",
        "is_default": true
      }
    ]
  }
}
```

### 2. Modifier le profil
**PUT** `/profile`

**Requête:**
```json
{
  "name": "Jean Dupont",
  "phone": "0612345678",
  "email": "jean.dupont@exemple.fr"
}
```

---

## 🏷️ Catégories

### Récupérer toutes les catégories
**GET** `/categories`

**Réponse (200):**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Processeurs",
      "description": "Processeurs Intel et AMD",
      "icon": "fas fa-microchip",
      "articles_count": 15
    },
    {
      "id": 2,
      "name": "Cartes Graphiques",
      "description": "Cartes vidéo haute performance",
      "icon": "fas fa-video",
      "articles_count": 12
    },
    {
      "id": 3,
      "name": "Mémoire RAM",
      "description": "DDR4 et DDR5",
      "icon": "fas fa-memory",
      "articles_count": 8
    }
  ]
}
```

---

## 🔍 Recherche

### Rechercher des produits
**GET** `/search?q=processeur`

**Réponse (200):**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Processeur Intel Core i9",
      "price": 499.99,
      "category": "Processeurs",
      "image": "https://via.placeholder.com/100x100"
    }
  ]
}
```

---

## ❌ Codes d'Erreur

| Code | Message | Description |
|------|---------|-------------|
| 400 | Bad Request | Données invalides |
| 401 | Unauthorized | Authentification requise |
| 403 | Forbidden | Accès refusé |
| 404 | Not Found | Ressource non trouvée |
| 422 | Unprocessable Entity | Validation échouée |
| 500 | Server Error | Erreur serveur |

---

## ✅ Exemple Complet - Flux d'Achat

```javascript
// 1. Connexion
POST /auth/login
{ "email": "utilisateur@exemple.fr", "password": "motdepasse" }

// 2. Récupérer les produits
GET /articles?category_id=1

// 3. Ajouter au panier
POST /cart
{ "article_id": 1, "quantity": 2 }

// 4. Créer une commande
POST /orders
{ "shipping_name": "Jean Dupont", ... }

// 5. Récupérer mes commandes
GET /orders

// 6. Tracker la commande
GET /orders/1001
```

---

**Version française - API RESTful Complète**
