<?php
/**
 * Review Model
 * Handles customer reviews for restaurants
 */

namespace BiomeBistro\Models;

use BiomeBistro\Config\Database;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;

class Review {
    private $collection;
    
    public function __construct() {
        $this->collection = Database::getCollection('reviews');
    }
    
    /**
     * Get all reviews
     * 
     * @param int $limit Optional limit
     * @return array Array of review documents
     */
    public function getAll(int $limit = 0): array {
        $options = ['sort' => ['created_at' => -1]];
        
        if ($limit > 0) {
            $options['limit'] = $limit;
        }
        
        return $this->collection->find([], $options)->toArray();
    }
    
    /**
     * Get review by ID
     * 
     * @param string $id Review ID
     * @return array|null Review document or null
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
     * Get reviews by restaurant
     * 
     * @param string $restaurantId Restaurant ID
     * @param array $filters Optional filters
     * @param int $limit Optional limit
     * @return array Array of reviews
     */
    public function getByRestaurant(string $restaurantId, array $filters = [], int $limit = 0): array {
        try {
            $query = ['restaurant_id' => new ObjectId($restaurantId)];
            
            // Apply additional filters
            if (isset($filters['min_rating'])) {
                $query['rating'] = ['$gte' => (float)$filters['min_rating']];
            }
            
            if (isset($filters['verified_visit'])) {
                $query['verified_visit'] = $filters['verified_visit'];
            }
            
            $options = ['sort' => ['created_at' => -1]];
            
            if ($limit > 0) {
                $options['limit'] = $limit;
            }
            
            return $this->collection->find($query, $options)->toArray();
        } catch (\Exception $e) {
            error_log("Error getting reviews: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get recent reviews across all restaurants
     * 
     * @param int $limit Number of reviews to return
     * @return array Array of recent reviews
     */
    public function getRecent(int $limit = 10): array {
        return $this->collection->find(
            [],
            ['sort' => ['created_at' => -1], 'limit' => $limit]
        )->toArray();
    }
    
    /**
     * Get top reviews (highest rated with most helpful votes)
     * 
     * @param string $restaurantId Restaurant ID
     * @param int $limit Number of reviews to return
     * @return array Array of top reviews
     */
    public function getTopReviews(string $restaurantId, int $limit = 5): array {
        try {
            return $this->collection->find(
                ['restaurant_id' => new ObjectId($restaurantId)],
                [
                    'sort' => ['rating' => -1, 'helpful_votes' => -1],
                    'limit' => $limit
                ]
            )->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }
    
    /**
     * Create a new review
     * 
     * @param array $data Review data
     * @return string|null Inserted ID or null on failure
     */
    public function create(array $data): ?string {
        try {
            // Set default values
            $data['helpful_votes'] = $data['helpful_votes'] ?? 0;
            $data['verified_visit'] = $data['verified_visit'] ?? false;
            $data['created_at'] = new UTCDateTime();
            
            // Convert restaurant_id to ObjectId if it's a string
            if (isset($data['restaurant_id']) && is_string($data['restaurant_id'])) {
                $data['restaurant_id'] = new ObjectId($data['restaurant_id']);
            }
            
            // Ensure ratings_breakdown has all required fields
            if (!isset($data['ratings_breakdown'])) {
                $rating = $data['rating'] ?? 5;
                $data['ratings_breakdown'] = [
                    'food_quality' => $rating,
                    'service' => $rating,
                    'ambiance' => $rating,
                    'value_for_money' => $rating,
                    'cleanliness' => $rating
                ];
            }
            
            $result = $this->collection->insertOne($data);
            $insertedId = (string)$result->getInsertedId();
            
            // Update restaurant's average rating
            if (isset($data['restaurant_id'])) {
                $restaurantModel = new Restaurant();
                $restaurantModel->updateRating((string)$data['restaurant_id']);
            }
            
            return $insertedId;
        } catch (\Exception $e) {
            error_log("Error creating review: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Update a review
     * 
     * @param string $id Review ID
     * @param array $data Updated data
     * @return bool Success status
     */
    public function update(string $id, array $data): bool {
        try {
            // Get the review to know which restaurant to update
            $review = $this->getById($id);
            
            // Convert restaurant_id to ObjectId if present and is string
            if (isset($data['restaurant_id']) && is_string($data['restaurant_id'])) {
                $data['restaurant_id'] = new ObjectId($data['restaurant_id']);
            }
            
            $result = $this->collection->updateOne(
                ['_id' => new ObjectId($id)],
                ['$set' => $data]
            );
            
            $success = $result->getModifiedCount() > 0 || $result->getMatchedCount() > 0;
            
            // Update restaurant rating if review was modified
            if ($success && $review && isset($review['restaurant_id'])) {
                $restaurantModel = new Restaurant();
                $restaurantModel->updateRating((string)$review['restaurant_id']);
            }
            
            return $success;
        } catch (\Exception $e) {
            error_log("Error updating review: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete a review
     * 
     * @param string $id Review ID
     * @return bool Success status
     */
    public function delete(string $id): bool {
        try {
            // Get the review to know which restaurant to update
            $review = $this->getById($id);
            
            $result = $this->collection->deleteOne(['_id' => new ObjectId($id)]);
            $success = $result->getDeletedCount() > 0;
            
            // Update restaurant rating after deletion
            if ($success && $review && isset($review['restaurant_id'])) {
                $restaurantModel = new Restaurant();
                $restaurantModel->updateRating((string)$review['restaurant_id']);
            }
            
            return $success;
        } catch (\Exception $e) {
            error_log("Error deleting review: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Add a helpful vote to a review
     * 
     * @param string $id Review ID
     * @return bool Success status
     */
    public function addHelpfulVote(string $id): bool {
        try {
            $result = $this->collection->updateOne(
                ['_id' => new ObjectId($id)],
                ['$inc' => ['helpful_votes' => 1]]
            );
            return $result->getModifiedCount() > 0;
        } catch (\Exception $e) {
            error_log("Error adding helpful vote: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Add restaurant response to a review
     * 
     * @param string $id Review ID
     * @param string $reply Response text
     * @return bool Success status
     */
    public function addRestaurantResponse(string $id, string $reply): bool {
        try {
            $response = [
                'from_restaurant' => true,
                'reply' => $reply,
                'replied_at' => new UTCDateTime()
            ];
            
            $result = $this->collection->updateOne(
                ['_id' => new ObjectId($id)],
                ['$set' => ['response' => $response]]
            );
            return $result->getModifiedCount() > 0;
        } catch (\Exception $e) {
            error_log("Error adding restaurant response: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Calculate average rating for a restaurant
     * 
     * @param string $restaurantId Restaurant ID
     * @return float Average rating
     */
    public function calculateAverageRating(string $restaurantId): float {
        $reviews = $this->getByRestaurant($restaurantId);
        
        if (empty($reviews)) {
            return 0;
        }
        
        $totalRating = 0;
        foreach ($reviews as $review) {
            $totalRating += $review['rating'];
        }
        
        return round($totalRating / count($reviews), 1);
    }
    
    /**
     * Get rating breakdown statistics for a restaurant
     * 
     * @param string $restaurantId Restaurant ID
     * @return array Rating breakdown with averages
     */
    public function getRatingBreakdown(string $restaurantId): array {
        $reviews = $this->getByRestaurant($restaurantId);
        
        if (empty($reviews)) {
            return [
                'food_quality' => 0,
                'service' => 0,
                'ambiance' => 0,
                'value_for_money' => 0,
                'cleanliness' => 0
            ];
        }
        
        $breakdown = [
            'food_quality' => 0,
            'service' => 0,
            'ambiance' => 0,
            'value_for_money' => 0,
            'cleanliness' => 0
        ];
        
        foreach ($reviews as $review) {
            if (isset($review['ratings_breakdown'])) {
                foreach ($breakdown as $key => $value) {
                    $breakdown[$key] += $review['ratings_breakdown'][$key] ?? 0;
                }
            }
        }
        
        $count = count($reviews);
        foreach ($breakdown as $key => $value) {
            $breakdown[$key] = round($value / $count, 1);
        }
        
        return $breakdown;
    }
    
    /**
     * Count reviews for a restaurant
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
     * Get reviews by rating range
     * 
     * @param string $restaurantId Restaurant ID
     * @param int $minRating Minimum rating
     * @param int $maxRating Maximum rating
     * @return array Array of reviews
     */
    public function getByRatingRange(string $restaurantId, int $minRating, int $maxRating): array {
        try {
            return $this->collection->find([
                'restaurant_id' => new ObjectId($restaurantId),
                'rating' => ['$gte' => $minRating, '$lte' => $maxRating]
            ])->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }
}
