<?php
/**
 * BiomeBistro - Modifier un Avis
 * Fonctionnalité de mise à jour pour les avis
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
$success = false;
$errors = [];

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

// Traitement de la soumission du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updateData = [
        'rating'  => intval($_POST['rating'] ?? 0),
        'title'   => $_POST['title'] ?? '',
        'comment' => $_POST['comment'] ?? ''
    ];
    
    // Validation
    if ($updateData['rating'] < 1 || $updateData['rating'] > 5) {
        $errors[] = $lang === 'fr' ? 'La note doit être entre 1 et 5' : 'Rating must be between 1 and 5';
    }
    if (empty($updateData['title'])) {
        $errors[] = $lang === 'fr' ? 'Le titre est requis' : 'Title is required';
    }
    if (empty($updateData['comment'])) {
        $errors[] = $lang === 'fr' ? 'Le commentaire est requis' : 'Comment is required';
    }
    
    if (empty($errors)) {
        $result = $reviewModel->update($reviewId, $updateData);
        if ($result) {
            $success = true;
            // Recharger l'avis mis à jour
            $review = $reviewModel->getById($reviewId);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang === 'fr' ? 'Modifier mon avis' : 'Edit my review'; ?> - BiomeBistro</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/animations.css">
    <style>
        .edit-container {
            max-width: 700px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        .edit-form {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .restaurant-info {
            background: var(--bg-light);
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            font-size: 1rem;
        }
        
        .rating-input {
            display: flex;
            gap: 0.5rem;
            font-size: 2rem;
        }
        
        .rating-input .star {
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .rating-input .star:hover,
        .rating-input .star.active {
            transform: scale(1.2);
        }
        
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }
        
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
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
        <div class="edit-container">
            <h1><?php echo $lang === 'fr' ? 'Modifier mon avis' : 'Edit my review'; ?></h1>
            
            <?php if ($success): ?>
                <div class="success-message">
                    ✅ <?php echo $lang === 'fr' ? 'Votre avis a été modifié avec succès !' : 'Your review has been updated successfully!'; ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($errors)): ?>
                <div class="error-message">
                    <strong><?php echo $lang === 'fr' ? 'Erreurs :' : 'Errors:'; ?></strong>
                    <ul style="margin: 0.5rem 0 0 1.5rem;">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <div class="restaurant-info">
                <h3><?php echo htmlspecialchars($restaurant['name']); ?></h3>
                <p style="margin-top: 0.5rem; color: var(--text-light);">
                    📍 <?php echo htmlspecialchars($restaurant['location']['address']); ?>
                </p>
            </div>
            
            <form method="POST" class="edit-form">
                <div class="form-group">
                    <label><?php echo $lang === 'fr' ? 'Votre note' : 'Your rating'; ?> *</label>
                    <div class="rating-input">
                        <input type="hidden" name="rating" id="rating-value" value="<?php echo $review['rating']; ?>">
                        <span class="star" data-rating="1">☆</span>
                        <span class="star" data-rating="2">☆</span>
                        <span class="star" data-rating="3">☆</span>
                        <span class="star" data-rating="4">☆</span>
                        <span class="star" data-rating="5">☆</span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="title"><?php echo $lang === 'fr' ? 'Titre' : 'Title'; ?> *</label>
                    <input type="text" id="title" name="title" required value="<?php echo htmlspecialchars($review['title'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="comment"><?php echo $lang === 'fr' ? 'Commentaire' : 'Comment'; ?> *</label>
                    <textarea id="comment" name="comment" rows="6" required><?php echo htmlspecialchars($review['comment']); ?></textarea>
                </div>

                <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                    <button type="submit" class="btn btn-primary" style="flex: 1;">
                        ✅ <?php echo $lang === 'fr' ? 'Enregistrer' : 'Save'; ?>
                    </button>
                    <a href="my-reviews.php?email=<?php echo urlencode($userEmail); ?>" class="btn btn-secondary">
                        <?php echo $lang === 'fr' ? 'Annuler' : 'Cancel'; ?>
                    </a>
                </div>
            </form>
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
        // Étoiles de notation
        const stars = document.querySelectorAll('.star');
        const ratingInput = document.getElementById('rating-value');
        
        // Initialiser avec la note actuelle
        const currentRating = parseInt(ratingInput.value);
        stars.forEach((s, i) => {
            if (i < currentRating) {
                s.classList.add('active');
                s.textContent = '⭐';
            }
        });
        
        stars.forEach((star, index) => {
            star.addEventListener('click', function() {
                const rating = this.getAttribute('data-rating');
                ratingInput.value = rating;
                
                // Mettre à jour l'état visuel
                stars.forEach((s, i) => {
                    if (i < rating) {
                        s.classList.add('active');
                        s.textContent = '⭐';
                    } else {
                        s.classList.remove('active');
                        s.textContent = '☆';
                    }
                });
            });
            
            star.addEventListener('mouseenter', function() {
                const rating = this.getAttribute('data-rating');
                stars.forEach((s, i) => {
                    s.textContent = (i < rating) ? '⭐' : '☆';
                });
            });
        });
        
        document.querySelector('.rating-input').addEventListener('mouseleave', function() {
            const currentRating = parseInt(ratingInput.value) || 0;
            stars.forEach((s, i) => {
                s.textContent = (i < currentRating) ? '⭐' : '☆';
            });
        });
    </script>
</body>
</html>