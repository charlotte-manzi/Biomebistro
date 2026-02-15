<?php
/**
 * BiomeBistro - Biomes (Design amélioré avec vraies images)
 */

require_once __DIR__ . '/../vendor/autoload.php';

use BiomeBistro\Models\Biome;
use BiomeBistro\Models\Restaurant;
use BiomeBistro\Utils\Language;

session_start();

// Gérer le changement de langue
if (isset($_GET['lang']) && in_array($_GET['lang'], ['fr', 'en'])) {
    $_SESSION['lang'] = $_GET['lang'];
}

Language::init();
Language::setLanguage($_SESSION['lang'] ?? 'fr');
$lang = Language::getCurrentLanguage();

http://localhost:8000/

$biomeModel = new Biome();
$restaurantModel = new Restaurant();

$biomes = $biomeModel->getAll();

// Images réelles par biome
$biomeImages = [
    'Tropical Rainforest'    => 'https://images.unsplash.com/photo-1518509562904-e7ef99cdcc86?w=1200&q=80',
    'Desert Oasis'           => 'https://images.unsplash.com/photo-1547036967-23d11aacaee0?w=1200&q=80',
    'Coral Reef'             => 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=1200&q=80',
    'Alpine Mountain'        => 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=1200&q=80',
    'Arctic Tundra'          => 'https://images.unsplash.com/photo-1551244072-5d12893278ab?w=1200&q=80',
    'Temperate Forest'       => 'https://images.unsplash.com/photo-1511497584788-876760111969?w=1200&q=80',
    'African Savanna'        => 'https://images.unsplash.com/photo-1516426122078-c23e76319801?w=1200&q=80',
    'Mystical Mushroom Forest'=> 'https://images.unsplash.com/photo-1506318137071-a8e063b4bec0?w=1200&q=80'
];
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo Language::t('biomes'); ?> - BiomeBistro</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/animations.css">
    <style>
        /* En-tête de page */
        .page-header {
            background: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.5)), 
                        url('https://images.unsplash.com/photo-1441974231531-c6227db76b6e?w=1920&q=80');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: white;
            padding: 5rem 0;
            text-align: center;
        }
        
        .page-header h1 {
            font-size: 3.5rem;
            margin-bottom: 1rem;
            text-shadow: 3px 3px 10px rgba(0,0,0,0.8);
        }
        
        /* Grande carte de biome */
        .biome-card-large {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 8px 30px rgba(0,0,0,0.15);
            margin-bottom: 3rem;
            transition: all 0.3s ease;
        }
        
        .biome-card-large:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.25);
        }
        
        .biome-header {
            position: relative;
            height: 350px;
            overflow: hidden;
        }
        
        .biome-header-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .biome-card-large:hover .biome-header-image {
            transform: scale(1.05);
        }
        
        .biome-header-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.9) 0%, transparent 100%);
            padding: 2rem;
            color: white;
        }
        
        .biome-title {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
            text-shadow: 2px 2px 8px rgba(0,0,0,0.8);
        }
        
        .biome-body {
            padding: 2rem;
        }
        
        .biome-description {
            font-size: 1.1rem;
            line-height: 1.8;
            color: var(--text-color);
            margin-bottom: 2rem;
        }
        
        .biome-details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .detail-section {
            background: var(--bg-light);
            padding: 1.5rem;
            border-radius: 12px;
        }
        
        .detail-section h4 {
            color: var(--primary-color);
            margin-bottom: 1rem;
            font-size: 1.1rem;
        }
        
        .detail-section ul {
            list-style: none;
            padding: 0;
        }
        
        .detail-section li {
            padding: 0.5rem 0;
            border-bottom: 1px solid var(--border-color);
        }
        
        .detail-section li:last-child {
            border-bottom: none;
        }
        
        .climate-info {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }
        
        .climate-item {
            background: white;
            padding: 0.75rem 1.25rem;
            border-radius: 8px;
            border: 2px solid var(--border-color);
        }
        
        .restaurants-preview {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 2px solid var(--border-color);
        }
        
        .restaurant-mini-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .restaurant-mini-card {
            background: var(--bg-light);
            padding: 1rem;
            border-radius: 8px;
            transition: all 0.2s ease;
            text-decoration: none;
            color: inherit;
            display: block;
        }
        
        .restaurant-mini-card:hover {
            background: white;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
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
                    <a href="biomes.php" class="active"><?php echo Language::t('biomes'); ?></a>
                    <a href="restaurants.php"><?php echo Language::t('restaurants'); ?></a>
                    <a href="my-reservations.php?email=demo@example.com">
                        <?php echo $lang === 'fr' ? '📅 Réservations' : '📅 Reservations'; ?>
                    </a>
                    <a href="my-reviews.php?email=demo@example.com">
                        <?php echo $lang === 'fr' ? '⭐ Avis' : '⭐ Reviews'; ?>
                    </a>
                    <a href="all-menus.php">🍽️ <?php echo $lang === 'fr' ? 'Menus' : 'Menus'; ?></a>
                </nav>
                <div class="language-switcher">
                    <a href="?lang=fr" class="lang-btn <?php echo $lang === 'fr' ? 'active' : ''; ?>">🇫🇷 FR</a>
                    <a href="?lang=en" class="lang-btn <?php echo $lang === 'en' ? 'active' : ''; ?>">🇬🇧 EN</a>
                </div>
            </div>
        </div>
    </header>

    <section class="page-header">
        <h1><?php echo $lang === 'fr' ? '8 Écosystèmes Uniques' : '8 Unique Ecosystems'; ?></h1>
        <p><?php echo $lang === 'fr' ? 'Découvrez des expériences culinaires inspirées de la nature' : 'Discover culinary experiences inspired by nature'; ?></p>
    </section>

    <div class="container" style="padding: 3rem 1rem;">
        <?php foreach ($biomes as $biome):
            // Récupérer les restaurants de ce biome
            $biomeRestaurants = [];
            $allRestaurants = $restaurantModel->getAll();
            foreach ($allRestaurants as $restaurant) {
                if ((string)$restaurant['biome_id'] === (string)$biome['_id']) {
                    $biomeRestaurants[] = $restaurant;
                }
            }
        ?>
            <div class="biome-card-large">
                <!-- En-tête du biome avec image -->
                <div class="biome-header">
                    <img src="<?php echo $biomeImages[$biome['name']] ?? ''; ?>"
                         alt="<?php echo htmlspecialchars($biome['name']); ?>"
                         class="biome-header-image">
                    <div class="biome-header-overlay">
                        <div class="biome-title">
                            <?php echo $biome['icon']; ?> <?php echo htmlspecialchars($biome['name']); ?>
                        </div>
                        <p style="font-size: 1.1rem; opacity: 0.95;">
                            <?php echo htmlspecialchars($biome['season_best'] ?? ''); ?> • 
                            <?php echo count($biomeRestaurants); ?> <?php echo $lang === 'fr' ? 'restaurants' : 'restaurants'; ?>
                        </p>
                    </div>
                </div>
                
                <!-- Corps du biome -->
                <div class="biome-body">
                    <p class="biome-description">
                        <?php echo htmlspecialchars($biome['description']); ?>
                    </p>
                    
                    <!-- Grille des détails -->
                    <div class="biome-details-grid">
                        <!-- Section Climat -->
                        <div class="detail-section">
                            <h4><?php echo $lang === 'fr' ? '🌡️ Climat' : '🌡️ Climate'; ?></h4>
                            <div class="climate-info">
                                <?php if (isset($biome['climate']['temperature_range'])): ?>
                                    <div class="climate-item">
                                        <strong><?php echo $lang === 'fr' ? 'Température' : 'Temperature'; ?>:</strong><br>
                                        <?php echo htmlspecialchars($biome['climate']['temperature_range']); ?>
                                    </div>
                                <?php endif; ?>
                                <?php if (isset($biome['climate']['humidity'])): ?>
                                    <div class="climate-item">
                                        <strong><?php echo $lang === 'fr' ? 'Humidité' : 'Humidity'; ?>:</strong><br>
                                        <?php echo htmlspecialchars($biome['climate']['humidity']); ?>
                                    </div>
                                <?php endif; ?>
                                <?php if (isset($biome['climate']['rainfall'])): ?>
                                    <div class="climate-item">
                                        <strong><?php echo $lang === 'fr' ? 'Précipitations' : 'Rainfall'; ?>:</strong><br>
                                        <?php echo htmlspecialchars($biome['climate']['rainfall']); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Section Ingrédients -->
                        <div class="detail-section">
                            <h4><?php echo $lang === 'fr' ? '🌿 Ingrédients Locaux' : '🌿 Local Ingredients'; ?></h4>
                            <ul>
                                <?php foreach ($biome['native_ingredients'] ?? [] as $ingredient): ?>
                                    <li><?php echo htmlspecialchars($ingredient); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        
                        <!-- Section Caractéristiques -->
                        <div class="detail-section">
                            <h4><?php echo $lang === 'fr' ? '✨ Caractéristiques' : '✨ Characteristics'; ?></h4>
                            <ul>
                                <?php foreach ($biome['characteristics'] ?? [] as $char): ?>
                                    <li><?php echo htmlspecialchars($char); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                    
                    <!-- Prévisualisation des restaurants -->
                    <?php if (!empty($biomeRestaurants)): ?>
                        <div class="restaurants-preview">
                            <h3><?php echo $lang === 'fr' ? 'Restaurants dans cet écosystème' : 'Restaurants in this ecosystem'; ?></h3>
                            <div class="restaurant-mini-cards">
                                <?php foreach ($biomeRestaurants as $restaurant): ?>
                                    <a href="restaurant-detail.php?id=<?php echo $restaurant['_id']; ?>" class="restaurant-mini-card">
                                        <strong><?php echo htmlspecialchars($restaurant['name']); ?></strong><br>
                                        <small style="color: var(--text-light);">
                                            ⭐ <?php echo number_format($restaurant['average_rating'], 1); ?> • 
                                            <?php echo $restaurant['price_range']; ?>
                                        </small>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Bouton voir tous les restaurants -->
                    <div style="margin-top: 2rem;">
                        <a href="restaurants.php?biome=<?php echo urlencode($biome['name']); ?>" class="btn btn-primary">
                            <?php echo $lang === 'fr' ? 'Voir tous les restaurants' : 'View all restaurants'; ?> →
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

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