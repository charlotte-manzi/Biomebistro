<?php
/**
 * Sample Data Import Script
 * Populates the database with sample biomes and restaurants
 */

require_once __DIR__ . '/../vendor/autoload.php';

use BiomeBistro\Config\Database;
use BiomeBistro\Models\Biome;
use BiomeBistro\Models\Restaurant;
use MongoDB\BSON\UTCDateTime;

echo "🌍 BiomeBistro - Sample Data Import\n";
echo "===================================\n\n";

// Test database connection
echo "Testing database connection...\n";
if (!Database::testConnection()) {
    die("❌ Database connection failed! Please ensure MongoDB is running.\n");
}
echo "✅ Database connected successfully!\n\n";

// Clear existing data
echo "Clearing existing data...\n";
$db = Database::getDatabase();
$db->dropCollection('biomes');
$db->dropCollection('restaurants');
echo "✅ Existing data cleared!\n\n";

// Create indexes
echo "Creating database indexes...\n";
Database::createIndexes();
echo "✅ Indexes created!\n\n";

$biomeModel = new Biome();
$restaurantModel = new Restaurant();

// Insert Biomes
echo "📚 Inserting biomes...\n";
$biomes = [
    [
        "name" => "Tropical Rainforest",
        "description" => "Lush, humid, vibrant ecosystem teeming with life",
        "climate" => [
            "temperature_range" => "25-30°C",
            "humidity" => "80-90%",
            "rainfall" => "High"
        ],
        "color_theme" => "#2ECC71",
        "icon" => "🌴",
        "native_ingredients" => ["coconut", "banana", "mango", "papaya", "cassava", "passion fruit"],
        "characteristics" => ["dense vegetation", "high biodiversity", "constant warmth", "tropical sounds"],
        "season_best" => "Year-round"
    ],
    [
        "name" => "Desert Oasis",
        "description" => "Arid landscape with hidden pockets of life and water",
        "climate" => [
            "temperature_range" => "15-35°C",
            "humidity" => "10-30%",
            "rainfall" => "Very Low"
        ],
        "color_theme" => "#F39C12",
        "icon" => "🏜️",
        "native_ingredients" => ["dates", "figs", "pomegranate", "mint", "cumin", "saffron"],
        "characteristics" => ["sandy terrain", "extreme temperature", "rare water sources", "resilient plants"],
        "season_best" => "Spring, Autumn"
    ],
    [
        "name" => "Coral Reef",
        "description" => "Underwater paradise with colorful marine life",
        "climate" => [
            "temperature_range" => "23-29°C",
            "humidity" => "Ocean environment",
            "rainfall" => "N/A"
        ],
        "color_theme" => "#3498DB",
        "icon" => "🌊",
        "native_ingredients" => ["seaweed", "shellfish", "fish", "sea urchin", "octopus", "squid"],
        "characteristics" => ["vibrant colors", "marine biodiversity", "wave sounds", "underwater feeling"],
        "season_best" => "Year-round"
    ],
    [
        "name" => "Alpine Mountain",
        "description" => "High-altitude ecosystem with crisp air and stunning views",
        "climate" => [
            "temperature_range" => "5-15°C",
            "humidity" => "50-70%",
            "rainfall" => "Moderate"
        ],
        "color_theme" => "#95A5A6",
        "icon" => "🏔️",
        "native_ingredients" => ["wild berries", "mushrooms", "herbs", "cheese", "honey", "nuts"],
        "characteristics" => ["thin air", "rocky terrain", "evergreen trees", "mountain echoes"],
        "season_best" => "Summer, Autumn"
    ],
    [
        "name" => "Arctic Tundra",
        "description" => "Frozen landscape with extreme cold and unique adaptations",
        "climate" => [
            "temperature_range" => "-40 to 10°C",
            "humidity" => "Low",
            "rainfall" => "Low (mostly snow)"
        ],
        "color_theme" => "#AED6F1",
        "icon" => "🧊",
        "native_ingredients" => ["arctic char", "seal", "berries", "root vegetables", "lichens"],
        "characteristics" => ["permafrost", "long winters", "midnight sun", "northern lights"],
        "season_best" => "Summer"
    ],
    [
        "name" => "Temperate Forest",
        "description" => "Seasonal woodland with deciduous trees and rich soil",
        "climate" => [
            "temperature_range" => "10-25°C",
            "humidity" => "60-80%",
            "rainfall" => "Moderate"
        ],
        "color_theme" => "#27AE60",
        "icon" => "🌿",
        "native_ingredients" => ["mushrooms", "acorns", "apples", "chestnuts", "wild game", "herbs"],
        "characteristics" => ["four distinct seasons", "leaf canopy", "forest floor", "bird songs"],
        "season_best" => "Spring, Autumn"
    ],
    [
        "name" => "African Savanna",
        "description" => "Vast grassland dotted with acacia trees and megafauna",
        "climate" => [
            "temperature_range" => "20-30°C",
            "humidity" => "30-50%",
            "rainfall" => "Seasonal"
        ],
        "color_theme" => "#F4D03F",
        "icon" => "🦁",
        "native_ingredients" => ["millet", "sorghum", "baobab fruit", "wild grains", "dried meats"],
        "characteristics" => ["open grasslands", "scattered trees", "dry and wet seasons", "wildlife sounds"],
        "season_best" => "Dry season"
    ],
    [
        "name" => "Mystical Mushroom Forest",
        "description" => "Fantastical woodland dominated by giant fungi and bioluminescence",
        "climate" => [
            "temperature_range" => "15-20°C",
            "humidity" => "85-95%",
            "rainfall" => "Constant mist"
        ],
        "color_theme" => "#9B59B6",
        "icon" => "🍄",
        "native_ingredients" => ["mushrooms", "truffles", "forest berries", "edible flowers", "moss"],
        "characteristics" => ["glowing fungi", "dense spores", "damp ground", "mystical atmosphere"],
        "season_best" => "Autumn"
    ]
];

