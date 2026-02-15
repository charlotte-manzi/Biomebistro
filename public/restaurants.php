<?php
/**
 * BiomeBistro - Liste des restaurants avec filtres
 */

require_once __DIR__ . '/../vendor/autoload.php';

use BiomeBistro\Models\Restaurant;
use BiomeBistro\Models\Biome;
use BiomeBistro\Utils\Language;

session_start();

// Gérer le changement de langue
if (isset($_GET['lang']) && in_array($_GET['lang'], ['fr', 'en'])) {
    $_SESSION['lang'] = $_GET['lang'];
}

Language::init();
Language::setLanguage($_SESSION['lang'] ?? 'fr');
$lang = Language::getCurrentLanguage();

$restaurantModel = new Restaurant();
$biomeModel = new Biome();

// Récupération des paramètres de filtre depuis l'URL
$selectedBiome = $_GET['biome'] ?? '';
$minRating = isset($_GET['min_rating']) ? floatval($_GET['min_rating']) : 0;
$priceRange = $_GET['price_range'] ?? '';

// Construction du tableau de filtres pour la requête
$filterParams = [];
if (!empty($selectedBiome)) {
    $biome = $biomeModel->getByName($selectedBiome);
    if ($biome) {
        $filterParams['biome_id'] = (string)$biome['_id'];
    }
}
if ($minRating > 0) {
    $filterParams['min_rating'] = $minRating;
}
if (!empty($priceRange)) {
    $filterParams['price_range'] = $priceRange;
}

// Récupération des restaurants selon les filtres appliqués
$restaurants = empty($filterParams) ? $restaurantModel->getAll() : $restaurantModel->filter($filterParams);
$allBiomes = $biomeModel->getAll();

