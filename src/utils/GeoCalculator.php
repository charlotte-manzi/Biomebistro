<?php
/**
 * GeoCalculator - GPS and distance calculation utilities
 * Calculates distances between coordinates and handles geospatial queries
 */

namespace BiomeBistro\Utils;

class GeoCalculator {
    // Earth's radius in kilometers
    private const EARTH_RADIUS_KM = 6371;
    
    /**
     * Calculate distance between two GPS coordinates using Haversine formula
     * 
     * @param float $lat1 Latitude of first point
     * @param float $lon1 Longitude of first point
     * @param float $lat2 Latitude of second point
     * @param float $lon2 Longitude of second point
     * @return float Distance in kilometers
     */
    public static function calculateDistance(
        float $lat1, 
        float $lon1, 
        float $lat2, 
        float $lon2
    ): float {
        // Convert degrees to radians
        $lat1Rad = deg2rad($lat1);
        $lon1Rad = deg2rad($lon1);
        $lat2Rad = deg2rad($lat2);
        $lon2Rad = deg2rad($lon2);
        
        // Calculate differences
        $latDiff = $lat2Rad - $lat1Rad;
        $lonDiff = $lon2Rad - $lon1Rad;
        
        // Haversine formula
        $a = sin($latDiff / 2) * sin($latDiff / 2) +
             cos($lat1Rad) * cos($lat2Rad) *
             sin($lonDiff / 2) * sin($lonDiff / 2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
        // Distance in kilometers
        $distance = self::EARTH_RADIUS_KM * $c;
        
        return round($distance, 2);
    }
    
    /**
     * Format distance for display
     * 
     * @param float $distanceKm Distance in kilometers
     * @param string $lang Language ('fr' or 'en')
     * @return string Formatted distance string
     */
    public static function formatDistance(float $distanceKm, string $lang = 'fr'): string {
        if ($distanceKm < 1) {
            $meters = round($distanceKm * 1000);
            return $meters . ($lang === 'fr' ? ' m' : ' m');
        }
        
        return round($distanceKm, 1) . ($lang === 'fr' ? ' km' : ' km');
    }
    
    /**
     * Find restaurants within a certain radius from a point
     * Returns MongoDB geospatial query
     * 
     * @param float $latitude Center latitude
     * @param float $longitude Center longitude
     * @param float $radiusKm Radius in kilometers
     * @return array MongoDB query array
     */
    public static function getNearbyQuery(
        float $latitude, 
        float $longitude, 
        float $radiusKm
    ): array {
        // Convert km to meters for MongoDB
        $radiusMeters = $radiusKm * 1000;
        
        return [
            'location.coordinates' => [
                '$near' => [
                    '$geometry' => [
                        'type' => 'Point',
                        'coordinates' => [$longitude, $latitude] // MongoDB uses [lon, lat]
                    ],
                    '$maxDistance' => $radiusMeters
                ]
            ]
        ];
    }
    
    /**
     * Get Paris center coordinates (for default location)
     * 
     * @return array ['lat' => float, 'lon' => float]
     */
    public static function getParisCenter(): array {
        return [
            'lat' => 48.8566,
            'lon' => 2.3522
        ];
    }
    
    /**
     * Validate GPS coordinates
     * 
     * @param float $latitude
     * @param float $longitude
     * @return bool
     */
    public static function validateCoordinates(float $latitude, float $longitude): bool {
        return ($latitude >= -90 && $latitude <= 90) && 
               ($longitude >= -180 && $longitude <= 180);
    }
    
    /**
     * Get arrondissement from coordinates (simplified for Paris)
     * This is a simplified version - in production, you'd use a proper geocoding service
     * 
     * @param float $latitude
     * @param float $longitude
     * @return string Arrondissement (e.g., "18ème")
     */
    public static function getArrondissement(float $latitude, float $longitude): string {
        // This is a simplified mapping - in real application, use proper geocoding
        // Based on rough Paris arrondissement coordinates
        $arrondissements = [
            ['lat' => 48.8566, 'lon' => 2.3522, 'arr' => '1er'],   // Center
            ['lat' => 48.8647, 'lon' => 2.3370, 'arr' => '9ème'],  // Opera
            ['lat' => 48.8738, 'lon' => 2.3505, 'arr' => '18ème'], // Montmartre
            ['lat' => 48.8566, 'lon' => 2.3522, 'arr' => '4ème'],  // Marais
            ['lat' => 48.8448, 'lon' => 2.3736, 'arr' => '12ème'], // Bercy
            ['lat' => 48.8534, 'lon' => 2.3488, 'arr' => '5ème'],  // Latin Quarter
            ['lat' => 48.8422, 'lon' => 2.3219, 'arr' => '14ème'], // Montparnasse
            ['lat' => 48.8738, 'lon' => 2.2950, 'arr' => '17ème'], // Batignolles
        ];
        
        $minDistance = PHP_FLOAT_MAX;
        $closestArr = '1er';
        
        foreach ($arrondissements as $arr) {
            $distance = self::calculateDistance($latitude, $longitude, $arr['lat'], $arr['lon']);
            if ($distance < $minDistance) {
                $minDistance = $distance;
                $closestArr = $arr['arr'];
            }
        }
        
        return $closestArr;
    }
}
