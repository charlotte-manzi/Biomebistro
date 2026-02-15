<?php
/**
 * BiomeBistro - Tous les Menus
 * Afficher TOUS les plats de TOUS les restaurants
 */

require_once __DIR__ . '/../vendor/autoload.php';

use BiomeBistro\Models\MenuItem;
use BiomeBistro\Models\Restaurant;
use BiomeBistro\Utils\Language;

session_start();
Language::init();
Language::setLanguage($_SESSION['lang'] ?? 'fr');
$lang = Language::getCurrentLanguage();

$menuModel = new MenuItem();
$restaurantModel = new Restaurant();

// Récupérer TOUS les plats
$allMenuItems = $menuModel->getAll();

// Grouper par restaurant
$menusByRestaurant = [];
foreach ($allMenuItems as $item) {
    $restaurantId = (string)$item['restaurant_id'];
    if (!isset($menusByRestaurant[$restaurantId])) {
        $menusByRestaurant[$restaurantId] = [];
    }
    $menusByRestaurant[$restaurantId][] = $item;
}

?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang === 'fr' ? 'Tous les Menus' : 'All Menus'; ?> - BiomeBistro</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/animations.css">
    <style>
        .menus-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        .restaurant-menu-section {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .restaurant-header {
            border-bottom: 3px solid var(--primary-color);
            padding-bottom: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .restaurant-title {
            font-size: 2rem;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }
        
        .menu-items-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-top: 1.5rem;
        }
        
        .menu-item-card {
            border: 2px solid var(--border-color);
            border-radius: 8px;
            padding: 1rem;
            transition: all 0.3s ease;
        }
        
        .menu-item-card:hover {
            border-color: var(--primary-color);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .item-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 0.5rem;
        }
        
        .item-name {
            font-weight: 600;
            font-size: 1.1rem;
            color: var(--text-color);
        }
        
        .item-price {
            font-size: 1.2rem;
            font-weight: bold;
            color: var(--primary-color);
            white-space: nowrap;
        }
        
        .item-description {
            color: var(--text-light);
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }
        
        .item-category {
            display: inline-block;
            background: var(--bg-light);
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.85rem;
            color: var(--text-color);
        }
        
        .stats-summary {
            background: var(--bg-light);
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .stat-item {
            text-align: center;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: var(--primary-color);
        }
        
        .stat-label {
            color: var(--text-light);
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <header class="main-header">
        <div class="container">
            <div class="header-content">
                <div class="logo"><h1>🌍 BiomeBistro</h1></div>
                <nav class="main-nav">
                    <a href="index.php"><?php echo Language::t('home'); ?></a>
                    <a href="biomes.php"><?php echo Language::t('biomes'); ?></a>
                    <a href="restaurants.php"><?php echo Language::t('restaurants'); ?></a>
                    <a href="all-menus.php" class="active">
                        <?php echo $lang === 'fr' ? '🍽️ Tous les Menus' : '🍽️ All Menus'; ?>
                    </a>
                </nav>
                <div class="language-switcher">
                    <a href="?lang=fr" class="lang-btn <?php echo $lang === 'fr' ? 'active' : ''; ?>">🇫🇷 FR</a>
                    <a href="?lang=en" class="lang-btn <?php echo $lang === 'en' ? 'active' : ''; ?>">🇬🇧 EN</a>
                </div>
            </div>
        </div>
    </header>

    <section class="top-rated-section">
        <div class="menus-container">
            <h1><?php echo $lang === 'fr' ? 'Tous les Menus - Carte Complète' : 'All Menus - Complete Menu'; ?></h1>
            
            <div class="stats-summary">
                <div class="stat-item">
                    <div class="stat-number"><?php echo count($allMenuItems); ?></div>
                    <div class="stat-label"><?php echo $lang === 'fr' ? 'Plats au total' : 'Total dishes'; ?></div>
                </div>
                <div class="stat-item">
                    <div class="stat-number"><?php echo count($menusByRestaurant); ?></div>
                    <div class="stat-label"><?php echo $lang === 'fr' ? 'Restaurants' : 'Restaurants'; ?></div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">
                        €<?php 
                        $avgPrice = 0;
                        if (count($allMenuItems) > 0) {
                            $total = array_sum(array_column($allMenuItems, 'price'));
                            $avgPrice = $total / count($allMenuItems);
                        }
                        echo number_format($avgPrice, 2); 
                        ?>
                    </div>
                    <div class="stat-label"><?php echo $lang === 'fr' ? 'Prix moyen' : 'Average price'; ?></div>
                </div>
            </div>

            <?php foreach ($menusByRestaurant as $restaurantId => $items): 
                $restaurant = $restaurantModel->getById($restaurantId);
                if (!$restaurant) continue;
                
                // Grouper par catégorie
                $itemsByCategory = [];
                foreach ($items as $item) {
                    $category = $item['category'] ?? ($lang === 'fr' ? 'Autre' : 'Other');
                    if (!isset($itemsByCategory[$category])) {
                        $itemsByCategory[$category] = [];
                    }
                    $itemsByCategory[$category][] = $item;
                }
            ?>
                <div class="restaurant-menu-section">
                    <div class="restaurant-header">
                        <h2 class="restaurant-title"><?php echo htmlspecialchars($restaurant['name']); ?></h2>
                        <p style="color: var(--text-light);">
                            📍 <?php echo htmlspecialchars($restaurant['location']['district']); ?> • 
                            <?php echo count($items); ?> <?php echo $lang === 'fr' ? 'plats' : 'dishes'; ?>
                        </p>
                        <a href="restaurant-detail.php?id=<?php echo $restaurantId; ?>" class="btn btn-small btn-primary" style="margin-top: 0.5rem;">
                            <?php echo $lang === 'fr' ? 'Voir le restaurant' : 'View restaurant'; ?>
                        </a>
                    </div>
                    
                    <?php foreach ($itemsByCategory as $category => $categoryItems): ?>
                        <h3 style="margin-top: 2rem; margin-bottom: 1rem; color: var(--primary-color);">
                            <?php echo htmlspecialchars($category); ?>
                        </h3>
                        
                        <div class="menu-items-grid">
                            <?php foreach ($categoryItems as $item): ?>
                                <div class="menu-item-card">
                                    <div class="item-header">
                                        <div class="item-name">
                                            <?php echo htmlspecialchars($item['name']); ?>
                                            <?php if ($item['is_signature_dish'] ?? false): ?>
                                                <span style="color: gold;">⭐</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="item-price">
                                            €<?php echo number_format($item['price'], 2); ?>
                                        </div>
                                    </div>
                                    
                                    <?php if (!empty($item['description'])): ?>
                                        <p class="item-description">
                                            <?php echo htmlspecialchars($item['description']); ?>
                                        </p>
                                    <?php endif; ?>
                                    
                                    <span class="item-category"><?php echo htmlspecialchars($category); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <footer class="main-footer">
        <div class="container">
            <div class="footer-bottom">
                <p><?php echo Language::t('copyright'); ?></p>
            </div>
        </div>
    </footer>
    
    <script src="/js/main.js"></script>
    <script src="/js/animations.js"></script>
</body>
</html>