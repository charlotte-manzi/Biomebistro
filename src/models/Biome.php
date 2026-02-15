<?php
/**
 * Modèle Biome
 * Représente les types d'écosystèmes (Tropical Rainforest, Desert, etc.)
 */

namespace BiomeBistro\Models;

use BiomeBistro\Config\Database;
use MongoDB\BSON\ObjectId;

class Biome {
    private $collection;
    
    public function __construct() {
        $this->collection = Database::getCollection('biomes');
    }
    
    /**
     * Récupère tous les biomes
     * 
     * @return array Tableau de documents biomes
     */
    public function getAll(): array {
        return $this->collection->find()->toArray();
    }
    
    /**
     * Récupère un biome par son ID
     * 
     * @param string $id ID du biome
     * @return array|null Document biome ou null si non trouvé
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
     * Récupère un biome par son nom
     * 
     * @param string $name Nom du biome
     * @return array|null Document biome ou null si non trouvé
     */
    public function getByName(string $name): ?array {
        $result = $this->collection->findOne(['name' => $name]);
        return $result ? (array)$result : null;
    }
    
    /**
     * Crée un nouveau biome
     * 
     * @param array $data Données du biome
     * @return string|null ID inséré ou null en cas d'échec
     */
    public function create(array $data): ?string {
        try {
            $result = $this->collection->insertOne($data);
            return (string)$result->getInsertedId();
        } catch (\Exception $e) {
            error_log("Erreur lors de la création du biome : " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Met à jour un biome
     * 
     * @param string $id ID du biome
     * @param array $data Données mises à jour
     * @return bool Statut de succès
     */
    public function update(string $id, array $data): bool {
        try {
            $result = $this->collection->updateOne(
                ['_id' => new ObjectId($id)],
                ['$set' => $data]
            );
            return $result->getModifiedCount() > 0;
        } catch (\Exception $e) {
            error_log("Erreur lors de la mise à jour du biome : " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Supprime un biome
     * 
     * @param string $id ID du biome
     * @return bool Statut de succès
     */
    public function delete(string $id): bool {
        try {
            $result = $this->collection->deleteOne(['_id' => new ObjectId($id)]);
            return $result->getDeletedCount() > 0;
        } catch (\Exception $e) {
            error_log("Erreur lors de la suppression du biome : " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Compte le nombre de restaurants dans ce biome
     * 
     * @param string $biomeId ID du biome
     * @return int Nombre de restaurants
     */
    public function countRestaurants(string $biomeId): int {
        $restaurantModel = new Restaurant();
        return $restaurantModel->countByBiome($biomeId);
    }
    
    /**
     * Récupère tous les biomes avec leur nombre de restaurants
     * 
     * @return array Tableau de biomes avec compteurs de restaurants
     */
    public function getAllWithCounts(): array {
        $biomes = $this->getAll();
        $restaurantModel = new Restaurant();
        
        foreach ($biomes as &$biome) {
            $biome['restaurant_count'] = $restaurantModel->countByBiome((string)$biome['_id']);
        }
        
        return $biomes;
    }
}