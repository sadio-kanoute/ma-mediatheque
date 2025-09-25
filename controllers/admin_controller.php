<?php

function admin_dashboard() {
    require_admin();

    // Récupérer les stats depuis le modèle
    $stats = get_dashboard_stats();

    // Charger la vue dans le layout (affiche le header/navbar)
    load_view_with_layout('admin/dashboard', ['stats' => $stats, 'title' => 'Tableau de bord']);
}

// ----------------- GESTION DES MÉDIAS -----------------
function admin_media_list()
{
    // Vérifie les droits de la'administrateur
    require_admin();
    // Pagination simple sur tous les items (admin) — pas de recherche ici
    $page = max(1, intval($_GET['page'] ?? 1));
    $per_page = 20;
    $offset = ($page - 1) * $per_page;

    $raw = get_all_items();
    // Normaliser les résultats pour la vue admin
    $medias = [];
    foreach ($raw as $row) {
        $rid = $row['id'] ?? '';
        $parts = explode('_', $rid);
        $prefix = $parts[0] ?? '';
        $numId = isset($parts[1]) ? intval($parts[1]) : ($row['id'] ?? 0);
        // map type from item_model: 'book','film','game' -> media_type
        $type_field = $row['type'] ?? $prefix;
        $media_type = 'book';
        if ($type_field === 'film' || $prefix === 'film') $media_type = 'movie';
        if ($type_field === 'game' || $prefix === 'game') $media_type = 'video_game';
        if ($type_field === 'book' || $prefix === 'book') $media_type = 'book';

        $medias[] = [
            'media_type' => $media_type,
            'id' => $numId,
            'title' => $row['title'] ?? '',
            'genre' => $row['genre'] ?? ($row['gender'] ?? ''),
            'stock' => isset($row['stock']) ? intval($row['stock']) : 0,
            'image_url' => $row['image_url'] ?? ($row['image'] ?? 'default.jpg')
        ];
    }

    $total = count($medias);
    $paged = array_slice($medias, $offset, $per_page);

    load_view_with_layout('admin/media_list', [
        'medias' => $paged,
        'total' => $total,
        'page' => $page,
        'per_page' => $per_page
    ]);
}

function admin_media_edit($id = null)
{
    // Vérifie les droits de l'administrateur
    require_admin();
    // Initialisation de la variable média
    $media = null;
    $resolved_type = '';
    if ($id) {
        // Sépare l'ID en type Et identifiant
        $parts = explode('_', $id);
        if (count($parts) === 2) {
            $type = $parts[0];
            $media_id = $parts[1];
            $media = get_media_by_id($media_id, $type);
            if ($media) {
                $media['media_type'] = $type;
                $resolved_type = $type;
            }
        }
    }
    // If adding, allow ?type=livre|film|jeu to pre-select the form
    if (!$resolved_type) {
        $resolved_type = $_GET['type'] ?? '';
    }

    // Affiche le formulaire d'édition ou de création
    load_view_with_layout('admin/media_edit', ['media' => $media, 'title' => $id ? 'Éditer média' : 'Ajouter média', 'resolved_type' => $resolved_type]);
}

/**
 * Enregistre un média (création ou mise à jour)
 */
