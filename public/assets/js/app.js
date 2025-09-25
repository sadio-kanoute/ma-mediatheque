/**
 * Fichier JavaScript principal de l'application
 */

// Attendre que le DOM soit chargé
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const searchButton = document.getElementById('searchButton');
    const typeFilter = document.getElementById('type');
    const genreFilter = document.getElementById('genre');
    const availabilityFilter = document.getElementById('availability');

   //écoute les événements
    if (searchInput && searchButton && typeFilter && genreFilter && availabilityFilter) {
        searchInput.addEventListener('input', filterItems);
        searchButton.addEventListener('click', filterItems);
        typeFilter.addEventListener('change', filterItems);
        genreFilter.addEventListener('change', filterItems);
        availabilityFilter.addEventListener('change', filterItems);

       //Filtre initial lors du chargement de la page
        filterItems();
    }
    // Gestion des messages flash avec auto-hide
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        // Auto-hide après 5 secondes
        setTimeout(function() {
            alert.style.opacity = '0';
            setTimeout(function() {
                alert.remove();
            }, 300);
        }, 5000);
        
        // Permettre de fermer manuellement
        alert.addEventListener('click', function() {
            alert.style.opacity = '0';
            setTimeout(function() {
                alert.remove();
            }, 300);
        });
    });
    
    // Validation de formulaire côté client
    const forms = document.querySelectorAll('form');
    forms.forEach(function(form) {
        form.addEventListener('submit', function(e) {
            if (!validateForm(form)) {
                e.preventDefault();
            }
        });
    });
    
   //Smooth Scroll vers les ancres (sauf pour les liens modaux)
    const anchors = document.querySelectorAll('a[href^="#"]:not(.btn-detail):not(.btn-back)');
    anchors.forEach(function(anchor) {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Confirmation pour les actions de suppression
    const deleteLinks = document.querySelectorAll('a[href*="delete"], button[data-action="delete"]');
    deleteLinks.forEach(function(link) {
        link.addEventListener('click', function(e) {
            if (!confirm('Êtes-vous sûr de vouloir supprimer cet élément ?')) {
                e.preventDefault();
            }
        });
    });
    
    // Animation d'entrée pour les cartes
    const cards = document.querySelectorAll('.feature-card, .step, .info-box');
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);
    
    cards.forEach(function(card) {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(card);
    });
});

/**
 * Valide un formulaire
 */
function validateForm(form) {
    let isValid = true;
    
    // Validation des champs requis
    const requiredFields = form.querySelectorAll('[required]');
    requiredFields.forEach(function(field) {
        if (!field.value.trim()) {
            showFieldError(field, 'Ce champ est obligatoire');
            isValid = false;
        } else {
            hideFieldError(field);
        }
    });
    
    // Validation des emails
    const emailFields = form.querySelectorAll('input[type="email"]');
    emailFields.forEach(function(field) {
        if (field.value && !isValidEmail(field.value)) {
            showFieldError(field, 'Adresse email invalide');
            isValid = false;
        }
    });
    
    // Validation des mots de passe
    const passwordField = form.querySelector('input[name="password"]');
    const confirmPasswordField = form.querySelector('input[name="confirm_password"]');
    
    if (passwordField && passwordField.value.length < 6) {
        showFieldError(passwordField, 'Le mot de passe doit contenir au moins 6 caractères');
        isValid = false;
    }
    
    if (confirmPasswordField && passwordField && 
        confirmPasswordField.value !== passwordField.value) {
        showFieldError(confirmPasswordField, 'Les mots de passe ne correspondent pas');
        isValid = false;
    }
    
    return isValid;
}

/**
 * Affiche une erreur sur un champ
 */
function showFieldError(field, message) {
    hideFieldError(field);
    
    const error = document.createElement('div');
    error.className = 'field-error';
    error.textContent = message;
    error.style.color = '#ef4444';
    error.style.fontSize = '0.875rem';
    error.style.marginTop = '0.25rem';
    
    field.style.borderColor = '#ef4444';
    field.parentNode.appendChild(error);
}

