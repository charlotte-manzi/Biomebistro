<?php
/**
 * BiomeBistro - Page d'accueil (Design amélioré avec vraies images)
 */

require_once __DIR__ . '/../vendor/autoload.php';

use BiomeBistro\Models\Restaurant;
use BiomeBistro\Models\Biome;
use BiomeBistro\Utils\Language;

session_start();

// Gérer le changement de langue
if (isset($_GET['lang']) && in_array($_GET['lang'], ['fr', 'en'])) {
    $_SESSION['lang'] = $_GET['lang'];
}

Language::init();
Language::setLanguage($_SESSION['lang'] ?? 'fr');
$lang = Language::getCurrentLanguage();

$restaurantModel = new Restaurant();
$biomeModel = new Biome();

$topRestaurants = $restaurantModel->getTopRated(6);
$allBiomes = $biomeModel->getAll();

// Images réelles par biome
$biomeImages = [
    'Tropical Rainforest'     => 'https://images.unsplash.com/photo-1518509562904-e7ef99cdcc86?w=800&q=80',
    'Desert Oasis'            => 'https://images.unsplash.com/photo-1547036967-23d11aacaee0?w=800&q=80',
    'Coral Reef'              => 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=800&q=80',
    'Alpine Mountain'         => 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=800&q=80',
    'Arctic Tundra'           => 'https://images.unsplash.com/photo-1551244072-5d12893278ab?w=800&q=80',
    'Temperate Forest'        => 'https://images.unsplash.com/photo-1511497584788-876760111969?w=800&q=80',
    'African Savanna'         => 'https://images.unsplash.com/photo-1516426122078-c23e76319801?w=800&q=80',
    'Mystical Mushroom Forest' => 'https://images.unsplash.com/photo-1506318137071-a8e063b4bec0?w=800&q=80'
];

