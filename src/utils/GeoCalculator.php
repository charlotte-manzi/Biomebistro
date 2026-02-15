<?php
/**
 * GeoCalculator - Utilitaires de calcul GPS et de distance
 * Calcule les distances entre coordonnées et gère les requêtes géospatiales
 */

namespace BiomeBistro\Utils;

class GeoCalculator {
    // Rayon de la Terre en kilomètres
    private const EARTH_RADIUS_KM = 6371;
    
    /**
     * Calcule la distance entre deux coordonnées GPS en utilisant la formule de Haversine
     * 
     * @param float $lat1 Latitude du premier point
     * @param float $lon1 Longitude du premier point
     * @param float $lat2 Latitude du second point
     * @param float $lon2 Longitude du second point
     * @return float Distance en kilomètres
     */
    public static function calculateDistance(
        float $lat1, 
        float $lon1, 
        float $lat2, 
        float $lon2
    ): float {
        // Convertir les degrés en radians
        $lat1Rad = deg2rad($lat1);
        $lon1Rad = deg2rad($lon1);
        $lat2Rad = deg2rad($lat2);
        $lon2Rad = deg2rad($lon2);
        
        // Calculer les différences
        $latDiff = $lat2Rad - $lat1Rad;
        $lonDiff = $lon2Rad - $lon1Rad;
        
        // Formule de Haversine
        $a = sin($latDiff / 2) * sin($latDiff / 2) +
             cos($lat1Rad) * cos($lat2Rad) *
             sin($lonDiff / 2) * sin($lonDiff / 2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
        // Distance en kilomètres
        $distance = self::EARTH_RADIUS_KM * $c;
        
        return round($distance, 2);
    }
    
    /**
     * Formate la distance pour l'affichage
     * 
     * @param float $distanceKm Distance en kilomètres
     * @param string $lang Langue ('fr' ou 'en')
     * @return string Chaîne de distance formatée
     */
    public static function formatDistance(float $distanceKm, string $lang = 'fr'): string {
        if ($distanceKm < 1) {
            $meters = round($distanceKm * 1000);
            return $meters . ($lang === 'fr' ? ' m' : ' m');
        }
        
        return round($distanceKm, 1) . ($lang === 'fr' ? ' km' : ' km');
    }
    
    /**
     * Trouve les restaurants dans un certain rayon à partir d'un point
     * Retourne une requête géospatiale MongoDB
     * 
     * @param float $latitude Latitude du centre
     * @param float $longitude Longitude du centre
     * @param float $radiusKm Rayon en kilomètres
     * @return array Tableau de requête MongoDB
     */
    public static function getNearbyQuery(
        float $latitude, 
        float $longitude, 
        float $radiusKm
    ): array {
        // Convertir km en mètres pour MongoDB
        $radiusMeters = $radiusKm * 1000;
        
        return [
            'location.coordinates' => [
                '$near' => [
                    '$geometry' => [
                        'type' => 'Point',
                        'coordinates' => [$longitude, $latitude] // MongoDB utilise [lon, lat]
                    ],
                    '$maxDistance' => $radiusMeters
                ]
            ]
        ];
    }
    
    /**
     * Récupère les coordonnées du centre de Paris (pour la localisation par défaut)
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
     * Valide les coordonnées GPS
     * 
     * @param float $latitude Latitude
     * @param float $longitude Longitude
     * @return bool True si valides
     */
    public static function validateCoordinates(float $latitude, float $longitude): bool {
        return ($latitude >= -90 && $latitude <= 90) && 
               ($longitude >= -180 && $longitude <= 180);
    }
    
    /**
     * Récupère l'arrondissement à partir des coordonnées (simplifié pour Paris)
     * Ceci est une version simplifiée - en production, utiliser un service de géocodage approprié
     * 
     * @param float $latitude Latitude
     * @param float $longitude Longitude
     * @return string Arrondissement (par ex., "18ème")
     */
    public static function getArrondissement(float $latitude, float $longitude): string {
        // Ceci est une correspondance simplifiée - dans une application réelle, utiliser un géocodage approprié
        // Basé sur des coordonnées approximatives des arrondissements de Paris
        $arrondissements = [
            ['lat' => 48.8566, 'lon' => 2.3522, 'arr' => '1er'],   // Centre
            ['lat' => 48.8647, 'lon' => 2.3370, 'arr' => '9ème'],  // Opéra
            ['lat' => 48.8738, 'lon' => 2.3505, 'arr' => '18ème'], // Montmartre
            ['lat' => 48.8566, 'lon' => 2.3522, 'arr' => '4ème'],  // Marais
            ['lat' => 48.8448, 'lon' => 2.3736, 'arr' => '12ème'], // Bercy
            ['lat' => 48.8534, 'lon' => 2.3488, 'arr' => '5ème'],  // Quartier Latin
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