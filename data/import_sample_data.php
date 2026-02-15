<?php
/**
 * Script d'importation de données d'exemple
 * Peuple la base de données avec des biomes et restaurants exemples
 */

require_once __DIR__ . '/../vendor/autoload.php';

use BiomeBistro\Config\Database;
use BiomeBistro\Models\Biome;
use BiomeBistro\Models\Restaurant;
use MongoDB\BSON\UTCDateTime;

echo "🌍 BiomeBistro - Importation des données d'exemple\n";
echo "===================================\n\n";

// Tester la connexion à la base de données
echo "Test de la connexion à la base de données...\n";
if (!Database::testConnection()) {
    die("❌ Connexion à la base de données échouée ! Vérifiez que MongoDB est en cours d'exécution.\n");
}
echo "✅ Base de données connectée avec succès !\n\n";

// Supprimer les données existantes
echo "Suppression des données existantes...\n";
$db = Database::getDatabase();
$db->dropCollection('biomes');
$db->dropCollection('restaurants');
echo "✅ Données existantes supprimées !\n\n";

// Créer les index
echo "Création des index de la base de données...\n";
Database::createIndexes();
echo "✅ Index créés !\n\n";

$biomeModel = new Biome();
$restaurantModel = new Restaurant();

// Insertion des biomes
echo "📚 Insertion des biomes...\n";
$biomes = [
    [
        "name" => "Tropical Rainforest",
        "description" => "Écosystème luxuriant, humide et vibrant, grouillant de vie",
        "climate" => [
            "temperature_range" => "25-30°C",
            "humidity" => "80-90%",
            "rainfall" => "Élevé"
        ],
        "color_theme" => "#2ECC71",
        "icon" => "🌴",
        "native_ingredients" => ["coconut", "banana", "mango", "papaya", "cassava", "passion fruit"],
        "characteristics" => ["végétation dense", "haute biodiversité", "chaleur constante", "sons tropicaux"],
        "season_best" => "Toute l'année"
    ],
    [
        "name" => "Desert Oasis",
        "description" => "Paysage aride avec des poches cachées de vie et d'eau",
        "climate" => [
            "temperature_range" => "15-35°C",
            "humidity" => "10-30%",
            "rainfall" => "Très faible"
        ],
        "color_theme" => "#F39C12",
        "icon" => "🏜️",
        "native_ingredients" => ["dates", "figs", "pomegranate", "mint", "cumin", "saffron"],
        "characteristics" => ["terrain sableux", "températures extrêmes", "sources d'eau rares", "plantes résistantes"],
        "season_best" => "Printemps, Automne"
    ],
    [
        "name" => "Coral Reef",
        "description" => "Paradis sous-marin aux espèces marines colorées",
        "climate" => [
            "temperature_range" => "23-29°C",
            "humidity" => "Environnement océanique",
            "rainfall" => "N/A"
        ],
        "color_theme" => "#3498DB",
        "icon" => "🌊",
        "native_ingredients" => ["seaweed", "shellfish", "fish", "sea urchin", "octopus", "squid"],
        "characteristics" => ["couleurs vives", "biodiversité marine", "sons de vagues", "ambiance sous-marine"],
        "season_best" => "Toute l'année"
    ],
    [
        "name" => "Alpine Mountain",
        "description" => "Écosystème de haute altitude avec air pur et vues saisissantes",
        "climate" => [
            "temperature_range" => "5-15°C",
            "humidity" => "50-70%",
            "rainfall" => "Modéré"
        ],
        "color_theme" => "#95A5A6",
        "icon" => "🏔️",
        "native_ingredients" => ["wild berries", "mushrooms", "herbs", "cheese", "honey", "nuts"],
        "characteristics" => ["air raréfié", "terrain rocheux", "arbres à feuilles persistantes", "échos montagnards"],
        "season_best" => "Été, Automne"
    ],
    [
        "name" => "Arctic Tundra",
        "description" => "Paysage glacé avec un froid extrême et des adaptations uniques",
        "climate" => [
            "temperature_range" => "-40 à 10°C",
            "humidity" => "Faible",
            "rainfall" => "Faible (surtout neige)"
        ],
        "color_theme" => "#AED6F1",
        "icon" => "🧊",
        "native_ingredients" => ["arctic char", "seal", "berries", "root vegetables", "lichens"],
        "characteristics" => ["pergélisol", "longs hivers", "soleil de minuit", "aurores boréales"],
        "season_best" => "Été"
    ],
    [
        "name" => "Temperate Forest",
        "description" => "Forêt saisonnière avec des arbres à feuilles caduques et un sol riche",
        "climate" => [
            "temperature_range" => "10-25°C",
            "humidity" => "60-80%",
            "rainfall" => "Modéré"
        ],
        "color_theme" => "#27AE60",
        "icon" => "🌿",
        "native_ingredients" => ["mushrooms", "acorns", "apples", "chestnuts", "wild game", "herbs"],
        "characteristics" => ["quatre saisons distinctes", "canopée de feuilles", "sol forestier", "chants d'oiseaux"],
        "season_best" => "Printemps, Automne"
    ],
    [
        "name" => "African Savanna",
        "description" => "Vaste prairie parsemée d'acacias et de grands animaux",
        "climate" => [
            "temperature_range" => "20-30°C",
            "humidity" => "30-50%",
            "rainfall" => "Saisonnier"
        ],
        "color_theme" => "#F4D03F",
        "icon" => "🦁",
        "native_ingredients" => ["millet", "sorghum", "baobab fruit", "wild grains", "dried meats"],
        "characteristics" => ["prairies ouvertes", "arbres épars", "saisons sèches et humides", "sons de la faune"],
        "season_best" => "Saison sèche"
    ],
    [
        "name" => "Mystical Mushroom Forest",
        "description" => "Forêt fantastique dominée par des champignons géants et la bioluminescence",
        "climate" => [
            "temperature_range" => "15-20°C",
            "humidity" => "85-95%",
            "rainfall" => "Brume constante"
        ],
        "color_theme" => "#9B59B6",
        "icon" => "🍄",
        "native_ingredients" => ["mushrooms", "truffles", "forest berries", "edible flowers", "moss"],
        "characteristics" => ["champignons lumineux", "spores denses", "sol humide", "atmosphère mystique"],
        "season_best" => "Automne"
    ]
];