function admin_media_save($id = null)
{
    require_admin();
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    set_flash('error', 'Token CSRF invalide');
    redirect($id ? 'admin/media_edit/'.$id : 'admin/media_add');
        return;
    }
    $title = trim($_POST['title'] ?? '');
    $type = $_POST['type'] ?? '';
    $genre = trim($_POST['genre'] ?? '');
    $stock = intval($_POST['stock'] ?? 1);
    $type_map = ['livre' => 'book', 'film' => 'movie', 'jeu' => 'video_game'];
    // If editing an existing media, derive the DB type from the id prefix (prevent changing type on edit)
    if ($id) {
        $parts = explode('_', $id);
        if (count($parts) === 2) {
            $type_db = $parts[0];
            $media_id = intval($parts[1]);
        } else {
            $type_db = $type_map[$type] ?? $type;
        }
    } else {
        $type_db = $type_map[$type] ?? $type;
    }

    // If editing, load the existing media now so we can accept its current values during validation
    $existing_media = null;
    if (!empty($media_id)) {
        $existing_media = get_media_by_id($media_id, $type_db);
    }
    if (!in_array($type_db, ['book', 'movie', 'video_game'])) {
    set_flash('error', 'Type de média invalide');
    redirect($id ? 'admin/media_edit/'.$id : 'admin/media_add');
        return;
    }
    $extra_fields = ['title' => $title, 'genre' => $genre, 'stock' => $stock];
    if ($type_db === 'book') {
        $extra_fields['writer'] = trim($_POST['writer'] ?? '');
        $extra_fields['ISBN13'] = trim($_POST['ISBN13'] ?? '');
        $extra_fields['page_number'] = intval($_POST['page_number'] ?? 0);
        $extra_fields['synopsis'] = trim($_POST['synopsis_book'] ?? '');
        $year_input = trim($_POST['year_book'] ?? '');
        $extra_fields['year'] = $year_input !== '' ? intval($year_input) : date('Y'); // Default to current year if empty
    } elseif ($type_db === 'movie') {
        $extra_fields['producer'] = trim($_POST['producer'] ?? '');
        $extra_fields['duration'] = intval($_POST['duration'] ?? 0);
        $extra_fields['synopsis'] = trim($_POST['synopsis_movie'] ?? '');
        $extra_fields['classification'] = trim($_POST['classification'] ?? '');
        $year_input = trim($_POST['year_movie'] ?? '');
        $extra_fields['year'] = $year_input !== '' ? intval($year_input) : date('Y'); // Default to current year if empty
    } elseif ($type_db === 'video_game') {
        $extra_fields['editor'] = trim($_POST['editor'] ?? '');
        $extra_fields['plateform'] = trim($_POST['plateform'] ?? ''); // Note: 'plateform' in DB
        $extra_fields['min_age'] = intval($_POST['min_age'] ?? 0);
        $extra_fields['description'] = trim($_POST['description_game'] ?? ''); // Note: 'description' in DB
        $year_input = trim($_POST['year_game'] ?? '');
        $extra_fields['year'] = $year_input !== '' ? intval($year_input) : date('Y'); // Default to current year if empty
    }
    
    // === Initialisation des erreurs et gestion de l'upload d'image ===
    $errors = [];
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        // Utiliser la fonction media_upload_image() améliorée selon le cahier des charges
        $image_result = media_upload_image($type_db, [], $_FILES['image']);
        if (!empty($image_result['errors'])) {
            foreach ($image_result['errors'] as $error) {
                $errors[] = $error;
            }
        } else {
            $extra_fields['image_url'] = $image_result['success'];
        }
    }
    
    // === Validation côté serveur (selon cahier des charges) ===
    // Titre
    if (strlen($title) < 1 || strlen($title) > 200) {
        $errors[] = 'Le titre doit contenir entre 1 et 200 caractères.';
    }
    // Type
    if (!in_array($type_db, ['book', 'movie', 'video_game'])) {
        $errors[] = 'Type de média invalide.';
    }
    // Genre
    if (empty($genre)) {
        $errors[] = 'Le genre est obligatoire.';
    }
    // Stock
    if (!is_int($stock) || $stock < 1) {
        $errors[] = 'Le stock doit être un entier positif (minimum 1).';
    }

    // Validation spécifique par type
    if ($type_db === 'book') {
        $writer = $extra_fields['writer'] ?? '';
        $isbn = $extra_fields['ISBN13'] ?? '';
        $page_number = $extra_fields['page_number'] ?? 0;
        $year = $extra_fields['year']; // Can be null now
        if (strlen($writer) < 2 || strlen($writer) > 100) $errors[] = 'Auteur doit contenir entre 2 et 100 caractères.';
        // ISBN format check (10 or 13 digits)
        if (!empty($isbn) && !preg_match('/^(\d{10}|\d{13})$/', $isbn)) $errors[] = 'ISBN invalide (10 ou 13 chiffres).';
        // ISBN uniqueness
        if (!empty($isbn)) {
            $exclude = null;
            if ($id) {
                $parts = explode('_', $id);
                if (count($parts) === 2) $exclude = intval($parts[1]);
            }
            if (isbn_exists($isbn, $exclude)) $errors[] = 'Cet ISBN existe déjà dans la base.';
        }
        if ($page_number < 1 || $page_number > 9999) $errors[] = 'Nombre de pages invalide.';
        $current_year = intval(date('Y'));
        if ($year < 1900 || $year > $current_year) $errors[] = 'Année de publication invalide.';
    } elseif ($type_db === 'movie') {
        $producer = $extra_fields['producer'] ?? '';
        $duration = $extra_fields['duration'] ?? 0;
        $classification = $extra_fields['classification'] ?? '';
        $year = $extra_fields['year']; // Can be null now
        if (strlen($producer) < 2 || strlen($producer) > 100) $errors[] = 'Réalisateur doit contenir entre 2 et 100 caractères.';
        if ($duration < 1 || $duration > 999) $errors[] = 'Durée invalide (1-999 minutes).';
        $current_year = intval(date('Y'));
        if ($year < 1900 || $year > $current_year) $errors[] = 'Année invalide.';
        $allowed_class = ['Tous publics', '-12', '-16', '-18'];
        if ($existing_media) {
            $existing_class = $existing_media['classification'] ?? '';
            if ($existing_class !== '' && !in_array($existing_class, $allowed_class)) $allowed_class[] = $existing_class;
        }
        if (!in_array($classification, $allowed_class)) $errors[] = 'Classification invalide.';
    } elseif ($type_db === 'video_game') {
        $editor = $extra_fields['editor'] ?? '';
        $platform = $extra_fields['plateform'] ?? ''; // Note: 'plateform' in DB
        $min_age = $extra_fields['min_age'] ?? 0;
        $year = $extra_fields['year']; // Can be null now
        if (strlen($editor) < 2 || strlen($editor) > 100) $errors[] = 'Éditeur doit contenir entre 2 et 100 caractères.';
        $allowed_platforms = ['PC', 'PlayStation', 'Xbox', 'Nintendo', 'Mobile'];
        // Accept existing DB platform values for edits (non-destructive)
        if ($existing_media) {
            $existing_platform = $existing_media['plateform'] ?? ($existing_media['platform'] ?? ''); // Note: DB uses 'plateform'
            if ($existing_platform !== '' && !in_array($existing_platform, $allowed_platforms)) {
                $allowed_platforms[] = $existing_platform;
            }
        }
        if (!in_array($platform, $allowed_platforms)) $errors[] = 'Plateforme invalide.';
        $allowed_ages = [3,7,12,16,18];
        if ($existing_media) {
            $existing_age = isset($existing_media['min_age']) ? intval($existing_media['min_age']) : null;
            if ($existing_age !== null && !in_array($existing_age, $allowed_ages)) {
                $allowed_ages[] = $existing_age;
            }
        }
        if (!in_array($min_age, $allowed_ages)) $errors[] = 'Âge minimum invalide.';
        $current_year = intval(date('Y'));
        if ($year < 1900 || $year > $current_year) $errors[] = 'Année invalide.';
    }

    if (!empty($errors)) {
        foreach ($errors as $err) set_flash('error', $err);
        redirect($id ? 'admin/media_edit/'.$id : 'admin/media_add');
        return;
    }
    if ($id) {
        $parts = explode('_', $id);
        if (count($parts) === 2) {
            $type_db = $parts[0];
            $media_id = $parts[1];
            $media = get_media_by_id($media_id, $type_db);

            // Si une nouvelle image est uploadée, supprimer l'ancienne
            if (isset($extra_fields['image_url']) && $media && !empty($media['image_url'])) {
                $old_file_path = __DIR__ . '/../uploads/covers/' . $media['image_url'];
                if (file_exists($old_file_path)) {
                    unlink($old_file_path);
                }
            }
            update_media($media_id, $type_db, $extra_fields);
            set_flash('success', 'Média mis à jour avec succès.');
        }
    } else {
        create_media($type_db, $extra_fields);
        set_flash('success', 'Média créé avec succès.');
    }
    redirect('admin/media');
}

