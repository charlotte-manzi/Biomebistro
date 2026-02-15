<?php
/**
 * BiomeBistro - Tous les Avis
 * Afficher TOUS les avis de TOUS les restaurants
 */

require_once __DIR__ . '/../vendor/autoload.php';

use BiomeBistro\Models\Review;
use BiomeBistro\Models\Restaurant;
use BiomeBistro\Utils\Language;

session_start();
Language::init();
Language::setLanguage($_SESSION['lang'] ?? 'fr');
$lang = Language::getCurrentLanguage();

$reviewModel = new Review();
$restaurantModel = new Restaurant();

// Récupérer TOUS les avis
$allReviews = $reviewModel->getAll();

?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang === 'fr' ? 'Tous les Avis' : 'All Reviews'; ?> - BiomeBistro</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/animations.css">
    <style>
        .reviews-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        .review-card {
            background: white;
            border: 2px solid var(--border-color);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .review-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--border-color);
        }
        
        .review-restaurant {
            font-size: 1.3rem;
            font-weight: bold;
            color: var(--primary-color);
        }
        
        .review-stars {
            font-size: 1.5rem;
        }
        
        .review-content {
            margin: 1rem 0;
        }
        
        .review-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .review-author {
            font-weight: 600;
            color: var(--text-color);
            margin-top: 1rem;
        }
        
        .review-date {
            color: var(--text-light);
            font-size: 0.9rem;
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
        
        .filter-bar {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .filter-bar select {
            padding: 0.5rem 1rem;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            font-size: 1rem;
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
                    <a href="all-reviews.php" class="active">
                        <?php echo $lang === 'fr' ? '⭐ Tous les Avis' : '⭐ All Reviews'; ?>
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
        <div class="reviews-container">
            <h1><?php echo $lang === 'fr' ? 'Tous les Avis Clients' : 'All Customer Reviews'; ?></h1>
            
            <div class="stats-summary">
                <div class="stat-item">
                    <div class="stat-number"><?php echo count($allReviews); ?></div>
                    <div class="stat-label"><?php echo $lang === 'fr' ? 'Avis au total' : 'Total reviews'; ?></div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">
                        <?php
                        $avgRating = 0;
                        if (count($allReviews) > 0) {
                            $totalRating = array_sum(array_column($allReviews, 'rating'));
                            $avgRating = $totalRating / count($allReviews);
                        }
                        echo number_format($avgRating, 1);
                        ?>
                    </div>
                    <div class="stat-label"><?php echo $lang === 'fr' ? 'Note moyenne' : 'Average rating'; ?></div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">
                        <?php
                        $fiveStarCount = count(array_filter($allReviews, function($r) { return $r['rating'] == 5; }));
                        $percentage = count($allReviews) > 0 ? round(($fiveStarCount / count($allReviews)) * 100) : 0;
                        echo $percentage;
                        ?>%
                    </div>
                    <div class="stat-label"><?php echo $lang === 'fr' ? 'Avis 5 étoiles' : '5-star reviews'; ?></div>
                </div>
            </div>

            <?php if (empty($allReviews)): ?>
                <div style="text-align: center; padding: 4rem 2rem;">
                    <div style="font-size: 5rem; margin-bottom: 1rem; opacity: 0.5;">⭐</div>
                    <h2><?php echo $lang === 'fr' ? 'Aucun avis pour le moment' : 'No reviews yet'; ?></h2>
                    <p><?php echo $lang === 'fr' ? 'Soyez le premier à laisser un avis !' : 'Be the first to leave a review!'; ?></p>
                </div>
            <?php else: ?>
                <?php foreach ($allReviews as $review):
                    $restaurant = $restaurantModel->getById((string)$review['restaurant_id']);
                    $createdDate = $review['created_at']->toDateTime();
                ?>
                    <div class="review-card">
                        <div class="review-header">
                            <div>
                                <div class="review-restaurant">
                                    <?php echo htmlspecialchars($restaurant['name'] ?? 'Restaurant'); ?>
                                </div>
                                <a href="restaurant-detail.php?id=<?php echo $review['restaurant_id']; ?>"
                                   style="font-size: 0.9rem; color: var(--primary-color);">
                                    <?php echo $lang === 'fr' ? 'Voir le restaurant →' : 'View restaurant →'; ?>
                                </a>
                            </div>
                            <div class="review-stars">
                                <?php for ($i = 1; $i <= 5; $i++) echo $i <= $review['rating'] ? '⭐' : '☆'; ?>
                            </div>
                        </div>
                        
                        <div class="review-content">
                            <?php if (!empty($review['title'])): ?>
                                <div class="review-title"><?php echo htmlspecialchars($review['title']); ?></div>
                            <?php endif; ?>
                            
                            <p style="line-height: 1.6;">
                                "<?php echo htmlspecialchars($review['comment']); ?>"
                            </p>
                            
                            <div style="margin-top: 1rem; display: flex; justify-content: space-between; align-items: center;">
                                <div class="review-author">
                                    — <?php echo htmlspecialchars($review['reviewer_name']); ?>
                                </div>
                                <div class="review-date">
                                    <?php echo $createdDate->format('d/m/Y'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
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