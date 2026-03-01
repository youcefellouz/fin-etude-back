# ⚡ Commandes Principales - Tech Hub

## 🚀 Démarrage Rapide (3 commandes)

```bash
# 1. Installer les dépendances
npm install

# 2. Lancer le serveur
npm start

# 3. Ouvrir http://localhost:4200
```

---

## 📦 Installation et Configuration

### Installer les dépendances
```bash
npm install
```

### Alternative: Yarn
```bash
yarn install
```

### Alternative: PNPM
```bash
pnpm install
```

---

## 🔧 Développement

### Lancer le serveur
```bash
npm start
```

### Lancer avec un autre port
```bash
ng serve --port 4201
```

### Lancer avec l'observateur
```bash
ng serve --watch
```

### Lancer pour production (local)
```bash
ng serve --configuration=production
```

---

## 🏗️ Build

### Build pour développement
```bash
ng build
```

### Build pour production
```bash
ng build --configuration production
```

### Ou directement
```bash
npm run build
```

### Build et servir
```bash
ng build && ng serve --prod
```

---

## 🧪 Tests

### Lancer les tests unitaires
```bash
ng test
```

### Lancer les tests avec code coverage
```bash
ng test --code-coverage
```

### Lancer les tests en CI
```bash
ng test --watch=false --browsers=ChromeHeadless
```

### Linter le code
```bash
ng lint
```

---

## 📝 Génération de Code

### Générer un composant
```bash
ng generate component nom
# Ou raccourci
ng g c nom
```

### Générer un service
```bash
ng generate service mon-service
ng g s mon-service
```

### Générer un module
```bash
ng generate module mon-module
ng g m mon-module
```

### Générer un directive
```bash
ng generate directive ma-directive
ng g d ma-directive
```

### Générer un pipe
```bash
ng generate pipe mon-pipe
ng g p mon-pipe
```

---

## 🗂️ Gestion des Fichiers

### Afficher la structure
```bash
# Linux/Mac
tree src/

# Windows PowerShell
Get-ChildItem src/ -Recurse
```

### Compter les lignes de code
```bash
# Linux/Mac
find src -name '*.ts' | xargs wc -l

# Avec SCSS aussi
find src -name '*.ts' -o -name '*.scss' | xargs wc -l
```

---

## 🔍 Débogage

### Ouvrir la console du navigateur
```
F12 (Windows/Linux)
Cmd+Option+I (Mac)
```

### Afficher les logs
```typescript
console.log('message', variable);
console.warn('attention', variable);
console.error('erreur', variable);
```

### Déboguer avec VS Code
```bash
# Lancer le serveur
ng serve

# Puis F5 dans VS Code pour attacher le débogueur
```

---

## 📦 Gestion des Dépendances

### Installer une nouvelle dépendance
```bash
npm install nom-package
ng add @angular/schematics
```

### Mettre à jour les dépendances
```bash
npm update
npm update --save
```

### Afficher les dépendances installées
```bash
npm list
```

### Afficher les dépendances obsolètes
```bash
npm outdated
```

### Supprimer une dépendance
```bash
npm uninstall nom-package
```

---

## 🔐 Sécurité

### Vérifier les vulnérabilités
```bash
npm audit
```

### Corriger les vulnérabilités automatiquement
```bash
npm audit fix
```

### Corriger manuellement
```bash
npm audit fix --force
```

---

## 📊 Analyse du Code

### Vérifier le code avec lint
```bash
ng lint
```

### Vérifier la taille du bundle
```bash
ng build --stats-json
npx webpack-bundle-analyzer dist/*/stats.json
```

---

## 🚀 Déploiement

### Build pour production
```bash
ng build --configuration production
```

### Servir les fichiers de production
```bash
# Avec http-server (installer d'abord)
npm install -g http-server
http-server dist/
```

### Déployer sur Vercel
```bash
npm i -g vercel
vercel
```

### Déployer sur Netlify
```bash
npm i -g netlify-cli
netlify deploy --prod --dir=dist/
```

---

## 🧹 Nettoyage

### Supprimer le cache
```bash
rm -rf .angular/cache
rm -rf node_modules
npm install
```

### Supprimer le dossier dist
```bash
rm -rf dist/
```

### Nettoyer complètement
```bash
rm -rf node_modules dist/ .angular/
npm install
ng build
```

---

## 📱 Testing sur Mobile

