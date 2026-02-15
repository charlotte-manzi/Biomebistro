<?php
/**
 * BiomeBistro - Mes Avis
 * Gestion des avis de l'utilisateur
 */

require_once __DIR__ . '/../vendor/autoload.php';

use BiomeBistro\Models\Review;
use BiomeBistro\Models\Restaurant;
use BiomeBistro\Utils\Language;

session_start();
Language::init();
Language::setLanguage($_SESSION['lang'] ?? 'fr');
$lang = Language::getCurrentLanguage();

$userEmail = $_GET['email'] ?? 'demo@example.com';

$reviewModel = new Review();
$restaurantModel = new Restaurant();

// Récupérer les avis de l'utilisateur
$userReviews = $reviewModel->getByEmail($userEmail);

?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang === 'fr' ? 'Mes Avis' : 'My Reviews'; ?> - BiomeBistro</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/animations.css">
    <style>
        .reviews-container {
            max-width: 1000px;
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
        
        .review-date {
            color: var(--text-light);
            font-size: 0.9rem;
            margin-top: 1rem;
        }
        
        .review-actions {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }
        
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
        }
        
        .empty-icon {
            font-size: 5rem;
            margin-bottom: 1rem;
            opacity: 0.5;
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
                    <a href="my-reservations.php?email=<?php echo urlencode($userEmail); ?>">
                        <?php echo $lang === 'fr' ? '📅 Mes Réservations' : '📅 My Reservations'; ?>
                    </a>
                    <a href="my-reviews.php?email=<?php echo urlencode($userEmail); ?>" class="active">
                        <?php echo $lang === 'fr' ? '⭐ Mes Avis' : '⭐ My Reviews'; ?>
                    </a>
                </nav>
                <div class="language-switcher">
                    <a href="?email=<?php echo urlencode($userEmail); ?>&lang=fr" class="lang-btn <?php echo $lang === 'fr' ? 'active' : ''; ?>">🇫🇷 FR</a>
                    <a href="?email=<?php echo urlencode($userEmail); ?>&lang=en" class="lang-btn <?php echo $lang === 'en' ? 'active' : ''; ?>">🇬🇧 EN</a>
                </div>
            </div>
        </div>
    </header>

    <section class="top-rated-section">
        <div class="reviews-container">
            <h1><?php echo $lang === 'fr' ? 'Mes Avis' : 'My Reviews'; ?></h1>

            <?php if (empty($userReviews)): ?>
                <div class="empty-state">
                    <div class="empty-icon">⭐</div>
                    <h2><?php echo $lang === 'fr' ? 'Aucun avis' : 'No reviews'; ?></h2>
                    <p><?php echo $lang === 'fr' ? 'Vous n\'avez pas encore laissé d\'avis.' : 'You haven\'t left any reviews yet.'; ?></p>
                    <a href="restaurants.php" class="btn btn-primary" style="margin-top: 1.5rem;">
                        <?php echo $lang === 'fr' ? 'Découvrir nos restaurants' : 'Discover our restaurants'; ?>
                    </a>
                </div>
            <?php else: ?>
                <?php foreach ($userReviews as $review): 
                    $restaurant = $restaurantModel->getById((string)$review['restaurant_id']);
                    $createdDate = $review['created_at']->toDateTime();
                ?>
                    <div class="review-card">
                        <div class="review-header">
                            <div class="review-restaurant">
                                <?php echo htmlspecialchars($restaurant['name'] ?? 'Restaurant'); ?>
                            </div>
                            <div class="review-stars">
                                <?php for ($i = 1; $i <= 5; $i++) echo $i <= $review['rating'] ? '⭐' : '☆'; ?>
                            </div>
                        </div>
                        
                        <div class="review-content">
                            <?php if (!empty($review['title'])): ?>
                                <div class="review-title"><?php echo htmlspecialchars($review['title']); ?></div>
                            <?php endif; ?>
                            
                            <p><?php echo htmlspecialchars($review['comment']); ?></p>
                            
                            <div class="review-date">
                                <?php echo $lang === 'fr' ? 'Publié le' : 'Published on'; ?> 
                                <?php echo $createdDate->format('d/m/Y'); ?>
                            </div>
                        </div>
                        
                        <div class="review-actions">
                            <a href="restaurant-detail.php?id=<?php echo $review['restaurant_id']; ?>" class="btn btn-small btn-primary">
                                <?php echo $lang === 'fr' ? 'Voir le restaurant' : 'View restaurant'; ?>
                            </a>
                            
                            <a href="edit-review.php?id=<?php echo $review['_id']; ?>&email=<?php echo urlencode($userEmail); ?>" class="btn btn-small btn-secondary">
                                ✏️ <?php echo $lang === 'fr' ? 'Modifier' : 'Edit'; ?>
                            </a>
                            
                            <a href="delete-review.php?id=<?php echo $review['_id']; ?>&email=<?php echo urlencode($userEmail); ?>" 
                               class="btn btn-small"
                               style="background: #e74c3c; color: white;"
                               onclick="return confirm('<?php echo $lang === 'fr' ? 'Êtes-vous sûr de vouloir supprimer cet avis ?' : 'Are you sure you want to delete this review?'; ?>');">
                                🗑️ <?php echo $lang === 'fr' ? 'Supprimer' : 'Delete'; ?>
                            </a>
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
