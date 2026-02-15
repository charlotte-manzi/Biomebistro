<?php
/**
 * Modèle Review
 * Gère les avis clients pour les restaurants
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
     * Récupère tous les avis
     * 
     * @param int $limit Limite optionnelle
     * @return array Tableau de documents d'avis
     */
    public function getAll(int $limit = 0): array {
        $options = ['sort' => ['created_at' => -1]];
        
        if ($limit > 0) {
            $options['limit'] = $limit;
        }
        
        return $this->collection->find([], $options)->toArray();
    }
    
    /**
     * Récupère un avis par son ID
     * 
     * @param string $id ID de l'avis
     * @return array|null Document d'avis ou null
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
     * Récupère les avis par restaurant
     * 
     * @param string $restaurantId ID du restaurant
     * @param array $filters Filtres optionnels
     * @param int $limit Limite optionnelle
     * @return array Tableau d'avis
     */
    public function getByRestaurant(string $restaurantId, array $filters = [], int $limit = 0): array {
        try {
            $query = ['restaurant_id' => new ObjectId($restaurantId)];
            
            // Appliquer des filtres supplémentaires
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
            error_log("Erreur lors de la récupération des avis : " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Récupère tous les avis d'un utilisateur par email
     * 
     * @param string $email Email de l'utilisateur
     * @return array Tableau d'avis de l'utilisateur
     */
    public function getByEmail(string $email): array
    {
        return $this->collection->find([
            'reviewer_email' => $email
        ], [
            'sort' => ['created_at' => -1]
        ])->toArray();
    }
    
    /**
     * Récupère les avis récents sur tous les restaurants
     * 
     * @param int $limit Nombre d'avis à retourner
     * @return array Tableau d'avis récents
     */
    public function getRecent(int $limit = 10): array {
        return $this->collection->find(
            [],
            ['sort' => ['created_at' => -1], 'limit' => $limit]
        )->toArray();
    }
    
    /**
     * Récupère les meilleurs avis (les mieux notés avec le plus de votes utiles)
     * 
     * @param string $restaurantId ID du restaurant
     * @param int $limit Nombre d'avis à retourner
     * @return array Tableau des meilleurs avis
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
     * Crée un nouvel avis
     * 
     * @param array $data Données de l'avis
     * @return string|null ID inséré ou null en cas d'échec
     */
    public function create(array $data): ?string {
        try {
            // Définir les valeurs par défaut
            $data['helpful_votes'] = $data['helpful_votes'] ?? 0;
            $data['verified_visit'] = $data['verified_visit'] ?? false;
            $data['created_at'] = new UTCDateTime();
            
            // Convertir restaurant_id en ObjectId s'il s'agit d'une chaîne
            if (isset($data['restaurant_id']) && is_string($data['restaurant_id'])) {
                $data['restaurant_id'] = new ObjectId($data['restaurant_id']);
            }
            
            // S'assurer que ratings_breakdown contient tous les champs requis
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
            
            // Mettre à jour la note moyenne du restaurant
            if (isset($data['restaurant_id'])) {
                $restaurantModel = new Restaurant();
                $restaurantModel->updateRating((string)$data['restaurant_id']);
            }
            
            return $insertedId;
        } catch (\Exception $e) {
            error_log("Erreur lors de la création de l'avis : " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Met à jour un avis
     * 
     * @param string $id ID de l'avis
     * @param array $data Données mises à jour
     * @return bool Statut de succès
     */
    public function update(string $id, array $data): bool {
        try {
            // Récupérer l'avis pour savoir quel restaurant mettre à jour
            $review = $this->getById($id);
            
            // Convertir restaurant_id en ObjectId s'il est présent et est une chaîne
            if (isset($data['restaurant_id']) && is_string($data['restaurant_id'])) {
                $data['restaurant_id'] = new ObjectId($data['restaurant_id']);
            }
            
            $result = $this->collection->updateOne(
                ['_id' => new ObjectId($id)],
                ['$set' => $data]
            );
            
            $success = $result->getModifiedCount() > 0 || $result->getMatchedCount() > 0;
            
            // Mettre à jour la note du restaurant si l'avis a été modifié
            if ($success && $review && isset($review['restaurant_id'])) {
                $restaurantModel = new Restaurant();
                $restaurantModel->updateRating((string)$review['restaurant_id']);
            }
            
            return $success;
        } catch (\Exception $e) {
            error_log("Erreur lors de la mise à jour de l'avis : " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Supprime un avis
     * 
     * @param string $id ID de l'avis
     * @return bool Statut de succès
     */
    public function delete(string $id): bool {
        try {
            // Récupérer l'avis pour savoir quel restaurant mettre à jour
            $review = $this->getById($id);
            
            $result = $this->collection->deleteOne(['_id' => new ObjectId($id)]);
            $success = $result->getDeletedCount() > 0;
            
            // Mettre à jour la note du restaurant après suppression
            if ($success && $review && isset($review['restaurant_id'])) {
                $restaurantModel = new Restaurant();
                $restaurantModel->updateRating((string)$review['restaurant_id']);
            }
            
            return $success;
        } catch (\Exception $e) {
            error_log("Erreur lors de la suppression de l'avis : " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Ajoute un vote "utile" à un avis
     * 
     * @param string $id ID de l'avis
     * @return bool Statut de succès
     */
    public function addHelpfulVote(string $id): bool {
        try {
            $result = $this->collection->updateOne(
                ['_id' => new ObjectId($id)],
                ['$inc' => ['helpful_votes' => 1]]
            );
            return $result->getModifiedCount() > 0;
        } catch (\Exception $e) {
            error_log("Erreur lors de l'ajout du vote utile : " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Ajoute une réponse du restaurant à un avis
     * 
     * @param string $id ID de l'avis
     * @param string $reply Texte de la réponse
     * @return bool Statut de succès
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
            error_log("Erreur lors de l'ajout de la réponse du restaurant : " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Calcule la note moyenne pour un restaurant
     * 
     * @param string $restaurantId ID du restaurant
     * @return float Note moyenne
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
     * Récupère les statistiques détaillées des notes pour un restaurant
     * 
     * @param string $restaurantId ID du restaurant
     * @return array Détail des notes avec moyennes
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
     * Compte les avis pour un restaurant
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
     * Récupère les avis par plage de notes
     * 
     * @param string $restaurantId ID du restaurant
     * @param int $minRating Note minimum
     * @param int $maxRating Note maximum
     * @return array Tableau d'avis
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