/**
 * Supprime un média
 */
function admin_media_delete($id, $type)
{
    require_admin();
    // Ensure only POST requests can delete and verify CSRF
    if (!is_post()) {
        set_flash('error', 'Méthode non autorisée.');
        redirect('admin/media');
        return;
    }
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash('error', 'Token CSRF invalide');
        redirect('admin/media');
        return;
    }
    $type_map = ['livre' => 'book', 'film' => 'movie', 'jeu' => 'video_game'];
    $type_db = $type_map[$type] ?? $type;

    // Vérifier s'il y a des emprunts en cours
    $active_loans = db_select_one(
        "SELECT COUNT(*) as total FROM loans WHERE media_id = ? AND media_type = ? AND returned_at IS NULL",
        [$id, $type_db]
    );
    if (($active_loans['total'] ?? 0) > 0) {
            set_flash('error', 'Impossible de supprimer ce média : emprunts en cours.');
        redirect('admin/media');
        return;
    }

    // Suppression physique de l'image
    $media = get_media_by_id($id, $type_db);
    if ($media && !empty($media['image_url'])) {
        $file_path = PUBLIC_PATH . '/assets/images/' . $media['image_url'];
        if (file_exists($file_path)) unlink($file_path);
    }

    // Suppression en base
    if (in_array($type_db, ['book', 'movie', 'video_game']) && delete_media($id, $type_db)) {
        set_flash('success', 'Média supprimé avec succès.');
    } else {
        set_flash('error', 'Impossible de supprimer ce média.');
    }
    redirect('admin/media');
}

