<?php
require_once CORE_PATH . '/database.php';

// Sélectionner un item par son ID
function get_item_by_id($item_id) {
    $pdo = db_connect();
    $parts = explode('_', $item_id);
    if (count($parts) != 2) {
        error_log("Invalid item_id format: $item_id");
        return false;
    }
    $type = $parts[0];
    $pure_id = (int)$parts[1];

    // Sélection selon le type
    if ($type == 'book') {
        $query = "SELECT CONCAT('book_', id) AS id, title, writer AS author_director_publisher, ISBN13 AS isbn_rating_platform, gender AS genre, page_number AS pages_duration_min_age, synopsis AS description, year, available, stock, image_url, 'book' AS type, upload_date 
                  FROM books WHERE id = ?";
    } elseif ($type == 'film') {
        $query = "SELECT CONCAT('film_', id) AS id, title, producer AS author_director_publisher, classification AS isbn_rating_platform, gender AS genre, duration AS pages_duration_min_age, synopsis AS description, year, available, stock, image_url, 'film' AS type, upload_date 
                  FROM movies WHERE id = ?";
    } elseif ($type == 'game') {
        $query = "SELECT CONCAT('game_', id) AS id, title, editor AS author_director_publisher, plateform AS isbn_rating_platform, gender AS genre, min_age AS pages_duration_min_age, description, year, available, stock, image_url, 'game' AS type, upload_date 
                  FROM video_games WHERE id = ?";
    } else {
        error_log("Invalid item type: $type");
        return false;
    }

    $stmt = $pdo->prepare($query);
    $stmt->execute([$pure_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    error_log("get_item_by_id result for item_id $item_id: " . print_r($result, true));
    return $result ?: false;
}

// Sélectionner tous les items
// Correction : Utilisation de producer au lieu de director pour la table movies et plateform au lieu de platform pour la table video_games
function get_all_items() {
    $query = "
        SELECT CONCAT('book_', id) AS id, title, writer AS author_director_publisher, ISBN13 AS isbn_rating_platform, gender AS genre, page_number AS pages_duration_min_age, synopsis AS description, year, available, stock, image_url, 'book' AS type, upload_date FROM books
        UNION
        SELECT CONCAT('film_', id) AS id, title, producer AS author_director_publisher, classification AS isbn_rating_platform, gender AS genre, duration AS pages_duration_min_age, synopsis AS description, year, available, stock, image_url, 'film' AS type, upload_date FROM movies
        UNION
        SELECT CONCAT('game_', id) AS id, title, editor AS author_director_publisher, plateform AS isbn_rating_platform, gender AS genre, min_age AS pages_duration_min_age, description, year, available, stock, image_url, 'game' AS type, upload_date FROM video_games
        ORDER BY upload_date DESC";
    return db_select($query);
}

// Sélectionner les items par type
// Correction : Suppression de la condition available lorsque search_availability = 'all' et utilisation de plateform au lieu de platform pour la table video_games, ajout de débogage pour vérifier les résultats
function get_items_by_type($type, $search_term = '', $search_genre = 'all', $search_availability = 'all', $per_page = 20, $offset = 0) {
    $pdo = db_connect();
    $conditions = [];
    $params = [];

    // Recherche par titre
    if (!empty($search_term)) {
        $conditions[] = "LOWER(title) LIKE LOWER(?)";
        $params[] = "%" . trim($search_term) . "%";
    }

    // Filtrer par genre
    if ($search_genre != 'all') {
        $conditions[] = "gender = ?";
        $params[] = $search_genre;
    }

    // Commentaire: Changement pour utiliser stock à la place de available pour la disponibilité
if ($search_availability != 'all') {
    if ($search_availability == 'true') {
        $conditions[] = "stock > 0";
    } else {
        $conditions[] = "stock = 0";
    }
}

    $condition_str = !empty($conditions) ? " WHERE " . implode(" AND ", $conditions) : "";

    // Sélection selon le type
    if ($type == 'book') {
        $query = "SELECT CONCAT('book_', id) AS id, title, writer AS author_director_publisher, ISBN13 AS isbn_rating_platform, gender AS genre, page_number AS pages_duration_min_age, synopsis AS description, year, available, stock, image_url, 'book' AS type, upload_date 
                  FROM books" . $condition_str . "
                  ORDER BY upload_date DESC 
                  LIMIT ? OFFSET ?";
        $params[] = $per_page;
        $params[] = $offset;
    } elseif ($type == 'film') {
        $query = "SELECT CONCAT('film_', id) AS id, title, producer AS author_director_publisher, classification AS isbn_rating_platform, gender AS genre, duration AS pages_duration_min_age, synopsis AS description, year, available, stock, image_url, 'film' AS type, upload_date 
                  FROM movies" . $condition_str . "
                  ORDER BY upload_date DESC 
                  LIMIT ? OFFSET ?";
        $params[] = $per_page;
        $params[] = $offset;
    } elseif ($type == 'game') {
        $query = "SELECT CONCAT('game_', id) AS id, title, editor AS author_director_publisher, plateform AS isbn_rating_platform, gender AS genre, min_age AS pages_duration_min_age, description, year, available, stock, image_url, 'game' AS type, upload_date 
                  FROM video_games" . $condition_str . "
                  ORDER BY upload_date DESC 
                  LIMIT ? OFFSET ?";
        $params[] = $per_page;
        $params[] = $offset;
    }

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Compter les items par type
function get_items_count_by_type($type, $search_term = '', $search_genre = 'all', $search_availability = 'all') {
    $pdo = db_connect();
    $conditions = [];
    $params = [];

    if (!empty($search_term)) {
        $conditions[] = "LOWER(title) LIKE LOWER(?)";
        $params[] = "%" . trim($search_term) . "%";
    }

    if ($search_genre != 'all') {
        $conditions[] = "gender = ?";
        $params[] = $search_genre;
    }

  // Commentaire: Changement pour utiliser stock à la place de available pour la disponibilité
if ($search_availability != 'all') {
    if ($search_availability == 'true') {
        $conditions[] = "stock > 0";
    } else {
        $conditions[] = "stock = 0";
    }
}

    $condition_str = !empty($conditions) ? " WHERE " . implode(" AND ", $conditions) : "";

    if ($type == 'book') {
        $query = "SELECT COUNT(*) as count FROM books" . $condition_str;
    } elseif ($type == 'film') {
        $query = "SELECT COUNT(*) as count FROM movies" . $condition_str;
    } elseif ($type == 'game') {
        $query = "SELECT COUNT(*) as count FROM video_games" . $condition_str;
    }

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    return $stmt->fetch(PDO::FETCH_ASSOC)['count'];
}

// Rechercher les items
function search_items($search_term = '', $search_type = 'all', $search_genre = 'all', $search_availability = 'all') {
    $pdo = db_connect();
    $conditions = [];
    $params = [];

    // Recherche par titre
    if (!empty($search_term)) {
        $conditions[] = "LOWER(title) LIKE LOWER(?)";
        $params[] = "%" . trim($search_term) . "%";
    }

    // Filtrer par genre
    if ($search_genre != 'all') {
        $conditions[] = "gender = ?";
        $params[] = $search_genre;
    }

    // Commentaire: Changement pour utiliser stock à la place de available pour la disponibilité
if ($search_availability != 'all') {
    if ($search_availability == 'true') {
        $conditions[] = "stock > 0";
    } else {
        $conditions[] = "stock = 0";
    }
}

    $condition_str = !empty($conditions) ? " AND " . implode(" AND ", $conditions) : "";

    // Sélection selon le type
    if ($search_type == 'book') {
        $query = "
            SELECT CONCAT('book_', id) AS id, title, writer AS author_director_publisher, ISBN13 AS isbn_rating_platform, gender AS genre, page_number AS pages_duration_min_age, synopsis AS description, year, available, stock, image_url, 'book' AS type, upload_date
            FROM books
            WHERE 1=1 $condition_str
            ORDER BY upload_date DESC";
    } elseif ($search_type == 'film') {
        $query = "
            SELECT CONCAT('film_', id) AS id, title, producer AS author_director_publisher, classification AS isbn_rating_platform, gender AS genre, duration AS pages_duration_min_age, synopsis AS description, year, available, stock, image_url, 'film' AS type, upload_date
            FROM movies
            WHERE 1=1 $condition_str
            ORDER BY upload_date DESC";
    } elseif ($search_type == 'game') {
        $query = "
            SELECT CONCAT('game_', id) AS id, title, editor AS author_director_publisher, plateform AS isbn_rating_platform, gender AS genre, min_age AS pages_duration_min_age, description, year, available, stock, image_url, 'game' AS type, upload_date
            FROM video_games
            WHERE 1=1 $condition_str
            ORDER BY upload_date DESC";
    } else {
        $query = "
            SELECT CONCAT('book_', id) AS id, title, writer AS author_director_publisher, ISBN13 AS isbn_rating_platform, gender AS genre, page_number AS pages_duration_min_age, synopsis AS description, year, available, stock, image_url, 'book' AS type, upload_date
            FROM books
            WHERE 1=1 $condition_str
            UNION ALL
            SELECT CONCAT('film_', id) AS id, title, producer AS author_director_publisher, classification AS isbn_rating_platform, gender AS genre, duration AS pages_duration_min_age, synopsis AS description, year, available, stock, image_url, 'film' AS type, upload_date
            FROM movies
            WHERE 1=1 $condition_str
            UNION ALL
            SELECT CONCAT('game_', id) AS id, title, editor AS author_director_publisher, plateform AS isbn_rating_platform, gender AS genre, min_age AS pages_duration_min_age, description, year, available, stock, image_url, 'game' AS type, upload_date
            FROM video_games
            WHERE 1=1 $condition_str
            ORDER BY upload_date DESC";
        // Répéter les params pour chaque partie du UNION
        if (!empty($params)) {
            $params = array_merge($params, $params, $params);
        }
    }

    $stmt = $pdo->prepare($query);
    foreach ($params as $index => $value) {
        $stmt->bindValue($index + 1, $value);
    }
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Debug: Afficher les résultats pour vérifier les données
    // var_dump($results); exit; // Décommenter pour débogage
    return $results;
}
?>