# 📖 Dictionnaire de Traduction - Arabe vers Français

## Vue d'ensemble
Ce document contient tous les remplacements de texte nécessaires pour convertir l'application de l'arabe au français.

## Termes Courants

### Navigation
- `الرئيسية` → `Accueil`
- `المنتجات` → `Produits`
- `السلة` → `Panier`
- `حسابي` → `Mon Compte`
- `الملف الشخصي` → `Profil`
- `طلباتي` → `Mes Commandes`
- `تسجيل الخروج` → `Déconnexion`
- `دخول` → `Connexion`
- `تسجيل` → `S'inscrire`

### Formulaires et Validation
- `البريد الإلكتروني` → `Email`
- `كلمة المرور` → `Mot de passe`
- `أدخل` → `Entrez`
- `الرجاء ملء جميع الحقول` → `Veuillez remplir tous les champs`
- `مطلوب` → `Requis`
- `تأكيد كلمة المرور` → `Confirmer le mot de passe`

### المنتجات
- `المنتجات المميزة` → `Produits Vedettes`
- `الفئات` → `Catégories`
- `السعر` → `Prix`
- `الترتيب` → `Trier par`
- `البحث` → `Recherche`
- `تصفية المنتجات` → `Filtrer les produits`
- `عرض التفاصيل` → `Voir les détails`
- `إعادة تعيين` → `Réinitialiser`

### الطلبات والدفع
- `الطلبات` → `Commandes`
- `الدفع` → `Paiement`
- `الشحن` → `Livraison`
- `العنوان` → `Adresse`
- `المدينة` → `Ville`
- `الرمز البريدي` → `Code Postal`
- `الدولة` → `Pays`
- `إجمالي` → `Total`
- `الضريبة` → `Taxes`

### الرسائل
- `تحميل...` → `Chargement...`
- `جاري` → `En cours`
- `خطأ` → `Erreur`
- `نجاح` → `Succès`
- `تم` → `Complété`
- `جاري...` → `En attente...`

### العملات
- `ر.س` → `€` (Euro)
- `ر.ع.` → `€`

## Listes de Réplacement Complètes

### Textes du Header (En-tête)
```typescript
// AVANT
دخول → APRÈS
Connexion

// AVANT
تسجيل → APRÈS
S'inscrire

// AVANT
السلة → APRÈS
Panier

// AVANT
حسابي → APRÈS
Mon Compte

// AVANT
الملف الشخصي → APRÈS
Profil

// AVANT
طلباتي → APRÈS
Mes Commandes

// AVANT
تسجيل الخروج → APRÈS
Déconnexion
```

### Textes du Footer (Pied de Page)
```
جميع الحقوق محفوظة → Tous les droits réservés
البريد الإلكتروني → Email
الهاتف → Téléphone
العنوان → Adresse
تابعنا → Nous suivre
روابط مفيدة → Liens Utiles
عن المتجر → À propos
سياسة الخصوصية → Politique de Confidentialité
شروط الخدمة → Conditions d'Utilisation
```

### Messages d'Erreur
```
حدث خطأ → Une erreur s'est produite
فشل التحميل → Impossible de charger
عذرا، حاول لاحقا → Désolé, réessayez plus tard
هذا الحقل مطلوب → Ce champ est obligatoire
بريد إلكتروني غير صحيح → Email invalide
كلمات المرور غير متطابقة → Les mots de passe ne correspondent pas
```

### Pages Spécifiques

#### Home Page
- `متجر معدات الحاسوب الأفضل` → `Votre Boutique de Matériel Informatique`
- `أجود المعدات بأفضل الأسعار` → `Composants de qualité aux meilleurs prix`
- `ابدأ التسوق` → `Commencer les achats`
- `أفضل المنتجات المختارة` → `Nos meilleures sélections`
- `شحن سريع` → `Livraison Rapide`
- `منتجات أصلية` → `Produits Authentiques`
- `استرجاع آمن` → `Retours Sécurisés`
- `دعم العملاء` → `Support Client`

#### Products Page
- `تصفية المنتجات` → `Filtrer les produits`
- `البحث` → `Recherche`
- `الفئات` → `Catégories`
- `السعر` → `Prix`
- `الترتيب` → `Trier par`
- `الافتراضي` → `Par défaut`
- `السعر (من الأقل إلى الأعلى)` → `Prix (croissant)`
- `السعر (من الأعلى إلى الأقل)` → `Prix (décroissant)`

#### Cart & Checkout
- `سلة التسوق` → `Panier`
- `المنتج` → `Produit`
- `الكمية` → `Quantité`
- `السعر` → `Prix`
- `إجمالي` → `Total`
- `شحن` → `Livraison`
- `ضريبة` → `Taxes`
- `الإجمالي النهائي` → `Total Final`
- `متابعة الشراء` → `Continuer les achats`
- `إتمام الطلب` → `Finaliser la commande`

#### Login/Register
- `تسجيل الدخول` → `Connexion`
- `ليس لديك حساب؟` → `Pas encore de compte?`
- `إنشاء حساب جديد` → `Créer un compte`
- `البريد الإلكتروني` → `Email`
- `كلمة المرور` → `Mot de passe`
- `تأكيد كلمة المرور` → `Confirmer le mot de passe`
- `الاسم الأول` → `Prénom`
- `اسم العائلة` → `Nom`
- `رقم الهاتف` → `Numéro de téléphone`

## Monnaie
- Remplacez tous les `ر.س` par `€`
- Remplacez tous les `ر.ع.` par `€`

## Instructions d'Application

1. **Utiliser Find & Replace** dans votre IDE (VS Code, WebStorm, etc.)
2. **Ignorer le contexte RTL** - Le code est déjà configuré en LTR
3. **Tester chaque composant** après la traduction
4. **Mettre à jour les tests** avec les nouvelles traductions

## État de la Conversion

- ✅ index.html - Complété
- ✅ app.component.ts - Complété
- ✅ home.component.ts - Complété
- ✅ header.component.ts - Complété
- ✅ login.component.ts - Complété
- ✅ products.component.ts - Complété
- ⏳ register.component.ts - À faire
- ⏳ cart.component.ts - À faire
- ⏳ checkout.component.ts - À faire
- ⏳ product-detail.component.ts - À faire
- ⏳ profile.component.ts - À faire
- ⏳ orders.component.ts - À faire
- ⏳ footer.component.ts - À faire
- ⏳ Fichiers SCSS (commentaires) - À faire
