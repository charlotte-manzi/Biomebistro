<?php
/**
 * BiomeBistro - Page de Réservation
 */

require_once __DIR__ . '/../vendor/autoload.php';

use BiomeBistro\Models\Restaurant;
use BiomeBistro\Models\Reservation;
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
    $reservationModel = new Reservation();
    
    $data = [
        'restaurant_id' => $restaurantId,
        'customer_info' => [
            'name'  => $_POST['name']  ?? '',
            'email' => $_POST['email'] ?? '',
            'phone' => $_POST['phone'] ?? ''
        ],
        'reservation_date' => $_POST['date']    ?? '',
        'reservation_time' => $_POST['time']    ?? '',
        'party_size'       => intval($_POST['party_size'] ?? 0),
        'special_requests' => $_POST['special_requests'] ?? '',
        'status'           => 'pending'
    ];
    
    $errors = Validator::validateReservation($data);
    
    if (empty($errors)) {
        $reservationId = $reservationModel->create($data);
        if ($reservationId) {
            $success = true;
            $confirmationCode = 'BIO-' . strtoupper(substr($restaurantId, 0, 4)) . '-' . date('Ymd') . '-' . substr($reservationId, 0, 4);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo Language::t('book_table'); ?> - <?php echo htmlspecialchars($restaurant['name']); ?></title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/animations.css">
    <style>
        .reservation-form {
            max-width: 600px;
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
            margin-bottom: 1rem;
        }
        .confirmation-code {
            font-size: 1.5rem;
            font-weight: bold;
            background: #fff;
            padding: 1rem;
            border-radius: 8px;
            margin: 1rem 0;
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
                <div class="reservation-form">
                    <div class="success-message">
                        <h2>✅ <?php echo $lang === 'fr' ? 'Réservation confirmée !' : 'Reservation confirmed!'; ?></h2>
                        <p><?php echo $lang === 'fr' ? 'Votre réservation a été enregistrée avec succès.' : 'Your reservation has been successfully recorded.'; ?></p>
                        <div class="confirmation-code">
                            <?php echo $lang === 'fr' ? 'Code de confirmation' : 'Confirmation code'; ?>:<br>
                            <?php echo $confirmationCode ?? 'N/A'; ?>
                        </div>
                        <p style="margin-top: 1rem;">
                            <?php echo $lang === 'fr' ? 'Nous avons envoyé une confirmation à' : 'We sent a confirmation to'; ?> :
                            <strong><?php echo htmlspecialchars($_POST['email']); ?></strong>
                        </p>
                        <div style="margin-top: 2rem;">
                            <a href="restaurant-detail.php?id=<?php echo $restaurantId; ?>" class="btn btn-primary">
                                <?php echo $lang === 'fr' ? 'Retour au restaurant' : 'Back to restaurant'; ?>
                            </a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <h1><?php echo Language::t('book_table'); ?></h1>
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

                <form method="POST" class="reservation-form">
                    <div class="form-group">
                        <label for="name"><?php echo $lang === 'fr' ? 'Nom complet' : 'Full name'; ?> *</label>
                        <input type="text" id="name" name="name" required value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="email"><?php echo Language::t('email'); ?> *</label>
                        <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="phone"><?php echo $lang === 'fr' ? 'Téléphone' : 'Phone'; ?> *</label>
                        <input type="tel" id="phone" name="phone" required placeholder="+33 1 23 45 67 89" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="date"><?php echo Language::t('date'); ?> *</label>
                        <input type="date" id="date" name="date" required min="<?php echo date('Y-m-d'); ?>" value="<?php echo htmlspecialchars($_POST['date'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="time"><?php echo $lang === 'fr' ? 'Heure' : 'Time'; ?> *</label>
                        <select id="time" name="time" required>
                            <option value=""><?php echo $lang === 'fr' ? 'Sélectionner une heure' : 'Select a time'; ?></option>
                            <?php for ($h = 11; $h <= 22; $h++): ?>
                                <?php for ($m = 0; $m < 60; $m += 30): ?>
                                    <?php $time = sprintf('%02d:%02d', $h, $m); ?>
                                    <option value="<?php echo $time; ?>" <?php echo (($_POST['time'] ?? '') === $time) ? 'selected' : ''; ?>>
                                        <?php echo $time; ?>
                                    </option>
                                <?php endfor; ?>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="party_size"><?php echo $lang === 'fr' ? 'Nombre de personnes' : 'Party size'; ?> *</label>
                        <select id="party_size" name="party_size" required>
                            <option value=""><?php echo $lang === 'fr' ? 'Sélectionner' : 'Select'; ?></option>
                            <?php for ($i = 1; $i <= 12; $i++): ?>
                                <option value="<?php echo $i; ?>" <?php echo (($_POST['party_size'] ?? '') == $i) ? 'selected' : ''; ?>>
                                    <?php echo $i; ?> <?php echo $i === 1 ? ($lang === 'fr' ? 'personne' : 'person') : ($lang === 'fr' ? 'personnes' : 'people'); ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="special_requests"><?php echo $lang === 'fr' ? 'Demandes spéciales' : 'Special requests'; ?></label>
                        <textarea id="special_requests" name="special_requests" rows="4" placeholder="<?php echo $lang === 'fr' ? 'Allergies, préférences, occasion spéciale...' : 'Allergies, preferences, special occasion...'; ?>"><?php echo htmlspecialchars($_POST['special_requests'] ?? ''); ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        📅 <?php echo $lang === 'fr' ? 'Confirmer la réservation' : 'Confirm reservation'; ?>
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
</body>
</html>