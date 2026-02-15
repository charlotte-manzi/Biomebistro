<?php

namespace BiomeBistro\Tests;

use PHPUnit\Framework\TestCase;
use BiomeBistro\Models\Reservation;
use BiomeBistro\Models\Restaurant;
use BiomeBistro\Models\Biome;
use BiomeBistro\Utils\Validator;

/**
 * Unit tests for Reservation model
 */
class ReservationTest extends TestCase
{
    private $reservationModel;
    private $restaurantModel;
    private $biomeModel;
    private $testReservationId;
    private $testRestaurantId;
    private $testBiomeId;

    protected function setUp(): void
    {
        $this->reservationModel = new Reservation();
        $this->restaurantModel = new Restaurant();
        $this->biomeModel = new Biome();
        
        // Create test biome
        $biomeData = [
            'name' => 'Test Biome for Reservations',
            'description' => 'Test biome',
            'climate' => ['temperature_range' => '20°C', 'humidity' => '60%', 'rainfall' => 'Low'],
            'color_theme' => '#27AE60',
            'icon' => '🧪',
            'native_ingredients' => ['test'],
            'characteristics' => ['test'],
            'season_best' => 'All'
        ];
        $this->testBiomeId = $this->biomeModel->create($biomeData);
        
        // Create test restaurant
        $restaurantData = [
            'name' => 'Test Restaurant for Reservations',
            'biome_id' => $this->testBiomeId,
            'description' => 'Test restaurant',
            'location' => [
                'address' => '123 Test St, Paris',
                'coordinates' => ['type' => 'Point', 'coordinates' => [2.3522, 48.8566]],
                'district' => 'Test'
            ],
            'contact' => [
                'phone' => '+33 1 23 45 67 89',
                'email' => 'test@test.fr',
                'website' => 'www.test.fr'
            ],
            'cuisine_style' => 'Test',
            'price_range' => '€€',
            'capacity' => 50,
            'atmosphere' => ['music' => 'Test', 'lighting' => 'Test', 'decor' => 'Test'],
            'opening_hours' => [],
            'features' => [],
            'photos' => [],
            'average_rating' => 4.0,
            'total_reviews' => 0,
            'special_events' => [],
            'sustainability_score' => 7.0,
            'eco_certifications' => [],
            'status' => 'open'
        ];
        $this->testRestaurantId = $this->restaurantModel->create($restaurantData);
    }

    protected function tearDown(): void
    {
        if ($this->testReservationId) {
            $this->reservationModel->delete($this->testReservationId);
        }
        if ($this->testRestaurantId) {
            $this->restaurantModel->delete($this->testRestaurantId);
        }
        if ($this->testBiomeId) {
            $this->biomeModel->delete($this->testBiomeId);
        }
    }

    /**
     * Test creating a reservation
     */
    public function testCreateReservation()
    {
        $testData = [
            'restaurant_id' => $this->testRestaurantId,
            'customer_info' => [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'phone' => '+33 1 23 45 67 89'
            ],
            'reservation_date' => '2026-03-15',
            'reservation_time' => '19:00',
            'party_size' => 4,
            'special_requests' => 'Window table please',
            'status' => 'pending'
        ];

        $this->testReservationId = $this->reservationModel->create($testData);

        $this->assertNotNull($this->testReservationId);
        $this->assertIsString($this->testReservationId);
    }

    /**
     * Test retrieving a reservation by ID
     */
    public function testGetReservationById()
    {
        $testData = [
            'restaurant_id' => $this->testRestaurantId,
            'customer_info' => [
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'phone' => '+33 1 98 76 54 32'
            ],
            'reservation_date' => '2026-04-20',
            'reservation_time' => '20:00',
            'party_size' => 2,
            'special_requests' => 'Birthday celebration',
            'status' => 'confirmed'
        ];

        $this->testReservationId = $this->reservationModel->create($testData);
        $reservation = $this->reservationModel->getById($this->testReservationId);

        $this->assertNotNull($reservation);
        $this->assertEquals('Jane Smith', $reservation['customer_info']['name']);
        $this->assertEquals(2, $reservation['party_size']);
        $this->assertEquals('confirmed', $reservation['status']);
    }

    /**
     * Test getting reservations by restaurant
     */
    public function testGetReservationsByRestaurant()
    {
        // Create multiple reservations
        for ($i = 1; $i <= 3; $i++) {
            $testData = [
                'restaurant_id' => $this->testRestaurantId,
                'customer_info' => [
                    'name' => "Customer $i",
                    'email' => "customer$i@example.com",
                    'phone' => '+33 1 00 00 00 0' . $i
                ],
                'reservation_date' => '2026-05-0' . $i,
                'reservation_time' => '19:00',
                'party_size' => $i + 1,
                'special_requests' => '',
                'status' => 'pending'
            ];
            
            $id = $this->reservationModel->create($testData);
            if ($i === 1) {
                $this->testReservationId = $id;
            }
        }

        $reservations = $this->reservationModel->getByRestaurant($this->testRestaurantId);

        $this->assertIsArray($reservations);
        $this->assertGreaterThanOrEqual(3, count($reservations));
    }

