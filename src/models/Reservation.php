<?php
/**
 * Modèle Reservation
 * Gère les réservations de tables
 */

namespace BiomeBistro\Models;

use BiomeBistro\Config\Database;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;
use DateTime;

class Reservation {
    private $collection;
    
    public function __construct() {
        $this->collection = Database::getCollection('reservations');
    }
    
    /**
     * Récupère toutes les réservations
     * 
     * @param array $filters Filtres optionnels
     * @param int $limit Limite optionnelle
     * @return array Tableau de documents de réservations
     */
    public function getAll(array $filters = [], int $limit = 0): array {
        $options = ['sort' => ['reservation_date' => 1, 'reservation_time' => 1]];
        
        if ($limit > 0) {
            $options['limit'] = $limit;
        }
        
        return $this->collection->find($filters, $options)->toArray();
    }
    
    /**
     * Récupère une réservation par son ID
     * 
     * @param string $id ID de la réservation
     * @return array|null Document de réservation ou null
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
     * Récupère une réservation par code de confirmation
     * 
     * @param string $code Code de confirmation
     * @return array|null Document de réservation ou null
     */
    public function getByConfirmationCode(string $code): ?array {
        $result = $this->collection->findOne(['confirmation_code' => $code]);
        return $result ? (array)$result : null;
    }
    