### Trouver l'adresse locale
```bash
ipconfig getifaddr en0  # Mac
ipconfig              # Windows
hostname -I           # Linux
```

### Accéder depuis le mobile
```
http://[votre-ip]:4200
```

### Tester le responsive dans le navigateur
```
F12 → Ctrl+Shift+M (ou Cmd+Shift+M sur Mac)
```

---

## 🔄 Git et Versionning

### Initialiser un repo git
```bash
git init
git add .
git commit -m "Initial commit"
```

### Cloner un repo
```bash
git clone https://github.com/user/repo.git
```

### Commits
```bash
git add .
git commit -m "description"
git push
```

---

## 📚 Documentation

### Générer la documentation
```bash
npm install -g compodoc
compodoc -p tsconfig.json -d docs
```

### Servir la documentation
```bash
compodoc -s
```

---

## 🆘 Troubleshooting

### Réinstaller complètement
```bash
rm -rf node_modules package-lock.json
npm install
```

### Vider le cache npm
```bash
npm cache clean --force
```

### Vérifier les versions
```bash
node --version
npm --version
ng version
```

### Mettre à jour Angular
```bash
ng update @angular/cli @angular/core
```

---

## 🎯 Commandes Utiles Courantes

```bash
# Démarrage
npm start                              # Lancer l'appli

# Build
ng build                               # Build dev
ng build --prod                        # Build prod

# Testing
npm test                               # Tests unitaires
ng lint                                # Vérifier le code

# Génération
ng generate component nom              # Nouveau composant
ng g s mon-service                    # Nouveau service

# Maintenance
npm update                             # Mettre à jour
npm audit fix                          # Fixer les vulnérabilités
rm -rf node_modules && npm install    # Réinstaller tout
```

---

## 📋 Alias Utiles (à ajouter dans `.bashrc` ou `.zshrc`)

```bash
# Ajouter ces lignes à ~/.bashrc ou ~/.zshrc

alias nstart="npm start"
alias ngc="ng generate component"
alias ngs="ng generate service"
alias ngb="ng build --configuration production"
alias nglint="ng lint"
alias ngtest="ng test"
alias ngdoc="compodoc -p tsconfig.json -s"
```

Puis recharger:
```bash
source ~/.bashrc  # ou source ~/.zshrc
```

---

## 🚀 Script de Démarrage Complet

```bash
#!/bin/bash
echo "🚀 Démarrage de Tech Hub..."
echo ""
echo "1️⃣ Vérification de Node.js..."
node --version
npm --version
echo ""
echo "2️⃣ Installation des dépendances..."
npm install
echo ""
echo "3️⃣ Lancement du serveur..."
npm start
echo ""
echo "✅ L'application est accessible à http://localhost:4200"
```

Sauvegarder dans `start.sh` et lancer:
```bash
chmod +x start.sh
./start.sh
```

---

## 🎓 Références Utiles

| Commande | Description |
|----------|------------|
| `npm install` | Installer les dépendances |
| `npm start` | Lancer le serveur |
| `ng build --prod` | Build pour production |
| `ng test` | Lancer les tests |
| `ng lint` | Vérifier le code |
| `ng g c nom` | Créer un composant |
| `ng g s nom` | Créer un service |
| `npm audit fix` | Fixer les vulnérabilités |
| `ng update` | Mettre à jour Angular |
| `npm uninstall pkg` | Supprimer un package |

---

## 💾 Sauvegarde et Restauration

### Sauvegarder le projet
```bash
tar -czf tech-hub-backup.tar.gz .
# ou
zip -r tech-hub-backup.zip .
```

### Restaurer le projet
```bash
tar -xzf tech-hub-backup.tar.gz
# ou
unzip tech-hub-backup.zip
```

---

## ✅ Checklist Avant Déploiement

```bash
# 1. Vérifier les versions
ng version

# 2. Lancer les tests
npm test

# 3. Lancer le linter
ng lint

# 4. Construire pour production
ng build --prod

# 5. Vérifier les fichiers générés
ls -la dist/

# 6. Tester la version produit localement
npx http-server dist/
```

---

## 📞 Aide et Support

### Afficher l'aide d'Angular
```bash
ng help
```

### Afficher l'aide d'une commande
```bash
ng serve --help
ng build --help
```

### Vérifier la documentation
```bash
npm docs
```

---

**Gardez cette page pour une référence rapide!**

Version française - Tech Hub 2024