// ----------------- GESTION DES UTILISATEURS -----------------
function admin_users_list()
{
    // Vérifie les droits de l'administrateur
    require_admin();
    // Récupère tous les utilisateurs
    $users = get_all_users();
    // Affiche la liste des utilisateurs
    load_view_with_layout('admin/users_list', ['users' => $users]);
}

function admin_user_detail($id)
{
    require_admin();

    // Récupérer les infos utilisateur
    $user = get_user_by_id($id);
    if (!$user) {
        set_flash('error', 'Utilisateur introuvable.');
        redirect('admin/users');
        return;
    }

    // Stats emprunts
    $all_loans = get_user_loans($id) ?: [];
    $user['total_loans'] = count($all_loans);
    $user['active_loans'] = count_active_loans($id) ?? 0;
    $user['overdue_loans'] = get_user_overdue_loans($id) ?: [];

    // Affiche la vue
    load_view_with_layout('admin/user_detail', [
        'user' => $user,
        'loans' => $all_loans
    ]);
}

/**
 * Met à jour le rôle d'un utilisateur (admin only)
 * Route: POST /admin/user_update_role/{id}
 */
function admin_user_update_role($id)
{
    require_admin();

    // Only allow POST
    if (!is_post()) {
        set_flash('error', 'Méthode non autorisée.');
        redirect('admin/user_detail/' . $id);
        return;
    }

    // CSRF
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash('error', 'Token CSRF invalide');
        redirect('admin/user_detail/' . $id);
        return;
    }

    $new_role = ($_POST['role'] ?? 'user') === 'admin' ? 'admin' : 'user';

    // Prevent an admin from demoting themselves
    if (isset($_SESSION['user']) && $_SESSION['user']['id'] == $id && $new_role !== 'admin') {
        set_flash('error', 'Vous ne pouvez pas vous retirer les droits administrateur.');
        redirect('admin/user_detail/' . $id);
        return;
    }

    // Load the user to ensure exists
    $target = get_user_by_id($id);
    if (!$target) {
        set_flash('error', 'Utilisateur introuvable.');
        redirect('admin/users');
        return;
    }

    // Update user role via model
    if (update_user($id, $target['name'], $target['last_name'], $target['email'], $new_role)) {
        set_flash('success', 'Rôle mis à jour.');
        // If we changed the current logged-in user, refresh session role
        if (isset($_SESSION['user']) && $_SESSION['user']['id'] == $id) {
            $_SESSION['user']['role'] = $new_role;
        }
    } else {
        set_flash('error', 'Impossible de mettre à jour le rôle.');
    }
    redirect('admin/user_detail/' . $id);
}



// ----------------- GESTION DES EMPRUNTS -----------------
function admin_loans_list()
{
    // Vérifie les droits 'administrateur
    require_admin();
    // Récupère tous les emprunts
    $loans = get_all_loans();
    // Affiche la liste des emprunts
    load_view_with_layout('admin/loans_list', ['loans' => $loans]);
}

