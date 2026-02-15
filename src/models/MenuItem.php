<?php
/**
 * Modèle MenuItem
 * Gère les éléments de menu des restaurants
 */

namespace BiomeBistro\Models;

use BiomeBistro\Config\Database;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;

class MenuItem {
    private $collection;
    
    public function __construct() {
        $this->collection = Database::getCollection('menu_items');
    }
    
    /**
     * Récupère tous les éléments de menu
     * 
     * @return array Tableau de documents d'éléments de menu
     */
    public function getAll(): array {
        return $this->collection->find()->toArray();
    }
    
    /**
     * Récupère un élément de menu par son ID
     * 
     * @param string $id ID de l'élément de menu
     * @return array|null Document de l'élément de menu ou null
     */
    public function getById(string $id): ?array {
        try {
            $result = $this->collection->findOne(['_id' => new ObjectId($id)]);
            return $result ? (array)$result : null;
        } catch (\Exception $e) {
            return null;
        }
    }
    
    /**
     * Récupère les éléments de menu par restaurant
     * 
     * @param string $restaurantId ID du restaurant
     * @param array $filters Filtres optionnels (category, available, etc.)
     * @return array Tableau d'éléments de menu
     */
    public function getByRestaurant(string $restaurantId, array $filters = []): array {
        try {
            $query = ['restaurant_id' => new ObjectId($restaurantId)];
            
            // Ajouter des filtres supplémentaires
            if (isset($filters['category'])) {
                $query['category'] = $filters['category'];
            }
            
            if (isset($filters['is_available'])) {
                $query['is_available'] = $filters['is_available'];
            }
            
            if (isset($filters['is_signature_dish'])) {
                $query['is_signature_dish'] = $filters['is_signature_dish'];
            }
            
            // Trier par catégorie, puis par rang de popularité
            return $this->collection->find(
                $query,
                ['sort' => ['category' => 1, 'popularity_rank' => 1]]
            )->toArray();
        } catch (\Exception $e) {
            error_log("Erreur lors de la récupération des éléments de menu : " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Récupère les éléments de menu par catégorie pour un restaurant
     * 
     * @param string $restaurantId ID du restaurant
     * @param string $category Nom de la catégorie
     * @return array Tableau d'éléments de menu
     */
    public function getByCategory(string $restaurantId, string $category): array {
        return $this->getByRestaurant($restaurantId, ['category' => $category]);
    }
    
    /**
     * Récupère les plats signature d'un restaurant
     * 
     * @param string $restaurantId ID du restaurant
     * @return array Tableau de plats signature
     */
    public function getSignatureDishes(string $restaurantId): array {
        return $this->getByRestaurant($restaurantId, ['is_signature_dish' => true]);
    }
    
    /**
     * Récupère les éléments de menu disponibles pour un restaurant
     * 
     * @param string $restaurantId ID du restaurant
     * @return array Tableau d'éléments de menu disponibles
     */
    public function getAvailable(string $restaurantId): array {
        return $this->getByRestaurant($restaurantId, ['is_available' => true]);
    }
    
    /**
     * Recherche des éléments de menu
     * 
     * @param string $query Requête de recherche
     * @param string|null $restaurantId Filtre restaurant optionnel
     * @return array Tableau d'éléments de menu correspondants
     */
    public function search(string $query, ?string $restaurantId = null): array {
        $filter = [
            '$or' => [
                ['name' => ['$regex' => $query, '$options' => 'i']],
                ['description' => ['$regex' => $query, '$options' => 'i']]
            ]
        ];
        
        if ($restaurantId) {
            try {
                $filter['restaurant_id'] = new ObjectId($restaurantId);
            } catch (\Exception $e) {
                // ObjectId invalide, ignorer le filtre
            }
        }
        
        return $this->collection->find($filter)->toArray();
    }
    
    /**
     * Crée un nouvel élément de menu
     * 
     * @param array $data Données de l'élément de menu
     * @return string|null ID inséré ou null en cas d'échec
     */
    public function create(array $data): ?string {
        try {
            // Définir les valeurs par défaut
            $data['is_available'] = $data['is_available'] ?? true;
            $data['is_signature_dish'] = $data['is_signature_dish'] ?? false;
            $data['is_seasonal'] = $data['is_seasonal'] ?? false;
            $data['popularity_rank'] = $data['popularity_rank'] ?? 999;
            $data['created_at'] = new UTCDateTime();
            
            // Convertir restaurant_id en ObjectId s'il s'agit d'une chaîne
            if (isset($data['restaurant_id']) && is_string($data['restaurant_id'])) {
                $data['restaurant_id'] = new ObjectId($data['restaurant_id']);
            }
            
            $result = $this->collection->insertOne($data);
            return (string)$result->getInsertedId();
        } catch (\Exception $e) {
            error_log("Erreur lors de la création de l'élément de menu : " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Met à jour un élément de menu
     * 
     * @param string $id ID de l'élément de menu
     * @param array $data Données mises à jour
     * @return bool Statut de succès
     */
    public function update(string $id, array $data): bool {
        try {
            // Convertir restaurant_id en ObjectId s'il est présent et est une chaîne
            if (isset($data['restaurant_id']) && is_string($data['restaurant_id'])) {
                $data['restaurant_id'] = new ObjectId($data['restaurant_id']);
            }
            
            $result = $this->collection->updateOne(
                ['_id' => new ObjectId($id)],
                ['$set' => $data]
            );
            return $result->getModifiedCount() > 0 || $result->getMatchedCount() > 0;
        } catch (\Exception $e) {
            error_log("Erreur lors de la mise à jour de l'élément de menu : " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Supprime un élément de menu
     * 
     * @param string $id ID de l'élément de menu
     * @return bool Statut de succès
     */
    public function delete(string $id): bool {
        try {
            $result = $this->collection->deleteOne(['_id' => new ObjectId($id)]);
            return $result->getDeletedCount() > 0;
        } catch (\Exception $e) {
            error_log("Erreur lors de la suppression de l'élément de menu : " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Bascule la disponibilité d'un élément de menu
     * 
     * @param string $id ID de l'élément de menu
     * @param bool $available Statut de disponibilité
     * @return bool Statut de succès
     */
    public function setAvailability(string $id, bool $available): bool {
        return $this->update($id, ['is_available' => $available]);
    }
    
    /**
     * Récupère les catégories de menu pour un restaurant
     * 
     * @param string $restaurantId ID du restaurant
     * @return array Tableau de catégories uniques
     */
    public function getCategories(string $restaurantId): array {
        try {
            $categories = $this->collection->distinct('category', [
                'restaurant_id' => new ObjectId($restaurantId)
            ]);
            return $categories;
        } catch (\Exception $e) {
            return ['Appetizer', 'Main Course', 'Dessert', 'Beverage'];
        }
    }
    
    /**
     * Compte les éléments de menu pour un restaurant
     * 
     * @param string $restaurantId ID du restaurant
     * @return int Compteur
     */
    public function countByRestaurant(string $restaurantId): int {
        try {
            return $this->collection->countDocuments([
                'restaurant_id' => new ObjectId($restaurantId)
            ]);
        } catch (\Exception $e) {
            return 0;
        }
    }
    
    /**
     * Récupère les éléments par allergène (trouve les éléments qui NE contiennent PAS un allergène spécifique)
     * 
     * @param string $restaurantId ID du restaurant
     * @param string $allergen Allergène à exclure
     * @return array Tableau d'éléments de menu sûrs
     */
    public function getWithoutAllergen(string $restaurantId, string $allergen): array {
        try {
            return $this->collection->find([
                'restaurant_id' => new ObjectId($restaurantId),
                'allergens' => ['$ne' => $allergen]
            ])->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }
    
    /**
     * Récupère les éléments dans une fourchette de prix
     * 
     * @param string $restaurantId ID du restaurant
     * @param float $minPrice Prix minimum
     * @param float $maxPrice Prix maximum
     * @return array Tableau d'éléments de menu
     */
    public function getByPriceRange(string $restaurantId, float $minPrice, float $maxPrice): array {
        try {
            return $this->collection->find([
                'restaurant_id' => new ObjectId($restaurantId),
                'price' => ['$gte' => $minPrice, '$lte' => $maxPrice]
            ])->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }
}