<?php
// Affichage de la page des emprunts de l'utilisateur avec deux tableaux : emprunts actifs et historiques
// Utilisation de la fonction url() pour les liens
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($title ?? 'Mes Emprunts'); ?></title>
    <link rel="stylesheet" href="<?php echo url('assets/css/style.css'); ?>">

</head>

<body>
    <section class="container">
        <section class="banner_emprunt">
            <section class="hero-banner">
                <h1>Mes Emprunts</h1>
                <p class="hero-subtitle">Liste de vos médias empruntés</p>
            </section>
        </section>

        <!-- Affichage des messages flash (succès ou erreur) -->
        <?php if (has_flash_messages('success')): ?>
            <?php foreach (get_flash_messages('success') as $message): ?>
                <div class="alert alert-success">
                    <?php echo e($message); ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        <?php if (has_flash_messages('error')): ?>
            <?php foreach (get_flash_messages('error') as $message): ?>
                <div class="alert alert-error">
                    <?php echo e($message); ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <!-- Affichage du message de confirmation si nécessaire -->
        <?php if (isset($_SESSION['confirm_rental_item_id'])): ?>
            <div class="confirm-message">
                <p>Confirmer l'emprunt de ce média ?</p>
                <form method="POST" action="<?php echo url('rental/rent/' . $_SESSION['confirm_rental_item_id']); ?>">
                    <button type="submit" name="confirm" value="yes">Oui</button>
                    <button type="submit" name="confirm" value="no">Non</button>
                </form>
            </div>
            <?php unset($_SESSION['confirm_rental_item_id']); // Supprime la session après affichage 
            ?>
        <?php endif; ?>

        <!-- Affichage des emprunts actifs -->
        <section class="rentals-list">
            <h2 class="section-title">Emprunts en cours :</h2>
            <?php
            // Trier les emprunts actifs par date d'emprunt (nouveau en haut)
            usort($active_rentals, function ($a, $b) {
                return strtotime($b['rent_date']) - strtotime($a['rent_date']);
            });
            ?>
            <?php if (empty($active_rentals)): ?>
                <p>Vous n'avez aucun emprunt en cours !</p>
            <?php else: ?>
                <table class="rentals-table">
                    <thead>
                        <tr>
                            <th>Titre</th>
                            <th>Type</th>
                            <th>Date d'Emprunt</th>
                            <th>Date de Retour</th>
                            <th>Retard (jours)</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($active_rentals as $rental): ?>
                            <tr>
                                <td>
                                    <!-- Affichage de l'image à côté du titre avec une taille de 40px -->
                                    <img src="<?php echo e($rental['image_url'] ?? 'https://via.placeholder.com/40'); ?>" alt="<?php echo e($rental[''] ?? ''); ?>" class="rental-image">
                                    <?php echo e($rental['title'] ?? ''); ?>
                                </td>
                                <td><?php echo e($rental['type'] == 'book' ? 'Livre' : ($rental['type'] == 'movie' ? 'Film' : ($rental['type'] == 'video_game' ? 'Jeu Vidéo' : ''))); ?></td>
                                <td><?php echo e($rental['rent_date'] ? format_date($rental['rent_date']) : ''); // Utilisation de format_date pour fuseau horaire utilisateur 
                                    ?></td>
                                <td><?php echo e($rental['return_date'] ? format_date($rental['return_date']) : ''); // Utilisation de format_date pour fuseau horaire utilisateur 
                                    ?></td>
                                <td>
                                    <?php
                                    // Calcul du retard en utilisant le rendement de la date réel

                                    $days_late = calculate_days_late($rental['return_date'], $rental['returned_at']);
                                    echo e($days_late) . ' jours';
                                    ?>
                                </td>
                                <td>
                                    <?php if ($rental['returned_at'] === null): ?>
                                        <a href="<?php echo url('rental/return/' . $rental['id']); ?>" class="btn-return">Retourner</a>
                                    <?php else: ?>
                                        <span class="returned">Retourné</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>

        <!-- Affichage des emprunts retournés (historique) -->
        <section class="rentals-list">
            <h2 class="section-title">Historique des emprunts :</h2>
            <?php
            // Tierrier Les Emprurts Retour en fonction de la date de retour (Novin on Top)

            usort($returned_rentals, function ($a, $b) {
                return strtotime($b['returned_at']) - strtotime($a['returned_at']);
            });
            ?>
            <?php if (empty($returned_rentals)): ?>
                <p>Vous n'avez aucun emprunt retourné.</p>
            <?php else: ?>
                <table class="rentals-table">
                    <thead>
                        <tr>
                            <th>Titre</th>
                            <th>Type</th>
                            <th>Date d'Emprunt</th>
                            <th>Date de Retour</th>
                            <th>Retard (jours)</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($returned_rentals as $rental): ?>
                            <tr>
                                <td>
                                    <!-- Affichage de l'image à côté du titre avec une taille de 40px -->
                                    <img src="<?php echo e($rental['image_url'] ?? ''); ?>" alt="<?php echo e($rental[''] ?? ''); ?>" class="rental-image">
                                    <?php echo e($rental['title'] ?? ''); ?>
                                </td>
                                <td><?php echo e($rental['type'] == 'book' ? 'Livre' : ($rental['type'] == 'movie' ? 'Film' : ($rental['type'] == 'video_game' ? 'Jeu Vidéo' : ''))); ?></td>
                                <td><?php echo e($rental['rent_date'] ? format_date($rental['rent_date']) : ''); // Utilisation de format_date Pour fuseau horaire کاربر 
                                    ?></td>
                                <td><?php echo e($rental['returned_at'] ? format_date($rental['returned_at']) : ''); // Utilisation de format_date Pour fuseau horaire کاربر 
                                    ?></td>
                                <td>
                                    <?php
                                    // Calculez le retard en utilisant la date réelle du retour

                                    $days_late = calculate_days_late($rental['return_date'], $rental['returned_at']);
                                    echo e($days_late) . ' jours';
                                    ?>
                                </td>
                                <td>
                                    <span class="returned">Retourné</span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>
    </section>

    <script src="<?php echo url('assets/js/app.js'); ?>"></script>
</body>

</html>