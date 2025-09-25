<?php
require_once CORE_PATH . '/database.php';
require_once MODEL_PATH . '/item_model.php';

/**
 * Compter le nombre total de locations
 */
function get_rentals_count() {
    $query = "SELECT COUNT(*) as total FROM loans";
    $result = db_select_one($query);
    return $result['total'] ?? 0;
}

/**
 * Création d'une nouvelle location avec heure précise (incluant secondes)
 */
function create_rental($user_id, $item_id) {
    // Débogage : enregistrer les paramètres d'entrée
    error_log("create_rental called with user_id: $user_id, item_id: $item_id");
    
    $parts = explode('_', $item_id);
    if (count($parts) != 2) {
        error_log("Invalid item_id format: $item_id");
        return ['success' => false, 'error' => 'Identifiant de média invalide.'];
    }
    $type = $parts[0];
    $pure_id = $parts[1];
    $media_type = ($type == 'book' || $type == 'livre') ? 'book' : (($type == 'film') ? 'movie' : 'video_game');
    $item = get_item_by_id($item_id);

    // Vérifie si le média est disponible (stock > 0)
    if (!$item || !isset($item['stock']) || $item['stock'] <= 0) {
        error_log("Media not available: item_id=$item_id, stock=" . ($item['stock'] ?? 'null'));
        return ['success' => false, 'error' => 'Le média n\'est pas disponible.'];
    }
    
    // Vérifie si l'utilisateur a déjà emprunté ce média (emprunt non retourné)
    $query = "SELECT COUNT(*) as count FROM loans WHERE user_id = ? AND media_id = ? AND media_type = ? AND returned_at IS NULL";
    $result = db_select_one($query, [$user_id, $pure_id, $media_type]);
    error_log("db_select_one result for duplicate rental check: " . print_r($result, true));
    if ($result === false || !isset($result['count'])) {
        error_log("Error: db_select_one failed or returned no count for duplicate check, user_id: $user_id, media_id: $pure_id, media_type: $media_type");
        return ['success' => false, 'error' => 'Erreur lors de la vérification des emprunts existants.'];
    }
    if ((int)$result['count'] > 0) {
        error_log("Duplicate rental attempt for user_id: $user_id, media_id: $pure_id, media_type: $media_type");
        return ['success' => false, 'error' => 'Vous avez déjà emprunté ce média. Veuillez le retourner avant de l\'emprunter à nouveau.'];
    }
    
    // Vérifie si l'utilisateur a déjà 3 emprunts en cours
    $query = "SELECT COUNT(*) as count FROM loans WHERE user_id = ? AND returned_at IS NULL";
    $result = db_select_one($query, [$user_id]);
    error_log("db_select_one result for current rentals: " . print_r($result, true));
    if ($result === false || !isset($result['count'])) {
        error_log("Error: db_select_one failed or returned no count for user_id: $user_id");
        return ['success' => false, 'error' => 'Erreur lors de la vérification des emprunts en cours.'];
    }
    $current_rentals = (int)$result['count'];
    error_log("Current rentals for user_id $user_id: $current_rentals");
    if ($current_rentals >= 3) {
        error_log("Rental limit exceeded for user_id: $user_id");
        return ['success' => false, 'error' => 'Vous ne pouvez pas emprunter plus de trois médias à la fois !'];
    }
    
    // Vérifie si l'utilisateur a des emprunts en retard
    $overdue_query = "SELECT COUNT(*) as count FROM loans WHERE user_id = ? AND returned_at IS NULL AND return_date < CURDATE()";
    $overdue_result = db_select_one($overdue_query, [$user_id]);
    error_log("db_select_one result for overdue rentals: " . print_r($overdue_result, true));
    if ($overdue_result === false || !isset($overdue_result['count'])) {
        error_log("Error: db_select_one failed or returned no count for overdue check, user_id: $user_id");
        return ['success' => false, 'error' => 'Erreur lors de la vérification des emprunts en retard.'];
    }
    $overdue = (int)$overdue_result['count'];
    error_log("Overdue rentals for user_id $user_id: $overdue");
    if ($overdue > 0) {
        return ['success' => false, 'error' => 'Vous avez des emprunts en retard. Veuillez les retourner avant d\'emprunter de nouveau.'];
    }
    
    db_begin_transaction();
    try {
        // Définit les dates de l'emprunt avec heure précise incluant secondes (14 jours)
        $loan_date = date('Y-m-d H:i:s');
        $return_date = date('Y-m-d', strtotime('+14 days', strtotime($loan_date))); // Date de retour prévue (seulement la date, sans heure)
        $query = "INSERT INTO loans (user_id, media_id, media_type, loan_date, return_date) VALUES (?, ?, ?, ?, ?)";
        db_execute($query, [$user_id, $pure_id, $media_type, $loan_date, $return_date]);
        
        // Diminue la disponibilité du média
        $table = ($media_type == 'book') ? 'books' : (($media_type == 'movie') ? 'movies' : 'video_games');
        $update_query = "UPDATE $table SET stock = stock - 1 WHERE id = ?";
        db_execute($update_query, [$pure_id]);
        
        db_commit();
        error_log("Rental created successfully for user_id: $user_id, item_id: $item_id");
        return ['success' => true, 'error' => null, 'return_date' => $return_date]; // Retourne la date de retour pour l'utiliser dans le message
    } catch (Exception $e) {
        db_rollback();
        error_log("Error creating rental: " . $e->getMessage());
        return ['success' => false, 'error' => 'Erreur technique lors de l\'emprunt.'];
    }
}

