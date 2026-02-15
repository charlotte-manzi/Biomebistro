<?php
/**
 * BiomeBistro - Supprimer un Avis
 * Fonctionnalité de suppression pour les avis
 */

require_once __DIR__ . '/../vendor/autoload.php';

use BiomeBistro\Models\Review;
use BiomeBistro\Models\Restaurant;
use BiomeBistro\Utils\Language;

session_start();
Language::init();
Language::setLanguage($_SESSION['lang'] ?? 'fr');
$lang = Language::getCurrentLanguage();

$reviewId = $_GET['id'] ?? null;
$userEmail = $_GET['email'] ?? 'demo@example.com';

if (!$reviewId) {
    header('Location: my-reviews.php?email=' . urlencode($userEmail));
    exit;
}

$reviewModel = new Review();
$review = $reviewModel->getById($reviewId);

if (!$review) {
    header('Location: my-reviews.php?email=' . urlencode($userEmail));
    exit;
}

$restaurantModel = new Restaurant();
$restaurant = $restaurantModel->getById((string)$review['restaurant_id']);

// Traitement de la suppression
$deleted = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
    $result = $reviewModel->delete($reviewId);
    if ($result) {
        $deleted = true;
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang === 'fr' ? 'Supprimer mon avis' : 'Delete my review'; ?> - BiomeBistro</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/animations.css">
    <style>
        .delete-container {
            max-width: 600px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        .delete-card {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            border: 2px solid #f8d7da;
        }
        
        .warning-icon {
            text-align: center;
            font-size: 5rem;
            margin-bottom: 1rem;
        }
        
        .warning-message {
            background: #fff3cd;
            border: 2px solid #ffc107;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }
        
        .review-summary {
            background: var(--bg-light);
            padding: 1.5rem;
            border-radius: 8px;
            margin: 1.5rem 0;
        }
        
        .success-card {
            background: #d4edda;
            border: 2px solid #c3e6cb;
            padding: 2rem;
            border-radius: 12px;
            text-align: center;
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
                    <a href="my-reviews.php?email=<?php echo urlencode($userEmail); ?>">
                        <?php echo $lang === 'fr' ? '⭐ Mes Avis' : '⭐ My Reviews'; ?>
                    </a>
                </nav>
                <div class="language-switcher">
                    <a href="?id=<?php echo $reviewId; ?>&email=<?php echo urlencode($userEmail); ?>&lang=fr" class="lang-btn <?php echo $lang === 'fr' ? 'active' : ''; ?>">🇫🇷 FR</a>
                    <a href="?id=<?php echo $reviewId; ?>&email=<?php echo urlencode($userEmail); ?>&lang=en" class="lang-btn <?php echo $lang === 'en' ? 'active' : ''; ?>">🇬🇧 EN</a>
                </div>
            </div>
        </div>
    </header>

    <section class="top-rated-section">
        <div class="delete-container">
            <?php if ($deleted): ?>
                <div class="success-card">
                    <div style="font-size: 5rem; margin-bottom: 1rem;">✅</div>
                    <h2><?php echo $lang === 'fr' ? 'Avis supprimé' : 'Review deleted'; ?></h2>
                    <p style="margin: 1rem 0;">
                        <?php echo $lang === 'fr' ? 'Votre avis a été supprimé avec succès.' : 'Your review has been deleted successfully.'; ?>
                    </p>
                    <a href="my-reviews.php?email=<?php echo urlencode($userEmail); ?>" class="btn btn-primary" style="margin-top: 2rem;">
                        <?php echo $lang === 'fr' ? 'Retour à mes avis' : 'Back to my reviews'; ?>
                    </a>
                </div>
            <?php else: ?>
                <div class="delete-card">
                    <div class="warning-icon">⚠️</div>
                    <h1 style="text-align: center; margin-bottom: 1rem;">
                        <?php echo $lang === 'fr' ? 'Supprimer cet avis ?' : 'Delete this review?'; ?>
                    </h1>
                    
                    <div class="warning-message">
                        <strong><?php echo $lang === 'fr' ? 'Attention :' : 'Warning:'; ?></strong>
                        <?php echo $lang === 'fr' ? 'Cette action est irréversible. Votre avis sera définitivement supprimé.' : 'This action is irreversible. Your review will be permanently deleted.'; ?>
                    </div>
                    
                    <h3><?php echo $lang === 'fr' ? 'Avis à supprimer :' : 'Review to delete:'; ?></h3>
                    
                    <div class="review-summary">
                        <p><strong><?php echo $lang === 'fr' ? 'Restaurant :' : 'Restaurant:'; ?></strong>
                        <?php echo htmlspecialchars($restaurant['name']); ?></p>
                        
                        <p><strong><?php echo $lang === 'fr' ? 'Note :' : 'Rating:'; ?></strong>
                        <?php for ($i = 1; $i <= 5; $i++) echo $i <= $review['rating'] ? '⭐' : '☆'; ?>
                        </p>
                        
                        <?php if (!empty($review['title'])): ?>
                        <p><strong><?php echo $lang === 'fr' ? 'Titre :' : 'Title:'; ?></strong>
                        <?php echo htmlspecialchars($review['title']); ?></p>
                        <?php endif; ?>
                        
                        <p><strong><?php echo $lang === 'fr' ? 'Commentaire :' : 'Comment:'; ?></strong></p>
                        <p style="font-style: italic; color: var(--text-light);">
                            "<?php echo htmlspecialchars($review['comment']); ?>"
                        </p>
                    </div>
                    
                    <form method="POST">
                        <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                            <button type="submit" name="confirm_delete" value="1"
                                    class="btn" style="flex: 1; background: #e74c3c; color: white;">
                                🗑️ <?php echo $lang === 'fr' ? 'Confirmer la suppression' : 'Confirm deletion'; ?>
                            </button>
                            <a href="my-reviews.php?email=<?php echo urlencode($userEmail); ?>" class="btn btn-secondary">
                                <?php echo $lang === 'fr' ? 'Annuler' : 'Cancel'; ?>
                            </a>
                        </div>
                    </form>
                </div>
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