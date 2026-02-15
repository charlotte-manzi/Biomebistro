<?php
/**
 * BiomeBistro - Mes Réservations
 * Gestion des réservations de l'utilisateur
 */

require_once __DIR__ . '/../vendor/autoload.php';

use BiomeBistro\Models\Reservation;
use BiomeBistro\Models\Restaurant;
use BiomeBistro\Utils\Language;

session_start();
Language::init();
Language::setLanguage($_SESSION['lang'] ?? 'fr');
$lang = Language::getCurrentLanguage();

// Pour cet exemple, on simule un email utilisateur
// Dans une vraie app, ce serait $_SESSION['user_email']
$userEmail = $_GET['email'] ?? 'demo@example.com';

$reservationModel = new Reservation();
$restaurantModel = new Restaurant();

// Récupérer les réservations de l'utilisateur par email
$userReservations = $reservationModel->getByCustomerEmail($userEmail);

?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang === 'fr' ? 'Mes Réservations' : 'My Reservations'; ?> - BiomeBistro</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/animations.css">
    <style>
        .reservations-container {
            max-width: 1000px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        .reservation-card {
            background: white;
            border: 2px solid var(--border-color);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        
        .reservation-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.15);
        }
        
        .reservation-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--border-color);
        }
        
        .reservation-restaurant {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--primary-color);
        }
        
        .reservation-status {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-confirmed {
            background: #d4edda;
            color: #155724;
        }
        
        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }
        
        .reservation-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin: 1rem 0;
        }
        
        .detail-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .detail-icon {
            font-size: 1.2rem;
        }
        
        .detail-text {
            font-size: 1rem;
        }
        
        .reservation-actions {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }
        
        .btn-edit {
            background: var(--primary-color);
            color: white;
        }
        
        .btn-cancel {
            background: #e74c3c;
            color: white;
        }
        
        .btn-view {
            background: #3498db;
            color: white;
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
        
        .user-info {
            background: var(--bg-light);
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 2rem;
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
                    <a href="my-reservations.php?email=<?php echo urlencode($userEmail); ?>" class="active">
                        <?php echo $lang === 'fr' ? 'Mes Réservations' : 'My Reservations'; ?>
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
        <div class="reservations-container">
            <h1><?php echo $lang === 'fr' ? 'Mes Réservations' : 'My Reservations'; ?></h1>
            
            <div class="user-info">
                <strong><?php echo $lang === 'fr' ? 'Compte :' : 'Account:'; ?></strong> <?php echo htmlspecialchars($userEmail); ?>
                <p style="margin-top: 0.5rem; color: var(--text-light); font-size: 0.9rem;">
                    <?php echo $lang === 'fr' ? 'Demo - Dans une vraie application, vous seriez connecté avec votre compte.' : 'Demo - In a real app, you would be logged in with your account.'; ?>
                </p>
            </div>

            <?php if (empty($userReservations)): ?>
                <div class="empty-state">
                    <div class="empty-icon">📅</div>
                    <h2><?php echo $lang === 'fr' ? 'Aucune réservation' : 'No reservations'; ?></h2>
                    <p><?php echo $lang === 'fr' ? 'Vous n\'avez pas encore de réservation.' : 'You don\'t have any reservations yet.'; ?></p>
                    <a href="restaurants.php" class="btn btn-primary" style="margin-top: 1.5rem;">
                        <?php echo $lang === 'fr' ? 'Découvrir nos restaurants' : 'Discover our restaurants'; ?>
                    </a>
                </div>
            <?php else: ?>
                <?php foreach ($userReservations as $reservation):
                    $restaurant = $restaurantModel->getById((string)$reservation['restaurant_id']);
                    // Gérer le format de date MongoDB
                    if ($reservation['reservation_date'] instanceof MongoDB\BSON\UTCDateTime) {
                        $reservationDate = $reservation['reservation_date']->toDateTime();
                    } else {
                        $reservationDate = new DateTime($reservation['reservation_date']);
                    }
                ?>
                    <div class="reservation-card">
                        <div class="reservation-header">
                            <div class="reservation-restaurant">
                                <?php echo htmlspecialchars($restaurant['name'] ?? 'Restaurant'); ?>
                            </div>
                            <div class="reservation-status status-<?php echo $reservation['status']; ?>">
                                <?php
                                // Traduire les statuts selon la langue
                                $statusLabels = [
                                    'pending'   => $lang === 'fr' ? 'En attente' : 'Pending',
                                    'confirmed' => $lang === 'fr' ? 'Confirmée'  : 'Confirmed',
                                    'cancelled' => $lang === 'fr' ? 'Annulée'    : 'Cancelled'
                                ];
                                echo $statusLabels[$reservation['status']] ?? $reservation['status'];
                                ?>
                            </div>
                        </div>
                        
                        <div class="reservation-details">
                            <div class="detail-item">
                                <span class="detail-icon">📅</span>
                                <span class="detail-text">
                                    <strong><?php echo $reservationDate->format('d/m/Y'); ?></strong>
                                </span>
                            </div>
                            
                            <div class="detail-item">
                                <span class="detail-icon">🕐</span>
                                <span class="detail-text">
                                    <strong><?php echo htmlspecialchars($reservation['reservation_time']); ?></strong>
                                </span>
                            </div>
                            
                            <div class="detail-item">
                                <span class="detail-icon">👥</span>
                                <span class="detail-text">
                                    <strong><?php echo $reservation['party_size']; ?> <?php echo $reservation['party_size'] > 1 ? ($lang === 'fr' ? 'personnes' : 'people') : ($lang === 'fr' ? 'personne' : 'person'); ?></strong>
                                </span>
                            </div>
                            
                            <div class="detail-item">
                                <span class="detail-icon">📍</span>
                                <span class="detail-text">
                                    <?php echo htmlspecialchars($restaurant['location']['district'] ?? ''); ?>
                                </span>
                            </div>
                        </div>
                        
                        <?php if (!empty($reservation['special_requests'])): ?>
                            <div style="margin-top: 1rem; padding: 1rem; background: var(--bg-light); border-radius: 8px;">
                                <strong><?php echo $lang === 'fr' ? 'Demandes spéciales :' : 'Special requests:'; ?></strong>
                                <p style="margin-top: 0.5rem;"><?php echo htmlspecialchars($reservation['special_requests']); ?></p>
                            </div>
                        <?php endif; ?>
                        
                        <div class="reservation-actions">
                            <a href="restaurant-detail.php?id=<?php echo $reservation['restaurant_id']; ?>" class="btn btn-small btn-view">
                                <?php echo $lang === 'fr' ? 'Voir le restaurant' : 'View restaurant'; ?>
                            </a>
                            
                            <?php if ($reservation['status'] !== 'cancelled'): ?>
                                <a href="edit-reservation.php?id=<?php echo $reservation['_id']; ?>&email=<?php echo urlencode($userEmail); ?>" class="btn btn-small btn-edit">
                                    ✏️ <?php echo $lang === 'fr' ? 'Modifier' : 'Edit'; ?>
                                </a>
                                
                                <a href="cancel-reservation.php?id=<?php echo $reservation['_id']; ?>&email=<?php echo urlencode($userEmail); ?>"
                                   class="btn btn-small btn-cancel"
                                   onclick="return confirm('<?php echo $lang === 'fr' ? 'Êtes-vous sûr de vouloir annuler cette réservation ?' : 'Are you sure you want to cancel this reservation?'; ?>');">
                                    ❌ <?php echo $lang === 'fr' ? 'Annuler' : 'Cancel'; ?>
                                </a>
                            <?php endif; ?>
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