# Tech Hub - Angular E-commerce Frontend

Boutique de matériel informatique construite avec Angular 21.

## Installation

```bash
npm install
```

## Lancer le projet

```bash
npm start
```

L'application sera disponible sur `http://localhost:4200`

## Structure du projet

```
src/
├── app/
│   ├── pages/          # Pages principales
│   ├── shared/         # Composants partagés
│   ├── core/           # Services et interceptors
│   └── app.routes.ts   # Routes de l'application
├── index.html          # Fichier HTML principal
├── main.ts             # Point d'entrée
└── styles.scss         # Styles globaux
```

## Configuration API

Modifiez l'URL de l'API dans `src/app/core/services/api.service.ts`:

```typescript
private apiUrl = 'http://votre-api.com/api';
```

## Pages disponibles

- Accueil
- Produits (avec recherche et filtres)
- Détails produit
- Panier
- Paiement
- Connexion
- Inscription
- Profil utilisateur
- Mes commandes
