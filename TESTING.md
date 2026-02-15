# 🧪 Guide des Tests Unitaires - BiomeBistro

## 📋 Vue d'ensemble

Ce projet contient des tests unitaires pour vérifier le bon fonctionnement des modèles MongoDB.

### Tests inclus :
- ✅ **BiomeTest** - Tests du modèle Biome
- ✅ **RestaurantTest** - Tests du modèle Restaurant
- ✅ **ReservationTest** - Tests du modèle Reservation et des validateurs

---

## 🚀 Comment lancer les tests

### Prérequis
1. MongoDB doit être en cours d'exécution
2. Les dépendances Composer doivent être installées (`composer install`)

### Commande pour lancer tous les tests

```bash
vendor/bin/phpunit
```

ou avec le chemin complet PHP :

```bash
php vendor/phpunit/phpunit/phpunit
```

### Lancer un fichier de test spécifique

```bash
vendor/bin/phpunit tests/BiomeTest.php
vendor/bin/phpunit tests/RestaurantTest.php
vendor/bin/phpunit tests/ReservationTest.php
```

### Lancer un test spécifique

```bash
vendor/bin/phpunit --filter testCreateBiome
vendor/bin/phpunit --filter testCreateRestaurant
```

---

## 📊 Résultats attendus

Vous devriez voir quelque chose comme :

```
PHPUnit 9.6.x by Sebastian Bergmann and contributors.

Runtime:       PHP 8.2.12
Configuration: /path/to/biomebistro/phpunit.xml

.....................                                             21 / 21 (100%)

Time: 00:02.345, Memory: 18.00 MB

OK (21 tests, 75 assertions)
```

---

## 🧪 Tests couverts

### BiomeTest (7 tests)
1. ✅ testCreateBiome - Création d'un biome
2. ✅ testGetBiomeById - Récupération par ID
3. ✅ testGetAllBiomes - Récupération de tous les biomes
4. ✅ testUpdateBiome - Mise à jour d'un biome
5. ✅ testDeleteBiome - Suppression d'un biome
6. ✅ testDatabaseConnection - Connexion à la base de données
7. ✅ testBiomeRequiredFields - Validation des champs requis

### RestaurantTest (9 tests)
1. ✅ testCreateRestaurant - Création d'un restaurant
2. ✅ testGetRestaurantById - Récupération par ID
3. ✅ testGetAllRestaurants - Récupération de tous les restaurants
4. ✅ testFilterRestaurants - Filtrage des restaurants
5. ✅ testGetTopRatedRestaurants - Top restaurants par note
6. ✅ testUpdateRestaurant - Mise à jour d'un restaurant
7. ✅ testDeleteRestaurant - Suppression d'un restaurant
8. ✅ testSearchRestaurants - Recherche de restaurants
9. ✅ testCountRestaurants - Comptage des restaurants

### ReservationTest (11 tests)
1. ✅ testCreateReservation - Création d'une réservation
2. ✅ testGetReservationById - Récupération par ID
3. ✅ testGetReservationsByRestaurant - Réservations par restaurant
4. ✅ testUpdateReservationStatus - Mise à jour du statut
5. ✅ testDeleteReservation - Suppression d'une réservation
6. ✅ testEmailValidation - Validation d'email
7. ✅ testPhoneValidation - Validation de téléphone
8. ✅ testDateValidation - Validation de date
9. ✅ testPartySizeValidation - Validation du nombre de personnes
10. ✅ testReservationValidation - Validation complète de réservation
11. ✅ testInvalidReservationData - Détection de données invalides

---

## 🔧 Dépannage

### Erreur: "Class not found"
**Solution:** Exécutez `composer dump-autoload`

### Erreur: "MongoDB connection failed"
**Solution:** Vérifiez que MongoDB est en cours d'exécution
```bash
# Windows - Vérifier le service
services.msc
# Cherchez "MongoDB Server" et assurez-vous qu'il est démarré
```

### Erreur: "PHPUnit not found"
**Solution:** Réinstallez les dépendances
```bash
composer install
```

---

## 📈 Couverture de code

Pour générer un rapport de couverture de code (nécessite Xdebug) :

```bash
vendor/bin/phpunit --coverage-html coverage
```

Le rapport sera généré dans le dossier `coverage/`.

---

## ✅ Checklist avant soumission

- [ ] Tous les tests passent (21/21)
- [ ] MongoDB est en cours d'exécution
- [ ] Aucune erreur PHP
- [ ] Tests couvrent CREATE, READ, UPDATE, DELETE
- [ ] Tests de validation fonctionnent

---

## 📝 Notes

- Les tests créent et suppriment automatiquement des données de test
- Les tests n'affectent PAS les données de production
- Chaque test est indépendant et peut être exécuté seul
- Les tests utilisent `setUp()` et `tearDown()` pour la préparation et le nettoyage

---

## 🎯 Score attendu

**27 tests au total** couvrant :
- ✅ CRUD complet sur 3 modèles
- ✅ Validations
- ✅ Filtres et recherches
- ✅ Connexion base de données
- ✅ Relations entre collections

**Taux de réussite attendu : 100%**
