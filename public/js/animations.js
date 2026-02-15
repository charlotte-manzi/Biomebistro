/**
 * BiomeBistro - Générateur de Fonds Animés
 * Crée des effets de particules dynamiques selon le type de biome
 */

// Configurations des biomes
const biomeConfigs = {
    'tropical': {
        particles: 20,
        type: 'leaf',
        className: 'tropical-leaf',
        extras: ['tropical-mist']
    },
    'desert': {
        particles: 0,
        extras: ['desert-sand', 'heat-wave']
    },
    'coral': {
        particles: 30,
        type: 'bubble',
        className: 'bubble',
        extras: ['wave-overlay']
    },
    'alpine': {
        particles: 40,
        type: 'snow',
        className: 'snowflake',
        extras: []
    },
    'arctic': {
        particles: 25,
        type: 'crystal',
        className: 'ice-crystal',
        extras: ['aurora']
    },
    'forest': {
        particles: 15,
        type: 'leaf',
        className: 'forest-leaf',
        extras: ['sunray', 'sunray', 'sunray']
    },
    'savanna': {
        particles: 30,
        type: 'grass',
        className: 'grass-blade',
        extras: ['sun-glow']
    },
    'mushroom': {
        particles: 35,
        type: 'spore',
        className: 'spore',
        extras: ['magic-glow', 'magic-glow', 'magic-glow']
    }
};

/**
 * Initialise le fond animé
 */
function initAnimatedBackground(biomeName) {
    // Nettoyer le nom du biome
    biomeName = biomeName.toLowerCase().replace(/\s+/g, '');
    
    // Trouver la configuration correspondante
    let config = null;
    for (let key in biomeConfigs) {
        if (biomeName.includes(key)) {
            config = biomeConfigs[key];
            break;
        }
    }
    
    if (!config) {
        console.log('Aucune configuration d\'animation pour le biome :', biomeName);
        return;
    }
    
    // Créer le conteneur
    const container = document.createElement('div');
    container.className = 'animated-background';
    
    // Ajouter le fond dégradé
    const gradient = document.createElement('div');
    gradient.className = `bg-gradient bg-${Object.keys(biomeConfigs).find(k => biomeName.includes(k))}`;
    container.appendChild(gradient);
    
    // Ajouter le conteneur de particules
    const particlesContainer = document.createElement('div');
    particlesContainer.className = 'particles';
    
    // Générer les particules
    for (let i = 0; i < config.particles; i++) {
        const particle = document.createElement('div');
        particle.className = `particle ${config.className}`;
        
        // Positionnement aléatoire
        particle.style.left = Math.random() * 100 + '%';
        particle.style.animationDelay = Math.random() * 10 + 's';
        particle.style.animationDuration = (Math.random() * 5 + 5) + 's';
        
        // Variation de taille aléatoire
        const scale = 0.7 + Math.random() * 0.6;
        particle.style.transform = `scale(${scale})`;
        
        particlesContainer.appendChild(particle);
    }
    
    container.appendChild(particlesContainer);
    
    // Ajouter les effets supplémentaires
    if (config.extras) {
        config.extras.forEach((extraClass, index) => {
            const extra = document.createElement('div');
            extra.className = extraClass;
            
            // Positionner les effets supplémentaires
            if (extraClass.includes('sunray')) {
                extra.style.left = (20 + index * 30) + '%';
            } else if (extraClass.includes('glow')) {
                extra.style.left = (20 + index * 30) + '%';
                extra.style.top = (30 + index * 20) + '%';
            } else if (extraClass.includes('cloud')) {
                extra.style.top = (10 + index * 15) + '%';
                extra.style.animationDelay = (index * 7) + 's';
            }
            
            container.appendChild(extra);
        });
    }
    
    // Insérer au début du body
    document.body.insertBefore(container, document.body.firstChild);
}

/**
 * Détecte automatiquement le biome de la page et initialise l'animation
 */
