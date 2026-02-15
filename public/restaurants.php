<?php
/**
 * BiomeBistro - Restaurants List Page
 * Browse and filter all restaurants
 */

require_once __DIR__ . '/../vendor/autoload.php';

use BiomeBistro\Models\Restaurant;
use BiomeBistro\Models\Biome;
use BiomeBistro\Utils\Language;

session_start();
Language::init();
Language::setLanguage($_SESSION['lang'] ?? 'fr');
$lang = Language::getCurrentLanguage();

$restaurantModel = new Restaurant();
$biomeModel = new Biome();

// Get all biomes for filter
$biomes = $biomeModel->getAll();

// Build filter parameters
$filterParams = [];

if (isset($_GET['biome'])) {
    $filterParams['biome_id'] = $_GET['biome'];
}

if (isset($_GET['price_range'])) {
    $filterParams['price_range'] = $_GET['price_range'];
}

if (isset($_GET['min_rating'])) {
    $filterParams['min_rating'] = floatval($_GET['min_rating']);
}

if (isset($_GET['search'])) {
    $filterParams['search'] = $_GET['search'];
}

if (isset($_GET['sort_by'])) {
    $filterParams['sort_by'] = $_GET['sort_by'];
}

// Get filtered restaurants
$restaurants = empty($filterParams) ? $restaurantModel->getAll() : $restaurantModel->filter($filterParams);

// Count results
$resultCount = count($restaurants);

?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo Language::t('restaurants'); ?> - BiomeBistro</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/animations.css">
    <style>
        .filters-section {
            background: var(--bg-light);
            padding: var(--spacing-lg) 0;
            border-bottom: 2px solid var(--border-color);
        }
        
        .filters-container {
            display: flex;
            gap: var(--spacing-md);
            flex-wrap: wrap;
            align-items: center;
        }
        
        .filter-group {
            flex: 1;
            min-width: 200px;
        }
        
        .filter-group label {
            display: block;
            margin-bottom: var(--spacing-xs);
            font-weight: 600;
            color: var(--text-color);
        }
        
        .filter-group select,
        .filter-group input {
            width: 100%;
            padding: var(--spacing-sm);
            border: 2px solid var(--border-color);
            border-radius: var(--radius-md);
            font-size: 0.95rem;
        }
        
        .filter-buttons {
            display: flex;
            gap: var(--spacing-sm);
            align-items: flex-end;
        }
        
        .results-header {
            padding: var(--spacing-lg) 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: var(--spacing-md);
        }
        
        .results-count {
            font-size: 1.1rem;
            color: var(--text-light);
        }
        
        .no-results {
            text-align: center;
            padding: var(--spacing-xl) 0;
        }
        
        .no-results-icon {
            font-size: 4rem;
            margin-bottom: var(--spacing-md);
        }
        
        .clear-filters {
            background: transparent;
            color: var(--text-light);
            border: 2px solid var(--border-color);
        }
        
        .clear-filters:hover {
            border-color: var(--primary-color);
            color: var(--primary-color);
        }
    </style>
