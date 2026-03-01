#!/usr/bin/env node

const fs = require('fs');
const path = require('path');

// Dictionnaire de traduction complet
const translationDict = {
  // Navigation
  'الرئيسية': 'Accueil',
  'المنتجات': 'Produits',
  'السلة': 'Panier',
  'حسابي': 'Mon Compte',
  'الملف الشخصي': 'Profil',
  'طلباتي': 'Mes Commandes',
  'تسجيل الخروج': 'Déconnexion',
  'دخول': 'Connexion',
  'تسجيل': 'S\'inscrire',

  // Formulaires
  'البريد الإلكتروني': 'Email',
  'كلمة المرور': 'Mot de passe',
  'أدخل': 'Entrez',
  'الرجاء ملء جميع الحقول': 'Veuillez remplir tous les champs',
  'البحث': 'Recherche',
  'ابحث عن منتج': 'Rechercher un produit',
  'تأكيد كلمة المرور': 'Confirmer le mot de passe',

  // المنتجات
  'المنتجات المميزة': 'Produits Vedettes',
  'الفئات': 'Catégories',
  'السعر': 'Prix',
  'الترتيب': 'Trier par',
  'تصفية المنتجات': 'Filtrer les produits',
  'عرض التفاصيل': 'Voir les détails',
  'إعادة تعيين': 'Réinitialiser',
  'الافتراضي': 'Par défaut',
  'خصم': 'Réduction',

  // Textes de la page d'accueil
  'متجر معدات الحاسوب الأفضل': 'Votre Boutique de Matériel Informatique',
  'أجود المعدات بأفضل الأسعار من أشهر الماركات العالمية': 'Composants de qualité aux meilleurs prix des plus grandes marques mondiales',
  'ابدأ التسوق': 'Commencer les achats',
  'أفضل المنتجات المختارة بعناية': 'Nos meilleures sélections de produits',
  'استكشف المنتجات': 'Découvrez les produits',
  'شحن سريع': 'Livraison Rapide',
  'توصيل لجميع أنحاء البلاد في 48 ساعة': 'Livraison dans toute la France en 48h',
  'منتجات أصلية': 'Produits Authentiques',
  '100% ضمان على جميع المنتجات': 'Garantie 100% sur tous les produits',
  'استرجاع آمن': 'Retours Sécurisés',
  'سياسة إرجاع سهلة وآمنة': 'Politique de retour simple et sécurisée',
  'دعم العملاء': 'Support Client',
  'خدمة عملاء 24/7 للمساعدة': 'Service client 24/7 pour vous aider',

  // Monnaie
  ' ر.س': ' €',
  ' ر.ع.': ' €',

  // Ordres et Paiement
  'الطلبات': 'Commandes',
  'الدفع': 'Paiement',
  'الشحن': 'Livraison',
  'العنوان': 'Adresse',
  'المدينة': 'Ville',
  'الرمز البريدي': 'Code Postal',
  'الدولة': 'Pays',
  'إجمالي': 'Total',
  'الضريبة': 'Taxes',

  // Rédaction et messages
  'جاري التحميل': 'Chargement',
  'حدث خطأ': 'Une erreur s\'est produite',
  'نجاح': 'Succès',
  'جاري': 'En cours',

  // Authentification
  'تسجيل الدخول': 'Connexion',
  'ليس لديك حساب؟': 'Pas encore de compte?',
  'إنشاء حساب جديد': 'Créer un compte',
  'الاسم الأول': 'Prénom',
  'اسم العائلة': 'Nom',
  'رقم الهاتف': 'Numéro de téléphone',
  'هل نسيت كلمة المرور؟': 'Mot de passe oublié?',
  'إرسال كود التحقق': 'Envoyer le code',
  'العودة إلى التسجيل': 'Retour à la connexion',

  // Messages d'erreur spécifiques
  'حدث خطأ في التسجيل': 'Erreur lors de la connexion',
  'تم إرسال كود التحقق إلى بريدك الإلكتروني': 'Code de réinitialisation envoyé à votre email',

  // Breadcrumb
  'المنتجات': 'Produits',

  // Autres
  'الاسم': 'Nom',
  'البيانات': 'Données',
  'تفاصيل': 'Détails',
};

// Récupérer tous les fichiers
function getAllFiles(dir, ext) {
  let results = [];
  const files = fs.readdirSync(dir);
  
  files.forEach(file => {
    const filePath = path.join(dir, file);
    const stat = fs.statSync(filePath);
    
    if (stat.isDirectory()) {
      results = results.concat(getAllFiles(filePath, ext));
    } else if (file.endsWith(ext)) {
      results.push(filePath);
    }
  });
  
  return results;
}

// Traduire le contenu du fichier
function translateContent(content) {
  let result = content;
  
  Object.entries(translationDict).forEach(([ar, fr]) => {
    // Remplacer dans les chaînes de caractères avec guillemets simples
    result = result.replace(new RegExp(`'${ar}'`, 'g'), `'${fr}'`);
    // Remplacer dans les chaînes de caractères avec guillemets doubles
    result = result.replace(new RegExp(`"${ar}"`, 'g'), `"${fr}"`);
    // Remplacer dans les templates HTML (texte brut)
    result = result.replace(new RegExp(`>${ar}<`, 'g'), `>${fr}<`);
    // Remplacer dans les templates (balises avec texte)
    result = result.replace(new RegExp(`>${ar} `, 'g'), `>${fr} `);
  });
  
  return result;
}

// Fichiers à traduire
const srcDir = '/vercel/share/v0-project/src';
const tsFiles = getAllFiles(srcDir, '.ts');
const scssFiles = getAllFiles(srcDir, '.scss');

console.log('🌍 Début de la traduction vers le français...\n');

let translatedCount = 0;

// Traduire les fichiers TypeScript
tsFiles.forEach(filePath => {
  try {
    let content = fs.readFileSync(filePath, 'utf8');
    const originalLength = content.length;
    content = translateContent(content);
    
    if (content.length !== originalLength) {
      fs.writeFileSync(filePath, content, 'utf8');
      console.log(`✅ Traduit: ${path.relative(srcDir, filePath)}`);
      translatedCount++;
    }
  } catch (error) {
    console.error(`❌ Erreur pour ${filePath}:`, error.message);
  }
});

// Traduire les fichiers SCSS (commentaires)
scssFiles.forEach(filePath => {
  try {
    let content = fs.readFileSync(filePath, 'utf8');
    const originalLength = content.length;
    content = translateContent(content);
    
    if (content.length !== originalLength) {
      fs.writeFileSync(filePath, content, 'utf8');
      console.log(`✅ Traduit: ${path.relative(srcDir, filePath)}`);
      translatedCount++;
    }
  } catch (error) {
    console.error(`❌ Erreur pour ${filePath}:`, error.message);
  }
});

console.log(`\n✨ Traduction complétée! ${translatedCount} fichiers traduits.`);
console.log('🎉 L\'application est maintenant en français!');
