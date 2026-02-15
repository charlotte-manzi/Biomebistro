<?php
/**
 * Add Menu Items and Reviews to BiomeBistro
 * Run this after import_sample_data.php
 */

require_once __DIR__ . '/../vendor/autoload.php';

use BiomeBistro\Models\Restaurant;
use BiomeBistro\Models\MenuItem;
use BiomeBistro\Models\Review;
use MongoDB\BSON\UTCDateTime;

echo "🍽️  BiomeBistro - Adding Menu Items & Reviews\n";
echo "=============================================\n\n";

$restaurantModel = new Restaurant();
$menuModel = new MenuItem();
$reviewModel = new Review();

// Get all restaurants
$restaurants = $restaurantModel->getAll();

echo "📋 Adding menu items...\n";

// Menu items by category
$menuTemplates = [
    'Starters' => [
        ['name' => 'Seasonal Soup', 'desc' => 'Chef\'s daily creation with local ingredients', 'price' => 8.50],
        ['name' => 'Garden Salad', 'desc' => 'Fresh greens with house dressing', 'price' => 9.00],
        ['name' => 'Artisan Bread Basket', 'desc' => 'Warm bread with infused butter', 'price' => 6.50],
        ['name' => 'Crispy Appetizer', 'desc' => 'Seasonal crispy bites with dipping sauce', 'price' => 11.00],
    ],
    'Main Courses' => [
        ['name' => 'Grilled Specialty', 'desc' => 'Chef\'s signature grilled dish', 'price' => 24.00],
        ['name' => 'Braised Delight', 'desc' => 'Slow-cooked perfection with seasonal vegetables', 'price' => 26.50],
        ['name' => 'Vegetarian Harmony', 'desc' => 'Plant-based masterpiece', 'price' => 19.50],
        ['name' => 'Ocean Treasure', 'desc' => 'Fresh catch of the day', 'price' => 28.00],
        ['name' => 'Traditional Classic', 'desc' => 'Time-honored recipe with modern twist', 'price' => 22.00],
    ],
    'Desserts' => [
        ['name' => 'Chocolate Dream', 'desc' => 'Rich chocolate creation', 'price' => 9.50],
        ['name' => 'Seasonal Fruit Tart', 'desc' => 'Fresh fruits on buttery crust', 'price' => 8.50],
        ['name' => 'Ice Cream Selection', 'desc' => 'Artisan flavors', 'price' => 7.00],
    ],
    'Beverages' => [
        ['name' => 'House Wine', 'desc' => 'Red or white, by the glass', 'price' => 7.50],
        ['name' => 'Signature Cocktail', 'desc' => 'Mixologist\'s creation', 'price' => 12.00],
        ['name' => 'Fresh Juice', 'desc' => 'Seasonal pressed juice', 'price' => 5.50],
    ]
];

$menuCount = 0;
foreach ($restaurants as $restaurant) {
    $restaurantId = (string)$restaurant['_id'];
    
    foreach ($menuTemplates as $category => $items) {
        // Add 2-3 items per category
        $itemsToAdd = array_rand($items, min(3, count($items)));
        if (!is_array($itemsToAdd)) $itemsToAdd = [$itemsToAdd];
        
        foreach ($itemsToAdd as $idx) {
            $item = $items[$idx];
            
            $menuData = [
                'restaurant_id' => $restaurantId,
                'name' => $item['name'],
                'description' => $item['desc'],
                'category' => $category,
                'price' => $item['price'] * (rand(8, 15) / 10), // Vary prices
                'currency' => 'EUR',
                'ingredients' => [],
                'allergens' => [],
                'dietary_info' => [],
                'spice_level' => 0,
                'biome_authenticity' => rand(7, 10),
                'preparation_time' => rand(15, 45),
                'is_signature_dish' => rand(0, 10) > 8,
                'is_seasonal' => rand(0, 10) > 6,
                'is_available' => true,
                'popularity_rank' => rand(1, 100)
            ];
            
            $menuModel->create($menuData);
            $menuCount++;
        }
    }
    
    echo "  ✓ Added menu items for {$restaurant['name']}\n";
}

echo "\n✅ Added {$menuCount} menu items!\n\n";

// Add reviews
echo "⭐ Adding customer reviews...\n";

