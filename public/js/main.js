/**
 * BiomeBistro - JavaScript Principal
 * Gère l'interactivité, la validation des formulaires et les fonctionnalités dynamiques
 */

// Attendre que le DOM soit entièrement chargé
document.addEventListener('DOMContentLoaded', function() {
    console.log('🌍 BiomeBistro initialisé !');
    
    // Initialiser toutes les fonctionnalités
    initLanguageSwitcher();
    initFormValidation();
    initSmoothScroll();
    initSearchFeatures();
});

/**
 * Sélecteur de langue
 * Gère les transitions de langue fluides
 */
function initLanguageSwitcher() {
    const langButtons = document.querySelectorAll('.lang-btn');
    
    langButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            // Ajouter un indicateur de chargement
            const originalText = this.textContent;
            this.textContent = '...';
            
            // Le changement de langue s'effectue via la redirection PHP
            // Ceci fournit uniquement un retour visuel
        });
    });
}

/**
 * Validation des formulaires
 * Validation côté client pour tous les formulaires
 */
function initFormValidation() {
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateForm(this)) {
                e.preventDefault();
                showError('Veuillez remplir correctement tous les champs obligatoires.');
            }
        });
    });
}

/**
 * Valider un formulaire
 * @param {HTMLFormElement} form - Le formulaire à valider
 * @returns {boolean} - Indique si le formulaire est valide
 */
function validateForm(form) {
    const requiredFields = form.querySelectorAll('[required]');
    let isValid = true;
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            isValid = false;
            field.classList.add('error');
        } else {
            field.classList.remove('error');
        }
        
        // Validation de l'e-mail
        if (field.type === 'email' && field.value) {
            if (!validateEmail(field.value)) {
                isValid = false;
                field.classList.add('error');
            }
        }
        
        // Validation du téléphone
        if (field.type === 'tel' && field.value) {
            if (!validatePhone(field.value)) {
                isValid = false;
                field.classList.add('error');
            }
        }
    });
    
    return isValid;
}

/**
 * Valider le format d'un e-mail
 * @param {string} email - E-mail à valider
 * @returns {boolean}
 */
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

/**
 * Valider le format d'un numéro de téléphone (français)
 * @param {string} phone - Numéro à valider
 * @returns {boolean}
 */
function validatePhone(phone) {
    // Supprimer les espaces et les tirets
    const cleaned = phone.replace(/[\s\-]/g, '');
    // Téléphone français : +33 ou 0 suivi de 9 chiffres
    const re = /^(\+33|0)[1-9]\d{8}$/;
    return re.test(cleaned);
}

/**
 * Afficher un message d'erreur
 * @param {string} message - Message d'erreur à afficher
 */
function showError(message) {
    // Créer l'élément d'erreur s'il n'existe pas
    let errorDiv = document.getElementById('error-message');
    
    if (!errorDiv) {
        errorDiv = document.createElement('div');
        errorDiv.id = 'error-message';
        errorDiv.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: #E74C3C;
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            z-index: 9999;
            animation: slideIn 0.3s ease;
        `;
        document.body.appendChild(errorDiv);
    }
    
    errorDiv.textContent = message;
    errorDiv.style.display = 'block';
    
    // Masquer après 5 secondes
    setTimeout(() => {
        errorDiv.style.display = 'none';
    }, 5000);
}

/**
 * Afficher un message de succès
 * @param {string} message - Message de succès à afficher
 */
function showSuccess(message) {
    let successDiv = document.getElementById('success-message');
    
    if (!successDiv) {
        successDiv = document.createElement('div');
        successDiv.id = 'success-message';
        successDiv.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: #27AE60;
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            z-index: 9999;
            animation: slideIn 0.3s ease;
        `;
        document.body.appendChild(successDiv);
    }
    
    successDiv.textContent = message;
    successDiv.style.display = 'block';
    
    setTimeout(() => {
        successDiv.style.display = 'none';
    }, 5000);
}

/**
 * Défilement fluide pour les liens d'ancrage
 */
