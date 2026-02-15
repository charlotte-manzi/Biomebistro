<?php
/**
 * Biome Model
 * Represents ecosystem types (Tropical Rainforest, Desert, etc.)
 */

namespace BiomeBistro\Models;

use BiomeBistro\Config\Database;
use MongoDB\BSON\ObjectId;

class Biome {
    private $collection;
    
    public function __construct() {
        $this->collection = Database::getCollection('biomes');
    }
    
    /**
     * Get all biomes
     * 
     * @return array Array of biome documents
     */
    public function getAll(): array {
        return $this->collection->find()->toArray();
    }
    
    /**
     * Get biome by ID
     * 
     * @param string $id Biome ID
     * @return array|null Biome document or null
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
     * Get biome by name
     * 
     * @param string $name Biome name
     * @return array|null Biome document or null
     */
    public function getByName(string $name): ?array {
        $result = $this->collection->findOne(['name' => $name]);
        return $result ? (array)$result : null;
    }
    
    /**
     * Create a new biome
     * 
     * @param array $data Biome data
     * @return string|null Inserted ID or null on failure
     */
    public function create(array $data): ?string {
        try {
            $result = $this->collection->insertOne($data);
            return (string)$result->getInsertedId();
        } catch (\Exception $e) {
            error_log("Error creating biome: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Update a biome
     * 
     * @param string $id Biome ID
     * @param array $data Updated data
     * @return bool Success status
     */
    public function update(string $id, array $data): bool {
        try {
            $result = $this->collection->updateOne(
                ['_id' => new ObjectId($id)],
                ['$set' => $data]
            );
            return $result->getModifiedCount() > 0;
        } catch (\Exception $e) {
            error_log("Error updating biome: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete a biome
     * 
     * @param string $id Biome ID
     * @return bool Success status
     */
    public function delete(string $id): bool {
        try {
            $result = $this->collection->deleteOne(['_id' => new ObjectId($id)]);
            return $result->getDeletedCount() > 0;
        } catch (\Exception $e) {
            error_log("Error deleting biome: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Count restaurants in this biome
     * 
     * @param string $biomeId Biome ID
     * @return int Count of restaurants
     */
    public function countRestaurants(string $biomeId): int {
        $restaurantModel = new Restaurant();
        return $restaurantModel->countByBiome($biomeId);
    }
    
    /**
     * Get biomes with restaurant count
     * 
     * @return array Array of biomes with restaurant counts
     */
    public function getAllWithCounts(): array {
        $biomes = $this->getAll();
        $restaurantModel = new Restaurant();
        
        foreach ($biomes as &$biome) {
            $biome['restaurant_count'] = $restaurantModel->countByBiome((string)$biome['_id']);
        }
        
        return $biomes;
    }
}