function admin_loan_return($loan_id)
{
    // Vérifie les droits de l'administrateur
    require_admin();
    // Only allow POST and verify CSRF
    if (!is_post()) {
        set_flash('error', 'Méthode non autorisée.');
        redirect('admin/loans');
        return;
    }
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash('error', 'Token CSRF invalide');
        redirect('admin/loans');
        return;
    }
    // Marque l'emprunt comme rendu
    if (return_loan($loan_id)) {
        set_flash('success', 'Emprunt marqué comme rendu.');
    } else {
        set_flash('error', 'Impossible de marquer cet emprunt comme rendu.');
    }
    // Redirection vers la liste des emprunts
    redirect('admin/loans');
}

function admin_loan_create($user_id, $media_id, $media_type)
{
    // Vérifie les droits de l'administrateur
    require_admin();
    // Only allow POST and verify CSRF
    if (!is_post()) {
        set_flash('error', 'Méthode non autorisée.');
        redirect('admin/loans');
        return;
    }
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash('error', 'Token CSRF invalide');
        redirect('admin/loans');
        return;
    }
    // Crée un nouvel emprunt
    if (create_loan($user_id, $media_id, $media_type)) {
        set_flash('success', 'Emprunt enregistré avec succès.');
    } else {
        set_flash('error', 'Impossible de créer cet emprunt (limite atteinte ou média indisponible).');
    }
    // Redirection vers la liste des emprunts
    redirect('admin/loans');
}

/**
 * Affiche le formulaire d'édition d'un emprunt
 */
function admin_loan_edit($loan_id)
{
    require_admin();
    $loan = get_loan_by_id($loan_id);
    if (!$loan) {
        set_flash('error', 'Emprunt introuvable.');
        redirect('admin/loans');
        return;
    }
    load_view_with_layout('admin/loan_edit', ['loan' => $loan]);
}

/**
 * Traite la mise à jour d'un emprunt (date de retour)
 */
function admin_loan_update($loan_id)
{
    require_admin();
    if (!is_post()) {
        redirect('admin/loan_edit/' . $loan_id);
        return;
    }
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash('error', 'Token CSRF invalide');
        redirect('admin/loan_edit/' . $loan_id);
        return;
    }
    $return_date = trim($_POST['return_date'] ?? '');
    $mark_returned = isset($_POST['mark_returned']);

    if ($mark_returned) {
        // Utilise la fonction existante pour marquer comme retourné
        if (return_loan($loan_id)) {
            set_flash('success', 'Emprunt marqué comme rendu.');
        } else {
            set_flash('error', 'Impossible de marquer cet emprunt comme rendu.');
        }
        redirect('admin/loans');
        return;
    }

    // Validation simple de la date
    if ($return_date !== '') {
        $d = date_parse($return_date);
        if (!checkdate($d['month'] ?? 0, $d['day'] ?? 0, $d['year'] ?? 0)) {
            set_flash('error', 'Date de retour invalide. Utilisez JJ/MM/AAAA ou YYYY-MM-DD.');
            redirect('admin/loan_edit/' . $loan_id);
            return;
        }
        // Normaliser en YYYY-MM-DD si fournie en JJ/MM/AAAA
        if (strpos($return_date, '/') !== false) {
            $parts = explode('/', $return_date);
            if (count($parts) === 3) {
                $return_date = $parts[2] . '-' . $parts[1] . '-' . $parts[0];
            }
        }
    }

    if (update_loan($loan_id, ['return_date' => $return_date])) {
        set_flash('success', 'Date de retour mise à jour.');
    } else {
        set_flash('error', 'Impossible de mettre à jour cet emprunt.');
    }
    redirect('admin/loans');
}


// ----------------- ALIASES POUR ROUTER -----------------
// Regroupés ici pour simplifier le routing
function admin_media()
{
    return admin_media_list();
}

/**
 * Affiche le formulaire d'ajout de média (alias)
 */
function admin_media_add()
{
    return admin_media_edit(null);
}

function admin_users()
{
    return admin_users_list();
}

function admin_loans()
{
    return admin_loans_list();
}
