<?php
/**
 * BiomeBistro - Restaurant Detail Page
 */

require_once __DIR__ . '/../vendor/autoload.php';

use BiomeBistro\Models\Restaurant;
use BiomeBistro\Models\Biome;
use BiomeBistro\Models\MenuItem;
use BiomeBistro\Models\Review;
use BiomeBistro\Utils\Language;

session_start();
Language::init();
Language::setLanguage($_SESSION['lang'] ?? 'fr');
$lang = Language::getCurrentLanguage();

$restaurantId = $_GET['id'] ?? null;
if (!$restaurantId) {
    header('Location: restaurants.php');
    exit;
}

$restaurantModel = new Restaurant();
$biomeModel = new Biome();
$menuModel = new MenuItem();
$reviewModel = new Review();

$restaurant = $restaurantModel->getById($restaurantId);

if (!$restaurant) {
    header('Location: restaurants.php');
    exit;
}

$biome = $biomeModel->getById((string)$restaurant['biome_id']);
$menuItems = $menuModel->getByRestaurant($restaurantId);
$reviews = $reviewModel->getByRestaurant($restaurantId);

// Group menu items by category
$menuByCategory = [];
foreach ($menuItems as $item) {
    $category = $item['category'] ?? 'Other';
    if (!isset($menuByCategory[$category])) {
        $menuByCategory[$category] = [];
    }
    $menuByCategory[$category][] = $item;
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($restaurant['name']); ?> - BiomeBistro</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/animations.css">
    <style>
        .detail-header {
            background: linear-gradient(135deg, <?php echo $biome['color_theme'] ?? '#27AE60'; ?> 0%, #2C3E50 100%);
            color: white;
            padding: 3rem 0;
            position: relative;
            z-index: 1;
        }
        .detail-title { 
            font-size: 3rem; 
            margin-bottom: 1rem; 
        }
        .detail-meta { 
            display: flex; 
            gap: 2rem; 
            flex-wrap: wrap; 
            margin-top: 1rem; 
        }
        .detail-meta div { 
            background: rgba(255,255,255,0.2); 
            padding: 0.5rem 1rem; 
            border-radius: 8px; 
        }
        .detail-section { 
            margin: 3rem 0; 
        }
        .info-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); 
            gap: 2rem; 
            margin: 2rem 0; 
        }
        .info-card { 
            background: var(--bg-light); 
            padding: 1.5rem; 
            border-radius: 12px; 
        }
        .tabs {
            display: flex;
            gap: var(--spacing-sm);
            border-bottom: 2px solid var(--border-color);
            margin: var(--spacing-lg) 0;
            flex-wrap: wrap;
        }
        .tab {
            padding: var(--spacing-md) var(--spacing-lg);
            background: none;
            border: none;
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-light);
            cursor: pointer;
            border-bottom: 3px solid transparent;
            transition: all 0.3s ease;
        }
        .tab.active {
            color: var(--primary-color);
            border-bottom-color: var(--primary-color);
        }
        .tab:hover {
            color: var(--primary-color);
        }
        .tab-content {
            display: none;
            padding: var(--spacing-lg) 0;
        }
        .tab-content.active {
            display: block;
        }
        .menu-category {
            margin-bottom: var(--spacing-xl);
        }
        .menu-category h3 {
            font-size: 1.8rem;
            color: var(--primary-color);
            margin-bottom: var(--spacing-md);
            padding-bottom: var(--spacing-sm);
            border-bottom: 2px solid var(--border-color);
        }
        .menu-item {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: var(--spacing-md) 0;
            border-bottom: 1px solid var(--border-color);
        }
        .menu-item:last-child {
            border-bottom: none;
        }
        .menu-item-info {
            flex: 1;
        }
        .menu-item-name {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--text-color);
            margin-bottom: var(--spacing-xs);
        }
        .menu-item-description {
            color: var(--text-light);
            font-size: 0.95rem;
            line-height: 1.5;
        }
        .menu-item-price {
            font-size: 1.3rem;
            font-weight: bold;
            color: var(--primary-color);
            margin-left: var(--spacing-md);
            white-space: nowrap;
        }
        .review-item {
            background: white;
            border: 2px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: var(--spacing-lg);
            margin-bottom: var(--spacing-md);
        }
        .review-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: var(--spacing-sm);
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
                </nav>
                <div class="language-switcher">
                    <a href="?id=<?php echo $restaurantId; ?>&lang=fr" class="lang-btn <?php echo $lang === 'fr' ? 'active' : ''; ?>">🇫🇷 FR</a>
                    <a href="?id=<?php echo $restaurantId; ?>&lang=en" class="lang-btn <?php echo $lang === 'en' ? 'active' : ''; ?>">🇬🇧 EN</a>
                </div>
            </div>
        </div>
    </header>

    <section class="detail-header">
        <div class="container">
            <div class="restaurant-badge" style="background: <?php echo $biome['color_theme']; ?>; display: inline-block; padding: 0.5rem 1rem; border-radius: 5px; margin-bottom: 1rem;" data-biome="<?php echo htmlspecialchars($biome['name']); ?>">
                <?php echo $biome['icon']; ?> <?php echo htmlspecialchars($biome['name']); ?>
            </div>
            <h1 class="detail-title"><?php echo htmlspecialchars($restaurant['name']); ?></h1>
            <div class="detail-meta">
                <div>
                    <?php for ($i = 1; $i <= 5; $i++) echo $i <= $restaurant['average_rating'] ? '⭐' : '☆'; ?>
                    <?php echo number_format($restaurant['average_rating'], 1); ?> (<?php echo $restaurant['total_reviews']; ?>)
                </div>
                <div><?php echo htmlspecialchars($restaurant['price_range']); ?></div>
                <div>🍽️ <?php echo htmlspecialchars($restaurant['cuisine_style']); ?></div>
                <div>📍 <?php echo htmlspecialchars($restaurant['location']['district']); ?></div>
            </div>
            <div style="margin-top: 2rem;">
                <a href="make-reservation.php?restaurant=<?php echo $restaurantId; ?>" class="btn btn-primary">📅 <?php echo Language::t('book_table'); ?></a>
                <a href="add-review.php?restaurant=<?php echo $restaurantId; ?>" class="btn btn-secondary">✍️ <?php echo Language::t('write_review'); ?></a>
            </div>
        </div>
    </section>

    <section class="top-rated-section">
        <div class="container">
            <!-- Tabs -->
            <div class="tabs">
                <button class="tab active" onclick="switchTab('about')">
                    <?php echo Language::t('about'); ?>
                </button>
                <button class="tab" onclick="switchTab('menu')">
                    <?php echo Language::t('menu'); ?>
                </button>
                <button class="tab" onclick="switchTab('reviews')">
                    <?php echo Language::t('reviews'); ?>
                </button>
                <button class="tab" onclick="switchTab('info')">
                    <?php echo Language::t('contact'); ?>
                </button>
            </div>

            <!-- Tab: About -->
            <div id="tab-about" class="tab-content active">
                <h2><?php echo Language::t('about'); ?></h2>
                <p style="font-size: 1.1rem; line-height: 1.8;"><?php echo htmlspecialchars($restaurant['description']); ?></p>
                
                <div class="info-grid">
                    <div class="info-card">
                        <h3><?php echo Language::t('atmosphere'); ?></h3>
                        <p><strong><?php echo $lang === 'fr' ? 'Musique' : 'Music'; ?>:</strong> <?php echo htmlspecialchars($restaurant['atmosphere']['music'] ?? 'N/A'); ?></p>
                        <p><strong><?php echo $lang === 'fr' ? 'Éclairage' : 'Lighting'; ?>:</strong> <?php echo htmlspecialchars($restaurant['atmosphere']['lighting'] ?? 'N/A'); ?></p>
                        <p><strong><?php echo $lang === 'fr' ? 'Décor' : 'Decor'; ?>:</strong> <?php echo htmlspecialchars($restaurant['atmosphere']['decor'] ?? 'N/A'); ?></p>
                    </div>
                    
                    <div class="info-card">
                        <h3><?php echo $lang === 'fr' ? 'Équipements' : 'Features'; ?></h3>
                        <ul>
                            <?php foreach ($restaurant['features'] ?? [] as $feature): ?>
                                <li>✓ <?php echo htmlspecialchars($feature); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    
                    <div class="info-card">
                        <h3><?php echo $lang === 'fr' ? 'Score de durabilité' : 'Sustainability Score'; ?></h3>
                        <p style="font-size: 2rem; font-weight: bold; color: var(--primary-color);">
                            <?php echo number_format($restaurant['sustainability_score'], 1); ?>/10
                        </p>
                    </div>
                </div>
            </div>

            <!-- Tab: Menu -->
            <div id="tab-menu" class="tab-content">
                <h2><?php echo Language::t('menu'); ?></h2>
                
                <?php if (empty($menuByCategory)): ?>
                    <p style="text-align: center; padding: 2rem; color: var(--text-light);">
                        <?php echo $lang === 'fr' ? 'Menu à venir...' : 'Menu coming soon...'; ?>
                    </p>
                <?php else: ?>
                    <?php foreach ($menuByCategory as $category => $items): ?>
                        <div class="menu-category">
                            <h3><?php echo htmlspecialchars($category); ?></h3>
                            
                            <?php foreach ($items as $item): ?>
                                <div class="menu-item">
                                    <div class="menu-item-info">
                                        <div class="menu-item-name">
                                            <?php echo htmlspecialchars($item['name']); ?>
                                            <?php if ($item['is_signature_dish'] ?? false): ?>
                                                <span style="color: gold;">⭐</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="menu-item-description">
                                            <?php echo htmlspecialchars($item['description'] ?? ''); ?>
                                        </div>
                                    </div>
                                    <div class="menu-item-price">
                                        €<?php echo number_format($item['price'], 2); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Tab: Reviews -->
            <div id="tab-reviews" class="tab-content">
                <h2><?php echo Language::t('reviews'); ?> (<?php echo $restaurant['total_reviews']; ?>)</h2>
                
                <?php if (empty($reviews)): ?>
                    <p style="text-align: center; padding: 2rem; color: var(--text-light);">
                        <?php echo $lang === 'fr' ? 'Aucun avis pour le moment. Soyez le premier à laisser un avis !' : 'No reviews yet. Be the first to leave a review!'; ?>
                    </p>
                <?php else: ?>
                    <?php foreach ($reviews as $review): ?>
                        <div class="review-item">
                            <div class="review-header">
                                <div class="stars">
                                    <?php 
                                    for ($i = 1; $i <= 5; $i++) {
                                        echo $i <= $review['rating'] ? '⭐' : '☆';
                                    }
                                    ?>
                                </div>
                                <span style="color: var(--text-light);">
                                    <?php echo $review['created_at']->toDateTime()->format('d/m/Y'); ?>
                                </span>
                            </div>
                            
                            <?php if (!empty($review['title'])): ?>
                                <h4 style="margin: var(--spacing-sm) 0;"><?php echo htmlspecialchars($review['title']); ?></h4>
                            <?php endif; ?>
                            
                            <p style="margin: var(--spacing-sm) 0; line-height: 1.6;">
                                "<?php echo htmlspecialchars($review['comment']); ?>"
                            </p>
                            
                            <p style="font-weight: 600; margin-top: var(--spacing-sm);">
                                — <?php echo htmlspecialchars($review['reviewer_name']); ?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                
                <a href="add-review.php?restaurant=<?php echo $restaurantId; ?>" class="btn btn-primary" style="margin-top: var(--spacing-lg);">
                    ✍️ <?php echo Language::t('write_review'); ?>
                </a>
            </div>

            <!-- Tab: Info -->
            <div id="tab-info" class="tab-content">
                <h2><?php echo Language::t('contact'); ?> & <?php echo Language::t('opening_hours'); ?></h2>
                
                <div class="info-grid">
                    <div class="info-card">
                        <h3><?php echo Language::t('contact'); ?></h3>
                        <p>📞 <?php echo htmlspecialchars($restaurant['contact']['phone'] ?? 'N/A'); ?></p>
                        <p>📧 <?php echo htmlspecialchars($restaurant['contact']['email'] ?? 'N/A'); ?></p>
                        <p>🌐 <?php echo htmlspecialchars($restaurant['contact']['website'] ?? 'N/A'); ?></p>
                    </div>
                    
                    <div class="info-card">
                        <h3><?php echo Language::t('location'); ?></h3>
                        <p>📍 <?php echo htmlspecialchars($restaurant['location']['address']); ?></p>
                        <p>🏙️ <?php echo htmlspecialchars($restaurant['location']['district']); ?></p>
                        <p>👥 <?php echo Language::t('capacity'); ?>: <?php echo $restaurant['capacity']; ?> <?php echo $lang === 'fr' ? 'personnes' : 'people'; ?></p>
                    </div>
                    
                    <div class="info-card">
                        <h3><?php echo Language::t('opening_hours'); ?></h3>
                        <?php foreach ($restaurant['opening_hours'] as $hours): ?>
                            <p>
                                <strong><?php echo $hours['day']; ?>:</strong>
                                <?php echo $hours['closed'] ? ($lang === 'fr' ? 'Fermé' : 'Closed') : $hours['open'] . ' - ' . $hours['close']; ?>
                            </p>
                        <?php endforeach; ?>
                    </div>
                </div>
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
    <script>
        function switchTab(tabName) {
            // Hide all tab contents
            const tabContents = document.querySelectorAll('.tab-content');
            tabContents.forEach(content => content.classList.remove('active'));
            
            // Remove active class from all tabs
            const tabs = document.querySelectorAll('.tab');
            tabs.forEach(tab => tab.classList.remove('active'));
            
            // Show selected tab content
            document.getElementById('tab-' + tabName).classList.add('active');
            
            // Add active class to clicked tab
            event.target.classList.add('active');
        }
    </script>
</body>
</html>