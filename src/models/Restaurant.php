<?php
/**
 * Restaurant Model
 * Handles all restaurant-related database operations
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
     * Get all restaurants
     * 
     * @param array $filters Optional filters
     * @param array $sort Optional sort criteria
     * @param int $limit Optional limit
     * @return array Array of restaurant documents
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
     * Get restaurant by ID
     * 
     * @param string $id Restaurant ID
     * @return array|null Restaurant document or null
     */
    public function getById(string $id): ?array {
        try {
            $result = $this->collection->findOne(['_id' => new ObjectId($id)]);
            return $result ? (array)$result : null;
        } catch (\Exception $e) {
            error_log("Error getting restaurant: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get restaurants by biome
     * 
     * @param string $biomeId Biome ID
     * @return array Array of restaurants
     */
    public function getByBiome(string $biomeId): array {
        try {
            return $this->collection->find(['biome_id' => new ObjectId($biomeId)])->toArray();
        } catch (\Exception $e) {
            error_log("Error getting restaurants by biome: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Count restaurants by biome
     * 
     * @param string $biomeId Biome ID
     * @return int Count
     */
    public function countByBiome(string $biomeId): int {
        try {
            return $this->collection->countDocuments(['biome_id' => new ObjectId($biomeId)]);
        } catch (\Exception $e) {
            return 0;
        }
    }
    
    /**
     * Get top-rated restaurants
     * 
     * @param int $limit Number of restaurants to return
     * @return array Array of top-rated restaurants
     */
    public function getTopRated(int $limit = 4): array {
        return $this->getAll(
            ['status' => 'open'],
            ['average_rating' => -1, 'total_reviews' => -1],
            $limit
        );
    }
    
    /**
     * Search restaurants
     * 
     * @param string $query Search query
     * @return array Array of matching restaurants
     */
    public function search(string $query): array {
        // Text search on name and description
        return $this->collection->find([
            '$text' => ['$search' => $query]
        ])->toArray();
    }
    
    /**
     * Advanced filter/search
     * 
     * @param array $params Filter parameters
     * @return array Array of restaurants
     */
    public function filter(array $params): array {
        $filters = [];
        
        // Biome filter
        if (!empty($params['biome_id'])) {
            try {
                $filters['biome_id'] = new ObjectId($params['biome_id']);
            } catch (\Exception $e) {
                // Invalid ObjectId, skip filter
            }
        }
        
        // Price range filter
        if (!empty($params['price_range'])) {
            $filters['price_range'] = $params['price_range'];
        }
        
        // Rating filter (minimum rating)
        if (!empty($params['min_rating'])) {
            $filters['average_rating'] = ['$gte' => (float)$params['min_rating']];
        }
        
        // Status filter (open/closed)
        if (!empty($params['status'])) {
            $filters['status'] = $params['status'];
        }
        
        // Text search
        if (!empty($params['search'])) {
            $filters['$text'] = ['$search' => $params['search']];
        }
        
        // Cuisine style
        if (!empty($params['cuisine_style'])) {
            $filters['cuisine_style'] = ['$regex' => $params['cuisine_style'], '$options' => 'i'];
        }
        
        // Sort options
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
     * Get nearby restaurants using GPS coordinates
     * 
     * @param float $latitude
     * @param float $longitude
     * @param float $radiusKm Radius in kilometers
     * @return array Array of nearby restaurants with distances
     */
    public function getNearby(float $latitude, float $longitude, float $radiusKm = 10): array {
        $query = GeoCalculator::getNearbyQuery($latitude, $longitude, $radiusKm);
        $restaurants = $this->collection->find($query)->toArray();
        
        // Add distance to each restaurant
        foreach ($restaurants as &$restaurant) {
            if (isset($restaurant['location']['coordinates'])) {
                $coords = $restaurant['location']['coordinates'];
                $distance = GeoCalculator::calculateDistance(
                    $latitude,
                    $longitude,
                    $coords[1], // MongoDB stores [lon, lat]
                    $coords[0]
                );
                $restaurant['distance_km'] = $distance;
            }
        }
        
        return $restaurants;
    }
    
    /**
     * Create a new restaurant
     * 
     * @param array $data Restaurant data
     * @return string|null Inserted ID or null on failure
     */
    public function create(array $data): ?string {
        try {
            // Set default values
            $data['average_rating'] = $data['average_rating'] ?? 0;
            $data['total_reviews'] = $data['total_reviews'] ?? 0;
            $data['status'] = $data['status'] ?? 'open';
            $data['created_at'] = new UTCDateTime();
            $data['updated_at'] = new UTCDateTime();
            
            // Convert biome_id to ObjectId if it's a string
            if (isset($data['biome_id']) && is_string($data['biome_id'])) {
                $data['biome_id'] = new ObjectId($data['biome_id']);
            }
            
            $result = $this->collection->insertOne($data);
            return (string)$result->getInsertedId();
        } catch (\Exception $e) {
            error_log("Error creating restaurant: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Update a restaurant
     * 
     * @param string $id Restaurant ID
     * @param array $data Updated data
     * @return bool Success status
     */
    public function update(string $id, array $data): bool {
        try {
            $data['updated_at'] = new UTCDateTime();
            
            // Convert biome_id to ObjectId if present and is string
            if (isset($data['biome_id']) && is_string($data['biome_id'])) {
                $data['biome_id'] = new ObjectId($data['biome_id']);
            }
            
            $result = $this->collection->updateOne(
                ['_id' => new ObjectId($id)],
                ['$set' => $data]
            );
            return $result->getModifiedCount() > 0 || $result->getMatchedCount() > 0;
        } catch (\Exception $e) {
            error_log("Error updating restaurant: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete a restaurant
     * 
     * @param string $id Restaurant ID
     * @return bool Success status
     */
    public function delete(string $id): bool {
        try {
            $result = $this->collection->deleteOne(['_id' => new ObjectId($id)]);
            return $result->getDeletedCount() > 0;
        } catch (\Exception $e) {
            error_log("Error deleting restaurant: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update restaurant rating (called when new review is added)
     * 
     * @param string $restaurantId Restaurant ID
     * @return bool Success status
     */
    public function updateRating(string $restaurantId): bool {
        try {
            // Calculate average rating from all reviews
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
            error_log("Error updating restaurant rating: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Check if restaurant is open at a given time
     * 
     * @param string $restaurantId Restaurant ID
     * @param string $day Day of week (e.g., "Monday")
     * @param string $time Time in HH:MM format
     * @return bool True if open
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
     * Get similar restaurants (same biome, different restaurant)
     * 
     * @param string $restaurantId Current restaurant ID
     * @param int $limit Number of similar restaurants to return
     * @return array Array of similar restaurants
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
     * Get count of all restaurants
     * 
     * @return int Total count
     */
    public function count(): int {
        return $this->collection->countDocuments([]);
    }
}