/**
 * Cache l'erreur d'un champ
 */
function hideFieldError(field) {
    const existingError = field.parentNode.querySelector('.field-error');
    if (existingError) {
        existingError.remove();
    }
    field.style.borderColor = '';
}

/**
 * Valide une adresse email
 */
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

/**
 * Affiche un message de notification
 */
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type}`;
    notification.textContent = message;
    notification.style.position = 'fixed';
    notification.style.top = '20px';
    notification.style.right = '20px';
    notification.style.zIndex = '1000';
    notification.style.minWidth = '300px';
    notification.style.cursor = 'pointer';
    
    document.body.appendChild(notification);
    
    // Auto-hide
    setTimeout(function() {
        notification.style.opacity = '0';
        setTimeout(function() {
            notification.remove();
        }, 300);
    }, 5000);
    
    // Click to hide
    notification.addEventListener('click', function() {
        notification.remove();
    });
}

/**
 * Utilitaire AJAX simple
 */
function ajax(url, options = {}) {
    const defaults = {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
        }
    };
    
    const config = Object.assign({}, defaults, options);
    
    return fetch(url, config)
        .then(response => {
            if (!response.ok) {
                throw new Error('Erreur réseau');
            }
            return response.json();
        })
        .catch(error => {
            console.error('Erreur AJAX:', error);
            showNotification('Une erreur est survenue', 'error');
            throw error;
        });
}

/**
 *Filtrage des articles
 */
function filterItems() {
    const searchInput = document.getElementById('searchInput');
    const searchButton = document.getElementById('searchButton');
    const typeFilter = document.getElementById('type');
    const genreFilter = document.getElementById('genre');
    const availabilityFilter = document.getElementById('availability');

    const searchTerm = searchInput.value.toLowerCase();
    const typeValue = typeFilter.value.toLowerCase();
    const genreValue = genreFilter.value.toLowerCase();
    const availabilityValue = availabilityFilter.value;

    document.querySelectorAll('.catalog-section').forEach(section => {
        const sectionType = section.dataset.section; // livre, film, jeu
        const items = section.querySelectorAll('.carousel-item');

        items.forEach(item => {
            const title = item.dataset.title;
            const genre = item.dataset.genre;
            const available = item.dataset.available;

            const matchesSearch = title.includes(searchTerm);
            const matchesType = (typeValue === 'all' || sectionType.includes(typeValue));
            const matchesGenre = (genreValue === 'all' || genre.includes(genreValue));
            const matchesAvailability = (availabilityValue === 'all' || available === availabilityValue);

            if (matchesSearch && matchesType && matchesGenre && matchesAvailability) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });
}
//CARCELS GESTION (pour tous les contaps de carrousel)
const carousels = document.querySelectorAll('.carousel-container');
carousels.forEach(carousel => {
    let isDragging = false;
    let startX;
    let scrollLeft;

    // شروع درگ
    carousel.addEventListener('mousedown', e => {
        e.preventDefault(); // Empêcher le comportement par défaut (comme les images de glisser)

        isDragging = true;
        carousel.classList.add('active'); // Pour changer le curseur en saisie

        startX = e.pageX - carousel.offsetLeft;
        scrollLeft = carousel.scrollLeft;
    });

    // Arrêter de traîner

    document.addEventListener('mouseup', () => {
        isDragging = false;
        carousel.classList.remove('active');
    });

    //stop drag during mouse exit
    document.addEventListener('mouseleave', () => {
        isDragging = false;
        carousel.classList.remove('active');
    });

    // Le mouvement de Carusol quand traîner


    carousel.addEventListener('mousemove', e => {
        if (!isDragging) return;
        e.preventDefault();
        const x = e.pageX - carousel.offsetLeft;
        const walk = (x - startX) * 2; //Coefficient 2 pour la vitesse de défilement
        carousel.scrollLeft = scrollLeft - walk;
    });

//Empêcher le texte ou les images de sélectionner
    carousel.addEventListener('selectstart', e => e.preventDefault());
});