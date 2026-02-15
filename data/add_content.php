<?php
/**
 * Ajouter des articles de menu et des avis à BiomeBistro
 * À exécuter après import_sample_data.php
 */

require_once __DIR__ . '/../vendor/autoload.php';

use BiomeBistro\Models\Restaurant;
use BiomeBistro\Models\MenuItem;
use BiomeBistro\Models\Review;
use MongoDB\BSON\UTCDateTime;

echo "🍽️  BiomeBistro - Ajout des articles de menu & avis\n";
echo "=============================================\n\n";

$restaurantModel = new Restaurant();
$menuModel = new MenuItem();
$reviewModel = new Review();

// Récupérer tous les restaurants
$restaurants = $restaurantModel->getAll();

echo "📋 Ajout des articles de menu...\n";

// Articles de menu par catégorie
$menuTemplates = [
    'Entrées' => [
        ['name' => 'Seasonal Soup', 'desc' => 'Création du chef avec des ingrédients locaux', 'price' => 8.50],
        ['name' => 'Garden Salad', 'desc' => 'Feuilles fraîches avec vinaigrette maison', 'price' => 9.00],
        ['name' => 'Artisan Bread Basket', 'desc' => 'Pain chaud avec beurre aromatisé', 'price' => 6.50],
        ['name' => 'Crispy Appetizer', 'desc' => 'Bouchées croustillantes de saison avec sauce', 'price' => 11.00],
    ],
    'Plats Principaux' => [
        ['name' => 'Grilled Specialty', 'desc' => 'Plat grillé signature du chef', 'price' => 24.00],
        ['name' => 'Braised Delight', 'desc' => 'Mijoté lentement avec légumes de saison', 'price' => 26.50],
        ['name' => 'Vegetarian Harmony', 'desc' => 'Chef-d\'œuvre à base de plantes', 'price' => 19.50],
        ['name' => 'Ocean Treasure', 'desc' => 'Poisson du jour frais', 'price' => 28.00],
        ['name' => 'Traditional Classic', 'desc' => 'Recette traditionnelle avec touche moderne', 'price' => 22.00],
    ],
    'Desserts' => [
        ['name' => 'Chocolate Dream', 'desc' => 'Riche création au chocolat', 'price' => 9.50],
        ['name' => 'Seasonal Fruit Tart', 'desc' => 'Fruits frais sur pâte beurrée', 'price' => 8.50],
        ['name' => 'Ice Cream Selection', 'desc' => 'Saveurs artisanales', 'price' => 7.00],
    ],
    'Boissons' => [
        ['name' => 'House Wine', 'desc' => 'Rouge ou blanc, au verre', 'price' => 7.50],
        ['name' => 'Signature Cocktail', 'desc' => 'Création du mixologue', 'price' => 12.00],
        ['name' => 'Fresh Juice', 'desc' => 'Jus pressé de saison', 'price' => 5.50],
    ]
];

$menuCount = 0;
foreach ($restaurants as $restaurant) {
    $restaurantId = (string)$restaurant['_id'];
    
    foreach ($menuTemplates as $category => $items) {
        // Ajouter 2-3 articles par catégorie
        $itemsToAdd = array_rand($items, min(3, count($items)));
        if (!is_array($itemsToAdd)) $itemsToAdd = [$itemsToAdd];
        
        foreach ($itemsToAdd as $idx) {
            $item = $items[$idx];
            
            $menuData = [
                'restaurant_id' => $restaurantId,
                'name' => $item['name'],
                'description' => $item['desc'],
                'category' => $category,
                'price' => $item['price'] * (rand(8, 15) / 10), // Varier les prix
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
    
    echo "  ✓ Articles ajoutés pour {$restaurant['name']}\n";
}

echo "\n✅ {$menuCount} articles de menu ajoutés !\n\n";

// Ajouter des avis
echo "⭐ Ajout des avis clients...\n";

$reviewTemplates = [
    [
        'title' => 'Une expérience exceptionnelle !',
        'comment' => 'L\'ambiance était parfaite et la nourriture a dépassé toutes nos attentes. Chaque plat était joliment présenté et délicieux. Le personnel était attentif sans être intrusif. Hautement recommandé !',
        'rating' => 5
    ],
    [
        'title' => 'Super atmosphère',
        'comment' => 'J\'ai vraiment apprécié la décoration thématique unique. La nourriture était bonne, bien que légèrement chère. Le service était excellent et le personnel très bien renseigné sur le menu.',
        'rating' => 4
    ],
    [
        'title' => 'Un dîner mémorable',
        'comment' => 'Nous avons fêté notre anniversaire ici et c\'était parfait. L\'attention aux détails, aussi bien dans la nourriture que dans l\'environnement, était remarquable. Nous reviendrons sans hésiter !',
        'rating' => 5
    ],
    [
        'title' => 'Un bon choix',
        'comment' => 'Bonne cuisine et belle atmosphère. Le menu offre une belle variété et tout ce que nous avons goûté était bien préparé. Une valeur sûre pour une soirée réussie.',
        'rating' => 4
    ],
    [
        'title' => 'Une expérience culinaire unique',
        'comment' => 'Je n\'avais jamais vécu quelque chose de tel ! L\'environnement thématique vous transporte vraiment. La nourriture s\'accorde parfaitement au thème. À ne pas manquer !',
        'rating' => 5
    ],
    [
        'title' => 'Un concept impressionnant',
        'comment' => 'Le concept est brillant et bien exécuté. La qualité des plats est constante. Les prix sont raisonnables pour ce qu\'on reçoit. Idéal pour les occasions spéciales.',
        'rating' => 4
    ],
    [
        'title' => 'Ça vaut le déplacement',
        'comment' => 'Venu sur la recommandation d\'un ami et je n\'ai pas été déçu. Les plats sont créatifs et savoureux. Le personnel nous a fait nous sentir les bienvenus.',
        'rating' => 4
    ],
    [
        'title' => 'Au-delà de nos espérances',
        'comment' => 'Dès notre arrivée, nous avons été impressionnés. L\'attention portée au thème est incroyable et la nourriture est de qualité restaurant. On reviendra certainement !',
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
    
    // Ajouter 3-5 avis par restaurant
    $numReviews = rand(3, 5);
    $usedReviews = array_rand($reviewTemplates, $numReviews);
    if (!is_array($usedReviews)) $usedReviews = [$usedReviews];
    
    foreach ($usedReviews as $idx) {
        $template = $reviewTemplates[$idx];
        $reviewer = $reviewerNames[array_rand($reviewerNames)];
        
        // Date aléatoire dans les 6 derniers mois
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
            'dining_occasion' => ['Affaires', 'Romantique', 'Famille', 'Amis'][array_rand(['Affaires', 'Romantique', 'Famille', 'Amis'])],
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
    
    echo "  ✓ Avis ajoutés pour {$restaurant['name']}\n";
}

echo "\n✅ {$reviewCount} avis clients ajoutés !\n\n";

echo "🎉 Terminé ! Votre site BiomeBistro contient désormais :\n";
echo "   - {$menuCount} articles de menu dans tous les restaurants\n";
echo "   - {$reviewCount} avis clients\n";
echo "   - Des pages restaurant entièrement remplies\n\n";
echo "🌐 Rafraîchissez votre navigateur pour voir les changements !\n";
echo "   Visiter : http://localhost:8000\n";