$biomeIds = [];
foreach ($biomes as $biome) {
    $id = $biomeModel->create($biome);
    $biomeIds[$biome['name']] = $id;
    echo "  ✓ Created: {$biome['icon']} {$biome['name']}\n";
}
echo "\n";

// Paris locations (GPS coordinates)
$parisLocations = [
    ['address' => '123 Rue de la Forêt, 75018 Paris', 'coords' => [2.3505, 48.8738], 'district' => 'Montmartre'],
    ['address' => '45 Avenue des Sables, 75008 Paris', 'coords' => [2.3120, 48.8737], 'district' => 'Champs-Élysées'],
    ['address' => '78 Boulevard Maritime, 75001 Paris', 'coords' => [2.3376, 48.8606], 'district' => 'Louvre'],
    ['address' => '92 Rue des Sommets, 75005 Paris', 'coords' => [2.3488, 48.8534], 'district' => 'Latin Quarter'],
    ['address' => '156 Avenue Polaire, 75015 Paris', 'coords' => [2.2897, 48.8421], 'district' => 'Vaugirard'],
    ['address' => '201 Chemin des Bois, 75012 Paris', 'coords' => [2.3736, 48.8448], 'district' => 'Bercy'],
    ['address' => '89 Place du Soleil, 75014 Paris', 'coords' => [2.3219, 48.8422], 'district' => 'Montparnasse'],
    ['address' => '34 Impasse Mystique, 75009 Paris', 'coords' => [2.3370, 48.8647], 'district' => 'Opéra']
];

// Opening hours
$standardHours = [
    ['day' => 'Monday', 'open' => '11:00', 'close' => '22:00', 'closed' => false],
    ['day' => 'Tuesday', 'open' => '11:00', 'close' => '22:00', 'closed' => false],
    ['day' => 'Wednesday', 'open' => '11:00', 'close' => '22:00', 'closed' => false],
    ['day' => 'Thursday', 'open' => '11:00', 'close' => '23:00', 'closed' => false],
    ['day' => 'Friday', 'open' => '11:00', 'close' => '23:30', 'closed' => false],
    ['day' => 'Saturday', 'open' => '10:00', 'close' => '23:30', 'closed' => false],
    ['day' => 'Sunday', 'open' => '10:00', 'close' => '22:00', 'closed' => false]
];

