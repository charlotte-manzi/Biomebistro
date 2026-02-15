<?php

namespace BiomeBistro\Tests;

use PHPUnit\Framework\TestCase;
use BiomeBistro\Models\Biome;
use BiomeBistro\Config\Database;

/**
 * Tests unitaires pour le modèle Biome
 */
class BiomeTest extends TestCase
{
    private $biomeModel;
    private $testBiomeId;

    protected function setUp(): void
    {
        $this->biomeModel = new Biome();
    }

    protected function tearDown(): void
    {
        // Nettoyer les données de test
        if ($this->testBiomeId) {
            $this->biomeModel->delete($this->testBiomeId);
        }
    }

    /**
     * Teste la création d'un nouveau biome
     */
    public function testCreateBiome()
    {
        $testData = [
            'name' => 'Test Biome',
            'description' => 'A test biome for unit testing',
            'climate' => [
                'temperature_range' => '20-30°C',
                'humidity' => '60-80%',
                'rainfall' => 'Moderate'
            ],
            'color_theme' => '#27AE60',
            'icon' => '🧪',
            'native_ingredients' => ['test_ingredient_1', 'test_ingredient_2'],
            'characteristics' => ['test_char_1', 'test_char_2'],
            'season_best' => 'All year'
        ];

        $this->testBiomeId = $this->biomeModel->create($testData);

        $this->assertNotNull($this->testBiomeId);
        $this->assertIsString($this->testBiomeId);
    }

    /**
     * Teste la récupération d'un biome par son ID
     */
    public function testGetBiomeById()
    {
        // D'abord créer un biome de test
        $testData = [
            'name' => 'Test Biome Retrieval',
            'description' => 'Testing retrieval',
            'climate' => [
                'temperature_range' => '10-20°C',
                'humidity' => '50-70%',
                'rainfall' => 'Low'
            ],
            'color_theme' => '#3498DB',
            'icon' => '🔬',
            'native_ingredients' => ['ingredient_a'],
            'characteristics' => ['char_a'],
            'season_best' => 'Spring'
        ];

        $this->testBiomeId = $this->biomeModel->create($testData);
        
        // Le récupérer
        $biome = $this->biomeModel->getById($this->testBiomeId);

        $this->assertNotNull($biome);
        $this->assertEquals('Test Biome Retrieval', $biome['name']);
        $this->assertEquals('Testing retrieval', $biome['description']);
        $this->assertEquals('#3498DB', $biome['color_theme']);
    }

    /**
     * Teste la récupération de tous les biomes
     */
    public function testGetAllBiomes()
    {
        $biomes = $this->biomeModel->getAll();

        $this->assertIsArray($biomes);
        $this->assertGreaterThan(0, count($biomes));
        
        // Vérifier la structure du premier biome
        if (count($biomes) > 0) {
            $firstBiome = $biomes[0];
            $this->assertArrayHasKey('name', $firstBiome);
            $this->assertArrayHasKey('description', $firstBiome);
            $this->assertArrayHasKey('climate', $firstBiome);
            $this->assertArrayHasKey('color_theme', $firstBiome);
        }
    }

    /**
     * Teste la mise à jour d'un biome
     */
    public function testUpdateBiome()
    {
        // Créer un biome de test
        $testData = [
            'name' => 'Original Name',
            'description' => 'Original description',
            'climate' => [
                'temperature_range' => '15-25°C',
                'humidity' => '55-75%',
                'rainfall' => 'Medium'
            ],
            'color_theme' => '#E74C3C',
            'icon' => '🌡️',
            'native_ingredients' => ['orig_ingredient'],
            'characteristics' => ['orig_char'],
            'season_best' => 'Summer'
        ];

        $this->testBiomeId = $this->biomeModel->create($testData);

        // Le mettre à jour
        $updateData = [
            'name' => 'Updated Name',
            'description' => 'Updated description'
        ];

        $result = $this->biomeModel->update($this->testBiomeId, $updateData);
        $this->assertTrue($result);

        // Vérifier la mise à jour
        $updatedBiome = $this->biomeModel->getById($this->testBiomeId);
        $this->assertEquals('Updated Name', $updatedBiome['name']);
        $this->assertEquals('Updated description', $updatedBiome['description']);
    }

    /**
     * Teste la suppression d'un biome
     */
    public function testDeleteBiome()
    {
        // Créer un biome de test
        $testData = [
            'name' => 'To Be Deleted',
            'description' => 'This will be deleted',
            'climate' => [
                'temperature_range' => '5-15°C',
                'humidity' => '40-60%',
                'rainfall' => 'Low'
            ],
            'color_theme' => '#95A5A6',
            'icon' => '🗑️',
            'native_ingredients' => ['temp'],
            'characteristics' => ['temp'],
            'season_best' => 'Never'
        ];

        $id = $this->biomeModel->create($testData);
        
        // Le supprimer
        $result = $this->biomeModel->delete($id);
        $this->assertTrue($result);

        // Vérifier la suppression
        $deletedBiome = $this->biomeModel->getById($id);
        $this->assertNull($deletedBiome);
        
        $this->testBiomeId = null; // Déjà supprimé
    }

    /**
     * Teste la connexion à la base de données
     */
    public function testDatabaseConnection()
    {
        $connected = Database::testConnection();
        $this->assertTrue($connected);
    }

    /**
     * Teste la validation des données du biome
     */
    public function testBiomeRequiredFields()
    {
        // Tester que les données valides ont les champs requis
        $validData = [
            'name' => 'Valid Biome',
            'description' => 'Has required fields',
            'climate' => ['temperature_range' => '20°C', 'humidity' => '60%', 'rainfall' => 'Low'],
            'color_theme' => '#27AE60',
            'icon' => '✅',
            'native_ingredients' => ['test'],
            'characteristics' => ['test'],
            'season_best' => 'All'
        ];
        
        $this->assertArrayHasKey('name', $validData);
        $this->assertArrayHasKey('description', $validData);
        $this->assertArrayHasKey('climate', $validData);
        $this->assertArrayHasKey('color_theme', $validData);
    }
}