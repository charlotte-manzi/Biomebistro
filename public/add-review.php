<?php
/**
 * BiomeBistro - Page d'ajout d'avis
 */

require_once __DIR__ . '/../vendor/autoload.php';

use BiomeBistro\Models\Restaurant;
use BiomeBistro\Models\Review;
use BiomeBistro\Utils\Language;
use BiomeBistro\Utils\Validator;

session_start();
Language::init();
Language::setLanguage($_SESSION['lang'] ?? 'fr');
$lang = Language::getCurrentLanguage();

$restaurantId = $_GET['restaurant'] ?? null;
$success = false;
$errors = [];

if (!$restaurantId) {
    header('Location: restaurants.php');
    exit;
}

$restaurantModel = new Restaurant();
$restaurant = $restaurantModel->getById($restaurantId);

if (!$restaurant) {
    header('Location: restaurants.php');
    exit;
}

// Traitement de la soumission du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reviewModel = new Review();
    
    $data = [
        'restaurant_id' => $restaurantId,
        'reviewer_name' => $_POST['name'] ?? '',
        'reviewer_email' => $_POST['email'] ?? '',
        'rating' => intval($_POST['rating'] ?? 0),
        'title' => $_POST['title'] ?? '',
        'comment' => $_POST['comment'] ?? '',
        'visit_date' => $_POST['visit_date'] ?? null
    ];
    
    $errors = Validator::validateReview($data);
    
    if (empty($errors)) {
        $reviewId = $reviewModel->create($data);
        if ($reviewId) {
            $success = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo Language::t('write_review'); ?> - <?php echo htmlspecialchars($restaurant['name']); ?></title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/animations.css">
    <style>
        .review-form {
            max-width: 700px;
            margin: 2rem auto;
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--text-color);
        }
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            font-size: 1rem;
        }
        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary-color);
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
        .error-message {
            background: #ffebee;
            color: #c62828;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }
        .success-message {
            background: #e8f5e9;
            color: #2e7d32;
            padding: 2rem;
            border-radius: 12px;
            text-align: center;
        }
        .success-message h2 {
            color: #2e7d32;
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
                    <a href="my-reservations.php?email=demo@example.com">
                        <?php echo $lang === 'fr' ? 'Mes Réservations' : 'My Reservations'; ?>
                    </a>
                </nav>
                <div class="language-switcher">
                    <a href="?restaurant=<?php echo $restaurantId; ?>&lang=fr" class="lang-btn <?php echo $lang === 'fr' ? 'active' : ''; ?>">🇫🇷 FR</a>
                    <a href="?restaurant=<?php echo $restaurantId; ?>&lang=en" class="lang-btn <?php echo $lang === 'en' ? 'active' : ''; ?>">🇬🇧 EN</a>
                </div>
            </div>
        </div>
    </header>

    <section class="top-rated-section">
        <div class="container">
            <?php if ($success): ?>
                <div class="review-form">
                    <div class="success-message">
                        <h2>✅ <?php echo $lang === 'fr' ? 'Avis publié !' : 'Review published!'; ?></h2>
                        <p><?php echo $lang === 'fr' ? 'Merci pour votre avis ! Il a été publié avec succès.' : 'Thank you for your review! It has been published successfully.'; ?></p>
                        <div style="margin-top: 2rem;">
                            <a href="restaurant-detail.php?id=<?php echo $restaurantId; ?>" class="btn btn-primary">
                                <?php echo $lang === 'fr' ? 'Voir le restaurant' : 'View restaurant'; ?>
                            </a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <h1><?php echo Language::t('write_review'); ?></h1>
                <h2><?php echo htmlspecialchars($restaurant['name']); ?></h2>
                
                <?php if (!empty($errors)): ?>
                    <div class="error-message">
                        <strong><?php echo $lang === 'fr' ? 'Erreurs :' : 'Errors:'; ?></strong>
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" class="review-form">
                    <div class="form-group">
                        <label><?php echo $lang === 'fr' ? 'Votre note' : 'Your rating'; ?> *</label>
                        <div class="rating-input">
                            <input type="hidden" name="rating" id="rating-value" value="<?php echo $_POST['rating'] ?? 0; ?>">
                            <span class="star" data-rating="1">☆</span>
                            <span class="star" data-rating="2">☆</span>
                            <span class="star" data-rating="3">☆</span>
                            <span class="star" data-rating="4">☆</span>
                            <span class="star" data-rating="5">☆</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="name"><?php echo $lang === 'fr' ? 'Votre nom' : 'Your name'; ?> *</label>
                        <input type="text" id="name" name="name" required value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="email"><?php echo Language::t('email'); ?> *</label>
                        <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="visit_date"><?php echo $lang === 'fr' ? 'Date de visite' : 'Visit date'; ?></label>
                        <input type="date" id="visit_date" name="visit_date" max="<?php echo date('Y-m-d'); ?>" value="<?php echo htmlspecialchars($_POST['visit_date'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="title"><?php echo $lang === 'fr' ? 'Titre de votre avis' : 'Review title'; ?> *</label>
                        <input type="text" id="title" name="title" required placeholder="<?php echo $lang === 'fr' ? 'Résumez votre expérience' : 'Summarize your experience'; ?>" value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="comment"><?php echo $lang === 'fr' ? 'Votre avis' : 'Your review'; ?> *</label>
                        <textarea id="comment" name="comment" rows="6" required placeholder="<?php echo $lang === 'fr' ? 'Partagez votre expérience...' : 'Share your experience...'; ?>"><?php echo htmlspecialchars($_POST['comment'] ?? ''); ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        ✍️ <?php echo $lang === 'fr' ? 'Publier mon avis' : 'Publish my review'; ?>
                    </button>
                </form>
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
    <script>
        // Fonctionnalité des étoiles de notation
        const stars = document.querySelectorAll('.star');
        const ratingInput = document.getElementById('rating-value');
        
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
        
        // Initialiser avec la valeur actuelle
        const initialRating = parseInt(ratingInput.value) || 0;
        if (initialRating > 0) {
            stars.forEach((s, i) => {
                if (i < initialRating) {
                    s.classList.add('active');
                    s.textContent = '⭐';
                }
            });
        }
    </script>
</body>
</html>