// Insert Restaurants (2 per biome)
echo "🍽️  Inserting restaurants...\n";
$restaurants = [
    // Tropical Rainforest
    ['name' => 'Canopy Dreams Café', 'biome' => 'Tropical Rainforest', 'location_idx' => 0, 'cuisine_style' => 'Tropical Fusion', 'price_range' => '€€€', 'capacity' => 60, 'sustainability_score' => 8.5],
    ['name' => 'Jungle Paradise', 'biome' => 'Tropical Rainforest', 'location_idx' => 0, 'cuisine_style' => 'Latin American', 'price_range' => '€€', 'capacity' => 45, 'sustainability_score' => 7.8],
    
    // Desert Oasis
    ['name' => 'Sahara Sunset Lounge', 'biome' => 'Desert Oasis', 'location_idx' => 1, 'cuisine_style' => 'North African', 'price_range' => '€€€€', 'capacity' => 50, 'sustainability_score' => 7.2],
    ['name' => 'Mirage Palace', 'biome' => 'Desert Oasis', 'location_idx' => 1, 'cuisine_style' => 'Middle Eastern', 'price_range' => '€€€', 'capacity' => 40, 'sustainability_score' => 6.9],
    
    // Coral Reef
    ['name' => "Neptune's Haven", 'biome' => 'Coral Reef', 'location_idx' => 2, 'cuisine_style' => 'Seafood & Sushi', 'price_range' => '€€€€', 'capacity' => 70, 'sustainability_score' => 9.1],
    ['name' => 'Reef & Rhythm', 'biome' => 'Coral Reef', 'location_idx' => 2, 'cuisine_style' => 'Coastal Mediterranean', 'price_range' => '€€€', 'capacity' => 55, 'sustainability_score' => 8.7],
    
    // Alpine Mountain
    ['name' => 'Summit Chalet', 'biome' => 'Alpine Mountain', 'location_idx' => 3, 'cuisine_style' => 'Swiss & Austrian', 'price_range' => '€€€', 'capacity' => 50, 'sustainability_score' => 8.3],
    ['name' => 'Altitude Bistro', 'biome' => 'Alpine Mountain', 'location_idx' => 3, 'cuisine_style' => 'Contemporary Alpine', 'price_range' => '€€', 'capacity' => 35, 'sustainability_score' => 7.9],
    
    // Arctic Tundra
    ['name' => 'Aurora Ice Palace', 'biome' => 'Arctic Tundra', 'location_idx' => 4, 'cuisine_style' => 'Nordic', 'price_range' => '€€€€', 'capacity' => 40, 'sustainability_score' => 8.9],
    ['name' => 'Polar Station', 'biome' => 'Arctic Tundra', 'location_idx' => 4, 'cuisine_style' => 'Scandinavian', 'price_range' => '€€€', 'capacity' => 30, 'sustainability_score' => 9.3],
    
    // Temperate Forest
    ['name' => 'Woodland Retreat', 'biome' => 'Temperate Forest', 'location_idx' => 5, 'cuisine_style' => 'French Countryside', 'price_range' => '€€€', 'capacity' => 55, 'sustainability_score' => 8.8],
    ['name' => 'Seasons Table', 'biome' => 'Temperate Forest', 'location_idx' => 5, 'cuisine_style' => 'Farm-to-Table', 'price_range' => '€€', 'capacity' => 40, 'sustainability_score' => 9.5],
    
    // African Savanna
    ['name' => 'Serengeti Grill', 'biome' => 'African Savanna', 'location_idx' => 6, 'cuisine_style' => 'African BBQ', 'price_range' => '€€€', 'capacity' => 65, 'sustainability_score' => 7.5],
    ['name' => 'Baobab Kitchen', 'biome' => 'African Savanna', 'location_idx' => 6, 'cuisine_style' => 'Pan-African', 'price_range' => '€€', 'capacity' => 50, 'sustainability_score' => 8.1],
    
    // Mystical Mushroom Forest
    ['name' => 'Funghi Fantasy', 'biome' => 'Mystical Mushroom Forest', 'location_idx' => 7, 'cuisine_style' => 'Mushroom Gastronomy', 'price_range' => '€€€€', 'capacity' => 35, 'sustainability_score' => 9.2],
    ['name' => 'Enchanted Grove', 'biome' => 'Mystical Mushroom Forest', 'location_idx' => 7, 'cuisine_style' => 'Fantasy Cuisine', 'price_range' => '€€€', 'capacity' => 30, 'sustainability_score' => 8.6]
];

foreach ($restaurants as $restaurant) {
    $location = $parisLocations[$restaurant['location_idx']];
    $biomeId = $biomeIds[$restaurant['biome']];
    
    $data = [
        'name' => $restaurant['name'],
        'biome_id' => $biomeId,
        'description' => 'Description for ' . $restaurant['name'],
        'location' => [
            'address' => $location['address'],
            'coordinates' => [
                'type' => 'Point',
                'coordinates' => $location['coords']
            ],
            'district' => $location['district']
        ],
        'contact' => [
            'phone' => '+33 1 ' . sprintf('%02d', rand(10, 99)) . ' ' . sprintf('%02d', rand(10, 99)) . ' ' . sprintf('%02d', rand(10, 99)) . ' ' . sprintf('%02d', rand(10, 99)),
            'email' => strtolower(str_replace([' ', '\''], ['', ''], $restaurant['name'])) . '@biomebistro.fr',
            'website' => 'www.' . strtolower(str_replace([' ', '\''], ['', ''], $restaurant['name'])) . '.fr'
        ],
        'cuisine_style' => $restaurant['cuisine_style'],
        'price_range' => $restaurant['price_range'],
        'capacity' => $restaurant['capacity'],
        'atmosphere' => [
            'music' => 'Ambient music',
            'lighting' => 'Warm lighting',
            'decor' => 'Themed decor'
        ],
        'opening_hours' => $standardHours,
        'features' => ['WiFi', 'Parking'],
        'photos' => [],
        'average_rating' => rand(40, 50) / 10,
        'total_reviews' => rand(10, 100),
        'special_events' => [],
        'sustainability_score' => $restaurant['sustainability_score'],
        'eco_certifications' => [],
        'status' => 'open'
    ];
    
    $restaurantModel->create($data);
    echo "  ✓ Created: {$restaurant['name']} ({$restaurant['biome']})\n";
}
echo "\n";

echo "✅ Sample data import completed successfully!\n";
echo "📊 Summary:\n";
echo "   - Biomes: " . count($biomes) . "\n";
echo "   - Restaurants: " . count($restaurants) . "\n";
echo "\n";
echo "🎉 BiomeBistro is ready to use!\n";
echo "   Run: php -S localhost:8000 -t public\n";
echo "   Then visit: http://localhost:8000\n";