/**
 * Récupérer les locations d'un utilisateur par statut (actif Ou retourné)
 */
function get_user_rentals_by_status($user_id, $status = 'active') {
    $condition = ($status == 'active') ? 'returned_at IS NULL' : 'returned_at IS NOT NULL';
    $query = "SELECT r.id, 
                     r.media_id, 
                     r.loan_date AS rent_date, 
                     r.return_date, 
                     r.returned_at, 
                     CASE 
                         WHEN r.media_type = 'book' THEN (SELECT title FROM books WHERE id = r.media_id)
                         WHEN r.media_type = 'movie' THEN (SELECT title FROM movies WHERE id = r.media_id)
                         WHEN r.media_type = 'video_game' THEN (SELECT title FROM video_games WHERE id = r.media_id)
                     END AS title,
                     r.media_type AS type, 
                     CASE 
                         WHEN r.media_type = 'book' THEN (SELECT image_url FROM books WHERE id = r.media_id)
                         WHEN r.media_type = 'movie' THEN (SELECT image_url FROM movies WHERE id = r.media_id)
                         WHEN r.media_type = 'video_game' THEN (SELECT image_url FROM video_games WHERE id = r.media_id)
                     END AS image_url
              FROM loans r 
              WHERE r.user_id = ? AND $condition
              ORDER BY r.loan_date DESC";
    return db_select($query, [$user_id]);
}

/**
 * Retourner un item loué
 */
function return_rental($rental_id, $user_id) {
    $rental = db_select_one("SELECT * FROM loans WHERE id = ? AND user_id = ?", [$rental_id, $user_id]);
    if (!$rental || $rental['returned_at'] !== NULL) return false;
    
    db_begin_transaction();
    try {
        // Marque l'emprunt comme retourné
        $query = "UPDATE loans SET returned_at = NOW() WHERE id = ?";
        db_execute($query, [$rental_id]);
        
        // Augmente la disponibilité du média
        $table = ($rental['media_type'] == 'book') ? 'books' : (($rental['media_type'] == 'movie') ? 'movies' : 'video_games');
        $update_query = "UPDATE $table SET stock = stock + 1 WHERE id = ?";
        db_execute($update_query, [$rental['media_id']]); // Correction : remplacement de ) par ] pour fermer l'array
        
        db_commit();
        return true;
    } catch (Exception $e) {
        db_rollback();
        return false;
    }
}
?>