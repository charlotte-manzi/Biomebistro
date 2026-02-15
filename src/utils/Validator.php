<?php
/**
 * Validator - Classe de validation des données
 * Fournit des méthodes de validation pour différents types de données utilisateur
 */

namespace BiomeBistro\Utils;

class Validator
{
    /**
     * Valide le format d'un email
     * 
     * @param string $email Adresse email à valider
     * @return bool True si l'email est valide
     */
    public static function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Valide le numéro de téléphone (format français)
     * 
     * @param string $phone Numéro de téléphone à valider
     * @return bool True si le numéro est valide
     */
    public static function validatePhone(string $phone): bool
    {
        // Supprimer les espaces et les tirets
        $cleaned = preg_replace('/[\s\-]/', '', $phone);
        
        // Téléphone français : +33 ou 0 suivi de 9 chiffres
        return preg_match('/^(\+33|0)[1-9]\d{8}$/', $cleaned) === 1;
    }
    
    /**
     * Valide une note (1-5)
     * 
     * @param int $rating Note à valider
     * @return bool True si la note est entre 1 et 5
     */
    public static function validateRating(int $rating): bool
    {
        return $rating >= 1 && $rating <= 5;
    }
    
    /**
     * Valide le format de date (Y-m-d)
     * 
     * @param string $date Date à valider (format YYYY-MM-DD)
     * @return bool True si la date est valide
     */
    public static function validateDate(string $date): bool
    {
        $d = \DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }
    
    /**
     * Valide le format d'heure (H:i)
     * 
     * @param string $time Heure à valider (format HH:MM)
     * @return bool True si l'heure est valide
     */
    public static function validateTime(string $time): bool
    {
        return preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $time) === 1;
    }
    
    /**
     * Valide un prix
     * 
     * @param float $price Prix à valider
     * @return bool True si le prix est positif et raisonnable
     */
    public static function validatePrice(float $price): bool
    {
        return $price >= 0 && $price <= 10000;
    }
    
    /**
     * Valide la taille d'un groupe
     * 
     * @param int $size Nombre de personnes
     * @return bool True si la taille est entre 1 et 20
     */
    public static function validatePartySize(int $size): bool
    {
        return $size >= 1 && $size <= 20;
    }
    
    /**
     * Valide un ObjectId MongoDB
     * 
     * @param string $id ID à valider
     * @return bool True si l'ID est un ObjectId MongoDB valide
     */
    public static function validateObjectId(string $id): bool
    {
        return preg_match('/^[a-f\d]{24}$/i', $id) === 1;
    }
    
    /**
     * Nettoie et sécurise une chaîne de caractères
     * 
     * @param string $input Chaîne à nettoyer
     * @return string Chaîne nettoyée et sécurisée
     */
    public static function sanitizeString(string $input): string
    {
        return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Valide les données d'une réservation
     * 
     * @param array $data Données de réservation à valider
     * @return array Tableau d'erreurs (vide si pas d'erreur)
     */
    public static function validateReservation(array $data): array
    {
        $errors = [];
        
        // Valider le nom du client
        if (empty($data['customer_info']['name'])) {
            $errors[] = "Le nom est requis";
        }
        
        // Valider l'email
        if (empty($data['customer_info']['email'])) {
            $errors[] = "L'email est requis";
        } elseif (!self::validateEmail($data['customer_info']['email'])) {
            $errors[] = "Format d'email invalide";
        }
        
        // Valider le téléphone
        if (empty($data['customer_info']['phone'])) {
            $errors[] = "Le téléphone est requis";
        } elseif (!self::validatePhone($data['customer_info']['phone'])) {
            $errors[] = "Format de téléphone invalide (utiliser +33 1 XX XX XX XX)";
        }
        
        // Valider la date
        if (empty($data['reservation_date'])) {
            $errors[] = "La date de réservation est requise";
        } elseif (!self::validateDate($data['reservation_date'])) {
            $errors[] = "Format de date invalide";
        }
        
        // Valider l'heure
        if (empty($data['reservation_time'])) {
            $errors[] = "L'heure de réservation est requise";
        }
        
        // Valider la taille du groupe
        if (empty($data['party_size']) || $data['party_size'] < 1 || $data['party_size'] > 20) {
            $errors[] = "La taille du groupe doit être entre 1 et 20";
        }
        
        return $errors;
    }
    
    /**
     * Valide les données d'un avis
     * 
     * @param array $data Données d'avis à valider
     * @return array Tableau d'erreurs (vide si pas d'erreur)
     */
    public static function validateReview(array $data): array
    {
        $errors = [];
        
        // Valider le nom du reviewer
        if (empty($data['reviewer_name'])) {
            $errors[] = "Le nom est requis";
        }
        
        // Valider l'email
        if (empty($data['reviewer_email'])) {
            $errors[] = "L'email est requis";
        } elseif (!self::validateEmail($data['reviewer_email'])) {
            $errors[] = "Format d'email invalide";
        }
        
        // Valider la note
        if (empty($data['rating']) || !self::validateRating($data['rating'])) {
            $errors[] = "Veuillez sélectionner une note (1-5 étoiles)";
        }
        
        // Valider le titre
        if (empty($data['title'])) {
            $errors[] = "Le titre de l'avis est requis";
        }
        
        // Valider le commentaire
        if (empty($data['comment'])) {
            $errors[] = "Le commentaire de l'avis est requis";
        } elseif (strlen($data['comment']) < 10) {
            $errors[] = "Le commentaire doit contenir au moins 10 caractères";
        }
        
        return $errors;
    }
}