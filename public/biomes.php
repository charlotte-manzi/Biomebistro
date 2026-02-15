<?php
/**
 * BiomeBistro - Biomes Explorer Page
 */

require_once __DIR__ . '/../vendor/autoload.php';

use BiomeBistro\Models\Biome;
use BiomeBistro\Utils\Language;

session_start();
Language::init();
Language::setLanguage($_SESSION['lang'] ?? 'fr');
$lang = Language::getCurrentLanguage();

$biomeModel = new Biome();
$biomes = $biomeModel->getAllWithCounts();
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo Language::t('biomes'); ?> - BiomeBistro</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/animations.css">
    <style>
        .biomes-hero {
            background: linear-gradient(135deg, #27AE60 0%, #3498DB 100%);
            color: white;
            padding: 4rem 0;
            text-align: center;
        }
        .biomes-hero h1 { font-size: 3rem; margin-bottom: 1rem; }
        .biome-detail-card {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
            border: 2px solid var(--border-color);
            transition: all 0.3s ease;
        }
        .biome-detail-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }
        .biome-header {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .biome-icon-large {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
        }
        .biome-info { flex: 1; }
        .biome-title { font-size: 2rem; margin: 0; }
        .biome-climate {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin: 1.5rem 0;
            background: var(--bg-light);
            padding: 1rem;
            border-radius: 8px;
        }
        .climate-item { padding: 0.5rem; }
        .climate-label { font-weight: 600; color: var(--text-color); }
        .ingredients-list {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin: 1rem 0;
        }
        .ingredient-tag {
            background: var(--primary-color);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
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
                    <a href="biomes.php" class="active"><?php echo Language::t('biomes'); ?></a>
                    <a href="restaurants.php"><?php echo Language::t('restaurants'); ?></a>
                </nav>
                <div class="language-switcher">
                    <a href="?lang=fr" class="lang-btn <?php echo $lang === 'fr' ? 'active' : ''; ?>">🇫🇷 FR</a>
                    <a href="?lang=en" class="lang-btn <?php echo $lang === 'en' ? 'active' : ''; ?>">🇬🇧 EN</a>
                </div>
            </div>
        </div>
    </header>

    <section class="biomes-hero">
        <div class="container">
            <h1><?php echo Language::t('explore_biomes'); ?></h1>
            <p style="font-size: 1.3rem; opacity: 0.95;">
                <?php echo $lang === 'fr' ? 'Découvrez 8 écosystèmes uniques du monde entier' : 'Discover 8 unique ecosystems from around the world'; ?>
            </p>
        </div>
    </section>

    <section class="top-rated-section">
        <div class="container">
            <?php foreach ($biomes as $biome): ?>
                <div class="biome-detail-card">
                    <div class="biome-header">
                        <div class="biome-icon-large" style="background: <?php echo $biome['color_theme']; ?>">
                            <?php echo $biome['icon']; ?>
                        </div>
                        <div class="biome-info">
                            <h2 class="biome-title"><?php echo htmlspecialchars($biome['name']); ?></h2>
                            <p style="color: var(--text-light); font-size: 1.1rem;">
                                <?php echo htmlspecialchars($biome['description']); ?>
                            </p>
                            <p style="margin-top: 0.5rem;">
                                <strong><?php echo $biome['restaurant_count']; ?> <?php echo Language::t('restaurants_count'); ?></strong>
                            </p>
                        </div>
                    </div>

                    <div class="biome-climate">
                        <div class="climate-item">
                            <div class="climate-label">🌡️ <?php echo $lang === 'fr' ? 'Température' : 'Temperature'; ?></div>
                            <div><?php echo htmlspecialchars($biome['climate']['temperature_range']); ?></div>
                        </div>
                        <div class="climate-item">
                            <div class="climate-label">💧 <?php echo $lang === 'fr' ? 'Humidité' : 'Humidity'; ?></div>
                            <div><?php echo htmlspecialchars($biome['climate']['humidity']); ?></div>
                        </div>
                        <div class="climate-item">
                            <div class="climate-label">🌧️ <?php echo $lang === 'fr' ? 'Précipitations' : 'Rainfall'; ?></div>
                            <div><?php echo htmlspecialchars($biome['climate']['rainfall']); ?></div>
                        </div>
                        <div class="climate-item">
                            <div class="climate-label">📅 <?php echo $lang === 'fr' ? 'Meilleure saison' : 'Best season'; ?></div>
                            <div><?php echo htmlspecialchars($biome['season_best']); ?></div>
                        </div>
                    </div>

                    <div>
                        <h4><?php echo $lang === 'fr' ? 'Ingrédients natifs' : 'Native ingredients'; ?>:</h4>
                        <div class="ingredients-list">
                            <?php foreach ($biome['native_ingredients'] as $ingredient): ?>
                                <span class="ingredient-tag" style="background: <?php echo $biome['color_theme']; ?>">
                                    <?php echo htmlspecialchars($ingredient); ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div style="margin-top: 1.5rem;">
                        <h4><?php echo $lang === 'fr' ? 'Caractéristiques' : 'Characteristics'; ?>:</h4>
                        <ul>
                            <?php foreach ($biome['characteristics'] as $char): ?>
                                <li><?php echo htmlspecialchars($char); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <div style="margin-top: 1.5rem;">
                        <a href="restaurants.php?biome=<?php echo $biome['_id']; ?>" class="btn btn-primary">
                            <?php echo Language::t('view_restaurants'); ?> (<?php echo $biome['restaurant_count']; ?>)
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
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
