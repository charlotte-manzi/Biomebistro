<?php
/**
 * MenuItem Model
 * Handles menu items for restaurants
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
     * Get all menu items
     * 
     * @return array Array of menu item documents
     */
    public function getAll(): array {
        return $this->collection->find()->toArray();
    }
    
    /**
     * Get menu item by ID
     * 
     * @param string $id Menu item ID
     * @return array|null Menu item document or null
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
     * Get menu items by restaurant
     * 
     * @param string $restaurantId Restaurant ID
     * @param array $filters Optional filters (category, available, etc.)
     * @return array Array of menu items
     */
    public function getByRestaurant(string $restaurantId, array $filters = []): array {
        try {
            $query = ['restaurant_id' => new ObjectId($restaurantId)];
            
            // Add additional filters
            if (isset($filters['category'])) {
                $query['category'] = $filters['category'];
            }
            
            if (isset($filters['is_available'])) {
                $query['is_available'] = $filters['is_available'];
            }
            
            if (isset($filters['is_signature_dish'])) {
                $query['is_signature_dish'] = $filters['is_signature_dish'];
            }
            
            // Sort by category, then by popularity
            return $this->collection->find(
                $query,
                ['sort' => ['category' => 1, 'popularity_rank' => 1]]
            )->toArray();
        } catch (\Exception $e) {
            error_log("Error getting menu items: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get menu items by category for a restaurant
     * 
     * @param string $restaurantId Restaurant ID
     * @param string $category Category name
     * @return array Array of menu items
     */
    public function getByCategory(string $restaurantId, string $category): array {
        return $this->getByRestaurant($restaurantId, ['category' => $category]);
    }
    
    /**
     * Get signature dishes for a restaurant
     * 
     * @param string $restaurantId Restaurant ID
     * @return array Array of signature dishes
     */
    public function getSignatureDishes(string $restaurantId): array {
        return $this->getByRestaurant($restaurantId, ['is_signature_dish' => true]);
    }
    
    /**
     * Get available menu items for a restaurant
     * 
     * @param string $restaurantId Restaurant ID
     * @return array Array of available menu items
     */
    public function getAvailable(string $restaurantId): array {
        return $this->getByRestaurant($restaurantId, ['is_available' => true]);
    }
    
    /**
     * Search menu items
     * 
     * @param string $query Search query
     * @param string|null $restaurantId Optional restaurant filter
     * @return array Array of matching menu items
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
                // Invalid ObjectId, ignore filter
            }
        }
        
        return $this->collection->find($filter)->toArray();
    }
    
    /**
     * Create a new menu item
     * 
     * @param array $data Menu item data
     * @return string|null Inserted ID or null on failure
     */
    public function create(array $data): ?string {
        try {
            // Set default values
            $data['is_available'] = $data['is_available'] ?? true;
            $data['is_signature_dish'] = $data['is_signature_dish'] ?? false;
            $data['is_seasonal'] = $data['is_seasonal'] ?? false;
            $data['popularity_rank'] = $data['popularity_rank'] ?? 999;
            $data['created_at'] = new UTCDateTime();
            
            // Convert restaurant_id to ObjectId if it's a string
            if (isset($data['restaurant_id']) && is_string($data['restaurant_id'])) {
                $data['restaurant_id'] = new ObjectId($data['restaurant_id']);
            }
            
            $result = $this->collection->insertOne($data);
            return (string)$result->getInsertedId();
        } catch (\Exception $e) {
            error_log("Error creating menu item: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Update a menu item
     * 
     * @param string $id Menu item ID
     * @param array $data Updated data
     * @return bool Success status
     */
    public function update(string $id, array $data): bool {
        try {
            // Convert restaurant_id to ObjectId if present and is string
            if (isset($data['restaurant_id']) && is_string($data['restaurant_id'])) {
                $data['restaurant_id'] = new ObjectId($data['restaurant_id']);
            }
            
            $result = $this->collection->updateOne(
                ['_id' => new ObjectId($id)],
                ['$set' => $data]
            );
            return $result->getModifiedCount() > 0 || $result->getMatchedCount() > 0;
        } catch (\Exception $e) {
            error_log("Error updating menu item: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete a menu item
     * 
     * @param string $id Menu item ID
     * @return bool Success status
     */
    public function delete(string $id): bool {
        try {
            $result = $this->collection->deleteOne(['_id' => new ObjectId($id)]);
            return $result->getDeletedCount() > 0;
        } catch (\Exception $e) {
            error_log("Error deleting menu item: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Toggle availability of a menu item
     * 
     * @param string $id Menu item ID
     * @param bool $available Availability status
     * @return bool Success status
     */
    public function setAvailability(string $id, bool $available): bool {
        return $this->update($id, ['is_available' => $available]);
    }
    
    /**
     * Get menu categories for a restaurant
     * 
     * @param string $restaurantId Restaurant ID
     * @return array Array of unique categories
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
     * Count menu items for a restaurant
     * 
     * @param string $restaurantId Restaurant ID
     * @return int Count
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
     * Get items by allergen (find items that DON'T contain specific allergen)
     * 
     * @param string $restaurantId Restaurant ID
     * @param string $allergen Allergen to exclude
     * @return array Array of safe menu items
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
     * Get items within a price range
     * 
     * @param string $restaurantId Restaurant ID
     * @param float $minPrice Minimum price
     * @param float $maxPrice Maximum price
     * @return array Array of menu items
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
