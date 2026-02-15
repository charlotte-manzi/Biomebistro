<?php
/**
 * Modèle Restaurant
 * Gère toutes les opérations de base de données liées aux restaurants
 */

namespace BiomeBistro\Models;

use BiomeBistro\Config\Database;
use BiomeBistro\Utils\GeoCalculator;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;

class Restaurant {
    private $collection;
    
    public function __construct() {
        $this->collection = Database::getCollection('restaurants');
    }
    
    /**
     * Récupère tous les restaurants
     * 
     * @param array $filters Filtres optionnels
     * @param array $sort Critères de tri optionnels
     * @param int $limit Limite optionnelle
     * @return array Tableau de documents restaurants
     */
    public function getAll(array $filters = [], array $sort = [], int $limit = 0): array {
        $options = [];
        
        if (!empty($sort)) {
            $options['sort'] = $sort;
        }
        
        if ($limit > 0) {
            $options['limit'] = $limit;
        }
        
        return $this->collection->find($filters, $options)->toArray();
    }
    
    /**
     * Récupère un restaurant par son ID
     * 
     * @param string $id ID du restaurant
     * @return array|null Document restaurant ou null
     */
    public function getById(string $id): ?array {
        try {
            $result = $this->collection->findOne(['_id' => new ObjectId($id)]);
            return $result ? (array)$result : null;
        } catch (\Exception $e) {
            error_log("Erreur lors de la récupération du restaurant : " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Récupère les restaurants par biome
     * 
     * @param string $biomeId ID du biome
     * @return array Tableau de restaurants
     */
    public function getByBiome(string $biomeId): array {
        try {
            return $this->collection->find(['biome_id' => new ObjectId($biomeId)])->toArray();
        } catch (\Exception $e) {
            error_log("Erreur lors de la récupération des restaurants par biome : " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Compte les restaurants par biome
     * 
     * @param string $biomeId ID du biome
     * @return int Compteur
     */
    public function countByBiome(string $biomeId): int {
        try {
            return $this->collection->countDocuments(['biome_id' => new ObjectId($biomeId)]);
        } catch (\Exception $e) {
            return 0;
        }
    }
    
    /**
     * Récupère les restaurants les mieux notés
     * 
     * @param int $limit Nombre de restaurants à retourner
     * @return array Tableau des restaurants les mieux notés
     */
    public function getTopRated(int $limit = 4): array {
        return $this->getAll(
            ['status' => 'open'],
            ['average_rating' => -1, 'total_reviews' => -1],
            $limit
        );
    }
    
    /**
     * Recherche des restaurants
     * 
     * @param string $query Requête de recherche
     * @return array Tableau de restaurants correspondants
     */
    public function search(string $query): array {
        // Recherche textuelle sur le nom et la description
        return $this->collection->find([
            '$text' => ['$search' => $query]
        ])->toArray();
    }
    
    /**
     * Filtrage/recherche avancé
     * 
     * @param array $params Paramètres de filtrage
     * @return array Tableau de restaurants
     */
    public function filter(array $params): array {
        $filters = [];
        
        // Filtre par biome
        if (!empty($params['biome_id'])) {
            try {
                $filters['biome_id'] = new ObjectId($params['biome_id']);
            } catch (\Exception $e) {
                // ObjectId invalide, ignorer le filtre
            }
        }
        
        // Filtre par gamme de prix
        if (!empty($params['price_range'])) {
            $filters['price_range'] = $params['price_range'];
        }
        
        // Filtre par note (note minimum)
        if (!empty($params['min_rating'])) {
            $filters['average_rating'] = ['$gte' => (float)$params['min_rating']];
        }
        
        // Filtre par statut (ouvert/fermé)
        if (!empty($params['status'])) {
            $filters['status'] = $params['status'];
        }
        
        // Recherche textuelle
        if (!empty($params['search'])) {
            $filters['$text'] = ['$search' => $params['search']];
        }
        
        // Style de cuisine
        if (!empty($params['cuisine_style'])) {
            $filters['cuisine_style'] = ['$regex' => $params['cuisine_style'], '$options' => 'i'];
        }
        
        // Options de tri
        $sort = [];
        if (!empty($params['sort_by'])) {
            switch ($params['sort_by']) {
                case 'rating':
                    $sort = ['average_rating' => -1];
                    break;
                case 'price_low':
                    $sort = ['price_range' => 1];
                    break;
                case 'price_high':
                    $sort = ['price_range' => -1];
                    break;
                case 'newest':
                    $sort = ['created_at' => -1];
                    break;
                case 'name':
                    $sort = ['name' => 1];
                    break;
                default:
                    $sort = ['average_rating' => -1];
            }
        }
        
        return $this->getAll($filters, $sort);
    }
    
    /**
     * Récupère les restaurants à proximité en utilisant les coordonnées GPS
     * 
     * @param float $latitude Latitude
     * @param float $longitude Longitude
     * @param float $radiusKm Rayon en kilomètres
     * @return array Tableau de restaurants proches avec leurs distances
     */
    public function getNearby(float $latitude, float $longitude, float $radiusKm = 10): array {
        $query = GeoCalculator::getNearbyQuery($latitude, $longitude, $radiusKm);
        $restaurants = $this->collection->find($query)->toArray();
        
        // Ajouter la distance à chaque restaurant
        foreach ($restaurants as &$restaurant) {
            if (isset($restaurant['location']['coordinates'])) {
                $coords = $restaurant['location']['coordinates'];
                $distance = GeoCalculator::calculateDistance(
                    $latitude,
                    $longitude,
                    $coords[1], // MongoDB stocke [lon, lat]
                    $coords[0]
                );
                $restaurant['distance_km'] = $distance;
            }
        }
        
        return $restaurants;
    }
    
    /**
     * Crée un nouveau restaurant
     * 
     * @param array $data Données du restaurant
     * @return string|null ID inséré ou null en cas d'échec
     */
    public function create(array $data): ?string {
        try {
            // Définir les valeurs par défaut
            $data['average_rating'] = $data['average_rating'] ?? 0;
            $data['total_reviews'] = $data['total_reviews'] ?? 0;
            $data['status'] = $data['status'] ?? 'open';
            $data['created_at'] = new UTCDateTime();
            $data['updated_at'] = new UTCDateTime();
            
            // Convertir biome_id en ObjectId s'il s'agit d'une chaîne
            if (isset($data['biome_id']) && is_string($data['biome_id'])) {
                $data['biome_id'] = new ObjectId($data['biome_id']);
            }
            
            $result = $this->collection->insertOne($data);
            return (string)$result->getInsertedId();
        } catch (\Exception $e) {
            error_log("Erreur lors de la création du restaurant : " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Met à jour un restaurant
     * 
     * @param string $id ID du restaurant
     * @param array $data Données mises à jour
     * @return bool Statut de succès
     */
    public function update(string $id, array $data): bool {
        try {
            $data['updated_at'] = new UTCDateTime();
            
            // Convertir biome_id en ObjectId s'il est présent et est une chaîne
            if (isset($data['biome_id']) && is_string($data['biome_id'])) {
                $data['biome_id'] = new ObjectId($data['biome_id']);
            }
            
            $result = $this->collection->updateOne(
                ['_id' => new ObjectId($id)],
                ['$set' => $data]
            );
            return $result->getModifiedCount() > 0 || $result->getMatchedCount() > 0;
        } catch (\Exception $e) {
            error_log("Erreur lors de la mise à jour du restaurant : " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Supprime un restaurant
     * 
     * @param string $id ID du restaurant
     * @return bool Statut de succès
     */
    public function delete(string $id): bool {
        try {
            $result = $this->collection->deleteOne(['_id' => new ObjectId($id)]);
            return $result->getDeletedCount() > 0;
        } catch (\Exception $e) {
            error_log("Erreur lors de la suppression du restaurant : " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Met à jour la note du restaurant (appelé lorsqu'un nouvel avis est ajouté)
     * 
     * @param string $restaurantId ID du restaurant
     * @return bool Statut de succès
     */
    public function updateRating(string $restaurantId): bool {
        try {
            // Calculer la note moyenne à partir de tous les avis
            $reviewModel = new Review();
            $reviews = $reviewModel->getByRestaurant($restaurantId);
            
            if (empty($reviews)) {
                $avgRating = 0;
                $totalReviews = 0;
            } else {
                $totalRating = 0;
                foreach ($reviews as $review) {
                    $totalRating += $review['rating'];
                }
                $avgRating = round($totalRating / count($reviews), 1);
                $totalReviews = count($reviews);
            }
            
            return $this->update($restaurantId, [
                'average_rating' => $avgRating,
                'total_reviews' => $totalReviews
            ]);
        } catch (\Exception $e) {
            error_log("Erreur lors de la mise à jour de la note du restaurant : " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Vérifie si le restaurant est ouvert à une heure donnée
     * 
     * @param string $restaurantId ID du restaurant
     * @param string $day Jour de la semaine (par ex., "Monday")
     * @param string $time Heure au format HH:MM
     * @return bool True si ouvert
     */
    public function isOpenAt(string $restaurantId, string $day, string $time): bool {
        $restaurant = $this->getById($restaurantId);
        
        if (!$restaurant || empty($restaurant['opening_hours'])) {
            return false;
        }
        
        foreach ($restaurant['opening_hours'] as $hours) {
            if ($hours['day'] === $day && !$hours['closed']) {
                return $time >= $hours['open'] && $time <= $hours['close'];
            }
        }
        
        return false;
    }
    
    /**
     * Récupère des restaurants similaires (même biome, restaurant différent)
     * 
     * @param string $restaurantId ID du restaurant actuel
     * @param int $limit Nombre de restaurants similaires à retourner
     * @return array Tableau de restaurants similaires
     */
    public function getSimilar(string $restaurantId, int $limit = 3): array {
        $restaurant = $this->getById($restaurantId);
        
        if (!$restaurant) {
            return [];
        }
        
        try {
            return $this->collection->find(
                [
                    'biome_id' => $restaurant['biome_id'],
                    '_id' => ['$ne' => new ObjectId($restaurantId)]
                ],
                [
                    'limit' => $limit,
                    'sort' => ['average_rating' => -1]
                ]
            )->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }
    
    /**
     * Récupère le nombre total de restaurants
     * 
     * @return int Compteur total
     */
    public function count(): int {
        return $this->collection->countDocuments([]);
    }
}