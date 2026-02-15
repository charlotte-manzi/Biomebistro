<?php

namespace BiomeBistro\Utils;

class Validator
{
    /**
     * Validate email format
     */
    public static function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Validate phone number (French format)
     */
    public static function validatePhone(string $phone): bool
    {
        // Remove spaces and dashes
        $cleaned = preg_replace('/[\s\-]/', '', $phone);
        
        // French phone: +33 or 0 followed by 9 digits
        return preg_match('/^(\+33|0)[1-9]\d{8}$/', $cleaned) === 1;
    }
    
    /**
     * Validate rating (1-5)
     */
    public static function validateRating(int $rating): bool
    {
        return $rating >= 1 && $rating <= 5;
    }
    
    /**
     * Validate date format (Y-m-d)
     */
    public static function validateDate(string $date): bool
    {
        $d = \DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }
    
    /**
     * Validate time format (H:i)
     */
    public static function validateTime(string $time): bool
    {
        return preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $time) === 1;
    }
    
    /**
     * Validate price
     */
    public static function validatePrice(float $price): bool
    {
        return $price >= 0 && $price <= 10000;
    }
    
    /**
     * Validate party size
     */
    public static function validatePartySize(int $size): bool
    {
        return $size >= 1 && $size <= 20;
    }
    
    /**
     * Validate MongoDB ObjectId
     */
    public static function validateObjectId(string $id): bool
    {
        return preg_match('/^[a-f\d]{24}$/i', $id) === 1;
    }
    
    /**
     * Sanitize string input
     */
    public static function sanitizeString(string $input): string
    {
        return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Validate reservation data
     */
    public static function validateReservation(array $data): array
    {
        $errors = [];
        
        // Validate customer name
        if (empty($data['customer_info']['name'])) {
            $errors[] = "Name is required";
        }
        
        // Validate email
        if (empty($data['customer_info']['email'])) {
            $errors[] = "Email is required";
        } elseif (!self::validateEmail($data['customer_info']['email'])) {
            $errors[] = "Invalid email format";
        }
        
        // Validate phone
        if (empty($data['customer_info']['phone'])) {
            $errors[] = "Phone is required";
        } elseif (!self::validatePhone($data['customer_info']['phone'])) {
            $errors[] = "Invalid phone format (use +33 1 XX XX XX XX)";
        }
        
        // Validate date
        if (empty($data['reservation_date'])) {
            $errors[] = "Reservation date is required";
        } elseif (!self::validateDate($data['reservation_date'])) {
            $errors[] = "Invalid date format";
        }
        
        // Validate time
        if (empty($data['reservation_time'])) {
            $errors[] = "Reservation time is required";
        }
        
        // Validate party size
        if (empty($data['party_size']) || $data['party_size'] < 1 || $data['party_size'] > 20) {
            $errors[] = "Party size must be between 1 and 20";
        }
        
        return $errors;
    }
    
    /**
     * Validate review data
     */
    public static function validateReview(array $data): array
    {
        $errors = [];
        
        // Validate reviewer name
        if (empty($data['reviewer_name'])) {
            $errors[] = "Name is required";
        }
        
        // Validate email
        if (empty($data['reviewer_email'])) {
            $errors[] = "Email is required";
        } elseif (!self::validateEmail($data['reviewer_email'])) {
            $errors[] = "Invalid email format";
        }
        
        // Validate rating
        if (empty($data['rating']) || !self::validateRating($data['rating'])) {
            $errors[] = "Please select a rating (1-5 stars)";
        }
        
        // Validate title
        if (empty($data['title'])) {
            $errors[] = "Review title is required";
        }
        
        // Validate comment
        if (empty($data['comment'])) {
            $errors[] = "Review comment is required";
        } elseif (strlen($data['comment']) < 10) {
            $errors[] = "Review comment must be at least 10 characters";
        }
        
        return $errors;
    }
}