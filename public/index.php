<?php
/**
 * BiomeBistro - Home Page
 * Welcome page with biome explorer and top-rated restaurants
 */

require_once __DIR__ . '/../vendor/autoload.php';

use BiomeBistro\Models\Restaurant;
use BiomeBistro\Models\Biome;
use BiomeBistro\Models\Review;
use BiomeBistro\Utils\Language;

// Start session and initialize language
session_start();
Language::init();

// Handle language switch
if (isset($_GET['lang']) && in_array($_GET['lang'], ['fr', 'en'])) {
    $_SESSION['lang'] = $_GET['lang'];
    Language::setLanguage($_GET['lang']);
} else {
    Language::setLanguage($_SESSION['lang'] ?? 'fr');
}

$lang = Language::getCurrentLanguage();

// Initialize models
$restaurantModel = new Restaurant();
$biomeModel = new Biome();
$reviewModel = new Review();

// Get data
$biomes = $biomeModel->getAllWithCounts();
$topRestaurants = $restaurantModel->getTopRated(4);
$recentReviews = $reviewModel->getRecent(3);
$totalRestaurants = $restaurantModel->count();

?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BiomeBistro - <?php echo Language::t('welcome_title'); ?></title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/animations.css">
</head>
<body>
    <!-- Header -->
    <header class="main-header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <h1>🌍 BiomeBistro</h1>
                    <p class="tagline"><?php echo Language::t('welcome_title'); ?></p>
                </div>
                
                <!-- Navigation -->
                <nav class="main-nav">
                    <a href="index.php" class="active"><?php echo Language::t('home'); ?></a>
                    <a href="biomes.php"><?php echo Language::t('biomes'); ?></a>
                    <a href="restaurants.php"><?php echo Language::t('restaurants'); ?></a>
                </nav>
                
                <!-- Language Switcher -->
                <div class="language-switcher">
                    <a href="?lang=fr" class="lang-btn <?php echo $lang === 'fr' ? 'active' : ''; ?>">
                        🇫🇷 FR
                    </a>
                    <a href="?lang=en" class="lang-btn <?php echo $lang === 'en' ? 'active' : ''; ?>">
                        🇬🇧 EN
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h2 class="hero-title"><?php echo Language::t('welcome_title'); ?></h2>
            <p class="hero-subtitle"><?php echo Language::t('welcome_subtitle'); ?></p>
            <div class="hero-stats">
                <div class="stat">
                    <span class="stat-number">8</span>
                    <span class="stat-label"><?php echo Language::t('biomes'); ?></span>
                </div>
                <div class="stat">
                    <span class="stat-number"><?php echo $totalRestaurants; ?></span>
                    <span class="stat-label"><?php echo Language::t('restaurants'); ?></span>
                </div>
            </div>
            <div class="hero-buttons">
                <a href="biomes.php" class="btn btn-primary"><?php echo Language::t('explore_biomes'); ?></a>
                <a href="restaurants.php" class="btn btn-secondary"><?php echo Language::t('restaurants'); ?></a>
            </div>
        </div>
    </section>

    <!-- Search Bar -->
    <section class="search-section">
        <div class="container">
            <form action="restaurants.php" method="GET" class="search-form">
                <input 
                    type="text" 
                    name="search" 
                    class="search-input" 
                    placeholder="<?php echo Language::t('search_placeholder'); ?>"
                >
                <button type="submit" class="search-btn">
                    🔍 <?php echo Language::t('search'); ?>
                </button>
            </form>
        </div>
    </section>

    <!-- Explore by Biome Section -->
    <section class="biomes-section">
        <div class="container">
            <h2 class="section-title"><?php echo Language::t('explore_by_biome'); ?></h2>
            
            <div class="biomes-grid">
                <?php foreach ($biomes as $biome): ?>
                    <a href="restaurants.php?biome=<?php echo $biome['_id']; ?>" class="biome-card">
                        <div class="biome-icon" style="background: <?php echo htmlspecialchars($biome['color_theme']); ?>">
                            <span class="icon-large"><?php echo $biome['icon']; ?></span>
                        </div>
                        <h3 class="biome-name"><?php echo htmlspecialchars($biome['name']); ?></h3>
                        <p class="biome-count">
                            <?php echo $biome['restaurant_count']; ?> 
                            <?php echo Language::t('restaurants_count'); ?>
                        </p>
                        <p class="biome-description">
                            <?php echo htmlspecialchars(substr($biome['description'], 0, 80)); ?>...
                        </p>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Top Rated Restaurants -->
    <section class="top-rated-section">
        <div class="container">
            <h2 class="section-title"><?php echo Language::t('top_rated'); ?></h2>
            
            <div class="restaurant-grid">
                <?php foreach ($topRestaurants as $restaurant): 
                    // Get biome info
                    $biome = $biomeModel->getById((string)$restaurant['biome_id']);
                ?>
                    <div class="restaurant-card">
                        <div class="restaurant-badge" style="background: <?php echo $biome['color_theme'] ?? '#27AE60'; ?>">
                            <?php echo $biome['icon'] ?? '🌍'; ?> <?php echo htmlspecialchars($biome['name'] ?? ''); ?>
                        </div>
                        
                        <h3 class="restaurant-name">
                            <a href="restaurant-detail.php?id=<?php echo $restaurant['_id']; ?>">
                                <?php echo htmlspecialchars($restaurant['name']); ?>
                            </a>
                        </h3>
                        
                        <p class="restaurant-description">
                            <?php echo htmlspecialchars(substr($restaurant['description'], 0, 120)); ?>...
                        </p>
                        
                        <div class="restaurant-info">
                            <div class="rating">
                                <span class="stars">
                                    <?php 
                                    $rating = $restaurant['average_rating'];
                                    for ($i = 1; $i <= 5; $i++) {
                                        echo $i <= $rating ? '⭐' : '☆';
                                    }
                                    ?>
                                </span>
                                <span class="rating-number"><?php echo number_format($rating, 1); ?></span>
                                <span class="review-count">(<?php echo $restaurant['total_reviews']; ?>)</span>
                            </div>
                            
                            <div class="price-range">
                                <?php echo htmlspecialchars($restaurant['price_range']); ?>
                            </div>
                        </div>
                        
                        <div class="restaurant-location">
                            📍 <?php echo htmlspecialchars($restaurant['location']['district']); ?>
                        </div>
                        
                        <div class="restaurant-cuisine">
                            🍽️ <?php echo htmlspecialchars($restaurant['cuisine_style']); ?>
                        </div>
                        
                        <div class="restaurant-actions">
                            <a href="restaurant-detail.php?id=<?php echo $restaurant['_id']; ?>" class="btn btn-small btn-primary">
                                <?php echo Language::t('view_details'); ?>
                            </a>
                            <a href="make-reservation.php?restaurant=<?php echo $restaurant['_id']; ?>" class="btn btn-small btn-secondary">
                                <?php echo Language::t('book_table'); ?>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="section-footer">
                <a href="restaurants.php" class="btn btn-outline">
                    <?php echo Language::t('view_more'); ?> →
                </a>
            </div>
        </div>
    </section>

    <!-- Latest Reviews -->
    <section class="reviews-section">
        <div class="container">
            <h2 class="section-title"><?php echo Language::t('latest_reviews'); ?></h2>
            
            <div class="reviews-grid">
                <?php foreach ($recentReviews as $review): 
                    $restaurant = $restaurantModel->getById((string)$review['restaurant_id']);
                ?>
                    <div class="review-card">
                        <div class="review-header">
                            <div class="review-rating">
                                <?php 
                                for ($i = 1; $i <= 5; $i++) {
                                    echo $i <= $review['rating'] ? '⭐' : '☆';
                                }
                                ?>
                            </div>
                            <div class="review-date">
                                <?php 
                                $date = $review['created_at']->toDateTime();
                                echo $date->format('d/m/Y');
                                ?>
                            </div>
                        </div>
                        
                        <h4 class="review-title"><?php echo htmlspecialchars($review['title'] ?? ''); ?></h4>
                        
                        <p class="review-comment">
                            "<?php echo htmlspecialchars(substr($review['comment'], 0, 150)); ?>..."
                        </p>
                        
                        <div class="review-footer">
                            <div class="reviewer-name">
                                <?php echo htmlspecialchars($review['reviewer_name']); ?>
                            </div>
                            <div class="restaurant-link">
                                <a href="restaurant-detail.php?id=<?php echo $review['restaurant_id']; ?>">
                                    <?php echo htmlspecialchars($restaurant['name'] ?? ''); ?>
                                </a>
                            </div>
                        </div>
                        
                        <?php if (!empty($review['helpful_votes'])): ?>
                        <div class="review-helpful">
                            👍 <?php echo $review['helpful_votes']; ?> 
                            <?php echo Language::t('people_found_helpful'); ?>
                        </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>BiomeBistro</h3>
                    <p><?php echo Language::t('welcome_subtitle'); ?></p>
                </div>
                
                <div class="footer-section">
                    <h4><?php echo Language::t('biomes'); ?></h4>
                    <ul>
                        <li><a href="restaurants.php?biome_name=Tropical+Rainforest">🌴 <?php echo Language::t('tropical_rainforest'); ?></a></li>
                        <li><a href="restaurants.php?biome_name=Desert+Oasis">🏜️ <?php echo Language::t('desert_oasis'); ?></a></li>
                        <li><a href="restaurants.php?biome_name=Coral+Reef">🌊 <?php echo Language::t('coral_reef'); ?></a></li>
                        <li><a href="biomes.php"><?php echo Language::t('view_more'); ?></a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h4><?php echo Language::t('about_us'); ?></h4>
                    <ul>
                        <li><a href="#"><?php echo Language::t('contact'); ?></a></li>
                        <li><a href="#"><?php echo Language::t('careers'); ?></a></li>
                        <li><a href="#"><?php echo Language::t('terms'); ?></a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h4><?php echo Language::t('follow_us'); ?></h4>
                    <div class="social-links">
                        <a href="#" class="social-link">Facebook</a>
                        <a href="#" class="social-link">Instagram</a>
                        <a href="#" class="social-link">Twitter</a>
                    </div>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p><?php echo Language::t('copyright'); ?></p>
            </div>
        </div>
    </footer>

    <script src="/js/main.js"></script>
    <script src="/js/animations.js"></script>
</body>
</html>