# Guide Complet - Tech Hub E-commerce (Français)

## 🎯 Vue d'ensemble du projet

Tech Hub est un **site e-commerce professionnel** pour vendre du matériel informatique. L'application est construite avec **Angular 21** et communique avec une API **Laravel**.

### ✅ Langues et Régions
- **Langue**: Français (FR)
- **Direction**: Gauche à droite (LTR)
- **Devise**: Euro (€)
- **Région**: France

---

## 📂 Structure du Projet

```
project-root/
├── src/
│   ├── app/
│   │   ├── core/              # Services et intercepteurs
│   │   │   ├── services/
│   │   │   │   ├── api.service.ts
│   │   │   │   ├── auth.service.ts
│   │   │   │   └── cart.service.ts
│   │   │   └── interceptors/
│   │   │       └── auth.interceptor.ts
│   │   ├── pages/             # Pages/Composants principaux
│   │   │   ├── home/
│   │   │   ├── products/
│   │   │   ├── product-detail/
│   │   │   ├── auth/
│   │   │   │   ├── login/
│   │   │   │   └── register/
│   │   │   ├── cart/
│   │   │   ├── checkout/
│   │   │   ├── profile/
│   │   │   └── orders/
│   │   ├── shared/            # Composants partagés
│   │   │   └── layout/
│   │   │       ├── header/
│   │   │       └── footer/
│   │   ├── app.component.ts
│   │   └── app.routes.ts
│   ├── main.ts
│   ├── index.html
│   └── styles.scss
├── package.json
├── angular.json
└── tsconfig.json
```

---

## 🚀 Démarrage Rapide

### 1. Installation

```bash
# Installer les dépendances
npm install

# Ou avec yarn
yarn install
```

### 2. Lancer le serveur de développement

```bash
# Démarrer Angular
npm start

# Ou utiliser ng directly
ng serve
```

L'application sera accessible à: **http://localhost:4200**

### 3. Configuration de l'API

Ouvrez le fichier `src/app/core/services/api.service.ts` et changez l'URL de l'API:

```typescript
private apiUrl = 'http://localhost:8000/api'; // Changez l'URL de votre API Laravel
```

---

## 🏠 Pages Principales

### 1. **Accueil** (`/`)
- Affiche les produits vedettes
- Affiche les catégories
- Affiche les avantages de la boutique
- Bouton "Commencer les achats"

### 2. **Produits** (`/products`)
- Liste complète des produits
- Filtrage par catégorie
- Filtre par prix (min/max)
- Recherche par mots-clés
- Tri (prix, nom)
- Pagination

### 3. **Détails Produit** (`/products/:id`)
- Images du produit
- Description complète
- Avis et évaluations
- Sélection de quantité
- Bouton "Ajouter au panier"

### 4. **Panier** (`/cart`)
- Liste des articles du panier
- Modification des quantités
- Suppression d'articles
- Résumé de la commande
- Calcul des taxes
- Bouton "Procéder au paiement"

### 5. **Paiement** (`/checkout`)
- Formulaire de livraison
- Adresse et contact
- Choix du mode de livraison
- Choix du mode de paiement
- Résumé final de la commande

### 6. **Connexion** (`/login`)
- Formulaire de connexion
- Récupération de mot de passe
- Lien vers inscription

### 7. **Inscription** (`/register`)
- Formulaire d'inscription
- Validation des mots de passe
- Accord aux conditions d'utilisation

### 8. **Profil** (`/profile`)
- Affichage des informations personnelles
- Modification du profil
- Adresses de livraison
- Historique de commandes

### 9. **Commandes** (`/orders`)
- Liste des commandes passées
- Statut de chaque commande
- Détails des articles commandés
- Suivi de livraison

---

## 🔧 Services Principaux

### ApiService
Gère toutes les communications avec l'API:
- `getAllArticles()` - Récupère tous les produits
- `getArticle(id)` - Récupère un produit spécifique
- `getCategories()` - Récupère les catégories
- `searchArticles(term)` - Cherche des produits
- `createOrder(data)` - Crée une commande
- `getOrders()` - Récupère les commandes de l'utilisateur

### AuthService
Gère l'authentification:
- `login(email, password)` - Connexion utilisateur
- `register(data)` - Inscription utilisateur
- `logout()` - Déconnexion
- `forgotPassword(email)` - Récupération de mot de passe
- `getCurrentUser()` - Récupère l'utilisateur actuel
- `isAuthenticated()` - Vérifie si l'utilisateur est connecté

### CartService
Gère le panier d'achat:
- `addToCart(product)` - Ajoute un produit au panier
- `removeFromCart(productId)` - Supprime un produit
- `updateQuantity(productId, qty)` - Modifie la quantité
- `getCartItems()` - Récupère les articles
- `getCartTotal()` - Calcule le total
- `clearCart()` - Vide le panier