function initSmoothScroll() {
    const anchors = document.querySelectorAll('a[href^="#"]');
    
    anchors.forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            
            if (href !== '#' && href !== '#!') {
                const target = document.querySelector(href);
                
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }
        });
    });
}

/**
 * Initialiser les fonctionnalités de recherche
 */
function initSearchFeatures() {
    const searchInput = document.querySelector('.search-input');
    
    if (searchInput) {
        // Comportement de l'icône de recherche
        searchInput.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });
        
        searchInput.addEventListener('blur', function() {
            this.parentElement.classList.remove('focused');
        });
    }
}

/**
 * Interaction avec les étoiles de notation
 * Utilisé dans les formulaires d'avis
 */
function initRatingStars() {
    const ratingContainers = document.querySelectorAll('.rating-input');
    
    ratingContainers.forEach(container => {
        const stars = container.querySelectorAll('.star');
        const input = container.querySelector('input[type="hidden"]');
        
        stars.forEach((star, index) => {
            star.addEventListener('click', function() {
                const rating = index + 1;
                input.value = rating;
                
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
                const rating = index + 1;
                
                stars.forEach((s, i) => {
                    if (i < rating) {
                        s.textContent = '⭐';
                    } else {
                        s.textContent = '☆';
                    }
                });
            });
        });
        
        container.addEventListener('mouseleave', function() {
            const currentRating = parseInt(input.value) || 0;
            
            stars.forEach((s, i) => {
                if (i < currentRating) {
                    s.textContent = '⭐';
                } else {
                    s.textContent = '☆';
                }
            });
        });
    });
}

/**
 * Amélioration du sélecteur de date
 * Empêche la réservation dans le passé
 */
function initDatePicker() {
    const dateInputs = document.querySelectorAll('input[type="date"]');
    
    dateInputs.forEach(input => {
        // Définir la date minimale à aujourd'hui
        const today = new Date().toISOString().split('T')[0];
        input.setAttribute('min', today);
    });
}

/**
 * Vérification de disponibilité des créneaux horaires (simulation)
 * En production, cela effectuerait un appel AJAX pour vérifier la disponibilité réelle
 */
function checkAvailability(restaurantId, date, time) {
    // Simuler la vérification de disponibilité
    return new Promise((resolve) => {
        setTimeout(() => {
            // Disponibilité aléatoire pour la démo
            const available = Math.random() > 0.3;
            resolve(available);
        }, 500);
    });
}

/**
 * Chargement différé des images
 * Améliore les performances de chargement de la page
 */
function initLazyLoading() {
    const images = document.querySelectorAll('img[data-src]');
    
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.removeAttribute('data-src');
                observer.unobserve(img);
            }
        });
    });
    
    images.forEach(img => imageObserver.observe(img));
}

/**
 * Menu mobile
 * Pour la navigation responsive
 */
function initMobileMenu() {
    const menuToggle = document.querySelector('.menu-toggle');
    const mainNav = document.querySelector('.main-nav');
    
    if (menuToggle && mainNav) {
        menuToggle.addEventListener('click', function() {
            mainNav.classList.toggle('active');
            this.classList.toggle('active');
        });
    }
}

/**
 * Bouton retour en haut de page
 */
function initScrollToTop() {
    const scrollBtn = document.createElement('button');
    scrollBtn.id = 'scroll-to-top';
    scrollBtn.innerHTML = '↑';
    scrollBtn.style.cssText = `
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: var(--primary-color);
        color: white;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        display: none;
        z-index: 1000;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        transition: all 0.3s ease;
    `;
    
    document.body.appendChild(scrollBtn);
    
    // Afficher/masquer selon la position de défilement
    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            scrollBtn.style.display = 'block';
        } else {
            scrollBtn.style.display = 'none';
        }
    });
    
    // Remonter en haut au clic
    scrollBtn.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
}

// Initialiser le bouton retour en haut
initScrollToTop();

// Ajouter l'animation CSS pour les messages
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    .error input,
    .error textarea,
    .error select {
        border-color: #E74C3C !important;
    }
`;
document.head.appendChild(style);