// Galerie d'images de restaurants pour l'affichage (rotation cyclique)
$restaurantImages = [
    'https://images.unsplash.com/photo-1552566626-52f8b828add9?w=600&q=80',
    'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=600&q=80',
    'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=600&q=80',
    'https://images.unsplash.com/photo-1559339352-11d035aa65de?w=600&q=80',
    'https://images.unsplash.com/photo-1578474846511-04ba529f0b88?w=600&q=80',
    'https://images.unsplash.com/photo-1466978913421-dad2ebd01d17?w=600&q=80',
    'https://images.unsplash.com/photo-1590846406792-0adc7f938f1d?w=600&q=80',
    'https://images.unsplash.com/photo-1424847651672-bf20a4b0982b?w=600&q=80',
];
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo Language::t('restaurants'); ?> - BiomeBistro</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/animations.css">
    <style>
        /* En-tête de page avec image de fond et overlay sombre */
        .page-header {
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.6)), 
                        url('https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=1920&q=80');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 4rem 0 3rem;
            text-align: center;
        }
        
        .page-header h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 8px rgba(0,0,0,0.8);
        }
        
        /* Section des filtres avec effet d'élévation */
        .filters-section {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin: -2rem auto 2rem;
            max-width: 1200px;
            position: relative;
            z-index: 10;
        }
        
        /* Grille responsive pour les contrôles de filtre */
        .filters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            align-items: end;
        }
        
        .filter-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--text-color);
        }
        
        .filter-group select {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            font-size: 1rem;
        }
        
        /* Cartes de restaurants avec design moderne et effets hover */
        .restaurant-card-enhanced {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        
        .restaurant-card-enhanced:hover {
            transform: translateY(-8px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        
        /* Conteneur de l'image du restaurant */
        .restaurant-image-wrapper {
            position: relative;
            width: 100%;
            height: 240px;
            overflow: hidden;
        }
        
        .restaurant-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        /* Effet de zoom sur l'image au survol */
        .restaurant-card-enhanced:hover .restaurant-image {
            transform: scale(1.1);
        }
        
        /* Badge du biome positionné sur l'image */
        .biome-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: rgba(255,255,255,0.95);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }
        
        /* Contenu textuel de la carte restaurant */
        .restaurant-content {
            padding: 1.5rem;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        .restaurant-name {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }
        
        .restaurant-info {
            color: var(--text-light);
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }
        
        /* Affichage de la note avec étoiles */
        .restaurant-rating {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }
        
        /* Tags des équipements du restaurant */
        .restaurant-features {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }
        
        .feature-tag {
            background: var(--bg-light);
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.85rem;
        }
        
        /* Boutons d'action en bas de carte */
        .restaurant-actions {
            margin-top: auto;
        }
        
        /* Compteur de résultats */
        .results-count {
            text-align: center;
            color: var(--text-light);
            margin: 2rem 0;
            font-size: 1.1rem;
        }
    </style>
</head>
<body>
    <!-- En-tête principale avec navigation -->
    <header class="main-header">
        <div class="container">
            <div class="header-content">
                <div class="logo"><h1>🌍 BiomeBistro</h1></div>
                <nav class="main-nav">
                    <a href="index.php"><?php echo Language::t('home'); ?></a>
                    <a href="biomes.php"><?php echo Language::t('biomes'); ?></a>
                    <a href="restaurants.php" class="active"><?php echo Language::t('restaurants'); ?></a>
                    <a href="my-reservations.php?email=demo@example.com">
                        <?php echo $lang === 'fr' ? '📅 Réservations' : '📅 Reservations'; ?>
                    </a>
                    <a href="my-reviews.php?email=demo@example.com">
                        <?php echo $lang === 'fr' ? '⭐ Avis' : '⭐ Reviews'; ?>
                    </a>
                    <a href="all-menus.php">
                        <?php echo $lang === 'fr' ? '🍽️ Menus' : '🍽️ Menus'; ?>
                    </a>
                </nav>
                <div class="language-switcher">
                    <a href="?lang=fr<?php echo !empty($selectedBiome) ? '&biome='.urlencode($selectedBiome) : ''; ?>" 
                       class="lang-btn <?php echo $lang === 'fr' ? 'active' : ''; ?>">🇫🇷 FR</a>
                    <a href="?lang=en<?php echo !empty($selectedBiome) ? '&biome='.urlencode($selectedBiome) : ''; ?>" 
                       class="lang-btn <?php echo $lang === 'en' ? 'active' : ''; ?>">🇬🇧 EN</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Bannière d'en-tête avec titre et slogan -->
    <section class="page-header">
        <h1><?php echo $lang === 'fr' ? 'Nos Restaurants' : 'Our Restaurants'; ?></h1>
        <p><?php echo $lang === 'fr' ? 'Découvrez des expériences culinaires uniques' : 'Discover unique culinary experiences'; ?></p>
    </section>

    <div class="container">
        <!-- Formulaire de filtres interactif -->
        <form method="GET" class="filters-section">
            <div class="filters-grid">
                <!-- Filtre par écosystème/biome -->
                <div class="filter-group">
                    <label><?php echo $lang === 'fr' ? 'Écosystème' : 'Ecosystem'; ?></label>
                    <select name="biome">
                        <option value=""><?php echo $lang === 'fr' ? 'Tous les biomes' : 'All biomes'; ?></option>
                        <?php foreach ($allBiomes as $biome): ?>
                            <option value="<?php echo htmlspecialchars($biome['name']); ?>" 
                                    <?php echo $selectedBiome === $biome['name'] ? 'selected' : ''; ?>>
                                <?php echo $biome['icon']; ?> <?php echo htmlspecialchars($biome['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Filtre par note minimale -->
                <div class="filter-group">
                    <label><?php echo $lang === 'fr' ? 'Note minimum' : 'Minimum rating'; ?></label>
                    <select name="min_rating">
                        <option value="0"><?php echo $lang === 'fr' ? 'Toutes' : 'All'; ?></option>
                        <option value="4" <?php echo $minRating == 4 ? 'selected' : ''; ?>>4★ <?php echo $lang === 'fr' ? 'et plus' : 'and above'; ?></option>
                        <option value="4.5" <?php echo $minRating == 4.5 ? 'selected' : ''; ?>>4.5★ <?php echo $lang === 'fr' ? 'et plus' : 'and above'; ?></option>
                    </select>
                </div>

                <!-- Filtre par gamme de prix -->
                <div class="filter-group">
                    <label><?php echo $lang === 'fr' ? 'Gamme de prix' : 'Price range'; ?></label>
                    <select name="price_range">
                        <option value=""><?php echo $lang === 'fr' ? 'Toutes' : 'All'; ?></option>
                        <option value="€€" <?php echo $priceRange === '€€' ? 'selected' : ''; ?>>€€</option>
                        <option value="€€€" <?php echo $priceRange === '€€€' ? 'selected' : ''; ?>>€€€</option>
                        <option value="€€€€" <?php echo $priceRange === '€€€€' ? 'selected' : ''; ?>>€€€€</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">
                    <?php echo $lang === 'fr' ? '🔍 Rechercher' : '🔍 Search'; ?>
                </button>
            </div>
        </form>

        <!-- Affichage du nombre de résultats trouvés -->
        <p class="results-count">
            <?php echo count($restaurants); ?> <?php echo $lang === 'fr' ? 'restaurant(s) trouvé(s)' : 'restaurant(s) found'; ?>
        </p>

        <!-- Grille des cartes de restaurants -->
        <div class="restaurant-grid">
            <?php foreach ($restaurants as $index => $restaurant): 
                $biome = $biomeModel->getById((string)$restaurant['biome_id']);
            ?>
                <div class="restaurant-card-enhanced">
                    <!-- Section image avec badge biome -->
                    <div class="restaurant-image-wrapper">
                        <img src="<?php echo $restaurantImages[$index % count($restaurantImages)]; ?>" 
                             alt="<?php echo htmlspecialchars($restaurant['name']); ?>" 
                             class="restaurant-image">
                        <?php if ($biome): ?>
                            <div class="biome-badge" style="color: <?php echo $biome['color_theme']; ?>">
                                <?php echo $biome['icon']; ?> <?php echo htmlspecialchars($biome['name']); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Section contenu texte -->
                    <div class="restaurant-content">
                        <h3 class="restaurant-name"><?php echo htmlspecialchars($restaurant['name']); ?></h3>
                        
                        <!-- Informations de base du restaurant -->
                        <p class="restaurant-info">
                            📍 <?php echo htmlspecialchars($restaurant['location']['district']); ?><br>
                            🍽️ <?php echo htmlspecialchars($restaurant['cuisine_style']); ?> • <?php echo $restaurant['price_range']; ?>
                        </p>
                        
                        <!-- Affichage de la note avec étoiles -->
                        <div class="restaurant-rating">
                            <span style="color: gold; font-size: 1.2rem;">
                                <?php for ($i = 1; $i <= 5; $i++) echo $i <= floor($restaurant['average_rating']) ? '★' : '☆'; ?>
                            </span>
                            <span><strong><?php echo number_format($restaurant['average_rating'], 1); ?></strong></span>
                            <span style="color: var(--text-light);">(<?php echo $restaurant['total_reviews']; ?>)</span>
                        </div>
                        
                        <!-- Tags des équipements (3 premiers) -->
                        <div class="restaurant-features">
                            <?php 
                            $features = $restaurant['features'] ?? [];
                            if ($features instanceof MongoDB\Model\BSONArray) {
                                $features = $features->getArrayCopy();
                            }
                            foreach (array_slice($features, 0, 3) as $feature): 
                            ?>
                                <span class="feature-tag"><?php echo htmlspecialchars($feature); ?></span>
                            <?php endforeach; ?>
                        </div>
                        
                        <!-- Bouton d'action vers la page de détail -->
                        <div class="restaurant-actions">
                            <a href="restaurant-detail.php?id=<?php echo $restaurant['_id']; ?>" class="btn btn-primary" style="width: 100%;">
                                <?php echo $lang === 'fr' ? 'Voir les détails' : 'View details'; ?>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Pied de page -->
    <footer class="main-footer" style="margin-top: 4rem;">
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