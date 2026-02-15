# 🌍 BiomeBistro - Plateforme de Restaurants Thématiques

**BiomeBistro** est une plateforme de restaurants où chaque établissement est inspiré par un écosystème unique. Découvrez 8 biomes différents à travers Paris, de la forêt tropicale au récif corallien.

---

## 📋 Contexte du Projet

Projet final pour le cours NoSQL à l'UCO.

**Objectif :** Créer une application complète utilisant MongoDB avec :
- 5 collections NoSQL (biomes, restaurants, menu_items, reviews, reservations)
- CRUD complet (Create, Read, Update, Delete)
- Tests unitaires
  

**Données :**
- 8 biomes (Forêt tropicale, Désert, Récif corallien, Montagne alpine, Toundra arctique, Forêt tempérée, Savane africaine, Forêt de champignons)
- 16 restaurants (2 par biome)
- 192 plats au menu
- Système de réservations et d'avis clients

---

## 🚀 Installation

### Prérequis

- **PHP 8.0+** ([Télécharger](https://www.php.net/downloads))
- **MongoDB 4.4+** ([Télécharger](https://www.mongodb.com/try/download/community))
- **Composer** ([Télécharger](https://getcomposer.org/download/))

### Étapes d'installation

**1. Cloner le projet**
```bash
git clone https://github.com/charlotte-manzi/Biomebistro.git
cd Biomebistro
```

**2. Installer les dépendances**
```bash
composer install
```

**3. Vérifier que MongoDB est lancé**

MongoDB doit tourner sur `mongodb://localhost:27017`
```bash
# Vérifier avec mongo shell
mongosh
```

**4. Importer les données d'exemple**
```bash
php data/import_sample_data.php
php data/add_content.php
```

Ceci crée :
- 8 biomes
- 16 restaurants
- 192 plats
- Avis et réservations d'exemple

**5. Lancer le serveur**
```bash
php -S localhost:8000 -t public
```

**6. Ouvrir dans le navigateur**
```
http://localhost:8000
```

✅ **L'application est prête !**

---

## 🧪 Lancer les Tests

**Exécuter tous les tests unitaires :**
```bash
vendor/bin/phpunit
```

**Résultat attendu :**
```
OK (27 tests, 96 assertions)
```

**Tests couverts :**
- Opérations CRUD sur toutes les collections
- Validation des données
- Recherche et filtres
- Calculs (notes moyennes, distances GPS)

---

## 📁 Structure des Collections MongoDB

### 1. **biomes** (8 documents)
Écosystèmes avec climat, ingrédients natifs, caractéristiques

### 2. **restaurants** (16 documents)
Restaurants avec coordonnées GPS, horaires, ambiance, score de durabilité

### 3. **menu_items** (192 documents)
Plats avec prix, allergènes, informations nutritionnelles

### 4. **reviews** (64 documents)
Avis clients avec notes détaillées (nourriture, service, ambiance)

### 5. **reservations** (dynamique)
Réservations avec statut (pending, confirmed, cancelled)

---

## ✨ Fonctionnalités Principales

- ✅ **Découvrir des restaurants uniques** : 16 restaurants thématiques organisés par écosystème
- ✅ **Consulter les menus** : 192 plats avec descriptions, prix et allergènes
- ✅ **Réserver une table** : Système de réservation en ligne avec gestion des disponibilités
- ✅ **Laisser des avis** : Système de notation et commentaires clients
- ✅ **Recherche et filtres** : Par biome, prix, note
- ✅ **CRUD complet** : Créer, modifier, supprimer réservations et avis
- ✅ **Tests unitaires** : 27 tests, 100% passing
- ✅ **Design moderne** : Interface responsive avec images réelles

---
---

## 📌 Notes Importantes

### Système de démonstration
Pour ce projet académique, l'email `demo@example.com` est utilisé comme identifiant de démonstration pour tester les fonctionnalités de réservations et d'avis. 

Dans une application en production, un système complet d'authentification utilisateur serait implémenté avec :
- Inscription et connexion sécurisées
- Gestion de sessions
- Hashage des mots de passe
- Chaque utilisateur verrait uniquement ses propres réservations et avis

Cette approche permet de se concentrer sur les aspects principaux du projet : MongoDB, CRUD, et les requêtes NoSQL.

---

## 👨‍💻 Auteur

**Charlotte Keza Manzi**  
Université Catholique de l'Ouest  
Projet Final - NoSQL  
15 Février 2025

**Repository :** https://github.com/charlotte-manzi/Biomebistro

---

**© 2025 BiomeBistro**