---

## 🔐 Fonctionnalités de Sécurité

### Intercepteur d'Authentification
- Ajoute automatiquement le token JWT aux en-têtes
- Gère les réponses d'erreur 401
- Redirige vers la connexion si nécessaire

### RLS (Row Level Security)
- Chaque utilisateur ne voit que ses propres données
- Les commandes sont liées à l'utilisateur

---

## 🎨 Personnalisation du Design

### Couleurs principales
```scss
$primary: #0066cc;      // Bleu principal
$secondary: #6c757d;    // Gris
$success: #28a745;      // Vert
$danger: #dc3545;       // Rouge
$light: #f8f9fa;        // Très clair
$dark: #343a40;         // Très foncé
```

### Modification du style global
Éditez `src/styles.scss` pour changer:
- Les couleurs
- Les polices
- Les espacements
- Les animations

---

## 📱 Responsive Design

L'application est **entièrement responsive** et fonctionne sur:
- ✅ Desktop (1200px+)
- ✅ Tablette (768px - 1200px)
- ✅ Mobile (< 768px)

---

## 🌐 Intégration API

### Configuration de l'API

Fichier: `src/app/core/services/api.service.ts`

```typescript
private apiUrl = 'http://votre-api-laravel.com/api';
```

### Format des requêtes

Toutes les requêtes utilisent Axios avec authentification:

```typescript
// Exemple de requête
this.apiService.get('/products').subscribe({
  next: (data) => console.log(data),
  error: (err) => console.error(err)
});
```

---

## 📦 Composants Clés

### Header
- Logo et marque
- Menu de navigation
- Recherche
- Panier (avec badge du nombre d'articles)
- Menu utilisateur

### Footer
- Informations de contact
- Liens rapides
- Politique de confidentialité
- Droits d'auteur

### ProductCard
- Image du produit
- Prix et réduction
- Notation
- Bouton "Voir les détails"

---

## 🛒 Flux d'Achat Complet

```
1. Accueil → 2. Parcourir les produits
     ↓
3. Voir les détails → 4. Ajouter au panier
     ↓
5. Aller au panier → 6. Vérifier la commande
     ↓
7. Procéder au paiement → 8. Remplir l'adresse
     ↓
9. Choisir la livraison → 10. Choisir le paiement
     ↓
11. Confirmer la commande → 12. Redirection vers succès
     ↓
13. Consulter les commandes dans le profil
```

---

## ⚙️ Variables d'Environnement

Créez un fichier `environment.ts` pour les configurations:

```typescript
export const environment = {
  production: false,
  apiUrl: 'http://localhost:8000/api',
  appName: 'Tech Hub'
};
```

---

## 🚨 Gestion des Erreurs

L'application gère automatiquement:
- ✅ Erreurs de connexion réseau
- ✅ Erreurs d'authentification (401, 403)
- ✅ Erreurs de validation (400)
- ✅ Erreurs serveur (500)
- ✅ Affichage de messages d'erreur clairs

---

## 📊 Traduction et Localisation

### Textes en Français
Tous les textes visibles sont en français. Pour ajouter une nouvelle langue:

1. Créez un fichier `i18n/fr.json`
2. Installez `@ngx-translate/core`
3. Mettez à jour le service de traduction

---

## 🔄 Mise à Jour de l'API

Si votre API change:

1. Ouvrez `src/app/core/services/api.service.ts`
2. Mettez à jour les chemins des endpoints
3. Testez les appels API dans le navigateur (DevTools → Network)

---

## 📝 Notes Importantes

- ⚠️ **Toujours** utiliser des tokens JWT sécurisés
- ⚠️ **Ne jamais** stocker les mots de passe en dur
- ⚠️ **Valider** toujours les données côté serveur
- ✅ **Utiliser** HTTPS en production
- ✅ **Implémenter** CORS correctement

---

## 🆘 Dépannage

### Le serveur ne démarre pas
```bash
# Vérifier que le port 4200 est libre
lsof -i :4200

# Sinon, utiliser un autre port
ng serve --port 4201
```

### Problèmes de CORS
- Vérifiez que votre API Laravel accepte les requêtes cross-origin
- Configurez correctement les en-têtes CORS

### Authentification qui ne marche pas
- Vérifiez que le token est stocké correctement
- Vérifiez que l'intercepteur ajoute le token aux requêtes
- Vérifiez les logs du navigateur (DevTools → Console)

---

## 🎓 Ressources Utiles

- [Documentation Angular](https://angular.io/docs)
- [Bootstrap 5](https://getbootstrap.com/)
- [API RESTful Concepts](https://restfulapi.net/)
- [JWT Authentication](https://jwt.io/)

---

**Créé avec ❤️ pour Tech Hub**
Version française - Angular 21
