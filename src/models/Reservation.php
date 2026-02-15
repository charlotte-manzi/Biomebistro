<?php
/**
 * Reservation Model
 * Handles table reservations/bookings
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
     * Get all reservations
     * 
     * @param array $filters Optional filters
     * @param int $limit Optional limit
     * @return array Array of reservation documents
     */
    public function getAll(array $filters = [], int $limit = 0): array {
        $options = ['sort' => ['reservation_date' => 1, 'reservation_time' => 1]];
        
        if ($limit > 0) {
            $options['limit'] = $limit;
        }
        
        return $this->collection->find($filters, $options)->toArray();
    }
    
    /**
     * Get reservation by ID
     * 
     * @param string $id Reservation ID
     * @return array|null Reservation document or null
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
     * Get reservation by confirmation code
     * 
     * @param string $code Confirmation code
     * @return array|null Reservation document or null
     */
    public function getByConfirmationCode(string $code): ?array {
        $result = $this->collection->findOne(['confirmation_code' => $code]);
        return $result ? (array)$result : null;
    }
    
    /**
     * Get reservations by restaurant
     * 
     * @param string $restaurantId Restaurant ID
     * @param array $filters Optional filters (date, status, etc.)
     * @return array Array of reservations
     */
    public function getByRestaurant(string $restaurantId, array $filters = []): array {
        try {
            $query = ['restaurant_id' => new ObjectId($restaurantId)];
            
            // Add date filter
            if (isset($filters['date'])) {
                $query['reservation_date'] = $this->convertToUTCDateTime($filters['date']);
            }
            
            // Add status filter
            if (isset($filters['status'])) {
                $query['status'] = $filters['status'];
            }
            
            return $this->collection->find(
                $query,
                ['sort' => ['reservation_date' => 1, 'reservation_time' => 1]]
            )->toArray();
        } catch (\Exception $e) {
            error_log("Error getting reservations: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get reservations by customer email
     * 
     * @param string $email Customer email
     * @return array Array of reservations
     */
    public function getByCustomerEmail(string $email): array {
        return $this->collection->find(
            ['customer_info.email' => $email],
            ['sort' => ['reservation_date' => -1]]
        )->toArray();
    }
    
    /**
     * Get upcoming reservations for a restaurant
     * 
     * @param string $restaurantId Restaurant ID
     * @param int $limit Optional limit
     * @return array Array of upcoming reservations
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
     * Create a new reservation
     * 
     * @param array $data Reservation data
     * @return string|null Inserted ID or null on failure
     */
    public function create(array $data): ?string {
        try {
            // Set default values
            $data['status'] = $data['status'] ?? 'confirmed';
            $data['reminder_sent'] = false;
            $data['created_at'] = new UTCDateTime();
            
            // Convert restaurant_id to ObjectId if it's a string
            if (isset($data['restaurant_id']) && is_string($data['restaurant_id'])) {
                $data['restaurant_id'] = new ObjectId($data['restaurant_id']);
            }
            
            // Convert reservation_date to UTCDateTime if it's a string
            if (isset($data['reservation_date']) && is_string($data['reservation_date'])) {
                $data['reservation_date'] = $this->convertToUTCDateTime($data['reservation_date']);
            }
            
            // Generate confirmation code
            $data['confirmation_code'] = $this->generateConfirmationCode();
            
            $result = $this->collection->insertOne($data);
            return (string)$result->getInsertedId();
        } catch (\Exception $e) {
            error_log("Error creating reservation: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Update a reservation
     * 
     * @param string $id Reservation ID
     * @param array $data Updated data
     * @return bool Success status
     */
    public function update(string $id, array $data): bool {
        try {
            // Convert restaurant_id to ObjectId if present and is string
            if (isset($data['restaurant_id']) && is_string($data['restaurant_id'])) {
                $data['restaurant_id'] = new ObjectId($data['restaurant_id']);
            }
            
            // Convert reservation_date to UTCDateTime if present and is string
            if (isset($data['reservation_date']) && is_string($data['reservation_date'])) {
                $data['reservation_date'] = $this->convertToUTCDateTime($data['reservation_date']);
            }
            
            $result = $this->collection->updateOne(
                ['_id' => new ObjectId($id)],
                ['$set' => $data]
            );
            return $result->getModifiedCount() > 0 || $result->getMatchedCount() > 0;
        } catch (\Exception $e) {
            error_log("Error updating reservation: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Cancel a reservation
     * 
     * @param string $id Reservation ID
     * @param string $reason Cancellation reason
     * @return bool Success status
     */
    public function cancel(string $id, string $reason = ''): bool {
        return $this->update($id, [
            'status' => 'cancelled',
            'cancelled_at' => new UTCDateTime(),
            'cancellation_reason' => $reason
        ]);
    }
    
    /**
     * Delete a reservation
     * 
     * @param string $id Reservation ID
     * @return bool Success status
     */
    public function delete(string $id): bool {
        try {
            $result = $this->collection->deleteOne(['_id' => new ObjectId($id)]);
            return $result->getDeletedCount() > 0;
        } catch (\Exception $e) {
            error_log("Error deleting reservation: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Check availability for a given date/time
     * 
     * @param string $restaurantId Restaurant ID
     * @param string $date Date (YYYY-MM-DD)
     * @param string $time Time (HH:MM)
     * @return bool True if time slot is available
     */
    public function checkAvailability(string $restaurantId, string $date, string $time): bool {
        try {
            // Get restaurant to check capacity
            $restaurantModel = new Restaurant();
            $restaurant = $restaurantModel->getById($restaurantId);
            
            if (!$restaurant) {
                return false;
            }
            
            $capacity = $restaurant['capacity'] ?? 50;
            
            // Count existing reservations for this date/time
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
            
            // Simple availability check - if total guests < 80% of capacity
            return $totalGuests < ($capacity * 0.8);
        } catch (\Exception $e) {
            error_log("Error checking availability: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get available time slots for a date
     * 
     * @param string $restaurantId Restaurant ID
     * @param string $date Date (YYYY-MM-DD)
     * @return array Array of available time slots
     */
    public function getAvailableTimeSlots(string $restaurantId, string $date): array {
        // Standard restaurant time slots (simplified)
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
     * Mark reservation as checked in
     * 
     * @param string $id Reservation ID
     * @return bool Success status
     */
    public function checkIn(string $id): bool {
        return $this->update($id, [
            'check_in_time' => new UTCDateTime(),
            'status' => 'completed'
        ]);
    }
    
    /**
     * Mark reservation as checked out
     * 
     * @param string $id Reservation ID
     * @return bool Success status
     */
    public function checkOut(string $id): bool {
        return $this->update($id, [
            'check_out_time' => new UTCDateTime()
        ]);
    }
    
    /**
     * Count reservations by restaurant
     * 
     * @param string $restaurantId Restaurant ID
     * @param array $filters Optional filters
     * @return int Count
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
     * Generate unique confirmation code
     * 
     * @return string Confirmation code
     */
    private function generateConfirmationCode(): string {
        // Format: BIO-XX-YYYYMMDD-NNNN
        $date = date('Ymd');
        $random = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        $biomeCode = ['RF', 'DO', 'CR', 'AM', 'AT', 'TF', 'AS', 'MF'][rand(0, 7)];
        
        return "BIO-{$biomeCode}-{$date}-{$random}";
    }
    
    /**
     * Convert date string to MongoDB UTCDateTime
     * 
     * @param string $date Date string (YYYY-MM-DD)
     * @return UTCDateTime
     */
    private function convertToUTCDateTime(string $date): UTCDateTime {
        $datetime = new DateTime($date);
        return new UTCDateTime($datetime->getTimestamp() * 1000);
    }
}
