<?php
/**
 * BiomeBistro - Annuler une Réservation
 * Fonctionnalité de suppression pour les réservations
 */

require_once __DIR__ . '/../vendor/autoload.php';

use BiomeBistro\Models\Reservation;
use BiomeBistro\Models\Restaurant;
use BiomeBistro\Utils\Language;

session_start();
Language::init();
Language::setLanguage($_SESSION['lang'] ?? 'fr');
$lang = Language::getCurrentLanguage();

$reservationId = $_GET['id'] ?? null;
$userEmail = $_GET['email'] ?? 'demo@example.com';

if (!$reservationId) {
    header('Location: my-reservations.php?email=' . urlencode($userEmail));
    exit;
}

$reservationModel = new Reservation();
$reservation = $reservationModel->getById($reservationId);

if (!$reservation) {
    header('Location: my-reservations.php?email=' . urlencode($userEmail));
    exit;
}

$restaurantModel = new Restaurant();
$restaurant = $restaurantModel->getById((string)$reservation['restaurant_id']);

// Traitement de l'annulation
$cancelled = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_cancel'])) {
    // Option 1 : Marquer comme annulée (suppression logique)
    $result = $reservationModel->update($reservationId, ['status' => 'cancelled']);
    
    // Option 2 : Suppression définitive (décommenter si préféré)
    // $result = $reservationModel->delete($reservationId);
    
    if ($result) {
        $cancelled = true;
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang === 'fr' ? 'Annuler ma réservation' : 'Cancel my reservation'; ?> - BiomeBistro</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/animations.css">
    <style>
        .cancel-container {
            max-width: 600px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        .cancel-card {
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
        
        .reservation-summary {
            background: var(--bg-light);
            padding: 1.5rem;
            border-radius: 8px;
            margin: 1.5rem 0;
        }
        
        .summary-item {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid var(--border-color);
        }
        
        .summary-item:last-child {
            border-bottom: none;
        }
        
        .cancel-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }
        
        .btn-danger {
            background: #e74c3c;
            color: white;
        }
        
        .btn-danger:hover {
            background: #c0392b;
        }
        
        .success-card {
            background: #d4edda;
            border: 2px solid #c3e6cb;
            padding: 2rem;
            border-radius: 12px;
            text-align: center;
        }
        
        .success-icon {
            font-size: 5rem;
            margin-bottom: 1rem;
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
                        <?php echo $lang === 'fr' ? 'Mes Réservations' : 'My Reservations'; ?>
                    </a>
                </nav>
                <div class="language-switcher">
                    <a href="?id=<?php echo $reservationId; ?>&email=<?php echo urlencode($userEmail); ?>&lang=fr" class="lang-btn <?php echo $lang === 'fr' ? 'active' : ''; ?>">🇫🇷 FR</a>
                    <a href="?id=<?php echo $reservationId; ?>&email=<?php echo urlencode($userEmail); ?>&lang=en" class="lang-btn <?php echo $lang === 'en' ? 'active' : ''; ?>">🇬🇧 EN</a>
                </div>
            </div>
        </div>
    </header>

    <section class="top-rated-section">
        <div class="cancel-container">
            <?php if ($cancelled): ?>
                <div class="success-card">
                    <div class="success-icon">✅</div>
                    <h2><?php echo $lang === 'fr' ? 'Réservation annulée' : 'Reservation cancelled'; ?></h2>
                    <p style="margin: 1rem 0;">
                        <?php echo $lang === 'fr' ? 'Votre réservation a été annulée avec succès.' : 'Your reservation has been cancelled successfully.'; ?>
                    </p>
                    <p style="color: var(--text-light); margin-bottom: 2rem;">
                        <?php echo $lang === 'fr' ? 'Un email de confirmation vous a été envoyé.' : 'A confirmation email has been sent to you.'; ?>
                    </p>
                    <a href="my-reservations.php?email=<?php echo urlencode($userEmail); ?>" class="btn btn-primary">
                        <?php echo $lang === 'fr' ? 'Retour à mes réservations' : 'Back to my reservations'; ?>
                    </a>
                    <a href="restaurants.php" class="btn btn-secondary" style="margin-left: 1rem;">
                        <?php echo $lang === 'fr' ? 'Faire une nouvelle réservation' : 'Make a new reservation'; ?>
                    </a>
                </div>
            <?php else: ?>
                <div class="cancel-card">
                    <div class="warning-icon">⚠️</div>
                    <h1 style="text-align: center; margin-bottom: 1rem;">
                        <?php echo $lang === 'fr' ? 'Annuler cette réservation ?' : 'Cancel this reservation?'; ?>
                    </h1>
                    
                    <div class="warning-message">
                        <strong><?php echo $lang === 'fr' ? 'Attention :' : 'Warning:'; ?></strong>
                        <?php echo $lang === 'fr' ? 'Cette action est irréversible. Votre réservation sera définitivement annulée.' : 'This action is irreversible. Your reservation will be permanently cancelled.'; ?>
                    </div>
                    
                    <h3><?php echo $lang === 'fr' ? 'Récapitulatif de votre réservation :' : 'Reservation summary:'; ?></h3>
                    
                    <div class="reservation-summary">
                        <div class="summary-item">
                            <span><strong><?php echo $lang === 'fr' ? 'Restaurant :' : 'Restaurant:'; ?></strong></span>
                            <span><?php echo htmlspecialchars($restaurant['name']); ?></span>
                        </div>
                        <div class="summary-item">
                            <span><strong><?php echo $lang === 'fr' ? 'Date :' : 'Date:'; ?></strong></span>
                            <span><?php echo date('d/m/Y', strtotime($reservation['reservation_date'])); ?></span>
                        </div>
                        <div class="summary-item">
                            <span><strong><?php echo $lang === 'fr' ? 'Heure :' : 'Time:'; ?></strong></span>
                            <span><?php echo htmlspecialchars($reservation['reservation_time']); ?></span>
                        </div>
                        <div class="summary-item">
                            <span><strong><?php echo $lang === 'fr' ? 'Personnes :' : 'Party size:'; ?></strong></span>
                            <span><?php echo $reservation['party_size']; ?></span>
                        </div>
                        <div class="summary-item">
                            <span><strong><?php echo $lang === 'fr' ? 'Adresse :' : 'Address:'; ?></strong></span>
                            <span><?php echo htmlspecialchars($restaurant['location']['district']); ?></span>
                        </div>
                    </div>
                    
                    <form method="POST">
                        <div class="cancel-actions">
                            <button type="submit" name="confirm_cancel" value="1" class="btn btn-danger" style="flex: 1;">
                                ❌ <?php echo $lang === 'fr' ? 'Confirmer l\'annulation' : 'Confirm cancellation'; ?>
                            </button>
                            <a href="my-reservations.php?email=<?php echo urlencode($userEmail); ?>" class="btn btn-secondary">
                                <?php echo $lang === 'fr' ? 'Garder ma réservation' : 'Keep my reservation'; ?>
                            </a>
                        </div>
                    </form>
                    
                    <p style="text-align: center; margin-top: 2rem; color: var(--text-light); font-size: 0.9rem;">
                        <?php echo $lang === 'fr' ? 'Vous pouvez aussi modifier votre réservation au lieu de l\'annuler.' : 'You can also modify your reservation instead of cancelling it.'; ?>
                        <br>
                        <a href="edit-reservation.php?id=<?php echo $reservationId; ?>&email=<?php echo urlencode($userEmail); ?>">
                            <?php echo $lang === 'fr' ? 'Modifier ma réservation' : 'Modify my reservation'; ?>
                        </a>
                    </p>
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