$biomeIds = [];
foreach ($biomes as $biome) {
    $id = $biomeModel->create($biome);
    $biomeIds[$biome['name']] = $id;
    echo "  ✓ Créé : {$biome['icon']} {$biome['name']}\n";
}
echo "\n";

// Emplacements à Paris (coordonnées GPS)
$parisLocations = [
    ['address' => '123 Rue de la Forêt, 75018 Paris', 'coords' => [2.3505, 48.8738], 'district' => 'Montmartre'],
    ['address' => '45 Avenue des Sables, 75008 Paris', 'coords' => [2.3120, 48.8737], 'district' => 'Champs-Élysées'],
    ['address' => '78 Boulevard Maritime, 75001 Paris', 'coords' => [2.3376, 48.8606], 'district' => 'Louvre'],
    ['address' => '92 Rue des Sommets, 75005 Paris', 'coords' => [2.3488, 48.8534], 'district' => 'Quartier Latin'],
    ['address' => '156 Avenue Polaire, 75015 Paris', 'coords' => [2.2897, 48.8421], 'district' => 'Vaugirard'],
    ['address' => '201 Chemin des Bois, 75012 Paris', 'coords' => [2.3736, 48.8448], 'district' => 'Bercy'],
    ['address' => '89 Place du Soleil, 75014 Paris', 'coords' => [2.3219, 48.8422], 'district' => 'Montparnasse'],
    ['address' => '34 Impasse Mystique, 75009 Paris', 'coords' => [2.3370, 48.8647], 'district' => 'Opéra']
];

// Horaires d'ouverture
$standardHours = [
    ['day' => 'Lundi',    'open' => '11:00', 'close' => '22:00', 'closed' => false],
    ['day' => 'Mardi',    'open' => '11:00', 'close' => '22:00', 'closed' => false],
    ['day' => 'Mercredi', 'open' => '11:00', 'close' => '22:00', 'closed' => false],
    ['day' => 'Jeudi',    'open' => '11:00', 'close' => '23:00', 'closed' => false],
    ['day' => 'Vendredi', 'open' => '11:00', 'close' => '23:30', 'closed' => false],
    ['day' => 'Samedi',   'open' => '10:00', 'close' => '23:30', 'closed' => false],
    ['day' => 'Dimanche', 'open' => '10:00', 'close' => '22:00', 'closed' => false]
];

// Insertion des restaurants (2 par biome)
echo "🍽️  Insertion des restaurants...\n";
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
        'description' => 'Description de ' . $restaurant['name'],
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
            'music' => 'Musique d\'ambiance',
            'lighting' => 'Éclairage chaleureux',
            'decor' => 'Décoration thématique'
        ],
        'opening_hours' => $standardHours,
        'features' => ['WiFi', 'Parking'],
        'photos' => [],
        'average_rating' => rand(40, 50) / 10,
        'total_reviews' => rand(10, 100),
        'special_events' => [],
        'sustainability_score' => $restaurant['sustainability_score'],
        'eco_certifications' => [],
        'status' => 'ouvert'
    ];

    $restaurantModel->create($data);
    echo "  ✓ Créé : {$restaurant['name']} ({$restaurant['biome']})\n";
}
echo "\n";

echo "✅ Importation des données d'exemple terminée avec succès !\n";
echo "📊 Résumé :\n";
echo "   - Biomes : " . count($biomes) . "\n";
echo "   - Restaurants : " . count($restaurants) . "\n";
echo "\n";
echo "🎉 BiomeBistro est prêt à l'emploi !\n";
echo "   Lancer : php -S localhost:8000 -t public\n";
echo "   Puis visiter : http://localhost:8000\n";