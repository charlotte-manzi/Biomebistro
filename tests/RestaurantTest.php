<?php

namespace BiomeBistro\Tests;

use PHPUnit\Framework\TestCase;
use BiomeBistro\Models\Restaurant;
use BiomeBistro\Models\Biome;

/**
 * Tests unitaires pour le modèle Restaurant
 */
class RestaurantTest extends TestCase
{
    private $restaurantModel;
    private $biomeModel;
    private $testRestaurantId;
    private $testBiomeId;

    protected function setUp(): void
    {
        $this->restaurantModel = new Restaurant();
        $this->biomeModel = new Biome();
        
        // Créer un biome de test pour les tests de restaurants
        $biomeData = [
            'name' => 'Test Biome for Restaurants',
            'description' => 'Test biome',
            'climate' => [
                'temperature_range' => '20-25°C',
                'humidity' => '60%',
                'rainfall' => 'Medium'
            ],
            'color_theme' => '#27AE60',
            'icon' => '🧪',
            'native_ingredients' => ['test'],
            'characteristics' => ['test'],
            'season_best' => 'All'
        ];
        
        $this->testBiomeId = $this->biomeModel->create($biomeData);
    }

    protected function tearDown(): void
    {
        // Nettoyer les données de test
        if ($this->testRestaurantId) {
            $this->restaurantModel->delete($this->testRestaurantId);
        }
        if ($this->testBiomeId) {
            $this->biomeModel->delete($this->testBiomeId);
        }
    }

    /**
     * Teste la création d'un nouveau restaurant
     */
    public function testCreateRestaurant()
    {
        $testData = [
            'name' => 'Test Restaurant',
            'biome_id' => $this->testBiomeId,
            'description' => 'A test restaurant',
            'location' => [
                'address' => '123 Test Street, Paris',
                'coordinates' => [
                    'type' => 'Point',
                    'coordinates' => [2.3522, 48.8566]
                ],
                'district' => 'Test District'
            ],
            'contact' => [
                'phone' => '+33 1 23 45 67 89',
                'email' => 'test@biomebistro.fr',
                'website' => 'www.test.fr'
            ],
            'cuisine_style' => 'Test Cuisine',
            'price_range' => '€€',
            'capacity' => 50,
            'atmosphere' => [
                'music' => 'Test music',
                'lighting' => 'Test lighting',
                'decor' => 'Test decor'
            ],
            'opening_hours' => [
                ['day' => 'Monday', 'open' => '12:00', 'close' => '22:00', 'closed' => false]
            ],
            'features' => ['WiFi', 'Parking'],
            'photos' => [],
            'average_rating' => 4.5,
            'total_reviews' => 10,
            'special_events' => [],
            'sustainability_score' => 8.0,
            'eco_certifications' => [],
            'status' => 'open'
        ];

        $this->testRestaurantId = $this->restaurantModel->create($testData);

        $this->assertNotNull($this->testRestaurantId);
        $this->assertIsString($this->testRestaurantId);
    }

    /**
     * Teste la récupération d'un restaurant par son ID
     */
    public function testGetRestaurantById()
    {
        // Créer un restaurant de test
        $testData = [
            'name' => 'Retrieval Test Restaurant',
            'biome_id' => $this->testBiomeId,
            'description' => 'Testing retrieval',
            'location' => [
                'address' => '456 Test Ave, Paris',
                'coordinates' => [
                    'type' => 'Point',
                    'coordinates' => [2.3522, 48.8566]
                ],
                'district' => 'Test District'
            ],
            'contact' => [
                'phone' => '+33 1 98 76 54 32',
                'email' => 'retrieval@test.fr',
                'website' => 'www.retrieval.fr'
            ],
            'cuisine_style' => 'French',
            'price_range' => '€€€',
            'capacity' => 60,
            'atmosphere' => [
                'music' => 'Jazz',
                'lighting' => 'Dim',
                'decor' => 'Modern'
            ],
            'opening_hours' => [],
            'features' => ['Terrace'],
            'photos' => [],
            'average_rating' => 4.0,
            'total_reviews' => 5,
            'special_events' => [],
            'sustainability_score' => 7.5,
            'eco_certifications' => [],
            'status' => 'open'
        ];

        $this->testRestaurantId = $this->restaurantModel->create($testData);
        
        // Le récupérer
        $restaurant = $this->restaurantModel->getById($this->testRestaurantId);

        $this->assertNotNull($restaurant);
        $this->assertEquals('Retrieval Test Restaurant', $restaurant['name']);
        $this->assertEquals('French', $restaurant['cuisine_style']);
        $this->assertEquals('€€€', $restaurant['price_range']);
    }

    /**
     * Teste la récupération de tous les restaurants
     */
    public function testGetAllRestaurants()
    {
        $restaurants = $this->restaurantModel->getAll();

        $this->assertIsArray($restaurants);
        $this->assertGreaterThan(0, count($restaurants));
        
        if (count($restaurants) > 0) {
            $firstRestaurant = $restaurants[0];
            $this->assertArrayHasKey('name', $firstRestaurant);
            $this->assertArrayHasKey('biome_id', $firstRestaurant);
            $this->assertArrayHasKey('location', $firstRestaurant);
        }
    }

