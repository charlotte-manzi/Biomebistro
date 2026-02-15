/**
 * BiomeBistro - Animated Backgrounds Generator
 * Creates dynamic particle effects based on biome type
 */

// Biome configurations
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
 * Initialize animated background
 */
function initAnimatedBackground(biomeName) {
    // Clean biome name
    biomeName = biomeName.toLowerCase().replace(/\s+/g, '');
    
    // Find matching config
    let config = null;
    for (let key in biomeConfigs) {
        if (biomeName.includes(key)) {
            config = biomeConfigs[key];
            break;
        }
    }
    
    if (!config) {
        console.log('No animation config for biome:', biomeName);
        return;
    }
    
    // Create container
    const container = document.createElement('div');
    container.className = 'animated-background';
    
    // Add gradient background
    const gradient = document.createElement('div');
    gradient.className = `bg-gradient bg-${Object.keys(biomeConfigs).find(k => biomeName.includes(k))}`;
    container.appendChild(gradient);
    
    // Add particles container
    const particlesContainer = document.createElement('div');
    particlesContainer.className = 'particles';
    
    // Generate particles
    for (let i = 0; i < config.particles; i++) {
        const particle = document.createElement('div');
        particle.className = `particle ${config.className}`;
        
        // Random positioning
        particle.style.left = Math.random() * 100 + '%';
        particle.style.animationDelay = Math.random() * 10 + 's';
        particle.style.animationDuration = (Math.random() * 5 + 5) + 's';
        
        // Random size variation
        const scale = 0.7 + Math.random() * 0.6;
        particle.style.transform = `scale(${scale})`;
        
        particlesContainer.appendChild(particle);
    }
    
    container.appendChild(particlesContainer);
    
    // Add extra effects
    if (config.extras) {
        config.extras.forEach((extraClass, index) => {
            const extra = document.createElement('div');
            extra.className = extraClass;
            
            // Position extras
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
    
    // Insert at the beginning of body
    document.body.insertBefore(container, document.body.firstChild);
}

/**
 * Auto-detect biome from page and initialize
 */
document.addEventListener('DOMContentLoaded', function() {
    // Try to detect biome from page content
    let biomeName = null;
    
    // Method 1: Look for biome badge
    const biomeBadge = document.querySelector('.restaurant-badge, .biome-badge');
    if (biomeBadge) {
        biomeName = biomeBadge.textContent.trim();
    }
    
    // Method 2: Look for biome data attribute
    if (!biomeName) {
        const biomeElement = document.querySelector('[data-biome]');
        if (biomeElement) {
            biomeName = biomeElement.getAttribute('data-biome');
        }
    }
    
    // Method 3: Check page title or meta
    if (!biomeName) {
        const title = document.title;
        for (let key in biomeConfigs) {
            if (title.toLowerCase().includes(key)) {
                biomeName = key;
                break;
            }
        }
    }
    
    // Initialize if biome detected
    if (biomeName) {
        console.log('Initializing animation for biome:', biomeName);
        initAnimatedBackground(biomeName);
    } else {
        // Default homepage animation (mix of all)
        initHomepageAnimation();
    }
    
    // Add page transition effect
    document.body.classList.add('page-transition');
});

/**
 * Homepage mixed animation
 */
function initHomepageAnimation() {
    const container = document.createElement('div');
    container.className = 'animated-background';
    
    // Gradient that shifts through biome colors
    const gradient = document.createElement('div');
    gradient.className = 'bg-gradient';
    gradient.style.background = 'linear-gradient(135deg, #27AE60 0%, #3498DB 50%, #9B59B6 100%)';
    gradient.style.backgroundSize = '200% 200%';
    gradient.style.animation = 'gradientMove 20s ease infinite';
    
    // Add keyframe for gradient movement
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
    
    // Add some gentle floating particles
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
    
    // Add gentle float animation
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

// Export for use in other scripts
window.BiomeAnimations = {
    init: initAnimatedBackground,
    homepage: initHomepageAnimation
};