document.addEventListener('DOMContentLoaded', function() {
    // Tenter de détecter le biome depuis le contenu de la page
    let biomeName = null;
    
    // Méthode 1 : Rechercher le badge de biome
    const biomeBadge = document.querySelector('.restaurant-badge, .biome-badge');
    if (biomeBadge) {
        biomeName = biomeBadge.textContent.trim();
    }
    
    // Méthode 2 : Rechercher l'attribut data-biome
    if (!biomeName) {
        const biomeElement = document.querySelector('[data-biome]');
        if (biomeElement) {
            biomeName = biomeElement.getAttribute('data-biome');
        }
    }
    
    // Méthode 3 : Vérifier le titre ou les méta de la page
    if (!biomeName) {
        const title = document.title;
        for (let key in biomeConfigs) {
            if (title.toLowerCase().includes(key)) {
                biomeName = key;
                break;
            }
        }
    }
    
    // Initialiser si un biome est détecté
    if (biomeName) {
        console.log('Initialisation de l\'animation pour le biome :', biomeName);
        initAnimatedBackground(biomeName);
    } else {
        // Animation par défaut pour la page d'accueil (mélange de tous les biomes)
        initHomepageAnimation();
    }
    
    // Ajouter l'effet de transition de page
    document.body.classList.add('page-transition');
});

/**
 * Animation mixte pour la page d'accueil
 */
function initHomepageAnimation() {
    const container = document.createElement('div');
    container.className = 'animated-background';
    
    // Dégradé qui transite entre les couleurs des biomes
    const gradient = document.createElement('div');
    gradient.className = 'bg-gradient';
    gradient.style.background = 'linear-gradient(135deg, #27AE60 0%, #3498DB 50%, #9B59B6 100%)';
    gradient.style.backgroundSize = '200% 200%';
    gradient.style.animation = 'gradientMove 20s ease infinite';
    
    // Ajouter l'animation du dégradé si absente
    if (!document.querySelector('#gradient-move-style')) {
        const style = document.createElement('style');
        style.id = 'gradient-move-style';
        style.textContent = `
            @keyframes gradientMove {
                0% { background-position: 0% 50%; }
                50% { background-position: 100% 50%; }
                100% { background-position: 0% 50%; }
            }
        `;
        document.head.appendChild(style);
    }
    
    container.appendChild(gradient);
    
    // Ajouter des particules flottantes légères
    const particlesContainer = document.createElement('div');
    particlesContainer.className = 'particles';
    
    for (let i = 0; i < 20; i++) {
        const particle = document.createElement('div');
        particle.className = 'particle';
        particle.style.width = '6px';
        particle.style.height = '6px';
        particle.style.background = `rgba(255, 255, 255, ${Math.random() * 0.3 + 0.2})`;
        particle.style.borderRadius = '50%';
        particle.style.left = Math.random() * 100 + '%';
        particle.style.top = Math.random() * 100 + '%';
        particle.style.animation = `gentleFloat ${Math.random() * 10 + 10}s ease-in-out infinite`;
        particle.style.animationDelay = Math.random() * 5 + 's';
        
        particlesContainer.appendChild(particle);
    }
    
    // Ajouter l'animation de flottement doux si absente
    if (!document.querySelector('#gentle-float-style')) {
        const style = document.createElement('style');
        style.id = 'gentle-float-style';
        style.textContent = `
            @keyframes gentleFloat {
                0%, 100% { transform: translateY(0) translateX(0); }
                25% { transform: translateY(-20px) translateX(10px); }
                75% { transform: translateY(20px) translateX(-10px); }
            }
        `;
        document.head.appendChild(style);
    }
    
    container.appendChild(particlesContainer);
    document.body.insertBefore(container, document.body.firstChild);
}

// Exporter pour utilisation dans d'autres scripts
window.BiomeAnimations = {
    init: initAnimatedBackground,
    homepage: initHomepageAnimation
};