    /**
     * Récupère les réservations par restaurant
     * 
     * @param string $restaurantId ID du restaurant
     * @param array $filters Filtres optionnels (date, status, etc.)
     * @return array Tableau de réservations
     */
    public function getByRestaurant(string $restaurantId, array $filters = []): array {
        try {
            $query = ['restaurant_id' => new ObjectId($restaurantId)];
            
            // Ajouter un filtre de date
            if (isset($filters['date'])) {
                $query['reservation_date'] = $this->convertToUTCDateTime($filters['date']);
            }
            
            // Ajouter un filtre de statut
            if (isset($filters['status'])) {
                $query['status'] = $filters['status'];
            }
            
            return $this->collection->find(
                $query,
                ['sort' => ['reservation_date' => 1, 'reservation_time' => 1]]
            )->toArray();
        } catch (\Exception $e) {
            error_log("Erreur lors de la récupération des réservations : " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Récupère les réservations par email du client
     * 
     * @param string $email Email du client
     * @return array Tableau de réservations
     */
    public function getByCustomerEmail(string $email): array {
        return $this->collection->find(
            ['customer_info.email' => $email],
            ['sort' => ['reservation_date' => -1, 'reservation_time' => -1]]
        )->toArray();
    }
    
    /**
     * Récupère les réservations à venir pour un restaurant
     * 
     * @param string $restaurantId ID du restaurant
     * @param int $limit Limite optionnelle
     * @return array Tableau de réservations à venir
     */
    public function getUpcoming(string $restaurantId, int $limit = 0): array {
        try {
            $today = new UTCDateTime();
            
            $options = [
                'sort' => ['reservation_date' => 1, 'reservation_time' => 1]
            ];
            
            if ($limit > 0) {
                $options['limit'] = $limit;
            }
            
            return $this->collection->find(
                [
                    'restaurant_id' => new ObjectId($restaurantId),
                    'reservation_date' => ['$gte' => $today],
                    'status' => ['$in' => ['pending', 'confirmed']]
                ],
                $options
            )->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }
    
    /**
     * Crée une nouvelle réservation
     * 
     * @param array $data Données de la réservation
     * @return string|null ID inséré ou null en cas d'échec
     */
    public function create(array $data): ?string {
        try {
            // Définir les valeurs par défaut
            $data['status'] = $data['status'] ?? 'confirmed';
            $data['reminder_sent'] = false;
            $data['created_at'] = new UTCDateTime();
            
            // Convertir restaurant_id en ObjectId s'il s'agit d'une chaîne
            if (isset($data['restaurant_id']) && is_string($data['restaurant_id'])) {
                $data['restaurant_id'] = new ObjectId($data['restaurant_id']);
            }
            
            // Convertir reservation_date en UTCDateTime s'il s'agit d'une chaîne
            if (isset($data['reservation_date']) && is_string($data['reservation_date'])) {
                $data['reservation_date'] = $this->convertToUTCDateTime($data['reservation_date']);
            }
            
            // Générer un code de confirmation
            $data['confirmation_code'] = $this->generateConfirmationCode();
            
            $result = $this->collection->insertOne($data);
            return (string)$result->getInsertedId();
        } catch (\Exception $e) {
            error_log("Erreur lors de la création de la réservation : " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Met à jour une réservation
     * 
     * @param string $id ID de la réservation
     * @param array $data Données mises à jour
     * @return bool Statut de succès
     */
    public function update(string $id, array $data): bool {
        try {
            // Convertir restaurant_id en ObjectId s'il est présent et est une chaîne
            if (isset($data['restaurant_id']) && is_string($data['restaurant_id'])) {
                $data['restaurant_id'] = new ObjectId($data['restaurant_id']);
            }
            
            // Convertir reservation_date en UTCDateTime s'il est présent et est une chaîne
            if (isset($data['reservation_date']) && is_string($data['reservation_date'])) {
                $data['reservation_date'] = $this->convertToUTCDateTime($data['reservation_date']);
            }
            
            $result = $this->collection->updateOne(
                ['_id' => new ObjectId($id)],
                ['$set' => $data]
            );
            return $result->getModifiedCount() > 0 || $result->getMatchedCount() > 0;
        } catch (\Exception $e) {
            error_log("Erreur lors de la mise à jour de la réservation : " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Annule une réservation
     * 
     * @param string $id ID de la réservation
     * @param string $reason Raison de l'annulation
     * @return bool Statut de succès
     */
    public function cancel(string $id, string $reason = ''): bool {
        return $this->update($id, [
            'status' => 'cancelled',
            'cancelled_at' => new UTCDateTime(),
            'cancellation_reason' => $reason
        ]);
    }
    
    /**
     * Supprime une réservation
     * 
     * @param string $id ID de la réservation
     * @return bool Statut de succès
     */
    public function delete(string $id): bool {
        try {
            $result = $this->collection->deleteOne(['_id' => new ObjectId($id)]);
            return $result->getDeletedCount() > 0;
        } catch (\Exception $e) {
            error_log("Erreur lors de la suppression de la réservation : " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Vérifie la disponibilité pour une date/heure donnée
     * 
     * @param string $restaurantId ID du restaurant
     * @param string $date Date (YYYY-MM-DD)
     * @param string $time Heure (HH:MM)
     * @return bool True si le créneau horaire est disponible
     */
    public function checkAvailability(string $restaurantId, string $date, string $time): bool {
        try {
            // Récupérer le restaurant pour vérifier la capacité
            $restaurantModel = new Restaurant();
            $restaurant = $restaurantModel->getById($restaurantId);
            
            if (!$restaurant) {
                return false;
            }
            
            $capacity = $restaurant['capacity'] ?? 50;
            
            // Compter les réservations existantes pour cette date/heure
            $reservations = $this->collection->find([
                'restaurant_id' => new ObjectId($restaurantId),
                'reservation_date' => $this->convertToUTCDateTime($date),
                'reservation_time' => $time,
                'status' => ['$in' => ['pending', 'confirmed']]
            ])->toArray();
            
            $totalGuests = 0;
            foreach ($reservations as $reservation) {
                $totalGuests += $reservation['party_size'] ?? 0;
            }
            
            // Vérification de disponibilité simple - si le total des invités < 80% de la capacité
            return $totalGuests < ($capacity * 0.8);
        } catch (\Exception $e) {
            error_log("Erreur lors de la vérification de disponibilité : " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Récupère les créneaux horaires disponibles pour une date
     * 
     * @param string $restaurantId ID du restaurant
     * @param string $date Date (YYYY-MM-DD)
     * @return array Tableau de créneaux horaires disponibles
     */
    public function getAvailableTimeSlots(string $restaurantId, string $date): array {
        // Créneaux horaires standards de restaurant (simplifié)
        $allTimeSlots = [
            '11:00', '11:30', '12:00', '12:30', '13:00', '13:30', '14:00',
            '18:00', '18:30', '19:00', '19:30', '20:00', '20:30', '21:00', '21:30'
        ];
        
        $availableSlots = [];
        
        foreach ($allTimeSlots as $time) {
            if ($this->checkAvailability($restaurantId, $date, $time)) {
                $availableSlots[] = $time;
            }
        }
        
        return $availableSlots;
    }
    
    /**
     * Marque une réservation comme enregistrée (check-in)
     * 
     * @param string $id ID de la réservation
     * @return bool Statut de succès
     */
    public function checkIn(string $id): bool {
        return $this->update($id, [
            'check_in_time' => new UTCDateTime(),
            'status' => 'completed'
        ]);
    }
    
    /**
     * Marque une réservation comme sortie (check-out)
     * 
     * @param string $id ID de la réservation
     * @return bool Statut de succès
     */
    public function checkOut(string $id): bool {
        return $this->update($id, [
            'check_out_time' => new UTCDateTime()
        ]);
    }
    
    /**
     * Compte les réservations par restaurant
     * 
     * @param string $restaurantId ID du restaurant
     * @param array $filters Filtres optionnels
     * @return int Compteur
     */
    public function countByRestaurant(string $restaurantId, array $filters = []): int {
        try {
            $query = ['restaurant_id' => new ObjectId($restaurantId)];
            
            if (isset($filters['status'])) {
                $query['status'] = $filters['status'];
            }
            
            return $this->collection->countDocuments($query);
        } catch (\Exception $e) {
            return 0;
        }
    }
    
    /**
     * Génère un code de confirmation unique
     * 
     * @return string Code de confirmation
     */
    private function generateConfirmationCode(): string {
        // Format: BIO-XX-YYYYMMDD-NNNN
        $date = date('Ymd');
        $random = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        $biomeCode = ['RF', 'DO', 'CR', 'AM', 'AT', 'TF', 'AS', 'MF'][rand(0, 7)];
        
        return "BIO-{$biomeCode}-{$date}-{$random}";
    }
    
    /**
     * Convertit une chaîne de date en UTCDateTime MongoDB
     * 
     * @param string $date Chaîne de date (YYYY-MM-DD)
     * @return UTCDateTime
     */
    private function convertToUTCDateTime(string $date): UTCDateTime {
        $datetime = new DateTime($date);
        return new UTCDateTime($datetime->getTimestamp() * 1000);
    }
}