<?php
/**
 * BiomeBistro - Toutes les Réservations
 * Afficher TOUTES les réservations (Vue Admin)
 */

require_once __DIR__ . '/../vendor/autoload.php';

use BiomeBistro\Models\Reservation;
use BiomeBistro\Models\Restaurant;
use BiomeBistro\Utils\Language;

session_start();
Language::init();
Language::setLanguage($_SESSION['lang'] ?? 'fr');
$lang = Language::getCurrentLanguage();

$reservationModel = new Reservation();
$restaurantModel = new Restaurant();

// Récupérer TOUTES les réservations
$allReservations = $reservationModel->getAll();

// Statistiques
$totalReservations = count($allReservations);
$pendingCount   = count(array_filter($allReservations, fn($r) => $r['status'] === 'pending'));
$confirmedCount = count(array_filter($allReservations, fn($r) => $r['status'] === 'confirmed'));
$cancelledCount = count(array_filter($allReservations, fn($r) => $r['status'] === 'cancelled'));

?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang === 'fr' ? 'Toutes les Réservations' : 'All Reservations'; ?> - BiomeBistro</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/animations.css">
    <style>
        .reservations-container {
            max-width: 1400px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .stat-number {
            font-size: 3rem;
            font-weight: bold;
            color: var(--primary-color);
        }
        
        .stat-label {
            color: var(--text-light);
            margin-top: 0.5rem;
        }
        
        .reservations-table {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        thead {
            background: var(--bg-light);
        }
        
        th {
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            border-bottom: 2px solid var(--border-color);
        }
        
        td {
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
        }
        
        tr:hover {
            background: var(--bg-light);
        }
        
        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
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
                    <a href="all-reservations.php" class="active">
                        <?php echo $lang === 'fr' ? '📋 Toutes les Réservations' : '📋 All Reservations'; ?>
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
        <div class="reservations-container">
            <h1><?php echo $lang === 'fr' ? 'Toutes les Réservations' : 'All Reservations'; ?></h1>
            <p style="color: var(--text-light); margin-bottom: 2rem;">
                <?php echo $lang === 'fr' ? 'Vue d\'ensemble de toutes les réservations du système' : 'Overview of all system reservations'; ?>
            </p>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $totalReservations; ?></div>
                    <div class="stat-label">Total</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" style="color: #856404;"><?php echo $pendingCount; ?></div>
                    <div class="stat-label"><?php echo $lang === 'fr' ? 'En attente' : 'Pending'; ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" style="color: #155724;"><?php echo $confirmedCount; ?></div>
                    <div class="stat-label"><?php echo $lang === 'fr' ? 'Confirmées' : 'Confirmed'; ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" style="color: #721c24;"><?php echo $cancelledCount; ?></div>
                    <div class="stat-label"><?php echo $lang === 'fr' ? 'Annulées' : 'Cancelled'; ?></div>
                </div>
            </div>

            <?php if (empty($allReservations)): ?>
                <div style="text-align: center; padding: 4rem 2rem;">
                    <div style="font-size: 5rem; margin-bottom: 1rem; opacity: 0.5;">📅</div>
                    <h2><?php echo $lang === 'fr' ? 'Aucune réservation' : 'No reservations'; ?></h2>
                </div>
            <?php else: ?>
                <div class="reservations-table">
                    <table>
                        <thead>
                            <tr>
                                <th><?php echo $lang === 'fr' ? 'Restaurant' : 'Restaurant'; ?></th>
                                <th><?php echo $lang === 'fr' ? 'Client' : 'Customer'; ?></th>
                                <th><?php echo $lang === 'fr' ? 'Date' : 'Date'; ?></th>
                                <th><?php echo $lang === 'fr' ? 'Heure' : 'Time'; ?></th>
                                <th><?php echo $lang === 'fr' ? 'Personnes' : 'Party'; ?></th>
                                <th><?php echo $lang === 'fr' ? 'Statut' : 'Status'; ?></th>
                                <th><?php echo $lang === 'fr' ? 'Contact' : 'Contact'; ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($allReservations as $reservation): 
                                $restaurant = $restaurantModel->getById((string)$reservation['restaurant_id']);
                                
                                // Gérer le format de date
                                if ($reservation['reservation_date'] instanceof MongoDB\BSON\UTCDateTime) {
                                    $reservationDate = $reservation['reservation_date']->toDateTime()->format('d/m/Y');
                                } else {
                                    $reservationDate = date('d/m/Y', strtotime($reservation['reservation_date']));
                                }
                            ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($restaurant['name'] ?? 'N/A'); ?></strong><br>
                                        <small style="color: var(--text-light);">
                                            <?php echo htmlspecialchars($restaurant['location']['district'] ?? ''); ?>
                                        </small>
                                    </td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($reservation['customer_info']['name']); ?></strong><br>
                                        <small style="color: var(--text-light);">
                                            <?php echo htmlspecialchars($reservation['customer_info']['email']); ?>
                                        </small>
                                    </td>
                                    <td><?php echo $reservationDate; ?></td>
                                    <td><?php echo htmlspecialchars($reservation['reservation_time']); ?></td>
                                    <td><?php echo $reservation['party_size']; ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo $reservation['status']; ?>">
                                            <?php
                                            // Traduire les statuts selon la langue
                                            $statusLabels = [
                                                'pending'   => $lang === 'fr' ? 'En attente' : 'Pending',
                                                'confirmed' => $lang === 'fr' ? 'Confirmée'  : 'Confirmed',
                                                'cancelled' => $lang === 'fr' ? 'Annulée'    : 'Cancelled'
                                            ];
                                            echo $statusLabels[$reservation['status']] ?? $reservation['status'];
                                            ?>
                                        </span>
                                    </td>
                                    <td>
                                        <small><?php echo htmlspecialchars($reservation['customer_info']['phone']); ?></small>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
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