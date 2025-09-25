<?php
require_once MODEL_PATH . '/rental_model.php';
require_once INCLUDE_PATH . '/helpers.php';

/**
 * Gère la location d'un média par un utilisateur
 * @param string $item_id Identifiant du média à emprunter
 */
function rental_rent($item_id) {
    // Vérifie si l'utilisateur est connecté
    if (!is_logged_in()) {
        set_flash('error', 'Vous devez vous connecter pour emprunter.');
        redirect('auth/login');
        return; // Arrête l'exécution si l'utilisateur n'est pas connecté
    }
    $user_id = current_user_id();
    if (!$user_id) {
        set_flash('error', 'Erreur : utilisateur non identifié.');
        redirect('auth/login');
        return;
    }

    // Vérifie si la confirmation est envoyée via POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['confirm']) && $_POST['confirm'] === 'yes') {
            // Crée une nouvelle location et vérifie le résultat
            $result = create_rental($user_id, $item_id);
            // Débogage : affiche le contenu de $result pour vérifier la structure
            error_log('rental_rent result: ' . print_r($result, true));
            if (is_array($result) && isset($result['success']) && $result['success'] === true) {
                // Message de succès avec date de retour prévue formatée au format DD-MM-YYYY
                $return_date = $result['return_date'] ?? date('Y-m-d', strtotime('+14 days'));
                set_flash('success', 'L\'emprunt a été enregistré avec succès. Date de retour prévue : ' . format_date($return_date));
            } else {
                // Vérifie plus précisément pour s'assurer de l'existence du message d'erreur
                $error_message = (is_array($result) && isset($result['error']) && !empty($result['error'])) ? $result['error'] : 'Erreur inconnue lors de l\'emprunt (vérifiez les logs pour plus de détails).';
                set_flash('error', $error_message);
            }
            redirect('rental/my_rentals');
            return;
        } else {
            // Si annulé, redirige sans emprunter
            set_flash('info', 'Emprunt annulé.');
            redirect('rental/my_rentals');
            return;
        }
    } else {
        // Si pas de POST, stocke l'item_id dans la session pour afficher le message de confirmation dans my_rentals
        $_SESSION['confirm_rental_item_id'] = $item_id;
        redirect('rental/my_rentals');
        return;
    }
}

/**
 * Affiche la liste des locations de l'utilisateur (actives et historiques)
 */
function rental_my_rentals() {
    // Vérifie si l'utilisateur est connecté
    if (!is_logged_in()) {
        redirect('auth/login');
        return; // Arrête l'exécution si l'utilisateur n'est pas connecté
    }
    $user_id = current_user_id();
    // Prépare les données pour la vue avec locations actives et historiques
    $data = [
        'title' => 'Mes Emprunts',
        'active_rentals' => get_user_rentals_by_status($user_id, 'active'),
        'returned_rentals' => get_user_rentals_by_status($user_id, 'returned'),
    ];
    load_view_with_layout('rental/my_rentals', $data);
}

/**
 * Gère le retour د'un média emprunté
 * param int $rental_id Identifiant de la location à retourner
 */
function rental_return($rental_id) {
    // Vérifie si l'utilisateur Est connecté
    if (!is_logged_in()) {
        redirect('auth/login');
        return;
// Arrête l'exécution si l'utilisateur n'est pas connecté        
    }
    $user_id = current_user_id();
    // Marque la location comme retournée
    if (return_rental($rental_id, $user_id)) {
        set_flash('success', 'Retourné avec succès!');
    } else {
        set_flash('error', 'Erreur lors du retour.');
    }
    redirect('rental/my_rentals');
}
?>