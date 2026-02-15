<?php

namespace BiomeBistro\Config;

use MongoDB\Client;
use MongoDB\Database as MongoDatabase;

/**
 * Classe de gestion de la connexion à la base de données MongoDB
 * Implémente le pattern Singleton pour une connexion unique
 */
class Database {
    private static $client = null;
    private static $database = null;
    
    // Paramètres de connexion MongoDB
    private const HOST = 'localhost';
    private const PORT = 27017;
    private const DB_NAME = 'biomebistro';
    
    /**
     * Récupère ou crée le client MongoDB (Singleton)
     * @return Client Instance du client MongoDB
     */
    public static function getClient() {
        if (self::$client === null) {
            $uri = "mongodb://" . self::HOST . ":" . self::PORT;
            self::$client = new Client($uri);
        }
        return self::$client;
    }
    
    /**
     * Récupère ou crée l'instance de la base de données (Singleton)
     * @return MongoDatabase Instance de la base de données
     */
    public static function getDatabase() {
        if (self::$database === null) {
            self::$database = self::getClient()->selectDatabase(self::DB_NAME);
        }
        return self::$database;
    }
    
    /**
     * Récupère une collection spécifique de la base de données
     * @param string $collectionName Nom de la collection à récupérer
     * @return \MongoDB\Collection Instance de la collection
     */
    public static function getCollection($collectionName) {
        return self::getDatabase()->selectCollection($collectionName);
    }
    
    /**
     * Teste la connexion à la base de données MongoDB
     * @return bool True si la connexion réussit, false sinon
     */
    public static function testConnection() {
        try {
            self::getClient()->listDatabases();
            return true;
        } catch (\Exception $e) {
            error_log("Échec de connexion à la base de données : " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Crée tous les index nécessaires pour optimiser les performances
     * des requêtes sur les collections MongoDB
     * @return void
     */
    public static function createIndexes(): void
    {
        $db = self::getDatabase();
        
        // Index géospatial pour les requêtes basées sur la localisation
        $db->restaurants->createIndex(['location.coordinates' => '2dsphere']);
        
        // Index de performance pour les filtres courants
        $db->restaurants->createIndex(['biome_id' => 1]);
        $db->restaurants->createIndex(['average_rating' => -1]);
        $db->restaurants->createIndex(['price_range' => 1]);
        
        // Index TEXT pour la fonctionnalité de recherche textuelle
        $db->restaurants->createIndex(
            ['name' => 'text', 'description' => 'text', 'cuisine_style' => 'text'],
            ['name' => 'restaurant_text_index']
        );
        
        // Index pour les autres collections
        $db->menu_items->createIndex(['restaurant_id' => 1]);
        $db->reviews->createIndex(['restaurant_id' => 1]);
        $db->reservations->createIndex(['restaurant_id' => 1]);
    }
}