</head>
<body>
    <!-- Header (same as index.php) -->
    <header class="main-header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <h1>🌍 BiomeBistro</h1>
                    <p class="tagline"><?php echo Language::t('welcome_title'); ?></p>
                </div>
                
                <nav class="main-nav">
                    <a href="index.php"><?php echo Language::t('home'); ?></a>
                    <a href="biomes.php"><?php echo Language::t('biomes'); ?></a>
                    <a href="restaurants.php" class="active"><?php echo Language::t('restaurants'); ?></a>
                </nav>
                
                <div class="language-switcher">
                    <a href="?lang=fr<?php echo isset($_SERVER['QUERY_STRING']) ? '&' . $_SERVER['QUERY_STRING'] : ''; ?>" 
                       class="lang-btn <?php echo $lang === 'fr' ? 'active' : ''; ?>">
                        🇫🇷 FR
                    </a>
                    <a href="?lang=en<?php echo isset($_SERVER['QUERY_STRING']) ? '&' . $_SERVER['QUERY_STRING'] : ''; ?>" 
                       class="lang-btn <?php echo $lang === 'en' ? 'active' : ''; ?>">
                        🇬🇧 EN
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Filters Section -->
    <section class="filters-section">
        <div class="container">
            <form method="GET" action="restaurants.php" class="filters-container">
                <!-- Search -->
                <div class="filter-group">
                    <label for="search"><?php echo Language::t('search'); ?></label>
                    <input 
                        type="text" 
                        id="search" 
                        name="search" 
                        placeholder="<?php echo Language::t('search_placeholder'); ?>"
                        value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>"
                    >
                </div>
                
                <!-- Biome Filter -->
                <div class="filter-group">
                    <label for="biome"><?php echo Language::t('biome'); ?></label>
                    <select id="biome" name="biome">
                        <option value=""><?php echo Language::t('all'); ?></option>
                        <?php foreach ($biomes as $biome): ?>
                            <option value="<?php echo $biome['_id']; ?>" 
                                    <?php echo (isset($_GET['biome']) && $_GET['biome'] == $biome['_id']) ? 'selected' : ''; ?>>
                                <?php echo $biome['icon']; ?> <?php echo htmlspecialchars($biome['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Price Range Filter -->
                <div class="filter-group">
                    <label for="price_range"><?php echo Language::t('price_range'); ?></label>
                    <select id="price_range" name="price_range">
                        <option value=""><?php echo Language::t('all'); ?></option>
                        <option value="€" <?php echo (isset($_GET['price_range']) && $_GET['price_range'] == '€') ? 'selected' : ''; ?>>€</option>
                        <option value="€€" <?php echo (isset($_GET['price_range']) && $_GET['price_range'] == '€€') ? 'selected' : ''; ?>>€€</option>
                        <option value="€€€" <?php echo (isset($_GET['price_range']) && $_GET['price_range'] == '€€€') ? 'selected' : ''; ?>>€€€</option>
                        <option value="€€€€" <?php echo (isset($_GET['price_range']) && $_GET['price_range'] == '€€€€') ? 'selected' : ''; ?>>€€€€</option>
                    </select>
                </div>
                
                <!-- Min Rating Filter -->
                <div class="filter-group">
                    <label for="min_rating"><?php echo Language::t('rating'); ?></label>
                    <select id="min_rating" name="min_rating">
                        <option value=""><?php echo Language::t('all'); ?></option>
                        <option value="4.5" <?php echo (isset($_GET['min_rating']) && $_GET['min_rating'] == '4.5') ? 'selected' : ''; ?>>4.5+ ⭐</option>
                        <option value="4.0" <?php echo (isset($_GET['min_rating']) && $_GET['min_rating'] == '4.0') ? 'selected' : ''; ?>>4.0+ ⭐</option>
                        <option value="3.5" <?php echo (isset($_GET['min_rating']) && $_GET['min_rating'] == '3.5') ? 'selected' : ''; ?>>3.5+ ⭐</option>
                        <option value="3.0" <?php echo (isset($_GET['min_rating']) && $_GET['min_rating'] == '3.0') ? 'selected' : ''; ?>>3.0+ ⭐</option>
                    </select>
                </div>
                
                <!-- Sort By -->
                <div class="filter-group">
                    <label for="sort_by"><?php echo Language::t('sort_by'); ?></label>
                    <select id="sort_by" name="sort_by">
                        <option value="rating" <?php echo (isset($_GET['sort_by']) && $_GET['sort_by'] == 'rating') ? 'selected' : ''; ?>>
                            <?php echo Language::t('rating_high_low'); ?>
                        </option>
                        <option value="name" <?php echo (isset($_GET['sort_by']) && $_GET['sort_by'] == 'name') ? 'selected' : ''; ?>>
                            <?php echo Language::t('name'); ?>
                        </option>
                        <option value="price_low" <?php echo (isset($_GET['sort_by']) && $_GET['sort_by'] == 'price_low') ? 'selected' : ''; ?>>
                            <?php echo Language::t('price_low_high'); ?>
                        </option>
                        <option value="price_high" <?php echo (isset($_GET['sort_by']) && $_GET['sort_by'] == 'price_high') ? 'selected' : ''; ?>>
                            <?php echo Language::t('price_high_low'); ?>
                        </option>
                    </select>
                </div>
                
                <!-- Filter Buttons -->
                <div class="filter-buttons">
                    <button type="submit" class="btn btn-primary">
                        🔍 <?php echo Language::t('filter'); ?>
                    </button>
                    <a href="restaurants.php" class="btn clear-filters">
                        <?php echo Language::t('all'); ?>
                    </a>
                </div>
            </form>
        </div>
    </section>

    <!-- Results Section -->
    <section class="top-rated-section">
        <div class="container">
            <div class="results-header">
                <div class="results-count">
                    <?php echo $resultCount; ?> <?php echo Language::t('restaurants_count'); ?>
                    <?php if (!empty($filterParams)): ?>
                        <?php echo $lang === 'fr' ? 'trouvé(s)' : 'found'; ?>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php if (empty($restaurants)): ?>
                <div class="no-results">
                    <div class="no-results-icon">🔍</div>
                    <h2><?php echo $lang === 'fr' ? 'Aucun restaurant trouvé' : 'No restaurants found'; ?></h2>
                    <p><?php echo $lang === 'fr' ? 'Essayez de modifier vos filtres' : 'Try adjusting your filters'; ?></p>
                    <a href="restaurants.php" class="btn btn-primary"><?php echo Language::t('view_more'); ?></a>
                </div>
            <?php else: ?>
                <div class="restaurant-grid">
                    <?php foreach ($restaurants as $restaurant): 
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
            <?php endif; ?>
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
                    <h4><?php echo Language::t('about_us'); ?></h4>
                    <ul>
                        <li><a href="#"><?php echo Language::t('contact'); ?></a></li>
                        <li><a href="#"><?php echo Language::t('careers'); ?></a></li>
                        <li><a href="#"><?php echo Language::t('terms'); ?></a></li>
                    </ul>
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