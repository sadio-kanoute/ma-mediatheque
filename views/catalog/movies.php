<?php
// Affichage de la page des films
// Utilisation de la fonction url() pour les liens
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($title ?? 'Films'); ?></title>
    <link rel="stylesheet" href="<?php echo url('assets/css/style.css'); ?>">
</head>

<body>
    <section class="container">
        <section class="banner_media">
            <section class="hero-banner">
                <h1>Catalogue des Films</h1>
                <p class="hero-subtitle">Découvrez notre collection de films</p>
            </section>
            <!-- Début de la section des filtres de recherche pour la page des films -->
            <section class="search-filters">
                <!-- Formulaire de recherche avec méthode GET pour envoyer les paramètres à catalog/movies -->
                <form method="GET" action="<?php echo url('catalog/movies'); ?>">
                    <!-- Barre de recherche pour la saisie du titre et les boutons -->
                    <div class="search-bar">
                        <!-- Champ de saisie pour la recherche du titre du film -->
                        <input type="text" name="search_term" placeholder="Rechercher un film..." class="search-input" value="<?php echo e($search_term ?? ''); ?>">
                        <!-- Bouton pour soumettre le formulaire et lancer la recherche -->
                        <button type="submit" class="search-button">Rechercher</button>
                        <!-- Condition d'affichage du bouton reset : seulement si au moins un filtre est actif -->
                        <?php if (!empty($search_term) || ($search_genre ?? 'all') != 'all' || ($search_availability ?? 'all') != 'all'): ?>
                            <!-- Bouton reset pour effacer tous les filtres et revenir à l'état initial -->
                            <a href="<?php echo url('catalog/movies'); ?>" class="btn-effacer">Effacer</a>
                        <?php endif; ?>
                    </div>
                    <!-- Section des filtres supplémentaires (genre et disponibilité) -->
                    <div class="filters">
                        <!-- Groupe de filtre pour le genre -->
                        <div class="filter-group">
                            <!-- Label pour le menu déroulant du genre -->
                            <label for="genre">Genre</label>
                            <!-- Menu déroulant pour sélectionner le genre du film -->
                            <select id="genre" name="genre" class="filter-select">
                                <!-- Option par défaut : tous les genres -->
                                <option value="all" <?php echo ($search_genre ?? 'all') == 'all' ? 'selected' : ''; ?>>Tous</option>
                                <!-- Options de genres spécifiques pour les films -->
                                <option value="Science-Fiction" <?php echo ($search_genre ?? '') == 'Science-Fiction' ? 'selected' : ''; ?>>Science-Fiction</option>
                                <option value="Drame" <?php echo ($search_genre ?? '') == 'Drame' ? 'selected' : ''; ?>>Drame</option>
                                <option value="Romance" <?php echo ($search_genre ?? '') == 'Romance' ? 'selected' : ''; ?>>Romance</option>
                                <option value="Crime" <?php echo ($search_genre ?? '') == 'Crime' ? 'selected' : ''; ?>>Crime</option>
                                <option value="Action" <?php echo ($search_genre ?? '') == 'Action' ? 'selected' : ''; ?>>Action</option>
                            </select>
                        </div>
                        <!-- Groupe de filtre pour la disponibilité -->
                        <div class="filter-group">
                            <!-- Label pour le menu déroulant de disponibilité -->
                            <label for="availability">Disponibilité</label>
                            <!-- Menu déroulant pour sélectionner le statut de disponibilité -->
                            <select id="availability" name="availability" class="filter-select">
                                <!-- Option par défaut : tous les statuts -->
                                <option value="all" <?php echo ($search_availability ?? 'all') == 'all' ? 'selected' : ''; ?>>Tous</option>
                                <!-- Option pour afficher les items disponibles -->
                                <option value="true" <?php echo ($search_availability ?? '') == 'true' ? 'selected' : ''; ?>>En stock</option>
                                <!-- Option pour afficher les items empruntés -->
                                <option value="false" <?php echo ($search_availability ?? '') == 'false' ? 'selected' : ''; ?>>Emprunté</option>
                            </select>
                        </div>
                    </div>
                </form>
            </section>
        </section>
        <!-- Fin de la section des filtres de recherche -->
        <div class="grid-container">
            <?php if (empty($items)): ?>
                <p class="no-results">Aucun film trouvé.</p>
            <?php else: ?>
                <?php foreach ($items as $item): ?>
                    <div class="item">
                        <img src="<?php echo e($item['image_url'] ?? 'https://via.placeholder.com/300'); ?>" alt="<?php echo e($item['title']); ?>">
                        <h3><?php echo e($item['title']); ?></h3>
                        <p><?php echo e($item['author_director_publisher']); ?></p>
                        <p>Durée : <?php echo e($item['pages_duration_min_age']); ?> min</p>
                        <p><?php echo isset($item['stock']) && $item['stock'] > 0 ? '<span class="available">Disponible</span>' : '<span class="unavailable">Indisponible</span>'; ?> : <?php echo e($item['stock'] ?? '0'); ?></p>
                        <div class="item-buttons">
                            <a href="#item-<?php echo $item['id']; ?>" class="btn btn-detail">Détails</a>
                            <?php if ($item['stock'] > 0): ?>
                                <a href="<?php echo url('rental/rent/' . $item['id']); ?>" target="rental_tab" class="btn btn-rent">Emprunter</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <div class="pagination">
            <!-- Bouton retour au catalogue -->
            <a href="<?php echo url('catalog/index'); ?>" class="btn btn-catalog-return">Retour au catalogue</a>

            <!-- Bouton page précédente -->
            <?php if ($current_page > 1): ?>
                <a href="<?php echo url('catalog/movies?page=' . ($current_page - 1) . '&search_term=' . urlencode($search_term) . '&genre=' . ($search_genre ?? 'all') . '&availability=' . ($search_availability ?? 'all')); ?>" class="btn btn-prev-page">Page précédente</a>
            <?php endif; ?>

            <!-- Liens numériques des pages -->
            <div class="page-numbers">
                <!-- Toujours afficher la page 1 -->
                <a href="<?php echo url('catalog/movies?page=1&search_term=' . urlencode($search_term) . '&genre=' . ($search_genre ?? 'all') . '&availability=' . ($search_availability ?? 'all')); ?>" class="page-number <?php echo $current_page == 1 ? 'active' : ''; ?>">1</a>

                <?php
                // Si la page actuelle است supérieure à 3, afficher le signe ...
                if ($current_page > 3) {
                    echo '<span class="ellipsis">...</span>';
                }

                // Afficher les pages autour de la page actuelle (sauf la page 1)
                $start_page = max(2, $current_page - 2);
                $end_page = min($total_pages, $current_page + 2);
                for ($i = $start_page; $i <= $end_page; $i++): ?>
                    <a href="<?php echo url('catalog/movies?page=' . $i . '&search_term=' . urlencode($search_term) . '&genre=' . ($search_genre ?? 'all') . '&availability=' . ($search_availability ?? 'all')); ?>" class="page-number <?php echo $i == $current_page ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>

                <!-- Si la page actuelle est inférieure au total des pages moins 2, afficher le signe ... -->
                <?php if ($current_page < $total_pages - 2): ?>
                    <span class="ellipsis">...</span>
                <?php endif; ?>
            </div>

            <!-- Bouton page suivante -->
            <?php if ($current_page < $total_pages): ?>
                <a href="<?php echo url('catalog/movies?page=' . ($current_page + 1) . '&search_term=' . urlencode($search_term) . '&genre=' . ($search_genre ?? 'all') . '&availability=' . ($search_availability ?? 'all')); ?>" class="btn btn-next-page">Page suivante</a>
            <?php endif; ?>
            <!-- Affichage du numéro de page actuel et du total des pages -->
            <span class="page-info">Page <?php echo $current_page; ?> sur <?php echo $total_pages; ?></span>
        </div>

        <!-- Modal pour les détails -->
        <?php foreach ($items as $item): ?>
            <div class="modal" id="item-<?php echo $item['id']; ?>">
                <div class="modal-content">
                    <div class="detail">
                        <img src="<?php echo e($item['image_url'] ?? 'https://via.placeholder.com/300'); ?>" alt="<?php echo e($item['title']); ?>">
                        <h2><?php echo e($item['title']); ?></h2>
                        <p>Genre : <?php echo e($item['genre'] ?? 'N/A'); ?></p>
                        <p>Année : <?php echo e($item['year'] ?? 'N/A'); ?></p>
                        <p>Résumé : <?php echo e($item['description'] ?? 'N/A'); ?></p>
                        <p>Réalisateur : <?php echo e($item['author_director_publisher'] ?? 'N/A'); ?></p>
                        <p>Note : <?php echo e($item['isbn_rating_platform'] ?? 'N/A'); ?></p>
                        <p>Durée : <?php echo e($item['pages_duration_min_age'] ?? 'N/A'); ?> min</p>
                        <p><?php echo isset($item['stock']) && $item['stock'] > 0 ? '<span class="available">Disponible</span>' : '<span class="unavailable">Indisponible</span>'; ?> : <?php echo e($item['stock'] ?? '0'); ?></p>
                        <div>
                            <?php if (
                                $item['stock'] > 0
                            ): ?>
                                <a href="<?php echo url('rental/rent/' . $item['id']); ?>" target="rental_tab" class="btn btn-rent">Emprunter</a>
                            <?php endif; ?>
                            <a href="#close-modal" class="btn btn-back">Fermer</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <!-- Élément caché pour éviter le saut de page -->
        <div id="close-modal" style="display: none;"></div>
    </section>

    <script src="<?php echo url('assets/js/app.js'); ?>"></script>
</body>

</html>