    /**
     * Teste le filtrage des restaurants
     */
    public function testFilterRestaurants()
    {
        $filterParams = [
            'min_rating' => 4.0,
            'price_range' => '€€'
        ];

        $filteredRestaurants = $this->restaurantModel->filter($filterParams);

        $this->assertIsArray($filteredRestaurants);
        
        // Chaque restaurant doit respecter les critères de filtrage
        foreach ($filteredRestaurants as $restaurant) {
            if (isset($restaurant['average_rating'])) {
                $this->assertGreaterThanOrEqual(4.0, $restaurant['average_rating']);
            }
        }
    }

    /**
     * Teste la récupération des restaurants les mieux notés
     */
    public function testGetTopRatedRestaurants()
    {
        $topRestaurants = $this->restaurantModel->getTopRated(5);

        $this->assertIsArray($topRestaurants);
        $this->assertLessThanOrEqual(5, count($topRestaurants));
        
        // Vérifier qu'ils sont triés par note
        $previousRating = 5.0;
        foreach ($topRestaurants as $restaurant) {
            $this->assertLessThanOrEqual($previousRating, $restaurant['average_rating']);
            $previousRating = $restaurant['average_rating'];
        }
    }

    /**
     * Teste la mise à jour d'un restaurant
     */
    public function testUpdateRestaurant()
    {
        // Créer un restaurant de test
        $testData = [
            'name' => 'Original Restaurant Name',
            'biome_id' => $this->testBiomeId,
            'description' => 'Original description',
            'location' => [
                'address' => '789 Test Blvd, Paris',
                'coordinates' => [
                    'type' => 'Point',
                    'coordinates' => [2.3522, 48.8566]
                ],
                'district' => 'Original District'
            ],
            'contact' => [
                'phone' => '+33 1 11 22 33 44',
                'email' => 'original@test.fr',
                'website' => 'www.original.fr'
            ],
            'cuisine_style' => 'Italian',
            'price_range' => '€€',
            'capacity' => 40,
            'atmosphere' => ['music' => 'Classical', 'lighting' => 'Bright', 'decor' => 'Traditional'],
            'opening_hours' => [],
            'features' => [],
            'photos' => [],
            'average_rating' => 3.5,
            'total_reviews' => 2,
            'special_events' => [],
            'sustainability_score' => 6.0,
            'eco_certifications' => [],
            'status' => 'open'
        ];

        $this->testRestaurantId = $this->restaurantModel->create($testData);

        // Le mettre à jour
        $updateData = [
            'name' => 'Updated Restaurant Name',
            'description' => 'Updated description',
            'price_range' => '€€€€'
        ];

        $result = $this->restaurantModel->update($this->testRestaurantId, $updateData);
        $this->assertTrue($result);

        // Vérifier la mise à jour
        $updatedRestaurant = $this->restaurantModel->getById($this->testRestaurantId);
        $this->assertEquals('Updated Restaurant Name', $updatedRestaurant['name']);
        $this->assertEquals('Updated description', $updatedRestaurant['description']);
        $this->assertEquals('€€€€', $updatedRestaurant['price_range']);
    }

    /**
     * Teste la suppression d'un restaurant
     */
    public function testDeleteRestaurant()
    {
        // Créer un restaurant de test
        $testData = [
            'name' => 'To Be Deleted Restaurant',
            'biome_id' => $this->testBiomeId,
            'description' => 'Will be deleted',
            'location' => [
                'address' => '999 Delete St, Paris',
                'coordinates' => [
                    'type' => 'Point',
                    'coordinates' => [2.3522, 48.8566]
                ],
                'district' => 'Delete District'
            ],
            'contact' => [
                'phone' => '+33 1 00 00 00 00',
                'email' => 'delete@test.fr',
                'website' => 'www.delete.fr'
            ],
            'cuisine_style' => 'Temporary',
            'price_range' => '€',
            'capacity' => 10,
            'atmosphere' => ['music' => 'None', 'lighting' => 'None', 'decor' => 'None'],
            'opening_hours' => [],
            'features' => [],
            'photos' => [],
            'average_rating' => 1.0,
            'total_reviews' => 0,
            'special_events' => [],
            'sustainability_score' => 1.0,
            'eco_certifications' => [],
            'status' => 'closed'
        ];

        $id = $this->restaurantModel->create($testData);
        
        // Le supprimer
        $result = $this->restaurantModel->delete($id);
        $this->assertTrue($result);

        // Vérifier la suppression
        $deletedRestaurant = $this->restaurantModel->getById($id);
        $this->assertNull($deletedRestaurant);
        
        $this->testRestaurantId = null; // Déjà supprimé
    }

    /**
     * Teste la fonctionnalité de recherche
     */
    public function testSearchRestaurants()
    {
        $searchTerm = 'Test';
        $results = $this->restaurantModel->search($searchTerm);
        
        $this->assertIsArray($results);
    }
    
    /**
     * Teste la fonctionnalité de comptage
     */
    public function testCountRestaurants()
    {
        $count = $this->restaurantModel->count();

        $this->assertIsInt($count);
        $this->assertGreaterThan(0, $count);
    }
}