// Images de restaurants
$restaurantImages = [
    'https://images.unsplash.com/photo-1552566626-52f8b828add9?w=800&q=80',
    'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=800&q=80',
    'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=800&q=80',
    'https://images.unsplash.com/photo-1559339352-11d035aa65de?w=800&q=80',
    'https://images.unsplash.com/photo-1578474846511-04ba529f0b88?w=800&q=80',
    'https://images.unsplash.com/photo-1466978913421-dad2ebd01d17?w=800&q=80',
];
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BiomeBistro - <?php echo $lang === 'fr' ? 'Restaurants Thématiques par Écosystème' : 'Themed Restaurants by Ecosystem'; ?></title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/animations.css">
    <style>
        /* Section Hero améliorée */
        .hero-enhanced {
            position: relative;
            height: 85vh;
            min-height: 600px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .hero-background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.5)), 
                        url('https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=1920&q=80');
            background-size: cover;
            background-position: center;
            animation: slowZoom 30s ease-in-out infinite alternate;
        }
        
        @keyframes slowZoom {
            0% { transform: scale(1); }
            100% { transform: scale(1.1); }
        }
        
        .hero-content {
            position: relative;
            z-index: 2;
            text-align: center;
            color: white;
            padding: 2rem;
            max-width: 900px;
        }
        
        .hero-content h1 {
            font-size: 4rem;
            font-weight: bold;
            margin-bottom: 1.5rem;
            text-shadow: 3px 3px 10px rgba(0,0,0,0.8);
            animation: fadeInUp 1s ease;
        }
        
        .hero-content p {
            font-size: 1.5rem;
            margin-bottom: 2rem;
            text-shadow: 2px 2px 8px rgba(0,0,0,0.7);
            animation: fadeInUp 1.2s ease;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .hero-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            animation: fadeInUp 1.4s ease;
        }
        
        .hero-buttons .btn {
            padding: 1rem 2rem;
            font-size: 1.1rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        }
        
        /* Cartes de restaurants améliorées */
        .restaurant-card-enhanced {
            position: relative;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
            transition: all 0.3s ease;
            background: white;
            height: 400px;
        }
        
        .restaurant-card-enhanced:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.25);
        }
        
        .restaurant-image {
            width: 100%;
            height: 220px;
            object-fit: cover;
        }
        
        .restaurant-content {
            padding: 1.5rem;
        }
        
        .restaurant-name {
            font-size: 1.4rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
            color: var(--primary-color);
        }
        
        .restaurant-location {
            color: var(--text-light);
            font-size: 0.9rem;
            margin-bottom: 0.75rem;
        }
        
        .restaurant-rating {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }
        
        /* Cartes de biomes améliorées */
        .biome-card-enhanced {
            position: relative;
            height: 300px;
            border-radius: 15px;
            overflow: hidden;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .biome-card-enhanced:hover {
            transform: scale(1.05);
        }
        
        .biome-image-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-size: cover;
            background-position: center;
            transition: transform 0.5s ease;
        }
        
        .biome-card-enhanced:hover .biome-image-bg {
            transform: scale(1.1);
        }
        
        .biome-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to bottom, transparent 0%, rgba(0,0,0,0.7) 100%);
            display: flex;
            align-items: flex-end;
            padding: 1.5rem;
        }
        
        .biome-info {
            color: white;
        }
        
        .biome-info h3 {
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
            text-shadow: 2px 2px 8px rgba(0,0,0,0.8);
        }
        
        .stats-section {
            background: var(--bg-light);
            padding: 3rem 0;
            text-align: center;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .stat-item {
            padding: 2rem;
        }
        
        .stat-number {
            font-size: 3rem;
            font-weight: bold;
            color: var(--primary-color);
            display: block;
        }
        
        .stat-label {
            font-size: 1.1rem;
            color: var(--text-light);
            margin-top: 0.5rem;
        }
    </style>
</head>
<body>
    <header class="main-header">
        <div class="container">
            <div class="header-content">
                <div class="logo"><h1>🌍 BiomeBistro</h1></div>
                <nav class="main-nav">
                    <a href="index.php" class="active"><?php echo Language::t('home'); ?></a>
                    <a href="biomes.php"><?php echo Language::t('biomes'); ?></a>
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

    <!-- Section Hero améliorée -->
    <section class="hero-enhanced">
        <div class="hero-background"></div>
        <div class="hero-content">
            <h1><?php echo $lang === 'fr' ? 'Explorez 8 Écosystèmes Culinaires' : 'Explore 8 Culinary Ecosystems'; ?></h1>
            <p><?php echo $lang === 'fr' ? 'Une expérience gastronomique unique dans des ambiances inspirées de la nature' : 'A unique gastronomic experience in nature-inspired settings'; ?></p>
            <div class="hero-buttons">
                <a href="restaurants.php" class="btn btn-primary">
                    <?php echo $lang === 'fr' ? '🍽️ Découvrir les Restaurants' : '🍽️ Discover Restaurants'; ?>
                </a>
                <a href="biomes.php" class="btn btn-secondary">
                    <?php echo $lang === 'fr' ? '🌍 Explorer les Biomes' : '🌍 Explore Biomes'; ?>
                </a>
            </div>
        </div>
    </section>

    <!-- Section Statistiques -->
    <section class="stats-section">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-item">
                    <span class="stat-number">8</span>
                    <span class="stat-label"><?php echo $lang === 'fr' ? 'Écosystèmes' : 'Ecosystems'; ?></span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">16</span>
                    <span class="stat-label"><?php echo $lang === 'fr' ? 'Restaurants' : 'Restaurants'; ?></span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">192+</span>
                    <span class="stat-label"><?php echo $lang === 'fr' ? 'Plats' : 'Dishes'; ?></span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">4.5★</span>
                    <span class="stat-label"><?php echo $lang === 'fr' ? 'Note Moyenne' : 'Average Rating'; ?></span>
                </div>
            </div>
        </div>
    </section>

    <!-- Top Restaurants -->
    <section class="top-rated-section">
        <div class="container">
            <h2><?php echo $lang === 'fr' ? 'Restaurants les Mieux Notés' : 'Top Rated Restaurants'; ?></h2>
            <div class="restaurant-grid">
                <?php foreach ($topRestaurants as $index => $restaurant): ?>
                    <div class="restaurant-card-enhanced">
                        <img src="<?php echo $restaurantImages[$index % count($restaurantImages)]; ?>"
                             alt="<?php echo htmlspecialchars($restaurant['name']); ?>"
                             class="restaurant-image">
                        <div class="restaurant-content">
                            <h3 class="restaurant-name"><?php echo htmlspecialchars($restaurant['name']); ?></h3>
                            <p class="restaurant-location">
                                📍 <?php echo htmlspecialchars($restaurant['location']['district']); ?>
                            </p>
                            <div class="restaurant-rating">
                                <span style="color: gold; font-size: 1.2rem;">
                                    <?php for ($i = 1; $i <= 5; $i++) echo $i <= floor($restaurant['average_rating']) ? '★' : '☆'; ?>
                                </span>
                                <span><strong><?php echo number_format($restaurant['average_rating'], 1); ?></strong></span>
                                <span style="color: var(--text-light);">(<?php echo $restaurant['total_reviews']; ?> <?php echo $lang === 'fr' ? 'avis' : 'reviews'; ?>)</span>
                            </div>
                            <a href="restaurant-detail.php?id=<?php echo $restaurant['_id']; ?>" class="btn btn-small btn-primary">
                                <?php echo $lang === 'fr' ? 'Voir les détails' : 'View details'; ?>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Grille des Biomes -->
    <section class="biomes-section">
        <div class="container">
            <h2><?php echo $lang === 'fr' ? 'Explorez nos Écosystèmes' : 'Explore Our Ecosystems'; ?></h2>
            <div class="biomes-grid">
                <?php foreach ($allBiomes as $biome): ?>
                    <a href="restaurants.php?biome=<?php echo urlencode($biome['name']); ?>" class="biome-card-enhanced">
                        <div class="biome-image-bg" style="background-image: url('<?php echo $biomeImages[$biome['name']] ?? ''; ?>')"></div>
                        <div class="biome-overlay">
                            <div class="biome-info">
                                <h3><?php echo $biome['icon']; ?> <?php echo htmlspecialchars($biome['name']); ?></h3>
                                <p><?php echo htmlspecialchars(substr($biome['description'], 0, 80)) . '...'; ?></p>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
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
    <script>BiomeAnimations.homepage();</script>
</body>
</html>