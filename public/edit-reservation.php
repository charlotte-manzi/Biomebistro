<?php
/**
 * BiomeBistro - Modifier une Réservation
 * Fonctionnalité de mise à jour pour les réservations
 */

require_once __DIR__ . '/../vendor/autoload.php';

use BiomeBistro\Models\Reservation;
use BiomeBistro\Models\Restaurant;
use BiomeBistro\Utils\Language;
use BiomeBistro\Utils\Validator;

session_start();
Language::init();
Language::setLanguage($_SESSION['lang'] ?? 'fr');
$lang = Language::getCurrentLanguage();

$reservationId = $_GET['id'] ?? null;
$userEmail = $_GET['email'] ?? 'demo@example.com';
$success = false;
$errors = [];

if (!$reservationId) {
    header('Location: my-reservations.php?email=' . urlencode($userEmail));
    exit;
}

$reservationModel = new Reservation();
$restaurantModel = new Restaurant();

$reservation = $reservationModel->getById($reservationId);

if (!$reservation) {
    header('Location: my-reservations.php?email=' . urlencode($userEmail));
    exit;
}

$restaurant = $restaurantModel->getById((string)$reservation['restaurant_id']);

// Traitement de la soumission du formulaire (mise à jour)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updateData = [
        'reservation_date' => $_POST['date'] ?? '',
        'reservation_time' => $_POST['time'] ?? '',
        'party_size'       => intval($_POST['party_size'] ?? 0),
        'special_requests' => $_POST['special_requests'] ?? ''
    ];
    
    // Validation
    if (empty($updateData['reservation_date'])) {
        $errors[] = $lang === 'fr' ? 'La date est requise' : 'Date is required';
    }
    if (empty($updateData['reservation_time'])) {
        $errors[] = $lang === 'fr' ? 'L\'heure est requise' : 'Time is required';
    }
    if ($updateData['party_size'] < 1 || $updateData['party_size'] > 20) {
        $errors[] = $lang === 'fr' ? 'Le nombre de personnes doit être entre 1 et 20' : 'Party size must be between 1 and 20';
    }
    
    if (empty($errors)) {
        $result = $reservationModel->update($reservationId, $updateData);
        if ($result) {
            $success = true;
            // Recharger la réservation mise à jour
            $reservation = $reservationModel->getById($reservationId);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang === 'fr' ? 'Modifier ma réservation' : 'Edit my reservation'; ?> - BiomeBistro</title>
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
            color: var(--text-color);
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            font-size: 1rem;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary-color);
        }
        
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            border: 2px solid #c3e6cb;
        }
        
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            border: 2px solid #f5c6cb;
        }
        
        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
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
        <div class="edit-container">
            <h1><?php echo $lang === 'fr' ? 'Modifier ma réservation' : 'Edit my reservation'; ?></h1>
            
            <?php if ($success): ?>
                <div class="success-message">
                    ✅ <?php echo $lang === 'fr' ? 'Votre réservation a été modifiée avec succès !' : 'Your reservation has been updated successfully!'; ?>
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
                    <label for="date"><?php echo $lang === 'fr' ? 'Nouvelle date' : 'New date'; ?> *</label>
                    <input 
                        type="date" 
                        id="date" 
                        name="date" 
                        required 
                        min="<?php echo date('Y-m-d'); ?>" 
                        value="<?php echo htmlspecialchars($reservation['reservation_date']); ?>"
                    >
                </div>

                <div class="form-group">
                    <label for="time"><?php echo $lang === 'fr' ? 'Nouvelle heure' : 'New time'; ?> *</label>
                    <select id="time" name="time" required>
                        <?php for ($h = 11; $h <= 22; $h++): ?>
                            <?php for ($m = 0; $m < 60; $m += 30): ?>
                                <?php $time = sprintf('%02d:%02d', $h, $m); ?>
                                <option value="<?php echo $time; ?>" <?php echo ($reservation['reservation_time'] === $time) ? 'selected' : ''; ?>>
                                    <?php echo $time; ?>
                                </option>
                            <?php endfor; ?>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="party_size"><?php echo $lang === 'fr' ? 'Nombre de personnes' : 'Party size'; ?> *</label>
                    <select id="party_size" name="party_size" required>
                        <?php for ($i = 1; $i <= 12; $i++): ?>
                            <option value="<?php echo $i; ?>" <?php echo ($reservation['party_size'] == $i) ? 'selected' : ''; ?>>
                                <?php echo $i; ?> <?php echo $i === 1 ? ($lang === 'fr' ? 'personne' : 'person') : ($lang === 'fr' ? 'personnes' : 'people'); ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="special_requests"><?php echo $lang === 'fr' ? 'Demandes spéciales' : 'Special requests'; ?></label>
                    <textarea 
                        id="special_requests" 
                        name="special_requests" 
                        rows="4"
                        placeholder="<?php echo $lang === 'fr' ? 'Allergies, préférences...' : 'Allergies, preferences...'; ?>"
                    ><?php echo htmlspecialchars($reservation['special_requests'] ?? ''); ?></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary" style="flex: 1;">
                        ✅ <?php echo $lang === 'fr' ? 'Enregistrer les modifications' : 'Save changes'; ?>
                    </button>
                    <a href="my-reservations.php?email=<?php echo urlencode($userEmail); ?>" class="btn btn-secondary">
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
</body>
</html>