$reviewTemplates = [
    [
        'title' => 'Outstanding experience!',
        'comment' => 'The ambiance was perfect and the food exceeded all expectations. Every dish was beautifully presented and delicious. The staff was attentive without being intrusive. Highly recommend!',
        'rating' => 5
    ],
    [
        'title' => 'Great atmosphere',
        'comment' => 'Really enjoyed the unique themed decor. The food was good, though a bit pricey. Service was excellent and the staff was very knowledgeable about the menu.',
        'rating' => 4
    ],
    [
        'title' => 'Memorable dinner',
        'comment' => 'Celebrated our anniversary here and it was perfect. The attention to detail in both the food and the environment was remarkable. Will definitely return!',
        'rating' => 5
    ],
    [
        'title' => 'Solid choice',
        'comment' => 'Good food and nice atmosphere. The menu has great variety and everything we tried was well-prepared. A reliable choice for a nice evening out.',
        'rating' => 4
    ],
    [
        'title' => 'Unique dining experience',
        'comment' => 'Never experienced anything quite like this! The themed environment really transports you. The food complements the theme beautifully. A must-try!',
        'rating' => 5
    ],
    [
        'title' => 'Impressive concept',
        'comment' => 'The whole concept is brilliant and well-executed. Food quality is consistently high. Prices are fair for what you get. Great for special occasions.',
        'rating' => 4
    ],
    [
        'title' => 'Worth the visit',
        'comment' => 'Came here based on a friend\'s recommendation and wasn\'t disappointed. The dishes are creative and flavorful. The staff made us feel welcome.',
        'rating' => 4
    ],
    [
        'title' => 'Exceeded expectations',
        'comment' => 'From the moment we walked in, we were impressed. The attention to the theme is incredible, and the food is restaurant-quality. Definitely coming back!',
        'rating' => 5
    ]
];

$reviewerNames = [
    'Sophie Martin', 'Lucas Dubois', 'Emma Leroy', 'Thomas Bernard',
    'Chloé Petit', 'Alexandre Roux', 'Léa Moreau', 'Maxime Simon',
    'Camille Laurent', 'Hugo Garcia', 'Julie Martinez', 'Nicolas Robert'
];

$reviewCount = 0;
foreach ($restaurants as $restaurant) {
    $restaurantId = (string)$restaurant['_id'];
    
    // Add 3-5 reviews per restaurant
    $numReviews = rand(3, 5);
    $usedReviews = array_rand($reviewTemplates, $numReviews);
    if (!is_array($usedReviews)) $usedReviews = [$usedReviews];
    
    foreach ($usedReviews as $idx) {
        $template = $reviewTemplates[$idx];
        $reviewer = $reviewerNames[array_rand($reviewerNames)];
        
        // Random date in the last 6 months
        $daysAgo = rand(1, 180);
        $timestamp = time() - ($daysAgo * 24 * 60 * 60);
        
        $reviewData = [
            'restaurant_id' => $restaurantId,
            'reviewer_name' => $reviewer,
            'reviewer_email' => strtolower(str_replace(' ', '.', $reviewer)) . '@example.com',
            'rating' => $template['rating'],
            'ratings_breakdown' => [
                'food_quality' => rand($template['rating'] - 1, 5),
                'service' => rand($template['rating'] - 1, 5),
                'ambiance' => rand($template['rating'] - 1, 5),
                'value_for_money' => rand($template['rating'] - 1, 5),
                'cleanliness' => rand($template['rating'], 5)
            ],
            'title' => $template['title'],
            'comment' => $template['comment'],
            'visit_date' => new UTCDateTime($timestamp * 1000),
            'dining_occasion' => ['Business', 'Romantic', 'Family', 'Friends'][array_rand(['Business', 'Romantic', 'Family', 'Friends'])],
            'pros' => [],
            'cons' => [],
            'photos' => [],
            'recommended_dishes' => [],
            'helpful_votes' => rand(0, 15),
            'verified_visit' => true
        ];
        
        $reviewModel->create($reviewData);
        $reviewCount++;
    }
    
    echo "  ✓ Added reviews for {$restaurant['name']}\n";
}

echo "\n✅ Added {$reviewCount} customer reviews!\n\n";

echo "🎉 Complete! Your BiomeBistro site now has:\n";
echo "   - {$menuCount} menu items across all restaurants\n";
echo "   - {$reviewCount} customer reviews\n";
echo "   - Fully populated restaurant pages\n\n";
echo "🌐 Refresh your browser to see the changes!\n";
echo "   Visit: http://localhost:8000\n";