    /**
     * Test updating a reservation status
     */
    public function testUpdateReservationStatus()
    {
        $testData = [
            'restaurant_id' => $this->testRestaurantId,
            'customer_info' => [
                'name' => 'Update Test',
                'email' => 'update@example.com',
                'phone' => '+33 1 11 11 11 11'
            ],
            'reservation_date' => '2026-06-10',
            'reservation_time' => '18:30',
            'party_size' => 6,
            'special_requests' => '',
            'status' => 'pending'
        ];

        $this->testReservationId = $this->reservationModel->create($testData);

        // Update status
        $updateData = ['status' => 'confirmed'];
        $result = $this->reservationModel->update($this->testReservationId, $updateData);
        $this->assertTrue($result);

        // Verify
        $updated = $this->reservationModel->getById($this->testReservationId);
        $this->assertEquals('confirmed', $updated['status']);
    }

    /**
     * Test deleting a reservation
     */
    public function testDeleteReservation()
    {
        $testData = [
            'restaurant_id' => $this->testRestaurantId,
            'customer_info' => [
                'name' => 'Delete Test',
                'email' => 'delete@example.com',
                'phone' => '+33 1 99 99 99 99'
            ],
            'reservation_date' => '2026-07-01',
            'reservation_time' => '21:00',
            'party_size' => 3,
            'special_requests' => '',
            'status' => 'pending'
        ];

        $id = $this->reservationModel->create($testData);
        
        $result = $this->reservationModel->delete($id);
        $this->assertTrue($result);

        $deleted = $this->reservationModel->getById($id);
        $this->assertNull($deleted);
        
        $this->testReservationId = null;
    }

    /**
     * Test email validation
     */
    public function testEmailValidation()
    {
        $this->assertTrue(Validator::validateEmail('test@example.com'));
        $this->assertTrue(Validator::validateEmail('user.name@domain.co.uk'));
        $this->assertFalse(Validator::validateEmail('invalid-email'));
        $this->assertFalse(Validator::validateEmail('no-at-sign.com'));
    }

    /**
     * Test phone validation
     */
    public function testPhoneValidation()
    {
        $this->assertTrue(Validator::validatePhone('+33 1 23 45 67 89'));
        $this->assertTrue(Validator::validatePhone('0123456789'));
        $this->assertTrue(Validator::validatePhone('+33123456789'));
    }

    /**
     * Test date validation
     */
    public function testDateValidation()
    {
        $this->assertTrue(Validator::validateDate('2026-12-31'));
        $this->assertTrue(Validator::validateDate('2026-01-01'));
        $this->assertFalse(Validator::validateDate('2026-13-01')); // Invalid month
        $this->assertFalse(Validator::validateDate('31-12-2026')); // Wrong format
    }

    /**
     * Test party size validation
     */
    public function testPartySizeValidation()
    {
        $this->assertTrue(Validator::validatePartySize(1));
        $this->assertTrue(Validator::validatePartySize(10));
        $this->assertTrue(Validator::validatePartySize(20));
        $this->assertFalse(Validator::validatePartySize(0));
        $this->assertFalse(Validator::validatePartySize(25));
        $this->assertFalse(Validator::validatePartySize(-1));
    }

    /**
     * Test reservation validation
     */
    public function testReservationValidation()
    {
        $validData = [
            'restaurant_id' => $this->testRestaurantId,
            'customer_info' => [
                'name' => 'Valid Customer',
                'email' => 'valid@example.com',
                'phone' => '+33 1 23 45 67 89'
            ],
            'reservation_date' => '2026-08-15',
            'reservation_time' => '19:30',
            'party_size' => 4,
            'special_requests' => '',
            'status' => 'pending'
        ];

        $errors = Validator::validateReservation($validData);
        $this->assertEmpty($errors);
    }

    /**
     * Test invalid reservation data
     */
    public function testInvalidReservationData()
    {
        $invalidData = [
            'restaurant_id' => $this->testRestaurantId,
            'customer_info' => [
                'name' => '',
                'email' => 'invalid-email',
                'phone' => '123'
            ],
            'reservation_date' => 'invalid-date',
            'reservation_time' => '',
            'party_size' => 0,
            'special_requests' => '',
            'status' => 'pending'
        ];

        $errors = Validator::validateReservation($invalidData);
        $this->assertNotEmpty($errors);
    }
}
