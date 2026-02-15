<?php
/**
 * Language/Translation System for BiomeBistro
 * Supports French (default) and English
 */

namespace BiomeBistro\Utils;

class Language {
    private static string $currentLang = 'fr'; // Default language
    private static array $translations = [];
    
    /**
     * Initialize translations
     */
    public static function init(): void {
        self::$translations = [
            'fr' => [
                // Navigation
                'home' => 'Accueil',
                'biomes' => 'Biomes',
                'restaurants' => 'Restaurants',
                'reservations' => 'Réservations',
                'about' => 'À propos',
                'contact' => 'Contact',
                
                // Common
                'search' => 'Rechercher',
                'search_placeholder' => 'Rechercher un restaurant, un biome, un plat...',
                'filter' => 'Filtrer',
                'sort' => 'Trier',
                'view_more' => 'Voir plus',
                'view_details' => 'Voir les détails',
                'back' => 'Retour',
                'next' => 'Suivant',
                'previous' => 'Précédent',
                'submit' => 'Envoyer',
                'cancel' => 'Annuler',
                'confirm' => 'Confirmer',
                'save' => 'Enregistrer',
                'edit' => 'Modifier',
                'delete' => 'Supprimer',
                'add' => 'Ajouter',
                
                // Home page
                'welcome_title' => 'Goûtez les Écosystèmes du Monde',
                'welcome_subtitle' => 'Découvrez 8 restaurants uniques, chacun inspiré d\'un biome différent',
                'explore_biomes' => 'Explorer les Biomes',
                'make_reservation' => 'Réserver une Table',
                'top_rated' => 'Restaurants les Mieux Notés',
                'latest_reviews' => 'Derniers Avis',
                'explore_by_biome' => 'Explorer par Biome',
                
                // Restaurant
                'restaurant' => 'Restaurant',
                'restaurants_count' => 'restaurants',
                'cuisine_style' => 'Style de Cuisine',
                'price_range' => 'Gamme de Prix',
                'capacity' => 'Capacité',
                'rating' => 'Note',
                'reviews' => 'Avis',
                'menu' => 'Menu',
                'opening_hours' => 'Horaires d\'Ouverture',
                'location' => 'Localisation',
                'contact' => 'Contact',
                'atmosphere' => 'Ambiance',
                'features' => 'Caractéristiques',
                'sustainability_score' => 'Score de Durabilité',
                'open_now' => 'Ouvert maintenant',
                'closed_now' => 'Fermé',
                'similar_restaurants' => 'Restaurants Similaires',
                
                // Menu
                'menu_categories' => 'Catégories du Menu',
                'appetizer' => 'Entrée',
                'main_course' => 'Plat Principal',
                'dessert' => 'Dessert',
                'beverage' => 'Boisson',
                'special' => 'Spécialité',
                'ingredients' => 'Ingrédients',
                'allergens' => 'Allergènes',
                'spice_level' => 'Niveau d\'Épices',
                'preparation_time' => 'Temps de Préparation',
                'signature_dish' => 'Plat Signature',
                'available' => 'Disponible',
                'not_available' => 'Non Disponible',
                
                // Reviews
                'write_review' => 'Écrire un Avis',
                'your_rating' => 'Votre Note',
                'your_review' => 'Votre Avis',
                'food_quality' => 'Qualité de la Nourriture',
                'service' => 'Service',
                'ambiance' => 'Ambiance',
                'value_for_money' => 'Rapport Qualité-Prix',
                'cleanliness' => 'Propreté',
                'review_title' => 'Titre de l\'Avis',
                'review_comment' => 'Commentaire',
                'helpful' => 'Utile',
                'people_found_helpful' => 'personnes ont trouvé cet avis utile',
                'verified_visit' => 'Visite Vérifiée',
                'restaurant_response' => 'Réponse du Restaurant',
                
                // Reservations
                'book_table' => 'Réserver une Table',
                'reservation_date' => 'Date de Réservation',
                'reservation_time' => 'Heure',
                'party_size' => 'Nombre de Personnes',
                'table_preference' => 'Préférence de Table',
                'special_requests' => 'Demandes Spéciales',
                'dietary_restrictions' => 'Restrictions Alimentaires',
                'occasion' => 'Occasion',
                'confirmation_code' => 'Code de Confirmation',
                'reservation_confirmed' => 'Réservation Confirmée',
                'reservation_pending' => 'Réservation en Attente',
                'reservation_cancelled' => 'Réservation Annulée',
                'my_reservations' => 'Mes Réservations',
                
                // Biomes
                'biome' => 'Biome',
                'climate' => 'Climat',
                'temperature' => 'Température',
                'humidity' => 'Humidité',
                'native_ingredients' => 'Ingrédients Typiques',
                'characteristics' => 'Caractéristiques',
                'tropical_rainforest' => 'Forêt Tropicale',
                'desert_oasis' => 'Oasis Désertique',
                'coral_reef' => 'Récif Corallien',
                'alpine_mountain' => 'Montagne Alpine',
                'arctic_tundra' => 'Toundra Arctique',
                'temperate_forest' => 'Forêt Tempérée',
                'african_savanna' => 'Savane Africaine',
                'mushroom_forest' => 'Forêt de Champignons',
                
                // Filters & Sort
                'filter_by' => 'Filtrer par',
                'sort_by' => 'Trier par',
                'all' => 'Tous',
                'price_low_high' => 'Prix: Croissant',
                'price_high_low' => 'Prix: Décroissant',
                'rating_high_low' => 'Note: Meilleure d\'abord',
                'distance' => 'Distance',
                'newest' => 'Plus Récent',
                'most_popular' => 'Plus Populaire',
                
                // Days of week
                'monday' => 'Lundi',
                'tuesday' => 'Mardi',
                'wednesday' => 'Mercredi',
                'thursday' => 'Jeudi',
                'friday' => 'Vendredi',
                'saturday' => 'Samedi',
                'sunday' => 'Dimanche',
                
                // Months
                'january' => 'Janvier',
                'february' => 'Février',
                'march' => 'Mars',
                'april' => 'Avril',
                'may' => 'Mai',
                'june' => 'Juin',
                'july' => 'Juillet',
                'august' => 'Août',
                'september' => 'Septembre',
                'october' => 'Octobre',
                'november' => 'Novembre',
                'december' => 'Décembre',
                
                // Footer
                'about_us' => 'À Propos de Nous',
                'terms' => 'Mentions Légales',
                'privacy' => 'Confidentialité',
                'careers' => 'Carrières',
                'follow_us' => 'Suivez-nous',
                'copyright' => '© 2025 BiomeBistro - Tous droits réservés',
            ],
            
            'en' => [
                // Navigation
                'home' => 'Home',
                'biomes' => 'Biomes',
                'restaurants' => 'Restaurants',
                'reservations' => 'Reservations',
                'about' => 'About',
                'contact' => 'Contact',
                
                // Common
                'search' => 'Search',
                'search_placeholder' => 'Search for a restaurant, biome, dish...',
                'filter' => 'Filter',
                'sort' => 'Sort',
                'view_more' => 'View More',
                'view_details' => 'View Details',
                'back' => 'Back',
                'next' => 'Next',
                'previous' => 'Previous',
                'submit' => 'Submit',
                'cancel' => 'Cancel',
                'confirm' => 'Confirm',
                'save' => 'Save',
                'edit' => 'Edit',
                'delete' => 'Delete',
                'add' => 'Add',
                
                // Home page
                'welcome_title' => 'Taste the World\'s Ecosystems',
                'welcome_subtitle' => 'Discover 8 unique restaurants, each inspired by a different biome',
                'explore_biomes' => 'Explore Biomes',
                'make_reservation' => 'Make a Reservation',
                'top_rated' => 'Top Rated Restaurants',
                'latest_reviews' => 'Latest Reviews',
                'explore_by_biome' => 'Explore by Biome',
                
                // Restaurant
                'restaurant' => 'Restaurant',
                'restaurants_count' => 'restaurants',
                'cuisine_style' => 'Cuisine Style',
                'price_range' => 'Price Range',
                'capacity' => 'Capacity',
                'rating' => 'Rating',
                'reviews' => 'Reviews',
                'menu' => 'Menu',
                'opening_hours' => 'Opening Hours',
                'location' => 'Location',
                'contact' => 'Contact',
                'atmosphere' => 'Atmosphere',
                'features' => 'Features',
                'sustainability_score' => 'Sustainability Score',
                'open_now' => 'Open Now',
                'closed_now' => 'Closed',
                'similar_restaurants' => 'Similar Restaurants',
                
                // Menu
                'menu_categories' => 'Menu Categories',
                'appetizer' => 'Appetizer',
                'main_course' => 'Main Course',
                'dessert' => 'Dessert',
                'beverage' => 'Beverage',
                'special' => 'Special',
                'ingredients' => 'Ingredients',
                'allergens' => 'Allergens',
                'spice_level' => 'Spice Level',
                'preparation_time' => 'Preparation Time',
                'signature_dish' => 'Signature Dish',
                'available' => 'Available',
                'not_available' => 'Not Available',
                
                // Reviews
                'write_review' => 'Write a Review',
                'your_rating' => 'Your Rating',
                'your_review' => 'Your Review',
                'food_quality' => 'Food Quality',
                'service' => 'Service',
                'ambiance' => 'Ambiance',
                'value_for_money' => 'Value for Money',
                'cleanliness' => 'Cleanliness',
                'review_title' => 'Review Title',
                'review_comment' => 'Comment',
                'helpful' => 'Helpful',
                'people_found_helpful' => 'people found this helpful',
                'verified_visit' => 'Verified Visit',
                'restaurant_response' => 'Restaurant Response',
                
                // Reservations
                'book_table' => 'Book a Table',
                'reservation_date' => 'Reservation Date',
                'reservation_time' => 'Time',
                'party_size' => 'Party Size',
                'table_preference' => 'Table Preference',
                'special_requests' => 'Special Requests',
                'dietary_restrictions' => 'Dietary Restrictions',
                'occasion' => 'Occasion',
                'confirmation_code' => 'Confirmation Code',
                'reservation_confirmed' => 'Reservation Confirmed',
                'reservation_pending' => 'Reservation Pending',
                'reservation_cancelled' => 'Reservation Cancelled',
                'my_reservations' => 'My Reservations',
                
                // Biomes
                'biome' => 'Biome',
                'climate' => 'Climate',
                'temperature' => 'Temperature',
                'humidity' => 'Humidity',
                'native_ingredients' => 'Native Ingredients',
                'characteristics' => 'Characteristics',
                'tropical_rainforest' => 'Tropical Rainforest',
                'desert_oasis' => 'Desert Oasis',
                'coral_reef' => 'Coral Reef',
                'alpine_mountain' => 'Alpine Mountain',
                'arctic_tundra' => 'Arctic Tundra',
                'temperate_forest' => 'Temperate Forest',
                'african_savanna' => 'African Savanna',
                'mushroom_forest' => 'Mushroom Forest',
                
                // Filters & Sort
                'filter_by' => 'Filter by',
                'sort_by' => 'Sort by',
                'all' => 'All',
                'price_low_high' => 'Price: Low to High',
                'price_high_low' => 'Price: High to Low',
                'rating_high_low' => 'Rating: Highest First',
                'distance' => 'Distance',
                'newest' => 'Newest',
                'most_popular' => 'Most Popular',
                
                // Days of week
                'monday' => 'Monday',
                'tuesday' => 'Tuesday',
                'wednesday' => 'Wednesday',
                'thursday' => 'Thursday',
                'friday' => 'Friday',
                'saturday' => 'Saturday',
                'sunday' => 'Sunday',
                
                // Months
                'january' => 'January',
                'february' => 'February',
                'march' => 'March',
                'april' => 'April',
                'may' => 'May',
                'june' => 'June',
                'july' => 'July',
                'august' => 'August',
                'september' => 'September',
                'october' => 'October',
                'november' => 'November',
                'december' => 'December',
                
                // Footer
                'about_us' => 'About Us',
                'terms' => 'Terms & Conditions',
                'privacy' => 'Privacy Policy',
                'careers' => 'Careers',
                'follow_us' => 'Follow Us',
                'copyright' => '© 2025 BiomeBistro - All rights reserved',
            ]
        ];
    }
    
    /**
     * Set current language
     */
    public static function setLanguage(string $lang): void {
        if (in_array($lang, ['fr', 'en'])) {
            self::$currentLang = $lang;
            $_SESSION['lang'] = $lang;
        }
    }
    
    /**
     * Get current language
     */
    public static function getCurrentLanguage(): string {
        return self::$currentLang;
    }
    
    /**
     * Get translation for a key
     */
    public static function get(string $key): string {
        if (empty(self::$translations)) {
            self::init();
        }
        
        return self::$translations[self::$currentLang][$key] ?? $key;
    }
    
    /**
     * Alias for get() - shorter syntax
     */
    public static function t(string $key): string {
        return self::get($key);
    }
}
