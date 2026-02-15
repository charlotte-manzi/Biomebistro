<?php

namespace BiomeBistro\Config;

use MongoDB\Client;
use MongoDB\Database as MongoDatabase;

class Database {
    private static $client = null;
    private static $database = null;
    
    private const HOST = 'localhost';
    private const PORT = 27017;
    private const DB_NAME = 'biomebistro';
    
    public static function getClient() {
        if (self::$client === null) {
            $uri = "mongodb://" . self::HOST . ":" . self::PORT;
            self::$client = new Client($uri);
        }
        return self::$client;
    }
    
    public static function getDatabase() {
        if (self::$database === null) {
            self::$database = self::getClient()->selectDatabase(self::DB_NAME);
        }
        return self::$database;
    }
    
    public static function getCollection($collectionName) {
        return self::getDatabase()->selectCollection($collectionName);
    }
    
    public static function testConnection() {
        try {
            self::getClient()->listDatabases();
            return true;
        } catch (\Exception $e) {
            error_log("Database connection failed: " . $e->getMessage());
            return false;
        }
    }
    
    public static function createIndexes(): void
{
    $db = self::getDatabase();
    
    // Geospatial index for location-based queries
    $db->restaurants->createIndex(['location.coordinates' => '2dsphere']);
    
    // Performance indexes
    $db->restaurants->createIndex(['biome_id' => 1]);
    $db->restaurants->createIndex(['average_rating' => -1]);
    $db->restaurants->createIndex(['price_range' => 1]);
    
    // TEXT INDEX for search functionality
    $db->restaurants->createIndex(
        ['name' => 'text', 'description' => 'text', 'cuisine_style' => 'text'],
        ['name' => 'restaurant_text_index']
    );
    
    // Other collection indexes
    $db->menu_items->createIndex(['restaurant_id' => 1]);
    $db->reviews->createIndex(['restaurant_id' => 1]);
    $db->reservations->createIndex(['restaurant_id' => 1]);
}
}