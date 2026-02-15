# 🌍 BiomeBistro - Taste the World's Ecosystems

**BiomeBistro** est une plateforme innovante de restaurants où chaque établissement est inspiré par un écosystème unique. Découvrez 8 biomes différents à travers Paris, de la forêt tropicale au récif corallien!

---

## 📋 Table des Matières

- [Fonctionnalités](#-fonctionnalités)
- [Technologies](#-technologies)
- [Prérequis](#-prérequis)
- [Installation](#-installation)
- [Utilisation](#-utilisation)
- [Structure du Projet](#-structure-du-projet)
- [Tests](#-tests)
- [Documentation API](#-documentation-api)
- [Contributions](#-contributions)

---

## ✨ Fonctionnalités

### Fonctionnalités Principales
- ✅ **8 Biomes Uniques** - Forêt tropicale, Désert, Récif corallien, Montagne alpine, Toundra arctique, Forêt tempérée, Savane africaine, Forêt de champignons
- ✅ **16 Restaurants** - 2 restaurants par biome, chacun avec sa propre personnalité
- ✅ **Système de Menus** - Plats signature, catégories, allergènes, prix
- ✅ **Système d'Avis** - Notes détaillées, commentaires, photos
- ✅ **Réservations** - Système de booking complet avec vérification de disponibilité
- ✅ **Recherche Avancée** - Filtres par biome, prix, note, disponibilité
- ✅ **Géolocalisation GPS** - Restaurants sur carte interactive avec calcul de distance
- ✅ **Bilinguisme** - Interface en français et anglais (toggle FR/EN)

### Fonctionnalités Techniques
- 🔍 Recherche full-text sur restaurants et menus
- 📍 Requêtes géospatiales MongoDB (restaurants à proximité)
- ⭐ Calcul automatique des moyennes de notes
- 📊 Statistiques et analytics
- 🎨 Design responsive et moderne
- ♻️ Scores de durabilité écologique

---

## 🛠️ Technologies

### Backend
- **PHP 7.4+** - Langage serveur
- **MongoDB** - Base de données NoSQL
- **Composer** - Gestionnaire de dépendances PHP

### Frontend
- **HTML5** - Structure
- **CSS3** - Styles et responsive design
- **JavaScript (Vanilla)** - Interactivité

### Librairies
- **mongodb/mongodb** - Driver PHP MongoDB
- **PHPUnit** - Tests unitaires

---

## 📦 Prérequis

Avant de commencer, assurez-vous d'avoir installé:

1. **PHP 7.4 ou supérieur**
   ```bash
   php --version
   ```

2. **MongoDB 4.4 ou supérieur**
   - Télécharger: https://www.mongodb.com/try/download/community
   - MongoDB doit être lancé sur `localhost:27017`

3. **Composer**
   - Télécharger: https://getcomposer.org/download/

4. **Serveur Web** (optionnel)
   - Apache, Nginx, ou serveur PHP intégré

---

## 🚀 Installation

### Étape 1: Cloner le projet

```bash
git clone https://github.com/votre-username/biomebistro.git
cd biomebistro
```

### Étape 2: Installer les dépendances

```bash
composer install
```

### Étape 3: Configurer MongoDB

1. Assurez-vous que MongoDB est lancé:
   ```bash
   # Windows
   "C:\Program Files\MongoDB\Server\[VERSION]\bin\mongod.exe"
   
   # Linux/Mac
   mongod
   ```

2. Vérifier la connexion:
   ```bash
   # Windows
   "C:\Program Files\MongoDB\Server\[VERSION]\bin\mongo.exe"
   
   # Linux/Mac
   mongo
   ```

### Étape 4: Importer les données d'exemple

```bash
php data/import_sample_data.php
```

Ceci va créer:
- 8 biomes
- 16 restaurants (2 par biome)
- Menus complets
- Avis clients
- Réservations exemples

### Étape 5: Lancer le serveur

```bash
php -S localhost:8000 -t public
```

### Étape 6: Accéder à l'application

Ouvrez votre navigateur à: **http://localhost:8000**

🎉 **BiomeBistro est maintenant opérationnel!**

---

## 💻 Utilisation

### Pages Principales

1. **Page d'accueil** (`/`)
   - Vue d'ensemble des biomes
   - Restaurants les mieux notés
   - Derniers avis
   - Carte interactive

2. **Liste des restaurants** (`/restaurants.php`)
   - Tous les restaurants avec filtres
   - Tri par note, prix, distance
   - Recherche par nom ou type de cuisine

3. **Détail restaurant** (`/restaurant-detail.php?id=...`)
   - Informations complètes
   - Menu détaillé
   - Avis clients
   - Formulaire de réservation

4. **Explorer les biomes** (`/biomes.php`)
   - Galerie des 8 biomes
   - Informations sur chaque écosystème
   - Restaurants par biome

5. **Réserver** (`/make-reservation.php?restaurant=...`)
   - Sélection date/heure
   - Vérification disponibilité
   - Confirmation instantanée

### Changer la langue

Cliquez sur le drapeau dans le header:
- 🇫🇷 **Français** (par défaut)
- 🇬🇧 **English**

### Rechercher un restaurant

Utilisez la barre de recherche en haut de chaque page:
- Par nom de restaurant
- Par type de biome
- Par type de cuisine
- Par plat du menu

---

## 📁 Structure du Projet

```
biomebistro/
│
├── config/
│   └── Database.php              # Configuration MongoDB
│
├── src/
│   ├── models/                   # Modèles de données
│   │   ├── Biome.php
│   │   ├── Restaurant.php
│   │   ├── MenuItem.php
│   │   ├── Review.php
│   │   └── Reservation.php
│   │
│   ├── controllers/              # Contrôleurs (logique métier)
│   │   ├── RestaurantController.php
│   │   ├── MenuController.php
│   │   ├── ReviewController.php
│   │   └── ReservationController.php
│   │
│   └── utils/                    # Utilitaires
│       ├── Language.php          # Système bilangue
│       ├── GeoCalculator.php     # Calculs GPS
│       └── Validator.php         # Validation données
│
├── public/                       # Fichiers publics
│   ├── index.php                 # Page d'accueil
│   ├── restaurants.php           # Liste restaurants
│   ├── restaurant-detail.php    # Détail restaurant
│   ├── biomes.php               # Explorer biomes
│   ├── make-reservation.php     # Formulaire réservation
│   ├── add-review.php           # Ajouter avis
│   │
│   ├── css/
│   │   └── style.css            # Styles globaux
│   │
│   ├── js/
│   │   └── main.js              # JavaScript
│   │
│   └── uploads/                  # Images uploadées
│       ├── restaurants/
│       ├── menu/
│       └── reviews/
│
├── tests/                        # Tests unitaires
│   ├── BiomeTest.php
│   ├── RestaurantTest.php
│   ├── MenuItemTest.php
│   ├── ReviewTest.php
│   └── ReservationTest.php
│
├── data/
│   └── import_sample_data.php   # Script d'import données
│
├── vendor/                       # Dépendances Composer
├── composer.json
├── composer.lock
├── .gitignore
└── README.md
```

---

## 🧪 Tests

### Lancer tous les tests

```bash
./vendor/bin/phpunit tests/
```

### Lancer des tests spécifiques

```bash
# Test du modèle Restaurant
./vendor/bin/phpunit tests/RestaurantTest.php

# Test du modèle Review
./vendor/bin/phpunit tests/ReviewTest.php

# Test avec détails
./vendor/bin/phpunit --testdox tests/
```

### Couverture de tests

Les tests couvrent:
- ✅ Opérations CRUD pour chaque modèle
- ✅ Validations de données
- ✅ Calculs de distance GPS
- ✅ Calculs de notes moyennes
- ✅ Vérification de disponibilité réservations
- ✅ Recherche et filtres

---

## 📚 Documentation API

### Collections MongoDB

#### 1. **biomes**
Représente les types d'écosystèmes.

```javascript
{
  _id: ObjectId,
  name: String,
  description: String,
  climate: {
    temperature_range: String,
    humidity: String,
    rainfall: String
  },
  color_theme: String,
  icon: String,
  native_ingredients: [String],
  characteristics: [String],
  season_best: String
}
```

#### 2. **restaurants**
Restaurants thématisés par biome.

```javascript
{
  _id: ObjectId,
  name: String,
  biome_id: ObjectId, // Référence à biomes
  description: String,
  location: {
    address: String,
    coordinates: {
      type: "Point",
      coordinates: [Number, Number] // [longitude, latitude]
    },
    district: String
  },
  contact: {
    phone: String,
    email: String,
    website: String
  },
  cuisine_style: String,
  price_range: String, // €, €€, €€€, €€€€
  capacity: Number,
  atmosphere: {
    music: String,
    lighting: String,
    decor: String
  },
  opening_hours: [{
    day: String,
    open: String,
    close: String,
    closed: Boolean
  }],
  features: [String],
  photos: [String],
  average_rating: Number, // 0-5
  total_reviews: Number,
  special_events: [{
    name: String,
    description: String,
    schedule: String
  }],
  sustainability_score: Number, // 0-10
  eco_certifications: [String],
  status: String, // open, temporarily_closed, permanently_closed
  created_at: Date,
  updated_at: Date
}
```

#### 3. **menu_items**
Plats du menu des restaurants.

```javascript
{
  _id: ObjectId,
  restaurant_id: ObjectId, // Référence à restaurants
  name: String,
  description: String,
  category: String, // Appetizer, Main Course, Dessert, Beverage, Special
  price: Number,
  currency: String,
  ingredients: [{
    name: String,
    origin: String
  }],
  allergens: [String],
  dietary_info: [String],
  spice_level: Number, // 0-5
  biome_authenticity: Number, // 0-10
  preparation_time: Number, // minutes
  nutritional_info: {
    calories: Number,
    protein: Number,
    carbs: Number,
    fat: Number
  },
  photo: String,
  is_signature_dish: Boolean,
  is_seasonal: Boolean,
  is_available: Boolean,
  popularity_rank: Number,
  chef_notes: String,
  pairing_suggestions: [String],
  created_at: Date
}
```

#### 4. **reviews**
Avis clients sur les restaurants.

```javascript
{
  _id: ObjectId,
  restaurant_id: ObjectId, // Référence à restaurants
  reviewer_name: String,
  reviewer_email: String,
  rating: Number, // 1-5
  ratings_breakdown: {
    food_quality: Number,
    service: Number,
    ambiance: Number,
    value_for_money: Number,
    cleanliness: Number
  },
  title: String,
  comment: String,
  visit_date: Date,
  dining_occasion: String,
  pros: [String],
  cons: [String],
  photos: [String],
  recommended_dishes: [String],
  helpful_votes: Number,
  verified_visit: Boolean,
  response: {
    from_restaurant: Boolean,
    reply: String,
    replied_at: Date
  },
  created_at: Date
}
```

#### 5. **reservations**
Réservations de tables.

```javascript
{
  _id: ObjectId,
  restaurant_id: ObjectId, // Référence à restaurants
  customer_info: {
    name: String,
    email: String,
    phone: String
  },
  reservation_date: Date,
  reservation_time: String, // HH:MM
  party_size: Number,
  table_preference: String,
  special_requests: String,
  dietary_restrictions: [String],
  occasion: String,
  status: String, // pending, confirmed, cancelled, completed, no_show
  confirmation_code: String,
  reminder_sent: Boolean,
  reminder_sent_at: Date,
  estimated_duration: Number,
  expected_departure: String,
  deposit_required: Boolean,
  deposit_amount: Number,
  special_arrangements: {
    birthday_cake: Boolean,
    gift_voucher: Boolean,
    vip_treatment: Boolean
  },
  notes_from_staff: String,
  created_at: Date,
  cancelled_at: Date,
  cancellation_reason: String,
  check_in_time: Date,
  check_out_time: Date
}
```

---

## 🎯 Fonctionnalités Avancées

### 1. Géolocalisation GPS
Utilise l'index géospatial MongoDB `2dsphere` pour:
- Trouver restaurants à proximité d'un point
- Calculer distances précises (formule Haversine)
- Afficher sur carte interactive

### 2. Système Bilangue
- Français (par défaut)
- Anglais
- Toggle simple dans le header
- Toutes les chaînes de traduction dans `Language.php`

### 3. Calcul Automatique de Notes
- Note moyenne mise à jour automatiquement
- Décomposition détaillée (nourriture, service, ambiance, etc.)
- Compteur de reviews

### 4. Recherche Full-Text
- Index MongoDB sur `name` et `description`
- Recherche instantanée
- Suggestions automatiques

### 5. Validation Robuste
- Validation côté serveur (PHP)
- Validation emails, téléphones, dates
- Sécurisation des inputs (anti-XSS)

---

## 🌟 Points Forts du Projet

### Originalité
- ✨ Concept unique: restaurants thématiques par écosystème
- 🎨 8 biomes différents avec personnalités distinctes
- 🎭 Ambiances immersives (sons, lumières, décor)

### Performance
- 🚀 Index MongoDB optimisés (géospatial, text, compound)
- 💾 Requêtes optimisées avec projections
- 📊 Calculs en cache (ratings)

### Initiative & Risque
- 📍 GPS coordinates avec calculs de distance
- 📅 Dates et timestamps MongoDB
- 🗂️ Documents imbriqués (embedded documents)
- 🔗 Relations entre collections (foreign keys)
- 🌐 Système bilangue complet

### Qualité du Code
- 📝 Commentaires exhaustifs en anglais
- 🏗️ Architecture MVC propre
- 🧪 Tests unitaires complets
- 🔒 Validation et sécurité

### Utilisation GitHub
- 📌 Commits réguliers et descriptifs
- 🌿 Branches pour features
- 📚 README détaillé
- ⚖️ Licence MIT

### UX/UI
- 🎨 Design moderne et responsive
- 🖱️ Navigation intuitive
- 📱 Mobile-friendly
- ♿ Accessible

---

## 🤝 Contributions

Ce projet est un projet académique. Pour toute question ou suggestion:

**Email:** votre-email@example.com  
**GitHub:** https://github.com/votre-username

---

## 📄 Licence

MIT License - Voir LICENSE file pour plus de détails

---

## 👨‍💻 Auteur

**Votre Nom**  
Université Catholique de l'Ouest  
Projet Final - PHP & MongoDB  
Février 2025

---

## 🎉 Remerciements

- Professeur J. Vercoutere pour l'encadrement
- MongoDB pour l'excellente documentation
- La communauté PHP pour les ressources

---

**© 2025 BiomeBistro - Tous droits réservés**

*Goûtez les Écosystèmes du Monde 🌍*
